<?php

namespace App\Http\Controllers\Ppemanagement;

use App\Models\Master\Employee;
use App\Models\Master\PpeRequest;
use App\Models\Master\PpeType;
use App\Models\Master\PpeTypeMaster;
use App\Models\UploadLog;
use App\Http\Controllers\Controller;
use App\Mail\PpeExemptionEmail;
use App\Mail\PpeExemptionRejectEmail;
use App\Mail\PpeExemptionRequestorEmail;
use App\Models\ApproveStatus;
use App\Models\Master\Company;
use App\Models\Master\Department;
use App\Models\Master\PpeExemption;
use App\Models\Master\Work;
use App\Models\Master\Unit;
use App\Models\Ppemanagement\PpeFiles;
use App\Models\Statuslog;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Mpdf\Tag\Tr;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;

class PpeExemptionController extends Controller
{

    private $ppeexemption;
    private $user;
    private $pperequest;
    private $ppestatus;
    private $department;
    private $unit;
    private $employee;
    private $approvestatus;
    private $company;
    private $ppeFiles;
    private $work;

    private $uploadlog;

    public function __construct()
    {
        $this->ppeexemption = new PpeExemption();
        $this->uploadlog = new UploadLog();
        $this->user = new User();
        $this->pperequest = new PpeRequest();
        $this->ppestatus = new Statuslog();
        $this->employee = new Employee();
        $this->department = new Department();
        $this->unit = new Unit();
        $this->company = new Company();
        $this->approvestatus = new ApproveStatus();
        $this->ppeFiles = new PpeFiles();
        $this->work = new Work();
    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->ppeexemption->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active<span>";
                            if ($row->status == 1) {
                                $text = "<span style='color:green;'>Active<span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;'>In-Active<span>";
                            }


                            if (CheckUserRole(ROLE_SUPERADMIN)) {
                                if ($row->status == 1) {
                                    $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type='1'>Active<span>";
                                } else if ($row->status == 0) {
                                    $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type='0'>In-Active<span>";
                                }
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
                        ->editColumn('unit', function ($row) {
                            return $row->unit_name;
                        })
                        ->editColumn('company', function ($row) {
                            return getCompanyname($row->company);
                        })
                        ->editColumn('location_id', function ($row) {
                            return getLocationname($row->location_id);
                        })
                        ->addColumn('approve_status', function ($row) {

                            if ($row->approve_status ==  STATUS_EHS_APPROVAL_PENDING) {
                                $text = "<span class='badge bg-info' style='font-size: 1.0em;'>EHS Head Approval Pending</span>";
                            } else if ($row->approve_status == STATUS_EHS_APPROVED) {
                                $text = "<span class='badge bg-success' style='font-size: 1.0em;'>EHS Head Approved</span>";
                            } else if ($row->approve_status == STATUS_EHS_REJECTED) {
                                $text = "<span class='badge bg-danger' style='font-size: 1.0em;'>EHS Head Rejected</span>";
                            }
                            return $text;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            if (CheckUserPermission('view')) {
                                $btn .= '<a href="' . admin_url('ppe_exemption/view/' . encryptId($row->id)) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            // if (CheckUserPermission('edit')) {
                            //     $btn .= '<a href="' . admin_url('ppe_exemption/edit/' . encryptId($row->id)) . '" class="" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a> ';
                            // }
                            // $btn .= '<a href="javascript:void(0);" data-id="' . encryptId($row->id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger"></i></a> ';

                            if ((CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_HEAD)) && $row->approve_status == STATUS_EHS_APPROVAL_PENDING && $row->status == 1) {
                                $btn .= '<a href="' . admin_url('ppe_exemption/approval/view/' . encryptId($row->id)) . '" class="" title="Action"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }

                            $btn .= '<a href="' . admin_url('ppe_exemption/generalpdf/' . encryptId($row->id)) . '" class="" title="Pdf"> <i class="fa-solid fa-file-pdf" style="color: #e67265;"></i></a> ';

                            return $btn;
                        })
                        ->rawColumns(['action', 'created_at', 'created_by', 'approve_status', 'status'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);

                    return response()->json($datatables->getData());
                } catch (Exception $ex) {
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => __('Please try after some time')], 406);
                }
            }
        }

        $department = $this->department->getdepartment();
        $unit = $this->unit->getunit();
        $company = $this->company->getcompany();
        $approvestatus = $this->approvestatus->status();

        $data = [
            'department' => $department,
            'unit' => $unit,
            'company' => $company,
            'approvestatus' => $approvestatus,
        ];
        return view('ppemanagement.ppeexemption.list', $data);
    }


    public function add()
    {

        $userData = $this->user->getUserdata();
        $employee = $this->user->getEmployeedata();
        $employeelist = $this->user->getEmployeeID();

        $data = [
            'userData' => $userData,
            'employee' => $employee,
            'employeelist' => $employeelist,
        ];
        return view('ppemanagement.ppeexemption.add', $data);
    }

    public function store(Request $request)
    {
        try {

            $rules = [


                'from_date' => 'required',
                'to_date' => 'required',
                'reason' => 'required',
            ];


            $messages = [
                'from_date.required' => __('From Date is required'),
                'from_date.date_format' => __('From Date must be in the format Y-m-d'),
                'to_date.required' => __('To Date is required'),
                'to_date.date_format' => __('To Date must be in the format Y-m-d'),
                'to_date.after_or_equal' => __('To Date must be on or after From Date'),
                'reason.required' => __('Reason is required'),

            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $lastStatus = $this->ppeexemption->laststatus();
            switch (true) {
                case $lastStatus && $lastStatus->status == 1:
                    Session::flash('error', __('Invalid request. Last PPE Exemption is still active.'));
                    return redirect(admin_url('ppe_exemption/list'));
                default:
                    break;
            }


            try {

                $ppeexemption = $this->ppeexemption->store();
                $this->ppeFiles->store($ppeexemption);

                // Mail
                $id = $ppeexemption->id;
                $statuslog = $this->ppestatus->exemptionstatus($ppeexemption);
                $ehsofficer = $this->user->findEhsHead();

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

                foreach ($ehsofficer as $officer) {
                    $officer_email = getUseremail($officer->id);
                    if ($officer_email != '' || $officer_email != null) {
                        Mail::to($officer_email)->queue(new PpeExemptionRequestorEmail($details));
                    }
                }

                // Notification
                $id = $ppeexemption->id;
                $message = 'New PPE Exemption Request';

                $assigned_user = $this->user->assigneduser($ehsofficer);
                $img = admin_url('public/assets/images/ppe-management.jpg');
                $notificationData = array(
                    'notification_type' => 1,
                    'module' => 2,
                    'notification_message' => $message,
                    'mobile_notification' => json_encode(array(
                        'title' => $message,
                        'message' => $ppeexemption->emp_name . ' has requested a PPE Exemption request on ' . displaydateformat($ppeexemption->created_at) . ' from ' .
                            displaydateformat($ppeexemption->from_date) . ' to ' . displaydateformat($ppeexemption->to_date),
                        'icon' => $img,
                       'module' => 2,
                       'id'=> $id,
                        'style' => 'font-size: 1rem;'
                    )),
                    'web_link' => admin_url('ppe_exemption/approval/view/' . encryptId($id)),
                    'assigned_user' => array_to_string($assigned_user),
                    'created_by' => Auth::id(),
                );

                notificationSave($notificationData);


                Session::flash('success', __('Your data has been created successfully!'));
                return redirect(admin_url('ppe_exemption/list'));
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }
            return redirect(admin_url('ppe_exemption/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ppe_exemption/list'));
        }
    }

    public function view(Request $request)
    {

        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $ppeexemption = $this->ppeexemption->selectOne($id);
            }
            if ($ppeexemption->approve_status == 5) {
                $ehsheadstatus = $this->ppestatus->getehsheadstatuslog($id);
            } elseif ($ppeexemption->approve_status == 6) {
                $ehsheadstatus = $this->ppestatus->getehsheadrejectstatuslog($id);
            } else {
                $ehsheadstatus = null;
            }
            $ppefiles = $this->ppeFiles->getExemptionFile($id);

            $data = [
                'ppeexemption' =>  $ppeexemption,
                'encryptid' => $request->id,
                'ehsheadstatus' => $ehsheadstatus,
                'ppefiles' => $ppefiles,

            ];


            return view('ppemanagement.ppeexemption.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }


    public function pdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $ppeexemption = $this->ppeexemption->selectOne($id);
            }

            // Initialize the variable to avoid undefined variable errors
            $ehsheadstatus = null;

            if ($ppeexemption->approve_status == 5) {
                $ehsheadstatus = $this->ppestatus->getehsheadstatuslog($id);
            } else if ($ppeexemption->approve_status == 6) {
                $ehsheadstatus = $this->ppestatus->getehsheadrejectstatuslog($id);
            }

            $data = [
                'ppeexemption' => $ppeexemption,
                'ehsheadstatus' => $ehsheadstatus,
                'pagetitle' => "PPE Exemption",
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

            $html = view('ppemanagement.ppeexemption.exportpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "PPE_Exemption.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }



    public function edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $ppeexemption = $this->ppeexemption->find($id);
            $userData = $this->user->getUserdata();
            $employee = $this->user->getEmployeedata();


            $data = [
                'userData' => $userData,
                'ppeexemption' => $ppeexemption,
                'encryptid' => $request->id,
                'employee' => $employee
            ];

            return view('ppemanagement.ppeexemption.edit', $data);
        } catch (Exception $ex) {
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ppe_exemption/list'));
        }
    }


    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->ppeexemption->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => __('PPE Exemption  status changed sucessfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('Please try after some time')], 406);
        }
    }



    public function ApprovalReject(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $ppeexemption = $this->ppeexemption->selectOne($id);
            }
            $ppefiles = $this->ppeFiles->getExemptionFile($id);
            $ehsstatus = STATUS_EHS_APPROVAL_PENDING;
            $status = $ppeexemption->approve_status;
            if ($status != $ehsstatus) {
                return redirect(admin_url('ppe_exemption/view/' . encryptId($id)));
            }

            $data = [
                'ppeexemption' =>  $ppeexemption,
                'encryptid' => $request->id,
                'ppefiles' => $ppefiles,
            ];
            return view('ppemanagement.ppeexemption.approvereject', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function storeapprovereject(Request $request)
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

        try {
            $emp_details = $this->ppeexemption->find($id);
            $department =  $emp_details->department;
            $departmentId = $this->ppeexemption->findDepartment($department, $id);

            $remarks = $request->input('remarks');
            $approved_at = $request->input('date');
            $action = $request->input('action');
            $approveStatus = $action == 'approve' ? STATUS_EHS_APPROVED : STATUS_EHS_REJECTED;

            $updateData = [
                'remarks' => $remarks,
                'approved_by' => Auth::id(),
                'approve_status' => $approveStatus,
                'approved_at' => DBdatetimeformat($approved_at),
            ];

            $statuslog = $this->ppestatus->storeexemptionstatus($updateData, $emp_details);
            $emp_details->updateapproval($updateData, $id);
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


            if ($action == 'approve') {
                $recipients = array_filter([$requestor, $hod]);
                if (!empty($recipients)) {
                    Mail::to($recipients)->queue(new PpeExemptionEmail($details));
                }


                $message = 'New PPE Exemption Request';
                $hodId = $this->user->getdepartmenthodId($departmentId);
                $requestorId = $this->user->getrequestId($empId);
                $storemanagerId = $this->user->getStoreManagerId();
                $assigned_user = array_merge($hodId, $requestorId, $storemanagerId);
                $img = admin_url('public/assets/images/ppe-management.jpg');
                $notificationData = array(
                    'notification_type' => 1,
                    'module' => 2,
                    'notification_message' => $message,
                    'mobile_notification' => json_encode(array(
                        'title' => $message,
                        'message' => getUsername($updateData['approved_by']) .  " has"  . removeUnderScore(getStatus($updateData['approve_status']))  . " a PPE Exemption request on " . displaydateformat($emp_details->created_at) . " from " . displaydateformat($emp_details->from_date) . " to " . displaydateformat($emp_details->to_date),
                        'icon' => $img,
                       'module' => 2,
                       'id'=> $id,
                        'style' => 'font-size: 1rem;'
                    )),
                    'web_link' => admin_url('ppe_exemption/approval/view/' . encryptId($id)),
                    'assigned_user' => array_to_string($assigned_user),
                    'created_by' => Auth::id(),
                );

                notificationSave($notificationData);
            } else {
                $recipients = array_filter([$requestor, $hod]);
                if ($recipients) {
                    Mail::to($recipients)->queue(new PpeExemptionRejectEmail($details));
                }

                $message = 'New PPE Exemption Request';
                $hodId = $this->user->getdepartmenthodId($departmentId);
                $requestorId = $this->user->getrequestId($empId);
                $assigned_user = array_merge($hodId, $requestorId);
                $img = admin_url('public/assets/images/ppe-management.jpg');
                $notificationData = array(
                    'notification_type' => 1,
                    'module_type' => 2,
                    'notification_message' => $message,
                    'mobile_notification' => json_encode(array(
                        'title' => $message,
                        'message' => getUsername($updateData['approved_by']) .  " has"  . removeUnderScore(getStatus($updateData['approve_status']))  . " a PPE Exemption request on " . displaydateformat($emp_details->created_at) . " from " . displaydateformat($emp_details->from_date) . " to " . displaydateformat($emp_details->to_date),
                        'icon' => $img,
                       'module' => 2,
                       'id'=> $id,
                        'style' => 'font-size: 1rem;'
                    )),
                    'web_link' => admin_url('ppe_exemption/approval/view/' . encryptId($id)),
                    'assigned_user' => array_to_string($assigned_user),
                    'created_by' => Auth::id(),
                );

                notificationSave($notificationData);
            }


            Session::flash('success', 'PPE Exemption has successfully responded');
            return redirect(admin_url('ppe_exemption/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ppe_exemption/list'));
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->ppeexemption->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("Emp Id"),
                __('Emp Name'),
                __("Company"),
                __("Location"),
                 __("Unit"),
                __("Department"),
                __("From Date"),
                __("To Date"),
                __("common.status"),
                __("From Status"),
                __("To Status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $ehsstatus = STATUS_EHS_APPROVAL_PENDING;

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->emp_id;
                $export[] =  $data->emp_name;
                $export[] =  getCompanyname($data->company);
                $export[] =  getLocationname($data->location_id);
                $export[] =  getUnitname($data->unit);
                $export[] = getDepartment($data->department);
                $export[] =  Displaydateformat($data->from_date);
                $export[] =  Displaydateformat($data->to_date);
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                if ($data->approve_status == STATUS_EHS_APPROVAL_PENDING) {
                    $export[] = 'User Applied';
                } elseif ($data->approve_status == STATUS_EHS_APPROVED) {
                    $export[] = 'EHS Head Approval Pending';
                } elseif ($data->approve_status == STATUS_EHS_REJECTED) {
                    $export[] = 'EHS Head Approval Pending';
                } else {
                    $export[] = removeUnderScore(getStatus($data->approve_status));
                }

                if ($data->approve_status == STATUS_EHS_APPROVAL_PENDING) {
                    $export[] = 'EHS Head Approval Pending';
                } elseif ($data->approve_status == STATUS_EHS_APPROVED) {
                    $export[] = 'EHS Head Approved';
                } elseif ($data->approve_status == STATUS_EHS_REJECTED) {
                    $export[] = 'EHS Head Rejected';
                } else {
                    $export[] = removeUnderScore(getStatus($data->approve_status));
                }
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('PPE Exemption.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);

            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ppe_exemption/list'));
        }
    }



    public function ExportPdf(Request $request)
    {

        try {

            ini_set("pcre.backtrack_limit", "5000000");

            $allData = $this->ppeexemption->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("Emp Id"),
                __('Emp Name'),
                __("Company"),
                __("Location"),
                 __("Unit"),
                __("Department"),
                __("From Date"),
                __("To Date"),
                __("common.status"),
                __("From Status"),
                __("To Status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $ehsstatus = STATUS_EHS_APPROVAL_PENDING;

            $data = array(
                'header' => $header,
                'content' => $allData,
                'ehsstatus' => $ehsstatus,
                'pagetitle' => "PPE Exemption ",
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

            $view = view('ppemanagement.ppeexemption.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "PPE Exemption.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ppe_exemption/list'));
        }
    }

    public function list(Request $request)
    {
        $unitId = $request->input('unitId');

        $data = $this->department->getunitDeparment($unitId);

        return response()->json($data);
    }
}
