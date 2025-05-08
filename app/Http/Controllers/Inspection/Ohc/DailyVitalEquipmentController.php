<?php

namespace App\Http\Controllers\Inspection\Ohc;

use Exception;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Inspection\Master\Shift;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Ohc\DailyVitalEquipment;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;


class DailyVitalEquipmentController extends Controller
{
    private $daily_vital;
    private $unit;
    private $shift;
    private $signature;
    private $document_reference;

    public function __construct()
    {
        $this->daily_vital = new DailyVitalEquipment();
        $this->unit = new Unit();
        $this->shift = new Shift();
        $this->signature = new OhcSignature();
        $this->document_reference = new InspectionStaticDocno();
    }
    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->daily_vital->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('date_of_inspection', function ($row) {
                            return Displaydateformat($row->date_of_inspection);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('ohc/daily-vital-equipment/view/' . encryptId($row->id)) . '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('ohc/daily-vital-equipment/exportViewPdf/' . encryptId($row->id)) . '" class=" me-1" title="PDF">
                                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                                    </a>';
                            $btn .= '<a href="' . admin_url('ohc/daily-vital-equipment/generalexcel/' . encryptId($row->id)) . '" class=" me-1" title="PDF">
                                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                                    </a>';
                            return $btn;
                        })
                        ->rawColumns(['action' ,'created_date', 'created_by', 'date_of_inspection'])
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
        $shifts = $this->shift->getShiftname();
        $unit = $this->unit->getUnit();
        $data = [
            'shifts' => $shifts,
            'units' => $unit,
        ];
        return view('inspection.inspection_ohc.daily_vital_equipment.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $checklistQuestions = getCheckListQuestion(OHC_DAILY_VITAL_EQUIPMENT_CHECKLIST);
            $options =  getoption(OHC_DAILY_VITAL_EQUIPMENT_CHECKLIST);
            $getoption = string_to_array($options->type);
            $shifts = $this->shift->getShiftname();
            $unit = $this->unit->getUnit();
            $document_no = $this->document_reference->selectUsingName('DailyVitalEquipment');
            if (count($checklistQuestions) <= 0) {
                Session::flash('error', __('inspection.checklist_add'));
                return redirect()->back();
            }
            $data = array(
                'checklist_details' => $checklistQuestions,
                'getoption' => $getoption,
                'shifts' => $shifts,
                'units' => $unit,
                'document_no' => $document_no,
            );
            return view('inspection.inspection_ohc.daily_vital_equipment.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/daily-vital-equipment/list'));
        }
    }

    public function Store(Request $request)
    {
        try {
            $daily_vital = $this->daily_vital->store();
            $id = $daily_vital->id;
            $inspection_type = OHC_TYPE_DAILY_VITAL_EQUIPMENT_CHECKLIST;
            $signature_update = $this->signature->requestorsignatureUpload($inspection_type,$id);

            Session::flash('success', __('common.created_msg'));
            return redirect(admin_url('ohc/daily-vital-equipment/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/daily-vital-equipment/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $daily_vital = $this->daily_vital->selectOne($id);

            $document_no = $this->document_reference->selectOne($daily_vital->document_reference_id);

            $data = [
                'daily_vital' => $daily_vital,
                'document_no' => $document_no,
            ];
            return view('inspection.inspection_ohc.daily_vital_equipment.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/daily-vital-equipment/list'));
        }
    }

    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->daily_vital->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $currentRow = 1;

            foreach ($allData as $index => $daily_vital) {

                $user_response = json_decode($daily_vital->responses, true);

                $document_no = $this->document_reference->selectUsingName('DailyVitalEquipment');
                $inspection_type = OHC_TYPE_DAILY_VITAL_EQUIPMENT_CHECKLIST;

                $inspection_created_by = GetOHCSignature($daily_vital->checked_by, $daily_vital->id, $inspection_type);

                $sheet->getDefaultColumnDimension()->setWidth(14);

                for ($i = 0; $i < 100; $i++) {
                    $sheet->getRowDimension($currentRow + $i)->setRowHeight(25);
                }

                $sheet->mergeCells("A{$currentRow}:B" . ($currentRow + 2));
                $logoPath = public_path('assets/images/logo-dark.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates("A{$currentRow}");
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);
                    $drawing->setWidth(90);
                    $drawing->setHeight(50);
                    $drawing->setWorksheet($sheet);
                }
                $sheet->getStyle("A{$currentRow}:B" . ($currentRow + 2))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->mergeCells("C{$currentRow}:C" . ($currentRow + 2));

                $logoRightPath = public_path('assets/images/plus-image.webp');
                if (file_exists($logoRightPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Left Logo');
                    $drawing->setPath($logoRightPath);
                    $drawing->setCoordinates("C{$currentRow}");

                    $drawing->setOffsetX(20);
                    $drawing->setOffsetY(15);
                    $drawing->setWidth(30);
                    $drawing->setHeight(60);
                    $drawing->setWorksheet($sheet);

                }

                $sheet->mergeCells("I{$currentRow}:I" . ($currentRow + 2));

                $logoRightPath = public_path('assets/images/plus-image.webp');
                if (file_exists($logoRightPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Right Logo');
                    $drawing->setPath($logoRightPath);
                    $drawing->setCoordinates("I{$currentRow}");

                    $drawing->setOffsetX(20);
                    $drawing->setOffsetY(15);
                    $drawing->setWidth(30);
                    $drawing->setHeight(60);
                    $drawing->setWorksheet($sheet);

                }

                $sheet->mergeCells("D{$currentRow}:H" . ($currentRow + 2));
                $sheet->setCellValue("D{$currentRow}", "OCCUPATIONAL HEALTH CENTER\nपारमर्शिक स्वास्थ्य केंद्र\nPN INTERNATIONAL PVT LTD");
                $sheet->getStyle("D{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    // 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);

                $sheet->mergeCells("J{$currentRow}:K{$currentRow}")->setCellValue("J{$currentRow}", 'Doc. No.');
                $sheet->mergeCells("J" . ($currentRow + 1) . ":K" . ($currentRow + 1))->setCellValue("J" . ($currentRow + 1), 'Issue Dt.');
                $sheet->mergeCells("J" . ($currentRow + 2) . ":K" . ($currentRow + 2))->setCellValue("J" . ($currentRow + 2), 'Rev. & Dt.');
                $sheet->mergeCells("L{$currentRow}:M{$currentRow}")->setCellValue("L{$currentRow}", $document_no->doc_no);
                $sheet->mergeCells("L" . ($currentRow + 1) . ":M" . ($currentRow + 1))->setCellValue("L" . ($currentRow + 1), Displaydateformat($document_no->issue_date));
                $sheet->mergeCells("L" . ($currentRow + 2) . ":M" . ($currentRow + 2))->setCellValue("L" . ($currentRow + 2), $document_no->rev_dt);
                $sheet->getStyle("J{$currentRow}:M" . ($currentRow + 2))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $titleRow = $currentRow + 3;
                $sheet->mergeCells("A{$titleRow}:M" . ($titleRow + 1))->setCellValue("A{$titleRow}", "DAILY VITAL EQUIPMENT INSPECTION CHECKLIST");
                $sheet->getStyle("A{$titleRow}:M" . ($titleRow + 1))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FF0000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $infoRow = $titleRow + 2;
                $sheet->mergeCells("A{$infoRow}:D{$infoRow}")->setCellValue("A{$infoRow}", "DATE OF INSPECTION :- " . Displaydateformat($daily_vital->date_of_inspection));
                $sheet->mergeCells("E{$infoRow}:H{$infoRow}")->setCellValue("E{$infoRow}", "UNIT :- " . getUnitname($daily_vital->unit ?? '-'));
                $sheet->mergeCells("I{$infoRow}:M{$infoRow}")->setCellValue("I{$infoRow}", "SHIFT :- " . $daily_vital->shift);
                $sheet->getStyle("A{$infoRow}:M{$infoRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $headerRow = $infoRow + 1;
                $sheet->mergeCells("A{$headerRow}:B{$headerRow}")->setCellValue("A{$headerRow}", "SR. NO.");
                $sheet->mergeCells("C{$headerRow}:G{$headerRow}")->setCellValue("C{$headerRow}", "CHECK ITEMS");
                $sheet->mergeCells("H{$headerRow}:I{$headerRow}")->setCellValue("H{$headerRow}", "STATUS (YES/NO)");
                $sheet->mergeCells("J{$headerRow}:K{$headerRow}")->setCellValue("J{$headerRow}", "QUANTITY");
                $sheet->mergeCells("L{$headerRow}:M{$headerRow}")->setCellValue("L{$headerRow}", "REMARK");
                $sheet->getStyle("A{$headerRow}:M{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $row = $headerRow + 1;
                $srNo = 1;
                foreach ($user_response as $subcategory => $questions) {
                    foreach ($questions as $questionId => $answer) {
                        $statusSymbol = '-';
                        $responseText = strtoupper($answer['response'] ?? '');
                        $statusColor = null;
                        if ($responseText === 'YES') {
                            $statusSymbol = '✓';
                            $statusColor = '008000';
                        } elseif (in_array($responseText, ['NO', 'N/A'])) {
                            $statusSymbol = 'X';
                            $statusColor = 'FF0000';
                        } else {
                            $statusSymbol = $responseText;
                        }

                        $sheet->mergeCells("A{$row}:B{$row}")->setCellValue("A{$row}", $srNo++);
                        $sheet->mergeCells("C{$row}:G{$row}")->setCellValue("C{$row}", GetChecklistTypeDate($questionId));
                        $sheet->mergeCells("H{$row}:I{$row}")->setCellValue("H{$row}", $statusSymbol);
                        $sheet->mergeCells("J{$row}:K{$row}")->setCellValue("J{$row}", $answer['quantity'] ?? '-');
                        $sheet->mergeCells("L{$row}:M{$row}")->setCellValue("L{$row}", $answer['remark'] ?? '-');

                        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray([
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        ]);

                        if ($statusColor) {
                            $sheet->getStyle("H{$row}:I{$row}")->getFont()->getColor()->setRGB($statusColor);
                        }

                        $row++;
                    }
                }

                $sheet->getRowDimension($row)->setRowHeight(60);
                $sheet->mergeCells("A{$row}:M{$row}")->setCellValue("A{$row}", "CHECKED BY (NAME & SIGNATURE) :- ");

                $sheet->getStyle("A{$row}:M{$row}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                if (file_exists($inspection_created_by)) {
                    $drawing = new Drawing();
                    $drawing->setName('Signature');
                    $drawing->setPath($inspection_created_by);
                    $drawing->setCoordinates("J{$row}");
                    $drawing->setOffsetX(80);
                    $drawing->setOffsetY(15);
                    $drawing->setWidth(120);
                    $drawing->setHeight(50);
                    $drawing->setWorksheet($sheet);
                }

                $sheet->getStyle("A{$currentRow}:M{$row}")->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $currentRow = $row + 5;
            }


            $writer = new Xlsx($spreadsheet);
            $fileName = 'Daily Vital Equipment.xlsx';
            $filePath = storage_path("app/public/{$fileName}");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/daily-vital-equipment/list'));
        }
    }


    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->daily_vital->exportdata();

            $document_no = $this->document_reference->selectUsingName('DailyVitalEquipment');

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }elseif(count($allData) > 20){
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }

            $data = array(
                'content' => $allData,
                'document_no' => $document_no,
                'pagetitle' => "Daily Vital Equipment",
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

            $view = view('inspection.inspection_ohc.daily_vital_equipment.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Daily Vital Equipment.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/daily-vital-equipment/list'));
        }
    }

    public function exportViewPdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $daily_vital = $this->daily_vital->selectOne($id);
                $document_no = $this->document_reference->selectUsingName('DailyVitalEquipment');

                $data = [
                    'daily_vital' => $daily_vital,
                    'document_no' => $document_no,
                    'pagetitle' => "OHC Daily Vital Equipment",
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

            $html = view('inspection.inspection_ohc.daily_vital_equipment.viewPdf',$data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Daily Vital Equipment.pdf";
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

            $daily_vital = $this->daily_vital->selectOne($id);
            $document_no = $this->document_reference->selectUsingName('DailyVitalEquipment');
            $user_response = json_decode($daily_vital->responses, true);
            $inspection_type = OHC_TYPE_DAILY_VITAL_EQUIPMENT_CHECKLIST;
            $inspection_created_by = GetOHCSignature($daily_vital->created_by, $daily_vital->id, $inspection_type);

            $sheet->getDefaultColumnDimension()->setWidth(14);

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $row = 1;

            $sheet->mergeCells("A1:B3");
            $logoPath = public_path('assets/images/logo-dark.png');
            if (file_exists($logoPath)) {
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setPath($logoPath);
                $drawing->setCoordinates("A1");
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(10);
                $drawing->setWidth(90);
                $drawing->setHeight(50);
                $drawing->setWorksheet($sheet);
            }

            $sheet->getStyle("A1:B3")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ]);


            $sheet->mergeCells("C{$row}:C" . ($row + 2));

            $logoRightPath = public_path('assets/images/plus-image.webp');
            if (file_exists($logoRightPath)) {
                $drawing = new Drawing();
                $drawing->setName('Left Logo');
                $drawing->setPath($logoRightPath);
                $drawing->setCoordinates("C{$row}");

                $drawing->setOffsetX(20);
                $drawing->setOffsetY(15);
                $drawing->setWidth(30);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);

            }

            $sheet->mergeCells("I{$row}:I" . ($row + 2));

            $logoRightPath = public_path('assets/images/plus-image.webp');
            if (file_exists($logoRightPath)) {
                $drawing = new Drawing();
                $drawing->setName('Right Logo');
                $drawing->setPath($logoRightPath);
                $drawing->setCoordinates("I{$row}");

                $drawing->setOffsetX(20);
                $drawing->setOffsetY(15);
                $drawing->setWidth(30);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);

            }

            $sheet->mergeCells("D1:H3");
            $sheet->setCellValue("D1", "OCCUPATIONAL HEALTH CENTER\nपारमर्शिक स्वास्थ्य केंद्र\nPN INTERNATIONAL PVT LTD");
            $sheet->getStyle("D1")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);

            $sheet->mergeCells("J1:K1")->setCellValue("J1", 'Doc. No.');
            $sheet->mergeCells("J2:K2")->setCellValue("J2", 'Issue Dt.');
            $sheet->mergeCells("J3:K3")->setCellValue("J3", 'Rev. & Dt.');
            $sheet->mergeCells("L1:M1")->setCellValue("L1", $document_no->doc_no);
            $sheet->mergeCells("L2:M2")->setCellValue("L2", Displaydateformat($document_no->issue_date));
            $sheet->mergeCells("L3:M3")->setCellValue("L3", $document_no->rev_dt);
            $sheet->getStyle("J1:M3")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells("A4:M5")->setCellValue("A4", "DAILY VITAL EQUIPMENT INSPECTION CHECKLIST");
            $sheet->getStyle("A4:M5")->applyFromArray([
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FF0000']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells("A6:D6")->setCellValue("A6", "DATE OF INSPECTION :- " . Displaydateformat($daily_vital->date_of_inspection));
            $sheet->mergeCells("E6:H6")->setCellValue("E6", "UNIT :- " . getUnitname($daily_vital->unit ?? '-'));
            $sheet->mergeCells("I6:M6")->setCellValue("I6", "SHIFT :- " . getShiftname($daily_vital->shift));
            $sheet->getStyle("A6:M6")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells("A7:B7")->setCellValue("A7", "SR. NO.");
            $sheet->mergeCells("C7:G7")->setCellValue("C7", "CHECK ITEMS");
            $sheet->mergeCells("H7:I7")->setCellValue("H7", "STATUS (YES/NO)");
            $sheet->mergeCells("J7:K7")->setCellValue("J7", "QUANTITY");
            $sheet->mergeCells("L7:M7")->setCellValue("L7", "REMARK");

            $sheet->getStyle("A7:M7")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $row = 8;
            $srNo = 1;
            foreach ($user_response as $subcategory => $questions) {
                foreach ($questions as $questionId => $answer) {
                    $sheet->mergeCells("A{$row}:B{$row}")->setCellValue("A{$row}", $srNo);
                    $sheet->mergeCells("C{$row}:G{$row}")->setCellValue("C{$row}", GetChecklistTypeDate($questionId));

                    $statusSymbol = '-';
                    $responseText = $answer['response'] ?? '';
                    if ($responseText === 'YES') {
                        $statusSymbol = '✓';
                        $statusColor = '008000';
                    } elseif (in_array($responseText, ['NO', 'N/A'])) {
                        $statusSymbol = 'X';
                        $statusColor = 'FF0000';
                    } else {
                        $statusSymbol = $answer['response'] ?? '-';
                        $statusColor = null;
                    }

                    $sheet->mergeCells("H{$row}:I{$row}")->setCellValue("H{$row}", $statusSymbol);
                    $sheet->mergeCells("J{$row}:K{$row}")->setCellValue("J{$row}", $answer['quantity'] ?? '-');
                    $sheet->mergeCells("L{$row}:M{$row}")->setCellValue("L{$row}", $answer['remark'] ?? '-');

                    $sheet->getStyle("A{$row}:M{$row}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    if ($statusColor) {
                        $sheet->getStyle("H{$row}:I{$row}")->applyFromArray([
                            'bold' => true,
                            'size' => 14,
                            'font' => ['color' => ['rgb' => $statusColor]],
                        ]);
                    }

                    $row++;
                    $srNo++;
                }
            }

            $sheet->getRowDimension($row)->setRowHeight(60);
            $sheet->mergeCells("A{$row}:M{$row}")->setCellValue("A{$row}", "CHECKED BY (NAME & SIGNATURE) :- ");

            $sheet->getStyle("A{$row}:M{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            if (file_exists($inspection_created_by)) {
                $drawing = new Drawing();
                $drawing->setName('Signature');
                $drawing->setPath($inspection_created_by);
                $drawing->setCoordinates("J{$row}");
                $drawing->setOffsetX(80);
                $drawing->setOffsetY(15);
                $drawing->setWidth(120);
                $drawing->setHeight(50);
                $drawing->setWorksheet($sheet);
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Daily Vital Equipment.xlsx';
            $filePath = storage_path("app/public/{$fileName}");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/daily-vital-equipment/list'));
        }
    }


}
