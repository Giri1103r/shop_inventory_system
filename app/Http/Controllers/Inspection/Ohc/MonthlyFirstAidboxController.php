<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use App\Mail\Inspection\Safety\SafetyInspection;
use App\Models\Inspection\Master\ChecklistOptionType;
use App\Models\Inspection\Master\ChecklistSubTypeData;
use App\Models\Inspection\Master\ChecklistSubTypeDataName;
use App\Models\Inspection\Master\ChecklistType;
use App\Models\Inspection\Master\Frequency;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\Ohc\InspectionOhcStatuslog;
use App\Models\Inspection\Ohc\MonthlyFirstAidbox;
use App\Models\Inspection\Ohc\MonthlyFirstAidboxChecklist;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Models\Inspection\Ohc\WeeklyAmbulance;
use App\Models\Inspection\Ohc\WeeklyAmbulanceChecklist;
use App\Models\Master\Department;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use App\Models\UploadLog;
use App\Models\User;
use Exception;
use Illuminate\Container\Attributes\Database;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inspection\InspectionStaticDocno;



use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class MonthlyFirstAidboxController extends Controller
{

    private $OhcDetails;
    private $user;
    private $monthly_first_aid;
    private $frequency;
    private $upload_log;
    private $unit;
    private $shift;
    private $department;
    private $checklist_type;
    private $sub_type_data;
    private $sub_type_data_name;
    private $questionery;
    private $signature;
    private $location;
    private $inspection_ohc_status_log;
    private $monthly_first_aid_audit_checklist;
    private $document_reference;
    public function __construct()
    {

        $this->upload_log = new UploadLog();
        $this->unit = new Unit();
        $this->department = new Department();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->monthly_first_aid = new MonthlyFirstAidbox();
        $this->monthly_first_aid_audit_checklist = new MonthlyFirstAidboxChecklist();
        $this->user = new User();
        $this->frequency = new Frequency();
        $this->signature = new OhcSignature();
        $this->inspection_ohc_status_log = new InspectionOhcStatuslog();
        $this->document_reference = new InspectionStaticDocno();
    }
    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {

                    $data = $this->monthly_first_aid->list();

                    $datatables = Datatables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->inspection_id) . "' data-type='1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->inspection_id) . "' data-type='0'>In-Active</span>";
                            }
                            return $text;
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('shift', function ($row) {
                            return getShift($row->shift);
                        })
                        ->addColumn('frequency', function ($row) {
                            return getFrequencyname($row->frequency);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })

                        ->addColumn('action', function ($row) {
                            $btn = '';

                            $btn .=  '<a href="' . admin_url('ohc/first-aid-box/monthly-audit/view/' . encryptId($row->inspection_id)) . '" class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a>';



                            $btn .= '<a href="' . admin_url('ohc/first-aid-box/monthly-audit/generalpdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';
                            $btn .= '<a href="' . admin_url('ohc/first-aid-box/monthly-audit/generalExcel/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                     </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'approve_status'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);

                    return $datatables;
                } catch (Exception $ex) {
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
        $shift = $this->shift->getShiftname();
        $frequency = $this->frequency->getFrequency();
        $data = array(

            'shift' => $shift,
            'frequency' => $frequency,


        );
        return view('inspection.inspection_ohc.monthly_first_aid_audit_checklist.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $shift = $this->shift->getShiftname();
            $frequency = $this->frequency->getFrequency();
            $location = $this->location->getLocationname();
            $signature_upload = $this->user->getSignature();
            $document_no = $this->document_reference->selectUsingName('MonthlyFirstAidBoxAuditChecklist');
            $data = array(
                'unit' => $unit,
                'shift' => $shift,
                'frequency' => $frequency,
                'location' => $location,
                'document_no' => $document_no,
                'signature_upload' => $signature_upload,

            );
            return view('inspection.inspection_ohc.monthly_first_aid_audit_checklist.add', $data);
        } catch (Exception $ex) {
             report($ex);
        }
    }
    // store
    public function Store(Request $request)
    {
        try {

            $store = $this->monthly_first_aid->store();
            $inspection_type = OHC_TYPE_MONTHLY_FIRST_AID_BOX_AUDIT_INSPECTION_CHECKLIST;
            $monthly_first_aid_audit_checklist = $this->monthly_first_aid_audit_checklist->store($store);
            $id = $store->id;
            $files = $this->signature->requestorsignatureUpload($inspection_type, $id);

            Session::flash('success', 'Your data has been added successfully');
            return redirect(admin_url('ohc/first-aid-box/monthly-audit/list'));
        } catch (Exception $ex) {

             report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/first-aid-box/monthly-audit/list'));
        }
    }
    // view

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $monthly_first_aid = $this->monthly_first_aid->selectOne($id);
                $monthly_first_aid_audit_checklist = $this->monthly_first_aid_audit_checklist->selectOne($id);
            }

            $requestorsignature =  $monthly_first_aid->created_by;

            $type = OHC_TYPE_MONTHLY_FIRST_AID_BOX_AUDIT_INSPECTION_CHECKLIST;
            $requestor_signature = $this->signature->requestorSignature($id, $requestorsignature, $type);
            $signatureview = $this->user->where('id', $requestorsignature)->first();

            $document_no = $this->document_reference->selectUsingName('MonthlyFirstAidBoxAuditChecklist');
            $data = [
                'monthly_first_aid' => $monthly_first_aid,
                'monthly_first_aid_audit_checklist' => $monthly_first_aid_audit_checklist,
                'signatureview' => $signatureview,
                'document_no' => $document_no,
                'pagetitle' => "Monthly First Aid Audit Checklist Inspection",
            ];
            return view('inspection.inspection_ohc.monthly_first_aid_audit_checklist.view', $data);
        } catch (Exception $ex) {
             report($ex);
        }
    }
    // general pdf

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $monthly_first_aid = $this->monthly_first_aid->selectOne($id);
                $monthly_first_aid_audit_checklist = $this->monthly_first_aid_audit_checklist->selectOne($id);
            }
            $requestorsignature =  $monthly_first_aid->created_by;

            $type = OHC_TYPE_MONTHLY_FIRST_AID_BOX_AUDIT_INSPECTION_CHECKLIST;
            $requestor_signature = $this->signature->requestorSignature($id, $requestorsignature, $type);
            $signatureview = $this->user->where('id', $requestorsignature)->first();

            $document_no = $this->document_reference->selectUsingName('MonthlyFirstAidBoxAuditChecklist');
            $data = [
                'monthly_first_aid' => $monthly_first_aid,
                'monthly_first_aid_audit_checklist' => $monthly_first_aid_audit_checklist,
                'signatureview' => $signatureview,
                'document_no' => $document_no,
                'pagetitle' => "Monthly First Aid Audit Checklist Inspection",
            ];

            $property = [
                'tempDir' => storage_path('app/public/pdf/temp/'), // Corrected path
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            // Load HTML from the Blade view
            $html = view('inspection.inspection_ohc.monthly_first_aid_audit_checklist.viewpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "Monthly First Aid Audit Checklist Inspection.pdf";

            return $mpdf->Output($filename, 'i');
        } catch (\Exception $ex) {
             report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aid-box/monthly-audit/list'));
        }
    }

    // Excel
    public function ExportExcel(Request $request)
    {

        try {

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $allData = $this->monthly_first_aid->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }


            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $row = 1;

            foreach ($allData as $details) {
                $monthly_first_aid = $this->monthly_first_aid->selectOne($details->id);
                $document_no = $this->document_reference->selectOne($details->document_reference_id);
                $monthly_first_aid_audit_checklist = $this->monthly_first_aid_audit_checklist->selectOne($details->id);
                $safetyofficerSignature = GetOHCSignature($monthly_first_aid->approved_by, $details->id, OHC_TYPE_MONTHLY_FIRST_AID_BOX_AUDIT_INSPECTION_CHECKLIST);
                $RequestorSignature = GetOHCSignature($monthly_first_aid->created_by, $details->id, OHC_TYPE_MONTHLY_FIRST_AID_BOX_AUDIT_INSPECTION_CHECKLIST);

                $currentRow = $row;
                $logoLeftPath = public_path('assets/images/logo-dark.png');
                if (file_exists($logoLeftPath)) {
                    $sheet->mergeCells("A$currentRow:F" . ($currentRow + 2));

                    $drawing = new Drawing();
                    $drawing->setName('Left Logo');
                    $drawing->setPath($logoLeftPath);
                    $drawing->setCoordinates("B$currentRow");
                    $drawing->setOffsetX(100);
                    $drawing->setOffsetY(15);
                    $drawing->setWidth(70);
                    $drawing->setHeight(70);
                    $drawing->setWorksheet($sheet);

                    $range = "A$currentRow:F" . ($currentRow + 2);
                    $sheet->getStyle($range)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                }

                // Title Section
                $sheet->mergeCells("G{$currentRow}:M" . ($currentRow + 2));
                $sheet->setCellValue("G{$currentRow}", "MONTHLY FIRST AID BOX AUDIT CHECKLIST PN INTERNATIONAL PVT LTD");
                $sheet->getStyle("G{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Document Info
                $sheet->mergeCells("N$currentRow:P$currentRow")->setCellValue("N$currentRow", 'Doc. No.');
                $sheet->mergeCells("N" . ($currentRow + 1) . ":P" . ($currentRow + 1))->setCellValue("N" . ($currentRow + 1), 'Issue Dt.');
                $sheet->mergeCells("N" . ($currentRow + 2) . ":P" . ($currentRow + 2))->setCellValue("N" . ($currentRow + 2), 'Rev. & Dt.');

                $sheet->mergeCells("Q$currentRow:S$currentRow")->setCellValue("Q$currentRow", $document_no->doc_no);
                $sheet->mergeCells("Q" . ($currentRow + 1) . ":S" . ($currentRow + 1))->setCellValue("Q" . ($currentRow + 1), Displaydateformat($document_no->issue_date));
                $sheet->mergeCells("Q" . ($currentRow + 2) . ":S" . ($currentRow + 2))->setCellValue("Q" . ($currentRow + 2), $document_no->rev_dt);

                $sheet->getStyle("N$currentRow:S" . ($currentRow + 2))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'font' => ['bold' => true],
                ]);
                // NXT ROW
                $sheet->mergeCells("A" . ($currentRow + 3) . ":I" . ($currentRow + 3));
                $richText1 = new RichText();
                $richText1->createTextRun('SHIFT:- ')->getFont()->setBold(true);
                $richText1->createText(getShift($monthly_first_aid->shift));
                $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

                $sheet->mergeCells("J" . ($currentRow + 3) . ":S" . ($currentRow + 3));
                $richText2 = new RichText();
                $richText2->createTextRun('DATE :-  ')->getFont()->setBold(true);
                $richText2->createText(Displaydateformat($monthly_first_aid->date_of_inspection));
                $sheet->getCell("J" . ($currentRow + 3))->setValue($richText2);



                $sheet->getStyle("A" . ($currentRow + 3) . ":S" . ($currentRow + 3))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // next row 5

                $sheet->mergeCells("A" . ($currentRow + 4) . ":I" . ($currentRow + 4));
                $richText1 = new RichText();
                $richText1->createTextRun(' NEXT DUE DATE OF INSPECTION :- ')->getFont()->setBold(true);
                $richText1->createText(getFrequencyname($monthly_first_aid->frequency));
                $sheet->getCell("A" . ($currentRow + 4))->setValue($richText1);

                $sheet->mergeCells("J" . ($currentRow + 4) . ":S" . ($currentRow + 4));
                $richText2 = new RichText();
                $richText2->createTextRun('UNIT :- ')->getFont()->setBold(true);
                $richText2->createText(getUnitname($monthly_first_aid->unit));
                $sheet->getCell("J" . ($currentRow + 4))->setValue($richText2);



                $sheet->getStyle("A" . ($currentRow + 4) . ":S" . ($currentRow + 4))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $headerRow = $currentRow + 5;

                $sheet->mergeCells("B$headerRow:D$headerRow")->setCellValue("B$headerRow", "First-Aid Box Number");
                $sheet->mergeCells("E$headerRow:G$headerRow")->setCellValue("E$headerRow", "Department/Location");
                $sheet->mergeCells("H$headerRow:I$headerRow")->setCellValue("H$headerRow", "First-aid register is being properly maintained as & when required.");
                $sheet->mergeCells("J$headerRow:K$headerRow")->setCellValue("J$headerRow", "First-aid box is being inspected as per periodicity");
                $sheet->setCellValue("L$headerRow", "Does the First-aid box inspection checklist is being filled as per periodicity");
                $sheet->setCellValue("M$headerRow", "Does the First-aid box is being maintained as per the freeze quantity");
                $sheet->setCellValue("N$headerRow", "Does the medical requisition slip record is being maintained");
                $sheet->setCellValue("O$headerRow", "Does the First-aid box is clean");
                $sheet->mergeCells("P$headerRow:Q$headerRow");
                $sheet->setCellValue("P$headerRow", "Does the First-aid box sticker available");
                $sheet->mergeCells("R$headerRow:S$headerRow");
                $sheet->setCellValue("R$headerRow", "Does the First-aid material index is available");

                // Set column widths for better visibility
                $columnWidths = [
                    'B' => 22,
                    'C' => 22,
                    'D' => 22,
                    'E' => 25,
                    'F' => 25,
                    'G' => 25,
                    'H' => 30,
                    'I' => 30,
                    'J' => 30,
                    'K' => 30,
                    'L' => 40,
                    'M' => 40,
                    'N' => 30,
                    'O' => 25,
                    'P' => 25,
                    'Q' => 25,
                    'R' => 30,
                    'S' => 30,
                ];

                foreach ($columnWidths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                // Set row height
                $sheet->getRowDimension($headerRow)->setRowHeight(50);

                // Style formatting
                $sheet->getStyle("A$headerRow:S$headerRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'font' => ['bold' => true],
                ]);


                $inspectionRow = $headerRow + 1;

                foreach ($monthly_first_aid_audit_checklist as $index => $detail) {
                    $department = getDepartment($detail->department_id);
                    $first_aid_register_maintained = getYesNoStatus($detail->first_aid_register_maintained);
                    $first_aid_box_inspect_periodicity = getYesNoStatus($detail->first_aid_box_inspect_periodicity);
                    $first_aid_box_sticker = getYesNoStatus($detail->first_aid_box_sticker);
                    $first_aid_material_index = getYesNoStatus($detail->first_aid_material_index);
                    $sheet->setCellValue("A$inspectionRow", $index + 1);
                    $sheet->mergeCells("B$inspectionRow:D$inspectionRow")->setCellValue("B$inspectionRow", $detail->first_aid_box_no);
                    $sheet->mergeCells("E$inspectionRow:G$inspectionRow")->setCellValue("E$inspectionRow", $department);
                    $sheet->mergeCells("H$inspectionRow:I$inspectionRow")->setCellValue("H$inspectionRow", $first_aid_register_maintained);
                    $sheet->mergeCells("J$inspectionRow:K$inspectionRow")->setCellValue("J$inspectionRow", $first_aid_box_inspect_periodicity);

                    $sheet->setCellValue("L$inspectionRow", getYesNoStatus($detail->first_aid_box_checklist_periodicity));
                    $sheet->setCellValue("M$inspectionRow", getYesNoStatus($detail->first_aid_box_freeze_quantity));
                    $sheet->setCellValue("N$inspectionRow", getYesNoStatus($detail->medicine_requisition_slip_record));
                    $sheet->setCellValue("O$inspectionRow", getYesNoStatus($detail->first_aid_box_clean));
                    $sheet->mergeCells("P$inspectionRow:Q$inspectionRow")->setCellValue("P$inspectionRow", $first_aid_box_sticker);
                    $sheet->mergeCells("R$inspectionRow:S$inspectionRow")->setCellValue("R$inspectionRow", $first_aid_material_index);


                    $sheet->getStyle("A$inspectionRow:S$inspectionRow")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $inspectionRow++;
                }


                $signatureStartRow = $inspectionRow;
                $signatureEndRow = $signatureStartRow + 3;
                $labelRow = $signatureEndRow + 1;
                $imageHeight = 60;

                // Requestor Signature
                $sheet->mergeCells("A{$signatureStartRow}:S{$signatureEndRow}");
                if (file_exists($RequestorSignature)) {
                    $drawing = new Drawing();
                    $drawing->setName('Creator Signature');
                    $drawing->setDescription('Creator Signature');
                    $drawing->setPath($RequestorSignature);
                    $drawing->setCoordinates("J{$signatureStartRow}");
                    $drawing->setOffsetX(110);
                    $drawing->setOffsetY(10);
                    $drawing->setWidth($imageHeight);
                    $drawing->setHeight($imageHeight);
                    $drawing->setWorksheet($sheet);
                }
                $userName = getUsername($monthly_first_aid->created_by);
                $sheet->mergeCells("A{$labelRow}:S{$labelRow}")->setCellValue("A{$labelRow}", "Creator Signature:{$userName}");


                $sheet->getStyle("A{$labelRow}:S{$labelRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);



                $sheet->getStyle("A{$signatureStartRow}:O{$signatureEndRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $row =  $signatureStartRow+ 5;
            }



            $fileName = 'Monthly First Aid Box Audit Checklist.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/medical-requisition-slip/fdo-security-gate/list'));
        }
    }


    // pdf
    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->monthly_first_aid->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }

            foreach( $allData as $details){
                $document_no = $this->document_reference->selectOne($details->document_reference_id);

            }



            $data = array(

                'content' => $allData,
                'document_no' => $document_no,
                'pagetitle' => "Monthly First Aid Audit Checklist Inspection",
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

            $view = view('inspection.inspection_ohc.monthly_first_aid_audit_checklist.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Monthly First Aid Audit Checklist Inspection.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
             report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aid-box/monthly-audit/list'));
        }
    }

    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $row = 1;


            $monthly_first_aid = $this->monthly_first_aid->selectOne($id);
            $document_no = $this->document_reference->selectOne($monthly_first_aid->document_reference_id);
            $monthly_first_aid_audit_checklist = $this->monthly_first_aid_audit_checklist->selectOne($id);
            $safetyofficerSignature = GetOHCSignature($monthly_first_aid->approved_by, $id, OHC_TYPE_MONTHLY_FIRST_AID_BOX_AUDIT_INSPECTION_CHECKLIST);
            $RequestorSignature = GetOHCSignature($monthly_first_aid->created_by, $id, OHC_TYPE_MONTHLY_FIRST_AID_BOX_AUDIT_INSPECTION_CHECKLIST);

            $currentRow = $row;
            $logoLeftPath = public_path('assets/images/logo-dark.png');
            if (file_exists($logoLeftPath)) {
                $sheet->mergeCells("A$currentRow:F" . ($currentRow + 2));

                $drawing = new Drawing();
                $drawing->setName('Left Logo');
                $drawing->setPath($logoLeftPath);
                $drawing->setCoordinates("B$currentRow");
                $drawing->setOffsetX(100);
                $drawing->setOffsetY(15);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);

                $range = "A$currentRow:F" . ($currentRow + 2);
                $sheet->getStyle($range)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }

            // Title Section
            $sheet->mergeCells("G{$currentRow}:M" . ($currentRow + 2));
            $sheet->setCellValue("G{$currentRow}", "MONTHLY FIRST AID BOX AUDIT CHECKLIST PN INTERNATIONAL PVT LTD");
            $sheet->getStyle("G{$currentRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            // Document Info
            $sheet->mergeCells("N$currentRow:P$currentRow")->setCellValue("N$currentRow", 'Doc. No.');
            $sheet->mergeCells("N" . ($currentRow + 1) . ":P" . ($currentRow + 1))->setCellValue("N" . ($currentRow + 1), 'Issue Dt.');
            $sheet->mergeCells("N" . ($currentRow + 2) . ":P" . ($currentRow + 2))->setCellValue("N" . ($currentRow + 2), 'Rev. & Dt.');

            $sheet->mergeCells("Q$currentRow:S$currentRow")->setCellValue("Q$currentRow", $document_no->doc_no);
            $sheet->mergeCells("Q" . ($currentRow + 1) . ":S" . ($currentRow + 1))->setCellValue("Q" . ($currentRow + 1), Displaydateformat($document_no->issue_date));
            $sheet->mergeCells("Q" . ($currentRow + 2) . ":S" . ($currentRow + 2))->setCellValue("Q" . ($currentRow + 2), $document_no->rev_dt);

            $sheet->getStyle("N$currentRow:S" . ($currentRow + 2))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font' => ['bold' => true],
            ]);
            // NXT ROW
            $sheet->mergeCells("A" . ($currentRow + 3) . ":I" . ($currentRow + 3));
            $richText1 = new RichText();
            $richText1->createTextRun('SHIFT:- ')->getFont()->setBold(true);
            $richText1->createText(getShift($monthly_first_aid->shift));
            $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

            $sheet->mergeCells("J" . ($currentRow + 3) . ":S" . ($currentRow + 3));
            $richText2 = new RichText();
            $richText2->createTextRun('DATE :-  ')->getFont()->setBold(true);
            $richText2->createText(Displaydateformat($monthly_first_aid->date_of_inspection));
            $sheet->getCell("J" . ($currentRow + 3))->setValue($richText2);



            $sheet->getStyle("A" . ($currentRow + 3) . ":S" . ($currentRow + 3))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // next row 5

            $sheet->mergeCells("A" . ($currentRow + 4) . ":I" . ($currentRow + 4));
            $richText1 = new RichText();
            $richText1->createTextRun(' NEXT DUE DATE OF INSPECTION :- ')->getFont()->setBold(true);
            $richText1->createText(getFrequencyname($monthly_first_aid->frequency));
            $sheet->getCell("A" . ($currentRow + 4))->setValue($richText1);

            $sheet->mergeCells("J" . ($currentRow + 4) . ":S" . ($currentRow + 4));
            $richText2 = new RichText();
            $richText2->createTextRun('UNIT :- ')->getFont()->setBold(true);
            $richText2->createText(getUnitname($monthly_first_aid->unit));
            $sheet->getCell("J" . ($currentRow + 4))->setValue($richText2);



            $sheet->getStyle("A" . ($currentRow + 4) . ":S" . ($currentRow + 4))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $headerRow = $currentRow + 5;

            $sheet->mergeCells("B$headerRow:D$headerRow")->setCellValue("B$headerRow", "First-Aid Box Number");
            $sheet->mergeCells("E$headerRow:G$headerRow")->setCellValue("E$headerRow", "Department/Location");
            $sheet->mergeCells("H$headerRow:I$headerRow")->setCellValue("H$headerRow", "First-aid register is being properly maintained as & when required.");
            $sheet->mergeCells("J$headerRow:K$headerRow")->setCellValue("J$headerRow", "First-aid box is being inspected as per periodicity");
            $sheet->setCellValue("L$headerRow", "Does the First-aid box inspection checklist is being filled as per periodicity");
            $sheet->setCellValue("M$headerRow", "Does the First-aid box is being maintained as per the freeze quantity");
            $sheet->setCellValue("N$headerRow", "Does the medical requisition slip record is being maintained");
            $sheet->setCellValue("O$headerRow", "Does the First-aid box is clean");
            $sheet->mergeCells("P$headerRow:Q$headerRow");
            $sheet->setCellValue("P$headerRow", "Does the First-aid box sticker available");
            $sheet->mergeCells("R$headerRow:S$headerRow");
            $sheet->setCellValue("R$headerRow", "Does the First-aid material index is available");

            // Set column widths for better visibility
            $columnWidths = [
                'B' => 22,
                'C' => 22,
                'D' => 22,
                'E' => 25,
                'F' => 25,
                'G' => 25,
                'H' => 30,
                'I' => 30,
                'J' => 30,
                'K' => 30,
                'L' => 40,
                'M' => 40,
                'N' => 30,
                'O' => 25,
                'P' => 25,
                'Q' => 25,
                'R' => 30,
                'S' => 30,
            ];

            foreach ($columnWidths as $col => $width) {
                $sheet->getColumnDimension($col)->setWidth($width);
            }

            // Set row height
            $sheet->getRowDimension($headerRow)->setRowHeight(50);

            // Style formatting
            $sheet->getStyle("A$headerRow:S$headerRow")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'font' => ['bold' => true],
            ]);


            $inspectionRow = $headerRow + 1;

            foreach ($monthly_first_aid_audit_checklist as $index => $detail) {
                $department = getDepartment($detail->department_id);
                $first_aid_register_maintained = getYesNoStatus($detail->first_aid_register_maintained);
                $first_aid_box_inspect_periodicity = getYesNoStatus($detail->first_aid_box_inspect_periodicity);
                $first_aid_box_sticker = getYesNoStatus($detail->first_aid_box_sticker);
                $first_aid_material_index = getYesNoStatus($detail->first_aid_material_index);
                $sheet->setCellValue("A$inspectionRow", $index + 1);
                $sheet->mergeCells("B$inspectionRow:D$inspectionRow")->setCellValue("B$inspectionRow", $detail->first_aid_box_no);
                $sheet->mergeCells("E$inspectionRow:G$inspectionRow")->setCellValue("E$inspectionRow", $department);
                $sheet->mergeCells("H$inspectionRow:I$inspectionRow")->setCellValue("H$inspectionRow", $first_aid_register_maintained);
                $sheet->mergeCells("J$inspectionRow:K$inspectionRow")->setCellValue("J$inspectionRow", $first_aid_box_inspect_periodicity);

                $sheet->setCellValue("L$inspectionRow", getYesNoStatus($detail->first_aid_box_checklist_periodicity));
                $sheet->setCellValue("M$inspectionRow", getYesNoStatus($detail->first_aid_box_freeze_quantity));
                $sheet->setCellValue("N$inspectionRow", getYesNoStatus($detail->medicine_requisition_slip_record));
                $sheet->setCellValue("O$inspectionRow", getYesNoStatus($detail->first_aid_box_clean));
                $sheet->mergeCells("P$inspectionRow:Q$inspectionRow")->setCellValue("P$inspectionRow", $first_aid_box_sticker);
                $sheet->mergeCells("R$inspectionRow:S$inspectionRow")->setCellValue("R$inspectionRow", $first_aid_material_index);


                $sheet->getStyle("A$inspectionRow:S$inspectionRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $inspectionRow++;
            }


            $signatureStartRow = $inspectionRow;
            $signatureEndRow = $signatureStartRow + 3;
            $labelRow = $signatureEndRow + 1;
            $imageHeight = 60;

            // Requestor Signature
            $sheet->mergeCells("A{$signatureStartRow}:S{$signatureEndRow}");
            if (file_exists($RequestorSignature)) {
                $drawing = new Drawing();
                $drawing->setName('Creator Signature');
                $drawing->setDescription('Creator Signature');
                $drawing->setPath($RequestorSignature);
                $drawing->setCoordinates("J{$signatureStartRow}");
                $drawing->setOffsetX(110);
                $drawing->setOffsetY(10);
                $drawing->setWidth($imageHeight);
                $drawing->setHeight($imageHeight);
                $drawing->setWorksheet($sheet);
            }
            $userName = getUsername($monthly_first_aid->created_by);
            $sheet->mergeCells("A{$labelRow}:S{$labelRow}")->setCellValue("A{$labelRow}", "Creator Signature:{$userName}");


            $sheet->getStyle("A{$labelRow}:S{$labelRow}")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);



            $sheet->getStyle("A{$signatureStartRow}:O{$signatureEndRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);



            $fileName = 'Monthly First Aid Box Audit Checklist.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/medical-requisition-slip/fdo-security-gate/list'));
        }
    }
}
