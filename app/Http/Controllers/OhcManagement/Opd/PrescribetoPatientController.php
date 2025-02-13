<?php

namespace App\Http\Controllers\OhcManagement\Opd;

use App\Http\Controllers\Controller;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\Unit;
use App\Models\Master\Work;
use App\Models\OhcManagement\Master\CertifiedFirstAider;
use App\Models\OhcManagement\Master\Medicine;
use App\Models\OhcManagement\Master\Vendor;
use App\Models\OhcManagement\UserMedicineIssuance;
use App\Models\OhcManagement\MedicineIssuance;
use App\Models\OhcManagement\MedicineReceiving;
use App\Models\OhcManagement\Opd\PatientStatus;
use App\Models\OhcManagement\Opd\ReferedVechicle;
use App\Models\OhcManagement\Opd\Suggestedby;
use App\Models\OhcManagement\UserMedicineRequisition;
use App\Models\UploadLog;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use phpseclib3\File\ASN1\Maps\CertificateIssuer;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;

class PrescribetoPatientController extends Controller
{

    private $medicine;
    private $vendor;
    private $user_medicine_issuance;
    private $unit;
    private $department;
    private $medicine_issuance;
    private $medicine_receiving;
    private $work;
    private $employee;
    private $suggestedBy;
    private $refered_vechicle;
    private $patient_status;

    public function __construct()
    {
        $this->medicine = new Medicine();
        $this->vendor = new Vendor();
        $this->user_medicine_issuance = new UserMedicineIssuance();
        $this->medicine_issuance = new MedicineIssuance();
        $this->medicine_receiving = new MedicineReceiving();
        $this->employee = new Employee();
        $this->work = new Work();
        $this->suggestedBy = new Suggestedby();
        $this->refered_vechicle = new ReferedVechicle();
        $this->patient_status = new PatientStatus();

        $this->unit = new Unit();
        $this->department = new Department();
    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->user_medicine_issuance->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->editColumn('unit_id', function ($row) {
                            return $row->unit_name;
                        })
                        ->editColumn('department_id', function ($row) {
                            return $row->department_name;
                        })

                        ->editColumn('issue_date', function ($row) {
                            return displaydateformat($row->issue_date);
                        })
                        ->editColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                            $btn .= '<a href="' . admin_url('ohc/prescribe-to-patient/view/' . encryptId($row->id)) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                            $btn .= '<a href="' . admin_url('ohc/prescribe-to-patient/edit/' . encryptId($row->id)) . '" class="" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a> ';
                            // }
                            // $btn .= '<a href="javascript:void(0);" data-id="' . encryptId($row->id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger"></i></a> ';


                            return $btn;
                        })

                        ->rawColumns(['action', 'request_date'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);

                    return response()->json($datatables->getData());
                } catch (Exception $ex) {
                    dd($ex);
                    return response()->json(['status' => 'error', 'msg' => __('ppe.please_try_after_some_time')], 406);
                }
            }
        }

        $unit = $this->unit->getunit();
        $data = array(

            'unit' => $unit
        );

        return view('ohcmanagement.ohc-opd.prescribe-to-patient.list', $data);
    }

    public function add()
    {
        try {
            $unit = $this->unit->getunit();
            $suggestedBy = $this->suggestedBy->getSuggestedBy();
            $reffered = $this->refered_vechicle->getreffered();
            $patientstatus = $this->patient_status->getpatientstatus();

            $data = array(
                'unit' => $unit,
                'suggestedBy'=>$suggestedBy,
                'reffered'=>$reffered,
                'patientstatus'=>$patientstatus,

            );
            return view('ohcmanagement.ohc-opd.prescribe-to-patient.add',$data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/prescribe-to-patient/list'));
        }
    }

    // Fetch of employee and worker name

    public function fetchemployeename(Request $request)
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

    // fetching the first aiders

    public function firstaider(Request $request){
        $search = $request->input('search');

        $employees = CertifiedFirstAider::where(function ($query) use ($search) {
            $query->where('certifier_name', 'like', '%' . $search . '%')
                ->orWhere('emp_id', 'like', '%' . $search . '%');
        })
            ->where('status', 1)
            ->limit(10)
            ->get();

        return response()->json(
            $employees->map(function ($employee) {
                return [
                    'id' => $employee->emp_id,
                    'text' => $employee->certifier_name . ' - ' . $employee->emp_id,
                ];
            })
        );
    }

    // first aider mobile number

    public function firstaidernumber(Request $request)
    {
        $empID = $request->input('empId');
        $employee = CertifiedFirstAider::where('emp_id', $empID)->where('trash', 'no')->where('status', 1)->first();
        return response()->json(
            $employee->mobile_no

        );
    }

}
