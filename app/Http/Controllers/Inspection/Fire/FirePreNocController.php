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
use App\Models\Inspection\Fire\FirePreNocInspection;
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

class FirePreNocController extends Controller
{

    private $checklist_type;
    private $checklist_subtype;
    private $checklist_subtypedata;
    private $checklist_subtypename;
    private $fireNoc;
    private $upload_log;
    private $checklist_option;
    private $shift;
    private $static_docno;
    private $unit;
    private $signature;

    public function __construct()
    {
        $this->fireNoc = new FirePreNocInspection();
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
                    $data =    $this->fireNoc->list();
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
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('fire/pre-noc/checklist/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                            // $btn .= '<a href="' . admin_url('inspection/master/checklist-sub-type/edit/' . encryptId($row->id)) . '" class="edit-icon " title="' . __('common.edit') . '"><i class="fa-solid fa-pen-to-square"></i> ';
                            // }

                            $btn .= '<a href="' . admin_url('fire/pre-noc/checklist/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';
                            $btn .= '<a href="' . admin_url('fire/pre-noc/checklist/generalExcel/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="Excel">
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
        $checklist_types  = $this->checklist_type->select('id', 'category_name')->where('status', '1')->get();
        $data = array(
            'checklist_types' => $checklist_types,

        );
        return view('inspection.fire.firePreNoc.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $checklist_types  = $this->checklist_type->select('id', 'category_name')->where('status', '1')->get();
            $shift  = $this->shift->select('id', 'shift')->where('status', '1')->get();
            $unit  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $checklist_details = getCheckListQuestion(FIRE_PRE_NOC_CHECKLIST);
            $staticDocno  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                ['type', "FirePreNocChecklist"],
                ['status', '1']
            ])->first();
           if (count($checklist_details) <= 0) {

                Session::flash('success', __('inspection.checklist_add'));
                return redirect(admin_url('inspection/master/checklist-sub-type-data/add'));
            }
            $data = array(
                'checklist_types' => $checklist_types,
                'shift' => $shift,
                'checklist_details' => $checklist_details,
                'staticDocno' => $staticDocno,
                'unit' => $unit,
            );
            return view('inspection.fire.firePreNoc.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('fire/pre-noc/checklist/list'));
        }
    }

    public function store(Request $request)
    {

        try {
            try {
                $inspection = $this->fireNoc->store();
                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('fire/pre-noc/checklist/list'));
        } catch (Exception $ex) {
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('fire/pre-noc/checklist/list'));
        }
    }
    public function view($id)
    {
        try {
            $id = decryptId($id);
            if (Auth::check()) {
                $checklist_details = getCheckListQuestion(FIRE_PRE_NOC_CHECKLIST);
                $fireNoc =   $this->fireNoc->selectOne($id);


                $data = array(
                    'fireNoc' => $fireNoc,
                    'checklist_details' => $checklist_details,
                );
            }
            return view('inspection.fire.firePreNoc.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/pre-noc/checklist/list'));
        }
    }

