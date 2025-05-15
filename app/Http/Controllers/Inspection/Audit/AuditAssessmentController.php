<?php

namespace App\Http\Controllers\Inspection\Audit;

use Exception;
use Response;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Master\Employee;
use App\Models\Inspection\audit\AuditAssessment;
use App\Models\Inspection\Master\ChecklistType;
use App\Models\Inspection\Master\ChecklistSubType;
use App\Models\Inspection\Master\ChecklistSubTypeDataName;
use App\Models\Inspection\Master\ChecklistSubTypeData;
use App\Models\Inspection\Master\ChecklistOptionType;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\InspectionStaticDocno;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class AuditAssessmentController extends Controller
{

    private $checklist_type;
    private $checklist_subtype;
    private $checklist_subtypedata;
    private $checklist_subtypename;
    private $audit_assessment;
    private $upload_log;
    private $checklist_option;
    private $shift;
    private $document_reference;


    public function __construct()
    {
        $this->audit_assessment = new AuditAssessment();
        $this->checklist_type = new ChecklistType();
        $this->checklist_subtype = new ChecklistSubType();
        $this->checklist_option = new ChecklistOptionType();
        $this->checklist_option = new ChecklistOptionType();
        $this->checklist_subtypename = new ChecklistSubTypeDataName();
        $this->checklist_subtypedata = new ChecklistSubTypeData();
        $this->shift = new Shift();
        $this->document_reference = new InspectionStaticDocno();
    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =    $this->audit_assessment->list();
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
                        ->addColumn('audit_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('audit/assessment/view/' . encryptId($row->id)) . '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                            // $btn .= '<a href="' . admin_url('inspection/master/checklist-sub-type/edit/' . encryptId($row->id)) . '" class="edit-icon " title="' . __('common.edit') . '"><i class="fa-solid fa-pen-to-square"></i> ';
                            // }

                            $btn .= '<a href="' . admin_url('audit/assessment/generalpdf/' . encryptId($row->id)) . '" class="view-icon me-1" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';
                            $btn .= '<a href="' . admin_url('audit/assessment/generalExcel/' . encryptId($row->id)) . '" class="view-icon me-1" style="margin-right: 5px;" title="PDF">
                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                     </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'audit_date', 'created_by', 'status'])
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
        $checklist_types  = $this->checklist_type->select('id', 'category_name')->where('status', '1')->get();
        $data = array(
            'checklist_types' => $checklist_types,

        );
        return view('inspection.inspection_audit.auditAssessment.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $checklist_types  = $this->checklist_type->select('id', 'category_name')->where('status', '1')->get();
            $shift  = $this->shift->select('id', 'shift')->where('status', '1')->get();
            $checklist_details = getCheckListQuestion(CHECKLIST_AUDIT_ASSESSMENT);

            $options =  getoption(CHECKLIST_AUDIT_ASSESSMENT);
            $getoption = string_to_array($options->type);

            if (count($checklist_details) <= 0) {

                Session::flash('success', __('inspection.checklist_add'));
                return redirect(admin_url('inspection/master/checklist-sub-type-data/add'));
            }
            $data = array(
                'checklist_types' => $checklist_types,
                'shift' => $shift,
                'checklist_details' => $checklist_details,
                'getoption' => $getoption,
            );
            return view('inspection.inspection_audit.auditAssessment.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('audit/assessment/list'));
        }
    }

    public function employeename(Request $request)
    {
        $name = $request->input('search');

        $employees = Employee::where('emp_name', 'like', '%' . $name . '%')
            ->orWhere('emp_id', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->limit(10)
            ->get();


        return response()->json(
            $employees->map(function ($employee) {
                return [
                    'id' => encryptId($employee->id),
                    'text' => $employee->emp_name . ' - ' . $employee->emp_id,
                ];
            })
        );
    }
    public function store(Request $request)
    {

        try {
            try {

                $this->audit_assessment->store();

                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {


                report($ex);
                Session::flash('error', __('common.message_error'));
            }

            return redirect(admin_url('audit/assessment/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('audit/assessment/list'));
        }
    }
    public function view($id)
    {
        try {
            $id = decryptId($id);
            if (Auth::check()) {
                $audit_assessment =   $this->audit_assessment->selectOne($id);


                $data = array(
                    'audit_assessment' => $audit_assessment,
                );
            }
            return view('inspection.inspection_audit.auditAssessment.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('audit/assessment/list'));
        }
    }



    public function statusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $this->audit_assessment->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => '6S Audit Assessment status changed'], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function exportExcel()
    {
        try {

            $allData =   $this->audit_assessment->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }
            $row = 1;
            $currentRow = $row;
            foreach ($allData as $details) {
                $currentRow = $row;
                $audit_assessment =   $this->audit_assessment->selectOne($details->id);
                $audit_assessmentCkeclist = json_decode($audit_assessment);
                $document_no = $this->document_reference->selectUsingName('6SAuditAssessment');



                // Logo Section
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
                $sheet->setCellValue("G{$currentRow}", "6S AUDIT ASSESSMENT");
                $sheet->getStyle("G{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);


                if ($document_no) {
                    $sheet->mergeCells("N$currentRow:S" . ($currentRow + 2));
                    $sheet->setCellValue("N$currentRow", $document_no->doc_no);
                    $sheet->getStyle("N$currentRow:S" . ($currentRow + 2))->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'font' => ['bold' => true],
                    ]);
                }

                $sheet->mergeCells("A" . ($currentRow + 3) . ":S" . ($currentRow + 3));
                $richText1 = new RichText();
                $richText1->createTextRun('Name Of The Shop Floor:- ')->getFont()->setBold(true);
                $richText1->createText(($audit_assessment->floor_name));
                $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

                $range = "A$currentRow:s" . ($currentRow + 3);
                $sheet->getStyle($range)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],

                ]);

                $sheet->mergeCells("A" . ($currentRow + 4) . ":S" . ($currentRow + 4));
                $richText1 = new RichText();
                $richText1->createTextRun(' Date Of Audit:- ')->getFont()->setBold(true);
                $richText1->createText(Displaydateformat($audit_assessment->audit_date));
                $sheet->getCell("A" . ($currentRow + 4))->setValue($richText1);

                $range = "A$currentRow:S" . ($currentRow + 4);
                $sheet->getStyle($range)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],

                ]);
                $sheet->mergeCells("A" . ($currentRow + 5) . ":S" . ($currentRow + 5));
                $richText1 = new RichText();
                $richText1->createTextRun('Shift:- ')->getFont()->setBold(true);
                $richText1->createText(getShift($audit_assessment->shift_id));
                $sheet->getCell("A" . ($currentRow + 5))->setValue($richText1);

                $range = "A$currentRow:S" . ($currentRow + 5);
                $sheet->getStyle($range)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],

                ]);

                $sheet->mergeCells("A" . ($currentRow + 6) . ":S" . ($currentRow + 6));
                $richText1 = new RichText();
                $richText1->createTextRun('Floor Executive On Duty:- ')->getFont()->setBold(true);
                $richText1->createText(getEmployeename($audit_assessment->floor_executive));
                $sheet->getCell("A" . ($currentRow + 6))->setValue($richText1);

                $range = "A$currentRow:S" . ($currentRow + 6);
                $sheet->getStyle($range)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],

                ]);
                $headerRow = $currentRow + 7;
                $sheet->mergeCells("A$headerRow:J$headerRow")->setCellValue("A$headerRow", "CHECK POINTS");
                $sheet->mergeCells("K$headerRow:S$headerRow")->setCellValue("K$headerRow", "YES/NO/N/A");
                $sheet->getStyle("A$headerRow:S$headerRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'font' => ['bold' => true],
                ]);

                $inspectionRow = $headerRow + 1;
                $srNo = 1;
                $user_response = json_decode($audit_assessment->checklist, true);

                if (!empty($user_response)) {
                    foreach ($user_response as $subcategory => $questions) {
                        if (!is_array($questions)) {
                            continue; // skip if not a valid sub-array
                        }

                        $rowCount = count($questions);
                        $firstRow = true;

                        foreach ($questions as $questionId => $answer) {
                            if ($firstRow) {
                                $sheet->mergeCells("A$inspectionRow:C" . ($inspectionRow + $rowCount - 1))
                                    ->setCellValue("A$inspectionRow", getSubcategoryname($subcategory));
                                $firstRow = false;
                            }

                            $sheet->mergeCells("D$inspectionRow:J$inspectionRow")
                                ->setCellValue("D$inspectionRow", getSubcategoryDataname($questionId));

                            $statusIcon = '-';
                            if (!empty($answer) && strtoupper($answer) == 'YES') {
                                $statusIcon = 'YES';
                            } elseif (!empty($answer) && strtoupper($answer) == 'NO') {
                                $statusIcon = 'NO';
                            } elseif (!empty($answer) && strtoupper($answer) == 'N/A') {
                                $statusIcon = 'N/A';
                            }

                            $sheet->mergeCells("K$inspectionRow:S$inspectionRow")
                                ->setCellValue("K$inspectionRow", $statusIcon);

                            $sheet->getStyle("A$inspectionRow:S$inspectionRow")->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                ],
                                'borders' => [
                                    'allBorders' => [
                                        'borderStyle' => Border::BORDER_THIN,
                                    ],
                                ],
                            ]);

                            $inspectionRow++;
                            $srNo++;
                        }
                    }
                } else {
                    $sheet->mergeCells("A$inspectionRow:S$inspectionRow")
                        ->setCellValue("A$inspectionRow", 'No Questions Found!');

                    $sheet->getStyle("A$inspectionRow:S$inspectionRow")->applyFromArray([
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ],
                        ],
                    ]);

                    $inspectionRow++;
                }



                $row =  $inspectionRow + 2;
            }
            $filename = 'Audit_Assesment.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('audit/assessment/list'));
        }
    }

    public function exportPDF()
    {
        try {


            $allData =   $this->audit_assessment->exportdata();
            $document_no = $this->document_reference->selectUsingName('6SAuditAssessment');

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }
            $data = array(

                'content' => $allData,
                'document_no' => $document_no,
                'pagetitle' => "6S Audit Assessment",
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

            $view = view('inspection.inspection_audit.auditAssessment.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "6S Audit Assessment.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('audit/assessment/list'));
        }
    }

    public function import(Request $request)
    {
        $data = array();

        return view('master.checklist_subtype.import', $data);
    }

    public function generalpdf($id)
    {
        try {
            $audit_id = decryptId($id);
            if (Auth::check()) {
                $document_no = $this->document_reference->selectUsingName('6SAuditAssessment');
                $audit_assessment =   $this->audit_assessment->selectOne($audit_id);
                $audit_assessmentCkeclist = json_decode($audit_assessment);
                $checklist_details = getCheckListQuestion(CHECKLIST_AUDIT_ASSESSMENT);
                $options =  getoption(CHECKLIST_AUDIT_ASSESSMENT);
                $getoption = string_to_array($options->type);
            }
            $data = [
                'audit_assessment' => $audit_assessment,
                'getoption' => $getoption,
                'document_no' => $document_no,
                'pagetitle' => "6S Audit Assessment",
            ];

            $property = [
                'tempDir' => 'public/pdf/temp/',
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,

            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $html = view('inspection.inspection_audit.auditAssessment.viewpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "6S Audit Assessment.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('audit/assessment/list'));
        }
    }


    public function generalExcel($id, Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $audit_assessment =   $this->audit_assessment->selectOne($id);
            $audit_assessmentCkeclist = json_decode($audit_assessment);
            $document_no = $this->document_reference->selectUsingName('6SAuditAssessment');

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }
            $row = 1;
            $currentRow = $row;

            // Logo Section
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
            $sheet->setCellValue("G{$currentRow}", "6S AUDIT ASSESSMENT");
            $sheet->getStyle("G{$currentRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);


            if ($document_no) {
                $sheet->mergeCells("N$currentRow:S" . ($currentRow + 2));
                $sheet->setCellValue("N$currentRow", $document_no->doc_no);
                $sheet->getStyle("N$currentRow:S" . ($currentRow + 2))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'font' => ['bold' => true],
                ]);
            }

            $sheet->mergeCells("A" . ($currentRow + 3) . ":S" . ($currentRow + 3));
            $richText1 = new RichText();
            $richText1->createTextRun('Name Of The Shop Floor:- ')->getFont()->setBold(true);
            $richText1->createText(($audit_assessment->floor_name));
            $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

            $range = "A$currentRow:s" . ($currentRow + 3);
            $sheet->getStyle($range)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],

            ]);

            $sheet->mergeCells("A" . ($currentRow + 4) . ":S" . ($currentRow + 4));
            $richText1 = new RichText();
            $richText1->createTextRun(' Date Of Audit:- ')->getFont()->setBold(true);
            $richText1->createText(Displaydateformat($audit_assessment->audit_date));
            $sheet->getCell("A" . ($currentRow + 4))->setValue($richText1);

            $range = "A$currentRow:S" . ($currentRow + 4);
            $sheet->getStyle($range)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],

            ]);
            $sheet->mergeCells("A" . ($currentRow + 5) . ":S" . ($currentRow + 5));
            $richText1 = new RichText();
            $richText1->createTextRun('Shift:- ')->getFont()->setBold(true);
            $richText1->createText(getShift($audit_assessment->shift_id));
            $sheet->getCell("A" . ($currentRow + 5))->setValue($richText1);

            $range = "A$currentRow:S" . ($currentRow + 5);
            $sheet->getStyle($range)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],

            ]);

            $sheet->mergeCells("A" . ($currentRow + 6) . ":S" . ($currentRow + 6));
            $richText1 = new RichText();
            $richText1->createTextRun('Floor Executive On Duty:- ')->getFont()->setBold(true);
            $richText1->createText(getEmployeename($audit_assessment->floor_executive));
            $sheet->getCell("A" . ($currentRow + 6))->setValue($richText1);

            $range = "A$currentRow:S" . ($currentRow + 6);
            $sheet->getStyle($range)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],

            ]);
            $headerRow = $currentRow + 7;
            $sheet->mergeCells("A$headerRow:J$headerRow")->setCellValue("A$headerRow", "CHECK POINTS");
            $sheet->mergeCells("K$headerRow:S$headerRow")->setCellValue("K$headerRow", "YES/NO/N/A");
            $sheet->getStyle("A$headerRow:S$headerRow")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font' => ['bold' => true],
            ]);

            $inspectionRow = $headerRow + 1;
            $srNo = 1;
            $user_response = json_decode($audit_assessment->checklist, true);
            if (!empty($user_response)) {
                foreach ($user_response as $subcategory => $questions) {
                    $rowCount = count($questions);
                    $firstRow = true;
                    foreach ($questions as $questionId => $answer) {
                        if ($firstRow) {
                            // Merge and set subcategory only once per group
                            $sheet->mergeCells("A$inspectionRow:C" . ($inspectionRow + $rowCount - 1))
                                ->setCellValue("A$inspectionRow", getSubcategoryname($subcategory));
                            $firstRow = false;
                        }

                        // Write the question text
                        $sheet->mergeCells("D$inspectionRow:J$inspectionRow")
                            ->setCellValue("D$inspectionRow", getSubcategoryDataname($questionId));

                        // Handle status icon
                        $statusIcon = '-';
                        if (!empty($answer) && strtoupper($answer) == 'YES') {
                            $statusIcon = 'YES';
                        } elseif (!empty($answer) && strtoupper($answer) == 'NO') {
                            $statusIcon = 'NO';
                        } elseif (!empty($answer) && strtoupper($answer) == 'N/A') {
                            $statusIcon = 'N/A';
                        }

                        // Write the status icon
                        $sheet->mergeCells("K$inspectionRow:S$inspectionRow")
                            ->setCellValue("K$inspectionRow", $statusIcon);

                        $sheet->getStyle("A$inspectionRow:S$inspectionRow")->applyFromArray([
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                ],
                            ],
                        ]);

                        $inspectionRow++;
                        $srNo++;
                    }
                }
            } else {
                $sheet->mergeCells("A$inspectionRow:S$inspectionRow")
                    ->setCellValue("A$inspectionRow", 'No Questions Found!');

                $sheet->getStyle("A$inspectionRow:S$inspectionRow")->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                $inspectionRow++;
            }


            $filename = 'Audit_Assesment.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('audit/assessment/list'));
        }
    }
}
