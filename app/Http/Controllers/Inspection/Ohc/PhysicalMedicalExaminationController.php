<?php

namespace App\Http\Controllers\Inspection\Ohc;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\Inspection\Safety\SafetyInspection;
use Illuminate\Support\Facades\Auth;
use App\Models\Inspection\Master\Shift;
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
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Ohc\Master\FamilyHistory;
use App\Models\Inspection\Ohc\Master\PersonalDetails;
use App\Models\Inspection\ohc\OHCHygieneCleaningChecklist;
use App\Models\Inspection\Ohc\PhysicalHealthExamination;
use Illuminate\Support\Facades\Mail;
use Mpdf\Tag\Dd;

class PhysicalMedicalExaminationController extends Controller
{
    private $ohc_hygiene;
    private $shift;
    private $signature;
    private $document_reference;
    private $personalDetails;
    private $familyHistory;
    private $physicalHealth;


    public function __construct()
    {
        $this->ohc_hygiene = new OHCHygieneCleaningChecklist();
        $this->shift = new Shift();
        $this->signature = new OhcSignature();
        $this->document_reference = new InspectionStaticDocno();
        $this->personalDetails = new PersonalDetails();
        $this->familyHistory = new FamilyHistory();
        $this->physicalHealth = new PhysicalHealthExamination();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->physicalHealth->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type = '1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type = '0'>In-Active</span>";
                            }
                            // }
                            return $text;
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('date', function ($row) {
                            return Displaydateformat($row->date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('unit_id', function ($row) {
                            return getUnitname($row->unit_id);
                        })
                        ->addColumn('department_id', function ($row) {
                            return getDepartment($row->department_id);
                        })
                        ->addColumn('checklist_status', function ($row) {
                            $text = '';
                            switch ($row->checklist_status) {
                                case CLEANER_SUBMITTED_THE_CHECKLIST:
                                    $text = "<span class='badge bg-primary rounded' style='font-size: 1.0em;'>Waiting For Nursing Officer Action</span>";
                                    break;
                                case NURSING_OFFICER_SUBMITTED_THE_CHECKLIST:
                                    $text = "<span class='badge bg-success rounded' style='font-size: 1.0em;'>Inspection Approved</span>";
                                    break;
                                case NURSING_OFFICER_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>Inspection Rejected</span>";
                                    break;
                                default:
                                    $text = "<span class='badge rounded-pill text-bg-warning'>Unknown</span>";
                            }
                            return $text;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('ohc/physical-medical-examination/yearly/view/' . encryptId($row->id)) . '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';

                            $btn .= '<a href="' . admin_url('ohc/physical-medical-examination/yearly/generalpdf/' . encryptId($row->id)) . '"class="me-1" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';

                            $btn .= '<a href="' . admin_url('ohc/physical-medical-examination/yearly/generalexcel/' . encryptId($row->id)) . '"class="me-1" title="EXCEL">
                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                     </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'checklist_status', 'date'])
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
        $shift  = $this->shift->select('id', 'shift')->where('status', '1')->get();

        $data = array(
            'shifts' => $shift,
        );
        return view('inspection.inspection_ohc.physical_medical_examination.list', $data);
    }

    public function add(Request $request)
    {
        try {

            $document_no = $this->document_reference->selectUsingName('PhysicalHealthExamination');
            $personalDetails = $this->personalDetails->personalDetails();
            $familyHistory = $this->familyHistory->familyHistory();
            $check_points = getCheckListQuestion(OHC_PHYSICAL_HEALTH_EXAMINATION);

            $data = array(

                'document_no' => $document_no,
                'personalDetails' => $personalDetails,
                'check_points' => $check_points,
                'familyHistory' => $familyHistory,

            );
            return view('inspection.inspection_ohc.physical_medical_examination.add', $data);
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/physical-medical-examination/yearly/list'));
        }
    }

    public function store(Request $request)
    {
        try {


          $document_no = $this->document_reference->selectUsingName('PhysicalHealthExamination');
            $physicalHealth = $this->physicalHealth->store($document_no);
            $signature_update = $this->signature->requestorsignatureUpload(OHC_TYPE_PHYSICAL_HEALTH_EXAMINATION, $physicalHealth->id);

            Session::flash('success', __('common.created_msg'));
            return redirect(admin_url('ohc/physical-medical-examination/yearly/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/physical-medical-examination/yearly/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $physicalHealth = $this->physicalHealth->selectOne($id);
            $document_no = $this->document_reference->selectOne($physicalHealth->document_reference_id);

            $fmo_signature = GetOHCSignature($physicalHealth->created_by, $id, OHC_TYPE_PHYSICAL_HEALTH_EXAMINATION);

            $personalDetails = $this->personalDetails->personalDetails();
            $familyHistory = $this->familyHistory->familyHistory();
            $check_points = getCheckListQuestion(OHC_PHYSICAL_HEALTH_EXAMINATION);
            $data = [
                'physicalHealth' => $physicalHealth,
                'document_no' => $document_no,
                'fmo_signature' => $fmo_signature,
                'personalDetails' => $personalDetails,
                'check_points' => $check_points,
                'familyHistory' => $familyHistory,


            ];
            return view('inspection.inspection_ohc.physical_medical_examination.view', $data);
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/physical-medical-examination/yearly/list'));
        }
    }
    public function approval(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->ohc_hygiene->selectOne($id);
            $cleaner_signature = GetOHCSignature($inspection_details->created_by, $inspection_details->id, DAILY_OHC_HYGIENE_CLEANING_CHECKLIST);
            $nursing_signature = GetOHCSignature($inspection_details->updated_by, $inspection_details->id, DAILY_OHC_HYGIENE_CLEANING_CHECKLIST);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);

            $data = [
                'inspection_details' => $inspection_details,
                'cleaner_signature' => $cleaner_signature,
                'nursing_signature' => $nursing_signature,
                'document_no' => $document_no,

            ];
            return view('inspection.inspection_ohc.physical_medical_examination.approval', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/physical-medical-examination/yearly/list'));
        }
    }

    public function approvalSubmit(Request $request)
    {
        try {

            $id = decryptId($request->id);
            // dd($id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->capa_remarks;
            $signature_update = $this->signature->signatureUpload(DAILY_OHC_HYGIENE_CLEANING_CHECKLIST);
            if ($status == 1) {
                $message = 'OHC HYGIENE CLEANING CHECKLIST - APPROVED';
                $to_status = NURSING_OFFICER_SUBMITTED_THE_CHECKLIST;
            } else {
                $message = 'OHC HYGIENE CLEANING CHECKLIST - REJECTED';
                $to_status = NURSING_OFFICER_REJECTED;
            }

            $eye_wash_inspection = $this->ohc_hygiene->approvalSubmit($id, $to_status, $remarks);

            $inspection_details = $this->ohc_hygiene->selectOne($id);
            $ehsOfficer = [$inspection_details->created_by];
            $web_link =   admin_url('ohc/physical-medical-examination/yearly/view/' . encryptId($inspection_details->id));
            $mailsubject = 'OHC HYGIENE CLEANING CHECKLIST';
            $notificationData = array(
                'notification_type' => OHC_INSPECTION,
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

            $title = 'OHC HYGIENE CLEANING CHECKLIST';
            $email_id = getUseremail($ehsOfficer);
            $url = admin_url('ohc/physical-medical-examination/yearly/view/' . encryptId($inspection_details->id));
            $details = array(
                'safety_type' => 'OHC HYGIENE CLEANING CHECKLIST',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new SafetyInspection($details));
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/physical-medical-examination/yearly/list'));
        } catch (Exception $ex) {
            report($ex);
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/physical-medical-examination/yearly/list'));
        }
    }


    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $inspection_detail = $this->ohc_hygiene->selectOne($id);
            $inspection_type = DAILY_OHC_HYGIENE_CLEANING_CHECKLIST;
            $nursing_signature = GetOHCSignature($inspection_detail->updated_by, $inspection_detail->id, $inspection_type);
            $cleaner_signature = GetOHCSignature($inspection_detail->created_by, $inspection_detail->id, $inspection_type);

            for ($i = 1; $i <= 50; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $sheet->mergeCells("A1:F3");
            $sheet->mergeCells("G1:N3");
            $sheet->mergeCells("O1:T3");

            $sheet->getStyle("A1:T3")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            if (file_exists(public_path('assets/images/logo-dark.png'))) {
                $drawing = new Drawing();
                $drawing->setName('Left Logo');
                $drawing->setPath(public_path('assets/images/logo-dark.png'));
                $drawing->setCoordinates('B1');
                $drawing->setOffsetX(100);
                $drawing->setOffsetY(15);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
            }

            $sheet->setCellValue("G1", "DAILY OHC HYGIENE CLEANING CHECKLIST - PN INTERNATIONAL PNT. LTD.");
            $sheet->getStyle("G1")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);

            if (file_exists(public_path('assets/images/plus-image.webp'))) {
                $drawing = new Drawing();
                $drawing->setName('Right Logo');
                $drawing->setPath(public_path('assets/images/plus-image.webp'));
                $drawing->setCoordinates('P1');
                $drawing->setOffsetX(100);
                $drawing->setOffsetY(15);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
            }

            $sheet->mergeCells("A4:B5")->setCellValue("A4", "DATE");
            $sheet->mergeCells("C4:D5")->setCellValue("C4", "SHIFT");
            $sheet->mergeCells("E4:J5")->setCellValue("E4", "DESCRIPTION");
            $sheet->mergeCells("K4:L4")->setCellValue("K4", "CLEANING AND SANITIZATION");
            $sheet->setCellValue("K5", "YES");
            $sheet->setCellValue("L5", "NO");
            $sheet->mergeCells("M4:N5")->setCellValue("M4", "SIGNATURE OF CLEANER");
            $sheet->mergeCells("O4:P5")->setCellValue("O4", "SIGNATURE OF NURSING OFFICER");
            $sheet->mergeCells("Q4:R5")->setCellValue("Q4", "REMARKS");
            $sheet->mergeCells("S4:T5")->setCellValue("S4", "NURSING OFFICER REMARKS");

            $sheet->getStyle("A4:T5")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ],
            ]);

            $row = 6;
            $sheet->mergeCells("A{$row}:B{$row}")->setCellValue("A{$row}", Displaydateformat($inspection_detail->issue_date ?? ''));
            $sheet->mergeCells("C{$row}:D{$row}")->setCellValue("C{$row}", getShiftname($inspection_detail->shift_id ?? null));
            $sheet->mergeCells("E{$row}:J{$row}")->setCellValue("E{$row}", $inspection_detail->inspection_question ?? 'INSPECTION HAS NOT BEEN VERIFIED YET');

            if ($inspection_detail->inspection_value == 1) {
                $sheet->setCellValue("K{$row}", '✔');
                $sheet->getStyle("K{$row}")->applyFromArray(['font' => ['color' => ['rgb' => '000000']]]);
            } else {
                $sheet->setCellValue("L{$row}", 'X');
                $sheet->getStyle("L{$row}")->applyFromArray(['font' => ['color' => ['rgb' => 'FF0000']]]);
            }

            $sheet->mergeCells("M{$row}:N{$row}");
            $sheet->mergeCells("O{$row}:P{$row}");

            if (!empty($cleaner_signature)) {
                $drawing = new Drawing();
                $drawing->setName('Cleaner Signature');
                $drawing->setPath($cleaner_signature);
                $drawing->setCoordinates("M{$row}");
                $drawing->setOffsetX(10);
                $drawing->setOffsetY(10);
                $drawing->setWidth(50);
                $drawing->setHeight(50);
                $drawing->setWorksheet($sheet);
                $sheet->getRowDimension($row)->setRowHeight($drawing->getHeight() + 20);
            }

            if (!empty($nursing_signature)) {
                $drawing = new Drawing();
                $drawing->setName('Nursing Officer Signature');
                $drawing->setPath($nursing_signature);
                $drawing->setCoordinates("O{$row}");
                $drawing->setOffsetX(10);
                $drawing->setOffsetY(10);
                $drawing->setWidth(50);
                $drawing->setHeight(50);
                $drawing->setWorksheet($sheet);
                $sheet->getRowDimension($row)->setRowHeight($drawing->getHeight() + 20);
            } else {
                $sheet->setCellValue("O{$row}", 'INSPECTION HAS NOT BEEN VERIFIED YET');
            }

            $sheet->mergeCells("Q{$row}:R{$row}")->setCellValue("Q{$row}", $inspection_detail->cleaner_remarks ?? '');
            $sheet->mergeCells("S{$row}:T{$row}")->setCellValue("S{$row}", $inspection_detail->nursing_officer_remarks ?? 'INSPECTION HAS NOT BEEN VERIFIED YET');

            $sheet->getStyle("A{$row}:T{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Daily_OHC_Hygiene_Checklist.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            dd($e);
            return back()->with('error', $e->getMessage());
        }
    }


