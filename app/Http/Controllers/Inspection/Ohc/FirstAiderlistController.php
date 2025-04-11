<?php

namespace App\Http\Controllers\Inspection\Ohc;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Department;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\Ohc\FirstAiderList;
use App\Models\Inspection\Ohc\FirstAiderListDetails;
use App\Models\Master\Employee;
use App\Models\Master\Work;
use App\Models\OhcManagement\Report\Inventory;
use App\Models\UploadLog;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inspection\InspectionStaticDocno;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class FirstAiderlistController extends Controller
{

    private $upload_log;
    private $unit;
    private $shift;
    private $department;
    private $user;
    private $first_aider;
    private $first_aider_details;
    private $employee;
    private $document_reference;
    private $inventory;


    private $location;
    public function __construct()
    {

        $this->upload_log = new UploadLog();
        $this->unit = new Unit();
        $this->department = new Department();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->first_aider = new FirstAiderList();
        $this->inventory = new Inventory();
        $this->user = new User();
        $this->employee = new Employee();
        $this->document_reference = new InspectionStaticDocno();
        $this->first_aider_details = new FirstAiderListDetails();
    }
    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->first_aider->list();

                    $datatables = Datatables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('inspection_status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            if ($row->inspection_status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->inspection_id) . "' data-type='1'>Active</span>";
                            } else if ($row->inspection_status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->inspection_id) . "' data-type='0'>In-Active</span>";
                            }
                            return $text;
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('last_updated_date', function ($row) {
                            return Displaydateformat($row->last_updated_date);
                        })
                        ->addColumn('next_review_date', function ($row) {
                            return Displaydateformat($row->last_updated_date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })

                        ->addColumn('action', function ($row) {
                            $btn = "";
                            $btn .= '<a href="' . admin_url('ohc/first-aider/view/' . encryptId($row->inspection_id)) . '" class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a>';

                            $btn .= '<a href="' . admin_url('ohc/first-aider/generalpdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';
                            $btn .= '<a href="' . admin_url('ohc/first-aider/generalExcel/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="Excel">
                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                     </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'issue_date', 'created_by', 'inspection_status', 'last_updated_date', 'next_review_date'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);

                    return $datatables;
                } catch (Exception $ex) {
                    dd($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }

        return view('inspection.inspection_ohc.first_aider_list.list');
    }

    public function Add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $employee = $this->employee->getEmployeefulldata();
            $document_no = $this->document_reference->selectUsingName('FirstAiderList');

            $data = array(
                'unit' => $unit,
                'employee' => $employee,
                'document_no' => $document_no,



            );
            return view('inspection.inspection_ohc.first_aider_list.add', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'unit_id' => 'required',
                'department_id' => 'required',
                'review_date' => 'required',

            ];
            $messages = [
                'department_id.required' => 'Please select a Deparment.',
                'unit_id.required' => 'Please select a unit.',
                'review_date.required' => 'Please select the expiry date.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                // dd($request->all());
                // Store user medicine requisition
                $first_aider = $this->first_aider->store();
                $first_aider_details = $this->first_aider_details->store($first_aider);


                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/first-aider/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aider/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $first_aider = $this->first_aider->Selectone($id);
                $first_aider_details = $this->first_aider_details->Selectone($id);
                $document_no = $this->document_reference->selectUsingName('FirstAiderList');

                $data = array(
                    'first_aider' => $first_aider,
                    'first_aider_details' => $first_aider_details,
                    'document_no' => $document_no,
                );
            }
            return view('inspection.inspection_ohc.first_aider_list.view', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $first_aider = $this->first_aider->Selectone($id);
                $first_aider_details = $this->first_aider_details->Selectone($id);
                $document_no = $this->document_reference->selectUsingName('FirstAiderList');
            }
            $data = [
                'first_aider' => $first_aider,
                'first_aider_details' => $first_aider_details,
                'document_no' => $document_no,
                'pagetitle' => "First Aider List",
            ];
            $property = [
                'tempDir' => storage_path('app/public/pdf/temp/'),
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            // Load HTML from the Blade view
            $html = view('inspection.inspection_ohc.first_aider_list.viewpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "First Aider List.pdf";

            return $mpdf->Output($filename, 'D');
        } catch (\Exception $ex) {
            dd($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->first_aider->statuschange($id);
            $this->first_aider_details->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Your Status Changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function ExportExcel()
    {
        try {
            $allData = $this->first_aider->exportdata();
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $row = 1;

            foreach ($allData as $details) {
                $document_no = $this->document_reference->selectOne($details->document_reference_id);
                $first_aider = $this->first_aider->Selectone($details->id);
                $first_aider_details = $this->first_aider_details->Selectone($details->id);

                $currentRow = $row;

                // Logo Section
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
                $sheet->setCellValue("G{$currentRow}", "First Aider List PN International Pvt.Ltd");
                $sheet->getStyle("G{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Document Info
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
                $sheet->mergeCells("A" . ($currentRow + 3) . ":J" . ($currentRow + 3));
                $richText1 = new RichText();
                $richText1->createTextRun(' NEXT REVIEW DATE:- ')->getFont()->setBold(true);
                $richText1->createText(Displaydateformat($first_aider->next_review_date));
                $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

                $sheet->mergeCells("K" . ($currentRow + 3) . ":S" . ($currentRow + 3));
                $richText2 = new RichText();
                $richText2->createTextRun(' LAST UPDATED DATE :- ')->getFont()->setBold(true);
                $richText2->createText(Displaydateformat($first_aider->last_updated_date));
                $sheet->getCell("K" . ($currentRow + 3))->setValue($richText2);

                $sheet->getStyle("A" . ($currentRow + 3) . ":S" . ($currentRow + 3))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // Table Header
                $headerRow = $currentRow + 4;
                $sheet->mergeCells("A$headerRow:C$headerRow")->setCellValue("A$headerRow", "SERIAL NO");
                $sheet->mergeCells("D$headerRow:G$headerRow")->setCellValue("D$headerRow", "NAME OF THE EMPLOYEE");
                $sheet->mergeCells("H$headerRow:J$headerRow")->setCellValue("H$headerRow", "DESIGNATION");
                $sheet->mergeCells("K$headerRow:M$headerRow")->setCellValue("K$headerRow", "DEPARTMENT");
                $sheet->mergeCells("N$headerRow:P$headerRow")->setCellValue("N$headerRow", "UNIT");
                $sheet->mergeCells("Q$headerRow:S$headerRow")->setCellValue("Q$headerRow", "MOBILE NUMBER");

                $sheet->getStyle("A$headerRow:S$headerRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'font' => ['bold' => true],
                ]);

                // Details Rows
                $inspectionRow = $headerRow + 1;
                foreach ($first_aider_details as $index => $detail) {
                    $empName = getEmployeename($detail->emp_id) ?? '-';
                    $designation = $detail->designation_id ?? '-';
                    $department = getDepartment($detail->department_id) ?? '-';
                    $unit = getUnitname($detail->unit_id) ?? '-';
                    $mobile = $detail->mobile_no ?? '-';

                    $sheet->mergeCells("A$inspectionRow:C$inspectionRow")->setCellValue("A$inspectionRow", $index + 1);
                    $sheet->mergeCells("D$inspectionRow:G$inspectionRow")->setCellValue("D$inspectionRow", $empName);
                    $sheet->mergeCells("H$inspectionRow:J$inspectionRow")->setCellValue("H$inspectionRow", $designation);
                    $sheet->mergeCells("K$inspectionRow:M$inspectionRow")->setCellValue("K$inspectionRow", $department);
                    $sheet->mergeCells("N$inspectionRow:P$inspectionRow")->setCellValue("N$inspectionRow", $unit);
                    $sheet->mergeCells("Q$inspectionRow:S$inspectionRow")->setCellValue("Q$inspectionRow", $mobile);

                    $sheet->getStyle("A$inspectionRow:S$inspectionRow")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $inspectionRow++;
                }

                // Leave space before next record
                $row = $inspectionRow + 2;
            }

            // Set headers for download
            $filename = 'First Aider List.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            return back()->with('error', 'Excel Export Failed: ' . $e->getMessage());
        }
    }




    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->first_aider->exportdata();
            $document_no = $this->document_reference->selectUsingName('FirstAiderList');

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }



            $data = array(

                'content' => $allData,
                'document_no' => $document_no,
                'pagetitle' => "First Aider List",
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

            $view = view('inspection.inspection_ohc.first_aider_list.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "First Aider List.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aider/list'));
        }
    }

    public function employeename(Request $request)
    {
        $unit_id = decryptId($request->input('unit_id'));
        $department_id = decryptId($request->input('department'));
        $employees = Employee::where('unit', $unit_id)
            ->where('department', $department_id)
            ->get();

        if ($employees->isNotEmpty()) {
            return response()->json([
                'employee' => $employees->map(function ($employee) {
                    return [
                        'id' => encryptId($employee->id),
                        'emp_name' => $employee->emp_name,
                    ];
                }),
            ]);
        } else {
            return response()->json([
                'message' => 'Employee not found'
            ], 404);
        }
    }


    public function employeedetails(Request $request)
    {
        $id = decryptId($request->input('emp_name'));
        $employee = Employee::find($id);

        if ($employee) {
            return response()->json([
                'employee' => [
                    'designation' => $employee->designation,
                    'mobile_no' => $employee->mobile_no
                ]
            ]);
        } else {
            return response()->json(['message' => 'Employee not found'], 404);
        }
    }


    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $document_no = $this->document_reference->selectUsingName('FirstAiderList');

            $first_aider = $this->first_aider->Selectone($id);
            $first_aider_details = $this->first_aider_details->Selectone($id);

            $sheet->mergeCells("A1:F3");
            $sheet->getStyle("A1:F3")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
            ]);
            $logoLeftPath = public_path('assets/images/logo-dark.png');
            if (file_exists($logoLeftPath)) {
                $drawing = new Drawing();
                $drawing->setName('Left Logo');
                $drawing->setPath($logoLeftPath);
                $drawing->setCoordinates('B1');
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setWidth(60);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
            }

            $sheet->mergeCells("G1:M3");
            $sheet->setCellValue("G1", "FIRST AIDER LIST PN INTERNATIONAL PVT. LTD.");
            $sheet->getStyle("G1")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $headerLabels = [
                'N1:P1' => 'Doc. No.',
                'N2:P2' => 'Issue Dt.',
                'N3:P3' => 'Rev. & Dt.',
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

            $sheet->mergeCells("Q1:S1")->setCellValue("Q1", $document_no->doc_no);
            $sheet->mergeCells("Q2:S2")->setCellValue("Q2", Displaydateformat($document_no->issue_date));
            $sheet->mergeCells("Q3:S3")->setCellValue("Q3", $document_no->rev_dt);

            $sheet->getStyle("N1:S3")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['argb' => '000000']]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells("A4:J4");
            $richText1 = new RichText();
            $richText1->createTextRun(' NEXT REVIEW DATE:- ')->getFont()->setBold(true);
            $richText1->createText(Displaydateformat($first_aider->last_updated_date));
            $sheet->getCell("A4")->setValue($richText1);

            $sheet->mergeCells("K4:S4");
            $richText2 = new RichText();
            $richText2->createTextRun(' LAST UPDATED DATE :- ')->getFont()->setBold(true);
            $richText2->createText(Displaydateformat($first_aider->next_review_date));
            $sheet->getCell("K4")->setValue($richText2);

            $sheet->getStyle("A4:S4")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);






            $sheet->mergeCells("A5:C5")->setCellValue("A5", "SERIAL NO");
            $sheet->mergeCells("D5:G5")->setCellValue("D5", "NAME OF THE EMPLOYEE");
            $sheet->mergeCells("H5:J5")->setCellValue("H5", "DESIGNATION");
            $sheet->mergeCells("K5:M5")->setCellValue("K5", "DEPARTMENT");
            $sheet->mergeCells("N5:P5")->setCellValue("N5", "UNIT");
            $sheet->mergeCells("Q5:S5")->setCellValue("Q5", "MOBILE NUMER");

            $sheet->getStyle("A5:S5")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font' => ['bold' => true],
            ]);

            $row = 6;
            foreach ($first_aider_details as $index => $detail) {

                $sheet->mergeCells("A$row:C$row")->setCellValue("A$row", $index + 1);
                $empName = getEmployeename($detail->emp_id) ?? '-';

                $designation = $detail->designation_id ?? '-';
                $department = getDepartment($detail->department_id) ?? '-';
                $unit = getUnitname($detail->unit_id) ?? '-';
                $mobile = $detail->mobile_no ?? '-';
                $sheet->mergeCells("A$row:C$row")->setCellValue("A$row", $index + 1);

                // NAME OF EMPLOYEE
                $sheet->mergeCells("D$row:G$row")->setCellValue("D$row", $empName);

                // DESIGNATION
                $sheet->mergeCells("H$row:J$row")->setCellValue("H$row", $designation);

                // DEPARTMENT
                $sheet->mergeCells("K$row:M$row")->setCellValue("K$row", $department);

                // UNIT
                $sheet->mergeCells("N$row:P$row")->setCellValue("N$row", $unit);

                // MOBILE NUMBER
                $sheet->mergeCells("Q$row:S$row")->setCellValue("Q$row", $mobile);

                // Styling
                $sheet->getStyle("A$row:S$row")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);


                $row++;
            }


            $fileName = 'first aider list.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/medical-requisition-slip/fdo-security-gate/list'));
        }
    }
}
