<?php

namespace App\Http\Controllers\Inspection\Fire;

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
use App\Models\Inspection\Fire\FireSafetyEquipment;
use App\Models\Inspection\Fire\Fire;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Master\Department;
use App\Models\Master\Unit;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FireSafetyEquipmentsController extends Controller
{
    private $unit;
    private $fire_safety_equipment;
    private $upload_log;
    private $fire;
    private $static_docno;

    public function __construct()
    {
        $this->fire_safety_equipment = new FireSafetyEquipment();
        $this->unit = new Unit();
        $this->fire = new Fire();
        $this->static_docno = new InspectionStaticDocno();
    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $type = 3;
                    $data  = $this->fire->list($type);
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
                        ->addColumn('created_date', function ($row) {
                            return Displaydatetimeformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('fire/fire-safety/equipments/code-sheet/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {

                            // $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '"  class="recordDelete" title="' . __('common.delete') . '"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            // }
                            $btn .= '<a href="' . admin_url('fire/fire-safety/equipments/code-sheet/exportViewPdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                                    </a>';

                            $btn .= '<a href="' . admin_url('fire/fire-safety/equipments/code-sheet/generalExcel/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="Excel">
                                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                                    </a>';

                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status'])
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
        $fireList  = $this->fire->select('id', 'fire_no')->where('type', 3)->where('status', '1')->get();

        $data = array(
            'fireList' => $fireList,
        );
        return view('inspection.fire.firesafetyequipment.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $staticDocno  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                ['type', "FireSafetyEquipment"],
                ['status', '1']
            ])->first();

            $data = array(
                'unitList' => $unitList,
                'staticDocno' => $staticDocno,
            );
            return view('inspection.fire.firesafetyequipment.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('fire/fire-safety/equipments/code-sheet/list'));
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'fire_safety_equipment_no' => 'required',
            ];
            $messages = [
                'fire_safety_equipment_no.required' => "Certified Fire Fighter No is Required",
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                $fire_no = $request->fire_safety_equipment_no;
                $type = 3;
                $fire =   $this->fire->store($fire_no, $type);

                $this->fire_safety_equipment->store($fire->id);

                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {
                Session::flash('error', __('common.message_error'));
            }

            return redirect(admin_url('fire/fire-safety/equipments/code-sheet/list'));
        } catch (Exception $ex) {
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('fire/fire-safety/equipments/code-sheet/list'));
        }
    }



    public function View($id)
    {
        try {
            $id = decryptId($id);
            if (Auth::check()) {
                $type = 3;
                $fireData =   $this->fire->selectOne($id, $type);
                $fireSafetyEquipmentDataList = $this->fire_safety_equipment->selectOne($id);
                $staticDocno  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                    ['type', "FireSafetyEquipment"],
                    ['status', '1']
                ])->first();
                $data = array(
                    'fireData' => $fireData,
                    'fireSafetyEquipmentDataList' => $fireSafetyEquipmentDataList,
                    'staticDocno' => $staticDocno,
                );
            }
            return view('inspection.fire.firesafetyequipment.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/fire-safety/equipments/code-sheet/list'));
        }
    }


    public function StatusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $type = 3;
            $fireID = $this->fire->statuschange($id, $type);
            $this->fire_safety_equipment->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'status changed'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function ExportExcel(Request $request)
    {
        try {
            $type = 3;

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $allData = $this->fire->exportdata($type);



            foreach (range('A', 'P') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            for ($i = 1; $i <= 2000; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $currentRow = 1;

            foreach ($allData as $dataSet) {

                $document_no = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')
                    ->where([
                        ['type', "FireSafetyEquipment"],
                        ['status', '1']
                    ])->first();

                $leftLogoPath = public_path('assets/images/logo-dark.png');
                if (file_exists($leftLogoPath)) {
                    $leftDrawing = new Drawing();
                    $leftDrawing->setName('LeftLogo');
                    $leftDrawing->setDescription('Left Company Logo');
                    $leftDrawing->setPath($leftLogoPath);
                    $leftDrawing->setCoordinates('A' . $currentRow);
                    $leftDrawing->setOffsetX(10);
                    $leftDrawing->setOffsetY(5);
                    $leftDrawing->setHeight(60);
                    $leftDrawing->setWorksheet($sheet);
                }
                $sheet->mergeCells("A{$currentRow}:B" . ($currentRow + 2));
                $sheet->getStyle("A{$currentRow}:B" . ($currentRow + 2))->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $rightLogoPath = public_path('assets/images/fire_safety_logo.jpg');
                if (file_exists($rightLogoPath)) {
                    $rightDrawing = new Drawing();
                    $rightDrawing->setName('RightLogo');
                    $rightDrawing->setDescription('Right Company Logo');
                    $rightDrawing->setPath($rightLogoPath);
                    $rightDrawing->setCoordinates('K' . $currentRow);
                    $rightDrawing->setOffsetX(15);
                    $rightDrawing->setOffsetY(15);
                    $rightDrawing->setHeight(60);
                    $rightDrawing->setWorksheet($sheet);

                    $sheet->getStyle("K{$currentRow}:M" . ($currentRow + 2))->applyFromArray([
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]
                    ]);
                }
                $sheet->mergeCells("K{$currentRow}:M" . ($currentRow + 2));

                $sheet->mergeCells("C{$currentRow}:J" . ($currentRow + 2));
                $sheet->setCellValue("C{$currentRow}", "Fire Safety Equipment Resource Code Sheet");
                $sheet->getStyle("C{$currentRow}:J" . ($currentRow + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $labelMap = [
                    'N' => ['label' => 'Doc. No.', 'value' => $document_no->doc_no ?? ''],
                    'N' => ['label' => 'Issue Dt.', 'value' => Displaydateformat($document_no->issue_date) ?? ''],
                    'N' => ['label' => 'Rev. & Dt.', 'value' => $document_no->rev_dt ?? ''],
                ];

                $labels = ['Doc. No.', 'Issue Dt.', 'Rev. & Dt.'];
                $docValues = [
                    $document_no->doc_no ?? '',
                    Displaydateformat($document_no->issue_date) ?? '',
                    $document_no->rev_dt ?? '',
                ];

                $rowOffset = 0;
                foreach ($labels as $index => $label) {
                    $sheet->setCellValue('N' . ($currentRow + $rowOffset), $label);
                    $sheet->setCellValue('O' . ($currentRow + $rowOffset), $docValues[$index]);

                    $sheet->getStyle('N' . ($currentRow + $rowOffset))->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    ]);
                    $sheet->getStyle('O' . ($currentRow + $rowOffset))->applyFromArray([
                        'font' => ['size' => 11],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    ]);
                    $rowOffset++;
                }

                $headingRow = $currentRow + 3;

                $sheet->mergeCells("A{$headingRow}:A" . ($headingRow + 1))->setCellValue("A{$headingRow}", 'Sr.No');
                $sheet->mergeCells("B{$headingRow}:C" . ($headingRow + 1))->setCellValue("B{$headingRow}", 'Name of Fire & Safety Equipment');
                $sheet->mergeCells("D{$headingRow}:E" . ($headingRow + 1))->setCellValue("D{$headingRow}", 'Resource Code No');
                $sheet->mergeCells("F{$headingRow}:G" . ($headingRow + 1))->setCellValue("F{$headingRow}", 'Series Code');
                $sheet->mergeCells("H{$headingRow}:I" . ($headingRow + 1))->setCellValue("H{$headingRow}", 'Unit');
                $sheet->mergeCells("J{$headingRow}:K" . ($headingRow + 1))->setCellValue("J{$headingRow}", 'Allotted Series Code');
                $sheet->mergeCells("L{$headingRow}:M" . ($headingRow + 1))->setCellValue("L{$headingRow}", 'Total Allotted Code');
                $sheet->mergeCells("N{$headingRow}:O" . ($headingRow + 1))->setCellValue("N{$headingRow}", 'Remark');

                $sheet->getStyle("A{$headingRow}:O" . ($headingRow + 1))->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $row = $headingRow + 2;
                $sr = 1;

                foreach ($dataSet as $item) {
                    $sheet->setCellValue("A{$row}", $sr);
                    $sheet->mergeCells("B{$row}:C{$row}")->setCellValue("B{$row}", $item->name_of_fire_safety ?? '');
                    $sheet->mergeCells("D{$row}:E{$row}")->setCellValue("D{$row}", $item->resource_code ?? '');
                    $sheet->mergeCells("F{$row}:G{$row}")->setCellValue("F{$row}", $item->series_code ?? '');
                    $sheet->mergeCells("H{$row}:I{$row}")->setCellValue("H{$row}", getUnitname($item->unit_id) ?? '');
                    $sheet->mergeCells("J{$row}:K{$row}")->setCellValue("J{$row}", $item->allotted_series_code ?? '');
                    $sheet->mergeCells("L{$row}:M{$row}")->setCellValue("L{$row}", $item->total_allotted_code ?? '');
                    $sheet->mergeCells("N{$row}:O{$row}")->setCellValue("N{$row}", $item->remark ?? '');

                    $sheet->getStyle("A{$row}:O{$row}")->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);

                    $row++;
                    $sr++;
                }

                $currentRow = $row + 4;
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Fire Safety Equipment.xlsx';
            $filePath = storage_path("app/public/{$fileName}");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/fire-safety/equipments/code-sheet/list'));
        }
    }


    public function ExportPDF()
    {
        try {
            $type = 3;

            $allData = $this->fire->exportdata($type);

            $document_no = $this->static_docno->selectUsingName('FireSafetyEquipment');

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }

            $data = array(
                'content' => $allData,
                'document_no' => $document_no,
                'pagetitle' => "Fire Safety Equipment",
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

            $view = view('inspection.fire.firesafetyequipment.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Fire Safety Equipment.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/fire-safety/equipments/code-sheet/list'));
        }
    }

    public function ExportViewPDF(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $fire_safety_equipment = $this->fire_safety_equipment->selectOne($id);

                $document_no = $this->static_docno->selectUsingName('FireSafetyEquipment');

                $data = [
                    'fire_safety_equipment' => $fire_safety_equipment,
                    'pagetitle' => "Fire Safety Equipment",
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

            $html = view('inspection.fire.firesafetyequipment.viewPdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Fire Safety Equipment.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/fire-safety/equipments/code-sheet/list'));
        }
    }

    public function generalExcel(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $fire_safety_equipments = $this->fire_safety_equipment->selectFireId($id);
            $fire_safety_equipment = $fire_safety_equipments->first();


            $document_no = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')
                ->where([
                    ['type', "FireSafetyEquipment"],
                    ['status', '1']
                ])->first();

            foreach (range('A', 'P') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $leftLogoPath = public_path('assets/images/logo-dark.png');
            if (file_exists($leftLogoPath)) {
                $leftDrawing = new Drawing();
                $leftDrawing->setName('LeftLogo');
                $leftDrawing->setDescription('Left Company Logo');
                $leftDrawing->setPath($leftLogoPath);
                $leftDrawing->setCoordinates('A1');
                $leftDrawing->setOffsetX(10);
                $leftDrawing->setOffsetY(5);
                $leftDrawing->setHeight(60);
                $leftDrawing->setWorksheet($sheet);
            }
            $sheet->mergeCells('A1:B3');
            $sheet->getStyle('A1:B3')->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $rightLogoPath = public_path('assets/images/fire_safety_logo.jpg');
            if (file_exists($rightLogoPath)) {
                $rightDrawing = new Drawing();
                $rightDrawing->setName('RightLogo');
                $rightDrawing->setDescription('Right Company Logo');
                $rightDrawing->setPath($rightLogoPath);
                $rightDrawing->setCoordinates('K1');
                $rightDrawing->setOffsetX(15);
                $rightDrawing->setOffsetY(15);
                $rightDrawing->setHeight(60);
                $rightDrawing->setWorksheet($sheet);
            }

            $sheet->mergeCells('K1:M3');

            $sheet->mergeCells('C1:J3');
            $sheet->setCellValue('C1', "Fire Safety Equipment Resource Code Sheet");
            $sheet->getStyle('C1:J3')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $labelMap = [
                'N1' => ['label' => 'Doc. No.', 'valueCell' => 'O1', 'value' => $document_no->doc_no ?? ''],
                'N2' => ['label' => 'Issue Dt.', 'valueCell' => 'O2', 'value' => Displaydateformat($document_no->issue_date) ?? ''],
                'N3' => ['label' => 'Rev. & Dt.', 'valueCell' => 'O3', 'value' => $document_no->rev_dt ?? ''],
            ];

            foreach ($labelMap as $labelCell => $info) {
                $sheet->setCellValue($labelCell, $info['label']);
                $sheet->setCellValue($info['valueCell'], $info['value']);

                $sheet->getStyle($labelCell)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                ]);

                $sheet->getStyle($info['valueCell'])->applyFromArray([
                    'font' => ['size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                ]);
            }

            $sheet->mergeCells('A4:A5')->setCellValue('A4', 'Sr.No');
            $sheet->mergeCells('B4:C5')->setCellValue('B4', 'Name of Fire & Safety Equipment');
            $sheet->mergeCells('D4:E5')->setCellValue('D4', 'Resource Code No');
            $sheet->mergeCells('F4:G5')->setCellValue('F4', 'Series Code ');
            $sheet->mergeCells('H4:I5')->setCellValue('H4', 'Unit');
            $sheet->mergeCells('J4:K5')->setCellValue('J4', 'Allotted Series Code');
            $sheet->mergeCells('L4:M5')->setCellValue('L4', 'Total Allotted Code ');
            $sheet->mergeCells('N4:O5')->setCellValue('N4', 'Remark');

            $sheet->getStyle('A4:O5')->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $row = 6;
            $sr = 1;

            foreach ($fire_safety_equipments as $fire_safety_equipment) {

                $sheet->setCellValue("A{$row}", $sr);
                $sheet->mergeCells("B{$row}:C{$row}")->setCellValue("B{$row}", $fire_safety_equipment['name_of_fire_safety'] ?? '');
                $sheet->mergeCells("D{$row}:E{$row}")->setCellValue("D{$row}", $fire_safety_equipment['resource_code'] ?? '');
                $sheet->mergeCells("F{$row}:G{$row}")->setCellValue("F{$row}", $fire_safety_equipment['series_code'] ?? '');
                $sheet->mergeCells("H{$row}:I{$row}")->setCellValue("H{$row}", getUnitname($fire_safety_equipment['unit_id']) ?? '');
                $sheet->mergeCells("J{$row}:K{$row}")->setCellValue("J{$row}", $fire_safety_equipment['allotted_series_code'] ?? '');
                $sheet->mergeCells("L{$row}:M{$row}")->setCellValue("L{$row}", $fire_safety_equipment['total_allotted_code'] ?? '');
                $sheet->mergeCells("N{$row}:O{$row}")->setCellValue("N{$row}", $fire_safety_equipment['remark'] ?? '');

                $sheet->getStyle("A{$row}:O{$row}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $row++;
                $sr++;
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Fire Safety Equipment.xlsx';
            $filePath = storage_path("app/public/{$fileName}");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/fire-safety/equipments/code-sheet/list'));
        }
    }
}
