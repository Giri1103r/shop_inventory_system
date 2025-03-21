<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use App\Mail\Inspection\Ohc\MedicineRequistionFdoEmail;
use Illuminate\Http\Request;
use App\Models\Master\Department;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use App\Models\Inspection\Master\Shift;

use App\Models\Inspection\Ohc\MedicineRequistionFdoChecklist;
use App\Models\Inspection\Ohc\MedicineRequistionSlipfdodetails;
use App\Models\OhcManagement\Report\Inventory;
use App\Models\UploadLog;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Models\Inspection\Ohc\InspectionOhcStatuslog;
use Illuminate\Support\Facades\Mail;

class MedicalRequisitionSlipSecurityGateController extends Controller
{

    private $upload_log;
    private $unit;
    private $shift;
    private $department;
    private $user;
    private $medicine_requisition_fdo_checklist;
    private $inventory;
    private $medicine_requisition_fdo_details;
    private $signature;
    private $inspection_ohc_status_log;

    private $location;
    public function __construct()
    {

        $this->upload_log = new UploadLog();
        $this->unit = new Unit();
        $this->department = new Department();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->medicine_requisition_fdo_checklist = new MedicineRequistionFdoChecklist();
        $this->inventory = new Inventory();
        $this->user = new User();
        $this->signature = new OhcSignature();
        $this->inspection_ohc_status_log = new InspectionOhcStatuslog();

        $this->medicine_requisition_fdo_details = new MedicineRequistionSlipfdodetails();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->medicine_requisition_fdo_details->list();

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
                                    $text = "<span class='badge bg-info rounded' style='font-size: 1.0em;'>Safety Officer Approval Pending</span>";
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
                            $btn .= '<a href="' . admin_url('ohc/medical-requisition-slip/fdo-security-gate/view/' . encryptId($row->id)) . '" class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a>';

                            if ((checkUserRole(ROLE_SUPERADMIN) && $row->approve_status == FLOOR_MANAGER_APPROVAL_PENDING) || (checkUserRole(ROLE_FLOOR_MANAGER) && $row->approve_status == FLOOR_MANAGER_APPROVAL_PENDING)  || ((checkUserRole(ROLE_SAFETY_OFFICER) && $row->approve_status == SAFETY_OFFICER_APPROVAL_PENDING) || (checkUserRole(ROLE_SUPERADMIN) && $row->approve_status == SAFETY_OFFICER_APPROVAL_PENDING))) {
                                $btn .= '<a href="' . admin_url('ohc/medical-requisition-slip/fdo-security-gate/approval/view/' . encryptId($row->id)) . '" class="" title="Action"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }

                            $btn .= '<a href="' . admin_url('ohc/medical-requisition-slip/fdo-security-gate/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';

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

        return view('inspection.inspection_ohc.medical_requisition_slip_security_gate.list');
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
            return view('inspection.inspection_ohc.medical_requisition_slip_security_gate.add', $data);
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
                $medicine_requisition_fdo_details = $this->medicine_requisition_fdo_details->store();

                $signature = $this->signature->signatureupload(OHC_TYPE_MEDICINE_REQUISTION_FDO);
                $medicine_requisition_fdo_checklist = $this->medicine_requisition_fdo_checklist->store($medicine_requisition_fdo_details);
                $data = [
                    'type' => OHC_TYPE_MEDICINE_REQUISTION_FDO,
                    'from_status' => OHC_CREATION,
                    'to_status' => SAFETY_OFFICER_APPROVAL_PENDING,
                    'reference_id' => $medicine_requisition_fdo_details->id,
                    'remarks' => "",
                    'approved_by' => null,
                    'created_by' => Auth::id(),

                ];
                $id = $medicine_requisition_fdo_details->id;
                $this->inspection_ohc_status_log->store($data);

                // Safety Officer

                $getsafetyofficer = getSafetyOfficer();
                $getsafetyofficers = $getsafetyofficer->pluck('id')->toArray();
                $getsafetyofficerEmail = $getsafetyofficer->pluck('email')->toArray();

                // medical officer

                $getmedicalassistant = getMedicalAssistant();
                $getmedicalassistantEmail = $getmedicalassistant->pluck('email')->toArray();
                $getmedicalassistants = $getmedicalassistant->pluck('id')->toArray();
// Select One
                $medicine_requisition_fdo_details = $this->medicine_requisition_fdo_checklist->Selectone($id);
                $medicine_requisition_fdo_checklist_details = $this->medicine_requisition_fdo_checklist->Selectone($id);

                $title = "Medical Requisition Slip- Fdo & Security Gate";
                $mailsubject = "Medical Requisition Slip- Fdo & Security Gate";
                $details = array(
                    'ohc_type' => 'Medical Requisition Slip- Fdo & Security Gate',
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'data' => $medicine_requisition_fdo_details,
                    'checklist'=>   $medicine_requisition_fdo_checklist_details
                );

                $recipients = array_merge($getsafetyofficerEmail, $getmedicalassistantEmail);
                if (!empty($recipients)) {
                    Mail::to($recipients)->queue(new MedicineRequistionFdoEmail($details));
                }

                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medical-requisition-slip/fdo-security-gate/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-requisition-slip/fdo-security-gate/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $medicine_requisition_fdo_details = $this->medicine_requisition_fdo_checklist->Selectone($id);
                $medicine_requisition_fdo_checklist_details = $this->medicine_requisition_fdo_checklist->Selectone($id);

                $data = array(
                    'medicinerequisition' => $medicine_requisition_fdo_details,
                    'medicine_requisition_fdo_checklist' => $medicine_requisition_fdo_checklist_details,
                );
            }
            return view('inspection.inspection_ohc.medical_requisition_slip_security_gate.view', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function approval(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $medicine_requisition_fdo_details = $this->medicine_requisition_fdo_checklist->Selectone($id);
                $medicine_requisition_fdo_checklist_details = $this->medicine_requisition_fdo_checklist->Selectone($id);

                $data = array(
                    'medicinerequisition' => $medicine_requisition_fdo_details,
                    'medicine_requisition_fdo_checklist' => $medicine_requisition_fdo_checklist_details,
                );
            }
            return view('inspection.inspection_ohc.medical_requisition_slip_security_gate.approval', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }
}
