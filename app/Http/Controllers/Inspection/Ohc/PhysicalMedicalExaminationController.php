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
use App\Models\Inspection\Ohc\OHCHygieneCleaningChecklist;
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
            report($ex);
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
            report($ex);
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

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $physicalHealth = $this->physicalHealth->selectOne($id);
            $document_no = $this->document_reference->selectOne($physicalHealth->document_reference_id);

            $fmo_signature = GetOHCSignature($physicalHealth->created_by, $id, OHC_TYPE_PHYSICAL_HEALTH_EXAMINATION);

            $personalDetails = $this->personalDetails->personalDetails();
            $familyHistory = $this->familyHistory->familyHistory();
            $check_points = getCheckListQuestion(OHC_PHYSICAL_HEALTH_EXAMINATION);
            $row = 1;
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
            $sheet->setCellValue("G{$currentRow}", "PHYSICAL HEALTH EXAMINATION CHECK - UP ");

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

            $sheet->mergeCells("A" . ($currentRow + 3) . ":G" . ($currentRow + 3));
            $richText1 = new RichText();
            $richText1->createTextRun('EMPLOYEE CODE:- ')->getFont()->setBold(true);
            $richText1->createText(($physicalHealth->emp_id));
            $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

            $sheet->mergeCells("H" . ($currentRow + 3) . ":N" . ($currentRow + 3));
            $richText2 = new RichText();
            $richText2->createTextRun('EMPLOYEE NAME :-  ')->getFont()->setBold(true);
            $richText2->createText(($physicalHealth->emp_name));
            $sheet->getCell("H" . ($currentRow + 3))->setValue($richText2);

            $sheet->mergeCells("O" . ($currentRow + 3) . ":S" . ($currentRow + 3));
            $richText2 = new RichText();
            $richText2->createTextRun('CONTACT NUMBER :-  ')->getFont()->setBold(true);
            $richText2->createText(($physicalHealth->mobile_no));
            $sheet->getCell("O" . ($currentRow + 3))->setValue($richText2);

            $sheet->getStyle("A" . ($currentRow + 3) . ":S" . ($currentRow + 3))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells("A" . ($currentRow + 4) . ":G" . ($currentRow + 4));
            $richText1 = new RichText();
            $richText1->createTextRun('GENDER:- ')->getFont()->setBold(true);
            $richText1->createText(($physicalHealth->gender));
            $sheet->getCell("A" . ($currentRow + 4))->setValue($richText1);

            $sheet->mergeCells("H" . ($currentRow + 4) . ":N" . ($currentRow + 4));
            $richText2 = new RichText();
            $richText2->createTextRun('DATE OF BIRTH :-  ')->getFont()->setBold(true);
            $richText2->createText(Displaydateformat($physicalHealth->dob));
            $sheet->getCell("H" . ($currentRow + 4))->setValue($richText2);

            $sheet->mergeCells("O" . ($currentRow + 4) . ":S" . ($currentRow + 4));
            $richText2 = new RichText();
            $richText2->createTextRun('AGE:-  ')->getFont()->setBold(true);
            $richText2->createText(Displaydateformat($physicalHealth->age));
            $sheet->getCell("O" . ($currentRow + 4))->setValue($richText2);

            $sheet->getStyle("A" . ($currentRow + 4) . ":S" . ($currentRow + 4))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells("A" . ($currentRow + 5) . ":G" . ($currentRow + 5));
            $richText1 = new RichText();
            $richText1->createTextRun('BLOOD GROUP:- ')->getFont()->setBold(true);
            $richText1->createText(($physicalHealth->blood_group));
            $sheet->getCell("A" . ($currentRow + 5))->setValue($richText1);

            $sheet->mergeCells("H" . ($currentRow + 5) . ":N" . ($currentRow + 5));
            $richText2 = new RichText();
            $richText2->createTextRun('DATE :-  ')->getFont()->setBold(true);
            $richText2->createText(Displaydateformat($physicalHealth->date));
            $sheet->getCell("H" . ($currentRow + 5))->setValue($richText2);

            $sheet->mergeCells("O" . ($currentRow + 5) . ":S" . ($currentRow + 5));
            $richText2 = new RichText();
            $richText2->createTextRun('UNIT :-  ')->getFont()->setBold(true);
            $richText2->createText(getUnitname($physicalHealth->unit_id));
            $sheet->getCell("O" . ($currentRow + 5))->setValue($richText2);

            $sheet->getStyle("A" . ($currentRow + 5) . ":S" . ($currentRow + 5))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells("A" . ($currentRow + 6) . ":G" . ($currentRow + 6));
            $richText1 = new RichText();
            $richText1->createTextRun('DEPARTMENT:- ')->getFont()->setBold(true);
            $richText1->createText(getDepartment($physicalHealth->department_id));
            $sheet->getCell("A" . ($currentRow + 6))->setValue($richText1);

            $sheet->mergeCells("H" . ($currentRow + 6) . ":N" . ($currentRow + 6));
            $richText2 = new RichText();
            $richText2->createTextRun('HEIGHT :-  ')->getFont()->setBold(true);
            $richText2->createText(($physicalHealth->height));
            $sheet->getCell("H" . ($currentRow + 6))->setValue($richText2);

            $sheet->mergeCells("O" . ($currentRow + 6) . ":S" . ($currentRow + 6));
            $richText2 = new RichText();
            $richText2->createTextRun('WEIGHT :-  ')->getFont()->setBold(true);
            $richText2->createText(($physicalHealth->weight));
            $sheet->getCell("O" . ($currentRow + 6))->setValue($richText2);

            $sheet->getStyle("A" . ($currentRow + 6) . ":S" . ($currentRow + 6))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells("A" . ($currentRow + 7) . ":G" . ($currentRow + 7));
            $richText1 = new RichText();
            $richText1->createTextRun('BMI:- ')->getFont()->setBold(true);
            $richText1->createText(($physicalHealth->bmi));
            $sheet->getCell("A" . ($currentRow + 7))->setValue($richText1);

            $sheet->mergeCells("H" . ($currentRow + 7) . ":S" . ($currentRow + 7));
            $richText2 = new RichText();
            $richText2->createTextRun('ADDRESS :-  ')->getFont()->setBold(true);
            $richText2->createText(($physicalHealth->address));
            $sheet->getCell("H" . ($currentRow + 7))->setValue($richText2);



            $sheet->getStyle("A" . ($currentRow + 7) . ":S" . ($currentRow + 7))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells("A" . ($currentRow + 8) . ":S" . ($currentRow + 8));

            $richText2 = new RichText();
            $richText2->createTextRun('CLINICAL DETAILS')->getFont()->setBold(true);


            $sheet->getCell("A" . ($currentRow + 8))->setValue($richText2);


            $sheet->getStyle("A" . ($currentRow + 8) . ":S" . ($currentRow + 8))->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
            $row = $currentRow + 9;

            // ===== 1. Table Header Row =====
            $sheet->mergeCells("A$row:C$row");
            $sheet->mergeCells("D$row:P$row");
            $sheet->mergeCells("Q$row:S$row");

            $sheet->setCellValue("A$row", "Sr. No.");
            $sheet->setCellValue("D$row", "Details Of Personal Habits");
            $sheet->setCellValue("Q$row", "Status");

            $sheet->getStyle("A$row:S$row")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E9ECEF']
                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);

            $row++;

            $clinicalDetails = json_decode($physicalHealth->personal_details, true);
            $statusArray = $clinicalDetails['status'] ?? [];

            // ===== 2. Data Rows =====
            foreach ($statusArray as $index => $value) {
                // Merge cells for each column group
                $sheet->mergeCells("A$row:C$row");
                $sheet->mergeCells("D$row:P$row");
                $sheet->mergeCells("Q$row:S$row");

                // Fill data
                $sheet->setCellValue("A$row", $row - ($currentRow + 9)); // Sr. No.
                $sheet->setCellValue("D$row", getClinicalDetails($index));
                $sheet->setCellValue("Q$row", $value == '1' ? '✓' : '✗');

                // Apply styling
                $sheet->getStyle("A$row:S$row")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],

                ]);

                $row++;
            }

            $PastRow = $row;

            $sheet->mergeCells("A" . ($PastRow) . ":S" . ($PastRow));
            $richText1 = new RichText();
            $richText1->createTextRun('PRESENT COMPLAINTS:- ')->getFont()->setBold(true);
            $richText1->createText(($physicalHealth->present_complaint));
            $sheet->getCell("A" . ($PastRow))->setValue($richText1);

            $sheet->getStyle("A" . ($PastRow) . ":S" . ($PastRow))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells("A" . ($PastRow + 1) . ":S" . ($PastRow + 1));
            $richText1 = new RichText();
            $richText1->createTextRun('PAST HISTORY:- ')->getFont()->setBold(true);
            $richText1->createText(($physicalHealth->past_history));
            $sheet->getCell("A" . ($PastRow + 1))->setValue($richText1);

            $sheet->getStyle("A" . ($PastRow + 1) . ":S" . ($PastRow + 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells("A" . ($PastRow + 2) . ":S" . ($PastRow + 2));

            $richText2 = new RichText();
            $richText2->createTextRun('FAMILY HISTORY')->getFont()->setBold(true);


            $sheet->getCell("A" . ($PastRow + 2))->setValue($richText2);


            $sheet->getStyle("A" . ($PastRow + 2) . ":S" . ($PastRow + 2))->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);


            // Starting row
            $familyHistoryRow = $PastRow + 3;

            // Merge header cells
            $sheet->mergeCells("A$familyHistoryRow:C$familyHistoryRow"); // Sr. No.
            $sheet->mergeCells("D$familyHistoryRow:J$familyHistoryRow"); // Details
            $sheet->mergeCells("K$familyHistoryRow:O$familyHistoryRow"); // Status
            $sheet->mergeCells("P$familyHistoryRow:S$familyHistoryRow"); // Remarks

            // Set header values
            $sheet->setCellValue("A$familyHistoryRow", "Sr. No.");
            $sheet->setCellValue("D$familyHistoryRow", "Details Of Personal Habits");
            $sheet->setCellValue("K$familyHistoryRow", "Status");
            $sheet->setCellValue("P$familyHistoryRow", "Remarks");

            // Style header
            $sheet->getStyle("A$familyHistoryRow:S$familyHistoryRow")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],

                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);

            $row = $familyHistoryRow + 1;

            // Decode JSON data from DB
            $FamilyHistoryData = json_decode($physicalHealth->family_history, true);
            $statusArray = $FamilyHistoryData['status'] ?? [];
            $remarksArray = $FamilyHistoryData['remarks'] ?? [];

            // Loop through family history items
            foreach ($familyHistory as $index => $item) {
                // Merge cells for the current row
                $sheet->mergeCells("A$row:C$row");
                $sheet->mergeCells("D$row:J$row");
                $sheet->mergeCells("K$row:O$row");
                $sheet->mergeCells("P$row:S$row");

                $status = $statusArray[$item->id] ?? null;
                $remarks = $remarksArray[$item->id] ?? 'No remarks';

                // Set cell values
                $sheet->setCellValue("A$row", $index + 1);
                $sheet->setCellValue("D$row", $item->family_history);
                $sheet->setCellValue("K$row", $status == '1' ? '✓' : '✗');
                $sheet->setCellValue("P$row", $remarks);

                // Set styling
                $sheet->getStyle("A$row:S$row")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],

                ]);

                $row++;
            }

            $vitalcheckpoints = $row;
            $sheet->mergeCells("A" . ($vitalcheckpoints) . ":S" . ($vitalcheckpoints));

            $richText2 = new RichText();
            $richText2->createTextRun('VITAL CHECK POINTS')->getFont()->setBold(true);


            $sheet->getCell("A" . ($vitalcheckpoints))->setValue($richText2);


            $sheet->getStyle("A" . ($vitalcheckpoints) . ":S" . ($vitalcheckpoints))->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            $row = $vitalcheckpoints + 1;

            $sheet->mergeCells("A$row:C$row");
            $sheet->mergeCells("D$row:P$row");
            $sheet->mergeCells("Q$row:S$row");

            $sheet->setCellValue("A$row", "Sr. No.");
            $sheet->setCellValue("D$row", "Check Points");
            $sheet->setCellValue("Q$row", "Reading Value");

            // Style header row
            $sheet->getStyle("A$row:S$row")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E9ECEF']
                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);

            // Decode values
            $vital_checkpoints = json_decode($physicalHealth->vital_checkpoints, true);
            $reading_value = $vital_checkpoints['reading_value'] ?? [];

            $row++; // Move to next row for content
            $serial = 1;

            // Loop through $check_points
            foreach ($check_points as $label => $items) {
                foreach ($items as $point) {
                    // Merge cells for each column group
                    $sheet->mergeCells("A$row:C$row");
                    $sheet->mergeCells("D$row:P$row");
                    $sheet->mergeCells("Q$row:S$row");

                    // Set values
                    $sheet->setCellValue("A$row", $serial);
                    $sheet->setCellValue("D$row", $point->name);
                    $sheet->setCellValue("Q$row", $reading_value[$point->id] ?? 'No Value');

                    // Style row
                    $sheet->getStyle("A$row:S$row")->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'font' => ['size' => 11]
                    ]);

                    $serial++;
                    $row++;
                }
            }

            $eyecheckup = $row;
            $sheet->mergeCells("A" . ($eyecheckup) . ":S" . ($eyecheckup));

            $richText2 = new RichText();
            $richText2->createTextRun('EYE CHECK UP')->getFont()->setBold(true);


            $sheet->getCell("A" . ($eyecheckup))->setValue($richText2);


            $sheet->getStyle("A" . ($eyecheckup) . ":S" . ($eyecheckup))->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
            // Starting row
            $row = $eyecheckup + 1; // Adjust as needed

            // Merge header cells (19 columns: A to S)
            $sheet->mergeCells("A$row:E$row"); // Vision
            $sheet->mergeCells("F$row:J$row"); // Without Glasses (Right)
            $sheet->mergeCells("K$row:O$row"); // With Glasses (Left)
            $sheet->mergeCells("P$row:S$row"); // Color Blindness

            // Set header values
            $sheet->setCellValue("A$row", "Vision");
            $sheet->setCellValue("F$row", "Without Glasses (Right)");
            $sheet->setCellValue("K$row", "With Glasses (Left)");
            $sheet->setCellValue("P$row", "Color Blindness");

            // Style header
            $sheet->getStyle("A$row:S$row")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,

                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);

            // Distance row
            $row++;
            $sheet->mergeCells("A$row:E$row");
            $sheet->mergeCells("F$row:J$row");
            $sheet->mergeCells("K$row:O$row");
            $sheet->mergeCells("P$row:S$row");

            $sheet->setCellValue("A$row", "Distance");
            $sheet->setCellValue("F$row", $physicalHealth->distance_without_glass ?? '');
            $sheet->setCellValue("K$row", $physicalHealth->distance_with_glass ?? '');

            $distanceCheck = $physicalHealth->distance_without_glass_yes ?? null;
            $sheet->setCellValue("P$row", $distanceCheck === '1' ? '✓' : ($distanceCheck === '0' ? '✗' : ''));

            // Style distance row
            $sheet->getStyle("A$row:S$row")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'font' => [

                    'size' => 12
                ]
            ]);

            // Near row
            $row++;
            $sheet->mergeCells("A$row:E$row");
            $sheet->mergeCells("F$row:J$row");
            $sheet->mergeCells("K$row:O$row");
            $sheet->mergeCells("P$row:S$row");

            $sheet->setCellValue("A$row", "Near");
            $sheet->setCellValue("F$row", $physicalHealth->near_without_glass ?? '');
            $sheet->setCellValue("K$row", $physicalHealth->near_with_glass ?? '');

            $nearCheck = $physicalHealth->near_without_glass_yes ?? null;
            $sheet->setCellValue("P$row", $nearCheck === '1' ? '✓' : ($nearCheck === '0' ? '✗' : ''));

            // Style near row
            $sheet->getStyle("A$row:S$row")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'font' => [

                    'size' => 12
                ]
            ]);
            $remarksRow = $row + 1;

            // Merge entire row A to S
            $sheet->mergeCells("A$remarksRow:S$remarksRow");

            // Create rich text for remarks
            $richText2 = new RichText();
            $richText2->createTextRun('REMARKS BY MEDICAL OFFICER :-  ')->getFont()->setBold(true);
            $richText2->createText($physicalHealth->remarks ?? '');


            $sheet->getCell("A$remarksRow")->setValue($richText2);


            $sheet->getStyle("A$remarksRow:S$remarksRow")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);


            $writer = new Xlsx($spreadsheet);
            $fileName = 'physical_health_examination.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/physical-medical-examination/yearly/list'));
        }
    }


    public function ExportExcel()
    {
        try {
            $allData = $this->physicalHealth->exportdata();
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }
            $row = 1;
            $currentRow = $row;

            foreach ($allData as $details) {
                $physicalHealth = $this->physicalHealth->selectOne($details->id);
                $document_no = $this->document_reference->selectOne($physicalHealth->document_reference_id);

                $personalDetails = $this->personalDetails->personalDetails();
                $familyHistory = $this->familyHistory->familyHistory();
                $check_points = getCheckListQuestion(OHC_PHYSICAL_HEALTH_EXAMINATION);
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
                $sheet->setCellValue("G{$currentRow}", "PHYSICAL HEALTH EXAMINATION CHECK - UP ");

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

                $sheet->mergeCells("A" . ($currentRow + 3) . ":G" . ($currentRow + 3));
                $richText1 = new RichText();
                $richText1->createTextRun('EMPLOYEE CODE:- ')->getFont()->setBold(true);
                $richText1->createText(($physicalHealth->emp_id));
                $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

                $sheet->mergeCells("H" . ($currentRow + 3) . ":N" . ($currentRow + 3));
                $richText2 = new RichText();
                $richText2->createTextRun('EMPLOYEE NAME :-  ')->getFont()->setBold(true);
                $richText2->createText(($physicalHealth->emp_name));
                $sheet->getCell("H" . ($currentRow + 3))->setValue($richText2);

                $sheet->mergeCells("O" . ($currentRow + 3) . ":S" . ($currentRow + 3));
                $richText2 = new RichText();
                $richText2->createTextRun('CONTACT NUMBER :-  ')->getFont()->setBold(true);
                $richText2->createText(($physicalHealth->mobile_no));
                $sheet->getCell("O" . ($currentRow + 3))->setValue($richText2);

                $sheet->getStyle("A" . ($currentRow + 3) . ":S" . ($currentRow + 3))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->mergeCells("A" . ($currentRow + 4) . ":G" . ($currentRow + 4));
                $richText1 = new RichText();
                $richText1->createTextRun('GENDER:- ')->getFont()->setBold(true);
                $richText1->createText(($physicalHealth->gender));
                $sheet->getCell("A" . ($currentRow + 4))->setValue($richText1);

                $sheet->mergeCells("H" . ($currentRow + 4) . ":N" . ($currentRow + 4));
                $richText2 = new RichText();
                $richText2->createTextRun('DATE OF BIRTH :-  ')->getFont()->setBold(true);
                $richText2->createText(Displaydateformat($physicalHealth->dob));
                $sheet->getCell("H" . ($currentRow + 4))->setValue($richText2);

                $sheet->mergeCells("O" . ($currentRow + 4) . ":S" . ($currentRow + 4));
                $richText2 = new RichText();
                $richText2->createTextRun('AGE:-  ')->getFont()->setBold(true);
                $richText2->createText(Displaydateformat($physicalHealth->age));
                $sheet->getCell("O" . ($currentRow + 4))->setValue($richText2);

                $sheet->getStyle("A" . ($currentRow + 4) . ":S" . ($currentRow + 4))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->mergeCells("A" . ($currentRow + 5) . ":G" . ($currentRow + 5));
                $richText1 = new RichText();
                $richText1->createTextRun('BLOOD GROUP:- ')->getFont()->setBold(true);
                $richText1->createText(($physicalHealth->blood_group));
                $sheet->getCell("A" . ($currentRow + 5))->setValue($richText1);

                $sheet->mergeCells("H" . ($currentRow + 5) . ":N" . ($currentRow + 5));
                $richText2 = new RichText();
                $richText2->createTextRun('DATE :-  ')->getFont()->setBold(true);
                $richText2->createText(Displaydateformat($physicalHealth->date));
                $sheet->getCell("H" . ($currentRow + 5))->setValue($richText2);

                $sheet->mergeCells("O" . ($currentRow + 5) . ":S" . ($currentRow + 5));
                $richText2 = new RichText();
                $richText2->createTextRun('UNIT :-  ')->getFont()->setBold(true);
                $richText2->createText(getUnitname($physicalHealth->unit_id));
                $sheet->getCell("O" . ($currentRow + 5))->setValue($richText2);

                $sheet->getStyle("A" . ($currentRow + 5) . ":S" . ($currentRow + 5))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->mergeCells("A" . ($currentRow + 6) . ":G" . ($currentRow + 6));
                $richText1 = new RichText();
                $richText1->createTextRun('DEPARTMENT:- ')->getFont()->setBold(true);
                $richText1->createText(getDepartment($physicalHealth->department_id));
                $sheet->getCell("A" . ($currentRow + 6))->setValue($richText1);

                $sheet->mergeCells("H" . ($currentRow + 6) . ":N" . ($currentRow + 6));
                $richText2 = new RichText();
                $richText2->createTextRun('HEIGHT :-  ')->getFont()->setBold(true);
                $richText2->createText(($physicalHealth->height));
                $sheet->getCell("H" . ($currentRow + 6))->setValue($richText2);

                $sheet->mergeCells("O" . ($currentRow + 6) . ":S" . ($currentRow + 6));
                $richText2 = new RichText();
                $richText2->createTextRun('WEIGHT :-  ')->getFont()->setBold(true);
                $richText2->createText(($physicalHealth->weight));
                $sheet->getCell("O" . ($currentRow + 6))->setValue($richText2);

                $sheet->getStyle("A" . ($currentRow + 6) . ":S" . ($currentRow + 6))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->mergeCells("A" . ($currentRow + 7) . ":G" . ($currentRow + 7));
                $richText1 = new RichText();
                $richText1->createTextRun('BMI:- ')->getFont()->setBold(true);
                $richText1->createText(($physicalHealth->bmi));
                $sheet->getCell("A" . ($currentRow + 7))->setValue($richText1);

                $sheet->mergeCells("H" . ($currentRow + 7) . ":S" . ($currentRow + 7));
                $richText2 = new RichText();
                $richText2->createTextRun('ADDRESS :-  ')->getFont()->setBold(true);
                $richText2->createText(($physicalHealth->address));
                $sheet->getCell("H" . ($currentRow + 7))->setValue($richText2);



                $sheet->getStyle("A" . ($currentRow + 7) . ":S" . ($currentRow + 7))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->mergeCells("A" . ($currentRow + 8) . ":S" . ($currentRow + 8));

                $richText2 = new RichText();
                $richText2->createTextRun('CLINICAL DETAILS')->getFont()->setBold(true);


                $sheet->getCell("A" . ($currentRow + 8))->setValue($richText2);


                $sheet->getStyle("A" . ($currentRow + 8) . ":S" . ($currentRow + 8))->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $row = $currentRow + 9;

                // ===== 1. Table Header Row =====
                $sheet->mergeCells("A$row:C$row");
                $sheet->mergeCells("D$row:P$row");
                $sheet->mergeCells("Q$row:S$row");

                $sheet->setCellValue("A$row", "Sr. No.");
                $sheet->setCellValue("D$row", "Details Of Personal Habits");
                $sheet->setCellValue("Q$row", "Status");

                $sheet->getStyle("A$row:S$row")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E9ECEF']
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
                ]);

                $row++;

                $clinicalDetails = json_decode($physicalHealth->personal_details, true);
                $statusArray = $clinicalDetails['status'] ?? [];

                // ===== 2. Data Rows =====
                foreach ($statusArray as $index => $value) {
                    // Merge cells for each column group
                    $sheet->mergeCells("A$row:C$row");
                    $sheet->mergeCells("D$row:P$row");
                    $sheet->mergeCells("Q$row:S$row");

                    // Fill data
                    $sheet->setCellValue("A$row", $row - ($currentRow + 9)); // Sr. No.
                    $sheet->setCellValue("D$row", getClinicalDetails($index));
                    $sheet->setCellValue("Q$row", $value == '1' ? '✓' : '✗');

                    // Apply styling
                    $sheet->getStyle("A$row:S$row")->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],

                    ]);

                    $row++;
                }

                $PastRow = $row;

                $sheet->mergeCells("A" . ($PastRow) . ":S" . ($PastRow));
                $richText1 = new RichText();
                $richText1->createTextRun('PRESENT COMPLAINTS:- ')->getFont()->setBold(true);
                $richText1->createText(($physicalHealth->present_complaint));
                $sheet->getCell("A" . ($PastRow))->setValue($richText1);

                $sheet->getStyle("A" . ($PastRow) . ":S" . ($PastRow))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->mergeCells("A" . ($PastRow + 1) . ":S" . ($PastRow + 1));
                $richText1 = new RichText();
                $richText1->createTextRun('PAST HISTORY:- ')->getFont()->setBold(true);
                $richText1->createText(($physicalHealth->past_history));
                $sheet->getCell("A" . ($PastRow + 1))->setValue($richText1);

                $sheet->getStyle("A" . ($PastRow + 1) . ":S" . ($PastRow + 1))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->mergeCells("A" . ($PastRow + 2) . ":S" . ($PastRow + 2));

                $richText2 = new RichText();
                $richText2->createTextRun('FAMILY HISTORY')->getFont()->setBold(true);


                $sheet->getCell("A" . ($PastRow + 2))->setValue($richText2);


                $sheet->getStyle("A" . ($PastRow + 2) . ":S" . ($PastRow + 2))->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);


                // Starting row
                $familyHistoryRow = $PastRow + 3;

                // Merge header cells
                $sheet->mergeCells("A$familyHistoryRow:C$familyHistoryRow"); // Sr. No.
                $sheet->mergeCells("D$familyHistoryRow:J$familyHistoryRow"); // Details
                $sheet->mergeCells("K$familyHistoryRow:O$familyHistoryRow"); // Status
                $sheet->mergeCells("P$familyHistoryRow:S$familyHistoryRow"); // Remarks

                // Set header values
                $sheet->setCellValue("A$familyHistoryRow", "Sr. No.");
                $sheet->setCellValue("D$familyHistoryRow", "Details Of Personal Habits");
                $sheet->setCellValue("K$familyHistoryRow", "Status");
                $sheet->setCellValue("P$familyHistoryRow", "Remarks");

                // Style header
                $sheet->getStyle("A$familyHistoryRow:S$familyHistoryRow")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],

                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
                ]);

                $row = $familyHistoryRow + 1;

                // Decode JSON data from DB
                $FamilyHistoryData = json_decode($physicalHealth->family_history, true);
                $statusArray = $FamilyHistoryData['status'] ?? [];
                $remarksArray = $FamilyHistoryData['remarks'] ?? [];

                // Loop through family history items
                foreach ($familyHistory as $index => $item) {
                    // Merge cells for the current row
                    $sheet->mergeCells("A$row:C$row");
                    $sheet->mergeCells("D$row:J$row");
                    $sheet->mergeCells("K$row:O$row");
                    $sheet->mergeCells("P$row:S$row");

                    $status = $statusArray[$item->id] ?? null;
                    $remarks = $remarksArray[$item->id] ?? 'No remarks';

                    // Set cell values
                    $sheet->setCellValue("A$row", $index + 1);
                    $sheet->setCellValue("D$row", $item->family_history);
                    $sheet->setCellValue("K$row", $status == '1' ? '✓' : '✗');
                    $sheet->setCellValue("P$row", $remarks);

                    // Set styling
                    $sheet->getStyle("A$row:S$row")->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],

                    ]);

                    $row++;
                }

                $vitalcheckpoints = $row;
                $sheet->mergeCells("A" . ($vitalcheckpoints) . ":S" . ($vitalcheckpoints));

                $richText2 = new RichText();
                $richText2->createTextRun('VITAL CHECK POINTS')->getFont()->setBold(true);


                $sheet->getCell("A" . ($vitalcheckpoints))->setValue($richText2);


                $sheet->getStyle("A" . ($vitalcheckpoints) . ":S" . ($vitalcheckpoints))->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $row = $vitalcheckpoints + 1;

                $sheet->mergeCells("A$row:C$row");
                $sheet->mergeCells("D$row:P$row");
                $sheet->mergeCells("Q$row:S$row");

                $sheet->setCellValue("A$row", "Sr. No.");
                $sheet->setCellValue("D$row", "Check Points");
                $sheet->setCellValue("Q$row", "Reading Value");

                // Style header row
                $sheet->getStyle("A$row:S$row")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E9ECEF']
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
                ]);

                // Decode values
                $vital_checkpoints = json_decode($physicalHealth->vital_checkpoints, true);
                $reading_value = $vital_checkpoints['reading_value'] ?? [];

                $row++; // Move to next row for content
                $serial = 1;

                // Loop through $check_points
                foreach ($check_points as $label => $items) {
                    foreach ($items as $point) {
                        // Merge cells for each column group
                        $sheet->mergeCells("A$row:C$row");
                        $sheet->mergeCells("D$row:P$row");
                        $sheet->mergeCells("Q$row:S$row");

                        // Set values
                        $sheet->setCellValue("A$row", $serial);
                        $sheet->setCellValue("D$row", $point->name);
                        $sheet->setCellValue("Q$row", $reading_value[$point->id] ?? 'No Value');

                        // Style row
                        $sheet->getStyle("A$row:S$row")->applyFromArray([
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                            'font' => ['size' => 11]
                        ]);

                        $serial++;
                        $row++;
                    }
                }

                $eyecheckup = $row;
                $sheet->mergeCells("A" . ($eyecheckup) . ":S" . ($eyecheckup));

                $richText2 = new RichText();
                $richText2->createTextRun('EYE CHECK UP')->getFont()->setBold(true);


                $sheet->getCell("A" . ($eyecheckup))->setValue($richText2);


                $sheet->getStyle("A" . ($eyecheckup) . ":S" . ($eyecheckup))->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                // Starting row
                $row = $eyecheckup + 1; // Adjust as needed

                // Merge header cells (19 columns: A to S)
                $sheet->mergeCells("A$row:E$row"); // Vision
                $sheet->mergeCells("F$row:J$row"); // Without Glasses (Right)
                $sheet->mergeCells("K$row:O$row"); // With Glasses (Left)
                $sheet->mergeCells("P$row:S$row"); // Color Blindness

                // Set header values
                $sheet->setCellValue("A$row", "Vision");
                $sheet->setCellValue("F$row", "Without Glasses (Right)");
                $sheet->setCellValue("K$row", "With Glasses (Left)");
                $sheet->setCellValue("P$row", "Color Blindness");

                // Style header
                $sheet->getStyle("A$row:S$row")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,

                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
                ]);

                // Distance row
                $row++;
                $sheet->mergeCells("A$row:E$row");
                $sheet->mergeCells("F$row:J$row");
                $sheet->mergeCells("K$row:O$row");
                $sheet->mergeCells("P$row:S$row");

                $sheet->setCellValue("A$row", "Distance");
                $sheet->setCellValue("F$row", $physicalHealth->distance_without_glass ?? '');
                $sheet->setCellValue("K$row", $physicalHealth->distance_with_glass ?? '');

                $distanceCheck = $physicalHealth->distance_without_glass_yes ?? null;
                $sheet->setCellValue("P$row", $distanceCheck === '1' ? '✓' : ($distanceCheck === '0' ? '✗' : ''));

                // Style distance row
                $sheet->getStyle("A$row:S$row")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'font' => [

                        'size' => 12
                    ]
                ]);

                // Near row
                $row++;
                $sheet->mergeCells("A$row:E$row");
                $sheet->mergeCells("F$row:J$row");
                $sheet->mergeCells("K$row:O$row");
                $sheet->mergeCells("P$row:S$row");

                $sheet->setCellValue("A$row", "Near");
                $sheet->setCellValue("F$row", $physicalHealth->near_without_glass ?? '');
                $sheet->setCellValue("K$row", $physicalHealth->near_with_glass ?? '');

                $nearCheck = $physicalHealth->near_without_glass_yes ?? null;
                $sheet->setCellValue("P$row", $nearCheck === '1' ? '✓' : ($nearCheck === '0' ? '✗' : ''));

                // Style near row
                $sheet->getStyle("A$row:S$row")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'font' => [

                        'size' => 12
                    ]
                ]);
                $remarksRow = $row + 1;

                // Merge entire row A to S
                $sheet->mergeCells("A$remarksRow:S$remarksRow");

                // Create rich text for remarks
                $richText2 = new RichText();
                $richText2->createTextRun('REMARKS BY MEDICAL OFFICER :-  ')->getFont()->setBold(true);
                $richText2->createText($physicalHealth->remarks ?? '');


                $sheet->getCell("A$remarksRow")->setValue($richText2);


                $sheet->getStyle("A$remarksRow:S$remarksRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $row =   $remarksRow + 4;
            }


            $writer = new Xlsx($spreadsheet);
            $fileName = 'physical_health_examination.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            report($e);
            return back()->with('error', 'Something went wrong');
        }
    }




    public function ExportPdf(Request $request)
    {

        try {

            ini_set("pcre.backtrack_limit", "5000000");

            $allData = $this->physicalHealth->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } else if (count($allData) > 20) {
                return redirect()->back()->with('error', __('inspection.excess_error'));
            }


            foreach ($allData as $details) {
                $document_no = $this->document_reference->selectOne($details->document_reference_id);
            }
            $personalDetails = $this->personalDetails->personalDetails();
            $familyHistory = $this->familyHistory->familyHistory();
            $check_points = getCheckListQuestion(OHC_PHYSICAL_HEALTH_EXAMINATION);
            $data = array(
                'content' => $allData,
                'personalDetails' => $personalDetails,
                'familyHistory' => $familyHistory,
                'check_points' => $check_points,
                'document_no' => $document_no,
                'pagetitle' => "Physical Health Examination Check-up",
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

            $filename = "Physical Health Examination Check-up.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/physical-medical-examination/yearly/list'));
        }
    }


    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
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
                    'pagetitle' => "Physical Health Examination Check-up",

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

            $filename = "Physical Health Examination Check-up.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/physical-medical-examination/yearly/list'));
        }
    }
}
