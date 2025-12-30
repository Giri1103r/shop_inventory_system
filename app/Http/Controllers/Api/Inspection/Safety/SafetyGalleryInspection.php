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
use App\Mail\Inspection\Safety\SafetyGalleryEmail;
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
            $query = SafetySafetyGalleryInspection::select('inspection_safety_gallery.*', 'masters_unit.unit_name', 'masters_location.location_name', 'inspection_safety_gallery.id as inspection_id', 'inspection_safety_gallery.created_at as inspection_created_at')
                ->leftJoin('masters_location', 'inspection_safety_gallery.location', '=', 'masters_location.id')
                ->leftJoin('masters_unit', 'inspection_safety_gallery.unit', '=', 'masters_unit.id');



            if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_HEAD) || CheckUserRole(ROLE_L1_MANAGER) || CheckUserRole(ROLE_L2_MANAGER)) {
            } else if (CheckUserRole(ROLE_EHS_OFFICER)) {
                $query->where('inspection_safety_gallery.created_by', Auth::id());
            }

            $search = $request->input('search', '');

            if (!empty($search)) {
                $audit_response = [
                    "Waiting For Level Two Manager Approval" => 1,
                    "Approved by the Level Two Manager" => 2,
                    "Rejected By the Level Two Manager" => 3,
                    "EHS Head Approval Pending" => 4,
                    "Closed" => 5,
                    "EHS Head Rejected" => 6,
                ];

                $query->where(function ($query) use ($search, $audit_response) {
                    $query->orWhere('masters_unit.unit_name', 'LIKE', "%{$search}%")
                        ->orWhere('masters_location.location_name', 'LIKE', "%{$search}%")
                        ->orWhere('inspection_safety_gallery.resource_code', 'LIKE', "%{$search}%");


                    if (isset($audit_response[$search])) {
                        $query->orWhere('inspection_safety_gallery.inspection_status', $audit_response[$search]);
                    }


                    if (strtotime($search)) {
                        $query->orWhereDate('inspection_safety_gallery.date_of_inspection', '=', $search);
                    }
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
                $data['id'] = $datas['inspection_id'] ?? '';
                $data['date_of_inspection'] = Displaydateformat($datas['date_of_inspection']);
                $data['location_name'] = ($datas['location_name'] ?? '');
                $data['resource_code'] = ($datas['resource_code'] ?? '');
                $data['unit_name'] = ($datas['unit_name'] ?? '');
                $data['inspection_status'] = getSafetyGalleryinspectionstatus($datas['inspection_status'] ?? '');
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
                'safety_gallery_inspection' => $inspection_details
            ];
            return $this->sendResponse($success, 'Safety Gallery Inspection');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    // view

    public function view(Request $request)
    {
        try {
            $id = $request->id;
            $safety_gallery_inspection = $this->safetygallery->find($id);

            if (!$safety_gallery_inspection) {
                return $this->sendError('Record not found.', [], 404);
            }

            $doc_no = $this->document_reference->find($safety_gallery_inspection->document_reference_id);

            $success['safety_gallery_inspection'] = [
                'doc_no' => $doc_no->doc_no ?? '',
                'issue_date' => Displaydateformat($doc_no->issue_date ?? ''),
                'rev_dt' => $doc_no->rev_dt ?? '',
                'date_of_inspection' => Displaydateformat($safety_gallery_inspection->date_of_inspection ?? ''),
                'resource_code' => $safety_gallery_inspection->resource_code ?? '',
                'location' => getLocationname($safety_gallery_inspection->location ?? ''),
                'unit' => getUnitname($safety_gallery_inspection->unit ?? ''),
                'excat_location' => $safety_gallery_inspection->excat_location ?? '',
            ];
            // checklist
            $checkList = json_decode($safety_gallery_inspection->responses, true) ?? [];
            $safety_gallery_checklist = [];

            foreach ($checkList as $data) {
                $safety_gallery_checklist[] = [
                    'question_name' => GetChecklistTypeDate($data['question_id']),
                    'yes_or_no' => $data['answer'] ?? '',
                    'remarks' => $data['remarks'] ?? '',
                ];
            }
            $success['safety_gallery_checklist'] = $safety_gallery_checklist;
            // level two manager verification
            if ($safety_gallery_inspection->remarks) {
                $success['level_two_manager_verification'] = [
                    'approver_name' => getUsername($safety_gallery_inspection->verified_by),
                    'approver_date' => Displaydateformat($safety_gallery_inspection->verified_date),
                    'remarks' => $safety_gallery_inspection->remarks,
                ];
            }

            // ehs head verification
            if ($safety_gallery_inspection->level_two_manager_remarks) {
                $success['ehs_head_verification'] = [
                    'approver_name' => getUsername($safety_gallery_inspection->l2_manager_verified_by),
                    'approver_date' => Displaydateformat($safety_gallery_inspection->approved_date),
                    'remarks' => $safety_gallery_inspection->level_two_manager_remarks,
                ];
            }
            // approval logs
            $statuslog = $this->statusLog->selectOne($id, SAFETY_GALLERY_INSPECTION);
            if (!empty($statuslog)) {
                $approvalLogs = [];
                foreach ($statuslog as $logs) {
                    $approvalLogs[] = [
                        'from_status'   => getSafetyGalleryinspectionstatus($logs->from_status),
                        'to_status'     => getSafetyGalleryinspectionstatus($logs->to_status),
                        'approver_name' => getUsername($logs->approved_by),
                        'created_by'    => getUsername($logs->created_by),
                        'created_at'    => Displaydateformat($logs->created_at),
                    ];
                }
                $success['approvalLogs'] = $approvalLogs;
            }

            return $this->sendResponse($success, 'Safety Gallery Inspection Details');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorized', ['error' => 'Something went wrong, please try again later.']);
        }
    }

    public function leveloneverification(Request $request)
    {

        try {
            $id = $request->id;
            $remarks = $request->remarks;
            $date = $request->verified_date;
            $action = $request->action == 'approve' ? 1 : 0;
            $inspection_updates = $this->safetygallery->EHSOfficerUpdateAPI($id, $date, $remarks, $action);
            $inspection_details = $this->safetygallery->selectOne($id);
            if ($action == 1) {
                $message = 'Safety gallery Inspeciton Approved Successfully';
                $web_link =   admin_url('safety/safety-gallery-inspection/view/' . encryptId($inspection_details->id));
                $to_status = SAFETY_EHS_HEAD_APPROVAL_PENDING;
            } else {
                $message = 'Safety gallery Inspeciton Rejected Successfully';
                $web_link =   admin_url('safety/safety-gallery-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = SAFETY_L2_MANAGER_REJECTED;
            }

            $mailsubject = 'Safety Gallery inspection';
            $ehsOfficer = GetEHSHead();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            // $signature_update = $this->signature->signatureUpload(SAFETY_GALLERY_INSPECTION, $safety_gallery_inspection->id);
            $mailsubject = 'Safety Gallery inspection';



            $title = 'Safety Gallery Inspection Created';
            if ($ehsOfficer->isNotEmpty()) {
                foreach ($ehsOfficers as $user) {
                    $email_id = getUseremail($user);
                    $url = admin_url('safety/safety-gallery-inspection/verification/' . encryptId($id) . '/ehs');
                    $details = array(
                        'safety_type' => 'Safety Gallery Inspection',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'url' => $url,
                        'data' => $inspection_details
                    );
                    Mail::to($email_id)->queue(new SafetyGalleryEmail($details));
                }

                $notificationData = array(
                    'notification_type' => SAFETY_INSPECTION,
                    'module_type' => 3,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => "Safety Gallery Inspection Created",
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $inspection_details->id,
                        'module' => 1,
                    )),
                    'web_link' => admin_url('safety/safety-gallery-inspection/view/' . encryptId($inspection_details->id)),
                    'assigned_user' => implode(',', $ehsOfficers), // fixed array to string
                    'created_by' => Auth::id(),
                );

                notificationSave($notificationData);
            }

            $insert_array = [
                'type' => SAFETY_GALLERY_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => SAFETY_L2_MANAGER_APPROVAL_PENDING,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            $success = [
                'safety_gallery_inspection' => $id
            ];
            return $this->sendResponse($success, 'Level Two Verification Completed Successfully !');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorized', ['error' => 'Something went wrong, please try again later.']);
        }
    }

    public function ehsheadapproval(Request $request)
    {

        try {
            $id = $request->id;
            $remarks = $request->remarks;
            $date = $request->verified_date;
            $action = $request->action == 'approve' ? 1 : 0;
            $inspection_updates = $this->safetygallery->finalapprovalapi($id,$date, $remarks, $action);
            $inspection_details = $this->safetygallery->selectOne($id);
            if ($action == 1) {
                $message = 'Safety gallery Inspeciton Approved Successfully';
                $web_link =   admin_url('safety/safety-gallery-inspection/view/' . encryptId($inspection_details->id));
                $to_status = SAFETY_EHS_HEAD_APPROVED;
            } else {
                $message = 'Safety gallery Inspeciton Rejected Successfully';
                $web_link =   admin_url('safety/safety-gallery-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = SAFETY_EHS_HEAD_REJECTED;
            }
            $userIds = [
                'users' => $inspection_details->created_by,
            ];
            $mailsubject = 'Safety Gallery inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => $message,
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_details->id,
                    'module' => 1,
                )),
                'web_link' =>  $web_link,
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = $message;
            $user = $inspection_details->created_by;
            $email_id = getUseremail($user);
            $url = admin_url('safety/safety-gallery-inspection/monthly/verification/' . encryptId($id) . '/capa');
            $details = array(
                'safety_type' => 'Safety Gallery Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new SafetyGalleryEmail($details));

            $insert_array = [
                'type' => SAFETY_GALLERY_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => SAFETY_EHS_HEAD_APPROVAL_PENDING,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $remarks,
            ];
            $this->statusLog->create($insert_array);
            $success = [
                'safety_gallery_inspection' => $id
            ];
            return $this->sendResponse($success, 'EHS Head Approval Completed Successfully !');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorized', ['error' => 'Something went wrong, please try again later.']);
        }
    }
}
