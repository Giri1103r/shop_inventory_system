<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use App\Models\Inspection\Ohc\FirstAidRecordChecklist;
use App\Models\Inspection\Ohc\FirstAidRecordDetails;
use App\Models\Inspection\Ohc\FirstAidRecordSignatureUpload;
use App\Models\Inspection\Ohc\FirstAidRecordStatusLog;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use Exception;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class FirstAidRecordController extends Controller
{
    private $first_aid_details;
    private $first_aid_checklist;
    private $statusLog;
    private $signature;
    private $unit;

    public function __construct()
    {
        $this->first_aid_details = new FirstAidRecordDetails();
        $this->first_aid_checklist = new FirstAidRecordChecklist();
        $this->statusLog = new FirstAidRecordStatusLog();
        $this->signature = new FirstAidRecordSignatureUpload();
        $this->unit = new Unit();

    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->first_aid_details->list();
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
                            $btn = '<a href="' . admin_url('ohc/first-aid-record/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            if ($row->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('ohc/first-aid-record/verification/' . encryptId($row->id)) . '/ehs" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if (($row->inspection_status == WAITING_FOR_CAPA_ACTION || $row->inspection_status == L2_MANAGER_REJECTED || $row->inspection_status == EHS_OFFICER_REJECTED || $row->inspection_status == L1_MANAGER_REJECTED) && (CheckUserRole(ROLE_FIRE_ASSOCIATES) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('ohc/first-aid-record/verification/' . encryptId($row->id)) . '/capa" class="" title="' . __('inspection.capa_action') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_CAPA_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('ohc/first-aid-record/verification/' . encryptId($row->id)) . '/ehsVerify" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L1_VERIFICATION && (CheckUserRole(ROLE_L1_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('ohc/first-aid-record/verification/' . encryptId($row->id)) . '/level-one-manager" class="" title="' . __('inspection.l1_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L2_VERIFICATION && (CheckUserRole(ROLE_L2_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('ohc/first-aid-record/verification/' . encryptId($row->id)) . '/level-two-manager" class="" title="' . __('inspection.l2_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            $btn .= '<a href="' . admin_url('ohc/first-aid-record/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                                <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                            </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'inspection_status', 'status'])
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

        return view('inspection.inspection_ohc.first_aid_record.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();

            $data = [
                'unit' => $unit,
            ];
            return view('inspection.inspection_ohc.first_aid_record.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {

            $first_aid_details = $this->first_aid_details->store();
            $first_aid_detail_id = $first_aid_details->id; 
            $this->first_aid_checklist->store($first_aid_detail_id);

            $mailsubject = 'First Aid Record Inspection completed by fire associate';
            $ehsOfficer = GetEHSOfficer();
            $message = 'First Aid Record Inspection completed by fire associate';
               
            if (count($ehsOfficer) > 0) {
                foreach ($ehsOfficer as $user) {
                    $email_id = $user->email;
                    if ($email_id != '' || $email_id != null) {
                        $data = $this->first_aid_details->selectOne($first_aid_detail_id);
                        $data = array(
                            'data' => $data,
                            'mail_subject' => 'First Aid Record Inspection',
                            'message' => $message,
                        );
                        Mail::to($email_id)->queue(new RRAAEmail($data));
                    }
                }
            }

            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $notificationData = array(
                'notification_type' => RRAA_INSPECTION,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => $message,
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $first_aid_details->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('ohc/first-aid-record/view/' . encryptId($first_aid_details->id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            Session::flash('success', __('Your data has been created successfully'));
            
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/first-aid-record/list'));
        }
    }

    public function StatusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->first_aid_details->statuschange($id);
           
            $this->first_aid_checklist->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Your status  has changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $first_aid_details = $this->first_aid_details->find($id);
                $first_aid_checklist = $this->first_aid_checklist->selectOne($id);
                $status_log = $this->statusLog->selectOne($id);
                $inspection_details = $this->first_aid_details->selectOne($id);

                $data = array(
                    'first_aid_details' => $first_aid_details,
                    'first_aid_checklist' => $first_aid_checklist  ?? [],
                    'status_log' => $status_log,
                    'inspection_details' => $inspection_details,
                );
            }
            return view('inspection.inspection_ohc.first_aid_record.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function employeeid(Request $request)
    {
        $name = $request->input('search');

        $employee_code = $this->employee->where('emp_id', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->limit(10)
            ->get();

        return response()->json(
            $employee_code->map(function ($employee) {
                return [
                    'id' => $employee->login_id,
                    'text' => $employee->emp_id . ' - ' . $employee->emp_name,
                ];
            })
        );
    }

    public function approvals(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->first_aid_details->selectOne($id);
            $first_aid_details = $this->first_aid_details->find($id);
            $first_aid_checklist = $this->first_aid_checklist->selectOne($id);
            $data = [
                'inspection_details' => $inspection_details,
                'first_aid_details' => $first_aid_details,
                'first_aid_checklist' => $first_aid_checklist,
            ];
            return view('inspection.inspection_ohc.first_aid_record.approval', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/first-aid-record/list'));
        }
    }

    public function EHSOfficerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_updates = $this->first_aid_details->EHSOfficerUpdate($id);
            $inspection_details = $this->first_aid_details->selectOne($id);
            $signature_update = $this->signature->signatureUpload();
            if ($request->is_passed == 1) {
                $message = 'First Aid Record Inspeciton Approved Successfully';
                $web_link =   admin_url('ohc/first-aid-record/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
            } else {
                $message = 'Inspection Recommended for the CAPA Action';
                $web_link =   admin_url('ohc/first-aid-record/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = WAITING_FOR_CAPA_ACTION;
            }
            $userIds = [
                'users' => $inspection_details->created_by,
            ];
            $mailsubject = 'SAFETY INSPECTION';
            $notificationData = array(
                'notification_type' => RRAA_INSPECTION,
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
            $insert_array = [
                'first_aid_record_details_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ]; 
            $this->statusLog->create($insert_array);

            $ehsOfficer = GetEHSOfficer();
        
            if (count($ehsOfficer) > 0) {
                 foreach ($ehsOfficer as $user) {

                     $email_id = $user->email;

                     if ($email_id != '' || $email_id != null) {
                         $data = $this->first_aid_details->selectOne($id);

                        $data = array(
                            'data' => $data,
                            'mail_subject' => 'First Aid Record Inspection',
                            'message' => $message,
                        );
                        Mail::to($email_id)->queue(new RRAAEmail($data));
                         
                     }
                 }
             }

            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/first-aid-record/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('Something Went Wrong!'));
            return redirect(admin_url('ohc/first-aid-record/list'));
        }
    }

    public function CAPASubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $forklift_inspection = $this->first_aid_details->capaSubmit($id);
            $inspection_details = $this->first_aid_details->selectOne($id);
            $signature_update = $this->signature->signatureUpload();
            $ehsOfficers = $inspection_details->verified_by;
            $userIds = [
                'users' => $ehsOfficers,
            ];
            $mailsubject = 'Safety Inspection';
            $notificationData = array(
                'notification_type' => RRAA_INSPECTION,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "CAPA Action completed by Fire Associates",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_details->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('ohc/first-aid-record/verification/' . encryptId($inspection_details->id)) . '/ehsVerify',
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);
            $insert_array = [
                'first_aid_record_details_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_ACTION,
                'to_status' => WAITING_FOR_CAPA_VERIFICATION,
                'created_by' => Auth::id(),
                'remarks' => $request->capa_remarks,
            ];
            $this->statusLog->create($insert_array);

            $message = 'CAPA Action completed by Fire Associates';
            $ehsOfficer = GetEHSOfficer();
        
            if (count($ehsOfficer) > 0) {
                 foreach ($ehsOfficer as $user) {

                     $email_id = $user->email;

                     if ($email_id != '' || $email_id != null) {
                         $data = $this->first_aid_details->selectOne($id);

                        $data = array(
                            'data' => $data,
                            'mail_subject' => 'First Aid Record Inspection',
                            'message' => $message,
                        );
                        Mail::to($email_id)->queue(new RRAAEmail($data));
                         
                     }
                 }
             }

            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/first-aid-record/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('ohc/first-aid-record/list'));
        }
    }

    public function CAPAVerifySubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->remarks;
            $forklift_inspection = $this->first_aid_details->capaVerifySubmit($id, $status, $remarks);
            $inspection_details = $this->first_aid_details->selectOne($id);
            $signature_update = $this->signature->signatureUpload();
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('ohc/first-aid-record/verification/' . encryptId($inspection_details->id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : 1;
                $to_status = WAITING_FOR_L1_VERIFICATION;
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('ohc/first-aid-record/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = EHS_OFFICER_REJECTED;
            }
            $userIds = [
                'users' => $users,
            ];
            $mailsubject = 'SAFETY INSPECTION';
            $notificationData = array(
                'notification_type' => RRAA_INSPECTION,
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
            $insert_array = [
                'first_aid_record_details_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);

            $ehsOfficer = GetEHSOfficer();
        
            if (count($ehsOfficer) > 0) {
                 foreach ($ehsOfficer as $user) {

                     $email_id = $user->email;

                     if ($email_id != '' || $email_id != null) {
                         $data = $this->first_aid_details->selectOne($id);

                        $data = array(
                            'data' => $data,
                            'mail_subject' => 'First Aid Record Inspection',
                            'message' => $message,
                        );
                        Mail::to($email_id)->queue(new RRAAEmail($data));
                         
                     }
                 }
             }

            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/first-aid-record/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('ohc/first-aid-record/list'));
        }
    }

    public function levelOneManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_one_manager;
            $forklift_inspection = $this->first_aid_details->levelOneManagerSubmit($id, $status, $remarks);
            $inspection_details = $this->first_aid_details->selectOne($id);
            $signature_update = $this->signature->signatureUpload();
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('ohc/first-aid-record/verification/' . encryptId($inspection_details->id) . '/level-two-manager');
                $user = GetLevelTwoManager();
                $users = $user ? $user->pluck('id')->toArray() : 1;
                $to_status = WAITING_FOR_L2_VERIFICATION;
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('ohc/first-aid-record/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = L1_MANAGER_REJECTED;
            }
            $userIds = [
                'users' => $users,
            ];
            $mailsubject = 'SAFETY INSPECTION';
            $notificationData = array(
                'notification_type' => RRAA_INSPECTION,
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
            $insert_array = [
                'ohc_first_aid_record_details_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L1_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_one_manager,
            ];
            $this->statusLog->create($insert_array);

            $ehsOfficer = GetEHSOfficer();
        
            if (count($ehsOfficer) > 0) {
                 foreach ($ehsOfficer as $user) {

                     $email_id = $user->email;

                     if ($email_id != '' || $email_id != null) {
                         $data = $this->first_aid_details->selectOne($id);

                        $data = array(
                            'data' => $data,
                            'mail_subject' => 'First Aid Record Inspection',
                            'message' => $message,
                        );
                        Mail::to($email_id)->queue(new RRAAEmail($data));
                         
                     }
                 }
             }

            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/first-aid-record/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('ohc/first-aid-record/list'));
        }
    }

    public function levelTwoManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_two_manager;
            $forklift_inspection = $this->first_aid_details->levelTwoManagerSubmit($id, $status, $remarks);
            $inspection_details = $this->first_aid_details->selectOne($id);
            $signature_update = $this->signature->signatureUpload();
            if ($status == 1) {
                $message = 'First Aid Record Inspeciton Approved Successfully!';
                $web_link =   admin_url('ohc/first-aid-record/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('ohc/first-aid-record/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = L2_MANAGER_REJECTED;
            }
            $users = $inspection_details->created_by;
            $userIds = [
                'users' => $users,
            ];
            $mailsubject = 'SAFETY INSPECTION';
            $notificationData = array(
                'notification_type' => RRAA_INSPECTION,
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
            $insert_array = [
                'first_aid_record_details_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L2_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_two_manager,
            ];
            $this->statusLog->create($insert_array);

            $ehsOfficer = GetEHSOfficer();
        
            if (count($ehsOfficer) > 0) {
                 foreach ($ehsOfficer as $user) {

                     $email_id = $user->email;

                     if ($email_id != '' || $email_id != null) {
                         $data = $this->first_aid_details->selectOne($id);

                        $data = array(
                            'data' => $data,
                            'mail_subject' => 'First Aid Record Inspection',
                            'message' => $message,
                        );
                        Mail::to($email_id)->queue(new RRAAEmail($data));
                         
                     }
                 }
             }

            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/first-aid-record/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('ohc/first-aid-record/list'));
        }
    }


    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->first_aid_details->exportdata();
            
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Issue Date',
                'Revision Date',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->document_number;
                $export[] =  $data->issue_date;
                $export[] = $data->revision_date;
                $export[] =  getInspectionStatus($data->inspection_status);;
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('First Aid Record.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aid-record/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->first_aid_details->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Issue Date',
                'Revision Date',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "First Aid Record Details",
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

            $view = view('inspection.inspection_ohc.first_aid_record.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "First Aid Record.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aid-record/list'));
        }
    }

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $first_aid_details = $this->first_aid_details->find($id);
                $first_aid_checklist = $this->first_aid_checklist->selectOne($id);
                $status_log = $this->statusLog->selectOne($id);
                $inspection_details = $this->first_aid_details->selectOne($id);

                $data = array(
                    'first_aid_details' => $first_aid_details,
                    'first_aid_checklist' => $first_aid_checklist  ?? [],
                    'status_log' => $status_log,
                    'inspection_details' => $inspection_details,
                    'pagetitle' => "First Aid Record Details",
                );
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

            $html = view('inspection.inspection_ohc.first_aid_record.generalpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "First Aid Record Details.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }

}
