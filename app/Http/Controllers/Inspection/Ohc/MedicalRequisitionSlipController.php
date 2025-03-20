<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Department;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\Ohc\InspectionOhcStatuslog;
use App\Models\Inspection\Ohc\MedicineRequisitionSlipFloor;
use App\Models\Inspection\Ohc\MedicineRequistionSlipfloordetails;
use App\Models\OhcManagement\Report\Inventory;
use App\Models\UploadLog;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;


class MedicalRequisitionSlipController extends Controller
{

    private $upload_log;
    private $unit;
    private $shift;
    private $department;
    private $user;
    private $medicine_requisition_floor_checklist;
    private $medicine_requisition_floor_details;
    private $inspection_ohc_status_log;

    private $inventory;


    private $location;
    public function __construct()
    {

        $this->upload_log = new UploadLog();
        $this->unit = new Unit();
        $this->department = new Department();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->medicine_requisition_floor_checklist = new MedicineRequisitionSlipFloor();
        $this->inventory = new Inventory();
        $this->user = new User();
        $this->inspection_ohc_status_log = new InspectionOhcStatuslog();

        $this->medicine_requisition_floor_details = new MedicineRequistionSlipfloordetails();
    }
    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->medicine_requisition_floor_details->list();

                    $datatables = Datatables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type='1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type='0'>In-Active</span>";
                            }
                            return $text;
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('approve_status', function ($row) {
                            $text = '';
                            switch ($row->approve_status) {
                                case FLOOR_MANAGER_APPROVAL_PENDING:
                                    $text = "<span class='badge bg-info rounded' style='font-size: 1.0em;'>Floor Manager Approval Pending</span>";
                                    break;
                                case FLOOR_MANAGER_APPROVED:
                                    $text = "<span class='badge bg-success rounded' style='font-size: 1.0em;'>Floor Manager Approved</span>";
                                    break;
                                case FLOOR_MANAGER_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>Floor Manager Rejected</span>";
                                    break;
                                case SAFETY_OFFICER_APPROVAL_PENDING:
                                    $text = "<span class='badge bg-INFO rounded' style='font-size: 1.0em;'>Safety Officer Approval Pending</span>";
                                    break;
                                case SAFETY_OFFICER_APPROVED:
                                    $text = "<span class='badge bg-success rounded' style='font-size: 1.0em;'>Safety Officer Approved</span>";
                                    break;

                                default:
                                    $text = "<span class='badge rounded-pill text-bg-warning'>Unknown</span>";
                            }
                            return $text;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn .= '<a href="' . admin_url('ohc/medical-requisition-slip/view/' . encryptId($row->id)) . '" class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a>';
                            // if ((checkUserRole(ROLE_SUPERADMIN) && $row->approve_status == STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING) || (checkUserRole(ROLE_DOCTOR) && $row->approve_status == STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING)  || ((checkUserRole(ROLE_EHS_HEAD) && $row->approve_status == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING) || (checkUserRole(ROLE_SUPERADMIN) && $row->approve_status == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING))) {
                            $btn .= '<a href="' . admin_url('ohc/medical-requisition-slip/approval/view/' . encryptId($row->id)) . '" class="" title="Action"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            // }
                            return   $btn;
                        })
                        ->rawColumns(['action', 'issue_date', 'created_by', 'approve_status', 'issue_date'])
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

        return view('inspection.inspection_ohc.medical_requisition_slip.list');
    }

    public function Add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $shift = $this->shift->getShiftname();
            $medicine = $this->inventory->getstockdata();
            $signature_upload = $this->user->getSignature();
            $location = $this->location->getLocationname();
            $data = array(
                'unit' => $unit,
                'shift' => $shift,
                'medicine' => $medicine,
                'signature_upload' => $signature_upload,

            );
            return view('inspection.inspection_ohc.medical_requisition_slip.add', $data);
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
                // Store user medicine requisition
                $medicine_requisition_floor_details = $this->medicine_requisition_floor_details->store();
                $medicine_requisition_floor_checklist = $this->medicine_requisition_floor_checklist->store($medicine_requisition_floor_details);
                $data = [
                    'type' => OHC_TYPE_MEDICINE_REQUISTION_FLOOR,
                    'from_status' => OHC_CREATION,
                    'to_status' => FLOOR_MANAGER_APPROVAL_PENDING,
                    'reference_id' => $medicine_requisition_floor_details->id,
                    'remarks' => "",

                ];

                $this->inspection_ohc_status_log->store($data);

                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medical-requisition-slip/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-requisition-slip/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $medicinerequisition = $this->medicine_requisition_floor_details->Selectone($id);
                $medicine_requisition_floor_checklist = $this->medicine_requisition_floor_checklist->Selectone($id);

                $data = array(
                    'medicinerequisition' => $medicinerequisition,
                    'medicine_requisition_floor_checklist' => $medicine_requisition_floor_checklist,
                );
            }
            return view('inspection.inspection_ohc.medical_requisition_slip.view', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function approval(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $medicinerequisition = $this->medicine_requisition_floor_details->Selectone($id);
                $medicine_requisition_floor_checklist = $this->medicine_requisition_floor_checklist->Selectone($id);

                $data = array(
                    'medicinerequisition' => $medicinerequisition,
                    'medicine_requisition_floor_checklist' => $medicine_requisition_floor_checklist,
                );
            }
            return view('inspection.inspection_ohc.medical_requisition_slip.approval', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function safetyofficerapproval(Request $request)
    {
        try {
            $id = decryptId($request->id);


            try {

                $this->medicine_requisition_floor_details->safetyofficerapprovalupdate($id);
                Session::flash('success', 'Your data has been Responded successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medical-requisition-slip/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-requisition-slip/list'));
        }
    }

    public function floormanagerapproval(Request $request)
    {
        try {
            $id = decryptId($request->id);


            try {

                    $approveStatus = FLOOR_MANAGER_APPROVED ?? FLOOR_MANAGER_REJECTED;

                    $data = [
                        'type' => OHC_TYPE_MEDICINE_REQUISTION_FLOOR,
                        'from_status' => FLOOR_MANAGER_APPROVAL_PENDING,
                        'to_status' => $approveStatus,
                        'reference_id' =>$id,
                        'remarks' => $request->remarks,

                    ];

                    $this->inspection_ohc_status_log->store($data);
                    $this->medicine_requisition_floor_details->floormanagerapprovalupdate($id, $approveStatus);
              if($request->action == "approve"){

              }


                Session::flash('success', 'Your data has been Responded successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medical-requisition-slip/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-requisition-slip/list'));
        }
    }
}
