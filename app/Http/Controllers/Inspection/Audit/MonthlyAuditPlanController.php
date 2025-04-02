<?php

namespace App\Http\Controllers\Inspection\Audit;

use App\Http\Controllers\Controller;
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


class MonthlyAuditPlanController extends Controller
{
    private $unit;
    private $audit_task;
    private $frequency;
    private $monthly_audit_plan;




    public function __construct()
    {
        $this->unit = new Unit();
        $this->audit_task = new Task();
        $this->frequency = new Frequency();
        $this->monthly_audit_plan = new MonthlyAuditPlan();

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
                            $btn = '<a href="' . admin_url('audit/monthly-audit/audit-plan/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('audit/monthly-audit/audit-plan/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF"><i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i></a>';
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

                    dd($ex);
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
        $unit = $this->unit->getunit();
        $audit_task = $this->audit_task->getAuditTask();

        $data = array(
            'unit' => $unit,
            'audit_task' => $audit_task,

        );
        return view('inspection.inspection_audit.monthlyAudit.list', $data);
    }


    public function Add(Request $request)
    {
        try {
            $unitList = $this->unit->getUnitList();
            $audit_task = $this->audit_task->getAuditTask();
            $frequency = $this->frequency->getFrequency();


            $data = array(
                'unitList' => $unitList,
                'audit_task' => $audit_task,
                'frequency' => $frequency,

            );
            return view('inspection.inspection_audit.monthlyAudit.add',$data);
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
            // dd($data);
            return view('inspection.inspection_audit.monthlyAudit.view',$data);


        } catch (Exception $ex) {
            dd($ex);
            report($ex);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {
            $allData = $this->monthly_audit_plan->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Auditee Name',
                'Unit',
                'Task Name',
                 'Complaince Category',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {
                $export = [];
                $export[] =  $i;
                $export[] =  $data->auditee_name;
                $export[] =  getUnitname($data->unit_id);
                $export[] =  getTaskName($data->task_id);
                $export[] =  getCategoryType($data->compliance_category_id);
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);
                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Monthly Audit plan.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            ini_set("pcre.backtrack_limit", "5000000");

            $allData = $this->monthly_audit_plan->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Auditee Name',
                'Unit',
                'Task Name',
                 'Complaince Category',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Monthly Audit Plan",
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

            $filename = " Monthlt Audit Plan.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
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
            // dd($data);

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
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }
    
}

