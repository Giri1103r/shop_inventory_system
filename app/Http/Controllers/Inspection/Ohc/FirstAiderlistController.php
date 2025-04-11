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
                            $btn ="";
                            $btn .= '<a href="' . admin_url('ohc/first-aider/view/' . encryptId($row->inspection_id)) . '" class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a>';

                            $btn .= '<a href="' . admin_url('ohc/first-aider/generalpdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
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

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->first_aider->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Review date',
                'Issued Date',
                'Last Updated Date',
                'Next Reiview Date',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->doc_no;
                $export[] =  $data->rev_dt;
                $export[] =  Displaydateformat($data->issue_date);
                $export[] = Displaydateformat($data->last_updated_date);
                $export[] = Displaydateformat($data->next_review_date);
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->inspection_created_by);
                $export[] =  Displaydateformat($data->inspection_created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('First Aider List.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aider/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->first_aider->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Review date',
                'Issued Date',
                'Last Updated Date',
                'Next Reiview Date',

                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
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
                $drawing->setOffsetX(100);
                $drawing->setOffsetY(15);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
            }

            $sheet->mergeCells("G1:M3");
            $sheet->setCellValue("G1", "FIRST AIDER LIST");
            $sheet->getStyle("G1")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
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
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['argb' => '000000']]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells("A4:J4");
            $richText1 = new RichText();
            $richText1->createTextRun('DATE OF INSPECTION :- ')->getFont()->setBold(true);
            $richText1->createText(Displaydateformat($first_aider->last_updated_date));
            $sheet->getCell("A4")->setValue($richText1);

            $sheet->mergeCells("K4:S4");
            $richText2 = new RichText();
            $richText2->createTextRun('NEXT DUE :- ')->getFont()->setBold(true);
            $richText2->createText(Displaydateformat($first_aider->next_review_date));
            $sheet->getCell("K4")->setValue($richText2);

            $sheet->getStyle("A4:S4")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);




        

            $sheet->mergeCells("A5:C5")->setCellValue("A5", "SERIAL NO");
            $sheet->mergeCells("D5:H5")->setCellValue("D5", "NAME OF THE EMPLOYEE");
            $sheet->mergeCells("I5:K5")->setCellValue("I5", "DESIGNATION");
            $sheet->mergeCells("L5:O5")->setCellValue("L5", "DEPARTMENT");
            $sheet->mergeCells("L5:O5")->setCellValue("L5", "UNIT");
            $sheet->mergeCells("L5:O5")->setCellValue("L5", "MOBILE NUMER");

            $sheet->getStyle("A5:O5")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THICK]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font' => ['bold' => true],
            ]);

            $row = 6;
            foreach ($first_aider_details as $index => $detail) {
                $sheet->mergeCells("A$row:C$row")->setCellValue("A$row", $index + 1);
                $unitName = getUnitname($detail->unit_id) ?? '';
                $deptName = getDepartment($detail->department_id) ?? '';
                $userName = getUsername($detail->emp_id) ?? '';
                $sheet->mergeCells("D$row:H$row")->setCellValue("D$row", $unitName);
                $sheet->mergeCells("D$row:H$row")->setCellValue("D$row", $deptName);
                $sheet->mergeCells("D$row:H$row")->setCellValue("D$row", $detail->mobile_no);
                $sheet->mergeCells("D$row:H$row")->setCellValue("D$row", $detail->designation_id);
                $sheet->mergeCells("D$row:H$row")->setCellValue("D$row", $detail->mobile_no);
                $sheet->mergeCells("I$row:K$row")->setCellValue("I$row", $userName);

                $sheet->getStyle("A$row:O$row")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THICK]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $row++;
            }


            $fileName = 'Medical Requisition Slip- Fdo & Security Gate.xlsx';
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
