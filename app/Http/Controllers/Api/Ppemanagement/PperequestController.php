<?php

namespace App\Http\Controllers\Api\Ppemanagement;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Mail\PpeEhsRequestEmail;
use App\Mail\PpeRejectRequestEmail;
use App\Mail\PpeRequestEhsRejectEmail;
use App\Mail\PpeRequestHodApprovalEmail;
use App\Mail\PpeRequestRequestorEmail;
use App\Mail\PpeRequestStoremanagerEmail;
use App\Models\Master\Employee;
use App\Models\Master\PpeRequest;
use App\Models\Master\PpeType;
use App\Models\Statuslog;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\Master\PpeStockinventory;
use App\Models\Master\Work;
use Illuminate\Support\Str;

class PperequestController extends BaseController
{
    private $ppetype;
    private $pperequest;
    private $user;
    private $ppestatus;
    private $ppestock;


    public function __construct()
    {
        $this->ppetype = new PpeType();
        $this->pperequest = new PpeRequest();
        $this->user = new User();
        $this->user = new User();
        $this->ppestatus = new Statuslog();
        $this->ppestock = new PpeStockinventory();
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
            $ppe_request_array = $this->pperequest->select('ppe_pperequest.*', 'ppe_pperequest.created_at as ppe_created_at', 'masters_department.department_name',  'inventory2.*', 'ppe_pperequest.id As ppe_request_id')
                ->join('masters_department', 'ppe_pperequest.department', '=', 'masters_department.id')

                ->join('ppe_stock_inventory as inventory2', 'ppe_pperequest.item_code', '=', 'inventory2.id')

                ->where('inventory2.trash', 'NO')
                ->where('masters_department.trash', 'NO')
                ->where('ppe_pperequest.trash', 'NO');

            if (in_array(ROLE_EHS_OFFICER, $userRole)) {
                $ppe_request_array->orderBy('ppe_request_id', 'DESC');
            } elseif (in_array(ROLE_HOD, $userRole)) {
                $departmentId = $user->department_id;
                $ppe_request_array->where('ppe_pperequest.department', $departmentId)
                    ->orderBy('ppe_request_id', 'DESC');
            } elseif (in_array(ROLE_STORE_MANAGER, $userRole)) {
                $ppe_request_array->orderBy('ppe_request_id', 'DESC');
            } elseif (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)) {
            } else {
                $ppe_request_array->where('ppe_pperequest.created_by', Auth::id());
            }

            if (!empty($search)) {

                $searchDate = DBdateformat($search);
                $ppe_request_array->where(function ($query) use ($search, $searchDate) {
                    $query->orWhere('ppe_pperequest.emp_id', 'LIKE', "%{$search}%")
                        ->orWhereRaw("DATE_FORMAT(ppe_pperequest.created_at, '%Y-%m-%d') LIKE ?", ["%{$searchDate}%"])
                        ->orWhere('ppe_pperequest.ppe_name', 'LIKE', "%{$search}%")
                        ->orWhere('masters_department.department_name', 'LIKE', "%{$search}%")
                        ->orWhere('ppe_pperequest.emp_name', 'LIKE', "%{$search}%");
                });
            }



            $ppe_request_array = $ppe_request_array->orderBy('ppe_request_id', 'DESC')->paginate($request->input('per_page', 10));

            $ppe_request_list = $ppe_request_array->toArray();


