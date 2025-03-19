<?php

namespace App\Http\Controllers\IMS\Incident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Mail;
use App\Models\Master\Work;
use App\Models\Master\Employee;
use App\Models\Master\Unit;
use App\Models\Master\Location;
use App\Models\Master\Department;
use App\Models\User;
use App\Models\UploadLog;
use App\Jobs\ImportvendorJob;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\IMS\Incident\AccidentBodyParts;
use App\Models\IMS\Incident\EHSReview;
use App\Models\IMS\Incident\AccidentReport;
use App\Models\IMS\Incident\AccidentInvestigationInjury;
use App\Models\IMS\Incident\AccidentInvestigation;
use App\Models\IMS\Master\Hira;
use App\Models\IMS\Incident\HiraMoc;
use App\Models\IMS\Incident\RiskAnalysis;
use App\Models\IMS\Incident\WhyWhyAnalysis;
use App\Models\IMS\Incident\FishboneAnalysis;
use App\Models\IMS\Incident\Statuslog;
use App\Models\IMS\Incident\Incidentstatus;
use App\Mail\AccidentEmail;

class AccidentReportController extends Controller
{

    private $accident_investigation;
    private $accident_investigation_injury;
    private $accident_body_parts;
    private $accident_report;
    private $unit;
    private $location;
    private $department;
    private $user;
    private $ehs_review;
    private $employee;
    private $uploadlog;
    private $hira;
    private $hiramoc;
    private $riskanalysis;
    private $whyanalysis;
    private $fishboneAnalysis;
    private $Statuslog;

