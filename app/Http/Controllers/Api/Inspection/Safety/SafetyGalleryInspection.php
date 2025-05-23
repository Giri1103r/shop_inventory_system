<?php

namespace App\Http\Controllers\Api\Inspection\Safety;

use Exception;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Master\Location;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Inspection\Master\Shift;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use App\Mail\Inspection\Safety\SafetyInspection;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Safety\SafetyStatusLog;
use App\Models\Inspection\Safety\SignatureUpload;
use App\Models\Inspection\Safety\SafetyGalleryInspection as SafetySafetyGalleryInspection;

class SafetyGalleryInspection extends BaseController
{
    private $safetygallery;
    private $shift;
    private $location;
    private $unit;
    private $frequency;
    private $statusLog;
    private $signature;
    private $document_reference;

    public function __construct()
    {
        $this->safetygallery = new SafetySafetyGalleryInspection();
        $this->location = new Location();
        $this->shift = new Shift();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->unit = new Unit();
        $this->statusLog = new SafetyStatusLog();
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
            $query = SafetySafetyGalleryInspection::select(
                'inspection_safety_gallery.*',
                'masters_unit.*',
                'masters_location.*',
                'inspection_safety_gallery.id as inspection_id',
                'inspection_safety_gallery.created_at as inspection_created_at'
            )
                ->leftJoin('masters_location', 'inspection_safety_gallery.location', '=', 'masters_location.id')
                ->leftJoin('masters_unit', 'inspection_safety_gallery.unit', '=', 'masters_unit.id')
                ->leftJoin('inspection_static_docno', 'inspection_safety_gallery.document_reference_id', '=', 'inspection_static_docno.id');

            $org_total_counts = $query->count();


            if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_OFFICER) || CheckUserRole(ROLE_L1_MANAGER) || CheckUserRole(ROLE_L2_MANAGER)) {
            } else if (CheckUserRole(ROLE_FIRE_ASSOCIATES)) {
                $query->where('inspection_safety_gallery.created_by', Auth::id());
            }

            if (!empty($search)) {
                $search = ($search);
                $query->where(function ($query) use ($search) {
                    $query->orWhere('masters_unit.unit_name', $search)
                        ->orWhere('masters_location.location_name', $search);
                });
            }

            $query_array = $query->orderBy('inspection_safety_gallery.id', 'DESC')->paginate($request->input('per_page', 10));

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
                $data['resource_code'] = ($datas['resource_code'] ?? '');
                $data['unit_name'] = ($datas['unit_name'] ?? '');
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
                $inspection = $this->safetygallery
                    ->leftJoin('inspection_static_docno', 'inspection_safety_gallery.document_reference_id', '=', 'inspection_static_docno.id')
                    ->where('inspection_safety_gallery.id', $id)
                    ->select(
                        'inspection_safety_gallery.*',
                        'inspection_static_docno.*',
                        'inspection_safety_gallery.id as inspection_id',
                        'inspection_safety_gallery.created_by as inspection_created_by',
                        'inspection_safety_gallery.updated_at as inspection_updated_at',
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
                    SAFETY_GALLERY_INSPECTION,
                );

                $statuslog = $this->statusLog->selectOne($id, SAFETY_GALLERY_INSPECTION);

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

                $inspection_details = [
                    'id' => $inspection->inspection_id,
                    'issue_date' => Displaydateformat($inspection->issue_date),
                    'doc_no' => $inspection->doc_no,
                    'rev_dt' => $inspection->rev_dt,
                    'date_of_inspection' => Displaydateformat($inspection->date_of_inspection),
                    'location' => getLocationname($inspection->location),
                    'unit' => getUnitname($inspection->unit),
                    'resource_code' => $inspection->resource_code,
                    'responses' => $responses,
                    'inspection_creator_signature' => admin_url($signature),
                ];

                if (!empty($inspection->verified_by)) {
                    $updated_time = GetSafetyUpdatedTime(
                        $inspection->verified_by,
                        $inspection->inspection_id,
                        SAFETY_GALLERY_INSPECTION,
                        WAITING_FOR_EHS_OFFICER_VERIFICATION,
                    );

                    $verifier_signature = GetSafetySignature(
                        $inspection->verified_by,
                        $inspection->inspection_id,
                        SAFETY_GALLERY_INSPECTION,
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
                    $capa_creator_time = GetSafetyUpdatedTime(
                        $inspection->inspection_created_by,
                        $inspection->inspection_id,
                        SAFETY_GALLERY_INSPECTION,
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
                    $ehs_updated_time = GetSafetyUpdatedTime(
                        $inspection->verified_by,
                        $inspection->inspection_id,
                        SAFETY_GALLERY_INSPECTION,
                        WAITING_FOR_CAPA_VERIFICATION,
                    );

                    $ehs_signature = GetSafetySignature(
                        $inspection->verified_by,
                        $inspection->inspection_id,
                        SAFETY_GALLERY_INSPECTION,
                    );

                    $inspection_details += [
                        'capa_ehs_remarks' => $inspection->capa_ehs_remarks,
                        'capa_ehs_by' => getUsername($inspection->verified_by),
                        'capa_ehs_at' => Displaydateformat($ehs_updated_time->created_at),
                        'capa_ehs_signature' => admin_url($ehs_signature),
                    ];
                }


                if (!empty($inspection->l1_manager_verified_by)) {
                    $l1_signature = GetSafetySignature(
                        $inspection->l1_manager_verified_by,
                        $inspection->inspection_id,
                        SAFETY_GALLERY_INSPECTION,
                    );

                    $l1_updated_time = GetSafetyUpdatedTime(
                        $inspection->l1_manager_verified_by,
                        $inspection->inspection_id,
                        SAFETY_GALLERY_INSPECTION,
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
                        $l2_signature = GetSafetySignature(
                            $inspection->l2_manager_verified_by,
                            $inspection->inspection_id,
                            SAFETY_GALLERY_INSPECTION,
                        );

                        $inspection_details += [
                            'l2_verified_by'   => getUsername($inspection->l2_manager_verified_by),
                            'l2_remarks'       => $inspection->level_two_manager_remarks ?? '',
                            'l2_updated_time'  => Displaydateformat($inspection->inspection_updated_at),
                            'l2_signature'     => !empty($l2_signature) ? admin_url($l2_signature) : '',
                        ];
                    } else {
                        $approver_signature = GetSafetySignature(
                            $inspection->approved_by,
                            $inspection->inspection_id,
                            SAFETY_GALLERY_INSPECTION,
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


    public function store(Request $request)
    {
        try {
            $rules = [
                'doc_no' => 'required',
                'issue_date' => 'required',
                'resource_code' => 'required',
                'location_id' => 'required',
                'unit_id' => 'required',
                'inspection_date' => 'required',
            ];

            $messages = [
                'doc_no.required' => 'Document number is required.',
                'issue_date.required' => 'Issue Date is Required',
                'resource_code.required' => 'Resource code is Required',
                'location_id.required' => 'Location is Required',
                'inspection_date.required' => 'Inspection Date is Required',
                'unit_id.required' => 'Unit is Required',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }

            $safety_gallery_inspection = $this->safetygallery->store_api();
            $id = $safety_gallery_inspection->id;
            $signature_update = $this->signature->signatureUpload_api(SAFETY_GALLERY_INSPECTION, $safety_gallery_inspection->id);

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
                    'id' => $safety_gallery_inspection->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safety/safety-gallery-inspection/view/' . encryptId($safety_gallery_inspection->id)),
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
                    'data' => $safety_gallery_inspection
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }

            $insert_array = [
                'type' => SAFETY_GALLERY_INSPECTION,
                'inspection_id' => $safety_gallery_inspection->id,
                'from_status' => 0,
                'to_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'created_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);

            $success = [
                "success" => $safety_gallery_inspection,
            ];
            return $this->sendResponse($success, 'Inspection Created');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
