<?php

namespace App\Http\Controllers\Api\Ims;

use Mail;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\Master\Unit;
use Illuminate\Support\Facades\Auth;
use Exception;
use Validator;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\User;
use App\Models\UploadLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\IMS\Incident\InitialIncident;
use App\Models\IMS\Incident\InjuryDetails;
use App\Models\IMS\Incident\Rcpa;
use App\Models\IMS\Incident\RiskAnalysis;
use App\Models\IMS\Incident\WhyWhyAnalysis;
use App\Models\IMS\Incident\FishboneAnalysis;
use App\Models\IMS\Incident\Statuslog;
use App\Models\IMS\Incident\Incidentstatus;
use App\Models\IMS\Incident\EHSReview;
use App\Models\IMS\Incident\IncidentInvestigation;
use App\Models\IMS\Incident\IntialIncidentEvidencefile;
use App\Mail\IncidentEmail;
use App\Models\IMS\Master\IncidentType;
use App\Models\IMS\Incident\IncidentBodyParts;

class InitialIncidentController extends BaseController
{
    private $user;
    private $initialincident;
    private $initialincidentevidence;
    private $incidentinvestigation;
    private $riskanalysis;
    private $whyanalysis;
    private $fishboneAnalysis;
    private $Statuslog;
    private $status;
    private $injury_details;
    private $incident_body_parts;
    private $rcpa;
    private $incidenttype;

    public function __construct()
    {
        $this->initialincidentevidence = new IntialIncidentEvidencefile();
        $this->initialincident = new InitialIncident();
        $this->incidentinvestigation = new IncidentInvestigation();
        $this->riskanalysis = new RiskAnalysis();
        $this->whyanalysis = new WhyWhyAnalysis();
        $this->fishboneAnalysis = new FishboneAnalysis();
        $this->Statuslog = new Statuslog();
        $this->status = new Incidentstatus();
        $this->injury_details = new InjuryDetails();
        $this->rcpa = new Rcpa();
        $this->incidenttype = new IncidentType();
        $this->incident_body_parts = new IncidentBodyParts();
    }

    public function iirTypeList()
    {
        try {
            if (Auth::check()) {
                $iirTypeList = $this->incidenttype->select(
                    'id',
                    'incident_type_id',
                    'incident_type_name',
                    'short_name',
                )

                    ->where('status', 1)
                    ->get();

                $success = [
                    'responsible_person' => $iirTypeList,
                ];



                return $this->sendResponse($success, 'Incident Type details');
            }

            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        } catch (Exception $ex) {
            Log::error('Employee Fetch Error: ' . $ex->getMessage());

            return $this->sendError('Something went wrong.', ['error' => $ex->getMessage()], 500);
        }
    }



    public function list()
    {
        if (!Auth::check()) {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }

        $request = request();
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        try {
            $incidentQuery = InitialIncident::query()
                ->select(
                    'ims_initial_incident.id',
                    'masters_unit.unit_name',
                    'ims_initial_incident.shift',
                    'ims_initial_incident.sr_no',
                    'ims_initial_incident.status',
                    'ims_initial_incident.created_by',
                    'ims_initial_incident.created_at',
                    'ims_incident_status.status_name',
                    'ims_incident_status.id as status_id',
                    'ims_incident_status.bg_color'
                )
                ->leftJoin('masters_unit', 'ims_initial_incident.unit_id', '=', 'masters_unit.id')
                ->leftJoin('ims_incident_status', 'ims_initial_incident.incident_status', '=', 'ims_incident_status.id');

            // Role-based access
            if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_ADMIN) || CheckUserRole(ROLE_EHS_HEAD)) {
                $incidentQuery->where('ims_initial_incident.status', 1);
            } elseif (CheckUserRole(ROLE_EHS_OFFICER)) {
                $incidentQuery->where('ims_initial_incident.created_by', Auth::id());
            } else {
                $incidentQuery->where('ims_initial_incident.created_by', Auth::id());
            }

            // Search filter (by created date)

            if (!empty($search)) {
                $searchDate = DBdateformat($search);
                $incidentQuery->where(function ($query) use ($search, $searchDate) {
                    $query->orWhere('ims_initial_incident.sr_no', 'LIKE', "%{$search}%")
                        ->orWhereDate('ims_initial_incident.created_at', 'LIKE', "%{$searchDate}%")
                        ->orWhere('masters_unit.unit_name', 'LIKE', "%{$search}%")
                        ->orWhere('ims_incident_status.status_name', 'LIKE', "%{$search}%");
                });
            }

            // Pagination
            $incidents = $incidentQuery->orderByDesc('ims_initial_incident.id')->paginate($perPage);

            if ($incidents->isEmpty()) {
                return $this->sendError('No records found.', [], 404);
            }

            $data_array = [];
            foreach ($incidents as $incident) {
                $data_array[] = [
                    'id' => $incident->id,
                    'sr_no' => $incident->sr_no,
                    'unit' => $incident->unit_name,
                    'shift' => $incident->shift,
                    'shift' => getIIRTypename($incident->iir_type),
                    'approve_status' => $incident->status_name,
                    'status' => $incident->status == 1 ? 'Active' : 'In-Active',
                    'created_by' => getUsername($incident->created_by),
                    'created_at' => Displaydateformat($incident->created_at),
                ];
            }

            $incident_details = [
                'per_page' => $incidents->perPage(),
                'current_page' => $incidents->currentPage(),
                'from' => $incidents->firstItem(),
                'to' => $incidents->lastItem(),
                'total' => $incidents->total(),
                'total_page' => $incidents->lastPage(),
                'list' => $data_array,
            ];

