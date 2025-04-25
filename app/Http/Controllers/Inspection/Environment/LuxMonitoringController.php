<?php

namespace App\Http\Controllers\Inspection\Environment;

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
use App\Models\Inspection\Environment\LuxMonitoring;
use App\Models\Inspection\Environment\Environment;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Master\Location;
use App\Models\Master\Unit;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class LuxMonitoringController extends Controller
{
    private $location;
    private $unit;
    private $lux_monitoring;
    private $upload_log;
    private $environment;
    private $static_docno;

    public function __construct()
    {
        $this->lux_monitoring = new LuxMonitoring();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->environment = new Environment();
        $this->static_docno = new InspectionStaticDocno();
    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $type = LUX;
                    $data  = $this->environment->list($type);
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
                        ->addColumn('created_at', function ($row) {
                            return Displaydatetimeformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('environment/lux/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {

                            // $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '"  class="recordDelete" title="' . __('common.delete') . '"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            // }
                            $btn .= '<a href="' . admin_url('environment/lux/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf" style="color: #e67265;" aria-hidden="true"></i>
                         </a>';

                            $btn .= '<a href="' . admin_url('environment/lux/generalExcel/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="Excel">
                            <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                         </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_at', 'created_by', 'status'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {
                    dd($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
        $environmentList  = $this->environment->select('id', 'environment_no')->where('type', LUX)->where('status', '1')->get();

        $data = array(
            'environmentList' => $environmentList,
        );
        return view('inspection.environment.luxMonitoring.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $locationList  = $this->location->select('id', 'location_name')->where('status', '1')->get();
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $staticDocno  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                ['type', "Lux"],
                ['status', '1']
            ])->first();

            $data = array(
                'locationList' => $locationList,
                'unitList' => $unitList,
                'staticDocno' => $staticDocno,
            );
            return view('inspection.environment.luxMonitoring.add', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'lux_no' => 'required',
            ];
            $messages = [
                'lux_no.required' => "Lux No is Required",
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                $env_no = $request->lux_no;
                $type = LUX;
                $environment =   $this->environment->store($env_no, $type);

                $this->lux_monitoring->store($environment->id);

                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', __('common.message_error'));
            }

            return redirect(admin_url('environment/lux/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('environment/lux/list'));
        }
    }



    public function View($id)
    {
        try {
            $id = decryptId($id);
            if (Auth::check()) {
                $type = LUX;
                $environmentData =   $this->environment->selectOne($id, $type);
                $luxDataList = $this->lux_monitoring->selectOne($id);
                $staticDocno  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                    ['type', "Lux"],
                    ['status', '1']
                ])->first();
                $data = array(
                    'environmentData' => $environmentData,
                    'luxDataList' => $luxDataList,
                    'staticDocno' => $staticDocno,
                );
            }
            return view('inspection.environment.luxMonitoring.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('environment/lux/list'));
        }
    }


    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $type = LUX;
            $environmentID = $this->environment->statuschange($id, $type);
            $this->lux_monitoring->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'status changed'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function ExportExcel()
    {
        try {
            $type = LUX;
            $allData =   $this->environment->exportdata($type);

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
                $environmentData =   $this->environment->selectOne($details->id, $type);
                $luxDataList = $this->lux_monitoring->selectOne($details->id);
                $document_no  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                    ['type', "Lux"],
                    ['status', '1']
                ])->first();

                $logoPath = public_path('assets/images/logo-dark.png');
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
                $sheet->setCellValue("G{$currentRow}", " LUX EMISSION MONITORING MASTER SHEET PN INTERNATIONAL PVT. LTD");
                $sheet->getStyle("G{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);


                // document number


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

                $headerRow = $currentRow + 3;

                $sheet->mergeCells("A$headerRow:B$headerRow")->setCellValue("A$headerRow", "SERIAL NO");
                $sheet->mergeCells("C$headerRow:D$headerRow")->setCellValue("C$headerRow", "Location");
                $sheet->mergeCells("E$headerRow:F$headerRow")->setCellValue("E$headerRow", "Unit");
                $sheet->mergeCells("G$headerRow:H$headerRow")->setCellValue("G$headerRow", "department");
                $sheet->mergeCells("I$headerRow:J$headerRow")->setCellValue("I$headerRow", "Lux Level");
                $sheet->mergeCells("K$headerRow:L$headerRow")->setCellValue("K$headerRow", "Date of Monitoring");
                $sheet->mergeCells("M$headerRow:M$headerRow")->setCellValue("M$headerRow", "Next Due Date Of Monitoring");
                $sheet->mergeCells("N$headerRow:N$headerRow")->setCellValue("N$headerRow", "Lux Level");
                $sheet->mergeCells("O$headerRow:O$headerRow")->setCellValue("O$headerRow", "Date of Monitoring");
                $sheet->mergeCells("P$headerRow:P$headerRow")->setCellValue("P$headerRow", "Next Due Date Of Monitoring");
                $sheet->mergeCells("Q$headerRow:Q$headerRow")->setCellValue("Q$headerRow", "Act/Rule");
                $sheet->mergeCells("R$headerRow:S$headerRow")->setCellValue("R$headerRow", "Remark");


                $sheet->getStyle("A$headerRow:S$headerRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'font' => ['bold' => true],
                ]);

                $inspectionRow = $headerRow + 1;
                foreach ($luxDataList as $index => $detail) {

                    $sheet->mergeCells("A$inspectionRow:B$inspectionRow")->setCellValue("A$inspectionRow", $detail->sr_no);
                    $sheet->mergeCells("C$inspectionRow:D$inspectionRow")->setCellValue("C$inspectionRow", getLocationname($detail->location_id));
                    $sheet->mergeCells("E$inspectionRow:F$inspectionRow")->setCellValue("E$inspectionRow", $detail->unit_name);
                    $sheet->mergeCells("G$inspectionRow:H$inspectionRow")->setCellValue("G$inspectionRow", getDepartment($detail->department_id));
                    $sheet->mergeCells("I$inspectionRow:J$inspectionRow")->setCellValue("I$inspectionRow", ($detail->lux_level1));
                    $sheet->mergeCells("K$inspectionRow:L$inspectionRow")->setCellValue("K$inspectionRow", Displaydateformat($detail->date_of_monitoring));
                    $sheet->mergeCells("M$inspectionRow:M$inspectionRow")->setCellValue("M$inspectionRow", Displaydateformat($detail->next_due_date_of_monitoring));
                    $sheet->mergeCells("N$inspectionRow:N$inspectionRow")->setCellValue("N$inspectionRow", $detail->lux_level2);
                    $sheet->mergeCells("O$inspectionRow:O$inspectionRow")->setCellValue("O$inspectionRow", Displaydateformat($detail->date_of_monitoring));
                    $sheet->mergeCells("P$inspectionRow:P$inspectionRow")->setCellValue("P$inspectionRow", Displaydateformat($detail->next_due_date_of_monitoring2));
                    $sheet->mergeCells("Q$inspectionRow:Q$inspectionRow")->setCellValue("Q$inspectionRow", $detail->act_rule);
                    $sheet->mergeCells("R$inspectionRow:S$inspectionRow")->setCellValue("R$inspectionRow", $detail->remark);



                    $sheet->getStyle("A$inspectionRow:S$inspectionRow")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $inspectionRow++;
                }
                $row =  $inspectionRow + 2;
            }
            $fileName = ' LUX.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('environment/lux/list'));
        }
    }

    public function ExportPDF()
    {
        try {
            ini_set("pcre.backtrack_limit", "5000000");
            $type = LUX;
            $allData = $this->environment->exportdata($type);

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }

            $exportedData = [];

            foreach ($allData as $details) {

                $environmentData =   $this->environment->selectOne($details->id, $type);
                $luxDataList = $this->lux_monitoring->selectOne($details->id);
                $document_no  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                    ['type', "Lux"],
                    ['status', '1']
                ])->first();
                $exportedData[] = [
                    'environmentData' => $environmentData,
                    'luxDataList' => $luxDataList,
                    'document_no' => $document_no,
                ];
            }

            $data = [
                'exportedData' => $exportedData,
                'pagetitle' => " LUX",
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

            $view = view('inspection.environment.luxMonitoring.pdf', $data);
            $html = $view->render();

            $filename = " LUX.pdf";
            $mpdf->WriteHTML($html);
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('environment/lux/list'));
        }
    }

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {

                $type = LUX;
                $environmentData =   $this->environment->selectOne($id, $type);
                $luxDataList = $this->lux_monitoring->selectOne($id);
                $document_no  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                    ['type', "Lux"],
                    ['status', '1']
                ])->first();
                $property = [
                    'tempDir' => 'public/pdf/temp/',
                    'mode' => 'c',
                    'margin_left' => 10,
                    'margin_right' => 10,
                    'margin_top' => 10,

                ];
                $data = [
                    'environmentData' => $environmentData,
                    'luxDataList' => $luxDataList,
                    'document_no' => $document_no,
                ];
                // dd( $data);
                $mpdf = new \Mpdf\Mpdf($property);
                $mpdf->setAutoTopMargin = 'stretch';

                $html = view('inspection.environment.luxMonitoring.viewpdf', $data)->render();
                $mpdf->WriteHTML($html);

                $filename = " LUX.pdf";
                return $mpdf->Output($filename, 'D');
            }
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('environment/lux/list'));
        }
    }

    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $type = LUX;
                $environmentData =   $this->environment->selectOne($id, $type);
                $luxDataList = $this->lux_monitoring->selectOne($id);
                $document_no  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                    ['type', "Lux"],
                    ['status', '1']
                ])->first();
                for ($i = 1; $i <= 200; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(25);
                }

                $row = 1;
                $currentRow = $row;
                $logoPath = public_path('assets/images/logo-dark.png');
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
                $sheet->setCellValue("G{$currentRow}", " LUX EMISSION MONITORING MASTER SHEET PN INTERNATIONAL PVT. LTD");
                $sheet->getStyle("G{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);


                // document number


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

                $headerRow = $currentRow + 3;

                $sheet->mergeCells("A$headerRow:B$headerRow")->setCellValue("A$headerRow", "SERIAL NO");
                $sheet->mergeCells("C$headerRow:D$headerRow")->setCellValue("C$headerRow", "Location");
                $sheet->mergeCells("E$headerRow:F$headerRow")->setCellValue("E$headerRow", "Unit");
                $sheet->mergeCells("G$headerRow:H$headerRow")->setCellValue("G$headerRow", "department");
                $sheet->mergeCells("I$headerRow:J$headerRow")->setCellValue("I$headerRow", "Lux Level");
                $sheet->mergeCells("K$headerRow:L$headerRow")->setCellValue("K$headerRow", "Date of Monitoring");
                $sheet->mergeCells("M$headerRow:M$headerRow")->setCellValue("M$headerRow", "Next Due Date Of Monitoring");
                $sheet->mergeCells("N$headerRow:N$headerRow")->setCellValue("N$headerRow", "Lux Level");
                $sheet->mergeCells("O$headerRow:O$headerRow")->setCellValue("O$headerRow", "Date of Monitoring");
                $sheet->mergeCells("P$headerRow:P$headerRow")->setCellValue("P$headerRow", "Next Due Date Of Monitoring");
                $sheet->mergeCells("Q$headerRow:Q$headerRow")->setCellValue("Q$headerRow", "Act/Rule");
                $sheet->mergeCells("R$headerRow:S$headerRow")->setCellValue("R$headerRow", "Remark");


                $sheet->getStyle("A$headerRow:S$headerRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'font' => ['bold' => true],
                ]);

                $inspectionRow = $headerRow + 1;
                foreach ($luxDataList as $index => $detail) {

                    $sheet->mergeCells("A$inspectionRow:B$inspectionRow")->setCellValue("A$inspectionRow", $detail->sr_no);
                    $sheet->mergeCells("C$inspectionRow:D$inspectionRow")->setCellValue("C$inspectionRow", getLocationname($detail->location_id));
                    $sheet->mergeCells("E$inspectionRow:F$inspectionRow")->setCellValue("E$inspectionRow", $detail->unit_name);
                    $sheet->mergeCells("G$inspectionRow:H$inspectionRow")->setCellValue("G$inspectionRow", getDepartment($detail->department_id));
                    $sheet->mergeCells("I$inspectionRow:J$inspectionRow")->setCellValue("I$inspectionRow", ($detail->lux_level1));
                    $sheet->mergeCells("K$inspectionRow:L$inspectionRow")->setCellValue("K$inspectionRow", Displaydateformat($detail->date_of_monitoring));
                    $sheet->mergeCells("M$inspectionRow:M$inspectionRow")->setCellValue("M$inspectionRow", Displaydateformat($detail->next_due_date_of_monitoring));
                    $sheet->mergeCells("N$inspectionRow:N$inspectionRow")->setCellValue("N$inspectionRow", $detail->lux_level2);
                    $sheet->mergeCells("O$inspectionRow:O$inspectionRow")->setCellValue("O$inspectionRow", Displaydateformat($detail->date_of_monitoring));
                    $sheet->mergeCells("P$inspectionRow:P$inspectionRow")->setCellValue("P$inspectionRow", Displaydateformat($detail->next_due_date_of_monitoring2));
                    $sheet->mergeCells("Q$inspectionRow:Q$inspectionRow")->setCellValue("Q$inspectionRow", $detail->act_rule);
                    $sheet->mergeCells("R$inspectionRow:S$inspectionRow")->setCellValue("R$inspectionRow", $detail->remark);



                    $sheet->getStyle("A$inspectionRow:S$inspectionRow")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $inspectionRow++;
                }
            }
            $fileName = 'lux.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('environment/lux/list'));
        }
    }
}
