<?php

namespace App\Http\Controllers\Safetypermit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Str;
use DB;
use Response;
use Exception;
use DataTables;
use Mail;
use App\Models\User;
use App\Models\Permit\SafetyPermit;
use App\Models\Permit\SafetyPermitEHSfile;
use App\Models\Permit\WorkmanInvolved;
use App\Models\Permit\SafetyApproveReject;
use App\Models\Permit\SafetyPermitExtension;
use App\Models\Permit\Statuslog;
use App\Models\Permit\SafetyPermitstatus;
use App\Mail\SafetyPermitEmail;

use App\Models\Master\Unit;
use App\Models\Master\TypeofWork;
use App\Models\Master\TypeofWorkChecklist;
use App\Models\Master\ProtectiveEquip;
use App\Models\Master\EquipInvalve;
use App\Models\Master\SafeWork;
use App\Models\Master\Precaution;
use App\Models\Master\Checklist;
use App\Models\Master\Employee;
use App\Models\Master\Department;
use Illuminate\Support\Facades\Session;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SafetyPermitController extends Controller
{
    private $safetypermit;
    private $safetypermitehsfile;
    private $safetyPermitExtension;
    private $workmaninvolved;
    private $unit;
    private $typeofwork;
    private $typeofworkchecklist;
    private $protective;
    private $equipinvalve;
    private $safework;
    private $precaution;
    private $checklist;
    private $department;
    private $approvereject;
    private $statuslog;
    private $employee;
    private $status;

    public function __construct()
    {
        $this->safetypermit = new SafetyPermit();
        $this->safetypermitehsfile = new SafetyPermitEHSfile();
        $this->safetyPermitExtension = new SafetyPermitExtension();
        $this->workmaninvolved = new WorkmanInvolved();
        $this->unit = new Unit();
        $this->typeofwork = new TypeofWork();
        $this->typeofworkchecklist = new TypeofWorkChecklist();
        $this->protective = new ProtectiveEquip();
        $this->equipinvalve = new EquipInvalve();
        $this->safework = new SafeWork();
        $this->precaution = new Precaution();
        $this->checklist = new Checklist();
        $this->department = new Department();
        $this->approvereject = new SafetyApproveReject();
        $this->statuslog = new Statuslog();
        $this->employee = new Employee();
        $this->status = new SafetyPermitstatus();
    }

    public function index(Request $request)
    {

        if (Auth::check()) {
            if ($request->ajax()) {
                try {

                    $data =  $this->safetypermit->list();
                    $datatables = Datatables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            // $text = "<span style='color:red'>In-Active<span>";
                            // if ($row->permit_status == 2) {
                            //     $text = "<span style='color:green;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->id) . "' data-type = '1' >Active<span>";
                            // }
                            // return $text;
                        })
                        ->addColumn('location_name', function ($row) {
                            return ($row->location_type_name);
                        })


                        ->editColumn('status_batch', function ($row) {
                            return  "<span class='" . $row->bg_color . "' >" . $row->status_name . "</span>";
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydatetimeformat($row->created_at);
                        })
                        ->addColumn('date', function ($row) {
                            return Displaydateformat($row->date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('verified_by', function ($row) {
                            return getUsername($row->verified_by);
                        })
                        ->addColumn('approved_by', function ($row) {
                            return getUsername($row->approved_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';

                            if ((CheckUserRole(ROLE_SUPERADMIN) &&  $row->permit_status != STATUS_CANCELLED &&  $row->permit_status != STATUS_CLOSED && $row->permit_status != STATUS_PLANT_HEAD_APPROVED && $row->permit_status != STATUS_EHS_DECLINE && $row->permit_status != STATUS_PERMIT_EXPIRED) || (!in_array(ROLE_USER, getUserRoleId(Auth::id()))) && ((in_array(ROLE_EHS_OFFICER, getUserRoleId(Auth::id())) && $row->permit_status == STATUS_EHS_VERIFICATION_PENDING) || ($row->verified_by == Auth::id() && $row->permit_status == STATUS_EHS_APPROVE_PENDING) || (in_array(ROLE_PLANT_HEAD, getUserRoleId(Auth::id())) && $row->permit_status == STATUS_PLANT_HEAD_PENDING) || (($row->verified_by == Auth::id() && $row->reassign_to == null) && $row->permit_status == STATUS_EHS_HOLD) || ($row->reassign_to == Auth::id() && $row->permit_status == STATUS_EHS_HOLD) || (($row->verified_by == Auth::id() && $row->reassign_to == null) && $row->permit_status == STATUS_EHS_RESUME) || ($row->reassign_to == Auth::id() && $row->permit_status == STATUS_EHS_RESUME) || ($row->reassign_to == Auth::id() && $row->permit_status == STATUS_EHS_REASSIGN) || ($row->verified_by == Auth::id() &&  $row->permit_status == STATUS_PERMIT_EXTENDED) || ($row->verified_by == Auth::id() && $row->permit_status == STATUS_PERMIT_EXTENDED_APPROVAL) || ($row->verified_by == Auth::id() && $row->permit_status == STATUS_PLANTHEAD_REJECTED))) {
                                $btn = '<a href="' . admin_url('safetypermit/approvereject/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="Approval">
                            <i class="fa-solid fa-check-to-slot text-success"></i>
                        </a>';
                            }

                            if ($row->date == date('Y-m-d')) {
                                if (($row->permit_status == STATUS_PERMIT_EXPIRED)
                                    && ($row->created_by == Auth::id() || isAdmin())
                                ) {
                                    $btn .= '<a href="' . admin_url('safetypermit/permitExtension/' . encryptId($row->id)) . '" class="permitExtension" title="' . __('Permit Extension') . '"><i class="fa fa-external-link"></i></a>';
                                }
                            }

                            if (CheckUserPermission('view')) {
                                $btn .= '<a href="' . admin_url('safetypermit/view/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="' . __('common.view') . '">
                            <i class="fa-solid fa-eye"></i>
                        </a>';
                            }

                            if (CheckUserPermission('edit')) {
                                if (($row->created_by == Auth::id() && ($row->permit_status == STATUS_EHS_VERIFICATION_PENDING ||  $row->permit_status == STATUS_EHS_DECLINE))) {
                                    $btn .= '<a href="' . admin_url('safetypermit/edit/' . encryptId($row->id)) . '" class="" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a> ';
                                }
                            }
                            if (($row->permit_status >= STATUS_EHS_APPROVE_PENDING  &&  $row->permit_status != STATUS_CANCELLED &&  $row->permit_status != STATUS_CLOSED)) {
                                $permitDateTime = Carbon::parse($row->date . ' ' . $row->time_to);
                                if ($permitDateTime->isFuture()) {
                                    $btn .= '<a href="' . admin_url('safetypermit/qr/pdf/' . encryptId($row->id)) . '" target="__blank" style="margin-right: 5px;" title="QR PDF">
                                                <i class="fa-solid fa-qrcode"></i>
                                            </a>';
                                }
                            }


                            $btn .= '<a href="' . admin_url('safetypermit/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';

                            if (($row->created_by == Auth::id() || isAdmin()) &&
                                ($row->permit_status == STATUS_EHS_APPROVE_PENDING || $row->permit_status == STATUS_EHS_VERIFICATION_PENDING)
                            ) {
                                $btn .= '<a href="javascript:void(0);" data-id="' . encryptId($row->id) . '" class="recordDelete" title="Cancel" style="color: #e21e23;margin-right: 5px;"><i class="fa fa-times-circle"></i></a> ';
                            }


                            if (($row->created_by == Auth::id() || isAdmin()) && ($row->permit_status == STATUS_PLANT_HEAD_APPROVED)) {
                                $btn .= '<a href="javascript:void(0);" data-id="' . encryptId($row->id) . '" class="permitClose" title="Close" style="color: green;margin-right: 5px;"><i class="fa fa-window-close" aria-hidden="true"></i></a> ';
                            }


                            return $btn;
                        })
                        ->rawColumns(['action', 'date', 'created_date', 'created_by', 'status', 'status_batch', 'verified_by', 'approved_by'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {

                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => __('ptw.please_try_after_some_time')], 406);
                }
            }
        }
        $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
        $status = $this->status->get();
        // $location = $this->location->select('id', 'location_type_name')->where('status', 1)->where('trash', 'NO')->get();
        $data = array(
            'unitList' => $unitList,
            'status' => $status,
            // 'location' => $location,
        );
        return view('permit.safetypermit.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $typeofwork = $this->typeofwork->gettypework();

            $employeeList = Employee::select('id', 'emp_id', 'emp_name', 'email', 'department', 'employee_status')->where('user_role', ROLE_USER)->where('status', 1)->get();

            $getprotectiveequipment = $this->protective->selectchecklist();
            $getequipmentinvolved = $this->equipinvalve->selectchecklist();
            $getinstruction = $this->safework->selectchecklist();
            $getprecaution = $this->precaution->selectchecklist();
            $getchecklist = $this->checklist->selectchecklist();
            $data = array(
                'unitList' => $unitList,
                'typeofwork' => $typeofwork,
                'getprotectiveequipment' => $getprotectiveequipment,
                'getequipmentinvolved' => $getequipmentinvolved,
                'getprecaution' => $getprecaution,
                'getchecklist' => $getchecklist,
                'getinstruction' => $getinstruction,
            );
            return view('permit.safetypermit.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function store(Request $request)
    {
        try {

            try {
                $rules = [
                    'date' => 'required|date',
                    'time_from' => 'required',
                    'time_to' => 'required',
                    'unit_id' => 'required',
                    'exact_location_job' => 'required',
                    'job_location_area' => 'required',
                    'sub_permit' => 'required|array|min:1',
                    'job_description' => 'required|min:3|max:600',
                    'equipment_checklist_inspection' => 'required',
                    'toolbox_talk' => 'required',
                    'talk_givenby' => 'required|min:3|max:30',
                    'assigned_job' => 'required',
                    'attendance_toolbox_talk' => 'required',
                ];

                $messages = [
                    'date.required' => 'Date cannot be empty.',
                    'time_from.required' => 'From Time cannot be empty.',
                    'time_to.required' => 'To Time cannot be empty.',
                    'unit_id.required' => 'Please Select the unit.',
                    'exact_location_job.required' => 'Exact Job Location cannot be empty.',
                    'job_location_area.required' => 'Job Location Area cannot be empty.',
                    'sub_permit.required' => 'At least one work type should be selected.',
                    'job_description.required' => 'Job Description cannot be empty.',
                    'job_description.min' => 'Job Description must be between 3 and 600 characters.',
                    'job_description.max' => 'Job Description must be between 3 and 600 characters.',
                    'equipment_checklist_inspection.required' => 'Equipment Checklist Inspection is required.',
                    'toolbox_talk.required' => 'Toolbox Talk is required.',
                    'talk_givenby.required' => 'Talk Given By is required',
                    'talk_givenby.min' => 'Name must be between 3 and 30 characters',
                    'talk_givenby.max' => 'Name must be between 3 and 30 characters',
                    'assigned_job.required' => 'Please check the Assigned job',
                    'attendance_toolbox_talk.required' => 'Attendance Tool box talk is required ',
                ];

                $validator = Validator::make($request->all(), $rules, $messages);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                // $lastStatus = $this->safetypermit->laststatus();
                // if($lastStatus !=STATUS_PLANT_HEAD_APPROVED || $lastStatus !=STATUS_PERMIT_EXPIRED ||$lastStatus !=STATUS_EHS_DECLINE ){
                //    Session::flash('error','last status is still in active');
                //    return redirect('safetypermit/list');
                // }
                $safetypermit =   $this->safetypermit->store();
                $WorkmanInvolved =   $this->workmaninvolved->store($safetypermit->id);

                $permit_status =  1;
                $mailsubject = 'Safety Permit has been submitted';
                $user_role = ROLE_EHS_OFFICER;

                // $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->where('unit_id', $safetypermit->unit_id)->pluck('id')->toArray();
                // $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->where('unit_id', $safetypermit->unit_id)->get();

                $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
                $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();


                if (count($users) > 0) {

                    foreach ($users as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            $safetypermitdetails =  $this->safetypermit->selectmail($safetypermit->id);
                            $permitrray  = $safetypermitdetails->toArray();

                            $permitrray['name'] = $user->name;
                            $permitrray['email_id'] =  $email_id;
                            $permitrray['mail_subject'] = $mailsubject;

                            Mail::to($permitrray['email_id'])->queue(new SafetyPermitEmail($permitrray));
                        }
                    }
                }


                /**
                 * Send Web notification
                 */

                $notificationData = array(
                    'notification_type' => 3,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Safety Permit ' . $safetypermit->permit_id . ' submitted by ' . getUsername($safetypermit->created_by),
                        'icon' =>  admin_url('public/assets/icons/permit_to_work.png'),
                        'id' => $safetypermit->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('safetypermit/approvereject/' . encryptId($safetypermit->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $insert_array = array(
                    'permit_type' => 0,
                    'permit_id' => $safetypermit->id,
                    'from_status' => 0,
                    'to_status' => $permit_status,
                    'is_reject' => null,
                    'remarks' => null,
                    'approved_by' => Auth::id(),
                );
                $this->statuslog->create($insert_array);

                Session::flash('success', __('Your data has been created successfully'));

                return redirect(admin_url('safetypermit/list'));
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong Please try again after some time');
                return redirect(admin_url('safetypermit/list'));
            }
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong Please try again after some time');
            return redirect(admin_url('safetypermit/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $safetypermit = $this->safetypermit->selectOne($id);
                $workmaninvolved = $this->safetypermit->workmaninvolved($id);
                $stateIsolationLoto = json_decode($safetypermit->state_isolation_loto);
                $confined_space_entry = json_decode($safetypermit->confined_space_entry);

                $status_log = $this->statuslog->selectOne($id);

                $getEhSverification =   $this->approvereject->getEhSverification($id);
                $getEhsapproval =   $this->approvereject->getEhsapproval($id);
                $getplantheadapproval =   $this->approvereject->getplantheadapproval($id);
                $getsafetyPermitExtension =   $this->safetyPermitExtension->permitextensionelectOne($id);
                $getpermitextensionapproval =   $this->approvereject->getpermitextensionapproval($id);
                $data = array(
                    'safetypermit' => $safetypermit,
                    'stateIsolationLoto' => $stateIsolationLoto,
                    'confined_space_entry' => $confined_space_entry,
                    'workmaninvolved' => $workmaninvolved,
                    'getEhSverification' => $getEhSverification,
                    'getEhsapproval' => $getEhsapproval,
                    'getplantheadapproval' => $getplantheadapproval,
                    'getsafetyPermitExtension' => $getsafetyPermitExtension,
                    'getpermitextensionapproval' => $getpermitextensionapproval,
                    'status_log' => $status_log,

                );
            }
            return view('permit.safetypermit.view', $data);
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong Please try again after some time');
            return redirect(admin_url('safetypermit/list'));
        }
    }

    public function edit(Request $request)
    {
        try {

            $id = decryptId($request->id);

            $safetypermit = $this->safetypermit->selectOne($id);

            // dd($safetypermit);
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $typeofwork = $this->typeofwork->gettypework();

            $employeeList = Employee::select('id', 'emp_id', 'emp_name', 'email', 'department', 'employee_status')->where('user_role', ROLE_USER)->where('status', 1)->get();

            $getprotectiveequipment = $this->protective->selectchecklist();
            $getequipmentinvolved = $this->equipinvalve->selectchecklist();
            $getinstruction = $this->safework->selectchecklist();
            $getprecaution = $this->precaution->selectchecklist();
            $getchecklist = $this->checklist->selectchecklist();
            $confinedSpaceEntry = json_decode($safetypermit->confined_space_entry, true);
            $stateIsolationLoto = json_decode($safetypermit->state_isolation_loto, true);
            $protectiveEquipment = json_decode($safetypermit->protective_equip, true);
            $equipmentInvolved = json_decode($safetypermit->equipment_involved, true);

            $workman = $this->workmaninvolved->getWorkmaninvolved($id);


            $data = [
                'unitList' => $unitList,
                'typeofwork' => $typeofwork,
                'getprotectiveequipment' => $getprotectiveequipment,
                'getequipmentinvolved' => $getequipmentinvolved,
                'getprecaution' => $getprecaution,
                'getchecklist' => $getchecklist,
                'getinstruction' => $getinstruction,
                'safetypermit' => $safetypermit,
                'stateIsolationLoto' => $stateIsolationLoto,
                'confinedSpaceEntry' => $confinedSpaceEntry,
                'equipmentInvolved' => $equipmentInvolved,
                'protectiveEquipment' => $protectiveEquipment,
                'workman' => $workman,
            ];
            return view('permit.safetypermit.edit', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went Wrong Please try again after some time');
            return redirect('safetypermit/list');
        }
    }

    public function update(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $rules = [
                'date' => 'required|date',
                'time_from' => 'required',
                'time_to' => 'required',
                'unit_id' => 'required',
                'exact_location_job' => 'required',
                'job_location_area' => 'required',
                'sub_permit' => 'required|array|min:1',
                'job_description' => 'required|min:3|max:600',
                'equipment_checklist_inspection' => 'required',
                'toolbox_talk' => 'required',
                'talk_givenby' => 'required|min:3|max:30',
                'assigned_job' => 'required',
                'attendance_toolbox_talk' => 'required',
            ];

            $messages = [
                'date.required' => 'Date cannot be empty.',
                'time_from.required' => 'From Time cannot be empty.',
                'time_to.required' => 'To Time cannot be empty.',
                'unit_id.required' => 'Please Select the unit.',
                'exact_location_job.required' => 'Exact Job Location cannot be empty.',
                'job_location_area.required' => 'Job Location Area cannot be empty.',
                'sub_permit.required' => 'At least one work type should be selected.',
                'job_description.required' => 'Job Description cannot be empty.',
                'job_description.min' => 'Job Description must be between 3 and 600 characters.',
                'job_description.max' => 'Job Description must be between 3 and 600 characters.',
                'equipment_checklist_inspection.required' => 'Equipment Checklist Inspection is required.',
                'toolbox_talk.required' => 'Toolbox Talk is required.',
                'talk_givenby.required' => 'Talk Given By is required',
                'talk_givenby.min' => 'Name must be between 3 and 30 characters',
                'talk_givenby.max' => 'Name must be between 3 and 30 characters',
                'assigned_job.required' => 'Please check the Assigned job',
                'attendance_toolbox_talk.required' => 'Attendance Tool box talk is required ',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {

                return redirect()->back()->withErrors($validator)->withInput();
            }
            $permit_status =  1;
            $safetypermit = $this->safetypermit->find($id);
            $this->safetypermit->updates($id);
            $this->workmaninvolved->store($id);

            $mailsubject = 'Safety Permit has been submitted';
            $user_role = ROLE_EHS_OFFICER;

            // $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->where('unit_id', $safetypermit->unit_id)->pluck('id')->toArray();
            // $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->where('unit_id', $safetypermit->unit_id)->get();

            $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
            $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();

            if (count($users) > 0) {

                foreach ($users as $user) {

                    $email_id = $user->email;

                    if ($email_id != '' || $email_id != null) {
                        $safetypermitdetails =  $this->safetypermit->selectmail($safetypermit->id);
                        $permitrray  = $safetypermitdetails->toArray();

                        $permitrray['name'] = $user->name;
                        $permitrray['email_id'] =  $email_id;
                        $permitrray['mail_subject'] = $mailsubject;

                        Mail::to($permitrray['email_id'])->queue(new SafetyPermitEmail($permitrray));
                    }
                }
            }


            /**
             * Send Web notification
             */

            $notificationData = array(
                'notification_type' => 3,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => 'Safety Permit ' . $safetypermit->permit_id . ' submitted by ' . getUsername($safetypermit->created_by),
                    'icon' =>  admin_url('public/assets/icons/permit_to_work.png'),
                    'id' => $safetypermit->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safetypermit/approvereject/' . encryptId($safetypermit->id)),
                'assigned_user' => array_to_string($userids),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $insert_array = array(
                'permit_type' => 0,
                'permit_id' => $safetypermit->id,
                'from_status' => 0,
                'to_status' => $permit_status,
                'is_reject' => null,
                'remarks' => null,
                'approved_by' => Auth::id(),
            );
            $this->statuslog->create($insert_array);


            Session::flash('success', __('Your data has been updated successfully'));
            return redirect(admin_url('safetypermit/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong Please try again after some time');
            return redirect(admin_url('safetypermit/list'));
        }
    }


    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->safetypermit->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => __('Safety Permit status changed sucessfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('Please try after some time')], 406);
        }
    }
    public function approvereject(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $safetypermit = $this->safetypermit->selectOne($id);
                $workmaninvolved = $this->safetypermit->workmaninvolved($id);
                $stateIsolationLoto = json_decode($safetypermit->state_isolation_loto);
                $confined_space_entry = json_decode($safetypermit->confined_space_entry);

                $getEhSverification =   $this->approvereject->getEhSverification($id);
                $getEhsapproval =   $this->approvereject->getEhsapproval($id);
                $getplantheadapproval =   $this->approvereject->getplantheadapproval($id);
                $getsafetyPermitExtension =   $this->safetyPermitExtension->permitextensionelectOne($id);
                $getpermitextensionapproval =   $this->approvereject->getpermitextensionapproval($id);

                $data = array(
                    'safetypermit' => $safetypermit,
                    'stateIsolationLoto' => $stateIsolationLoto,
                    'confined_space_entry' => $confined_space_entry,
                    'workmaninvolved' => $workmaninvolved,
                    'getEhSverification' => $getEhSverification,
                    'getEhsapproval' => $getEhsapproval,
                    'getplantheadapproval' => $getplantheadapproval,
                    'getsafetyPermitExtension' => $getsafetyPermitExtension,
                    'getpermitextensionapproval' => $getpermitextensionapproval,
                );
            }

            // $user=$safetypermit->created_by;
            // $unitID = User::where('id', $user)->pluck('unit_id')->first();

            // if (CheckUserRole(ROLE_EHS_OFFICER)) {

            //     if ($unitID != Auth::user()->unit_id) {
            //         Session::flash('error', 'This permit belongs to another unit');
            //         return redirect('safetypermit/list');
            //     }
            // }


            return view('permit.safetypermit.approvereject', $data);
        } catch (Exception $ex) {

            report($ex);
            report($ex);
            Session::flash('error', 'Something went wrong. Please try again after some time');
            return redirect(admin_url('safetypermit/list'));
        }
    }

    public function ehsverification(Request $request)
    {

        try {

            $id = $request->permit_id;
            $safetypermit = $this->safetypermit->find($id);

            if ($request->has('verify')) {
                $permit_status = STATUS_EHS_APPROVE_PENDING;
            }



            $approve =   $this->approvereject->ehsverification($permit_status);
            $this->safetypermitehsfile->store($approve, $permit_status);
            $this->safetypermit->verifiedby($approve->created_by, $id);
            $this->safetypermit->permitstatus($permit_status, $id);


            $mailsubject = 'EHS Verified';
            $Assignedusers = User::where('id', $approve->created_by)
                ->select('name', 'email')
                ->get()
                ->unique('email');

            if ($Assignedusers != null) {

                foreach ($Assignedusers as $user) {

                    $email_id = $user->email;

                    if ($email_id != '' || $email_id != null) {
                        $safetypermitdetails =  $this->safetypermit->selectmail($id);
                        $permitrray  = $safetypermitdetails->toArray();

                        $permitrray['name'] = $user->name;
                        $permitrray['email_id'] =  $email_id;
                        $permitrray['mail_subject'] = $mailsubject;

                        Mail::to($permitrray['email_id'])->queue(new SafetyPermitEmail($permitrray));
                    }
                }
            }

            // $userids = User::where('id', $safetypermit->verified_by)->pluck('id')->toArray();
            // $users = User::where('id', $safetypermit->verified_by)->pluck('id')->get();
            // dd($userids,$safetypermit->verified_by);
            // $users = User::where($notifywhere)->whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();
            /**
             * Send Web notification
             */

            $notificationData = array(
                'notification_type' => 3,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => 'Safety Permit ' . $safetypermit->permit_id . ' verified by ' . getUsername($approve->created_by),
                    'icon' =>  admin_url('public/assets/icons/permit_to_work.png'),
                    'id' => $safetypermit->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safetypermit/approvereject/' . encryptId($safetypermit->id)),
                'assigned_user' => $approve->created_by,
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $insert_array = array(
                'permit_type' => 1,
                'permit_id' => $id,
                'from_status' => 2,
                'to_status' => $permit_status,
                'is_reject' => null,
                'remarks' => $request->ehs_verification_remarks,
                'approved_by' => Auth::id(),
            );
            $this->statuslog->create($insert_array);

            return redirect(admin_url('safetypermit/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong Please try again after some time');
            return redirect(admin_url('safetypermit/list'));
        }
    }

    public function ehsapproval(Request $request)
    {

        try {

            $id = $request->permit_id;
            $safetypermit = $this->safetypermit->find($id);

            if ($request->has('hold')) {
                $permit_status = STATUS_EHS_HOLD;
            } elseif ($request->has('resume')) {
                $permit_status = STATUS_EHS_RESUME;
            } elseif ($request->has('decline')) {
                $permit_status = STATUS_EHS_DECLINE;
            } elseif ($request->has('reassign')) {
                $permit_status = STATUS_EHS_REASSIGN;
            } elseif ($request->has('forward')) {

                $permit_status = STATUS_PLANT_HEAD_PENDING;
            }

            $approve =   $this->approvereject->ehsapproval($permit_status);
            if ($request->has('reassign')) {
                $this->safetypermit->reassignto($request->reassign_to, $id);
            } elseif ($request->has('resume') || $request->has('hold')) {
                $this->safetypermit->resume_hold($approve->created_by, $id);
            }
            $this->safetypermit->permitstatus($permit_status, $id);

            if ($request->has('hold')) {

                $mailsubject = 'EHS Holded the permit';
                $Assignedusers = User::whereIn('id', [$approve->created_by, $safetypermit->created_by])
                    ->select('name', 'email')
                    ->get()
                    ->unique('email');

                if (count($Assignedusers) > 0) {

                    foreach ($Assignedusers as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            $safetypermitdetails =  $this->safetypermit->selectmail($id);
                            $permitrray  = $safetypermitdetails->toArray();

                            $permitrray['name'] = $user->name;
                            $permitrray['email_id'] =  $email_id;
                            $permitrray['mail_subject'] = $mailsubject;

                            Mail::to($permitrray['email_id'])->queue(new SafetyPermitEmail($permitrray));
                        }
                    }
                }
                $UserIds = User::whereIn('id', [$safetypermit->resume_hold_by, $safetypermit->created_by])
                    ->pluck('id')
                    ->toArray();

                $UserIdsCommaSeparated = implode(',', $UserIds);
                $notificationData = array(
                    'notification_type' => 3,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Safety Permit ' . $safetypermit->permit_id . $mailsubject . getUsername($approve->created_by),
                        'icon' =>  admin_url('public/assets/icons/permit_to_work.png'),
                        'id' => $safetypermit->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('safetypermit/approvereject/' . encryptId($safetypermit->id)),
                    'assigned_user' => $UserIdsCommaSeparated,
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
            } elseif ($request->has('resume')) {
                $mailsubject = 'EHS Resumed the permit';
                $Assignedusers = User::whereIn('id', [$approve->created_by, $safetypermit->created_by])
                    ->select('name', 'email')
                    ->get()
                    ->unique('email');

                if (count($Assignedusers) > 0) {

                    foreach ($Assignedusers as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            $safetypermitdetails =  $this->safetypermit->selectmail($id);
                            $permitrray  = $safetypermitdetails->toArray();

                            $permitrray['name'] = $user->name;
                            $permitrray['email_id'] =  $email_id;
                            $permitrray['mail_subject'] = $mailsubject;

                            Mail::to($permitrray['email_id'])->queue(new SafetyPermitEmail($permitrray));
                        }
                    }
                }
                $UserIds = User::whereIn('id', [$safetypermit->resume_hold_by, $safetypermit->created_by])
                    ->pluck('id')
                    ->toArray();

                $UserIdsCommaSeparated = implode(',', $UserIds);
                $notificationData = array(
                    'notification_type' => 3,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Safety Permit ' . $safetypermit->permit_id . $mailsubject . getUsername($approve->created_by),
                        'icon' =>  admin_url('public/assets/icons/permit_to_work.png'),
                        'id' => $safetypermit->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('safetypermit/approvereject/' . encryptId($safetypermit->id)),
                    'assigned_user' => $UserIdsCommaSeparated,
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
            } elseif ($request->has('decline')) {
                $mailsubject = 'EHS declined the permit Rework the permit';
                $notifywhere = array(
                    'id' => $safetypermit->created_by,
                );
                $userids = User::where($notifywhere)->pluck('id')->toArray();
                $users = User::where($notifywhere)->get();

                if (count($users) > 0) {

                    foreach ($users as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            $safetypermitdetails =  $this->safetypermit->selectmail($id);
                            $permitrray  = $safetypermitdetails->toArray();

                            $permitrray['name'] = $user->name;
                            $permitrray['email_id'] =  $email_id;
                            $permitrray['mail_subject'] = $mailsubject;

                            Mail::to($permitrray['email_id'])->queue(new SafetyPermitEmail($permitrray));
                        }
                    }
                }


                /**
                 * Send Web notification
                 */

                $notificationData = array(
                    'notification_type' => 3,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Safety Permit ' . $safetypermit->permit_id . $mailsubject . getUsername($approve->created_by),
                        'icon' =>  admin_url('public/assets/icons/permit_to_work.png'),
                        'id' => $safetypermit->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('safetypermit/edit/' . encryptId($safetypermit->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
            } elseif ($request->has('reassign')) {
                $mailsubject = 'EHS Re-assigned the permit';
                $notifywhere = array(
                    'id' => $request->reassign_to,
                );
                $userids = User::where($notifywhere)->pluck('id')->toArray();
                $users = User::where($notifywhere)->get();

                if (count($users) > 0) {

                    foreach ($users as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            $safetypermitdetails =  $this->safetypermit->selectmail($id);
                            $permitrray  = $safetypermitdetails->toArray();

                            $permitrray['name'] = $user->name;
                            $permitrray['email_id'] =  $email_id;
                            $permitrray['mail_subject'] = $mailsubject;

                            Mail::to($permitrray['email_id'])->queue(new SafetyPermitEmail($permitrray));
                        }
                    }
                }


                /**
                 * Send Web notification
                 */

                $notificationData = array(
                    'notification_type' => 3,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Safety Permit ' . $safetypermit->permit_id . $mailsubject . getUsername($approve->created_by),
                        'icon' =>  admin_url('public/assets/icons/permit_to_work.png'),
                        'id' => $safetypermit->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('safetypermit/approvereject/' . encryptId($safetypermit->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
            } elseif ($request->has('forward')) {
                // dd('STATUS_PLANT_HEAD_PENDING', $request);
                $mailsubject = 'EHS Approved';
                $user_role = ROLE_PLANT_HEAD;

                $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->where('unit_id', $safetypermit->unit_id)->pluck('id')->toArray();
                $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->where('unit_id', $safetypermit->unit_id)->get();



                if (count($users) > 0) {

                    foreach ($users as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            $safetypermitdetails =  $this->safetypermit->selectmail($id);
                            $permitrray  = $safetypermitdetails->toArray();

                            $permitrray['name'] = $user->name;
                            $permitrray['email_id'] =  $email_id;
                            $permitrray['mail_subject'] = $mailsubject;

                            Mail::to($permitrray['email_id'])->queue(new SafetyPermitEmail($permitrray));
                        }
                    }
                }


                /**
                 * Send Web notification
                 */

                $notificationData = array(
                    'notification_type' => 3,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Safety Permit ' . $safetypermit->permit_id . $mailsubject . getUsername($approve->created_by),
                        'icon' =>  admin_url('public/assets/icons/permit_to_work.png'),
                        'id' => $safetypermit->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('safetypermit/approvereject/' . encryptId($safetypermit->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
            }

            $insert_array = array(
                'permit_type' => 2,
                'permit_id' => $id,
                'from_status' => 2,
                'to_status' => $permit_status,
                'is_reject' => null,
                'remarks' => $request->ehs_approval_remarks,
                'approved_by' => Auth::id(),
            );

            $this->statuslog->create($insert_array);

            return redirect(admin_url('safetypermit/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong Please try again after some time');
            return redirect(admin_url('safetypermit/list'));
        }
    }

    public function plantheadapproval(Request $request)
    {

        try {

            $id = $request->permit_id;
            $safetypermit = $this->safetypermit->find($id);

            if ($request->has('approve')) {
                $permit_status = STATUS_PLANT_HEAD_APPROVED;
                $mailsubject = 'Plant Head Approved';
                $Assignedusers = User::whereRaw('FIND_IN_SET(' . ROLE_EHS_HEAD . ', role)')
                    ->orWhere('id', $safetypermit->created_by)
                    ->select('name', 'email')
                    ->get()
                    ->unique('email');

                $UserId =  User::whereRaw('FIND_IN_SET(' . ROLE_EHS_HEAD . ', role)')->pluck('id')->toArray();
                $assigned_user = array_merge([$safetypermit->created_by], $UserId);
                $assigned_user = array_unique($assigned_user);
            } elseif ($request->has('reject')) {
                $permit_status = STATUS_PLANTHEAD_REJECTED;
                $mailsubject = 'Plant Head Rejected';
                $Assignedusers = User::where('id', $safetypermit->verified_by)
                    ->select('name', 'email')
                    ->get()
                    ->unique('email');
                $assigned_user = User::where('id', $safetypermit->verified_by)->pluck('id')->toArray();
            }

            $approve =   $this->approvereject->plantheadApproval($permit_status);
            $this->safetypermit->approved_by($approve->created_by, $id);
            $this->safetypermit->permitstatus($permit_status, $id);



            if ($Assignedusers != null) {

                foreach ($Assignedusers as $user) {
                    $email_id = $user->email;

                    if ($email_id != '' || $email_id != null) {
                        $safetypermitdetails =  $this->safetypermit->selectmail($id);
                        $permitrray  = $safetypermitdetails->toArray();

                        $permitrray['name'] = $user->name;
                        $permitrray['email_id'] =  $email_id;
                        $permitrray['mail_subject'] = $mailsubject;

                        Mail::to($permitrray['email_id'])->queue(new SafetyPermitEmail($permitrray));
                    }
                }
            }


            /**
             * Send Web notification
             */


            $notificationData = array(
                'notification_type' => 3,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => 'Safety Permit ' . $safetypermit->permit_id . ' approved by ' . getUsername($approve->created_by),
                    'icon' =>  admin_url('public/assets/icons/permit_to_work.png'),
                    'id' => $safetypermit->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safetypermit/approvereject/' . encryptId($safetypermit->id)),
                'assigned_user' => array_to_string($assigned_user),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $insert_array = array(
                'permit_type' => 1,
                'permit_id' => $id,
                'from_status' => 6,
                'to_status' => $permit_status,
                'is_reject' => null,
                'remarks' => $request->planthead_approval_remarks,
                'approved_by' => Auth::id(),
            );
            $this->statuslog->create($insert_array);

            return redirect(admin_url('safetypermit/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong Please try again after some time');
            return redirect(admin_url('safetypermit/list'));
        }
    }

    public function delete(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $remarks = $request->remarks;
            $safetypermit = $this->safetypermit->find($id);
            $this->safetypermit->deleterecord($id, $remarks);

            $mailsubject = 'Safety Permit has been Cancelled';
            $user_roles = [ROLE_EHS_OFFICER, ROLE_PLANT_HEAD]; // Define roles to notify
            $userids = [];
            $users = collect(); // Initialize an empty collection

            foreach ($user_roles as $user_role) {
                $roleUsers = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')
                    ->where('unit_id', $safetypermit->unit_id)
                    ->get();

                $userids = array_merge($userids, $roleUsers->pluck('id')->toArray());
                $users = $users->merge($roleUsers);
            }

            // Remove duplicate user IDs (if any)
            $userids = array_unique($userids);

            if ($users->count() > 0) {
                foreach ($users as $user) {
                    $email_id = $user->email;

                    if (!empty($email_id)) {
                        $safetypermitdetails = $this->safetypermit->selectmail($safetypermit->id);
                        $permitrray = $safetypermitdetails->toArray();

                        $permitrray['name'] = $user->name;
                        $permitrray['email_id'] = $email_id;
                        $permitrray['mail_subject'] = $mailsubject;

                        // Send email
                        Mail::to($permitrray['email_id'])->queue(new SafetyPermitEmail($permitrray));
                    }
                }
            }

            /**
             * Send Web Notification
             */
            $notificationData = array(
                'notification_type' => 3,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => 'Safety Permit ' . $safetypermit->permit_id . ' Cancelled by ' . getUsername($safetypermit->created_by),
                    'icon' => admin_url('public/assets/icons/permit_to_work.png'),
                    'id' => $safetypermit->id,
                    'module' => 1,
                )),
                'web_link' => admin_url('safetypermit/view/' . encryptId($safetypermit->id)),
                'assigned_user' => implode(',', $userids), // Assign all user IDs
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);


            $insert_array = array(
                'permit_type' => 0,
                'permit_id' => $safetypermit->id,
                'from_status' => $safetypermit->permit_status,
                'to_status' => 14,
                'is_reject' => null,
                'remarks' => null,
                'approved_by' => Auth::id(),
            );
            $this->statuslog->create($insert_array);

            return response()->json(['status' => 'success', 'msg' => __('Work Permit Cancelled Successfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('ptw.please_try_after_some_time')], 406);
        }
    }


    public function close(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $remarks = $request->remarks;
            $safetypermit = $this->safetypermit->find($id);
            $this->safetypermit->closePermit($id, $remarks);

            $mailsubject = 'Safety Permit has been Closed';
            $user_roles = [ROLE_EHS_OFFICER, ROLE_EHS_HEAD];
            $userids = [];
            $users = collect();

            foreach ($user_roles as $user_role) {
                $roleUsers = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')
                    ->get();

                $userids = array_merge($userids, $roleUsers->pluck('id')->toArray());
                $users = $users->merge($roleUsers);
            }

            // Remove duplicate user IDs (if any)
            $userids = array_unique($userids);

            if ($users->count() > 0) {
                foreach ($users as $user) {
                    $email_id = $user->email;

                    if (!empty($email_id)) {
                        $safetypermitdetails = $this->safetypermit->selectmail($safetypermit->id);
                        $permitrray = $safetypermitdetails->toArray();

                        $permitrray['name'] = $user->name;
                        $permitrray['email_id'] = $email_id;
                        $permitrray['mail_subject'] = $mailsubject;

                        // Send email
                        Mail::to($permitrray['email_id'])->queue(new SafetyPermitEmail($permitrray));
                    }
                }
            }

            /**
             * Send Web Notification
             */
            $notificationData = array(
                'notification_type' => 3,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => 'Safety Permit ' . $safetypermit->permit_id . ' Closed by ' . getUsername($safetypermit->created_by),
                    'icon' => admin_url('public/assets/icons/permit_to_work.png'),
                    'id' => $safetypermit->id,
                    'module' => 1,
                )),
                'web_link' => admin_url('safetypermit/view/' . encryptId($safetypermit->id)),
                'assigned_user' => implode(',', $userids), // Assign all user IDs
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);


            $insert_array = array(
                'permit_type' => 0,
                'permit_id' => $safetypermit->id,
                'from_status' => $safetypermit->permit_status,
                'to_status' => 15,
                'is_reject' => null,
                'remarks' => null,
                'approved_by' => Auth::id(),
            );
            $this->statuslog->create($insert_array);

            return response()->json(['status' => 'success', 'msg' => __('Work Permit Closed Successfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('ptw.please_try_after_some_time')], 406);
        }
    }


    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->safetypermit->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("Work Permit No"),
                __("Unit"),
                __("Date"),
                __("Exact Job Location"),
                __("From Status"),
                __("To Status"),
                __("Verified By"),
                __("Approved By"),
                __("Created By"),
            ];
            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->permit_id;
                $export[] = getUnitname($data->unit_id);
                $export[] = Displaydateformat($data->date);
                $export[] = $data->exact_location_job;
                $export[] =  $data->to_status;
                $export[] =  $data->status_name;
                $export[] =  getUsername($data->verified_by);
                $export[] =  getUsername($data->approved_by);
                $export[] =  getUsername($data->created_by);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Safety Permit.xlsx')
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

            $allData = $this->safetypermit->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("Work Permit No"),
                __("Unit"),
                __("Date"),
                __("Exact Job Location"),
                __("From Status"),
                __("To Status"),
                __("Verified By"),
                __("Approved By"),
                __("Created By"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Safety Permit",
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

            $view = view('permit.safetypermit.generalpdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Safety Permit.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong Please try again after some time');
        }
    }

    public function getprotectivechecklist(Request $request, $workId)
    {
        $id = decryptId($request->input('id'));

        $checkpoints = $this->typeofworkchecklist->getprotectiveequipment($workId, 'type1');

        return response()->json($checkpoints);
    }

    public function getequipmentinvolved($workId)
    {

        $getequipmentinvolved = $this->typeofworkchecklist->getequipmentinvolved($workId, 'type2');
        return response()->json($getequipmentinvolved);
    }

    public function getprecaution($workId)
    {
        $getprecaution = $this->typeofworkchecklist->getprecaution($workId, 'type3');
        return response()->json($getprecaution);
    }

    public function getchecklist($workId)
    {

        $getchecklist = $this->typeofworkchecklist->getchecklist($workId, 'type4');
        return response()->json($getchecklist);
    }

    public function getinstruction($workId)
    {
        $getinstruction = $this->typeofworkchecklist->getinstruction($workId, 'type5');
        return response()->json($getinstruction);
    }

    public function employeename(Request $request)
    {
        $name = $request->input('search');

        $employees = Employee::where('emp_name', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->limit(10)
            ->get();

        return response()->json(
            $employees->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'text' => $employee->emp_name . ' - ' . $employee->emp_id,
                ];
            })
        );
    }

    public function reassignemployeename(Request $request)
    {
        $name = $request->input('search');
        $unitId = $request->input('unitId');

        $employees = Employee::where('emp_name', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->whereRaw("FIND_IN_SET(?, user_role)", [3])
            ->where('login_id', '!=', Auth::id())
            ->limit(10)
            ->get();

        return response()->json(
            $employees->map(function ($employee) {
                return [
                    'id' => $employee->login_id,
                    'text' => $employee->emp_name . ' - ' . $employee->emp_id,
                ];
            })
        );
    }

    public function employeeid(Request $request)
    {
        $name = $request->input('search');

        $permit_id = decryptId($request->input('permit_id'));


        $emp_ids = $this->workmaninvolved->getempIds($permit_id);

        if ($permit_id) {

            $employee_code = Employee::whereIn('id', $emp_ids)
                ->where('emp_id', 'like', '%' . $name . '%')
                ->where('status', 1)
                ->limit(10)
                ->get();
        } else {
            $employee_code = Employee::where('emp_id', 'like', '%' . $name . '%')
                ->where('status', 1)
                ->limit(10)
                ->get();
        }

        return response()->json(
            $employee_code->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'text' => $employee->emp_id,
                ];
            })
        );
    }

    public function fetchEmployeeDetails($emp_id)
    {
        $employee = Employee::select('emp_name', 'email', 'department', 'designation')
            ->where('id', $emp_id)
            ->first();

        $departments = $this->department->select('id', 'department_name')->where('status', '1')->get();

        return response()->json([
            'employee' => $employee,
            'departments' => $departments
        ]);
    }

    public function permitQRPDF($id)
    {
        $url = admin_url('safetypermit/join/' . $id);

        $safetypermit = $this->safetypermit->selectOne(decryptId($id));
        $qrSvg = QrCode::size(150)
            ->backgroundColor(255, 255, 255)
            ->color(1, 1, 1)
            ->generate($url);
        $permit_no = get_permit_no(decryptId($id));
        $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);

        $pagetitle = 'Safety Permit QR';
        $property = [
            'tempDir' => 'public/pdf/temp/',
            'mode' => 'c',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
        ];

        $mpdf = new \Mpdf\Mpdf($property);
        $mpdf->setAutoTopMargin = 'stretch';

        $view = view('permit.safetypermit.permitjoin', compact('qrBase64', 'permit_no', 'safetypermit'));
        $html = $view->render();

        $mpdf->WriteHTML($html);
        $filename = "SafetyPermit.pdf";
        $mpdf->Output($filename, 'I');
    }
    public function permit_join($id)
    {
        return 'permit__' . $id;
    }

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $safetypermit = $this->safetypermit->selectOne($id);
                $stateIsolationLoto = json_decode($safetypermit->state_isolation_loto);
                $confined_space_entry = json_decode($safetypermit->confined_space_entry);
                $workmaninvolved = $this->safetypermit->workmaninvolved($id);

                $getEhSverification =   $this->approvereject->getEhSverification($id);
                $getEhsapproval =   $this->approvereject->getEhsapproval($id);
                $getplantheadapproval =   $this->approvereject->getplantheadapproval($id);
                $getsafetyPermitExtension =   $this->safetyPermitExtension->permitextensionelectOne($id);
                $getpermitextensionapproval =   $this->approvereject->getpermitextensionapproval($id);
            }

            $data = [
                'safetypermit' => $safetypermit,
                'pagetitle' => "Safety Permit",
                'stateIsolationLoto' => $stateIsolationLoto,
                'confined_space_entry' => $confined_space_entry,
                'workmaninvolved' => $workmaninvolved,
                'getEhSverification' => $getEhSverification,
                'getEhsapproval' => $getEhsapproval,
                'getplantheadapproval' => $getplantheadapproval,
                'getsafetyPermitExtension' => $getsafetyPermitExtension,
                'getpermitextensionapproval' => $getpermitextensionapproval,
            ];

            $property = [
                'tempDir' => 'public/pdf/temp/',
                // 'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'fontDir' => array_merge((new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'], [
                    public_path('assets/fonts/Noto_Sans_Devanagari'),
                ]),
                'fontdata' => array_merge((new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'], [
                    'NotoSansDevanagari' => [
                        'R' => 'NotoSansDevanagari-Regular.ttf',
                        'B' => 'NotoSansDevanagari-Bold.ttf',
                    ],
                ]),
                'default_font' => 'NotoSansDevanagari',

            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';
            // $mpdf = new \Mpdf\Mpdf([
            //     'fontDir' => array_merge((new Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'], [
            //         public_path('assets/fonts/Noto_Sans_Devanagari'),
            //     ]),
            //     'fontdata' => array_merge((new Mpdf\Config\FontVariables())->getDefaults()['fontdata'], [
            //         'NotoSansDevanagari' => [
            //             'R' => 'NotoSansDevanagari-Regular.ttf',
            //             'B' => 'NotoSansDevanagari-Bold.ttf',
            //         ],
            //     ]),
            //     'default_font' => 'NotoSansDevanagari',
            // ]);
            $html = view('permit.safetypermit.exportpdf', $data)->render();
            $mpdf->WriteHTML($html);
            $filename = "Safety Permit.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            report($ex);
        }
    }

    public function permitExtension(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $safetypermit = $this->safetypermit->selectOne($id);
            // $getpermitextension = $this->extension->getpermitextension($id);
            // $getPermitExtensionsupervisor = $this->approvereject->getPermitExtensionsupervisor($id);
            // $getPermitExtensionsuperintendednt = $this->approvereject->getPermitExtensionsuperintendednt($id);

            $data = array(
                'safetypermit' => $safetypermit,
                // 'getpermitextension' => $getpermitextension,
                // 'getPermitExtensionsupervisor' => $getPermitExtensionsupervisor,
                // 'getPermitExtensionsuperintendednt' => $getPermitExtensionsuperintendednt,
            );
            return view('permit.safetypermit.permitextension', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function permitExtensionsubmit(Request $request)
    {

        try {

            $id = $request->permit_id;
            $safetypermit = $this->safetypermit->find($id);
            $permit_status = STATUS_PERMIT_EXTENDED;
            $approve =   $this->safetyPermitExtension->store($permit_status, $id);
            $this->safetypermit->permitstatus($permit_status, $id);
            $this->safetypermit->permit_extended_status($id, $permit_status);
            $this->safetypermit->permit_extended_time($id, $request->time_to);

            $mailsubject = 'Permit Extended';
            $Assignedusers = User::where('id', $safetypermit->verified_by)
                ->select('name', 'email')
                ->get()
                ->unique('email');

            if ($Assignedusers != null) {

                foreach ($Assignedusers as $user) {

                    $email_id = $user->email;

                    if ($email_id != '' || $email_id != null) {
                        $safetypermitdetails =  $this->safetypermit->selectmail($id);
                        $permitrray  = $safetypermitdetails->toArray();

                        $permitrray['name'] = $user->name;
                        $permitrray['email_id'] =  $email_id;
                        $permitrray['mail_subject'] = $mailsubject;

                        Mail::to($permitrray['email_id'])->queue(new SafetyPermitEmail($permitrray));
                    }
                }
            }

            $userids = User::where('id', $safetypermit->verified_by)->pluck('id')->toArray();
            /**
             * Send Web notification
             */

            $notificationData = array(
                'notification_type' => 3,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => 'Safety Permit ' . $safetypermit->permit_id . 'submitted for permit extension by ' . getUsername($approve->created_by),
                    'icon' =>  admin_url('public/assets/icons/permit_to_work.png'),
                    'id' => $safetypermit->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safetypermit/approvereject/' . encryptId($safetypermit->id)),
                'assigned_user' => array_to_string($userids),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $insert_array = array(
                'permit_type' => 1,
                'permit_id' => $id,
                'from_status' => $request->permit_status,
                'to_status' => $permit_status,
                'is_reject' => null,
                'remarks' => $request->extension_remarks,
                'approved_by' => Auth::id(),
            );
            $this->statuslog->create($insert_array);


            return redirect(admin_url('safetypermit/list'));
        } catch (Exception $ex) {

            report($ex);
            return redirect(admin_url('safetypermit/list'));
        }
    }

    public function permitextensionapproval(Request $request)
    {

        try {

            $id = $request->permit_id;
            $safetypermit = $this->safetypermit->find($id);

            if ($request->has('approve')) {
                $permit_status = STATUS_PERMIT_EXTENDED_APPROVAL;
            } elseif ($request->has('reject')) {
                $permit_status = STATUS_PERMIT_EXTENDED_REJECTED;
            }

            $approve =   $this->approvereject->extensionApproval($permit_status);
            $this->safetypermit->permitstatus($permit_status, $id);

            if ($request->has('approve')) {
                $mailsubject = 'EHS approved the extension';
                $Assignedusers = User::where('id', $safetypermit->verified_by)
                    ->select('name', 'email')
                    ->get()
                    ->unique('email');
                $userids = User::where('id', $safetypermit->verified_by)->pluck('id')->toArray();
            } elseif ($request->has('reject')) {

                $mailsubject = 'EHS rejected the extension';
                $Assignedusers = User::where('id', $safetypermit->created_by)
                    ->select('name', 'email')
                    ->get()
                    ->unique('email');
                $userids = User::where('id', $safetypermit->created_by)->pluck('id')->toArray();
            }

            if ($Assignedusers != null) {

                foreach ($Assignedusers as $user) {

                    $email_id = $user->email;

                    if ($email_id != '' || $email_id != null) {
                        // $safetypermit = new SafetyPermit();
                        $safetypermitdetails =  $this->safetypermit->selectmail($id);
                        $permitrray  = $safetypermitdetails->toArray();

                        $permitrray['name'] = $user->name;
                        $permitrray['email_id'] =  $email_id;
                        $permitrray['mail_subject'] = $mailsubject;

                        Mail::to($permitrray['email_id'])->queue(new SafetyPermitEmail($permitrray));
                    }
                }
            }


            /**
             * Send Web notification
             */

            $notificationData = array(
                'notification_type' => 3,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => 'Safety Permit ' . $safetypermit->permit_id .  $mailsubject . getUsername($approve->created_by),
                    'icon' =>  admin_url('public/assets/icons/permit_to_work.png'),
                    'id' => $safetypermit->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safetypermit/approvereject/' . encryptId($safetypermit->id)),
                'assigned_user' => array_to_string($userids),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $insert_array = array(
                'permit_type' => 1,
                'permit_id' => $id,
                'from_status' => 2,
                'to_status' => $permit_status,
                'is_reject' => null,
                'remarks' => $request->ehs_verification_remarks,
                'approved_by' => Auth::id(),
            );
            $this->statuslog->create($insert_array);

            return redirect(admin_url('safetypermit/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong Please try again after some time');
            return redirect(admin_url('safetypermit/list'));
        }
    }


    public function dashboard(Request $request)
    {
        try {
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $data = array(
                'unitList' => $unitList,
            );
            return view('permit.safetypermit.dashboard', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }


    public function unitwiseptw(Request $request)
    {
        $user = Auth::user();
        $id = Auth::id();


        $unit = $this->unit
            ->select('id', 'unit_name')
            ->where('status', 1)
            ->where('trash', 'NO')
            ->get();


        $permitCountsQuery = $this->safetypermit
            ->selectRaw('unit_id, COUNT(*) as permit_count')
            ->where('status', 1)
            ->where('trash', 'NO')
            ->groupBy('unit_id');


        if ($request->has('Unit') && $request->Unit) {
            $permitCountsQuery->where('unit_id', 'LIKE', '%' . $request->Unit . '%');
        }
        if ($request->has('Fromdate') && !empty($request->Fromdate)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->Fromdate)->startOfDay()->format('Y-m-d H:i:s');
            $permitCountsQuery->where('created_at', '>=', $startDate);
        }
        if ($request->has('Todate') && !empty($request->Todate)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->Todate)->endOfDay()->format('Y-m-d H:i:s');
            $permitCountsQuery->where('created_at', '<=', $endDate);
        }
        if ($request->has('Fromdate') && !empty($request->Fromdate) && $request->has('Todate') && !empty($request->Todate)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->Fromdate)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->Todate)->endOfDay()->format('Y-m-d H:i:s');
            $permitCountsQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $permitCounts = $permitCountsQuery->get()->pluck('permit_count', 'unit_id');

        $result = $unit->map(function ($unit) use ($permitCounts) {
            return [
                'unit_id' => $unit->id,
                'unit_name' => $unit->unit_name,
                'permit_count' => $permitCounts[$unit->id] ?? 0,
            ];
        });

        return view('permit.safetypermit.unitwisecount', [
            'unit' => $unit,
            'unitData' => $result,
        ]);
    }


    public function monthwiseptw(Request $request)
    {
        // Fetch month-wise permit counts grouped by month and year
        $permitCounts = $this->safetypermit
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as permit_count')
            ->where('status', 1)
            ->where('trash', 'NO')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();


        $result = [];
        $currentYear = date('Y');


        for ($month = 1; $month <= 12; $month++) {
            $result[$month] = 0;
        }


        foreach ($permitCounts as $count) {
            if ($count->year == $currentYear) {
                $result[$count->month] = $count->permit_count;
            }
        }

        return view('permit.safetypermit.monthwisecount', [
            'monthlyCounts' => $result,
        ]);
    }



    public function getPermitStatus()
    {

        $request = request();

        $params = [
            'unit_id' => $request->Unit ? (array)$request->Unit : [],
            'from_date' => $request->Fromdate ?? null,
            'to_date' => $request->Todate ?? null,
        ];

        $permitStatus = [
            [
                'name' => 'Total Training',
                'count' => permitStatusCount('', $params),
                'icon' => 'bx bx-message-square-detail',
                'icon_color' => 'text-primary',
                'url' => admin_url('training_schedule/list/'),
            ],
            [
                'name' => 'EHS Verification  Pending',
                'count' => permitStatusCount(1, $params),
                'icon' => 'bx bx-file-find',
                'icon_color' => 'text-primary',
                'url' => admin_url('training_schedule/list/' . encryptId(1)),
            ],
            [
                'name' => 'EHS Approval Pending',
                'count' => permitStatusCount(2, $params),
                'icon' => 'bx bx-file-find',
                'icon_color' => 'text-primary',
                'url' => admin_url('training_schedule/list/' . encryptId(1)),
            ],
            [
                'name' => 'EHS Hold the Permit',
                'count' => permitStatusCount(3, $params),
                'icon' => 'bx bx-file-find',
                'icon_color' => 'text-primary',
                'url' => admin_url('training_schedule/list/' . encryptId(1)),
            ],
            [
                'name' => 'EHS Declined the Permit-Rework the permit',
                'count' => permitStatusCount(4, $params),
                'icon' => 'bx bx-file-find',
                'icon_color' => 'text-primary',
                'url' => admin_url('training_schedule/list/' . encryptId(1)),
            ],
            [
                'name' => 'EHS Re-assigned the Permit',
                'count' => permitStatusCount(5, $params),
                'icon' => 'bx bx-file-find',
                'icon_color' => 'text-primary',
                'url' => admin_url('training_schedule/list/' . encryptId(1)),
            ],
            [
                'name' => 'Plant Head Approval Pending',
                'count' => permitStatusCount(6, $params),
                'icon' => 'bx bx-file-find',
                'icon_color' => 'text-primary',
                'url' => admin_url('training_schedule/list/' . encryptId(1)),
            ],
            [
                'name' => 'Plant Head Approved',
                'count' => permitStatusCount(7, $params),
                'icon' => 'bx bx-file-find',
                'icon_color' => 'text-primary',
                'url' => admin_url('training_schedule/list/' . encryptId(1)),
            ],
            [
                'name' => 'EHS Resumed the permit',
                'count' => permitStatusCount(8, $params),
                'icon' => 'bx bx-file-find',
                'icon_color' => 'text-primary',
                'url' => admin_url('training_schedule/list/' . encryptId(1)),
            ],
            [
                'name' => 'Permit Expired',
                'count' => permitStatusCount(9, $params),
                'icon' => 'bx bx-file-find',
                'icon_color' => 'text-primary',
                'url' => admin_url('training_schedule/list/' . encryptId(1)),
            ],
            [
                'name' => 'Permit Extended - EHS extension approval pending',
                'count' => permitStatusCount(10, $params),
                'icon' => 'bx bx-message-square-edit',
                'icon_color' => 'text-info',
                'url' => admin_url('training_schedule/list/' . encryptId(2)),
            ],
            [
                'name' => 'Permit extension approved - EHS approval pending',
                'count' => permitStatusCount(11, $params),
                'icon' => 'bx bx-x-circle',
                'icon_color' => 'text-info',
                'url' => admin_url('training_schedule/list/' . encryptId(3)),
            ],
            [
                'name' => 'Permit extension rejected - Resubmit extension',
                'count' => permitStatusCount(12, $params),
                'icon' => 'bx bx-x-circle',
                'icon_color' => 'text-danger',
                'url' => admin_url('training_schedule/list/' . encryptId(3)),
            ],
            [
                'name' => 'Plant Head Rejected - EHS Verification resubmit',
                'count' => permitStatusCount(13, $params),
                'icon' => 'bx bx-x-circle',
                'icon_color' => 'text-danger',
                'url' => admin_url('training_schedule/list/' . encryptId(3)),
            ],
            [
                'name' => 'Permit Cancelled',
                'count' => permitStatusCount(14, $params),
                'icon' => 'bx bx-x-circle',
                'icon_color' => 'text-danger',
                'url' => admin_url('training_schedule/list/' . encryptId(3)),
            ],
            [
                'name' => 'Closed',
                'count' => permitStatusCount(15, $params),
                'icon' => 'bx bx-message-square-check',
                'icon_color' => 'text-success',
                'url' => admin_url('training_schedule/list/' . encryptId(4)),
            ],
        ];

        //    dd($permitStatus);
        $data = [
            'permit_status' => $permitStatus,
        ];
        // return view('permit.safetypermit.dashboard', $data);

        return json_encode($data);
    }
}
