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
use Exception;
use App\Models\Inspection\Ohc\InspectionOhcStatuslog;
use KitLoong\MigrationsGenerator\Repositories\Repository;

class OHCHygieneCleaningChecklist extends BaseController
{
    private $ohc_hygiene;
    private $shift;
    private $signature;
    private $document_reference;
    private $inspection_ohc_status_log;


    public function __construct()
    {
        $this->ohc_hygiene = new OhcOHCHygieneCleaningChecklist();
        $this->shift = new Shift();
        $this->inspection_ohc_status_log = new InspectionOhcStatuslog();
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
            $id = $request->id;
            $ohc_hygiene_cleaning_checklist = $this->ohc_hygiene->find($id);

            $ohc_hygiene = [
                'date' => Displaydateformat($ohc_hygiene_cleaning_checklist->issue_date),
                'shift_id' => getShift($ohc_hygiene_cleaning_checklist->shift_id),
                'created_by' => getUsername($ohc_hygiene_cleaning_checklist->created_by),
                'created_at' => Displaydateformat($ohc_hygiene_cleaning_checklist->created_at),
                'checklist_question' => $ohc_hygiene_cleaning_checklist->inspection_question,
                'inspection_value' => getYesNoStatus($ohc_hygiene_cleaning_checklist->inspection_value),
                'cleaner_remarks' => $ohc_hygiene_cleaning_checklist->cleaner_remarks,
            ];
            $success = [
                'ohc_hygiene' => $ohc_hygiene,
            ];
            $type = DAILY_OHC_HYGIENE_CLEANING_CHECKLIST;
            $status_log = $this->inspection_ohc_status_log->getStatuslog($id, $type);
            if (!empty($statuslog)) {
                $approvalLogs = [];
                foreach ($statuslog as $logs) {
                    $approvalLogs[] = [
                        'from_status'   => getOhcHygieneCleaningStatus($logs->from_status),
                        'to_status'     => getOhcHygieneCleaningStatus($logs->to_status),
                        'remarks'     => ($logs->remarks) ?? '-',
                        'approver_name' => getUsername($logs->approved_by),
                        'created_by'    => getUsername($logs->created_by),
                        'created_at'    => Displaydateformat($logs->created_at),
                    ];
                }
                $success['approvalLogs'] = $approvalLogs;
            }

            return $this->sendResponse($success, 'OHC Hygiene Cleaning Checklist Fetched Successfully');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Server Error', ['error' => $ex->getMessage()], 500);
        }
    }

    public function approval(Request $request)
    {
        try {
            $id = ($request->id);
            $status = $request->action == 'approve' ? 1 : 0;
            $remarks = $request->capa_remarks;

            if ($status == 1) {
                $message = 'OHC HYGIENE CLEANING CHECKLIST - APPROVED';
                $to_status = NURSING_OFFICER_SUBMITTED_THE_CHECKLIST;
            } else {
                $message = 'OHC HYGIENE CLEANING CHECKLIST - REJECTED';
                $to_status = NURSING_OFFICER_REJECTED;
            }

            $ohc_hygiene_checklist = $this->ohc_hygiene->approvalSubmit($id, $to_status, $remarks);

            $inspection_details = $this->ohc_hygiene->selectOne($id);
            $ehsOfficer = [$inspection_details->created_by];
            $web_link =   admin_url('ohc/ohc-hygiene-cleaning-checklist/view/' . encryptId($inspection_details->id));
            $mailsubject = 'OHC HYGIENE CLEANING CHECKLIST';
            $notificationData = array(
                'notification_type' => OHC_INSPECTION,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => $message,
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_details->id,
                    'module' => 1,
                )),
                'web_link' =>  $web_link,
                'assigned_user' => array_to_string($ehsOfficer),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'OHC HYGIENE CLEANING CHECKLIST';
            $email_id = getUseremail($ehsOfficer);
            $url = admin_url('ohc/ohc-hygiene-cleaning-checklist/view/' . encryptId($inspection_details->id));
            $details = array(
                'safety_type' => 'OHC HYGIENE CLEANING CHECKLIST',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new SafetyInspection($details));

            $data = [
                'type' => DAILY_OHC_HYGIENE_CLEANING_CHECKLIST,
                'from_status' => CLEANER_SUBMITTED_THE_CHECKLIST,
                'to_status' => $to_status,
                'reference_id' => $inspection_details->id,
                'remarks' => $remarks,
                'approved_by' => Auth::id(),
                'created_by' => $inspection_details->created_by,

            ];


            $this->inspection_ohc_status_log->store($data);
            $success = [
                'ohc_hygiene_cleaning_checklist' => $id,
            ];
            if($status == 1 ){
                return $this->sendResponse($success, 'OHC Hygiene Cleaning Checklist has been approved Successfully');
            }else{
                  return $this->sendResponse($success, 'OHC Hygiene Cleaning Checklist has been rejected Successfully');
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Server Error', ['error' => $ex->getMessage()], 500);
        }
    }
}