            return $this->sendResponse(['incident_details' => $incident_details], 'Incident List fetched successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Something went wrong.', ['error' => $e->getMessage()], 500);
        }
    }

    public function Store(Request $request)
    {
        try {
            $rules = [
                'incident_date_time'       => 'required|date',
                'company_id'               => 'required',
                'location_id'              => 'required',
                'unit_id'                  => 'required',
                'shift'                    => 'required',
                'exact_location'           => 'required',
                'iir_type'                 => 'required|integer',
                'reported_name'            => 'required',
                'designation'              => 'required',
                'department'               => 'required',
                'employee_code'            => 'required',
                'time_of_reporting'        => 'required',
                'reporting_media'          => 'required|array',
                'brief_description'        => 'required',
                'immediate_action_taken'   => 'required',
                'anyone_injured'           => 'required|in:0,1',
                'evidence'                 => 'required|array',
                'injuredPerson'            => 'array',
            ];

            $messages = [
                'incident_date_time.required'     => 'Please enter Date and Time',
                'company_id.required'             => 'Please select a Company',
                'unit_id.required'                => 'Please select a Unit',
                'location_id.required'            => 'Please select a Location',
                'shift.required'                  => 'Please enter Shift',
                'exact_location.required'         => 'Please enter Exact Location',
                'iir_type.required'               => 'Please enter IIR Type',
                'reported_name.required'          => 'Please enter Name',
                'designation.required'            => 'Please enter Designation',
                'department.required'             => 'Please select a Department',
                'employee_code.required'          => 'Please enter Employee Code',
                'time_of_reporting.required'      => 'Please enter Time of reporting',
                'reporting_media.required'        => 'Please select at least one Reporting Media',
                'brief_description.required'      => 'Please enter Brief Description',
                'immediate_action_taken.required' => 'Please enter Immediate Action Taken',
                'anyone_injured.required'         => 'Please specify if anyone was injured',
                'evidence.required'               => 'Please upload at least one evidence file',
                'evidence.*.required'             => 'Each evidence file is required',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            $validator->after(function ($validator) use ($request) {
                if ($request->anyone_injured == 1) {
                    $injuredPersons = $request->input('injuredPerson', []);
                    if (empty($injuredPersons) || !is_array($injuredPersons)) {
                        $validator->errors()->add('injuredPerson', 'Please provide at least one injured person record.');
                    } else {
                        foreach ($injuredPersons as $index => $person) {
                            if (empty($person['injury_person_type'])) {
                                $validator->errors()->add("injuredPerson.$index.injury_person_type", 'Injury person type is required.');
                            }
                            if (empty($person['injury_person_id'])) {
                                $validator->errors()->add("injuredPerson.$index.injury_person_id", 'Injury person ID is required.');
                            }
                            if (isset($person['injury_person_type']) && $person['injury_person_type'] == 3) {
                                if (empty($person['injury_person_name'])) {
                                    $validator->errors()->add("injuredPerson.$index.injury_person_name", 'Injury person name is required when type is 3.');
                                }
                            }
                            if (empty($person['injury_person_designation'])) {
                                $validator->errors()->add("injuredPerson.$index.injury_person_designation", 'Injury person designation is required.');
                            }
                            if (empty($person['injury_person_department_id'])) {
                                $validator->errors()->add("injuredPerson.$index.injury_person_department_id", 'Injury person department ID is required.');
                            }
                            if (empty($person['nature_of_injury'])) {
                                $validator->errors()->add("injuredPerson.$index.nature_of_injury", 'Nature of injury is required.');
                            }
                        }
                    }
                }
            });

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }
            $randomID = '';
            if ($request->anyone_injured  == 1) {
                $getRandomID = IncidentBodyParts::select('random_id')
                    ->where('status', 'T')
                    ->orderByDesc('id')
                    ->first();
                $randomID = $getRandomID->random_id;
            }

            $initialincident = $this->initialincident->incidentStore_api($randomID);


            $this->initialincidentevidence->evidenceStore_api($initialincident->id);

            if ($initialincident->anyone_injured == 1) {

                $this->injury_details->storeinjuryApi($initialincident->id, $initialincident->random_id);
            }

            $incident_status = STATUS_INCIDENT_REPORT;
            $user_role = ROLE_EHS_HEAD;
            $mailsubject = 'Incident has been submitted';

            $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();
            $userids = $users->pluck('id')->toArray();

            foreach ($users as $user) {
                if (!empty($user->email)) {
                    $incidentDetails = $this->initialincident->selectOne($initialincident->id);
                    $incidentarray = $incidentDetails->toArray();
                    $incidentarray['name'] = $user->name;
                    $incidentarray['email_id'] = $user->email;
                    $incidentarray['mail_subject'] = $mailsubject;

                    Mail::to($user->email)->queue(new IncidentEmail($incidentarray));
                }
            }

            $notificationData = [
                'notification_type'    => 5,
                'module_type'          => 1,
                'notification_message' => $mailsubject,
                'mobile_notification'  => json_encode([
                    'title'   => $mailsubject,
                    'message' => 'Incident ' . $initialincident->sr_no . ' submitted by ' . getUsername($initialincident->created_by),
                    'icon'    => admin_url('public/assets/icons/incident.png'),
                    'id'      => $initialincident->id,
                    'module'  => 5,
                ]),
                'web_link'             => admin_url('incident/initial-incident/review/' . encryptId($initialincident->id)),
                'assigned_user'        => array_to_string($userids),
                'created_by'           => Auth::id(),
            ];
            notificationSave($notificationData);

            $insert_array = [
                'ims_type'   => 1,
                'ims_id'     => $initialincident->id,
                'from_status' => 0,
                'to_status'  => $incident_status,
                'is_reject'  => null,
                'remarks'    => null,
                'approved_by' => Auth::id(),
            ];
            $this->Statuslog->create($insert_array);

            $success = [
                'incident_id' => $initialincident->id,
            ];

            return $this->sendResponse($success, 'Your data has been created successfully');
        } catch (Exception $ex) {
            dd($ex);
            return $this->sendError('Server Error', ['error' => $ex->getMessage()], 500);
        }
    }



    public function view(Request $request)
    {
        try {
            if (Auth::user()) {
                $id = $request->id;

                $incident_report = $this->initialincident->selectOne($id);
                $rcpa = $this->rcpa->getRCPA($id);
                $getEHSVerify = $this->initialincident->getEHSVerifyincident($id);
                $getEHSReview = $this->initialincident->getEHSReviewincident($id);
                $getInvestigation = $this->initialincident->getInvestigation($id);
                $getwhywhy = $this->initialincident->getwhywhy($id);
                $getfishbone = $this->initialincident->getfishbone($id);
                $getrisklevel = $this->initialincident->getrisklevel($id);
                $getEHSApprovalincident = $this->initialincident->getEHSApprovalincident($id);
                $initialincidentevidence = $this->initialincidentevidence->selectOne($id);
                $injury_details = $this->injury_details->getBodypartsInjuryPerson($id);


                $status_log = $this->Statuslog->selectOne($id, 1);

                $status_logList = [];

                if (!empty($status_log)) {
                    foreach ($status_log as $status) {
                        $status_logList[] = [
                            'from_status'   => $status['to_status'] ?? '',
                            'to_status'     => $status['status_name'] ?? '',
                            'approved_by'   => getUsername($status['approved_by']) ?? '',
                            'remarks'       => $status['remarks'] ?? '',
                            'date'          => isset($status['created_at']) ? Displaydateformat($status['created_at']) : '',
                        ];
                    }
                }

                $mediaOptions = [
                    1 => 'Phone',
                    2 => 'Walkie Talkie',
                    3 => 'Extension',
                    4 => 'Others',
                ];

                $selectedMedia = isset($incident_report->reporting_media)
                    ? explode(',', $incident_report->reporting_media)
                    : [];

                $displayMedia = array_map(function ($media) use ($mediaOptions) {
                    return $mediaOptions[$media] ?? $media;
                }, $selectedMedia);


                $existing_evidenceimages = [];
                if (count($initialincidentevidence) > 0) {
                    foreach ($initialincidentevidence as $usee) {
                        $existing_evidenceimages[] = url($usee->file_path);
                    }
                }

                $incident_reported_by = [
                    'id' => $incident_report->id,
                    'employee_code' => $incident_report->employee_code,
                    'name' => $incident_report->reported_name,
                    'designation' => $incident_report->designation,
                    'department' => $incident_report->reported_department,
                    'time_of_reporting' => $incident_report->time_of_reporting,
                    'reporting_media' => implode(', ', $displayMedia),
                    'status' =>  $incident_report->status == 1 ? 'Active' : 'In-Active',
                    'created_by' => getusername($incident_report->created_by),
                    'created_at' => Displaydateformat($incident_report->created_at),
                ];



                $incident_report_details = [
                    'sr_no' => $incident_report->sr_no,
                    'incident_date_time' => Displaydatetimeformat($incident_report->incident_date_time),
                    'company' => getCompanyname($incident_report->company_id),
                    'location' => $incident_report->location_name,
                    'unit' => getUnitname($incident_report->unit_id),
                    'shift' => $incident_report->shift,
                    'exact_location' => $incident_report->exact_location,
                    'iir_type' => $incident_report->incident_type_name,
                    'brief_description' => $incident_report->brief_description,
                    'existing_evidence' => $existing_evidenceimages,
                    'immediate_action_taken' => $incident_report->immediate_action_taken,
                    'anyone_injured_person' => $incident_report->anyone_injured == 1 ? 'Yes' : 'No',
                ];

                $injuryDetailList = [];
                if ($incident_report->anyone_injured == 1) {
                    foreach ($injury_details as $injury) {
                        $injury_person_name = in_array($injury->injury_person_type, [1, 2]) ? $injury->emp_name : $injury->injury_person_name;
                        $imgMapDataDecoded = json_decode($injury->imgMapdata, true);

                        $description = "No data available";
                        if (isset($imgMapDataDecoded['map']['total']) && is_array($imgMapDataDecoded['map']['total'])) {
                            $descParts = [];
                            foreach ($imgMapDataDecoded['map']['total'] as $key => $value) {
                                $descParts[] = ucfirst($key) . ': ' . $value;
                            }
                            $description = implode(', ', $descParts);
                        }

                        $injuryDetailList[] = [
                            'id' => $injury->id,
                            'injury_person_type' => $injury->injury_person_type == 1 ? 'Employee' : ($injury->injury_person_type == 2 ? 'Worker' : 'Others'),
                            'injury_person_name' => $injury_person_name,
                            'injury_person_emp_id' => $injury->emp_id,
                            'injury_person_designation' => $injury->injury_person_designation,
                            'injury_person_department' => $injury->injury_person_department_id,
                            'nature_of_injury' => $injury->nature_of_injury == 1 ? 'Major' : ($injury->nature_of_injury == 2 ? 'Minor' : 'Fatal'),
                            'body_part_image' => admin_url('storage/app/public/uploads/' . $injury->body_part_image),
                            'description' => $description,
                        ];
                    }
                }
                $accelerating_incident_investigations = [];
                if ($getEHSReview) {
                    $accelerating_incident_investigations = [
                        'id' => $getEHSReview->id,
                        'reviewer_name' => $getEHSReview->reviewer_name,
                        'date' => Displaydateformat($getEHSReview->date),
                        'target_date' => Displaydateformat($getEHSReview->target_date),
                        'team_member_names' => $getEHSReview->team_member_names,
                        'investigation_reported_prepared_by' => getUsername($getEHSReview->investigation_reported_by),
                        'remark' => $getEHSReview->remark,
                    ];
                }

                $damageTypes = [
                    1 => 'Man',
                    2 => 'Machine',
                    3 => 'Materials',
                ];
                $damagedItems = explode(',', $getInvestigation->anything_damaged);
                $damagedLabels = array_map(function ($item) use ($damageTypes) {
                    return $damageTypes[$item] ?? 'NA';
                }, $damagedItems);

                $remark = $getInvestigation->risk_analysis == 2 ? $getInvestigation->risk_analysis_remark : null;

                $why_why_analysis = [];
                if ($getInvestigation->root_cause_analysis == 1) {
                    foreach ($getwhywhy as $item) {
                        $why_why_analysis[] = [
                            'why_1' => $item->why_1,
                            'why_2' => $item->why_2,
                            'why_3' => $item->why_3,
                            'why_4' => $item->why_4,
                            'why_5' => $item->why_5,
                        ];
                    }
                }

                $fishboneDecodedData = [];
                if ($getInvestigation->root_cause_analysis == 2 && $getfishbone->first()) {
                    $decoded = json_decode($getfishbone->first()->fishbone, true);
                    if (is_array($decoded)) {
                        $fishboneDecodedData = $decoded;
                    }
                }

                $Investigation = [
                    'name_of_the_witness' => $getInvestigation->witness_name,
                    'was_anything_damaged' => implode(', ', $damagedLabels),
                    'prca' => $getInvestigation->root_cause_analysis == 1 ? 'Why Why Analysis' : ($getInvestigation->root_cause_analysis == 2 ? 'Fish Bone Analysis' : 'NA'),
                    'remark' => $getInvestigation->remark,
                    'investigation_submission_date' => Displaydateformat($getInvestigation->investigation_date),
                    'investigation_submission_time' => $getInvestigation->investigation_time,
                    'risk_analysis' => $getInvestigation->risk_analysis == 1 ? 'Yes' : 'No',
                    'risk_analysis_remark' => $remark,
                    'why_why_analysis' => $why_why_analysis,
                    'fishboneData' => $fishboneDecodedData,
                    'corrective_preventive_action' => $getInvestigation->corrective_preventive_action,
                ];




                $rcpaList = [];
                if ($rcpa) {
                    foreach ($rcpa as $item) {
                        $rcpaList[] = [
                            'rcpa_id' => $item->rcpa_id,
                            'rcpa' => $item->rcpa,
                            'responsibility' => getUsername($item->responsibility),
                            'timeline' => DisplayDateformat($item->timeline),
                            'status' =>  $item->capa_status == 1 ? 'Open' : ($item->capa_status == 2 ? 'In Progress' : 'Closed'),
                            'remark' => $item->remark,
                        ];
                    }
                }
                $ua_uc_labels = [];

                if ($incident_report->ua_uc_yes_no == 1) {
                    $ua_uc_values = explode(',', $incident_report->ua_or_uc);

                    $ua_uc_map = [
                        1 => 'Unsafe Act',
                        2 => 'Unsafe Condition',
                        3 => 'Natural Causes',
                    ];

                    foreach ($ua_uc_values as $value) {
                        $value = (int)trim($value); // Convert to integer and trim whitespace
                        if (isset($ua_uc_map[$value])) {
                            $ua_uc_labels[] = $ua_uc_map[$value];
                        }
                    }
                }

                $recommended_causesList = [
                    'uauc' => $incident_report->ua_uc_yes_no == 1 ? 'Yes' : 'No',
                    'ua/uc' => $ua_uc_labels,
                ];

                $risk_levelList = [
                    'risk_level' => $getrisklevel->risk_level == 1 ? 'Low' : ($getrisklevel->risk_level == 2 ? 'Medium' : 'High'),
                    'description_capa' => $getrisklevel->description_ca,
                ];

                return response()->json([
                    'success' => true,
                    'data' => [
                        'incident_reported_by' => $incident_reported_by,
                        'incident_report_details' => $incident_report_details,
                        'injury_details' => $injuryDetailList,
                        'accelerating_incident_investigations' => $accelerating_incident_investigations,
                        'investigation' => $Investigation,
                        'rcpa' => $rcpaList,
                        'main_root_cause' =>  $getInvestigation->main_root_cause,
                        'leading_factors' =>  $getInvestigation->leading_factors == 1 ? 'Human Factor' : ' System Factor',
                        'recommended_causes' => $recommended_causesList,
                        'risk_level' => $risk_levelList,
                        'status_log' => $status_logList,
                    ]
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function investigationList()
    {
        if (!Auth::check()) {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }

        $request = request();
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        try {
            $incidentQuery = InitialIncident::query()
                ->select(
                    'ims_initial_incident.id',
                    'masters_unit.unit_name',
                    'ims_initial_incident.shift',
                    'ims_initial_incident.sr_no',
                    'ims_initial_incident.status',
                    'ims_initial_incident.created_by',
                    'ims_initial_incident.created_at',
                    'ims_incident_status.status_name',
                    'ims_incident_status.id as status_id',
                    'ims_incident_status.bg_color',
                    'ims_initial_incident_investigation.risk_analysis'
                )
                ->leftJoin('masters_unit', 'ims_initial_incident.unit_id', '=', 'masters_unit.id')
                ->leftJoin('ims_initial_incident_investigation', 'ims_initial_incident_investigation.incident_id', '=', 'ims_initial_incident.id')
                ->leftJoin('ims_incident_status', 'ims_initial_incident.incident_status', '=', 'ims_incident_status.id');

            // Role-based access
            if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_ADMIN) || CheckUserRole(ROLE_EHS_HEAD)) {
                $incidentQuery->where('ims_initial_incident.status', 1);
            } elseif (CheckUserRole(ROLE_EHS_OFFICER)) {
                $incidentQuery->where('ims_initial_incident.created_by', Auth::id());
            } else {
                $incidentQuery->where('ims_initial_incident.created_by', Auth::id());
            }

            // Search filter (by created date)

            if (!empty($search)) {
                $searchDate = DBdateformat($search);
                $incidentQuery->where(function ($query) use ($search, $searchDate) {
                    $query->orWhere('ims_initial_incident.sr_no', 'LIKE', "%{$search}%")
                        ->orWhereDate('ims_initial_incident.created_at', 'LIKE', "%{$searchDate}%")
                        ->orWhere('masters_unit.unit_name', 'LIKE', "%{$search}%")
                        ->orWhere('ims_incident_status.status_name', 'LIKE', "%{$search}%");
                });
            }

            // Pagination
            $incidents = $incidentQuery->orderByDesc('ims_initial_incident.id')->paginate($perPage);

            if ($incidents->isEmpty()) {
                return $this->sendError('No records found.', [], 404);
            }

            $data_array = [];
            foreach ($incidents as $incident) {
                $data_array[] = [
                    'id' => $incident->id,
                    'sr_no' => $incident->sr_no,
                    'unit' => $incident->unit_name,
                    'shift' => $incident->shift,
                    'approve_status' => $incident->status_name,
                    'status' => $incident->status == 1 ? 'Active' : 'In-Active',
                    'created_by' => getUsername($incident->created_by),
                    'created_at' => Displaydateformat($incident->created_at),
                ];
            }

            $incident_investigation_details = [
                'per_page' => $incidents->perPage(),
                'current_page' => $incidents->currentPage(),
                'from' => $incidents->firstItem(),
                'to' => $incidents->lastItem(),
                'total' => $incidents->total(),
                'total_page' => $incidents->lastPage(),
                'list' => $data_array,
            ];

            return $this->sendResponse(['incident_investigation_details' => $incident_investigation_details], 'Incident Investigation List fetched successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Something went wrong.', ['error' => $e->getMessage()], 500);
        }
    }

    public function investigationView(Request $request)
    {
        try {
            if (Auth::user()) {
                $id = $request->id;

                $incident_report = $this->initialincident->selectOne($id);
                $rcpa = $this->rcpa->getRCPA($id);
                $getEHSVerify = $this->initialincident->getEHSVerifyincident($id);
                $getEHSReview = $this->initialincident->getEHSReviewincident($id);
                $getInvestigation = $this->initialincident->getInvestigation($id);
                $getwhywhy = $this->initialincident->getwhywhy($id);
                $getfishbone = $this->initialincident->getfishbone($id);
                $getrisklevel = $this->initialincident->getrisklevel($id);
                $getEHSApprovalincident = $this->initialincident->getEHSApprovalincident($id);
                $initialincidentevidence = $this->initialincidentevidence->selectOne($id);
                $injury_details = $this->injury_details->getBodypartsInjuryPerson($id);

                $status_log = $this->Statuslog->selectOne($id, 1);

                $status_logList = [];

                if (!empty($status_log)) {
                    foreach ($status_log as $status) {
                        $status_logList[] = [
                            'from_status'   => $status['to_status'] ?? '',
                            'to_status'     => $status['status_name'] ?? '',
                            'approved_by'   => getUsername($status['approved_by']) ?? '',
                            'remarks'       => $status['remarks'] ?? '',
                            'date'          => isset($status['created_at']) ? Displaydateformat($status['created_at']) : '',
                        ];
                    }
                }

                $mediaOptions = [
                    1 => 'Phone',
                    2 => 'Walkie Talkie',
                    3 => 'Extension',
                    4 => 'Others',
                ];

                $selectedMedia = isset($incident_report->reporting_media)
                    ? explode(',', $incident_report->reporting_media)
                    : [];

                $displayMedia = array_map(function ($media) use ($mediaOptions) {
                    return $mediaOptions[$media] ?? $media;
                }, $selectedMedia);


                $existing_evidenceimages = [];
                if (count($initialincidentevidence) > 0) {
                    foreach ($initialincidentevidence as $usee) {
                        $existing_evidenceimages[] = url($usee->file_path);
                    }
                }

                $incident_reported_by = [
                    'id' => $incident_report->id,
                    'employee_code' => $incident_report->employee_code,
                    'name' => $incident_report->reported_name,
                    'designation' => $incident_report->designation,
                    'department' => $incident_report->reported_department,
                    'time_of_reporting' => $incident_report->time_of_reporting,
                    'reporting_media' => implode(', ', $displayMedia),
                    'status' =>  $incident_report->status == 1 ? 'Active' : 'In-Active',
                    'created_by' => getusername($incident_report->created_by),
                    'created_at' => Displaydateformat($incident_report->created_at),
                ];


                $incident_report_details = [
                    'sr_no' => $incident_report->sr_no,
                    'incident_date_time' => Displaydatetimeformat($incident_report->incident_date_time),
                    'company' => getCompanyname($incident_report->company_id),
                    'location' => $incident_report->location_name,
                    'unit' => getUnitname($incident_report->unit_id),
                    'shift' => $incident_report->shift,
                    'exact_location' => $incident_report->exact_location,
                    'iir_type' => $incident_report->incident_type_name,
                    'brief_description' => $incident_report->brief_description,
                    'existing_evidence' => $existing_evidenceimages,
                    'immediate_action_taken' => $incident_report->immediate_action_taken,
                    'anyone_injured_person' => $incident_report->anyone_injured == 1 ? 'Yes' : 'No',
                ];

                $injuryDetailList = [];
                if ($incident_report->anyone_injured == 1) {
                    foreach ($injury_details as $injury) {
                        $injury_person_name = in_array($injury->injury_person_type, [1, 2]) ? $injury->emp_name : $injury->injury_person_name;
                        $imgMapDataDecoded = json_decode($injury->imgMapdata, true);

                        $description = "No data available";
                        if (isset($imgMapDataDecoded['map']['total']) && is_array($imgMapDataDecoded['map']['total'])) {
                            $descParts = [];
                            foreach ($imgMapDataDecoded['map']['total'] as $key => $value) {
                                $descParts[] = ucfirst($key) . ': ' . $value;
                            }
                            $description = implode(', ', $descParts);
                        }

                        $injuryDetailList[] = [
                            'id' => $injury->id,
                            'injury_person_type' => $injury->injury_person_type == 1 ? 'Employee' : ($injury->injury_person_type == 2 ? 'Worker' : 'Others'),
                            'injury_person_name' => $injury_person_name,
                            'injury_person_emp_id' => $injury->emp_id,
                            'injury_person_designation' => $injury->injury_person_designation,
                            'injury_person_department' => $injury->injury_person_department_id,
                            'nature_of_injury' => $injury->nature_of_injury == 1 ? 'Major' : ($injury->nature_of_injury == 2 ? 'Minor' : 'Fatal'),
                            'body_part_image' => admin_url('storage/app/public/uploads/' . $injury->body_part_image),
                            'description' => $description,
                        ];
                    }
                }

                $accelerating_incident_investigations = [];
                if ($getEHSReview) {
                    $accelerating_incident_investigations = [
                        'id' => $getEHSReview->id,
                        'reviewer_name' => $getEHSReview->reviewer_name,
                        'date' => Displaydateformat($getEHSReview->date),
                        'target_date' => Displaydateformat($getEHSReview->target_date),
                        'team_member_names' => $getEHSReview->team_member_names,
                        'investigation_reported_prepared_by' => getUsername($getEHSReview->investigation_reported_by),
                        'remark' => $getEHSReview->remark,
                    ];
                }

                $damageTypes = [
                    1 => 'Man',
                    2 => 'Machine',
                    3 => 'Materials',
                ];
                $damagedItems = explode(',', $getInvestigation->anything_damaged);
                $damagedLabels = array_map(function ($item) use ($damageTypes) {
                    return $damageTypes[$item] ?? 'NA';
                }, $damagedItems);

                $remark = $getInvestigation->risk_analysis == 2 ? $getInvestigation->risk_analysis_remark : null;

                $why_why_analysis = [];
                if ($getInvestigation->root_cause_analysis == 1) {
                    foreach ($getwhywhy as $item) {
                        $why_why_analysis[] = [
                            'why_1' => $item->why_1,
                            'why_2' => $item->why_2,
                            'why_3' => $item->why_3,
                            'why_4' => $item->why_4,
                            'why_5' => $item->why_5,
                        ];
                    }
                }

                $fishboneDecodedData = [];
                if ($getInvestigation->root_cause_analysis == 2 && $getfishbone->first()) {
                    $decoded = json_decode($getfishbone->first()->fishbone, true);
                    if (is_array($decoded)) {
                        $fishboneDecodedData = $decoded;
                    }
                }

                $Investigation = [
                    'name_of_the_witness' => $getInvestigation->witness_name,
                    'was_anything_damaged' => implode(', ', $damagedLabels),
                    'prca' => $getInvestigation->root_cause_analysis == 1 ? 'Why Why Analysis' : ($getInvestigation->root_cause_analysis == 2 ? 'Fish Bone Analysis' : 'NA'),
                    'remark' => $getInvestigation->remark,
                    'investigation_submission_date' => Displaydateformat($getInvestigation->investigation_date),
                    'investigation_submission_time' => $getInvestigation->investigation_time,
                    'risk_analysis' => $getInvestigation->risk_analysis == 1 ? 'Yes' : 'No',
                    'risk_analysis_remark' => $remark,
                    'why_why_analysis' => $why_why_analysis,
                    'fishboneData' => $fishboneDecodedData,
                    'corrective_preventive_action' => $getInvestigation->corrective_preventive_action,
                ];



                $rcpaList = [];
                if ($rcpa) {
                    foreach ($rcpa as $item) {
                        $rcpaList[] = [
                            'rcpa_id' => $item->rcpa_id,
                            'rcpa' => $item->rcpa,
                            'responsibility' => getUsername($item->responsibility),
                            'timeline' => DisplayDateformat($item->timeline),
                            'status' =>  $item->capa_status == 1 ? 'Open' : ($item->capa_status == 2 ? 'In Progress' : 'Closed'),
                            'remark' => $item->remark,
                        ];
                    }
                }
                $ua_uc_labels = [];

                if ($incident_report->ua_uc_yes_no == 1) {
                    $ua_uc_values = explode(',', $incident_report->ua_or_uc);

                    $ua_uc_map = [
                        1 => 'Unsafe Act',
                        2 => 'Unsafe Condition',
                        3 => 'Natural Causes',
                    ];

                    foreach ($ua_uc_values as $value) {
                        $value = (int)trim($value); // Convert to integer and trim whitespace
                        if (isset($ua_uc_map[$value])) {
                            $ua_uc_labels[] = $ua_uc_map[$value];
                        }
                    }
                }

                $recommended_causesList = [
                    'uauc' => $incident_report->ua_uc_yes_no == 1 ? 'Yes' : 'No',
                    'ua/uc' => $ua_uc_labels,
                ];

                $risk_levelList = [
                    'risk_level' => $getrisklevel->risk_level == 1 ? 'Low' : ($getrisklevel->risk_level == 2 ? 'Medium' : 'High'),
                    'description_capa' => $getrisklevel->description_ca,
                ];

                return response()->json([
                    'success' => true,
                    'data' => [
                        'incident_reported_by' => $incident_reported_by,
                        'incident_report_details' => $incident_report_details,
                        'injury_details' => $injuryDetailList,
                        'accelerating_incident_investigations' => $accelerating_incident_investigations,
                        'investigation' => $Investigation,
                        'rcpa' => $rcpaList,
                        'main_root_cause' =>  $getInvestigation->main_root_cause,
                        'leading_factors' =>  $getInvestigation->leading_factors == 1 ? 'Human Factor' : ' System Factor',
                        'recommended_causes' => $recommended_causesList,
                        'risk_level' => $risk_levelList,
                        'status_log' => $status_logList,
                    ]
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function calist()
    {
        if (!Auth::check()) {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }

        $request = request();
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);



        try {
            $incidentQuery = Rcpa::query()
                ->select(
                    'ims_rcpa_responsible.*',
                    'ims_initial_incident.id as incident_id',
                    'ims_initial_incident.sr_no',
                    'ims_initial_incident.shift',
                    'ims_initial_incident.status',
                    'ims_initial_incident.created_by',
                    'ims_initial_incident.created_at',
                    'ims_initial_incident.incident_status',
                    'masters_unit.unit_name',
                    'ims_incident_status.status_name',
                    'ims_incident_status.id as status_id',
                    'ims_incident_status.bg_color',
                )
                ->leftJoin('ims_initial_incident', 'ims_initial_incident.id', '=', 'ims_rcpa_responsible.incident_id')
                ->leftJoin('masters_unit', 'ims_initial_incident.unit_id', '=', 'masters_unit.id')
                ->leftJoin('ims_incident_status', 'ims_rcpa_responsible.incident_status', '=', 'ims_incident_status.id');
            // Role-based access
            if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_ADMIN) || CheckUserRole(ROLE_EHS_HEAD)) {
                $incidentQuery->where('ims_initial_incident.status', 1);
            } elseif (CheckUserRole(ROLE_EHS_OFFICER)) {
                $incidentQuery->where('ims_initial_incident.created_by', Auth::id());
            } else {
                $incidentQuery->where('ims_initial_incident.created_by', Auth::id());
            }

            // Search filter
            if (!empty($search)) {
                $searchDate = DBdateformat($search);
                $incidentQuery->where(function ($query) use ($search, $searchDate) {
                    $query->orWhere('ims_initial_incident.sr_no', 'LIKE', "%{$search}%")
                        ->orWhereDate('ims_initial_incident.created_at', 'LIKE', "%{$searchDate}%")
                        ->orWhere('masters_unit.unit_name', 'LIKE', "%{$search}%")
                        ->orWhere('ims_incident_status.status_name', 'LIKE', "%{$search}%");
                });
            }

            // Pagination
            $incidents = $incidentQuery->orderByDesc('ims_initial_incident.id')->paginate($perPage);

            if ($incidents->isEmpty()) {
                return $this->sendError('No records found.', [], 404);
            }

            $data_array = [];
            foreach ($incidents as $incident) {
                $data_array[] = [
                    'id' => $incident->id,
                    'sr_no' => $incident->sr_no,
                    'rcpa_id' => $incident->rcpa_id,
                    'unit' => $incident->unit_name,
                    'shift' => $incident->shift,
                    'approve_status' => $incident->status_name,
                    'status' => $incident->status == 1 ? 'Active' : 'In-Active',
                    'created_by' => getUsername($incident->created_by),
                    'created_at' => Displaydateformat($incident->created_at),
                ];
            }

            $incident_corrective_action_details = [
                'per_page' => $incidents->perPage(),
                'current_page' => $incidents->currentPage(),
                'from' => $incidents->firstItem(),
                'to' => $incidents->lastItem(),
                'total' => $incidents->total(),
                'total_page' => $incidents->lastPage(),
                'list' => $data_array,
            ];

            return $this->sendResponse(['incident_corrective_action_details' => $incident_corrective_action_details], 'Incident Corrective Action List fetched successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Something went wrong.', ['error' => $e->getMessage()], 500);
        }
    }
    public function capaView(Request $request)
    {
        try {
            if (Auth::user()) {
                $id = $request->id;
                $rcpa = $this->rcpa->selectOne($id);
                $incidentId = $rcpa->incident_id;
                $incident_report = $this->initialincident->selectOne($incidentId);
                $getEHSVerify = $this->initialincident->getEHSVerifyincident($incidentId);
                $getEHSReview = $this->initialincident->getEHSReviewincident($incidentId);
                $getInvestigation = $this->initialincident->getInvestigation($incidentId);
                $getwhywhy = $this->initialincident->getwhywhy($incidentId);
                $getfishbone = $this->initialincident->getfishbone($incidentId);
                $getrisklevel = $this->initialincident->getrisklevel($incidentId);
                $getEHSApprovalincident = $this->initialincident->getEHSApprovalincident($incidentId);
                $initialincidentevidence = $this->initialincidentevidence->selectOne($incidentId);
                $injury_details = $this->injury_details->getBodypartsInjuryPerson($incidentId);
                $capaEvidence = $this->initialincidentevidence->SelectcapaEvidence($id, $incidentId);


                $mediaOptions = [
                    1 => 'Phone',
                    2 => 'Walkie Talkie',
                    3 => 'Extension',
                    4 => 'Others',
                ];

                $selectedMedia = isset($incident_report->reporting_media)
                    ? explode(',', $incident_report->reporting_media)
                    : [];

                $displayMedia = array_map(function ($media) use ($mediaOptions) {
                    return $mediaOptions[$media] ?? $media;
                }, $selectedMedia);


                $existing_evidenceimages = [];
                if (count($initialincidentevidence) > 0) {
                    foreach ($initialincidentevidence as $usee) {
                        $existing_evidenceimages[] = url($usee->file_path);
                    }
                }

                $incident_reported_by = [
                    'id' => $incident_report->id,
                    'employee_code' => $incident_report->employee_code,
                    'name' => $incident_report->reported_name,
                    'designation' => $incident_report->designation,
                    'department' => $incident_report->reported_department,
                    'time_of_reporting' => $incident_report->time_of_reporting,
                    'reporting_media' => implode(', ', $displayMedia),
                    'status' =>  $incident_report->status == 1 ? 'Active' : 'In-Active',
                    'created_by' => getusername($incident_report->created_by),
                    'created_at' => Displaydateformat($incident_report->created_at),
                ];


                $incident_report_details = [
                    'sr_no' => $incident_report->sr_no,
                    'incident_date_time' => Displaydatetimeformat($incident_report->incident_date_time),
                    'company' => getCompanyname($incident_report->company_id),
                    'location' => $incident_report->location_name,
                    'unit' => getUnitname($incident_report->unit_id),
                    'shift' => $incident_report->shift,
                    'exact_location' => $incident_report->exact_location,
                    'iir_type' => $incident_report->incident_type_name,
                    'brief_description' => $incident_report->brief_description,
                    'existing_evidence' => $existing_evidenceimages,
                    'immediate_action_taken' => $incident_report->immediate_action_taken,
                    'anyone_injured_person' => $incident_report->anyone_injured == 1 ? 'Yes' : 'No',
                ];


                $injuryDetailList = [];
                if ($incident_report->anyone_injured == 1) {
                    foreach ($injury_details as $injury) {
                        $injury_person_name = in_array($injury->injury_person_type, [1, 2]) ? $injury->emp_name : $injury->injury_person_name;
                        $imgMapDataDecoded = json_decode($injury->imgMapdata, true);

                        $description = "No data available";
                        if (isset($imgMapDataDecoded['map']['total']) && is_array($imgMapDataDecoded['map']['total'])) {
                            $descParts = [];
                            foreach ($imgMapDataDecoded['map']['total'] as $key => $value) {
                                $descParts[] = ucfirst($key) . ': ' . $value;
                            }
                            $description = implode(', ', $descParts);
                        }

                        $injuryDetailList[] = [
                            'id' => $injury->id,
                            'injury_person_type' => $injury->injury_person_type == 1 ? 'Employee' : ($injury->injury_person_type == 2 ? 'Worker' : 'Others'),
                            'injury_person_name' => $injury_person_name,
                            'injury_person_emp_id' => $injury->emp_id,
                            'injury_person_designation' => $injury->injury_person_designation,
                            'injury_person_department' => $injury->injury_person_department_id,
                            'nature_of_injury' => $injury->nature_of_injury == 1 ? 'Major' : ($injury->nature_of_injury == 2 ? 'Minor' : 'Fatal'),
                            'body_part_image' => admin_url('storage/app/public/uploads/' . $injury->body_part_image),
                            'description' => $description,
                        ];
                    }
                }

                $accelerating_incident_investigations = [];
                if ($getEHSReview) {
                    $accelerating_incident_investigations = [
                        'id' => $getEHSReview->id,
                        'reviewer_name' => $getEHSReview->reviewer_name ?? '',
                        'date' => Displaydateformat($getEHSReview->date) ?? '',
                        'target_date' => Displaydateformat($getEHSReview->target_date) ?? '', 
                        'team_member_names' => $getEHSReview->team_member_names,
                        'investigation_reported_prepared_by' => getUsername($getEHSReview->investigation_reported_by),
                        'remark' => $getEHSReview->remark,
                    ];
                }

                $damageTypes = [
                    1 => 'Man',
                    2 => 'Machine',
                    3 => 'Materials',
                ];
                $damagedItems = explode(',', $getInvestigation->anything_damaged);
                $damagedLabels = array_map(function ($item) use ($damageTypes) {
                    return $damageTypes[$item] ?? 'NA';
                }, $damagedItems);

                $remark = $getInvestigation->risk_analysis == 2 ? $getInvestigation->risk_analysis_remark : null;

                $why_why_analysis = [];
                if ($getInvestigation->root_cause_analysis == 1) {
                    foreach ($getwhywhy as $item) {
                        $why_why_analysis[] = [
                            'why_1' => $item->why_1,
                            'why_2' => $item->why_2,
                            'why_3' => $item->why_3,
                            'why_4' => $item->why_4,
                            'why_5' => $item->why_5,
                        ];
                    }
                }

                $fishboneDecodedData = [];
                if ($getInvestigation->root_cause_analysis == 2 && $getfishbone->first()) {
                    $decoded = json_decode($getfishbone->first()->fishbone, true);
                    if (is_array($decoded)) {
                        $fishboneDecodedData = $decoded;
                    }
                }
                if ($getInvestigation) {
                    $Investigation = [
                        'name_of_the_witness' => $getInvestigation->witness_name,
                        'was_anything_damaged' => implode(', ', $damagedLabels),
                        'prca' => $getInvestigation->root_cause_analysis == 1 ? 'Why Why Analysis' : ($getInvestigation->root_cause_analysis == 2 ? 'Fish Bone Analysis' : 'NA'),
                        'remark' => $getInvestigation->remark,
                        'investigation_submission_date' => Displaydateformat($getInvestigation->investigation_date),
                        'investigation_submission_time' => $getInvestigation->investigation_time,
                        'risk_analysis' => $getInvestigation->risk_analysis == 1 ? 'Yes' : 'No',
                        'risk_analysis_remark' => $remark,
                        'why_why_analysis' => $why_why_analysis,
                        'fishboneData' => $fishboneDecodedData,
                        'corrective_preventive_action' => $getInvestigation->corrective_preventive_action,
                    ];
                }


                if ($rcpa) {
                    $rcpaList = [
                        'rcpa_id' => $rcpa->rcpa_id,
                        'rcpa' => $rcpa->rcpa,
                        'responsibility' => getUsername($rcpa->responsibility),
                        'timeline' => DisplayDateformat($rcpa->timeline),
                        'status' =>  $rcpa->capa_status == 1 ? 'Open' : ($rcpa->capa_status == 2 ? 'In Progress' : 'Closed'),
                        'remark' => $rcpa->remark,
                    ];
                }



                $ua_uc_labels = [];

                if ($incident_report->ua_uc_yes_no == 1) {
                    $ua_uc_values = explode(',', $incident_report->ua_or_uc);

                    $ua_uc_map = [
                        1 => 'Unsafe Act',
                        2 => 'Unsafe Condition',
                        3 => 'Natural Causes',
                    ];

                    foreach ($ua_uc_values as $value) {
                        $value = (int)trim($value); // Convert to integer and trim whitespace
                        if (isset($ua_uc_map[$value])) {
                            $ua_uc_labels[] = $ua_uc_map[$value];
                        }
                    }
                }

                $recommended_causesList = [
                    'uauc' => $incident_report->ua_uc_yes_no == 1 ? 'Yes' : 'No',
                    'ua/uc' => $ua_uc_labels,
                ];
                if ($getrisklevel) {
                    $risk_levelList = [
                        'risk_level' => $getrisklevel->risk_level == 1 ? 'Low' : ($getrisklevel->risk_level == 2 ? 'Medium' : 'High'),
                        'description_capa' => $getrisklevel->description_ca,
                    ];
                }



                $action_submission_evidenceimages = [];
                if (!$capaEvidence->isEmpty()) {
                    foreach ($capaEvidence as $key => $capaEvidence) {
                        $action_submission_evidenceimages[] = url($capaEvidence->file_path);
                    }
                }

                $action_submissionList = [];
                if ($rcpa) {
                    $action_submissionList = [
                        'submission_by' => getUsername($rcpa->action_submission_by),
                        'date' => Displaydateformat($rcpa->action_submission_date),
                        'existing_evidence' => $action_submission_evidenceimages,
                        'action_taken' => $rcpa->action_submission_description,
                    ];
                }

                $ehs_approval = [];
                if ($getEHSApprovalincident) {
                    $ehs_approval = [
                        'approval_by' => $getEHSApprovalincident->reviewer_name,
                        'date' => Displaydateformat($getEHSApprovalincident->date),
                        'remark' => $getEHSApprovalincident->remark,
                    ];
                }



                return response()->json([
                    'success' => true,
                    'data' => [
                        'incident_reported_by' => $incident_reported_by,
                        'incident_report_details' => $incident_report_details,
                        'injury_details' => $injuryDetailList,
                        'accelerating_incident_investigations' => $accelerating_incident_investigations,
                        'investigation' => $Investigation,
                        'rcpa' => $rcpaList,
                        'main_root_cause' =>  $getInvestigation->main_root_cause ?? '',
                        'leading_factors' =>  $getInvestigation->leading_factors == 1 ? 'Human Factor' : ' System Factor',
                        'recommended_causes' => $recommended_causesList,
                        'risk_level' => $risk_levelList,
                        'action_submission' => $action_submissionList,
                        'ehs_approval' => $ehs_approval,
                    ]
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // public function generate()
    // {
    //     $count = IncidentBodyParts::withoutGlobalScopes()->count() + 1;

    //     $randomId = 'INCIDENTBODY-' . getautogen($count);

    //     return response()->json([
    //         'status' => true,
    //         'randomId' => $randomId
    //     ]);
    // }
    public function generate()
    {
        $randomID = getsequence('IncidentRandomID');

        $maxRowId = IncidentBodyParts::withoutGlobalScopes()->max('row_id');
        $nextRowId = $maxRowId ? ($maxRowId + 1) : 1;

        return response()->json([
            'status'    => true,
            'randomId'  => $randomID,
            'rowId'     => $nextRowId,
            'message'   => 'New random id generated.'
        ]);
    }
    public function getSavedOrNot(Request $request)
    {
        $isSaved = IncidentBodyParts::where('random_id', $request->random_id)
            ->where('row_id', $request->row_id)
            ->value('is_saved');

        return response()->json([
            'status'   => true,
            'is_saved' => $isSaved ?? 0,
        ]);
    }
}
