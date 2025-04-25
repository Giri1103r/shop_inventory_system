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
use App\Models\Inspection\Environment\WorkZoneAirMonitoring;
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

class WorkZoneAirMonitoringController extends Controller
{
    private $location;
    private $unit;
    private $workZone_air_monitoring;
    private $upload_log;
    private $environment;
    private $static_docno;

    public function __construct()
    {
        $this->workZone_air_monitoring = new WorkZoneAirMonitoring();
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
                    $type = WORKZONE_AIR;
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
                            $btn = '<a href="' . admin_url('environment/work-zone/air/view/' . encryptId($row->id)) . '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {

                            // $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '"  class="recordDelete" title="' . __('common.delete') . '"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            // }
                            $btn .= '<a href="' . admin_url('environment/work-zone/air/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf" style="color: #e67265;" aria-hidden="true"></i>
                         </a>';

                            $btn .= '<a href="' . admin_url('environment/work-zone/air/generalExcel/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="Excel">
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
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
        $environmentList  = $this->environment->select('id', 'environment_no')->where('type', WORKZONE_AIR)->where('status', '1')->get();

        $data = array(
            'environmentList' => $environmentList,
        );
        return view('inspection.environment.workZoneAirMonitoring.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $locationList  = $this->location->select('id', 'location_name')->where('status', '1')->get();
            $staticDocno  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                ['type', "WorkZoneAir"],
                ['status', '1']
            ])->first();

            $data = array(
                'locationList' => $locationList,
                'staticDocno' => $staticDocno,
            );
            return view('inspection.environment.workZoneAirMonitoring.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'work_zone_air_no' => 'required',
            ];
            $messages = [
                'work_zone_air_no.required' => "Work Zone Air No is Required",
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                $env_no = $request->work_zone_air_no;
                $type = WORKZONE_AIR;
                $environment =   $this->environment->store($env_no, $type);

                $this->workZone_air_monitoring->store($environment->id);

                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', __('common.message_error'));
            }

            return redirect(admin_url('environment/work-zone/air/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('environment/work-zone/air/list'));
        }
    }



    public function View($id)
    {
        try {
            $id = decryptId($id);
            if (Auth::check()) {
                $type = WORKZONE_AIR;
                $environmentData =   $this->environment->selectOne($id, $type);
                $workZoneAirDataList = $this->workZone_air_monitoring->selectOne($id);
                $staticDocno  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                    ['type', "WorkZoneAir"],
                    ['status', '1']
                ])->first();
                $data = array(
                    'environmentData' => $environmentData,
                    'workZoneAirDataList' => $workZoneAirDataList,
                    'staticDocno' => $staticDocno,
                );
            }
            return view('inspection.environment.workZoneAirMonitoring.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('environment/work-zone/air/list'));
        }
    }


    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $type = WORKZONE_AIR;
            $environmentID = $this->environment->statuschange($id, $type);
            $this->workZone_air_monitoring->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Work Zone Air status changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function ExportExcel()
    {
        try {
            $type = WORKZONE_AIR;
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
                $workZoneAirDataList = $this->workZone_air_monitoring->selectOne($details->id);
                $document_no  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                    ['type', "workZoneAir"],
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
                $sheet->mergeCells("G{$currentRow}:P" . ($currentRow + 2));
                $sheet->setCellValue("G{$currentRow}", " WORK ZONE AIR MONITORING (YEARLY) PN INTERNATIONAL PVT. LTD");
                $sheet->getStyle("G{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);


                // document number


                $sheet->mergeCells("Q$currentRow:S$currentRow")->setCellValue("Q$currentRow", 'Doc. No.');
                $sheet->mergeCells("Q" . ($currentRow + 1) . ":S" . ($currentRow + 1))->setCellValue("Q" . ($currentRow + 1), 'Issue Dt.');
                $sheet->mergeCells("Q" . ($currentRow + 2) . ":S" . ($currentRow + 2))->setCellValue("Q" . ($currentRow + 2), 'Rev. & Dt.');

                $sheet->mergeCells("T$currentRow:V$currentRow")->setCellValue("Q$currentRow", $document_no->doc_no);
                $sheet->mergeCells("T" . ($currentRow + 1) . ":W" . ($currentRow + 1))->setCellValue("T" . ($currentRow + 1), Displaydateformat($document_no->issue_date));
                $sheet->mergeCells("T" . ($currentRow + 2) . ":W" . ($currentRow + 2))->setCellValue("T" . ($currentRow + 2), $document_no->rev_dt);

                $sheet->getStyle("Q$currentRow:W" . ($currentRow + 2))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'font' => ['bold' => true],
                ]);

                $headerRow = $currentRow + 3;

                $equalWidth = 15; // Adjust as needed
                foreach (range('A', 'W') as $col) {
                    $sheet->getColumnDimension($col)->setWidth($equalWidth);
                }

                // First set
                $sheet->mergeCells("A$headerRow:B$headerRow")->setCellValue("A$headerRow", "SERIAL NO");
                $sheet->mergeCells("C$headerRow:D$headerRow")->setCellValue("C$headerRow", "Location");
                $sheet->mergeCells("E$headerRow:F$headerRow")->setCellValue("E$headerRow", "Unit");
                $sheet->mergeCells("G$headerRow:H$headerRow")->setCellValue("G$headerRow", "Department");
                $sheet->mergeCells("I$headerRow:J$headerRow")->setCellValue("I$headerRow", "Date of Monitoring");
                $sheet->mergeCells("K$headerRow:L$headerRow")->setCellValue("K$headerRow", "Next Due Date Of Monitoring");
                $sheet->setCellValue("M$headerRow", "SPM");
                $sheet->setCellValue("N$headerRow", "SO2");
                $sheet->setCellValue("O$headerRow", "NO2");

                // Second set
                $sheet->setCellValue("P$headerRow", "Date of Monitoring");
                $sheet->setCellValue("Q$headerRow", "Next Due Date Of Monitoring");
                $sheet->setCellValue("R$headerRow", "SPM");
                $sheet->setCellValue("S$headerRow", "SO2");
                $sheet->setCellValue("T$headerRow", "NO2");

                // Other fields
                $sheet->setCellValue("U$headerRow", "Act/Rule");
                $sheet->mergeCells("V$headerRow:W$headerRow")->setCellValue("V$headerRow", "Remark");

                // Optional styling
                $sheet->getStyle("A$headerRow:W$headerRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A$headerRow:W$headerRow")->getFont()->setBold(true);


                $inspectionRow = $headerRow + 1;
                foreach ($workZoneAirDataList as $index => $detail) {

                    $sheet->mergeCells("A$inspectionRow:B$inspectionRow")->setCellValue("A$inspectionRow", $detail->sr_no);
                    $sheet->mergeCells("C$inspectionRow:D$inspectionRow")->setCellValue("C$inspectionRow", getLocationname($detail->location_id));
                    $sheet->mergeCells("E$inspectionRow:F$inspectionRow")->setCellValue("E$inspectionRow",  $detail->unit_name);
                    $sheet->mergeCells("G$inspectionRow:H$inspectionRow")->setCellValue("G$inspectionRow", getDepartment($detail->department_id));
                    $sheet->mergeCells("I$inspectionRow:J$inspectionRow")->setCellValue("I$inspectionRow", Displaydateformat( $detail->date_of_monitoring));
                    $sheet->mergeCells("K$inspectionRow:L$inspectionRow")->setCellValue("K$inspectionRow", Displaydateformat($detail->next_due_date_of_monitoring));
                    $sheet->setCellValue("M$inspectionRow",  $detail->spm);
                    $sheet->setCellValue("N$inspectionRow",  $detail->so2);
                    $sheet->setCellValue("O$inspectionRow",  $detail->no2);

                    // Second set
                    $sheet->setCellValue("P$inspectionRow", Displaydateformat( $detail->date_of_monitoring2));
                    $sheet->setCellValue("Q$inspectionRow",  Displaydateformat($detail->next_due_date_of_monitoring2));
                    $sheet->setCellValue("R$inspectionRow", $detail->spm_session2);
                    $sheet->setCellValue("S$inspectionRow",  $detail->so2_session2);
                    $sheet->setCellValue("T$inspectionRow",  $detail->no2_session2);

                    // Other fields
                    $sheet->setCellValue("U$inspectionRow", "Act/Rule");
                    $sheet->mergeCells("V$inspectionRow:W$inspectionRow")->setCellValue("V$inspectionRow", "Remark");



                    $sheet->getStyle("A$inspectionRow:W$inspectionRow")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $inspectionRow++;
                }
                $row =  $inspectionRow + 2;
            }
            $fileName = ' workZoneAir.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('environment/work-zone/air/list'));
        }
    }

    public function ExportPDF()
    {
        try {
            ini_set("pcre.backtrack_limit", "5000000");
            $type = WORKZONE_AIR;
            $allData = $this->environment->exportdata($type);

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }

            $exportedData = [];

            foreach ($allData as $details) {

                $environmentData =   $this->environment->selectOne($details->id, $type);
                $workZoneAirDataList = $this->workZone_air_monitoring->selectOne($details->id);
                $document_no  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                    ['type', "workZoneAir"],
                    ['status', '1']
                ])->first();
                $exportedData[] = [
                    'environmentData' => $environmentData,
                    'workZoneAirDataList' => $workZoneAirDataList,
                    'document_no' => $document_no,
                ];
            }

            $data = [
                'exportedData' => $exportedData,
                'pagetitle' => " workZoneAir",
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

            $view = view('inspection.environment.workZoneAirMonitoring.pdf', $data);
            $html = $view->render();

            $filename = " workZoneAir.pdf";
            $mpdf->WriteHTML($html);
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('environment/work-zone/air/list'));
        }
    }

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {

                $type = WORKZONE_AIR;
                $environmentData =   $this->environment->selectOne($id, $type);
                $workZoneAirDataList = $this->workZone_air_monitoring->selectOne($id);
                $document_no  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                    ['type', "workZoneAir"],
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
                    'workZoneAirDataList' => $workZoneAirDataList,
                    'document_no' => $document_no,
                ];
                // dd( $data);
                $mpdf = new \Mpdf\Mpdf($property);
                $mpdf->setAutoTopMargin = 'stretch';

                $html = view('inspection.environment.workZoneAirMonitoring.viewpdf', $data)->render();
                $mpdf->WriteHTML($html);

                $filename = " workZoneAir.pdf";
                return $mpdf->Output($filename, 'D');
            }
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('environment/work-zone/air/list'));
        }
    }

    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $type = WORKZONE_AIR;
                $environmentData =   $this->environment->selectOne($id, $type);
                $workZoneAirDataList = $this->workZone_air_monitoring->selectOne($id);
                $document_no  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                    ['type', "workZoneAir"],
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

                $sheet->mergeCells("G{$currentRow}:P" . ($currentRow + 2));
                $sheet->setCellValue("G{$currentRow}", " WORK ZONE AIR MONITORING (YEARLY) PN INTERNATIONAL PVT. LTD");
                $sheet->getStyle("G{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);


                // document number


                $sheet->mergeCells("Q$currentRow:S$currentRow")->setCellValue("Q$currentRow", 'Doc. No.');
                $sheet->mergeCells("Q" . ($currentRow + 1) . ":S" . ($currentRow + 1))->setCellValue("Q" . ($currentRow + 1), 'Issue Dt.');
                $sheet->mergeCells("Q" . ($currentRow + 2) . ":S" . ($currentRow + 2))->setCellValue("Q" . ($currentRow + 2), 'Rev. & Dt.');

                $sheet->mergeCells("T$currentRow:V$currentRow")->setCellValue("Q$currentRow", $document_no->doc_no);
                $sheet->mergeCells("T" . ($currentRow + 1) . ":W" . ($currentRow + 1))->setCellValue("T" . ($currentRow + 1), Displaydateformat($document_no->issue_date));
                $sheet->mergeCells("T" . ($currentRow + 2) . ":W" . ($currentRow + 2))->setCellValue("T" . ($currentRow + 2), $document_no->rev_dt);

                $sheet->getStyle("Q$currentRow:W" . ($currentRow + 2))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'font' => ['bold' => true],
                ]);

                $headerRow = $currentRow + 3;

                $equalWidth = 15; // Adjust as needed
                foreach (range('A', 'W') as $col) {
                    $sheet->getColumnDimension($col)->setWidth($equalWidth);
                }

                // First set
                $sheet->mergeCells("A$headerRow:B$headerRow")->setCellValue("A$headerRow", "SERIAL NO");
                $sheet->mergeCells("C$headerRow:D$headerRow")->setCellValue("C$headerRow", "Location");
                $sheet->mergeCells("E$headerRow:F$headerRow")->setCellValue("E$headerRow", "Unit");
                $sheet->mergeCells("G$headerRow:H$headerRow")->setCellValue("G$headerRow", "Department");
                $sheet->mergeCells("I$headerRow:J$headerRow")->setCellValue("I$headerRow", "Date of Monitoring");
                $sheet->mergeCells("K$headerRow:L$headerRow")->setCellValue("K$headerRow", "Next Due Date Of Monitoring");
                $sheet->setCellValue("M$headerRow", "SPM");
                $sheet->setCellValue("N$headerRow", "SO2");
                $sheet->setCellValue("O$headerRow", "NO2");

                // Second set
                $sheet->setCellValue("P$headerRow", "Date of Monitoring");
                $sheet->setCellValue("Q$headerRow", "Next Due Date Of Monitoring");
                $sheet->setCellValue("R$headerRow", "SPM");
                $sheet->setCellValue("S$headerRow", "SO2");
                $sheet->setCellValue("T$headerRow", "NO2");

                // Other fields
                $sheet->setCellValue("U$headerRow", "Act/Rule");
                $sheet->mergeCells("V$headerRow:W$headerRow")->setCellValue("V$headerRow", "Remark");

                // Optional styling
                $sheet->getStyle("A$headerRow:W$headerRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A$headerRow:W$headerRow")->getFont()->setBold(true);


                $inspectionRow = $headerRow + 1;
                foreach ($workZoneAirDataList as $index => $detail) {

                    $sheet->mergeCells("A$inspectionRow:B$inspectionRow")->setCellValue("A$inspectionRow", $detail->sr_no);
                    $sheet->mergeCells("C$inspectionRow:D$inspectionRow")->setCellValue("C$inspectionRow", getLocationname($detail->location_id));
                    $sheet->mergeCells("E$inspectionRow:F$inspectionRow")->setCellValue("E$inspectionRow",  $detail->unit_name);
                    $sheet->mergeCells("G$inspectionRow:H$inspectionRow")->setCellValue("G$inspectionRow", getDepartment($detail->department_id));
                    $sheet->mergeCells("I$inspectionRow:J$inspectionRow")->setCellValue("I$inspectionRow", Displaydateformat( $detail->date_of_monitoring));
                    $sheet->mergeCells("K$inspectionRow:L$inspectionRow")->setCellValue("K$inspectionRow", Displaydateformat($detail->next_due_date_of_monitoring));
                    $sheet->setCellValue("M$inspectionRow",  $detail->spm);
                    $sheet->setCellValue("N$inspectionRow",  $detail->so2);
                    $sheet->setCellValue("O$inspectionRow",  $detail->no2);

                    // Second set
                    $sheet->setCellValue("P$inspectionRow", Displaydateformat( $detail->date_of_monitoring2));
                    $sheet->setCellValue("Q$inspectionRow",  Displaydateformat($detail->next_due_date_of_monitoring2));
                    $sheet->setCellValue("R$inspectionRow", $detail->spm_session2);
                    $sheet->setCellValue("S$inspectionRow",  $detail->so2_session2);
                    $sheet->setCellValue("T$inspectionRow",  $detail->no2_session2);

                    // Other fields
                    $sheet->setCellValue("U$inspectionRow", "Act/Rule");
                    $sheet->mergeCells("V$inspectionRow:W$inspectionRow")->setCellValue("V$inspectionRow", "Remark");



                    $sheet->getStyle("A$inspectionRow:W$inspectionRow")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $inspectionRow++;
                }
            }
            $fileName = 'workZoneAir.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('environment/work-zone/air/list'));
        }
    }
}
