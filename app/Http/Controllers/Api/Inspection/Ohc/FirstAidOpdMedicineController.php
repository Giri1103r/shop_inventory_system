<?php

namespace App\Http\Controllers\Api\Inspection\Ohc;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Mail\Inspection\Ohc\FirstAidEmail;
use App\Models\Inspection\Ohc\FirstAidMedicineInspection;
use App\Models\Inspection\Ohc\InspectionOhcStatuslog;
use App\Models\OhcManagement\OhcStatuslog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class FirstAidOpdMedicineController extends BaseController
{
    private $first_aid_opd_medicine;
    private $inspection_ohc_status_log;

    public function __construct()
    {
        $this->first_aid_opd_medicine = new FirstAidMedicineInspection();
        $this->inspection_ohc_status_log = new InspectionOhcStatuslog();
    }

    public function list()
    {
        if (Auth::user()) {
            $request = request();
            $search = '';
            if ($request->has('search')) {
                if ($request->search != '') {
                    $search = $request->search;
                }
            }
            $query = $this->first_aid_opd_medicine->select('inspection_ohc_first_aid_inspection.*');

            $org_total_counts = $query->count();

            $user = Auth::user();
            $userRole = string_to_array($user->role);
            if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)  || in_array(ROLE_EHS_OFFICER, $userRole)  || in_array(ROLE_INSPECTION_CREATOR, $userRole) || in_array(ROLE_SAFETY_OFFICER, $userRole) || in_array(ROLE_MEDICAL_ASSISTANT, $userRole)) {
                $query->orderBy('inspection_ohc_first_aid_inspection.id', 'DESC');
            } else {
                $query->where('inspection_ohc_first_aid_inspection.created_by', Auth::id());
            }

            if (!empty($search)) {
                $audit_response = [
                    "Waiting For EHS Officer Verification" => 1,
                    "EHS Officer Rejected" => 2,
                    "Approved by Ehs officer" => 3,

                ];

                $query->where(function ($query) use ($search, $audit_response) {

                    if (isset($audit_response[$search])) {
                        $query->orWhere('inspection_ohc_first_aid_inspection.inspection_status', $audit_response[$search]);
                    }
                    if (strtotime($search)) {
                        $query->orWhereDate('inspection_ohc_first_aid_inspection.next_due', '=', $search);
                    }

                    if (strtotime($search)) {
                        $query->orWhereDate('inspection_ohc_first_aid_inspection.inspection_date', '=', $search);
                    }
                });
            }


            $query_array = $query->orderBy('inspection_ohc_first_aid_inspection.id', 'DESC')->paginate($request->input('per_page', 10));

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

            $first_aid_opd_medicine = [
                'per_page' => $inspection_list['per_page'] ?? 0,
                'current_page' => $inspection_list['current_page'] ?? 0,
                'from' => $inspection_list['from'] ?? 0,
                'to' => $inspection_list['to'] ?? 0,
                'total' => $inspection_list['total'] ?? 0,
                'total_page' => $inspection_list['last_page'] ?? 0,
                'list' => $data_array,
            ];

            $success = [
                'first_aid_opd_medicine' => $first_aid_opd_medicine
            ];

            return $this->sendResponse($success, 'First Aid OPD Medicine Details');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    public function view(Request $request)
    {
        try {
            $id = $request->id;

            $first_aid_medicine = $this->first_aid_opd_medicine->find($id);

            if (!$first_aid_medicine) {
                return $this->sendError('Not Found', ['error' => 'Record not found.']);
            }

            $first_aid = [
                'date_of_inspection' => Displaydateformat($first_aid_medicine->inspection_date ?? null),
                'next_due_date'      => Displaydateformat($first_aid_medicine->next_due ?? null),
                'created_by'         => getUsername($first_aid_medicine->created_by ?? null),
                'created_at'         => Displaydateformat($first_aid_medicine->created_at ?? null),
            ];

            $checklist = json_decode($first_aid_medicine->inspection_data, true) ?? [];

            $first_medicine_details_checklist = [];
            foreach ($checklist as $data) {
                $first_medicine_details_checklist[] = [
                    'medicine_name'      => isset($data['medicine_id']) ? getMedicinename($data['medicine_id']) : null,
                    'available_quantity' => $data['available_quantity'] ?? null,
                    'expired_date'       => isset($data['expired_date']) ? Displaydateformat($data['expired_date']) : null,
                    'employee_name'      => $data['emp_id'] ?? null,
                    'remarks'            => $data['remarks'] ?? null,
                ];
            }

            $success = [
                'first_aid'                     => $first_aid,
                'first_medicine_details_checklist' => $first_medicine_details_checklist,
            ];

            // ehs officer approvals
            if (!empty($first_aid_medicine->approval_remarks)) {
                $success['ehs_officer_approval'] = [
                    'approver_name' => getUsername($first_aid_medicine->updated_by),
                    'approved_date' => Displaydateformat($first_aid_medicine->updated_at),
                    'status' => $first_aid_medicine->inspection_status == 3 ? 'Approved' : 'Rejected',
                    'approval_remarks' => $first_aid_medicine->approval_remarks,
                ];
            }


            $type = OHC_OPD_MEDICINE_INSPECTION;
            $status_log = $this->inspection_ohc_status_log->getStatuslog($id, $type);


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
            $data =  $this->first_aid_opd_medicine->store_api();
            $success = [
                'First Aid OPD Medicine' => $data->id
            ];

            return $this->sendResponse($success, 'First Aid OPD Medicine Created successfully');
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
            $eye_wash_inspection = $this->first_aid_opd_medicine->approvalSubmit($id, $status, $remarks);
            $inspection_details = $this->first_aid_opd_medicine->selectOne($id);
            $ehsOfficer = [$inspection_details->created_by];

            if ($status == 1) {
                $message = 'Monthly OHC First-Aid Medicine Inspection Checklist - APPROVED';
                $to_status = OBSERVATION_APPROVED;
            } else {
                $message = 'Monthly OHC First-Aid Medicine Inspection Checklist - REJECTED';
                $to_status = OBSERVATION_REJECTED;
            }
            $web_link =   admin_url('ohc/first-aid/opd-medicine-inspection/view/' . encryptId($inspection_details->id));
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

            $title = 'Monthly OHC First-Aid Medicine Inspection Checklist';
            $email_id = getUseremail($ehsOfficer);
            $url = admin_url('ohc/first-aid/opd-medicine-inspection/view/' . encryptId($inspection_details->id));
            $details = array(
                'ohc_type' => 'Monthly OHC First-Aid Medicine Inspection Checklist',
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

            $this->inspection_ohc_status_log->store($data);
            $success=[
                'first_aid_medicine'=>$id,
            ];
            return $this->sendError($success,'Approval has been Completed Successfully!');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
