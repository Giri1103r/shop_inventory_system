<?php

namespace App\Http\Controllers\IMS\Incident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Mail;
use App\Models\Master\Unit;
use App\Models\Master\Location;
use App\Models\Master\Department;
use App\Models\Master\Employee;
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
use App\Models\IMS\Incident\InitialFireIncident;
use App\Models\IMS\Incident\IntialFireIncidentEvidencefile;
use App\Models\IMS\Master\IncidentType;
use App\Models\IMS\Master\Hira;
use App\Models\IMS\Incident\EHSReview;
use App\Models\IMS\Incident\HiraMoc;
use App\Models\IMS\Incident\FireIncidentInvestigation;
use App\Models\IMS\Incident\RiskAnalysis;
use App\Models\IMS\Incident\WhyWhyAnalysis;
use App\Models\IMS\Incident\FishboneAnalysis;
use App\Models\IMS\Incident\Statuslog;
use App\Models\IMS\Incident\Incidentstatus;
use App\Mail\IncidentEmail;

class InitialFireIncidentController extends Controller
{

    private $initialfireincident;
    private $initialfireincidentevidence;
    private $unit;
    private $location;
    private $inctype;
    private $employee;
    private $user;
    private $uploadlog;
    private $ehs_review;
    private $department;
    private $hira;
    private $hiramoc;
    private $fireincidentinvestigation;
    private $riskanalysis;
    private $whyanalysis;
    private $fishboneAnalysis;
    private $Statuslog;
    private $status;


