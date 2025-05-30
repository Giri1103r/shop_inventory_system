<?php

namespace App\Http\Controllers\Api\Inspection\Fire;

use Exception;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\FuncCall;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use App\Mail\Inspection\Fire\FireInspection;
use App\Models\Inspection\Fire\FireStatusLog;
use App\Models\Inspection\Fire\FireFileUpload;
use App\Models\Inspection\Fire\FireSignatureUpload;
use App\Models\Inspection\Fire\HydrantRiserInspection;
use App\Models\Inspection\Fire\HydrantRiserInspectionDetails;

class HydrantRiserController extends BaseController
{
    private $hydrant;
    private $hydrant_checklist;
    private $files;
    private $statusLog;
    private $signature;

    public function __construct()
    {
        $this->hydrant = new HydrantRiserInspection();
        $this->hydrant_checklist = new HydrantRiserInspectionDetails();
        $this->files = new FireFileUpload();
        $this->statusLog = new FireStatusLog();
        $this->signature = new FireSignatureUpload();
    }

    public  function list()
    {
        if (Auth::check()) {
            try {
                $data = $this->hydrant->listApi();
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
                dd($ex);
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

    public function view(Request $request)
    {
        try {
            if (Auth::check()) {
                $id = $request->id;
                $inspection_type = HYDRANT_RISER;
                $inspection = $this->hydrant->getInspectionDetails($id);
                $details = $this->hydrant_checklist->GetDetails($inspection->id);
                $inspection_image = $this->files->GetFileApi($inspection_type, $id);
                $inspection_created_by_signature =  GetFireSignature($inspection->created_by, $inspection->id, $inspection_type);

                // dd($inspection);

                // Hooter Main Section
                $inspection_main = [
                    'id' => $inspection->id,
                    'document_no' => $inspection->doc_no,
                    'issue_date' => Displaydateformat($inspection->issue_date),
                    'rev_dt' => $inspection->rev_dt,
                    'date_of_inspection' => Displaydateformat($inspection->date_of_inspection),
                    'location_name' => getLocationname($inspection->location),
                    'shift_name' => getShiftname($inspection->shift),
                    'next_due' => Displaydateformat($inspection->next_due),
                    'unit_name' => getUnitname($inspection->unit),
                    'frequency_name' => getFrequencyname($inspection->frequency),
                    'inspection_image' => $inspection_image ?? [],
                    'inspection_created_by_signature' => admin_url($inspection_created_by_signature),
                    'observation' => $inspection->observation_needed == 1 ? 'Yes' : 'No',
                ];

                $inspection_details = [];

                foreach ($details as $index => $values) {

                    $inspection_details[$index] = [
                        'id' => $values->id,
                        'sr_no' => $index + 1,
                        'inspection_id' => $values->inspection_id,
                        'location_name' => getLocationname($values->location_check_id),
                        'hydrant_no' => $values->hydrant_no,
                        'lugs' => $values->lugs_id == 1 ? 'Present' : 'Missing',
                        'rubber_washer' => $values->rubber_washer == '1' ? 'Intact' : 'Damaged',
                        'check_nut' => $values->check_nut == 1 ? 'Present' : 'Missing',
                        'spindle_wheel' => $values->spindle_wheel == '1' ? 'Functional' : 'Non-Functional',
                        'blank_cap' => $values->blank_cap == 1 ? 'Present' : 'Missing',
                        'female_coupling' => $values->female_coupling == '1' ? 'Functional' : 'Non-Functional',
                        'lever' => $values->lever == '1' ? 'Functional' : 'Non-Functional',
                        'flow_test' => $values->flow_test,
                        'physical_condition' => GetConditionName($values->physical_condition),
                        'condition_of_ivs' => $values->condition_of_ivs == '1' ? 'Functional' : 'Non-Functional',
                        'approach' => $values->approach,
                        'remarks' => $values->remarks,
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

                return $this->sendResponse($data, 'Inspection Details');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
            }
        } catch (Exception $ex) {
            dd($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    public function store(Request $request)
    {
        // dd($request->all());
        try {

            $rules = [
                'inspection_date' => 'required',
                'location_id' => 'required',
                'shift_id' => 'required',
                'next_due' => 'required',
                'unit_id' => 'required',
                'frequency_id' => 'required',

                'sr_no.*' => 'required',
                'department.*' => 'required',
                'location.*' => 'required',
                'length.*' => 'required',
                'nozzle.*' => 'required',
                'hose.*' => 'required',
                'flow.*' => 'required',
                'approach.*' => 'required',
                'remarks.*' => 'required',
            ];

            $messages = [
                'inspection_date.required' => 'Inspection Date is required',
                'location_id.required' => 'Location is required',
                'shift_id.required' => 'Shift is required',
                'next_due.required' => 'Next due date is required',
                'unit_id.required' => 'Unit is required',
                'frequency_id.required' => 'Frequency is required',

                'sr_no.*.required' => 'Serial number is required',
                'department.*.required' => 'Department is required',
                'location.*.required' => 'Location is required',
                'length.*.required' => 'Length is required',
                'nozzle.*.required' => 'Condition of the nozzle is required',
                'hose.*.required' => 'Condition of the hose is required',
                'flow.*.required' => 'Status of flow test is required',
                'approach.*.required' => 'Approach field is required',
                'remarks.*.required' => 'Remarks are required',
            ];



            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }


            $inspection = $this->hydrant->storeApi();
            $inspection_type = HYDRANT_RISER;
            $id = $inspection->id;

            $inspection_details = $this->hydrant_checklist->storeApi($id);
            $inspection_file = $this->files->file_upload_api($inspection_type, $id);

            // $checklist_store = $this->checklist_follow->store($inspection_type, $id);

            $signature_update = $this->signature->CheckedBySignatureApi($id, $inspection_type);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'Fire Hydrant And Riser Inspection';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Hydrant and Riser Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('fire/hydrant-riser-inspection/verification/' . encryptId($id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Hydrant and Riser Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('fire/hydrant-riser-inspection/verification/' . encryptId($id) . '/ehs');
                $details = array(
                    'fire_type' => 'Hydrant and Riser Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => HYDRANT_RISER,
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
            dd($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
