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
                            $btn = '<a href="' . admin_url('audit/6s-analysis/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
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

                dd($ex);
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

            $header = [
                __("common.sno"),
                'Document Number',
                'Issue Date',
                'Revision Date',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->document_number;
                $export[] =  $data->issue_date;
                $export[] = $data->revision_date;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('MSDS.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
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

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Issue Date',
                'Revision Date',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "MSDS Details",
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

            $filename = "MSDS.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('audit/6s-analysis/list'));
        }
    }

    public function edit($id)
    {
        try {
            $id = decryptId($id);

            $msdsDetails = $this->auditAnalysis->find($id);

            $msdsCheckList = $this->msdsCheckList->selectOne($id);

            $data = [
                'msdsDetails' => $msdsDetails,
                'msdsCheckList' => $msdsCheckList  ?? [],
            ];

            return view('inspection.inspection_audit.auditAnalysis.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function update(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $rules = [
                'document_number' => 'required',
                'issue_date' => 'required',
                'revision_date' => 'required',
                'item_code' => 'required',
                'name_of_chemical' => 'required',
                'msds_availability_status' => 'required',
                'remark' => 'required',
            ];
            $messages = [
                'document_number.required' => __('Document Number is required'),
                'issue_date.required' => __('Issue Date is required'),
                'revision_date.required' => __('Revision Date is required'),
                'item_code.required' => __('Item Code is required'),
                'name_of_chemical.required' => __('Name of Chemical is required'),
                'msds_availability_status.required' => __('MSDS Availability Status is required'),
                'remark.required' => __('Remark is required'),
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $msds = $this->auditAnalysis->updates($id);
                $msds_details = $this->auditAnalysis->selectOne($id);
                $msdsId = $msds_details->id;

                $this->msdsCheckList->updates($msdsId);

                Session::flash('success', __('Your data has been updated successfully'));
            } catch (Exception $ex) {
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('audit/6s-analysis/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
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

    // public function generalExcel(Request $request)
    // {
    //     try {

    //         $id = decryptId($request->id);
    //         $spreadsheet = new Spreadsheet();
    //         $sheet = $spreadsheet->getActiveSheet();

    //         $auditData =   $this->auditAnalysis->selectOne($id);
    //         $auditAnalysisData =   $this->auditAnalysisCheckList->selectOne($id);

    //         $document_no  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
    //                             ['type', "6SAuditAnalysis"],
    //                             ['status', '1']
    //                         ])->first();

    //         foreach (range('A', 'L') as $col) {
    //             $sheet->getColumnDimension($col)->setAutoSize(true);
    //         }

    //         for ($i = 1; $i <= 200; $i++) {
    //             $sheet->getRowDimension($i)->setRowHeight(25);
    //         }

    //         $logoPath = public_path('assets/images/logo-dark.png');
    //         if (file_exists($logoPath)) {
    //             $drawing = new Drawing();
    //             $drawing->setName('Logo');
    //             $drawing->setDescription('Company Logo');
    //             $drawing->setPath($logoPath);
    //             $drawing->setCoordinates('A1');
    //             $drawing->setOffsetX(5);
    //             $drawing->setOffsetY(5);
    //             $drawing->setHeight(60);
    //             $drawing->setWorksheet($sheet);
    //         }

    //         $logoPath = public_path('assets/images/audit_left_logo.jpg');
    //         if (file_exists($logoPath)) {
    //             $drawing = new Drawing();
    //             $drawing->setName('Logo');
    //             $drawing->setDescription('Audit Left Logo');
    //             $drawing->setPath($logoPath);
    //             $drawing->setCoordinates('A1');
    //             $drawing->setOffsetX(5);
    //             $drawing->setOffsetY(5);
    //             $drawing->setHeight(60);
    //             $drawing->setWorksheet($sheet);
    //         }

    //         $logoPath = public_path('assets/images/audit_right_logo.jpg');
    //         if (file_exists($logoPath)) {
    //             $drawing = new Drawing();
    //             $drawing->setName('Logo');
    //             $drawing->setDescription('Audit Right Logo');
    //             $drawing->setPath($logoPath);
    //             $drawing->setCoordinates('A1');
    //             $drawing->setOffsetX(5);
    //             $drawing->setOffsetY(5);
    //             $drawing->setHeight(60);
    //             $drawing->setWorksheet($sheet);
    //         }

    //         $sheet->mergeCells('A1:B3');
    //         $sheet->mergeCells("C1:I3");
    //         $sheet->setCellValue("C1", "   6'S AUDIT ANALYSIS REPORT (FY FROM ….... TO ……) PN INTERNATIONAL PVT LTD");
    //         $sheet->getStyle("C1:I3")->applyFromArray([
    //             'font' => ['bold' => true, 'size' => 14],
    //             'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
    //             'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    //         ]);
    //         $sheet->getStyle('A1:B3')->applyFromArray([
    //             'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    //             'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
    //         ]);

    //         $labelMap = [

    //             'J1' => ['value' => 'Doc. No.', 'valueCell' => 'K1', 'data' => $document_no->doc_no],
    //             'J2' => ['value' => 'Issue Dt.', 'valueCell' => 'K2', 'data' => Displaydateformat($document_no->issue_date)],
    //             'J3' => ['value' => 'Rev. & Dt.', 'valueCell' => 'K3', 'data' => $document_no->rev_dt],

    //         ];

    //         foreach ($labelMap as $labelCell => $info) {
    //             $sheet->setCellValue($labelCell, $info['value']);
    //             $sheet->setCellValue($info['valueCell'], $info['data']);

    //             $sheet->getStyle($labelCell)->applyFromArray([
    //                 'font' => ['bold' => true],
    //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    //                 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
    //             ]);

    //             $sheet->getStyle($info['valueCell'])->applyFromArray([
    //                 'font' => ['bold' => true],
    //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    //                 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
    //             ]);
    //         }

    //         $sheet->mergeCells("A6:A7")->setCellValue("A6", "Sr.No.");
    //         $sheet->mergeCells("B6:B7")->setCellValue("B6", "Department Name");
    //         $sheet->mergeCells("B6:B7")->setCellValue("B6", "Unit");
    //         $sheet->mergeCells("C6:I6")->setCellValue("C6", "MARKS OBTAINED");

    //         $sheet->setCellValue("C7", "April");
    //         $sheet->setCellValue("D7", "May");
    //         $sheet->setCellValue("E7", "June");
    //         $sheet->setCellValue("F7", "July");
    //         $sheet->setCellValue("G7", "August");
    //         $sheet->setCellValue("H7", "September");
    //         $sheet->setCellValue("I7", "October");
    //         $sheet->setCellValue("I7", "November");
    //         $sheet->setCellValue("I7", "December");
    //         $sheet->setCellValue("I7", "January");
    //         $sheet->setCellValue("I7", "February");
    //         $sheet->setCellValue("I7", "March");
    //         $sheet->mergeCells("J6:K7")->setCellValue("J6", "Total No's of Audit");
    //         $sheet->mergeCells("J6:K7")->setCellValue("J6", "Total Marks");
    //         $sheet->mergeCells("J6:K7")->setCellValue("J6", "Total Marks Obtained");
    //         $sheet->mergeCells("J6:K7")->setCellValue("J6", "%");

    //         $sheet->getStyle("A6:K7")->applyFromArray([
    //             'font' => ['bold' => true],
    //             'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    //             'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    //         ]);

    //         $row = 8;
    //         $sr = 1;
    //         foreach ($auditAnalysisData as $detail) {
    //             $sheet->setCellValue("A$row", $sr);
    //             $sheet->setCellValue("B$row", $detail['department_name'] ?? '');
    //             $sheet->setCellValue("C$row", $detail['unit_name'] ?? '');
    //             @php
    //                     $marks = json_decode($records->first()->marks ?? '{}', true);
    //                 @endphp

    //                 @foreach ($months as $month)
    //                     @php
    //                         $mark = $marks[strtolower($month)] ?? '-';
    //                     @endphp
    //                     <td style="border: 1px solid black; padding: 8px;">{{ $mark }}</td>
    //             @endforeach
    //             $sheet->setCellValue("C$row", $detail['no_of_audit'] ?? '');
    //             $sheet->setCellValue("C$row", $detail['total_marks'] ?? '');
    //             $sheet->setCellValue("C$row", $detail['marks_obtained'] ?? '');
    //             $sheet->setCellValue("C$row", $detail['percentage'] ?? '');


    //             $sheet->getStyle("A$row:K$row")->applyFromArray([
    //                 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    //             ]);

    //             $sr++;
    //             $row++;
    //         }

    //         $writer = new Xlsx($spreadsheet);
    //         $fileName = "6'S AUDIT ANALYSIS REPORT.xlsx";
    //         $filePath = storage_path("app/public/$fileName");
    //         $writer->save($filePath);

    //         return response()->download($filePath)->deleteFileAfterSend(true);
    //     } catch (Exception $ex) {
    //         dd($ex);
    //         report($ex);
    //         Session::flash('error', 'Something went wrong!');
    //         return redirect(admin_url('audit/6s-analysis/list'));
    //     }
    // }
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

        // Set columns auto-size
        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set rows height
        for ($i = 1; $i <= 200; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(25);
        }

        // Insert Logos
        $logoPaths = [
            ['path' => public_path('assets/images/logo-dark.png'), 'coordinates' => 'A1'],
            ['path' => public_path('assets/images/audit_left_logo.jpg'), 'coordinates' => 'A1'],
            ['path' => public_path('assets/images/audit_right_logo.jpg'), 'coordinates' => 'K1'],
        ];

        foreach ($logoPaths as $logo) {
            if (file_exists($logo['path'])) {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo');
                $drawing->setPath($logo['path']);
                $drawing->setCoordinates($logo['coordinates']);
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
            }
        }

        // Merge and Set Main Title
        $sheet->mergeCells('C1:J3');
        $sheet->setCellValue('C1', "   6'S AUDIT ANALYSIS REPORT (FY FROM ….... TO ……) PN INTERNATIONAL PVT LTD");
        $sheet->getStyle('C1:J3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Static Labels (Doc No, Issue Date, Rev)
        $labelMap = [
            'L1' => ['label' => 'Doc. No.', 'value' => $document_no->doc_no ?? ''],
            'L2' => ['label' => 'Issue Dt.', 'value' => Displaydateformat($document_no->issue_date) ?? ''],
            'L3' => ['label' => 'Rev. & Dt.', 'value' => $document_no->rev_dt ?? ''],
        ];

        foreach ($labelMap as $cell => $info) {
            $sheet->setCellValue($cell, $info['label']);
            $sheet->setCellValue(chr(ord($cell[0]) + 1) . substr($cell, 1), $info['value']); // Next column
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

        // Column Headers
        $months = ['April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'January', 'February', 'March'];

        $sheet->mergeCells('A6:A7')->setCellValue('A6', 'Sr. No.');
        $sheet->mergeCells('B6:B7')->setCellValue('B6', 'Department Name');
        $sheet->mergeCells('C6:C7')->setCellValue('C6', 'Unit');
        $sheet->mergeCells('D6:O6')->setCellValue('D6', 'MARKS OBTAINED');

        $col = 'D';
        foreach ($months as $month) {
            $sheet->setCellValue($col.'7', $month);
            $col++;
        }

        $sheet->mergeCells('P6:P7')->setCellValue('P6', "Total No's of Audit");
        $sheet->mergeCells('Q6:Q7')->setCellValue('Q6', 'Total Marks');
        $sheet->mergeCells('R6:R7')->setCellValue('R6', 'Marks Obtained');
        $sheet->mergeCells('S6:S7')->setCellValue('S6', '%');

        $sheet->getStyle('A6:S7')->applyFromArray([
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Fill Data
        $row = 8;
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

        // Export
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = "6S_Audit_Analysis_Report.xlsx";
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
