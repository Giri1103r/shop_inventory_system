<?php

namespace App\Http\Controllers\Inspection\Ohc;

use Exception;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Ohc\FirstAidRecordDetails;
use App\Models\OhcManagement\Master\FirstAidLocation;
use App\Models\Inspection\Ohc\FirstAidRecordChecklist;
use App\Models\Inspection\Ohc\FirstAidRecordStatusLog;
use App\Models\Inspection\Ohc\FirstAidRecordSignatureUpload;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class FirstAidRecordController extends Controller
{
    private $first_aid_details;
    private $first_aid_checklist;
    private $unit;
    private $firstAidLocation;
    private $document_reference;

    public function __construct()
    {
        $this->first_aid_details = new FirstAidRecordDetails();
        $this->first_aid_checklist = new FirstAidRecordChecklist();
        $this->unit = new Unit();
        $this->firstAidLocation = new FirstAidLocation();
        $this->document_reference = new InspectionStaticDocno();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->first_aid_details->list();
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
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('ohc/first-aid-record/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('ohc/first-aid-record/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                                    </a>';
                            $btn .= '<a href="' . admin_url('ohc/first-aid-record/generalexcel/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                                    </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'issue_date', 'created_by', 'status'])
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

        $data = [];

        return view('inspection.inspection_ohc.first_aid_record.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $document_no = $this->document_reference->selectUsingName('FirstAidRecord');
            $data = [
                'unit' => $unit,
                'document_no' => $document_no,
            ];
            return view('inspection.inspection_ohc.first_aid_record.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }
    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $month = $request->month;
            $year = $request->year;
            $id = $request->id;

            if ($id == '') {
                $record = $this->first_aid_details->uniqueCheck($month, $year);
            } else {
                $id = decryptId($id);
                $record = $this->first_aid_details->ExistuniqueCheck($month, $year, $id);
            }

            if ($record->count()) {
                return Response::json(false);
            }

            return Response::json(true);
        }
    }

    public function getFirstAidDetails(Request $request)
    {
        $unitId = decryptId($request->unit_id);
        $departmentId = decryptId($request->department_id);

        $firstAidLocation = $this->firstAidLocation->getDetail($unitId,$departmentId);

        if ($firstAidLocation) {
            return response()->json([
                'station_number' => $firstAidLocation->station_number,
                'first_aid_box_no' => $firstAidLocation->first_aid_box_no,
            ]);
        } else {
            return response()->json([
                'station_number' => '',
                'first_aid_box_no' => '',
            ]);
        }
    }

    public function Store(Request $request)
    {
        try {

            $first_aid_details = $this->first_aid_details->store();
            $first_aid_detail_id = $first_aid_details->id;
            $this->first_aid_checklist->store($first_aid_detail_id);

            Session::flash('success', __('Your data has been created successfully'));
            return redirect(admin_url('ohc/first-aid-record/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/first-aid-record/list'));
        }
    }

    public function StatusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->first_aid_details->statuschange($id);

            $this->first_aid_checklist->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Your status  has changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $first_aid_details = $this->first_aid_details->find($id);

                $first_aid_checklist = $this->first_aid_checklist->selectOne($id);
                $document_no = $this->document_reference->selectOne($first_aid_details->document_reference_id);

                $data = array(
                    'first_aid_details' => $first_aid_details,
                    'first_aid_checklist' => $first_aid_checklist  ?? [],
                    'document_no' => $document_no,
                );
            }
            return view('inspection.inspection_ohc.first_aid_record.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->first_aid_details->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $columnWidths = [
                'A' => 8, 'B' => 12, 'C' => 16, 'D' => 16,
                'E' => 25, 'F' => 25, 'G' => 25, 'H' => 18, 'I' => 18
            ];
            foreach ($columnWidths as $col => $width) {
                $sheet->getColumnDimension($col)->setWidth($width);
            }

            for ($i = 1; $i <= 1000; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(22);
            }

            $row = 1;

            foreach ($allData as $groupId => $checklistGroup) {
                $firstItem = $checklistGroup->first();

                $document_no = $this->document_reference->selectUsingName('FirstAidRecord');

                $sheet->mergeCells("A{$row}:B" . ($row + 2));
                $logoPath = public_path('assets/images/logo-dark.png');
                if (file_exists($logoPath)) {
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setName('Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates("A{$row}");
                    $drawing->setOffsetX(10);
                    $drawing->setOffsetY(5);
                    $drawing->setWidth(60);
                    $drawing->setHeight(50);
                    $drawing->setWorksheet($sheet);
                }

                $sheet->mergeCells("C{$row}:F" . ($row + 2))->setCellValue("C{$row}", "OCCUPATIONAL HEALTH CENTER FIRST AID RECORD\nPN INTERNATIONAL PVT LTD");
                $sheet->getStyle("C{$row}:F" . ($row + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
                ]);

                $sheet->mergeCells("G{$row}:G" . ($row + 2));

                $logoRightPath = public_path('assets/images/plus-image.webp');
                if (file_exists($logoRightPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Right Logo');
                    $drawing->setPath($logoRightPath);
                    $drawing->setCoordinates("G{$row}");

                    $drawing->setOffsetX(50);
                    $drawing->setOffsetY(15);
                    $drawing->setWidth(60);
                    $drawing->setHeight(60);
                    $drawing->setWorksheet($sheet);

                    $sheet->getStyle("G{$row}:G" . ($row + 2))->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                }

                $sheet->mergeCells("H{$row}:H{$row}")->setCellValue("H{$row}", "Doc. No.");
                $sheet->setCellValue("I{$row}", $document_no->doc_no ?? '-');

                $sheet->mergeCells("H" . ($row + 1) . ":H" . ($row + 1))->setCellValue("H" . ($row + 1), "Issue Dt.");
                $sheet->setCellValue("I" . ($row + 1), Displaydateformat($document_no->issue_date ?? ''));

                $sheet->mergeCells("H" . ($row + 2) . ":H" . ($row + 2))->setCellValue("H" . ($row + 2), "Rev. & Dt.");
                $sheet->setCellValue("I" . ($row + 2), $document_no->rev_dt ?? '-');

                $sheet->getStyle("H{$row}:I" . ($row + 2))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                $sheet->getStyle("H{$row}:H" . ($row + 2))->getFont()->setBold(true);

                $sheet->getStyle("A{$row}:I" . ($row + 2))->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['argb' => '000000']
                        ]
                    ]
                ]);

                $row += 3;

                $sheet->fromArray([
                    "Sr No.", "Month", "Department", "Unit",
                    "First Aid Station Number", "First Aid Box Number",
                    "Total Number of First Aid", "Remark"
                ], null, "A{$row}");
                $sheet->mergeCells("H{$row}:I{$row}");
                $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(30);
                $row++;

                $srNo = 1;
                $month = $firstItem->month ?? '-';
                $total = 0;

                foreach ($checklistGroup as $details) {
                    $sheet->fromArray([
                        $srNo,
                        $month,
                        getDepartment($details->department),
                        getUnitname($details->unit),
                        $details->first_aid_station_number ?? '-',
                        $details->first_aid_box_number ?? '-',
                        $details->total_number_of_first_aid ?? '-',
                        $details->remark ?? '-'
                    ], null, "A{$row}");

                    $sheet->mergeCells("H{$row}:I{$row}");
                    $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    ]);

                    $total += intval($details->total_number_of_first_aid ?? 0);

                    $srNo++;
                    $row++;
                }

                $sheet->mergeCells("A{$row}:F{$row}")->setCellValue("A{$row}", "Total Number of First Aid");
                $sheet->mergeCells("G{$row}:I{$row}")->setCellValue("G{$row}", $total);

                $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                $blockStart = $row - $srNo;
                $sheet->getStyle("A{$blockStart}:I{$row}")->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['argb' => '000000']
                        ]
                    ]
                ]);

                $row += 4;
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'First Aid Record.xlsx';
            $filePath = storage_path("app/public/{$fileName}");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);

        } catch (\Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong! Please try again.');
            return redirect(admin_url('ohc/first-aid-record/list'));
        }
    }


    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->first_aid_details->exportdata();
            $document_no = $this->document_reference->selectUsingName('FirstAidRecord');


            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }elseif(count($allData) > 20){
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }

            $data = array(
                'content' => $allData,
                'document_no' => $document_no,
                'pagetitle' => "First Aid Record Details",
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

            $view = view('inspection.inspection_ohc.first_aid_record.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "First Aid Record.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aid-record/list'));
        }
    }

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $first_aid_details = $this->first_aid_details->find($id);
                $first_aid_checklist = $this->first_aid_checklist->selectOne($id);
                $document_no = $this->document_reference->selectUsingName('FirstAidRecord');

                $data = array(
                    'first_aid_details' => $first_aid_details,
                    'first_aid_checklist' => $first_aid_checklist  ?? [],
                    'document_no' => $document_no,
                    'pagetitle' => "OHC FIRST AID RECORD",
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

            $html = view('inspection.inspection_ohc.first_aid_record.generalpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "First Aid Record.pdf";
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

            $first_aid_details = $this->first_aid_details->selectOne($id);
            $first_aid_checklist = $this->first_aid_checklist->selectOne($id);
            $document_no = $this->document_reference->selectUsingName('FirstAidRecord');

            $columnWidths = [
                'A' => 8, 'B' => 12, 'C' => 16, 'D' => 16,
                'E' => 25, 'F' => 25, 'G' => 25, 'H' => 18, 'I' => 18
            ];
            foreach ($columnWidths as $col => $width) {
                $sheet->getColumnDimension($col)->setWidth($width);
            }

            for ($i = 1; $i <= 100; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(22);
            }

            $row = 1;

            $sheet->mergeCells("A1:B3");
            $logoPath = public_path('assets/images/logo-dark.png');
            if (file_exists($logoPath)) {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('Logo');
                $drawing->setPath($logoPath);
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(10);
                $drawing->setOffsetY(5);
                $drawing->setWidth(60);
                $drawing->setHeight(50);
                $drawing->setWorksheet($sheet);
            }

            $sheet->mergeCells("C1:F3")->setCellValue("C1", "OCCUPATIONAL HEALTH CENTER FIRST AID RECORD\nPN INTERNATIONAL PVT LTD");
            $sheet->getStyle("C1:F3")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            ]);

            $sheet->mergeCells("G{$row}:G" . ($row + 2));

            $logoRightPath = public_path('assets/images/plus-image.webp');
            if (file_exists($logoRightPath)) {
                $drawing = new Drawing();
                $drawing->setName('Right Logo');
                $drawing->setPath($logoRightPath);
                $drawing->setCoordinates("G{$row}");

                $drawing->setOffsetX(50);
                $drawing->setOffsetY(15);
                $drawing->setWidth(60);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);

                $sheet->getStyle("G{$row}:G" . ($row + 2))->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
            }

            $sheet->mergeCells("H1")->setCellValue("H1", "Doc. No.");
            $sheet->setCellValue("I1", $document_no->doc_no);

            $sheet->mergeCells("H2")->setCellValue("H2", "Issue Dt.");
            $sheet->setCellValue("I2", Displaydateformat($document_no->issue_date));

            $sheet->mergeCells("H3")->setCellValue("H3", "Rev. & Dt.");
            $sheet->setCellValue("I3", $document_no->rev_dt);

            $sheet->getStyle("H1:I3")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle("G1:G3")->getFont()->setBold(true);

            $row = 4;
            $sheet->fromArray([
                "Sr No.", "Month", "Department", "Unit",
                "First Aid Station Number", "First Aid Box Number",
                "Total Number of First Aid", "Remark"
            ], null, "A{$row}");
            $sheet->getRowDimension($row)->setRowHeight(30);
            $sheet->mergeCells("H{$row}:I{$row}");
            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $row++;
            $srNo = 1;
            $month = $first_aid_details->month ?? '-';

            foreach ($first_aid_checklist as $details) {
                $sheet->fromArray([
                    $srNo,
                    $month,
                    getDepartment($details['department']),
                    getUnitname($details['unit']),
                    $details['first_aid_station_number'] ?? '-',
                    $details['first_aid_box_number'] ?? '-',
                    $details['total_number_of_first_aid'] ?? '-',
                    $details['remark'] ?? '-'
                ], null, "A{$row}");

                $sheet->mergeCells("H{$row}:I{$row}");
                $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $row++;
                $srNo++;
            }

            $sheet->mergeCells("A{$row}:F{$row}")->setCellValue("A{$row}", "Total Number of First Aid");
            $sheet->mergeCells("G{$row}:I{$row}");
            $sheet->setCellValue("G{$row}", $first_aid_details->overall_total_number_of_first_aid ?? '0');

            $sheet->getStyle("G{$row}:I{$row}")
                  ->getAlignment()
                  ->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle("A5:I{$row}")->applyFromArray([
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ]);

            $writer = new Xlsx($spreadsheet);
            $fileName = 'First Aid Record.xlsx';
            $filePath = storage_path("app/public/{$fileName}");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/first-aid-record/list'));
        }
    }



}

