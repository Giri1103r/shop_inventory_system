<?php

namespace App\Http\Controllers\OhcManagement\SafetyPettyLogbook;

use App\Http\Controllers\Controller;
use App\Models\Master\Employee;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\OhcManagement\SafetyPettyLogbook\SafetyPettyChecklist;
use App\Models\OhcManagement\SafetyPettyLogbook\SafetyPettyDetails;
use Exception;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Master\Work;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Models\User;

class SafetyPettyController extends Controller
{
    private $sfty_petty_details;
    private $sfty_petty_checklist;
    private $employee;
    private $work;
    private $unit;
    private $signature;
    private $user;

    public function __construct()
    {
        $this->sfty_petty_details = new SafetyPettyDetails();
        $this->sfty_petty_checklist = new SafetyPettyChecklist();
        $this->employee = new Employee();
        $this->work = new Work();
        $this->unit = new Unit();
        $this->signature = new OhcSignature();
        $this->user = new User();

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
                            $btn = '<a href="' . admin_url('ohc/safety-petty-logbook/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('ohc/safety-petty-logbook/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                                <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
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

        $data = [];

        return view('ohcmanagement.safety_petty.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $type = OHC_SAFETY_PETTY_LOGBOOK_INSPECTION;
            $unit = $this->unit->getunit();
            $data = [
                'unit' => $unit,
            ];
            return view('ohcmanagement.safety_petty.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {

            $sfty_petty_details = $this->sfty_petty_details->store();
            $sfty_petty_id = $sfty_petty_details->id;
            $this->sfty_petty_checklist->store($sfty_petty_id);
            $empId =  Auth::user()->employee_id;
           
            $this->signature->signatureLogUpload(
                $empId, $sfty_petty_id ,
                OHC_AMOUNT_GIVENBY_INSPECTION, 
                'signature_givenby_image' 
            );
    
            $this->signature->signatureLogUpload(
                  $empId, $sfty_petty_id ,
                OHC_AMOUNT_RECEIVEDBY_INSPECTION, 
                'signature_receivedby_image'
            );

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

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $sfty_petty_details = $this->sfty_petty_details->find($id);
                $sfty_petty_checklist = $this->sfty_petty_checklist->selectOne($id);

                $type = OHC_SAFETY_PETTY_LOGBOOK_INSPECTION;
                $sub_type_given = OHC_AMOUNT_GIVENBY_INSPECTION;
                $sub_type_received = OHC_AMOUNT_RECEIVEDBY_INSPECTION;

                $signature_amount = $this->signature->getLogByTypeAndSubType($type,$sub_type_given,$sub_type_received);
                // $signature_received_by = $this->signature->getLogReceivedby($type,$sub_type_received);

                $data = array(
                    'sfty_petty_details' => $sfty_petty_details,
                    'sfty_petty_checklist' => $sfty_petty_checklist  ?? [],
                    // 'signature_given_by' => $signature_given_by,
                    'signature_amount' => $signature_amount,
                );
            }
            return view('ohcmanagement.safety_petty.view', $data);
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
        }
    }

}