    public function __construct()
    {


        $this->accident_investigation_injury = new AccidentInvestigationInjury();
        $this->accident_investigation = new AccidentInvestigation();
        $this->accident_body_parts = new AccidentBodyParts();
        $this->employee = new Employee();
        $this->ehs_review = new EHSReview();
        $this->accident_report = new AccidentReport();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->department = new Department();
        $this->user = new User();
        $this->uploadlog = new UploadLog();
        $this->hira = new Hira();
        $this->hiramoc = new HiraMoc();
        $this->riskanalysis = new RiskAnalysis();
        $this->whyanalysis = new WhyWhyAnalysis();
        $this->fishboneAnalysis = new FishboneAnalysis();
        $this->Statuslog = new Statuslog();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->accident_report->list();
                    $datatables = Datatables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active<span>";
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->id) . "' data-type = '1' >Active<span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->id) . "' data-type = '0' >In-Active<span>";
                            }
                            return $text;
                        })
                        ->editColumn('status_batch', function ($row) {
                            return "<span class='" . $row->bg_color . "' >" . $row->status_name . "</span>";
                        })
                        ->addColumn('date_and_time', function ($row) {
                            return Displaydatetimeformat($row->date_and_time);
                        })
                        ->addColumn('created_at', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                            $btn = '<a href="' . admin_url('accidentReport/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            if ($row->accident_status == 1 && $row->created_by == Auth::id()) {
                                $btn .= '<a href="' . admin_url('accidentReport/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            }


                            if ((CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_HEAD)) && ($row->accident_status == 1 || $row->accident_status == 5 || $row->accident_status == 8 || $row->accident_status == 7)) {
                                $btn .= '<a href="' . admin_url('accidentReport/review/' . encryptId($row->id)) . '" class=" " title="Review"><i class="fa-solid fa-circle-check" style="color:rgb(0, 37, 132);"></i> ';
                            }

                            if (!empty($row->investigation_assigned) && $row->accident_status == 2) {
                                $assignedUsers = explode(',', $row->investigation_assigned);
                                $loggedInUserId = Auth::id();
                                $assignedLoginIds = Employee::whereIn('id', $assignedUsers)->pluck('login_id')->toArray();
                                if (in_array($loggedInUserId, $assignedLoginIds) || (CheckUserRole(ROLE_SUPERADMIN))) {
                                    $btn .= '<a href="' . admin_url('accidentReport/investigation/' . encryptId($row->id)) . '" class=" " title="Investigation">
                                                <i class="fa fa-search" style="color: #000000;"></i>
                                             </a>';
                                }
                            }

                            if ($row->accident_status == 3) {
                                $btn .= '<a href="' . admin_url('accidentReport/uauc_riskanalysis/' . encryptId($row->id)) . '" class=" " title="uauc"><i class="fas fa-user-shield" style="color: #7e9611;"></i>';
                            }
                            if ($row->accident_status == 4  && $row->risk_analysis != 2) {
                                $btn .= '<a href="' . admin_url('accidentReport/uauc_riskanalysis/' . encryptId($row->id)) . '" class=" " title="Risk Analysis"><i class="fa fa-exclamation-triangle" style="color: #e83333;"></i>';
                            }

                            if ($row->accident_status == 6) {
                                $btn .= '<a href="' . admin_url('accidentReport/review/' . encryptId($row->id)) .  '" 
                                            class="edit-icon" 
                                            title="' . __('Corrective Action') . '">';
                                $btn .= '<img src="' . public_image('common/ca.png') . '" 
                                            alt="' . __('common.edit') . '" 
                                            style="width: 20px;">';
                                $btn .= '</a>';
                            }
                            $btn .= '<a href="' . admin_url('accidentReport/accidentpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                             </a>';

                            return $btn;
                        })
                        ->rawColumns(['action', 'status_batch', 'date_and_time', 'created_date', 'created_by', 'status'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {
                    
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
        $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
        $accidentStatusList  = Incidentstatus::select('id', 'status_name')->where('status', '1')->get();
        $employeeList  = $this->employee->select('id', 'emp_id')->whereRaw('FIND_IN_SET(' . ROLE_ADMIN . ', user_role)')->where('status', '1')->get();

        $data = array(
            'departmentList' => $departmentList,
            'accidentStatusList' => $accidentStatusList,
            'employeeList' => $employeeList,
        );

        return view('ims.incident.accidentReport.list', $data);
    }
    public function getEmployeeDetails($emp_id)
    {
        $id = decryptId($emp_id);
        $employee = $this->employee->fetchempDetails($id);
        if (!$employee) {
            return response()->json(['error' => 'Employee not found.'], 404);
        }
        return response()->json([
            'employee' => [
                'designation' => $employee->designation ?? '',
                'department_name' => $employee->department_name ?? '',
            ],
        ]);
    }
    public function fetchEmployeeDetails(Request $request, $emp_code)
    {
        $employee = $this->employee->getempDetails($emp_code);
        if (!$employee) {
            return response()->json(['error' => 'Employee not found.'], 404);
        }

        return response()->json([
            'employee' => [
                'designation' => $employee->designation_name ?? '',
                'unit_id' => $employee->unit ?? '',
                'encrypted_unit_id' => encryptId($employee->unit) ?? '',
                'unit_name' => $employee->unit_name ?? '',
                'department_name' => $employee->department_name ?? '',
                'department_id' => $employee->department ?? '',
                'encrypted_department_id' => encryptId($employee->department) ?? '',
            ],
        ]);
    }

    public function Add(Request $request)
    {

        try {
            $employeeList  = $this->employee->select('id', 'emp_id')->where('status', '1')->get();
            $locationList  = $this->location->select('id', 'location_name')->where('status', '1')->get();
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();

            $data = array(
                'locationList' => $locationList,
                'employeeList' => $employeeList,
                'unitList' => $unitList,
            );
            return view('ims.incident.accidentReport.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {
            $rules = [
                'date_and_time' => 'required',
                'shift' => 'required',
                'location_id' => 'required',
                'designation' => 'required',
                'emp_code' => 'required',
                'address_of_the_injuredperson' => 'required',
                'unit_id' => 'required',
                'department_id' => 'required',
            ];
            $messages = [
                'date_and_time.required' => 'Please enter the date and time of the accident.',
                'unit_id.required' => 'Please enter Unit',
                'department_id.required' => 'Please enter Department',
                'shift.required' => 'Shift is required.',
                'location_id.required' => 'Location is required.',
                'designation.required' => 'Designation is required.',
                'emp_code.required' => 'Employee Code is required.',
                'address_of_the_injuredperson.required' => 'Address of the injured person is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            try {
                $accident_report =  $this->accident_report->store();
                $accident_status = STATUS_ACCIDENT_REPORT;
                $user_role = ROLE_EHS_HEAD;
                $mailsubject = 'Accident has been submitted';
                $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
                $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();
                if (count($users) > 0) {
                    foreach ($users as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            $incidentDetails =  $this->accident_report->selectOne($accident_report->id);
                            $incidentarray  = $incidentDetails->toArray();

                            $incidentarray['name'] = $user->name;
                            $incidentarray['email_id'] =  $email_id;
                            $incidentarray['mail_subject'] = $mailsubject;

                            Mail::to($incidentarray['email_id'])->queue(new AccidentEmail($incidentarray));
                        }
                    }
                }

                $notificationData = array(
                    'notification_type' => 3,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Accident' . $accident_report->accident_report_no . ' submitted by ' . getUsername($accident_report->created_by),
                        'icon' =>  admin_url('public/assets/icons/accident.png'),
                        'id' => $accident_report->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('accidentReport/review/' . encryptId($accident_report->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
                $insert_array = array(
                    'ims_type' => 2,
                    'ims_id' => $incidentDetails->id,
                    'from_status' => $incidentDetails->accident_status,
                    'to_status' => $accident_status,
                    'is_reject' => null,
                    'remarks' => null,
                    'approved_by' => Auth::id(),
                );
                $this->Statuslog->create($insert_array);
                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {

                
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('accidentReport/list'));
        } catch (Exception $ex) {
            
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('accidentReport/list'));
        }
    }
    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $accident_report = $this->accident_report->selectOne($id);
                $getEHSVerify = $this->accident_report->getEHSVerifyAccident($id);
                $getEHSReview = $this->accident_report->getEHSReviewaccident($id);
                $getInvestigation = $this->accident_report->getInvestigation($id);
                $accident_investigation_injury = $this->accident_investigation_injury->getBodypartsInjuryPerson($getInvestigation->id);
                $getwhywhy = $this->accident_report->getwhywhy($id);
                $getfishbone = $this->accident_report->getfishbone($id);
                $fishboneData = json_decode($getfishbone->first()->fishbone, true);
                $getrisklevel = $this->accident_report->getrisklevel($id);
                $getEHSApprovalAccident = $this->accident_report->getEHSApprovalAccident($id);
                $status_log = $this->Statuslog->selectOne($id,2);
                $data = array(
                    'accident_report' => $accident_report,
                    'getEHSVerify' => $getEHSVerify,
                    'getInvestigation' => $getInvestigation,
                    'accident_investigation_injury' => $accident_investigation_injury,
                    'getEHSReview' => $getEHSReview,
                    'getwhywhy' => $getwhywhy,
                    'fishboneData' => $fishboneData,
                    'getrisklevel' => $getrisklevel,
                    'getEHSApprovalAccident' => $getEHSApprovalAccident,
                    'status_log' => $status_log,
                );
            }
            return view('ims.incident.accidentReport.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $employeeList  = $this->employee->select('id', 'emp_id')->where('status', '1')->get();
            $locationList  = $this->location->select('id', 'location_name')->where('status', '1')->get();

            $accident_report = $this->accident_report->find($id);
            $data = array(
                'departmentList' => $departmentList,
                'unitList' => $unitList,
                'employeeList' => $employeeList,
                'accident_report' => $accident_report,
                'locationList' => $locationList,

            );

            return view('ims.incident.accidentReport.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'date_and_time' => 'required',
                'unit_id' => 'required',
                'shift' => 'required',
                'location_id' => 'required',
                'designation' => 'required',
                'department_id' => 'required',
                'emp_code' => 'required',
                'address_of_the_injuredperson' => 'required',
            ];
            $messages = [
                'date_and_time.required' => 'Please enter the date and time of the accident.',
                'unit_id.required' => 'Unit is required.',
                'shift.required' => 'Shift is required.',
                'location_id.required' => 'Location is required.',
                'designation.required' => 'Designation is required.',
                'department_id.required' => 'Department is required.',
                'emp_code.required' => 'Employee Code is required.',
                'address_of_the_injuredperson.required' => 'Address of the injured person is required.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->accident_report->updates($id);


            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('accidentReport/list'));
        } catch (Exception $ex) {
            
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('accidentReport/list'));
        }
    }

    public function fetchEmployeeOrWorkerList($type)
    {
        if ($type == encryptId(1)) { // Employee
            $name = request()->input('search');
            $employees = Employee::select('id', 'emp_id', 'emp_name')
                ->where(function ($query) use ($name) {
                    $query->where('emp_name', 'like', '%' . $name . '%')
                        ->orWhere('emp_id', 'like', '%' . $name . '%');
                })
                ->where('status', 1)
                ->limit(10)
                ->get();

            return response()->json(
                $employees->map(function ($employee) {
                    return [
                        'id' => encryptId($employee->id),
                        'text' => $employee->emp_name . ' - ' . $employee->emp_id,
                    ];
                })
            );
        } elseif ($type == encryptId(2)) { // Worker
            $name = request()->input('search');
            $workers = Work::select('id', 'emp_id', 'emp_name')
                ->where(function ($query) use ($name) {
                    $query->where('emp_name', 'like', '%' . $name . '%')
                        ->orWhere('emp_id', 'like', '%' . $name . '%');
                })
                ->where('status', 1)
                ->limit(10)
                ->get();
            return response()->json(
                $workers->map(function ($worker) {
                    return [
                        'id' => encryptId($worker->id),
                        'text' => $worker->emp_name . ' - ' . $worker->emp_id,
                    ];
                })
            );
        }
    }
    public function fetchPersonDetails($id, $type)
    {
        if ($type == encryptId(1)) { // Employee
            $empId = decryptId($id);
            $employee = Employee::select(
                'masters_employee.id as employee_id',  // Alias to prevent ambiguity
                'masters_employee.emp_id',
                'masters_employee.emp_name',
                'masters_employee.designation',
                'masters_department.department_name'
            )
                ->leftJoin('masters_department', 'masters_employee.department', '=', 'masters_department.id')
                ->where('masters_employee.status', 1)
                ->where('masters_employee.id', $empId)
                ->where('masters_employee.trash', 'NO')  // Ensure this condition is correctly applied
                ->first();

            if (!$employee) {
                return response()->json(['error' => 'Employee not found.'], 404);
            }

            return response()->json([
                'employee' => [
                    'designation' => $employee->designation ?? '',
                    'department_name' => $employee->department_name ?? '',
                ],
            ]);
        } elseif ($type == encryptId(2)) { // Worker
            $workerId = decryptId($id);
            $worker = Work::select(
                'masters_work.id as worker_id',  // Alias to prevent ambiguity
                'masters_work.emp_id',
                'masters_work.emp_name',
                'masters_work.designation',
                'masters_department.department_name'
            )
                ->leftJoin('masters_department', 'masters_work.department', '=', 'masters_department.id')
                ->where('masters_work.status', 1)
                ->where('masters_work.id', $workerId)
                ->where('masters_work.trash', 'NO')  // Ensure this condition is correctly applied
                ->first();

            if (!$worker) {
                return response()->json(['error' => 'Worker not found.'], 404);
            }

            return response()->json([
                'worker' => [
                    'designation' => $worker->designation ?? '',
                    'department_name' => $worker->department_name ?? '',
                ],
            ]);
        }
    }


    public function employeename(Request $request)
    {
        $name = $request->input('search');
        $employees = Employee::select('id', 'emp_id', 'emp_name')
            ->where(function ($query) use ($name) {
                $query->where('emp_name', 'like', '%' . $name . '%')
                    ->orWhere('emp_id', 'like', '%' . $name . '%');
            })
            ->where('status', 1)
            ->limit(10)
            ->get();

        return response()->json(
            $employees->map(function ($employee) {
                return [
                    'id' => encryptId($employee->id),
                    'text' => $employee->emp_name . ' - ' . $employee->emp_id,
                ];
            })
        );
    }
    public function review(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $accident_report = $this->accident_report->selectOne($id);
                $employeeList  = $this->employee->select('id', 'emp_id', 'emp_name')->where('status', '1')->get();
                $getEHSVerify = $this->accident_report->getEHSVerifyAccident($id);
                $getEHSReview = $this->accident_report->getEHSReviewaccident($id);
                $getInvestigation = $this->accident_report->getInvestigation($id);
                $accident_investigation_injury = $this->accident_investigation_injury->getBodypartsInjuryPerson($getInvestigation->id);
                $getwhywhy = $this->accident_report->getwhywhy($id);
                $getfishbone = $this->accident_report->getfishbone($id);
                $fishboneData = json_decode($getfishbone->first()->fishbone, true);
                $getrisklevel = $this->accident_report->getrisklevel($id);


                $data = array(
                    'employeeList' => $employeeList,
                    'accident_investigation_injury' => $accident_investigation_injury,
                    'accident_report' => $accident_report,
                    'getEHSVerify' => $getEHSVerify,
                    'getInvestigation' => $getInvestigation,
                    'getEHSReview' => $getEHSReview,
                    'getwhywhy' => $getwhywhy,
                    'fishboneData' => $fishboneData,
                    'getrisklevel' => $getrisklevel,
                );
            }
            return view('ims.incident.accidentReport.review', $data);
        } catch (Exception $ex) {
            
        }
    }


    public function ehsHeadReviewSubmit(Request $request)
    {
        try {
            $rules = [
                'remark' => 'required',
            ];
            $messages = [
                'remark.required' => 'Please provide a remark.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $approve_type = EHS_REVIEW;
            $ehsReview = $this->ehs_review->store($approve_type);
            $accident_status = STATUS_INVESTIGATION_PENDING;
            $accident_id = $ehsReview->accident_report_id;
            $accident_report = $this->accident_report->find($accident_id);
            $investigationassigned = $this->accident_report->investigationassigned($accident_id);
            $accident = $this->accident_report->updateStatus($accident_id, $accident_status);


            if ($ehsReview->team_member) {
                $teamMemberIds = explode(',', $ehsReview->team_member);

                $employees = Employee::whereIn('id', $teamMemberIds)->get(['emp_name', 'email', 'login_id']);
                $mailsubject = 'Investigation Assigned';

                // Fetch incident details once, not inside the loop
                $incidentDetails = $this->accident_report->selectOne($accident_id);
                $incidentarray = $incidentDetails->toArray();

                foreach ($employees as $employee) {
                    $username = $employee->emp_name;
                    $email_id = $employee->email;

                    if (!empty($email_id)) { // Corrected email validation
                        $incidentarray['name'] = $username;
                        $incidentarray['email_id'] = $email_id;
                        $incidentarray['mail_subject'] = $mailsubject;

                        Mail::to($email_id)->queue(new AccidentEmail($incidentarray));
                    }
                }

                // Use incidentDetails for notification data
                $notificationData = array(
                    'notification_type' => 3,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Incident ' . $incidentDetails->accident_report_no . ' submitted by ' . getUsername($ehsReview->created_by),
                        'icon' => admin_url('public/assets/icons/accident.png'),
                        'id' => $incidentDetails->id,
                        'module' => 1,
                    )),
                    'web_link' => admin_url('accidentReport/investigation/' . encryptId($incidentDetails->id)),
                    'assigned_user' => array_to_string($teamMemberIds),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                // Insert status log
                $insert_array = array(
                    'ims_type' => 2,
                    'ims_id' => $incidentDetails->id,
                    'from_status' => $incidentDetails->accident_status,
                    'to_status' => $accident_status,
                    'is_reject' => null,
                    'remarks' => $ehsReview->remark,
                    'approved_by' => Auth::id(),
                );
                $this->Statuslog->create($insert_array);
            }
            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('accidentReport/list'));
        } catch (Exception $ex) {
            
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('accidentReport/list'));
        }
    }


    public function investigation(Request $request, $accident_id)
    {
        try {
            $incident_id = null;
            $fire_id = null;

            $accidentId = decryptId($accident_id);

            $accident_report = $this->accident_report->selectOne($accidentId);
            $getEHSReview = $this->accident_report->getEHSReviewaccident($accidentId);

            $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
            $id = decryptId($request->id);
            $accident_investigation = $this->accident_investigation->find($id);

            $accident_body_parts = $this->accident_body_parts->delete_temprow();

            $hiramoc = $this->hiramoc->delete_temprow($accidentId, $incident_id, $fire_id);
            $data = array(
                'accidentId' => $accidentId,
                'accident_body_parts' => $accident_body_parts,
                'accident_report' => $accident_report,
                'departmentList' => $departmentList,
                'getEHSReview' => $getEHSReview,

            );

            return view('ims.incident.accidentReport.investigation', $data);
        } catch (Exception $error) {
            dd($error->getMessage());
        }
    }

    public function investigationSubmit(Request $request)
    {
        try {

            $incident_id = null;
            $fire_id = null;
            $accident_id = decryptId($request->accident_id);


            if ($request->root_cause_analysis ==  3) {
                $accident_status = STATUS_ACCIDENT_CLOSED;
            } else {
                $accident_status = STATUS_UAUC_PENDING;
            }

            $accident_investigation =  $this->accident_investigation->store();
            $investigation_injury =  $this->accident_investigation_injury->store($accident_id, $accident_investigation->id);

            $accident = $this->accident_report->updateStatus($accident_id, $accident_status);

            if ($accident_investigation->root_cause_analysis ==  1) {
                $whyanalysis = $this->whyanalysis->store($accident_id, $incident_id, $fire_id, $accident_investigation->id);
            }
            if ($accident_investigation->root_cause_analysis == 2) {
                $this->fishboneAnalysis->storeFishbone($accident_id, $incident_id, $fire_id, $accident_investigation->id);
            }

            $this->hiramoc->updateAccidentInvestigation($accident_id, $accident_investigation->id);
            $getEHSReview = $this->accident_report->getEHSReviewaccident($accident_id);
            $initialaccident = $this->accident_report->selectOne($accident_id);

            $teamMemberIds = explode(',', $getEHSReview->team_member);

            $employees = Employee::whereIn('id', $teamMemberIds)->get(['emp_name', 'email', 'login_id']);

            // Extract login IDs into an array for notification
            $loginIds = $employees->pluck('login_id')->toArray();

            $mailsubject = 'Investigation Submitted';

            // Fetch incident details once, not inside the loop
            $incidentDetails = $this->accident_report->selectOne($accident_id);
            $incidentarray = $incidentDetails->toArray();

            foreach ($employees as $employee) {
                $username = $employee->emp_name;
                $email_id = $employee->email;

                if (!empty($email_id)) { // Corrected email validation
                    $incidentarray['name'] = $username;
                    $incidentarray['email_id'] = $email_id;
                    $incidentarray['mail_subject'] = $mailsubject;

                    Mail::to($email_id)->queue(new AccidentEmail($incidentarray));
                }
            }

            $notificationData = array(
                'notification_type' => 3,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => 'Accident' . $initialaccident->accident_report_no . ' submitted by ' . getUsername($initialaccident->created_by),
                    'icon' =>  admin_url('public/assets/icons/accident.png'),
                    'id' => $initialaccident->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('accidentReport/uauc_riskanalysis/' . encryptId($initialaccident->id)),
                'assigned_user' => implode(',', $loginIds),
                'created_by' => Auth::id(),
            );

            notificationSave($notificationData);

            $insert_array = array(
                'ims_type' => 2,
                'ims_id' => $initialaccident->id,
                'from_status' => $initialaccident->accident_status,
                'to_status' => $accident_status,
                'is_reject' => null,
                'remarks' => null,
                'approved_by' => Auth::id(),
            );

            $this->Statuslog->create($insert_array);

            Session::flash('success', 'Your data has been created successfully!');

            return redirect(admin_url('accidentReport/list'));
        } catch (Exception $ex) {
            
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('accidentReport/list'));
        }
    }
    public function uaucRiskanalysis(Request $request, $accident_id)
    {
        try {
            $accidentId = decryptId($accident_id);
            $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
            $hiraList  = $this->hira->select('id', 'services')->where('status', '1')->get();
            $accident_report = $this->accident_report->selectOne($accidentId);
            $getEHSReview = $this->accident_report->getEHSReviewaccident($accidentId);
            $getInvestigation = $this->accident_report->getInvestigation($accidentId);
            $accident_investigation_injury = $this->accident_investigation_injury->getBodypartsInjuryPerson($getInvestigation->id);
            $getwhywhy = $this->accident_report->getwhywhy($accidentId);
            $getfishbone = $this->accident_report->getfishbone($accidentId);
            $fishboneData = json_decode($getfishbone->first()->fishbone, true);
            $getrisklevel = $this->accident_report->getrisklevel($accidentId);

            // dd($incident_report);
            $data = array(
                'accidentId' => $accidentId,
                'departmentList' => $departmentList,
                'hiraList' => $hiraList,
                'accident_report' => $accident_report,
                'accident_investigation_injury' => $accident_investigation_injury,
                'getInvestigation' => $getInvestigation,
                'fishboneData' => $fishboneData,
                'getwhywhy' => $getwhywhy,
                'getrisklevel' => $getrisklevel,
                'getEHSReview' => $getEHSReview,

            );

            return view('ims.incident.accidentReport.uauc_riskanalysis', $data);
        } catch (Exception $error) {
            dd($error->getMessage());
        }
    }
    public function uaucSubmit(Request $request)
    {

        try {
            $accident_id = decryptId($request->accident_id);
            $getInvestigation = $this->accident_report->getInvestigation($accident_id);
            if ($getInvestigation->risk_analysis == 1) {
                $accident_status = STATUS_RISKANALYSIS_PENDING;
            } else {
                $accident_status = STATUS_EHSVERIFY_PENDING;
            }

            $this->accident_report->uaucsubmit($accident_id);
            $this->accident_report->updateStatus($accident_id, $accident_status);

            $initialaccident = $this->accident_report->selectOne($accident_id);

            if ($getInvestigation->risk_analysis == 1) {
                $user_role = ROLE_EHS_HEAD;
                $mailsubject = 'Risk Analysis';
                $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
                $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();


                if (count($users) > 0) {
                    foreach ($users as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            // $accidentDetails =  $this->initialaccident->selectOne($initialaccident->id);
                            $accidentarray  = $initialaccident->toArray();

                            $accidentarray['name'] = $user->name;
                            $accidentarray['email_id'] =  $email_id;
                            $accidentarray['mail_subject'] = $mailsubject;

                            Mail::to($accidentarray['email_id'])->queue(new AccidentEmail($accidentarray));
                        }
                    }
                }

                $notificationData = array(
                    'notification_type' => 3,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Accident' . $initialaccident->sr_no . ' submitted by ' . getUsername($initialaccident->created_by),
                        'icon' =>  admin_url('public/assets/icons/accident.png'),
                        'id' => $initialaccident->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('accidentReport/uauc_riskanalysis/' . encryptId($initialaccident->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
                $insert_array = array(
                    'ims_type' => 1,
                    'ims_id' => $initialaccident->id,
                    'from_status' => $initialaccident->accident_status,
                    'to_status' => $accident_status,
                    'is_reject' => null,
                    'remarks' => null,
                    'approved_by' => Auth::id(),
                );

                $this->Statuslog->create($insert_array);
            } else {
                $user_role = ROLE_EHS_HEAD;
                $mailsubject = 'EHS Verification Pending';
                $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
                $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();


                if (count($users) > 0) {
                    foreach ($users as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            // $incidentDetails =  $this->initialincident->selectOne($initialincident->id);
                            $accidentarray  = $initialaccident->toArray();

                            $accidentarray['name'] = $user->name;
                            $accidentarray['email_id'] =  $email_id;
                            $accidentarray['mail_subject'] = $mailsubject;

                            Mail::to($accidentarray['email_id'])->queue(new AccidentEmail($accidentarray));
                        }
                    }
                }

                $notificationData = array(
                    'notification_type' => 3,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Accident' . $initialaccident->sr_no . ' submitted by ' . getUsername($initialaccident->created_by),
                        'icon' =>  admin_url('public/assets/icons/accident.png'),
                        'id' => $initialaccident->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('accidentReport/uauc_riskanalysis/' . encryptId($initialaccident->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
                $insert_array = array(
                    'ims_type' => 1,
                    'ims_id' => $initialaccident->id,
                    'from_status' => $initialaccident->accident_status,
                    'to_status' => $accident_status,
                    'is_reject' => null,
                    'remarks' => null,
                    'approved_by' => Auth::id(),
                );

                $this->Statuslog->create($insert_array);
            }
            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('accidentReport/list'));
        } catch (Exception $ex) {
            
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('accidentReport/list'));
        }
    }

    public function riskAnalysisSubmit(Request $request)
    {

        try {
            $accident_id = decryptId($request->accident_id);
            $accident_status = STATUS_EHSVERIFY_PENDING;
            $riskanalysis = $this->riskanalysis->store($accident_id, $accident_status);
            $this->accident_report->updateStatus($accident_id, $accident_status);

            $accident_report = $this->accident_report->selectOne($accident_id);
            $user_role = ROLE_EHS_HEAD;
            $mailsubject = 'EHS Verification Pending';
            $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
            $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();

            if (count($users) > 0) {
                foreach ($users as $user) {

                    $email_id = $user->email;

                    if ($email_id != '' || $email_id != null) {
                        // $incidentDetails =  $this->initialincident->selectOne($initialincident->id);
                        $incidentarray  = $accident_report->toArray();

                        $incidentarray['name'] = $user->name;
                        $incidentarray['email_id'] =  $email_id;
                        $incidentarray['mail_subject'] = $mailsubject;

                        Mail::to($incidentarray['email_id'])->queue(new AccidentEmail($incidentarray));
                    }
                }
            }

            $notificationData = array(
                'notification_type' => 3,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => 'Accident' . $accident_report->accident_report_no . ' submitted by ' . getUsername($accident_report->created_by),
                    'icon' =>  admin_url('public/assets/icons/accident.png'),
                    'id' => $accident_report->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('accidentReport/review/' . encryptId($accident_report->id)),
                'assigned_user' => array_to_string($userids),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $insert_array = array(
                'ims_type' => 2,
                'ims_id' => $accident_report->id,
                'from_status' => $accident_report->accident_status,
                'to_status' => $accident_status,
                'is_reject' => null,
                'remarks' => null,
                'approved_by' => Auth::id(),
            );
            $this->Statuslog->create($insert_array);
            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('accidentReport/list'));
        } catch (Exception $ex) {
            
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('accidentReport/list'));
        }
    }
    public function ehsHeadVerifySubmit(Request $request)
    {
        try {

            $approve_type = EHS_VERIFY;
            $ehsReview = $this->ehs_review->store($approve_type);
            $accident_status = STATUS_ACTION_PENDING;
            $accident_id = $ehsReview->accident_report_id;
            $accident = $this->accident_report->updateStatus($accident_id, $accident_status);

            if ($ehsReview->team_member) {
                $teamMemberIds = explode(',', $ehsReview->team_member);

                $employees = Employee::whereIn('id', $teamMemberIds)->get(['emp_name', 'email', 'login_id']);
                $mailsubject = 'Action Submission Pending';

                // Fetch incident details once, not inside the loop
                $incidentDetails = $this->accident_report->selectOne($accident_id);
                $incidentarray = $incidentDetails->toArray();

                foreach ($employees as $employee) {
                    $username = $employee->emp_name;
                    $email_id = $employee->email;

                    if (!empty($email_id)) { // Corrected email validation
                        $incidentarray['name'] = $username;
                        $incidentarray['email_id'] = $email_id;
                        $incidentarray['mail_subject'] = $mailsubject;

                        Mail::to($email_id)->queue(new AccidentEmail($incidentarray));
                    }
                }

                // Use incidentDetails for notification data
                $notificationData = array(
                    'notification_type' => 3,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Accident ' . $incidentDetails->accident_report_no . ' submitted by ' . getUsername($ehsReview->created_by),
                        'icon' => admin_url('public/assets/icons/accident.png'),
                        'id' => $incidentDetails->id,
                        'module' => 1,
                    )),
                    'web_link' => admin_url('accidentReport/review/' . encryptId($incidentDetails->id)),
                    'assigned_user' => array_to_string($teamMemberIds),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                // Insert status log
                $insert_array = array(
                    'ims_type' => 2,
                    'ims_id' => $incidentDetails->id,
                    'from_status' => $incidentDetails->accident_status,
                    'to_status' => $accident_status,
                    'is_reject' => null,
                    'remarks' => $ehsReview->remark,
                    'approved_by' => Auth::id(),
                );
                $this->Statuslog->create($insert_array);
            }

            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('accidentReport/list'));
        } catch (Exception $ex) {
            
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('accidentReport/list'));
        }
    }
    public function actiontakenSubmit(Request $request)
    {

        try {
            $accident_id = decryptId($request->accident_id);
            $accident_status = STATUS_EHSAPPROVAL_PENDING;
            $this->accident_report->actiontakensubmit($accident_id);
            $this->accident_report->updateStatus($accident_id, $accident_status);
            $accident_report = $this->accident_report->selectOne($accident_id);
            $user_role = ROLE_EHS_HEAD;
            $mailsubject = 'Action Submitted';
            $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
            $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();

            if (count($users) > 0) {
                foreach ($users as $user) {

                    $email_id = $user->email;

                    if ($email_id != '' || $email_id != null) {
                        // $incidentDetails =  $this->initialfireincident->selectOne($fire_inicdent_report_id);
                        $incidentarray  = $accident_report->toArray();

                        $incidentarray['name'] = $user->name;
                        $incidentarray['email_id'] =  $email_id;
                        $incidentarray['mail_subject'] = $mailsubject;

                        Mail::to($incidentarray['email_id'])->queue(new AccidentEmail($incidentarray));
                    }
                }
            }


            $notificationData = array(
                'notification_type' => 3,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => 'Accident' . $accident_report->accident_report_no . ' submitted by ' . getUsername($accident_report->created_by),
                    'icon' =>  admin_url('public/assets/icons/accident.png'),
                    'id' => $accident_report->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('accidentReport/review/' . encryptId($accident_report->id)),
                'assigned_user' => array_to_string($userids),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);
            $insert_array = array(
                'ims_type' => 2,
                'ims_id' => $accident_report->id,
                'from_status' => $accident_report->accident_status,
                'to_status' => $accident_status,
                'is_reject' => null,
                'remarks' => null,
                'approved_by' => Auth::id(),
            );
            $this->Statuslog->create($insert_array);
            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('accidentReport/list'));
        } catch (Exception $ex) {
            
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('accidentReport/list'));
        }
    }

    public function ehsApprovalSubmit(Request $request)
    {
        try {
            $approve_type = EHS_APPROVAL;
            $ehsApproval = $this->ehs_review->store($approve_type);
            if ($request->has('approve')) {
                $accident_status = STATUS_ACCIDENT_CLOSED;
            } else {
                $accident_status = STATUS_EHSAPPROVAL_REJECTED;
            }
            $accident_id = $ehsApproval->accident_report_id;
            $accident = $this->accident_report->updateStatus($accident_id, $accident_status);

            $accident_report = $this->accident_report->selectOne($accident_id);


            if ($request->has('approve')) {
                $mailsubject = 'Accident Closed';
                $Assignedusers = User::where('id', $accident_report->created_by)
                    ->select('name', 'email')
                    ->get()
                    ->unique('email');

                if ($Assignedusers != null) {

                    foreach ($Assignedusers as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            // $safetypermitdetails =  $this->initialincident->selectmail($id);
                            $incidentarray  = $accident_report->toArray();

                            $incidentarray['name'] = $user->name;
                            $incidentarray['email_id'] =  $email_id;
                            $incidentarray['mail_subject'] = $mailsubject;

                            Mail::to($incidentarray['email_id'])->queue(new AccidentEmail($incidentarray));
                        }
                    }
                }

                $notificationData = array(
                    'notification_type' => 3,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Accident' . $accident_report->accident_report_no . ' submitted by ' . getUsername($accident_report->created_by),
                        'icon' =>  admin_url('public/assets/icons/accident.png'),
                        'id' => $accident_report->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('accidentReport/view/' . encryptId($accident_report->id)),
                    'assigned_user' => $accident_report->created_by,
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
                $insert_array = array(
                    'ims_type' => 2,
                    'ims_id' => $accident_report->id,
                    'from_status' => $accident_report->accident_status,
                    'to_status' => $accident_status,
                    'is_reject' => null,
                    'remarks' => $ehsApproval->remark,
                    'approved_by' => Auth::id(),
                );

                $this->Statuslog->create($insert_array);
            } else {
                $user_role = ROLE_EHS_HEAD;
                $mailsubject = 'EHS Rejected';
                $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
                $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();

                if (count($users) > 0) {
                    foreach ($users as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            // $incidentDetails =  $this->initialincident->selectOne($incident_id);
                            $incidentarray  = $accident_report->toArray();

                            $incidentarray['name'] = $user->name;
                            $incidentarray['email_id'] =  $email_id;
                            $incidentarray['mail_subject'] = $mailsubject;

                            Mail::to($incidentarray['email_id'])->queue(new AccidentEmail($incidentarray));
                        }
                    }
                }
                $notificationData = array(
                    'notification_type' => 3,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Accident' . $accident_report->accident_report_no . ' submitted by ' . getUsername($accident_report->created_by),
                        'icon' =>  admin_url('public/assets/icons/accident.png'),
                        'id' => $accident_report->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('accidentReport/view/' . encryptId($accident_report->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
                $insert_array = array(
                    'ims_type' => 2,
                    'ims_id' => $accident_report->id,
                    'from_status' => $accident_report->accident_status,
                    'to_status' => $accident_status,
                    'is_reject' => null,
                    'remarks' => $ehsApproval->remark,
                    'approved_by' => Auth::id(),
                );
            }

            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('accidentReport/list'));
        } catch (Exception $ex) {
            
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('accidentReport/list'));
        }
    }
    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $vendor_name = $request->vendor_name;
            $license_no = $request->license_no;
            $id = $request->id;

            if (empty($id)) {
                $isUnique = !$this->accident_report->uniqueCheck($vendor_name, $license_no);
            } else {
                $id = decryptId($id);
                $isUnique = !$this->accident_report->existUniqueCheck($vendor_name, $license_no, $id);
            }

            return Response::json($isUnique);
        }
    }
    public function accidentExportPdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $accident_report = $this->accident_report->selectOne($id);
                $getEHSVerify = $this->accident_report->getEHSVerifyAccident($id);
                $getEHSReview = $this->accident_report->getEHSReviewaccident($id);
                $getInvestigation = $this->accident_report->getInvestigation($id);
                $accident_investigation_injury = $this->accident_investigation_injury->getBodypartsInjuryPerson($getInvestigation->id);
                $getwhywhy = $this->accident_report->getwhywhy($id);
                $getfishbone = $this->accident_report->getfishbone($id);
                $fishboneData = json_decode($getfishbone->first()->fishbone, true);
                $getrisklevel = $this->accident_report->getrisklevel($id);
                $getEHSApprovalAccident = $this->accident_report->getEHSApprovalAccident($id);
            }
            $data = array(
                'accident_report' => $accident_report,
                'getEHSVerify' => $getEHSVerify,
                'getInvestigation' => $getInvestigation,
                'accident_investigation_injury' => $accident_investigation_injury,
                'getEHSReview' => $getEHSReview,
                'getwhywhy' => $getwhywhy,
                'fishboneData' => $fishboneData,
                'getrisklevel' => $getrisklevel,
                'getEHSApprovalAccident' => $getEHSApprovalAccident,
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
            $html = view('ims.incident.accidentReport.accidentPdf', $data)->render();
            $mpdf->WriteHTML($html);
            $filename = "Accident.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            
            report($ex);
        }
    }
    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->accident_report->statuschange($id);
            return response()->json(['status' => 'success', 'msg' => 'Your status has changed successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }



    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->accident_report->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Sr. No',
                'Source, Situation, Act,Activity, Product,Services',
                'Type of Hazard',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->accident_report_no;
                $export[] =  $data->services;
                if ($data->hazard_type == 1) {
                    $export[] = 'P - Physical Hazard';
                } elseif ($data->hazard_type == 2) {
                    $export[] = 'C - Chemical Hazard';
                } elseif ($data->hazard_type == 3) {
                    $export[] = 'B - Behavioral Hazard';
                } elseif ($data->hazard_type == 4) {
                    $export[] = 'O - Other Hazard';
                }
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('AccidentReport.xlsx')
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

            $allData = $this->accident_report->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Sr. No',
                'Source, Situation, Act,Activity, Product,Services',
                'Type of Hazard',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Accident Report Details",
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

            $view = view('ims.incident.accidentReport.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Accident Report.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }
    public function addInjury(Request $request)
    {
        try {
            $addInjury = $this->accident_body_parts->addInjury();
            return $addInjury;
        } catch (Exception $ex) {
            
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function getbodyEmpdetails(Request $request)
    {
        try {
            $getEmpdetails = $this->accident_body_parts->getEmpdetails();
            return $getEmpdetails;
        } catch (Exception $ex) {
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }


    public function existingHira($accident_id, Request $request)
    {
        try {
            $hiraList = $this->hira->select('id', 'services')->where('status', '1')->where('hira_status', 2)->get();
            $acc_id = decryptId($accident_id);
            $newHiraList = $this->hira->select('id', 'incident_id', 'accident_id', 'fire_id', 'hiramoc_id', 'services', 'likelihood', 'risk_levels')->where('accident_id', $acc_id)->where('hiramoc_id', '1')->first();
            // dd($newHiraList,$inc_id);

            $selectedhira = $this->hiramoc
                ->select('id', 'hira_id', 'accident_id', 'hiramoc_status')
                ->where('hiramoc_status', 'T')
                ->where('accident_id', $acc_id)
                ->where('hira_id', '!=', 0)
                ->first();
            if ($request->ajax()) {
                return view('ims.incident.accidentReport.existinghira', compact('hiraList', 'selectedhira', 'accident_id', 'newHiraList'))->render();
            }

            return view('ims.incident.accidentReport.existinghira', compact('hiraList', 'selectedhira',  'accident_id', 'newHiraList'));
        } catch (Exception $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }
    public function existingMOC($accident_id, Request $request)
    {
        try {
            $hiraList = $this->hira->select('id', 'services')->where('status', '1')->where('hira_status', 2)->get();
            $acc_id = decryptId($accident_id);
            $newHiraList = $this->hira->select('id', 'incident_id', 'accident_id', 'fire_id', 'hiramoc_id', 'services', 'likelihood', 'risk_levels')->where('accident_id', $acc_id)->where('hiramoc_id', '2')->first();
            // dd($newHiraList,$inc_id);

            $selectedhira = $this->hiramoc
                ->select('id', 'moc_id', 'accident_id', 'hiramoc_status')
                ->where('hiramoc_status', 'T')
                ->where('accident_id', $acc_id)
                ->where('moc_id', '!=', 0)
                ->first();
            if ($request->ajax()) {
                return view('ims.incident.accidentReport.existingMOC', compact('hiraList', 'selectedhira', 'accident_id', 'newHiraList'))->render();
            }

            return view('ims.incident.accidentReport.existingMOC', compact('hiraList', 'selectedhira',  'accident_id', 'newHiraList'));
        } catch (Exception $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }
    public function gethiradetails($hira_id)
    {
        $hira_id = decryptId($hira_id);

        $hira = Hira::select('services', 'likelihood', 'risk_levels')
            ->where('id', $hira_id)
            ->first();
        $risk_levels = '';
        if ($hira->risk_levels == 1) {
            $risk_levels = '1 to 9';
        } elseif ($hira->risk_levels == 2) {
            $risk_levels = '10 to 16';
        } elseif ($hira->risk_levels == 3) {
            $risk_levels = '17 to 25';
        } elseif ($hira->risk_levels == 4) {
            $risk_levels = 'Legal';
        }

        return response()->json([
            'hira' => $hira,
            'risk_levels' => $risk_levels,
        ]);
    }
    public function saveHira(Request $request)
    {
        try {
            $HiraMoc = new HiraMoc();
            $HiraMoc->accident_id = decryptId($request->accidentId);
            $HiraMoc->hira_id = decryptId($request->hira_id) ?? null;
            $HiraMoc->moc_id = decryptId($request->moc_id) ?? null;
            $HiraMoc->created_by = Auth::id();
            $HiraMoc->save();
            return response()->json([
                'success' => true,
                'message' => 'HIRA saved successfully!',
                // 'hira_id' => $HiraMoc->hira_id
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('vendor');

        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        //return Response::download($filePath, $customFileName);
        return redirect(url($filePath));
    }
}
