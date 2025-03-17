<?php

namespace App\Http\Controllers\Inspection\Safety;

use Exception;
use App\Models\UploadLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inspection\Master\Frequency;
use Illuminate\Support\Facades\Auth;
use App\Models\Inspection\Master\Shift;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inspection\Safety\MonthlyForkLiftInspection;
use App\Models\Master\ForkLiftType;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use Spatie\IcalendarGenerator\ValueObjects\RRule;

class MonthlyForkLiftInspectionController extends Controller
{
    private $forklift;
    private $forklift_type;
    private $upload_log;
    private $shift;
    private $location;
    private $unit;
    private $frequency;

    public function __construct()
    {
        $this->forklift = new MonthlyForkLiftInspection();
        $this->upload_log = new UploadLog();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->unit = new Unit();
        $this->frequency = new Frequency();
        $this->forklift_type = new ForkLiftType();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->forklift->list();
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
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>Waiting For CAPA Action</span>";
                                    break;
                                case WAITING_FOR_CAPA_VERIFICATION:
                                    $text = "<span class='badge bg-warning rounded' style='font-size: 1.0em;'>Waiting For CAPA Verification</span>";
                                    break;
                                case WAITING_FOR_L1_VERIFICATION:
                                    $text = "<span class='badge bg-info rounded' style='font-size: 1.0em;'>Waiting For Level-1 Manager Verification</span>";
                                    break;
                                case WAITING_FOR_L2_VERIFICATION:
                                    $text = "<span class='badge bg-info rounded' style='font-size: 1.0em;'>Waiting For Level-2 Manager Verification</span>";
                                    break;
                                case INSPECTION_APPROVED:
                                    $text = "<span class='badge bg-success rounded' style='font-size: 1.0em;'>INSPECTION APPROVED</span>";
                                    break;
                                default:
                                    $text = "<span class='badge rounded-pill text-bg-warning'>Unknown</span>";
                            }
                            return $text;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('safety/forklift-inspection/monthly/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            if (CheckUserRole(ROLE_SUPERADMIN)) {
                                $btn .= '<a href="' . admin_url('safety/forklift-inspection/monthly/edit/' . encryptId($row->id)) . '" class="edit-icon " title="' . __('common.edit') . '"><i class="fa-solid fa-pen-to-square"></i> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($row->id)) . '/ehs" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-info"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_CAPA_ACTION && (CheckUserRole(ROLE_FIRE_ASSOCIATES) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($row->id)) . '/capa" class="" title="' . __('inspection.capa_action') . '"><i class="fa-solid fa-check-to-slot text-danger"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_CAPA_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($row->id)) . '/ehsVerify" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-warning"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L1_VERIFICATION && (CheckUserRole(ROLE_L1_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($row->id)) . '/level-one-manager" class="" title="' . __('inspection.l1_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-info"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L2_VERIFICATION && (CheckUserRole(ROLE_L2_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($row->id)) . '/level-two-manager" class="" title="' . __('inspection.l2_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-info"></i></a> ';
                            }
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'inspection_status', 'issue_date'])
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
        return view('inspection.Safety.forklift_inspection_monthly.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $checklistQuestions = getCheckListQuestion(FORKLIFT_INSPECTION_MONTHLY_CHECKLIST);
            $options =  getoption(FORKLIFT_INSPECTION_MONTHLY_CHECKLIST);
            $shift  = $this->shift->select('id', 'shift')->where('status', '1')->get();
            $getoption = string_to_array($options->type);
            $location = $this->location->getLocation();
            $unit = $this->unit->getUnit();
            $frequency = $this->frequency->getFrequency();
            $forklifts = $this->forklift_type->getForkLift();
            $data = array(
                'checklist_details' => $checklistQuestions,
                'shift' => $shift,
                'getoption' => $getoption,
                'locations' => $location,
                'units' => $unit,
                'frequency' => $frequency,
                'forklifts' => $forklifts,
            );
            return view('inspection.Safety.forklift_inspection_monthly.add', $data);
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        }
    }

    public function store(Request $request)
    {
        try {
            $forklift_inspection = $this->forklift->store();
            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $admins = array_to_string(GetSuperAdmin());
            foreach ($ehsOfficers as $ehsOfficer) {
                $userIds = [
                    'users' => $ehsOfficer,
                    'admin' => $admins,
                ];
                $mailsubject = 'Safety Inspection';
                $notificationData = array(
                    'notification_type' => SAFETY_INSPECTION,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => "Safety Inspection done by Fire Associates",
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $forklift_inspection->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('safety/forklift-inspection/monthly/view/' . encryptId($forklift_inspection->id)),
                    'assigned_user' => array_to_string($userIds),
                    'created_by' => Auth::id(),
                );
            }
            notificationSave($notificationData);
            Session::flash('success', 'Inspection Completed Successfully!');
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->forklift->selectOne($id);
            $data = [
                'inspection_details' => $inspection_details,
            ];
            return view('inspection.Safety.forklift_inspection_monthly.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        }
    }

    public function approvals(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->forklift->selectOne($id);
            $data = [
                'inspection_details' => $inspection_details,
            ];
            return view('inspection.Safety.forklift_inspection_monthly.approval', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        }
    }

    public function EHSOfficerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_updates = $this->forklift->EHSOfficerUpdate($id);
            $inspection_details = $this->forklift->selectOne($id);
            if ($request->is_passed == 1) {
                $message = 'ForkLift Inspeciton Approved Successfully';
                $web_link =   admin_url('safety/forklift-inspection/monthly/view/' . encryptId($inspection_details->id));
            } else {
                $message = 'Inspection Recommended for the CAPA Action';
                $web_link =   admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/capa');
            }
            $admins = array_to_string(GetSuperAdmin());
            $userIds = [
                'users' => $inspection_details->created_by,
                'admin' => $admins,
            ];
            $mailsubject = 'SAFETY INSPECTION';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
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
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);
            Session::flash('success', __('inspection.verification_success_msg'));
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('Something Went Wrong!'));
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        }
    }

    public function CAPASubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $forklift_inspection = $this->forklift->capaSubmit($id);
            $inspection_details = $this->forklift->selectOne($id);
            $ehsOfficers = $inspection_details->verified_by;
            $admins = array_to_string(GetSuperAdmin());
            $userIds = [
                'users' => $ehsOfficers,
                'admin' => $admins,
            ];
            $mailsubject = 'Safety Inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "CAPA Action done by Fire Associates",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_details->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($inspection_details->id)) . '/ehsVerify',
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);
            Session::flash('success', __('inspection.capa_action_success_msg'));
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('safety/forklift-inspecttion/monthly/list'));
        }
    }

    public function CAPAVerifySubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->is_passed;
            $remarks = $request->capa_recomendation;
            $forklift_inspection = $this->forklift->capaVerifySubmit($id, $status, $remarks);
            $inspection_details = $this->forklift->selectOne($id);
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : 1;
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
            }
            $admins = array_to_string(GetSuperAdmin());
            $userIds = [
                'users' => $users,
                'admin' => $admins,
            ];
            $mailsubject = 'SAFETY INSPECTION';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
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
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);
            Session::flash('success', __('inspection.verification_success_msg'));
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        }
    }

    public function levelOneManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->is_passed;
            $remarks = $request->level_one_manager;
            $forklift_inspection = $this->forklift->levelOneManagerSubmit($id, $status, $remarks);
            $inspection_details = $this->forklift->selectOne($id);
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/level-two-manager');
                $user = GetLevelTwoManager();
                $users = $user ? $user->pluck('id')->toArray() : 1;
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
            }
            $admins = array_to_string(GetSuperAdmin());
            $userIds = [
                'users' => $users,
                'admin' => $admins,
            ];
            $mailsubject = 'SAFETY INSPECTION';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
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
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);
            Session::flash('success', __('inspection.l1_manager_verification_success_msg'));
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        }
    }

    public function levelTwoManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->is_passed;
            $remarks = $request->level_two_manager;
            $forklift_inspection = $this->forklift->levelTwoManagerSubmit($id, $status, $remarks);
            $inspection_details = $this->forklift->selectOne($id);
            if ($status == 1) {
                $message = 'ForkLift Inspeciton Approved Successfully!';
                $web_link =   admin_url('safety/forklift-inspection/monthly/view/' . encryptId($inspection_details->id));
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/capa');
            }
            $users = $inspection_details->created_by;
            $admins = array_to_string(GetSuperAdmin());
            $userIds = [
                'users' => $users,
                'admin' => $admins,
            ];
            $mailsubject = 'SAFETY INSPECTION';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
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
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);
            Session::flash('success', __('inspection.l2_manager_verification_success_msg'));
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        }
    }
}