            $data_array = [];
            foreach ($ppe_request_array as $listdata) {

                if (isset($listdata->approve_status) && $listdata->approve_status == 1) {
                    $text = "HOD Approval Pending";
                } elseif (isset($listdata->approve_status) && $listdata->approve_status == 2) {
                    $text = "HOD Approved";
                } elseif (isset($listdata->approve_status) && $listdata->approve_status == 3) {
                    $text = "HOD Rejected";
                } elseif (isset($listdata->approve_status) && $listdata->approve_status == 4) {
                    if (checkUserRole(4)) {
                        $text = "HOD Approved";
                    } elseif (checkUserRole(3)) {
                        $text = "EHS Officer Approval Pending";
                    } else {
                        $text = "EHS Officer Approval Pending";
                    }
                } elseif (isset($listdata->approve_status) && $listdata->approve_status == 5) {
                    $text = "Store manager Issue Pending";
                } elseif (isset($listdata->approve_status) && $listdata->approve_status == 6) {
                    $text = "EHS Officer Rejected";
                } elseif (isset($listdata->approve_status) && $listdata->approve_status == 8) {
                    $text = "Issued";
                }
                $data = [];

                $data['id'] = $listdata->ppe_request_id;
                $data['emp_id'] = $listdata->emp_id;
                $data['emp_name'] = $listdata->emp_name;
                $data['item_code'] = $listdata->item_code;
                $data['ppe_name'] = $listdata->ppe_name;
                $data['department'] = $listdata->department_name;
                $data['approve_status'] = $text;
                $data['created_by'] = getUsername($listdata->created_by);
                $data['created_at'] = Displaydateformat($listdata->created_at);

                $data_array[] = $data;
            }

            $ppe_request_details = [
                'per_page' => $ppe_request_list['per_page'],
                'current_page' => $ppe_request_list['current_page'],
                'from' => $ppe_request_list['from'],
                'to' => $ppe_request_list['to'],
                'total' => $ppe_request_list['total'],
                'total_page' => $ppe_request_list['last_page'],
                'list' => $data_array,
            ];

            $success = [
                'ppe_request_details' => $ppe_request_details
            ];


