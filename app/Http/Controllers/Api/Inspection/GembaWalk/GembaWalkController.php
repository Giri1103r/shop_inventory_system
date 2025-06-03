<?php

namespace App\Http\Controllers\Api\Inspection\GembaWalk;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Master\Employee;
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
                $inspection_details = $this->gembaWalk->getChecklistDetails($inspections->id);
                $type = GEMBA_WALK;
                $image_type = 4;
                $prepared_by_signature = GetSignature($inspections->created_by, $inspections->id, $type);
                $gembaWalk_ehs_capa_details = $this->gembaWalkInspectionEhsAprroval->getEHSCapaReview($id);
                $gembaWalk_ehs_floor_manager_details = $this->gembaWalkInspectionEhsAprroval->getEHSFloormanagerReview($id);
                $gembaWalk_ehs_verificatioin_details = $this->gembaWalkInspectionEhsAprroval->getEHSOfficerReview($id);
                $closing_image = getGembaWalkClosingImage($id, $image_type);

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
                ];


                $inspection_checklist_details = [];

                foreach ($inspection_details as $data) {
                    $hazard_data = [];
                    $hazard_ids = explode(',', $data->hazard ?? '');
                    foreach ($hazard_ids as $id) {
                        $id = trim($id);
                        $hazard_data[] = [
                            'id' => $id,
                            'name' => getGembaWalkHazardName($id),
                        ];
                    }

                    $responsible_person_data = [];
                    $respon_per_ids = explode(',', $data->responsibility_id ?? '');
                    foreach ($respon_per_ids as $id) {
                        $id = trim($id);
                        $responsible_person_data[] = [
                            'id' => $id,
                            'name' => getUsername($id)
                        ];
                    }

                    $inspection_data = [
                        'id' => $data->gemba_walk_id,
                        'location_name' => getLocationname($data->location_id),
                        'unit_name' => getUnitname($data->unit_id),
                        'department_name' => getDepartment($data->department_id),
                        'exact_location' => $data->exact_location,
                        'date_of_observation' => Displaydateformat($data->date_of_observation),
                        'observation_time' => $data->time,
                        'risk_category' => getRiskCategory($data->risk_category),
                        'description' => $data->description,
                        'observation_type' => getObservationType($data->observation_type_id),
                        'hazard' => $hazard_data,
                        'recommended_capa_action' => $data->capa_needed == 1 ? 'Yes' : 'No',
                        'recommended_capa' => $data->capa,
                        'responsible_person' => $responsible_person_data,
                        'gemba_walk_status' => getGembaWalkStatus($data->gemba_walk_checklist_status),
                        'remarks' => $data->remark,
                        'observer_person' => getUsername($data->created_by),
                        'evidence' => admin_url($data->file_path),
                        'closing_evidence' => admin_url($closing_image)
                    ];

                    $inspection_checklist_details[] = $inspection_data;
                }
                // action taken
                if (!empty($gembaWalk_ehs_floor_manager_details)) {
                    $inspection_checklist_details += [
                        'responsible_person_action_taken' => $gembaWalk_ehs_floor_manager_details->name,
                        'responsible_person_created_date' => Displaydateformat($gembaWalk_ehs_floor_manager_details->created_at),
                        'responsible_person_remarks' => $gembaWalk_ehs_floor_manager_details->remarks ? $gembaWalk_ehs_floor_manager_details->remarks : '-',
                        'capa_action_date' => Displaydateformat($gembaWalk_ehs_floor_manager_details->capa_action_date),
                        'observer_capa_image' => admin_url($gembaWalk_ehs_floor_manager_details->file_path)
                    ];
                }

                // ehs approve
                if (!empty($gembaWalk_ehs_verificatioin_details)) {
                    $inspection_checklist_details += [
                        'ehs_approver_name' => $gembaWalk_ehs_verificatioin_details->name,
                        'ehs_approver_remarks' => $gembaWalk_ehs_verificatioin_details->remarks ? $gembaWalk_ehs_verificatioin_details->remarks : '-',
                        'ehs_approver_created_date' => Displaydateformat($gembaWalk_ehs_verificatioin_details->created_at),


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
                'is_passed' => 'required',
                'gemba_walk.*.location_id' => 'required',
                'gemba_walk.*.unit_id' => 'required',
                'gemba_walk.*.date_of_observation' => 'required',
                'gemba_walk.*.observation_type' => 'required',
                'gemba_walk.*.checklist_description' => 'required',
                'gemba_walk.*.hazard' => 'required',
                'gemba_walk.*.checklist_capa' => 'required',
                // 'gemba_walk.*.date_of_compliance' => 'nullable',
                // 'gemba_walk.*.responsibility_id' => 'nullable',
                'gemba_walk.*.current_status' => 'required',
                'gemba_walk.*.checklist_remark' => 'required',
            ];

            $messages = [
                'document_no.required' => 'Document number is required.',
                'document_upload_date.required' => 'Please provide the document upload date.',
                'document_revision_date.required' => 'Please provide the document revision date.',
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

            $gembaWalk_id = $gembawalk_data->id;


            $gembaWalk_details = $this->gembaWalk->getUserId($gembaWalk_id);
            $ResponsibleId = $this->gembaWalkCheckList->selectone($gembaWalk_id);

            $capa_needed = $request->is_passed;
            $status_closed = $request->gemba_walk[0]['current_status'];

            if ($capa_needed == 2  ||  $status_closed == 2) {
                $gembaWalk_status = GEMBA_WALK_INSPECTION_CLOSED;
                $gembaWalk_status_details = $this->gembaWalk->updateStatus($gembaWalk_id, $gembaWalk_status);
                $to_status = GEMBA_WALK_INSPECTION_CLOSED;

                $gembaWalk_ehs_verificatioin_details = $this->gembaWalkInspectionEhsAprroval->usercapaSubmit($gembaWalk_id);
                $mailsubject = 'Gemba Walk has been Closed';
                $user_roles = [ROLE_EHS_OFFICER, ROLE_UNIT_HEAD, ROLE_EHS_HEAD];

                $users = User::where(function ($query) use ($user_roles) {
                    foreach ($user_roles as $role) {
                        $query->orWhereRaw("FIND_IN_SET(?, role)", [$role]);
                    }
                })->where('status', 1)->get();

                $userIds = $users->pluck('id')->toArray();
            } else {
                $gembaWalk_status = GEMBA_WALK_INSPECTION_WAITING_FOR_FLOOR_MANAGER_VERIFICATION;
                $gembaWalk_status_details = $this->gembaWalk->updateStatus($gembaWalk_id, $gembaWalk_status);
                $to_status = GEMBA_WALK_INSPECTION_WAITING_FOR_FLOOR_MANAGER_VERIFICATION;

                $mailsubject = 'Gemba Walk has been Created';
                $user_roles = [ROLE_EHS_OFFICER, ROLE_UNIT_HEAD, ROLE_EHS_HEAD];

                $responsible_person_id = $ResponsibleId->responsibility_id;
                $observer_person_ids = explode(',', $responsible_person_id);

                $users = collect(); // Laravel Collection
                $userIds = [];       // For notification assignment

                foreach ($observer_person_ids as $employeeLoginId) {
                    $employee = Employee::where('login_id', $employeeLoginId)->where('status', 1)->first();

                    if ($employee) {
                        // Observer user
                        $observerUser = User::where('id', $employee->login_id)->first();
                        if ($observerUser) {
                            $users->push($observerUser);
                            $userIds[] = $observerUser->id;
                        }

                        // Reporting manager user
                        if (!empty($employee->reporting_manager)) {
                            $managerUser = User::where('employee_id', $employee->reporting_manager)->first();
                            // dd(   $managerUser);
                            if ($managerUser) {
                                $users->push($managerUser);
                                $userIds[] = $managerUser->id;
                            }
                        }
                    }
                }

                $userIds = array_unique($userIds);
            }

            if ($users->count() > 0) {

                foreach ($users as $user) {
                    $email_id = $user->email;
                    if (!empty($email_id)) {
                        $gembaWalk_details = $this->gembaWalk->selectmail($gembaWalk_id);
                        $gembaWalk_checklist = $this->gembaWalkCheckList->selectmail($gembaWalk_id);
                        $gembaWalk = $gembaWalk_details->toArray();
                        $gembaWalkChecklist = $gembaWalk_checklist->toArray();
                        $gembaWalk['name'] = $user->name;
                        $gembaWalk['email_id'] = $email_id;
                        $gembaWalk['mail_subject'] = $mailsubject;

                        // Send email
                        Mail::to($gembaWalk['email_id'])->queue(new GembaWalkMail($gembaWalk, $gembaWalkChecklist));
                    }
                }
            }

            // Ensure $gembaWalk_details is available
            $gembaWalk_details = $gembaWalk_details ?? $this->gembaWalk->selectmail($gembaWalk_id);

            // Send notification
            $notificationData = [
                'notification_type' => GEMBA_WALK_NOTIIFCATION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode([
                    'title' => $mailsubject,
                    'message' => 'CAPA Action ' . $gembaWalk_details->gemba_walk_auto_id . ' Submitted by ' . getUsername($gembaWalk_details->created_by),
                    'icon' => admin_url('public/assets/icons/permit_to_work.png'),
                    'id' => $gembaWalk_details->id,
                    'module' => 1,
                ]),
                'web_link' => admin_url('inspection/gemba-walk/list'),
                'assigned_user' => implode(',', $userIds),
                'created_by' => Auth::id(),
            ];

            notificationSave($notificationData);



            $insert_array = array(
                'gemba_walk_id' => $gembaWalk_id,
                'from_status' => GEMBA_WALK_INSPECTION_START,
                'to_status' => $to_status,
                'is_reject' => $ResponsibleId->capa,
                'remarks' => ($ResponsibleId->remarks) ? $request->capa_remarks : '-',
                'approved_by' => Auth::id(),
            );

            $this->statusLog->create($insert_array);


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
