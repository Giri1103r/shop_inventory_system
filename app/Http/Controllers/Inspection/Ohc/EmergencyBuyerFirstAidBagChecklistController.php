<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use App\Models\Inspection\Master\Frequency;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\Ohc\EmergencyBuyerFirstAidChecklist;
use App\Models\Inspection\Ohc\Master\FirstAidEquipment;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Exception;
use Illuminate\Support\Facades\Validator;
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


class EmergencyBuyerFirstAidBagChecklistController extends Controller
{
    private $unit;
    private $shift;
    private $medicine;
    private $emergency_buyer_first_aid_bag;
    private $signature;
    private $frequency;



    public function __construct()
    {
        $this->unit = new Unit();
        $this->shift = new Shift();
        $this->medicine = new FirstAidEquipment();
        $this->emergency_buyer_first_aid_bag = new EmergencyBuyerFirstAidChecklist();
        $this->signature = new OhcSignature();
        $this->frequency = new Frequency();
    }
    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->emergency_buyer_first_aid_bag->list();
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
                        ->addColumn('date_of_inspection', function ($row) {
                            return Displaydateformat($row->date_of_inspection);
                        })
                        ->addColumn('created_at', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })

                        ->addColumn('action', function ($row) {
                            $btn = '<a href="' . admin_url('ohc/emergency-buyer-first-aid-bag/checklist/view/' . encryptId($row->id)) . '" class="view-icon" title="' . __('common.view') . '">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>';

                            $btn .= '<a href="' . admin_url('ohc/emergency-buyer-first-aid-bag/checklist/generalpdf/' . encryptId($row->id)) . '" style="margin-left: 5px;" title="PDF">
                                        <i class="fas fa-file-pdf" style="color: #e67265;" aria-hidden="true"></i>
                                    </a>';

                            $btn .= '<a href="' . admin_url('ohc/emergency-buyer-first-aid-bag/checklist/generalExcel/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF"> <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i></a>';


                            return $btn;
                        })

                        ->rawColumns(['action', 'created_by', 'date_of_inspection', 'created_at'])
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
        $unit = $this->unit->getunit();
        $shift = $this->shift->getShiftname();
        $data = array(
            'unit' => $unit,
            'shift' => $shift
        );
        return view('inspection.inspection_ohc.emergency_buyer_bag_inspection.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $shift = $this->shift->getShiftname();
            $medicines = $this->medicine->getFirstAidData();
            $frequency = $this->frequency->getFrequency();
            $data = array(
                'unit' => $unit,
                'shift' => $shift,
                'medicines' => $medicines,
                'frequency' => $frequency,

            );

            return view('inspection.inspection_ohc.emergency_buyer_bag_inspection.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {
            $rules = [
                'date_of_inspection' => 'required',
                'location_first_aid_bag' => 'required',
                'shift' => 'required',
                'next_due_date' => 'required',
                'unit_id' => 'required',
                'frequency_id' => 'required',
                'medicine_id.*' => 'required',
                'freeze_quantity.*' => 'required',
                'available_quantity.*' => 'required',
                'expired_date.*' => 'required',
                'remarks.*' => 'required',
                'remark_by' => 'required',
            ];

            $messages = [
                'date_of_inspection.required' => 'Date of inspection is required.',
                'location_first_aid_bag.required' => 'Location of the first aid bag is required.',
                'shift.required' => 'Shift selection is required.',
                'next_due_date.required' => 'Next due date is required.',
                'unit_id.required' => 'Unit selection is required.',
                'frequency_id.required' => 'Frequency is required.',
                'medicine_id.*.required' => 'Medicine ID is required.',
                'freeze_quantity.*.required' => 'Freeze quantity is required.',
                'available_quantity.*.required' => 'Available quantity is required.',
                'expired_date.*.required' => 'Expired date is required.',
                'remarks.*.required' => 'Remarks are required.',
                'remark_by.required' => 'Remark by is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $emergency_buyer_first_aid_bag = $this->emergency_buyer_first_aid_bag->store();
                $emergency_buyer_first_aid_bag_id = $emergency_buyer_first_aid_bag->id;
                $inspection_type = OHC_TYPE_EMERGENCY_BUYER_FIRST_AID_BAG_CHECKLIST;
                $inspection_details = $this->emergency_buyer_first_aid_bag->selectOne($emergency_buyer_first_aid_bag_id);
                $files = $this->signature->requestorsignatureUpload($inspection_type, $inspection_details->id);

                Session::flash('success', 'Your data has been created successfully!');
                return redirect(admin_url('ohc/emergency-buyer-first-aid-bag/checklist/list'));
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
            $inspection_details = $this->emergency_buyer_first_aid_bag->selectOne($id);
            $inspection_type = OHC_TYPE_EMERGENCY_BUYER_FIRST_AID_BAG_CHECKLIST;
            $inspection_data = json_decode($inspection_details->inspection_data, true);
            $inspection_file = GetOHCSignature($inspection_details->created_by, $inspection_details->id, $inspection_type);

            $data = array(
                'inspection_details' => $inspection_details,
                'inspection_file' => $inspection_file,
                'inspection_data' => $inspection_data,
            );

            return view('inspection.inspection_ohc.emergency_buyer_bag_inspection.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/monthly-medicine-store/inspection/list'));
        }
    }

    public function generalpdf(Request $request)
    {
        try {

            if (Auth::check()) {
                $id = decryptId($request->id);
                $inspection_details = $this->emergency_buyer_first_aid_bag->selectOne($id);
                $inspection_type = OHC_TYPE_EMERGENCY_BUYER_FIRST_AID_BAG_CHECKLIST;
                $inspection_data = json_decode($inspection_details->inspection_data, true);
                $inspection_created_by = GetOHCSignature($inspection_details->created_by, $inspection_details->id, $inspection_type);

                $data = array(
                    'inspection_details' => $inspection_details,
                    'inspection_created_by' => $inspection_created_by,
                    'inspection_data' => $inspection_data,
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

            $html = view('inspection.inspection_ohc.emergency_buyer_bag_inspection.generalpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "Emergency Buyer Bag Inspection Details.pdf";
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
            $sheet->getColumnDimension('B')->setWidth(40);  
            $sheet->getColumnDimension('C')->setWidth(15);  
            $sheet->getColumnDimension('D')->setWidth(20);  
            $sheet->getColumnDimension('E')->setWidth(30);  
            $sheet->getColumnDimension('F')->setWidth(25);  
    
            $inspection_detail = $this->emergency_buyer_first_aid_bag->selectOne($id);
            $inspection_type = OHC_TYPE_EMERGENCY_BUYER_FIRST_AID_BAG_CHECKLIST;
            $inspection_data = json_decode($inspection_detail->inspection_data, true);
            $inspection_created_by = GetOHCSignature($inspection_detail->created_by, $inspection_detail->id, $inspection_type);
    
            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }
    
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
    
            $sheet->mergeCells("A4:B4");
            $sheet->setCellValue("A4", "Date of Inspection:- " . Displaydateformat($inspection_detail->date_of_inspection));
    
            $sheet->mergeCells("C4:D4");
            $sheet->setCellValue("C4", "Location First Aid Bag: " . $inspection_detail->location_first_aid_bag);
    
            $sheet->mergeCells("E4:F4");
            $sheet->setCellValue("E4", "Shift: " . getShift($inspection_detail->shift_id));
    
            $sheet->mergeCells("A5:B5");
            $sheet->setCellValue("A5", "Next Due On:- " . Displaydateformat($inspection_detail->due_date));
    
            $sheet->mergeCells("C5:D5");
            $sheet->setCellValue("C5", "Unit:- " . getUnitname($inspection_detail->unit_id));
    
            $sheet->mergeCells("E5:F5");
            $sheet->setCellValue("E5", "Frequency:- " . getFrequencyname($inspection_detail->frequency_id));
    
            $sheet->getStyle("A4:F4")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
    
            $sheet->getStyle("A5:F5")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
    
            $sheet->setCellValue("A6", "SERIAL NO");
            $sheet->setCellValue("B6", "NAME OF THE MEDICINE");
            $sheet->setCellValue("C6", "FREEZE QUANTITY");
            $sheet->setCellValue("D6", "AVAILABLE QUANTITY");
            $sheet->setCellValue("E6", "EXPIRY DATE");
            $sheet->setCellValue("F6", "REMARKS");
    
            $sheet->getStyle("A6:F6")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
    
            $row = 7;
            $sr = 1;
            foreach ($inspection_data as $index => $detail) {
                $sheet->setCellValue("A$row", $sr); 
                $sheet->setCellValue("B$row", getMedicinename($detail['medicine_id']));
                $sheet->setCellValue("C$row", $detail['freeze_quantity'] ?? '');
                $sheet->setCellValue("D$row", $detail['available_quantity'] ?? '');
                $sheet->setCellValue("E$row", Displaydateformat($detail['expired_date']));
                $sheet->setCellValue("F$row", $detail['remarks'] ?? '');
    
                $sheet->getStyle("A$row:F$row")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $row++;
                $sr++;
            }
    
            $sheet->mergeCells("A{$row}:F{$row}");
            $sheet->setCellValue("A{$row}", "PPE'S For Visitors:- 05 Air Plugs, 05 Pairs Cotton Gloves, 02 Pairs Rubber Gloves, 05 Mask, 04 Spectacles.");
            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $row++;
    
            $sheet->mergeCells("A{$row}:F{$row}");
            $sheet->setCellValue("A{$row}", "Remark By:- " . $inspection_detail->remark_by);
            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $row++;
    
            $signatureRow = $row;
            $sheet->getRowDimension($signatureRow)->setRowHeight(80);
            $sheet->mergeCells("A{$signatureRow}:F{$signatureRow}");
            $sheet->getStyle("A{$signatureRow}:F{$signatureRow}")->applyFromArray([
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
    
            $writer = new Xlsx($spreadsheet);
            $fileName = 'Emergency Buyer First Aid Bag.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);
    
            return response()->download($filePath)->deleteFileAfterSend(true);
    
        } catch (\Exception $e) {
            dd($e);
            return back()->with('error', $e->getMessage());
        }
    }
    

    public function ExportExcel()
    {
        try {
            $allData = $this->emergency_buyer_first_aid_bag->exportdata();
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
    
           
            $sheet->getColumnDimension('A')->setWidth(12);  
            $sheet->getColumnDimension('B')->setWidth(35);  
            $sheet->getColumnDimension('C')->setWidth(15);  
            $sheet->getColumnDimension('D')->setWidth(18);  
            $sheet->getColumnDimension('E')->setWidth(28);  
            $sheet->getColumnDimension('F')->setWidth(25);  
    
            $row = 1;
    
            foreach ($allData as $inspection_detail) {
                $startRow = $row;
    
                $inspection_type = OHC_TYPE_EMERGENCY_BUYER_FIRST_AID_BAG_CHECKLIST;
                $inspection_data = json_decode($inspection_detail->inspection_data, true);
                $inspection_created_by = GetOHCSignature($inspection_detail->created_by, $inspection_detail->id, $inspection_type);
    
                // Add logo
                $logoPath = public_path('assets/images/logo-dark.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo');
                    $drawing->setDescription('Company Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates("A{$row}");
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);
                    $drawing->setHeight(60);
                    $drawing->setWorksheet($sheet);
                }
    
                $sheet->mergeCells("A{$row}:B" . ($row + 2));
                $sheet->getStyle("A{$row}:B" . ($row + 2))->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]
                ]);
    
                $sheet->mergeCells("C{$row}:F" . ($row + 2));
                $sheet->setCellValue("C{$row}", "BUYER'S FIRST AID BAG INSPECTION CHECKLIST PN INTERNATIONAL PNT. LTD.");
                $sheet->getStyle("C{$row}:F" . ($row + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
    
                $row += 3;
    
                $sheet->mergeCells("A{$row}:B{$row}");
                $sheet->setCellValue("A{$row}", "Date of Inspection:- " . Displaydateformat($inspection_detail->date_of_inspection));
    
                $sheet->mergeCells("C{$row}:D{$row}");
                $sheet->setCellValue("C{$row}", "Location First Aid Bag: " . $inspection_detail->location_first_aid_bag);
    
                $sheet->mergeCells("E{$row}:F{$row}");
                $sheet->setCellValue("E{$row}", "Shift: " . getShift($inspection_detail->shift_id));
    
                $row++;
    
                $sheet->mergeCells("A{$row}:B{$row}");
                $sheet->setCellValue("A{$row}", "Next Due On:- " . Displaydateformat($inspection_detail->due_date));
    
                $sheet->mergeCells("C{$row}:D{$row}");
                $sheet->setCellValue("C{$row}", "Unit:- " . getUnitname($inspection_detail->unit_id));
    
                $sheet->mergeCells("E{$row}:F{$row}");
                $sheet->setCellValue("E{$row}", "Frequency:- " . getFrequencyname($inspection_detail->frequency_id));
    
                $sheet->getStyle("A" . ($row - 1) . ":F{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
    
                $row++;
    
                // Header row
                $sheet->setCellValue("A6", "SERIAL NO");
                $sheet->setCellValue("B6", "NAME OF THE MEDICINE");
                $sheet->setCellValue("C6", "FREEZE QUANTITY");
                $sheet->setCellValue("D6", "AVAILABLE QUANTITY");
                $sheet->setCellValue("E6", "EXPIRY DATE");
                $sheet->setCellValue("F6", "REMARKS");
    
                $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
    
                $row++;
                $sr =  1;
                foreach ($inspection_data as $index => $detail) {
                    $sheet->setCellValue("A$row", $sr); 
                    $sheet->setCellValue("B$row", getMedicinename($detail['medicine_id']));
                    $sheet->setCellValue("C$row", $detail['freeze_quantity'] ?? '');
                    $sheet->setCellValue("D$row", $detail['available_quantity'] ?? '');
                    $sheet->setCellValue("E$row", Displaydateformat($detail['expired_date']));
                    $sheet->setCellValue("F$row", $detail['remarks'] ?? '');
    
                    $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $row++;
                    $sr++;
                }

                $sheet->mergeCells("A{$row}:F{$row}");
                $sheet->setCellValue("A{$row}", "PPE'S For Visitors:- 05 Air Plugs, 05 Pairs Cotton Gloves, 02 Pairs Rubber Gloves, 05 Mask, 04 Spectacles.");
                $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $row++;

                $sheet->mergeCells("A{$row}:F{$row}");
                $sheet->setCellValue("A{$row}", "Remark By:- " . $inspection_detail->remark_by);
                $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $row++;
    
                // Signature Row
                $signatureRow = $row;
                $sheet->getRowDimension($signatureRow)->setRowHeight(80);
                $sheet->mergeCells("A{$signatureRow}:F{$signatureRow}");
                $sheet->getStyle("A{$signatureRow}:F{$signatureRow}")->applyFromArray([
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
    
                // Draw a border around the whole block
                $endRow = $signatureRow;
                $sheet->getStyle("A{$startRow}:F{$endRow}")->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN],
                    ]
                ]);
    
                $row = $signatureRow + 6; // spacing after each section
            }
    
            $writer = new Xlsx($spreadsheet);
            $fileName = 'Monthly Medicine Store Inspection.xlsx';
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

            $allData = $this->emergency_buyer_first_aid_bag->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }


            $data = array(
                'content' => $allData,
                'pagetitle' => "Emergency Buyer Bag Inspection",
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

            $view = view('inspection.inspection_ohc.emergency_buyer_bag_inspection.pdf', $data);
            $html = $view->render();


            $mpdf->WriteHTML($html);

            $filename = "Emergency Buyer Bag Inspection.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }
}
