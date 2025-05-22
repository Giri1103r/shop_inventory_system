<?php

namespace App\Http\Controllers\Api\Inspection\GembaWalk;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\GembaWalk\GembaWalkMail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use App\Models\Inspection\GembaWalk\GembaWalk;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\GembaWalk\GembaWalkChecklist;
use App\Models\Inspection\GembaWalk\GembaWalkStatusLog;
use App\Models\Inspection\GembaWalk\GembaWalkChecklistFile;
use App\Models\Inspection\GembaWalk\GembaWalkInspectionEhsApproval;

class GembaWalkController extends BaseController
{
    private $gembaWalk;
    private $ddocument_reference;
    private $gembaWalkCheckList;
    private $gembaWalkInspectionEhsAprroval;
    private $statusLog;
    private $gembaWalkChecklistFile;




    public function __construct()
    {
        $this->gembaWalk = new GembaWalk();
        $this->ddocument_reference = new InspectionStaticDocno();
        $this->gembaWalkCheckList = new GembaWalkChecklist();
        $this->gembaWalkInspectionEhsAprroval = new GembaWalkInspectionEhsApproval();
        $this->statusLog = new GembaWalkStatusLog();
        $this->gembaWalkChecklistFile = new GembaWalkChecklistFile();
    }

    public function list(Request $request)
    {
        if (Auth::check()) {
            try {
                $data = $this->gembaWalk->listApi();
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
                    404
                );
            }
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 404);
        }
    }

    public function view(Request $request)
    {
        try {

            if (Auth::check()) {
                $id = $request->id;
                $inspections = $this->gembaWalk->getInspectionDetails($id);
                $insspection_details = $this->gembaWalkCheckList->getChecklistDetails($inspections->id);
                $type = GEMBA_WALK;
                $prepared_by_signature = GetSignature($inspections->created_by, $inspections->id, $type);
                $gembaWalk_ehs_capa_details = $this->gembaWalkInspectionEhsAprroval->getEHSCapaReview($id);
                $gembaWalk_ehs_floor_manager_details = $this->gembaWalkInspectionEhsAprroval->getEHSFloormanagerReview($id);
                $gembaWalk_ehs_verificatioin_details = $this->gembaWalkInspectionEhsAprroval->getEHSOfficerReview($id);

                // status log
                $statuslog = $this->statusLog->getDetails($inspections->id);
                if (count($statuslog) > 0) {
                    foreach ($statuslog as $key => $status) {
                        $statuslog[$key]->from_status = getGembaWalkLogStatus($status->from_status);
                        $statuslog[$key]->to_status = getGembaWalkLogStatus($status->to_status);
                        $statuslog[$key]->remarks = $status->remarks;
                        $statuslog[$key]->approved_by = getUsername($status->approved_by);
                        $statuslog[$key]->created_at = Displaydateformat($status->created_at);
                    }
                } else {
                    $statuslog = [];
                }

                $inspection = [
                    'gemba_walk_auto_id' => $inspections->gemba_walk_auto_id,
                    'document_no' => $inspections->doc_no,
                    'issue_date' => Displaydateformat($inspections->issue_date),
                    'revision_date' => $inspections->rev_dt,
                    'date' => Displaydateformat($inspections->date),
                    'shift_name' => getShift($inspections->shift_id),
                    'responsibile_person' => getUsername($inspections->responsible_person_id),
                    'prepared_by_signature' => admin_url($prepared_by_signature)

                ];

                $inspection_checklist_details = [];

                foreach ($insspection_details as $index => $data) {
                    $inspection_data = [
                        'id' => $data->gemba_walk_id,
                        'location_name' => getLocationname($data->location_id),
                        'unit_name' => getUnitname($data->unit_id),
                        'observation_type' => getObservationType($data->observation_type_id),
                        'description' => $data->description,
                        'hazard' => $data->hazard,
                        'recommended_capa' => $data->capa,
                        'gemba_walk_status' => $data->gemba_walk_checklist_status,
                        'remarks' => $data->remark,
                        'device_image' => admin_url($data->file_path)
                    ];
                    $inspection_checklist_details[] = $inspection_data;
                }

                // CAPA ACtion
                if (!empty($gembaWalk_ehs_capa_details)) {
                    $inspection_checklist_details += [
                        'ehs_officer_name' => $gembaWalk_ehs_capa_details->name,
                        'ehs_created_date' => Displaydateformat($gembaWalk_ehs_capa_details->created_at),
                        'ehs_remarks' => $gembaWalk_ehs_capa_details->remarks ? $gembaWalk_ehs_capa_details->remarks : '-',
                        'capa_needed' => $gembaWalk_ehs_capa_details->capa == 1 ? 'Yes' : 'No'
                    ];
                }

                // FLOOR MANAGER ACTION
                if (!empty($gembaWalk_ehs_floor_manager_details)) {
                    $floor_manager_verified_signature = GetSignature($inspections->verified_by, $inspections->id, $type);
                    $inspection_checklist_details += [
                        'floor_manager_name' => $gembaWalk_ehs_floor_manager_details->name,
                        'floor_manager_remarks' => Displaydateformat($gembaWalk_ehs_floor_manager_details->created_at),
                        'floor_manager_created_date' => $gembaWalk_ehs_floor_manager_details->remarks ? $gembaWalk_ehs_floor_manager_details->remarks : '-',
                        'floor_manager_verified_signature' => admin_url($floor_manager_verified_signature)

                    ];
                }

                // ehs approve
                if (!empty($gembaWalk_ehs_verificatioin_details)) {
                    $approved_by_signature = GetSignature($inspections->created_by, $inspections->id, $type);

                    $inspection_checklist_details += [
                        'ehs_approver_name' => $gembaWalk_ehs_verificatioin_details->name,
                        'ehs_approver_remarks' => $gembaWalk_ehs_verificatioin_details->remarks ? $gembaWalk_ehs_verificatioin_details->remarks : '-',
                        'ehs_approver_created_date' => Displaydateformat($gembaWalk_ehs_verificatioin_details->created_at),
                        'ehs_approved_signature' => admin_url($approved_by_signature)

                    ];
                }

                $success = [
                    'id' => $inspections->id,
                    'inspection_main' => $inspection,
                    'inspection_checklist_details' => $inspection_checklist_details,
                    'statuslog' => $statuslog
                ];

                return $this->sendResponse($success, 'Gemba Walk Inspection Details');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 404);
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError(
                'Unauthorised.',
                ['error' => 'Please try again after sometimes'],
                404
            );
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'document_no' => 'required',
                'document_upload_date' => 'required',
                'document_revision_date' => 'required',
                'observation_needed' => 'required',
                'is_passed' => 'required',
                'gemba_walk.*.location_id' => 'required',
                'gemba_walk.*.unit_id' => 'required',
                'gemba_walk.*.date_of_observation' => 'required',
                'gemba_walk.*.observation_type' => 'required|integer',
                'gemba_walk.*.checklist_description' => 'nullable|string',
                'gemba_walk.*.hazard' => 'nullable|string',
                'gemba_walk.*.checklist_capa' => 'nullable|string',
                // 'gemba_walk.*.date_of_compliance' => 'nullable',
                // 'gemba_walk.*.responsibility_id' => 'nullable',
                'gemba_walk.*.current_status' => 'nullable|string',
                'gemba_walk.*.checklist_remark' => 'nullable|string',
            ];

            $messages = [
                'document_no.required' => 'Document number is required.',
                'document_upload_date.required' => 'Please provide the document upload date.',
                'document_revision_date.required' => 'Please provide the document revision date.',
                'observation_needed.required' => 'Please provide the observation.',
                'capa_needed.required' => 'Please provide the CAPA.',
                'gemba_walk.*.location_id.required' => 'Location ID is required.',
                'gemba_walk.*.unit_id.required' => 'Unit ID is required.',
                'gemba_walk.*.date_of_observation.required' => 'Date of observation is required.',
                'gemba_walk.*.date_of_observation.date_format' => 'Date of observation must be in the format dd-mm-yyyy.',
                'gemba_walk.*.observation_type.required' => 'Observation type is required.',
                'gemba_walk.*.observation_type.integer' => 'Observation type must be a number.',
                'gemba_walk.*.checklist_description.string' => 'Checklist description must be a valid text.',
                'gemba_walk.*.hazard.string' => 'Hazard must be a valid text.',
                'gemba_walk.*.checklist_capa.string' => 'Checklist CAPA must be a valid text.',
                // 'gemba_walk.*.date_of_compliance.date_format' => 'Date of compliance must be in the format dd-mm-yyyy.',
                'gemba_walk.*.checklist_remark.string' => 'Checklist remark must be a valid text.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }

            $gembawalk_data = $this->gembaWalk->storeApi();
            $gembaWalk_checklist_data = $this->gembaWalkCheckList->storeApi($gembawalk_data->id);
            $gembaWalk_singnature = $this->gembaWalkChecklistFile->storeSignatureApi($gembawalk_data->id);

            $gembaWalk_id = $gembawalk_data->id;

            if ($gembawalk_data->capa_needed == "2") {
                $resposible_person = $request->responsible_person_id;
                $capa_type = GEMBA_WALK_INSPECTION_PASS_L1;

                $gembaWalk_ehs = $this->gembaWalkInspectionEhsAprroval->capaSubmit($gembaWalk_id, $capa_type);

                $gembaWalk_status = GEMBA_WALK_INSPECTION_WAITING_FOR_FLOOR_MANAGER_VERIFICATION;


                $gembaWalk_status = $this->gembaWalk->updateStatus($gembaWalk_id, $gembaWalk_status);
                $to_status = GEMBA_WALK_INSPECTION_WAITING_FOR_FLOOR_MANAGER_VERIFICATION;
                //mail
                $mailsubject = 'Gemba Walk CAPA Action  Report has been Submitted';
                $user_roles = [ROLE_EHS_OFFICER, ROLE_UNIT_HEAD, ROLE_EHS_HEAD];
                $userids = [$resposible_person];
                $users = collect();

                foreach ($user_roles as $user_role) {
                    $roleUsers = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')
                        ->get();

                    $userids = array_merge($userids, $roleUsers->pluck('id')->toArray());
                    $users = $users->merge($roleUsers);
                }

                $userids = array_unique($userids);

                $gembaWalk_details = $this->gembaWalk->selectmail($gembaWalk_id);
                $gembaWalk_status = $this->gembaWalkInspectionEhsAprroval->getStatus($gembaWalk_id);

                if ($users->count() > 0) {
                    foreach ($users as $user) {
                        $email_id = $user->email;

                        if (!empty($email_id)) {
                            $gembaWalk_details = $this->gembaWalk->selectmail($gembaWalk_id);
                            $gembaWalk = $gembaWalk_details->toArray();

                            $gembaWalk['name'] = $user->name;
                            $gembaWalk['email_id'] = $email_id;
                            $gembaWalk['mail_subject'] = $mailsubject;

                            // Send email
                            Mail::to($gembaWalk['email_id'])->queue(new GembaWalkMail($gembaWalk));
                        }
                    }
                }

                $notificationData = array(
                    'notification_type' => GEMBA_WALK_NOTIIFCATION,
                    'module_type' => 3,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'CAPA Action  ' . $gembaWalk_details->gemba_walk_auto_id . ' Submmited by ' . getUsername($gembaWalk_details->created_by),
                        'icon' => admin_url('public/assets/icons/permit_to_work.png'),
                        'id' => $gembaWalk_details->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('inspection/gemba-walk/list'),
                    'assigned_user' => implode(',', $userids),
                    'created_by' => Auth::id(),
                );

                notificationSave($notificationData);

                $insert_array = array(
                    'gemba_walk_id' => $gembaWalk_details->id,
                    'from_status' => GEMBA_WALK_INSPECTION_START,
                    'to_status' => $to_status,
                    'is_reject' => $gembaWalk_status->capa,
                    'remarks' => $gembaWalk_status->remarks,
                    'approved_by' => Auth::id(),
                );

                $this->statusLog->create($insert_array);
            } else {
                $resposible_person = $request->responsible_person_id;
                $gembaWalk_status = GEMBA_WALK_INSPECTION_CLOSED;
                $capa_type = GEMBA_WALK_INSPECTION_PASS;
                // $gembaWalk_id = decryptId($request->id);
                $gembaWalk_ehs = $this->gembaWalkInspectionEhsAprroval->capaSubmit($gembaWalk_id, $capa_type);

                $gembaWalk_singnature = $this->gembaWalkChecklistFile->storeVerifiedSignatureApi($gembaWalk_id);
                $gembaWalk_status = $this->gembaWalk->updateStatus($gembaWalk_id, $gembaWalk_status);

                //mail
                $mailsubject = 'Gemba Walk has been Approved';
                $user_roles = [ROLE_EHS_OFFICER, ROLE_UNIT_HEAD, ROLE_EHS_HEAD];
                $userids = [$resposible_person];
                $users = collect();

                foreach ($user_roles as $user_role) {
                    $roleUsers = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')
                        ->get();

                    $userids = array_merge($userids, $roleUsers->pluck('id')->toArray());
                    $users = $users->merge($roleUsers);
                }

                $userids = array_unique($userids);
                $gembaWalk_details = $this->gembaWalk->selectmail($gembaWalk_id);
                $gembaWalk_status = $this->gembaWalkInspectionEhsAprroval->getApproveStatus($gembaWalk_id);
                $to_status = GEMBA_WALK_INSPECTION_CLOSED;

                if ($users->count() > 0) {
                    foreach ($users as $user) {
                        $email_id = $user->email;
                        if (!empty($email_id)) {
                            $gembaWalk_details = $this->gembaWalk->selectmail($gembaWalk_id);
                            $gembaWalk = $gembaWalk_details->toArray();
                            $gembaWalk['name'] = $user->name;
                            $gembaWalk['email_id'] = $email_id;
                            $gembaWalk['mail_subject'] = $mailsubject;
                            Mail::to($gembaWalk['email_id'])->queue(new GembaWalkMail($gembaWalk));
                        }
                    }
                }

                $notificationData = array(
                    'notification_type' => GEMBA_WALK_NOTIIFCATION,
                    'module_type' => 3,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Gemba Walk ' . $gembaWalk_details->gemba_walk_auto_id . ' has been Approved by ' . getUsername($gembaWalk_details->created_by),
                        'icon' => admin_url('public/assets/icons/permit_to_work.png'),
                        'id' => $gembaWalk_details->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('inspection/gemba-walk/list'),
                    'assigned_user' => implode(',', $userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $insert_array = array(
                    'gemba_walk_id' => $gembaWalk_details->id,
                    'from_status' => GEMBA_WALK_INSPECTION_START,
                    'to_status' => $to_status,
                    'is_reject' => $gembaWalk_status->capa,
                    'remarks' =>  $gembaWalk_status->remarks,
                    'approved_by' => Auth::id(),
                );

                $this->statusLog->create($insert_array);
            }


            $success = [
                "success" => $gembawalk_data,
            ];
            return $this->sendResponse($success, 'Inspection Created');
        } catch (Exception $ex) {
            report($ex);
              return $this->sendError(
                'Unauthorised.',
                ['error' => 'Please try again after sometimes'],
                404
            );
        }
    }
}
