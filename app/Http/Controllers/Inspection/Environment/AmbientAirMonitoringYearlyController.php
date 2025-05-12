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
use App\Models\Inspection\Environment\AmbientAirMonitoring;
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

class AmbientAirMonitoringYearlyController extends Controller
{
    private $location;
    private $unit;
    private $ambient_air_monitoring;
    private $upload_log;
    private $environment;
    private $static_docno;

    public function __construct()
    {
        $this->ambient_air_monitoring = new AmbientAirMonitoring();
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
                    $type = AMBIENT_AIR;
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
                            $btn = '<a href="' . admin_url('environment/ambient-air/yearly/view/' . encryptId($row->id)) . '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('environment/ambient-air/yearly/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf" style="color: #e67265;" aria-hidden="true"></i>
                         </a>';

                            $btn .= '<a href="' . admin_url('environment/ambient-air/yearly/generalExcel/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="Excel">
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
        $environmentList  = $this->environment->select('id', 'environment_no')->where('type', AMBIENT_AIR)->where('status', '1')->get();

        $data = array(
            'environmentList' => $environmentList,
        );
        return view('inspection.environment.ambientAirMonitoring.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $locationList  = $this->location->select('id', 'location_name')->where('status', '1')->get();
            $staticDocno  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                ['type', "AmbientAir"],
                ['status', '1']
            ])->first();

            $data = array(
                'locationList' => $locationList,
                'staticDocno' => $staticDocno,
            );
            return view('inspection.environment.ambientAirMonitoring.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'ambient_air_no' => 'required',
            ];
            $messages = [
                'ambient_air_no.required' => "Ambient Air No is Required",
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                $env_no = $request->ambient_air_no;
                $type = AMBIENT_AIR;
                $environment =   $this->environment->store($env_no, $type);

                $this->ambient_air_monitoring->store($environment->id);

                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', __('common.message_error'));
            }

            return redirect(admin_url('environment/ambient-air/yearly/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('environment/ambient-air/yearly/list'));
        }
    }

    public function View($id)
    {
        try {
            $id = decryptId($id);
            if (Auth::check()) {
                $type = AMBIENT_AIR;
                $environmentData =   $this->environment->selectOne($id, $type);
                $ambientAirDataList = $this->ambient_air_monitoring->selectOne($id);
                $staticDocno  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                    ['type', "AmbientAir"],
                    ['status', '1']
                ])->first();
                $data = array(
                    'environmentData' => $environmentData,
                    'ambientAirDataList' => $ambientAirDataList,
                    'staticDocno' => $staticDocno,
                );
            }
            return view('inspection.environment.ambientAirMonitoring.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('environment/ambient-air/yearly/list'));
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $type = AMBIENT_AIR;
            $environmentID = $this->environment->statuschange($id, $type);
            $this->ambient_air_monitoring->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Ambient Air Monitoring Status Changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function ExportExcel()
    {
        try {
            $type = AMBIENT_AIR;
            $allData =   $this->environment->exportdata($type);

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
                $ambientAirDataList = $this->ambient_air_monitoring->selectOne($details->id);
                $document_no  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                    ['type', "AmbientAir"],
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
                $sheet->mergeCells("G{$currentRow}:O" . ($currentRow + 2));
                $sheet->setCellValue("G{$currentRow}", "AMBIENT NOISE MONITORING");
                $sheet->getStyle("G{$currentRow}:O{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);


                // document number


                $sheet->mergeCells("P$currentRow:R$currentRow")->setCellValue("P$currentRow", 'Doc. No.');
                $sheet->mergeCells("P" . ($currentRow + 1) . ":R" . ($currentRow + 1))->setCellValue("P" . ($currentRow + 1), 'Issue Dt.');
                $sheet->mergeCells("P" . ($currentRow + 2) . ":R" . ($currentRow + 2))->setCellValue("P" . ($currentRow + 2), 'Rev. & Dt.');

                $sheet->mergeCells("S$currentRow:U$currentRow")->setCellValue("S$currentRow", $document_no->doc_no);
                $sheet->mergeCells("S" . ($currentRow + 1) . ":U" . ($currentRow + 1))->setCellValue("S" . ($currentRow + 1), Displaydateformat($document_no->issue_date));
                $sheet->mergeCells("S" . ($currentRow + 2) . ":U" . ($currentRow + 2))->setCellValue("S" . ($currentRow + 2), $document_no->rev_dt);

                $sheet->getStyle("P$currentRow:U" . ($currentRow + 2))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'font' => ['bold' => true],
                ]);

                $headerRow = $currentRow + 3;


                $sheet->mergeCells("A$headerRow:B$headerRow")->setCellValue("A$headerRow", "SERIAL NO");
                $sheet->mergeCells("C$headerRow:D$headerRow")->setCellValue("C$headerRow", "Location");
                $sheet->mergeCells("E$headerRow:F$headerRow")->setCellValue("E$headerRow", "Unit");
                $sheet->mergeCells("G$headerRow:H$headerRow")->setCellValue("G$headerRow", "Date of Monitoring");
                $sheet->mergeCells("I$headerRow:J$headerRow")->setCellValue("I$headerRow", "Next Due Date Of Monitoring");
                $sheet->mergeCells("K$headerRow:L$headerRow")->setCellValue("K$headerRow", "Last Due Date Of Monitoring");
                $sheet->mergeCells("M$headerRow:N$headerRow")->setCellValue("M$headerRow", "PM 10");
                $sheet->setCellValue("O$headerRow", "PM 25");
                $sheet->setCellValue("P$headerRow", "SO2");
                $sheet->setCellValue("Q$headerRow", "NO2");
                $sheet->setCellValue("R$headerRow", "CO");
                $sheet->setCellValue("S$headerRow", "Act/Rule");
                $sheet->mergeCells("T$headerRow:U$headerRow")->setCellValue("T$headerRow", "Remark");

                $sheet->getStyle("A$headerRow:U$headerRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'font' => ['bold' => true],
                ]);


                $inspectionRow = $headerRow + 1;
               foreach ($ambientAirDataList as $index => $detail) {
                        $sheet->mergeCells("A$inspectionRow:B$inspectionRow")->setCellValue("A$inspectionRow", $detail->sr_no);
                        $sheet->mergeCells("C$inspectionRow:D$inspectionRow")->setCellValue("C$inspectionRow", getLocationname($detail->location_id));
                        $sheet->mergeCells("E$inspectionRow:F$inspectionRow")->setCellValue("E$inspectionRow", $detail->unit_name);
                        $sheet->mergeCells("G$inspectionRow:H$inspectionRow")->setCellValue("G$inspectionRow", Displaydateformat($detail->date_of_monitoring));
                        $sheet->mergeCells("I$inspectionRow:J$inspectionRow")->setCellValue("I$inspectionRow", Displaydateformat($detail->next_due_date_of_monitoring));
                        $sheet->mergeCells("K$inspectionRow:L$inspectionRow")->setCellValue("K$inspectionRow", Displaydateformat($detail->last_due_date_of_monitoring));
                        $sheet->mergeCells("M$inspectionRow:N$inspectionRow")->setCellValue("M$inspectionRow", ($detail->pm10));
                        $sheet->setCellValue("O$inspectionRow", $detail->pm25);
                        $sheet->setCellValue("P$inspectionRow", $detail->so2);
                        $sheet->setCellValue("Q$inspectionRow", ($detail->no2));
                        $sheet->setCellValue("R$inspectionRow", ($detail->co));
                        $sheet->setCellValue("S$inspectionRow", $detail->act_rule);
                        $sheet->mergeCells("T$inspectionRow:U$inspectionRow")->setCellValue("T$inspectionRow", $detail->remark);

                        $sheet->getStyle("A$inspectionRow:U$inspectionRow")->applyFromArray([
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                        ]);

                        $inspectionRow++;
                    }
                $row =  $inspectionRow+4;
            }
            $fileName = 'ambientAir.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function ExportPDF()
    {
        try {

            ini_set("pcre.backtrack_limit", "5000000");
            $type = AMBIENT_AIR;
            $allData = $this->environment->exportdata($type);

            $exportedData = [];

            foreach ($allData as $details) {
                $environmentData =   $this->environment->selectOne($details->id, $type);
                $ambientAirDataList = $this->ambient_air_monitoring->selectOne($details->id);
                $document_no  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                    ['type', "AmbientAir"],
                    ['status', '1']
                ])->first();

                $exportedData[] = [
                    'environmentData' => $environmentData,
                    'ambientAirDataList' => $ambientAirDataList,
                    'document_no' => $document_no,
                ];
            }

            $data = [
                'exportedData' => $exportedData,
                'pagetitle' => "Ambient air Monitoring",
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

            $view = view('inspection.environment.ambientAirMonitoring.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Ambient Air Monitoring Yearly Details.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
        }
    }


    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $type = AMBIENT_AIR;
                $environmentData =   $this->environment->selectOne($id, $type);

                $ambientAirDataList = $this->ambient_air_monitoring->selectOne($id);

                $document_no  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                    ['type', "AmbientAir"],
                    ['status', '1']
                ])->first();
                $data = array(
                    'environmentData' => $environmentData,
                    'ambientAirDataList' => $ambientAirDataList,
                    'document_no' => $document_no,
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

                $html = view('inspection.environment.ambientAirMonitoring.viewpdf', $data)->render();
                $mpdf->WriteHTML($html);

                $filename = "ambientAirMonitoring.pdf";
                return $mpdf->Output($filename, 'D');
            }
        } catch (Exception $ex) {
            report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }

    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $type = AMBIENTNOISE;
                $environmentData =   $this->environment->selectOne($id, $type);
                $ambientAirDataList = $this->ambient_air_monitoring->selectOne($id);
                $document_no  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                    ['type', "AmbientAir"],
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
                $sheet->mergeCells("G{$currentRow}:O" . ($currentRow + 2));
                $sheet->setCellValue("G{$currentRow}", "AMBIENT NOISE MONITORING");
                $sheet->getStyle("G{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);


                // document number


                $sheet->mergeCells("P$currentRow:R$currentRow")->setCellValue("P$currentRow", 'Doc. No.');
                $sheet->mergeCells("P" . ($currentRow + 1) . ":R" . ($currentRow + 1))->setCellValue("P" . ($currentRow + 1), 'Issue Dt.');
                $sheet->mergeCells("P" . ($currentRow + 2) . ":R" . ($currentRow + 2))->setCellValue("P" . ($currentRow + 2), 'Rev. & Dt.');

                $sheet->mergeCells("S$currentRow:U$currentRow")->setCellValue("S$currentRow", $document_no->doc_no);
                $sheet->mergeCells("S" . ($currentRow + 1) . ":U" . ($currentRow + 1))->setCellValue("S" . ($currentRow + 1), Displaydateformat($document_no->issue_date));
                $sheet->mergeCells("S" . ($currentRow + 2) . ":U" . ($currentRow + 2))->setCellValue("S" . ($currentRow + 2), $document_no->rev_dt);

                $sheet->getStyle("P$currentRow:U" . ($currentRow + 2))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'font' => ['bold' => true],
                ]);

                $headerRow = $currentRow + 3;

                $sheet->mergeCells("A$headerRow:B$headerRow")->setCellValue("A$headerRow", "SERIAL NO");
                $sheet->mergeCells("C$headerRow:D$headerRow")->setCellValue("C$headerRow", "Location");
                $sheet->mergeCells("E$headerRow:F$headerRow")->setCellValue("E$headerRow", "Unit");
                $sheet->mergeCells("G$headerRow:H$headerRow")->setCellValue("G$headerRow", "Date of Monitoring");
                $sheet->mergeCells("I$headerRow:J$headerRow")->setCellValue("I$headerRow", "Next Due Date Of Monitoring");
                $sheet->mergeCells("K$headerRow:L$headerRow")->setCellValue("K$headerRow", "Last Due Date Of Monitoring");
                $sheet->mergeCells("M$headerRow:N$headerRow")->setCellValue("M$headerRow", "PM 10");
                $sheet->mergeCells("O$headerRow:O$headerRow")->setCellValue("O$headerRow", "PM 25");
                $sheet->mergeCells("P$headerRow:P$headerRow")->setCellValue("P$headerRow", "SO2");
                $sheet->mergeCells("Q$headerRow:Q$headerRow")->setCellValue("Q$headerRow", "NO2");
                $sheet->mergeCells("R$headerRow:R$headerRow")->setCellValue("R$headerRow", "CO");
                $sheet->mergeCells("S$headerRow:S$headerRow")->setCellValue("S$headerRow", "Act/Rule");
                $sheet->mergeCells("T$headerRow:U$headerRow")->setCellValue("T$headerRow", "Remark");



                $sheet->getStyle("A$headerRow:U$headerRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'font' => ['bold' => true],
                ]);

                $inspectionRow = $headerRow + 1;
                foreach ($ambientAirDataList as $index => $detail) {

                    $sheet->mergeCells("A$inspectionRow:B$inspectionRow")->setCellValue("A$inspectionRow", $detail->sr_no);
                    $sheet->mergeCells("C$inspectionRow:D$inspectionRow")->setCellValue("C$inspectionRow", getLocationname($detail->location_id));
                    $sheet->mergeCells("E$inspectionRow:F$inspectionRow")->setCellValue("E$inspectionRow", $detail->unit_name);
                    $sheet->mergeCells("G$inspectionRow:H$inspectionRow")->setCellValue("G$inspectionRow", Displaydateformat($detail->date_of_monitoring));
                    $sheet->mergeCells("I$inspectionRow:J$inspectionRow")->setCellValue("I$inspectionRow", Displaydateformat($detail->next_due_date_of_monitoring));
                    $sheet->mergeCells("K$inspectionRow:L$inspectionRow")->setCellValue("K$inspectionRow", Displaydateformat($detail->next_due_date_of_monitoring));
                    $sheet->mergeCells("M$inspectionRow:N$inspectionRow")->setCellValue("M$inspectionRow", ($detail->pm10));
                    $sheet->mergeCells("O$inspectionRow:O$inspectionRow")->setCellValue("O$inspectionRow", $detail->pm25);
                    $sheet->mergeCells("P$inspectionRow:P$inspectionRow")->setCellValue("P$inspectionRow", $detail->so2);
                    $sheet->mergeCells("Q$inspectionRow:Q$inspectionRow")->setCellValue("Q$inspectionRow", ($detail->no2));
                    $sheet->mergeCells("R$inspectionRow:R$inspectionRow")->setCellValue("R$inspectionRow", ($detail->co));
                    $sheet->mergeCells("S$inspectionRow:S$inspectionRow")->setCellValue("S$inspectionRow", $detail->act_rule);
                    $sheet->mergeCells("T$inspectionRow:U$inspectionRow")->setCellValue("T$inspectionRow", $detail->remark);

                    $sheet->getStyle("A$inspectionRow:U$inspectionRow")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $inspectionRow++;
                }
            }
            $fileName = 'ambientAir.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (Exception $ex) {
            report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }
}
