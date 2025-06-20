<?php

namespace App\Http\Controllers\Inspection\Fire;

use Response;
use Exception;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Master\Employee;
use App\Models\Master\Department;
use App\Http\Controllers\Controller;
use App\Models\Inspection\Fire\Fire;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\SimpleExcel\SimpleExcelWriter;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Models\Inspection\InspectionStaticDocno;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Inspection\Fire\CertifiedFireFighter;

class CertifiedFireFighterController extends Controller
{
    private $department;
    private $certified_fire_fighter;
    private $upload_log;
    private $fire;
    private $static_docno;
    private $unit;

    public function __construct()
    {
        $this->certified_fire_fighter = new CertifiedFireFighter();
        $this->department = new Department();
        $this->fire = new Fire();
        $this->static_docno = new InspectionStaticDocno();
        $this->unit = new Unit();
    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $type = 2;
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
                        ->addColumn('created_at', function ($row) {
                            return Displaydatetimeformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('fire/certified-fire-fighter/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {

                            // $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '"  class="recordDelete" title="' . __('common.delete') . '"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            // }
                            $btn .= '<a href="' . admin_url('fire/certified-fire-fighter/exportViewPdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                                    </a>';

                            $btn .= '<a href="' . admin_url('fire/certified-fire-fighter/generalExcel/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="Excel">
                                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                                    </a>';

                            return $btn;
                        })
                        ->rawColumns(['action', 'created_at', 'created_by', 'status'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
        $fireList  = $this->fire->select('id', 'fire_no')->where('type', 2)->where('status', '1')->get();

        $data = array(
            'fireList' => $fireList,
        );
        return view('inspection.fire.certifiedFireFighter.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
            $staticDocno  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                ['type', "CertifiedFireFighter"],
                ['status', '1']
            ])->first();

            $data = array(
                'staticDocno' => $staticDocno,
                'unit' => $unit,
            );
            return view('inspection.fire.certifiedFireFighter.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'certified_fire_fighter_no' => 'required',
                'unit_id' => 'required',
                'department_id' => 'required',
                'emp_name' => 'required',
                'emp_code' => 'required',
                'emp_phone' => 'required',
                'emp_status' => 'required',

            ];

            $messages = [

                'certified_fire_fighter_no.required' => "Certified Fire Fighter No is required.",
                'unit_id.required'                   => "Unit is required.",
                'department_id.required'            => "Department is required.",
                'emp_name.required'                 => "Employee Name is required.",
                'emp_code.required'                 => "Employee Code is required.",
                'emp_phone.required'                => "Contact Number is required.",
                'emp_status.required'               => "Status is required.",
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                $fire_no = $request->certified_fire_fighter_no;
                $type = 2;
                $fire =   $this->fire->store($fire_no, $type);

                $this->certified_fire_fighter->store($fire->id);

                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', __('common.message_error'));
            }

            return redirect(admin_url('fire/certified-fire-fighter/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('fire/certified-fire-fighter/list'));
        }
    }



    public function View($id)
    {
        try {
            $id = decryptId($id);
            if (Auth::check()) {
                $type = 2;
                $fireData =   $this->fire->selectOne($id, $type);
                $certifiedFireDataList = $this->certified_fire_fighter->selectOne($id);
                $staticDocno  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                    ['type', "CertifiedFireFighter"],
                    ['status', '1']
                ])->first();
                $data = array(
                    'fireData' => $fireData,
                    'certifiedFireDataList' => $certifiedFireDataList,
                    'staticDocno' => $staticDocno,
                );
            }
            return view('inspection.fire.certifiedFireFighter.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/certified-fire-fighter/list'));
        }
    }


    public function StatusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $type = 2;
            $fireID = $this->fire->statuschange($id, $type);
            $this->certified_fire_fighter->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'status changed'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }


    public function ExportExcel(Request $request)
    {
        try {
            $type = 2;
            $certified_fire_fighters = $this->fire->exportdata($type);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $document_no = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')
                ->where([
                    ['type', "CertifiedFireFighter"],
                    ['status', '1']
                ])->first();

            foreach (range('A', 'P') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            for ($i = 1; $i <= 1000; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $currentRow = 1;

            foreach ($certified_fire_fighters as $groupIndex => $group) {
                $leftLogoPath = public_path('assets/images/logo-dark.png');
                if (file_exists($leftLogoPath)) {
                    $leftDrawing = new Drawing();
                    $leftDrawing->setName('LeftLogo');
                    $leftDrawing->setDescription('Left Company Logo');
                    $leftDrawing->setPath($leftLogoPath);
                    $leftDrawing->setCoordinates("A{$currentRow}");
                    $leftDrawing->setOffsetX(10);
                    $leftDrawing->setOffsetY(5);
                    $leftDrawing->setHeight(60);
                    $leftDrawing->setWorksheet($sheet);
                }

                $sheet->mergeCells("A{$currentRow}:C" . ($currentRow + 2));
                $sheet->getStyle("A{$currentRow}:C" . ($currentRow + 2))->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $rightLogoPath = public_path('assets/images/Logo.JPG');
                if (file_exists($rightLogoPath)) {
                    $rightDrawing = new Drawing();
                    $rightDrawing->setName('RightLogo');
                    $rightDrawing->setDescription('Right Company Logo');
                    $rightDrawing->setPath($rightLogoPath);
                    $rightDrawing->setCoordinates("J{$currentRow}");
                    $rightDrawing->setOffsetX(30);
                    $rightDrawing->setOffsetY(10);
                    $rightDrawing->setHeight(60);
                    $rightDrawing->setWorksheet($sheet);

                    $sheet->getStyle("J{$currentRow}:K" . ($currentRow + 2))->applyFromArray([
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]
                    ]);
                }

                $sheet->mergeCells("J{$currentRow}:K" . ($currentRow + 2));

                $sheet->mergeCells("D{$currentRow}:I" . ($currentRow + 2));
                $sheet->setCellValue("D{$currentRow}", "Certified Fire Fighter List");
                $sheet->getStyle("D{$currentRow}:I" . ($currentRow + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $labelMap = [
                    ['label' => 'Doc. No.', 'value' => $document_no->doc_no ?? '', 'rowOffset' => 0],
                    ['label' => 'Issue Dt.', 'value' => Displaydateformat($document_no->issue_date) ?? '', 'rowOffset' => 1],
                    ['label' => 'Rev. & Dt.', 'value' => $document_no->rev_dt ?? '', 'rowOffset' => 2],
                ];

                foreach ($labelMap as $info) {
                    $rowNum = $currentRow + $info['rowOffset'];
                    $sheet->setCellValue("L{$rowNum}", $info['label']);
                    $sheet->setCellValue("M{$rowNum}", $info['value']);

                    $sheet->getStyle("L{$rowNum}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    ]);

                    $sheet->getStyle("M{$rowNum}")->applyFromArray([
                        'font' => ['size' => 11],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    ]);
                }

                $headerRow1 = $currentRow + 3;
                $headerRow2 = $currentRow + 4;

                $sheet->mergeCells("A{$headerRow1}:A{$headerRow2}")->setCellValue("A{$headerRow1}", 'SR. NO');
                $sheet->mergeCells("B{$headerRow1}:C{$headerRow2}")->setCellValue("B{$headerRow1}", 'Unit');
                $sheet->mergeCells("D{$headerRow1}:E{$headerRow2}")->setCellValue("D{$headerRow1}", 'Department');
                $sheet->mergeCells("F{$headerRow1}:G{$headerRow2}")->setCellValue("F{$headerRow1}", 'Employee Name');
                $sheet->mergeCells("H{$headerRow1}:I{$headerRow2}")->setCellValue("H{$headerRow1}", 'Employee Code');
                $sheet->mergeCells("J{$headerRow1}:K{$headerRow2}")->setCellValue("J{$headerRow1}", 'Contact Number');

                $sheet->mergeCells("L{$headerRow1}:M{$headerRow2}")->setCellValue("L{$headerRow1}", 'Status');

                $sheet->getStyle("A{$headerRow1}:M{$headerRow2}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);


                $row = $currentRow + 5;
                $sr = 1;

                foreach ($group as $fire_fighter) {
                    $sheet->setCellValue("A{$row}", $sr);
                    $sheet->mergeCells("B{$row}:C{$row}")->setCellValue("B{$row}", getUnitname($fire_fighter->unit_id) ?? '');
                    $sheet->mergeCells("D{$row}:E{$row}")->setCellValue("D{$row}", getDepartment($fire_fighter->department_id) ?? '');
                    $sheet->mergeCells("F{$row}:G{$row}")->setCellValue("F{$row}", getEmployeename($fire_fighter->emp_name) ?? '');
                    $sheet->mergeCells("H{$row}:I{$row}")->setCellValue("H{$row}", $fire_fighter->emp_code ?? '');
                    $sheet->mergeCells("J{$row}:K{$row}")->setCellValue("J{$row}", $fire_fighter->emp_phone ?? '');
                    $sheet->mergeCells("L{$row}:M{$row}")->setCellValue("L{$row}", $fire_fighter->emp_status == 1 ? 'Active' : 'Not-Active');

                    $statusColor = $fire_fighter->emp_status == 1 ? '00B050' : 'FF0000';
                    $sheet->getStyle("L{$row}:M{$row}")->getFont()->getColor()->setARGB($statusColor);

                    $sheet->getStyle("A{$row}:M{$row}")->getAlignment()->setWrapText(true);

                    $sheet->getStyle("A{$row}:M{$row}")->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);

                    $row++;
                    $sr++;
                }


                $currentRow = $row + 4;
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Certified Fire Fighter List.xlsx';
            $filePath = storage_path("app/public/{$fileName}");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/certified-fire-fighter/list'));
        }
    }

    public function ExportPDF()
    {
        try {
            $type = 2;

            $allData = $this->fire->exportdata($type);

            // $document_no = $this->static_docno->selectUsingName('CertifiedFireFighter');
            $document_no  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                ['type', "CertifiedFireFighter"],
                ['status', '1']
            ])->first();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error', __('inspection.excess_error'));
            }

            $data = array(
                'content' => $allData,
                'document_no' => $document_no,
                'pagetitle' => "Certified Fire Fighter",
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

            $view = view('inspection.fire.certifiedFireFighter.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Certified Fire Fighter.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/certified-fire-fighter/list'));
        }
    }

    public function ExportViewPDF(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $certified_fire_fighter = $this->certified_fire_fighter->selectOne($id);

                $document_no  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                    ['type', "CertifiedFireFighter"],
                    ['status', '1']
                ])->first();
                $data = [
                    'document_no' => $document_no,
                    'certified_fire_fighter' => $certified_fire_fighter,
                    'pagetitle' => "Certified Fire Fighter",
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

            $html = view('inspection.fire.certifiedFireFighter.viewPdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Certified Fire Fighter.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/certified-fire-fighter/list'));
        }
    }

