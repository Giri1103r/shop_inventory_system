<?php

namespace App\Http\Controllers\Api\Inspection\Fire;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use App\Mail\Inspection\Fire\FireInspection;
use App\Models\Inspection\Fire\FireStatusLog;
use App\Models\Inspection\Fire\FireFileUpload;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Fire\FireAlarmInspection;
use App\Models\Inspection\Fire\FireSignatureUpload;
use App\Models\Inspection\Fire\FireAlarmInspectionDetails;

class FireAlarmController extends BaseController
{
    private $inspection;
    private $inspection_details;
    private $statusLog;
    private $files;
    private $signature;
    private $document_reference;

    public function __construct()
    {
        $this->inspection = new FireAlarmInspection();
        $this->inspection_details = new FireAlarmInspectionDetails();
        $this->statusLog = new FireStatusLog();
        $this->files = new FireFileUpload();
        $this->signature = new FireSignatureUpload();
        $this->document_reference = new InspectionStaticDocno();
    }

    public function List(Request $request)
    {
        if (Auth::check()) {
            try {
                $data = $this->inspection->listApi();
                if (count($data) > 0) {
                    return response()->json([
                        'success' => true,
                        'data' => $data,
                        'message' => 'Data Retrieved Successfully',
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'data' => $data,
                        'message' => 'No Data Found',
                    ], 200);
                }
            } catch (Exception $ex) {
                report($ex);
                return $this->sendError(
                    'Unauthorised.',
                    ['error' => 'Please try again after sometimes'],
                    406
                );
            }
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    public function Add(Request $request)
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
                'glass.*' => 'required',
                'hammer.*' => 'required',
                'mannual_call_point.*' => 'required',
                'approach.*' => 'required',
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
                'glass.*.required' => 'Status of Glass is required',
                'hammer.*.required' => 'Status Of Hammer is required',
                'mannual_call_point.*.required' => 'Mannual Call Point is required',
                'leakage.*.required' => 'Leakage Status is required',
                'approach.*.required' => 'Approach is required',
                'observation.required' => 'Observation is required',
                'remarks.*.required' => 'Remarks is required',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }

            $inspection = $this->inspection->storeApi();
            $inspection_type = FIRE_ALARM_INSPECTION;
            $id = $inspection->id;

            $inspection_details = $this->inspection_details->storeApi($id);
            $inspection_file = $this->files->file_upload_api($inspection_type, $id);

            // $checklist_store = $this->checklist_follow->store($inspection_type, $id);

            $signature_update = $this->signature->CheckedBySignatureApi($id, $inspection_type);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'Fire Alarm Inspection';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Fire Alarm Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('fire/fire-alarm-inspection/view/' . encryptId($id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Fire Alarm Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('fire/fire-alarm-inspection/verification/' . encryptId($id) . '/ehs');
                $details = array(
                    'fire_type' => 'Fire Alarm Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => FIRE_ALARM_INSPECTION,
                'inspection_id' => $id,
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
                $inspection_type = FIRE_ALARM_INSPECTION;
                $inspection_image = $this->files->GetFileApi($inspection_type,$id);
                $document_no = $this->document_reference->selectOne($inspection->document_reference_id);

                // Hooter Main Section
                $inspection_main = [
                    'id' => $inspection->id,
                    'document_no' => $document_no->doc_no,
                    'issue_date' => Displaydateformat($document_no->issue_date),
                    'issue_date' => $document_no->rev_dt,
                    'document_reference_id' => $inspection->document_reference_id,
                    'date_of_inspection' => Displaydateformat($inspection->date_of_inspection),
                    'location_name' => getLocationname($inspection->location),
                    'shift_name' => getShiftname($inspection->shift),
                    'next_due' => Displaydateformat($inspection->next_due),
                    'unit_name' => getUnitname($inspection->unit),
                    'frequency_name' => getFrequencyname($inspection->frequency),
                    'inspection_status' => GetStatusValue($inspection->inspection_status),
                    'remarks' => $inspection->remarks,
                    'created_at' => Displaydateformat($inspection->created_at),
                    'updated_at' => Displaydateformat($inspection->updated_at),
                    'observation' => $inspection->observation == 1 ? 'Yes' : 'No',
                    'inspection_status' => GetStatusValue($inspection->inspection_status),
                    'capa_recomendation' => $inspection->capa_recomendation,
                    'capa_remarks' => $inspection->capa_remarks,
                    'capa_ehs_remarks' => $inspection->capa_ehs_remarks,
                    'level_one_manager_remarks' => $inspection->level_one_manager_remarks,
                    'level_two_manager_remarks' => $inspection->level_two_manager_remarks,
                    'level_two_manager_remarks' => $inspection->level_two_manager_remarks,
                    'remarks' => $inspection->remarks,
                    'checked_by' => getUsername($inspection->checked_by),
                    'verified_by' => getUsername($inspection->verified_by),
                    'approved_by' => getUsername($inspection->verified_by),
                    'checked_by_id' => $inspection->checked_by,
                    'verified_by_id' => $inspection->verified_by,
                    'approved_by_id' => $inspection->approved_by,
                    'created_by_id' => $inspection->created_by,
                    'created_by' => getUsername($inspection->created_by),
                    'inspection_image' => $inspection_image ?? [],
                ];

                // Inspection Sub Data
                $inspection_details = [];

                foreach($details as $index => $values)
                {
                    $inspection_details[$index + 1] = [
                        'id' => $values->id,
                        'sr_no' => $values->sr_no,
                        'inspection_id' => $values->inspection_id,
                        'resource_code' => $values->resource_code,
                        'department_name' => GetDeptName($values->department),
                        'quantity' => $values->quantity,
                        'glass' => $values->glass == FUNCTIONAL ? 'Functional' : 'Non-Functional',
                        'hammer' => $values->hammer == FUNCTIONAL ? 'Functional' : 'Non-Functional',
                        'mannual_call_point' => $values->mannual_call_point == PRESENT ? 'Present' : 'Missing',
                        'approach' => $values->approach,
                        'remarks' => $values->remarks,
                        'created_by_name' => getUsername($values->created_by),
                        'created_at' => Displaydateformat($values->created_at),
                    ];
                }


                if (isset($inspection->verified_by)) {
                    $ehs_officer_signature = GetFireSignature($inspection->verified_by, $id, $inspection_type);
                    $inspection_details += [
                        'verified_by' => getUsername($inspection->verified_by),
                        'date' => Displaydateformat($inspection->created_at),
                        'verified_by_signature' => admin_url($ehs_officer_signature),
                    ];
                }

                if (isset($inspection->approved_by)) {
                    $ehs_approved_signature = GetFireSignature($inspection->approved_by, $id, $inspection_type);
                    $inspection_details += [
                        'ehs_approved_by' => getUsername($inspection->approved_by),
                        'ehs_approved_date' => Displaydateformat($inspection->created_at),
                        'ehs_approved_by_signature' => admin_url($ehs_approved_signature),
                    ];
                }

                if (isset($inspection->capa_recomendation)) {
                    $capa_recommendation[] = [
                        'capa_recommendation' => $inspection->capa_recommendation,
                        'capa_recomendation_remarks' => $inspection->remarks,
                    ];
                }

                if (isset($inspection->capa_remarks)) {
                    $fire_associate_signature = GetFireSignature($inspection->created_by, $id, $inspection_type);
                    $inspection_details += [
                        'capa_name' => getUsername($inspection->created_by),
                        'capa_date' => Displaydateformat($inspection->created_at),
                        'capa_signature' => admin_url($fire_associate_signature),
                        'capa_remarks' => $inspection->capa_remarks,
                    ];
                }

                if (isset($inspection->capa_ehs_remarks)) {
                    $ehs_capa_signature = GetFireSignature($inspection->verified_by, $id, $inspection_type);
                    $inspection_details += [
                        'capa_ehs_name' => getUsername($inspection->created_by),
                        'capa_ehs_date' => Displaydateformat($inspection->created_at),
                        'capa_ehs__signature' => admin_url($ehs_capa_signature),
                        'capa_ehs_remarks' => $inspection->capa_ehs_remarks,
                    ];
                }

                if (isset($inspection->level_one_manager_remarks)) {
                    $level_one_signature = GetFireSignature($inspection->l1_manager_verified_by, $id, $inspection_type);
                    $inspection_details += [
                        'level_one_name' => getUsername($inspection->l1_manager_verified_by),
                        'level_one_date' => Displaydateformat($inspection->created_at),
                        'level_one_signature' => admin_url($level_one_signature),
                        'level_one_remarks' => $inspection->level_one_manager_remarks,
                    ];
                }

                if (isset($inspection->level_two_manager_remarks)) {
                    $level_two_signature = GetFireSignature($inspection->l2_manager_verified_by, $id, $inspection_type);
                    $inspection_details += [
                        'level_two_name' => getUsername($inspection->l2_manager_verified_by),
                        'level_two_date' => Displaydateformat($inspection->created_at),
                        'level_two_signature' => admin_url($level_two_signature),
                        'level_two_remarks' => $inspection->level_two_manager_remarks,
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

                $data = array(
                    'inspection_main' => $inspection_main,
                    'inspection_details' => $inspection_details,
                    'logs' => $logs,
                );

                return $this->sendResponse($data, 'Hooter Inspection Details');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError(
                'Unauthorised.',
                ['error' => 'Please try again after sometimes'],
                406
            );
        }
    }
}
