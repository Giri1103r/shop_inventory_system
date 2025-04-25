<?php

namespace App\Http\Controllers\Inspection\Safety;

use Exception;
use App\Models\UploadLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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
use App\Models\Inspection\Safety\Master\Equipment;
use App\Models\Inspection\Safety\FireSafetyEquipment;
use App\Models\Inspection\Safety\FireSafetyEquipmentDetails;

class FireSafetyEquipmentController extends Controller
{
    private $safety_equipment;
    private $safety_equipment_details;
    private $equipment;
    private $document_reference;


    public function __construct()
    {
        $this->safety_equipment = new FireSafetyEquipment();
        $this->equipment = new Equipment();
        $this->safety_equipment_details = new FireSafetyEquipmentDetails();
        $this->document_reference = new InspectionStaticDocno();
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
                            $btn = '<a href="' . admin_url('safety/fire-safety-equipment/view/' . encryptId($row->inspection_id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            //         $btn .= '<a href="' . admin_url('safety/fire-safety-equipment/exportViewPdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                            //     <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                            // </a>';
                            return $btn;
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
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
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'inspection_status', 'issue_date', 'standard_norms'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {
                    report($ex);
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }

        $equipment = $this->equipment->get();

        $data = array(
            'equipment' => $equipment,
        );
        return view('inspection.Safety.safety_equipment.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $equipment = $this->equipment->get();
            $document_no = $this->document_reference->selectUsingName('ListofFireSafetyEquipment');

            $data = array(
                'equipment' => $equipment,
                'document_no' => $document_no,
            );
            return view('inspection.Safety.safety_equipment.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/fire-safety-equipment/list'));
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'issue_date' => 'required',
                'equipment_name.*' => 'required',
                'item_code.*' => 'required',
                'standard_norms.*' => 'required',
                'equipment_category.*' => 'required',
                'unit_of_measurement.*' => 'required',
                'minimum_order_value.*' => 'required',
                'economic_order_quantity.*' => 'required',
                'observation_status.*' => 'required',
                'remarks.*' => 'required',
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
                'equipment_name.*.required' => 'Equipment Name is required.',
                'item_code.*.required' => 'Item code  is required.',
                'standard_norms.*.required' => 'Standard Norms is required.',
                'equipment_category.*.required' => 'Equipment Category is required.',
                'unit_of_measurement.*.required' => 'Unit of measurement is required.',
                'minimum_order_value.*.required' => 'Minimum order value is required.',
                'economic_order_quantity.*.required' => 'Economic Order Quantity is required.',
                'observation_status.*.required' => 'Observation Status is required.',
                'remarks.*.required' => 'Remarks is required.',
                'signature_upload' => 'Signature is required.',
            ];


            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $fire_safety_equipment_store = $this->safety_equipment->store();
            Session::flash('success', 'Equipment Name is Added Successfully');
            return redirect(admin_url('safety/fire-safety-equipment/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/fire-safety-equipment/list'));
        }
    }

    public function GetEquipment(Request $request)
    {
        try {
            $locations = $this->equipment->GetEquipment();
            return response()->json($locations);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['error' => 'Please try again after sometimes'], 406);
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->safety_equipment->selectOne($id);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);

            $data = array(
                'details' => $inspection_details,
                'document_no' => $document_no,
            );

            return view('inspection.Safety.safety_equipment.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/fire-safety-equipment/list'));
        }
    }

    public function ExportExcel(Request $request)
    {

        try {
            $allData = $this->safety_equipment->exportdata();
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $row = 1;
            $startRow = $row;
            $firstData = $allData->first();
            $document_no = $this->document_reference->selectOne($firstData->document_reference_id);

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
            $sheet->setCellValue("D{$row}", 'List of Fire Safety & Rescue Equipment PN INTERNATIONAL PVT. LTD.');
            $sheet->getStyle("D{$row}:H" . ($row + 2))->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $labelMapStart = $row;
            $labelMap = [
                ["I{$labelMapStart}", 'Doc. No.', $document_no->doc_no],
                ["I" . ($labelMapStart + 1), 'Issue Dt.', Displaydateformat($document_no->issue_date)],
                ["I" . ($labelMapStart + 2), 'Rev. & Dt.', $document_no->rev_dt],
            ];

            foreach ($labelMap as [$labelCell, $label, $data]) {
                $dataCell = str_replace('I', 'J', $labelCell);
                $sheet->setCellValue($labelCell, $label);
                $sheet->setCellValue($dataCell, $data);

                $sheet->getStyle("{$labelCell}:{$dataCell}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'wrapText' => true,
                ]);
            }

            $row += 3;
            $headers = [
                'A' => 'SR.NO',
                'B' => 'EQUIPMENT NAME',
                'C' => 'ITEM/CODE',
                'D' => 'STANDARD/NORMS',
                'E' => 'EQUIPMENT CATEGORY',
                'F' => 'UNIT OF MEASUREMENT',
                'G' => 'MINIMUM ORDER LEVEL(MOL)',
                'H' => 'ECONOMIC ORDER QUANTITY(EOQ)',
                'I' => 'STATUS',
                'J' => 'REMARK',
            ];
            foreach ($headers as $col => $text) {
                $sheet->setCellValue("{$col}{$row}", $text);
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'wrapText' => true,
            ]);
            $sheet->getRowDimension($row)->setRowHeight(40);
            $row++;

            $sr = 1;
            foreach ($allData as $detail) {
                $sheet->setCellValue("A{$row}", $sr++);
                $sheet->setCellValue("B{$row}", getEquipmentName($detail['equipment_id'] ?? ''));
                $sheet->setCellValue("C{$row}", ($detail['item_code'] ?? ''));
                $sheet->setCellValue("D{$row}", $detail['standard_norms'] ?? '');
                $sheet->setCellValue("E{$row}", $detail['equipment_category'] ?? '');
                $sheet->setCellValue("F{$row}", $detail['measurement_unit'] ?? '');
                $sheet->setCellValue("G{$row}", ($detail['minimum_order_level'] ?? ''));
                $sheet->setCellValue("H{$row}", ($detail['economic_order_quantity'] ?? ''));
                $status = ($detail['observation_status'] ?? '') == 1 ? 'Active' : 'Inactive';
                $sheet->setCellValue("I{$row}", $status);
                $sheet->setCellValue("J{$row}", $detail['remark'] ?? '');

                $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'wrapText' => true,
                ]);
                $sheet->getRowDimension($row)->setRowHeight(-1);
                $row++;
            }

            $fileName = 'List of Fire Safety Equipment.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer = new Xlsx($spreadsheet);
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('safety/fire-safety-equipment/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->safety_equipment->exportdata();
            $document_no = $this->document_reference->selectUsingName('ListofFireSafetyEquipment');

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $data = array(
                'content' => $allData,
                'document_no' => $document_no,
                'pagetitle' => "Safety Equipment Details",
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

            $view = view('inspection.safety.safety_equipment.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Safety Equipment Details.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('safety/fire-safety-equipment/list'));
        }
    }


    public function exportViewPdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $inspection_details = $this->safety_equipment->selectOne($id);
                $inspection = $this->safety_equipment_details->GetDetails($inspection_details->id);
                $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);

                $data = [
                    'inspection_details' => $inspection_details,
                    'inspection' => $inspection,
                    'pagetitle' => "Safety Equipment List",
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

            $html = view('inspection.Safety.safety_equipment.viewPdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Safety Equipment List.pdf";
            return $mpdf->Output($filename, 'i');
        } catch (Exception $ex) {
            report($ex);
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/fire-safety-equipment/list'));
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
}
