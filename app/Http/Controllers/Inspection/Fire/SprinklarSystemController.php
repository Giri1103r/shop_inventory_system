<?php

namespace App\Http\Controllers\Inspection\Fire;

use Exception;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Master\Location;
use App\Models\Master\Department;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Inspection\Master\Shift;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\Master\Frequency;
use App\Mail\Inspection\Fire\FireInspection;
use App\Models\Inspection\Fire\FireStatusLog;
use App\Models\Inspection\Fire\FireFileUpload;
use App\Models\Inspection\Fire\FireSignatureUpload;
use App\Models\Inspection\Fire\FireCheckListFollowUp;
use App\Models\Inspection\Fire\SprinklarSystemInspection;
use App\Models\Inspection\Fire\SprinklarSystemInspectionDetails;

class SprinklarSystemController extends Controller
{
    private $sprinklar_system;
    private $sprinklar_system_details;
    private $shift;
    private $location;
    private $unit;
    private $frequency;
    private $department;
    private $files;
    private $signature;
    private $statusLog;
    private $checklist_follow;

    public function __construct()
    {
        $this->sprinklar_system = new SprinklarSystemInspection();
        $this->sprinklar_system_details = new SprinklarSystemInspectionDetails();
        $this->department = new Department();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->unit = new Unit();
        $this->frequency = new Frequency();
        $this->files = new FireFileUpload();
        $this->signature = new FireSignatureUpload();
        $this->statusLog = new FireStatusLog();
        $this->checklist_follow = new FireCheckListFollowUp();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->sprinklar_system->list();
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
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('inspection_status', function ($row) {
                            $text = '';
                            switch ($row->inspection_status) {
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
                            $btn = '<a href="' . admin_url('fire/sprinkler-inspection/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            if ($row->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/sprinkler-inspection/verification/' . encryptId($row->id)) . '/ehs" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if (($row->inspection_status == WAITING_FOR_CAPA_ACTION || $row->inspection_status == L2_MANAGER_REJECTED || $row->inspection_status == EHS_OFFICER_REJECTED || $row->inspection_status == L1_MANAGER_REJECTED) && (CheckUserRole(ROLE_FIRE_ASSOCIATES) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/sprinkler-inspection/verification/' . encryptId($row->id)) . '/capa" class="" title="' . __('inspection.capa_action') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_CAPA_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/sprinkler-inspection/verification/' . encryptId($row->id)) . '/ehsVerify" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L1_VERIFICATION && (CheckUserRole(ROLE_L1_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/sprinkler-inspection/verification/' . encryptId($row->id)) . '/level-one-manager" class="" title="' . __('inspection.l1_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L2_VERIFICATION && (CheckUserRole(ROLE_L2_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/sprinkler-inspection/verification/' . encryptId($row->id)) . '/level-two-manager" class="" title="' . __('inspection.l2_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            $btn .= '<a href="' . admin_url('fire/sprinkler-inspection/exportViewPdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                    </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'inspection_status', 'issue_date'])
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

        $data = array();
        return view('inspection.fire.sprinklar_system.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $location = $this->location->getLocationName();
            $unit = $this->unit->getUnit();
            $frequency = $this->frequency->getFrequency();
            $shifts = $this->shift->getShiftname();
            $department = $this->department->getdepartment();

            $data = array(
                'locations' => $location,
                'units' => $unit,
                'frequency' => $frequency,
                'shifts' => $shifts,
                'department' => $department,
            );

            return view('inspection.fire.sprinklar_system.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }

    public function GetDepartment(Request $request)
    {
        try {
            $department = $this->department->getdepartment();
            $departments = [];

            foreach ($department as $department) {
                $departments[] = [
                    'id' => encryptId($department->id),
                    'department_name' => $department->department_name,
                ];
            }

            return response()->json($departments);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['error' => 'Something went wrong !'], 406);
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'issue_date' => 'required',
                'rev_date' => 'required',
                'inspection_date' => 'required',
                'location_id' => 'required',
                'shift_id' => 'required',
                'next_due' => 'required',
                'unit_id' => 'required',
                'frequency_id' => 'required',
                'sr_no.*' => 'required',
                'department.*' => 'required',
                'quantity.*' => 'required',
                'resource_code.*' => 'required',
                'water_leakage.*' => 'required',
                'painting.*' => 'required',
                'qbd.*' => 'required',
                'condition_of_flow_meter.*' => 'required',
                'main_isolation.*' => 'required',
                'drain_condition.*' => 'required',
                'observation.*' => 'required',
                'remarks.*' => 'required',
            ];

            $messages = [
                'issue_date.required' => 'Issue Date is required',
                'rev_date.required' => 'Revision Data is required',
                'inspection_date.required' => 'Inspection Date is required',
                'location_id.required' => 'Location is required',
                'shift_id.required' => 'Shift is required',
                'next_due.required' => 'Next due date is required',
                'unit_id.required' => 'Unit is required',
                'department.*.required' => 'Department is required',
                'quantity.*.required' => 'Quantity is required',
                'resource_code.*.required' => 'Resource Code is required',
                'water_leakage.*.required' => 'Status of Water Leakage is required',
                'painting.*.required' => 'Status Of Painting is required',
                'qbd.*.required' => 'Quality By Design Condition is required',
                'condition_of_flow_meter.*.required' => 'Condition of flow meter Status is required',
                'main_isolation.*.required' => 'Main Isolation Valve Status is required',
                'drain_condition.*.required' => 'Drain Isolation Valve Status is required',
                'observation.required' => 'Observation is required',
                'remarks.*.required' => 'Remarks is required',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $inspection = $this->sprinklar_system->store();
            $inspection_type = SPRINKLAR_SYSTEM_INSPECTION;
            $id = $inspection->id;

            $inspection_details = $this->sprinklar_system_details->store($id);
            $inspection_file = $this->files->file_upload($inspection_type, $id);

            $checklist_store = $this->checklist_follow->store($inspection_type, $id);

            $signature_update = $this->signature->CheckedBySignature($id,$inspection_type);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'FIRE INSPECTION';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Sprinklar System Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('fire/sprinkler-inspection/view/' . encryptId($id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Sprinklar System Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('fire/sprinkler-inspection/verification/' . encryptId($id) . '/ehs');
                $details = array(
                    'fire_type' => 'Sprinklar System Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => SPRINKLAR_SYSTEM_INSPECTION,
                'inspection_id' => $id,
                'from_status' => 0,
                'to_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'created_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', 'Your data added successfully');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }

    public function View(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_type = SPRINKLAR_SYSTEM_INSPECTION;

            $inspection = $this->sprinklar_system->selectOne($id);
            $inspection_details = $this->sprinklar_system_details->GetDetails($inspection->id);
            $inspection_image = $this->files->GetFile($inspection_type, $id);
            $status_log = $this->statusLog->selectOne($id, SPRINKLAR_SYSTEM_INSPECTION);

            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'inspection_image' => $inspection_image,
                'status_log' => $status_log,
            );
            return view('inspection.fire.sprinklar_system.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }

    public function Approvals(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_type = SPRINKLAR_SYSTEM_INSPECTION;

            $inspection = $this->sprinklar_system->selectOne($id);
            $inspection_details = $this->sprinklar_system_details->GetDetails($inspection->id);
            $inspection_image = $this->files->GetFile($inspection_type, $id);
            $status_log = $this->statusLog->selectOne($id, SPRINKLAR_SYSTEM_INSPECTION);

            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'inspection_image' => $inspection_image,
                'status_log' => $status_log,
            );
            return view('inspection.fire.sprinklar_system.approve', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }

    public function EHSOfficerSubmit(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $inspection_updates = $this->sprinklar_system->EHSOfficerUpdate($id);
            $signature_update = $this->signature->signatureUpload(SPRINKLAR_SYSTEM_INSPECTION);
            $inspection_details = $this->sprinklar_system->selectOne($id);
            if ($request->is_passed == 1) {
                $message = 'Sprinklar System Inspection Approved Successfully';
                $web_link =   admin_url('fire/sprinkler-inspection/verification/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
            } else {
                $message = 'Inspection Recommended for the CAPA Action';
                $web_link =   admin_url('fire/sprinkler-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = WAITING_FOR_CAPA_ACTION;
            }
            $userIds = [
                'users' => $inspection_details->created_by,
            ];
            $mailsubject = 'FIRE INSPECTION';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 2,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => $message,
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_details->id,
                    'module' => 1,
                )),
                'web_link' =>  $web_link,
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = $message;
            $user = $inspection_details->created_by;
            $email_id = getUseremail($user);
            $url = admin_url('fire/sprinkler-inspection/verification/' . encryptId($id) . '/capa');
            $details = array(
                'fire_type' => 'Sprinklar System Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new FireInspection($details));

            $insert_array = [
                'type' => SPRINKLAR_SYSTEM_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something Went Wrong!');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }

    public function CAPASubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $sprinklar_system_inspection = $this->sprinklar_system->capaSubmit($id);
            $inspection_details = $this->sprinklar_system->selectOne($id);
            $signature_update = $this->signature->signatureUpload(SPRINKLAR_SYSTEM_INSPECTION);
            $ehsOfficers = $inspection_details->verified_by;
            $userIds = [
                'users' => $ehsOfficers,
            ];
            $mailsubject = 'Fire Inspection';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "CAPA Action Completed by the Fire Associates",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_details->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('fire/sprinkler-inspection/verification/' . encryptId($inspection_details->id)) . '/ehsVerify',
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $user = $inspection_details->verified_by;
            $email_id = getUseremail($user);
            $url = admin_url('fire/sprinkler-inspection/verification/' . encryptId($id) . '/ehs');
            $details = array(
                'fire_type' => 'Sprinklar System Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => 'CAPA Action Completed by the Fire Associates',
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new FireInspection($details));

            $insert_array = [
                'type' => SPRINKLAR_SYSTEM_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_ACTION,
                'to_status' => WAITING_FOR_CAPA_VERIFICATION,
                'created_by' => Auth::id(),
                'remarks' => $request->capa_remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }

    public function CAPAVerifySubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->remarks;
            $sprinklar_system_inspection = $this->sprinklar_system->capaVerifySubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(SPRINKLAR_SYSTEM_INSPECTION);
            $inspection_details = $this->sprinklar_system->selectOne($id);
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('fire/sprinkler-inspection/verification/' . encryptId($inspection_details->id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = WAITING_FOR_L1_VERIFICATION;
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('fire/sprinkler-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = EHS_OFFICER_REJECTED;
            }

            $mailsubject = 'FIRE INSPECTION';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 1,
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
                    'fire_type' => 'Sprinklar System Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => SPRINKLAR_SYSTEM_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }

    public function levelOneManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_one_manager;
            $sprinklar_system_inspection = $this->sprinklar_system->levelOneManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(SPRINKLAR_SYSTEM_INSPECTION);
            $inspection_details = $this->sprinklar_system->selectOne($id);
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('fire/sprinkler-inspection/verification/' . encryptId($inspection_details->id) . '/level-two-manager');
                $user = GetLevelTwoManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by], [$inspection_details->verified_by]);
                $to_status = WAITING_FOR_L2_VERIFICATION;
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/sprinkler-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = L1_MANAGER_REJECTED;
            }

            $mailsubject = 'FIRE INSPECTION';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 1,
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
                    'fire_type' => 'Sprinklar System Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => SPRINKLAR_SYSTEM_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L1_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_one_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }

    public function levelTwoManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_two_manager;
            $sprinklar_system_inspection = $this->sprinklar_system->levelTwoManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(SPRINKLAR_SYSTEM_INSPECTION);
            $inspection_details = $this->sprinklar_system->selectOne($id);
            if ($status == 1) {
                $message = 'Sprinklar System Inspection Approved Successfully!';
                $web_link =   admin_url('fire/sprinkler-inspection/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by], [$inspection_details->l2_manager_verified_by]);
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/sprinkler-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = L2_MANAGER_REJECTED;
            }

            $mailsubject = 'FIRE INSPECTION';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 1,
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
                    'fire_type' => 'Sprinklar System Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => SPRINKLAR_SYSTEM_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L2_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_two_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }

    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->sprinklar_system->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Issue Date',
                'Revision Date',
                __("inspection.inspection_status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->doc_no;
                $export[] =  $data->issue_date;
                $export[] = $data->revision_data;
                $export[] =  getInspectionStatus($data->inspection_status);;
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);
                $exportData[] = $export;
                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Sprinklar System Inspection.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }

    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->sprinklar_system->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }
            $header = [
                __("common.sno"),
                'Document Number',
                'Issue Date',
                'Revision Date',
                __("inspection.inspection_status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Sprinklar System Inspection",
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

            $view = view('inspection.fire.pdf.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Sprinklar System Inspection Inspection.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }

    public function ExportViewPDF(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $status_log = $this->statusLog->selectOne($id,SPRINKLAR_SYSTEM_INSPECTION);
                $forklift_details = $this->sprinklar_system->selectOne($id);
                $inspection = $this->sprinklar_system_details->GetDetails($forklift_details->id);

                $data = [
                    'status_log' => $status_log,
                    'forklift_details' => $forklift_details,
                    'pagetitle' => "Sprinklar System Inspection",
                    'inspection' => $inspection,
                ];
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

            $html = view('inspection.fire.sprinklar_system.viewPdf',$data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Sprinklar System Inspection.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }
}
