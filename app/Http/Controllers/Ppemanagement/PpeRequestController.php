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
use App\Models\ApproveStatus;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\PpeRequest;
use App\Models\Master\PpeStockinventory;
use App\Models\Master\PpeType;
use App\Models\Master\PpeTypeMaster;
use App\Models\Master\Work;
use App\Models\Master\Worktemp;
use App\Models\Statuslog;

use App\Models\UploadLog;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
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
    private $approvestatus;
    private $department;
    private $work;

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
        $this->approvestatus = new ApproveStatus();
        $this->department = new Department();
        $this->work = new Work();
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
                        ->editColumn('ppe_created_at', function ($row) {
                            return Displaydateformat($row->ppe_created_at);
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

                        ->addColumn('approve_status', function ($row) {

                            if ($row->approve_status == STATUS_HOD_APPROVAL_PENDING) {
                                $text = "<span class='badge bg-info' style='font-size: 1.0em;'>HOD Approval Pending</span>";
                            } else if ($row->approve_status == STATUS_HOD_APPROVED) {
                                $text = "<span class='badge bg-info' style='font-size: 1.0em;'>HOD Approved</span>";
                            } else if ($row->approve_status == STATUS_HOD_REJECTED) {
                                $text = "<span class='badge bg-danger' style='font-size: 1.0em;'>HOD Rejected</span>";
                            } else if ($row->approve_status == STATUS_EHS_APPROVAL_PENDING) {

                                if (checkUserRole(ROLE_HOD)) {
                                    $text = "<span class='badge bg-success' style='font-size: 1.0em;'>HOD Approved</span>";
                                } elseif (checkUserRole(ROLE_EHS_OFFICER)) {
                                    $text = "<span class='badge bg-info' style='font-size: 1.0em;'>EHS Officer Approval Pending</span>";
                                } else {
                                    $text = "<span class='badge bg-info' style='font-size: 1.0em;'>EHS Officer Approval Pending</span>";
                                }
                            } else if ($row->approve_status == STATUS_EHS_APPROVED) {
                                $text = "<span class='badge bg-info' style='font-size: 1.0em;'>Store manager Issue Pending</span>";
                            } else if ($row->approve_status == STATUS_EHS_REJECTED) {
                                $text = "<span class='badge bg-danger' style='font-size: 1.0em;'>EHS Officer Rejected</span>";
                            } else if ($row->approve_status == STATUS_ISSUED) {
                                $text = "<span class='badge bg-success' style='font-size: 1.0em;'>Issued</span>";
                            }

                            return $text;
                        })

                        ->addColumn('action', function ($row) {
                            $btn = '';
                            if (CheckUserPermission('view')) {
                                $btn .= '<a href="' . admin_url('ppe_request/view/' . encryptId($row->ppe_request_id)) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            // if (CheckUserPermission('edit')) {
                            //     $btn .= '<a href="' . admin_url('ppe_request/edit/' . encryptId($row->id)) . '" class="" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a> ';
                            // }
                            // $btn .= '<a href="javascript:void(0);" data-id="' . encryptId($row->id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger"></i></a> ';

                            if ((CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_HOD)) && $row->approve_status == STATUS_HOD_APPROVAL_PENDING) {
                                $btn .= '<a href="' . admin_url('ppe_request/hodapproval/view/' . encryptId($row->ppe_request_id)) . '" class="" title="Action"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }

                            if (
                                (
                                    (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_OFFICER))  && $row->approve_status == STATUS_EHS_APPROVAL_PENDING
                                ) ||

                                (
                                    (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_STORE_MANAGER)) &&
                                    $row->approve_status == STATUS_EHS_APPROVED &&
                                    $row->approve_status != STATUS_HOD_APPROVED &&
                                    $row->approve_status != STATUS_EHS_APPROVAL_PENDING &&
                                    $row->approve_status != STATUS_HOD_REJECTED &&
                                    $row->approve_status != STATUS_HOD_APPROVAL_PENDING &&
                                    $row->approve_status != STATUS_ISSUED
                                )
                            ) {
                                $btn .= '<a href="' . admin_url('ppe_request/ehsapproval/view/' . encryptId($row->ppe_request_id)) . '" class="" title="Action"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }

                            $btn .= '<a href="' . admin_url('ppe_request/generalpdf/' . encryptId($row->ppe_request_id)) . '" class="" title="Pdf"> <i class="fa-solid fa-file-pdf" style="color: #e67265;"></i></a> ';

                            return $btn;
                        })

                        ->rawColumns(['action', 'created_at', 'created_by', 'approve_status'])
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
        $approvestatus = $this->approvestatus->status();

        $ppename = $this->ppetypemaster->getppetypemaster();
        $data = [
            'ppetype' => $ppetype,
            'ppename' => $ppename,
            'approvestatus' => $approvestatus,
        ];

        return view('ppemanagement.pperequest.list', $data);
    }

    public function add(Request $request)
    {
        $employee = $this->user->getEmployeedata();
        $ppetypedata = $this->ppetype->getPpetypedata();
        $itemCode = $this->ppestock->getItemCode();
        $userdata = $this->pperequest->userdata();
        // $employeelist = $this->employee->getEmployeeID();

        $worker  = $this->work->first();

        $data = [
            'employee' => $employee,
            'ppetypedata' => $ppetypedata,
            'itemCode' => $itemCode,
            'userdata' => $userdata,



        ];
        return view('ppemanagement.pperequest.add', $data);
    }
    public function employeeid(Request $request)
    {
        $name = $request->input('search');


        $employee_code = Work::where('emp_id', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->limit(10)
            ->get();

        return response()->json(
            $employee_code->map(function ($employee) {
                return [
                    'id' => $employee->emp_id,
                    'text' => $employee->emp_id,
                ];
            })
        );
    }

    public function fetchEmployeeDetails($emp_id)
    {

        $employee = $this->work->select('emp_name', 'department')
            ->where('emp_id', $emp_id)
            // ->where('emp_id','!=',Auth::user()->employee_id)
            ->first();

        $departments = $this->department->select('id', 'department_name')->where('status', '1')->first();

        return response()->json([
            'employee' => $employee,
            'departments' => $departments
        ]);
    }

    public function checkDepartmentrequest(Request $request)
    {
        $empId = $request->input('empId');
        $department = $request->input('department');

        $chemicaldepartment = $this->pperequest->lastsixmonthrequest();
        $lastPPERequest = $this->pperequest->lastPpeRequest();
        if ($lastPPERequest) {
            $lastRequestDate = Carbon::parse($lastPPERequest->created_at)->addYear()->format('Y-m-d');
            $currentDate = Carbon::now()->format('Y-m-d');

            $oneYearBeforeLastRequest = Carbon::parse($lastRequestDate)->subYear()->format('Y-m-d');
            if ($currentDate >= $oneYearBeforeLastRequest && $currentDate <= $lastRequestDate) {
                return response()->json(['showFields' => true]);
            }
        }

        if ($chemicaldepartment) {
            $RequestDate = Carbon::parse($chemicaldepartment->created_at)->addMonths(6);
            $currentDate = Carbon::now();

            $sixMonthsBeforeRequest = Carbon::parse($RequestDate)->subMonths(6);

            if ($currentDate >= $sixMonthsBeforeRequest && $currentDate <= $RequestDate) {
                return response()->json(['showFields' => true]);
            }
        }

        return response()->json(['showFields' => false]);
    }


    public function store(Request $request)
    {
        try {
            $rules = [
                'item_code' => 'required',
                'ppe_name' => 'required',
            ];
            $messages = [
                'item_code.required' => __('Item Code is required'),
                'ppe_name.required' => __('PPE Name is required'),
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

                    if ($RequestDate->lt($currentDate)) {
                        Session::flash('error', __('PPE Request is not allowed within six months'));
                        return redirect(admin_url('ppe_request/list'));
                    }
                    break;

                case $lastPPERequest:
                    $lastRequestDate = Carbon::parse($lastPPERequest->created_at)->addYear();
                    $currentDate = Carbon::now();

                    if ($lastRequestDate->lt($currentDate)) {
                        Session::flash('error', __('PPE Request is not allowed within one year'));
                        return redirect(admin_url('ppe_request/list'));
                    }
                    break;

                default:
                    break;
            }

            try {
                $pperequest = $this->pperequest->store();
                $id = $pperequest->id;
                $statuslog = $this->ppestatus->storestatus($pperequest, $id);

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
                Mail::to($hod)->queue(new PpeRequestRequestorEmail($details));

                // Notification
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
                Session::flash('success', __('Your data has been created successfully!'));
                return redirect(admin_url('ppe_request/list'));
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
                return redirect(admin_url('ppe_request/list'));
            }
        } catch (Exception $ex) {
            report($ex);
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
            $empId = $pperequest->emp_id;
            $userdata = $this->pperequest->getuserdata($empId);
            $hodstatuslog = $this->ppestatus->gethodstatuslog($id);
            $ehsstatuslog = $this->ppestatus->getehsstatuslog($id);
            $smStatuslog = $this->ppestatus->getsmstatuslog($id);
            $data = [
                'pperequest' =>  $pperequest,
                'encryptid' => $request->id,
                'userdata' => $userdata,
                'hodstatuslog' => $hodstatuslog,
                'ehsstatuslog' => $ehsstatuslog,
                'smStatuslog' => $smStatuslog,
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
            $empId = $pperequest->emp_id;
            // $ppestatuslog = $this->ppestatus->getstatusdetails($id);
            $userdata = $this->pperequest->getuserdata($empId);
            $hodstatuslog = $this->ppestatus->gethodstatuslog($id);
            $ehsstatuslog = $this->ppestatus->getehsstatuslog($id);
            $smStatuslog = $this->ppestatus->getsmstatuslog($id);

            $data = [
                'userdata' => $userdata,
                'pperequest' => $pperequest,
                'hodstatuslog' => $hodstatuslog,
                'ehsstatuslog' => $ehsstatuslog,
                'smStatuslog' => $smStatuslog,
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
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }

    public function hodApprovalview(Request $request)
    {

        try {
            $id = decryptId($request->id);
            // dd( $id);
            if (Auth::check()) {
                $pperequest = $this->pperequest->selectOne($id);
            }
            if ($pperequest->approve_status != STATUS_HOD_APPROVAL_PENDING) {
                return redirect('ppe_request/view/' . encryptId($id));
            }
            $itemId = $pperequest->item_code;
            $stockdata = $this->ppestock->getStockInventorydata($itemId);
            $data = [
                'pperequest' =>  $pperequest,
                'encryptid' => $request->id,
                'stockdata' => $stockdata,
            ];
            return view('ppemanagement.pperequest.hodapproval', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }


    public function storehodapproval(Request $request)
    {
        $rules = [
            'remarks' => 'required',
        ];
        $messages = [
            'remarks.required' => 'Remarks Field is Mandatory',

        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $id = decryptId($request->id);
        $remarks = $request->input('remarks');
        $approved_at = $request->input('date');
        $action = $request->input('action');
        if ($action == 'approve') {
            $approveStatus = STATUS_EHS_APPROVAL_PENDING;
            $status = 1;
        } else {
            $approveStatus = STATUS_HOD_REJECTED;
            $status = 0;
        }

        if ($action == 'approve') {
            $approveDStatus = STATUS_HOD_APPROVED;
            $status = 1;
        } else {
            $approveDStatus = STATUS_HOD_REJECTED;
            $status = 0;
        }


        $dateTime = Carbon::createFromFormat('d-m-Y H:i:s', $approved_at);
        try {
            $empDetails = $this->pperequest->find($id);

            $updateData = [
                'remarks' => $remarks,
                'approved_at' => $dateTime,
                'approved_by' => Auth::id(),
                'approve_status' => $approveStatus,
                'status' => $status
            ];


            $updatedatas = [
                'remarks' => $remarks,
                'approved_at' => $dateTime,
                'approved_by' => Auth::id(),
                'approve_status' => $approveDStatus,
                'status' => $status
            ];

            $this->ppestatus->store($updatedatas, $empDetails);

            $empDetails->updateapproval($updateData, $id);



            $details = [
                'emp_id' => $empDetails->emp_id,
                'emp_name' => $empDetails->emp_name,
                'remarks' => $updateData['remarks'],
                'status' => $updateData['approve_status'],
                'department' => $empDetails->department,
                'approved_by' => $updateData['approved_by'],
                'approve_link' => url('ppe_request/ehsapproval/view/' . encryptID($id)),
                'reject_link' => url('ppe_request/ehsapproval/view/' . encryptID($id)),
            ];

            $ehsofficer = $this->user->findEhsofficer();
            $empId = $empDetails->emp_id;
            $requestor = $this->user->finduseremail($empId);

            if ($action == 'approve') {
                foreach ($ehsofficer as $officer) {
                    $officer_email = $officer->email;
                    Mail::to($officer_email)->queue(new PpeRequestHodApprovalEmail($details));
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
                        'message' => getUsername($updateData['approved_by']) . " has " . removeUnderScore(getStatus($updateData['approve_status']))  . " a PPE request at " . displaydateformat($empDetails->created_at) . " on " . getPpename($empDetails->ppe_name) . " from " . getDepartment($empDetails->department) . " DEPARTMENT",
                        'icon' => $img,
                        'module' => 1,
                        'style' => 'font-size: 1rem;'
                    ]),
                    'web_link' => url('ppe_request/ehsapproval/view/' . encryptId($id)),
                    'assigned_user' => array_to_string($EhsId),
                    'created_by' => Auth::id(),
                ];

                notificationSave($notificationData);
            } else {
                Mail::to($requestor)->queue(new PpeRejectRequestEmail($details));

                $message = 'New PPE Request';
                $requestorId = $this->user->getrequestId($empId);
                $img = admin_url('public/assets/images/ppe-management.jpg');

                $notificationData = [
                    'notification_type' => 1,
                    'module_type' => 1,
                    'notification_message' => $message,
                    'mobile_notification' => json_encode([
                        'title' => $message,
                        'message' => getUsername($updateData['approved_by']) . " has " . removeUnderScore(getStatus($updateData['approve_status']))  . " a PPE request at " . displaydateformat($empDetails->created_at) . " on " . getPpename($empDetails->ppe_name) . " from " . getDepartment($empDetails->department) . " DEPARTMENT",
                        'icon' => $img,
                        'module' => 1,
                        'style' => 'font-size: 1rem;'
                    ]),
                    'web_link' => url('ppe_request/ehsapproval/view/' . encryptId($id)),
                    'assigned_user' => array_to_string($requestorId),
                    'created_by' => Auth::id(),
                ];

                notificationSave($notificationData);
            }

            Session::flash('success', 'PPE Request has successfully responded');
            return redirect()->to(admin_url('ppe_request/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect()->to(admin_url('ppe_request/list'));
        }
    }


    public function ehsApprovalview(Request $request)
    {

        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $pperequest = $this->pperequest->selectOne($id);
            }
            $empId = $pperequest->emp_id;
            $itemId = $pperequest->item_code;
            $userdata = $this->pperequest->getuserdata($empId);
            $statuslog = $this->ppestatus->getstatuslogdata($id);
            $ehslogdata = $this->ppestatus->getehsstatuslogdetails($id);
            $stockdata = $this->ppestock->getStockInventorydata($itemId);
            if ($pperequest->approve_status == STATUS_ISSUED) {
                return redirect('ppe_request/view/' . encryptId($id));
            }


            $data = [
                'pperequest' =>  $pperequest,
                'encryptid' => $request->id,
                'userdata' => $userdata,
                'statuslog' => $statuslog,
                'stockdata' => $stockdata,
                'ehslogdata' => $ehslogdata
            ];

            return view('ppemanagement.pperequest.ehsapproval', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function storeehsapproval(Request $request)
    {
        $rules = [
            'remarks' => 'required',
        ];
        $messages = [
            'remarks.required' => 'Remarks Field is Mandatory',

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
                'approved_at' =>  $dateTime,
                'approved_by' => Auth::id(),
                'approve_status' =>  $approveStatus,
            ];

            if ($action != 'approve') {
                $updateEhsData['status'] = 0;
            }

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



            $requestor = $this->user->getrequestEmail($empId);
            $recipients = array_filter([$requestor, $hod]);

            if ($action == 'approve') {
                Mail::to($recipients)->queue(new PpeEhsRequestEmail($details));


                $Storedetails = [
                    'emp_id' => $empDetails->emp_id,
                    'emp_name' => $empDetails->emp_name,
                    'remarks' => $updateEhsData['remarks'],
                    'status' => $updateEhsData['approve_status'],
                    'department' => $empDetails->department,
                    'approved_by' => $empDetails->approved_by,
                    'approve_link' => admin_url('ppe_request/ehsapproval/view/' . encryptId($id)),
                ];

                if ($action == 'approve') {
                    Mail::to($storemanager)->queue(new PpeRequestStoremanagerEmail($Storedetails));
                    $id = $empDetails->id;
                    $message = 'New PPE Request';
                    $storemanagerId = $this->user->getStoreManagerId();
                    $img = admin_url('public/assets/images/ppe-management.jpg');
                    $requestorId = $this->user->getrequestId($empId);
                    $assignedUsers =  $storemanagerId;

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
                        'web_link' => admin_url('ppe_request/ehsapproval/view/' . encryptId($id)),
                        'assigned_user' => array_to_string($assignedUsers),
                        'created_by' => Auth::id(),
                    ];

                    notificationSave($notificationData);
                }

                $id = $empDetails->id;
                $message = 'New PPE Request';
                $hodId = $this->user->getdepartmenthodId($departmentId);
                $storemanagerId = $this->user->getStoreManagerId();
                $img = admin_url('public/assets/images/ppe-management.jpg');
                $requestorId = $this->user->getrequestId($empId);
                $assignedUsers = array_filter(array_merge($hodId, $requestorId));
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
                        'module' => 1,
                    ]),
                    'web_link' => admin_url('ppe_request/view/' . encryptId($id)),
                    'assigned_user' => $assignedUserString,
                    'created_by' => Auth::id(),
                ];

                notificationSave($notificationData);
            } else {
                Mail::to($recipients)->queue(new PpeRequestEhsRejectEmail($details));

                $id = $empDetails->id;
                $message = 'New PPE Request';
                $img = admin_url('public/assets/images/ppe-management.jpg');
                $requestorId = $this->user->getrequestId($empId);
                $hodId = $this->user->getdepartmenthodId($departmentId);
                $storemanagerId = $this->user->getStoreManagerId();
                $assignedUsers = array_filter(array_merge($hodId, $storemanagerId, $requestorId));
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
            }


            Session::flash('success', 'PPE Request has successfully responded');
            return redirect()->to(admin_url('ppe_request/list'));
        } catch (Exception $ex) {
            report($ex);

            Session::flash('error', 'Something went wrong, Please try after some time!');
            return redirect()->to(admin_url('ppe_request/list'));
        }
    }


    public function storemanagerapproval(Request $request)
    {
        $rules = [
            'store_remarks' => 'required',
        ];

        $messages = [
            'store_remarks.required' => 'Remarks Field is Mandatory',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $id = decryptId($request->id);
            $remarks = $request->input('store_remarks');
            $approved_at = $request->input('store_date');
            $action = $request->input('action');


            $empDetails = $this->pperequest->find($id);
            $itemId = $empDetails->item_code;


            $ItemCode = $this->ppestock->Quantitydata($itemId);
            $Oldquantity = $ItemCode->quantity;


            $newQuantity = $Oldquantity - 1;

            if (!$empDetails) {
                throw new Exception('Employee details not found.');
            }

            $departmentId = $empDetails->department;

            // Define Status Arrays
            $updateStatus = [
                'remarks' => $remarks,
                'approved_at' => $approved_at,
                'approved_by' => Auth::id(),
                'approve_status' => STATUS_ISSUED,
            ];
            $storeStatus = [
                'remarks' => $remarks,
                'approved_at' => $approved_at,
                'approved_by' => Auth::id(),
                'approve_status' => STATUS_ISSUED,
                'status' => 0,
            ];

            $itemcodes = $this->ppetypemaster->getppetypemaster();

            // foreach ($itemcodes as $code) {
            //     $itemList = $code->item_code;

            //     // API Call
            //     $apiUrl = "https://vmsapi.karam.in/emp.asmx/GetPPEInventory?TokenId=123&Orgid=86&Item={$itemList}";
            //     $response = Http::get($apiUrl);

            //     if ($response->successful()) {
            //         $data = $response->json();

            //         if (!empty($data)) {

            //             $this->ppestock->store($data);
            //         } else {
            //             Session::flash('error', 'Something went wrong. please try again after some time.');
            //             return redirect()->to(admin_url('ppe_request/list'));
            //         }
            //     } else {
            //         Session::flash('error', 'Something went wrong. please try again after some time.');
            //         return redirect()->to(admin_url('ppe_request/list'));
            //     }
            // }


            $this->ppestock->updateQuantity($itemId, $newQuantity);
            $this->ppestatus->storemangerstatus($updateStatus, $empDetails);
            $this->pperequest->updatestoremanager($storeStatus, $id);

            Session::flash('success', 'PPE Request has been successfully responded to.');
            return redirect()->to(admin_url('ppe_request/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', $ex->getMessage());
            return redirect()->to(admin_url('ppe_request/list'));
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
                'item_code' => 'required',
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
                Mail::to($hod)->queue(new PpeRequestRequestorEmail($details));

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
            report($ex);
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
                __("Item Code"),
                __("PPE Name"),
                __("Department"),
                __("From Status"),
                __(" To Status"),
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
                $export[] = getItemCode($data->item_code);
                $export[] = getPpename($data->ppe_name);
                $export[] = getDepartment($data->department);
                if ($data->approve_status == STATUS_HOD_APPROVAL_PENDING) {
                    $export[] = 'User Applied';
                } elseif ($data->approve_status == STATUS_HOD_APPROVED) {
                    $export[] = 'HOD Approval Pending';
                } elseif ($data->approve_status == STATUS_HOD_REJECTED) {
                    $export[] = 'HOD Approval Pending';
                } elseif ($data->approve_status == STATUS_EHS_APPROVAL_PENDING) {
                    $export[] = 'HOD Approved';
                } elseif ($data->approve_status == STATUS_EHS_APPROVED) {
                    $export[] = 'EHS Officer Approval Pending';
                } elseif ($data->approve_status == STATUS_EHS_REJECTED) {
                    $export[] = 'EHS Officer Approval Pending';
                } elseif ($data->approve_status == STATUS_ISSUED) {
                    $export[] = 'EHS Officer Approved';
                } else {
                    $export[] = removeUnderScore(getStatus($data->approve_status));
                }
                if ($data->approve_status == STATUS_HOD_APPROVAL_PENDING) {
                    $export[] = 'HOD Approval Pending';
                } elseif ($data->approve_status == STATUS_HOD_APPROVED) {
                    $export[] = 'HOD Approved';
                } elseif ($data->approve_status == STATUS_HOD_REJECTED) {
                    $export[] = 'HOD Rejected';
                } elseif ($data->approve_status == STATUS_EHS_APPROVAL_PENDING) {
                    $export[] = 'EHS Officer Approval Pending';
                } elseif ($data->approve_status == STATUS_EHS_APPROVED) {
                    $export[] = 'EHS Officer Approved';
                } elseif ($data->approve_status == STATUS_EHS_REJECTED) {
                    $export[] = 'EHS Officer Rejected';
                } else {
                    $export[] = removeUnderScore(getStatus($data->approve_status));
                }
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
                __("Item Code"),
                __("PPE Name"),
                __("Department"),
                __("From Status"),
                __("To Status"),
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
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
        }
    }
}
