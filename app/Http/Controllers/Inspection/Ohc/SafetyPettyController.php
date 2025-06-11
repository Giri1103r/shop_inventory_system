<?php

namespace App\Http\Controllers\Inspection\Ohc;

use Exception;
use App\Models\User;
use App\Models\Master\Unit;
use App\Models\Master\Work;
use Illuminate\Http\Request;
use App\Models\Master\Employee;
use App\Models\Master\Department;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\SimpleExcel\SimpleExcelWriter;

use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\Inspection\Ohc\OhcSignature;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Ohc\SafetyPettyDetails;
use App\Models\Inspection\Ohc\SafetyPettyChecklist;

class SafetyPettyController extends Controller
{
    private $sfty_petty_details;
    private $sfty_petty_checklist;
    private $employee;
    private $work;
    private $unit;
    private $signature;
    private $user;
    private $document_reference;
    private $department;

    public function __construct()
    {
        $this->sfty_petty_details = new SafetyPettyDetails();
        $this->sfty_petty_checklist = new SafetyPettyChecklist();
        $this->employee = new Employee();
        $this->work = new Work();
        $this->unit = new Unit();
        $this->signature = new OhcSignature();
        $this->user = new User();
        $this->document_reference = new InspectionStaticDocno();
        $this->department = new Department();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->sfty_petty_details->list();

                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->safety_petty_id) . "' data-type = '1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->safety_petty_id) . "' data-type = '0'>In-Active</span>";
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
                            $btn = '<a href="' . admin_url('ohc/safety-petty-logbook/view/' . encryptId($row->safety_petty_id)) . '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('ohc/safety-petty-logbook/generalpdf/' . encryptId($row->safety_petty_id)) . '" class="view-icon me-1" title="PDF">
                                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                                    </a>';
                            $btn .= '<a href="' . admin_url('ohc/safety-petty-logbook/generalexcel/' . encryptId($row->id)) . '" class="view-icon me-1" title="PDF">
                                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                                    </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'inspection_status', 'created_by', 'status'])
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

        $units = $this->unit->getUnit();

        $data = [
            'units' => $units,
        ];
        return view('inspection.inspection_ohc.safety_petty.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $type = OHC_SAFETY_PETTY_LOGBOOK_INSPECTION;
            $unit = $this->unit->getunit();
            $document_no = $this->document_reference->selectUsingName('SafetyPettyLogbook');

            $data = [
                'unit' => $unit,
                'document_no' => $document_no,
            ];
            return view('inspection.inspection_ohc.safety_petty.add', $data);
        } catch (Exception $ex) {
           report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/safety-petty-logbook/list'));
        }
    }

    public function getSignature(Request $request)
    {
        $loginId = $request->input('login_id');

        if ($loginId) {
            $user = User::where('id', $loginId)->first();
            if ($user && $user->signature_upload) {
                return response()->json([
                    'signature_upload' => $user->signature_upload
                ]);
            }
        }

        return response()->json([
            'signature_upload' => null
        ]);
    }

    public function Store(Request $request)
    {
        try {

            $sfty_petty_details = $this->sfty_petty_details->store();

            $id = [];
            $index = 1;
            foreach ($sfty_petty_details as $details) {

                $id[$index] = $details->id;
                $index++;
            }

            $empId =  Auth::user()->id;

            // $this->signature->signatureLogUpload(
            //     $empId,
            //     $id,
            //     OHC_AMOUNT_GIVENBY_INSPECTION,
            //     'signature_givenby_image'
            // );
            // $this->signature->signatureLogUpload(
            //     $empId,
            //     $id,
            //     OHC_AMOUNT_RECEIVEDBY_INSPECTION,
            //     'signature_receivedby_image'
            // );


            Session::flash('success', __('Your data has been created successfully'));
            return redirect(admin_url('ohc/safety-petty-logbook/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/safety-petty-logbook/list'));
        }
    }

    public function employeeid(Request $request)
    {
        $name = $request->input('search');

        $employee_code = $this->employee->where('emp_id', 'like', '%' . $name . '%')
            ->orWhere('emp_name', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->limit(10)
            ->get();

        return response()->json(
            $employee_code->map(function ($employee) {
                return [
                    'id' => $employee->login_id,
                    'text' => $employee->emp_id . ' - ' . $employee->emp_name,
                ];
            })
        );
    }

    public function view(Request $request)
    {

        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $sfty_petty_details = $this->sfty_petty_details->find($id);
                $document_no = $this->document_reference->selectOne($sfty_petty_details->document_reference_id);

                $type = OHC_SAFETY_PETTY_LOGBOOK_INSPECTION;
                $sub_type_given = OHC_AMOUNT_GIVENBY_INSPECTION;
                $sub_type_received = OHC_AMOUNT_RECEIVEDBY_INSPECTION;

                // $signature_amount_givenby = $this->signature->getGivenBy($type, $sub_type_given, $sfty_petty_details->id);

                // $signature_amount_receivedby = $this->signature->getReceivedBy($type, $sub_type_received, $sfty_petty_details->id);

                $data = array(
                    'sfty_petty_details' => $sfty_petty_details,
                    // 'signature_amount_givenby' => $signature_amount_givenby,
                    // 'signature_amount_receivedby' => $signature_amount_receivedby,
                    'document_no' => $document_no,
                );
            }
            return view('inspection.inspection_ohc.safety_petty.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/safety-petty-logbook/list'));
        }
    }

    public function StatusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->sfty_petty_details->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Your status  has changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->sfty_petty_details->exportdata();

            $document_no = $this->document_reference->selectUsingName('SafetyPettyLogbook');

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }

            $type = OHC_SAFETY_PETTY_LOGBOOK_INSPECTION;
            $sub_type_given = OHC_AMOUNT_GIVENBY_INSPECTION;
            $sub_type_received = OHC_AMOUNT_RECEIVEDBY_INSPECTION;

            $data = array(
                'content' => $allData,
                'document_no' => $document_no,
                'type' => $type,
                'sub_type_given' => $sub_type_given,
                'sub_type_received' => $sub_type_received,
                'pagetitle' => "Safety Petty Logbook Details",
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

            $view = view('inspection.inspection_ohc.safety_petty.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Safety Petty Logbook.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/safety-petty-logbook/list'));
        }
    }

    public function ExportExcel(Request $request)
    {
        try {
            // $type = OHC_SAFETY_PETTY_LOGBOOK_INSPECTION;
            // $sub_type_given = OHC_AMOUNT_GIVENBY_INSPECTION;
            // $sub_type_received = OHC_AMOUNT_RECEIVEDBY_INSPECTION;

            $allData = $this->sfty_petty_details->exportdata();
            $document_no = $this->document_reference->selectUsingName('SafetyPettyLogbook');

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $currentRow = 1;

            foreach ($allData as $index => $data) {
                $logoPath = public_path('assets/images/logo-dark.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates("A{$currentRow}");
                    $drawing->setOffsetX(10);
                    $drawing->setWidth(100);
                    $drawing->setHeight(50);
                    $drawing->setWorksheet($sheet);
                }

                $sheet->mergeCells("A{$currentRow}:C" . ($currentRow + 2));
                $sheet->getStyle("A{$currentRow}:C" . ($currentRow + 2))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $sheet->mergeCells("D{$currentRow}:P" . ($currentRow + 2));
                $sheet->setCellValue("D{$currentRow}", "Safety Petty Log Book");
                $sheet->getStyle("D{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);

                $docLabelMap = [
                    'Doc. No.' => $document_no->doc_no,
                    'Issue Dt.' => Displaydateformat($document_no->issue_date),
                    'Rev. & Dt.' => $document_no->rev_dt
                ];
                $labelRow = $currentRow;
                foreach ($docLabelMap as $label => $value) {
                    $sheet->mergeCells("Q{$labelRow}:S{$labelRow}")->setCellValue("Q{$labelRow}", $label);
                    $sheet->mergeCells("T{$labelRow}:V{$labelRow}")->setCellValue("T{$labelRow}", $value);
                    $sheet->getStyle("Q{$labelRow}:V{$labelRow}")->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    ]);
                    $labelRow++;
                }

                $headerRow = $currentRow + 3;
                $headers = [
                    'Sr. No',
                    'Employee Name',
                    'Employee Code',
                    'Department',
                    'Unit',
                    'Date',
                    'Amount',
                    'Description',
                    'Amount Given By',
                    'Amount Received By ',
                    'Remark'
                ];
                $mergeMap = [
                    'A:B',
                    'C:D',
                    'E:F',
                    'G:H',
                    'I:J',
                    'K:L',
                    'M:N',
                    'O:P',
                    'Q:R',
                    'S:T',
                    'U:V'
                ];
                foreach ($headers as $i => $label) {
                    [$start, $end] = explode(':', $mergeMap[$i]);
                    $sheet->mergeCells("{$start}{$headerRow}:{$end}{$headerRow}")->setCellValue("{$start}{$headerRow}", $label);
                }
                $sheet->getStyle("A{$headerRow}:V{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']],
                ]);

                $sheet->getRowDimension($headerRow)->setRowHeight(30);

                $dataRow = $headerRow + 1;
                $sheet->mergeCells("A{$dataRow}:B{$dataRow}")->setCellValue("A{$dataRow}", '1');
                $sheet->mergeCells("C{$dataRow}:D{$dataRow}")->setCellValue("C{$dataRow}", ($data->employee_name) ?? '');
                $sheet->mergeCells("E{$dataRow}:F{$dataRow}")->setCellValue("E{$dataRow}", $data->employee_code ?? '');
                $sheet->mergeCells("G{$dataRow}:H{$dataRow}")->setCellValue("G{$dataRow}", getDepartment($data->department) ?? '');
                $sheet->mergeCells("I{$dataRow}:J{$dataRow}")->setCellValue("I{$dataRow}", getUnitname($data->unit) ?? '');
                $sheet->mergeCells("K{$dataRow}:L{$dataRow}")->setCellValue("K{$dataRow}", Displaydateformat($data->date) ?? '');
                $sheet->mergeCells("M{$dataRow}:N{$dataRow}")->setCellValue("M{$dataRow}", $data->amount ?? '');
                $sheet->mergeCells("O{$dataRow}:P{$dataRow}")->setCellValue("O{$dataRow}", $data->description ?? '');
                $sheet->mergeCells("Q{$dataRow}:R{$dataRow}")->setCellValue("Q{$dataRow}", getUsername($data->amount_given_by ?? ''));
                $sheet->mergeCells("S{$dataRow}:T{$dataRow}")->setCellValue("S{$dataRow}", getUsername($data->amount_received_by ?? ''));
                $sheet->mergeCells("U{$dataRow}:V{$dataRow}")->setCellValue("U{$dataRow}", $data->remark ?? '');
                $sheet->getRowDimension($dataRow)->setRowHeight(70);

                // $givenSignature = $this->signature->getGivenBy($type, $sub_type_given, $data->id);
                // $receivedSignature = $this->signature->getReceivedBy($type, $sub_type_received, $data->id);

                // if ($givenSignature && file_exists($givenSignature->file_path)) {
                //     $drawingGiven = new Drawing();
                //     $drawingGiven->setName('Given Signature');
                //     $drawingGiven->setPath($givenSignature->file_path);
                //     $drawingGiven->setCoordinates("Q{$dataRow}");
                //     $drawingGiven->setWidth(100);
                //     $drawingGiven->setHeight(50);
                //     $drawingGiven->setOffsetX(25);
                //     $drawingGiven->setOffsetY(10);
                //     $drawingGiven->setWorksheet($sheet);
                // }

                // if ($receivedSignature && file_exists($receivedSignature->file_path)) {
                //     $drawingReceived = new Drawing();
                //     $drawingReceived->setName('Received Signature');
                //     $drawingReceived->setPath($receivedSignature->file_path);
                //     $drawingReceived->setCoordinates("S{$dataRow}");
                //     $drawingReceived->setWidth(100);
                //     $drawingReceived->setHeight(50);
                //     $drawingReceived->setOffsetX(25);
                //     $drawingReceived->setOffsetY(10);
                //     $drawingReceived->setWorksheet($sheet);
                // }

                $sheet->getStyle("A{$dataRow}:V{$dataRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                foreach (range('A', 'V') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $blockStartRow = $currentRow;

                $sheet->getStyle("A{$blockStartRow}:V{$dataRow}")->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $currentRow = $dataRow + 5;
            }

            $fileName = 'Safety Petty Logbook.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/safety-petty-logbook/list'));
        }
    }




    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $sfty_petty_details = $this->sfty_petty_details->find($id);
                $document_no = $this->document_reference->selectUsingName('SafetyPettyLogbook');

                $type = OHC_SAFETY_PETTY_LOGBOOK_INSPECTION;
                $sub_type_given = OHC_AMOUNT_GIVENBY_INSPECTION;
                $sub_type_received = OHC_AMOUNT_RECEIVEDBY_INSPECTION;

                // $signature_amount_givenby = $this->signature->getGivenBy($type, $sub_type_given, $sfty_petty_details->id);

                // $signature_amount_receivedby = $this->signature->getReceivedBy($type, $sub_type_received, $sfty_petty_details->id);

                $data = [
                    'sfty_petty_details' => $sfty_petty_details,
                    'document_no' => $document_no,
                    'pagetitle' => "Safety Petty Logbook Details",
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

            $html = view('inspection.inspection_ohc.safety_petty.generalpdf', $data)->render();

            $mpdf->WriteHTML($html);

            $filename = "Safety Petty Logbook Details.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $employee_code = $request->employee_code;
            $id = $request->id;
            if ($id == '') {
                $record = $this->sfty_petty_details->uniqueCheck($employee_code);
            } else {
                $id = decryptId($id);
                $record = $this->sfty_petty_details->ExistuniqueCheck($employee_code, $id);
            }
            if ($record->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }


    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);

            // $type = OHC_SAFETY_PETTY_LOGBOOK_INSPECTION;
            // $sub_type_given = OHC_AMOUNT_GIVENBY_INSPECTION;
            // $sub_type_received = OHC_AMOUNT_RECEIVEDBY_INSPECTION;

            $sfty_petty_details = $this->sfty_petty_details->selectOne($id);
            $document_no = $this->document_reference->selectUsingName('SafetyPettyLogbook');

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

            $sheet->mergeCells('A1:C3');

            $sheet->getStyle("A1:C3")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);

            $sheet->mergeCells('D1:P3');
            $sheet->setCellValue('D1', "Safety Petty Log Book");
            $sheet->getStyle('D1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);

            $headerLabels = [
                'Q1:S1' => 'Doc. No.',
                'Q2:S2' => 'Issue Dt.',
                'Q3:S3' => 'Rev. & Dt.',
            ];
            foreach ($headerLabels as $cellRange => $label) {
                $cell = explode(':', $cellRange)[0];
                $sheet->mergeCells($cellRange)->setCellValue($cell, $label);
                $sheet->getStyle($cell)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }

            $sheet->mergeCells("T1:V1")->setCellValue("T1", $document_no->doc_no);
            $sheet->mergeCells("T2:V2")->setCellValue("T2", Displaydateformat($document_no->issue_date));
            $sheet->mergeCells("T3:V3")->setCellValue("T3", $document_no->rev_dt);

            $sheet->getStyle("Q1:V3")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['argb' => '000000']]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $headers = [
                'Sr. No',
                'Employee Name',
                'Employee Code',
                'Department',
                'Unit',
                'Date',
                'Amount',
                'Description',
                'Amount Given By',
                'Amount Received By',
                'Remark'
            ];

            $mergeMap = [
                'A4:B4',
                'C4:D4',
                'E4:F4',
                'G4:H4',
                'I4:J4',
                'K4:L4',
                'M4:N4',
                'O4:P4',
                'Q4:R4',
                'S4:T4',
                'U4:V4',
            ];

            foreach ($headers as $index => $label) {
                $cellRange = $mergeMap[$index];
                $cell = explode(':', $cellRange)[0];
                $sheet->mergeCells($cellRange)->setCellValue($cell, $label);
            }

            $sheet->getStyle('A4:V4')->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']],
            ]);

            $row = 5;
            $sheet->mergeCells("A{$row}:B{$row}")->setCellValue("A{$row}", '1');
            $sheet->mergeCells("C{$row}:D{$row}")->setCellValue("C{$row}", ($sfty_petty_details->employee_name) ?? '');
            $sheet->mergeCells("E{$row}:F{$row}")->setCellValue("E{$row}", $sfty_petty_details->employee_code ?? '');
            $sheet->mergeCells("G{$row}:H{$row}")->setCellValue("G{$row}", getDepartment($sfty_petty_details->department) ?? '');
            $sheet->mergeCells("I{$row}:J{$row}")->setCellValue("I{$row}", getUnitname($sfty_petty_details->unit) ?? '');
            $sheet->mergeCells("K{$row}:L{$row}")->setCellValue("K{$row}", Displaydateformat($sfty_petty_details->date) ?? '');
            $sheet->mergeCells("M{$row}:N{$row}")->setCellValue("M{$row}", $sfty_petty_details->amount ?? '');
            $sheet->mergeCells("O{$row}:P{$row}")->setCellValue("O{$row}", $sfty_petty_details->description ?? '');
            $sheet->mergeCells("U{$row}:V{$row}")->setCellValue("U{$row}", $sfty_petty_details->remark);

            // $givenSignaturePath = $this->signature->getGivenBy($type, $sub_type_given, $sfty_petty_details->id)->file_path;

            // $receivedSignaturePath = $this->signature->getReceivedBy($type, $sub_type_received, $sfty_petty_details->id)->file_path;

            $sheet->mergeCells("Q{$row}:R{$row}")->setCellValue("Q{$row}", getUsername($sfty_petty_details->amount_given_by ?? ''));
            $sheet->mergeCells("S{$row}:T{$row}")->setCellValue("S{$row}", getUsername($sfty_petty_details->amount_received_by ?? ''));

            $sheet->getRowDimension($row)->setRowHeight(70);

            // if (file_exists($givenSignaturePath)) {
            //     $drawingGiven = new Drawing();
            //     $drawingGiven->setName('Amount Given By Signature');
            //     $drawingGiven->setPath($givenSignaturePath);
            //     $drawingGiven->setCoordinates("Q{$row}");
            //     $drawingGiven->setWidth(100);
            //     $drawingGiven->setHeight(50);

            //     $drawingGiven->setOffsetX(15);
            //     $drawingGiven->setOffsetY(10);
            //     $drawingGiven->setWorksheet($sheet);
            // }

            // if (file_exists($receivedSignaturePath)) {
            //     $drawingReceived = new Drawing();
            //     $drawingReceived->setName('Amount Received By Signature');
            //     $drawingReceived->setPath($receivedSignaturePath);
            //     $drawingReceived->setCoordinates("S{$row}");
            //     $drawingReceived->setWidth(100);
            //     $drawingReceived->setHeight(50);

            //     $drawingReceived->setOffsetX(25);
            //     $drawingReceived->setOffsetY(10);
            //     $drawingReceived->setWorksheet($sheet);
            // }


            $sheet->getStyle("A{$row}:V{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            foreach (range('A', 'V') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $fileName = 'Safety Petty.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/safety-petty-logbook/list'));
        }
    }
}
