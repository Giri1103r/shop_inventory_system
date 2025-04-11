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
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Mail\Inspection\Safety\SafetyInspection;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Ohc\Master\FirstAidEquipment;
use App\Models\Inspection\Ohc\FirstAidMedicineInspection;

class FirstAidMedicineInspectionController extends Controller
{
    private $medicine_checklist;
    private $medicine;
    private $signature;
    private $document_reference;


    public function __construct()
    {
        $this->medicine_checklist = new FirstAidMedicineInspection();
        $this->medicine = new FirstAidEquipment();
        $this->signature = new OhcSignature();
        $this->document_reference = new InspectionStaticDocno();
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
                            $btn = '<a href="' . admin_url('ohc/first-aid/opd-medicine-inspection/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';

                            if ($row->inspection_status == OBSERVATION_PENDING &&  isAdmin()) {
                                $btn .= '<a href="' . admin_url('ohc/first-aid/opd-medicine-inspection/approval/' . encryptId($row->id)) . '" class="" title="Action"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }

                            $btn .= '<a href="' . admin_url('ohc/first-aid/opd-medicine-inspection/exportViewpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';

                            $btn .= '<a href="' . admin_url('ohc/first-aid/opd-medicine-inspection/generalexcel/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
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

        return view('inspection.inspection_ohc.first_aid_inspection.list');
    }

    public function Add(Request $request)
    {
        try {

            $medicines = $this->medicine->getFirstAidData();
            $document_no = $this->document_reference->selectUsingName('OHCFirstAidOPDMedicineInspection');

            $data = array(
                'medicines' => $medicines,
                'document_no' => $document_no,
            );
            return view('inspection.inspection_ohc.first_aid_inspection.add', $data);
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/first-aid/opd-medicine-inspection/list'));
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
            $inspection_type = OHC_OPD_MEDICINE_INSPECTION;
            $inspection_details = $this->medicine_checklist->selectOne($store->id);
            $files = $this->signature->requestorsignatureUpload($inspection_type, $inspection_details->id);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'OHC';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Safety Walk Observation - Observation Has been Created",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_details->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('ohc/first-aid/opd-medicine-inspection/view/' . encryptId($inspection_details->id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'First-Aid Medicine Inspection Checklist- Observation has been Created';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('ohc/first-aid/opd-medicine-inspection/approval/' . encryptId($inspection_details->id));
                $details = array(
                    'safety_type' => 'Safety Walk Observation',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }

            Session::flash('success', 'Your data has been added successfully');
            return redirect(admin_url('ohc/first-aid/opd-medicine-inspection/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/first-aid/opd-medicine-inspection/list'));
        }
    }

    public function View(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_details = $this->medicine_checklist->selectOne($id);
            $inspection_type = OHC_OPD_MEDICINE_INSPECTION;
            $inspection_data = json_decode($inspection_details->inspection_data, true);
            $inspection_file = GetOHCSignature($inspection_details->created_by, $inspection_details->id, $inspection_type);
            $verified_by = GetOHCSignature($inspection_details->updated_by, $inspection_details->id, $inspection_type);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);

            $data = array(
                'inspection_details' => $inspection_details,
                'inspection_file' => $inspection_file,
                'inspection_data' => $inspection_data,
                'verified_by' => $verified_by,
                'document_no' => $document_no,
            );


            return view('inspection.inspection_ohc.first_aid_inspection.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/first-aid/opd-medicine-inspection/list'));
        }
    }

    public function ExportExcel()
    {
        try {
            $allData = $this->medicine_checklist->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Date of Inspection',
                'Next Due',
                'Inspection Status',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  Displaydateformat($data->inspection_date);
                $export[] =  Displaydateformat($data->next_due);
                $export[] =  getObservationStatus($data->inspection_status);
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);
                $exportData[] = $export;
                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Monthly OHC First-Aid Medicine Inspection Checklist.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/first-aid/opd-medicine-inspection/list'));
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
                'pagetitle' => "Monthly OHC First-Aid Medicine Inspection Checklist",
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

            $view = view('inspection.inspection_ohc.first_aid_inspection.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Monthly OHC First-Aid Medicine Inspection Checklist.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/first-aid/opd-medicine-inspection/list'));
        }
    }

    public function ExportViewPDF(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_detail = $this->medicine_checklist->selectOne($id);
            $inspection_type = OHC_OPD_MEDICINE_INSPECTION;
            $inspection_file = $this->signature->getFiles($id, $inspection_type);
            $inspection_data = json_decode($inspection_detail->inspection_data, true);
            $inspection_updated_by = GetOHCSignature($inspection_detail->updated_by, $inspection_detail->id, $inspection_type);
            $inspection_created_by = GetOHCSignature($inspection_detail->created_by, $inspection_detail->id, $inspection_type);
            $document_no = $this->document_reference->selectOne($inspection_detail->document_reference_id);

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
                'pagetitle' => "Monthly OHC First-Aid Medicine Inspection Checklist",
                'inspection_data' => $inspection_data,
                'inspection_created_by' => $inspection_created_by,
                'inspection_updated_by' => $inspection_updated_by,
                'document_no' => $document_no,
            );


            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $html = view('inspection.inspection_ohc.first_aid_inspection.viewpdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Monthly OHC First-Aid Medicine Inspection Checklist.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/first-aid/opd-medicine-inspection/list'));
        }
    }

    public function approval(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_details = $this->medicine_checklist->selectOne($id);
            $inspection_type = OHC_OPD_MEDICINE_INSPECTION;
            $inspection_file = GetOHCSignature($inspection_details->created_by, $inspection_details->id, $inspection_type);
            $inspection_data = json_decode($inspection_details->inspection_data, true);
            $data = array(
                'inspection_details' => $inspection_details,
                'inspection_file' => $inspection_file,
                'inspection_data' => $inspection_data,

            );

            return view('inspection.inspection_ohc.first_aid_inspection.approval', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/first-aid/opd-medicine-inspection/list'));
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
            $signature_update = $this->signature->signatureUpload(OHC_OPD_MEDICINE_INSPECTION);
            $ehsOfficer = [$inspection_details->created_by];

            if ($status == 1) {
                $message = 'First-Aid Medicine Inspection Checklist - APPROVED';
                $to_status = OBSERVATION_APPROVED;
            } else {
                $message = 'First-Aid Medicine Inspection Checklist - REJECTED';
                $to_status = OBSERVATION_REJECTED;
            }
            $web_link =   admin_url('ohc/first-aid/opd-medicine-inspection/view/' . encryptId($inspection_details->id));
            $mailsubject = 'OHC';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 1,
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

            $title = 'First-Aid Medicine Inspection Checklist - Observation Status';
            $email_id = getUseremail($ehsOfficer);
            $url = admin_url('ohc/first-aid/opd-medicine-inspection/view/' . encryptId($inspection_details->id));
            $details = array(
                'safety_type' => 'First-Aid Medicine Inspection Checklist',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new SafetyInspection($details));

            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/first-aid/opd-medicine-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('ohc/first-aid/opd-medicine-inspection/list'));
        }
    }

    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $inspection_detail = $this->medicine_checklist->selectOne($id);
            $inspection_type = OHC_OPD_MEDICINE_INSPECTION;
            $inspection_data = json_decode($inspection_detail->inspection_data, true);
            $document_no = $this->document_reference->selectOne($inspection_detail->document_reference_id);

            // Insert logo image
            $logoPath = public_path('assets/images/logo-dark.png');
            if (file_exists($logoPath)) {
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Company Logo');
                $drawing->setPath($logoPath);
                $drawing->setCoordinates('A1');
                $drawing->setHeight(60); // optional: set height
                $drawing->setWorksheet($sheet);

                // Adjust cell A1 size for logo
                $sheet->getColumnDimension('A')->setWidth(15);
                $sheet->getRowDimension(1)->setRowHeight(60);
            }

            // Title section
            $sheet->mergeCells("H1:N3");
            $sheet->setCellValue("H1", "Monthly OHC First-Aid Medicine Inspection Checklist");
            $sheet->getStyle("H1")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FF0000']
                ]
            ]);

            $sheet->mergeCells("O1:X1")->setCellValue("O1", "Doc. No.");
            $sheet->mergeCells("O2:X2")->setCellValue("O2", "Issue Dt.");
            $sheet->mergeCells("O3:X3")->setCellValue("O3", "Rev. & Dt.");

            $sheet->getStyle("O1:X3")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['argb' => '000000']]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);

            $date_of_inspection = Displaydateformat($inspection_detail->inspection_date);
            $next_due = Displaydateformat($inspection_detail->next_due);

            $sheet->mergeCells("A4:L4");
            $richText1 = new RichText();
            $richText1->createTextRun('DATE OF INSPECTION :- ')->getFont()->setBold(true);
            $richText1->createText($date_of_inspection);
            $sheet->getCell("A4")->setValue($richText1);

            $sheet->mergeCells("M4:X4");
            $richText2 = new RichText();
            $richText2->createTextRun('NEXT DUE :- ')->getFont()->setBold(true);
            $richText2->createText($next_due);
            $sheet->getCell("M4")->setValue($richText2);

            $sheet->getStyle("A4:X4")->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THICK]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);

            // Table Header
            $sheet->mergeCells("A5:C5")->setCellValue("A5", "SERIAL NO");
            $sheet->mergeCells("D5:H5")->setCellValue("D5", "NAME OF THE MEDICINE");
            $sheet->mergeCells("I5:K5")->setCellValue("I5", "QUANTITY");
            $sheet->mergeCells("L5:N5")->setCellValue("L5", "EXPIRY DATE");
            $sheet->mergeCells("O5:S5")->setCellValue("O5", "INSPECTED BY");
            $sheet->mergeCells("T5:X5")->setCellValue("T5", "REMARKS");

            $sheet->getStyle("A5:X5")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THICK]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);

            // Table Data
            $row = 6;
            foreach ($inspection_data as $index => $detail) {
                $sheet->mergeCells("A$row:C$row")->setCellValue("A$row", $index);
                $sheet->mergeCells("D$row:H$row")->setCellValue("D$row", getMedicinename($detail['medicine_id']));
                $sheet->mergeCells("I$row:K$row")->setCellValue("I$row", $detail['available_quantity'] ?? '');
                $sheet->mergeCells("L$row:N$row")->setCellValue("L$row", Displaydateformat($detail['expired_date']));
                $sheet->mergeCells("O$row:S$row")->setCellValue("O$row", getUsername($detail['emp_id']));
                $sheet->mergeCells("T$row:X$row")->setCellValue("T$row", $detail['remarks'] ?? '');

                $sheet->getStyle("A$row:X$row")->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THICK]
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);

                $row++;
            }

            // Export
            $writer = new Xlsx($spreadsheet);
            $fileName = 'ohc-medicine-checklist.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
