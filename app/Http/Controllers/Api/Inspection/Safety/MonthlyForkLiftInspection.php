<?php

namespace App\Http\Controllers\Api\Inspection\Safety;

use Exception;
use App\Models\UploadLog;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use App\Mail\Inspection\Safety\SafetyInspection;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Master\Frequency;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\Safety\SafetyStatusLog;
use App\Models\Inspection\Safety\MonthlyForkLiftInspection as SafetyMonthlyForkLiftInspection;
use App\Models\Inspection\Safety\SignatureUpload;
use App\Models\Master\ForkLiftType;

class MonthlyForkLiftInspection extends BaseController
{
    private $unit;
    private $uploadlog;
    private $forklift_inspection;
    private $document_reference;
    private $statusLog;
    private $signature;
    private $forklift_type;
    private $frequency;
    private $shift;

    public function __construct()
    {
        $this->unit = new Unit();
        $this->uploadlog = new UploadLog();
        $this->forklift_inspection = new SafetyMonthlyForkLiftInspection();
        $this->document_reference = new InspectionStaticDocno();
        $this->statusLog = new SafetyStatusLog();
        $this->signature = new SignatureUpload();
        $this->forklift_type = new ForkLiftType();
        $this->frequency = new Frequency();
        $this->shift = new Shift();
    }

    public function list()
    {
        if (Auth::user()) {
            $request = request();
            if ($request->has('search')) {
                if ($request->search != '' && $request->search != null) {
                    $search = $request->search;
                }
            }
            $query = SafetyMonthlyForkLiftInspection::select('inspection_forklift_inpsection_monthly.*', 'inspection_shift_option.*', 'masters_unit.*', 'masters_location.*', 'inspection_frequency_option.*', 'inspection_forklift_inpsection_monthly.id as inspection_id', 'inspection_forklift_inpsection_monthly.created_at as inspection_created_at')
                ->leftJoin('masters_location', 'inspection_forklift_inpsection_monthly.location', '=', 'masters_location.id')
                ->leftJoin('inspection_shift_option', 'inspection_forklift_inpsection_monthly.shift', '=', 'inspection_shift_option.id')
                ->leftJoin('masters_unit', 'inspection_forklift_inpsection_monthly.unit', '=', 'masters_unit.id')
                ->leftJoin('inspection_frequency_option', 'inspection_forklift_inpsection_monthly.frequency', '=', 'inspection_frequency_option.id')
                ->leftJoin('inspection_static_docno', 'inspection_forklift_inpsection_monthly.document_reference_id', '=', 'inspection_static_docno.id');


            $org_total_counts = $query->count();

            if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_OFFICER) || CheckUserRole(ROLE_L1_MANAGER) || CheckUserRole(ROLE_L2_MANAGER)) {
            } else if (CheckUserRole(ROLE_FIRE_ASSOCIATES)) {
                $query->where('inspection_forklift_inpsection_monthly.created_by', Auth::id());
            }

            if (!empty($search)) {
                $search = ($search);
                $query->where(function ($query) use ($search) {
                    $query->orWhere('masters_unit.unit_name', $search)
                        ->orWhere('masters_location.location_name', $search)
                        ->orWhere('inspection_shift_option.shift', $search)
                        ->orWhere('inspection_frequency_option.frequency_name', $search);
                });
            }

            $query_array = $query->orderBy('inspection_forklift_inpsection_monthly.id', 'DESC')->paginate($request->input('per_page', 10));

            $inspection_list = $query_array->toArray();

            if (empty($inspection_list['data'])) {
                return $this->sendError('No records found.', [], 404);
            }

            $data_array = [];
            foreach ($inspection_list['data'] as $datas) {
                $data = [];
                $data['id'] = $datas['inspection_id'] ?? '';
                $data['date_of_inspection'] = Displaydateformat($datas['date_of_inspection']);
                $data['next_due'] = Displaydateformat($datas['next_due']);
                $data['location_name'] = ($datas['location_name'] ?? '');
                $data['shift_name'] = ($datas['shift'] ?? '');
                $data['unit_name'] = ($datas['unit_name'] ?? '');
                $data['frequency_name'] = ($datas['frequency_name'] ?? '');
                $data['inspection_status'] = getInspectionStatus($datas['inspection_status'] ?? '');
                $data['created_by'] = getUsername($datas['created_by'] ?? '');
                $data['created_at'] = Displaydateformat($datas['created_at'] ?? '');
                $data_array[] = $data;
            }

