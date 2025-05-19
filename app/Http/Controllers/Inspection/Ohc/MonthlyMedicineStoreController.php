<?php

namespace App\Http\Controllers\Inspection\Ohc;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\SimpleExcel\SimpleExcelWriter;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Models\OhcManagement\Master\Medicine;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Mail\Inspection\Safety\SafetyInspection;
use App\Models\Inspection\Ohc\MonthlyMedicineStore;
use App\Mail\Inspection\Ohc\FloorStretcher as OhcFloorStretcher;

class MonthlyMedicineStoreController extends Controller
{
    private $medicine_checklist;
    private $medicine;
    private $signature;


    public function __construct()
    {
        $this->medicine_checklist = new MonthlyMedicineStore();
        $this->medicine = new Medicine();
        $this->signature = new OhcSignature();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->medicine_checklist->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('inspection_status', function ($row) {
                            $text = '';
                            switch ($row->inspection_status) {
                                case OBSERVATION_PENDING:
                                    $text = "<span class='badge bg-primary rounded' style='font-size: 1.0em;'>OBSERVATION PENDING</span>";
                                    break;
                                case OBSERVATION_APPROVED:
                                    $text = "<span class='badge bg-success' style='font-size: 1.0em;'>OBSERVATION APPROVED</span>";
                                    break;
                                case OBSERVATION_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>OBSERVATION REJECTED</span>";
                                    break;
                                default:
                                    $text = "<span class='badge rounded-pill text-bg-warning'>Unknown</span>";
                            }
                            return $text;
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('inspection_date', function ($row) {
                            return Displaydateformat($row->inspection_date);
                        })
                        ->addColumn('next_due', function ($row) {
                            return Displaydateformat($row->next_due);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })

                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('ohc/monthly-medicine-store/inspection/view/' . encryptId($row->id)) . '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';

                            if (($row->inspection_status == OBSERVATION_PENDING &&  isAdmin()) ||($row->inspection_status == OBSERVATION_PENDING &&  CheckUserRole(ROLE_EHS_OFFICER)) ) {

                                $btn .= '<a href="' . admin_url('ohc/monthly-medicine-store/inspection/approval/' . encryptId($row->id)) . '" class="me-1" title="Action"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }

                            $btn .= '<a href="' . admin_url('ohc/monthly-medicine-store/inspection/exportViewpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';

                            $btn .= '<a href="' . admin_url('ohc/monthly-medicine-store/inspection/generalexcel/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                     </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'inspection_status', 'inspection_date', 'next_due'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }

        return view('inspection.inspection_ohc.monthly_medicine_store.list');
    }

    public function Add(Request $request)
    {
        try {

            $medicines = $this->medicine->getMedicineData();

            $data = array(
                'medicines' => $medicines,
            );
            return view('inspection.inspection_ohc.monthly_medicine_store.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/monthly-medicine-store/inspection/list'));
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'inspection_date' => 'required',
                'next_due' => 'required',
                'available_quantity.*' => 'required',
                'expired_date.*' => 'required',
                'emp_id.*' => 'required',
                'remarks.*' => 'required',
                'signature_upload.*' => [
                    function ($attribute, $value, $fail) {
                        $user = Auth::user();
                        if (is_null($user->signature_upload)) {
                            $fail('Signature is required.');
                        }
                    }
                ],
            ];

            $messages = [
                'inspection_date.required' => 'Inspection Date is required.',
                'next_due.required' => 'Next Due Date is required.',
                'available_quantity.*.required' => 'Available Quantity is required',
                'expired_date.*.required' => 'Expired Date is required',
                'remarks.*' => 'Remarks is required',
                'emp_id.*' => 'Employee is required',
                'signature_upload' => 'Signature is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }



            $store = $this->medicine_checklist->store();
            $inspection_type = OHC_TYPE_MONTHLY_MEDICINE_STORE;
            $inspection_details = $this->medicine_checklist->selectOne($store->id);
            $files = $this->signature->requestorsignatureUpload($inspection_type, $inspection_details->id);

            $ehsOfficer = GetEHSOfficer();
            if (!empty($ehsOfficer)) {
                $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
                $mailsubject = 'Monthly OHC Store Medicine Inspection Checklist';
                $notificationData = array(
                    'notification_type' => OHC_INSPECTION,
                    'module_type' => 18,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => "Monthly Medicine Store inspection Has been Created",
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $inspection_details->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('ohc/monthly-medicine-store/inspection/approval/' . encryptId($inspection_details->id)),
                    'assigned_user' => array_to_string($ehsOfficers),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $title = 'Monthly Medicine Store Observation has been Created';
                foreach ($ehsOfficers as $user) {
                    $email_id = getUseremail($user);
                    $url = admin_url('ohc/monthly-medicine-store/inspection/approval/' . encryptId($inspection_details->id));
                    $details = array(
                        'safety_type' => 'Monthly Medicine Store',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'url' => $url,
                        'data' => $inspection_details
                    );
                    Mail::to($email_id)->queue(new SafetyInspection($details));
                }
            }

            Session::flash('success', 'Your data has been added successfully');
            return redirect(admin_url('ohc/monthly-medicine-store/inspection/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/monthly-medicine-store/inspection/list'));
        }
    }

    public function View(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_details = $this->medicine_checklist->selectOne($id);
            $inspection_type = OHC_TYPE_MONTHLY_MEDICINE_STORE;
            $inspection_data = json_decode($inspection_details->inspection_data, true);
            $inspection_file = GetOHCSignature($inspection_details->created_by, $inspection_details->id, $inspection_type);
            $verified_by = GetOHCSignature($inspection_details->updated_by, $inspection_details->id, $inspection_type);

            $data = array(
                'inspection_details' => $inspection_details,
                'inspection_file' => $inspection_file,
                'inspection_data' => $inspection_data,
                'verified_by' => $verified_by,
            );


            return view('inspection.inspection_ohc.monthly_medicine_store.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/monthly-medicine-store/inspection/list'));
        }
    }

    public function ExportPDF()
    {
        try {
            $allData = $this->medicine_checklist->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }



            $data = array(
                'content' => $allData,
                'pagetitle' => "Monthly Medicine Store Inspection",
            );

            $property = [
                'tempDir' => 'public/pdf/temp/',
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,

            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $view = view('inspection.inspection_ohc.monthly_medicine_store.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Monthly Medicine Store Inspection.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/monthly-medicine-store/inspection/list'));
        }
    }

    public function ExportViewPDF(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_detail = $this->medicine_checklist->selectOne($id);
            $inspection_type = OHC_TYPE_MONTHLY_MEDICINE_STORE;
            $inspection_file = $this->signature->getFiles($id, $inspection_type);
            $inspection_data = json_decode($inspection_detail->inspection_data, true);
            $inspection_updated_by = GetOHCSignature($inspection_detail->updated_by, $inspection_detail->id, $inspection_type);
            $inspection_created_by = GetOHCSignature($inspection_detail->created_by, $inspection_detail->id, $inspection_type);

            $property = [
                'tempDir' => 'public/pdf/temp/',
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,

            ];

            $data = array(
                'inspection_detail' => $inspection_detail,
                'inspection_file' => $inspection_file,
                'pagetitle' => "Monthly Medicine Store Inspection",
                'inspection_data' => $inspection_data,
                'inspection_created_by' => $inspection_created_by,
                'inspection_updated_by' => $inspection_updated_by,
            );


            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $html = view('inspection.inspection_ohc.monthly_medicine_store.viewpdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Monthly Medicine Store Inspection.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/monthly-medicine-store/inspection/list'));
        }
    }

