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
use App\Models\Inspection\Fire\DailyFireHouseInspection;
use App\Models\Inspection\Master\ChecklistType;
use App\Models\Inspection\Master\ChecklistSubType;
use App\Models\Inspection\Master\ChecklistSubTypeDataName;
use App\Models\Inspection\Master\ChecklistSubTypeData;
use App\Models\Inspection\Master\ChecklistOptionType;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Master\Unit;
use App\Models\Inspection\Fire\FireSignatureUpload;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class FirePumpHouseController extends Controller
{

    private $checklist_type;
    private $checklist_subtype;
    private $checklist_subtypedata;
    private $checklist_subtypename;
    private $dailyFire;
    private $upload_log;
    private $checklist_option;
    private $shift;
    private $static_docno;
    private $unit;
    private $signature;

    public function __construct()
    {
        $this->dailyFire = new DailyFireHouseInspection();
        $this->checklist_type = new ChecklistType();
        $this->checklist_subtype = new ChecklistSubType();
        $this->checklist_option = new ChecklistOptionType();
        $this->checklist_option = new ChecklistOptionType();
        $this->checklist_subtypename = new ChecklistSubTypeDataName();
        $this->checklist_subtypedata = new ChecklistSubTypeData();
        $this->shift = new Shift();
        $this->static_docno = new InspectionStaticDocno();
        $this->unit = new Unit();
        $this->signature = new FireSignatureUpload();
    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =    $this->dailyFire->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {

                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->fire_id) . "' data-type = '1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->fire_id) . "' data-type = '0'>In-Active</span>";
                            }
                            // }
                            return $text;
                        })
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
                            $btn = '<a href="' . admin_url('fire/daily-fire-pump-house-inspection/view/' . encryptId($row->fire_id)) . '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                            // $btn .= '<a href="' . admin_url('inspection/master/checklist-sub-type/edit/' . encryptId($row->id)) . '" class="edit-icon " title="' . __('common.edit') . '"><i class="fa-solid fa-pen-to-square"></i> ';
                            // }

                            $btn .= '<a href="' . admin_url('fire/daily-fire-pump-house-inspection/generalpdf/' . encryptId($row->fire_id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';
                            $btn .= '<a href="' . admin_url('fire/daily-fire-pump-house-inspection/generalExcel/' . encryptId($row->fire_id)) . '" style="margin-right: 5px;" title="Excel">
                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                     </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'date_of_inspection'])
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
        $checklist_types  = $this->checklist_type->select('id', 'category_name')->where('status', '1')->get();
        $shift  = $this->shift->select('id', 'shift')->where('status', '1')->get();
        $unit  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
        $data = array(
            'checklist_types' => $checklist_types,
            'shift' => $shift,
            'unit' => $unit,
        );

        return view('inspection.fire.firePumpHouse.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $checklist_types  = $this->checklist_type->select('id', 'category_name')->where('status', '1')->get();
            $shift  = $this->shift->select('id', 'shift')->where('status', '1')->get();
            $unit  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $checklist_details = getCheckListQuestion(CHECKLIST_FIRE_PUMP_HOUSE_INSECTION_CHECKLIST);
            $options =  getoption(CHECKLIST_FIRE_PUMP_HOUSE_INSECTION_CHECKLIST);
            $getoption = string_to_array($options->type);
            if (count($checklist_details) <= 0) {
                Session::flash('success', __('inspection.checklist_add'));
                return redirect(admin_url('inspection/master/checklist-sub-type-data/add'));
            }
            $staticDocno  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                ['type', "DailyFirePumpHouseChecklist"],
                ['status', '1']
            ])->first();
            $data = array(
                'checklist_types' => $checklist_types,
                'shift' => $shift,
                'checklist_details' => $checklist_details,
                'getoption' => $getoption,
                'staticDocno' => $staticDocno,
                'unit' => $unit,
            );
            return view('inspection.fire.firePumpHouse.add', $data);
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('fire/daily-fire-pump-house-inspection/list'));
        }
    }

    public function store(Request $request)
    {
        try {
            $inspection = $this->dailyFire->store();
            // $inspection_type = DAILY_FIRE_PUMP;
            // $id = $inspection->id;
            // $inspection_file = $this->signature->dailyFirePump($inspection_type, $id);

            Session::flash('success', __('Your data has been created successfully'));

            return redirect(admin_url('fire/daily-fire-pump-house-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('fire/daily-fire-pump-house-inspection/list'));
        }
    }
    public function view($id)
    {
        try {
            $id = decryptId($id);
            if (Auth::check()) {
                $checklist_details = getCheckListQuestion(CHECKLIST_FIRE_PUMP_HOUSE_INSECTION_CHECKLIST);
                $options =  getoption(CHECKLIST_FIRE_PUMP_HOUSE_INSECTION_CHECKLIST);
                $getoption = string_to_array($options->type);
                $dailyFire =   $this->dailyFire->selectOne($id);


                $data = array(
                    'dailyFire' => $dailyFire,
                    'checklist_details' => $checklist_details,
                    'getoption' => $getoption,
                );
            }
            return view('inspection.fire.firePumpHouse.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/daily-fire-pump-house-inspection/list'));
        }
    }

    public function statusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $this->dailyFire->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Daily Fire Pump House Inspection status changed'], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function exportExcel()
    {
        try {
            $allData = $this->dailyFire->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error', __('inspection.excess_error'));
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $row = 1;
            foreach ($allData as $details) {
                $currentRow = $row;
                $document_no = $this->static_docno->SelectOne($details->document_reference_id);
                $dailyFire = $this->dailyFire->selectOne($details->id);
                $CreatorSignature = GetSignature($details->created_by, $details->id, DAILY_FIRE_PUMP);

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
                    $sheet->getStyle("A$currentRow:F" . ($currentRow + 2))->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                }

                $sheet->mergeCells("G{$currentRow}:M" . ($currentRow + 2));
                $sheet->setCellValue("G{$currentRow}", "DAILY FIRE PUMP HOUSE INSPECTION CHECKLIST");
                $sheet->getStyle("G{$currentRow}:M{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->mergeCells("N$currentRow:P$currentRow")->setCellValue("N$currentRow", 'Doc. No.');
                $sheet->mergeCells("N" . ($currentRow + 1) . ":P" . ($currentRow + 1))->setCellValue("N" . ($currentRow + 1), 'Issue Dt.');
                $sheet->mergeCells("N" . ($currentRow + 2) . ":P" . ($currentRow + 2))->setCellValue("N" . ($currentRow + 2), 'Rev. & Dt.');
                $sheet->mergeCells("Q$currentRow:S$currentRow")->setCellValue("Q$currentRow", $document_no->doc_no);
                $sheet->mergeCells("Q" . ($currentRow + 1) . ":S" . ($currentRow + 1))->setCellValue("Q" . ($currentRow + 1), Displaydateformat($document_no->issue_date));
                $sheet->mergeCells("Q" . ($currentRow + 2) . ":S" . ($currentRow + 2))->setCellValue("Q" . ($currentRow + 2), $document_no->rev_dt);
                $sheet->getStyle("N$currentRow:S" . ($currentRow + 2))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'font' => ['bold' => true],
                ]);

                $sheet->mergeCells("A" . ($currentRow + 3) . ":G" . ($currentRow + 3));
                $richText1 = new RichText();
                $richText1->createTextRun(' DATE OF INSPECTION:- ')->getFont()->setBold(true);
                $richText1->createText(Displaydateformat($details->date_of_inspection));
                $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

                $sheet->mergeCells("H" . ($currentRow + 3) . ":N" . ($currentRow + 3));
                $richText2 = new RichText();
                $richText2->createTextRun('UNIT :-  ')->getFont()->setBold(true);
                $richText2->createText(getUnitname($details->unit_id));
                $sheet->getCell("H" . ($currentRow + 3))->setValue($richText2);

                $sheet->mergeCells("O" . ($currentRow + 3) . ":S" . ($currentRow + 3));
                $richText2 = new RichText();
                $richText2->createTextRun('SHIFT :-  ')->getFont()->setBold(true);
                $richText2->createText(getShift($details->shift_id));
                $sheet->getCell("O" . ($currentRow + 3))->setValue($richText2);

                $sheet->getStyle("A" . ($currentRow + 3) . ":S" . ($currentRow + 3))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $headerRow = $currentRow + 4;
                $sheet->mergeCells("A$headerRow:C$headerRow")->setCellValue("A$headerRow", "SERIAL NO");
                $sheet->mergeCells("D$headerRow:I$headerRow")->setCellValue("D$headerRow", "CHECK POINTS");
                $sheet->mergeCells("J$headerRow:L$headerRow")->setCellValue("J$headerRow", "PUMP NO");
                $sheet->mergeCells("M$headerRow:O$headerRow")->setCellValue("M$headerRow", "STATUS");
                $sheet->mergeCells("P$headerRow:S$headerRow")->setCellValue("P$headerRow", "REMARKS");
                $sheet->getStyle("A$headerRow:S$headerRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'font' => ['bold' => true],
                ]);

                $row = $headerRow + 1;
                $srNo = 1;
                $user_response = json_decode($details->checklist, true);
                $groupedBySubtype = [];

                foreach ($user_response as $checkpointId => $data) {
                    $subTypeId = $data['sub_type_id'] ?? 'Unknown';
                    $groupedBySubtype[$subTypeId][$checkpointId] = $data;
                }

                foreach ($groupedBySubtype as $subTypeId => $checkpoints) {
                    $rowCount = count($checkpoints);
                    $firstRow = true;
                    foreach ($checkpoints as $checkpointId => $checkpoint) {
                        $checkItem = getSubcategoryDataname($checkpointId);
                        $pump = $checkpoint['pump_no'] ?? '';
                        $status = $checkpoint['response'] ?? '';
                        $remark = $checkpoint['remarks'] ?? '';

                        if ($firstRow) {
                            $sheet->mergeCells("A$row:C" . ($row + $rowCount - 1))->setCellValue("A$row", $srNo++);
                            $sheet->getStyle("A$row:C" . ($row + $rowCount - 1))->applyFromArray([
                                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                                'font' => ['bold' => true],
                            ]);
                            $firstRow = false;
                        }

                        $sheet->mergeCells("D$row:I$row")->setCellValue("D$row", $checkItem);
                        $sheet->mergeCells("J$row:L$row")->setCellValue("J$row", $pump);
                        $statusSymbol = $status === 'YES' ? '✓' : (($status === 'NO' || $status === 'N/A') ? 'X' : '-');
                        $sheet->mergeCells("M$row:O$row")->setCellValue("M$row", $statusSymbol);
                        $sheet->mergeCells("P$row:S$row")->setCellValue("P$row", $remark ? $remark : '-');

                        $sheet->getStyle("D$row:S$row")->applyFromArray([
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);

                        $row++;
                    }
                }

                $signatureStartRow = $row;
                $signatureEndRow = $signatureStartRow + 3;

                if ($details->created_by) {
                    $sheet->mergeCells("A$signatureStartRow:S$signatureStartRow");
                    // $drawing = new Drawing();
                    // $drawing->setName('Creator Signature');
                    // $drawing->setDescription('Creator Signature');
                    // $drawing->setPath($CreatorSignature);
                    // $drawing->setCoordinates("I$signatureStartRow");
                    // $drawing->setOffsetX(90);
                    // $drawing->setOffsetY(5);
                    // $drawing->setWidthAndHeight(120, 60);
                    // $drawing->setWorksheet($sheet);
                    $sheet->getRowDimension($signatureStartRow)->setRowHeight(80);
                    $sheet->getStyle("A$signatureStartRow:S$signatureStartRow")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                    $textRow = $signatureStartRow;
                    $sheet->mergeCells("A$textRow:S$textRow");
                    $sheet->setCellValue("A$textRow", "Requestor Name : " . getUserName($details->created_by));

                    $sheet->getStyle("A$textRow:S$textRow")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],
                        'font' => ['bold' => true],
                    ]);
                    $sheet->getRowDimension($textRow)->setRowHeight(25);
                }

                $noteRow = $textRow + 1;
                $note = $details->note;
                $sheet->mergeCells("A{$noteRow}:S{$noteRow}");
                $sheet->setCellValue("A{$noteRow}", "Note :- . $note");
                $sheet->getStyle("A{$noteRow}:S{$noteRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'font' => ['bold' => true],
                ]);
                $sheet->getRowDimension($textRow)->setRowHeight(25);


                $row = $noteRow + 6;
            }

            $fileName = 'Daily Fire Pump House Inspection.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/daily-fire-pump-house-inspection/list'));
        }
    }


    public function exportPDF()
    {
        try {

            ini_set("pcre.backtrack_limit", "5000000");

            $allData =   $this->dailyFire->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }
            foreach ($allData as $details) {
                $document_no = $this->static_docno->SelectOne($details->document_reference_id);
            }


            $data = array(

                'content' => $allData,
                'document_no' => $document_no,
                'pagetitle' => "Daily Fire Pump House Inspection",
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

            $view = view('inspection.fire.firePumpHouse.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Daily Fire Pump House Inspectiony.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/daily-fire-pump-house-inspection/list'));
        }
    }

    public function generalpdf($id)
    {
        try {
            $id = decryptId($id);
            if (Auth::check()) {
                $dailyFire =   $this->dailyFire->selectDataForPdf($id);
                $document_no = $this->static_docno->selectOne($dailyFire->document_reference_id);
                $audit_assessmentCkeclist = json_decode($dailyFire);
                $checklist_details = getCheckListQuestion(CHECKLIST_FIRE_PUMP_HOUSE_INSECTION_CHECKLIST);
                $options =  getoption(CHECKLIST_FIRE_PUMP_HOUSE_INSECTION_CHECKLIST);
                $getoption = string_to_array($options->type);
            }
            $data = [
                'dailyFire' => $dailyFire,
                'document_no' => $document_no,
                'getoption' => $getoption,
                'pagetitle' => "Daily Fire Pump House Inspection",
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

            $html = view('inspection.fire.firePumpHouse.viewpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "Daily Fire Pump House Inspection.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/daily-fire-pump-house-inspection/list'));
        }
    }


    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $dailyFire =   $this->dailyFire->selectOne($id);
            $document_no = $this->static_docno->selectOne($dailyFire->document_reference_id);



            $CreatorSignature = GetSignature($dailyFire->created_by, $id, DAILY_FIRE_PUMP);


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

            $sheet->mergeCells("G{$currentRow}:M" . ($currentRow + 2));
            $sheet->setCellValue("G{$currentRow}", "DAILY FIRE PUMP HOUSE INSPECTION CHECKLIST");
            $sheet->getStyle("G{$currentRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $sheet->mergeCells("N$currentRow:P$currentRow")->setCellValue("N$currentRow", 'Doc. No.');
            $sheet->mergeCells("N" . ($currentRow + 1) . ":P" . ($currentRow + 1))->setCellValue("N" . ($currentRow + 1), 'Issue Dt.');
            $sheet->mergeCells("N" . ($currentRow + 2) . ":P" . ($currentRow + 2))->setCellValue("N" . ($currentRow + 2), 'Rev. & Dt.');

            $sheet->mergeCells("Q$currentRow:S$currentRow")->setCellValue("Q$currentRow", $document_no->doc_no);
            $sheet->mergeCells("Q" . ($currentRow + 1) . ":S" . ($currentRow + 1))->setCellValue("Q" . ($currentRow + 1), Displaydateformat($document_no->issue_date));
            $sheet->mergeCells("Q" . ($currentRow + 2) . ":S" . ($currentRow + 2))->setCellValue("Q" . ($currentRow + 2), $document_no->rev_dt);

            $sheet->getStyle("N$currentRow:S" . ($currentRow + 2))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font' => ['bold' => true],
            ]);

            $sheet->mergeCells("A" . ($currentRow + 3) . ":G" . ($currentRow + 3));
            $richText1 = new RichText();
            $richText1->createTextRun(' DATE OF INSPECTION:- ')->getFont()->setBold(true);
            $richText1->createText(Displaydateformat($dailyFire->date_of_inspection));
            $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

            $sheet->mergeCells("H" . ($currentRow + 3) . ":N" . ($currentRow + 3));
            $richText2 = new RichText();
            $richText2->createTextRun('UNIT :-  ')->getFont()->setBold(true);
            $richText2->createText(getUnitname($dailyFire->unit_id));
            $sheet->getCell("H" . ($currentRow + 3))->setValue($richText2);

            $sheet->mergeCells("O" . ($currentRow + 3) . ":S" . ($currentRow + 3));
            $richText2 = new RichText();
            $richText2->createTextRun('SHIFT :-  ')->getFont()->setBold(true);
            $richText2->createText(getShift($dailyFire->shift_id));
            $sheet->getCell("O" . ($currentRow + 3))->setValue($richText2);

            $sheet->getStyle("A" . ($currentRow + 3) . ":S" . ($currentRow + 3))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);


            $headerRow = $currentRow + 4;

            $sheet->mergeCells("A$headerRow:C$headerRow")->setCellValue("A$headerRow", "SERIAL NO");
            $sheet->mergeCells("D$headerRow:I$headerRow")->setCellValue("D$headerRow", "CHECK POINTS");
            $sheet->mergeCells("J$headerRow:L$headerRow")->setCellValue("J$headerRow", "PUMP NO");
            $sheet->mergeCells("M$headerRow:O$headerRow")->setCellValue("M$headerRow", "STATUS");
            $sheet->mergeCells("P$headerRow:S$headerRow")->setCellValue("P$headerRow", "REMARKS");

            $sheet->getStyle("A$headerRow:S$headerRow")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font' => ['bold' => true],
            ]);

            $row = $headerRow + 1;
            $srNo = 1;

            $user_response = json_decode($dailyFire->checklist, true);
            $groupedBySubtype = [];

            foreach ($user_response as $checkpointId => $data) {
                $subTypeId = $data['sub_type_id'] ?? 'Unknown';
                $groupedBySubtype[$subTypeId][$checkpointId] = $data;
            }

            foreach ($groupedBySubtype as $subTypeId => $checkpoints) {
                $rowCount = count($checkpoints);
                $firstRow = true;

                foreach ($checkpoints as $checkpointId => $checkpoint) {
                    $checkItem = getSubcategoryDataname($checkpointId);
                    $pump = $checkpoint['pump_no'] ?? '';
                    $status = $checkpoint['response'] ?? '';
                    $remark = $checkpoint['remarks'] ?? '';

                    if ($firstRow) {
                        $sheet->mergeCells("A$row:C" . ($row + $rowCount - 1))
                            ->setCellValue("A$row", $srNo++);
                        $sheet->getStyle("A$row:C" . ($row + $rowCount - 1))->applyFromArray([
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                            'font' => ['bold' => true],
                        ]);
                        $firstRow = false;
                    }

                    $sheet->mergeCells("D$row:I$row")->setCellValue("D$row", $checkItem);

                    $sheet->mergeCells("J$row:L$row")->setCellValue("J$row", $pump);

                    $statusSymbol = $status === 'YES' ? '✓' : ($status === 'NO' || $status === 'N/A' ? 'X' : '-');
                    $sheet->mergeCells("M$row:O$row")->setCellValue("M$row", $statusSymbol);

                    $sheet->mergeCells("P$row:S$row")->setCellValue("P$row", $remark ? $remark : '-');

                    $sheet->getStyle("D$row:S$row")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ],
                    ]);

                    $row++;
                }
            }
            $signatureStartRow = $row;
            $signatureEndRow = $signatureStartRow + 3;

            if ($dailyFire->created_by) {
                $sheet->mergeCells("A$signatureStartRow:S$signatureStartRow");

                // $drawing = new Drawing();
                // $drawing->setName('Creator Signature');
                // $drawing->setDescription('Creator Signature');
                // $drawing->setPath($CreatorSignature);
                // $drawing->setCoordinates("I$signatureStartRow");
                // $drawing->setOffsetX(90);
                // $drawing->setOffsetY(5);
                // $drawing->setWidthAndHeight(120, 60);
                // $drawing->setWorksheet($sheet);



                $sheet->getStyle("A$signatureStartRow:S$signatureStartRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $textRow = $signatureStartRow;
                $sheet->mergeCells("A$textRow:S$textRow");

                $sheet->setCellValue("A$textRow", "Requestor Name : " . getUserName($dailyFire->created_by));

                $sheet->getStyle("A$textRow:S$textRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'font' => ['bold' => true],
                ]);

                $sheet->getRowDimension($textRow)->setRowHeight(25);
            }

            $noteRow = $signatureStartRow + 1;
            $note = $dailyFire->note;

            $sheet->mergeCells("A$noteRow:S$noteRow");
            $sheet->setCellValue("A$noteRow", "Note :-" . $note);

            $sheet->getStyle("A$noteRow:S$noteRow")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'font' => ['bold' => true],
            ]);

            $sheet->getRowDimension($noteRow)->setRowHeight(22);



            $fileName = 'Daily Fire Pump House Inspection.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/first-aid-box/daily-departmental/list'));
        }
    }
}