            $inspection_details = [
                'per_page' => $inspection_list['per_page'] ?? 0,
                'current_page' => $inspection_list['current_page'] ?? 0,
                'from' => $inspection_list['from'] ?? 0,
                'to' => $inspection_list['to'] ?? 0,
                'total' => $inspection_list['total'] ?? 0,
                'total_page' => $inspection_list['last_page'] ?? 0,
                'list' => $data_array,
            ];

            $success = [
                'inspection_details' => $inspection_details
            ];

            return $this->sendResponse($success, 'Inspection Details');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }



    public function view(Request $request)
    {
        try {

            $id = $request->id;

            $monthlyForklift = $this->forklift_inspection->find($id);
            $document_no =  $this->document_reference->find($monthlyForklift->document_reference_id);

            // general details
            $forklift_inspection = [
                'id' => $id,
                'document_number' => $document_no->doc_no,
                'issue_date' => Displaydateformat($document_no->issue_date),
                'rev_dt' => $document_no->rev_dt,
                'date_of_inspection' => Displaydateformat($monthlyForklift->date_of_inspection),
                'location' => getLocationname($monthlyForklift->location),
                'shift' => getShift($monthlyForklift->shift),
                'next_due_on' => Displaydateformat($monthlyForklift->next_due),
                'unit' => getUnitname($monthlyForklift->unit),
                'frequency' => getFrequencyname($monthlyForklift->frequency),
                'identification_serial_no' => $monthlyForklift->identification_no,
                'forklift_type' => GetForkLiftType($monthlyForklift->forklift_type),
                'capacity' => $monthlyForklift->capacity
            ];

            // checklist
            $checkList = json_decode($monthlyForklift->responses, true);
            $forklift_checklist = [];

            foreach ($checkList as $data) {
                $forklift_checklist[] = [
                    'question_name' => GetChecklistTypeDate($data['question_id']),
                    'yes_or_no' => $data['answer'],
                    'remarks' => $data['remarks'],
                ];
            }

            // start building response
            $success = [
                'forklift_inspection' => $forklift_inspection,
                'forklift_checklist' => $forklift_checklist,
            ];

            // ehs verification
            if ($monthlyForklift->verified_by) {
                $updated_time = GetSafetyUpdatedTime(
                    $monthlyForklift->verified_by,
                    $id,
                    MONTHLY_FORKLIFT_INSPECTION,
                    WAITING_FOR_EHS_OFFICER_VERIFICATION,
                );

                $success['ehs_verification'] = [
                    'verified_by' => getUsername($monthlyForklift->verified_by),
                    'Date' => Displaydateformat($updated_time->created_at),
                    'capa' => getYesNoStatus($monthlyForklift->is_passed),
                    'remarks' => $monthlyForklift->capa_recomendation ?? $monthlyForklift->remarks,
                ];
            }

            // capa action
            if ($monthlyForklift->capa_remarks) {
                $updated_time = GetSafetyUpdatedTime(
                    $monthlyForklift->created_by,
                    $id,
                    MONTHLY_FORKLIFT_INSPECTION,
                    WAITING_FOR_CAPA_ACTION,
                );

                $success['fire_associate_action'] = [
                    'name' => getUsername($monthlyForklift->created_by),
                    'date' => Displaydateformat($updated_time->created_at) ?? "",
                    'capa_action_remarks' => $monthlyForklift->capa_remarks,
                ];
            }

            // capa verification
            if ($monthlyForklift->capa_ehs_remarks) {
                $updated_time = GetSafetyUpdatedTime(
                    $monthlyForklift->verified_by,
                    $id,
                    MONTHLY_FORKLIFT_INSPECTION,
                    WAITING_FOR_CAPA_VERIFICATION,
                );
                $success['ehs_officer_reverification'] = [
                    'verified_by' => getUsername($monthlyForklift->verified_by),
                    'date' => Displaydateformat($updated_time->created_at) ?? "",
                    'capa_reverification_remarks' => $monthlyForklift->capa_ehs_remarks
                ];
            }

            // level one manager

            if ($monthlyForklift->level_one_manager_remarks) {
                $updated_time = GetSafetyUpdatedTime(
                    $monthlyForklift->l1_manager_verified_by,
                    $id,
                    MONTHLY_FORKLIFT_INSPECTION,
                    WAITING_FOR_L1_VERIFICATION,
                );
                $success['level_one_manager'] = [
                    'verified_by' => getUsername($monthlyForklift->l1_manager_verified_by),
                    'date' => Displaydateformat($updated_time->created_at) ?? "",
                    'capa_reverification_remarks' => $monthlyForklift->level_one_manager_remarks
                ];
            }

            // level two manager
            if ($monthlyForklift->level_two_manager_remarks) {
                $updated_time = GetSafetyUpdatedTime(
                    $monthlyForklift->l2_manager_verified_by,
                    $id,
                    MONTHLY_FORKLIFT_INSPECTION,
                    WAITING_FOR_L2_VERIFICATION,
                );
                $success['level_two_manager'] = [
                    'verified_by' => getUsername($monthlyForklift->l2_manager_verified_by),
                    'date' => Displaydateformat($updated_time->created_at) ?? "",
                    'capa_reverification_remarks' => $monthlyForklift->level_two_manager_remarks
                ];
            }

            // approval logs
            $statuslog = $this->statusLog->selectOne($id, MONTHLY_FORKLIFT_INSPECTION);
            if (!empty($statuslog)) {
                $approvalLogs = [];
                foreach ($statuslog as $logs) {
                    $approvalLogs[] = [
                        'from_status'   => getInspectionStatus($logs->from_status),
                        'to_status'     => getInspectionStatus($logs->to_status),
                        'approver_name' => getUsername($logs->approved_by),
                        'created_by'    => getUsername($logs->created_by),
                        'created_at'    => Displaydateformat($logs->created_at),
                    ];
                }
                $success['approvalLogs'] = $approvalLogs;
            }

            return $this->sendResponse($success, 'Monthly Forklift Inspection Details');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorized.', ['error' => 'Something went wrong, please try again later']);
        }
    }

    // Ehs officer submit

    public function ehsofficersubmit(Request $request)
    {
        try {
            $id = $request->id;
            $monthlyForklift = $this->forklift_inspection->find($id);

            $inspection_updates = $this->forklift_inspection->EHSOfficerUpdate($id);

            $inspection_details = $this->forklift_inspection->selectOne($id);
            if ($request->is_passed == 1) {
                $message = 'ForkLift Inspeciton Approved Successfully';
                $web_link =   admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
            } else {
                $message = 'Inspection Recommended for the CAPA Action';
                $web_link =   admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = WAITING_FOR_CAPA_ACTION;
            }
            $userIds = [
                'users' => $inspection_details->created_by,
            ];
            $mailsubject = 'Monthly Forklift Inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 2,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => $message,
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $id,
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
            $url = admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($id) . '/ehs');
            $details = array(
                'safety_type' => 'Monthly Forklift Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new SafetyInspection($details));

            $insert_array = [
                'type' => MONTHLY_FORKLIFT_INSPECTION,
                'inspection_id' => $id,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);


            $data = [
                'Monthlyforkliftinspection' => $id,
            ];
            return $this->sendResponse($data, 'Ehs officer Responded sucessfully');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    // Capa action

    public function capaSubmit(Request $request)
    {
        try {

            $id = $request->id;

            $monthlyForklift = $this->forklift_inspection->find($id);
            $forklift_inspection = $this->forklift_inspection->capaSubmit($id);
            $inspection_details = $this->forklift_inspection->selectOne($id);
            $ehsOfficers = $inspection_details->verified_by;
            $userIds = [
                'users' => $ehsOfficers,
            ];
            $mailsubject = 'Monthly Forklift Inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 2,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "CAPA Action Completed by the Fire Associates",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_details->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($inspection_details->id)) . '/ehsVerify',
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $user = $inspection_details->verified_by;
            $email_id = getUseremail($user);
            $url = admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($id) . '/ehsVerify');
            $details = array(
                'safety_type' => 'Monthly Forklift Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => 'CAPA Action Completed by the Fire Associates',
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new SafetyInspection($details));

            $insert_array = [
                'type' => MONTHLY_FORKLIFT_INSPECTION,
                'inspection_id' => $id,
                'from_status' => WAITING_FOR_CAPA_ACTION,
                'to_status' => WAITING_FOR_CAPA_VERIFICATION,
                'created_by' => Auth::id(),
                'remarks' => $request->capa_remarks,
            ];
            $this->statusLog->create($insert_array);
            $data = [
                'Monthlyforkliftinspection' => $id,
            ];
            return $this->sendResponse($data, 'Capa Action has been completed successfully !');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorized.', ['error' => 'Something went wrong, please try again later']);
        }
    }

    // capa verification
    public function capaverification(Request $request)
    {
        try {
            $id = $request->id;

            $monthlyForklift = $this->forklift_inspection->find($id);
            $status = $request->action == 'approve' ? 1 : 0;
            $remarks = $request->remarks;
            $forklift_inspection = $this->forklift_inspection->capaVerifySubmit($id, $status, $remarks);

            $inspection_details = $this->forklift_inspection->selectOne($id);
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = WAITING_FOR_L1_VERIFICATION;
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = [$inspection_details->created_by];
                $to_status = EHS_OFFICER_REJECTED;
            }
            $mailsubject = 'Monthly Forklift Inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
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
                'assigned_user' => array_to_string($users),
                'created_by' => Auth::id(),
            );

            foreach ($users as $user) {
                $title = $message;
                $email_id = getUseremail($user);
                $url = $web_link;
                $details = array(
                    'safety_type' => 'Monthly Forklift Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }


            notificationSave($notificationData);
            $insert_array = [
                'type' => MONTHLY_FORKLIFT_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            $data = [
                'Monthlyforkliftinspection' => $id,
            ];
            return $this->sendResponse($data, 'Capa Verification has been completed successfully !');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorized.', ['error' => 'Something went wrong, please try again later']);
        }
    }

    // l1 verification

    public function leveloneverfication(Request $request)
    {

        try {

            $id = $request->id;
            $status = $request->action == 'approve' ? 1 : 0;
            $remarks = $request->level_one_manager;
            $forklift_inspection = $this->forklift_inspection->levelOneManagerSubmit($id, $status, $remarks);

            $inspection_details = $this->forklift_inspection->selectOne($id);
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/level-two-manager');
                $user = GetLevelTwoManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
                $to_status = WAITING_FOR_L2_VERIFICATION;
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = [$inspection_details->created_by];
                $to_status = L1_MANAGER_REJECTED;
            }
            $mailsubject = 'Monthly Forklift Inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
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
                'assigned_user' => array_to_string($users),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            foreach ($users as $user) {
                $title = $message;
                $email_id = getUseremail($user);
                $url = $web_link;
                $details = array(
                    'safety_type' => 'Monthly Forklift Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }

            $insert_array = [
                'type' => MONTHLY_FORKLIFT_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L1_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_one_manager,
            ];
            $this->statusLog->create($insert_array);
            $data = [
                'Monthlyforkliftinspection' => $id,
            ];
            return $this->sendResponse($data, 'Level one manager Verification has been completed successfully !');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorized', ['error' => 'Something went wrong,Please try again later']);
        }
    }

    // l2 verification

     public function leveltwoverfication(Request $request)
    {

        try {

            $id = $request->id;
            $status = $request->action == 'approve' ? 1 : 0;
             $remarks = $request->level_two_manager;
            $forklift_inspection = $this->forklift_inspection->levelTwoManagerSubmit($id, $status, $remarks);
            // $signature_update = $this->signature->signatureUpload(MONTHLY_FORKLIFT_INSPECTION, $id);
            $inspection_details = $this->forklift_inspection->selectOne($id);
            if ($status == 1) {
                $message = 'ForkLift Inspeciton Approved Successfully!';
                $web_link =   admin_url('safety/forklift-inspection/monthly/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = L2_MANAGER_REJECTED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
            }

            $mailsubject = 'Monthly Forklift Inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
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
                'assigned_user' => array_to_string($users),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            foreach ($users as $user) {
                $title = $message;
                $email_id = getUseremail($user);
                $url = $web_link;
                $details = array(
                    'safety_type' => 'Monthly Forklift Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }

            $insert_array = [
                'type' => MONTHLY_FORKLIFT_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L2_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_two_manager,
            ];
            $this->statusLog->create($insert_array);
            $data = [
                'Monthlyforkliftinspection' => $id,
            ];
            return $this->sendResponse($data, 'Level two manager Verification has been completed successfully !');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorized', ['error' => 'Something went wrong,Please try again later']);
        }
    }


    public function store(Request $request)
    {
        try {
            $rules = [
                'doc_no' => 'required',
                'inspection_date' => 'required',
                'location_id' => 'required',
                'shift_id' => 'required',
                'next_due' => 'required',
                'unit_id' => 'required',
                'frequency_id' => 'required',
                'identification_no' => 'required',
                'forklift_type' => 'required',
                'capacity' => 'required',
            ];

            $messages = [
                'doc_no.required' => 'Document number is required.',
                'inspection_date.required' => 'Inspection  Date is Required',
                'location_id.required' => 'Location is Required',
                'shift_id.required' => 'Shift is Required',
                'next_due.required' => 'Next due date is Required',
                'unit_id.required' => 'Unit is Required',
                'frequency_id.required' => 'Frequency is Required',
                'forklift_type.required' => 'Forklift Type is Required',
                'capacity.required' => 'Capacity is Required',
                'identification_no.required' => 'Identification Number is Required',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }

            $forklift_inspection = $this->forklift_inspection->store_api();
            $id = $forklift_inspection->id;
            $signature_update = $this->signature->signatureUpload_api(MONTHLY_FORKLIFT_INSPECTION, $forklift_inspection->id);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'Monthly Forklift Inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 2,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Monthly ForkLift Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $forklift_inspection->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($forklift_inspection->id) . '/ehs'),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Monthly ForkLift Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($id) . '/ehs');
                $details = array(
                    'safety_type' => 'Monthly Forklift Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $forklift_inspection
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }

            $insert_array = [
                'type' => MONTHLY_FORKLIFT_INSPECTION,
                'inspection_id' => $forklift_inspection->id,
                'from_status' => 0,
                'to_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'created_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);

            $success = [
                "success" => $forklift_inspection,
            ];
            return $this->sendResponse($success, 'Inspection Created');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    public function forklift_type()
    {
        try {
            $forklift_type = $this->forklift_type->getForkLift()->toArray();

            $forklifts = array_map(function ($item) {
                return [
                    'id' => $item['id'],
                    'name' => $item['forklift'],
                    'created_by' => getUsername($item['created_by']),
                    'status' => ($item['status'] == 1 ? 'Active' : 'InActive'),
                ];
            }, $forklift_type);

            $success = array(
                'forklift_types' => $forklifts,
            );
            return $this->sendResponse($success, 'Forklift Type');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
    public function frequencyName()
    {
        try {
            $frquencies = $this->frequency->getFrequency()->toArray();

            $frequency = array_map(function ($item) {
                return  [
                    'id' => $item['id'],
                    'name' => $item['frequency_name'],
                    'created_by' => getUsername($item['created_by']),
                    'status' => ($item['status'] == 1 ? 'Active' : 'InActive'),
                ];
            }, $frquencies);

            $success = array(
                'frquencies' => $frequency,
            );
            return $this->sendResponse($success, 'Frequency');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
    public function shift()
    {
        try {
            $shifts = $this->shift->getShiftname()->toArray();
            $shift = array_map(function ($item) {
                return  [
                    'id' => $item['id'],
                    'name' => $item['shift'],
                    'created_by' => getUsername($item['created_by']),
                    'status' => ($item['status'] == 1 ? 'Active' : 'InActive'),
                ];
            }, $shifts);

            $success = array(
                'shifts' => $shift,
            );
            return $this->sendResponse($success, 'Shift');
        } catch (Exception $ex) {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
