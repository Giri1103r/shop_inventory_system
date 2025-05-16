<?php

namespace App\Http\Controllers\Inspection\RRAA;

use Exception;
use App\Models\Master\Work;
use Illuminate\Http\Request;
use App\Models\Master\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\Inspection\RRAA\RRAAEmail;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\Master\Frequency;
use App\Models\Inspection\RRAA\RRAADetails;
use App\Models\Inspection\RRAA\RRAACheckList;
use App\Models\Inspection\RRAA\RRAAStatusLog;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Inspection\Master\ChecklistType;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\RRAA\RRAAFiles;
use App\Models\Inspection\RRAA\RRAASignatureUpload;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class RRAAController extends Controller
{

    private $rraa_details;
    private $rraa_checkList;
    private $employee;
    private $work;
    private $frequency;
    private $category;
    private $statusLog;
    private $signature;
    private $document_reference;
    private $rraa_files;

    public function __construct()
    {
        $this->rraa_details = new RRAADetails();
        $this->rraa_checkList = new RRAACheckList();
        $this->employee = new Employee();
        $this->work = new Work();
        $this->frequency = new Frequency();
        $this->category = new ChecklistType();
        $this->statusLog = new RRAAStatusLog();
        $this->signature = new RRAASignatureUpload();
        $this->document_reference = new InspectionStaticDocno();
        $this->rraa_files = new RRAAFiles();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->rraa_details->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('rraa/ohc_fire_environment_compliance/view/' . encryptId($row->id)) . '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('rraa/ohc_fire_environment_compliance/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                                     </a>';
                            $btn .= '<a href="' . admin_url('rraa/ohc_fire_environment_compliance/generalExcel/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                                     </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'issue_date' ,'created_by'])
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

        $frequency = $this->frequency->getFrequency();
        $category = $this->category->getAll();

        $data = [
            'frequency' => $frequency,
            'category' => $category,
        ];

        return view('inspection.rraa.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $frequency = $this->frequency->getFrequency();
            $category = $this->category->getAll();
            $document_no = $this->document_reference->selectUsingName('RRAA');

            $data = [
                'frequency' => $frequency,
                'category' => $category,
                'document_no' => $document_no,
            ];
            return view('inspection.rraa.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('rraa/ohc_fire_environment_compliance/list'));
        }
    }

    public function Store(Request $request)
    {
        try {
            $rules = [
                'serial_number' => 'required',
                'category' => 'required',
                'ohs_compliance_index' => 'required',
                'frequency' => 'required',
                'scope' => 'required',
                'emp_id' => 'required',
                'authority' => 'required',
                'accountability' => 'required',
                'remark' => 'required',
            ];
            $messages = [
                'serial_number.required' => __('Serial Number is required'),
                'category.required' => __('category is required'),
                'ohs_compliance_index.required' => __('OHS Compliance Index is required'),
                'frequency.required' => __('Frequency is required'),
                'scope.required' => __('scope is required'),
                'emp_id.required' => __('Responsibility is required'),
                'authority.required' => __('authority is required'),
                'accountability.required' => __('accountability is required'),
                'remark.required' => __('remark is required'),
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


                $this->rraa_details->store();

                Session::flash('success', __('Your data has been created successfully'));

            return redirect(admin_url('rraa/ohc_fire_environment_compliance/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('rraa/ohc_fire_environment_compliance/list'));
        }
    }

    public function employeeid(Request $request)
    {
        $name = $request->input('search');

        $employee_code = $this->employee->where('emp_id', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->limit(10)
            ->get();

        return response()->json(
            $employee_code->map(function ($employee) {
                return [
                    'id' => $employee->login_id,
                    'text' => $employee->emp_id . ' - ' . $employee->emp_name,
                ];
            })
        );
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $rraa_details = $this->rraa_details->find($id);
                $document_no = $this->document_reference->selectOne($rraa_details->document_reference_id);
                $get_rraa_file = $this->rraa_files->get_rraa_file($rraa_details->id);

                $data = array(
                    'rraa_details' => $rraa_details,
                    'document_no' => $document_no,
                    'get_rraa_file'=>$get_rraa_file
                );
            }
            return view('inspection.rraa.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->rraa_details->exportdata();

            $document_no = $this->document_reference->selectUsingName('RRAA');

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $data = array(
                'content' => $allData,
                'document_no' => $document_no,
                'pagetitle' => "RRAA Details",
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

            $view = view('inspection.rraa.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "RRAA.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('rraa/ohc_fire_environment_compliance/list'));
        }
    }

    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->rraa_details->exportdata();
            $document_no = $this->document_reference->selectUsingName('RRAA');

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $currentRow = 1;

            foreach ($allData as $recordIndex => $data) {

                $logoPath = public_path('assets/images/logo-dark.png');
                if (file_exists($logoPath)) {
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setName('Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates("A{$currentRow}");
                    $drawing->setOffsetX(10);
                    $drawing->setWidth(100);
                    $drawing->setHeight(50);
                    $drawing->setWorksheet($sheet);
                }

                $sheet->mergeCells("A{$currentRow}:C" . ($currentRow + 2));
                $sheet->mergeCells("D{$currentRow}:N" . ($currentRow + 2));
                $sheet->mergeCells("O{$currentRow}:Q{$currentRow}");
                $sheet->mergeCells("O" . ($currentRow + 1) . ":Q" . ($currentRow + 1));
                $sheet->mergeCells("O" . ($currentRow + 2) . ":Q" . ($currentRow + 2));
                $sheet->mergeCells("R{$currentRow}:T{$currentRow}");
                $sheet->mergeCells("R" . ($currentRow + 1) . ":T" . ($currentRow + 1));
                $sheet->mergeCells("R" . ($currentRow + 2) . ":T" . ($currentRow + 2));

                $sheet->setCellValue("D{$currentRow}", "Occupational Health Safety, Fire & Environmental Compliance Sheet\n");
                $sheet->setCellValue("O{$currentRow}", 'Doc. No.');
                $sheet->setCellValue("O" . ($currentRow + 1), 'Issue Dt.');
                $sheet->setCellValue("O" . ($currentRow + 2), 'Rev. & Dt.');
                $sheet->setCellValue("R{$currentRow}", $document_no->doc_no);
                $sheet->setCellValue("R" . ($currentRow + 1), Displaydateformat($document_no->issue_date));
                $sheet->setCellValue("R" . ($currentRow + 2), $document_no->rev_dt);

                $sheet->getStyle("A{$currentRow}:T" . ($currentRow + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);


                $sheet->getStyle("O{$currentRow}:Q" . ($currentRow + 2))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_DOUBLE
                        ]
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'font' => ['bold' => true],
                ]);

                $sheet->getStyle("R{$currentRow}:T" . ($currentRow + 2))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_DOUBLE
                        ]
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'font' => ['bold' => true],
                ]);

                $headerRow = $currentRow + 3;
                $headers = [
                    'Sr. No', 'Category', 'OHS Compliance Index (Role)', 'Scope', 'Responsibility',
                    'Authority', 'Accountability', 'Remark',
                ];
                $mergeMap = [
                    'A', 'C', 'F', 'I', 'L', 'O', 'Q', 'S'
                ];
                $mergeEnds = [
                    'B', 'E', 'H', 'K', 'N', 'P', 'R', 'T'
                ];

                foreach ($headers as $i => $text) {
                    $start = $mergeMap[$i] . $headerRow;
                    $end = $mergeEnds[$i] . $headerRow;
                    $sheet->mergeCells("$start:$end")->setCellValue($start, $text);
                }

                $sheet->getStyle("A{$headerRow}:T{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F2F2F2']
                    ],
                ]);

                $dataRow = $headerRow + 1;
                $sheet->mergeCells("A{$dataRow}:B{$dataRow}")->setCellValue("A{$dataRow}", 1);
                $sheet->mergeCells("C{$dataRow}:E{$dataRow}")->setCellValue("C{$dataRow}", getCategoryname($data->category));
                $sheet->mergeCells("F{$dataRow}:H{$dataRow}")->setCellValue("F{$dataRow}", $data->ohs_compliance_index);
                $sheet->mergeCells("I{$dataRow}:K{$dataRow}")->setCellValue("I{$dataRow}", $data->scope);
                $sheet->mergeCells("L{$dataRow}:N{$dataRow}")->setCellValue("L{$dataRow}", getUsername($data->responsibility));
                $sheet->mergeCells("O{$dataRow}:P{$dataRow}")->setCellValue("O{$dataRow}", $data->authority);
                $sheet->mergeCells("Q{$dataRow}:R{$dataRow}")->setCellValue("Q{$dataRow}", $data->accountability);
                $sheet->mergeCells("S{$dataRow}:T{$dataRow}")->setCellValue("S{$dataRow}", $data->remark);

                $sheet->getStyle("A{$dataRow}:T{$dataRow}")->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $currentRow = $dataRow + 5;
            }

            foreach (range('A', 'T') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $fileName = 'RRAA.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);

        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong! Please try again later.');
            return redirect(admin_url('rraa/ohc_fire_environment_compliance/list'));
        }
    }

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $rraa_details = $this->rraa_details->find($id);
                $document_no = $this->document_reference->selectUsingName('RRAA');

                $data = array(
                    'rraa_details' => $rraa_details,
                    'document_no' => $document_no,
                    'pagetitle' => "RRAA Details",
                );
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

            $html = view('inspection.rraa.generalpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "RRAA.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }


    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rraa_details = $this->rraa_details->selectOne($id);
            $document_no = $this->document_reference->selectUsingName('RRAA');

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $logoPath = public_path('assets/images/logo-dark.png');
            if (file_exists($logoPath)) {
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setPath($logoPath);
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(10);
                $drawing->setWidth(100);
                $drawing->setHeight(50);
                $drawing->setWorksheet($sheet);
            }

            $sheet->mergeCells('A1:C3');
            $sheet->mergeCells('D1:N3');
            $sheet->setCellValue('D1', "Occupational Health Safety, Fire & Environmental Compliance Sheet\n");
            $sheet->getStyle('D1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ],
            ]);

            $headerLabels = [
                'O1:Q1' => 'Doc. No.',
                'O2:Q2' => 'Issue Dt.',
                'O3:Q3' => 'Rev. & Dt.',
            ];

            foreach ($headerLabels as $cellRange => $label) {
                $cell = explode(':', $cellRange)[0];
                $sheet->mergeCells($cellRange)->setCellValue($cell, $label);
                $sheet->getStyle($cell)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
            }

            $sheet->mergeCells("R1:T1")->setCellValue("R1", $document_no->doc_no);
            $sheet->mergeCells("R2:T2")->setCellValue("R2", Displaydateformat($document_no->issue_date));
            $sheet->mergeCells("R3:T3")->setCellValue("R3", $document_no->rev_dt);

            $sheet->getStyle("O1:T3")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['argb' => '000000']]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $headers = [
                'Sr. No',
                'Category',
                'OHS Compliance Index (Role)',
                'Scope',
                'Responsibility',
                'Authority',
                'Accountability',
                'Remark',
            ];

            $mergeMap = [
                'A4:B4', 'C4:E4', 'F4:H4', 'I4:K4', 'L4:N4', 'O4:P4', 'Q4:R4', 'S4:T4'
            ];

            foreach ($headers as $index => $label) {
                $cellRange = $mergeMap[$index];
                $cell = explode(':', $cellRange)[0];
                $sheet->mergeCells($cellRange)->setCellValue($cell, $label);
            }

            $sheet->getStyle('A4:T4')->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F2F2F2']
                ],
            ]);

            $row = 5;
            $sheet->mergeCells("A{$row}:B{$row}")->setCellValue("A{$row}", '1');
            $sheet->mergeCells("C{$row}:E{$row}")->setCellValue("C{$row}", getCategoryname($rraa_details->category) ?? '');
            $sheet->mergeCells("F{$row}:H{$row}")->setCellValue("F{$row}", $rraa_details->ohs_compliance_index ?? '');
            $sheet->mergeCells("I{$row}:K{$row}")->setCellValue("I{$row}", $rraa_details->scope ?? '');
            $sheet->mergeCells("L{$row}:N{$row}")->setCellValue("L{$row}", getUsername($rraa_details->responsibility) ?? '');
            $sheet->mergeCells("O{$row}:P{$row}")->setCellValue("O{$row}", $rraa_details->authority ?? '');
            $sheet->mergeCells("Q{$row}:R{$row}")->setCellValue("Q{$row}", $rraa_details->accountability ?? '');
            $sheet->mergeCells("S{$row}:T{$row}")->setCellValue("S{$row}", $rraa_details->remark ?? '');

            $sheet->getStyle("A{$row}:T{$row}")->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            foreach (range('A', 'T') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $fileName = 'RRAA.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('rraa/ohc_fire_environment_compliance/list'));
        }
    }



}