    public function generalExcel(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $certified_fire_fighters = $this->certified_fire_fighter->selectFireId($id);
            $certified_fire_fighter = $certified_fire_fighters->first();

            $document_no = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')
                ->where([
                    ['type', "CertifiedFireFighter"],
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
                $leftDrawing->setOffsetX(25);
                $leftDrawing->setOffsetY(15);
                $leftDrawing->setHeight(60);
                $leftDrawing->setWorksheet($sheet);
            }
            $sheet->mergeCells('A1:C3');
            $sheet->getStyle('A1:C3')->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $rightLogoPath = public_path('assets/images/Logo.JPG');
            if (file_exists($rightLogoPath)) {
                $rightDrawing = new Drawing();
                $rightDrawing->setName('RightLogo');
                $rightDrawing->setDescription('Right Company Logo');
                $rightDrawing->setPath($rightLogoPath);
                $rightDrawing->setCoordinates('J1');
                $rightDrawing->setOffsetX(30);
                $rightDrawing->setOffsetY(10);
                $rightDrawing->setHeight(60);
                $rightDrawing->setWorksheet($sheet);
            }

            $sheet->mergeCells('J1:K3');

            $sheet->mergeCells('D1:I3');
            $sheet->setCellValue('D1', "Certified Fire Fighter List");
            $sheet->getStyle('D1:I3')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
            ]);


