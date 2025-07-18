<?php

namespace App\Http\Controllers\Inspection\Audit;

use App\Http\Controllers\Controller;
use App\Models\Inspection\audit\Master\ComplianceCategory;
use App\Models\Inspection\audit\Master\Task;
use App\Models\Inspection\audit\MonthlyAuditPlan;
use App\Models\Inspection\Master\Frequency;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Spatie\SimpleExcel\SimpleExcelWriter;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;



class MonthlyAuditPlanController extends Controller
{
    private $unit;
    private $audit_task;
    private $frequency;
    private $monthly_audit_plan;
    private $category;

    public function __construct()
    {
        $this->unit = new Unit();
        $this->audit_task = new Task();
        $this->frequency = new Frequency();
        $this->monthly_audit_plan = new MonthlyAuditPlan();
        $this->category = new ComplianceCategory();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->monthly_audit_plan->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        // ->addColumn('status', function ($row) {
                        //     $text = "<span style='color:red'>In-Active</span>";
                        //     // if (CheckUserRole(ROLE_SUPERADMIN)) {
                        //     if ($row->status == 1) {
                        //         $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type = '1'>Active</span>";
                        //     } else if ($row->status == 0) {
                        //         $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type = '0'>In-Active</span>";
                        //     }
                        //     // }
                        //     return $text;
                        // })
                        ->addColumn('complaince_category', function ($row) {
                            return getCategoryType($row->compliance_category_id);
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('audit/monthly-audit/audit-plan/view/' . encryptId($row->id)) . '"   class="view-icon me-1 " title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('audit/monthly-audit/audit-plan/generalpdf/' . encryptId($row->id)) . '"  class="me-1" style="margin-right: 5px;" title="PDF"><i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i></a>';
                            $btn .= '<a href="' . admin_url('audit/monthly-audit/audit-plan/generalExcel/' . encryptId($row->id)) . '"  class="me-1" style="margin-right: 5px;" title="PDF"> <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i></a>';
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                            // $btn .= '<a href="' . admin_url('audit/master/task/edit/' . encryptId($row->id)) . '" class="edit-icon " title="' . __('common.edit') . '"><i class="fa-solid fa-pen-to-square"></i> ';
                            // $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '"  class="recordDelete" title="' . __('common.delete') . '"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            // }
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'complaince_category'])
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
        $unit = $this->unit->getunit();
        $audit_task = $this->audit_task->getAuditTask();
        $companyId = $request->company_id;
        $fromdate = $request->fromDate;
        $toDate = $request->toDate;

        $data = array(
            'unit' => $unit,
            'audit_task' => $audit_task,
            'fromdate' => $fromdate,
            'toDate' => $toDate,
            'companyId' => $companyId,

        );
        return view('inspection.inspection_audit.monthlyAudit.list', $data);
    }


