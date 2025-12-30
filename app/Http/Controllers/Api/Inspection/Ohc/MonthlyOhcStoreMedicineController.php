<?php

namespace App\Http\Controllers\Api\Inspection\Ohc;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Mail\Inspection\Ohc\FirstAidEmail;
use App\Models\Inspection\Ohc\InspectionOhcStatuslog;
use App\Models\Inspection\Ohc\MonthlyMedicineStore;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class MonthlyOhcStoreMedicineController extends BaseController
{
    private $monthly_medicne_store;
    private $statuslog;

    public function __construct()
    {
        $this->monthly_medicne_store = new MonthlyMedicineStore();
        $this->statuslog = new InspectionOhcStatuslog();
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
            $query = $this->monthly_medicne_store->select('inspection_ohc_medicine_store_inspection.*');

            $org_total_counts = $query->count();

            $user = Auth::user();
            $userRole = string_to_array($user->role);
            if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)  || in_array(ROLE_EHS_OFFICER, $userRole)  || in_array(ROLE_INSPECTION_CREATOR, $userRole) || in_array(ROLE_SAFETY_OFFICER, $userRole) || in_array(ROLE_MEDICAL_ASSISTANT, $userRole)) {
                $query->orderBy('inspection_ohc_medicine_store_inspection.id', 'DESC');
            } else {
                $query->where('inspection_ohc_medicine_store_inspection.created_by', Auth::id());
            }

            if (!empty($search)) {
                $audit_response = [
                    "Waiting For EHS Officer Verification" => 1,
                    "EHS Officer Rejected" => 2,
                    "Approved by Ehs officer" => 3,
                ];

                $query->where(function ($query) use ($search, $audit_response) {

                    if (isset($audit_response[$search])) {
                        $query->orWhere('inspection_ohc_medicine_store_inspection.inspection_status', $audit_response[$search]);
                    }
                    if (strtotime($search)) {
                        $query->orWhereDate('inspection_ohc_medicine_store_inspection.next_due', '=', $search);
                    }

                    if (strtotime($search)) {
                        $query->orWhereDate('inspection_ohc_medicine_store_inspection.inspection_date', '=', $search);
                    }
                });
            }


            $query_array = $query->orderBy('inspection_ohc_medicine_store_inspection.id', 'DESC')->paginate($request->input('per_page', 10));

            $inspection_list = $query_array->toArray();

            if (empty($inspection_list['data'])) {
                return $this->sendError('No records found.', [], 404);
            }

            $data_array = [];
            foreach ($inspection_list['data'] as $datas) {
                $data = [];
                $data['id'] = $datas['id'] ?? '';
                $data['date_of_inspection'] = Displaydateformat($datas['inspection_date']);
                $data['next_due_date'] = Displaydateformat($datas['next_due']);
                $data['inspection_status'] = getFirstAidOpdAPIStatus($datas['inspection_status'] ?? '');
                $data['created_by'] = getUsername($datas['created_by'] ?? '');
                $data['created_at'] = Displaydateformat($datas['created_at'] ?? '');
                $data_array[] = $data;
            }

            $monthly_medicine_store = [
                'per_page' => $inspection_list['per_page'] ?? 0,
                'current_page' => $inspection_list['current_page'] ?? 0,
                'from' => $inspection_list['from'] ?? 0,
                'to' => $inspection_list['to'] ?? 0,
                'total' => $inspection_list['total'] ?? 0,
                'total_page' => $inspection_list['last_page'] ?? 0,
                'list' => $data_array,
            ];

            $success = [
                'monthly_medicine_store' => $monthly_medicine_store
            ];

            return $this->sendResponse($success, 'Monthly OHC Medicine Store Inspection Details');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
    public function view(Request $request)
    {
        try {
            $id = $request->id;

            $monthly_ohc_medicine_store = $this->monthly_medicne_store->find($id);

            if (!$monthly_ohc_medicine_store) {
                return $this->sendError('Not Found', ['error' => 'Record not found.']);
            }

            $monthly_medicine = [
                'date_of_inspection' => Displaydateformat($monthly_ohc_medicine_store->inspection_date ?? null),
                'next_due_date'      => Displaydateformat($monthly_ohc_medicine_store->next_due ?? null),
                'created_by'         => getUsername($monthly_ohc_medicine_store->created_by ?? null),
                'created_at'         => Displaydateformat($monthly_ohc_medicine_store->created_at ?? null),
            ];

            $checklist = json_decode($monthly_ohc_medicine_store->inspection_data, true) ?? [];

            $monthly_medicine_checklist = [];
            foreach ($checklist as $data) {
                $monthly_medicine_checklist[] = [
                    'medicine_name'      => isset($data['medicine_id']) ? getMedicinename($data['medicine_id']) : null,
                    'available_quantity' => $data['available_quantity'] ?? null,
                    'expired_date'       => isset($data['expired_date']) ? Displaydateformat($data['expired_date']) : null,
                    'employee_name'      => $data['emp_id'] ?? null,
                    'remarks'            => $data['remarks'] ?? null,
                ];
            }

            $success = [
                'monthly_medicine'                     => $monthly_medicine,
                'monthly_medicine_checklist' => $monthly_medicine_checklist,
            ];

            // ehs officer approvals
            if (!empty($monthly_ohc_medicine_store->approval_remarks)) {
                $success['ehs_officer_approval'] = [
                    'approver_name' => getUsername($monthly_ohc_medicine_store->updated_by),
                    'approved_date' => Displaydateformat($monthly_ohc_medicine_store->updated_at),
                    'status' => $monthly_ohc_medicine_store->inspection_status == 3 ? 'Approved' : 'Rejected',
                    'approval_remarks' => $monthly_ohc_medicine_store->approval_remarks,
                ];
            }


            $type = OHC_TYPE_MONTHLY_MEDICINE_STORE;
            $status_log = $this->statuslog->getStatuslog($id, $type);


            if (!empty($status_log)) {
                $approvalLogs = [];
                foreach ($status_log as $logs) {
                    $approvalLogs[] = [
                        'from_status'   => getFirstAidOpdStatus($logs->from_status ?? null),
                        'to_status'     => getFirstAidOpdStatus($logs->to_status ?? null),
                        'remarks'       => $logs->remarks ?? null,
                        'approver_name' => getUsername($logs->approved_by ?? null),
                        'created_by'    => getUsername($logs->created_by ?? null),
                        'created_at'    => Displaydateformat($logs->created_at ?? null),
                    ];
                }

                $success['approvalLogs'] = $approvalLogs;
            }

            return $this->sendResponse($success, 'Data fetched successfully');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorized', ['error' => 'Something went wrong, please try again later.']);
        }
    }

    public function store(Request $request)
    {
        try {
            $data =  $this->monthly_medicne_store->store_api();
            $success = [
                'Montly OHC Medicine Store' => $data->id
            ];

            return $this->sendResponse($success, 'Monthly OHC Medicine Store Created successfully');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('error', 'Something Went Wrong Please try again');
        }
    }

    public function ehsapproval(Request $request)
    {
        try {
            $id = $request->id;

            $status = $request->action == 'approve' ? 1 : 0;
            $remarks = $request->capa_remarks;
            $eye_wash_inspection = $this->monthly_medicne_store->approvalSubmit($id, $status, $remarks);
            $inspection_details = $this->monthly_medicne_store->selectOne($id);
            $ehsOfficer = [$inspection_details->created_by];

            if ($status == 1) {
                $message = 'Monthly Store Medicine Checklist - APPROVED';
                $to_status = OBSERVATION_APPROVED;
            } else {
                $message = 'Monthly Store Medicine Checklist - REJECTED';
                $to_status = OBSERVATION_REJECTED;
            }
            $web_link =   admin_url('ohc/monthly-medicine-store/inspection/view/' . encryptId($inspection_details->id));
            $mailsubject = 'Monthly OHC Store Medicine Inspection Checklist';
            $notificationData = array(
                'notification_type' => OHC_INSPECTION,
                'module_type' => 18,
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

            $title = 'Monthly Store Medicine Inspection Checklist';
            $email_id = getUseremail($ehsOfficer);
            $url = admin_url('ohc/monthly-medicine-store/inspection/view/' . encryptId($inspection_details->id));
            $details = array(
                'ohc_type' => 'Monthly Store Medicine Inspection Checklist',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new FirstAidEmail($details));

            $data = [
                'type' => OHC_TYPE_MONTHLY_MEDICINE_STORE,
                'from_status' => OBSERVATION_PENDING,
                'to_status' => $to_status,
                'reference_id' => $inspection_details->id,
                'remarks' => $remarks,
                'created_by' => null,
                'approved_by' => Auth::id(),
            ];

            $this->statuslog->store($data);
            $success=[
                'Monthly medicine store'=>$id,
            ];
            return $this->sendError($success,'Approval has been Completed Successfully!');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
