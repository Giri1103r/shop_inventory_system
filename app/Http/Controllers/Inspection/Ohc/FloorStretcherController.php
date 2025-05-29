<?php

namespace App\Http\Controllers\Inspection\Ohc;

use Exception;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Master\Location;
use App\Models\Master\Department;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Inspection\Master\Shift;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\SimpleExcel\SimpleExcelWriter;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\Inspection\Master\Frequency;
use App\Models\Inspection\Ohc\FloorStretcher;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Models\Inspection\Ohc\FloorStretcherFiles;
use App\Mail\Inspection\Ohc\FloorStretcher as OhcFloorStretcher;

class FloorStretcherController extends Controller
{
    private $floor_strecther;
    private $floor_files;
    private $frequency;
    private $unit;
    private $shift;
    private $location;
    private $department;


    public function __construct()
    {
        $this->floor_strecther = new FloorStretcher();
        $this->floor_files = new FloorStretcherFiles();
        $this->frequency = new Frequency();
        $this->unit = new Unit();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->department = new Department();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->floor_strecther->list();
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
                        ->addColumn('inspection_created_at', function ($row) {
                            return Displaydateformat($row->inspection_created_at);
                        })
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })

                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('ohc/floor_stretcher/checklist/view/' . encryptId($row->checklist_id)) . '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';

                            $btn .= '<a href="' . admin_url('ohc/floor_stretcher/checklist/exportViewPdf/' . encryptId($row->checklist_id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';
                            $btn .= '<a href="' . admin_url('ohc/floor_stretcher/checklist/export/excel/' . encryptId($row->checklist_id)) . '" style="margin-right: 5px;" title="PDF">
                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                     </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'issue_date'])
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
        $frequency = $this->frequency->getFrequency();
        $unit = $this->unit->getUnit();
        $data = array(
            'shifts' => $shifts,
            'units' => $unit,
            'frequency' => $frequency,

        );
        return view('inspection.ohc.floor_stretcher.list',$data);
    }

    public function Add(Request $request)
    {
        try {

            $shifts = $this->shift->getShiftname();
            $frequency = $this->frequency->getFrequency();
            $unit = $this->unit->getUnit();
            $checklistQuestions = getCheckListQuestion(OHC_FLOOR_STRECTHER_CHECKLIST);
            $options =  getoption(OHC_FLOOR_STRECTHER_CHECKLIST);
            $getoption = string_to_array($options->type);

            if (count($checklistQuestions) <= 0) {
                Session::flash('success', __('inspection.checklist_add'));
                return redirect(admin_url('inspection/master/checklist-sub-type-data/add'));
            }

            $data = array(
                'shifts' => $shifts,
                'units' => $unit,
                'frequency' => $frequency,
                'checklist_details' => $checklistQuestions,
                'getoption' => $getoption,
            );
            return view('inspection.ohc.floor_stretcher.add', $data);
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/floor_stretcher/checklist/list'));
        }
    }

    public function Store(Request $request)
    {
        try {
            $store = $this->floor_strecther->store();
            $inspection_id = $store->id;



            $inspection_type = OHC_TYPE_FLOOR_STRETCHER;
            $inspection_details = $this->floor_strecther->selectOne($inspection_id);

            // Store Auditor Signature
            $files = $this->floor_files->signatureUpload($inspection_type, $inspection_id);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'OHC INSPECTION';
            $notificationData = array(
                'notification_type' => OHC_INSPECTION,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Floor Checklist Inspection Added",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('ohc/floor_stretcher/checklist/view/' . encryptId($inspection_id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'OHC Floor Stretcher Checklist Inspection Created';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('ohc/floor_stretcher/checklist/view/' . encryptId($inspection_id));
                $details = array(
                    'ohc_type' => 'Checklist Of Floor Stretcher',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new OhcFloorStretcher($details));
            }

            Session::flash('success', 'Your data has been added successfully');
            return redirect(admin_url('ohc/floor_stretcher/checklist/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/floor_stretcher/checklist/list'));
        }
    }

    public function View(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_details = $this->floor_strecther->selectOne($id);
            $inspection_type = OHC_TYPE_FLOOR_STRETCHER;
            $inspection_file = $this->floor_files->getFiles($id, $inspection_type);

            $data = array(
                'inspection_details' => $inspection_details,
                'inspection_file' => $inspection_file,
            );

            return view('inspection.ohc.floor_stretcher.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/floor_stretcher/checklist/list'));
        }
    }


    public function ExportExcel()
    {
        try {
            $allData = $this->floor_strecther->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $currentRow = 1;
            $formSpacing = 5;

            foreach ($allData as $data) {
                $inspection_type = OHC_TYPE_FLOOR_STRETCHER;
                $signature = $this->floor_files->getFiles($data->id, $inspection_type);

                for ($i = $currentRow; $i <= $currentRow + 50; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(25);
                }

                if ($currentRow == 1) {
                    $widths = [5, 18, 25, 18, 18, 18, 18, 18, 18, 20];
                    foreach (range('A', 'J') as $index => $col) {
                        $sheet->getColumnDimension($col)->setWidth($widths[$index]);
                    }
                }

                $sheet->getRowDimension($currentRow + 5)->setRowHeight(30);
                $sheet->getRowDimension($currentRow + 6)->setRowHeight(60);

                $logoPath = public_path('assets/images/logo-dark.png');
                if (file_exists($logoPath)) {
                    $logo = new Drawing();
                    $logo->setName('Logo');
                    $logo->setPath($logoPath);
                    $logo->setCoordinates("B{$currentRow}");
                    $logo->setOffsetX(10);
                    $logo->setOffsetY(10);
                    $logo->setWidth(90);
                    $logo->setHeight(60);
                    $logo->setWorksheet($sheet);
                }

                $sheet->mergeCells("A{$currentRow}:C" . ($currentRow + 2));
                $sheet->mergeCells("D{$currentRow}:J" . ($currentRow + 2));
                $sheet->setCellValue("D{$currentRow}", "Monthly Floor Patient Stretcher Checklist");
                $sheet->getStyle("D{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $sheet->getStyle("A{$currentRow}:J" . ($currentRow + 2))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $infoRow = $currentRow + 3;
                $sheet->mergeCells("A{$infoRow}:E{$infoRow}")->setCellValue("A{$infoRow}", "Date: " . Displaydateformat($data->issue_date));
                $sheet->mergeCells("F{$infoRow}:J{$infoRow}")->setCellValue("F{$infoRow}", "Shift: " . getShiftname($data->shift));

                $infoRow2 = $currentRow + 4;
                $sheet->mergeCells("A{$infoRow2}:E{$infoRow2}")->setCellValue("A{$infoRow2}", "Frequency: " . getFrequencyname($data->frequency));
                $sheet->mergeCells("F{$infoRow2}:J{$infoRow2}")->setCellValue("F{$infoRow2}", "Unit: " . getUnitname($data->unit));

                $sheet->getStyle("A{$infoRow}:J{$infoRow2}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $headerRow1 = $currentRow + 5;
                $headerRow2 = $currentRow + 6;

                $sheet->mergeCells("A{$headerRow1}:A{$headerRow2}")->setCellValue("A{$headerRow1}", 'Sr. No.');
                $sheet->mergeCells("B{$headerRow1}:B{$headerRow2}")->setCellValue("B{$headerRow1}", 'Resource Code');
                $sheet->mergeCells("C{$headerRow1}:C{$headerRow2}")->setCellValue("C{$headerRow1}", 'Department/Location');
                $sheet->mergeCells("D{$headerRow1}:J{$headerRow1}")->setCellValue("D{$headerRow1}", 'CHECK POINT (Yes/No)');

                $sheet->setCellValue("D{$headerRow2}", 'Are the Stretcher Cover and patient stretcher clean?');
                $sheet->setCellValue("E{$headerRow2}", 'Is the Stretcher Hanging Hooks OK?');
                $sheet->setCellValue("F{$headerRow2}", 'Is the floor patient stretcher resource code correct and available?');
                $sheet->setCellValue("G{$headerRow2}", 'Is the patient stretcher placed and floor Condition OK?');
                $sheet->setCellValue("H{$headerRow2}", 'Is the stretcher hung properly in Designated Area?');
                $sheet->setCellValue("I{$headerRow2}", 'Is the Patient Handling Stretcher Guide Line Displayed?');
                $sheet->setCellValue("J{$headerRow2}", 'Remark');

                $sheet->getStyle("A{$headerRow1}:J{$headerRow2}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $startDataRow = $currentRow + 7;
                $row = $startDataRow;
                $sr = 1;
                $user_response = json_decode($data->responses, true);

                foreach ($user_response['resource_code'] as $mainKey => $subResources) {
                    foreach ($subResources as $subKey => $resource_code) {
                        $sheet->setCellValue("A{$row}", $sr);
                        $sheet->setCellValue("B{$row}", $resource_code);
                        $sheet->setCellValue("C{$row}", GetChecklistTypeDate($subKey));

                        $responses = $user_response['response'][$mainKey][$subKey] ?? [];

                        $sheet->setCellValue("D{$row}", $responses['fs_first'] ?? '-');
                        $sheet->setCellValue("E{$row}", $responses['fs_second'] ?? '-');
                        $sheet->setCellValue("F{$row}", $responses['fs_third'] ?? '-');
                        $sheet->setCellValue("G{$row}", $responses['fs_fourth'] ?? '-');
                        $sheet->setCellValue("H{$row}", $responses['fs_fifth'] ?? '-');
                        $sheet->setCellValue("I{$row}", $responses['fs_sixth'] ?? '-');

                        $remark = $user_response['remarks'][$mainKey][$subKey] ?? '-';
                        $sheet->setCellValue("J{$row}", $remark);

                        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        ]);

                        $sr++;
                        $row++;
                    }
                }


                $sheet->getRowDimension($row)->setRowHeight(80);

                // if (!empty($signature) && isset($signature['file_path'])) {
                //     $signaturePath = public_path(str_replace('public/', '', $signature['file_path']));

                //     if (file_exists($signaturePath)) {
                //         $sign = new Drawing();
                //         $sign->setName('Signature');
                //         $sign->setPath($signaturePath);
                //         $sign->setCoordinates("F{$row}");
                //         $sign->setOffsetX(5);
                //         $sign->setOffsetY(25);
                //         $sign->setHeight(40);
                //         $sign->setWorksheet($sheet);
                //     }
                // }

                $sheet->mergeCells("A{$row}:J{$row}")->setCellValue("A{$row}", 'Auditor (Name & Signature):- ' . getUsername($data->created_by));

                $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_BOTTOM,
                        'wrapText' => true,
                    ],
                ]);

                $currentRow = $row + $formSpacing;

            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'floor_stretcher_inspection_bulk.xlsx';
            $filePath = storage_path("app/public/{$fileName}");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);

        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/floor_stretcher/checklist/list'));
        }
    }

    public function ExportPDF()
    {
        try {
            $allData = $this->floor_strecther->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }



            $data = array(
                'content' => $allData,
                'pagetitle' => "__('title.floor_stretcher)",
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

            $view = view('inspection.ohc.floor_stretcher.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Floor-Stretcher Inspection.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/floor_stretcher/checklist/list'));
        }
    }

    public function ExportViewPDF(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_detail = $this->floor_strecther->selectOne($id);
            $inspection_type = OHC_TYPE_FLOOR_STRETCHER;
            $inspection_file = $this->floor_files->getFiles($id, $inspection_type);

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
                'pagetitle' => "Checklist Of Floor Stretcher Inspection",
            );

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $html = view('inspection.ohc.floor_stretcher.viewpdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Floor Stretcher Inspection.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/floor_stretcher/checklist/list'));
        }
    }

    public function GeneralExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_detail = $this->floor_strecther->selectOne($id);
            $inspection_type = OHC_TYPE_FLOOR_STRETCHER;

            $signature = $this->floor_files->getFiles($id, $inspection_type);

            $username = getUsername($inspection_detail->created_by);
            $user_response = json_decode($inspection_detail->responses, true);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }
            $sheet->getRowDimension(6)->setRowHeight(30);
            $sheet->getRowDimension(7)->setRowHeight(60);

            $widths = [5, 18, 25, 18, 18, 18, 18, 18, 18, 20];
            foreach (range('A', 'J') as $index => $col) {
                $sheet->getColumnDimension($col)->setWidth($widths[$index]);
            }

            $logoPath = public_path('assets/images/logo-dark.png');
            if (file_exists($logoPath)) {
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setPath($logoPath);
                $drawing->setCoordinates('B1');
                $drawing->setOffsetX(10);
                $drawing->setOffsetY(10);
                $drawing->setWidth(90);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
            }

            $sheet->getStyle("A1:C3")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $sheet->mergeCells('A1:C3');

            $sheet->mergeCells('D1:J3');
            $sheet->setCellValue('D1', "Monthly Floor Patient Stretcher Checklist");
            $sheet->getStyle('D1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $sheet->getStyle('D1:J3')->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);

            $sheet->mergeCells("A4:E4")->setCellValue('A4', "Date: " . Displaydateformat($inspection_detail->issue_date));
            $sheet->mergeCells("F4:J4")->setCellValue('F4', "Shift: " . getShiftname($inspection_detail->shift));
            $sheet->mergeCells("A5:E5")->setCellValue("A5", "Frequency: " . getFrequencyname($inspection_detail->frequency));
            $sheet->mergeCells("F5:J5")->setCellValue("F5", "Unit: " . getUnitname($inspection_detail->unit));

            $sheet->getStyle("A4:J5")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $sheet->mergeCells('A6:A7')->setCellValue('A6', 'Sr. No.');
            $sheet->mergeCells('B6:B7')->setCellValue('B6', 'Resource Code');
            $sheet->mergeCells('C6:C7')->setCellValue('C6', 'Department/Location');
            $sheet->mergeCells('D6:J6')->setCellValue('D6', 'CHECK POINT (Yes/No)');

            $sheet->setCellValue('D7', 'Are the Stretcher Cover and patient stretcher clean?');
            $sheet->setCellValue('E7', 'Is the Stretcher Hanging Hooks OK?');
            $sheet->setCellValue('F7', 'Is the floor patient stretcher resource code correct and available?');
            $sheet->setCellValue('G7', 'Is the patient stretcher placed and floor Condition OK?');
            $sheet->setCellValue('H7', 'Is the stretcher hung properly in Designated Area?');
            $sheet->setCellValue('I7', 'Is the Patient Handling Stretcher Guide Line Displayed?');
            $sheet->setCellValue('J7', 'Remark');

            $sheet->getStyle('A6:J7')->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $row = 8;
            $sr = 1;

            foreach ($user_response['resource_code'] as $mainKey => $subResources) {
                foreach ($subResources as $subKey => $resource_code) {
                    $sheet->setCellValue("A{$row}", $sr);
                    $sheet->setCellValue("B{$row}", $resource_code);
                    $sheet->setCellValue("C{$row}", GetChecklistTypeDate($subKey));

                    $responses = $user_response['response'][$mainKey][$subKey] ?? [];

                    $sheet->setCellValue("D{$row}", $responses['fs_first'] ?? '-');
                    $sheet->setCellValue("E{$row}", $responses['fs_second'] ?? '-');
                    $sheet->setCellValue("F{$row}", $responses['fs_third'] ?? '-');
                    $sheet->setCellValue("G{$row}", $responses['fs_fourth'] ?? '-');
                    $sheet->setCellValue("H{$row}", $responses['fs_fifth'] ?? '-');
                    $sheet->setCellValue("I{$row}", $responses['fs_sixth'] ?? '-');

                    $remark = $user_response['remarks'][$mainKey][$subKey] ?? '-';
                    $sheet->setCellValue("J{$row}", $remark);

                    $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);

                    $sr++;
                    $row++;
                }
            }

            $sheet->getRowDimension($row)->setRowHeight(80);

            // if (!empty($signature) && isset($signature['file_path'])) {
            //     $signaturePath = public_path(str_replace('public/', '', $signature['file_path']));

            //     if (file_exists($signaturePath)) {
            //         $sign = new Drawing();
            //         $sign->setName('Signature');
            //         $sign->setPath($signaturePath);
            //         $sign->setCoordinates("F{$row}");
            //         $sign->setOffsetX(5);
            //         $sign->setOffsetY(25);
            //         $sign->setHeight(40);
            //         $sign->setWorksheet($sheet);
            //     }
            // }

            $sheet->mergeCells("A{$row}:J{$row}")->setCellValue("A{$row}", 'Auditor (Name & Signature):- ' . $username);

            $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_BOTTOM,
                    'wrapText' => true,
                ],
            ]);

            $writer = new Xlsx($spreadsheet);
            $fileName = 'floor stretcher checklist.xlsx';
            $filePath = storage_path("app/public/{$fileName}");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);

        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/floor_stretcher/checklist/list'));
        }
    }





}
