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
use Response;
use Session;
use Exception;
use DataTables;
use Mail;
use App\Models\User;
use App\Models\Permit\SafetyPermit;
use App\Models\Permit\WorkmanInvolved;
use App\Models\Permit\SafetyApproveReject;
use App\Models\Permit\Statuslog;
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


class SafetyPermitController extends Controller
{
    private $safetypermit;
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

    public function __construct()
    {
        $this->safetypermit = new SafetyPermit();
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
                            $text = "<span style='color:red'>In-Active<span>";
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->id) . "' data-type = '1' >Active<span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->id) . "' data-type = '0' >In-Active<span>";
                            }
                            return $text;
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
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';


                            $btn = '<a href="' . admin_url('safetypermit/approvereject/' . encryptId($row->id)) . '" class="" title="Approval"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';

                            $btn .= '<a href="' . admin_url('safetypermit/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';



                            // $btn .= '<a href="' . admin_url('ptw/hotwork_permit/view/pdf/' . encryptId($row->id)) . '" data-toggle="tooltip" data-placement="top" class="pdficon" title="Pdf"><i class="fas fa-file-pdf" aria-hidden="true"></i> ';


                            $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '"  class="recordDelete" title="' . __('common.delete') . '"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'status_batch'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {

                    dd($ex);
                    return response()->json(['status' => 'error', 'msg' => __('ptw.please_try_after_some_time')], 406);
                }
            }
        }
        $permit_id =  $this->safetypermit->select('permit_id')->where('status', 1)->where('trash', 'NO')->get();
        // $status = $this->status->get();
        // $location = $this->location->select('id', 'location_type_name')->where('status', 1)->where('trash', 'NO')->get();
        $data = array(
            'permit_id' => $permit_id,
            // 'status' => $status,
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

                $safetpermit =   $this->safetypermit->store();
                $WorkmanInvolved =   $this->workmaninvolved->store($safetpermit->id);

                Session::flash('success', __('Your data has been creted successfully'));

                return redirect(admin_url('safetypermit/list'));
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', __('common.message_error'));
            }
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error',  __('common.message_error'));
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
                $data = array(
                    'safetypermit' => $safetypermit,
                    'stateIsolationLoto' => $stateIsolationLoto,
                    'confined_space_entry' => $confined_space_entry,
                    'workmaninvolved' => $workmaninvolved,

                );
            }
            return view('permit.safetypermit.view', $data);
        } catch (Exception $ex) {

            dd($ex);
            report($ex);
        }
    }

    public function edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $locationlist = $this->location->where('status', 1)->get();
            $hot_cold = $this->safetypermit->find($id);
            $permit_type =  $hot_cold->permit_type;
            $location =  $hot_cold->location;
            // $sub_permit =  $hot_cold->sub_permit;
            $sub_permit = explode(',', $hot_cold->sub_permit);


            $checklist = $this->checklist->find(1);

            $checklist_details = $this->checklistdetails->getchecklist(1);
            $permitchecklist = $this->permitchecklist->where('ptw_hot_cold_id', $id)->get()->KeyBy('checklist_name_id');
            $file = $this->file->where('ptw_hot_cold_id', $id)->first();



            $data = array(
                'locationlist' => $locationlist,
                'hot_cold' => $hot_cold,
                'permit_type' => $permit_type,
                'location' => $location,
                'sub_permit' => $sub_permit,
                'checklist' => $checklist,
                'checklist_details' => $checklist_details,
                'permitchecklist' => $permitchecklist->toArray(),
                'file' => $file,
            );
            return view('ptw.main.hotptw.edit', $data);
        } catch (Exception $error) {
            dd($error);
            report($error->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $rules = [
                'permit_id' => 'required',
                'permit_type' => 'required',
                'location' => 'required',
                'sub_permit' => 'required',
                'desc_work' => 'required',
                'unit' => 'required',
                'workers' => 'required',
                'types_hotwork' => 'required',
                'risk_assess_no' => 'required',
                'flames_spark' => 'required',
                'work_from_time' => 'required',
                'work_to_time' => 'required',
                'permit_checklist1' => 'required',
                'cil_cont_name' => 'required',
                'cil_cont_upload' => 'required',
                'company_name' => 'required',
                'cil_cont_time' => 'required',

            ];
            $messages = [
                'permit_id.required' => __('ptw.permit_id_is_required'),
                'permit_type.required' => __('ptw.permit_type_is_required'),
                'location.required' => __('ptw.location_is_required'),
                'sub_permit.required' => __('ptw.sub_permit_is_required'),
                'desc_work.required' => __('ptw.brief_description_of_work_is_required'),
                'unit.required' => __('ptw.unit_location_is_required'),
                'workers.required' => __('ptw.no_of_workers_is_required'),
                'types_hotwork.required' => __('ptw.types_of_hot_work_is_required'),
                'risk_assess_no.required' => __('ptw.risk_assessment_form_no_is_required'),
                'flames_spark.required' => __('ptw.flames_sparks_producing_equipment_is_required'),
                'work_from_time.required' => __('ptw.period_of_work_from_time_is_required'),
                'work_to_time.required' => __('ptw.period_of_work_to_time_is_required'),
                'permit_checklist1.required' => __('ptw.permit_checklist_required'),
                'cil_cont_name.required' => __('ptw.cil_contractor_name_is_required'),
                'cil_cont_upload.required' => __('ptw.signature_is_required'),
                'company_name.required' => __('ptw.contractor_company_name_is_required'),
                'cil_cont_time.required' => __('ptw.time_is_required'),

            ];

            $permit_status = 2;
            $hotpermitdetails = $this->safetypermit->find($id);
            $hotpermit = $this->safetypermit->updates($id, $permit_status);
            $this->permitchecklist->updates($id);
            $this->file->updates($hotpermit);



            $mailsubject = 'Hot Work Permit Submitted';

            $user_role = ['ROLE_HOD', 'ROLE_ENGINEER'];

            $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
            $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();


            if (count($users) > 0) {

                foreach ($users as $user) {

                    $email_id = $user->email;

                    if ($email_id != '' || $email_id != null) {
                        $ptw = new Ptw();
                        $permitdetails =  $this->safetypermit->selectOne($id);
                        $permitrray  = $permitdetails->toArray();

                        $permitrray['name'] = $user->name;
                        $permitrray['email_id'] =  $email_id;
                        $permitrray['mail_subject'] = $mailsubject;

                        Mail::to($permitrray['email_id'])->queue(new HotWorkEmail($permitrray));
                    }
                }
            }

            /**
             * Send Web notification
             */

            $notificationData = array(
                'notification_type' => 1,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => 'Hot Work Permit ' . $request->permit_id . ' submitted by ' . getUsername(Auth::id()),
                    'icon' => 'public/assets/images/icon/permit_to_work.png',
                    'id' => $request->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('ptw/hotwork_permit/view/' . encryptId($request->id)),
                'assigned_user' => array_to_string($userids),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);


            Session::flash('success', __('ptw.hotwork_permit_updated_successfully'));
            return redirect(admin_url('safetypermit/list'));
        } catch (Exception $ex) {
            Session::flash('error', __('ptw.something_went_wrong_try_again'));
            return redirect(admin_url('safetypermit/list'));
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
                $data = array(
                    'safetypermit' => $safetypermit,
                    'stateIsolationLoto' => $stateIsolationLoto,
                    'confined_space_entry' => $confined_space_entry,
                    'workmaninvolved' => $workmaninvolved,
                    'getEhSverification' => $getEhSverification,
                    'getEhsapproval' => $getEhsapproval,

                );
            }
            return view('permit.safetypermit.approvereject', $data);
        } catch (Exception $ex) {

            dd($ex);
            report($ex);
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
            $this->safetypermit->verifiedby($approve->created_by, $id);
            $this->safetypermit->permitstatus($permit_status, $id);

            $mailsubject = 'EHS Verified';
            $user_role = ROLE_EHS_OFFICER;

            $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
            $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();

            if (count($users) > 0) {

                foreach ($users as $user) {

                    $email_id = $user->email;

                    if ($email_id != '' || $email_id != null) {
                        $safetypermit = new SafetyPermit();
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
                'notification_type' => 1,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => 'Safety Permit ' . $safetypermit->permit_id . ' verified by ' . getUsername($approve->created_by),
                    'icon' => 'public/assets/images/icon/permit_to_work.png',
                    'id' => $safetypermit->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safetypermit/view/' . encryptId($safetypermit->id)),
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

            dd($ex);
            report($ex);


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
            } elseif ($request->has('decline')) {
                $permit_status = STATUS_EHS_DECLINE;
            } elseif ($request->has('reassign')) {
                $permit_status = STATUS_EHS_REASSIGN;
            } elseif ($request->has('forward')) {
                $permit_status = STATUS_PLANT_HEAD_PENDING;
            }

            $approve =   $this->approvereject->ehsapproval($permit_status);
            if ($request->has('reassign')) {
                $this->safetypermit->reassignto($approve->created_by, $id);
            }
            $this->safetypermit->permitstatus($permit_status, $id);


            if ($permit_status = STATUS_EHS_HOLD) {
                $mailsubject = 'EHS Holded the permit';
                $user_role = ROLE_EHS_OFFICER;

                $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
                $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();
            } elseif ($permit_status = STATUS_EHS_DECLINE) {
                $mailsubject = 'EHS declined the permit';
                $notifywhere = array(
                    'id' => $safetypermit->created_by,
                );
                $userids = User::where($notifywhere)->pluck('id')->toArray();
                $users = User::where($notifywhere)->get();
            } elseif ($permit_status = STATUS_EHS_REASSIGN) {
                $mailsubject = 'EHS Verified';
                $notifywhere = array(
                    'id' => $request->reassign_to,
                );
                $userids = User::where($notifywhere)->pluck('id')->toArray();
                $users = User::where($notifywhere)->get();
            } elseif ($permit_status = STATUS_PLANT_HEAD_PENDING) {
                $mailsubject = 'EHS Approved';
                $user_role = ROLE_PLANT_HEAD;

                $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
                $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();
            }




            if (count($users) > 0) {

                foreach ($users as $user) {

                    $email_id = $user->email;

                    if ($email_id != '' || $email_id != null) {
                        $safetypermit = new SafetyPermit();
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
                'notification_type' => 1,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => 'Safety Permit ' . $safetypermit->permit_id . $mailsubject . getUsername($approve->created_by),
                    'icon' => 'public/assets/images/icon/permit_to_work.png',
                    'id' => $safetypermit->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safetypermit/view/' . encryptId($safetypermit->id)),
                'assigned_user' => array_to_string($userids),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $insert_array = array(
                'permit_type' => 2,
                'permit_id' => $id,
                'from_status' => 2,
                'to_status' => $permit_status,
                'is_reject' => null,
                'remarks' => $request->ehs_aaproval_remarks,
                'approved_by' => Auth::id(),
            );
            $this->statuslog->create($insert_array);

            return redirect(admin_url('safetypermit/list'));
        } catch (Exception $ex) {

            dd($ex);
            report($ex);


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
            }

            $approve =   $this->approvereject->plantheadApproval($permit_status);
            $this->safetypermit->approved_by($approve->created_by, $id);
            $this->safetypermit->permitstatus($permit_status, $id);

            $mailsubject = 'Plant Head Approved';
            $Assignedusers = User::whereRaw('FIND_IN_SET(' . ROLE_EHS_HEAD . ', role)')
            ->orWhere('id', $safetypermit->created_by)
            ->select('name', 'email')
            ->get()
            ->unique('email');

            if ($Assignedusers != null) {

                foreach ($Assignedusers as $user) {
                    $email_id = $user->email;

                    if ($email_id != '' || $email_id != null) {
                        $safetypermit = new SafetyPermit();
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

            $UserId =  User::whereRaw('FIND_IN_SET(' . ROLE_EHS_HEAD . ', role)')->pluck('id')->toArray();
            $assigned_user = array_merge([$safetypermit->created_by], $UserId);
            $assigned_user = array_unique($assigned_user);

            $notificationData = array(
                'notification_type' => 1,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => 'Safety Permit ' . $safetypermit->permit_id . ' approved by ' . getUsername($approve->created_by),
                    'icon' => 'public/assets/images/icon/permit_to_work.png',
                    'id' => $safetypermit->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safetypermit/view/' . encryptId($safetypermit->id)),
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

            dd($ex);
            report($ex);


            return redirect(admin_url('safetypermit/list'));
        }
    }
    public function delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->safetypermit->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => __('Work Permit Deleted Successfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('ptw.please_try_after_some_time')], 406);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->safetypermit->exportdata();

            $header = [
                __("common.sno"),
                __("Permit ID"),
                __("Location"),
                __("Sub permit"),
                __("common.status"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->permit_id;
                $export[] =  $data->location_type_name;
                if ($data->sub_permit == 1) {
                    $export[] = 'Confined Space Entry Permit';
                } elseif ($data->sub_permit == 2) {
                    $export[] = 'Lifting Work Permit';
                } elseif ($data->sub_permit == 3) {
                    $export[] = 'Work at Height Permit';
                }
                $export[] =  $data->status_name;
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Hot / Cold Work Permit.xlsx')
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



            $header = [
                __("common.sno"),
                __("Permit ID"),
                __("Location"),
                __("Sub permit"),
                __("common.status"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Hot / Cold Work Permit Details",
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

            $view = view('ptw.main.hotptw.generalpdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Hot Work Permit Details.pdf";
            $mpdf->Output($filename, 'I');
        } catch (Exception $ex) {

            dd($ex);
            report($ex);
        }
    }

    public function ExportViewPdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $hot_permit = $this->safetypermit->selectOne($id);
                $holidays = $this->holidays->get();

                $currentdate = $hot_permit->created_at;
                $date = Carbon::parse($currentdate);
                $dayOfWeek = $date->format('l');
                $created_at = Displaydateformat($hot_permit->created_at);

                $holidays = $this->holidays->where('status', 1)->get();

                $holidaysDates = [];
                foreach ($holidays as $holiday) {
                    $holidaysDates[] = Displaydateformat($holiday->public_holidays);
                }


                $checklist = $this->checklist->find(3);

                $checklist_details = $this->checklistdetails->getchecklist(2);

                $getchecklistdetails = $this->permitchecklist->getchecklistdetails($hot_permit->id, 3);
                $getEngineerapproval = $this->approvereject->getEngineerapproval($id);
                $getehsapproval = $this->approvereject->getehsapproval($id);
                $getworkcompletionapproval = $this->approvereject->getworkcompletionapproval($id);
                $getClosure = $this->approvereject->getClosure($id);
                $recordname = $this->recordname->getgasrecordname($id);
                $record = $this->record->getgasrecorddetails($id);
                $getapprovalWork = $this->approvalstart->getapprovalWork($id);
                $getIsolation = $this->isolation->getIsolation($id);
                $getIsolation1 = $this->isolation->getIsolation1($id);
                $getIsolation2 = $this->isolation->getIsolation2($id);
                $getIsolation3 = $this->isolation->getIsolation3($id);
                $getIsolation4 = $this->isolation->getIsolation4($id);
                if ($getapprovalWork !== null) {
                    $approvalData = json_decode($getapprovalWork->approval, true);
                } else {

                    $approvalData = [
                        'checks' => [],
                        'lel' => '',
                        'hydrogen' => '',
                        'oxygen' => '',
                        'hours' => '',
                        'declaration' => '',
                        'special_ppe' => '',
                        'others' => '',
                        'consideration' => '',
                    ];
                }
                $getapprovalStart = $this->approvereject->getapprovalStart($id);
                $getElectrical = $this->electrical->getElectrical($id);

                $getpermitextension = $this->extension->getpermitextension($id);
                $getPermitExtensionsupervisor = $this->approvereject->getPermitExtensionsupervisor($id);
                $getPermitExtensionsuperintendednt = $this->approvereject->getPermitExtensionsuperintendednt($id);
                $data = array(
                    'hot_permit' => $hot_permit,
                    'checklist' => $checklist,
                    'checklist_details' => $checklist_details,
                    'getchecklistdetails' => $getchecklistdetails,
                    'getEngineerapproval' => $getEngineerapproval,
                    'recordname' => $recordname,
                    'record' => $record,
                    'getapprovalStart' => $getapprovalStart,
                    'approvalData' => $approvalData,
                    'getehsapproval' => $getehsapproval,
                    'getworkcompletionapproval' => $getworkcompletionapproval,
                    'getElectrical' => $getElectrical,
                    'getClosure' => $getClosure,
                    'getIsolation' => $getIsolation,
                    'getIsolation1' => $getIsolation1,
                    'getIsolation2' => $getIsolation2,
                    'getIsolation3' => $getIsolation3,
                    'getIsolation4' => $getIsolation4,
                    'dayOfWeek' => $dayOfWeek,
                    'holidaysDates' => $holidaysDates,
                    'created_at' => $created_at,
                    'getpermitextension' => $getpermitextension,
                    'getPermitExtensionsupervisor' => $getPermitExtensionsupervisor,
                    'getPermitExtensionsuperintendednt' => $getPermitExtensionsuperintendednt,
                );
            }

            $property = [
                'tempDir' => 'public/pdf/temp/',
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,

            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $view = view('ptw.main.hotptw.pdf', $data);
            $html = $view->render();


            $mpdf->WriteHTML($html);

            $filename = "List.pdf";
            $mpdf->Output($filename, 'I');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
        }
    }

    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('uauc_notification');

        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        return Response::download($filePath, $customFileName);
    }

    public function getprotectivechecklist($workId)
    {
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


    public function employeeid(Request $request)
    {
        $name = $request->input('search');

        $employee_code = Employee::where('emp_id', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->limit(10)
            ->get();
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
}
