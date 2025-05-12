<?php

namespace App\Http\Controllers\Inspection\Audit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Master\Unit;
use App\Models\Master\Department;
use App\Models\Inspection\audit\AuditAnalysis;
use App\Models\Inspection\audit\AuditAnalysisChecklist;
use App\Models\Inspection\MSDSCheckList;
use Exception;
use App\Models\Inspection\InspectionStaticDocno;
use Spatie\SimpleExcel\SimpleExcelWriter;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AuditAnalysisController extends Controller
{
    private $unit;
    private $department;
    private $auditAnalysis;
    private $auditAnalysisCheckList;
    private $static_docno;

    public function __construct()
    {
        $this->auditAnalysis = new AuditAnalysis();
        $this->auditAnalysisCheckList = new AuditAnalysisChecklist();
        $this->department = new Department();
        $this->unit = new Unit();
        $this->static_docno = new InspectionStaticDocno();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->auditAnalysis->list();
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
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('audit/6s-analysis/view/' . encryptId($row->id)) . '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                            // $btn .= '<a href="' . admin_url('audit/6s-analysis/edit/' . encryptId($row->id)) . '" class="edit-icon " title="' . __('common.edit') . '"><i class="fa-solid fa-pen-to-square"></i> ';
                            // $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '"  class="recordDelete" title="' . __('common.delete') . '"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            // }
                            $btn .= '<a href="' . admin_url('audit/6s-analysis/exportViewPdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                                    </a>';

                            $btn .= '<a href="' . admin_url('audit/6s-analysis/generalExcel/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="Excel"> <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i></a>';

                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status'])
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

        $auditAnalysisList  = $this->auditAnalysis->select('id', 'audit_analysis_id')->where('status', '1')->get();

        $data = array(
            'auditAnalysisList' => $auditAnalysisList,
        );
        return view('inspection.inspection_audit.auditAnalysis.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $staticDocno  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                ['type', "6SAuditAnalysis"],
                ['status', '1']
                ])->first();

            $data = array(
                'departmentList' => $departmentList,
                'unitList' => $unitList,
                'staticDocno' => $staticDocno,
            );
            return view('inspection.inspection_audit.auditAnalysis.add', $data);
        } catch (Exception $ex) {
            report($ex);
             Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('audit/6s-analysis/list'));
        }
    }

    public function Store(Request $request)
    {
        try {
            try {
                $auditAnalysis = $this->auditAnalysis->store();
                $auditanalysis_id = $auditAnalysis->id;
                $this->auditAnalysisCheckList->store($auditanalysis_id);

                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {

               report($ex);
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('audit/6s-analysis/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('audit/6s-analysis/list'));
        }
    }
    public function Uniquecheck(Request $request)
    {
        $ids = decryptId($request->input('id'));
        $departmentId = decryptId($request->input('departmentId'));
        $unitId = decryptId($request->input('unitId'));
        $year = (new \DateTime($request->year))->format('Y');
        $month = (new \DateTime($request->month))->format('m');

        if (empty($ids)) {
            $conflicts = $this->auditAnalysisCheckList->getUniqueSchedule($departmentId, $unitId, $year, $month);
        } else {
            $conflicts = $this->auditAnalysisCheckList->getExistUniqueSchedule($departmentId, $unitId, $year, $month, $ids);
        }

        return response()->json(['conflicts' => $conflicts]);
    }


    public function StatusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->auditAnalysis->statuschange($id);

            $this->auditAnalysisCheckList->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Your status  has changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function View($id)
    {
        try {
            $id = decryptId($id);
            if (Auth::check()) {
                $auditData =   $this->auditAnalysis->selectOne($id);
                $auditAnalysisData =   $this->auditAnalysisCheckList->selectOne($id);
                $staticDocno  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                    ['type', "6SAuditAnalysis"],
                    ['status', '1']
                ])->first();
                $data = array(
                    'auditData' => $auditData,
                    'auditAnalysisData' => $auditAnalysisData,
                    'staticDocno' => $staticDocno,
                );
            }
            return view('inspection.inspection_audit.auditAnalysis.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('audit/6s-analysis/list'));
        }
    }


    public function ExportExcel(Request $request)
    {
        try {

            $allData = $this->auditAnalysis->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            foreach (range('A', 'S') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            for ($i = 1; $i <= 1000; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $row = 1;

            foreach ($allData as $auditAnalysisData) {

                $srGlobal = 1;
                $rowStart = $row;

                $leftLogoPath = public_path('assets/images/logo-dark.png');
                if (file_exists($leftLogoPath)) {
                    $leftDrawing = new Drawing();
                    $leftDrawing->setName('LeftLogo');
                    $leftDrawing->setDescription('Left Company Logo');
                    $leftDrawing->setPath($leftLogoPath);
                    $leftDrawing->setCoordinates('A' . $row);
                    $leftDrawing->setOffsetX(35);
                    $leftDrawing->setOffsetY(5);
                    $leftDrawing->setHeight(60);
                    $leftDrawing->setWorksheet($sheet);
                }
                $sheet->mergeCells("A{$row}:B" . ($row + 2));
                $sheet->getStyle("A{$row}:B" . ($row + 2))->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $leftAuditLogo = public_path('assets/images/audit_left_logo.jpg');
                if (file_exists($leftAuditLogo)) {
                    $leftAuditDrawing = new Drawing();
                    $leftAuditDrawing->setName('LeftAuditLogo');
                    $leftAuditDrawing->setDescription('Left Audit Logo');
                    $leftAuditDrawing->setPath($leftAuditLogo);
                    $leftAuditDrawing->setCoordinates('C' . $row);
                    $leftAuditDrawing->setOffsetX(25);
                    $leftAuditDrawing->setOffsetY(10);
                    $leftAuditDrawing->setHeight(60);
                    $leftAuditDrawing->setWorksheet($sheet);
                }
                $sheet->mergeCells('C'.$row.':D'.($row+2));

                $rightAuditLogo = public_path('assets/images/audit_right_logo.jpg');
                if (file_exists($rightAuditLogo)) {
                    $rightAuditDrawing = new Drawing();
                    $rightAuditDrawing->setName('RightAuditLogo');
                    $rightAuditDrawing->setDescription('Right Audit Logo');
                    $rightAuditDrawing->setPath($rightAuditLogo);
                    $rightAuditDrawing->setCoordinates('P' . $row);
                    $rightAuditDrawing->setOffsetX(130);
                    $rightAuditDrawing->setOffsetY(10);
                    $rightAuditDrawing->setHeight(60);
                    $rightAuditDrawing->setWorksheet($sheet);
                }
                $sheet->mergeCells('P'.$row.':Q'.($row+2));

                $sheet->mergeCells('E'.$row.':O'.($row+2));
                $sheet->setCellValue('E'.$row, "   6'S AUDIT ANALYSIS REPORT");
                $sheet->getStyle('E'.$row.':O'.($row+2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $document_no = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')
                    ->where([
                        ['type', "6SAuditAnalysis"],
                        ['status', '1']
                    ])->first();

                $labelMap = [
                    'R' . $row => ['label' => 'Doc. No.', 'value' => $document_no->doc_no ?? ''],
                    'R' . ($row + 1) => ['label' => 'Issue Dt.', 'value' => Displaydateformat($document_no->issue_date) ?? ''],
                    'R' . ($row + 2) => ['label' => 'Rev. & Dt.', 'value' => $document_no->rev_dt ?? ''],
                ];

                foreach ($labelMap as $cell => $info) {
                    $sheet->setCellValue($cell, $info['label']);
                    $sheet->setCellValue(chr(ord($cell[0])+1).substr($cell,1), $info['value']);
                    $sheet->getStyle($cell)->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    ]);
                    $sheet->getStyle(chr(ord($cell[0])+1).substr($cell,1))->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    ]);
                }

                $row += 3;

                $months = ['April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'January', 'February', 'March'];

                $sheet->mergeCells('A'.$row.':A'.($row+1))->setCellValue('A'.$row, 'Sr. No.');
                $sheet->mergeCells('B'.$row.':B'.($row+1))->setCellValue('B'.$row, 'Department Name');
                $sheet->mergeCells('C'.$row.':C'.($row+1))->setCellValue('C'.$row, 'Unit');
                $sheet->mergeCells('D'.$row.':O'.$row)->setCellValue('D'.$row, 'MARKS OBTAINED');

                $col = 'D';
                foreach ($months as $month) {
                    $sheet->setCellValue($col.($row+1), $month);
                    $col++;
                }

                $sheet->mergeCells('P'.$row.':P'.($row+1))->setCellValue('P'.$row, "Total No's of Audit");
                $sheet->mergeCells('Q'.$row.':Q'.($row+1))->setCellValue('Q'.$row, 'Total Marks');
                $sheet->mergeCells('R'.$row.':R'.($row+1))->setCellValue('R'.$row, 'Marks Obtained');
                $sheet->mergeCells('S'.$row.':S'.($row+1))->setCellValue('S'.$row, '%');

                $sheet->getStyle('A'.$row.':S'.($row+1))->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);

                $row += 2;

                foreach ($auditAnalysisData as $detail) {
                    $sheet->setCellValue('A'.$row, $srGlobal++);
                    $sheet->setCellValue('B'.$row, $detail['department_name'] ?? '');
                    $sheet->setCellValue('C'.$row, $detail['unit_name'] ?? '');

                    $marks = json_decode($detail['marks'] ?? '{}', true);
                    $col = 'D';
                    foreach ($months as $month) {
                        $value = $marks[strtolower($month)] ?? 0;
                        $sheet->setCellValue($col.$row, $value);
                        $col++;
                    }

                    $sheet->setCellValue('P'.$row, $detail['no_of_audit'] ?? '');
                    $sheet->setCellValue('Q'.$row, $detail['total_marks'] ?? '');
                    $sheet->setCellValue('R'.$row, $detail['marks_obtained'] ?? '');
                    $sheet->setCellValue('S'.$row, $detail['percentage'] ?? '');

                    $sheet->getStyle('A'.$row.':S'.$row)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $row++;
                }

                $lastDataRow = $row - 1;
                $sheet->getStyle('A'.$rowStart.':S'.$lastDataRow)->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $row += 4;
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = "6'S AUDIT ANALYSIS REPORT.xlsx";
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);

        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('audit/6s-analysis/list'));
        }
    }

    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->auditAnalysis->exportdata();

            $document_no = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')
                        ->where([
                            ['type', "6SAuditAnalysis"],
                            ['status', '1']
                        ])->first();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error', __('inspection.excess_error'));
            }

            $data = array(
                'content' => $allData,
                'document_no' => $document_no,
                'pagetitle' => "6'S AUDIT ANALYSIS REPORT",
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

            $view = view('inspection.inspection_audit.auditAnalysis.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "6'S AUDIT ANALYSIS REPORT.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('audit/6s-analysis/list'));
        }
    }



    public function ExportViewPDF(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {

                $auditData =   $this->auditAnalysis->selectOne($id);
                $auditAnalysisData =   $this->auditAnalysisCheckList->selectOne($id);

                $document_no  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                                    ['type', "6SAuditAnalysis"],
                                    ['status', '1']
                                ])->first();

                $data = [
                    'auditData' => $auditData,
                    'pagetitle' => " 6'S AUDIT ANALYSIS REPORT",
                    'auditAnalysisData' => $auditAnalysisData,
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

            $html = view('inspection.inspection_audit.auditAnalysis.viewPdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "6'S AUDIT ANALYSIS REPORT.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('audit/6s-analysis/list'));
        }
    }

    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $auditData = $this->auditAnalysis->selectOne($id);
            $auditAnalysisData = $this->auditAnalysisCheckList->selectOne($id);

            $document_no = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')
                ->where([
                    ['type', "6SAuditAnalysis"],
                    ['status', '1']
                ])->first();

            foreach (range('A', 'S') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $leftLogoPath = public_path('assets/images/logo-dark.png');
            if (file_exists($leftLogoPath)) {
                $leftDrawing = new Drawing();
                $leftDrawing->setName('LeftLogo');
                $leftDrawing->setDescription('Left Company Logo');
                $leftDrawing->setPath($leftLogoPath);
                $leftDrawing->setCoordinates('A1');
                $leftDrawing->setOffsetX(35);
                $leftDrawing->setOffsetY(5);
                $leftDrawing->setHeight(60);
                $leftDrawing->setWorksheet($sheet);
            }

            $sheet->mergeCells("A1:B3");
            $sheet->getStyle("A1:B3")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $leftAuditLogo = public_path('assets/images/audit_left_logo.jpg');
            if (file_exists($leftAuditLogo)) {
                $rightDrawing = new Drawing();
                $rightDrawing->setName('LeftAuditLogo');
                $rightDrawing->setDescription('Left Audit Logo');
                $rightDrawing->setPath($leftAuditLogo);
                $rightDrawing->setCoordinates('C1');
                $rightDrawing->setOffsetX(25);
                $rightDrawing->setOffsetY(10);
                $rightDrawing->setHeight(60);
                $rightDrawing->setWorksheet($sheet);
            }
            $sheet->mergeCells('C1:D3');

            $rightAuditLogo = public_path('assets/images/audit_right_logo.jpg');
            if (file_exists($rightAuditLogo)) {
                $rightDrawing = new Drawing();
                $rightDrawing->setName('RightAuditLogo');
                $rightDrawing->setDescription('Right Audit Logo');
                $rightDrawing->setPath($rightAuditLogo);
                $rightDrawing->setCoordinates('P1');
                $rightDrawing->setOffsetX(130);
                $rightDrawing->setOffsetY(10);
                $rightDrawing->setHeight(60);
                $rightDrawing->setWorksheet($sheet);
            }

            $sheet->mergeCells('P1:Q3');

            $sheet->mergeCells('E1:O3');
            $sheet->setCellValue('E1', "6'S AUDIT ANALYSIS REPORT");
            $sheet->getStyle('E1:O3')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $labelMap = [
                'R1' => ['label' => 'Doc. No.', 'value' => $document_no->doc_no ?? ''],
                'R2' => ['label' => 'Issue Dt.', 'value' => Displaydateformat($document_no->issue_date) ?? ''],
                'R3' => ['label' => 'Rev. & Dt.', 'value' => $document_no->rev_dt ?? ''],
            ];

            foreach ($labelMap as $cell => $info) {
                $sheet->setCellValue($cell, $info['label']);
                $sheet->setCellValue(chr(ord($cell[0]) + 1) . substr($cell, 1), $info['value']);
                $sheet->getStyle($cell)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                ]);
                $sheet->getStyle(chr(ord($cell[0]) + 1) . substr($cell, 1))->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                ]);
            }

            $months = ['April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'January', 'February', 'March'];

            $sheet->mergeCells('A4:A5')->setCellValue('A4', 'Sr. No.');
            $sheet->mergeCells('B4:B5')->setCellValue('B4', 'Department Name');
            $sheet->mergeCells('C4:C5')->setCellValue('C4', 'Unit');
            $sheet->mergeCells('D4:O4')->setCellValue('D4', 'MARKS OBTAINED');

            $col = 'D';
            foreach ($months as $month) {
                $sheet->setCellValue($col.'5', $month);
                $col++;
            }

            $sheet->mergeCells('P4:P5')->setCellValue('P4', "Total No's of Audit");
            $sheet->mergeCells('Q4:Q5')->setCellValue('Q4', 'Total Marks');
            $sheet->mergeCells('R4:R5')->setCellValue('R4', 'Marks Obtained');
            $sheet->mergeCells('S4:S5')->setCellValue('S4', '%');

            $sheet->getStyle('A4:S5')->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER,'wrapText' => true],
            ]);

            $row = 6;
            $sr = 1;

            foreach ($auditAnalysisData as $detail) {
                $sheet->setCellValue('A'.$row, $sr);
                $sheet->setCellValue('B'.$row, $detail['department_name'] ?? '');
                $sheet->setCellValue('C'.$row, $detail['unit_name'] ?? '');

                $marks = json_decode($detail['marks'] ?? '{}', true);
                $col = 'D';
                foreach ($months as $month) {
                    $value = $marks[strtolower($month)] ?? 0;
                    $sheet->setCellValue($col.$row, $value);
                    $col++;
                }

                $sheet->setCellValue('P'.$row, $detail['no_of_audit'] ?? '');
                $sheet->setCellValue('Q'.$row, $detail['total_marks'] ?? '');
                $sheet->setCellValue('R'.$row, $detail['marks_obtained'] ?? '');
                $sheet->setCellValue('S'.$row, $detail['percentage'] ?? '');

                $sheet->getStyle('A'.$row.':S'.$row)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sr++;
                $row++;

            }

            $writer = new Xlsx($spreadsheet);
            $fileName = "6S Audit Analysis Report.xlsx";
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);

        } catch (\Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('audit/6s-analysis/list'));
        }
    }




}
