<?php


namespace App\Http\Controllers\OhcManagement;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Jobs\Ohc\ImportRequisitionjob;
use App\Mail\Ohc\MedicineRequisitionEmail;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\Unit;
use App\Models\OhcManagement\Master\Medicine;
use App\Models\OhcManagement\Master\Vendor;
use App\Models\OhcManagement\MedicalFitnessCertificate;
use App\Models\OhcManagement\MedicineRequisition;
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
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;
use App\Models\OhcManagement\MedicineStock;
use App\Models\OhcManagement\OhcStatuslog;
use App\Models\OhcManagement\Report\Inventory;
use App\Models\User;
use Illuminate\Support\Facades\File;


class MedicalFitnessCertificateController extends Controller
{

    private $medical_fitness_certificate;


    public function __construct()
    {

        $this->medical_fitness_certificate = new MedicalFitnessCertificate();

    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->medical_fitness_certificate->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active<span>";
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->id) . "' data-type = '1' >Active<span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->id) . "' data-type = '0' >In-Active<span>";
                            }
                            return $text;
                        })

                        ->addColumn('approve_status', function ($row) {

                            if ($row->created_by == Auth::id()) {
                                if ($row->approve_status == STATUS_OHC_PARAMEDICS_APPROVED) {
                                    $text = "<span class='badge bg-info' style='font-size: 1.0em;'>Open</span>";
                                }
                            }

                            if ($row->approve_status == STATUS_OHC_PARAMEDICS_APPROVAL_PENDING) {
                                $text = "<span class='badge bg-info' style='font-size: 1.0em;'>Paramedics Approval Pending</span>";
                            } else if ($row->approve_status == STATUS_OHC_PARAMEDICS_APPROVED) {
                                $text = "<span class='badge bg-success' style='font-size: 1.0em;'>Paramedics Approved</span>";
                            } else if ($row->approve_status == STATUS_OHC_PARAMEDICS_REJECTED) {
                                $text = "<span class='badge bg-danger' style='font-size: 1.0em;'>Paramedics Rejected</span>";
                            } else if ($row->approve_status == STATUS_OHC_OPEN) {
                                $text = "<span class='badge bg-info' style='font-size: 1.0em;'>Open</span>";
                            } else if ($row->approve_status == STATUS_OHC_CLOSE) {
                                $text = "<span class='badge bg-success' style='font-size: 1.0em;'>Close</span>";
                            }
                            return $text;
                        })
                        ->editColumn('request_date', function ($row) {
                            return displaydateformat($row->request_date);
                        })
                        ->editColumn('unit_id', function ($row) {
                            return getUnitname($row->unit_id);
                        })
                        ->editColumn('department_id', function ($row) {
                            return getDepartment($row->department_id);
                        })
                        ->editColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                            $btn .= '<a href="' . admin_url('ohc/medical-fitness/view/' . encryptId($row->id)) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            if (CheckUserPermission('edit') && $row->approve_status == STATUS_OHC_PARAMEDICS_APPROVAL_PENDING) {
                            $btn .= '<a href="' . admin_url('ohc/medical-fitness/edit/' . encryptId($row->id)) . '" class="" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a> ';
                            }
                            if ((checkUserRole(ROLE_SUPERADMIN) && $row->approve_status == STATUS_OHC_PARAMEDICS_APPROVAL_PENDING) || (checkUserRole(ROLE_PARAMEDICS) && $row->approve_status == STATUS_OHC_PARAMEDICS_APPROVAL_PENDING)) {
                                $btn .= '<a href="' . admin_url('ohc/medical-fitness/approval/view/' . encryptId($row->id)) . '" class="" title="Action"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }


                            $btn .= '<a href="' . admin_url('ohc/medical-fitness/generalpdf/' . encryptId($row->id)) . '" class="" title="PDF"> <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i></a> ';
                            return $btn;
                        })

                        ->rawColumns(['action', 'request_date', 'approve_status', 'unit_id', 'department_id'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);

                    return response()->json($datatables->getData());
                } catch (Exception $ex) {
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => __('ppe.please_try_after_some_time')], 406);
                }
            }
        }


        $data = array(

        );

        return view('ohcmanagement.medical_fitness_certificate.list', $data);
    }

    public function add()
    {
        try {

            return view('ohcmanagement.medical_fitness_certificate.add');
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function store(Request $request){

    }
}
