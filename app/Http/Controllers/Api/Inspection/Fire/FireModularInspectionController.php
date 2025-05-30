<?php

namespace App\Http\Controllers\Api\Inspection\Fire;

use App\Http\Controllers\Api\BaseController;
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
use App\Mail\Inspection\Fire\FireInspection;
use App\Models\Inspection\Fire\DetectorType;
use App\Models\Inspection\Fire\FireStatusLog;
use App\Models\Inspection\Fire\FireFileUpload;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Fire\FireSignatureUpload;
use App\Models\Inspection\Fire\FireCheckListFollowUp;
use App\Models\Inspection\Fire\FireModularInspection;
use App\Models\Inspection\Fire\FireModularInspectionDetails;

class FireModularInspectionController extends BaseController
{
    private $fire_modular;
    private $fire_modular_details;
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
    private $fire_modular_type;

    public function __construct()
    {
        $this->fire_modular = new FireModularInspection();
        $this->fire_modular_details = new FireModularInspectionDetails();
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
        $this->fire_modular_type = new DetectorType();
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
            $query = $this->fire_modular->select('inspection_fire_modular_inspection.*', 'inspection_shift_option.*', 'masters_unit.*', 'masters_location.*', 'inspection_frequency_option.*', 'inspection_fire_modular_inspection.id as fire_detector_id')
                ->leftJoin('masters_location', 'inspection_fire_modular_inspection.location', '=', 'masters_location.id')
                ->leftJoin('inspection_shift_option', 'inspection_fire_modular_inspection.shift', '=', 'inspection_shift_option.id')
                ->leftJoin('masters_unit', 'inspection_fire_modular_inspection.unit', '=', 'masters_unit.id')
                ->leftJoin('inspection_frequency_option', 'inspection_fire_modular_inspection.frequency', '=', 'inspection_frequency_option.id')
                ->leftJoin('inspection_static_docno', 'inspection_fire_modular_inspection.document_reference_id', '=', 'inspection_static_docno.id');


            $org_total_counts = $query->count();

            if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_OFFICER) || CheckUserRole(ROLE_L1_MANAGER) || CheckUserRole(ROLE_L2_MANAGER)) {
            } else if (CheckUserRole(ROLE_FIRE_ASSOCIATES)) {
                $query->where('inspection_fire_modular_inspection.created_by', Auth::id());
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

            $query_array = $query->orderBy('inspection_fire_modular_inspection.id', 'DESC')->paginate($request->input('per_page', 10));

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
                $inspection = $this->fire_modular
                    ->leftJoin('inspection_static_docno', 'inspection_fire_modular_inspection.document_reference_id', '=', 'inspection_static_docno.id')
                    ->where('inspection_fire_modular_inspection.id', $id)
                    ->select(
                        'inspection_fire_modular_inspection.*',
                        'inspection_static_docno.*',
                        'inspection_fire_modular_inspection.id as inspection_id',
                        'inspection_fire_modular_inspection.created_by as inspection_created_by',
                        'inspection_fire_modular_inspection.updated_at as inspection_updated_at',
                    )
                    ->first();
                $inspection_details = $this->fire_modular_details->GetDetails($inspection->inspection_id);

                $inspection_details_array = [];
                foreach ($inspection_details as $inspection_detail) {
                    $data = [
                        'location_name' => getLocationname($inspection_detail->location),
                        'resource_code' => ($inspection_detail->resource_code),
                        'department' =>getDepartment($inspection_detail->department),
                        'types_of_equipment' => ($inspection_detail->types_of_equipment),
                        'capacity_of_equipment' => ($inspection_detail->capacity_of_equipment),
                        'working_temperature' => ($inspection_detail->working_temperature),
                        'sprinkler_head' => ($inspection_detail->sprinkler_head),
                        'neck_ring' => ($inspection_detail->neck_ring),
                        'cylinder_pressure' => ($inspection_detail->cylinder_pressure),
                        'remarks' => ($inspection_detail->remarks),
                    ];
                    $inspection_details_array[] = $data;
                }


                $signature = GetFireSignature(
                    $inspection->inspection_created_by,
                    $inspection->inspection_id,
                    FIRE_MODULAR_INSPECTION,
                );
                $inspection_image = $this->files->GetFile(FIRE_MODULAR_INSPECTION, $inspection->inspection_id);

                $statuslog = $this->statusLog->selectOne($id, FIRE_MODULAR_INSPECTION);

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
                        FIRE_MODULAR_INSPECTION,
                        WAITING_FOR_EHS_OFFICER_VERIFICATION,
                    );

                    $verifier_signature = GetFireSignature(
                        $inspection->verified_by,
                        $inspection->inspection_id,
                        FIRE_MODULAR_INSPECTION,
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
                        FIRE_MODULAR_INSPECTION,
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
                        FIRE_MODULAR_INSPECTION,
                        WAITING_FOR_CAPA_VERIFICATION,
                    );

                    $ehs_signature = GetFireSignature(
                        $inspection->verified_by,
                        $inspection->inspection_id,
                        FIRE_MODULAR_INSPECTION,
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
                        FIRE_MODULAR_INSPECTION,
                    );

                    $l1_updated_time = GetFireUpdatedTime(
                        $inspection->l1_manager_verified_by,
                        $inspection->inspection_id,
                        FIRE_MODULAR_INSPECTION,
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
                            FIRE_MODULAR_INSPECTION,
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
                            FIRE_MODULAR_INSPECTION,
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
                'location.*' => 'required',
                'types_of_equipment.*' => 'required',
                'capacity_of_equipment.*' => 'required',
                'working_temperature.*' => 'required',
                'sprinkler_head.*' => 'required',
                'neck_ring.*' => 'required',
                'cylinder_pressure.*' => 'required',
                'remarks.*' => 'required',
            ];

            $messages = [
                'issue_date.required' => 'Issue Date is required.',
                'rev_date.required' => 'Revision Date is required.',
                'inspection_date.required' => 'Inspection Date is required.',
                'location_id.required' => 'Location is required.',
                'shift_id.required' => 'Shift is required.',
                'next_due.required' => 'Next Due Date is required.',
                'unit_id.required' => 'Unit is required.',
                'frequency_id.required' => 'Frequency is required.',
                'department.*.required' => 'Department is required.',
                'resource_code.*.required' => 'Resource Code is required.',
                'location.*.required' => 'Location is required.',
                'types_of_equipment.*.required' => 'Type of Equipment is required.',
                'capacity_of_equipment.*.required' => 'Capacity of Equipment is required.',
                'working_temperature.*.required' => 'Working Temperature is required.',
                'sprinkler_head.*.required' => 'Sprinkler Head is required.',
                'neck_ring.*.required' => 'Neck Ring is required.',
                'cylinder_pressure.*.required' => 'Cylinder Pressure is required.',
                'remarks.*.required' => 'Remarks are required.',

            ];



            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }

            $fire_modular_inspection = $this->fire_modular->store_api();
            $id = $fire_modular_inspection->id;
            $sand_bucket_details = $this->fire_modular_details->store_api($id);
            $inspection_file = $this->files->file_upload_api(FIRE_MODULAR_INSPECTION, $id);
            $signature_update = $this->signature->CheckedBySignatureApi($fire_modular_inspection->id, FIRE_MODULAR_INSPECTION);

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
                    'id' => $fire_modular_inspection->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safety/safety-gallery-inspection/view/' . encryptId($fire_modular_inspection->id)),
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
                    'data' => $fire_modular_inspection
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => FIRE_MODULAR_INSPECTION,
                'inspection_id' => $fire_modular_inspection->id,
                'from_status' => 0,
                'to_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'created_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);

            $success = [
                "success" => $fire_modular_inspection,
            ];
            return $this->sendResponse($success, 'Inspection Created');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