    public function statusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $this->fireNoc->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Fire Pre Noc Checklist checklist status changed'], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function exportExcel()
    {
        try {

            $allData =   $this->fireNoc->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }
            $row = 1;
            $currentRow = $row;
            foreach ($allData as $details) {
                $currentRow = $row;
                $document_no =   $this->static_docno->selectOne($details->document_reference_id);
                $checklist_details = getCheckListQuestion(FIRE_PRE_NOC_CHECKLIST);
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

                // Title Section
                $sheet->mergeCells("G{$currentRow}:M" . ($currentRow + 2));
                $sheet->setCellValue("G{$currentRow}", "अग्नि अनापत्ति प्रमाण पत्र जांच-सूची(Fire Pre-Noc Checklist)");
                $sheet->getStyle("G{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);


                // document number


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

                // Review Dates
                $sheet->mergeCells("A" . ($currentRow + 3) . ":S" . ($currentRow + 3));
                $richText1 = new RichText();
                $richText1->createTextRun('ब्लाक आधारित विवरण (Block based statement):- ')->getFont()->setBold(true);
                $richText1->createText(($details->block_based_statement));
                $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

                $sheet->mergeCells("A" . ($currentRow + 4) . ":S" . ($currentRow + 4));
                $richText2 = new RichText();
                $richText2->createTextRun('ब्लाक(Block) :- :-  ')->getFont()->setBold(true);
                $richText2->createText(($details->block));
                $sheet->getCell("A" . ($currentRow + 4))->setValue($richText2);



                $sheet->getStyle("A" . ($currentRow + 3) . ":S" . ($currentRow + 3))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle("A" . ($currentRow + 4) . ":S" . ($currentRow + 4))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);




                $headerRow = $currentRow + 5;

                $sheet->mergeCells("A{$headerRow}:C{$headerRow}")->setCellValue("A{$headerRow}", "क्रमांक (Serial Number)");
                $sheet->mergeCells("D{$headerRow}:J{$headerRow}")->setCellValue("D{$headerRow}", "जाँच बिंदु (Check Point)");
                $sheet->mergeCells("K{$headerRow}:S{$headerRow}")->setCellValue("K{$headerRow}", "विवरण (Detail)");
                $sheet->getStyle("A$headerRow:S$headerRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'font' => ['bold' => true],
                ]);

                $inspectionRow = $headerRow + 1;
                $srNo = 1;
                $displayedSections = [];

                $user_response = json_decode($details->checklist, true);

                foreach ($user_response as $checklistId => $data) {
                    $sectionName = GetSubChecklistTypeName($data['sub_type_id']);

                    // Section header (merged row across all 18 columns)
                    if (!in_array($sectionName, $displayedSections)) {
                        $sheet->mergeCells("A{$inspectionRow}:S{$inspectionRow}")
                            ->setCellValue("A{$inspectionRow}", $sectionName);
                        $sheet->getStyle("A{$inspectionRow}")->applyFromArray([
                            'font' => ['bold' => true],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFF5F5F5']
                            ],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
                        ]);
                        $inspectionRow++;
                        $displayedSections[] = $sectionName;
                    }

                    // Data Row
                    $sheet->mergeCells("A{$inspectionRow}:C{$inspectionRow}")->setCellValue("A{$inspectionRow}", $srNo);
                    $sheet->mergeCells("D{$inspectionRow}:J{$inspectionRow}")->setCellValue("D{$inspectionRow}", GetChecklistTypeDate($checklistId));
                    $sheet->mergeCells("K{$inspectionRow}:S{$inspectionRow}")->setCellValue("K{$inspectionRow}", $data['remarks'] ?? '-');

                    // Style borders
                    $sheet->getStyle("A{$inspectionRow}:R{$inspectionRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $srNo++;
                    $sheet->getStyle("A$inspectionRow:S$inspectionRow")->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ],
                        ],
                    ]);

                    $inspectionRow++;
                }

                $row =  $inspectionRow + 2;
            }
            $fileName = 'Fire Pre-Noc Checklist.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/pre-noc/checklist/list'));
        }
    }

    public function exportPDF()
    {
        try {

            ini_set("pcre.backtrack_limit", "5000000");

            $allData =   $this->fireNoc->exportdata();
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
                'pagetitle' => "Fire Pre Noc Checklist",
            );

            $property = [
                'tempDir' => 'public/pdf/temp/',
                // 'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'fontDir' => array_merge((new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'], [
                    public_path('assets/fonts/Noto_Sans_Devanagari'),
                ]),
                'fontdata' => array_merge((new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'], [
                    'NotoSansDevanagari' => [
                        'R' => 'NotoSansDevanagari-Regular.ttf',
                        'B' => 'NotoSansDevanagari-Bold.ttf',
                    ],
                ]),
                'default_font' => 'NotoSansDevanagari',

            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $view = view('inspection.fire.firePreNoc.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Fire Pre Noc Checklist.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/pre-noc/checklist/list'));
        }
    }



    public function generalpdf($id)
    {
        try {


            $id = decryptId($id);
            if (Auth::check()) {

                $fireNoc =   $this->fireNoc->selectOne($id);
                $document_no =   $this->static_docno->selectOne($fireNoc->document_reference_id);
                $checklist_details = getCheckListQuestion(FIRE_PRE_NOC_CHECKLIST);
            }
            $data = [
                'fireNoc' => $fireNoc,
                'document_no' => $document_no,
                'pagetitle' => "Fire Pre-Noc Checklist",
            ];

            $property = [
                'tempDir' => 'public/pdf/temp/',
                // 'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'fontDir' => array_merge((new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'], [
                    public_path('assets/fonts/Noto_Sans_Devanagari'),
                ]),
                'fontdata' => array_merge((new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'], [
                    'NotoSansDevanagari' => [
                        'R' => 'NotoSansDevanagari-Regular.ttf',
                        'B' => 'NotoSansDevanagari-Bold.ttf',
                    ],
                ]),
                'default_font' => 'NotoSansDevanagari',

            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $html = view('inspection.fire.firePreNoc.viewpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "Fire Pre-Noc Checklist.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/pre-noc/checklist/list'));
        }
    }

    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $fireNoc =   $this->fireNoc->selectOne($id);
            $document_no =   $this->static_docno->selectOne($fireNoc->document_reference_id);
            $checklist_details = getCheckListQuestion(FIRE_PRE_NOC_CHECKLIST);

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

            // Title Section
            $sheet->mergeCells("G{$currentRow}:M" . ($currentRow + 2));
            $sheet->setCellValue("G{$currentRow}", "अग्नि अनापत्ति प्रमाण पत्र जांच-सूची(Fire Pre-Noc Checklist)");
            $sheet->getStyle("G{$currentRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);


            // document number


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

            // Review Dates
            $sheet->mergeCells("A" . ($currentRow + 3) . ":S" . ($currentRow + 3));
            $richText1 = new RichText();
            $richText1->createTextRun('ब्लाक आधारित विवरण (Block based statement):- ')->getFont()->setBold(true);
            $richText1->createText(($fireNoc->block_based_statement));
            $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

            $sheet->mergeCells("A" . ($currentRow + 4) . ":S" . ($currentRow + 4));
            $richText2 = new RichText();
            $richText2->createTextRun('ब्लाक(Block) :-  ')->getFont()->setBold(true);
            $richText2->createText(($fireNoc->block));
            $sheet->getCell("A" . ($currentRow + 4))->setValue($richText2);



            $sheet->getStyle("A" . ($currentRow + 3) . ":S" . ($currentRow + 3))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle("A" . ($currentRow + 4) . ":S" . ($currentRow + 4))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);




            $headerRow = $currentRow + 5;

            $sheet->mergeCells("A{$headerRow}:C{$headerRow}")->setCellValue("A{$headerRow}", "क्रमांक (Serial Number)");
            $sheet->mergeCells("D{$headerRow}:J{$headerRow}")->setCellValue("D{$headerRow}", "जाँच बिंदु (Check Point)");
            $sheet->mergeCells("K{$headerRow}:S{$headerRow}")->setCellValue("K{$headerRow}", "विवरण (Detail)");
            $sheet->getStyle("A$headerRow:S$headerRow")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font' => ['bold' => true],
            ]);

            $inspectionRow = $headerRow + 1;
            $srNo = 1;
            $displayedSections = [];

            $user_response = json_decode($fireNoc->checklist, true);

            foreach ($user_response as $checklistId => $data) {
                $sectionName = GetSubChecklistTypeName($data['sub_type_id']);

                // Section header (merged row across all 18 columns)
                if (!in_array($sectionName, $displayedSections)) {
                    $sheet->mergeCells("A{$inspectionRow}:S{$inspectionRow}")
                        ->setCellValue("A{$inspectionRow}", $sectionName);
                    $sheet->getStyle("A{$inspectionRow}")->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFF5F5F5']
                        ],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
                    ]);
                    $inspectionRow++;
                    $displayedSections[] = $sectionName;
                }

                // Data Row
                $sheet->mergeCells("A{$inspectionRow}:C{$inspectionRow}")->setCellValue("A{$inspectionRow}", $srNo);
                $sheet->mergeCells("D{$inspectionRow}:J{$inspectionRow}")->setCellValue("D{$inspectionRow}", GetChecklistTypeDate($checklistId));
                $sheet->mergeCells("K{$inspectionRow}:S{$inspectionRow}")->setCellValue("K{$inspectionRow}", $data['remarks'] ?? '-');

                // Style borders
                $sheet->getStyle("A{$inspectionRow}:R{$inspectionRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $srNo++;
                $sheet->getStyle("A$inspectionRow:S$inspectionRow")->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                $inspectionRow++;
            }

            $fileName = 'Fire Pre-Noc Checklist.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('fire/pre-noc/checklist/list'));
        }
    }
}
