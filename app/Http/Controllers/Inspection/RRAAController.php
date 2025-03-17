<?php

namespace App\Http\Controllers\Inspection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inspection\RRAADetails;
use App\Models\Inspection\RRAACheckList;
use Exception;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Master\Employee;
use App\Models\Master\Work;

class RRAAController extends Controller
{

    private $rraa_details;
    private $rraa_checkList;
    private $employee;
    private $work;
    private $frequency;

    public function __construct()
    {
        $this->rraa_details = new RRAADetails();
        $this->rraa_checkList = new RRAACheckList();
        $this->employee = new Employee();
        $this->work = new Work();
        
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->rraa_details->list();
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
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('rraa/ohc_fire_environment_compliance/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                        
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status'])
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

        return view('inspection.rraa.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $frequency = $this->frequency->getFrequency();
            $data = [
                'frequency' => $frequency,
            ];
            return view('inspection.rraa.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
       
        try {
            $rules = [
                'document_number' => 'required',
                'issue_date' => 'required',
                'revision_date' => 'required',
                'serial_number' => 'required',
                'category' => 'required',
                'ohs_compliance_index' => 'required',
                'frequency' => 'required',
                'scope' => 'required',
                'responsibility' => 'required',
                'authority' => 'required',
                'accountability' => 'required',
                'remark' => 'required',

            ];
            $messages = [
                'document_number.required' => __('Document Number is required'),
                'issue_date.required' => __('Issue Date is required'),
                'revision_date.required' => __('Revision Date is required'),
                'serial_number.required' => __('Serial Number is required'),
                'category.required' => __('category is required'),
                'ohs_compliance_index.required' => __('OHS Compliance Index is required'),
                'frequency.required' => __('Frequency is required'),
                'scope.required' => __('scope is required'),
                'responsibility.required' => __('Responsibility is required'),
                'authority.required' => __('authority is required'),
                'accountability.required' => __('accountability is required'),
                'remark.required' => __('remark is required'),

            ];
         
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            
            try {

               $rraa = $this->rraa_details->store();
               $rraa_id = $rraa->id;
               $this->rraa_checkList->store($rraa_id);

                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('rraa/ohc_fire_environment_compliance/list'));
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('rraa/ohc_fire_environment_compliance/list'));
        }
    }

    public function employeeid(Request $request)
    {
        $name = $request->input('search');

        $employee_code = $this->employee->where('emp_id', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->limit(10)
            ->get();

        $work = $this->work->where('emp_id', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->limit(10)
            ->get();


        $mergedResults = $employee_code->merge($work);

        return response()->json(
            $mergedResults->map(function ($employee) {
                return [
                    'id' => $employee->emp_id,
                    'text' => $employee->emp_id . ' - ' . $employee->emp_name,
                ];
            })
        );
    }

}
