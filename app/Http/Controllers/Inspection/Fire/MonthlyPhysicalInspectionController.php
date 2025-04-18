<?php

namespace App\Http\Controllers\Inspection\Fire;

use Exception;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Master\Location;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\SimpleExcel\SimpleExcelWriter;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\Inspection\Master\Frequency;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Safety\Master\Equipment;
use App\Models\Inspection\Safety\FireSafetyEquipment;
use App\Models\Inspection\Fire\MonthlyPhysicalInspection;
use App\Models\Inspection\Safety\FireSafetyEquipmentDetails;
use App\Models\Inspection\Safety\MonthlyPhysicalEquipmentList;
use App\Models\Inspection\Fire\MonthlyPhysicalInspectionFileUpload;

class MonthlyPhysicalInspectionController extends Controller
{
    private $safety_equipment;
    private $safety_equipment_details;
    private $equipment;
    private $document_reference;
    private $equipment_list;
    private $location;
    private $unit;
    private $monthly_inspection_file_upload;
    private $frequency;


    public function __construct()
    {
        $this->safety_equipment = new MonthlyPhysicalInspection();
        $this->equipment = new Equipment();
        $this->document_reference = new InspectionStaticDocno();
        $this->equipment_list = new MonthlyPhysicalEquipmentList();
        $this->location = new Location();
        $this->unit = new Unit();
        $this->monthly_inspection_file_upload = new MonthlyPhysicalInspectionFileUpload();
        $this->frequency = new Frequency();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->safety_equipment->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                            if ($row->equipment_status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->inspection_id) . "' data-type = '1'>Active</span>";
                            } else if ($row->equipment_status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->inspection_id) . "' data-type = '0'>In-Active</span>";
                            }
                            // }
                            return $text;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('fire/equipment-monthly-physical-inspection/view/' . encryptId($row->inspection_id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('fire/equipment-monthly-physical-inspection/exportViewPdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                                <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                            </a>';

                            $btn .= '<a href="' . admin_url('fire/equipment-monthly-physical-inspection/export/excel/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="Excel"> <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i></a>';

                            return $btn;
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('date_of_inspection', function ($row) {
                            return Displaydateformat($row->date_of_inspection);
                        })
                        ->addColumn('standard_norms', function ($row) {
                            if ($row->status == STANDARD) {
                                $text = "<span  data-id='" . encryptId($row->inspection_id) . "' data-type = '1'>Standard</span>";
                            } else if ($row->status == NORMS) {
                                $text = "<span data-id='" . encryptId($row->inspection_id) . "' data-type = '0'>Norms</span>";
                            }
                            return $text;
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'inspection_status', 'date_of_inspection', 'standard_norms'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {
                    dd($ex);
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }

        $equipment = $this->equipment->get();
        $location = $this->location->getLocationName();
        $unit = $this->unit->getUnit();

        $data = array(
            'equipment' => $equipment,
            'locations' => $location,
            'units' => $unit,
        );
        return view('inspection.fire.monthly_physical_inspection.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $equipment = $this->equipment->get();
            $document_no = $this->document_reference->selectUsingName('FireEquipmentMonthlyPhysicalInspection');
            $equipment_list = $this->equipment_list->getEquipmentList();
            $location = $this->location->getLocationName();
            $frequency = $this->frequency->getFrequency();
            $unit = $this->unit->getUnit();


            $data = array(
                'equipment' => $equipment,
                'document_no' => $document_no,
                'equipment_list' => $equipment_list,
                'locations' => $location,
                'units' => $unit,
                'frequencies' => $frequency
            );
            return view('inspection.fire.monthly_physical_inspection.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/equipment-monthly-physical-inspection/list'));
        }
    }

    public function Store(Request $request)
    {
        try {


            $rules = [
                'inspection_date' => 'required',
                'location_id' => 'required',
                'unit_id' => 'required',
                'status.*' => 'required',
                'remarks.*' => 'required',
                'equipment.*' => 'required',
                'frequency.*' => 'required',
            ];

            $messages = [
                'inspection_date.required' => 'Inspection Date is required.',
                'location_id.required' => 'Location is required.',
                'unit_id.required' => 'Unit is required.',
                'status.*.required' => 'Status is required.',
                'remarks.*.required' => 'Remarks are required.',
                'equipment.*.required' => 'Equipment is required.',
                'frequency.*.required' => 'Frequency is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $fire_safety_equipment = $this->safety_equipment->store();
            $image_upload = $this->monthly_inspection_file_upload->store($fire_safety_equipment->id);


            Session::flash('success', 'Equipment Name is Added Successfully');
            return redirect(admin_url('fire/equipment-monthly-physical-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/equipment-monthly-physical-inspection/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->safety_equipment->selectOne($id);
            $images = $this->monthly_inspection_file_upload->GetFile($inspection_details->id);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);
            $inspection_data = json_decode($inspection_details->inspected_data, true);

            $data = array(
                'inspection_details' => $inspection_details,
                'document_no' => $document_no,
                'images' => $images,
                'inspection_data' => $inspection_data,
            );



            return view('inspection.fire.monthly_physical_inspection.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/equipment-monthly-physical-inspection/list'));
        }
    }


    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->safety_equipment->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            foreach (range('A', 'L') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $startRow = 1;

            foreach ($allData as $inspection_data) {
                $inspection = $this->safety_equipment->selectOne($inspection_data->inspection_id);
                $images = $this->monthly_inspection_file_upload->GetFile($inspection->id);
                $document_no = $this->document_reference->selectOne($inspection->document_reference_id);
                $inspection_data = json_decode($inspection->inspected_data, true);

                $logoRow = $startRow;
                $sheet->mergeCells("A$logoRow:C" . ($logoRow + 2));
                $sheet->mergeCells("D$logoRow:J" . ($logoRow + 2));
                $sheet->setCellValue("D$logoRow", 'Fire Equipment Monthly Physical Inspection PN INTERNATIONAL PVT. LTD.');

                $sheet->getStyle("D$logoRow:J" . ($logoRow + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $logoPath = public_path('assets/images/logo-dark.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates("A$logoRow");
                    $drawing->setHeight(60);
                    $drawing->setWorksheet($sheet);
                }

                $sheet->setCellValue("K$logoRow", 'Doc. No.');
                $sheet->setCellValue("L$logoRow", $document_no->doc_no ?? '');

                $sheet->setCellValue("K" . ($logoRow + 1), 'Issue Dt.');
                $sheet->setCellValue("L" . ($logoRow + 1), Displaydateformat($document_no->issue_date));

                $sheet->setCellValue("K" . ($logoRow + 2), 'Rev. & Dt.');
                $sheet->setCellValue("L" . ($logoRow + 2), $document_no->rev_dt ?? '');

                $sheet->getStyle("K$logoRow:L" . ($logoRow + 2))->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                ]);

                $startRow += 3;

                $sheet->mergeCells("A$startRow:D$startRow")->setCellValue("A$startRow", 'Date of Inspection:- ' . Displaydateformat($inspection->date_of_inspection));
                $sheet->mergeCells("E$startRow:H$startRow")->setCellValue("E$startRow", 'Location :- ' . getLocationname($inspection->location));
                $sheet->mergeCells("I$startRow:L$startRow")->setCellValue("I$startRow", 'Unit:- ' . getUnitname($inspection->unit));

                $sheet->getStyle("A$startRow:L$startRow")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $startRow++;

                $sheet->mergeCells("A$startRow:A$startRow")->setCellValue("A$startRow", 'SR.NO');
                $sheet->mergeCells("B$startRow:D$startRow")->setCellValue("B$startRow", 'EQUIPMENT NAME');
                $sheet->mergeCells("E$startRow:F$startRow")->setCellValue("E$startRow", 'FREQUENCY');
                $sheet->mergeCells("G$startRow:H$startRow")->setCellValue("G$startRow", 'STATUS');
                $sheet->mergeCells("I$startRow:L$startRow")->setCellValue("I$startRow", 'REMARKS');

                $sheet->getStyle("A$startRow:L$startRow")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $startRow++;

                $sr = 1;
                foreach ($inspection_data as $detail) {
                    $sheet->mergeCells("B$startRow:D$startRow");
                    $sheet->mergeCells("E$startRow:F$startRow");
                    $sheet->mergeCells("G$startRow:H$startRow");
                    $sheet->mergeCells("I$startRow:L$startRow");

                    $sheet->setCellValue("A$startRow", $sr);
                    $sheet->setCellValue("B$startRow", getMonthlyInspectionEquipmentname($detail['id']) ?? '');
                    $sheet->setCellValue("E$startRow", getFrequencyname($detail['frequency']) ?? '');
                    $sheet->setCellValue("G$startRow", $detail['status'] ?? '');
                    $sheet->setCellValue("I$startRow", $detail['remarks'] ?? '');

                    $sheet->getStyle("A$startRow:L$startRow")->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER
                        ],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);

                    $startRow++;
                    $sr++;
                }

                foreach ($images as $index => $imageGroup) {
                    $sheet->mergeCells("A$startRow:L$startRow");
                    $sheet->setCellValue("A$startRow", getMonthlyInspectionEquipmentname($index));

                    $sheet->getStyle("A$startRow:L$startRow")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FF0000']
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER
                        ],
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);

                    $startRow++;

                    $sheet->getRowDimension($startRow)->setRowHeight(100);

                    $columns = ['B', 'I'];
                    foreach ($imageGroup as $i => $img) {
                        if ($i > 1) break;

                        $imagePath = $img->file_path;
                        if (file_exists($imagePath)) {
                            $drawing = new Drawing();
                            $drawing->setName('Image');
                            $drawing->setPath($imagePath);
                            $drawing->setCoordinates($columns[$i] . $startRow);
                            $drawing->setHeight(90);
                            $drawing->setOffsetX(10);
                            $drawing->setOffsetY(15);
                            $drawing->setWorksheet($sheet);
                        }
                    }

                    $startRow++;
                }

                $sheet->getStyle("A$logoRow:L" . ($startRow - 1))->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $startRow += 4;
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Fire_Equipment_Monthly_Physical_Inspection_Report.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);

        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong, Please try again later!');
            return redirect(admin_url('fire/equipment-monthly-physical-inspection/list'));
        }
    }


    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->safety_equipment->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } else if (count($allData) > 20) {
                return redirect()->back()->with('error', __('inspection.excess_error'));
            }

            $data = array(
                'content' => $allData,
                'pagetitle' => "Fire Equipment Monthly Physical Inspection Details",
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

            $view = view('inspection.fire.monthly_physical_inspection.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Fire Equipment Monthly Physical Inspection Details.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/equipment-monthly-physical-inspection/list'));
        }
    }


    public function exportViewPdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $inspection_details = $this->safety_equipment->selectOne($id);
                $images = $this->monthly_inspection_file_upload->GetFile($inspection_details->id);
                $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);
                $inspection_data = json_decode($inspection_details->inspected_data, true);

                $data = [
                    'inspection_details' => $inspection_details,
                    'inspection_data' => $inspection_data,
                    'images' => $images,
                    'pagetitle' => "Fire Equipment Monthly Physical Inspection List",
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

            $html = view('inspection.fire.monthly_physical_inspection.viewPdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Fire Equipment Monthly Physical Inspection List.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/equipment-monthly-physical-inspection/list'));
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->safety_equipment->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => __('Equipment Status is changed')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('administration.please_try_after_some_time')], 406);
        }
    }

    public function UniqueCheck(Request $request)
    {
        if ($request->ajax()) {
            $item_code  = $request->item_code;
            $equipment_name = decryptId($request->equipment_name);
            $isUnique = $this->safety_equipment->UniqueCheck($item_code, $equipment_name);
            return response()->json($isUnique);
        }
    }
    public function Equipmentunique(Request $request)
    {
        if ($request->ajax()) {
            $equipment_name = decryptId($request->equipment_name);
            $isUnique = $this->safety_equipment->EquipmentUniqueCheck($equipment_name);
            return response()->json($isUnique);
        }
    }

    public function GeneralExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $inspection = $this->safety_equipment->selectOne($id);
            $images = $this->monthly_inspection_file_upload->GetFile($inspection->id);
            $document_no = $this->document_reference->selectOne($inspection->document_reference_id);
            $inspection_data = json_decode($inspection->inspected_data, true);
            $titleRow = 1;

            foreach (range('A', 'L') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

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

            $sheet->mergeCells('A1:C3');
            $sheet->mergeCells('D1:J3');
            $sheet->setCellValue('D1', 'Fire Equipment Monthly Physical Inspection PN INTERNATIONAL PVT. LTD.');
            $sheet->getStyle('D1:J3')->applyFromArray([
                'font'      => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $sheet->getStyle('A1:C3')->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $sheet->setCellValue('K1', 'Doc. No.');
            $sheet->setCellValue('L1', $document_no->doc_no ?? '');

            $sheet->setCellValue('K2', 'Issue Dt.');
            $sheet->setCellValue('L2', Displaydateformat($document_no->issue_date));

            $sheet->setCellValue('K3', 'Rev. & Dt.');
            $sheet->setCellValue('L3', $document_no->rev_dt ?? '');

            $sheet->getStyle('K1:L3')->applyFromArray([
                'font'      => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
            ]);


            $sheet->mergeCells('A4:D4')->setCellValue('A4', 'Date of Inspection:- ' . Displaydateformat($inspection->date_of_inspection));
            $sheet->mergeCells('E4:H4')->setCellValue('E4', 'Location :- ' . getLocationname($inspection->location));
            $sheet->mergeCells('I4:L4')->setCellValue('I4', 'Unit:- ' . getUnitname($inspection->unit));

            $sheet->getStyle('A4:L4')->applyFromArray([
                'font'      => ['bold' => true],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);


            $sheet->mergeCells('A5:A5')->setCellValue('A5', 'SR.NO');
            $sheet->mergeCells('B5:D5')->setCellValue('B5', 'EQUIPMENT NAME');
            $sheet->mergeCells('E5:F5')->setCellValue('E5', 'FREQUENCY');
            $sheet->mergeCells('G5:H5')->setCellValue('G5', 'STATUS');
            $sheet->mergeCells('I5:L5')->setCellValue('I5', 'REMARKS');

            $sheet->getStyle('A5:L5')->applyFromArray([
                'font'      => ['bold' => true],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $row = 6;
            $sr  = 1;

            foreach ($inspection_data as $index => $detail) {
                $sheet->mergeCells("B$row:D$row");
                $sheet->mergeCells("E$row:F$row");
                $sheet->mergeCells("G$row:H$row");
                $sheet->mergeCells("I$row:L$row");

                $sheet->setCellValue("A$row", $sr);
                $sheet->setCellValue("B$row", getMonthlyInspectionEquipmentname($detail['id']) ?? '');
                $sheet->setCellValue("E$row", getFrequencyname($detail['frequency']) ?? '');
                $sheet->setCellValue("G$row", $detail['status'] ?? '');
                $sheet->setCellValue("I$row", $detail['remarks'] ?? '');

                $sheet->getStyle("A$row:L$row")->applyFromArray([
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sr++;
                $row++;
            }

            foreach ($images as $index => $imageGroup) {
                $sheet->mergeCells("A$row:L$row");
                $sheet->setCellValue("A$row", getMonthlyInspectionEquipmentname($index));
                $sheet->getStyle("A$row")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FF0000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $row++;

                $sheet->getRowDimension($row)->setRowHeight(100);

                $columns = ['B', 'G'];

                foreach ($imageGroup as $i => $img) {
                    if ($i > 1) break;

                    $imagePath = ($img->file_path);
                    if (file_exists($imagePath)) {
                        $drawing = new Drawing();
                        $drawing->setName('Signature');
                        $drawing->setDescription('Signature Upload');
                        $drawing->setPath($imagePath);
                        $drawing->setHeight(90);
                        $drawing->setCoordinates($columns[$i] . $row);
                        $drawing->setOffsetX(10);
                        $drawing->setOffsetY(15);
                        $drawing->setWorksheet($sheet);
                    }

                    $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([

                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => [
                                'argb' => 'FFFFFFFF', // white background
                            ],
                        ],
                    ]);
                }

                $row++;
            }

            $sheet->getStyle("A{$titleRow}:L{$row}")->applyFromArray([
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ]);




            $writer   = new Xlsx($spreadsheet);
            $fileName = 'Fire Equipment Monthly Physical Inspection Report.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }
}
