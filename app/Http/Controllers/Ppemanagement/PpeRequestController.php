<?php

namespace App\Http\Controllers\Ppemanagement;

use App\Http\Controllers\Controller;
use App\Mail\PpeEhsRequestEmail;
use App\Mail\PpeRejectRequestEmail;
use App\Mail\PpeRequestEmail;
use App\Models\Master\Employee;
use App\Models\Master\PpeRequest;
use App\Models\Master\PpeType;
use App\Models\Master\PpeTypeMaster;
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

    public function __construct()
    {
        $this->ppetypemaster = new PpeTypeMaster();
        $this->ppetype = new PpeType();
        $this->uploadlog = new UploadLog();
        $this->pperequest = new PpeRequest();
        $this->employee = new Employee();
        $this->user = new User();
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
                        ->addColumn('ppe_type', function ($row) {
                            return getPpeType($row->ppe_type);
                        })
                        ->addColumn('ppe_name', function ($row) {
                            return getPpename($row->ppe_name);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';

                            $btn .= '<a href="' . admin_url('ppe_request/view/' . encryptId($row->id)) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('ppe_request/edit/' . encryptId($row->id)) . '" class="" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a> ';
                            $btn .= '<a href="javascript:void(0);" data-id="' . encryptId($row->id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger"></i></a> ';
                            if ((CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_HOD)) && $row->approve_status == 'PENDING') {
                                $btn .= '<a href="' . admin_url('ppe_request/hodapproval/view/' . encryptId($row->id)) . '" class="" title="Approval"><i class="fa-solid fa-check-to-slot text-warning"></i></a> ';
                            }


                            return $btn;
                        })
                        ->rawColumns(['action', 'created_at', 'created_by', 'status'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);

                    return response()->json($datatables->getData());
                } catch (Exception $ex) {
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
        $employee = $this->employee->getEmployeedata();
        $ppetypedata = $this->ppetype->getPpetypedata();
        $ppetypemaster = $this->ppetypemaster->getppetypemaster();
        $data = [
            'employee' => $employee,
            'ppetypedata' => $ppetypedata,
            'ppetypemaster' => $ppetypemaster,

        ];
        // dd($data);
        return view('ppemanagement.pperequest.add', $data);
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'ppe_type' => 'required',
                'ppe_name' => 'required',
            ];

            $messages = [
                'ppe_name.required' => __('PPE Name is required'),
                'ppe_type.required' => __('PPE Type is required'),
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

                $id = $pperequest->id;
                $message = 'New PPE Request';
                $departmentId = $pperequest->department;
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
                Session::flash('success', __('PPE Request is added successfully'));
                return redirect(admin_url('ppe_request/list'));
            } catch (Exception $ex) {
                Session::flash('error', __('common.message_error'));
                return redirect(admin_url('ppe_request/list'));
            }
        } catch (Exception $ex) {
            Session::flash('error', __('common.message_error'));
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
            $data = [
                'pperequest' =>  $pperequest,
                'encryptid' => $request->id,
            ];
            return view('ppemanagement.pperequest.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function hodApprovalview(Request $request)
    {

        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $pperequest = $this->pperequest->selectOne($id);
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
        $action = $request->input('action');
        $remarks = $request->input('remarks');
        $approved_at = $request->input('date');

        try {
            $empDetails = $this->pperequest->find($id);

            $updateData = [
                'approve_msg' => $remarks,
                'approved_at' => $approved_at,
                'approved_by' => Auth::id(),
                'approve_status' => $action == 'approve' ? 'APPROVED' : 'REJECT',
                'status' => $action == 'approve' ? 1 : 0
            ];

            $empDetails->updateapproval($updateData, $id);
            $details = [
                'emp_id' => $empDetails->emp_id,
                'emp_name' => $empDetails->emp_name,
                'remarks' => $updateData['approve_msg'],
                'status' => $updateData['approve_status'],
                'department' => $empDetails->department,
                'approved_by' => $empDetails->approved_by,
                'approve_link' => url('ppe_request/ehsapproval/view/' . encryptID($id)),
                'reject_link' => url('ppe_request/ehsapproval/view/' . encryptID($id)),
            ];
            $ehsofficer = $this->user->findEhsofficer();
            $empId = $empDetails->emp_id;
            $requestor = $this->user->finduseremail($empId);
            if ($action == 'approve') {
                foreach ($ehsofficer as $officer) {
                    $officer_email = getUseremail($officer->id);
                    Mail::to($officer_email)->send(new PpeRequestEmail($details));
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
                        'message' => getUsername($updateData['approved_by']) . " has {$updateData['approve_status']} a PPE request at " . displaydateformat($empDetails->created_at) . " on " . getPpename($empDetails->ppe_name) . " from " . getDepartment($empDetails->department) . " DEPARTMENT",
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
            Session::flash('error',  __('common.message_error'));
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
        $action = $request->input('action');
        $remarks = $request->input('remarks');
        $approved_at = $request->input('date');

        try {
            $empDetails = $this->pperequest->find($id);
            $empId = $empDetails->emp_id;
            $departmentId = $empDetails->department;
            $hod = $this->user->findDepartmenthod($departmentId);
            $storemanager = $this->user->findStoremanager();
            $updateEhsData = [
                'remarks' => $remarks,
                'ehs_approved_at' => $approved_at,
                'ehs_approved_by' => Auth::id(),
                'ehs_approve_status' => $action == 'approve' ? 'APPROVED' : 'REJECT',
            ];

            if ($action == 'approve' || $action == 'reject') {
                $updateEhsData['status'] = 0;
            }

            $empDetails->updateehsapproval($updateEhsData, $id);
            $details = [
                'emp_id' => $empDetails->emp_id,
                'emp_name' => $empDetails->emp_name,
                'remarks' => $updateEhsData['remarks'],
                'status' => $updateEhsData['ehs_approve_status'],
                'department' => $empDetails->department,
                'approved_by' => $empDetails->approved_by,
            ];
            $requestor = $empDetails->email;
            if ($action == 'approve') {
                $recipients = array_filter([$requestor, $hod, $storemanager]);
                Mail::to($recipients)->send(new PpeEhsRequestEmail($details));

                $id = $empDetails->id;
                $message = 'New PPE Request';
                $hodId = $this->user->getdepartmenthodId($departmentId);
                $storemanagerId = $this->user->getStoreManagerId();
                $img = admin_url('public/assets/images/ppe-management.jpg');
                $requestorId = $this->user->getrequestId($empId);
                $assignedUsers = array_filter([$hodId, $storemanagerId, $requestorId]);
                $assignedUserString = implode(',', $assignedUsers);
                $notificationData = [
                    'notification_type' => 1,
                    'module_type' => 3,
                    'notification_message' => $message,
                    'mobile_notification' => json_encode([
                        'title' => $message,
                        'message' => getUsername($updateEhsData['ehs_approved_by']) . " has {$updateEhsData['ehs_approve_status']} a PPE request at " . displaydateformat($empDetails->created_at) . " on " . getPpename($empDetails->ppe_name) . " from " . getDepartment($empDetails->department) . " DEPARTMENT",
                        'icon' => $img,
                        'module' => 1,
                    ]),
                    'assigned_user' =>$assignedUserString,
                    'created_by' => Auth::id(),
                ];

                notificationSave($notificationData);
            } else {
                Mail::to($requestor)->send(new PpeEhsRequestEmail($details));
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
                        'message' => getUsername($updateEhsData['ehs_approved_by']) . " has {$updateEhsData['ehs_approve_status']} a PPE request at " . displaydateformat($empDetails->created_at) . " on " . getPpename($empDetails->ppe_name) . " from " . getDepartment($empDetails->department) . " DEPARTMENT",
                        'icon' => $img,
                        'module' => 1,
                    ]),
                    'assigned_user' => implode(',', [$requestorId]),
                    'created_by' => Auth::id(),
                ];

                notificationSave($notificationData);
            }

            Session::flash('success', 'PPE Request has successfully responded');
            return redirect(admin_url('ppe_request/list'));
        } catch (Exception $ex) {
            Session::flash('error', __('common.message_error'));
            return redirect(admin_url('ppe_request/list'));
        }
    }


    public function edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $pperequest = $this->pperequest->find($id);
            $employee = $this->employee->getEmployeedata();
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
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ppe_request/list'));
        }
    }

    public function update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'ppe_type' => 'required',
                'ppe_name' => 'required',

            ];
            $messages = [

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
                $message = 'New PPE Request';
                $departmentId = $pperequest->department;
                $hodId = $this->user->getdepartmenthodId($departmentId);
                $img = admin_url('public/assets/images/ppe-management.jpg');



                $notificationData = [
                    'notification_type' => 1,
                    'module_type' => 3,
                    'notification_message' => $message,
                    'mobile_notification' => json_encode([
                        'title' => $message,
                        'message' => $pperequest->emp_name . ' has updated a PPE request at ' . displaydateformat($pperequest->created_at) . ' on ' . getPpename($pperequest->ppe_name) . ' from ' . getDepartment($pperequest->department) . ' DEPARTMENT ',
                        'icon' => $img,
                        'module' => 1,
                    ]),
                    'web_link' => admin_url('ppe_request/hodapproval/view/' . encryptId($id)),
                    'assigned_user' => array_to_string($hodId),
                    'created_by' => Auth::id(),
                ];

                notificationSave($notificationData);
                Session::flash('success', __('PPE Request is taken updated successfully'));
                return redirect(admin_url('ppe_request/list'));
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('ppe_request/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ppe_request/list'));
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->pperequest->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => __('PPE Request  to be taken status changed')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('administration.please_try_after_some_time')], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $this->pperequest->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => __('PPE Request to be taken deleted successfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('administration.please_try_after_some_time')], 406);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->pperequest->exportdata();

            $header = [
                __("common.sno"),
                __("Emp Id"),
                __('Emp Name'),
                __("PPE Type"),
                __("PPE Name"),
                __("Department"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->emp_id;
                $export[] =  $data->emp_name;
                $export[] = getPpeType($data->ppe_type);
                $export[] =  getPpename($data->ppe_name);
                $export[] =  getDepartment($data->department);
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('PPE Request to be taken .xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
        }
    }



    public function ExportPdf(Request $request)
    {

        try {

            ini_set("pcre.backtrack_limit", "5000000");

            $allData = $this->pperequest->exportdata();
            $header = [
                __("common.sno"),
                __("Emp Id"),
                __('Emp Name'),
                __("PPE Type"),
                __("PPE Name"),
                __("Department"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
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

            $filename = "Precation to be takens Details.pdf";
            $mpdf->Output($filename, 'I');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
        }
    }
}