            return $this->sendResponse($success, 'PPE Request Details');
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
                    'item_code' => 'required',
                    // 'department_id' => 'required',
                    'ppe_name' => 'required',


                ];
                $messages = [
                    'emp_id.required' => 'Employee Code is Required',
                    'emp_name.required' => 'Employee name is Required',
                    'item_code.required' => 'Item code is Required',
                    // 'department_id.required' => 'Department Name is Required',
                    'ppe_name.required' => 'PPE Name is Required',
                ];

                $validator = Validator::make($request->all(), $rules, $messages);

                if ($validator->fails()) {
                    return $this->sendError('Validation Error', $validator->errors(), 422);
                }
                $request = request();
                $image_data = null;

                if ($request->has('ppe_image')) {
                    $sign = $request->ppe_image;
                    $fileExt = $request->ppe_file_extension  ?? 'png';

                    if ($sign) {
                        $upload_path =  'uploads/ppe_files';

                        if (!File::exists(public_path($upload_path))) {
                            File::makeDirectory(public_path($upload_path), 0777, true, true);
                        }

                        $file_name = time() . Str::random(10) . '.' . $fileExt;
                        $file_path = $upload_path . '/' . $file_name;

                        $image_data = base64_decode($sign);
                        file_put_contents(public_path($file_path), $image_data);
                    }
                }
                if ($request->request_for == 1) {

                    $employee = Employee::where('emp_id', $request->emp_id)
                        ->select('unit', 'department', 'company')
                        ->first();

                    $unit = $employee->unit;
                    $department = $employee->department;
                } elseif ($request->request_for == 2) {
                    $work = Work::where('emp_id', $request->emp_id)
                        ->select('unit', 'department', 'company')
                        ->first();

                    $unit = $work->unit;
                    $department = $work->department;
                } else {
                    $unit = Auth::user()->unit_id;
                    $department = Auth::user()->department_id;
                }
                $insert_array = array(
                    'emp_id' => $request->emp_id,
                    'emp_name' => $request->emp_name,
                    'department' => $department,
                    'unit_id' => $unit,
                    'request_for' => $request->request_for,
                    'item_code' => $request->item_code,
                    'ppe_type' => $request->ppe_type_id,
                    'ppe_name' => $request->ppe_name,
                    'employee_reason' => $request->employee_reason,
                    'ppe_image' => isset($file_path) ? $file_path : null,
                    'employee_remarks' => $request->employee_remarks,
                    'approve_status' => STATUS_HOD_APPROVAL_PENDING,
                    'created_by' => Auth::id()
                );


                $pperequest = $this->pperequest->create($insert_array);

                $id = $pperequest->id;
                $details = [
                    'emp_name' => $pperequest->emp_name,
                    'emp_id' => $pperequest->emp_id,
                    'department' => $pperequest->department,
                    'item_code' => $pperequest->item_code,
                    'remarks' => $pperequest->employee_reason,
                    'approve_link' => url('ppe_request/hodapproval/view/' . encryptID($id)),
                    'reject_link' => url('ppe_request/hodapproval/view/' . encryptID($id))
                ];
                $statuslog = $this->ppestatus->storestatus($pperequest, $id);
                $departmentId =   $pperequest->department;
                $hod = $this->pperequest->getdepartmenthod($departmentId);
                if (!empty($hod)) {
                    Mail::to($hod)->queue(new PpeRequestRequestorEmail($details));
                }
                $message = 'New PPE Request';
                $hodId = $this->user->getdepartmenthodId($departmentId);
                $img = admin_url('public/assets/images/ppe-management.jpg');

                $notificationData = [
                    'notification_type' => 1,
                    'module_type' => 1,
                    'notification_message' => $message,
                    'mobile_notification' => json_encode([
                        'title' => $message,
                        'message' => $pperequest->emp_name . ' has a PPE request at ' . displaydateformat($pperequest->created_at) . ' on ' . getPpename($pperequest->ppe_name) . ' from ' . getDepartment($pperequest->department) . ' DEPARTMENT ',
                        'icon' => $img,
                        'module' => 1,
                        'style' => 'font-size: 1rem;'
                    ]),
                    'web_link' => admin_url('ppe_request/hodapproval/view/' . encryptId($id)),
                    'assigned_user' => array_to_string($hodId),
                    'created_by' => Auth::id(),
                ];

                notificationSave($notificationData);

                /**
                 * Send Mobile Push notification
                 */

                $notifydata = [
                    'title' => $message,
                    'message' =>  $pperequest->emp_name . ' has a PPE request at ' . ' created by ' . getUsername($pperequest->created_by),
                    'module_id' => $pperequest->id,
                    'module_type' => 1,
                    'module_sub_type' => 1,
                ];
                mobilePushNotification(array_to_string($hodId), $notifydata);
                $success = [
                    'ppe_request' => $pperequest->id
                ];

                return $this->sendResponse($success, 'PPE Request Created successfully');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
            }
        } catch (Exception $ex) {
        dd($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    public function view(Request $request)
    {
        try {
            if (Auth::user()) {
                $id = $request->id;

                $details = $this->pperequest->selectOne($id);
                $empId = $details->emp_id;
                $statusLogs = $this->ppestatus->statuslog_api($id);
                $previoushistory = $this->pperequest->getuserdata($empId);


                $statusLabels = [
                    STATUS_HOD_APPROVAL_PENDING => 'HOD Approval Pending',
                    STATUS_HOD_APPROVED => 'HOD Approved',
                    STATUS_USER_APPLIED => 'User Applied',
                    STATUS_HOD_REJECTED => 'HOD Rejected',
                    STATUS_EHS_APPROVAL_PENDING => 'EHS Officer Approval Pending',
                    STATUS_EHS_APPROVED => 'EHS Officer Approved',
                    STATUS_EHS_REJECTED => 'EHS Officer Rejected',
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
                        $ppestatuslog[] = [
                            'from_status' => 'HOD Approval Pending',
                            'to_status' => '-',
                            'remarks' => '-',
                            'created_by' => '-',
                            'created_at' => '-',
                        ];
                        $ppestatuslog[] = [
                            'from_status' => 'EHS Offcer Approval Pending',
                            'to_status' => '-',
                            'remarks' => '-',
                            'created_by' => '-',
                            'created_at' => '-',
                        ];
                        $ppestatuslog[] = [
                            'from_status' => 'Store manager Issue Pending',
                            'to_status' => '-',
                            'remarks' => '-',
                            'created_by' => '-',
                            'created_at' => '-',
                        ];
                    }
                }

                $history = [];
                if (!empty($previoushistory)) {
                    foreach ($previoushistory as $value) {
                        $history[] = [
                            'emp_name' => $value->emp_name,
                            'emp_id' => $value->emp_id,
                            'created_at' => Displaydateformat($value->created_at),
                            'approve_status' => $statusLabels[$value->approve_status] ?? 'Unknown',
                            'remarks' => ($value->remarks),
                        ];
                    }
                }

                $success = [
                    'id' => $details->id,
                    'emp_id' => $details->emp_id,
                    'emp_name' => $details->emp_name,
                    'department' => getDepartment($details->department),
                    'item_code' => getItemCode($details->item_code),
                    'ppe_name' => $details->ppe_name,
                    'ppe_image' => $details->ppe_image,
                    'remarks' => $details->employee_reason ?? $details->employee_remarks,
                    'created_by' => getusername($details->created_by),
                    'created_at' => Displaydateformat($details->created_at),
                    'status_log' => $ppestatuslog,
                    'previous_history' => $history
                ];

                return $this->sendResponse($success, 'PPE Request Details');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
            }
        } catch (Exception $ex) {
            dd($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }


    public function hodapproval(Request $request)
    {
        try {

            if (Auth::user()) {

                $rules = [
                    'pperequest_id' => 'required',
                    'remarks' => 'required',
                    'status' => 'required',


                ];
                $messages = [
                    'pperequest_id.required' => 'id is Required',
                    'remarks.required' => 'Remarks is Required',
                    'status.required' => 'Status is Required',



                ];

                $validator = Validator::make($request->all(), $rules, $messages);

                if ($validator->fails()) {
                    return $this->sendError('Validation Error', $validator->errors(), 422);
                }
                if ($request->status == "approve") {
                    $id = $request->pperequest_id;
                    $emp_details = $this->pperequest->find($id);

                    $department = $emp_details->department;
                    $departmentId = $this->pperequest->findDepartment($department, $id);

                    $updateData = [
                        'remarks' => $request->remarks,
                        'approved_at' => Carbon::now(),
                        'approved_by' => Auth::id(),
                        'approve_status' => STATUS_EHS_APPROVAL_PENDING,
                        'status' => 1
                    ];


                    $updatedatas = [
                        'remarks' =>  $request->remarks,
                        'approved_at' => Carbon::now(),
                        'approved_by' => Auth::id(),
                        'approve_status' => STATUS_HOD_APPROVED,
                        'status' => 1
                    ];

                    $this->ppestatus->store($updatedatas, $emp_details);

                    $emp_details->updateapproval($updateData, $id);



                    $details = [
                        'emp_id' => $emp_details->emp_id,
                        'emp_name' => $emp_details->emp_name,
                        'remarks' => $updateData['remarks'],
                        'status' => $updateData['approve_status'],
                        'department' => $emp_details->department,
                        'approved_by' => $updateData['approved_by'],

                    ];

                    $ehsofficer = $this->user->findEhsofficer();
                    $empId = $emp_details->created_by;
                    $requestor = $this->user->finduseremail($empId);

                    foreach ($ehsofficer as $officer) {
                        $officer_email = $officer->email;
                        if (!empty($officer_email)) {
                            Mail::to($officer_email)->queue(new PpeRequestHodApprovalEmail($details));
                        }
                    }

                    $message = 'New PPE Request';
                    $EhsId = $this->user->assigneduser($ehsofficer);
                    $img = admin_url('public/assets/images/ppe-management.jpg');

                    $notificationData = [
                        'notification_type' => 1,
                        'module_type' => 1,
                        'notification_message' => $message,
                        'mobile_notification' => json_encode([
                            'title' => $message,
                            'message' => getUsername($updateData['approved_by']) . " has " . removeUnderScore(getStatus($updateData['approve_status']))  . " a PPE request at " . displaydateformat($emp_details->created_at) . " on " . getPpename($emp_details->ppe_name) . " from " . getDepartment($emp_details->department) . " DEPARTMENT",
                            'icon' => $img,
                            'module' => 1,
                            'style' => 'font-size: 1rem;'
                        ]),
                        'web_link' => url('ppe_request/ehsapproval/view/' . encryptId($id)),
                        'assigned_user' => array_to_string($EhsId),
                        'created_by' => Auth::id(),
                    ];

                    notificationSave($notificationData);
                    // Mobile push notification
                    // $notifydata = [
                    //     'title' => $message,
                    //     'message' => $emp_details->emp_name . ' has had their PPE Request Approved by ' . getUsername(Auth::id()),
                    //     'module_id' => $emp_details->id,
                    //     'module_type' => 1,
                    //     'module_sub_type' => 1,
                    // ];
                    // mobilePushNotification(array_to_string($EhsId), $notifydata);
                } elseif ($request->status == "reject") {
                    $id = $request->pperequest_id;
                    $emp_details = $this->pperequest->find($id);
                    $department = $emp_details->department;
                    $departmentId = $this->pperequest->findDepartment($department, $id);

                    $updateData = [
                        'remarks' => $request->remarks,
                        'approved_at' => Carbon::now(),
                        'approved_by' => Auth::id(),
                        'approve_status' => STATUS_HOD_REJECTED,
                        'status' => 1
                    ];


                    $updatedatas = [
                        'remarks' =>  $request->remarks,
                        'approved_at' => Carbon::now(),
                        'approved_by' => Auth::id(),
                        'approve_status' => STATUS_HOD_REJECTED,
                        'status' => 1
                    ];

                    $this->ppestatus->store($updatedatas, $emp_details);

                    $emp_details->updateapproval($updateData, $id);



                    $details = [
                        'emp_id' => $emp_details->emp_id,
                        'emp_name' => $emp_details->emp_name,
                        'remarks' => $updateData['remarks'],
                        'status' => $updateData['approve_status'],
                        'department' => $emp_details->department,
                        'approved_by' => $updateData['approved_by'],

                    ];
                    $empId = $emp_details->created_by;
                    $requestor = $this->user->finduseremail($empId);
                    $message = 'New PPE Request';
                    if (!empty($requestor)) {
                        Mail::to($requestor)->queue(new PpeRejectRequestEmail($details));
                    }
                    $img = admin_url('public/assets/images/ppe-management.jpg');
                    $requestorId = $this->user->getrequestId($empId);
                    $notificationData = [
                        'notification_type' => 1,
                        'module_type' => 1,
                        'notification_message' => $message,
                        'mobile_notification' => json_encode([
                            'title' => $message,
                            'message' => getUsername($updateData['approved_by']) . " has " . removeUnderScore(getStatus($updateData['approve_status']))  . " a PPE request at " . displaydateformat($emp_details->created_at) . " on " . getPpename($emp_details->ppe_name) . " from " . getDepartment($emp_details->department) . " DEPARTMENT",
                            'icon' => $img,
                            'module' => 1,
                            'style' => 'font-size: 1rem;'
                        ]),
                        'web_link' => url('ppe_request/ehsapproval/view/' . encryptId($id)),
                        'assigned_user' => array_to_string($requestorId),
                        'created_by' => Auth::id(),
                    ];

                    notificationSave($notificationData);
                    // Mobile push notification
                    // $notifydata = [
                    //     'title' => $message,
                    //     'message' => $emp_details->emp_name . ' has had their PPE Request Rejected by ' . getUsername(Auth::id()),
                    //     'module_id' => $emp_details->id,
                    //     'module_type' => 1,
                    //     'module_sub_type' => 1,
                    // ];
                    // mobilePushNotification( $emp_details->created_by, $notifydata);
                }


                $success = [
                    'ppe_request' => $id
                ];


                return $this->sendResponse($success, 'Responded  successfully');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    public function ehsapproval(Request $request)
    {
        try {

            if (Auth::user()) {

                $rules = [
                    'pperequest_id' => 'required',
                    'remarks' => 'required',
                    'status' => 'required',


                ];
                $messages = [
                    'pperequest_id.required' => 'id is Required',
                    'remarks.required' => 'Remarks is Required',
                    'status.required' => 'Status is Required',



                ];

                $validator = Validator::make($request->all(), $rules, $messages);

                if ($validator->fails()) {
                    return $this->sendError('Validation Error', $validator->errors(), 422);
                }
                if ($request->status == "approve") {
                    $id = $request->pperequest_id;
                    $empDetails = $this->pperequest->find($id);
                    $departmentId = $empDetails->department;
                    $hod = $this->user->findDepartmenthod($departmentId);
                    // $storemanager = $this->user->findStoremanager();
                    $user_role = ROLE_STORE_MANAGER;
                    $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
                    $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();

                    $updateEhsData = [
                        'remarks' => $request->remarks,
                        'approved_at' => Carbon::now(),
                        'approved_by' => Auth::id(),
                        'approve_status' => STATUS_EHS_APPROVED,
                        'status' => 1
                    ];


                    $statuslog = $this->ppestatus->storeEhsStatus($updateEhsData, $empDetails);
                    $empDetails->updateehsapproval($updateEhsData, $id);
                    $details = [
                        'emp_id' => $empDetails->emp_id,
                        'emp_name' => $empDetails->emp_name,
                        'remarks' => $updateEhsData['remarks'],
                        'status' => $updateEhsData['approve_status'],
                        'department' => $empDetails->department,
                        'approved_by' => $empDetails->approved_by,
                    ];



                    $empId = $empDetails->created_by;
                    $requestor = $this->user->getrequestEmail($empId);
                    $recipients = array_filter([$requestor, $hod]);



                    if (!empty($recipients)) {
                        Mail::to($recipients)->queue(new PpeEhsRequestEmail($details));
                    }

                    $Storedetails = [
                        'emp_id' => $empDetails->emp_id,
                        'emp_name' => $empDetails->emp_name,
                        'remarks' => $updateEhsData['remarks'],
                        'status' => $updateEhsData['approve_status'],
                        'department' => $empDetails->department,
                        'approved_by' => $empDetails->approved_by,
                        'approve_link' => admin_url('ppe_request/ehsapproval/view/' . encryptId($id)),
                    ];
                    if (count($users) > 0) {
                        foreach ($users as $user) {
                            $email_id = $user->email;
                            if ($email_id != '' || $email_id != null) {
                                Mail::to($email_id)->queue(new PpeRequestStoremanagerEmail($Storedetails));
                            }
                        }
                    }

                    $id = $empDetails->id;
                    $message = 'New PPE Request';
                    $hodId = $this->user->getdepartmenthodId($departmentId);
                    $storemanagerId = $this->user->getStoreManagerId();
                    $img = admin_url('public/assets/images/ppe-management.jpg');
                    $requestorId = $this->user->getrequestId($empId);
                    $assignedUsers = array_filter(array_merge($hodId, $requestorId, $storemanagerId));
                    $assignedUserString = array_to_string($assignedUsers);

                    $notificationData = [
                        'notification_type' => 1,
                        'module_type' => 1,
                        'notification_message' => $message,
                        'mobile_notification' => json_encode([
                            'title' => $message,
                            'message' => getUsername($updateEhsData['approved_by']) . " has " . removeUnderScore(getStatus($updateEhsData['approve_status']))  . " a PPE request at " . displaydateformat($empDetails->created_at) . " on " . getPpename($empDetails->ppe_name) . " from " . getDepartment($empDetails->department) . " DEPARTMENT",
                            'icon' => $img,
                            'style' => 'font-size: 1rem;',
                            'module' => 1,
                        ]),
                        'web_link' => admin_url('ppe_request/view/' . encryptId($id)),
                        'assigned_user' => $assignedUserString,
                        'created_by' => Auth::id(),
                    ];

                    notificationSave($notificationData);


                    // // Mobile push notification
                    // $notifydata = [
                    //     'title' => $message,
                    //     'message' => $empDetails->emp_name . ' has had their PPE Request Approved by ' . getUsername(Auth::id()),
                    //     'module_id' => $empDetails->id,
                    //     'module_type' => 1,
                    //     'module_sub_type' => 1,
                    // ];
                    // mobilePushNotification($assignedUserString, $notifydata);

                } elseif ($request->status == "reject") {
                    $id = $request->pperequest_id;
                    $empDetails = $this->pperequest->find($id);
                    $department = $empDetails->department;
                    $departmentId = $this->pperequest->findDepartment($department, $id);
                    $empId = $empDetails->created_by;
                    $hod = $this->user->findDepartmenthod($departmentId);
                    $requestor = $this->user->getrequestEmail($empId);
                    $recipients = array_filter([$requestor, $hod]);
                    $updateEhsData = [
                        'remarks' => $request->remarks,
                        'approved_at' => Carbon::now(),
                        'approved_by' => Auth::id(),
                        'approve_status' => STATUS_EHS_REJECTED,
                        'status' => 1
                    ];


                    $statuslog = $this->ppestatus->storeEhsStatus($updateEhsData, $empDetails);
                    $empDetails->updateehsapproval($updateEhsData, $id);
                    $details = [
                        'emp_id' => $empDetails->emp_id,
                        'emp_name' => $empDetails->emp_name,
                        'remarks' => $updateEhsData['remarks'],
                        'status' => $updateEhsData['approve_status'],
                        'department' => $empDetails->department,
                        'approved_by' => $empDetails->approved_by,
                    ];

                    if (!empty($recipients)) {
                        Mail::to($recipients)->queue(new PpeRequestEhsRejectEmail($details));
                    }
                    $message = 'New PPE Request';
                    $img = admin_url('public/assets/images/ppe-management.jpg');
                    $requestorId = $this->user->getrequestId($empId);
                    $hodId = $this->user->getdepartmenthodId($departmentId);
                    $storemanagerId = $this->user->getStoreManagerId();
                    $assignedUsers = array_filter(array_merge($hodId, $requestorId, $storemanagerId));
                    $assignedUserString = implode(',', $assignedUsers);
                    $notificationData = [
                        'notification_type' => 1,
                        'module_type' => 1,
                        'notification_message' => $message,
                        'mobile_notification' => json_encode([
                            'title' => $message,
                            'message' => getUsername($updateEhsData['approved_by']) . " has " . removeUnderScore(getStatus($updateEhsData['approve_status']))  . " a PPE request at " . displaydateformat($empDetails->created_at) . " on " . getPpename($empDetails->ppe_name) . " from " . getDepartment($empDetails->department) . " DEPARTMENT",
                            'icon' => $img,
                            'style' => 'font-size: 1rem;',
                        ]),
                        'web_link' => admin_url('ppe_request/view/' . encryptId($id)),
                        'assigned_user' =>  $assignedUserString,
                        'created_by' => Auth::id(),
                    ];

                    notificationSave($notificationData);
                    // Mobile push notification
                    // $notifydata = [
                    //     'title' => $message,
                    //     'message' => $empDetails->emp_name . ' has had their PPE Request Rejected by ' . getUsername(Auth::id()),
                    //     'module_id' => $empDetails->id,
                    //     'module_type' => 1,
                    //     'module_sub_type' => 1,
                    // ];
                    // mobilePushNotification(array_to_string($requestorId), $notifydata);
                }


                $success = [
                    'ppe_request' => $id
                ];


                return $this->sendResponse($success, 'Responded  successfully');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
            }
        } catch (Exception $ex) {
            report($ex);
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    public function smapproval(Request $request)
    {
        try {

            if (Auth::user()) {

                $rules = [
                    'pperequest_id' => 'required',
                    'remarks' => 'required',
                    'status' => 'required',


                ];
                $messages = [
                    'pperequest_id.required' => 'id is Required',
                    'remarks.required' => 'Remarks is Required',
                    'status.required' => 'Status is Required',



                ];

                $validator = Validator::make($request->all(), $rules, $messages);

                if ($validator->fails()) {
                    return $this->sendError('Validation Error', $validator->errors(), 422);
                }
                $id = $request->pperequest_id;
                $empDetails = $this->pperequest->find($id);
                $itemId = $empDetails->item_code;


                $ItemCode = $this->ppestock->Quantitydata($itemId);
                $Oldquantity = $ItemCode->quantity;

                if ($request->status == "issue") {
                    $newQuantity = $Oldquantity - 1;

                    $updateStatus = [
                        'remarks' => $request->remarks,
                        'approved_at' => Carbon::now(),
                        'approved_by' => Auth::id(),
                        'approve_status' => STATUS_ISSUED,
                    ];
                    $storeStatus = [
                        'remarks' =>  $request->remarks,
                        'approved_at' => Carbon::now(),
                        'approved_by' => Auth::id(),
                        'approve_status' => STATUS_ISSUED,
                        'status' => 0,
                    ];


                    $this->ppestock->updateQuantity($itemId, $newQuantity);
                    $this->ppestatus->storemangerstatus($updateStatus, $empDetails);
                    $this->pperequest->updatestoremanager($storeStatus, $id);
                }

                $success = [
                    'ppe_request' => $id
                ];
                return $this->sendResponse($success, 'Responded  successfully');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
