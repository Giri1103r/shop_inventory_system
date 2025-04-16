<?php

namespace App\Http\Controllers\Inspection\Fire;

use Exception;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Master\Employee;
use App\Models\Inspection\Fire\FireCheckListFollowUp;
use App\Models\Inspection\Fire\FireCheckListFollowUpObservation;
use App\Models\Inspection\Master\ChecklistType;
use App\Models\Inspection\Master\ChecklistSubType;
use App\Models\Inspection\Master\ChecklistSubTypeDataName;
use App\Models\Inspection\Master\ChecklistSubTypeData;
use App\Models\Inspection\Master\ChecklistOptionType;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Master\Unit;
use App\Models\Master\Department;
use App\Models\Inspection\Fire\FireSignatureUpload;
use App\Models\Inspection\Fire\FireStatusLog;
use App\Models\Inspection\Fire\ObservationWhyWhyAnalysis;
use App\Mail\Inspection\Fire\FireInspection;
use App\Models\User;

class ChecklistObservationFollowupController extends Controller
{

    private $checklist_type;
    private $checklist_subtype;
    private $checklist_subtypedata;
    private $checklist_subtypename;
    private $followup;
    private $upload_log;
    private $checklist_option;
    private $shift;
    private $static_docno;
    private $unit;
    private $department;
    private $signature;
    private $followupObservation;
    private $statusLog;
    private $whywhyanalysis;

