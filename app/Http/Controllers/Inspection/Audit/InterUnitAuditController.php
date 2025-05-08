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
use App\Models\Inspection\audit\InterUnitAudit;
use App\Models\Inspection\Master\ChecklistType;
use App\Models\Inspection\Master\ChecklistSubType;
use App\Models\Inspection\Master\ChecklistSubTypeDataName;
use App\Models\Inspection\Master\ChecklistSubTypeData;
use App\Models\Inspection\Master\ChecklistOptionType;
use App\Models\Inspection\Master\Shift;
use App\Models\Master\Unit;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class InterUnitAuditController extends Controller
{

    private $checklist_type;
    private $checklist_subtype;
    private $checklist_subtypedata;
    private $checklist_subtypename;
    private $inter_unit_audit;
    private $upload_log;
    private $checklist_option;
    private $shift;
    private $unit;

    public function __construct()
    {
        $this->inter_unit_audit = new InterUnitAudit();
        $this->checklist_type = new ChecklistType();
        $this->checklist_subtype = new ChecklistSubType();
        $this->checklist_option = new ChecklistOptionType();
        $this->checklist_option = new ChecklistOptionType();
        $this->checklist_subtypename = new ChecklistSubTypeDataName();
        $this->checklist_subtypedata = new ChecklistSubTypeData();
        $this->shift = new Shift();
        $this->unit = new Unit();
    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =    $this->inter_unit_audit->list();
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
                            $btn = '<a href="' . admin_url('audit/inter-unit-audit/checklist/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                            // $btn .= '<a href="' . admin_url('inspection/master/checklist-sub-type/edit/' . encryptId($row->id)) . '" class="edit-icon " title="' . __('common.edit') . '"><i class="fa-solid fa-pen-to-square"></i> ';
                            // }

                            $btn .= '<a href="' . admin_url('audit/inter-unit-audit/checklist/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';
                            $btn .= '<a href="' . admin_url('audit/inter-unit-audit/checklist/generalExcel/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                     </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'audit_date', 'status'])
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
        return view('inspection.inspection_audit.interUnitAudit.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $checklist_types  = $this->checklist_type->select('id', 'category_name')->where('status', '1')->get();
            $unit  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $checklist_details = getCheckListQuestion(INTER_UNIT_AUDIT_CHECKLIST);
            $options =  getoption(INTER_UNIT_AUDIT_CHECKLIST);

            $getoption = string_to_array($options->type);
            if (count($checklist_details) <= 0) {
                Session::flash('error', __('inspection.checklist_add'));
                return redirect()->back();
            }
            $data = array(
                'checklist_types' => $checklist_types,
                'unit' => $unit,
                'checklist_details' => $checklist_details,
                'getoption' => $getoption,
            );
            return view('inspection.inspection_audit.interUnitAudit.add', $data);
        } catch (Exception $ex) {

            report($ex);
        }
    }
    public function store(Request $request)
    {

        try {
            try {

                $this->inter_unit_audit->store();

                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {


                report($ex);
                Session::flash('error', __('common.message_error'));
            }

            return redirect(admin_url('audit/inter-unit-audit/checklist/list'));
        } catch (Exception $ex) {
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('audit/inter-unit-audit/checklist/list'));
        }
    }
    public function view($id)
    {
        try {
            $id = decryptId($id);
            if (Auth::check()) {
                $inter_unit_audit =   $this->inter_unit_audit->selectOne($id);


                $data = array(
                    'inter_unit_audit' => $inter_unit_audit,
                );
            }
            return view('inspection.inspection_audit.interUnitAudit.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('audit/inter-unit-audit/checklist/list'));
        }
    }



    public function statusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $this->inter_unit_audit->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Inter Unit Monthly Audit status changed'], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function exportExcel()
    {
        try {

            $allData =   $this->inter_unit_audit->exportdata();
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

                $inter_unit_audit = $this->inter_unit_audit->selectOne($details->id);

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
                $sheet->setCellValue("G{$currentRow}", "Inter Unit Monthly Audit Checklist
    PN International Pvt Ltd & PNSPL Pvt Ltd");
                $sheet->getStyle("G{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);



                $sheet->mergeCells("N$currentRow:P$currentRow")->setCellValue("N$currentRow", 'Doc. No.');
                $sheet->mergeCells("N" . ($currentRow + 1) . ":P" . ($currentRow + 1))->setCellValue("N" . ($currentRow + 1), 'Issue Dt.');
                $sheet->mergeCells("N" . ($currentRow + 2) . ":P" . ($currentRow + 2))->setCellValue("N" . ($currentRow + 2), 'Rev. & Dt.');

                $sheet->mergeCells("Q$currentRow:S$currentRow")->setCellValue("Q$currentRow", $inter_unit_audit->doc_no);
                $sheet->mergeCells("Q" . ($currentRow + 1) . ":S" . ($currentRow + 1))->setCellValue("Q" . ($currentRow + 1), Displaydateformat($inter_unit_audit->issue_date));
                $sheet->mergeCells("Q" . ($currentRow + 2) . ":S" . ($currentRow + 2))->setCellValue("Q" . ($currentRow + 2), $inter_unit_audit->rev_dt);

                $sheet->getStyle("N$currentRow:S" . ($currentRow + 2))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'font' => ['bold' => true],
                ]);



                $sheet->mergeCells("A" . ($currentRow + 3) . ":H" . ($currentRow + 3));
                $richText1 = new RichText();
                $richText1->createTextRun('Name of Safety Officer:- ')->getFont()->setBold(true);
                $richText1->createText($inter_unit_audit->safety_officer);
                $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

                $sheet->mergeCells("I" . ($currentRow + 3) . ":N" . ($currentRow + 3));
                $richText2 = new RichText();
                $richText2->createTextRun('Date of Audit:- ')->getFont()->setBold(true);
                $richText2->createText(Displaydateformat($inter_unit_audit->audit_date));
                $sheet->getCell("I" . ($currentRow + 3))->setValue($richText2);

                $sheet->mergeCells("O" . ($currentRow + 3) . ":S" . ($currentRow + 3));
                $richText3 = new RichText();
                $richText3->createTextRun('Unit:- ')->getFont()->setBold(true);
                $richText3->createText(getUnitname($inter_unit_audit->unit_id));
                $sheet->getCell("O" . ($currentRow + 3))->setValue($richText3);

                $range = "A$currentRow:S" . ($currentRow + 3);
                $sheet->getStyle($range)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $headerRow = $currentRow + 4;
                $sheet->mergeCells("A$headerRow:C$headerRow")->setCellValue("A$headerRow", "SERIAL NO");
                $sheet->mergeCells("D$headerRow:J$headerRow")->setCellValue("D$headerRow", "CHECK POINTS");
                $sheet->mergeCells("K$headerRow:N$headerRow")->setCellValue("K$headerRow", "OK/NOT-OK/N/A");
                $sheet->mergeCells("O$headerRow:S$headerRow")->setCellValue("O$headerRow", "REMARKS");
                $sheet->getStyle("A$headerRow:S$headerRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'font' => ['bold' => true],
                ]);

                $inspectionRow = $headerRow + 1;
                $srNo = 1;
                $user_response = json_decode($inter_unit_audit->checklist, true);
                $displayedSections = [];

                foreach ($user_response as $checklistId => $data) {
                    $sectionName = GetSubChecklistTypeName($data['sub_type_id']);

                    if (!in_array($sectionName, $displayedSections)) {
                        $sheet->mergeCells("A$inspectionRow:S$inspectionRow")
                            ->setCellValue("A$inspectionRow", strtoupper($sectionName));
                        $sheet->getStyle("A$inspectionRow")->applyFromArray([
                            'font' => ['bold' => true],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        ]);
                        $displayedSections[] = $sectionName;
                        $inspectionRow++;
                    }

                    $sheet->mergeCells("A$inspectionRow:C$inspectionRow")->setCellValue("A$inspectionRow", $srNo++);
                    $sheet->mergeCells("D$inspectionRow:J$inspectionRow")->setCellValue("D$inspectionRow", GetChecklistTypeDate($checklistId));
                    $sheet->mergeCells("K$inspectionRow:N$inspectionRow")->setCellValue("K$inspectionRow", $data['response']);
                    $sheet->mergeCells("O$inspectionRow:S$inspectionRow")->setCellValue("O$inspectionRow", $data['remarks']);

                    $sheet->getStyle("A$inspectionRow:S$inspectionRow")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $inspectionRow++;
                }

                $row =  $inspectionRow + 2;
            }
            $fileName = 'inter_unit_audit_assessment.xlsx';
            $writer = new Xlsx($spreadsheet);

            // Output to browser
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment;filename=\"$fileName\"");
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function exportPDF()
    {
        try {

            ini_set("pcre.backtrack_limit", "5000000");

            $allData =   $this->inter_unit_audit->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }
            $data = array(

                'content' => $allData,
                'pagetitle' => "Inter Unit Monthly Audit",
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

            $view = view('inspection.inspection_audit.interUnitAudit.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Inter Unit Monthly Audit.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
           report($ex);
        }
    }

    public function generalpdf($id)
    {
        try {
            $audit_id = decryptId($id);
            if (Auth::check()) {

                $inter_unit_audit =   $this->inter_unit_audit->selectOne($audit_id);
                $audit_assessmentCkeclist = json_decode($inter_unit_audit);
                $checklist_details = getCheckListQuestion(INTER_UNIT_AUDIT_CHECKLIST);
                $options =  getoption(INTER_UNIT_AUDIT_CHECKLIST);
                $getoption = string_to_array($options->type);
            }
            $data = [
                'inter_unit_audit' => $inter_unit_audit,
                'getoption' => $getoption,
                'pagetitle' => "Inter Unit Monthly Audit",
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

            $html = view('inspection.inspection_audit.interUnitAudit.viewpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "Inter Unit Monthly Audit.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
           report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }

    public function generalExcel($id, Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $inter_unit_audit = $this->inter_unit_audit->selectOne($id);

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
            $sheet->setCellValue("G{$currentRow}", "Inter Unit Monthly Audit Checklist
PN International Pvt Ltd & PNSPL Pvt Ltd");
            $sheet->getStyle("G{$currentRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);



            $sheet->mergeCells("N$currentRow:P$currentRow")->setCellValue("N$currentRow", 'Doc. No.');
            $sheet->mergeCells("N" . ($currentRow + 1) . ":P" . ($currentRow + 1))->setCellValue("N" . ($currentRow + 1), 'Issue Dt.');
            $sheet->mergeCells("N" . ($currentRow + 2) . ":P" . ($currentRow + 2))->setCellValue("N" . ($currentRow + 2), 'Rev. & Dt.');

            $sheet->mergeCells("Q$currentRow:S$currentRow")->setCellValue("Q$currentRow", $inter_unit_audit->doc_no);
            $sheet->mergeCells("Q" . ($currentRow + 1) . ":S" . ($currentRow + 1))->setCellValue("Q" . ($currentRow + 1), Displaydateformat($inter_unit_audit->issue_date));
            $sheet->mergeCells("Q" . ($currentRow + 2) . ":S" . ($currentRow + 2))->setCellValue("Q" . ($currentRow + 2), $inter_unit_audit->rev_dt);

            $sheet->getStyle("N$currentRow:S" . ($currentRow + 2))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font' => ['bold' => true],
            ]);



            $sheet->mergeCells("A" . ($currentRow + 3) . ":H" . ($currentRow + 3));
            $richText1 = new RichText();
            $richText1->createTextRun('Name of Safety Officer:- ')->getFont()->setBold(true);
            $richText1->createText($inter_unit_audit->safety_officer);
            $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

            $sheet->mergeCells("I" . ($currentRow + 3) . ":N" . ($currentRow + 3));
            $richText2 = new RichText();
            $richText2->createTextRun('Date of Audit:- ')->getFont()->setBold(true);
            $richText2->createText(Displaydateformat($inter_unit_audit->audit_date));
            $sheet->getCell("I" . ($currentRow + 3))->setValue($richText2);

            $sheet->mergeCells("O" . ($currentRow + 3) . ":S" . ($currentRow + 3));
            $richText3 = new RichText();
            $richText3->createTextRun('Unit:- ')->getFont()->setBold(true);
            $richText3->createText(getUnitname($inter_unit_audit->unit_id));
            $sheet->getCell("O" . ($currentRow + 3))->setValue($richText3);

            $range = "A$currentRow:S" . ($currentRow + 3);
            $sheet->getStyle($range)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $headerRow = $currentRow + 4;
            $sheet->mergeCells("A$headerRow:C$headerRow")->setCellValue("A$headerRow", "SERIAL NO");
            $sheet->mergeCells("D$headerRow:J$headerRow")->setCellValue("D$headerRow", "CHECK POINTS");
            $sheet->mergeCells("K$headerRow:N$headerRow")->setCellValue("K$headerRow", "OK/NOT-OK/N/A");
            $sheet->mergeCells("O$headerRow:S$headerRow")->setCellValue("O$headerRow", "REMARKS");
            $sheet->getStyle("A$headerRow:S$headerRow")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font' => ['bold' => true],
            ]);

            $inspectionRow = $headerRow + 1;
            $srNo = 1;
            $user_response = json_decode($inter_unit_audit->checklist, true);
            $displayedSections = [];

            foreach ($user_response as $checklistId => $data) {
                $sectionName = GetSubChecklistTypeName($data['sub_type_id']);

                if (!in_array($sectionName, $displayedSections)) {
                    $sheet->mergeCells("A$inspectionRow:S$inspectionRow")
                        ->setCellValue("A$inspectionRow", strtoupper($sectionName));
                    $sheet->getStyle("A$inspectionRow")->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    $displayedSections[] = $sectionName;
                    $inspectionRow++;
                }

                $sheet->mergeCells("A$inspectionRow:C$inspectionRow")->setCellValue("A$inspectionRow", $srNo++);
                $sheet->mergeCells("D$inspectionRow:J$inspectionRow")->setCellValue("D$inspectionRow", GetChecklistTypeDate($checklistId));
                $sheet->mergeCells("K$inspectionRow:N$inspectionRow")->setCellValue("K$inspectionRow", $data['response']);
                $sheet->mergeCells("O$inspectionRow:S$inspectionRow")->setCellValue("O$inspectionRow", $data['remarks']);

                $sheet->getStyle("A$inspectionRow:S$inspectionRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $inspectionRow++;
            }

            $fileName = 'inter_unit_audit_assessment.xlsx';
            $writer = new Xlsx($spreadsheet);

            // Output to browser
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment;filename=\"$fileName\"");
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            dd($e);
            return back()->with('error', 'Something went wrong while generating Excel.');
        }
    }
}