    public function __construct()
    {

        $this->initialfireincident = new InitialFireIncident();
        $this->initialfireincidentevidence = new IntialFireIncidentEvidencefile();
        $this->inctype = new IncidentType();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->employee = new Employee();
        $this->department = new Department();
        $this->user = new User();
        $this->uploadlog = new UploadLog();
        $this->ehs_review = new EHSReview();
        $this->hira = new Hira();
        $this->hiramoc = new HiraMoc();
        $this->fireincidentinvestigation = new FireIncidentInvestigation();
        $this->riskanalysis = new RiskAnalysis();
        $this->whyanalysis = new WhyWhyAnalysis();
        $this->fishboneAnalysis = new FishboneAnalysis();
        $this->Statuslog = new Statuslog();
        $this->status = new Incidentstatus();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->initialfireincident->list();


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
                        ->addColumn('unit_name', function ($row) {
                            return getUnitname($row->unit_id);
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
                            $btn = '<a href="' . admin_url('incident/fire-incident/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {

                            if ($row->incident_status == 1 && $row->created_by == Auth::id()) {
                                $btn .= '<a href="' . admin_url('incident/fire-incident/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            }
                            // }
                            if ((CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_HEAD)) && ($row->incident_status == 1 || $row->incident_status == 5 || $row->incident_status == 8 || $row->incident_status == 7)) {
                                $btn .= '<a href="' . admin_url('incident/fire-incident/review/' . encryptId($row->id)) . '" class=" " title="Review"><i class="fa-solid fa-circle-check" style="color:rgb(0, 37, 132);"></i> ';
                            }


                            if (!empty($row->investigation_assigned) && $row->incident_status == 2) {
                                $assignedUsers = explode(',', $row->investigation_assigned);
                                $loggedInUserId = Auth::id();
                                $assignedLoginIds = Employee::whereIn('id', $assignedUsers)->pluck('login_id')->toArray();
                                if (in_array($loggedInUserId, $assignedLoginIds) || (CheckUserRole(ROLE_SUPERADMIN))) {
                                    $btn .= '<a href="' . admin_url('incident/fire-incident/investigation/' . encryptId($row->id)) . '" class=" " title="Investigation">
                                                <i class="fa fa-search" style="color: #000000;"></i>
                                             </a>';
                                }
                            }


                            if ($row->incident_status == 3) {
                                $btn .= '<a href="' . admin_url('incident/fire-incident/approvereject/' . encryptId($row->id)) . '" class=" " title="Investigation"><i class="fas fa-user-shield" style="color: #7e9611;"></i>';
                            }

                            if ($row->incident_status == 4  && $row->risk_analysis != 2 && (CheckUserRole(ROLE_EHS_HEAD) || CheckUserRole(ROLE_SUPERADMIN))) {
                                $btn .= '<a href="' . admin_url('incident/fire-incident/approvereject/' . encryptId($row->id)) . '" class=" " title="Risk Analysis"><i class="fa fa-exclamation-triangle" style="color: #e83333;"></i>';
                            }

                            if ($row->incident_status == 6) {
                                $btn .= '<a href="' . admin_url('incident/fire-incident/review/' . encryptId($row->id)) .  '" 
                                            class="edit-icon" 
                                            title="' . __('Corrective Action') . '">';
                                $btn .= '<img src="' . public_image('common/ca.png') . '" 
                                            alt="' . __('common.edit') . '" 
                                            style="width: 20px;">';
                                $btn .= '</a>';
                            }

                            $btn .= '<a href="' . admin_url('incident/fire-incident/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                             </a>';

                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'status_batch'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {

                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
        $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
        $status = $this->status->get();
        $data = array(
            'unitList' => $unitList,
            'status' => $status,
        );

        return view('ims.initial.firereport.list', $data);
    }

    public function Add(Request $request)
    {

        try {
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $locationList  = $this->location->select('id', 'location_name')->where('status', '1')->get();
            $incTypeList  = $this->inctype->select('id', 'incident_type_name')->where('status', '1')->get();
            $data = array(
                'unitList' => $unitList,
                'locationList' => $locationList,
                'incTypeList' => $incTypeList,
            );
            return view('ims.initial.firereport.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }
    public function employeename(Request $request)
    {
        $name = $request->input('search');

        $employees = Employee::where('emp_name', 'like', '%' . $name . '%')
            ->orWhere('emp_id', 'like', '%' . $name . '%')
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

    public function fetchEmployeeDetails($emp_id)
    {
        $emp_id = decryptId($emp_id);
        $employee = Employee::select('emp_name', 'emp_id', 'email', 'department', 'designation')
            ->where('id', $emp_id)
            ->first();
        if ($employee) {
            return response()->json([
                'employee' => $employee,
                'departments' => $this->department->select('id', 'department_name')->where('status', '1')->get()
            ]);
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [

                'incident_date_time' => 'required',
                'unit_id' => 'required',
                'shift' => 'required',
                'location_id' => 'required',
                'exact_location' => 'required',
                'iir_type' => 'required',
                'reported_name' => 'required',
                'designation' => 'required',
                'department' => 'required',
                'employee_code' => 'required',
                'time_of_reporting' => 'required',
                'reporting_media' => 'required',
                'brief_description' => 'required',
            ];
            $messages = [

                'incident_date_time.required' => 'Please enter Date and Time',
                'unit_id.required' => 'Please enter Unit',
                'shift.required' => 'Please enter Shift',
                'location_id.required' => 'Please enter Location',
                'exact_location.required' => 'Please enter Exact Location',
                'iir_type.required' => 'Please enter IIR Type',
                'reported_name.required' => 'Please enter Name',
                'designation.required' => 'Please enter Designation',
                'department.required' => 'Please enter Department',
                'employee_code.required' => 'Please enter Employee Code',
                'time_of_reporting.required' => 'Please enter Time of reporting',
                'reporting_media.required' => 'Please enter Reporting Media',
                'brief_description.required' => 'Please enter Brief Description',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {


                $initialfireincident =   $this->initialfireincident->store();
                $this->initialfireincidentevidence->store($initialfireincident);
                $incident_status = STATUS_INCIDENT_REPORT;
                $user_role = ROLE_EHS_HEAD;
                $mailsubject = 'Fire Incident has been submitted';
                $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
                $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();



                if (count($users) > 0) {
                    foreach ($users as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            $incidentDetails =  $this->initialfireincident->selectOne($initialfireincident->id);
                            $incidentarray  = $incidentDetails->toArray();

                            $incidentarray['name'] = $user->name;
                            $incidentarray['email_id'] =  $email_id;
                            $incidentarray['mail_subject'] = $mailsubject;

                            Mail::to($incidentarray['email_id'])->queue(new IncidentEmail($incidentarray));
                        }
                    }
                }

                $notificationData = array(
                    'notification_type' => 5,
                    'module_type' => 3,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Fire Incident' . $initialfireincident->sr_no . ' submitted by ' . getUsername($initialfireincident->created_by),
                        'icon' =>  admin_url('public/assets/icons/fire_incident.png'),
                        'id' => $initialfireincident->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('incident/fire-incident/review/' . encryptId($initialfireincident->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
                $insert_array = array(
                    'ims_type' => 3,
                    'ims_id' => $initialfireincident->id,
                    'from_status' => 0,
                    'to_status' => $incident_status,
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

            return redirect(admin_url('incident/fire-incident/list'));
        } catch (Exception $ex) {
            
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/fire-incident/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $incident_report = $this->initialfireincident->selectOne($id);
                $getEHSVerify = $this->initialfireincident->getEHSVerifyincident($id);
                $getEHSReview = $this->initialfireincident->getEHSReviewincident($id);
                $getInvestigation = $this->initialfireincident->getInvestigation($id);
                $getwhywhy = $this->initialfireincident->getwhywhy($id);
                $getfishbone = $this->initialfireincident->getfishbone($id);
                $fishboneData = json_decode($getfishbone->first()->fishbone, true);
                $getrisklevel = $this->initialfireincident->getrisklevel($id);
                $getEHSApprovalincident = $this->initialfireincident->getEHSApprovalincident($id);
                // dd($getEHSVerify);
                $initialfireincidentevidence = $this->initialfireincidentevidence->selectOne($id);

                $mediaOptions = [
                    1 => 'Phone',
                    2 => 'Walkie Talkie',
                    3 => 'Extension',
                    4 => 'Others',
                ];

                $selectedMedia = isset($incident_report->reporting_media)
                    ? explode(',', $incident_report->reporting_media)
                    : [];

                $displayMedia = array_map(function ($media) use ($mediaOptions) {
                    return $mediaOptions[$media] ?? $media;
                }, $selectedMedia);
                $status_log = $this->Statuslog->selectOne($id,3);
                $data = array(
                    'incident_report' => $incident_report,
                    'displayMedia' => $displayMedia,
                    'initialfireincidentevidence' => $initialfireincidentevidence,
                    'getEHSVerify' => $getEHSVerify,
                    'getInvestigation' => $getInvestigation,
                    'getEHSReview' => $getEHSReview,
                    'getwhywhy' => $getwhywhy,
                    'fishboneData' => $fishboneData,
                    'getrisklevel' => $getrisklevel,
                    'getEHSApprovalincident' => $getEHSApprovalincident,
                    'status_log' => $status_log,
                );
            }
            return view('ims.initial.firereport.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);


            $initialfireincident = $this->initialfireincident->selectOne($id);
            $initialfireincidentevidence = $this->initialfireincidentevidence->selectOne($id);
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $locationList  = $this->location->select('id', 'location_name')->where('status', '1')->get();
            $incTypeList  = $this->inctype->select('id', 'incident_type_name')->where('status', '1')->get();
            $data = array(
                'unitList' => $unitList,
                'locationList' => $locationList,
                'incTypeList' => $incTypeList,
                'initialfireincident' => $initialfireincident,
                'initialfireincidentevidence' => $initialfireincidentevidence,
            );


            return view('ims.initial.firereport.edit', $data);
        } catch (Exception $error) {
            dd($error);
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [

                'incident_date_time' => 'required',
                'unit_id' => 'required',
                'shift' => 'required',
                'location_id' => 'required',
                'exact_location' => 'required',
                'iir_type' => 'required',
                'reported_name' => 'required',
                'designation' => 'required',
                'department' => 'required',
                'employee_code' => 'required',
                'time_of_reporting' => 'required',
                'reporting_media' => 'required',
                'brief_description' => 'required',
            ];
            $messages = [

                'incident_date_time.required' => 'Please enter Date and Time',
                'unit_id.required' => 'Please enter Unit',
                'shift.required' => 'Please enter Shift',
                'location_id.required' => 'Please enter Location',
                'exact_location.required' => 'Please enter Exact Location',
                'iir_type.required' => 'Please enter IIR Type',
                'reported_name.required' => 'Please enter Name',
                'designation.required' => 'Please enter Designation',
                'department.required' => 'Please enter Department',
                'employee_code.required' => 'Please enter Employee Code',
                'time_of_reporting.required' => 'Please enter Time of reporting',
                'reporting_media.required' => 'Please enter Reporting Media',
                'brief_description.required' => 'Please enter Brief Description',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                dd($validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
            }


            $initialfireincident =   $this->initialfireincident->updates($id);
            $this->initialfireincidentevidence->updates($id);

            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('incident/fire-incident/list'));
        } catch (Exception $ex) {
            
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/fire-incident/list'));
        }
    }

    public function deleteEvidence($evidenceid)
    {
        $id = $evidenceid;
        $this->initialfireincidentevidence->deleterecord($evidenceid);
        return response()->json(['success' => true, 'message' => 'Evidence deleted successfully.']);
    }


    public function review(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $incident_report = $this->initialfireincident->selectOne($id);
                $getEHSVerify = $this->initialfireincident->getEHSVerifyincident($id);
                $getEHSReview = $this->initialfireincident->getEHSReviewincident($id);
                $getInvestigation = $this->initialfireincident->getInvestigation($id);
                $getwhywhy = $this->initialfireincident->getwhywhy($id);
                $getfishbone = $this->initialfireincident->getfishbone($id);
                $fishboneData = json_decode($getfishbone->first()->fishbone, true);
                $getrisklevel = $this->initialfireincident->getrisklevel($id);
                // dd($getEHSVerify);
                $initialfireincidentevidence = $this->initialfireincidentevidence->selectOne($id);

                $mediaOptions = [
                    1 => 'Phone',
                    2 => 'Walkie Talkie',
                    3 => 'Extension',
                    4 => 'Others',
                ];

                $selectedMedia = isset($incident_report->reporting_media)
                    ? explode(',', $incident_report->reporting_media)
                    : [];

                $displayMedia = array_map(function ($media) use ($mediaOptions) {
                    return $mediaOptions[$media] ?? $media;
                }, $selectedMedia);

                $data = array(
                    'incident_report' => $incident_report,
                    'displayMedia' => $displayMedia,
                    'initialfireincidentevidence' => $initialfireincidentevidence,
                    'getEHSVerify' => $getEHSVerify,
                    'getInvestigation' => $getInvestigation,
                    'getEHSReview' => $getEHSReview,
                    'getwhywhy' => $getwhywhy,
                    'fishboneData' => $fishboneData,
                    'getrisklevel' => $getrisklevel,
                );
            }
            return view('ims.initial.firereport.review', $data);
        } catch (Exception $ex) {
            
        }
    }

    public function teamMembers(Request $request)
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
            $incident_status = STATUS_INVESTIGATION_PENDING;
            $incident_id = $ehsReview->fire_inicdent_report_id;
            $initialfireincident = $this->initialfireincident->find($incident_id);
            $investigationassigned = $this->initialfireincident->investigationassigned($incident_id);
            $incident = $this->initialfireincident->updateStatus($incident_id, $incident_status);


            if ($ehsReview->team_member) {
                $teamMemberIds = explode(',', $ehsReview->team_member);

                $employees = Employee::whereIn('id', $teamMemberIds)->get(['emp_name', 'email']);
                $loginIds = $employees->pluck('login_id')->toArray();
                $mailsubject = 'Investigation Assigned';

                // Fetch incident details once, not inside the loop
                $incidentDetails = $this->initialfireincident->selectOne($incident_id);
                $incidentarray = $incidentDetails->toArray();

                foreach ($employees as $employee) {
                    $username = $employee->emp_name;
                    $email_id = $employee->email;

                    if (!empty($email_id)) { // Corrected email validation
                        $incidentarray['name'] = $username;
                        $incidentarray['email_id'] = $email_id;
                        $incidentarray['mail_subject'] = $mailsubject;

                        Mail::to($email_id)->queue(new IncidentEmail($incidentarray));
                    }
                }

                // Use incidentDetails for notification data
                $notificationData = array(
                    'notification_type' => 5,
                    'module_type' => 3,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Fire Incident ' . $incidentDetails->sr_no . ' submitted by ' . getUsername($ehsReview->created_by),
                        'icon' => admin_url('public/assets/icons/fire_incident.png'),
                        'id' => $incidentDetails->id,
                        'module' => 1,
                    )),
                    'web_link' => admin_url('incident/fire-incident/investigation/' . encryptId($incidentDetails->id)),
                    'assigned_user' => implode(',', $loginIds),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                // Insert status log
                $insert_array = array(
                    'ims_type' => 3,
                    'ims_id' => $incidentDetails->id,
                    'from_status' => $incidentDetails->incident_status,
                    'to_status' => $incident_status,
                    'is_reject' => null,
                    'remarks' => $ehsReview->remark,
                    'approved_by' => Auth::id(),
                );
                $this->Statuslog->create($insert_array);
            }


            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('incident/fire-incident/list'));
        } catch (Exception $ex) {
            
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/fire-incident/list'));
        }
    }


    public function investigation(Request $request, $fire_id)
    {
        try {
            $accidentId = null;
            $incidentId = null;

            $fire_id = decryptId($fire_id);
            $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
            $hiraList  = $this->hira->select('id', 'services')->where('status', '1')->get();
            $incident_report = $this->initialfireincident->selectOne($fire_id);
            $initialfireincidentevidence = $this->initialfireincidentevidence->selectOne($fire_id);
            $getEHSVerify = $this->initialfireincident->getEHSVerifyincident($fire_id);
            $getEHSReview = $this->initialfireincident->getEHSReviewincident($fire_id);
            $mediaOptions = [
                1 => 'Phone',
                2 => 'Walkie Talkie',
                3 => 'Extension',
                4 => 'Others',
            ];

            $selectedMedia = isset($incident_report->reporting_media)
                ? explode(',', $incident_report->reporting_media)
                : [];

            $displayMedia = array_map(function ($media) use ($mediaOptions) {
                return $mediaOptions[$media] ?? $media;
            }, $selectedMedia);


            $hiramoc = $this->hiramoc->delete_temprow($accidentId, $incidentId, $fire_id);

            $data = array(
                'incidentId' => $fire_id,
                'departmentList' => $departmentList,
                'hiraList' => $hiraList,
                'incident_report' => $incident_report,
                'displayMedia' => $displayMedia,
                'initialfireincidentevidence' => $initialfireincidentevidence,
                'getEHSVerify' => $getEHSVerify,
                'getEHSReview' => $getEHSReview,

            );

            return view('ims.initial.firereport.investigation', $data);
        } catch (Exception $error) {
            dd($error->getMessage());
        }
    }
    public function existingHira($fireincident_id, Request $request)
    {
        try {
            $hiraList = $this->hira->select('id', 'services')->where('status', '1')->where('hira_status', 2)->get();
            $fireincident_id = decryptId($fireincident_id);
            $newHiraList = $this->hira->select('id', 'incident_id', 'accident_id', 'fire_id', 'hiramoc_id', 'services', 'likelihood', 'risk_levels')->where('fire_id', $fireincident_id)->where('hiramoc_id', '1')->first();

            // dd($newHiraList,$inc_id);

            $selectedhira = $this->hiramoc
                ->select('id', 'hira_id', 'fire_id', 'hiramoc_status')
                ->where('hiramoc_status', 'T')
                ->where('fire_id', $fireincident_id)
                ->where('hira_id', '!=', 0)
                ->first();
            if ($request->ajax()) {
                return view('ims.initial.firereport.existinghira', compact('hiraList', 'selectedhira', 'fireincident_id', 'newHiraList'))->render();
            }

            return view('ims.initial.firereport.existinghira', compact('hiraList', 'selectedhira',  'fireincident_id', 'newHiraList'));
        } catch (Exception $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }


    public function saveHira(Request $request)
    {
        try {

            $HiraMoc = new HiraMoc();
            $HiraMoc->fire_id = decryptId($request->fireincident_id);
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
    public function existingMOC($fireincident_id, Request $request)
    {
        try {
            $hiraList = $this->hira->select('id', 'services')->where('status', '1')->where('hira_status', 2)->get();
            $fireincident_id = decryptId($fireincident_id);
            $newHiraList = $this->hira->select('id', 'incident_id', 'accident_id', 'fire_id', 'hiramoc_id', 'services', 'likelihood', 'risk_levels')->where('fire_id', $fireincident_id)->where('hiramoc_id', 2)->first();

            // dd($newHiraList,$inc_id);

            $selectedhira = $this->hiramoc
                ->select('id', 'moc_id', 'fire_id', 'hiramoc_status')
                ->where('hiramoc_status', 'T')
                ->where('fire_id', $fireincident_id)
                ->where('moc_id', '!=', 0)
                ->first();
            if ($request->ajax()) {
                return view('ims.initial.firereport.existingMOC', compact('hiraList', 'selectedhira', 'fireincident_id', 'newHiraList'))->render();
            }

            return view('ims.initial.firereport.existingMOC', compact('hiraList', 'selectedhira',  'fireincident_id', 'newHiraList'));
        } catch (Exception $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }

    public function investigationSubmit(Request $request)
    {
        //  dd($request);

        try {
            $fire_id = decryptId($request->fire_incident_id);
            if ($request->root_cause ==  3) {
                $incident_status = STATUS_INCIDENT_CLOSED;
            } else {
                $incident_status = STATUS_UAUC_PENDING;
            }


            $accident_id = null;
            $incident_id = null;

            $fireincidentinvestigation = $this->fireincidentinvestigation->store($fire_id, $incident_status);
            // dd($fireincidentinvestigation);
            $incident = $this->initialfireincident->updateStatus($fire_id, $incident_status);
            if ($fireincidentinvestigation->root_cause_analysis ==  1) {
                $whyanalysis = $this->whyanalysis->store($accident_id, $incident_id, $fire_id, $fireincidentinvestigation->id);
            }
            if ($fireincidentinvestigation->root_cause_analysis == 2) {
                $this->fishboneAnalysis->storeFishbone($accident_id, $incident_id, $fire_id, $fireincidentinvestigation->id);
            }

            $this->hiramoc->updatefireInvestigation($fire_id, $fireincidentinvestigation->id);

            $initialfireincident = $this->initialfireincident->selectOne($fire_id);
            $getEHSReview = $this->initialfireincident->getEHSReviewincident($fire_id);

            $teamMemberIds = explode(',', $getEHSReview->team_member);

            $employees = Employee::whereIn('id', $teamMemberIds)->get(['emp_name', 'email', 'login_id']);
            // dd($employees);
            // Extract login IDs into an array for notification
            $loginIds = $employees->pluck('login_id')->toArray();

            $mailsubject = 'Investigation Submitted';

            // Fetch incident details once, not inside the loop
            $incidentDetails = $this->initialfireincident->selectOne($fire_id);
            $incidentarray = $incidentDetails->toArray();

            foreach ($employees as $employee) {
                $username = $employee->emp_name;
                $email_id = $employee->email;

                if (!empty($email_id)) { // Corrected email validation
                    $incidentarray['name'] = $username;
                    $incidentarray['email_id'] = $email_id;
                    $incidentarray['mail_subject'] = $mailsubject;

                    Mail::to($email_id)->queue(new IncidentEmail($incidentarray));
                }
            }

            // Use incidentDetails for notification data
            $notificationData = array(
                'notification_type' => 5,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => 'Fire Incident ' . $incidentDetails->sr_no . ' submitted by ' . getUsername($fireincidentinvestigation->created_by),
                    'icon' => admin_url('public/assets/icons/fire_incident.png'),
                    'id' => $incidentDetails->id,
                    'module' => 1,
                )),
                'web_link' => admin_url('incident/fire-incident/approvereject/' . encryptId($incidentDetails->id)),
                'assigned_user' => implode(',', $loginIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);
            $insert_array = array(
                'ims_type' => 1,
                'ims_id' => $incidentDetails->id,
                'from_status' => $incidentDetails->incident_status,
                'to_status' => $incident_status,
                'is_reject' => null,
                'remarks' => null,
                'approved_by' => Auth::id(),
            );

            $this->Statuslog->create($insert_array);
            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('incident/fire-incident/list'));
        } catch (Exception $ex) {
            
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/fire-incident/list'));
        }
    }

    public function approvereject(Request $request, $incident_id)
    {
        try {


            $incidentId = decryptId($incident_id);
            $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
            $hiraList  = $this->hira->select('id', 'services')->where('status', '1')->get();
            $incident_report = $this->initialfireincident->selectOne($incidentId);
            $initialfireincidentevidence = $this->initialfireincidentevidence->selectOne($incidentId);
            $getInvestigation = $this->initialfireincident->getInvestigation($incidentId);
            $getEHSVerify = $this->initialfireincident->getEHSVerifyincident($incidentId);
            $getwhywhy = $this->initialfireincident->getwhywhy($incidentId);
            $getfishbone = $this->initialfireincident->getfishbone($incidentId);
            $fishboneData = json_decode($getfishbone->first()->fishbone, true);
            $getrisklevel = $this->initialfireincident->getrisklevel($incidentId);
            $getEHSReview = $this->initialfireincident->getEHSReviewincident($incidentId);

            $mediaOptions = [
                1 => 'Phone',
                2 => 'Walkie Talkie',
                3 => 'Extension',
                4 => 'Others',
            ];

            $selectedMedia = isset($incident_report->reporting_media)
                ? explode(',', $incident_report->reporting_media)
                : [];

            $displayMedia = array_map(function ($media) use ($mediaOptions) {
                return $mediaOptions[$media] ?? $media;
            }, $selectedMedia);

            $data = array(
                'incidentId' => $incidentId,
                'departmentList' => $departmentList,
                'hiraList' => $hiraList,
                'incident_report' => $incident_report,
                'displayMedia' => $displayMedia,
                'initialfireincidentevidence' => $initialfireincidentevidence,
                'getInvestigation' => $getInvestigation,
                'getEHSVerify' => $getEHSVerify,
                'fishboneData' => $fishboneData,
                'getwhywhy' => $getwhywhy,
                'getrisklevel' => $getrisklevel,
                'getEHSReview' => $getEHSReview,

            );

            return view('ims.initial.firereport.riskanalysis', $data);
        } catch (Exception $error) {
            dd($error->getMessage());
        }
    }

    public function uaucSubmit(Request $request)
    {
        // dd($request);
        try {
            $fire_incident_id = decryptId($request->fire_incident_id);
            $getInvestigation = $this->initialfireincident->getInvestigation($fire_incident_id);
            if ($getInvestigation->risk_analysis == 1) {
                $incident_status = STATUS_RISKANALYSIS_PENDING;
            } else {
                $incident_status = STATUS_EHSVERIFY_PENDING;
            }

            $this->initialfireincident->uaucsubmit($fire_incident_id);
            $this->initialfireincident->updateStatus($fire_incident_id, $incident_status);

            $initialfireincident = $this->initialfireincident->selectOne($fire_incident_id);

            if ($getInvestigation->risk_analysis == 1) {
                $user_role = ROLE_EHS_HEAD;
                $mailsubject = 'Risk Analysis';
                $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
                $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();


                if (count($users) > 0) {
                    foreach ($users as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            // $incidentDetails =  $this->initialincident->selectOne($initialincident->id);
                            $incidentarray  = $initialfireincident->toArray();

                            $incidentarray['name'] = $user->name;
                            $incidentarray['email_id'] =  $email_id;
                            $incidentarray['mail_subject'] = $mailsubject;

                            Mail::to($incidentarray['email_id'])->queue(new IncidentEmail($incidentarray));
                        }
                    }
                }
                $notificationData = array(
                    'notification_type' => 5,
                    'module_type' => 3,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Fire Incident' . $initialfireincident->sr_no . ' submitted by ' . getUsername($initialfireincident->created_by),
                        'icon' =>  admin_url('public/assets/icons/fire_incident.png'),
                        'id' => $initialfireincident->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('incident/fire-incident/approvereject/' . encryptId($initialfireincident->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
                $insert_array = array(
                    'ims_type' => 3,
                    'ims_id' => $initialfireincident->id,
                    'from_status' => $initialfireincident->incident_status,
                    'to_status' => $incident_status,
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
                            $incidentarray  = $initialfireincident->toArray();

                            $incidentarray['name'] = $user->name;
                            $incidentarray['email_id'] =  $email_id;
                            $incidentarray['mail_subject'] = $mailsubject;

                            Mail::to($incidentarray['email_id'])->queue(new IncidentEmail($incidentarray));
                        }
                    }
                }
                $notificationData = array(
                    'notification_type' => 5,
                    'module_type' => 3,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Fire Incident' . $initialfireincident->sr_no . ' submitted by ' . getUsername($initialfireincident->created_by),
                        'icon' =>  admin_url('public/assets/icons/fire_incident.png'),
                        'id' => $initialfireincident->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('incident/fire-incident/approvereject/' . encryptId($initialfireincident->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
                $insert_array = array(
                    'ims_type' => 3,
                    'ims_id' => $initialfireincident->id,
                    'from_status' => $initialfireincident->incident_status,
                    'to_status' => $incident_status,
                    'is_reject' => null,
                    'remarks' => null,
                    'approved_by' => Auth::id(),
                );

                $this->Statuslog->create($insert_array);
            }
            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('incident/fire-incident/list'));
        } catch (Exception $ex) {
            
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/fire-incident/list'));
        }
    }


    public function riskAnalysisSubmit(Request $request)
    {
        // dd($request);
        try {
            $fire_incident_id = decryptId($request->fire_incident_id);
            $incident_status = STATUS_EHSVERIFY_PENDING;
            $riskanalysis = $this->riskanalysis->store($fire_incident_id, $incident_status);
            $incident = $this->initialfireincident->updateStatus($fire_incident_id, $incident_status);

            $initialfireincident = $this->initialfireincident->selectOne($fire_incident_id);
            $user_role = ROLE_EHS_HEAD;
            $mailsubject = 'EHS Verification Pending';
            $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
            $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();

            if (count($users) > 0) {
                foreach ($users as $user) {

                    $email_id = $user->email;

                    if ($email_id != '' || $email_id != null) {
                        // $incidentDetails =  $this->initialincident->selectOne($initialincident->id);
                        $incidentarray  = $initialfireincident->toArray();

                        $incidentarray['name'] = $user->name;
                        $incidentarray['email_id'] =  $email_id;
                        $incidentarray['mail_subject'] = $mailsubject;

                        Mail::to($incidentarray['email_id'])->queue(new IncidentEmail($incidentarray));
                    }
                }
            }
            $notificationData = array(
                'notification_type' => 5,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => 'Fire Incident' . $initialfireincident->sr_no . ' submitted by ' . getUsername($initialfireincident->created_by),
                    'icon' =>  admin_url('public/assets/icons/fire_incident.png'),
                    'id' => $initialfireincident->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('incident/fire-incident/review/' . encryptId($initialfireincident->id)),
                'assigned_user' => array_to_string($userids),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);
            $insert_array = array(
                'ims_type' => 3,
                'ims_id' => $initialfireincident->id,
                'from_status' => $initialfireincident->incident_status,
                'to_status' => $incident_status,
                'is_reject' => null,
                'remarks' => null,
                'approved_by' => Auth::id(),
            );

            $this->Statuslog->create($insert_array);

            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('incident/fire-incident/list'));
        } catch (Exception $ex) {
            
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/fire-incident/list'));
        }
    }

    public function ehsHeadVerifySubmit(Request $request)
    {
        try {
            // dd($request);
            $approve_type = EHS_VERIFY;
            $ehsReview = $this->ehs_review->store($approve_type);
            $incident_status = STATUS_ACTION_PENDING;
            $fire_inicdent_report_id = $ehsReview->fire_inicdent_report_id;
            $this->initialfireincident->chooseAssigneeUpdate($fire_inicdent_report_id,$ehsReview->team_member);
            $incident = $this->initialfireincident->updateStatus($fire_inicdent_report_id, $incident_status);
            if ($ehsReview->team_member) {
                $teamMemberIds = explode(',', $ehsReview->team_member);

                $employees = Employee::whereIn('id', $teamMemberIds)->get(['emp_name', 'email', 'login_id']);

                // Extract login IDs into an array for notification
                $loginIds = $employees->pluck('login_id')->toArray();
                $mailsubject = 'Action Submission Pending';
                $incidentDetails = $this->initialfireincident->selectOne($fire_inicdent_report_id);
                $incidentarray = $incidentDetails->toArray();

                foreach ($employees as $employee) {
                    $username = $employee->emp_name;
                    $email_id = $employee->email;

                    if (!empty($email_id)) {
                        $incidentarray['name'] = $username;
                        $incidentarray['email_id'] = $email_id;
                        $incidentarray['mail_subject'] = $mailsubject;

                        Mail::to($email_id)->queue(new IncidentEmail($incidentarray));
                    }
                }

                $notificationData = array(
                    'notification_type' => 5,
                    'module_type' => 3,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Incident ' . $incidentDetails->sr_no . ' submitted by ' . getUsername($ehsReview->created_by),
                        'icon' => admin_url('public/assets/icons/fire_incident.png'),
                        'id' => $incidentDetails->id,
                        'module' => 1,
                    )),
                    'web_link' => admin_url('incident/fire-incident/review/' . encryptId($incidentDetails->id)),
                    'assigned_user' => implode(',', $loginIds),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $insert_array = array(
                    'ims_type' => 3,
                    'ims_id' => $incidentDetails->id,
                    'from_status' => $incidentDetails->incident_status,
                    'to_status' => $incident_status,
                    'is_reject' => null,
                   'remarks' => $ehsReview->remark,
                    'approved_by' => Auth::id(),
                );
                $this->Statuslog->create($insert_array);
            }
            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('incident/fire-incident/list'));
        } catch (Exception $ex) {
            
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/fire-incident/list'));
        }
    }
    public function actiontakenSubmit(Request $request)
    {

        try {
            $fire_inicdent_report_id = decryptId($request->fire_inicdent_report_id);
            $incident_status = STATUS_EHSAPPROVAL_PENDING;
            $this->initialfireincident->actiontakensubmit($fire_inicdent_report_id);
            $this->initialfireincident->updateStatus($fire_inicdent_report_id, $incident_status);
            $incidentDetails = $this->initialfireincident->selectOne($fire_inicdent_report_id);
            $user_role = ROLE_EHS_HEAD;
            $mailsubject = 'Action Submitted';
            $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
            $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();

            if (count($users) > 0) {
                foreach ($users as $user) {

                    $email_id = $user->email;

                    if ($email_id != '' || $email_id != null) {
                        // $incidentDetails =  $this->initialfireincident->selectOne($fire_inicdent_report_id);
                        $incidentarray  = $incidentDetails->toArray();

                        $incidentarray['name'] = $user->name;
                        $incidentarray['email_id'] =  $email_id;
                        $incidentarray['mail_subject'] = $mailsubject;

                        Mail::to($incidentarray['email_id'])->queue(new IncidentEmail($incidentarray));
                    }
                }
            }
            $notificationData = array(
                'notification_type' => 5,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => 'Fire Incident' . $incidentDetails->sr_no . ' submitted by ' . getUsername($incidentDetails->created_by),
                    'icon' =>  admin_url('public/assets/icons/fire_incident.png'),
                    'id' => $incidentDetails->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('incident/fire-incident/review/' . encryptId($incidentDetails->id)),
                'assigned_user' => array_to_string($userids),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);
            $insert_array = array(
                'ims_type' => 3,
                'ims_id' => $incidentDetails->id,
                'from_status' => $incidentDetails->incident_status,
                'to_status' => $incident_status,
                'is_reject' => null,
                'remarks' => null,
                'approved_by' => Auth::id(),
            );
            $this->Statuslog->create($insert_array);


            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('incident/fire-incident/list'));
        } catch (Exception $ex) {
            
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/fire-incident/list'));
        }
    }

    public function ehsApprovalSubmit(Request $request)
    {
        try {
            $approve_type = EHS_APPROVAL;
            $ehsApproval = $this->ehs_review->store($approve_type);
            if ($request->has('approve')) {
                $incident_status = STATUS_INCIDENT_CLOSED;
            } else {
                $incident_status = STATUS_EHSAPPROVAL_REJECTED;
            }
            $fire_inicdent_report_id = $ehsApproval->fire_inicdent_report_id;
            $incident = $this->initialfireincident->updateStatus($fire_inicdent_report_id, $incident_status);
            $incidentDetails = $this->initialfireincident->selectOne($fire_inicdent_report_id);
            if ($request->has('approve')) {

                $mailsubject = 'Fire Incident Closed';
                $Assignedusers = User::where('id', $incidentDetails->created_by)
                    ->select('name', 'email')
                    ->get()
                    ->unique('email');

                if ($Assignedusers != null) {

                    foreach ($Assignedusers as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            // $safetypermitdetails =  $this->initialincident->selectmail($id);
                            $incidentarray  = $incidentDetails->toArray();

                            $incidentarray['name'] = $user->name;
                            $incidentarray['email_id'] =  $email_id;
                            $incidentarray['mail_subject'] = $mailsubject;

                            Mail::to($incidentarray['email_id'])->queue(new IncidentEmail($incidentarray));
                        }
                    }
                }

                $notificationData = array(
                    'notification_type' => 5,
                    'module_type' => 3,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Fire Incident' . $incidentDetails->sr_no . ' submitted by ' . getUsername($incidentDetails->created_by),
                        'icon' =>  admin_url('public/assets/icons/fire_incident.png'),
                        'id' => $incidentDetails->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('incident/fire-incident/view/' . encryptId($incidentDetails->id)),
                    'assigned_user' => $incidentDetails->created_by,
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
                $insert_array = array(
                    'ims_type' => 3,
                    'ims_id' => $incidentDetails->id,
                    'from_status' => $incidentDetails->incident_status,
                    'to_status' => $incident_status,
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
                            $incidentarray  = $incidentDetails->toArray();

                            $incidentarray['name'] = $user->name;
                            $incidentarray['email_id'] =  $email_id;
                            $incidentarray['mail_subject'] = $mailsubject;

                            Mail::to($incidentarray['email_id'])->queue(new IncidentEmail($incidentarray));
                        }
                    }
                }

                $notificationData = array(
                    'notification_type' => 5,
                    'module_type' => 3,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Fire Incident' . $incidentDetails->sr_no . ' submitted by ' . getUsername($incidentDetails->created_by),
                        'icon' =>  admin_url('public/assets/icons/fire_incident.png'),
                        'id' => $incidentDetails->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('incident/fire-incident/review/' . encryptId($incidentDetails->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
                $insert_array = array(
                    'ims_type' => 3,
                    'ims_id' => $incidentDetails->id,
                    'from_status' => $incidentDetails->incident_status,
                    'to_status' => $incident_status,
                    'is_reject' => null,
                    'remarks' => $ehsApproval->remark,
                    'approved_by' => Auth::id(),
                );

                $this->Statuslog->create($insert_array);
            }
            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('incident/fire-incident/list'));
        } catch (Exception $ex) {
            
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/fire-incident/list'));
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

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $vendor_name = $request->vendor_name;
            $license_no = $request->license_no;
            $id = $request->id;

            if (empty($id)) {
                $isUnique = !$this->hira->uniqueCheck($vendor_name, $license_no);
            } else {
                $id = decryptId($id);
                $isUnique = !$this->hira->existUniqueCheck($vendor_name, $license_no, $id);
            }

            return Response::json($isUnique);
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->initialfireincident->statuschange($id);
            return response()->json(['status' => 'success', 'msg' => 'Your status has changed successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $incident_report = $this->initialfireincident->selectOne($id);
                $getEHSVerify = $this->initialfireincident->getEHSVerifyincident($id);
                $getEHSReview = $this->initialfireincident->getEHSReviewincident($id);
                $getInvestigation = $this->initialfireincident->getInvestigation($id);
                $getwhywhy = $this->initialfireincident->getwhywhy($id);
                $getfishbone = $this->initialfireincident->getfishbone($id);
                $fishboneData = json_decode($getfishbone->first()->fishbone, true);
                $getrisklevel = $this->initialfireincident->getrisklevel($id);
                $getEHSApprovalincident = $this->initialfireincident->getEHSApprovalincident($id);
                $initialfireincidentevidence = $this->initialfireincidentevidence->selectOne($id);

                // dd($getfishbone, $initialfireincidentevidence);
                $mediaOptions = [
                    1 => 'Phone',
                    2 => 'Walkie Talkie',
                    3 => 'Extension',
                    4 => 'Others',
                ];

                $selectedMedia = isset($incident_report->reporting_media)
                    ? explode(',', $incident_report->reporting_media)
                    : [];

                $displayMedia = array_map(function ($media) use ($mediaOptions) {
                    return $mediaOptions[$media] ?? $media;
                }, $selectedMedia);
            }

            $data = array(
                'incident_report' => $incident_report,
                'displayMedia' => $displayMedia,
                'initialfireincidentevidence' => $initialfireincidentevidence,
                'getEHSVerify' => $getEHSVerify,
                'getInvestigation' => $getInvestigation,
                'getEHSReview' => $getEHSReview,
                'getwhywhy' => $getwhywhy,
                'fishboneData' => $fishboneData,
                'getfishbone' => $getfishbone,
                'getrisklevel' => $getrisklevel,
                'getEHSApprovalincident' => $getEHSApprovalincident,
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
            $html = view('ims.initial.firereport.exportpdf', $data)->render();
            $mpdf->WriteHTML($html);
            $filename = "Fire Incident.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            
            report($ex);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->initialfireincident->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Sr. No',
                'Unit',
                'Shift',
                // 'From Status',
                'Approve Status',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->sr_no;
                $export[] =  getUnitname($data->unit_id);
                $export[] =  $data->shift;
                // $export[] =  $data->to_status;
                $export[] =  $data->status_name;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Initial Fire Incident.xlsx')
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

            $allData = $this->initialfireincident->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Sr. No',
                'Unit',
                'Shift',
                // 'From Status',
                'Approve Status',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Initial Incident",
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

            $view = view('ims.initial.firereport.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Initial Fire Incident.pdf";
            $mpdf->Output($filename, 'I');
        } catch (Exception $ex) {
            
            report($ex);
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