            $labelMap = [
                'L1' => ['label' => 'Doc. No.', 'valueCell' => 'M1', 'value' => $document_no->doc_no ?? ''],
                'L2' => ['label' => 'Issue Dt.', 'valueCell' => 'M2', 'value' => Displaydateformat($document_no->issue_date) ?? ''],
                'L3' => ['label' => 'Rev. & Dt.', 'valueCell' => 'M3', 'value' => $document_no->rev_dt ?? ''],
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


            $sheet->mergeCells('A4:A5')->setCellValue('A4', 'SR. NO');
            $sheet->mergeCells('B4:C5')->setCellValue('B4', 'Unit');
            $sheet->mergeCells('D4:E5')->setCellValue('D4', 'Department');
            $sheet->mergeCells('F4:G5')->setCellValue('F4', 'Employee Name');
            $sheet->mergeCells('H4:I5')->setCellValue('H4', 'Employee Code');
            $sheet->mergeCells('J4:K5')->setCellValue('J4', 'Contact Number');
            $sheet->mergeCells('L4:M5')->setCellValue('L4', 'Status');



            $sheet->getStyle('A4:M5')->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $row = 6;
            $sr = 1;

            foreach ($certified_fire_fighters as $certified_fire_fighter) {
                $sheet->setCellValue("A{$row}", $sr);
                $sheet->mergeCells("B{$row}:C{$row}")->setCellValue("B{$row}", getUnitname($certified_fire_fighter['unit_id']) ?? '');
                $sheet->mergeCells("D{$row}:E{$row}")->setCellValue("D{$row}", getDepartment($certified_fire_fighter['department_id']) ?? '');
                $sheet->mergeCells("F{$row}:G{$row}")->setCellValue("F{$row}", getEmployeename($certified_fire_fighter['emp_name']) ?? '');
                $sheet->mergeCells("H{$row}:I{$row}")->setCellValue("H{$row}", $certified_fire_fighter['emp_code'] ?? '');
                $sheet->mergeCells("J{$row}:K{$row}")->setCellValue("J{$row}", $certified_fire_fighter['emp_phone'] ?? '');



                $sheet->mergeCells("L{$row}:M{$row}")
                    ->setCellValue("L{$row}", ($certified_fire_fighter['emp_status'] == 1 ? 'Active' : 'Not-Active'));

                $statusColor = ($certified_fire_fighter['emp_status'] == 1) ? '00B050' : 'FF0000';

                $sheet->getStyle("L{$row}:M{$row}")->getFont()->getColor()->setARGB($statusColor);

                $sheet->getStyle("L{$row}:M{$row}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);


                $sheet->getStyle("A{$row}:M{$row}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $sheet->getStyle("A{$row}:M{$row}")->getAlignment()->setWrapText(true);
                $row++;
                $sr++;
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Certified Fire Fighter.xlsx';
            $filePath = storage_path("app/public/{$fileName}");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/certified-fire-fighter/list'));
        }
    }

    public function employeedetails(Request $request)
    {
        $id = decryptId($request->input('emp_name'));
        $employee = Employee::find($id);
        if ($employee) {
            return response()->json([
                'employee' => [
                    'employee_id' => $employee->emp_id,
                    'mobile_no' => $employee->mobile_no
                ]
            ]);
        } else {
            return response()->json(['message' => 'Employee not found'], 404);
        }
    }
}
