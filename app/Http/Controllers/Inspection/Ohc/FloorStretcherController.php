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
                            $btn = '<a href="' . admin_url('ohc/floor_stretcher/checklist/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';

                            $btn .= '<a href="' . admin_url('ohc/floor_stretcher/checklist/exportViewPdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';
                            $btn .= '<a href="' . admin_url('ohc/floor_stretcher/checklist/export/excel/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
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

        return view('inspection.ohc.floor_stretcher.list');
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
            dd($ex);
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
            dd($ex);
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

            $header = [
                __("common.sno"),
                'Date of Inspection',
                'Unit',
                'Frequency',
                'Shift',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  Displaydateformat($data->issue_date);
                $export[] =  getUnitname($data->unit);
                $export[] =  getFrequencyname($data->frequency);
                $export[] =  getShiftname($data->shift);
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);
                $exportData[] = $export;
                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Floor Stretcher Inspection.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
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

            if (count($allData) > 20) {
                return redirect()->back()->with('error', "__('inspection.excess_error')");
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
            dd($ex);
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
            return $mpdf->Output($filename, 'I');
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
            $inspection_file = $this->floor_files->getFiles($id, $inspection_type);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }


            $widths = [5, 15, 40, 30, 25, 35, 35, 40, 35, 30, 25];
            foreach (range('A', 'K') as $index => $col) {
                $sheet->getColumnDimension($col)->setWidth($widths[$index]);
                
            }

            $logoPath = public_path('assets/images/logo-dark.png');
            if (file_exists($logoPath)) {
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setPath($logoPath);
                $drawing->setCoordinates('B1');
                $drawing->setOffsetX(20);
                $drawing->setOffsetY(10);
                $drawing->setWidth(80);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
            }

            $sheet->mergeCells("D1:J3");
            $sheet->setCellValue("D1", "Monthly Floor Patient Stretcher Checklist\nPN International Pvt. Ltd.");
            $sheet->getStyle("D1")->applyFromArray([
                'font' => ['bold' => true, 'size' => 13],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);

            $sheet->mergeCells("A4:F4")->setCellValue("A4", "Date: " . Displaydateformat($inspection_detail->date_of_inspection));
            $sheet->mergeCells("G4:K4")->setCellValue("D4", "Shift :-");
            $sheet->mergeCells("A5:F4")->setCellValue("G4", "Unit :-1");


            $sheet->mergeCells("G5:K5")->setCellValue("A5", "Frequency :: Monthly");
            $sheet->getStyle("A5")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
            ]);


            $headers = [
                'A' => 'Sr. No.',
                'B' => 'Resource code',
                'C' => 'Department/Location',
                'D' => 'Are the Stretcher Cover and patient stretcher clean?',
                'E' => 'Is the Stretcher Hanging Hooks are Ok',
                'F' => 'Is the floor patient stretcher resource code correct and available?',
                'G' => 'Is the patient stretcher placed on the floor Condition is OK',
                'H' => 'Is the patient’s stretcher placed on the floor and hung properly on a hook in Its Designated Area',
                'I' => 'Is the Patient Handling Stretcher Guide Line Displayed',
                'J' => 'Remark'
            ];

            foreach ($headers as $col => $text) {
                $sheet->mergeCells("{$col}6:{$col}7");
                $sheet->setCellValue("{$col}6", $text);
            }

            $sheet->getStyle("A6:K7")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFFFFFFF']
                ]
            ]);

            $writer = new Xlsx($spreadsheet);
            $fileName = 'floor_stretcher_checklist.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/floor_stretcher/checklist/list'));
        }
    }
}
