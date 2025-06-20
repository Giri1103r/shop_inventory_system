<?php

namespace App\Http\Controllers\Api\Permit;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Mail\SafetyPermitEmail;
use App\Models\Master\Employee;
use App\Models\Permit\SafetyApproveReject;
use App\Models\Permit\SafetyPermit;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Permit\SafetyPermitExtension;
use App\Models\Permit\Statuslog;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SafetyPermitController extends BaseController
{
    private $safetypermit;
    private $pperequest;
    private $approvereject;
    private $statuslog;
    private $safetyPermitExtension;


    public function __construct()
    {
        $this->safetypermit = new SafetyPermit();
        $this->approvereject = new SafetyApproveReject();
        $this->statuslog = new Statuslog();
        $this->safetyPermitExtension = new SafetyPermitExtension();
    }
    public function list(Request $request)
    {
        if (Auth::user()) {
            if ($request->has('search')) {
                if ($request->search != '' && $request->search != null) {
                    $search = $request->search;
                }
            }
            $empid = Auth::id();

            $user = Auth::user();
            $empId = $user->employee_id;
            $userRole = $user->role;
            $unit_id = $user->unit_id;
            $company_id = $user->company_id;
            $empid = $user->id;
            $userRole = string_to_array($userRole);
            if (isAdmin()) {
                $safety_permit_array = SafetyPermit::select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status');
            } elseif (in_array(ROLE_EHS_OFFICER, $userRole)) {
                $safety_permit_array = SafetyPermit::select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status')->where('ptw_safety.company_id', $company_id);
            } elseif (in_array(ROLE_PLANT_HEAD, $userRole)) {
                $safety_permit_array = SafetyPermit::select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status')->where('ptw_safety.unit_id', $unit_id);
            } elseif (in_array(ROLE_EHS_HEAD, $userRole)) {
                $safety_permit_array = SafetyPermit::select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status');
            } else {
                $safety_permit_array = SafetyPermit::select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status')->where('ptw_safety.created_by', $empid);
            }
            if (!empty($search)) {
                $searchDate = DBdateformat($search);

                $safety_permit_array->where(function ($query) use ($searchDate, $search) {
                    $query->whereDate('ptw_safety.date', $searchDate)
                        ->orWhere('ptw_safety.permit_id', $search);
                });
            }


            $safety_permit_array = $safety_permit_array->orderBy('ptw_safety.id', 'DESC')->paginate($request->input('per_page', 10));

            $safety_permit_list = $safety_permit_array->toArray();

            if (empty($safety_permit_list['data'])) {
                return $this->sendError('No records found.', [], 404);
            }

            $data_array = [];
            foreach ($safety_permit_list['data'] as $listdata) {
                $data = [];
                $data['id'] = $listdata['id'] ?? '';
                $data['permit_id'] = $listdata['permit_id'] ?? '';
                $data['unit_id'] =getUnitname( $listdata['unit_id'] ?? '');
                $data['date'] = $listdata['date'] ?? '';
                $data['exact_location_job'] = $listdata['exact_location_job'] ?? '';
                $data['status_name'] = $listdata['status_name'] ?? '';
                $data['approved_by'] = getUsername($listdata['approved_by'] ?? '');
                $data['verified_by'] = getUsername($listdata['verified_by'] ?? '');
                $data['created_by'] = getUsername($listdata['created_by'] ?? '');
                $data['created_at'] = Displaydateformat($listdata['created_at'] ?? '');

                $data_array[] = $data;
            }

            $safety_permit_details = [
                'per_page' => $safety_permit_list['per_page'] ?? 0,
                'current_page' => $safety_permit_list['current_page'] ?? 0,
                'from' => $safety_permit_list['from'] ?? 0,
                'to' => $safety_permit_list['to'] ?? 0,
                'total' => $safety_permit_list['total'] ?? 0,
                'total_page' => $safety_permit_list['last_page'] ?? 0,
                'list' => $data_array,
            ];


            $success = [
                'safety_permit_details' => $safety_permit_details
            ];


            return $this->sendResponse($success, 'Safety Permit Details');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    // view

    public function view(Request $request)
    {
        try {
            if (Auth::user()) {
                $id = $request->id;

                $safetypermit = $this->safetypermit->selectOne($id);
                $workmaninvolved = $this->safetypermit->workmaninvolved($id);
                $stateIsolationLoto = json_decode($safetypermit->state_isolation_loto);

                $confined_space_entry = json_decode($safetypermit->confined_space_entry);

                $status_log = $this->statuslog->selectOne($id);

                $getEhSverification =   $this->approvereject->getEhSverification($id);
                $getEhsapproval =   $this->approvereject->getEhsapproval($id);
                $getplantheadapproval =   $this->approvereject->getplantheadapproval($id);
                $getsafetyPermitExtension =   $this->safetyPermitExtension->permitextensionelectOne($id);
                $getpermitextensionapproval =   $this->approvereject->getpermitextensionapproval($id);


                $sub_permit_names = is_array($safetypermit->sub_permit_names) ?
                    $safetypermit->sub_permit_names :
                    json_decode($safetypermit->sub_permit_names, true);

                $sub_permit_images = is_array($safetypermit->sub_permit_images) ?
                    $safetypermit->sub_permit_images :
                    json_decode($safetypermit->sub_permit_images, true);




                $sub_permits = [];
                if (!empty($sub_permit_names) && !empty($sub_permit_images)) {
                    foreach ($sub_permit_names as $index => $name) {
                        $sub_permits[] = [
                            'name' => trim($name),
                            'image' => $sub_permit_images[$index] ?? null
                        ];
                    }
                }

                $stateIsolationLoto = json_decode($safetypermit->state_isolation_loto, true) ?? [];
                $state_of_isolation = [];
                $other_if_any = [];
                // dd($stateIsolationLoto);
                $knownItems = ['Air', 'Gas', 'Electrical', 'Water/Liquid'];

                foreach ($knownItems as $item) {
                    $key = strtolower(str_replace('/', '_', $item));

                    // Determine the correct image path
                    switch ($item) {
                        case 'Air':
                            $imagePath = 'public/assets/images/safetypermit/person.png';
                            break;
                        case 'Gas':
                            $imagePath = 'public/assets/images/safetypermit/natural-gas.png';
                            break;
                        case 'Electrical':
                            $imagePath = 'public/assets/images/safetypermit/electrician.png';
                            break;
                        case 'Water/Liquid':
                            $imagePath = 'public/assets/images/safetypermit/leak.png';
                            break;
                        default:
                            $imagePath = 'public/assets/images/safetypermit/default.png'; // fallback
                    }

                    $state_of_isolation[$key] = [
                        'image' => $imagePath,
                        'name' => $item,
                        'checked' => in_array($item, $stateIsolationLoto) ? 'Yes' : 'No'
                    ];
                }
                foreach ($stateIsolationLoto as $item) {
                    if (!in_array($item, $knownItems)) {
                        $other_if_any[] = [
                            'other_if_any' => $item,

                        ];
                    }
                }
                $isolationpanel = [
                    'image' => ('public/assets/images/safetypermit/fire.png'),
                    'name' => "Isolation fire panel",
                    'checked' => $safetypermit->isolationpanel_checkbox ? 'Yes' : 'No'
                ];

                $isolationpaneldescription = [
                    'name' => "Isolation fire panel Description",
                    'isolationpanel_description' => $safetypermit->isolationpanel_description
                ];



                $protective_equip = [];

                foreach ($safetypermit->mapped_protective_equip as $job => $details) {
                    foreach ($details['checkpoint_names'] as $checkpoint_name) {
                        $protective_equip[] = [
                            'name' => $checkpoint_name,
                            'checked' => 'Yes'
                        ];
                    }
                }
                // equipment involve
                $equipment_involve = [];

                foreach ($safetypermit->mapped_equiment_involved as $job => $details) {
                    foreach ($details['checkpoint_names'] as $checkpoint_name) {
                        $equipment_involve[] = [
                            'name' => $checkpoint_name,
                            'checked' => 'Yes'
                        ];
                    }
                }

                // precaution taken

                $precaution_taken = [];

                foreach ($safetypermit->mapped_precaution_taken as $job => $details) {
                    foreach ($details['checkpoint_names'] as $checkpoint_name) {
                        $precaution_taken[] = [
                            'name' => $checkpoint_name,
                            'checked' => 'Yes'
                        ];
                    }
                }

                // equipment checklist
                $equipment_checklist = [];

                foreach ($safetypermit->mapped_equipment_checklist as $job => $details) {
                    foreach ($details['checkpoint_names'] as $checkpoint_name) {
                        $equipment_checklist[] = [
                            'name' => $checkpoint_name,
                            'checked' => 'Yes'
                        ];
                    }
                }

                $safework_instruction = [];

                foreach ($safetypermit->mapped_safework_instruction as $job => $details) {
                    foreach ($details['checkpoint_names'] as $checkpoint_name) {
                        $safework_instruction[] = [
                            'name' => $checkpoint_name,
                            'checked' => 'Yes'
                        ];
                    }
                }
                $file_paths = [];

                if (!empty($getEhSverification->file_paths)) {
                    foreach (explode(',', $getEhSverification->file_paths) as $file) {
                        $file_paths[] = [
                            'file_path' => trim($file)
                        ];
                    }
                }

                $ptwstatusLogs = [];

                if (!empty($status_log)) {


                    foreach ($status_log as $status) {
                        $logEntry = [
                            'from_status' => isset($status['to_status']) ? $status['to_status'] : '-',
                            'to_status' => isset($status['status_name']) ? $status['status_name'] : '-',
                            'remarks' => isset($status['remarks']) ? $status['remarks'] : '-',
                            'created_at' => isset($status['created_at']) ? Displaydateformat($status['created_at']) : '-',
                        ];


                        if ($status['to_status'] == 'EHS Approved') {
                            $logEntry['additional_approval'] = [
                                'next_status_1' => 'Plant Head Approval Pending',
                                'next_status_2' => 'Plant Head Approved',
                                'approved_by' => isset($status['approved_by']) ? getUsername($status['approved_by']) : '-',
                                'approval_remarks' => isset($status['remarks']) ? $status['remarks'] : '-',
                                'approval_date' => isset($status['created_at']) ? Displaydateformat($status['created_at']) : '-',
                            ];
                        }

                        $ptwstatusLogs[] = $logEntry; // Push log entry to array
                    }
                }
                $success = [
                    'id' => $safetypermit->id,
                    'permit_id' => $safetypermit->permit_id,
                    'date' => Displaydateformat($safetypermit->date),
                    'to_date' => Displaydateformat($safetypermit->to_date),
                    'company' => getCompanyname($safetypermit->company_id),
                    'location' => getLocationname($safetypermit->location_id),
                    'time_from' => $safetypermit->time_from,
                    'time_to' => $safetypermit->time_to,
                    'unit_id' => getUnitname($safetypermit->unit_id),
                    'exact_location_job' => $safetypermit->exact_location_job,
                    'job_location_area' => $safetypermit->job_location_area,
                    'created_by' => getusername($safetypermit->created_by),
                    'created_at' => Displaydateformat($safetypermit->created_at),
                    'safety_permit_status' => [
                        'id' => $safetypermit->status_id,
                        'status' => $safetypermit->status_name,
                    ],
                    'verified_by' => [
                        'id' => $safetypermit->verified_by,
                        'name' => getusername($safetypermit->verified_by),
                    ],
                    'resume_hold_by' => [
                        'id' => $safetypermit->resume_hold_by,
                        'name' => getusername($safetypermit->resume_hold_by),
                    ],
                    'reassign_to' => [
                        'id' => $safetypermit->reassign_to,
                        'name' => getusername($safetypermit->reassign_to),
                    ],
                    'approved_by' => [
                        'id' => $safetypermit->approved_by,
                        'name' => getusername($safetypermit->approved_by),
                    ],
                    'type_of_work' => [
                        'sub_permit' => $sub_permits,

                        'job_description' => $safetypermit->job_description,

                        'list_of_workman_job' => [
                            'workman' => $workmaninvolved->map(function ($workman) {
                                return [
                                    'employee_code'   => $workman->emp_id,
                                    'name_of_workman' => $workman->workman_name,
                                    'designation'     => $workman->workman_desig,
                                    'department'      => $workman->department_name,
                                    'nature_of_job'   => $workman->nature_of_job,
                                ];
                            })->toArray(),
                        ],

                        'shut_down' => [
                            'images' => ('public/assets/images/safetypermit/power-off.png'),
                            'name' => "Shut Down Required",
                            'shutdown_req_checked' => $safetypermit->shutdown_req == 1 ? 'Yes' : 'No',
                        ],

                        'shut_down_takenby' => [
                            'images' => ('public/assets/images/safetypermit/profile.png'),
                            'name' => "Taken By (Name & Department)",
                            'shut_down_takenby' => $safetypermit->shut_down_takenby,
                        ],

                        'loto_req' => [
                            'images' => ('public/assets/images/safetypermit/process.png'),
                            'name' => "Isolation/LOTO Required",
                            'loto_req_checked' => $safetypermit->loto_req == 1 ? 'Yes' : 'No',
                        ],

                        'loto_req_takenby' => [
                            'images' => ('public/assets/images/safetypermit/profile.png'),
                            'name' => "Taken By (Name & Department)",
                            'loto_takenby' => $safetypermit->loto_takenby,
                        ],

                        'loto_no' => $safetypermit->loto_no,

                        'tagfield' => [
                            'name' => "Tag Field properly",
                            'tagfield_checked' => $safetypermit->tagfield == 1 ? 'Yes' : 'No'
                        ],
                    ],

                    'state_of_isolation' =>
                    $state_of_isolation,
                    'other_if_any' => $other_if_any,
                    'isolationpanel' => $isolationpanel,
                    'isolationpaneldescription' => $isolationpaneldescription,

                    'confined_space_entry' => [
                        'o2' => [
                            'name' => "O2%",
                            "value" => $confined_space_entry->o2_percentage ?? '',
                        ],
                        'system_isolated' => [
                            'name' => "System Isolated",
                            "value" => $confined_space_entry && $confined_space_entry->system_isolated == 1 ? 'Yes' : 'No',
                        ],
                        'rescue_system' => [
                            'name' => "Rescue System Available",
                            "value" => $confined_space_entry && $confined_space_entry->rescue_system  == 1 ? 'Yes' : 'No',
                        ],
                        'confined_attendant' => [
                            'name' => "Confined Space Attendant",
                            "value" => $confined_space_entry && $confined_space_entry->confined_attendant  == 1 ? 'Yes' : 'No',
                        ],
                        'attendant_name' => [
                            'name' => "Attendant Name",
                            "value" => $confined_space_entry->attendant_name ?? '',
                        ],
                        'register_entry_exits' => [
                            'name' => "Register for entry & exits ",
                            'value' => isset($confined_space_entry->register_entry_exits) && $confined_space_entry->register_entry_exits == 1 ? 'Yes' : 'No',
                        ],
                        'other_gas' => [
                            'name' => "Any Other Gas / PPM",
                            "value" => $confined_space_entry && $confined_space_entry->other_gas  == 1 ? 'Yes' : 'No',
                        ],
                        'ppm_safe_to_enter' => [
                            'name' => "PPM and is therefore safe to enter from",
                            "value" => isset($confined_space_entry->ppm_safe_to_enter) ? $confined_space_entry->ppm_safe_to_enter : '',
                        ],
                        'to' => [
                            'name' => "PPM and is therefore safe to enter To",
                            "value" =>  isset($confined_space_entry->to) ? $confined_space_entry->to : '',
                        ],
                    ],

                    'protective_equipments_worn' => [
                        'images' => [
                            ('public/assets/images/safetypermit/gloves.png'),
                            ('public/assets/images/safetypermit/helmet.png'),
                            ('public/assets/images/safetypermit/shoes.png'),
                            ('public/assets/images/safetypermit/gloves (1).png'),
                            ('public/assets/images/safetypermit/boots (1).png'),
                            ('public/assets/images/safetypermit/boots.png'),
                            ('public/assets/images/safetypermit/safety-goggles.png'),
                        ],
                        'protective_equipments_worn' => $protective_equip,
                    ],
                    'mandatory_notes_for_ppe' => [
                        'PPEs must be of national/international standard.',
                        'Damaged/defective PPEs shall not be used.',
                        'Non-standard PPEs shall not be used.',
                        'PPEs must be inspected before use.',
                    ],
                    'equipment_involved_job' => [
                        'images' => [
                            ('public/assets/images/safetypermit/flash.png'),
                            ('public/assets/images/safetypermit/shoes.png'),
                            ('public/assets/images/safetypermit/gloves (1).png'),
                            ('public/assets/images/safetypermit/gloves.png'),

                        ],
                        'equipment_involved_job' => $equipment_involve,
                        'others_if_any' => isset($safetypermit->equiment_involved_others) ? $safetypermit->equiment_involved_others : ''
                    ],
                    'precaution_taken' => [
                        'precaution_taken' => $precaution_taken,
                    ],
                    'equipment_checklist' => [
                        'equipment_checklist' => $equipment_checklist,
                        'inspection_checklist_prior_to_start_work_checked' => $safetypermit->equipment_checklist_inspection == 1 ? 'Yes' : 'No'
                    ],
                    'safework_instruction' => [
                        'safework_instruction' => $safework_instruction,
                        'safe_work_procedure_discussed_in_tool_box_talk_before_start_the_work' => $safetypermit->toolbox_talk  == 1 ? 'Yes' : 'No',
                        'tool_box_talk_given_by' => $safetypermit->talk_givenby,
                    ],
                    'mandatory_notes_for_equipments' => [
                        'Equipemnt must be of national/international standard.',
                        'Damaged/Defective equipment shall not be used.',
                        'Equipment should be in good working condition.',
                        'Non standard equipment shall not be used.',
                    ],

                    ' assigned_job_physically_fit_for_duty' => $safetypermit->assigned_job  == 1 ? 'Yes' : 'No',
                    'attendance_in_tool_box_talk' => $safetypermit->attendance_toolbox_talk,

                    'notes' => [
                        ' Work Permit is mandatory for non routine work, third party working agency & high risk Job.',
                        ' Work Permit is valid for 8 hours / Renewal may be extended as per unit head approval.',
                        ' Work Permit will be canceled in case of emergency i.e Fire, weather condition, disaster etc.',
                        ' Work permit is not valid without signature of Requestor, Verifier & Approver.',
                        ' Safe Work procedure & method of statement must be discussed in the tool box talk.',
                        '  Permit to be signed by (Requestor, Verifier & Approver) people not less than Site Engineer / Floor Manager.',
                        ' Permit Safety compliance shall be discussed to all involved person in local language.',
                    ],
                    'ehs_verification' => [
                        'forwarded_by' => $getEhSverification->approve_reject_by ?? '',
                        'date' => isset($getEhSverification->date) ? Displaydateformat($getEhSverification->date) : '',
                        'additional_suggestion' => $getEhSverification->remarks ?? '',
                        'signature' => $file_paths
                    ],

                    'ehs_head_approval' => [
                        'approver_name' => isset($getEhsapproval->approve_reject_by) ? $getEhsapproval->approve_reject_by : '',
                        'date' =>  isset($getEhsapproval->date) ? Displaydateformat($getEhsapproval->date) : '',
                        'remarks' => isset($getEhsapproval->remarks) ? $getEhsapproval->remarks : ''
                    ],
                    'plant_head_approval' => [
                        'approver_name' => isset($getplantheadapproval->approve_reject_by) ? $getplantheadapproval->approve_reject_by : '',
                        'date' => isset($getplantheadapproval->date) ? Displaydateformat($getplantheadapproval->date) : '',
                        'remarks' => isset($getplantheadapproval->remarks) ? $getplantheadapproval->remarks : ''
                    ],
                    'status_closed' => [
                        'approver_name' => getUsername(isset($safetypermit->closed_by) ? $safetypermit->closed_by : ''),
                        'date' => isset($safetypermit->closed_date) ? Displaydateformat($safetypermit->closed_date) : '',
                        'remarks' => isset($safetypermit->close_remarks) ? $safetypermit->close_remarks : ''
                    ],
                    'status_logs' => [
                        'status_log' => $ptwstatusLogs
                    ],

                ];

                return $this->sendResponse($success, 'Safety Permit Details');
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    public function ehsapproval(Request $request)
    {

        try {

            $rules = [
                'ptw_id' => 'required',
                'remarks' => 'required',
                'status' => 'required',


            ];
            $messages = [
                'ptw_id.required' => 'id is Required',
                'remarks.required' => 'Remarks is Required',
                'status.required' => 'Status is Required',



            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }
            $id = $request->ptw_id;
            $safetypermit = $this->safetypermit->find($id);

            if ($request->status == '1') {
                $permit_status = STATUS_EHS_HOLD;
            } elseif ($request->status == '2') {
                $permit_status = STATUS_EHS_RESUME;
            } elseif ($request->status == '3') {
                $permit_status = STATUS_EHS_DECLINE;
            } elseif ($request->status == '4') {
                $permit_status = STATUS_EHS_REASSIGN;
            } elseif ($request->status == '5') {

                $permit_status = STATUS_PLANT_HEAD_PENDING;
            }

            $approve =   $this->approvereject->ehsapproval_api($permit_status, $safetypermit);
            if ($request->status == '4') {
                $this->safetypermit->reassignto_api($request->reassign_to, $id);
            } elseif ($request->status == '2' || $request->status == '1') {
                $this->safetypermit->resume_hold($approve->created_by, $id);
            }
            $this->safetypermit->permitstatus($permit_status, $id);

            if ($request->status == '1') {

                $mailsubject = 'EHS Holded the permit';
                $Assignedusers = User::whereIn('id', [$approve->created_by, $safetypermit->created_by])
                    ->select('name', 'email')
                    ->get()
                    ->unique('email');

                if (count($Assignedusers) > 0) {

                    foreach ($Assignedusers as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            $safetypermitdetails =  $this->safetypermit->selectmail($id);
                            $permitrray  = $safetypermitdetails->toArray();

                            $permitrray['name'] = $user->name;
                            $permitrray['email_id'] =  $email_id;
                            $permitrray['mail_subject'] = $mailsubject;

                            Mail::to($permitrray['email_id'])->queue(new SafetyPermitEmail($permitrray));
                        }
                    }
                }
                $UserIds = User::whereIn('id', [$safetypermit->resume_hold_by, $safetypermit->created_by])
                    ->pluck('id')
                    ->toArray();

                $UserIdsCommaSeparated = implode(',', $UserIds);
                $notificationData = array(
                    'notification_type' => 3,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Safety Permit ' . $safetypermit->permit_id . $mailsubject . getUsername($approve->created_by),
                        'icon' =>  admin_url('public/assets/icons/permit_to_work.png'),
                        'id' => $safetypermit->id,
                        'module' => 3,
                    )),
                    'web_link' =>  admin_url('safetypermit/approvereject/' . encryptId($safetypermit->id)),
                    'assigned_user' => $UserIdsCommaSeparated,
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
                $notifydata = [
                    'title' => $mailsubject,
                    'message' =>   'Safety Permit ' . $safetypermit->permit_id . $mailsubject . getUsername($approve->created_by),
                    'module_id' => $safetypermit->permit_id,
                    'module_type' => 1,
                    'module_sub_type' => 0,
                ];
                mobilePushNotification($UserIdsCommaSeparated, $notifydata);
            } elseif ($request->status == '2') {
                $mailsubject = 'EHS Resumed the permit';
                $Assignedusers = User::whereIn('id', [$approve->created_by, $safetypermit->created_by])
                    ->select('name', 'email')
                    ->get()
                    ->unique('email');

                if (count($Assignedusers) > 0) {

                    foreach ($Assignedusers as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            $safetypermitdetails =  $this->safetypermit->selectmail($id);
                            $permitrray  = $safetypermitdetails->toArray();

                            $permitrray['name'] = $user->name;
                            $permitrray['email_id'] =  $email_id;
                            $permitrray['mail_subject'] = $mailsubject;

                            Mail::to($permitrray['email_id'])->queue(new SafetyPermitEmail($permitrray));
                        }
                    }
                }
                $UserIds = User::whereIn('id', [$safetypermit->resume_hold_by, $safetypermit->created_by])
                    ->pluck('id')
                    ->toArray();

                $UserIdsCommaSeparated = implode(',', $UserIds);
                $notificationData = array(
                    'notification_type' => 3,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Safety Permit ' . $safetypermit->permit_id . $mailsubject . getUsername($approve->created_by),
                        'icon' =>  admin_url('public/assets/icons/permit_to_work.png'),
                        'id' => $safetypermit->id,
                        'module' => 3,
                    )),
                    'web_link' =>  admin_url('safetypermit/approvereject/' . encryptId($safetypermit->id)),
                    'assigned_user' => $UserIdsCommaSeparated,
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
                $notifydata = [
                    'title' => $mailsubject,
                    'message' =>   'Safety Permit ' . $safetypermit->permit_id . $mailsubject . getUsername($approve->created_by),
                    'module_id' => $safetypermit->permit_id,
                    'module_type' => 1,
                    'module_sub_type' => 0,
                ];
                mobilePushNotification($UserIdsCommaSeparated, $notifydata);
            } elseif ($request->status == '3') {
                $mailsubject = 'EHS declined the permit Rework the permit';
                $notifywhere = array(
                    'id' => $safetypermit->created_by,
                );
                $userids = User::where($notifywhere)->pluck('id')->toArray();
                $users = User::where($notifywhere)->get();

                if (count($users) > 0) {

                    foreach ($users as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            $safetypermitdetails =  $this->safetypermit->selectmail($id);
                            $permitrray  = $safetypermitdetails->toArray();

                            $permitrray['name'] = $user->name;
                            $permitrray['email_id'] =  $email_id;
                            $permitrray['mail_subject'] = $mailsubject;

                            Mail::to($permitrray['email_id'])->queue(new SafetyPermitEmail($permitrray));
                        }
                    }
                }


                /**
                 * Send Web notification
                 */

                $notificationData = array(
                    'notification_type' => 3,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Safety Permit ' . $safetypermit->permit_id . $mailsubject . getUsername($approve->created_by),
                        'icon' =>  admin_url('public/assets/icons/permit_to_work.png'),
                        'id' => $safetypermit->id,
                        'module' => 3,
                    )),
                    'web_link' =>  admin_url('safetypermit/edit/' . encryptId($safetypermit->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
                $notifydata = [
                    'title' => $mailsubject,
                    'message' =>   'Safety Permit ' . $safetypermit->permit_id . $mailsubject . getUsername($approve->created_by),
                    'module_id' => $safetypermit->permit_id,
                    'module_type' => 1,
                    'module_sub_type' => 0,
                ];
                mobilePushNotification(array_to_string($userids), $notifydata);
            } elseif ($request->status == '4') {
                $mailsubject = 'EHS Re-assigned the permit';
                $notifywhere = array(
                    'id' => $request->reassign_to,
                );
                $userids = User::where($notifywhere)->pluck('id')->toArray();
                $users = User::where($notifywhere)->get();

                if (count($users) > 0) {

                    foreach ($users as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            $safetypermitdetails =  $this->safetypermit->selectmail($id);
                            $permitrray  = $safetypermitdetails->toArray();

                            $permitrray['name'] = $user->name;
                            $permitrray['email_id'] =  $email_id;
                            $permitrray['mail_subject'] = $mailsubject;

                            Mail::to($permitrray['email_id'])->queue(new SafetyPermitEmail($permitrray));
                        }
                    }
                }


                /**
                 * Send Web notification
                 */

                $notificationData = array(
                    'notification_type' => 3,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Safety Permit ' . $safetypermit->permit_id . $mailsubject . getUsername($approve->created_by),
                        'icon' =>  admin_url('public/assets/icons/permit_to_work.png'),
                        'id' => $safetypermit->id,
                        'module' => 3,
                    )),
                    'web_link' =>  admin_url('safetypermit/approvereject/' . encryptId($safetypermit->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
                $notifydata = [
                    'title' => $mailsubject,
                    'message' =>   'Safety Permit ' . $safetypermit->permit_id . $mailsubject . getUsername($approve->created_by),
                    'module_id' => $safetypermit->permit_id,
                    'module_type' => 1,
                    'module_sub_type' => 0,
                ];
                mobilePushNotification(array_to_string($userids), $notifydata);
            } elseif ($request->status == '5') {
                // dd('STATUS_PLANT_HEAD_PENDING', $request);
                $mailsubject = 'EHS Approved';
                $user_role = ROLE_PLANT_HEAD;

                $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->where('unit_id', $safetypermit->unit_id)->pluck('id')->toArray();
                $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->where('unit_id', $safetypermit->unit_id)->get();



                if (count($users) > 0) {

                    foreach ($users as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            $safetypermitdetails =  $this->safetypermit->selectmail($id);
                            $permitrray  = $safetypermitdetails->toArray();

                            $permitrray['name'] = $user->name;
                            $permitrray['email_id'] =  $email_id;
                            $permitrray['mail_subject'] = $mailsubject;

                            Mail::to($permitrray['email_id'])->queue(new SafetyPermitEmail($permitrray));
                        }
                    }
                }


                /**
                 * Send Web notification
                 */

                $notificationData = array(
                    'notification_type' => 3,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Safety Permit ' . $safetypermit->permit_id . $mailsubject . getUsername($approve->created_by),
                        'icon' =>  admin_url('public/assets/icons/permit_to_work.png'),
                        'id' => $safetypermit->id,
                        'module' => 3,
                    )),
                    'web_link' =>  admin_url('safetypermit/approvereject/' . encryptId($safetypermit->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
            }
            $notifydata = [
                'title' => $mailsubject,
                'message' =>   'Safety Permit ' . $safetypermit->permit_id . $mailsubject . getUsername($approve->created_by),
                'module_id' => $safetypermit->permit_id,
                'module_type' => 1,
                'module_sub_type' => 0,
            ];
            mobilePushNotification(array_to_string($userids), $notifydata);
            $insert_array = array(
                'permit_type' => 2,
                'permit_id' => $id,
                'from_status' => 2,
                'to_status' => $permit_status,
                'is_reject' => null,
                'remarks' => $request->remarks,
                'approved_by' => Auth::id(),
            );

            $this->statuslog->create($insert_array);
            $success = [
                'ptw_id' => $id
            ];
            return $this->sendResponse($success, 'Responded successfully');
        } catch (Exception $ex) {

            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    public function qrcode(Request $request)
    {
        try {
            $ptw_id = $request->ptw_id;


            $safetypermit = $this->safetypermit->selectOne($ptw_id);


            if (!$safetypermit) {
                return $this->sendError('Permit not found.', ['error' => 'Invalid PTW ID'], 404);
            }


            $url = admin_url('safetypermit/join/' . $ptw_id);
            $qrSvg = QrCode::size(150)
                ->backgroundColor(255, 255, 255)
                ->color(1, 1, 1)
                ->generate($url);


            $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);


            $success = [
                'permit_no' => get_permit_no($ptw_id),
                'date' => isset($safetypermit->date) ? Displaydateformat($safetypermit->date) : '-',
                'from_time' => $safetypermit->time_from ?? '-',
                'to_time' => $safetypermit->time_to ?? '-',
                'unit_id' => isset($safetypermit->unit_id) ? getUnitname($safetypermit->unit_id) : '-',
                'exact_location_job' => $safetypermit->exact_location_job ?? '-',
                'job_location_area' => $safetypermit->job_location_area ?? '-',
                'created_by' => isset($safetypermit->created_by) ? getUsername($safetypermit->created_by) : '-',

            ];

            return $this->sendResponse($success, 'Safety Permit Qr Code');
        } catch (Exception $ex) {
            Log::error('QR Code Generation Error: ' . $ex->getMessage());
            return $this->sendError('An error occurred.', ['error' => $ex->getMessage()], 500);
        }
    }

    public function getReassig1nEmployee(Request $request)
    {
        $name = $request->input('search');
        $unitId = $request->input('unitId');

        $employees = Employee::where('emp_name', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->whereRaw("FIND_IN_SET(?, user_role)", [3])
            ->where('login_id', '!=', Auth::id())
            ->limit(10)
            ->get();

        return response()->json(
            $employees->map(function ($employee) {
                return [
                    'id' => $employee->login_id,
                    'text' => $employee->emp_name . ' - ' . $employee->emp_id,
                ];
            })
        );
    }
    public function getReassignEmployee()
    {
        try {
            if (Auth::check()) {
                $employeeList = Employee::select(
                    'masters_employee.id',
                    'masters_employee.emp_name',
                    'masters_employee.login_id',
                    'masters_employee.emp_id',
                )
                    ->where('masters_employee.status', 1)
                    ->get()
                    ->map(function ($employee) {
                        return [
                            'id' => $employee->id,
                            'emp_name' => $employee->emp_name,
                            'emp_id' => $employee->emp_id,
                            'login_id' => $employee->login_id,
                        ];
                    });

                return $this->sendResponse(['responsible_person' => $employeeList], 'Reassign Employee details');
            }

            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        } catch (Exception $ex) {
            Log::error('Employee Fetch Error: ' . $ex->getMessage());
            return $this->sendError('Something went wrong.', ['error' => $ex->getMessage()], 500);
        }
    }
}
