<?php

namespace App\Http\Controllers\Api\Inspection\Safety;

use App\Http\Controllers\Api\BaseController;
use Exception;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Master\Location;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Inspection\Master\Shift;
use Illuminate\Support\Facades\Validator;
use App\Mail\Inspection\Safety\SafetyInspection;
use App\Mail\Inspection\Safety\SafetyWalkInspection;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Safety\SafetyStatusLog;
use App\Models\Inspection\Safety\SignatureUpload;
use App\Models\Inspection\Safety\SafetyWalkObservationDetails;
use App\Models\Inspection\Safety\SafetyWalkObservation as SafetySafetyWalkObservation;

class SafetyWalkObservation extends BaseController
{
    private $safety_walk;
    private $observation_details;
    private $shift;
    private $unit;
    private $location;
    private $signature;
    private $document_reference;
    private $statusLog;
    public function __construct()
    {
        $this->safety_walk = new SafetySafetyWalkObservation();
        $this->observation_details = new SafetyWalkObservationDetails();
        $this->shift = new Shift();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->signature = new SignatureUpload();
        $this->statusLog = new SafetyStatusLog();
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
            $query = $this->safety_walk
                ->select('inspection_safety_walk_observation.*', 'inspection_shift_option.*', 'masters_unit.*', 'users.name', 'inspection_safety_walk_observation.id as inspection_id', 'inspection_safety_walk_observation.created_at as inspection_created_at')
                ->leftJoin('inspection_shift_option', 'inspection_safety_walk_observation.shift_id', '=', 'inspection_shift_option.id')
                ->leftJoin('masters_unit', 'inspection_safety_walk_observation.unit', '=', 'masters_unit.id')
                ->leftJoin('users', 'inspection_safety_walk_observation.safety_walk_taken_by', '=', 'users.id')
                ->leftJoin('inspection_static_docno', 'inspection_safety_walk_observation.document_reference_id', '=', 'inspection_static_docno.id');

            $org_total_counts = $query->count();

            $user = Auth::user();
            $userRole = string_to_array($user->role);
            if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)) {
            } elseif (in_array(ROLE_INSPECTION_CREATOR, $userRole)) {
                $query->where('inspection_safety_walk_observation.created_by', Auth::user()->id);
            }

            if (!empty($search)) {
                $audit_response = [
                    "Waiting For Responsible Person Action" => 1,
                    "Waiting For EHS Officer Approval" => 2,
                    "Closed" => 3,
                    "Rejected by EHS Officer" => 4,
                    "Waiting for the Re-verification of Responsible Person" => 5,

                ];

                $query->where(function ($query) use ($search, $audit_response) {
                    $query->orWhere('masters_unit.unit_name', 'LIKE', "%{$search}%")
                        ->orwhere('users.name', 'LIKE', "%{$search}%")
                        ->orWhere('inspection_shift_option.shift', 'LIKE', "%{$search}%");


                    if (isset($audit_response[$search])) {
                        $query->orWhere('inspection_safety_walk_observation.observation_status', $audit_response[$search]);
                    }


                    if (strtotime($search)) {
                        $query->orWhereDate('inspection_safety_walk_observation.date', '=', $search);
                    }
                });
            }


            $query_array = $query->orderBy('inspection_safety_walk_observation.id', 'DESC')->paginate($request->input('per_page', 10));

            $inspection_list = $query_array->toArray();

            if (empty($inspection_list['data'])) {
                return $this->sendError('No records found.', [], 404);
            }

            $data_array = [];
            foreach ($inspection_list['data'] as $datas) {
                $data = [];
                $data['id'] = $datas['inspection_id'] ?? '';
                $data['date_of_inspection'] = Displaydateformat($datas['date']);
                $data['shift_name'] = $datas['shift'] ?? '';
                $data['unit_name'] = $datas['unit_name'] ?? '';
                $data['month'] = $datas['month'] ?? '';
                $data['observation_status'] = getSafetyWalkStatus($datas['observation_status'] ?? '');
                $data['safety_walk_taken_by'] = getUsername($datas['safety_walk_taken_by'] ?? '');
                $data['created_at'] = Displaydateformat($datas['created_at'] ?? '');
                $data_array[] = $data;
            }

            $safety_walk_details = [
                'per_page' => $inspection_list['per_page'] ?? 0,
                'current_page' => $inspection_list['current_page'] ?? 0,
                'from' => $inspection_list['from'] ?? 0,
                'to' => $inspection_list['to'] ?? 0,
                'total' => $inspection_list['total'] ?? 0,
                'total_page' => $inspection_list['last_page'] ?? 0,
                'list' => $data_array,
            ];

            $success = [
                'safety_walk_details' => $safety_walk_details
            ];

            return $this->sendResponse($success, 'Safety Walk Observation Details');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    // view

    public function view(Request $request)
    {
        try {
            $id = $request->id;
            $safety_walk_observation =  $this->safety_walk->find($id);
            $document_reference = $this->document_reference->find($safety_walk_observation->document_reference_id);
            $safety_walk = [
                'doc_no' => $document_reference->doc_no,
                'issue_date' => displaydateformat($document_reference->issue_date),
                'rev_dt' => ($document_reference->rev_dt),
                'date_of_inspection' => ($safety_walk_observation->date),
                'shift' => getShift($safety_walk_observation->shift_id),
                'month' => ($safety_walk_observation->month),
                'unit' => getUnitname($safety_walk_observation->unit),
                'safety_walk_taken_by' => getUsername($safety_walk_observation->safety_walk_taken_by),
            ];
            // dd($safety_walk_observation);
            // check list
            $responsiblePerson = [];
            $data = string_to_array($safety_walk_observation->responsible_persion);
            foreach ($data as $personId) {

                $responsiblePerson[] = [
                    'id' => $personId,
                    'name' => getUsername($personId),
                ];
            }
            $images = GetSafetyWalkImage($id);
            $safety_walk_checklist = [
                'location' => getLocationname($safety_walk_observation->location_id),
                'exact_location' => $safety_walk_observation->excat_location,
                'observation_date' => Displaydateformat($safety_walk_observation->observation_date),
                'observation' => $safety_walk_observation->observation,
                'recommended_action' => $safety_walk_observation->recomended_action,
                'responsible_person' => $responsiblePerson,
                'observation_status' => $safety_walk_observation->observation_status,
                'file' => admin_url($images),
                'remarks' => $safety_walk_observation->remarks,
            ];
            $success = [
                'safety_walk' =>  $safety_walk,
                'safety_walk_checklist' =>  $safety_walk_checklist,
            ];
            if ($safety_walk_observation->observer_remarks) {
                $success['action_required'] = [
                    'responsible_person' => getUsername($safety_walk_observation->observer_person),
                    'date_of_compilance' => Displaydateformat($safety_walk_observation->date_of_compilance),
                    'remarks' => $safety_walk_observation->observer_remarks,
                ];
            }

            if ($safety_walk_observation->approval_remarks) {
                $success['ehs_approval'] = [
                    'approver_name' => getUsername($safety_walk_observation->approver_id),
                    'approver_date' => Displaydateformat($safety_walk_observation->approver_date),
                    'remarks' => $safety_walk_observation->approval_remarks,
                ];
            }

            $statuslog = $this->statusLog->selectOne($id, SAFETY_WALK_OBSERVATION);
            if (!empty($statuslog)) {
                $approvalLogs = [];
                foreach ($statuslog as $logs) {
                    $approvalLogs[] = [
                        'from_status'   => getSafetyWalkStatus($logs->from_status),
                        'to_status'     => getSafetyWalkStatus($logs->to_status),
                        'approver_name' => getUsername($logs->approved_by),
                        'created_by'    => getUsername($logs->created_by),
                        'created_at'    => Displaydateformat($logs->created_at),
                    ];
                }
                $success['approvalLogs'] = $approvalLogs;
            }

            return $this->sendResponse($success, 'Safety Walk Observation Details');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Something went wrong please try again after some time'], 401);
        }
    }

    // responsible person approval

    public function responsiblePerson(Request $request)
    {
        try {
            $id = $request->id;
            $remarks = $request->responsible_person_remarks;
            $date = $request->responsible_person_date;

            $inspection_details = $this->safety_walk->selectOne($id);

            if ($inspection_details->observation_status == RESPONSIBLE_PERSON_APPROVAL_PENDING) {
                $fromStatus = RESPONSIBLE_PERSON_APPROVAL_PENDING;
                $toStatus  = SAFETY_OFFICER_APPROVAL_PENDING;
            } elseif ($inspection_details->observation_status == SAFETY_WALK_EHS_OFFICER_ON_PROCESS) {
                $fromStatus = SAFETY_WALK_EHS_OFFICER_ON_PROCESS;
                $toStatus  = SAFETY_OFFICER_APPROVAL_PENDING;
            }

            $safetyDetails =  $this->safety_walk->approvalSubmit($id, $date, $remarks);
            $safetyDetails = $this->safety_walk->selectOne($id);

            $title = 'SAFETY WALK OBERVATION';
            $mailsubject = 'Safety Walk Observation';

            // --- EHS Officers ---
            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            if (!empty($ehsOfficer)) {
                $notificationData = [
                    'notification_type' => SAFETY_INSPECTION,
                    'module_type' => 7,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode([
                        'title' => $mailsubject,
                        'message' => "Safety Walk Observation",
                        'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $id,
                        'module' => 1,
                    ]),
                    'web_link' => admin_url('safety/safety-walk-observation/approval/' . encryptId($id)),
                    'assigned_user' => implode(',', $ehsOfficers),
                    'created_by' => Auth::id(),
                ];
                notificationSave($notificationData);

                foreach ($ehsOfficers as $user) {
                    $email_id = getUseremail($user);
                    $details = [
                        'safety_type' => 'Safety Walk Observation',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'url' => admin_url('safety/safety-walk-observation/approval/' . encryptId($id)),
                        'data' => $safetyDetails
                    ];
                    Mail::to($email_id)->queue(new SafetyWalkInspection($details));
                }
            }



            // --- EHS Heads ---

            $ehsHead = GetEHSHead();
            $ehsHeads = $ehsHead->pluck('id')->toArray();
            if (!empty($ehsHead)) {
                $notificationData = [
                    'notification_type' => SAFETY_INSPECTION,
                    'module_type' => 7,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode([
                        'title' => $mailsubject,
                        'message' => "Safety Walk Observation",
                        'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $inspection_details->id,
                        'module' => 1,
                    ]),
                    'web_link' => admin_url('safety/safety-walk-observation/approval/' . encryptId($inspection_details->id)),
                    'assigned_user' => implode(',', $ehsHeads),
                    'created_by' => Auth::id(),
                ];
                notificationSave($notificationData);

                foreach ($ehsHeads as $user) {
                    $email_id = getUseremail($user);
                    $safetydetails = [
                        'safety_type' => 'Safety Walk Observation',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'url' => admin_url('safety/safety-walk-observation/approval/' . encryptId($inspection_details->id)),
                        'data' => $safetyDetails,
                    ];
                    Mail::to($email_id)->queue(new SafetyWalkInspection($safetydetails));
                }
            }

            // --- Admins ---
            $admin = GetAdmin();
            $admins = $admin->pluck('id')->toArray();
            if (!empty($admin)) {
                $notificationData = [
                    'notification_type' => SAFETY_INSPECTION,
                    'module_type' => 7,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode([
                        'title' => $mailsubject,
                        'message' => "Safety Walk Observation",
                        'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $inspection_details->id,
                        'module' => 1,
                    ]),
                    'web_link' => admin_url('safety/safety-walk-observation/approval/' . encryptId($inspection_details->id)),
                    'assigned_user' => implode(',', $admins),
                    'created_by' => Auth::id(),
                ];
                notificationSave($notificationData);

                foreach ($admins as $user) {
                    $email_id = getUseremail($user);
                    $safetydetails = [
                        'safety_type' => 'Safety Walk Observation',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'url' => admin_url('safety/safety-walk-observation/approval/' . encryptId($inspection_details->id)),
                        'data' => $safetyDetails,
                    ];
                    Mail::to($email_id)->queue(new SafetyWalkInspection($safetydetails));
                }
            }


            // --- Status Log ---
            $insert_array = [
                'type' => SAFETY_WALK_OBSERVATION,
                'inspection_id' => $id,
                'from_status' => $fromStatus,
                'to_status' => SAFETY_WALK_EHS_OFFICER_PENDING,
                'remarks' => $remarks,
                'approved_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);
            $success = [
                'safety_walk_observation' => $id,
            ];
            $this->sendResponse($success, "Respnsible Person Action Completed Successfully!");
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorized', ['error' => "Somrthing went Wrong"]);
        }
    }

    public function ehsHead(Request $request)
    {
        try {
            $id = $request->id;
            $status = '';

            if ($request->action == 1) {
                $status = SAFETY_WALK_EHS_OFFICER_APPROVED;
            } elseif ($request->action == 2) {
                $status = SAFETY_WALK_EHS_OFFICER_REJECTED;
            } elseif ($request->action == 3) {
                $status = SAFETY_WALK_EHS_OFFICER_ON_PROCESS;
            }

            $remarks = $request->remarks;
            $date = $request->date;

            $this->safety_walk->ehsapproval($id, $status, $remarks, $date);
            $inspection_details = $this->safety_walk->selectOne($id);
            $title = 'SAFETY WALK OBSERVATION';
            $mailsubject = 'Safety Walk Observation';

            if ($request->action == 1 || $request->action == 2) {
                // --- Notify EHS Officers ---
                $ehsOfficer = GetEHSOfficer();
                $ehsOfficers = GetEHSOfficer()->pluck('id')->toArray();
                if (!empty($ehsOfficer)) {
                    notificationSave([
                        'notification_type' => SAFETY_INSPECTION,
                        'module_type' => 7,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode([
                            'title' => $mailsubject,
                            'message' => "Safety Walk Observation",
                            'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $id,
                            'module' => 1,
                        ]),
                        'web_link' => admin_url('safety/safety-walk-observation/approval/' . encryptId($id)),
                        'assigned_user' => implode(',', $ehsOfficers),
                        'created_by' => Auth::id(),
                    ]);

                    foreach ($ehsOfficers as $user) {
                        $email = getUseremail($user);
                        Mail::to($email)->queue(new SafetyWalkInspection([
                            'safety_type' => 'Safety Walk Observation',
                            'email' => $email,
                            'mail_subject' => $mailsubject,
                            'title' => $title . ($request->approved == 1 ? ' - Has been Approved' : ' - Has been Rejected'),
                            'url' => admin_url('safety/safety-walk-observation/approval/' . encryptId($id)),
                            'data' => $inspection_details,
                        ]));
                    }
                }


                // --- Notify EHS Heads ---
                $ehsHead = GetEHSHead();
                $ehsHeads = GetEHSHead()->pluck('id')->toArray();
                if (!empty($ehsHead)) {
                    notificationSave([
                        'notification_type' => SAFETY_INSPECTION,
                        'module_type' => 7,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode([
                            'title' => $mailsubject,
                            'message' => "Safety Walk Observation",
                            'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $inspection_details->id,
                            'module' => 1,
                        ]),
                        'web_link' => admin_url('safety/safety-walk-observation/approval/' . encryptId($inspection_details->id)),
                        'assigned_user' => implode(',', $ehsHeads),
                        'created_by' => Auth::id(),
                    ]);

                    foreach ($ehsHeads as $user) {
                        $email = getUseremail($user);
                        Mail::to($email)->queue(new SafetyWalkInspection([
                            'safety_type' => 'Safety Walk Observation',
                            'email' => $email,
                            'mail_subject' => $mailsubject,
                            'title' => $title . ($request->approved == 1 ? ' - Has been Approved' : ' - Has been Rejected'),
                            'url' => admin_url('safety/safety-walk-observation/approval/' . encryptId($inspection_details->id)),
                            'data' => $inspection_details,
                        ]));
                    }
                }


                // --- Notify Admins ---
                $admin = GetAdmin();
                $admins = $admin->pluck('id')->toArray();
                if (!empty($admin)) {
                    notificationSave([
                        'notification_type' => SAFETY_INSPECTION,
                        'module_type' => 7,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode([
                            'title' => $mailsubject,
                            'message' => "Safety Walk Observation",
                            'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $inspection_details->id,
                            'module' => 1,
                        ]),
                        'web_link' => admin_url('safety/safety-walk-observation/approval/' . encryptId($inspection_details->id)),
                        'assigned_user' => implode(',', $admins),
                        'created_by' => Auth::id(),
                    ]);

                    foreach ($admins as $user) {
                        $email = getUseremail($user);
                        Mail::to($email)->queue(new SafetyWalkInspection([
                            'safety_type' => 'Safety Walk Observation',
                            'email' => $email,
                            'mail_subject' => $mailsubject,
                            'title' => $title . ($request->approved == 1 ? ' - Has been Approved' : ' - Has been Rejected'),
                            'url' => admin_url('safety/safety-walk-observation/approval/' . encryptId($inspection_details->id)),
                            'data' => $inspection_details,
                        ]));
                    }
                }


                // --- Notify Responsible Persons ---
                $responsiblePersons = string_to_array(is_array($inspection_details->responsible_persion) ? implode(',', $inspection_details->responsible_persion) : ($inspection_details->responsible_persion ?? ''));
                if (!empty($responsiblePersons)) {
                    foreach ($responsiblePersons as $userId) {
                        $email = getUseremail($userId);
                        if (!empty($email)) {
                            Mail::to($email)->queue(new SafetyWalkInspection([
                                'safety_type' => 'Safety Walk Observation',
                                'email' => $email,
                                'mail_subject' => $mailsubject,
                                'title' => $title . ($request->approved == 1 ? ' - Has been Approved' : ' - Has been Rejected'),
                                'url' => admin_url('safety/safety-walk-observation/approval/' . encryptId($inspection_details->id)),
                                'data' => $inspection_details,
                            ]));

                            notificationSave([
                                'notification_type' => SAFETY_INSPECTION,
                                'module_type' => 7,
                                'notification_message' => $mailsubject,
                                'mobile_notification' => json_encode([
                                    'title' => $mailsubject,
                                    'message' => "Safety Walk Observation - Observation Has been Created",
                                    'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                                    'id' => '',
                                    'module' => 1,
                                ]),
                                'web_link' => admin_url('safety/safety-walk-observation/list'),
                                'assigned_user' => $userId,
                                'created_by' => Auth::id(),
                            ]);
                        }
                    }
                }
            } elseif ($request->action == 3) {
                // --- Re-verification Request ---
                $responsiblePersons = string_to_array(is_array($inspection_details->responsible_persion) ? implode(',', $inspection_details->responsible_persion) : ($inspection_details->responsible_persion ?? ''));
                if (!empty($responsiblePersons)) {
                    foreach ($responsiblePersons as $userId) {
                        $email = getUseremail($userId);
                        if (!empty($email)) {
                            Mail::to($email)->queue(new SafetyWalkInspection([
                                'safety_type' => 'Safety Walk Observation',
                                'email' => $email,
                                'mail_subject' => $mailsubject,
                                'title' => $title . ' - Has been Sent For Re-Verification',
                                'url' => admin_url('safety/safety-walk-observation/approval/' . encryptId($inspection_details->id)),
                                'data' => $inspection_details,
                            ]));

                            notificationSave([
                                'notification_type' => SAFETY_INSPECTION,
                                'module_type' => 7,
                                'notification_message' => $mailsubject,
                                'mobile_notification' => json_encode([
                                    'title' => $mailsubject,
                                    'message' => "Safety Walk Observation - Observation Has been Created",
                                    'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                                    'id' => '',
                                    'module' => 1,
                                ]),
                                'web_link' => admin_url('safety/safety-walk-observation/list'),
                                'assigned_user' => $userId,
                                'created_by' => Auth::id(),
                            ]);
                        }
                    }
                }
            }

            // --- Status Log ---
            $this->statusLog->create([
                'type' => SAFETY_WALK_OBSERVATION,
                'inspection_id' => $id,
                'from_status' => SAFETY_WALK_EHS_OFFICER_PENDING,
                'to_status' => $status,
                'remarks' => $remarks,
                'approved_by' => Auth::id(),
            ]);
            $success = [
                'safety_walk_observation' => $id,
            ];

            $this->sendResponse($success, "EHS Head Approval Completed Successfully!");
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorized', ['error' => 'something went wrong']);
        }
    }
}
