<?php

namespace App\Http\Controllers\Api\Ppemanagement;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Mail\PpeExemptionEmail;
use App\Mail\PpeExemptionRejectEmail;
use App\Mail\PpeExemptionRequestorEmail;
use App\Models\Master\Employee;
use App\Models\Master\PpeExemption;
use App\Models\Master\PpeRequest;
use App\Models\Master\PpeType;
use App\Models\Master\Work;
use App\Models\Ppemanagement\PpeFiles;
use App\Models\Statuslog;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PpeExemptionController extends BaseController
{

    private $ppetype;
    private $pperequest;
    private $ppeexemption;
    private $ppeFiles;
    private $user;
    private $ppestatus;

    public function __construct()
    {
        $this->ppetype = new PpeType();
        $this->pperequest = new PpeRequest();
        $this->ppeexemption = new PpeExemption();
        $this->ppeFiles = new PpeFiles();
        $this->user = new User();
        $this->ppestatus = new Statuslog();
    }
    public function list(Request $request)
    {
        if (Auth::user()) {
            $user = Auth::user();
            $userRole = string_to_array($user->role);
            $search = '';
            if ($request->has('search')) {
                if ($request->search != '' && $request->search != null) {
                    $search = $request->search;
                }
            }
            $ppe_exemption_array = $this->ppeexemption->select('ppe_ppeexemption.*', 'masters_department.department_name', 'masters_unit.unit_name')
                ->join('masters_department', 'ppe_ppeexemption.department', '=', 'masters_department.id')
                ->join('masters_unit', 'ppe_ppeexemption.unit', '=', 'masters_unit.id')
                ->where('masters_department.trash', 'NO')
                ->where('masters_unit.trash', 'NO');

            if (in_array(ROLE_EHS_HEAD, $userRole)) {
                $ppe_exemption_array->whereIn('ppe_ppeexemption.approve_status', [STATUS_EHS_APPROVAL_PENDING, STATUS_EHS_APPROVED, STATUS_EHS_REJECTED]);
            } elseif (in_array(ROLE_HOD, $userRole)) {
                $departmentId = $user->department_id;
                $ppe_exemption_array->where('ppe_ppeexemption.department', $departmentId);
            } elseif (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)) {
            } elseif (in_array(ROLE_STORE_MANAGER, $userRole)) {
                $ppe_exemption_array
                    ->orderBy('ppe_ppeexemption.id', 'DESC');
            } else {
                $ppe_exemption_array->where('ppe_ppeexemption.created_by', Auth::id());
            }

            if (!empty($search)) {
                $searchDate = DBdateformat($search);
                $ppe_exemption_array->where(function ($query) use ($search, $searchDate) {
                    $query->orWhere('ppe_ppeexemption.emp_id', 'LIKE', "%{$search}%")
                        ->orWhereDate('ppe_ppeexemption.created_at', 'LIKE', "%{$searchDate}%")
                        ->orWhere('masters_department.department_name', 'LIKE', "%{$search}%")
                        ->orWhere('masters_unit.unit_name', 'LIKE', "%{$search}%")

                        ->orWhere('ppe_ppeexemption.emp_name', 'LIKE', "%{$search}%");
                });
            }

            $ppe_exemption_array = $ppe_exemption_array->orderBy('ppe_ppeexemption.id', 'DESC')->paginate($request->input('per_page', 10));

            $ppe_request_list = $ppe_exemption_array->toArray();


            $data_array = [];
            foreach ($ppe_exemption_array as $listdata) {


                if (isset($listdata->approve_status) && $listdata->approve_status == 4) {
                    $text = "EHS Head Approval Pending";
                } elseif (isset($listdata->approve_status) && $listdata->approve_status == 5) {
                    $text = "EHS Head Approved";
                } elseif (isset($listdata->approve_status) && $listdata->approve_status == 6) {
                    $text = "EHS Head Rejected";
                }
                $data = [];

                $data['id'] = $listdata->id;
                $data['emp_id'] = $listdata->emp_id;
                $data['emp_name'] = $listdata->emp_name;
                $data['department'] = $listdata->department_name;
                $data['unit_name'] = $listdata->unit_name;
                $data['from_date'] = Displaydateformat($listdata->from_date);
                $data['to_date'] = Displaydateformat($listdata->to_date);
                $data['approve_status'] = $text;
                $data['created_by'] = getUsername($listdata->created_by);
                $data['created_at'] = Displaydateformat($listdata->created_at);

                $data_array[] = $data;
            }

            $ppe_exemption_details = [
                'per_page' => $ppe_request_list['per_page'],
                'current_page' => $ppe_request_list['current_page'],
                'from' => $ppe_request_list['from'],
                'to' => $ppe_request_list['to'],
                'total' => $ppe_request_list['total'],
                'total_page' => $ppe_request_list['last_page'],
                'list' => $data_array,
            ];

            $success = [
                'ppe_exemption_details' => $ppe_exemption_details
            ];
            return $this->sendResponse($success, 'PPE Exemption Details');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    public function store(Request $request)
    {

        try {

            if (Auth::user()) {

                $rules = [
                    'emp_id' => 'required',
                    'emp_name' => 'required',
                    'department' => 'required',
                    'from_date' => 'required',
                    'to_date' => 'required',
                    'reason' => 'required',


                ];
                $messages = [
                    'emp_id.required' => 'Employee Code is Required',
                    'emp_name.required' => 'Employee name is Required',
                    'department.required' => 'Department Name is Required',
                    'from_date.required' => __('From Date is required'),
                    'to_date.required' => __('To Date is required'),
                    'reason.required' => __('Reason is required'),


                ];

                $validator = Validator::make($request->all(), $rules, $messages);

                if ($validator->fails()) {
                    return $this->sendError('Validation Error', $validator->errors(), 422);
                }

                if ($request->request_for == 1) {

                    $employee = Employee::where('emp_id', $request->emp_id)
                        ->select('unit', 'department', 'company')
                        ->first();

                    $unit = $employee->unit ?? null;
                    $department = $employee->department;
                    $company = $employee->company ?? null;
                } elseif ($request->request_for == 2) {
                    $work = Work::where('emp_id', $request->emp_id)
                        ->select('unit', 'department', 'company')
                        ->first();

                    $unit = $work->unit ?? null;
                    $department = $work->department;
                    $company = $work->company ?? null;
                } else {
                    $unit = Auth::user()->unit_id;
                    $department = Auth::user()->department_id;
                    $company = Auth::user()->company_id;
                }
                $insert_array = [
                    'emp_id' => $request->emp_id,
                    'emp_name' => $request->emp_name,
                    'department' => $department,
                    'unit' => $unit,
                    'company' =>  $company,
                    'request_for' => $request->request_for,
                    'from_date' => DBdateformat($request->from_date),
                    'to_date' => DBdateformat($request->to_date),
                    'approve_status' => STATUS_EHS_APPROVAL_PENDING,
                    'reason' => $request->reason,
                    'created_by' => Auth::id(),
                ];
                $ppeexemption = $this->ppeexemption->create($insert_array);
                $this->ppeFiles->store_api($ppeexemption);

                $statuslog = $this->ppestatus->exemptionstatus($ppeexemption);
                $id =  $ppeexemption->id;


                $details = [
                    'emp_id' => $ppeexemption->emp_id,
                    'emp_name' => $ppeexemption->emp_name,
                    'from_date' => $ppeexemption->from_date,
                    'to_date' => $ppeexemption->to_date,
                    'reason' => $ppeexemption->reason,
                    'department' => $ppeexemption->department,
                    'approve_link' => url('ppe_exemption/approval/view/' . encryptID($id)),
                    'reject_link' => url('ppe_exemption/approval/view/' . encryptID($id)),


                ];
                $ehsofficer = $this->user->findEhsHead();

                foreach ($ehsofficer as $officer) {
                    $officer_email = getUseremail($officer->id);
                    if ($officer_email != '' || $officer_email != null) {
                        Mail::to($officer_email)->queue(new PpeExemptionRequestorEmail($details));
                    }
                }
                $id = $ppeexemption->id;
                $message = 'New PPE Exemption Request';

                $assigned_user = $this->user->assigneduser($ehsofficer);
                $img = admin_url('public/assets/images/ppe-management.jpg');
                $notificationData = array(
                    'notification_type' => 1,
                    'module_type' => 1,
                    'notification_message' => $message,
                    'mobile_notification' => json_encode(array(
                        'title' => $message,
                        'message' => $ppeexemption->emp_name . ' has requested a PPE Exemption request on ' . displaydateformat($ppeexemption->created_at) . ' from ' .
                            displaydateformat($ppeexemption->from_date) . ' to ' . displaydateformat($ppeexemption->to_date),
                        'icon' => $img,
                        'module' => 1,
                        'style' => 'font-size: 1rem;'
                    )),
                    'web_link' => admin_url('ppe_exemption/approval/view/' . encryptId($id)),
                    'assigned_user' => array_to_string($assigned_user),
                    'created_by' => Auth::id(),
                );

                notificationSave($notificationData);

                // mobile push notification

                // $notifydata = [
                //     'title' => $message,
                //     'message' =>  $ppeexemption->emp_name . ' has a PPE Exemption at ' . ' created by ' . getUsername($ppeexemption->created_by),
                //     'module_id' => $ppeexemption->id,
                //     'module_type' => 1,
                //     'module_sub_type' => 1,
                // ];
                // mobilePushNotification(array_to_string($assigned_user), $notifydata);
                $success = [
                    'ppe_exemption' => $ppeexemption->id
                ];

                return $this->sendResponse($success, 'PPE Exemption Created successfully');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    public function view(Request $request)
    {
        try {
            if (Auth::user()) {
                $id = $request->id;

                $details = $this->ppeexemption->selectOne($id);
                $empId = $details->emp_id;
                $statusLogs = $this->ppestatus->Exemptionstatuslog_api($id);
                $ppefiles = $this->ppeFiles->getExemptionFile($id);



                $statusLabels = [
                    STATUS_HOD_APPROVAL_PENDING => 'HOD Approval Pending',
                    STATUS_HOD_APPROVED => 'HOD Approved',
                    STATUS_USER_APPLIED => 'User Applied',
                    STATUS_HOD_REJECTED => 'HOD Rejected',
                    STATUS_EHS_APPROVAL_PENDING => 'EHS  Head Approval Pending',
                    STATUS_EHS_APPROVED => 'EHS  Head Approved',
                    STATUS_EHS_REJECTED => 'EHS  Head Rejected',
                    STATUS_ISSUED => 'Issued'
                ];

                $ppestatuslog = [];
                if (!empty($statusLogs)) {
                    foreach ($statusLogs as $value) {
                        $ppestatuslog[] = [
                            'from_status' => $statusLabels[$value->from_status] ?? 'Unknown',
                            'to_status' => $statusLabels[$value->to_status] ?? 'Unknown',
                            'remarks' => $value->remarks,
                            'created_by' => getusername($value->created_by),
                            'created_at' => Displaydateformat($value->created_at),
                        ];
                        if ($value->from_status == STATUS_USER_APPLIED) {
                            $ppestatuslog[] = [
                                'from_status' => 'EHS Head Approval Pending',
                                'to_status' => '-',
                                'remarks' => '-',
                                'created_by' => '-',
                                'created_at' => '-',
                            ];
                        }
                    }
                }
                $files = [];
                if (!empty($ppefiles)) {
                    foreach ($ppefiles as $value) {
                        $files[] = [
                            'file_path' => $value->file_path
                        ];
                    }
                }

                $success = [
                    'id' => $details->id,
                    'emp_id ' => $details->emp_id,
                    'emp_name' => $details->emp_name,
                    'department' => getDepartment($details->department),
                    'from_date' => Displaydateformat($details->from_date),
                    'to_date' => Displaydateformat($details->to_date),
                    'reason' => ($details->reason),
                    'files' => $files,
                    'created_by' => getusername($details->created_by),
                    'created_at' => Displaydateformat($details->created_at),
                    'status_log' => $ppestatuslog,

                ];

                return $this->sendResponse($success, 'PPE Exemption Details');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }


    public function approvereject(Request $request)
    {
        try {

            if (Auth::user()) {

                $rules = [
                    'ppeexemption_id' => 'required',
                    'remarks' => 'required',
                    'status' => 'required',


                ];
                $messages = [
                    'ppeexemption_id.required' => 'id is Required',
                    'remarks.required' => 'Remarks is Required',
                    'status.required' => 'Status is Required',



                ];

                $validator = Validator::make($request->all(), $rules, $messages);

                if ($validator->fails()) {
                    return $this->sendError('Validation Error', $validator->errors(), 422);
                }
                if ($request->status == "approve") {
                    $id = $request->ppeexemption_id;
                    $emp_details = $this->ppeexemption->find($id);
                    $department = $emp_details->department;
                    $departmentId = $this->ppeexemption->findDepartment($department, $id);

                    $updateData = [
                        'remarks' => $request->remarks,
                        'approved_by' => Auth::id(),
                        'approve_status' => STATUS_EHS_APPROVED,
                        'approved_at' => Carbon::now(),
                    ];

                    $statuslog = $this->ppestatus->storeexemptionstatus_api($updateData, $emp_details);
                    $emp_details->updateapproval_api($updateData, $id);

                    $details = [
                        'emp_id' => $emp_details->emp_id,
                        'emp_name' => $emp_details->emp_name,
                        'remarks' => $updateData['remarks'],
                        'status' => $updateData['approve_status'],
                        'department' => $emp_details->department,
                        'unit' => $emp_details->unit,
                        'approved_by' => Auth::id(),
                    ];

                    $empId = $emp_details->created_by;
                    $requestor = $this->pperequest->getrequestemail($empId);
                    $hod = $this->pperequest->getdepartmenthod($departmentId);

                    $recipients = array_filter([$requestor, $hod]);
                    if (!empty($recipients)) {
                        Mail::to($recipients)->queue(new PpeExemptionEmail($details));
                    }

                    $message = 'PPE Exemption Request Approved';
                    $hodId = (array) $this->user->getdepartmenthodId($departmentId);
                    $requestorId = (array) $this->user->getrequestId($empId);
                    $storemanagerId = (array) $this->user->getStoreManagerId();
                    $assigned_user = array_merge($hodId, $requestorId, $storemanagerId);

                    $img = admin_url('public/assets/images/ppe-management.jpg');
                    $notificationData = [
                        'notification_type' => 1,
                        'module_type' => 1,
                        'notification_message' => $message,
                        'mobile_notification' => json_encode([
                            'title' => $message,
                            'message' => getUsername($updateData['approved_by']) . " has " . removeUnderScore(getStatus($updateData['approve_status'])) .
                                " a PPE Exemption request on " . displaydateformat($emp_details->created_at) .
                                " from " . displaydateformat($emp_details->from_date) .
                                " to " . displaydateformat($emp_details->to_date),
                            'icon' => $img,
                            'module' => 1,
                            'style' => 'font-size: 1rem;'
                        ]),
                        'web_link' => admin_url('ppe_exemption/approval/view/' . encryptId($id)),
                        'assigned_user' => array_to_string($assigned_user),
                        'created_by' => Auth::id(),
                    ];

                    notificationSave($notificationData);

                    // Mobile push notification
                    // $notifydata = [
                    //     'title' => $message,
                    //     'message' => $emp_details->emp_name . ' has had their PPE Exemption Approved by ' . getUsername(Auth::id()),
                    //     'module_id' => $emp_details->id,
                    //     'module_type' => 1,
                    //     'module_sub_type' => 1,
                    // ];
                    // mobilePushNotification(array_to_string($assigned_user), $notifydata);
                } elseif ($request->status == "reject") {
                    $id = $request->ppeexemption_id;
                    $emp_details = $this->ppeexemption->find($id);
                    $department = $emp_details->department;
                    $departmentId = $this->ppeexemption->findDepartment($department, $id);

                    $updateData = [
                        'remarks' => $request->remarks,
                        'approved_by' => Auth::id(),
                        'approve_status' => STATUS_EHS_REJECTED,
                        'approved_at' => Carbon::now(),
                    ];

                    $statuslog = $this->ppestatus->storeexemptionstatus_api($updateData, $emp_details);

                    $emp_details->updateapproval_api($updateData, $id);

                    $details = [
                        'emp_id' => $emp_details->emp_id,
                        'emp_name' => $emp_details->emp_name,
                        'remarks' => $updateData['remarks'],
                        'status' => $updateData['approve_status'],
                        'department' => $emp_details->department,
                        'unit' => $emp_details->unit,
                        'approved_by' => Auth::id(),
                    ];

                    $empId = $emp_details->created_by;
                    $requestor = $this->pperequest->getrequestemail($empId);
                    $hod = $this->pperequest->getdepartmenthod($departmentId);

                    $recipients = array_filter([$requestor, $hod]);
                    if (!empty($recipients)) {
                        Mail::to($recipients)->queue(new PpeExemptionRejectEmail($details));
                    }

                    $message = 'PPE Exemption Request Rejected';
                    $hodId = (array) $this->user->getdepartmenthodId($departmentId);
                    $requestorId = (array) $this->user->getrequestId($empId);
                    $assigned_user = array_merge($hodId, $requestorId);

                    $img = admin_url('public/assets/images/ppe-management.jpg');
                    $notificationData = [
                        'notification_type' => 1,
                        'module_type' => 1,
                        'notification_message' => $message,
                        'mobile_notification' => json_encode([
                            'title' => $message,
                            'message' => getUsername($updateData['approved_by']) . " has " . removeUnderScore(getStatus($updateData['approve_status'])) .
                                " a PPE Exemption request on " . displaydateformat($emp_details->created_at) .
                                " from " . displaydateformat($emp_details->from_date) .
                                " to " . displaydateformat($emp_details->to_date),
                            'icon' => $img,
                            'module' => 1,
                            'style' => 'font-size: 1rem;'
                        ]),
                        'web_link' => admin_url('ppe_exemption/approval/view/' . encryptId($id)),
                        'assigned_user' => array_to_string($assigned_user),
                        'created_by' => Auth::id(),
                    ];

                    notificationSave($notificationData);

                    // // Mobile push notification
                    // $notifydata = [
                    //     'title' => $message,
                    //     'message' => $emp_details->emp_name . ' has had their PPE Exemption Rejected by ' . getUsername(Auth::id()),
                    //     'module_id' => $emp_details->id,
                    //     'module_type' => 1,
                    //     'module_sub_type' => 1,
                    // ];
                    // mobilePushNotification(array_to_string($assigned_user), $notifydata);
                }


                $success = [
                    'ppe_exemption' => $id
                ];


                return $this->sendResponse($success, 'Responded successfully');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
