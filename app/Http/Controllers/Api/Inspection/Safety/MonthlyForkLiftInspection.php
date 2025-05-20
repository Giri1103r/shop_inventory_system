<?php

namespace App\Http\Controllers\Api\Inspection\Safety;

use Exception;
use App\Models\UploadLog;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use App\Mail\Inspection\Safety\SafetyInspection;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Master\Frequency;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\Safety\SafetyStatusLog;
use App\Models\Inspection\Safety\MonthlyForkLiftInspection as SafetyMonthlyForkLiftInspection;
use App\Models\Inspection\Safety\SignatureUpload;
use App\Models\Master\ForkLiftType;

class MonthlyForkLiftInspection extends BaseController
{
    private $unit;
    private $uploadlog;
    private $forklift_inspection;
    private $document_reference;
    private $statusLog;
    private $signature;
    private $forklift_type;
    private $frequency;
    private $shift;

    public function __construct()
    {

        $this->unit = new Unit();
        $this->uploadlog = new UploadLog();
        $this->forklift_inspection = new SafetyMonthlyForkLiftInspection();
        $this->document_reference = new InspectionStaticDocno();
        $this->statusLog = new SafetyStatusLog();
        $this->signature = new SignatureUpload();
        $this->forklift_type = new ForkLiftType();
        $this->frequency = new Frequency();
        $this->shift = new Shift();
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
            $query = SafetyMonthlyForkLiftInspection::select(
                'inspection_forklift_inpsection_monthly.*',
                'inspection_shift_option.*',
                'masters_unit.*',
                'masters_location.*',
                'inspection_frequency_option.*',
                'inspection_forklift_inpsection_monthly.id as inspection_id',
                'inspection_forklift_inpsection_monthly.created_at as inspection_created_at'
            )
                ->leftJoin('masters_location', 'inspection_forklift_inpsection_monthly.location', '=', 'masters_location.id')
                ->leftJoin('inspection_shift_option', 'inspection_forklift_inpsection_monthly.shift', '=', 'inspection_shift_option.id')
                ->leftJoin('masters_unit', 'inspection_forklift_inpsection_monthly.unit', '=', 'masters_unit.id')
                ->leftJoin('inspection_frequency_option', 'inspection_forklift_inpsection_monthly.frequency', '=', 'inspection_frequency_option.id')
                ->leftJoin('inspection_static_docno', 'inspection_forklift_inpsection_monthly.document_reference_id', '=', 'inspection_static_docno.id');

            $org_total_counts = $query->count();

            if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_OFFICER) || CheckUserRole(ROLE_L1_MANAGER) || CheckUserRole(ROLE_L2_MANAGER)) {
            } else if (CheckUserRole(ROLE_FIRE_ASSOCIATES)) {
                $query->where('inspection_forklift_inpsection_monthly.created_by', Auth::id());
            }

            if (!empty($search)) {
                $searchDate = DBdateformat($search);
                $query->where(function ($query) use ($searchDate) {
                    $query->orWhere('masters_unit.unit_name', $searchDate)
                        ->orWhere('masters_location.location_name', $searchDate)
                        ->orWhere('inspection_shift_option.shift', $searchDate)
                        ->orWhere('inspection_frequency_option.frequency_name', $searchDate);
                });
            }

            $query_array = $query->orderBy('inspection_forklift_inpsection_monthly.id', 'DESC')->paginate($request->input('per_page', 10));

            $inspection_list = $query_array->toArray();

            if (empty($inspection_list['data'])) {
                return $this->sendError('No records found.', [], 404);
            }

            $data_array = [];
            foreach ($inspection_list['data'] as $datas) {
                $data = [];
                $data['id'] = $datas['id'] ?? '';
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


    public function view(Request $request)
    {
        try {
            if (Auth::user()) {
                $id = $request->id;
                $inspection = $this->forklift_inspection
                    ->leftJoin('inspection_static_docno', 'inspection_forklift_inpsection_monthly.document_reference_id', '=', 'inspection_static_docno.id')
                    ->where('inspection_forklift_inpsection_monthly.id', $id)
                    ->select(
                        'inspection_forklift_inpsection_monthly.*',
                        'inspection_static_docno.*',
                        'inspection_forklift_inpsection_monthly.id as inspection_id',
                        'inspection_forklift_inpsection_monthly.created_by as inspection_created_by',
                        'inspection_forklift_inpsection_monthly.updated_at as inspection_updated_at',
                    )
                    ->first();

                $inspection_responses = json_decode($inspection->responses, true);

                $responses = [];
                foreach ($inspection_responses as $inspection_response) {
                    $data = [
                        'question_name' => GetChecklistTypeDate($inspection_response['question_id']),
                        'answer' => $inspection_response['answer'],
                        'remarks' => $inspection_response['remarks'],
                    ];
                    $responses[] = $data;
                }
                $signature = GetSafetySignature(
                    $inspection->inspection_created_by,
                    $inspection->inspection_id,
                    MONTHLY_FORKLIFT_INSPECTION,
                );

                $statuslog = $this->statusLog->selectOne($id, MONTHLY_FORKLIFT_INSPECTION);

                if (count($statuslog) > 0) {
                    foreach ($statuslog as $key => $status) {
                        $statuslog[$key]->from_status = getInspectionStatus($status->from_status);
                        $statuslog[$key]->to_status = getInspectionStatus($status->to_status);
                        $statuslog[$key]->remarks = getInspectionStatus($status->remarks);
                        $statuslog[$key]->approved_by = getUsername($status->approved_by);
                        $statuslog[$key]->created_by = getUsername($status->created_by);
                        $statuslog[$key]->created_at = Displaydateformat($status->created_at);
                    }
                } else {
                    $statuslog = null;
                }

                $inspection_details = [
                    'id' => $inspection->inspection_id,
                    'issue_date' => Displaydateformat($inspection->issue_date),
                    'doc_no' => $inspection->doc_no,
                    'rev_dt' => $inspection->rev_dt,
                    'date_of_inspection' => Displaydateformat($inspection->date_of_inspection),
                    'next_due' => Displaydateformat($inspection->next_due),
                    'location' => getLocationname($inspection->location),
                    'shift' => getShiftname($inspection->shift),
                    'unit' => getUnitname($inspection->unit),
                    'frequency' => getFrequencyname($inspection->frequency),
                    'identification_no' => $inspection->identification_no,
                    'forklift_type' => GetForkLiftType($inspection->forklift_type),
                    'capacity' => $inspection->capacity,
                    'remarks' => $inspection->remarks ?? '',
                    'responses' => $responses,
                    'inspection_creator_signature' => $signature,
                ];

                if (!empty($inspection->verified_by)) {
                    $updated_time = GetSafetyUpdatedTime(
                        $inspection->verified_by,
                        $inspection->inspection_id,
                        MONTHLY_FORKLIFT_INSPECTION,
                        WAITING_FOR_EHS_OFFICER_VERIFICATION,
                    );

                    $verifier_signature = GetSafetySignature(
                        $inspection->verified_by,
                        $inspection->inspection_id,
                        MONTHLY_FORKLIFT_INSPECTION,
                    );

                    $inspection_details += [
                        'inspection_verified_by' => getUsername($inspection->verified_by),
                        'inspection_verified_at' => Displaydateformat($updated_time->created_at),
                        'verifier_signature' => $verifier_signature,
                        'capa_recomendation' => !empty($inspection->capa_recomendation) ? $inspection->capa_recomendation : $inspection->remarks,
                    ];
                }

                if (!empty($inspection->capa_remarks) || !empty($inspection->capa_ehs_remarks)) {
                    $capa_updated_time = GetSafetyUpdatedTime(
                        $inspection->inspection_created_by,
                        $inspection->inspection_id,
                        MONTHLY_FORKLIFT_INSPECTION,
                        WAITING_FOR_CAPA_ACTION,
                    );

                    $inspection_details += [
                        'capa_created_by' => getUsername($inspection->inspection_created_by),
                        'capa_created_at' => Displaydateformat($capa_updated_time->created_at),
                        'capa_remarks' => $inspection->capa_remarks ?? '',
                        'capa_ehs_remarks' => $inspection->capa_ehs_remarks ?? '',
                        'capa_creator_signature' => $signature,
                    ];
                }

                if (!empty($inspection->l1_manager_verified_by)) {
                    $l1_signature = GetSafetySignature(
                        $inspection->l1_manager_verified_by,
                        $inspection->inspection_id,
                        MONTHLY_FORKLIFT_INSPECTION,
                    );

                    $l1_updated_time = GetSafetyUpdatedTime(
                        $inspection->l1_manager_verified_by,
                        $inspection->inspection_id,
                        MONTHLY_FORKLIFT_INSPECTION,
                        WAITING_FOR_L1_VERIFICATION,
                    );

                    $inspection_details += [
                        'l1_verified_by' => getUsername($inspection->l1_manager_verified_by),
                        'l1_remarks' => $inspection->level_one_manager_remarks ?? '',
                        'l1_updated_time' => Displaydateformat($l1_updated_time->created_at),
                        'l1_signature' => $l1_signature,
                    ];
                }

                if ($inspection->inspection_status == INSPECTION_APPROVED && !empty($inspection->approved_by)) {
                    $l2_signature = GetSafetySignature(
                        $inspection->l2_manager_verified_by,
                        $inspection->inspection_id,
                        MONTHLY_FORKLIFT_INSPECTION,
                    );

                    $inspection_details += [
                        'l2_verified_by' => getUsername($inspection->l2_manager_verified_by),
                        'l2_remarks' => $inspection->level_two_manager_remarks ?? '',
                        'l2_updated_time' => Displaydateformat($inspection->inspection_updated_at),
                        'l2_signature' => $l2_signature ?? '',
                    ];

                    $approver_signature = GetSafetySignature(
                        $inspection->approved_by,
                        $inspection->inspection_id,
                        MONTHLY_FORKLIFT_INSPECTION,
                    );

                    $inspection_details += [
                        'approved_by' => getUsername($inspection->approved_by),
                        'approved_remarks' => $inspection->remarks ?? '',
                        'approved_updated_time' => Displaydateformat($inspection->inspection_updated_at),
                        'approved_signature' => $approver_signature ?? '',
                    ];
                }

                $success = [
                    'id' => $inspection->inspection_id,
                    'inspection_details' => $inspection_details,
                    '$statuslog' => $statuslog,
                ];
                return $this->sendResponse($success, 'Inspection Details');
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
                'inspection_date' => 'required',
                'location_id' => 'required',
                'shift_id' => 'required',
                'next_due' => 'required',
                'unit_id' => 'required',
                'frequency_id' => 'required',
                'identification_no' => 'required',
                'forklift_type' => 'required',
                'capacity' => 'required',
            ];

            $messages = [
                'doc_no.required' => 'Document number is required.',
                'inspection_date.required' => 'Inspection  Date is Required',
                'location_id.required' => 'Location is Required',
                'shift_id.required' => 'Shift is Required',
                'next_due.required' => 'Next due date is Required',
                'unit_id.required' => 'Unit is Required',
                'frequency_id.required' => 'Frequency is Required',
                'forklift_type.required' => 'Forklift Type is Required',
                'capacity.required' => 'Capacity is Required',
                'identification_no.required' => 'Identification Number is Required',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }

            $forklift_inspection = $this->forklift_inspection->store_api();
            $id = $forklift_inspection->id;
            $signature_update = $this->signature->signatureUpload_api(MONTHLY_FORKLIFT_INSPECTION, $forklift_inspection->id);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'Monthly Forklift Inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 2,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Monthly ForkLift Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $forklift_inspection->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($forklift_inspection->id) . '/ehs'),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Monthly ForkLift Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($id) . '/ehs');
                $details = array(
                    'safety_type' => 'Monthly Forklift Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $forklift_inspection
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }

            $insert_array = [
                'type' => MONTHLY_FORKLIFT_INSPECTION,
                'inspection_id' => $forklift_inspection->id,
                'from_status' => 0,
                'to_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'created_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);

            $success = [
                "success" => $forklift_inspection,
            ];
            return $this->sendResponse($success, 'Inspection Created');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    public function forklift_type()
    {
        try {
            $forklift_type = $this->forklift_type->getForkLift();
            $success = array(
                'forklift_types' => $forklift_type,
            );
            return $this->sendResponse($success, 'Forklift Type');
        } catch (Exception $ex) {

            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
    public function frequencyName()
    {
        try {
            $frquencies = $this->frequency->getFrequency();
            $success = array(
                'frquencies' => $frquencies,
            );
            return $this->sendResponse($success, 'Frequency');
        } catch (Exception $ex) {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
    public function shift()
    {
        try {
            $shifts = $this->shift->getShiftname();
            $success = array(
                'shifts' => $shifts,
            );
            return $this->sendResponse($success, 'Shift');
        } catch (Exception $ex) {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
