<?php

namespace App\Http\Controllers\Api\Inspection\Fire;

use Exception;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Master\Location;
use App\Models\Master\Department;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Inspection\Master\Shift;
use Illuminate\Support\Facades\Validator;
use App\Models\Inspection\Master\Frequency;
use App\Http\Controllers\Api\BaseController;
use App\Mail\Inspection\Fire\FireInspection;
use App\Models\Inspection\Fire\DetectorType;
use App\Models\Inspection\Fire\FireStatusLog;
use App\Models\Inspection\Fire\FireFileUpload;
use App\Mail\Inspection\Safety\SafetyInspection;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Fire\DetectorInspection;
use App\Models\Inspection\Fire\FireSignatureUpload;
use App\Models\Inspection\Fire\FireCheckListFollowUp;
use App\Models\Inspection\Fire\DetectorInspectionDetails;

class DetectorInspectionController extends BaseController
{

    private $detector;
    private $detector_details;
    private $shift;
    private $location;
    private $unit;
    private $frequency;
    private $department;
    private $files;
    private $signature;
    private $statusLog;
    private $checklist_follow;
    private $document_reference;
    private $detector_type;

    public function __construct()
    {
        $this->detector = new DetectorInspection();
        $this->detector_details = new DetectorInspectionDetails();
        $this->department = new Department();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->unit = new Unit();
        $this->frequency = new Frequency();
        $this->files = new FireFileUpload();
        $this->signature = new FireSignatureUpload();
        $this->statusLog = new FireStatusLog();
        $this->checklist_follow = new FireCheckListFollowUp();
        $this->document_reference = new InspectionStaticDocno();
        $this->detector_type = new DetectorType();
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
            $query = $this->detector->select('inspection_fire_detector.*', 'inspection_shift_option.*', 'masters_unit.*', 'masters_location.*', 'inspection_frequency_option.*', 'inspection_fire_detector.id as fire_detector_id', 'inspection_fire_detector.created_by as checked_by')
                ->leftJoin('masters_location', 'inspection_fire_detector.location', '=', 'masters_location.id')
                ->leftJoin('inspection_shift_option', 'inspection_fire_detector.shift', '=', 'inspection_shift_option.id')
                ->leftJoin('masters_unit', 'inspection_fire_detector.unit', '=', 'masters_unit.id')
                ->leftJoin('inspection_frequency_option', 'inspection_fire_detector.frequency', '=', 'inspection_frequency_option.id')
                ->leftJoin('inspection_static_docno', 'inspection_fire_detector.document_reference_id', '=', 'inspection_static_docno.id');


            $org_total_counts = $query->count();

            if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_OFFICER) || CheckUserRole(ROLE_L1_MANAGER) || CheckUserRole(ROLE_L2_MANAGER)) {
            } else if (CheckUserRole(ROLE_FIRE_ASSOCIATES)) {
                $query->where('inspection_fire_detector.created_by', Auth::id());
            }

            if (!empty($search)) {
                $search = ($search);
                $query = $query->where(function ($query) use ($search) {
                    $query->orWhereRaw('masters_location.location_name LIKE "%' . $search . '%"');
                    $query->orWhereRaw('masters_unit.unit_name LIKE "%' . $search . '%"');
                    $query->orWhereRaw('inspection_shift_option.shift LIKE "%' . $search . '%"');
                    $query->orWhereRaw('inspection_frequency_option.frequency_name LIKE "%' . $search . '%"');
                });
            }

            $query_array = $query->orderBy('inspection_fire_detector.id', 'DESC')->paginate($request->input('per_page', 10));

            $inspection_list = $query_array->toArray();

            if (empty($inspection_list['data'])) {
                return $this->sendError('No records found.', [], 404);
            }

            $data_array = [];
            foreach ($inspection_list['data'] as $datas) {
                $data = [];
                $data['id'] = $datas['id'] ?? '';
                $data['date_of_inspection'] = Displaydateformat($datas['date_of_inspection']);
                $data['location_name'] = ($datas['location_name'] ?? '');
                $data['frequency_name'] = ($datas['frequency_name'] ?? '');
                $data['unit_name'] = ($datas['unit_name'] ?? '');
                $data['shift_name'] = ($datas['shift'] ?? '');
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
                $inspection = $this->detector
                    ->leftJoin('inspection_static_docno', 'inspection_fire_detector.document_reference_id', '=', 'inspection_static_docno.id')
                    ->where('inspection_fire_detector.id', $id)
                    ->select(
                        'inspection_fire_detector.*',
                        'inspection_static_docno.*',
                        'inspection_fire_detector.id as inspection_id',
                        'inspection_fire_detector.created_by as inspection_created_by',
                        'inspection_fire_detector.updated_at as inspection_updated_at',
                    )
                    ->first();

                $inspection_details = $this->detector_details->GetDetails($inspection->inspection_id);

                $inspection_details_array = [];
                foreach ($inspection_details as $inspection_detail) {
                    $data = [
                        'department' => getDepartment($inspection_detail->department),
                        'resource_code' => ($inspection_detail->resource_code),
                        'remarks' => ($inspection_detail->remarks),
                        'detector_type' => getDetectorName($inspection_detail->detector_type),
                        'physical_condition' => getPhysicalConditon($inspection_detail->physical_condition),
                        'cable_condition' => getCableCondition($inspection_detail->cable_condition),
                        'response_indicator' => ($inspection_detail->response_indicator == 1 ? 'WORKING' : 'NOT WORKING'),
                        'working_status' => ($inspection_detail->working_status == 1 ? 'OPERATIONAL' : 'NON OPERATIONAL'),
                    ];
                    $inspection_details_array[] = $data;
                }

                $signature = GetFireSignature(
                    $inspection->inspection_created_by,
                    $inspection->inspection_id,
                    DETECTOR_INSPECTION,
                );
                $inspection_image = $this->files->GetFile(DETECTOR_INSPECTION, $inspection->inspection_id);

                $statuslog = $this->statusLog->selectOne($id, DETECTOR_INSPECTION);

                if (count($statuslog) > 0) {
                    foreach ($statuslog as $key => $status) {
                        $statuslog[$key]->from_status = getInspectionStatus($status->from_status);
                        $statuslog[$key]->to_status = getInspectionStatus($status->to_status);
                        $statuslog[$key]->remarks = ($status->remarks);
                        $statuslog[$key]->approved_by = getUsername($status->approved_by);
                        $statuslog[$key]->created_by = getUsername($status->created_by);
                        $statuslog[$key]->created_at = Displaydateformat($status->created_at);
                    }
                } else {
                    $statuslog = null;
                }

                $inspections = [
                    'id' => $inspection->inspection_id,
                    'issue_date' => Displaydateformat($inspection->issue_date),
                    'doc_no' => $inspection->doc_no,
                    'rev_dt' => $inspection->rev_dt,
                    'date_of_inspection' => Displaydateformat($inspection->date_of_inspection),
                    'location_name' => getLocationname($inspection->location),
                    'unit_name' => getUnitname($inspection->unit),
                    'shift_name' => getShiftname($inspection->shift),
                    'frequency_name' => getFrequencyname($inspection->frequency),
                    'device_image' => admin_url($inspection_image),
                    'inspection_creator_signature' => admin_url($signature),
                ];

                $inspection_details  = [];


                if (!empty($inspection->verified_by)) {
                    $updated_time = GetFireUpdatedTime(
                        $inspection->verified_by,
                        $inspection->inspection_id,
                        DETECTOR_INSPECTION,
                        WAITING_FOR_EHS_OFFICER_VERIFICATION,
                    );

                    $verifier_signature = GetFireSignature(
                        $inspection->verified_by,
                        $inspection->inspection_id,
                        DETECTOR_INSPECTION,
                    );

                    $inspection_details += [
                        'inspection_verified_by' => getUsername($inspection->verified_by),
                        'inspection_verified_at' => Displaydateformat($updated_time->created_at),
                        'verifier_signature' => admin_url($verifier_signature),
                        'capa_recomendation' => !empty($inspection->capa_recomendation) ? $inspection->capa_recomendation : $inspection->remarks,
                    ];
                }

                // CAPA Remarks by Inspection Creator
                if (!empty($inspection->capa_remarks)) {
                    $capa_creator_time = GetFireUpdatedTime(
                        $inspection->inspection_created_by,
                        $inspection->inspection_id,
                        DETECTOR_INSPECTION,
                        WAITING_FOR_CAPA_ACTION,
                    );

                    $inspection_details += [
                        'capa_remarks' => $inspection->capa_remarks,
                        'capa_created_by' => getUsername($inspection->inspection_created_by),
                        'capa_created_at' => Displaydateformat($capa_creator_time->created_at),
                        'capa_creator_signature' => admin_url($signature),
                    ];
                }

                if (!empty($inspection->capa_ehs_remarks) && !empty($inspection->verified_by)) {
                    $ehs_updated_time = GetFireUpdatedTime(
                        $inspection->verified_by,
                        $inspection->inspection_id,
                        DETECTOR_INSPECTION,
                        WAITING_FOR_CAPA_VERIFICATION,
                    );

                    $ehs_signature = GetFireSignature(
                        $inspection->verified_by,
                        $inspection->inspection_id,
                        DETECTOR_INSPECTION,
                    );

                    $inspection_details += [
                        'capa_ehs_remarks' => $inspection->capa_ehs_remarks,
                        'capa_ehs_by' => getUsername($inspection->verified_by),
                        'capa_ehs_at' => Displaydateformat($ehs_updated_time->created_at),
                        'capa_ehs_signature' => admin_url($ehs_signature),
                    ];
                }


                if (!empty($inspection->l1_manager_verified_by)) {
                    $l1_signature = GetFireSignature(
                        $inspection->l1_manager_verified_by,
                        $inspection->inspection_id,
                        DETECTOR_INSPECTION,
                    );

                    $l1_updated_time = GetFireUpdatedTime(
                        $inspection->l1_manager_verified_by,
                        $inspection->inspection_id,
                        DETECTOR_INSPECTION,
                        WAITING_FOR_L1_VERIFICATION,
                    );

                    $inspection_details += [
                        'l1_verified_by' => getUsername($inspection->l1_manager_verified_by),
                        'l1_remarks' => $inspection->level_one_manager_remarks ?? '',
                        'l1_updated_time' => Displaydateformat($l1_updated_time->created_at),
                        'l1_signature' => admin_url($l1_signature),
                    ];
                }

                if ($inspection->inspection_status == INSPECTION_APPROVED && !empty($inspection->approved_by)) {

                    if (!empty($inspection->l2_manager_verified_by)) {
                        $l2_signature = GetFireSignature(
                            $inspection->l2_manager_verified_by,
                            $inspection->inspection_id,
                            DETECTOR_INSPECTION,
                        );

                        $inspection_details += [
                            'l2_verified_by'   => getUsername($inspection->l2_manager_verified_by),
                            'l2_remarks'       => $inspection->level_two_manager_remarks ?? '',
                            'l2_updated_time'  => Displaydateformat($inspection->inspection_updated_at),
                            'l2_signature'     => !empty($l2_signature) ? admin_url($l2_signature) : '',
                        ];
                    } else {
                        $approver_signature = GetFireSignature(
                            $inspection->approved_by,
                            $inspection->inspection_id,
                            DETECTOR_INSPECTION,
                        );

                        $inspection_details += [
                            'approved_by'           => getUsername($inspection->approved_by),
                            'approved_remarks'      => $inspection->remarks ?? '',
                            'approved_updated_time' => Displaydateformat($inspection->inspection_updated_at),
                            'approved_signature'    => !empty($approver_signature) ? admin_url($approver_signature) : '',
                        ];
                    }
                }


                $success = [
                    'id' => $inspection->inspection_id,
                    'inspections' => $inspections,
                    'inspection_details_array' => $inspection_details_array,
                    'inspection_details' => $inspection_details,
                    '$statuslog' => $statuslog,
                ];
                return $this->sendResponse($success, 'Inspection Details');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
            }
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }


    public function Add(Request $request)
    {
        try {
            $rules = [
                'issue_date' => 'required',
                'rev_date' => 'required',
                'inspection_date' => 'required',
                'location_id' => 'required',
                'shift_id' => 'required',
                'next_due' => 'required',
                'unit_id' => 'required',
                'frequency_id' => 'required',
                'department.*' => 'required',
                'resource_code.*' => 'required',
                'detector_type.*' => 'required',
                'physical_condition.*' => 'required',
                'cable_condition.*' => 'required',
                'response_indicator.*' => 'required',
                'working_status.*' => 'required',
                'remarks.*' => 'required',
            ];

            $messages = [
                'issue_date.required' => 'Issue Date is required.',
                'rev_date.required' => 'Revision Date is required.',
                'inspection_date.required' => 'Inspection Date is required.',
                'location_id.required' => 'Location is required.',
                'shift_id.required' => 'Shift is required.',
                'frequency_id.required' => 'Frequency is required.',
                'next_due.required' => 'Next Due Date is required.',
                'unit_id.required' => 'Unit is required.',
                'department.*.required' => 'Department is required.',
                'resource_code.*.required' => 'Resource Code is required.',
                'detector_type.*.required' => 'Detector Type is required.',
                'physical_condition.*.required' => 'Physical Condition is required.',
                'cable_condition.*.required' => 'Cable Condition is required.',
                'response_indicator.*.required' => 'Response Indicator is required.',
                'working_status.*.required' => 'Working Status is required.',
                'remarks.*.required' => 'Remarks are required.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }

            $detector_inspection = $this->detector->store_api();
            $id = $detector_inspection->id;
            $detector_inspection_details = $this->detector_details->store_api($id);
            $inspection_file = $this->files->file_upload_api(DETECTOR_INSPECTION, $id);
            $signature_update = $this->signature->CheckedBySignatureApi($detector_inspection->id, DETECTOR_INSPECTION);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'Safety Gallery inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Safety Gallery Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $detector_inspection->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safety/safety-gallery-inspection/view/' . encryptId($detector_inspection->id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Safety Gallery Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('safety/safety-gallery-inspection/verification/' . encryptId($id) . '/ehs');
                $details = array(
                    'safety_type' => 'Safety Gallery Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $detector_inspection
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => DETECTOR_INSPECTION,
                'inspection_id' => $detector_inspection->id,
                'from_status' => 0,
                'to_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'created_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);

            $success = [
                "success" => $detector_inspection,
            ];
            return $this->sendResponse($success, 'Inspection Created');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
