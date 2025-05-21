<?php

namespace App\Http\Controllers\Api\Inspection\Safety;

use App\Http\Controllers\Api\BaseController;
use Exception;
use App\Models\UploadLog;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\Inspection\Safety\SafetyInspection;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Safety\SafetyStatusLog;
use App\Models\Inspection\Safety\SignatureUpload;
use App\Models\Inspection\Safety\ForkLiftInspection as SafetyForkLiftInspection;
use App\Models\Inspection\Safety\ForkliftInspectionDetails;

class ForkliftInspection extends BaseController
{
    private $unit;
    private $uploadlog;
    private $forklift_inspection;
    private $forklift_inspection_details;
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
        $this->forklift_inspection = new SafetyForkLiftInspection();
        $this->forklift_inspection_details = new ForkliftInspectionDetails();
        $this->document_reference = new InspectionStaticDocno();
        $this->statusLog = new SafetyStatusLog();
        $this->signature = new SignatureUpload();
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
            $query = SafetyForkLiftInspection::select(
                'inspection_safety_forklift_inspection.*',
            );
            $org_total_counts = $query->count();

            if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_HEAD)) {
            } else if (CheckUserRole(ROLE_INSPECTION_CREATOR)) {
                $query->where('inspection_safety_forklift_inspection.created_by', Auth::id());
            }

            if (!empty($search)) {
                $searchDate = ($search);
                $query->where(function ($query) use ($searchDate) {
                    $query->orWhereRaw("DATE_FORMAT(inspection_safety_forklift_inspection.inspection_date, '%d-%m-%Y') LIKE ?", ["%{$searchDate}%"]);
                });
            }

            $query_array = $query->orderBy('inspection_safety_forklift_inspection.id', 'DESC')->paginate($request->input('per_page', 10));

            $inspection_list = $query_array->toArray();

            if (empty($inspection_list['data'])) {
                return $this->sendError('No records found.', [], 404);
            }

            $data_array = [];
            foreach ($inspection_list['data'] as $datas) {
                $data = [];
                $data['id'] = $datas['id'] ?? '';
                $data['date_of_inspection'] = Displaydateformat($datas['inspection_date']);
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
                $inspections = $this->forklift_inspection
                    ->where('inspection_safety_forklift_inspection.id', $id)
                    ->leftJoin(
                        'inspection_static_docno',
                        'inspection_safety_forklift_inspection.document_reference_id',
                        '=',
                        'inspection_static_docno.id'
                    )
                    ->select(
                        'inspection_safety_forklift_inspection.*',
                        'inspection_static_docno.*',
                        'inspection_safety_forklift_inspection.id as inspection_id',
                        'inspection_safety_forklift_inspection.created_by as inspection_created_by',
                        'inspection_safety_forklift_inspection.updated_at as inspection_updated_at',
                        'inspection_safety_forklift_inspection.updated_by as inspection_updated_by',
                    )
                    ->first();
                $inspection_details = $this->forklift_inspection_details->GetDetails($id);


                $signature = GetSafetySignature(
                    $inspections->inspection_created_by,
                    $inspections->inspection_id,
                    FORKLIFT_INSPECTION,
                );

                $inspection = [
                    'document_no' => $inspections->doc_no,
                    'issue_date' => Displaydateformat($inspections->issue_date),
                    'date_of_inspection' => Displaydateformat($inspections->inspection_date),
                    'rev_dt' => Displaydateformat($inspections->rev_dt),
                    'signature' => $signature,
                ];
                $inspection_details_array = [];
                foreach ($inspection_details as $inspection) {
                    $inspection_array = [
                        'id' => $inspection->inspection_id,
                        'department_name' => getDepartment($inspection->department_id),
                        'unit_name' => getUnitname($inspection->unit_id),
                        'identification_no' => $inspection->identification_no,
                        'observation' => ($inspection->observation),
                        'correction_preventive_action' => ($inspection->correction_preventive_action),
                        'responsibility' => getUsername($inspection->responsibility),
                        'date_of_compliance' => Displaydateformat($inspection->date_of_compliance),
                        'observation_status' => ($inspection->observation_status == "1" ? 'Active' : 'InActive'),
                        'remarks' => $inspection->remarks,
                    ];
                    $inspection_details_array[] = $inspection_array;
                }

                $approval_array = null;
                if ($inspections->observation_status != OBSERVATION_PENDING) {
                    $signature = GetSafetySignature(
                        $inspections->inspection_updated_by,
                        $inspections->inspection_id,
                        FORKLIFT_INSPECTION,
                    );
                    $approval_array = [
                        'approval_updated_by' => getUsername($inspections->inspection_updated_by),
                        'approval_updated_at' => Displaydateformat($inspections->inspection_updated_at),
                        'approver_signature' => $signature,
                        'approval_remarks' => $inspections->approval_remarks,
                    ];
                }

                $success = [
                    'id' => $inspections->inspection_id,
                    'inspection' => $inspections,
                    'inspection_details' => $inspection_details,
                    'approvals' => $approval_array,
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
            $signature_update = $this->signature->signatureUpload_api(FORKLIFT_INSPECTION, $forklift_inspection->id);

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
                'type' => FORKLIFT_INSPECTION,
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
}
