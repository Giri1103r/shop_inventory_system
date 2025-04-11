<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\Ohc\Master\FirstAidEquipment;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Models\Inspection\Ohc\WeeklyFirstAidBox;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use App\Models\OhcManagement\Master\CertifiedFirstAider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
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

class WeeklyFirstAidBoxController extends Controller
{

    private $unit;
    private $certified_First_aid;
    private $location;
    private $shift;
    private $medicine;
    private $weekly_first_aid;
    private $signature;
    private $document_reference;
    private $user;


    public function __construct()
    {
        $this->unit = new Unit();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->certified_First_aid = new CertifiedFirstAider();
        $this->medicine = new FirstAidEquipment();
        $this->weekly_first_aid = new WeeklyFirstAidBox();
        $this->signature = new OhcSignature();
        $this->document_reference = new InspectionStaticDocno();
        $this->user = new User();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->weekly_first_aid->list();
                    // dd($data);
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
                            $btn = '<a href="' . admin_url('ohc/first-aid-box/weekly-inspection/view/' . encryptId($row->id)) . '" class="view-icon" title="' . __('common.view') . '">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>';

                            $btn .= '<a href="' . admin_url('ohc/first-aid-box/weekly-inspection/generalpdf/' . encryptId($row->id)) . '" style="margin-left: 5px;" title="PDF">
                                        <i class="fas fa-file-pdf" style="color: #e67265;" aria-hidden="true"></i>
                                    </a>';
                            $btn .= '<a href="' . admin_url('ohc/first-aid-box/weekly-inspection/generalExcel/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF"> <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i></a>';

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
        $location = $this->location->getLocationname();
        $unit = $this->unit->getunit();
        $shift = $this->shift->getShiftname();
        $First_aid = $this->certified_First_aid->getFirsaid();

        $data = array(
            'location' => $location,
            'unit' => $unit,
            'shift' => $shift,
            'First_aid' => $First_aid,

        );

        return view('inspection.inspection_ohc.weekly_first_aid.list', $data);
    }


    public function Add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $shift = $this->shift->getShiftname();
            $First_aid = $this->certified_First_aid->getFirsaid();
            $medicines = $this->medicine->getFirstAidData();
            $location = $this->location->getLocationname();
            $document_no = $this->document_reference->selectUsingName('WeeklyFirstAidBoxInspectionChecklist');

            $data = array(
                'unit' => $unit,
                'shift' => $shift,
                'First_aid' => $First_aid,
                'location' => $location,
                'medicines' => $medicines,
                'document_no' => $document_no,
            );

            return view('inspection.inspection_ohc.weekly_first_aid.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }


    public function Store(Request $request)
    {
        // dd($request->all());
        try {

            try {

                $weekly_first_aid = $this->weekly_first_aid->store();
                $weekly_first_aid_id = $weekly_first_aid->id;
                $inspection_type = OHC_TYPE_WEEEKLY_FIRST_AID_MEDICINE_STORE;
                $inspection_details = $this->weekly_first_aid->selectOne($weekly_first_aid_id);
                $files = $this->signature->requestorsignatureUpload($inspection_type, $inspection_details->id);


                Session::flash('success', 'Your data has been created successfully!');
                return redirect(admin_url('ohc/first-aid-box/weekly-inspection/list'));
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function View(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_details = $this->weekly_first_aid->selectOne($id);
            $inspection_type = OHC_TYPE_WEEEKLY_FIRST_AID_MEDICINE_STORE;
            $inspection_data = json_decode($inspection_details->inspection_data, true);
            $inspection_file = GetOHCSignature($inspection_details->created_by, $inspection_details->id, $inspection_type);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);



            $data = array(
                'inspection_details' => $inspection_details,
                'inspection_file' => $inspection_file,
                'inspection_data' => $inspection_data,
                'document_no' => $document_no,

            );

            return view('inspection.inspection_ohc.weekly_first_aid.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/monthly-medicine-store/inspection/list'));
        }
    }

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $id = decryptId($request->id);
                $inspection_details = $this->weekly_first_aid->selectOne($id);
                $inspection_type = OHC_TYPE_WEEEKLY_FIRST_AID_MEDICINE_STORE;
                $inspection_created_by = GetOHCSignature($inspection_details->created_by, $inspection_details->id, $inspection_type);
                $inspection_data = json_decode($inspection_details->inspection_data, true);
                $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);


                $data = array(
                    'inspection_details' => $inspection_details,
                    'inspection_created_by' => $inspection_created_by,
                    'inspection_data' => $inspection_data,
                    'document_no' => $document_no,

                );
                // dd($data);
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

            $html = view('inspection.inspection_ohc.weekly_first_aid.generalpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "Weekly First Aid Details.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
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
            $sheet->getColumnDimension('B')->setWidth(12);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(15);
            $sheet->getColumnDimension('H')->setWidth(15);

            $inspection_detail = $this->weekly_first_aid->selectOne($id);
            $inspection_type = OHC_TYPE_WEEEKLY_FIRST_AID_MEDICINE_STORE;
            $inspection_data = json_decode($inspection_detail->inspection_data, true);
            $inspection_created_by = GetOHCSignature($inspection_detail->created_by, $inspection_detail->id, $inspection_type);
            $document_no = $this->document_reference->selectOne($inspection_detail->document_reference_id);
            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            // Add logo
            $logoPath = public_path('assets/images/logo-dark.png');
            if (file_exists($logoPath)) {
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Company Logo');
                $drawing->setPath($logoPath);
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
            }

            // Title & Header
            $sheet->mergeCells('A1:B3');
            $sheet->getStyle('A1:B3')->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);

            $sheet->mergeCells("C1:F3");
            $sheet->setCellValue("C1", "BUYER'S FIRST AID BAG INSPECTION CHECKLIST PN INTERNATIONAL PNT. LTD.");
            $sheet->getStyle("C1:F3")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFFFFFFF'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);

            $headerLabels = [
                'G1' => 'Doc. No.',
                'G2' => 'Issue Dt.',
                'G3' => 'Rev. & Dt.',
            ];

            foreach ($headerLabels as $cell => $label) {
                $sheet->setCellValue($cell, $label);

                $valueCell = 'H' . substr($cell, 1);

                $sheet->getStyle("$cell:$valueCell")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
            }

            $sheet->setCellValue("H1", $document_no->doc_no);
            $sheet->setCellValue("H2", Displaydateformat($document_no->issue_date));
            $sheet->setCellValue("H3", $document_no->rev_dt);


            $sheet->mergeCells("A4:C4")->setCellValue("A4", "Date of Inspection:- " . Displaydateformat($inspection_detail->date_of_inspection));
            $sheet->mergeCells("D4:E4")->setCellValue("D4", "Location First Aid Bag: " . $inspection_detail->location_first_aid_bag);
            $sheet->mergeCells("F4:H4")->setCellValue("F4", "Shift: " . getShift($inspection_detail->shift));

            $sheet->mergeCells("A5:C5")->setCellValue("A5", "Location:- " . getLocationname($inspection_detail->location));
            $sheet->mergeCells("D5:E5")->setCellValue("D5", "Unit:- " . getUnitname($inspection_detail->unit));
            $sheet->mergeCells("F5:H5")->setCellValue("F5", "Name Of First-Aider:- " . getFirstAider($inspection_detail->first_aider));

            $sheet->getStyle("A4:H5")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->setCellValue("A6", "SERIAL NO");
            $sheet->mergeCells("B6:C6")->setCellValue("B6", "NAME OF THE MATERIAL");
            $sheet->setCellValue("D6", "FREEZE QUANTITY");
            $sheet->setCellValue("E6", "AVAILABLE QUANTITY");
            $sheet->setCellValue("F6", "MATERIAL EXPIRY");
            $sheet->mergeCells("G6:H6")->setCellValue("G6", "REMARK");

            $sheet->getStyle("A6:H6")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $row = 7;
            $sr = 1;
            foreach ($inspection_data as $detail) {
                $sheet->setCellValue("A$row", $sr);
                $sheet->mergeCells("B$row:C$row")->setCellValue("B$row", getMedicinename($detail['medicine_id']));
                $sheet->setCellValue("D$row", $detail['freeze_quantity'] ?? '');
                $sheet->setCellValue("E$row", $detail['available_quantity'] ?? '');
                $sheet->setCellValue("F$row", Displaydateformat($detail['expired_date']));
                $sheet->mergeCells("G$row:H$row")->setCellValue("G$row", $detail['remarks'] ?? '');

                $sheet->getStyle("A$row:H$row")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $row++;
                $sr++;
            }

            $sheet->mergeCells("A{$row}:H{$row}");
            $sheet->setCellValue("A{$row}", "Remark By:- " . $inspection_detail->remark_by);
            $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $row++;

            // Signature
            $signatureRow = $row;
            $sheet->getRowDimension($signatureRow)->setRowHeight(80);
            $sheet->mergeCells("A{$signatureRow}:H{$signatureRow}");
            $sheet->getStyle("A{$signatureRow}:H{$signatureRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            if (file_exists($inspection_created_by)) {
                $drawing = new Drawing();
                $drawing->setName('Inspection and checked By');
                $drawing->setPath($inspection_created_by);
                $drawing->setCoordinates("C{$signatureRow}");
                $drawing->setOffsetX(50);
                $drawing->setOffsetY(10);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
            }

            $richText = new RichText();
            $richText->createTextRun("Inspected and checked By: " . getUsername($inspection_detail->created_by))->getFont()->setBold(true);
            $sheet->getCell("A{$signatureRow}")->setValue($richText);

            // Download Excel
            $writer = new Xlsx($spreadsheet);
            $fileName = 'Weekly First Aid Box.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            dd($e);
            return back()->with('error', $e->getMessage());
        }
    }


    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->weekly_first_aid->exportdata();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $columns = ['A' => 12, 'B' => 12, 'C' => 20, 'D' => 20, 'E' => 20, 'F' => 20, 'G' => 15, 'H' => 15];
            foreach ($columns as $col => $width) {
                $sheet->getColumnDimension($col)->setWidth($width);
            }

            $row = 1;

            foreach ($allData as $inspection_detail) {
                $startRow = $row;

                $inspection_type = OHC_TYPE_WEEEKLY_FIRST_AID_MEDICINE_STORE;
                $inspection_data = json_decode($inspection_detail->inspection_data, true);
                $inspection_created_by = GetOHCSignature($inspection_detail->created_by, $inspection_detail->id, $inspection_type);
                $document_no = $this->document_reference->selectOne($inspection_detail->document_reference_id);

                // Logo
                $logoPath = public_path('assets/images/logo-dark.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates('A' . $row);
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);
                    $drawing->setHeight(60);
                    $drawing->setWorksheet($sheet);
                }

                // Title and Headers
                $sheet->mergeCells("A{$row}:B" . ($row + 2));
                $sheet->mergeCells("C{$row}:F" . ($row + 2));
                $sheet->setCellValue("C{$row}", "BUYER'S FIRST AID BAG INSPECTION CHECKLIST PN INTERNATIONAL PNT. LTD.");
                $sheet->getStyle("C{$row}:F" . ($row + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Document details
                $headerLabels = [
                    'G1' => 'Doc. No.',
                    'G2' => 'Issue Dt.',
                    'G3' => 'Rev. & Dt.'
                ];

                $values = [
                    'H1' => $document_no->doc_no,
                    'H2' => Displaydateformat($document_no->issue_date),
                    'H3' => $document_no->rev_dt
                ];

                foreach (['1', '2', '3'] as $i) {
                    $labelCell = "G{$i}";
                    $valueCell = "H{$i}";
                    $sheet->setCellValue($labelCell, $headerLabels["G{$i}"] ?? '');
                    $sheet->setCellValue($valueCell, $values[$valueCell] ?? '');
                    $sheet->getStyle("{$labelCell}:{$valueCell}")->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                }

                $row += 3;

                // Inspection details
                $sheet->mergeCells("A{$row}:C{$row}")->setCellValue("A{$row}", "Date of Inspection:- " . Displaydateformat($inspection_detail->date_of_inspection));
                $sheet->mergeCells("D{$row}:E{$row}")->setCellValue("D{$row}", "Location First Aid Bag: " . $inspection_detail->location_first_aid_bag);
                $sheet->mergeCells("F{$row}:H{$row}")->setCellValue("F{$row}", "Shift: " . getShift($inspection_detail->shift));
                $row++;

                $sheet->mergeCells("A{$row}:C{$row}")->setCellValue("A{$row}", "Location:- " . getLocationname($inspection_detail->location));
                $sheet->mergeCells("D{$row}:E{$row}")->setCellValue("D{$row}", "Unit:- " . getUnitname($inspection_detail->unit));
                $sheet->mergeCells("F{$row}:H{$row}")->setCellValue("F{$row}", "Name Of First-Aider:- " . getFirstAider($inspection_detail->first_aider));

                $sheet->getStyle("A" . ($row - 1) . ":H{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $row++;

                // Table Header
                $sheet->setCellValue("A{$row}", "SERIAL NO");
                $sheet->mergeCells("B{$row}:C{$row}")->setCellValue("B{$row}", "NAME OF THE MATERIAL");
                $sheet->setCellValue("D{$row}", "FREEZE QUANTITY");
                $sheet->setCellValue("E{$row}", "AVAILABLE QUANTITY");
                $sheet->setCellValue("F{$row}", "MATERIAL EXPIRY");
                $sheet->mergeCells("G{$row}:H{$row}")->setCellValue("G{$row}", "REMARK");

                $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $row++;

                // Data Rows
                $sr = 1;
                foreach ($inspection_data as $detail) {
                    $sheet->setCellValue("A{$row}", $sr++);
                    $sheet->mergeCells("B{$row}:C{$row}")->setCellValue("B{$row}", getMedicinename($detail['medicine_id']));
                    $sheet->setCellValue("D{$row}", $detail['freeze_quantity'] ?? '');
                    $sheet->setCellValue("E{$row}", $detail['available_quantity'] ?? '');
                    $sheet->setCellValue("F{$row}", Displaydateformat($detail['expired_date']));
                    $sheet->mergeCells("G{$row}:H{$row}")->setCellValue("G{$row}", $detail['remarks'] ?? '');

                    $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $row++;
                }

                // Remark Row
                $sheet->mergeCells("A{$row}:H{$row}");
                $sheet->setCellValue("A{$row}", "Remark By:- " . $inspection_detail->remark_by);
                $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $row++;

                // Signature
                $sheet->getRowDimension($row)->setRowHeight(80);
                $sheet->mergeCells("A{$row}:H{$row}");
                $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                if (file_exists($inspection_created_by)) {
                    $drawing = new Drawing();
                    $drawing->setName('Signature');
                    $drawing->setPath($inspection_created_by);
                    $drawing->setCoordinates("C{$row}");
                    $drawing->setOffsetX(50);
                    $drawing->setOffsetY(10);
                    $drawing->setWidth(70);
                    $drawing->setHeight(70);
                    $drawing->setWorksheet($sheet);
                }

                $richText = new RichText();
                $richText->createTextRun("Inspected and checked By: " . getUsername($inspection_detail->created_by))->getFont()->setBold(true);
                $sheet->getCell("A{$row}")->setValue($richText);

                $row += 6; // spacing before next inspection
            }

            // Final output
            $writer = new Xlsx($spreadsheet);
            $fileName = 'Weekly_First_Aid_Inspection.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function ExportPdf(Request $request)
    {

        try {

            ini_set("pcre.backtrack_limit", "5000000");

            $allData = $this->weekly_first_aid->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }

            $data = array(
                'content' => $allData,
                'pagetitle' => "First Aid Name",
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

            $view = view('inspection.inspection_ohc.weekly_first_aid.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Weekly First Aid.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->weekly_first_aid->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => __('First Aid  Status is changed')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Something went wrong, Please try after sometimes!'], 406);
        }
    }
}