    public function ExportExcel()
    {
        try {
            $allData = $this->ohc_hygiene->exportdata();
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            for ($i = 1; $i <= 50; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $sheet->mergeCells("A1:F3");
            $sheet->mergeCells("G1:N3");
            $sheet->mergeCells("O1:T3");

            $sheet->getStyle("A1:T3")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            if (file_exists(public_path('assets/images/logo-dark.png'))) {
                $drawing = new Drawing();
                $drawing->setName('Left Logo');
                $drawing->setPath(public_path('assets/images/logo-dark.png'));
                $drawing->setCoordinates("B1");
                $drawing->setOffsetX(100);
                $drawing->setOffsetY(15);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
            }

            $sheet->setCellValue("G1", "DAILY OHC HYGIENE CLEANING CHECKLIST - PN INTERNATIONAL PNT. LTD.");
            $sheet->getStyle("G1")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);

            if (file_exists(public_path('assets/images/plus-image.webp'))) {
                $drawing = new Drawing();
                $drawing->setName('Right Logo');
                $drawing->setPath(public_path('assets/images/plus-image.webp'));
                $drawing->setCoordinates("O1");
                $drawing->setOffsetX(100);
                $drawing->setOffsetY(15);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
            }

            $sheet->mergeCells("A4:B5")->setCellValue("A4", "DATE");
            $sheet->mergeCells("C4:D5")->setCellValue("C4", "SHIFT");
            $sheet->mergeCells("E4:J5")->setCellValue("E4", "DESCRIPTION");
            $sheet->mergeCells("K4:L4")->setCellValue("K4", "CLEANING AND SANITIZATION");
            $sheet->setCellValue("K5", "YES");
            $sheet->setCellValue("L5", "NO");
            $sheet->mergeCells("M4:N5")->setCellValue("M4", "SIGNATURE OF CLEANER");
            $sheet->mergeCells("O4:P5")->setCellValue("O4", "SIGNATURE OF NURSING OFFICER");
            $sheet->mergeCells("Q4:R5")->setCellValue("Q4", "REMARKS");
            $sheet->mergeCells("S4:T5")->setCellValue("S4", "NURSING OFFICER REMARKS");

            $sheet->getStyle("A4:T5")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ],
            ]);

            $inspectionRow = 6;

            $sheet->getColumnDimension('M')->setWidth(14);
            $sheet->getColumnDimension('N')->setWidth(14);


            foreach ($allData as $inspection_detail) {
                $inspection_detail = $this->ohc_hygiene->selectOne($inspection_detail->inspection_id);
                $inspection_type = DAILY_OHC_HYGIENE_CLEANING_CHECKLIST;
                $nursing_signature = GetOHCSignature($inspection_detail->updated_by, $inspection_detail->id, $inspection_type);
                $cleaner_signature = GetOHCSignature($inspection_detail->created_by, $inspection_detail->id, $inspection_type);

                $sheet->mergeCells("A$inspectionRow:B$inspectionRow")->setCellValue("A$inspectionRow", $inspection_detail->issue_date);
                $sheet->mergeCells("C$inspectionRow:D$inspectionRow")->setCellValue("C$inspectionRow", getShiftname($inspection_detail->shift_id));
                $sheet->mergeCells("E$inspectionRow:J$inspectionRow")->setCellValue("E$inspectionRow", $inspection_detail->inspection_question);

                if ($inspection_detail->inspection_value == 1) {
                    $sheet->setCellValue("K{$inspectionRow}", '✔');
                    $sheet->getStyle("K{$inspectionRow}")->applyFromArray(['font' => ['color' => ['rgb' => '000000']]]);
                } else {
                    $sheet->setCellValue("L{$inspectionRow}", 'X');
                    $sheet->getStyle("L{$inspectionRow}")->applyFromArray(['font' => ['color' => ['rgb' => 'FF0000']]]);
                }

                $sheet->mergeCells("Q$inspectionRow:R$inspectionRow")->setCellValue("Q$inspectionRow", $inspection_detail->cleaner_remarks);
                $sheet->mergeCells("S$inspectionRow:T$inspectionRow")->setCellValue("S$inspectionRow", $inspection_detail->nursing_officer_remarks);

                $sheet->mergeCells("M{$inspectionRow}:N{$inspectionRow}");
                $sheet->mergeCells("O{$inspectionRow}:P{$inspectionRow}");

                if (file_exists($cleaner_signature)) {
                    $drawing = new Drawing();
                    $drawing->setPath($cleaner_signature);
                    $drawing->setCoordinates("M{$inspectionRow}");
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);
                    $drawing->setWidth(50);
                    $drawing->setHeight(50);
                    $drawing->setWorksheet($sheet);
                    $sheet->getRowDimension($inspectionRow)->setRowHeight($drawing->getHeight() + 20);
                }

                if (file_exists($nursing_signature)) {
                    $drawing = new Drawing();
                    $drawing->setName('Nursing Officer Signature');
                    $drawing->setPath($nursing_signature);
                    $drawing->setCoordinates("O$inspectionRow");
                    $drawing->setOffsetX(10);
                    $drawing->setOffsetY(10);
                    $drawing->setWidth(50);
                    $drawing->setHeight(50);
                    $drawing->setWorksheet($sheet);
                    $sheet->getRowDimension($inspectionRow)->setRowHeight($drawing->getHeight() + 20);
                }


                $sheet->getStyle("A$inspectionRow:T$inspectionRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $inspectionRow++;
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'OHC Hygiene Cleaning Checklist.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            dd($e);
            return back()->with('error', 'Something went wrong');
        }
    }




    public function ExportPdf(Request $request)
    {

        try {

            ini_set("pcre.backtrack_limit", "5000000");

            $allData = $this->ohc_hygiene->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } else if (count($allData) > 20) {
                return redirect()->back()->with('error', __('inspection.excess_error'));
            }



            $data = array(
                'content' => $allData,
                'pagetitle' => "OHC HYGIENE CLEANING CHECKLIST",
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

            $view = view('inspection.inspection_ohc.physical_medical_examination.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "OHC HYGIENE CLEANING CHECKLIST.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            report($ex);
        }
    }


    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $inspection_details = $this->ohc_hygiene->selectone($id);
                $cleaner_signature = GetOHCSignature($inspection_details->created_by, $inspection_details->id, DAILY_OHC_HYGIENE_CLEANING_CHECKLIST);
                $nursing_signature = GetOHCSignature($inspection_details->updated_by, $inspection_details->id, DAILY_OHC_HYGIENE_CLEANING_CHECKLIST);
                $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);

                $data = [
                    'cleaner_signature' => $cleaner_signature,
                    'nursing_signature' => $nursing_signature,
                    'inspection_details' => $inspection_details,
                    'pagetitle' => "OHC Hygiene Inspection Checklist",
                    'document_no' => $document_no,
                ];
            }
            $property = [
                'tempDir' => 'public/pdf/temp/',
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $html = view('inspection.inspection_ohc.physical_medical_examination.generalPdf', $data)->render();

            $mpdf->WriteHTML($html);

            $filename = "OHC Hygiene Inspection Checklist.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }
}