    public function __construct()
    {
        $this->followup = new FireCheckListFollowUp();
        $this->followupObservation = new FireCheckListFollowUpObservation();
        $this->checklist_type = new ChecklistType();
        $this->checklist_subtype = new ChecklistSubType();
        $this->checklist_option = new ChecklistOptionType();
        $this->checklist_option = new ChecklistOptionType();
        $this->checklist_subtypename = new ChecklistSubTypeDataName();
        $this->checklist_subtypedata = new ChecklistSubTypeData();
        $this->shift = new Shift();
        $this->static_docno = new InspectionStaticDocno();
        $this->unit = new Unit();
        $this->signature = new FireSignatureUpload();
        $this->department = new Department();
        $this->statusLog = new FireStatusLog();
        $this->whywhyanalysis = new ObservationWhyWhyAnalysis();
    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =    $this->followup->list();
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
                        ->addColumn('observation_status', function ($row) {
                            $text = '';
                            switch ($row->observation_status) {
                                case WAITING_FOR_EHS_OFFICER_VERIFICATION:
                                    $text = "<span class='badge bg-primary rounded' style='font-size: 1.0em;'>Waiting For EHS Officer Verification</span>";
                                    break;
                                case WAITING_FOR_CAPA_ACTION:
                                    $text = "<span class='badge bg-info rounded' style='font-size: 1.0em;'>Waiting For CAPA Action</span>";
                                    break;
                                case WAITING_FOR_CAPA_VERIFICATION:
                                    $text = "<span class='badge bg-warning rounded' style='font-size: 1.0em;'>Waiting For CAPA Verification</span>";
                                    break;
                                case WAITING_FOR_L1_VERIFICATION:
                                    $text = "<span class='badge bg-warning rounded' style='font-size: 1.0em;'>Waiting For Level-1 Manager Verification</span>";
                                    break;
                                case WAITING_FOR_L2_VERIFICATION:
                                    $text = "<span class='badge bg-warning rounded' style='font-size: 1.0em;'>Waiting For Level-2 Manager Verification</span>";
                                    break;
                                case INSPECTION_APPROVED:
                                    $text = "<span class='badge bg-success rounded' style='font-size: 1.0em;'>CLOSED</span>";
                                    break;
                                case L2_MANAGER_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>LEVEL 2 OFFICER REJECTED - WAITING FOR CAPA ACTION</span>";
                                    break;
                                case L1_MANAGER_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>LEVEL 1 OFFICER REJECTED - WAITING FOR CAPA ACTION</span>";
                                    break;
                                case EHS_OFFICER_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>EHS OFFICER REJECTED - WAITING FOR CAPA ACTION</span>";
                                    break;
                                default:
                                    $text = "<span class='badge rounded-pill text-bg-warning'>Unknown</span>";
                            }
                            return $text;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('fire/checklist-observation/view/'. encryptId($row->inspectionid) . '/' . encryptId($row->observationid)) .  '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                            // $btn .= '<a href="' . admin_url('inspection/master/checklist-sub-type/edit/' . encryptId($row->id)) . '" class="edit-icon " title="' . __('common.edit') . '"><i class="fa-solid fa-pen-to-square"></i> ';
                            // }
                            if (($row->observation_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && ((CheckUserRole(ROLE_EHS_OFFICER)) || isAdmin())) || ($row->observation_status == WAITING_FOR_CAPA_ACTION && ((CheckUserRole(ROLE_EHS_OFFICER)) || isAdmin())) || ($row->observation_status == WAITING_FOR_CAPA_VERIFICATION && ((CheckUserRole(ROLE_EHS_OFFICER)) || isAdmin())) || ($row->observation_status == WAITING_FOR_L1_VERIFICATION && ((CheckUserRole(ROLE_EHS_OFFICER)) || isAdmin()))  || ($row->observation_status == WAITING_FOR_L2_VERIFICATION && ((CheckUserRole(ROLE_EHS_OFFICER)) || isAdmin()))  || ($row->observation_status == EHS_OFFICER_REJECTED && ($row->ehs_verify_by == Auth::id() || isAdmin())) || ($row->observation_status == L1_MANAGER_REJECTED && ($row->ehs_verify_by == Auth::id() || isAdmin())) || ($row->observation_status == L2_MANAGER_REJECTED && ($row->ehs_verify_by == Auth::id() || isAdmin()))) {
                                $btn .= '<a href="' . admin_url('fire/checklist-observation/verification/' . encryptId($row->inspectionid) . '/' . encryptId($row->observationid)) . '" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }

                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'observation_status'])
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
        $checklist_types  = $this->checklist_type->select('id', 'category_name')->where('status', '1')->get();
        $data = array(
            'checklist_types' => $checklist_types,

        );
        return view('inspection.fire.observationFollowup.list', $data);
    }

    public function add(Request $request, $inspection_type, $inspection_id)

    {
        try {
            $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();

            $staticDocno  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                ['type', "ChecklistObservationFollowup"],
                ['status', '1']
            ])->first();
            $data = array(
                'staticDocno' => $staticDocno,
                'inspection_type' => $inspection_type,
                'inspection_id' => $inspection_id,
                'departmentList' => $departmentList,
                'unitList' => $unitList,
            );
            return view('inspection.fire.observationFollowup.add', $data);
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
        }
    }

    public function store(Request $request)
    {

        try {
            try {
                $inspection = $this->followup->store();
                $inspection_id = $inspection->id;
                $this->followupObservation->store($inspection_id);

                $ehsOfficer = GetEHSOfficer();
                $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
                $mailsubject = 'Observation FollowUp';
                $notificationData = array(
                    'notification_type' => FIRE_INSPECTION,
                    'module_type' => 3,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => "Fire Associate create the Observation",
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $inspection_id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('fire/checklist-observation/verification/' . encryptId($inspection_id)),
                    'assigned_user' => array_to_string($ehsOfficers),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $title = 'Fire Associate create the Observation FollowUp';
                foreach ($ehsOfficers as $user) {
                    $email_id = getUseremail($user);
                    $url = admin_url('fire/checklist-observation/verification/' . encryptId($inspection_id));
                    $details = array(
                        'fire_type' => 'Observation Followup',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'url' => $url,
                        'data' => $inspection
                    );
                    Mail::to($email_id)->queue(new FireInspection($details));
                }

                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {
                dd($ex);
                report($ex);
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('fire/checklist-observation/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('fire/checklist-observation/list'));
        }
    }
    public function view($id,$observationid)
    {
        try {
            if (Auth::check()) {
                $observation = $this->followup->selectOne(decryptId($id), decryptId($observationid));

                $data = array(
                    'observation' => $observation,
                );
            }
            return view('inspection.fire.observationFollowup.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/pre-noc/checklist/list'));
        }
    }

    public function employeename(Request $request)
    {
        $name = $request->input('search');

        $employees = Employee::where('emp_name', 'like', '%' . $name . '%')
            ->orWhere('emp_id', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->limit(10)
            ->get();


        return response()->json(
            $employees->map(function ($employee) {
                return [
                    'id' => encryptId($employee->login_id),
                    'text' => $employee->emp_name . ' - ' . $employee->emp_id,
                ];
            })
        );
    }
    public function Approvals(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $observationid = decryptId($request->observationid);

            $inspection_type = DETECTOR_INSPECTION;

            $observation = $this->followup->selectOne($id, $observationid);

            // $status_log = $this->statusLog->selectOne($id, DETECTOR_INSPECTION);


            $data = array(
                'observation' => $observation,
                // 'status_log' => $status_log,
            );
            return view('inspection.fire.observationFollowup.approve', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/checklist-observation/list'));
        }
    }

    public function EHSOfficerSubmit(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $observationid = decryptId($request->observation_id);
            $to_status = WAITING_FOR_CAPA_ACTION;
            $this->whywhyanalysis->store($id, $observationid);
            $inspection_updates = $this->followupObservation->EHSOfficerUpdate($observationid);
            $this->followupObservation->statusUpdate($observationid, $to_status);
            $inspection_details = $this->followup->selectOne($id, $observationid);
            $mailsubject = 'CAPA Assigned';
            $employees = User::where('id', $request->responsible_person_id)->get(['name', 'email', 'id']);

            foreach ($employees as $employee) {
                $email_id = $employee->email;

                if (!empty($email_id)) { // Corrected email validation
                    $details = array(
                        'fire_type' => 'Observation Followup',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $mailsubject,
                        'data' => $inspection_details
                    );
                    Mail::to($email_id)->queue(new FireInspection($details));
                }
            }

            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => $mailsubject,
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_details->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('fire/checklist-observation/verification/' . encryptId($id)),
                'assigned_user' => decryptId($request->responsible_person_id),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);
            $insert_array = [
                'type' => OBSERVATION_FOLLOWUP,
                'inspection_id' => $inspection_details->observationid,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/checklist-observation/list'));
        } catch (Exception $ex) {

            dd($ex);
            report($ex);
            Session::flash('error', 'Something Went Wrong!');
            return redirect(admin_url('fire/checklist-observation/list'));
        }
    }

    public function CAPASubmit(Request $request)
    {

        try {
            $to_status = WAITING_FOR_CAPA_VERIFICATION;
            $id = decryptId($request->id);
            $observationid = decryptId($request->observation_id);
            $inspection_updates = $this->followupObservation->capaUpdate($observationid);
            $this->followupObservation->statusUpdate($observationid, $to_status);
            $inspection_details = $this->followup->selectOne($id, $observationid);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'Observation FollowUp';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Observation",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('fire/checklist-observation/verification/' . encryptId($id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Observation FollowUp';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('fire/checklist-observation/verification/' . encryptId($id));
                $details = array(
                    'fire_type' => 'Observation Followup',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }
            $insert_array = [
                'type' => OBSERVATION_FOLLOWUP,
                'inspection_id' => $inspection_details->observationid,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/checklist-observation/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went Wrong!');
            return redirect(admin_url('fire/checklist-observation/list'));
        }
    }
    public function CAPAVerifySubmit(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $observationid = decryptId($request->observation_id);
            $remarks = $request->remarks;
            $status = $request->has('approved') ? 1 : 0;
            $inspection_details = $this->followup->selectOne($id, $observationid);
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('fire/checklist-observation/verification/' . encryptId($inspection_details->id));
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = WAITING_FOR_L1_VERIFICATION;
                $mailsubject = 'Observation FollowUp';
                $notificationData = array(
                    'notification_type' => FIRE_INSPECTION,
                    'module_type' => 3,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => $message,
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $inspection_details->id,
                        'module' => 1,
                    )),
                    'web_link' =>  $web_link,
                    'assigned_user' => array_to_string($users),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                foreach ($users as $user) {
                    $title = $message;
                    $email_id = getUseremail($user);
                    $url = $web_link;
                    $details = array(
                        'fire_type' => 'Observation FollowUp',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'url' => $url,
                        'data' => $inspection_details
                    );
                    Mail::to($email_id)->queue(new FireInspection($details));
                }
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('fire/checklist-observation/verification/' . encryptId($inspection_details->id));
                $users = $inspection_details->ehs_verify_by;
                $to_status = EHS_OFFICER_REJECTED;
                $mailsubject = 'Observation FollowUp';
                $notificationData = array(
                    'notification_type' => FIRE_INSPECTION,
                    'module_type' => 3,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => $message,
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $inspection_details->id,
                        'module' => 1,
                    )),
                    'web_link' =>  $web_link,
                    'assigned_user' => $users,
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $title = $message;
                $email_id = getUseremail($users);
                $url = $web_link;

                $details = array(
                    'fire_type' => 'Observation FollowUp',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );

                Mail::to($email_id)->queue(new FireInspection($details));
            }
            $inspection_updates = $this->followupObservation->capaVerifySubmit($observationid, $status, $remarks);
            $signature_update = $this->signature->observationFollowUp(OBSERVATION_FOLLOWUP, $observationid);
            $this->followupObservation->statusUpdate($observationid, $to_status);




            $insert_array = [
                'type' => OBSERVATION_FOLLOWUP,
                'inspection_id' => $inspection_details->observationid,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/checklist-observation/list'));
        } catch (Exception $ex) {

            dd($ex);
            report($ex);
            Session::flash('error', 'Something Went Wrong!');
            return redirect(admin_url('fire/checklist-observation/list'));
        }
    }

    public function levelOneManagerSubmit(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $observationid = decryptId($request->observation_id);
            $remarks = $request->level_one_manager;
            $status = $request->has('approved') ? 1 : 0;
            $inspection_details = $this->followup->selectOne($id, $observationid);
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('fire/checklist-observation/verification/' . encryptId($id));
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = WAITING_FOR_L2_VERIFICATION;

                $mailsubject = 'Observation FollowUp';
                $notificationData = array(
                    'notification_type' => FIRE_INSPECTION,
                    'module_type' => 3,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => $message,
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $inspection_details->id,
                        'module' => 1,
                    )),
                    'web_link' =>  $web_link,
                    'assigned_user' => array_to_string($users),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                foreach ($users as $user) {
                    $title = $message;
                    $email_id = getUseremail($user);
                    $url = $web_link;
                    $details = array(
                        'fire_type' => 'Observation FollowUp',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'url' => $url,
                        'data' => $inspection_details
                    );
                    Mail::to($email_id)->queue(new FireInspection($details));
                }
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/checklist-observation/verification/' . encryptId($id));
                $users = $inspection_details->ehs_verify_by;
                $to_status = L1_MANAGER_REJECTED;

                $mailsubject = 'Observation FollowUp';
                $notificationData = array(
                    'notification_type' => FIRE_INSPECTION,
                    'module_type' => 3,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => $message,
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $inspection_details->id,
                        'module' => 1,
                    )),
                    'web_link' =>  $web_link,
                    'assigned_user' => $users,
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $title = $message;
                $email_id = getUseremail($users);
                $url = $web_link;

                $details = array(
                    'fire_type' => 'Observation FollowUp',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );

                Mail::to($email_id)->queue(new FireInspection($details));
            }
            $inspection_updates = $this->followupObservation->levelOneManagerSubmit($observationid, $remarks);
            $signature_update = $this->signature->observationFollowUp(OBSERVATION_FOLLOWUP, $observationid);
            $this->followupObservation->statusUpdate($observationid, $to_status);




            $insert_array = [
                'type' => OBSERVATION_FOLLOWUP,
                'inspection_id' => $observationid,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/checklist-observation/list'));
        } catch (Exception $ex) {

            dd($ex);
            report($ex);
            Session::flash('error', 'Something Went Wrong!');
            return redirect(admin_url('fire/checklist-observation/list'));
        }
    }

    public function levelTwoManagerSubmit(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $observationid = decryptId($request->observation_id);
            $remarks = $request->level_two_manager;
            $status = $request->has('approved') ? 1 : 0;
            $inspection_details = $this->followup->selectOne($id, $observationid);
            if ($status == 1) {
                $message = 'Observation FollowUp Approved Successfully!';
                $web_link =   admin_url('fire/checklist-observation/verification/' . encryptId($id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = INSPECTION_APPROVED;
                $mailsubject = 'Observation FollowUp';
                $notificationData = array(
                    'notification_type' => FIRE_INSPECTION,
                    'module_type' => 3,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => $message,
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $inspection_details->id,
                        'module' => 1,
                    )),
                    'web_link' =>  $web_link,
                    'assigned_user' => array_to_string($users),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                foreach ($users as $user) {
                    $title = $message;
                    $email_id = getUseremail($user);
                    $url = $web_link;
                    $details = array(
                        'fire_type' => 'Observation FollowUp',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'url' => $url,
                        'data' => $inspection_details
                    );
                    Mail::to($email_id)->queue(new FireInspection($details));
                }
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/checklist-observation/verification/' . encryptId($id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = L2_MANAGER_REJECTED;

                $mailsubject = 'Observation FollowUp';
                $notificationData = array(
                    'notification_type' => FIRE_INSPECTION,
                    'module_type' => 3,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => $message,
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $inspection_details->id,
                        'module' => 1,
                    )),
                    'web_link' =>  $web_link,
                    'assigned_user' => $users,
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $title = $message;
                $email_id = getUseremail($users);
                $url = $web_link;

                $details = array(
                    'fire_type' => 'Observation FollowUp',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );

                Mail::to($email_id)->queue(new FireInspection($details));
            }
            $inspection_updates = $this->followupObservation->levelTwoManagerSubmit($observationid, $remarks);
            $signature_update = $this->signature->observationFollowUp(OBSERVATION_FOLLOWUP, $observationid);
            $this->followupObservation->statusUpdate($observationid, $to_status);




            $insert_array = [
                'type' => OBSERVATION_FOLLOWUP,
                'inspection_id' => $observationid,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/checklist-observation/list'));
        } catch (Exception $ex) {

            dd($ex);
            report($ex);
            Session::flash('error', 'Something Went Wrong!');
            return redirect(admin_url('fire/checklist-observation/list'));
        }
    }
    public function statusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $this->fireNoc->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Fire Pre Noc Checklist checklist status changed'], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function exportExcel()
    {
        try {

            $allData =   $this->checklist_subtype->exportdata();
            $header = [
                __("common.sno"),
                __("Checklist Sub-Type ID"),
                __("Checklist Type Name"),
                __("Checklist Sub-Type Name"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] = $data->subcategory_id;
                $export[] =  $data->category_name;
                $export[] =  $data->subcategory_name;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Checklist Sub Type Category.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function exportPDF()
    {
        try {

            ini_set("pcre.backtrack_limit", "5000000");

            $allData =   $this->checklist_subtype->exportdata();
            $header = [
                __("common.sno"),
                __("Checklist Sub-Type ID"),
                __("Checklist Type Name"),
                __("Checklist Sub-Type Name"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];
            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Checklist Sub Type Category",
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

            $view = view('inspection.fire.observationFollowup.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Checklist Sub Type Category.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }

    public function import(Request $request)
    {
        $data = array();

        return view('master.checklist_subtype.import', $data);
    }

    public function downloadSample()
    {

        $filedetails =  exportsamplefile('checklist_type');
        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        return redirect(url($filePath));
    }

    public function importSubmit(Request $request)
    {
        try {
            $file = $request->file('checklist_type_file_upload');
            $rules = [
                'checklist_type_file_upload' => 'required',
            ];
            $messages = [
                'checklist_type_file_upload.required' => 'Please upload a file',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            if ($file != null) {

                $uploadpath = 'uploads/checklist_type';

                $filenewname = time() . Str::random('16') . '.' . $file->getClientOriginalExtension();

                $fileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();

                $fileExt = $file->getClientOriginalExtension();

                uploadFile($file, $uploadpath, $filenewname);

                $path = $uploadpath . "/" . $filenewname;
                $user_id = Auth::id();

                $insert_data = array(
                    'upload_type' => checklist_type_UPLOAD,
                    'upload_status' => 0,
                    'file_name' => $filenewname,
                    'file_orgname' => $fileName,
                    'file_path' => $path,
                    'file_size' => $fileSize,
                    'file_extension' => $fileExt,
                    'created_by' => $user_id,
                );

                $insert_id =  $this->upload_log->create($insert_data)->id;

                $details = [
                    "user_id" => $user_id,
                    "log_id" => $insert_id,
                    "path" => $path,
                ];

                dispatch(new ImportChecklistCategoryJob($details));
            }
            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', 'Permit Checklist Category Upload Successfull');
            return redirect(admin_url('inspection/checklist-type/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Permit Checklist Category failed!');
            return redirect(admin_url('inspection/checklist-type/list'));
        }
    }
}
