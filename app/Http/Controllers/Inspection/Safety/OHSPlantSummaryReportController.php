<?php

namespace App\Http\Controllers\Inspection\Safety;

use Exception;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\SimpleExcel\SimpleExcelWriter;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Safety\OHSPlantSummaryReport;

class OHSPlantSummaryReportController extends Controller
{
    private $ohsreport;
    private $unit;
    private $document_reference;

    public function __construct()
    {
        $this->unit = new Unit();
        $this->ohsreport = new OHSPlantSummaryReport();
        $this->document_reference = new InspectionStaticDocno();
    }
    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->ohsreport->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='' data-id='" . encryptId($row->inspection_id) . "' data-type = '1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='' data-id='" . encryptId($row->inspection_id) . "' data-type = '0'>In-Active</span>";
                            }
                            // }
                            return $text;
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('inspection_date', function ($row) {
                            return Displaydateformat($row->inspection_date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('inspection_status', function ($row) {
                            $text = '';
                            switch ($row->inspection_status) {
                                case WAITING_FOR_EHS_OFFICER_VERIFICATION:
                                    $text = "<span class='badge bg-primary rounded' style='font-size: 1.0em;'>Waiting For EHS Officer Verification</span>";
                                    break;
                                case WAITING_FOR_CAPA_ACTION:
                                    $text = "<span class='badge bg-info rounded' style='font-size: 1.0em;'>Waiting For CAPA Action</span>";
                                    break;
                                case WAITING_FOR_CAPA_VERIFICATION:
                                    $text = "<span class='badge bg-warning rounded' style='font-size: 1.0em;'>Waiting For CAPA Verification</span>";
                                    break;
                                case WAITING_FOR_L1_VERIFICATION:
                                    $text = "<span class='badge bg-warning rounded' style='font-size: 1.0em;'>Waiting For Level-1 Manager Verification</span>";
                                    break;
                                case WAITING_FOR_L2_VERIFICATION:
                                    $text = "<span class='badge bg-warning rounded' style='font-size: 1.0em;'>Waiting For Level-2 Manager Verification</span>";
                                    break;
                                case INSPECTION_APPROVED:
                                    $text = "<span class='badge bg-success rounded' style='font-size: 1.0em;'>CLOSED</span>";
                                    break;
                                case L2_MANAGER_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>LEVEL 2 OFFICER REJECTED - WAITING FOR CAPA ACTION</span>";
                                    break;
                                case L1_MANAGER_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>LEVEL 1 OFFICER REJECTED - WAITING FOR CAPA ACTION</span>";
                                    break;
                                case EHS_OFFICER_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>EHS OFFICER REJECTED - WAITING FOR CAPA ACTION</span>";
                                    break;
                                default:
                                    $text = "<span class='badge rounded-pill text-bg-warning'>Unknown</span>";
                            }
                            return $text;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('safety/ohc-plant-summary/view/' . encryptId($row->inspection_id)) . '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            if ($row->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/ohc-plant-summary/verification/' . encryptId($row->inspection_id)) . '/ehs" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if (($row->inspection_status == WAITING_FOR_CAPA_ACTION || $row->inspection_status == L2_MANAGER_REJECTED || $row->inspection_status == EHS_OFFICER_REJECTED || $row->inspection_status == L1_MANAGER_REJECTED) && (CheckUserRole(ROLE_FIRE_ASSOCIATES) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/ohc-plant-summary/verification/' . encryptId($row->inspection_id)) . '/capa" class="" title="' . __('inspection.capa_action') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_CAPA_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/ohc-plant-summary/verification/' . encryptId($row->inspection_id)) . '/ehsVerify" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L1_VERIFICATION && (CheckUserRole(ROLE_L1_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/ohc-plant-summary/verification/' . encryptId($row->inspection_id)) . '/level-one-manager" class="" title="' . __('inspection.l1_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L2_VERIFICATION && (CheckUserRole(ROLE_L2_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/ohc-plant-summary/verification/' . encryptId($row->inspection_id)) . '/level-two-manager" class="" title="' . __('inspection.l2_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            $btn .= '<a href="' . admin_url('safety/ohc-plant-summary/exportViewPdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';

                            $btn .= '<a href="' . admin_url('safety/ohc-plant-summary/generalexcel/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="Excel"> <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i></a>';

                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'issue_date', 'inspection_date'])
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

        $data = array();
        return view('inspection.Safety.ohc_plant_summary.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $unit = $this->unit->getUnit();
            $document_no = $this->document_reference->selectUsingName('OhsPlantSummaryReport');

            $data = array(
                'units' => $unit,
                'document_no' => $document_no,

            );
            return view('inspection.Safety.ohc_plant_summary.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/ohc-plant-summary/list'));
        }
    }

    public function store(Request $request)
    {
        try {


            $rules = [
                'doc_no' => 'required',
                'issue_date' => 'required',
                'inspection_date' => 'required',
                'updated_frequency' => 'required',
                'description.*' => 'required',
                'unit_1.*' => 'required',
                'unit_2.*' => 'required',
                'unit_3.*' => 'required',
                'unit_4.*' => 'required',
                'total_quantity.*' => 'required',
                'fire_pump_details.*' => 'required',
                'fire_pump_details_unit_1.*' => 'required',
                'fire_pump_details_unit_2.*' => 'required',
                'fire_pump_details_unit_3.*' => 'required',
                'fire_pump_details_unit_4.*' => 'required',
                'signature_upload' => [
                    function ($attribute, $value, $fail) {
                        $user = Auth::user();
                        if (is_null($user->signature_upload)) {
                            $fail('Signature is required.');
                        }
                    }
                ],
            ];

            $messages = [
                'doc_no.required' => 'Document number is required.',
                'issue_date.required' => 'Issue Date is required.',
                'inspection_date.required' => 'Inspection Date is required.',
                'updated_frequency.required' => 'Updated Frequency is required.',
                'description.*.required' => 'Description is required.',
                'unit_1.*.required' => 'Unit 1 is required.',
                'unit_2.*.required' => 'Unit 2 is required.',
                'unit_3.*.required' => 'Unit 3 is required.',
                'unit_4.*.required' => 'Unit 4 is required.',
                'total_quantity.*.required' => 'Total Quantity is required.',
                'fire_pump_details.*.required' => 'Fire Pump Details are required.',
                'fire_pump_details_unit_1.*.required' => 'Unit 1 is required.',
                'fire_pump_details_unit_2.*.required' => 'Unit 2 is required.',
                'fire_pump_details_unit_3.*.required' => 'Unit 3 is required.',
                'fire_pump_details_unit_4.*.required' => 'Unit 4 is required.',
                'signature_upload' => 'Signature is required.',
            ];


            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $ohc_plant_summary = $this->ohsreport->store();

            Session::flash('success', __('common.created_msg'));
            return redirect(admin_url('safety/ohc-plant-summary/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/ohc-plant-summary/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->ohsreport->selectOne($id);
            $quantity_details = json_decode($inspection_details->quantity_details, true);
            $fire_water_pump_details = json_decode($inspection_details->fire_water_pump_details, true);
            $unit = $this->unit->getUnit();
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);

            $data = [
                'inspection_details' => $inspection_details,
                'quantity_details' => $quantity_details,
                'fire_water_pump_details' => $fire_water_pump_details,
                'units' => $unit,
                'document_no' => $document_no,

            ];
            return view('inspection.Safety.ohc_plant_summary.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/ohc-plant-summary/list'));
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->ohsreport->exportdata();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $row = 1;

            foreach ($allData as $data) {
                $startRow = $row;
                $inspection_details = $this->ohsreport->selectOne($data->inspection_id);
                $quantity_details = json_decode($inspection_details->quantity_details, true);
                $fire_water_pump_details = json_decode($inspection_details->fire_water_pump_details, true);
                $units = $this->unit->getUnit();
                $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);



                foreach (range('A', 'J') as $col) {
                    $sheet->getColumnDimension($col)->setWidth(15);
                    $sheet->getStyle($col)->getAlignment()->setWrapText(true);
                }

                $sheet->mergeCells("A{$row}:C" . ($row + 2));
                $sheet->getStyle("A{$row}:C" . ($row + 2))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
                ]);

                $logoPath = public_path('assets/images/logo-dark.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates("B{$row}");
                    $drawing->setOffsetX(25);
                    $drawing->setOffsetY(10);
                    $drawing->setWidth(90);
                    $drawing->setHeight(50);
                    $drawing->setWorksheet($sheet);
                }

                $sheet->mergeCells("D{$row}:H" . ($row + 2));
                $sheet->setCellValue("D{$row}", 'OHS PLANT SUMMARY REPORT PN INTERNATIONAL PVT. LTD.');
                $sheet->getStyle("D{$row}:H" . ($row + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $labelMap = [
                    ["I{$row}", 'Doc. No.', $document_no->doc_no],
                    ["I" . ($row + 1), 'Issue Dt.', Displaydateformat($document_no->issue_date)],
                    ["I" . ($row + 2), 'Rev. & Dt.', $document_no->rev_dt],
                ];

                foreach ($labelMap as [$labelCell, $label, $data]) {
                    $dataCell = str_replace('I', 'J', $labelCell);
                    $sheet->setCellValue($labelCell, $label);
                    $sheet->setCellValue($dataCell, $data);
                    $sheet->getStyle("$labelCell:$dataCell")->applyFromArray([
                        'font' => ['bold' => true],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'wrapText' => true,
                    ]);
                }

                $row += 3;

                $sheet->mergeCells("A{$row}:E{$row}")->setCellValue("A{$row}", "Date:- " . Displaydateformat($inspection_details->inspection_date));
                $sheet->mergeCells("F{$row}:J{$row}")->setCellValue("F{$row}", "UPDATED FREQUENCY :- " . $inspection_details->updated_frequency);
                $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $row += 2;

                $sheet->mergeCells("A{$row}:A" . ($row + 1))->setCellValue("A{$row}", "SR.NO");
                $sheet->mergeCells("B{$row}:D" . ($row + 1))->setCellValue("B{$row}", "DESCRIPTION");
                $sheet->mergeCells("E{$row}:H{$row}")->setCellValue("E{$row}", "QUANTITY");

                $colIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString('E');
                foreach ($units as $unit) {
                    $unitCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                    $sheet->setCellValue("{$unitCol}" . ($row + 1), $unit->unit_name);
                    $colIndex++;
                }

                $sheet->mergeCells("I{$row}:J" . ($row + 1))->setCellValue("I{$row}", "TOTAL QUANTITY");

                $sheet->getStyle("A{$row}:J" . ($row + 1))->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'wrapText' => true,
                ]);

                $row += 2;

                $sr = 1;
                foreach ($quantity_details as $detail) {
                    $sheet->mergeCells("B{$row}:D{$row}");
                    $sheet->setCellValue("A{$row}", $sr);
                    $sheet->setCellValue("B{$row}", $detail['description'] ?? '');
                    $sheet->setCellValue("E{$row}", $detail['unit_1'] ?? '');
                    $sheet->setCellValue("F{$row}", $detail['unit_2'] ?? '');
                    $sheet->setCellValue("G{$row}", $detail['unit_3'] ?? '');
                    $sheet->setCellValue("H{$row}", $detail['unit_4'] ?? '');
                    $sheet->mergeCells("I{$row}:J{$row}");
                    $sheet->setCellValue("I{$row}", $detail['total_quantity'] ?? '');

                    $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $row++;
                    $sr++;
                }

                $row += 2;

                $sheet->mergeCells("A{$row}:J{$row}")->setCellValue("A{$row}", "FIRE WATER PUMP DETAILS");
                $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $row++;

                $sheet->mergeCells("A{$row}:A" . ($row + 1))->setCellValue("A{$row}", "SR.NO");
                $sheet->mergeCells("B{$row}:F" . ($row + 1))->setCellValue("B{$row}", "FIRE PUMP DETAILS");
                $sheet->mergeCells("G{$row}:J{$row}")->setCellValue("G{$row}", "CAPACITY");

                $colIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString('G');
                foreach ($units as $unit) {
                    $unitCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                    $sheet->setCellValue("{$unitCol}" . ($row + 1), $unit->unit_name);
                    $colIndex++;
                }

                $sheet->getStyle("A{$row}:J" . ($row + 1))->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'wrapText' => true,
                ]);
                $row += 2;

                $sr = 1;
                foreach ($fire_water_pump_details as $item) {
                    $sheet->setCellValue("A{$row}", $sr);
                    $sheet->mergeCells("B{$row}:F{$row}");
                    $sheet->setCellValue("B{$row}", $item['fire_pump_details'] ?? '');
                    $sheet->setCellValue("G{$row}", $item['fire_pump_details_unit_1'] ?? '');
                    $sheet->setCellValue("H{$row}", $item['fire_pump_details_unit_2'] ?? '');
                    $sheet->setCellValue("I{$row}", $item['fire_pump_details_unit_3'] ?? '');
                    $sheet->setCellValue("J{$row}", $item['fire_pump_details_unit_4'] ?? '');

                    $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $row++;
                    $sr++;
                }

                $signatureRow = $row;
                // $sheet->getRowDimension($signatureRow)->setRowHeight(60);

                $approvedByName = getUserName($inspection_details->created_by);
                $sheet->mergeCells("A$signatureRow:J$signatureRow")->setCellValue("A$signatureRow", "CHECKED AND PREPARED BY: $approvedByName");

                $sheet->getStyle("A{$startRow}:J{$row}")->applyFromArray([
                    'borders' => [
                        'top'    => ['borderStyle' => Border::BORDER_THICK],
                        'bottom' => ['borderStyle' => Border::BORDER_THICK],
                        'left'   => ['borderStyle' => Border::BORDER_THICK],
                        'right'  => ['borderStyle' => Border::BORDER_THICK],
                    ],
                ]);
                $row += 4;
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'OHS_All_Reports.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('safety/ohc-plant-summary/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->ohsreport->exportdata();
            $unit = $this->unit->getUnit();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }


            $data = array(
                'units' => $unit,
                'content' => $allData,
                'pagetitle' => "OHS Plant Summary Report",
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

            $view = view('inspection.Safety.ohc_plant_summary.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "OHS Plant Summary Report.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('safety/ohc-plant-summary/list'));
        }
    }


    public function exportViewPdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $inspection_details = $this->ohsreport->selectOne($id);
                $quantity_details = json_decode($inspection_details->quantity_details, true);
                $units = $this->unit->getUnit();
                $fire_water_pump_details = json_decode($inspection_details->fire_water_pump_details, true);
                $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);


                $data = [
                    'inspection_details' => $inspection_details,
                    'quantity_details' => $quantity_details,
                    'fire_water_pump_details' => $fire_water_pump_details,
                    'pagetitle' => "OHS Plant Summary Report",
                    'units' => $units,
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

            $html = view('inspection.Safety.ohc_plant_summary.viewpdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "OHS Plant Summary Report.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('safety/ohc-plant-summary/list'));
        }
    }

    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $inspection_details = $this->ohsreport->selectOne($id);
            $quantity_details = json_decode($inspection_details->quantity_details, true);
            $fire_water_pump_details = json_decode($inspection_details->fire_water_pump_details, true);
            $units = $this->unit->getUnit();
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);

            $prepared_by_signature = GetSafetySignature($inspection_details->created_by, $inspection_details->id, OHS_SUMMARY_REPORT);
            $verified_by_signature = GetSafetySignature($inspection_details->updated_by, $inspection_details->id, OHS_SUMMARY_REPORT);

            $sheet->mergeCells("A1:C3");
            $sheet->getStyle("A1:C3")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);

            $logoPath = public_path('assets/images/logo-dark.png');
            if (file_exists($logoPath)) {
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setPath($logoPath);
                $drawing->setCoordinates("B1");
                $drawing->setOffsetX(25);
                $drawing->setOffsetY(10);
                $drawing->setWidth(90);
                $drawing->setHeight(50);
                $drawing->setWorksheet($sheet);
            }

            foreach (range('A', 'J') as $col) {
                $sheet->getColumnDimension($col)->setWidth(15);
                $sheet->getStyle($col)->getAlignment()->setWrapText(true);
            }

            $sheet->mergeCells('D1:H3');
            $sheet->setCellValue('D1', 'OHS PLANT SUMMARY REPORT PN INTERNATIONAL PVT. LTD.');
            $sheet->getStyle('D1:H3')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $labelMap = [
                ['I1', 'Doc. No.', $document_no->doc_no],
                ['I2', 'Issue Dt.', Displaydateformat($document_no->issue_date)],
                ['I3', 'Rev. & Dt.', $document_no->rev_dt],
            ];

            foreach ($labelMap as [$labelCell, $label, $data]) {
                $dataCell = str_replace('I', 'J', $labelCell);
                $sheet->setCellValue($labelCell, $label);
                $sheet->setCellValue($dataCell, $data);
                $sheet->getStyle("$labelCell:$dataCell")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'wrapText' => true,
                ]);
            }

            $sheet->mergeCells("A4:E4")->setCellValue("A4", "Date:- " . Displaydateformat($inspection_details->inspection_date));
            $sheet->mergeCells("F4:J4")->setCellValue("F4", "UPDATED FREQUENCY :- " . ($inspection_details->updated_frequency));
            $sheet->getStyle("A4:J4")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $sheet->mergeCells("A6:A7")->setCellValue("A6", "SR.NO");
            $sheet->mergeCells("B6:D7")->setCellValue("B6", "DESCRIPTION");
            $sheet->mergeCells("E6:H6")->setCellValue("E6", "QUANTITY");

            $colIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString('E');
            foreach ($units as $unit) {
                $unitCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue("{$unitCol}7", $unit->unit_name);
                $colIndex++;
            }

            $sheet->mergeCells("I6:J7")->setCellValue("I6", "TOTAL QUANTITY");

            $sheet->getStyle("A6:J7")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'wrapText' => true,
            ]);

            $row = 8;
            $sr = 1;
            foreach ($quantity_details as $detail) {
                $sheet->mergeCells("B{$row}:D{$row}");
                $sheet->setCellValue("A$row", $sr);
                $sheet->setCellValue("B$row", $detail['description'] ?? '');
                $sheet->setCellValue("E$row", $detail['unit - 1'] ?? '');
                $sheet->setCellValue("F$row", $detail['unit - 2'] ?? '');
                $sheet->setCellValue("G$row", $detail['unit - 3'] ?? '');
                $sheet->setCellValue("H$row", $detail['unit - 4'] ?? '');
                $sheet->mergeCells("I{$row}:J{$row}");
                $sheet->setCellValue("I$row", $detail['total_quantity'] ?? '');

                $sheet->getStyle("A$row:J$row")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $row++;
                $sr++;
            }

            $row += 2;
            $sheet->mergeCells("A{$row}:J{$row}")->setCellValue("A{$row}", "FIRE WATER PUMP DETAILS");
            $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $row++;

            $sheet->mergeCells("A{$row}:A" . ($row + 1))->setCellValue("A{$row}", "SR.NO");
            $sheet->mergeCells("B{$row}:F" . ($row + 1))->setCellValue("B{$row}", "FIRE PUMP DETAILS");
            $sheet->mergeCells("G{$row}:J{$row}")->setCellValue("G{$row}", "CAPACITY");

            $colIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString('G');
            foreach ($units as $unit) {
                $unitCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue("{$unitCol}" . ($row + 1), $unit->unit_name);
                $colIndex++;
            }

            $sheet->getStyle("A{$row}:J" . ($row + 1))->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'wrapText' => true,
            ]);
            $row += 2;

            $sr = 1;
            foreach ($fire_water_pump_details as $item) {
                $sheet->setCellValue("A{$row}", $sr);
                $sheet->mergeCells("B{$row}:F{$row}");
                $sheet->setCellValue("B{$row}", $item['fire_pump_details'] ?? '');
                $sheet->setCellValue("G{$row}", $item['fire_pump_details_unit_1'] ?? '');
                $sheet->setCellValue("H{$row}", $item['fire_pump_details_unit_2'] ?? '');
                $sheet->setCellValue("I{$row}", $item['fire_pump_details_unit_3'] ?? '');
                $sheet->setCellValue("J{$row}", $item['fire_pump_details_unit_4'] ?? '');

                $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $row++;
                $sr++;
            }

            $signatureRow = $row;
            $approvedByName = getUserName($inspection_details->created_by);

            $sheet->mergeCells("A$signatureRow:J$signatureRow")
                ->setCellValue("A$signatureRow", "CHECKED AND PREPARED BY: $approvedByName");

            $sheet->getStyle("A$signatureRow:J$signatureRow")->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                ],
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);
            $writer = new Xlsx($spreadsheet);
            $fileName = 'OHS Summary Report.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/ohc-plant-summary/list'));
        }
    }
}