    public function approval(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_details = $this->medicine_checklist->selectOne($id);
            $inspection_type = OHC_TYPE_MONTHLY_MEDICINE_STORE;
            $inspection_file = GetOHCSignature($inspection_details->created_by, $inspection_details->id, $inspection_type);
            $inspection_data = json_decode($inspection_details->inspection_data, true);

            $data = array(
                'inspection_details' => $inspection_details,
                'inspection_file' => $inspection_file,
                'inspection_data' => $inspection_data,
            );

            return view('inspection.inspection_ohc.monthly_medicine_store.approval', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/monthly-medicine-store/inspection/list'));
        }
    }

    public function approvalSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->capa_remarks;
            $eye_wash_inspection = $this->medicine_checklist->approvalSubmit($id, $status, $remarks);
            $inspection_details = $this->medicine_checklist->selectOne($id);
            $ehsOfficer = [$inspection_details->created_by];

            $signature_update = $this->signature->signatureUpload(OHC_TYPE_MONTHLY_MEDICINE_STORE);
            if ($status == 1) {
                $message = 'Monthly Store Medicine Checklist - APPROVED';
                $to_status = OBSERVATION_APPROVED;
            } else {
                $message = 'Monthly Store Medicine Checklist - REJECTED';
                $to_status = OBSERVATION_REJECTED;
            }
            $web_link =   admin_url('ohc/monthly-medicine-store/inspection/view/' . encryptId($inspection_details->id));
            $mailsubject = 'Monthly OHC Store Medicine Inspection Checklist';
            $notificationData = array(
                'notification_type' => OHC_INSPECTION,
                'module_type' => 18,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => $message,
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_details->id,
                    'module' => 1,
                )),
                'web_link' =>  $web_link,
                'assigned_user' => array_to_string($ehsOfficer),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Monthly Store Medicine Checklist - Observation Status';
            $email_id = getUseremail($ehsOfficer);
            $url = admin_url('ohc/monthly-medicine-store/inspection/view/' . encryptId($inspection_details->id));
            $details = array(
                'safety_type' => 'Monthly Store Medicine Checklist',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new SafetyInspection($details));

            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/monthly-medicine-store/inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('ohc/monthly-medicine-store/inspection/list'));
        }
    }

    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $inspection_detail = $this->medicine_checklist->selectOne($id);
            $inspection_type = OHC_TYPE_MONTHLY_MEDICINE_STORE;
            $inspection_data = json_decode($inspection_detail->inspection_data, true);
            $inspection_updated_by = GetOHCSignature($inspection_detail->updated_by, $inspection_detail->id, $inspection_type);
            $inspection_created_by = GetOHCSignature($inspection_detail->created_by, $inspection_detail->id, $inspection_type);

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $sheet->mergeCells("A1:F3");
            $sheet->getStyle("A1:F3")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
            ]);
            $logoLeftPath = public_path('assets/images/logo-dark.png');
            if (file_exists($logoLeftPath)) {
                $drawing = new Drawing();
                $drawing->setName('Left Logo');
                $drawing->setPath($logoLeftPath);
                $drawing->setCoordinates('B1');
                $drawing->setOffsetX(100);
                $drawing->setOffsetY(15);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
            }

            $sheet->mergeCells("G1:M3");
            $sheet->setCellValue("G1", "Monthly OHC Store Medicine Inspection Checklist");
            $sheet->getStyle("G1")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $sheet->mergeCells("N1:S3");
            $sheet->getStyle("N1:S3")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);
            $logoRightPath = public_path('assets/images/plus-image.webp');
            if (file_exists($logoRightPath)) {
                $drawing = new Drawing();
                $drawing->setName('Right Logo');
                $drawing->setPath($logoRightPath);
                $drawing->setCoordinates('O1');
                $drawing->setOffsetX(100);
                $drawing->setOffsetY(15);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
            }

            $sheet->mergeCells("A4:J4");
            $richText1 = new RichText();
            $richText1->createTextRun('DATE OF INSPECTION :- ')->getFont()->setBold(true);
            $richText1->createText(Displaydateformat($inspection_detail->inspection_date));
            $sheet->getCell("A4")->setValue($richText1);

            $sheet->mergeCells("K4:S4");
            $richText2 = new RichText();
            $richText2->createTextRun('NEXT DUE :- ')->getFont()->setBold(true);
            $richText2->createText(Displaydateformat($inspection_detail->next_due));
            $sheet->getCell("K4")->setValue($richText2);

            $sheet->getStyle("A4:S4")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells("A5:C5")->setCellValue("A5", "SERIAL NO");
            $sheet->mergeCells("D5:F5")->setCellValue("D5", "NAME OF THE MEDICINE");
            $sheet->mergeCells("G5:H5")->setCellValue("G5", "QUANTITY");
            $sheet->mergeCells("I5:K5")->setCellValue("I5", "EXPIRY DATE");
            $sheet->mergeCells("L5:O5")->setCellValue("L5", "INSPECTED BY");
            $sheet->mergeCells("P5:S5")->setCellValue("P5", "REMARKS");

            $sheet->getStyle("A5:S5")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $row = 6;
            foreach ($inspection_data as $index => $detail) {
                $sheet->mergeCells("A$row:C$row")->setCellValue("A$row", $index);
                $sheet->mergeCells("D$row:F$row")->setCellValue("D$row", getMedicinename($detail['medicine_id']));
                $sheet->mergeCells("G$row:H$row")->setCellValue("G$row", $detail['available_quantity'] ?? '');
                $sheet->mergeCells("I$row:K$row")->setCellValue("I$row", Displaydateformat($detail['expired_date']));
                $sheet->mergeCells("L$row:O$row")->setCellValue("L$row", getUsername($detail['emp_id']));
                $sheet->mergeCells("P$row:S$row")->setCellValue("P$row", $detail['remarks'] ?? '');

                $sheet->getStyle("A$row:S$row")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $row++;
            }

            $signatureRow = $row;

            $sheet->getRowDimension($signatureRow)->setRowHeight(80);

            $sheet->mergeCells("A{$signatureRow}:J{$signatureRow}");
            $sheet->getStyle("A{$signatureRow}:J{$signatureRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);
            if (file_exists($inspection_created_by)) {
                $drawing = new Drawing();
                $drawing->setName('Inspection and checked By');
                $drawing->setPath($inspection_created_by);
                $drawing->setCoordinates("D{$signatureRow}");
                $drawing->setOffsetX(50);
                $drawing->setOffsetY(10);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
            }

            $richText = new RichText();
            $richText->createTextRun("Inspected and checked By: " . getUsername($inspection_detail->created_by))->getFont()->setBold(true);
            $sheet->getCell("A{$signatureRow}")->setValue($richText);

            $sheet->mergeCells("K{$signatureRow}:S{$signatureRow}");
            $sheet->getStyle("K{$signatureRow}:S{$signatureRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);
            if (file_exists($inspection_updated_by)) {
                $drawing = new Drawing();
                $drawing->setName('Approved By');
                $drawing->setPath($inspection_updated_by);
                $drawing->setCoordinates("N{$signatureRow}");
                $drawing->setOffsetX(50);
                $drawing->setOffsetY(10);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
            }

            $richText2 = new RichText();
            $richText2->createTextRun("Approved By: " . getUsername($inspection_detail->updated_by))->getFont()->setBold(true);
            $sheet->getCell("K{$signatureRow}")->setValue($richText2);

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Monthly Medicine Store Inspection.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function ExportExcel()
    {
        try {
            $allData = $this->medicine_checklist->exportdata();
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $row = 1;

            foreach ($allData as $inspection_detail) {
                $inspection_detail = $this->medicine_checklist->selectOne($inspection_detail->id);
                $inspection_type = OHC_TYPE_MONTHLY_MEDICINE_STORE;
                $inspection_data = json_decode($inspection_detail->inspection_data, true);
                $inspection_updated_by = GetOHCSignature($inspection_detail->updated_by, $inspection_detail->id, $inspection_type);
                $inspection_created_by = GetOHCSignature($inspection_detail->created_by, $inspection_detail->id, $inspection_type);

                $currentRow = $row;

                $logoLeftPath = public_path('assets/images/logo-dark.png');
                if (file_exists($logoLeftPath)) {
                    $sheet->mergeCells("A$currentRow:F" . ($currentRow + 2));

                    $drawing = new Drawing();
                    $drawing->setName('Left Logo');
                    $drawing->setPath($logoLeftPath);
                    $drawing->setCoordinates('B' . $currentRow);
                    $drawing->setOffsetX(100);
                    $drawing->setOffsetY(15);
                    $drawing->setWidth(70);
                    $drawing->setHeight(70);
                    $drawing->setWorksheet($sheet);

                    $range = "A$currentRow:F" . ($currentRow + 2);

                    $sheet->getStyle($range)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                }

                $sheet->mergeCells("G{$currentRow}:N" . ($currentRow + 2));
                $sheet->setCellValue("G{$currentRow}", "Monthly OHC Store Medicine Inspection Checklist");
                $sheet->getStyle("G{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $logoRightPath = public_path('assets/images/plus-image.webp');
                if (file_exists($logoRightPath)) {
                    $sheet->mergeCells("O$currentRow:S" . ($currentRow + 2));

                    $drawing = new Drawing();
                    $drawing->setName('Right Logo');
                    $drawing->setPath($logoRightPath);
                    $drawing->setCoordinates('O' . $currentRow);
                    $drawing->setOffsetX(100);
                    $drawing->setOffsetY(15);
                    $drawing->setWidth(70);
                    $drawing->setHeight(70);
                    $drawing->setWorksheet($sheet);

                    $range = "O$currentRow:S" . ($currentRow + 2);

                    $sheet->getStyle($range)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                }

                $sheet->mergeCells("A" . ($currentRow + 3) . ":J" . ($currentRow + 3));
                $richText1 = new RichText();
                $richText1->createTextRun('DATE OF INSPECTION :- ')->getFont()->setBold(true);
                $richText1->createText(Displaydateformat($inspection_detail->inspection_date));
                $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

                $sheet->mergeCells("K" . ($currentRow + 3) . ":S" . ($currentRow + 3));
                $richText2 = new RichText();
                $richText2->createTextRun('NEXT DUE :- ')->getFont()->setBold(true);
                $richText2->createText(Displaydateformat($inspection_detail->next_due));
                $sheet->getCell("K" . ($currentRow + 3))->setValue($richText2);

                $sheet->getStyle("A" . ($currentRow + 3) . ":S" . ($currentRow + 3))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $headerRow = $currentRow + 4;
                $sheet->mergeCells("A$headerRow:C$headerRow")->setCellValue("A$headerRow", "SERIAL NO");
                $sheet->mergeCells("D$headerRow:F$headerRow")->setCellValue("D$headerRow", "NAME OF THE MEDICINE");
                $sheet->mergeCells("G$headerRow:H$headerRow")->setCellValue("G$headerRow", "QUANTITY");
                $sheet->mergeCells("I$headerRow:K$headerRow")->setCellValue("I$headerRow", "EXPIRY DATE");
                $sheet->mergeCells("L$headerRow:O$headerRow")->setCellValue("L$headerRow", "INSPECTED BY");
                $sheet->mergeCells("P$headerRow:S$headerRow")->setCellValue("P$headerRow", "REMARKS");

                $sheet->getStyle("A$headerRow:S$headerRow")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $inspectionRow = $headerRow + 1;
                foreach ($inspection_data as $index => $detail) {
                    $sheet->mergeCells("A$inspectionRow:C$inspectionRow")->setCellValue("A$inspectionRow", $index);
                    $sheet->mergeCells("D$inspectionRow:F$inspectionRow")->setCellValue("D$inspectionRow", getMedicinename($detail['medicine_id']));
                    $sheet->mergeCells("G$inspectionRow:H$inspectionRow")->setCellValue("G$inspectionRow", $detail['available_quantity'] ?? '');
                    $sheet->mergeCells("I$inspectionRow:K$inspectionRow")->setCellValue("I$inspectionRow", Displaydateformat($detail['expired_date']));
                    $sheet->mergeCells("L$inspectionRow:O$inspectionRow")->setCellValue("L$inspectionRow", getUsername($detail['emp_id']));
                    $sheet->mergeCells("P$inspectionRow:S$inspectionRow")->setCellValue("P$inspectionRow", $detail['remarks'] ?? '');

                    $sheet->getStyle("A$inspectionRow:S$inspectionRow")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $inspectionRow++;
                }

                $signatureRow = $inspectionRow;
                $sheet->getRowDimension($signatureRow)->setRowHeight(80);

                $sheet->mergeCells("A$signatureRow:J$signatureRow");
                $sheet->mergeCells("K$signatureRow:S$signatureRow");

                if (file_exists($inspection_created_by)) {
                    $drawing = new Drawing();
                    $drawing->setName('Inspection and checked By');
                    $drawing->setPath($inspection_created_by);
                    $drawing->setCoordinates("D$signatureRow");
                    $drawing->setOffsetX(50);
                    $drawing->setOffsetY(10);
                    $drawing->setWidth(70);
                    $drawing->setHeight(70);
                    $drawing->setWorksheet($sheet);
                }

                $richTextSig1 = new RichText();
                $richTextSig1->createTextRun("Inspected and checked By: " . getUsername($inspection_detail->created_by))->getFont()->setBold(true);
                $sheet->getCell("A$signatureRow")->setValue($richTextSig1);

                if (file_exists($inspection_updated_by)) {
                    $drawing = new Drawing();
                    $drawing->setName('Approved By');
                    $drawing->setPath($inspection_updated_by);
                    $drawing->setCoordinates("N$signatureRow");
                    $drawing->setOffsetX(50);
                    $drawing->setOffsetY(10);
                    $drawing->setWidth(70);
                    $drawing->setHeight(70);
                    $drawing->setWorksheet($sheet);
                }

                $richTextSig2 = new RichText();
                $richTextSig2->createTextRun("Approved By: " . getUsername($inspection_detail->updated_by))->getFont()->setBold(true);
                $sheet->getCell("K$signatureRow")->setValue($richTextSig2);

                $sheet->getStyle("A$signatureRow:J$signatureRow")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);
                $sheet->getStyle("K$signatureRow:S$signatureRow")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                $sheet->getStyle("A$currentRow:S$signatureRow")->applyFromArray([
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM]],
                ]);

                $row = $signatureRow + 6;
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Monthly Medicine Store Inspection.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