    public function Add(Request $request)
    {
        try {
            $unitList = $this->unit->getUnitList();
            $audit_task = $this->audit_task->getAuditTask();
            $frequency = $this->frequency->getFrequency();
            $categories = $this->category->GetCategory();


            $data = array(
                'unitList' => $unitList,
                'audit_task' => $audit_task,
                'frequency' => $frequency,
                'categories' => $categories
            );
            return view('inspection.inspection_audit.monthlyAudit.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {
            $rules = [
                'monthly_audit.*.auditee_name' => 'required',
                'monthly_audit.*.unit_id' => 'required',
                'monthly_audit.*.task_name' => 'required',
                'monthly_audit.*.compliance_category' => 'required',
                'monthly_audit.*.reference_doc_no' => 'required',
                'monthly_audit.*.frequency_id' => 'required',
                'monthly_audit.*.direct_in_direct' => 'required',
                'monthly_audit.*.points' => 'required',
                'monthly_audit.*.remark' => 'required',
            ];

            $messages = [
                'monthly_audit.*.auditee_name.required' => 'Auditee name is required.',
                'monthly_audit.*.unit_id.required' => 'Unit ID is required.',
                'monthly_audit.*.task_name.required' => 'Task name is required.',
                'monthly_audit.*.compliance_category.required' => 'Compliance category is required.',
                'monthly_audit.*.reference_doc_no.required' => 'Reference document number is required.',
                'monthly_audit.*.frequency_id.required' => 'Frequency ID is required.',
                'monthly_audit.*.direct_in_direct.required' => 'Direct/Indirect field is required.',
                'monthly_audit.*.points.required' => 'Points field is required.',
                'monthly_audit.*.remark.required' => 'Remark must required',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = $this->monthly_audit_plan->store();


            Session::flash('success', 'Your data has been created successfully!');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('audit/monthly-audit/audit-plan/list'));
        }
        return redirect(admin_url('audit/monthly-audit/audit-plan/list'));
    }



    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $monthly_audit_plan = $this->monthly_audit_plan->selectOne($id);

                $data = array(
                    'monthly_audit_plan' => $monthly_audit_plan,
                );
            }
            return view('inspection.inspection_audit.monthlyAudit.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('audit/monthly-audit/audit-plan/list'));
        }
    }





    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->monthly_audit_plan->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $logoPath = public_path('assets/images/logo-dark.png');
            if (file_exists($logoPath)) {
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Company Logo');
                $drawing->setPath($logoPath);
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
            }

            $sheet->mergeCells('A1:B3');
            $sheet->getStyle('A1:B3')->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);

            $sheet->setCellValue('C1', 'Monthly EHS Audit');
            $sheet->mergeCells('C1:G3');
            $sheet->getStyle("C1:G3")->applyFromArray([
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);

            $columnWidths = [
                'A' => 8,
                'B' => 25,
                'C' => 30,
                'D' => 25,
                'E' => 20,
                'F' => 15,
                'G' => 20,
            ];

            foreach ($columnWidths as $col => $width) {
                $sheet->getColumnDimension($col)->setWidth($width);
            }

            $row = 4;

            foreach ($allData as $unitName => $records) {

                $sheet->setCellValue("A{$row}", strtoupper($unitName));
                $sheet->mergeCells("A{$row}:G{$row}");
                $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '000000']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'ffb9bf']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(25);
                $row++;

                $sheet->fromArray([
                    'S.No',
                    'Auditee Name',
                    'Task Name',
                    'Reference Doc No',
                    'Category',
                    'Frequency',
                    'Created At'
                ], NULL, "A{$row}");

                $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(20);
                $row++;

                $sr = 1;
                foreach ($records as $item) {
                    $sheet->fromArray([
                        $sr,
                        $item->auditee_name,
                        $item->task_name,
                        $item->reference_doc_no,
                        getCategoryType($item->compliance_category_id),
                        getFrequencyname($item->frequency_id),
                        displayDateformat($item->created_at),
                    ], null, "A{$row}");

                    $sheet->getStyle("A{$row}:G{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    $row++;
                    $sr++;
                }

                $row += 1;
            }

            $lastRow = $sheet->getHighestRow();
            $sheet->getStyle("A3:G{$lastRow}")->applyFromArray([
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['argb' => '000000']
                    ]
                ]
            ]);

            $fileName = 'Monthly_Audit_Plan.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment;filename=\"$fileName\"");
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('audit/monthly-audit/audit-plan/list'));
        }
    }


    public function ExportPdf(Request $request)
    {

        try {

            ini_set("pcre.backtrack_limit", "5000000");

            $allData = $this->monthly_audit_plan->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }



            $data = array(
                'content' => $allData,
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

            $view = view('inspection.inspection_audit.monthlyAudit.pdf', $data);
            $html = $view->render();
            $mpdf->WriteHTML($html);

            $filename = "Monthly Audit Plan Details.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('audit/monthly-audit/audit-plan/list'));
        }
    }

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $monthly_audit_plan = $this->monthly_audit_plan->selectOne($id);

                $data = array(
                    'monthly_audit_plan' => $monthly_audit_plan,
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

            $html = view('inspection.inspection_audit.monthlyAudit.generalpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "Monthly Audit Plan Details.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('audit/monthly-audit/audit-plan/list'));
        }
    }

    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $monthly_audit_plan = $this->monthly_audit_plan->Selectone($id);
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

            // Merge and style A1:B3
            $sheet->mergeCells('A1:B3');
            $sheet->getStyle('A1:B3')->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                ],
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

            // Merge and style C1:K3
            $sheet->mergeCells('C1:K3');
            $sheet->setCellValue('C1', "Monthly EHS Audit");
            $sheet->getStyle('C1:K3')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 14,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);

            $headers = [
                'Sr.',
                'Auditee Name',
                'Unit',
                'Task Name',
                'Reference Doc No',
                'Category',
                'Frequency',
                'Direct/Indirect',
                'Status',
                'Points',
                'Remarks'
            ];
            $colIndex = 'A';
            foreach ($headers as $header) {
                $sheet->getRowDimension(4)->setRowHeight(25);

                $sheet->setCellValue("{$colIndex}4", $header);
                $sheet->getStyle("{$colIndex}4")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FF0000']
                    ]
                ]);

                $colIndex++;
            }

            $row = 5;
            $sheet->setCellValue("A{$row}", '1');
            $sheet->setCellValue("B{$row}", $monthly_audit_plan->auditee_name ?? '');
            $sheet->setCellValue("C{$row}", getUnitname($monthly_audit_plan->unit_id ?? ''));
            $sheet->setCellValue("D{$row}", getTaskName($monthly_audit_plan->task_id ?? ''));
            $sheet->setCellValue("E{$row}", $monthly_audit_plan->reference_doc_no ?? '');
            $sheet->setCellValue("F{$row}", getCategoryType($monthly_audit_plan->compliance_category_id ?? ''));
            $sheet->setCellValue("G{$row}", getFrequencyname($monthly_audit_plan->frequency_id ?? ''));
            $sheet->setCellValue("H{$row}", $monthly_audit_plan->direct_in_direct == 1 ? 'Direct' : 'Indirect');
            $sheet->setCellValue("I{$row}", $monthly_audit_plan->audit_plan_status == 1 ? 'YES' : 'NO');
            $sheet->setCellValue("J{$row}", $monthly_audit_plan->points ?? '');
            $sheet->setCellValue("K{$row}", $monthly_audit_plan->remarks ?? '');

            $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            foreach (range('A', 'J') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $fileName = 'Monthly_Audit_Plan.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('audit/monthly-audit/audit-plan/list'));
        }
    }
}
