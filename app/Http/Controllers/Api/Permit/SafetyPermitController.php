<?php

namespace App\Http\Controllers\Api\Permit;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Permit\SafetyApproveReject;
use App\Models\Permit\SafetyPermit;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Permit\SafetyPermitExtension;
use App\Models\Permit\Statuslog;

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
            $search = '';
            $empid = Auth::id();

            $user = Auth::user();
            $empId = $user->employee_id;
            $userRole = $user->role;
            $unit_id = $user->unit_id;
            $empid = $user->id;
            $userRole = string_to_array($userRole);
            if (isAdmin()) {
                $safety_permit_array = SafetyPermit::select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status');
            } elseif (in_array(ROLE_EHS_OFFICER, $userRole)) {
                $safety_permit_array = SafetyPermit::select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status');
            } elseif (in_array(ROLE_PLANT_HEAD, $userRole)) {
                $safety_permit_array = SafetyPermit::select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status')->where('ptw_safety.unit_id', $unit_id);
            } elseif (in_array(ROLE_EHS_HEAD, $userRole)) {
                $safety_permit_array = SafetyPermit::select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status');
            } else {
                $safety_permit_array = SafetyPermit::select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status')->where('ptw_safety.created_by', $empid);
            }
            if (!empty($request->search['value'])) {
                $search = $request->search['value'];
                $safety_permit_array = $safety_permit_array->where(function ($query) use ($search) {
                    $query->orWhereRaw('permit_id LIKE ?', ["%{$search}%"]);
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
                $data['unit_id'] = $listdata['unit_id'] ?? '';
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

                $stateIsolationLoto = json_decode($safetypermit->state_isolation_loto, true);
                $state_of_isolation = [];

                $items = ['Air', 'Gas', 'Electrical', 'Water/Liquid'];

                $state_of_isolation = [];
                $other_if_any = [];

                foreach ($items as $item) {
                    $key = strtolower(str_replace('/', '_', $item));

                    $state_of_isolation[$key] = [
                        'image' => asset('assets/images/safetypermit/person.png'),
                        'name' => $item,
                        'checked' => in_array($item, $stateIsolationLoto) ? 'Yes' : 'No'

                    ];
                    $isolationpanel = [
                        'image' => asset('assets/images/safetypermit/person.png'),
                        'name' => "Isolation fire panel",
                        'checked' => $safetypermit->isolationpanel_checkbox ? 'Yes' : 'No'

                    ];
                    $isolationpaneldescription = [

                        'name' => "Isolation fire panel Description",
                        'checked' => $safetypermit->isolationpanel_description

                    ];
                }

                $success = [
                    'id' => $safetypermit->id,
                    'permit_id' => $safetypermit->permit_id,
                    'date' => $safetypermit->date,
                    'time_from' => $safetypermit->time_from,
                    'time_to' => $safetypermit->time_to,
                    'unit_id' => getUnitname($safetypermit->unit_id),
                    'exact_location_job' => $safetypermit->exact_location_job,
                    'job_location_area' => $safetypermit->job_location_area,
                    'created_by' => getusername($safetypermit->created_by),
                    'created_at' => Displaydateformat($safetypermit->created_at),

                    'type_of_work' => [
                        'sub_permit' => $sub_permits,

                        'job_description' => $safetypermit->job_description,

                        'shut_down' => [
                            'images' => asset('assets/images/safetypermit/power-off.png'),
                            'name' => "Shut Down Required",
                            'shutdown_req_checked' => $safetypermit->shutdown_req == 1 ? 'Yes' : 'No',
                        ],

                        'shut_down_takenby' => [
                            'images' => asset('assets/images/safetypermit/profile.png'),
                            'name' => "Taken By (Name & Department)",
                            'shut_down_takenby' => $safetypermit->shut_down_takenby,
                        ],

                        'loto_req' => [
                            'images' => asset('assets/images/safetypermit/process.png'),
                            'name' => "Isolation/LOTO Required",
                            'loto_req_checked' => $safetypermit->loto_req == 1 ? 'Yes' : 'No',
                        ],

                        'loto_req_takenby' => [
                            'images' => asset('assets/images/safetypermit/profile.png'),
                            'name' => "Taken By (Name & Department)",
                            'loto_takenby' => $safetypermit->loto_takenby,
                        ],

                        'loto_no' => $safetypermit->loto_no,

                        'tagfield' => [
                            'name' => "Tag Field properly",
                            'tagfield_checked' => $safetypermit->tagfield == 1 ? 'Yes' : 'No'
                        ],
                    ],

                    'state_of_isolation' => $state_of_isolation,
                    'confined_space_entry' => [
                        'o2' => [
                            'name' => "O2%",
                            "O2" => $confined_space_entry->o2_percentage,
                        ],
                        'system_isolated' => [
                            'name' => "System Isolated",
                            "system_isolated_checked" => $confined_space_entry->system_isolated  == 1 ? 'Yes' : 'No',
                        ],
                        'rescue_system' => [
                            'name' => "Rescue System Available",
                            "rescue_system_checked" => $confined_space_entry->rescue_system  == 1 ? 'Yes' : 'No',
                        ],
                        'confined_attendant' => [
                            'name' => "Confined Space Attendant",
                            "confined_attendant_checked" => $confined_space_entry->confined_attendant  == 1 ? 'Yes' : 'No',
                        ],
                        'attendant_name' => [
                            'name' => "Attendant Name",
                            "attendant_name" => $confined_space_entry->attendant_name,
                        ],
                        'register_entry_exits' => [
                            'name' => "Register for entry & exits ",
                            "register_entry_exits_checked" => $confined_space_entry->register_entry_exits  == 1 ? 'Yes' : 'No',
                        ],
                        'other_gas' => [
                            'name' => "Any Other Gas / PPM",
                            "other_gas_checked" => $confined_space_entry->other_gas  == 1 ? 'Yes' : 'No',
                        ],
                        'ppm_safe_to_enter' => [
                            'name' => "PPM and is therefore safe to enter from",
                            "ppm_safe_to_enter" => $confined_space_entry->ppm_safe_to_enter ?? 'N/A',
                        ],
                        'to' => [
                            'name' => "PPM and is therefore safe to enter To",
                            "to" => $confined_space_entry->to ?? 'N/A',
                        ],
                    ],
                    'protective_equipments_worn' => [
                        'images' => [
                            "{{ url('public/assets/images/safetypermit/gloves.png') }}",
                            "{{ url('public/assets/images/safetypermit/helmet.png') }}",
                            "{{ url('public/assets/images/safetypermit/shoes.png') }}",
                            "{{ url('public/assets/images/safetypermit/gloves (1).png') }}",
                            "{{ url('public/assets/images/safetypermit/boots (1).png') }}",
                            "{{ url('public/assets/images/safetypermit/boots.png') }}",
                            "{{ url('public/assets/images/safetypermit/safety-goggles.png') }}",
                                 

                        ]
                    ]

                ];

                return $this->sendResponse($success, 'Safety Permit Details');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
            }
        } catch (Exception $ex) {
            dd($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
