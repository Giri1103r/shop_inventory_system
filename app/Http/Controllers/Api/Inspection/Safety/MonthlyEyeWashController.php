<?php

namespace App\Http\Controllers\Api\Inspection\Safety;

use App\Http\Controllers\Api\BaseController;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\Inspection\Safety\SafetyInspection;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Safety\SafetyStatusLog;
use App\Models\Inspection\Safety\SignatureUpload;
use App\Models\Inspection\Safety\EyeWashInspectionDetails;
use App\Models\Inspection\Safety\MonthlyEyeWashInspection;

class MonthlyEyeWashController extends BaseController
{
    private $inspection;
    private $inspection_details;
    private $statusLog;
    private $signature;
    private $document_reference;

    public function __construct()
    {
        $this->inspection = new MonthlyEyeWashInspection();
        $this->inspection_details = new EyeWashInspectionDetails();
        $this->statusLog = new SafetyStatusLog();
        $this->signature = new SignatureUpload();
        $this->document_reference = new InspectionStaticDocno();
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
            $query = MonthlyEyeWashInspection::select('inspection_monthly_eyewash.*', 'inspection_shift_option.*', 'masters_unit.*', 'masters_location.*', 'inspection_frequency_option.*', 'inspection_monthly_eyewash.id as inspection_id', 'inspection_monthly_eyewash.created_at as inspection_created_at')
                ->leftJoin('masters_location', 'inspection_monthly_eyewash.location', '=', 'masters_location.id')
                ->leftJoin('inspection_shift_option', 'inspection_monthly_eyewash.shift', '=', 'inspection_shift_option.id')
                ->leftJoin('masters_unit', 'inspection_monthly_eyewash.unit', '=', 'masters_unit.id')
                ->leftJoin('inspection_frequency_option', 'inspection_monthly_eyewash.frequency', '=', 'inspection_frequency_option.id')
                ->leftJoin('inspection_static_docno', 'inspection_monthly_eyewash.document_reference_id', '=', 'inspection_static_docno.id');
            $org_total_counts = $query->count();

            if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_OFFICER) || CheckUserRole(ROLE_L1_MANAGER) || CheckUserRole(ROLE_L2_MANAGER)) {
            } else if (CheckUserRole(ROLE_FIRE_ASSOCIATES)) {
                $query->where('inspection_monthly_eyewash.created_by', Auth::id());
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

            $query_array = $query->orderBy('inspection_monthly_eyewash.id', 'DESC')->paginate($request->input('per_page', 10));

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

    public function Add(Request $request)
    {
        try {
            $rules = [
                'inspection_date' => 'required',
                'location_id' => 'required',
                'shift_id' => 'required',
                'next_due' => 'required',
                'unit_id' => 'required',
                'frequency_id' => 'required',
                'sr_no.*' => 'required',
                'location.*' => 'required',
                'resource_code.*' => 'required',
                'condition.*' => 'required',
                'value.*' => 'required',
                'hfsov.*' => 'required',
                'foot_pedal.*' => 'required',
                'eyewash_heads.*' => 'required',
                'receptacle.*' => 'required',
                'water.*' => 'required',
                'quality.*' => 'required',
                'pressure.*' => 'required',
                'temperature.*' => 'required',
            ];

            $messages = [
                'inspection_date.required' => 'Inspection Date is required',
                'location_id.required' => 'Location is required',
                'shift_id.required' => 'Shift is required',
                'next_due.required' => 'Next due date is required',
                'unit_id.required' => 'Unit is required',
                'location.*.required' => 'Location is required',
                'resource_code.*.required' => 'Resource code is required',
                'condition.*.required' => 'Condition is required',
                'value.*.required' => 'Valve is required',
                'hfsov.*.required' => 'Hand free stay open value is required',
                'foot_pedal.*.required' => 'Foot Pedal Value is required',
                'eyewash_heads.*.required' => 'Eye wash heads is required',
                'receptacle.*.required' => 'Receptable name is requried',
                'water.*.required' => 'Water quality is required',
                'quality.*.required' => 'Quality is required',
                'pressure.*.required' => 'Pressure is required',
                'temperature.*.required' => 'Temperature is required',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }

            $store_eyewash_inspection = $this->inspection->storeApi();
            $inspection_id = $store_eyewash_inspection->id;
            $inspection_details = $this->inspection->selectOne($inspection_id);
            $store_inspection_details = $this->inspection_details->storeApi($inspection_id);
            $signature_update = $this->signature->signatureUpload_api(EYE_WASH_INSPECTION, $store_eyewash_inspection->id);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'Monthly EyeWash Inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Monthly EyeWash Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safety/eyewash/monthly/verification/view/' . encryptId($inspection_id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Monthly Eyewash Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('safety/eyewash/monthly/verification/verification/' . encryptId($inspection_id) . '/ehs');
                $details = array(
                    'safety_type' => 'Monthly Eyewash Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }

            $insert_array = [
                'type' => EYE_WASH_INSPECTION,
                'inspection_id' => $inspection_id,
                'from_status' => 0,
                'to_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'created_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);

            $data = [
                'inspection' => $inspection_details,
            ];
            return $this->sendResponse($data, 'Inspection Created');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    public function View(Request $request)
    {
        try {
            if (Auth::check()) {

                $id = $request->id;
                $inspection = $this->inspection->selectOne($id);
                $details = $this->inspection_details->GetDetails($inspection->id);
                // dd($details);
                $inspection_type = EYE_WASH_INSPECTION;
                $document_no = $this->document_reference->selectOne($inspection->document_reference_id);
                $signature = GetSafetySignature($inspection->created_by, $id, $inspection_type);

                $inspection_main = [
                    'id' => $inspection->id,
                    'document_no' => $document_no->doc_no,
                    'issue_date' => $document_no->issue_date,
                    'rev_dt' => $document_no->rev_dt,
                    'document_reference_id' => $inspection->document_reference_id,
                    'date_of_inspection' => Displaydateformat($inspection->date_of_inspection),
                    'location_name' => getLocationname($inspection->location),
                    'shift' => getShiftname($inspection->shift),
                    'next_due' => Displaydateformat($inspection->next_due),
                    'unit' => getUnitname($inspection->unit),
                    'frequency_name' => getFrequencyname($inspection->frequency),
                    'verified_by' => getUsername($inspection->verified_by),
                    'created_at' => Displaydateformat($inspection->created_at),
                    'updated_at' => Displaydateformat($inspection->updated_at),
                    'approved_by' => getUsername($inspection->approved_by),
                    'inspection_status' => GetStatusValue($inspection->inspection_status),
                    'capa_recomendation' => $inspection->capa_recomendation,
                    'capa_remarks' => $inspection->capa_remarks,
                    'capa_ehs_remarks' => $inspection->capa_ehs_remarks,
                    'level_one_manager_remarks' => $inspection->level_one_manager_remarks,
                    'level_two_manager_remarks' => $inspection->level_two_manager_remarks,
                    'remarks' => $inspection->remarks,
                    'checked_by' => getUsername($inspection->checked_by),
                    'verified_by' => getUsername($inspection->verified_by),
                    'approved_by' => getUsername($inspection->approved_by),
                    'checked_by_id' => getUsername($inspection->checked_by),
                    'verified_by_id' => getUsername($inspection->verified_by),
                    'approved_by_id' => getUsername($inspection->approved_by),
                    'created_by_id' => getUsername($inspection->created_by),
                    'created_by' => getUsername($inspection->created_by),
                ];

                $inspection_details = [];

                foreach ($details as $index => $values) {
                    $inspection_details[$index + 1] = [
                        'id' => $values->id,
                        'sr_no' => $values->sr_no,
                        'location' => $values->location,
                        'resource_code' => $values->resource_code,
                        'inspection_condition' => GetConditionName($values->inspection_condition),
                        'value' => $values->value,
                        'hand_free_stay_open_value' => $values->hand_free_stay_open_value,
                        'foot_pedal_value' => $values->foot_pedal_value,
                        'eyewash_heads_value' => $values->eyewash_heads_value == 1 ? 'OK' : "NOT Ok",
                        'receptacle' => $values->receptacle,
                        'water' => GetInspectionWater($values->water),
                        'pressure' => $values->pressure,
                        'temperature' => $values->temperature == "1" ? 'NORMAL' : 'ABNORMAL',
                        'remarks' => $values->remarks,
                    ];
                }

                if (isset($inspection->verified_by)) {
                    // $ehs_officer_signature = GetFireSignature($inspection->verified_by, $id, $inspection_type);
                    $inspection_details += [
                        'verified_by' => getUsername($inspection->verified_by),
                        'date' => Displaydateformat($inspection->created_at),
                        // 'verified_by_signature' => admin_url($ehs_officer_signature),
                    ];
                }

                if (isset($inspection->approved_by)) {
                    // $ehs_approved_signature = GetFireSignature($inspection->approved_by, $id, $inspection_type);
                    $inspection_details += [
                        'ehs_approved_by' => getUsername($inspection->approved_by),
                        'ehs_approved_date' => Displaydateformat($inspection->created_at),
                        // 'ehs_approved_by_signature' => admin_url($ehs_approved_signature),
                    ];
                }

                if (isset($inspection->capa_recomendation)) {
                    $capa_recommendation[] = [
                        'capa_recommendation' => $inspection->capa_recommendation,
                        'capa_recomendation_remarks' => $inspection->remarks,
                    ];
                }

                // Approval Logs
                $status_logs = $this->statusLog->selectOne($id, $inspection_type);
                $logs = [];

                foreach ($status_logs as $log_index => $status) {
                    $logs[$log_index] = [
                        'id' => $status->id,
                        'type' => $status->type,
                        'inspection_id' => $status->inspection_id,
                        'from_status' => getInspectionStatus($status->from_status),
                        'to_status' => getInspectionStatus($status->to_status),
                        'remarks' => $status->remarks ?? 'N/A',
                        'approved_by' => $status->approved_by ? getUsername($status->approved_by) : '-',
                        'created_by' => $status->created_by ? getUsername($status->created_by) : '-',
                        'created_at' => Displaydateformat($status->created_at),
                    ];
                }

                $data = [
                    'inspection_main' => $inspection_main,
                    'inspection_details' => $inspection_details,
                    'logs' => $logs,
                ];

                return $this->sendResponse($data, 'Inspection Details');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError(
                'Something went wrong.',
                ['error' => 'Please try again later'],
                406
            );
        }
    }


    // ehs officer verification
    public function EHSOfficerSubmit(Request $request)
    {
        try {
            $request = Request();
            $id = ($request->id);
            $inspection_updates = $this->inspection->EHSOfficerUpdate($id);
            // $signature_update = $this->signature->signatureUpload(EYE_WASH_INSPECTION, $id);
            $inspection_details = $this->inspection->selectOne($id);
            if ($request->is_passed == 1) {
                $message = 'eye_wash Inspeciton Approved Successfully';
                $web_link =   admin_url('safety/eye-wash-inspection/monthly/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
            } else {
                $message = 'Inspection Recommended for the CAPA Action';
                $web_link =   admin_url('safety/eye-wash-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = WAITING_FOR_CAPA_ACTION;
            }
            $userIds = [
                'users' => $inspection_details->created_by,
            ];
            $mailsubject = 'Monthly Eye Wash Inspection Inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
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
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = $message;
            $user = $inspection_details->created_by;
            $email_id = getUseremail($user);
            $url = admin_url('safety/eye-wash-inspection/monthly/verification/' . encryptId($id) . '/ehs');
            $details = array(
                'safety_type' => 'Monthly eye_wash Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new SafetyInspection($details));

            $insert_array = [
                'type' => EYE_WASH_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            $success = [
                "success" => $inspection_details,
            ];
            return $this->sendResponse($success, 'Inspection Created');
        } catch (Exception $ex) {
            dd($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    // capa submit

    public function CAPASubmit(Request $request)
    {
        try {
            $id = ($request->id);
            $eye_wash_inspection = $this->inspection->capaSubmit($id);
            $inspection_details = $this->inspection->selectOne($id);
            // $signature_update = $this->signature->signatureUpload(EYE_WASH_INSPECTION, $id);
            $ehsOfficers = $inspection_details->verified_by;
            $userIds = [
                'users' => $ehsOfficers,
            ];

            $mailsubject = 'Monthly Eye Wash Inspection Inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "CAPA Action Completed by the Fire Associates",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_details->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safety/eye-wash-inspection/monthly/verification/' . encryptId($inspection_details->id)) . '/ehsVerify',
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $user = $inspection_details->verified_by;
            $email_id = getUseremail($user);
            $url = admin_url('safety/eye-wash-inspection/monthly/verification/' . encryptId($id) . '/ehsVerify');
            $details = array(
                'safety_type' => 'Monthly eye_wash Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => 'CAPA Action Completed by the Fire Associates',
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new SafetyInspection($details));

            $insert_array = [
                'type' => EYE_WASH_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_ACTION,
                'to_status' => WAITING_FOR_CAPA_VERIFICATION,
                'created_by' => Auth::id(),
                'remarks' => $request->capa_remarks,
            ];
            $this->statusLog->create($insert_array);
            $success = [
                "success" => $eye_wash_inspection,
            ];
            return $this->sendResponse($success, 'Capa Action Done');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    public function CAPAVerifySubmit(Request $request)
    {
        try {
            $id = ($request->id);
            $status = $request->action == 'approve' ? 1 : 0;
            $remarks = $request->remarks;
            $eye_wash_inspection = $this->inspection->capaVerifySubmit($id, $status, $remarks);
            // $signature_update = $this->signature->signatureUpload(EYE_WASH_INSPECTION, $id);
            $inspection_details = $this->inspection->selectOne($id);
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('safety/eye-wash-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = WAITING_FOR_L1_VERIFICATION;
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('safety/eye-wash-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = EHS_OFFICER_REJECTED;
            }
            $mailsubject = 'Monthly Eye Wash Inspection Inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
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

            foreach ($users as $user) {
                $title = $message;
                $email_id = getUseremail($user);
                $url = $web_link;
                $details = array(
                    'safety_type' => 'Monthly eye_wash Inspection',
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
                'type' => EYE_WASH_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            $success = [
                "success" => $eye_wash_inspection,
            ];
            return $this->sendResponse($success, 'Capa Verification has been Completed');
        } catch (Exception $ex) {
            dd($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }


    public function levelOneManagerSubmit(Request $request)
    {
        try {
            $id = ($request->id);
            $status = $request->action == 'approve' ? 1 : 0;
            $remarks = $request->level_one_manager;
            $eye_wash_inspection = $this->inspection->levelOneManagerSubmit($id, $status, $remarks);
            // $signature_update = $this->signature->signatureUpload(EYE_WASH_INSPECTION, $id);
            $inspection_details = $this->inspection->selectOne($id);
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('safety/eye-wash-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/level-two-manager');
                $user = GetLevelTwoManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
                $to_status = WAITING_FOR_L2_VERIFICATION;
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('safety/eye-wash-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = L1_MANAGER_REJECTED;
            }
            $mailsubject = 'Monthly Eye Wash Inspection Inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
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
                    'safety_type' => 'Monthly eye_wash Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }

            $insert_array = [
                'type' => EYE_WASH_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L1_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_one_manager,
            ];
            $this->statusLog->create($insert_array);
            $success = [
                "success" => $eye_wash_inspection,
            ];
            return $this->sendResponse($success, 'Level One Verification has been Completed');
        } catch (Exception $ex) {
            dd($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

      public function levelTwoManagerSubmit(Request $request)
    {
        try {
            $id = ($request->id);
            $status = $request->action == 'approve' ? 1 : 0;
            $remarks = $request->level_two_manager;
            $eye_wash_inspection = $this->inspection->levelTwoManagerSubmit($id, $status, $remarks);
            // $signature_update = $this->signature->signatureUpload(EYE_WASH_INSPECTION, $id);
            $inspection_details = $this->inspection->selectOne($id);
            if ($status == 1) {
                $message = 'eye_wash Inspeciton Approved Successfully!';
                $web_link =   admin_url('safety/eye-wash-inspection/monthly/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('safety/eye-wash-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = L2_MANAGER_REJECTED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
            }

            $mailsubject = 'Monthly Eye Wash Inspection Inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
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
                    'safety_type' => 'Monthly eye_wash Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }

            $insert_array = [
                'type' => EYE_WASH_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L2_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_two_manager,
            ];
            $this->statusLog->create($insert_array);
             $success = [
                "success" => $eye_wash_inspection,
            ];
            return $this->sendResponse($success, 'Level Two Verification has been Completed');
        } catch (Exception $ex) {
            dd($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

}
