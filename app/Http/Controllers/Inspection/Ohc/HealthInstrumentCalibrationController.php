<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Master\Frequency;
use App\Models\Inspection\Ohc\HealthInstrumentCalibration;
use App\Models\Inspection\Ohc\HealthInstrumentCalibrationDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Master\Unit;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Exception;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HealthInstrumentCalibrationController extends Controller
{

    private $health_instrument_calibration;
    private $health_instrument_calibration_details;
    private $unit;
    private $document_reference;
    private $frequency;



    public function __construct()
    {
        $this->health_instrument_calibration = new HealthInstrumentCalibration();
        $this->health_instrument_calibration_details = new HealthInstrumentCalibrationDetails();
        $this->unit = new Unit();
        $this->document_reference = new InspectionStaticDocno();
        $this->frequency = new Frequency();
    }


    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->health_instrument_calibration->list();
                    $datatables = Datatables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type='1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type='0'>In-Active</span>";
                            }
                            return $text;
                        })
                        ->addColumn('created_at', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })

                        ->addColumn('action', function ($row) {
                            $btn = '<a href="' . admin_url('ohc/health-instrument/calibration-track-sheet/view/' . encryptId($row->id)) . '" class="view-icon" title="' . __('common.view') . '">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>';

                            $btn .= '<a href="' . admin_url('ohc/health-instrument/calibration-track-sheet/generalpdf/' . encryptId($row->id)) . '" style="margin-left: 5px;" title="PDF">
                                        <i class="fas fa-file-pdf" style="color: #e67265;" aria-hidden="true"></i>
                                    </a>';

                            $btn .= '<a href="' . admin_url('ohc/health-instrument/calibration-track-sheet/generalExcel/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="Excel"> <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i></a>';

                            return $btn;
                        })

                        ->rawColumns(['action', 'issue_date', 'created_by', 'status', 'created_at'])
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
        $unitList = $this->unit->getUnitList();
        $data = [
            'unitList' => $unitList,

        ];

        return view('inspection.inspection_ohc.health_instrument.list', $data);
    }


    public function Add(Request $request)
    {
        try {
            $unitList = $this->unit->getUnitList();
            $document_no = $this->document_reference->selectUsingName('HealthInstrumentcalibration');
            $frequency = $this->frequency->getFrequency();


            $data = array(
                'unitList' => $unitList,
                'document_no' => $document_no,
                'frequency' => $frequency,
            );
            return view('inspection.inspection_ohc.health_instrument.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        // dd($request->all());
        try {
            $rules = [
                'audit_id' => 'required',
                'document_no' => 'required',
                'issue_date' => 'required',
                'unit_id' => 'required',
                'review_date' => 'required',
                'health_instrument.*.instrument_name' => 'required',
                'health_instrument.*.resource_code' => 'required',
                'health_instrument.*.exact_location' => 'required',
                'health_instrument.*.instrument_serial_no' => 'required',
                'health_instrument.*.make' => 'required',
                'health_instrument.*.model' => 'required',
                'health_instrument.*.instrument_range' => 'required',
                'health_instrument.*.frequency_id' => 'required',
                'health_instrument.*.date_of_calibration' => 'required',
                'health_instrument.*.due_date_of_calibration' => 'required',
                'health_instrument.*.instrument_remarks' => 'required',
            ];

            $messages = [
                'document_no.required' => 'Document number is required.',
                'issue_date.required' => 'Issue date is required.',
                'review_date.required' => 'Review date is required.',
                'unit_id.required' => 'Please select a unit.',
                'health_instrument.*.instrument_name.required' => 'Please enter the instrument name.',
                'health_instrument.*.resource_code.required' => 'Please enter the resource code.',
                'health_instrument.*.exact_location.required' => 'Please specify the exact location.',
                'health_instrument.*.instrument_serial_no.required' => 'Instrument serial number is required.',
                'health_instrument.*.make.required' => 'Please enter the make of the instrument.',
                'health_instrument.*.model.required' => 'Please enter the model of the instrument.',
                'health_instrument.*.instrument_range.required' => 'Instrument range is required.',
                'health_instrument.*.frequency_id.required' => 'Calibration frequency is required.',
                'health_instrument.*.date_of_calibration.required' => 'Please select the date of calibration.',
                'health_instrument.*.due_date_of_calibration.required' => 'Please select the due date of calibration.',
                'health_instrument.*.instrument_remarks.required' => 'Remarks are required.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);


            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            try {

                $health_instrument = $this->health_instrument_calibration->store();
                $id = $health_instrument->id;

                $health_instrument_details = $this->health_instrument_calibration_details->store($id);

                Session::flash('success', 'Your data has been created successfully!');
                return redirect(admin_url('ohc/health-instrument/calibration-track-sheet/list'));
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function view(Request $request, $id)
    {
        try {
            if (Auth::check()) {

                $id = decryptId($id);
                $health_id = $this->health_instrument_calibration->getDocumentId($id);
                $health_instrument_calibration_details = $this->health_instrument_calibration->selectOne($id);
                $document_no = $this->document_reference->selectOne($health_id->document_reference_id);

                $data = array(
                    'health_instrument_calibration_details' => $health_instrument_calibration_details,
                    'document_no' => $document_no,
                );
            }
            return view('inspection.inspection_ohc.health_instrument.view', $data);
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
        }
    }


    public function ExportPdf(Request $request)
    {
        try {
            $allData = $this->health_instrument_calibration->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }

            $data = array(
                'content' => $allData,
                'pagetitle' => "Health Instrument Calibration Details",
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
            $view = view('inspection.inspection_ohc.health_instrument.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Heakth Instrument Calibration.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/health-instrument/calibration-track-sheet/list'));
        }
    }

    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->health_instrument_calibration->exportdata(); // grouped by unit

            if (empty($allData)) {
                return redirect()->back()->with('error', 'No data found');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            foreach (range('A', 'M') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }



            $leftLogoPath = public_path('assets/images/logo-dark.png');
            if (file_exists($leftLogoPath)) {
                $drawing = new Drawing();
                $drawing->setName('KARAM Logo');
                $drawing->setPath($leftLogoPath);
                $drawing->setCoordinates('B1');
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
            }

            $sheet->mergeCells('A1:C3');
            $sheet->getStyle('A1:C3')->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ]);

            $sheet->mergeCells("D1:I3");
            $sheet->setCellValue("D1", "Fire & Safety Equipment Instrument Calibration Track Sheet\nPN International Pvt Ltd");
            $sheet->getStyle("D1:I3")->applyFromArray([
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_THIN],
                    'bottom' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ]);

            $rightLogoPath = public_path('assets/images/calibration.png');
            if (file_exists($rightLogoPath)) {
                $drawing = new Drawing();
                $drawing->setName('Calibration Logo');
                $drawing->setPath($rightLogoPath);
                $drawing->setCoordinates('J1');
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
            }

            $sheet->mergeCells('J1:K3');
            $sheet->getStyle('J1:K3')->applyFromArray([
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_THIN],
                    'bottom' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ]);

            $docInfo = [
                'L1' => ['Doc. No.', 'OF/SA/133'],
                'L2' => ['Issue Dt.', '15.05.2021'],
                'L3' => ['Rev. & Dt.', '0'],
            ];

            foreach ($docInfo as $cell => [$label, $value]) {
                $valueCell = 'M' . substr($cell, 1);
                $sheet->setCellValue($cell, $label);
                $sheet->setCellValue($valueCell, $value);
                $sheet->getStyle("$cell:$valueCell")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
            }

            $headerRow = 4;

            // --- HEADER ROW ---
            $sheet->setCellValue("A{$headerRow}", "Sr.NO");
            $sheet->setCellValue("B{$headerRow}", "Instrument Name");
            $sheet->setCellValue("C{$headerRow}", "Resource Code");
            $sheet->setCellValue("D{$headerRow}", "Exact Location");
            $sheet->setCellValue("E{$headerRow}", "Unit");
            $sheet->setCellValue("F{$headerRow}", "Instrument Serial Number");
            $sheet->setCellValue("G{$headerRow}", "Make");
            $sheet->setCellValue("H{$headerRow}", "Model");
            $sheet->setCellValue("I{$headerRow}", "Instrument Range");
            $sheet->setCellValue("J{$headerRow}", "Calibration Frequency");
            $sheet->setCellValue("K{$headerRow}", "Date of Calibration");
            $sheet->setCellValue("L{$headerRow}", "Next Due Date of Calibration");
            $sheet->setCellValue("M{$headerRow}", "Remark");

            $sheet->getStyle("A{$headerRow}:M{$headerRow}")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $dataRow = $headerRow + 1;

            // UNIT HEADERS
            foreach ($allData as $unitName => $items) {
                $sheet->mergeCells("A{$dataRow}:M{$dataRow}");
                $sheet->setCellValue("A{$dataRow}", strtoupper($unitName));
                $sheet->getStyle("A{$dataRow}:M{$dataRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E8E8E8'],
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $dataRow++;

                $sr = 1;
                foreach ($items as $detail) {
                    $sheet->setCellValue("A{$dataRow}", $sr);
                    $sheet->setCellValue("B{$dataRow}", $detail['instrument_name']);
                    $sheet->setCellValue("C{$dataRow}", $detail['resource_code']);
                    $sheet->setCellValue("D{$dataRow}", $detail['exact_location'] ?? '');
                    $sheet->setCellValue("E{$dataRow}", getUnitname($detail['unit_id'] ?? ''));
                    $sheet->setCellValue("F{$dataRow}", $detail['instrument_serial_no']);
                    $sheet->setCellValue("G{$dataRow}", $detail['make'] ?? '');
                    $sheet->setCellValue("H{$dataRow}", $detail['model'] ?? '');
                    $sheet->setCellValue("I{$dataRow}", $detail['instrument_range'] ?? '');
                    $sheet->setCellValue("J{$dataRow}", getFrequencyname($detail['calibration_frequency'] ?? ''));
                    $sheet->setCellValue("K{$dataRow}", Displaydateformat($detail['date_of_calibration'] ?? ''));
                    $sheet->setCellValue("L{$dataRow}", Displaydateformat($detail['due_date_of_calibration'] ?? ''));
                    $sheet->setCellValue("M{$dataRow}", $detail['remarks'] ?? '');

                    $sheet->getStyle("A{$dataRow}:M{$dataRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $dataRow++;
                    $sr++;
                }
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Health Instrument calibration .xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after some time!');
            return redirect(admin_url('ohc/health-instrument/calibration-track-sheet/list'));
        }
    }



    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {

                $health_id = $this->health_instrument_calibration->getDocumentId($id);
                $health_instrument_calibration_details = $this->health_instrument_calibration->selectOne($id);
                $document_no = $this->document_reference->selectOne($health_id->document_reference_id);

                $data = [
                    'health_instrument_calibration_details' => $health_instrument_calibration_details,
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

            $html = view('inspection.inspection_ohc.health_instrument.generalpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "Health instrument Calibration Details.pdf";
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
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->getColumnDimension('A')->setWidth(12);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(20);
            $sheet->getColumnDimension('I')->setWidth(25);
            $sheet->getColumnDimension('J')->setWidth(25);
            $sheet->getColumnDimension('K')->setWidth(20);
            $sheet->getColumnDimension('L')->setWidth(20);
            $sheet->getColumnDimension('M')->setWidth(20);



            $health_id = $this->health_instrument_calibration->getDocumentId($id);
            $inspection_detail = $this->health_instrument_calibration->selectOne($id);
            $document_no = $this->document_reference->selectOne($health_id->document_reference_id);

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            // Left Logo (KARAM)
            $leftLogoPath = public_path('assets/images/logo-dark.png');
            if (file_exists($leftLogoPath)) {
                $drawing = new Drawing();
                $drawing->setName('KARAM Logo');
                $drawing->setDescription('Left Logo');
                $drawing->setPath($leftLogoPath);
                $drawing->setCoordinates('B1');
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
            }
            $sheet->mergeCells('A1:C3');
            $sheet->getStyle('A1:C3')->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ]);

            // Title (Center)
            $sheet->mergeCells("D1:I3");
            $sheet->setCellValue("D1", "Fire & Safety Equipment Instrument Calibration Track Sheet\nPN International Pvt Ltd");
            $sheet->getStyle("D1:I3")->applyFromArray([
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_THIN],
                    'bottom' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ]);

            $rightLogoPath = public_path('assets/images/calibration.png');
            if (file_exists($rightLogoPath)) {
                $drawing = new Drawing();
                $drawing->setName('Calibration Logo');
                $drawing->setDescription('Right Logo');
                $drawing->setPath($rightLogoPath);
                $drawing->setCoordinates('J1');
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
            }

            $sheet->mergeCells('J1:K3');
            $sheet->getStyle('J1:K3')->applyFromArray([
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_THIN],
                    'bottom' => ['borderStyle' => Border::BORDER_THIN],
                    'left' => ['borderStyle' => Border::BORDER_NONE],
                    'right' => ['borderStyle' => Border::BORDER_NONE],
                ],
            ]);


            // Document Info
            $docInfo = [
                'L1' => ['Doc. No.', 'OF/SA/133'],
                'L2' => ['Issue Dt.', '15.05.2021'],
                'L3' => ['Rev. & Dt.', '0'],
            ];

            foreach ($docInfo as $cell => [$label, $value]) {
                $valueCell = 'M' . substr($cell, 1);

                $sheet->setCellValue($cell, $label);
                $sheet->setCellValue($valueCell, $value);

                $sheet->getStyle("$cell:$valueCell")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);
            }


            $sheet->setCellValue("M1", $document_no->doc_no);
            $sheet->setCellValue("M2", Displaydateformat($document_no->issue_date));
            $sheet->setCellValue("M3", $document_no->rev_dt);


            $sheet->setCellValue("A4", "Sr.NO");
            $sheet->setCellValue("B4", "Instrument Name");
            $sheet->setCellValue("C4", "Resource Code");
            $sheet->setCellValue("D4", "Exact Location");
            $sheet->setCellValue("E4", "Unit");

            $sheet->setCellValue("F4", "Instrument Serial Number");
            $sheet->getStyle("F4")->getAlignment()->setWrapText(true);
            $sheet->getStyle("F4")->getAlignment()->setIndent(1);

            $sheet->setCellValue("G4", "Make");
            $sheet->setCellValue("H4", "Model");

            $sheet->setCellValue("I4", "Instrument Range");
            $sheet->getStyle("I4")->getAlignment()->setWrapText(true);


            $sheet->setCellValue("J4", "Calibration Frequency");
            $sheet->getStyle("J4")->getAlignment()->setWrapText(true);

            $sheet->setCellValue("K4", "Date of Calibration ");

            $sheet->getStyle("K4")->getAlignment()->setWrapText(true);
            $sheet->setCellValue("L4", "Next Due Date of Calibration");

            $sheet->getStyle("L4")->getAlignment()->setWrapText(true);
            $sheet->setCellValue("M4", "Remark");

            $sheet->getRowDimension(4)->setRowHeight(40);


            $sheet->getStyle("A4:M4")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $row = 5;
            $sr = 1;
            foreach ($inspection_detail as $detail) {
                $sheet->setCellValue("A{$row}", $sr);
                $sheet->setCellValue("B{$row}", $detail['instrument_name']);
                $sheet->setCellValue("C{$row}", $detail['resource_code']);
                $sheet->setCellValue("D{$row}", $detail['exact_location'] ?? '');
                $sheet->setCellValue("E{$row}", getUnitname($detail['unit_id'] ?? ''));
                $sheet->setCellValue("F{$row}", $detail['instrument_serial_no']);
                $sheet->setCellValue("G{$row}", $detail['make'] ?? '');
                $sheet->setCellValue("H{$row}", $detail['model'] ?? '');
                $sheet->setCellValue("I{$row}", $detail['instrument_range'] ?? '');
                $sheet->setCellValue("J{$row}", getFrequencyname($detail['calibration_frequency'] ?? ''));
                $sheet->setCellValue("K{$row}", Displaydateformat($detail['date_of_calibration'] ?? ''));
                $sheet->setCellValue("L{$row}", Displaydateformat($detail['due_date_of_calibration'] ?? ''));
                $sheet->setCellValue("M{$row}", $detail['remarks'] ?? '');


                $sheet->getStyle("A{$row}:M{$row}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $row++;
                $sr++;
            }


            $row++;

            // Download Excel
            $writer = new Xlsx($spreadsheet);
            $fileName = 'Health Instrument calibration .xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            dd($e);
            return back()->with('error', $e->getMessage());
        }
    }

    public function StatusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->health_instrument_calibration->statuschange($id);


            return response()->json(['status' => 'success', 'msg' => 'Your status  has changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }
}
