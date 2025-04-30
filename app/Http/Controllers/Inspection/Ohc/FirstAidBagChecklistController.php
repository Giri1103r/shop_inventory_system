<?php

namespace App\Http\Controllers\Inspection\Ohc;

use Exception;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Master\Location;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Inspection\Master\Shift;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\SimpleExcel\SimpleExcelWriter;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\Inspection\Master\Frequency;
use App\Models\Inspection\Ohc\OhcSignature;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\ohc\FirstAidBagChecklist;
use App\Models\Inspection\Ohc\Master\FirstAidEquipment;

class FirstAidBagChecklistController extends Controller
{
    private $medicine_checklist;
    private $medicine;
    private $signature;
    private $document_reference;
    private $location;
    private $unit;
    private $frequency;
    private $shift;


    public function __construct()
    {
        $this->medicine_checklist = new FirstAidBagChecklist();
        $this->medicine = new FirstAidEquipment();
        $this->signature = new OhcSignature();
        $this->document_reference = new InspectionStaticDocno();
        $this->location  = new Location();
        $this->unit = new Unit();
        $this->frequency = new Frequency();
        $this->shift = new Shift();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->medicine_checklist->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->inspection_id) . "' data-type='1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->inspection_id) . "' data-type='0'>In-Active</span>";
                            }
                            return $text;
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('inspection_date', function ($row) {
                            return Displaydateformat($row->inspection_date);
                        })
                        ->addColumn('next_due', function ($row) {
                            return Displaydateformat($row->next_due);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })

                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('ohc/emergency-floor-first-aid-bag/checklist/view/' . encryptId($row->inspection_id)) . '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';

                            if ($row->inspection_status == OBSERVATION_PENDING &&  isAdmin()) {
                                $btn .= '<a href="' . admin_url('ohc/emergency-floor-first-aid-bag/checklist/approval/' . encryptId($row->inspection_id)) . '" class="me-1" title="Action"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }

                            $btn .= '<a href="' . admin_url('ohc/emergency-floor-first-aid-bag/checklist/exportViewpdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';

                            $btn .= '<a href="' . admin_url('ohc/emergency-floor-first-aid-bag/checklist/generalexcel/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                     </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'inspection_date', 'next_due'])
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

        $location = $this->location->getLocationName();
        $unit = $this->unit->getUnit();
        $frequency = $this->frequency->getFrequency();
        $shift = $this->shift->getShiftname();

        $data = array(
            'locations' => $location,
            'units' => $unit,
            'frequency' => $frequency,
            'shifts' => $shift,
        );

        return view('inspection.inspection_ohc.first_aid_bag_inspection.list', $data);
    }

    public function Add(Request $request)
    {
        try {

            $medicines = $this->medicine->getFirstAidData();
            $document_no = $this->document_reference->selectUsingName('EmergencyFloorFirstAidBagChecklist');
            $location = $this->location->getLocationName();
            $unit = $this->unit->getUnit();
            $frequency = $this->frequency->getFrequency();
            $shift = $this->shift->getShiftname();

            $data = array(
                'medicines' => $medicines,
                'document_no' => $document_no,
                'locations' => $location,
                'units' => $unit,
                'frequency' => $frequency,
                'shifts' => $shift,

            );
            return view('inspection.inspection_ohc.first_aid_bag_inspection.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/emergency-floor-first-aid-bag/checklist/list'));
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'inspection_date' => 'required',
                'frequency_id' => 'required',
                'location_id' => 'required',
                'unit_id' => 'required',
                'next_due' => 'required',
                'available_quantity.*' => 'required',
                'expired_date.*' => 'required',
                'emp_id.*' => 'required',
                'remarks.*' => 'required'
            ];

            $messages = [
                'inspection_date.required' => 'Inspection Date is required.',
                'frequency_id.required' => 'Frequency is required.',
                'location_id.required' => 'Location is required.',
                'unit_id.required' => 'Unit is required.',
                'next_due.required' => 'Next Due Date is required.',
                'available_quantity.*.required' => 'Available Quantity is required',
                'expired_date.*.required' => 'Expired Date is required',
                'remarks.*' => 'Remarks is required',
                'emp_id.*' => 'Employee is required',
                'signature_upload' => 'Signature is required.',
            ];


            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $store = $this->medicine_checklist->store();
            $inspection_type = FIRST_AID_BAG_INSPECTION_CHECKLIST;
            $inspection_details = $this->medicine_checklist->selectOne($store->id);
            $files = $this->signature->requestorsignatureUpload($inspection_type, $inspection_details->id);

            Session::flash('success', 'Your data has been added successfully');
            return redirect(admin_url('ohc/emergency-floor-first-aid-bag/checklist/list'));
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/emergency-floor-first-aid-bag/checklist/list'));
        }
    }

    public function View(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_details = $this->medicine_checklist->selectOne($id);
            $inspection_type = FIRST_AID_BAG_INSPECTION_CHECKLIST;
            $signature = GetOHCSignature($inspection_details->created_by, $inspection_details->id, $inspection_type);
            $inspection_data = json_decode($inspection_details->inspection_data, true);

            $data = array(
                'inspection_details' => $inspection_details,
                'signature' => $signature,
                'inspection_data' => $inspection_data,
            );


            return view('inspection.inspection_ohc.first_aid_bag_inspection.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/emergency-floor-first-aid-bag/checklist/list'));
        }
    }


    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $inspection_detail = $this->medicine_checklist->selectOne($id);
            $inspection_type = FIRST_AID_BAG_INSPECTION_CHECKLIST;
            $inspection_data = json_decode($inspection_detail->inspection_data, true);
            $inspection_updated_by = GetOHCSignature($inspection_detail->updated_by, $inspection_detail->id, $inspection_type);
            $inspection_created_by = GetOHCSignature($inspection_detail->created_by, $inspection_detail->id, $inspection_type);

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $sheet->mergeCells("A1:F3");
            $sheet->getStyle("A1:F3")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $logoLeftPath = public_path('assets/images/logo-dark.png');
            if (file_exists($logoLeftPath)) {
                $drawing = new Drawing();
                $drawing->setName('Left Logo');
                $drawing->setPath($logoLeftPath);
                $drawing->setCoordinates('B1');
                $drawing->setOffsetX(100);
                $drawing->setOffsetY(15);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
            }

            $sheet->mergeCells("G1:M3");
            $sheet->setCellValue("G1", "FLOOR FIRST AID BAG INSPECTION CHECKLIST PN INTERNATIONAL PNT. LTD.");
            $sheet->getStyle("G1")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $sheet->mergeCells("N1:S3");
            $sheet->getStyle("N1:S3")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $logoRightPath = public_path('assets/images/plus-image.webp');
            if (file_exists($logoRightPath)) {
                $drawing = new Drawing();
                $drawing->setName('Right Logo');
                $drawing->setPath($logoRightPath);
                $drawing->setCoordinates('O1');
                $drawing->setOffsetX(100);
                $drawing->setOffsetY(15);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
            }

            $sheet->mergeCells("A4:F4");
            $richText1 = new RichText();
            $richText1->createTextRun('DATE OF INSPECTION :- ')->getFont()->setBold(true);
            $richText1->createText(Displaydateformat($inspection_detail->inspection_date));
            $sheet->getCell("A4")->setValue($richText1);

            $sheet->mergeCells("G4:M4");
            $richText2 = new RichText();
            $richText2->createTextRun('LOCATION :- ')->getFont()->setBold(true);
            $richText2->createText(getLocationname($inspection_detail->location));
            $sheet->getCell("G4")->setValue($richText2);

            $sheet->mergeCells("N4:S4");
            $richText3 = new RichText();
            $richText3->createTextRun('SHIFT :- ')->getFont()->setBold(true);
            $richText3->createText(getShiftname($inspection_detail->shift_id));
            $sheet->getCell("N4")->setValue($richText3);

            $sheet->mergeCells("A5:F5");
            $richText4 = new RichText();
            $richText4->createTextRun('NEXT DUE :- ')->getFont()->setBold(true);
            $richText4->createText(Displaydateformat($inspection_detail->next_due));
            $sheet->getCell("A5")->setValue($richText4);

            $sheet->mergeCells("G5:M5");
            $richText5 = new RichText();
            $richText5->createTextRun('UNIT :- ')->getFont()->setBold(true);
            $richText5->createText(getUnitname($inspection_detail->unit));
            $sheet->getCell("G5")->setValue($richText5);

            $sheet->mergeCells("N5:S5");
            $richText6 = new RichText();
            $richText6->createTextRun('FREQUENCY :- ')->getFont()->setBold(true);
            $richText6->createText(getFrequencyname($inspection_detail->frequency));
            $sheet->getCell("N5")->setValue($richText6);

            $sheet->getStyle("A4:S4")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle("A5:S5")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells("A6:B6")->setCellValue("A6", "SERIAL NO");
            $sheet->mergeCells("C6:E6")->setCellValue("C6", "NAME OF THE MEDICINE");
            $sheet->mergeCells("F6:H6")->setCellValue("F6", "FREEZE QUANTITY");
            $sheet->mergeCells("I6:J6")->setCellValue("I6", "QUANTITY");
            $sheet->mergeCells("K6:L6")->setCellValue("K6", "EXPIRY DATE");
            $sheet->mergeCells("M6:O6")->setCellValue("M6", "INSPECTED BY");
            $sheet->mergeCells("P6:S6")->setCellValue("P6", "REMARKS");

            $sheet->getStyle("A6:S6")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $row = 7;
            foreach ($inspection_data as $index => $detail) {
                $sheet->mergeCells("A$row:B$row")->setCellValue("A$row", $index);
                $sheet->mergeCells("C$row:E$row")->setCellValue("C$row", getMedicinename($detail['medicine_id']));
                $sheet->mergeCells("F$row:H$row")->setCellValue("F$row", $detail['freeze_quantity'] ?? '');
                $sheet->mergeCells("I$row:J$row")->setCellValue("I$row", $detail['available_quantity'] ?? '');
                $sheet->mergeCells("K$row:L$row")->setCellValue("K$row", Displaydateformat($detail['expired_date']));
                $sheet->mergeCells("M$row:O$row")->setCellValue("M$row", getUsername($detail['emp_id']) ?? '');
                $sheet->mergeCells("P$row:S$row")->setCellValue("P$row", $detail['remarks'] ?? '');

                $sheet->getStyle("A$row:S$row")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $row++;
            }

            $signatureRow = $row;
            $sheet->getRowDimension($signatureRow)->setRowHeight(80);
            $sheet->mergeCells("A{$signatureRow}:S{$signatureRow}");

            $sheet->getStyle("A{$signatureRow}:S{$signatureRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            if (file_exists($inspection_created_by)) {
                $drawing = new Drawing();
                $drawing->setName('Checked and Prepared By');
                $drawing->setPath($inspection_created_by);
                $drawing->setCoordinates("I{$signatureRow}");
                $drawing->setOffsetX(50);
                $drawing->setOffsetY(10);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
            }

            $richTextSig = new RichText();
            $richTextSig->createTextRun("Checked and Prepared By: ")->getFont()->setBold(true);
            $richTextSig->createText(getUsername($inspection_detail->created_by));
            $sheet->getCell("A{$signatureRow}")->setValue($richTextSig);

            $writer = new Xlsx($spreadsheet);
            $fileName = 'First Aid Bag Checklist.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function ExportExcel()
    {
        try {
            $allData = $this->medicine_checklist->exportdata();
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $row = 1;

            foreach ($allData as $inspection_detail) {
                $inspection_detail = $this->medicine_checklist->selectOne($inspection_detail->inspection_id);
                $inspection_type = FIRST_AID_BAG_INSPECTION_CHECKLIST;
                $inspection_data = json_decode($inspection_detail->inspection_data, true);
                $inspection_updated_by = GetOHCSignature($inspection_detail->updated_by, $inspection_detail->id, $inspection_type);
                $inspection_created_by = GetOHCSignature($inspection_detail->created_by, $inspection_detail->id, $inspection_type);

                $currentRow = $row;

                if (file_exists(public_path('assets/images/logo-dark.png'))) {
                    $sheet->mergeCells("A$currentRow:F" . ($currentRow + 2));
                    $drawing = new Drawing();
                    $drawing->setName('Left Logo');
                    $drawing->setPath(public_path('assets/images/logo-dark.png'));
                    $drawing->setCoordinates("B$currentRow");
                    $drawing->setOffsetX(100);
                    $drawing->setOffsetY(15);
                    $drawing->setWidth(70);
                    $drawing->setHeight(70);
                    $drawing->setWorksheet($sheet);

                    $sheet->getStyle("A$currentRow:F" . ($currentRow + 2))->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                }

                $sheet->mergeCells("G{$currentRow}:M" . ($currentRow + 2));
                $sheet->setCellValue("G$currentRow", "FLOOR FIRST AID BAG INSPECTION CHECKLIST PN INTERNATIONAL PNT. LTD.");
                $sheet->getStyle("G$currentRow")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                if (file_exists(public_path('assets/images/plus-image.webp'))) {
                    $sheet->mergeCells("O$currentRow:S" . ($currentRow + 2));
                    $drawing = new Drawing();
                    $drawing->setName('Right Logo');
                    $drawing->setPath(public_path('assets/images/plus-image.webp'));
                    $drawing->setCoordinates("O$currentRow");
                    $drawing->setOffsetX(100);
                    $drawing->setOffsetY(15);
                    $drawing->setWidth(70);
                    $drawing->setHeight(70);
                    $drawing->setWorksheet($sheet);

                    $sheet->getStyle("O$currentRow:S" . ($currentRow + 2))->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                }

                $infoRow1 = $currentRow + 3;
                $sheet->mergeCells("A$infoRow1:G$infoRow1");
                $sheet->mergeCells("H$infoRow1:N$infoRow1");
                $sheet->mergeCells("O$infoRow1:S$infoRow1");
                $sheet->setCellValue("A$infoRow1", "DATE OF INSPECTION: " . Displaydateformat($inspection_detail->inspection_date));
                $sheet->setCellValue("H$infoRow1", "LOCATION: " . getLocationname($inspection_detail->location));
                $sheet->setCellValue("O$infoRow1", "SHIFT: " . getShiftname($inspection_detail->shift_id));

                $infoRow2 = $infoRow1 + 1;
                $sheet->mergeCells("A$infoRow2:G$infoRow2");
                $sheet->mergeCells("H$infoRow2:N$infoRow2");
                $sheet->mergeCells("O$infoRow2:S$infoRow2");
                $sheet->setCellValue("A$infoRow2", "NEXT DUE: " . Displaydateformat($inspection_detail->next_due));
                $sheet->setCellValue("H$infoRow2", "UNIT: " . getUnitname($inspection_detail->unit));
                $sheet->setCellValue("O$infoRow2", "FREQUENCY: " . getFrequencyname($inspection_detail->frequency));

                $sheet->getStyle("A$infoRow1:S$infoRow2")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $headerRow = $infoRow2 + 1;
                $sheet->mergeCells("A$headerRow:B$headerRow")->setCellValue("A$headerRow", "SERIAL NO");
                $sheet->mergeCells("C$headerRow:E$headerRow")->setCellValue("C$headerRow", "NAME OF THE MEDICINE");
                $sheet->mergeCells("F$headerRow:G$headerRow")->setCellValue("F$headerRow", "FREEZE QUANTITY");
                $sheet->mergeCells("H$headerRow:J$headerRow")->setCellValue("H$headerRow", "AVAILABLE QUANTITY");
                $sheet->mergeCells("K$headerRow:L$headerRow")->setCellValue("K$headerRow", "EXPIRY DATE");
                $sheet->mergeCells("M$headerRow:O$headerRow")->setCellValue("M$headerRow", "INSPECTED BY");
                $sheet->mergeCells("P$headerRow:S$headerRow")->setCellValue("P$headerRow", "REMARKS");

                $sheet->getStyle("A$headerRow:S$headerRow")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $inspectionRow = $headerRow + 1;
                foreach ($inspection_data as $index => $detail) {
                    $sheet->mergeCells("A$inspectionRow:B$inspectionRow")->setCellValue("A$inspectionRow", $index);
                    $sheet->mergeCells("C$inspectionRow:E$inspectionRow")->setCellValue("C$inspectionRow", getMedicinename($detail['medicine_id']));
                    $sheet->mergeCells("F$inspectionRow:G$inspectionRow")->setCellValue("F$inspectionRow", $detail['freeze_quantity'] ?? '');
                    $sheet->mergeCells("H$inspectionRow:J$inspectionRow")->setCellValue("H$inspectionRow", $detail['available_quantity'] ?? '');
                    $sheet->mergeCells("K$inspectionRow:L$inspectionRow")->setCellValue("K$inspectionRow", Displaydateformat($detail['expired_date']));
                    $sheet->mergeCells("M$inspectionRow:O$inspectionRow")->setCellValue("M$inspectionRow", getUsername($detail['emp_id']));
                    $sheet->mergeCells("P$inspectionRow:S$inspectionRow")->setCellValue("P$inspectionRow", $detail['remarks'] ?? '');

                    $sheet->getStyle("A$inspectionRow:S$inspectionRow")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $inspectionRow++;
                }

                $signatureRow = $inspectionRow;
                $sheet->getRowDimension($signatureRow)->setRowHeight(80);
                $sheet->mergeCells("A$signatureRow:S$signatureRow");

                if (file_exists($inspection_created_by)) {
                    $drawing = new Drawing();
                    $drawing->setName('Checked By');
                    $drawing->setPath($inspection_created_by);
                    $drawing->setCoordinates("I$signatureRow");
                    $drawing->setOffsetX(50);
                    $drawing->setOffsetY(10);
                    $drawing->setWidth(70);
                    $drawing->setHeight(70);
                    $drawing->setWorksheet($sheet);
                }

                $sheet->setCellValue("A$signatureRow", "Checked and Prepared By: " . getUsername($inspection_detail->created_by));
                $sheet->getStyle("A$signatureRow:S$signatureRow")->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);

                $sheet->getStyle("A$currentRow:S$signatureRow")->applyFromArray([
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM]],
                ]);

                $row = $signatureRow + 6;
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'First Aid Bag Checklist.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            dd($e);
            return back()->with('error', $e->getMessage());
        }
    }






    public function ExportPDF()
    {
        try {
            $allData = $this->medicine_checklist->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } else if (count($allData) > 20) {
                return redirect()->back()->with('error', __('inspection.excess_error'));
            }

            $data = array(

                'content' => $allData,
                'pagetitle' => "FIRST AID BAG INSPECTION CHECKLIST",
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

            $view = view('inspection.inspection_ohc.first_aid_bag_inspection.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "FIRST AID BAG INSPECTION CHECKLIST.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/emergency-floor-first-aid-bag/checklist/list'));
        }
    }

    public function ExportViewPDF(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_detail = $this->medicine_checklist->selectOne($id);
            $inspection_type = FIRST_AID_BAG_INSPECTION_CHECKLIST;
            $inspection_file = $this->signature->getFiles($id, $inspection_type);
            $inspection_data = json_decode($inspection_detail->inspection_data, true);
            $inspection_created_by = GetOHCSignature($inspection_detail->created_by, $inspection_detail->id, $inspection_type);


            $property = [
                'tempDir' => 'public/pdf/temp/',
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,

            ];

            $data = array(
                'inspection_detail' => $inspection_detail,
                'inspection_file' => $inspection_file,
                'pagetitle' => "FIRST AID BAG INSPECTION CHECKLIST",
                'inspection_data' => $inspection_data,
                'inspection_created_by' => $inspection_created_by,
            );


            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $html = view('inspection.inspection_ohc.first_aid_bag_inspection.viewpdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "FIRST AID BAG INSPECTION CHECKLIST.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/emergency-floor-first-aid-bag/checklist/list'));
        }
    }

    public function approval(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_details = $this->medicine_checklist->selectOne($id);
            $inspection_type = FIRST_AID_BAG_INSPECTION_CHECKLIST;
            $inspection_file = GetOHCSignature($inspection_details->created_by, $inspection_details->id, $inspection_type);
            $inspection_data = json_decode($inspection_details->inspection_data, true);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);

            $data = array(
                'inspection_details' => $inspection_details,
                'inspection_file' => $inspection_file,
                'inspection_data' => $inspection_data,
                'document_no' => $document_no,

            );

            return view('inspection.inspection_ohc.first_aid_bag_inspection.approval', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/emergency-floor-first-aid-bag/checklist/list'));
        }
    }

    public function approvalSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->capa_remarks;
            $eye_wash_inspection = $this->medicine_checklist->approvalSubmit($id, $status, $remarks);
            $inspection_details = $this->medicine_checklist->selectOne($id);
            $signature_update = $this->signature->signatureUpload(FIRST_AID_BAG_INSPECTION_CHECKLIST);
            // $ehsOfficer = GetEHSOfficer();
            // $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            // if ($status == 1) {
            //     $message = 'FORKLIFT INSPECTION - OBSERVATION APPROVED';
            //     $to_status = OBSERVATION_APPROVED;
            // } else {
            //     $message = 'FORKLIFT INSPECTION - OBSERVATION APPROVED';
            //     $to_status = OBSERVATION_REJECTED;
            // }
            // $web_link =   admin_url('ohc/emergency-floor-first-aid-bag/checklist/view/' . encryptId($inspection_details->id));
            // $mailsubject = 'SAFETY INSPECTION';
            // $notificationData = array(
            //     'notification_type' => SAFETY_INSPECTION,
            //     'module_type' => 1,
            //     'notification_message' => $mailsubject,
            //     'mobile_notification' => json_encode(array(
            //         'title' => $mailsubject,
            //         'message' => $message,
            //         'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
            //         'id' => $inspection_details->id,
            //         'module' => 1,
            //     )),
            //     'web_link' =>  $web_link,
            //     'assigned_user' => array_to_string($ehsOfficers),
            //     'created_by' => Auth::id(),
            // );
            // notificationSave($notificationData);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/emergency-floor-first-aid-bag/checklist/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('ohc/emergency-floor-first-aid-bag/checklist/list'));
        }
    }
}
