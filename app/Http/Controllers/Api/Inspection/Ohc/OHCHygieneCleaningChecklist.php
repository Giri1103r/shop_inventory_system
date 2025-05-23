<?php

namespace App\Http\Controllers\Api\Inspection\Ohc;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Inspection\Master\Shift;
use Illuminate\Support\Facades\Validator;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Mail\Inspection\Safety\SafetyInspection;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Ohc\OHCHygieneCleaningChecklist as OhcOHCHygieneCleaningChecklist;

class OHCHygieneCleaningChecklist extends BaseController
{
    private $ohc_hygiene;
    private $shift;
    private $signature;
    private $document_reference;


    public function __construct()
    {
        $this->ohc_hygiene = new OhcOHCHygieneCleaningChecklist();
        $this->shift = new Shift();
        $this->signature = new OhcSignature();
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
            $query = $this->ohc_hygiene->select('inspection_ohc_hygiene_checklist.*', 'inspection_shift_option.*', 'inspection_ohc_hygiene_checklist.id as inspection_id', 'inspection_ohc_hygiene_checklist.created_by as checked_by', 'inspection_ohc_hygiene_checklist.updated_by as verified_by', 'inspection_ohc_hygiene_checklist.created_at as inspection_created_at',)
                ->leftjoin('inspection_shift_option', 'inspection_shift_option.id', '=', 'inspection_ohc_hygiene_checklist.shift_id');

            $org_total_counts = $query->count();

            if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_NURSING_OFFICER)) {
            } else if (CheckUserRole(ROLE_CLEANER)) {
                $query->where('inspection_ohc_hygiene_checklist.created_by', Auth::id());
            }

            if (!empty($search)) {
                $query->where(function ($query) use ($search) {
                    $query->orWhereRaw("DATE_FORMAT(inspection_ohc_hygiene_checklist.issue_date, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
                    $query->orWhere('shift', 'LIKE', '%' . $search . '%');
                });
            }


            $query_array = $query->orderBy('inspection_ohc_hygiene_checklist.id', 'DESC')->paginate($request->input('per_page', 10));
            $ohc_inspection = $query_array->toArray();
            if (empty($ohc_inspection['data'])) {
                return $this->sendError('No records found.', [], 404);
            }


            $data_array = [];
            foreach ($ohc_inspection['data'] as $datas) {
                $data = [];
                $data['id'] = $datas['id'] ?? '';
                $data['shift_name'] = $datas['shift'] ?? '';
                $data['date'] = Displaydateformat($datas['issue_date']);
                $data['checklist_status'] = getOHCStatus($datas['checklist_status'] ?? '');
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

            return $this->sendResponse($success, 'Daily OHC Hygiene Cleaning Checklist Details');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }


    public function view(Request $request)
    {
        try {
            if (Auth::user()) {
                $id = $request->id;
                $inspections = $this->ohc_hygiene
                    ->where('inspection_ohc_hygiene_checklist.id', $id)
                    ->select(
                        'inspection_ohc_hygiene_checklist.*',
                        'inspection_ohc_hygiene_checklist.id as inspection_id',
                        'inspection_ohc_hygiene_checklist.created_by as inspection_created_by',
                        'inspection_ohc_hygiene_checklist.updated_at as inspection_updated_at',
                        'inspection_ohc_hygiene_checklist.updated_by as inspection_updated_by',
                    )
                    ->first();


                $signature = GetOHCSignature(
                    $inspections->inspection_created_by,
                    $inspections->inspection_id,
                    DAILY_OHC_HYGIENE_CLEANING_CHECKLIST,
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
                if ($inspections->observation_status != CLEANER_SUBMITTED_THE_CHECKLIST) {
                    $signature = GetOHCSignature(
                        $inspections->inspection_updated_by,
                        $inspections->inspection_id,
                        DAILY_OHC_HYGIENE_CLEANING_CHECKLIST,
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
                'issue_date' => 'required',
                'shift_id' => 'required',
                'inspection' => 'required',
                'remarks' => 'required',
            ];

            $messages = [
                'issue_date.required' => 'Issue Date is required.',
                'shift_id.required' => 'Shift ID is required.',
                'inspection.required' => 'Inspection is required.',
                'remarks.required' => 'Remarks is required.',
            ];


            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }
            $safety_walk = $this->safety_walk->store_api();
            $id = $safety_walk->id;
            $forklift_observation_details = $this->observation_details->store_api($safety_walk->id);
            $signature_update = $this->signature->signatureUpload_api(DAILY_OHC_HYGIENE_CLEANING_CHECKLIST, $safety_walk->id);

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
