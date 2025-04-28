<?php

namespace App\Http\Controllers\Inspection\MSDS;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\Inspection\MSDS\MSDSEmail;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\MSDS\MSDSDetails;
use App\Models\Inspection\MSDS\MSDSCheckList;
use App\Models\Inspection\MSDS\MSDSStatusLog;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\MSDS\MSDSSignatureUpload;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;


class MSDSController extends Controller
{

    private $msdsDetails;
    private $msdsCheckList;
    private $statusLog;
    private $signature;
    private $document_reference;

    public function __construct()
    {
        $this->msdsDetails = new MSDSDetails();
        $this->msdsCheckList = new MSDSCheckList();
        $this->statusLog = new MSDSStatusLog();
        $this->signature = new MSDSSignatureUpload();
        $this->document_reference = new InspectionStaticDocno();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->msdsDetails->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('msds/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('msds/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                                    </a>';
                            $btn .= '<a href="' . admin_url('msds/generalExcel/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                                    </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date','issue_date','created_by'])
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

        return view('inspection.msds.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $document_no = $this->document_reference->selectUsingName('MSDS');
            $data = [
                'document_no' => $document_no,
            ];
            return view('inspection.msds.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {

        try {
            $rules = [
                'item_code' => 'required',
                'name_of_chemical' => 'required',
                'msds_availability_status' => 'required',
                'remark' => 'required',
            ];
            $messages = [
                'item_code.required' => __('Item Code is required'),
                'name_of_chemical.required' => __('Name of Chemical is required'),
                'msds_availability_status.required' => __('MSDS Availability Status is required'),
                'remark.required' => __('Remark is required'),
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            try {
               $msds = $this->msdsDetails->store();

                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('msds/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('msds/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $msdsDetails = $this->msdsDetails->find($id);
                $inspection_details = $this->msdsDetails->selectOne($id);
                $document_no = $this->document_reference->selectOne($msdsDetails->document_reference_id);

                $data = array(
                    'msdsDetails' => $msdsDetails,
                    'inspection_details' => $inspection_details,
                    'document_no' => $document_no,
                );
            }
            return view('inspection.msds.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }


    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->msdsDetails->exportdata();
            $document_no = $this->document_reference->selectUsingName('MSDS');


            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }elseif(count($allData) > 20){
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }

            $data = array(
                'content' => $allData,
                'document_no' => $document_no,
                'pagetitle' => "MSDS Details",
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

            $view = view('inspection.msds.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "MSDS.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('msds/list'));
        }
    }


    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->msdsDetails->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $document_no = $this->document_reference->selectUsingName('MSDS');
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $row = 1;
            $i = 1;

            foreach ($allData as $data) {

                $logoPath = public_path('assets/images/logo-dark.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates('A' . $row);
                    $drawing->setOffsetX(10);
                    $drawing->setWidth(100);
                    $drawing->setHeight(50);
                    $drawing->setWorksheet($sheet);
                }

                $sheet->mergeCells("A{$row}:B" . ($row + 2));
                $sheet->mergeCells("C{$row}:L" . ($row + 2));
                $sheet->setCellValue("C{$row}", "Chemical (MSDS) Master List PN International Pvt.Ltd.");
                $sheet->getStyle("A{$row}:O" . ($row + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);

                $docLabels = [
                    'M1:N1' => 'Doc. No.',
                    'M2:N2' => 'Issue Dt.',
                    'M3:N3' => 'Rev. & Dt.',
                ];

                foreach ([0, 1, 2] as $idx) {
                    $labelRow = $row + $idx;
                    $labelCell = "M{$labelRow}";
                    $mergeRange = "M{$labelRow}:N{$labelRow}";
                    $sheet->mergeCells($mergeRange)->setCellValue($labelCell, array_values($docLabels)[$idx]);
                    $sheet->getStyle($mergeRange)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 10],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                }

                $sheet->setCellValue("O{$row}", $document_no->doc_no);
                $sheet->setCellValue("O" . ($row + 1), Displaydateformat($document_no->issue_date));
                $sheet->setCellValue("O" . ($row + 2), $document_no->rev_dt);

                $sheet->getStyle("O{$row}:O" . ($row + 2))->applyFromArray([
                    'font' => ['size' => 10],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $headerRow = $row + 3;
                $sheet->mergeCells("A{$headerRow}:C{$headerRow}")->setCellValue("A{$headerRow}", 'Sr. No');
                $sheet->mergeCells("D{$headerRow}:F{$headerRow}")->setCellValue("D{$headerRow}", 'Item Code');
                $sheet->mergeCells("G{$headerRow}:I{$headerRow}")->setCellValue("G{$headerRow}", 'Name Of Chemical');
                $sheet->mergeCells("J{$headerRow}:L{$headerRow}")->setCellValue("J{$headerRow}", 'MSDS Available Status');
                $sheet->mergeCells("M{$headerRow}:O{$headerRow}")->setCellValue("M{$headerRow}", 'Remarks');

                $sheet->getStyle("A{$headerRow}:O{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'fill' => ['fillType' => Fill::FILL_SOLID],
                ]);

                $dataRow = $headerRow + 1;

                $sheet->mergeCells("A{$dataRow}:C{$dataRow}")->setCellValue("A{$dataRow}", 1); // Always 1 for each block
                $sheet->mergeCells("D{$dataRow}:F{$dataRow}")->setCellValue("D{$dataRow}", $data->item_code ?? '');
                $sheet->mergeCells("G{$dataRow}:I{$dataRow}")->setCellValue("G{$dataRow}", $data->name_of_chemical ?? '');
                $sheet->mergeCells("J{$dataRow}:L{$dataRow}");

                $status = strtoupper($data->msds_availability_status ?? '');
                switch ($status) {
                    case '1':
                        $symbol = '✓';
                        $color = '008000';
                        break;
                    case '2':
                        $symbol = 'X';
                        $color = 'FF0000';
                        break;
                    case 'null':
                        $symbol = 'N/A';
                        $color = '808080';
                        break;
                    default:
                        $symbol = '-';
                        $color = '808080';
                        break;
                }

                $sheet->setCellValue("J{$dataRow}", $symbol);
                $sheet->getStyle("J{$dataRow}:L{$dataRow}")->applyFromArray([
                    'font' => ['name' => 'Segoe UI Symbol', 'color' => ['rgb' => $color], 'bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->mergeCells("M{$dataRow}:O{$dataRow}")->setCellValue("M{$dataRow}", $data->remark ?? '');
                $sheet->getStyle("A{$dataRow}:O{$dataRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $row = $dataRow + 5;
                $i++;
            }

            foreach (range('A', 'O') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $fileName = 'MSDS.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('msds/list'));
        }
    }


    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $msdsDetails = $this->msdsDetails->find($id);
                $inspection_details = $this->msdsDetails->selectOne($id);
                $document_no = $this->document_reference->selectUsingName('MSDS');

                $data = [
                    'msdsDetails' => $msdsDetails,
                    'inspection_details' => $inspection_details,
                    'pagetitle' => "MSDS Details",
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

            $html = view('inspection.msds.generalpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "MSDS Details.pdf";
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
            $msdsDetails = $this->msdsDetails->selectOne($id);
            $document_no = $this->document_reference->selectUsingName('MSDS');

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $logoPath = public_path('assets/images/logo-dark.png');
            if (file_exists($logoPath)) {
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setPath($logoPath);
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(10);
                $drawing->setWidth(100);
                $drawing->setHeight(50);
                $drawing->setWorksheet($sheet);
            }

            $sheet->mergeCells('A1:B3');
            $sheet->mergeCells('C1:L3');
            $sheet->setCellValue('C1', "Chemical (MSDS) Master List PN International Pvt.Ltd.");
            $sheet->getStyle('C1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ],
            ]);

            $headerLabels = [
                'M1:N1' => 'Doc. No.',
                'M2:N2' => 'Issue Dt.',
                'M3:N3' => 'Rev. & Dt.',
            ];

            foreach ($headerLabels as $cellRange => $label) {
                $cell = explode(':', $cellRange)[0];
                $sheet->mergeCells($cellRange)->setCellValue($cell, $label);
                $sheet->getStyle($cell)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
            }

            $sheet->setCellValue("O1", $document_no->doc_no);
            $sheet->setCellValue("O2", Displaydateformat($document_no->issue_date));
            $sheet->setCellValue("O3", $document_no->rev_dt);

            $sheet->getStyle("M1:O3")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['argb' => '000000']]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $headers = [
                'Sr. No',
                'Item Code',
                'Name Of Chemical',
                'MSDS Available Status',
                'Remarks'
            ];

            $sheet->mergeCells('A4:C4')->setCellValue('A4', $headers[0]);
            $sheet->mergeCells('D4:F4')->setCellValue('D4', $headers[1]);
            $sheet->mergeCells('G4:I4')->setCellValue('G4', $headers[2]);
            $sheet->mergeCells('J4:L4')->setCellValue('J4', $headers[3]);
            $sheet->mergeCells('M4:O4')->setCellValue('M4', $headers[4]);

            $sheet->getStyle('A4:O4')->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                ],
            ]);

            $row = 5;
            $sheet->mergeCells("A{$row}:C{$row}")->setCellValue("A{$row}", '1');
            $sheet->mergeCells("D{$row}:F{$row}")->setCellValue("D{$row}", $msdsDetails->item_code ?? '');
            $sheet->mergeCells("G{$row}:I{$row}")->setCellValue("G{$row}", $msdsDetails->name_of_chemical ?? '');

            $sheet->mergeCells("J{$row}:L{$row}");

            $status = strtoupper($msdsDetails->msds_availability_status ?? '');

            switch ($status) {
                case '1':
                    $symbol = '✓';
                    $color = '008000';
                    break;
                case '2':
                    $symbol = 'X';
                    $color = 'FF0000';
                    break;
                case 'null':
                    $symbol = 'N/A';
                    $color = '808080';
                    break;
                default:
                    $symbol = '-';
                    $color = '808080';
                    break;
            }

            $sheet->setCellValue("J{$row}", $symbol);

            $sheet->getStyle("J{$row}:L{$row}")->applyFromArray([
                'font' => [
                    'name' => 'Segoe UI Symbol',
                    'color' => ['rgb' => $color],
                    'bold' => true,
                    'size' => 14,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);


            $sheet->mergeCells("M{$row}:O{$row}")->setCellValue("M{$row}", $msdsDetails->remark ?? '');

            $sheet->getStyle("A{$row}:O{$row}")->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);


            foreach (range('A', 'O') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $fileName = 'MSDS.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('msds/list'));
        }
    }





}
