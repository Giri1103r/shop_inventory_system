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
use App\Models\Inspection\InspectionStaticDocno;
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

    public function __construct()
    {
        $this->safety_walk = new SafetySafetyWalkObservation();
        $this->observation_details = new SafetyWalkObservationDetails();
        $this->shift = new Shift();
        $this->unit = new Unit();
        $this->location = new Location();
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
            $query = $this->safety_walk
                ->select(
                    'inspection_safety_walk_observation.*',
                    'inspection_shift_option.*',
                    'masters_unit.*',
                    'inspection_safety_walk_observation.id as inspection_id',
                    'inspection_safety_walk_observation.created_at as inspection_created_at'
                )
                ->leftJoin('inspection_shift_option', 'inspection_safety_walk_observation.shift_id', '=', 'inspection_shift_option.id')
                ->leftJoin('masters_unit', 'inspection_safety_walk_observation.unit', '=', 'masters_unit.id')
                ->leftJoin('inspection_static_docno', 'inspection_safety_walk_observation.document_reference_id', '=', 'inspection_static_docno.id');

            $org_total_counts = $query->count();

            $user = Auth::user();
            $userRole = string_to_array($user->role);
            if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)) {
            } elseif (in_array(ROLE_INSPECTION_CREATOR, $userRole)) {
                $query->where('inspection_safety_walk_observation.created_by', Auth::user()->id);
            }

            if (!empty($search)) {
                $query->where(function ($query) use ($search) {
                    $query->orWhereRaw("DATE_FORMAT(inspection_safety_walk_observation.date, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
                    $query->orWhereRaw('shift LIKE ?', ["%{$search}%"]);
                    $query->orWhereRaw('unit_name LIKE ?', ["%{$search}%"]);
                    $query->orWhereRaw('month LIKE ?', ["%{$search}%"]);
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
                $data['id'] = $datas['id'] ?? '';
                $data['shift_name'] = $datas['shift'] ?? '';
                $data['unit_name'] = $datas['unit_name'] ?? '';
                $data['month'] = $datas['month'] ?? '';
                $data['date_of_inspection'] = Displaydateformat($datas['date']);
                $data['observation_status'] = getObservationStatus($datas['observation_status'] ?? '');
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

            return $this->sendResponse($success, 'Safety Walk Observation Details');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }


    public function view(Request $request)
    {
        try {
            if (Auth::user()) {
                $id = $request->id;
                $inspections = $this->safety_walk
                    ->where('inspection_safety_walk_observation.id', $id)
                    ->leftJoin(
                        'inspection_static_docno',
                        'inspection_safety_walk_observation.document_reference_id',
                        '=',
                        'inspection_static_docno.id'
                    )
                    ->select(
                        'inspection_safety_walk_observation.*',
                        'inspection_static_docno.*',
                        'inspection_safety_walk_observation.id as inspection_id',
                        'inspection_safety_walk_observation.created_by as inspection_created_by',
                        'inspection_safety_walk_observation.updated_at as inspection_updated_at',
                        'inspection_safety_walk_observation.updated_by as inspection_updated_by',
                    )
                    ->first();
                $inspection_details = $this->observation_details->GetDetails($id);


                $signature = GetSafetySignature(
                    $inspections->inspection_created_by,
                    $inspections->inspection_id,
                    SAFETY_WALK_OBSERVATION,
                );


                $inspection = [
                    'document_no' => $inspections->doc_no,
                    'issue_date' => Displaydateformat($inspections->issue_date),
                    'date_of_inspection' => Displaydateformat($inspections->date),
                    'rev_dt' => ($inspections->rev_dt),
                    'shift_name' => getShiftname($inspections->shift_id),
                    'unit_name' => getUnitname($inspections->unit),
                    'month' => ($inspections->month),
                    'safety_walk_taken_by' => getUsername($inspections->safety_walk_taken_by),
                    'signature' => admin_url($signature),
                ];

                $inspection_details_array = [];
                foreach ($inspection_details as $inspection) {
                    $images = GetSafetyWalkImage($inspection->id);
                    $inspection_array = [
                        'id' => $inspection->id,
                        'location_name' => getLocationname($inspection->location),
                        'observation_date' => Displaydateformat($inspection->observation_date),
                        'observation' => $inspection->observation,
                        'recomended_action' => ($inspection->recomended_action),
                        'responsibility' => getUsername($inspection->responsibility),
                        'date_of_compliance' => Displaydateformat($inspection->date_of_compliance),
                        'observation_status' => ($inspection->observation_status == "1" ? 'Active' : 'InActive'),
                        'remarks' => $inspection->remarks,
                        'image' =>  admin_url($images),
                    ];
                    $inspection_details_array[] = $inspection_array;
                }

                $approval_array = null;
                if ($inspections->observation_status != OBSERVATION_PENDING) {
                    $signature = GetSafetySignature(
                        $inspections->inspection_updated_by,
                        $inspections->inspection_id,
                        SAFETY_WALK_OBSERVATION,
                    );
                    $approval_array = [
                        'approval_updated_by' => getUsername($inspections->inspection_updated_by),
                        'approval_updated_at' => Displaydateformat($inspections->inspection_updated_at),
                        'approver_signature' => admin_url($signature),
                        'approval_remarks' => $inspections->approval_remarks,
                    ];
                }

                $success = [
                    'id' => $inspections->inspection_id,
                    'inspection' => $inspections,
                    'inspection_details' => $inspection_details,
                    'approvals' => $approval_array,
                ];
                return $this->sendResponse($success, 'Safety Walk Observation Details');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }


    public function store(Request $request)
    {

        try {
            $rules = [
                'doc_no' => 'required',
                'issue_date' => 'required',
                'inspection_date' => 'required',
                'shift_id' => 'required',
                'month' => 'required',
                'unit' => 'required',
                'safety_walk_taken_by' => 'required',
                'unit' => 'required',
                'location.*' => 'required',
                'date_of_observation.*' => 'required',
                'observation.*' => 'required',
                'checklist_file.*' => 'required',
                'recomended_action.*' => 'required',
                'date_of_compliance.*' => 'required',
                'observation_status.*' => 'required',
                'remarks.*' => 'required',
                'emp_id.*' => 'required',


            ];

            $messages = [
                'doc_no.required' => 'Document number is required.',
                'issue_date.required' => 'Issue date is required.',
                'inspection_date.required' => 'Inspection date is required.',
                'shift_id.required' => 'Shift ID is required.',
                'month.required' => 'Month is required.',
                'unit.required' => 'Unit is required.',
                'safety_walk_taken_by.required' => 'Safety walk taken by is required.',
                'unit.*.required' => 'Unit is required.',
                'location.*.required' => 'Location is required.',
                'date_of_observation.*.required' => 'Date of observation is required.',
                'observation.*.required' => 'Observation is required.',
                'checklist_file.*.required' => 'Image is required.',
                'recomended_action.*.required' => 'Recommended action is required.',
                'date_of_compliance.*.required' => 'Date of compliance is required.',
                'observation_status.*.required' => 'Observation status is required.',
                'remarks.*.required' => 'Remarks are required.',
                'emp_id.*.required' => 'Employee ID is required.',

            ];


            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }
            $safety_walk = $this->safety_walk->store_api();
            $id = $safety_walk->id;
            $forklift_observation_details = $this->observation_details->store_api($safety_walk->id);
            $signature_update = $this->signature->signatureUpload_api(SAFETY_WALK_OBSERVATION, $safety_walk->id);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'Safety Walk Observation';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 7,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Safety Walk Observation - Observation Has been Created",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $safety_walk->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safety/safety-walk-observation/approval/' . encryptId($safety_walk->id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'SAFETY INSPECTION - Observation has been Created';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('safety/safety-walk-observation/approval/' . encryptId($safety_walk->id));
                $details = array(
                    'safety_type' => 'Safety Walk Observation',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $safety_walk
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }

            $success = [
                "success" => $safety_walk,
            ];
            return $this->sendResponse($success, 'Safety Walk Inspection Created');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
