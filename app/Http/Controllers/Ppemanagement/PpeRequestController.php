<?php

namespace App\Http\Controllers\Ppemanagement;

use App\Http\Controllers\Controller;
use App\Mail\PpeEhsRequestEmail;
use App\Mail\PpeRejectRequestEmail;
use App\Mail\PpeRequestEhsRejectEmail;
use App\Mail\PpeRequestEmail;
use App\Mail\PpeRequestHodApprovalEmail;
use App\Mail\PpeRequestRequestorEmail;
use App\Mail\PpeRequestStoremanagerEmail;
use App\Models\Master\Employee;
use App\Models\Master\PpeRequest;
use App\Models\Master\PpeStockinventory;
use App\Models\Master\PpeType;
use App\Models\Master\PpeTypeMaster;
use App\Models\Statuslog;

use App\Models\UploadLog;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;

class PpeRequestController extends Controller
{
    private $ppetypemaster;
    private $ppetype;
    private $pperequest;
    private $employee;
    private $user;
    private $uploadlog;
    private $ppestock;
    private $ppestatus;

    public function __construct()
    {
        $this->ppetypemaster = new PpeTypeMaster();
        $this->ppetype = new PpeType();
        $this->uploadlog = new UploadLog();
        $this->pperequest = new PpeRequest();
        $this->employee = new Employee();
        $this->user = new User();
        $this->ppestock = new PpeStockinventory();
        $this->ppestatus = new Statuslog();
    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->pperequest->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type='1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type='0'>In-Active</span>";
                            }
                            return $text;
                        })
                        ->addColumn('created_at', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->editColumn('department', function ($row) {
                            return $row->department_name;
                        })
                        ->editColumn('ppe_type', function ($row) {
                            return $row->ppe_type;
                        })
                        ->editColumn('ppe_name', function ($row) {
                            return $row->ppe_name;
                        })
                        ->addColumn('approve_status', function ($row) {

                            if ($row->approve_status == STATUS_HOD_APPROVAL_PENDING) {
                                $text = "<span class='badge bg-warning'>HOD Approval Pending</span>";
                            } else if ($row->approve_status == STATUS_HOD_APPROVED) {
                                $text = "<span class='badge bg-success'>HOD Approved</span>";
                            } else if ($row->approve_status == STATUS_HOD_REJECTED) {
                                $text = "<span class='badge bg-danger'>HOD Rejected</span>";
                            }
                            return $text;
                        })
                        ->addColumn('ehs_approve_status', function ($row) {

                            if ($row->ehs_approve_status == STATUS_EHS_APPROVAL_PENDING) {
                                $text = "<span class='badge bg-warning'>EHS Approval Pending</span>";
                            } else if ($row->ehs_approve_status == STATUS_EHS_APPROVED) {
                                $text = "<span class='badge bg-success'>EHS Approved</span>";
                            } else if ($row->approve_status == STATUS_HOD_REJECTED) {
                                $text = "<span class='badge bg-danger '>HOD Rejected</span>";
                            } else if ($row->ehs_approve_status == STATUS_EHS_REJECTED) {
                                $text = "<span class='badge bg-danger '>EHS  Rejected</span>";
                            }
                            return $text;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            if (CheckUserPermission('view')) {
                                $btn .= '<a href="' . admin_url('ppe_request/view/' . encryptId($row->id)) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            // if (CheckUserPermission('edit')) {
                            //     $btn .= '<a href="' . admin_url('ppe_request/edit/' . encryptId($row->id)) . '" class="" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a> ';
                            // }
                            // $btn .= '<a href="javascript:void(0);" data-id="' . encryptId($row->id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger"></i></a> ';

                            if ((CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_HOD)) && $row->approve_status == STATUS_HOD_APPROVAL_PENDING) {
                                $btn .= '<a href="' . admin_url('ppe_request/hodapproval/view/' . encryptId($row->id)) . '" class="" title="Approval"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }

                            if ((CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_OFFICER)) && $row->approve_status == STATUS_HOD_APPROVED && $row->ehs_approve_status == STATUS_EHS_APPROVAL_PENDING) {
                                $btn .= '<a href="' . admin_url('ppe_request/ehsapproval/view/' . encryptId($row->id)) . '" class="" title="EhsApproval"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            $btn .= '<a href="' . admin_url('ppe_request/generalpdf/' . encryptId($row->id)) . '" class="" title="Pdf"> <i class="fa-solid fa-file-pdf" style="color: #e67265;"></i></a> ';

                            return $btn;
                        })

                        ->rawColumns(['action', 'created_at', 'created_by', 'approve_status', 'ehs_approve_status'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);

                    return response()->json($datatables->getData());
                } catch (Exception $ex) {
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => __('ppe.please_try_after_some_time')], 406);
                }
            }
        }

        $ppetype = $this->ppetype->getPpetypedata();

        $ppename = $this->ppetypemaster->getppetypemaster();
        $data = [
            'ppetype' => $ppetype,
            'ppename' => $ppename,
        ];

        return view('ppemanagement.pperequest.list', $data);
    }

    public function add(Request $request)
    {
        $employee = $this->user->getEmployeedata();
        $ppetypedata = $this->ppetype->getPpetypedata();
        $ppetypemaster = $this->ppetypemaster->getppetypemaster();
        $userdata = $this->pperequest->userdata();
        $data = [
            'employee' => $employee,
            'ppetypedata' => $ppetypedata,
            'ppetypemaster' => $ppetypemaster,
            'userdata' => $userdata

        ];
        return view('ppemanagement.pperequest.add', $data);
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'item_code'=>'required',
                'ppe_type' => 'required',
                'ppe_name' => 'required',

            ];
            $messages = [
                'item_code.required' => __('Item Code is required'),
                'ppe_name.required' => __('PPE Name is required'),
                'ppe_type.required' => __('PPE Type  is required'),

            ];


            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $lastStatus = $this->pperequest->laststatus();
            $lastPPERequest = $this->pperequest->lastPpeRequest();
            $chemicaldepartment = $this->pperequest->lastsixmonthrequest();

            switch (true) {
                case $lastStatus && $lastStatus->status == 1:
                    Session::flash('error', __('Invalid request. Last PPE request is still active.'));
                    return redirect(admin_url('ppe_request/list'));

                case $chemicaldepartment:
                    $RequestDate = Carbon::parse($chemicaldepartment->created_at)->addMonths(6);
                    $currentDate = Carbon::now();

                    if (!$RequestDate->lt($currentDate)) {
                        Session::flash('error', __('PPE Request is not allowed within six months'));
                        return redirect(admin_url('ppe_request/list'));
                    }
                    break;

                case $lastPPERequest:
                    $lastRequestDate = Carbon::parse($lastPPERequest->created_at)->addYear();
                    $currentDate = Carbon::now();

                    if (!$lastRequestDate->lt($currentDate)) {
                        Session::flash('error', __('PPE Request is not allowed within one year'));
                        return redirect(admin_url('ppe_request/list'));
                    }
                    break;

                default:
                    break;
            }

            try {
                $pperequest = $this->pperequest->store();



                // Mail
                $id = $pperequest->id;

                $departmentId = $pperequest->department;

                $hod = $this->pperequest->getdepartmenthod($departmentId);
                $details = [
                    'emp_name' => $pperequest->emp_name,
                    'emp_id' => $pperequest->emp_id,
                    'department' => $pperequest->department,
                    'item_code' => $pperequest->item_code,
                    'remarks' => $pperequest->employee_reason,

                    'approve_link' => url('ppe_request/hodapproval/view/' . encryptID($id)),
                    'reject_link' => url('ppe_request/hodapproval/view/' . encryptID($id))
                ];
                Mail::to($hod)->send(new PpeRequestRequestorEmail($details));

                // Notification

                $message = 'New PPE Request';
                $hodId = $this->user->getdepartmenthodId($departmentId);
                $img = admin_url('public/assets/images/ppe-management.jpg');


                $notificationData = [
                    'notification_type' => 1,
                    'module_type' => 3,
                    'notification_message' => $message,
                    'mobile_notification' => json_encode([
                        'title' => $message,
                        'message' => $pperequest->emp_name . ' has a PPE request at ' . displaydateformat($pperequest->created_at) . ' on ' . getPpename($pperequest->ppe_name) . ' from ' . getDepartment($pperequest->department) . ' DEPARTMENT ',
                        'icon' => $img,
                        'module' => 1,
                    ]),
                    'web_link' => admin_url('ppe_request/hodapproval/view/' . encryptId($id)),
                    'assigned_user' => array_to_string($hodId),
                    'created_by' => Auth::id(),
                ];

                notificationSave($notificationData);
                Session::flash('success', __('Your data has been created successfully!'));
                return redirect(admin_url('ppe_request/list'));
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
                return redirect(admin_url('ppe_request/list'));
            }
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ppe_request/list'));
        }
    }


    public function view(Request $request)
    {

        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $pperequest = $this->pperequest->selectOne($id);
            }
            $userdata = $this->pperequest->userdata();
            $ppestatuslog = $this->ppestatus->getstatusdetails($id);
            $data = [
                'pperequest' =>  $pperequest,
                'encryptid' => $request->id,
                'userdata' => $userdata,
                'ppestatuslog' => $ppestatuslog
            ];
            return view('ppemanagement.pperequest.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function pdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $pperequest = $this->pperequest->selectOne($id);
            }
            $ppestatuslog = $this->ppestatus->getstatusdetails($id);
            $userdata = $this->pperequest->userdata();

            $data = [
                'userdata'=>$userdata,
                'pperequest' => $pperequest,
                'ppestatuslog' => $ppestatuslog,
                'pagetitle' => "PPE request",
            ];

            $property = [
                'tempDir' => 'public/pdf/temp/',
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,

            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $html = view('ppemanagement.pperequest.exportpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "PPE_request.pdf";
            return $mpdf->Output($filename, 'I');
        } catch (Exception $ex) {
            dd($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }

    public function hodApprovalview(Request $request)
    {

        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $pperequest = $this->pperequest->selectOne($id);
            }
            $hodstatus = STATUS_HOD_APPROVAL_PENDING;
            $status = $pperequest->approve_status;
            if ($status !=  $hodstatus) {
                Session::flash('success','You Have already responded to the user');
                return redirect(admin_url('ppe_request/view/' . encryptId($id)));
            }
            $data = [
                'pperequest' =>  $pperequest,
                'encryptid' => $request->id,
            ];
            return view('ppemanagement.pperequest.hodapproval', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function storehodapproval(Request $request)
    {
        $rules = [
            'remarks' => 'required|min:3|max:255|regex:/^[a-zA-Z].*/',
        ];
        $messages = [
            'remarks.required' => 'Remarks Field is Mandatory',
            'remarks.min' => 'Minimum 3 characters are required',
            'remarks.max' => 'Maximum limit is 255 characters',
            'remarks.regex' => 'First character should be an alphabet',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $id = decryptId($request->id);
        $remarks = $request->input('remarks');
        $approved_at = $request->input('date');
        $action = $request->input('action');
        $approveStatus = $action == 'approve' ? STATUS_HOD_APPROVED : STATUS_HOD_REJECTED;
        $dateTime = Carbon::createFromFormat('d-m-Y H:i:s', $approved_at);
        try {
            $empDetails = $this->pperequest->find($id);

            $updateData = [
                'approve_msg' => $remarks,
                'approved_at' => $dateTime,
                'approved_by' => Auth::id(),
                'approve_status' => $approveStatus,
                'status' => $action == 'approve' ? 1 : 0
            ];

            if ($action == 'reject') {
                $reject = $action == 'reject' ? STATUS_HOD_REJECTED : '';
                $updateData['ehs_approve_status'] = $reject;
                $updateData['remarks'] = $remarks;
                $updateData['ehs_approved_at'] = $dateTime;
                $updateData['ehs_approved_by'] = Auth::id();
            }

            $statuslog = $this->ppestatus->store($updateData, $empDetails);
            $empDetails->updateapproval($updateData, $id);
            $details = [
                'emp_id' => $empDetails->emp_id,
                'emp_name' => $empDetails->emp_name,
                'remarks' => $updateData['approve_msg'],
                'status' => $updateData['approve_status'],
                'department' => $empDetails->department,
                'approved_by' =>  $updateData['approved_by'],
                'approve_link' => url('ppe_request/ehsapproval/view/' . encryptID($id)),
                'reject_link' => url('ppe_request/ehsapproval/view/' . encryptID($id)),
            ];
            $ehsofficer = $this->user->findEhsofficer();
            $empId = $empDetails->emp_id;

            $requestor = $this->user->finduseremail($empId);
            if ($action == 'approve') {
                foreach ($ehsofficer as $officer) {

                    $officer_email = $officer->email;

                    Mail::to($officer_email)->send(new PpeRequestHodApprovalEmail($details));
                }
                $id = $empDetails->id;
                $message = 'New PPE Request';
                $EhsId = $this->user->assigneduser($ehsofficer);
                $img = admin_url('public/assets/images/ppe-management.jpg');

                $notificationData = [
                    'notification_type' => 1,
                    'module_type' => 3,
                    'notification_message' => $message,
                    'mobile_notification' => json_encode([
                        'title' => $message,
                       'message' => getUsername($updateData['approved_by']) . " has " . getStatus($updateData['approve_status']) . " a PPE request at " . displaydateformat($empDetails->created_at) . " on " . getPpename($empDetails->ppe_name) . " from " . getDepartment($empDetails->department) . " DEPARTMENT",
                        'icon' => $img,
                        'module' => 1,
                    ]),
                    'web_link' => admin_url('ppe_request/ehsapproval/view/' . encryptId($id)),
                    'assigned_user' => array_to_string($EhsId),
                    'created_by' => Auth::id(),
                ];

                notificationSave($notificationData);
            } else {
                $details = [
                    'emp_id' => $empDetails->emp_id,
                    'emp_name' => $empDetails->emp_name,
                    'remarks' => $updateData['approve_msg'],
                    'status' => $updateData['approve_status'],
                    'department' => $empDetails->department,
                    'approved_by' => $empDetails->approved_by,
                ];
                Mail::to($requestor)->send(new PpeRejectRequestEmail($details));

                $id = $empDetails->id;
                $message = 'New PPE Request';
                $requestorId = $this->user->getrequestId($empId);
                $img = admin_url('public/assets/images/ppe-management.jpg');

                $notificationData = [
                    'notification_type' => 1,
                    'module_type' => 3,
                    'notification_message' => $message,
                    'mobile_notification' => json_encode([
                        'title' => $message,
                        'message' => getUsername($updateData['approved_by']) . " has {$updateData['approve_status']} a PPE request at " . displaydateformat($empDetails->created_at) . " on " . getPpename($empDetails->ppe_name) . " from " . getDepartment($empDetails->department) . " DEPARTMENT",
                        'icon' => $img,
                        'module' => 1,
                    ]),
                    'web_link' => admin_url('ppe_request/ehsapproval/view/' . encryptId($id)),
                    'assigned_user' => array_to_string($requestorId),
                    'created_by' => Auth::id(),
                ];

                notificationSave($notificationData);
            }



            Session::flash('success', 'PPE Request has successfully responded');
            return redirect(admin_url('ppe_request/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error',  'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ppe_request/list'));
        }
    }

    public function ehsApprovalview(Request $request)
    {

        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $pperequest = $this->pperequest->selectOne($id);
            }
            $ehsstatus = STATUS_EHS_APPROVAL_PENDING;

            $status = $pperequest->ehs_approve_status;
            if ($status !=  $ehsstatus) {
                Session::flash('success','You Have already responded to the user');
                return redirect(admin_url('ppe_request/view/' . encryptId($id)));
            }

            $data = [
                'pperequest' =>  $pperequest,
                'encryptid' => $request->id,
            ];
            return view('ppemanagement.pperequest.ehsapproval', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function storeehsapproval(Request $request)
    {
        $rules = [
            'remarks' => 'required|min:3|max:255|regex:/^[a-zA-Z].*/',
        ];
        $messages = [
            'remarks.required' => 'Remarks Field is Mandatory',
            'remarks.min' => 'Minimum 3 characters are required',
            'remarks.max' => 'Maximum limit is 255 characters',
            'remarks.regex' => 'First character should be an alphabet',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $id = decryptId($request->id);
        $remarks = $request->input('remarks');
        $approved_at = $request->input('date');
        $action = $request->input('action');
        $approveStatus = $action == 'approve' ? STATUS_EHS_APPROVED : STATUS_EHS_REJECTED;

        $dateTime = Carbon::createFromFormat('d-m-Y H:i:s', $approved_at);
        try {
            $empDetails = $this->pperequest->find($id);
            $empId = $empDetails->emp_id;
            $departmentId = $empDetails->department;
            $hod = $this->user->findDepartmenthod($departmentId);
            $storemanager = $this->user->findStoremanager();
            $updateEhsData = [
                'remarks' => $remarks,
                'ehs_approved_at' =>  $dateTime,
                'ehs_approved_by' => Auth::id(),
                'ehs_approve_status' =>  $approveStatus,
            ];

            if ($action == 'approve' || $action == 'reject') {
                $updateEhsData['status'] = 0;
            }
            $statuslog = $this->ppestatus->storeEhsStatus($updateEhsData, $empDetails);
            $empDetails->updateehsapproval($updateEhsData, $id);
            $details = [
                'emp_id' => $empDetails->emp_id,
                'emp_name' => $empDetails->emp_name,
                'remarks' => $updateEhsData['remarks'],
                'status' => $updateEhsData['ehs_approve_status'],
                'department' => $empDetails->department,
                'approved_by' => $empDetails->approved_by,
            ];
            $requestor = $this->user->getrequestEmail($empId);
            if ($action == 'approve') {

                $recipients = array_filter([$requestor, $hod,$storemanager]);


                Mail::to($recipients)->send(new PpeEhsRequestEmail($details));


                // if ($storemanager) {
                //     $details = [
                //         'emp_id' => $empDetails->emp_id,
                //         'emp_name' => $empDetails->emp_name,
                //         'remarks' => $updateEhsData['remarks'],
                //         'status' => $updateEhsData['ehs_approve_status'],
                //         'department' => $empDetails->department,
                //         'approved_by' => $empDetails->approved_by,
                //         'item_code' => $empDetails->item_code,
                //         'approve_link' => url('ppe_request/smapproval/submit/' . $empDetails->item_code  . '/approve'),
                //     ];
                //     Mail::to($storemanager)->send(new PpeRequestStoremanagerEmail($details));
                // }


                $id = $empDetails->id;
                $message = 'New PPE Request';
                $hodId = $this->user->getdepartmenthodId($departmentId);
                $storemanagerId = $this->user->getStoreManagerId();
                $img = admin_url('public/assets/images/ppe-management.jpg');
                $requestorId = $this->user->getrequestId($empId);
                $assignedUsers = array_filter(array_merge($hodId, $storemanagerId, $requestorId));
                $assignedUserString = implode(',', $assignedUsers);
                $notificationData = [
                    'notification_type' => 1,
                    'module_type' => 3,
                    'notification_message' => $message,
                    'mobile_notification' => json_encode([
                        'title' => $message,
                        'message' => getUsername($updateEhsData['ehs_approved_by']) . " has" . getStatus($updateEhsData['ehs_approve_status']) ." a PPE request at " . displaydateformat($empDetails->created_at) . " on " . getPpename($empDetails->ppe_name) . " from " . getDepartment($empDetails->department) . " DEPARTMENT",
                        'icon' => $img,
                        'module' => 1,
                    ]),
                    'assigned_user' => $assignedUserString,
                    'created_by' => Auth::id(),
                ];

                notificationSave($notificationData);
            } else {
                Mail::to($requestor)->send(new PpeRequestEhsRejectEmail($details));

                //  notification

                $id = $empDetails->id;
                $message = 'New PPE Request';
                $img = admin_url('public/assets/images/ppe-management.jpg');
                $requestorId = $this->user->getrequestId($empId);
                $notificationData = [
                    'notification_type' => 1,
                    'module_type' => 3,
                    'notification_message' => $message,
                    'mobile_notification' => json_encode([
                        'title' => $message,
                        'message' => getUsername($updateEhsData['ehs_approved_by']) . " has". getStatus($updateEhsData['ehs_approve_status'])." a PPE request at " . displaydateformat($empDetails->created_at) . " on " . getPpename($empDetails->ppe_name) . " from " . getDepartment($empDetails->department) . " DEPARTMENT",
                        'icon' => $img,
                        'module' => 1,
                    ]),
                    'assigned_user' => array_to_string($requestorId),
                    'created_by' => Auth::id(),
                ];

                notificationSave($notificationData);
            }

            Session::flash('success', 'PPE Request has successfully responded');
            return redirect(admin_url('ppe_request/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ppe_request/list'));
        }
    }



    // public function smapproval(Request $request, $itemCode, $action)
    // {
    //     if ($action == 'approve') {
    //         $quantity = $this->ppestock->getquantity($itemCode, $action);
    //         Session::flash('success', 'Approved Successfully');
    //         return redirect('ppe_stock_inventory/list');
    //     } else {
    //         Session::flash('error', 'Something went Wrong Please try again after some time');
    //         return redirect('ppe_stock_inventory/list');
    //     }
    // }

    public function edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $pperequest = $this->pperequest->find($id);
            $employee = $this->user->getEmployeedata();
            $ppetypedata = $this->ppetype->getPpetypedata();
            $ppetypemaster = $this->ppetypemaster->getppetypemaster();
            $data = [
                'employee' => $employee,
                'ppetypedata' => $ppetypedata,
                'ppetypemaster' => $ppetypemaster,
                'encryptid' => $request->id,
                'pperequest' => $pperequest,

            ];
            return view('ppemanagement.pperequest.edit', $data);
        } catch (Exception $ex) {
            Session::flash('error',  'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ppe_request/list'));
        }
    }

    public function update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'item_code'=>'required',
                'ppe_type' => 'required',
                'ppe_name' => 'required',

            ];
            $messages = [
                'item_code.required' => __('Item Code is required'),
                'ppe_name.required' => __('PPE Name is required'),
                'ppe_type.required' => __('PPE Type  is required'),

            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $this->pperequest->updates($id);

                $pperequest = $this->pperequest->selectOne($id);

                // Mail

                $departmentId = $pperequest->department;

                $hod = $this->pperequest->getdepartmenthod($departmentId);
                $details = [
                    'emp_name' => $pperequest->emp_name,
                    'emp_id' => $pperequest->emp_id,
                    'department' => $pperequest->department,
                    'item_code' => $pperequest->item_code,
                    'remarks' => $pperequest->employee_reason,

                    'approve_link' => url('ppe_request/hodapproval/view/' . encryptID($id)),
                    'reject_link' => url('ppe_request/hodapproval/view/' . encryptID($id))
                ];
                Mail::to($hod)->send(new PpeRequestRequestorEmail($details));

                // Notification

                $message = 'New PPE Request';
                $hodId = $this->user->getdepartmenthodId($departmentId);
                $img = admin_url('public/assets/images/ppe-management.jpg');


                $notificationData = [
                    'notification_type' => 1,
                    'module_type' => 3,
                    'notification_message' => $message,
                    'mobile_notification' => json_encode([
                        'title' => $message,
                        'message' => $pperequest->emp_name . ' has a PPE request at ' . displaydateformat($pperequest->created_at) . ' on ' . getPpename($pperequest->ppe_name) . ' from ' . getDepartment($pperequest->department) . ' DEPARTMENT ',
                        'icon' => $img,
                        'module' => 1,
                    ]),
                    'web_link' => admin_url('ppe_request/hodapproval/view/' . encryptId($id)),
                    'assigned_user' => array_to_string($hodId),
                    'created_by' => Auth::id(),
                ];

                notificationSave($notificationData);
                Session::flash('success', __('Your data has been updated successfully!'));
                return redirect(admin_url('ppe_request/list'));
            } catch (Exception $ex) {
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }
            return redirect(admin_url('ppe_request/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error',  'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ppe_request/list'));
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->pperequest->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => __('PPE Request status changed sucessfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('Please try after some time')], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $this->pperequest->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => __('PPE Request deleted successfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('Please try after some time')], 406);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->pperequest->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("Emp Id"),
                __('Emp Name'),
                __("PPE Type"),
                __("PPE Name"),
                __("Department"),
                __("HOD Remarks"),
                __("HOD Approve Status"),
                __("EHS Officer Remarks"),
                __("EHS Officer Approve Status "),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $hodstatus = STATUS_HOD_APPROVAL_PENDING;
            $ehsstatus = STATUS_EHS_APPROVAL_PENDING;

            $exportData = [];
            $i = 1;

            foreach ($allData as $data) {
                $export = [];
                $export[] = $i;
                $export[] = $data->emp_id;
                $export[] = $data->emp_name;
                $export[] = getPpeType($data->ppe_type);
                $export[] = getPpename($data->ppe_name);
                $export[] = getDepartment($data->department);

                if ($data->approve_status == $hodstatus) {
                    $export[] = 'User Applied For Approval';
                } else {
                    $export[] = $data->approve_msg ? $data->approve_msg : 'Null';
                }

                $export[] = removeUnderScore(getStatus($data->approve_status));

                if ($data->approve_status == $hodstatus) {
                    $export[] = 'User Applied For  Approval';
                } else if ($data->ehs_approve_status == $ehsstatus) {
                    $export[] = 'User Applied For EHS Officer Approval';
                } else {
                    $export[] = $data->remarks ? $data->remarks : 'Null';
                }

                $export[] = removeUnderScore(getStatus($data->ehs_approve_status));
                $export[] = $data->status == 1 ? 'Active' : 'In-Active';
                $export[] = getusername($data->created_by);
                $export[] = Displaydateformat($data->created_at);

                $exportData[] = $export;
                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('PPE Request.xlsx')
                ->addHeader($header)
                ->addRows($exportData);
        } catch (Exception $ex) {
            report($ex);
            return redirect()->back()->with('error', 'An error occurred while exporting the data.');
        }
    }


    public function ExportPdf(Request $request)
    {

        try {

            ini_set("pcre.backtrack_limit", "5000000");

            $allData = $this->pperequest->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("Emp Id"),
                __('Emp Name'),
                __("PPE Type"),
                __("PPE Name"),
                __("Department"),
                __("HOD Remarks"),
                __("HOD Approve Status"),
                __("EHS Officer Remarks"),
                __("EHS Officer Approve Status "),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $hodstatus = STATUS_HOD_APPROVAL_PENDING;
            $ehsstatus = STATUS_EHS_APPROVAL_PENDING;

            $data = array(
                'header' => $header,
                'content' => $allData,
                'hodstatus' => $hodstatus,
                'ehsstatus' => $ehsstatus,
                'pagetitle' => "PPE Request ",
            );
            $property = [
                'tempDir' => 'public/pdf/temp/',
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,

            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $view = view('ppemanagement.pperequest.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "PPE Request Details.pdf";
            $mpdf->Output($filename, 'I');
        } catch (Exception $ex) {
            dd($ex);
        }
    }
}
