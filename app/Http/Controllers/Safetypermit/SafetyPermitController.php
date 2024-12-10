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
use App\Mail\PTW\HotWorkEmail;

use App\Models\Master\Unit;
use App\Models\Master\TypeofWork;
use App\Models\Master\TypeofWorkChecklist;

class SafetyPermitController extends Controller
{
    private $safetypermit;
    private $unit;
    private $typeofwork;
    private $typeofworkchecklist;

    public function __construct()
    {
        $this->safetypermit = new SafetyPermit();
        $this->unit = new Unit();
        $this->typeofwork = new TypeofWork();
        $this->typeofworkchecklist = new TypeofWorkChecklist();
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

                        ->addColumn('subpermit_type', function ($row) {
                            $sub_permit_types = [
                                1 => 'Confined Space Entry Permit',
                                2 => 'Lifting Work Permit',
                                3 => 'Work at Height Permit'
                            ];

                            $sub_permits = explode(',', $row->sub_permit);
                            $permit_names = array_map(function ($permit) use ($sub_permit_types) {
                                return $sub_permit_types[$permit] ?? '-';
                            }, $sub_permits);

                            return implode(', ', $permit_names);
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
                            $btn = '<a href="' . admin_url('ptw/hotwork_permit/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';

                       
                            $btn .= '<a href="' . admin_url('ptw/hotwork_permit/view/pdf/' . encryptId($row->id)) . '" data-toggle="tooltip" data-placement="top" class="pdficon" title="Pdf"><i class="fas fa-file-pdf" aria-hidden="true"></i> ';


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
           
            $getprotectiveequipment = $this->typeofworkchecklist->getprotectiveequipment(1, 'type1');
            $getequipmentinvolved = $this->typeofworkchecklist->getequipmentinvolved(1, 'type2');
            $getprecaution = $this->typeofworkchecklist->getprecaution(1, 'type3');
            $getchecklist = $this->typeofworkchecklist->getchecklist(1, 'type4');
            $getinstruction = $this->typeofworkchecklist->getinstruction(1, 'type5');

            $data = array(
                'unitList' => $unitList,
                'typeofwork' => $typeofwork,
                'getprotectiveequipment' => $getprotectiveequipment,
                'getequipmentinvolved' => $getequipmentinvolved,
                'getprecaution' => $getprecaution,
                'typeofwork' => $typeofwork,
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


            try {


                $permit_status = null;
                if ($request->has('submit')) {

                    $permit_status = 2;
                } else {
                    $permit_status = 1;
                }
                $ptw_hot_cold = $this->safetypermit->store($permit_status);
                $id = $ptw_hot_cold->id;
                $this->permitchecklist->store($id);
                $this->file->store($ptw_hot_cold);


                $subPermit = '';
                $subpermitDetails = $ptw_hot_cold->sub_permit;

                if ($subpermitDetails != null && $subpermitDetails != '') {
                    $subpermitArray = $this->safetypermitsubpermit->store($ptw_hot_cold->id, string_to_array($subpermitDetails));
                    $subPermit = $subpermitArray->sub_permit_id;
                }

                if ($subPermit == '') {
                    sendNotificationGeneral($ptw_hot_cold);
                }
                if ($subPermit != '') {
                    switch ($subPermit) {
                        case 1:
                            $redirectLink = 'ptw/confined_permit/add/';
                            break;
                        case 2:
                            $redirectLink = 'ptw/lift_permit/add/';
                            break;
                        case 3:
                            $redirectLink = 'ptw/wah/add/';
                            break;
                        default:
                            $redirectLink = 'ptw/hotwork_permit/list/';
                            Session::flash('success', __('ptw.hot_work_permit_submitted_successfully'));
                            break;
                    }
                    $ptwId = $ptw_hot_cold->id;
                    $redirectLink = $redirectLink . encryptId($ptwId);
                } else {
                    $redirectLink = 'ptw/hotwork_permit/list/';
                    Session::flash('success', __('ptw.hot_work_permit_submitted_successfully'));
                }

                return redirect(admin_url($redirectLink));
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', __('common.message_error'));
            }
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ptw/hotwork_permit/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $hot_permit = $this->safetypermit->selectOne($id);
                $checklist = $this->checklist->find(1);

                $status_log = $this->statuslog->selectOne($id);

                $sub_permit =  explode(',', $hot_permit->sub_permit);
                $currentdate = $hot_permit->created_at;
                $created_at = $hot_permit->created_at;
                $date = Carbon::parse($currentdate);
                $dayOfWeek = $date->format('l');

                $holidays = $this->holidays->where('status', 1)->get();

                $holidaysDates = [];
                foreach ($holidays as $holiday) {
                    $holidaysDates[] = Displaydateformat($holiday->public_holidays);
                }

                $checklist_details = $this->checklistdetails->getchecklist(1);
                $permit_type =  $hot_permit->permit_type;
                $locationlist = $this->location->where('status', 1)->get();
                $location =  $hot_permit->location;
                $permitchecklist = $this->permitchecklist->where('ptw_hot_cold_id', $id)->get()->KeyBy('checklist_name_id');

                $getchecklistdetails = $this->permitchecklist->getchecklistdetails($hot_permit->id, 3);
                $getEngineerapproval = $this->approvereject->getEngineerapproval($id);
                $getehsapproval = $this->approvereject->getehsapproval($id);
                $getworkcompletionapproval = $this->approvereject->getworkcompletionapproval($id);
                $getClosure = $this->approvereject->getClosure($id);
                $recordname = $this->recordname->getgasrecordname($id);
                $record = $this->record->getgasrecorddetails($id);
                $getIsolation = $this->isolation->getIsolation($id);
                $getIsolation1 = $this->isolation->getIsolation1($id);
                $getIsolation2 = $this->isolation->getIsolation2($id);
                $getIsolation3 = $this->isolation->getIsolation3($id);
                $getIsolation4 = $this->isolation->getIsolation4($id);
                $getapprovalWork = $this->approvalstart->getapprovalWork($id);
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

                $subpermitDetails =  $this->safetypermitsubpermit->getPermit($id);

                $getpermitextension = $this->extension->getpermitextension($id);
                $getPermitExtensionsupervisor = $this->approvereject->getPermitExtensionsupervisor($id);
                $getPermitExtensionsuperintendednt = $this->approvereject->getPermitExtensionsuperintendednt($id);
                $data = array(
                    'hot_permit' => $hot_permit,
                    'checklist' => $checklist,
                    'checklist_details' => $checklist_details,
                    'permit_type' => $permit_type,
                    'locationlist' => $locationlist,
                    'location' => $location,
                    'sub_permit' => $sub_permit,
                    'permitchecklist' => $permitchecklist,
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
                    'subpermitDetails' => $subpermitDetails,
                    'dayOfWeek' => $dayOfWeek,
                    'holidaysDates' => $holidaysDates,
                    'created_at' => $created_at,
                    'getpermitextension' => $getpermitextension,
                    'getPermitExtensionsupervisor' => $getPermitExtensionsupervisor,
                    'getPermitExtensionsuperintendednt' => $getPermitExtensionsuperintendednt,
                    'status_log' => $status_log,

                );
            }
            return view('ptw.main.hotptw.view', $data);
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
            return redirect(admin_url('ptw/hotwork_permit/list'));
        } catch (Exception $ex) {
            Session::flash('error', __('ptw.something_went_wrong_try_again'));
            return redirect(admin_url('ptw/hotwork_permit/list'));
        }
    }

    public function approvereject(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $hot_permit = $this->safetypermit->selectOne($id);
            $sub_permit =  explode(',', $hot_permit->sub_permit);
            $currentdate = $hot_permit->created_at;
            $date = Carbon::parse($currentdate);
            $dayOfWeek = $date->format('l');

            $locationlist = $this->location->where('status', 1)->get();

            $holidays = $this->holidays->where('status', 1)->get();

            $holidaysDates = [];
            foreach ($holidays as $holiday) {
                $holidaysDates[] = Displaydateformat($holiday->public_holidays);
            }


            $created_at = Displaydateformat($hot_permit->created_at);

            $checklist = $this->checklist->find(1);

            $checklist_details = $this->checklistdetails->getchecklist(1);
            $permit_type =  $hot_permit->permit_type;
            $location =  $hot_permit->location;

            // $sub_permit =  $hot_permit->sub_permit;
            $permitchecklist = $this->permitchecklist->where('ptw_hot_cold_id', $id)->get()->KeyBy('checklist_name_id');

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
                'locationlist' => $locationlist,
                'checklist' => $checklist,
                'checklist_details' => $checklist_details,
                'permit_type' => $permit_type,
                'location' => $location,
                'sub_permit' => $sub_permit,
                'permitchecklist' => $permitchecklist,
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
                'holidays' => $holidays,
                'holidaysDates' => $holidaysDates,
                'created_at' => $created_at,
                'getpermitextension' => $getpermitextension,
                'getPermitExtensionsupervisor' => $getPermitExtensionsupervisor,
                'getPermitExtensionsuperintendednt' => $getPermitExtensionsuperintendednt,
                'sub_permit' => $sub_permit,
            );

            return view('ptw.main.hotptw.approvereject', $data);
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
        }
    }
    public function approvereject1(Request $request)
    {

        try {

            $id = $request->permit_id;
            $ptw = $this->safetypermit->find($id);

            if ($request->has('approve')) {
                $is_reject = 0;
                $ptw_status = 3;
            } else if ($request->has('reject')) {
                $is_reject = 1;
                $ptw_status = 4;
            }

            $approve =   $this->approvereject->store1($ptw_status);
            $this->approverejectfile->store1($approve, $ptw_status);

            $this->safetypermit->permitstatus($ptw_status, $id);

            if ($is_reject == 0) {
                $mailsubject = 'Engineer Approved';
                $user_role = ROLE_SHIFT_SUPERINTENDENT;


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
                        'message' => 'Hot Work Permit ' . $ptw->permit_id . ' Approved by ' . getUsername($approve->created_by),
                        'icon' => 'public/assets/images/icon/permit_to_work.png',
                        'id' => $ptw->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('ptw/hotwork_permit/view/' . encryptId($ptw->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $insert_array = array(
                    'permit_type' => 1,
                    'ptw_id' => $id,
                    'sub_permit_id' => $ptw->sub_permit,
                    'from_status' => 2,
                    'to_status' => $ptw_status,
                    'is_reject' => $is_reject,
                    'remarks' => $request->manager_remarks,
                    'approved_by' => Auth::id(),
                );
                $this->statuslog->create($insert_array);
            } else {
                $mailsubject = 'Engineer Rejected';

                $notifywhere = array(
                    'id' => $ptw->created_by,
                );

                $userids = User::where($notifywhere)->pluck('id')->toArray();
                $users = User::where($notifywhere)->get();

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
                        'message' => 'Hot Work Permit ' . $ptw->permit_id . ' Rejected by ' . getUsername($approve->created_by) . 'Please update and resubmit.',
                        'icon' => 'public/assets/images/icon/permit_to_work.png',
                        'id' => $ptw->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('ptw/hotwork_permit/view/' . encryptId($ptw->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $insert_array = array(
                    'permit_type' => 1,
                    'ptw_id' => $id,
                    'sub_permit_id' => $ptw->sub_permit,
                    'from_status' => 2,
                    'to_status' => $ptw_status,
                    'is_reject' => $is_reject,
                    'remarks' => $request->manager_remarks,
                    'approved_by' => Auth::id(),
                );
                $this->statuslog->create($insert_array);
            }

            return redirect(admin_url('ptw/hotwork_permit/list'));
        } catch (Exception $ex) {
            report($ex);


            return redirect(admin_url('ptw/hotwork_permit/list'));
        }
    }

    public function gastestRecord(Request $request)
    {
        try {
            $id = $request->permit_id;
            $ptw = $this->safetypermit->find($id);
            $ptw_status = 5;
            $recordname = $this->recordname->store();
            $recordnameId = $recordname->id;
            $this->record->store($recordnameId);
            if ($request->has('signaturerecord')) {
                $signaturerecords = $request->file('signaturerecord');
                foreach ($signaturerecords as $key => $file) {
                    $this->gastestfile->store($file, $ptw);
                }
            }
            $mailsubject = 'Gas Test Record Submitted';
            $user_role = ROLE_SHIFT_SUPERINTENDENT;


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
                    'message' => 'Hot Work Permit ' . $ptw->permit_id . ' Approved by ' . getUsername($recordname->gas_tested_by),
                    'icon' => 'public/assets/images/icon/permit_to_work.png',
                    'id' => $ptw->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('ptw/hotwork_permit/view/' . encryptId($ptw->id)),
                'assigned_user' => array_to_string($userids),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);
            $insert_array = array(
                'permit_type' => 1,
                'ptw_id' => $id,
                'sub_permit_id' => $ptw->sub_permit,
                'from_status' => 3,
                'to_status' => $ptw_status,
                'is_reject' => 0,
                'remarks' => null,
                'approved_by' => Auth::id(),
            );
            $this->statuslog->create($insert_array);
            $this->safetypermit->permitstatus($ptw_status, $id);
            return redirect(admin_url('ptw/hotwork_permit/list'));
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            return redirect(admin_url('ptw/hotwork_permit/list'));
        }
    }

    public function approvalstart(Request $request)
    {

        try {
            $id = $request->permit_id;
            $ptw = $this->safetypermit->find($id);
            $holidays = $this->holidays->where('status', 1)->get();
            $currentdate = date('d', strtotime($ptw->created_at));

            $currentdate = $ptw->created_at;
            $date = Carbon::parse($currentdate);
            $dayOfWeek = $date->format('l');
            $holidaysDates = [];
            foreach ($holidays as $holiday) {
                $holidaysDates[] = Displaydateformat($holiday->public_holidays);
            }

            $created_at = Displaydateformat($ptw->created_at);

            // dd($holidaysDates, $created_at);
            if ($request->has('approve')) {
                $is_reject = 0;

                if (($dayOfWeek == "Saturday" || $dayOfWeek == "Sunday") || (in_array($created_at, $holidaysDates))) {
                    // if (($dayOfWeek == "Saturday" || $dayOfWeek == "Sunday")) {

                    $ptw_status = 7;
                } else {
                    $ptw_status = 6;
                }
            } else if ($request->has('reject')) {
                $is_reject = 1;
                $ptw_status = 13;
            }
            $approvalstart = $this->approvalstart->HotpermitApproval();
            $isolation = $this->isolation->storeisolation($ptw_status);
            $isolationfile = $this->isolationfile->store1($isolation);
            $isolationfile = $this->isolationfile->store2($isolation);
            $isolationfile = $this->isolationfile->store3($isolation);
            $isolationfile = $this->isolationfile->store4($isolation);
            $approve =   $this->approvereject->store2($ptw_status);
            $this->approverejectfile->store2($approve, $ptw_status);
            $this->safetypermit->permitstatus($ptw_status, $id);
            if ($is_reject == 0) {
                $mailsubject = 'Start To Work Approved';

                if ($dayOfWeek == "Saturday" || $dayOfWeek == "Sunday" || (in_array($created_at, $holidaysDates))) {
                    $notifywhere = array(
                        'id' => $ptw->created_by,
                    );

                    $userids = User::where($notifywhere)->pluck('id')->toArray();
                    $users = User::where($notifywhere)->get();
                } else {
                    $user_role = ROLE_EHS_PERSONNEL;
                    $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
                    $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();
                }



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
                        'message' => 'Hot Work Permit ' . $ptw->permit_id . ' Approved by ' . getUsername($approve->created_by),
                        'icon' => 'public/assets/images/icon/permit_to_work.png',
                        'id' => $ptw->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('ptw/hotwork_permit/view/' . encryptId($ptw->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $insert_array = array(
                    'permit_type' => 1,
                    'ptw_id' => $id,
                    'sub_permit_id' => $ptw->sub_permit,
                    'from_status' => 5,
                    'to_status' => $ptw_status,
                    'is_reject' => $is_reject,
                    'remarks' => $request->workapproval_remarks,
                    'approved_by' => Auth::id(),
                );
                $this->statuslog->create($insert_array);
            } else {
                $mailsubject = 'Start To Work Rejected';
                $notifywhere = array(
                    'id' => $ptw->created_by,
                );

                $userids = User::where($notifywhere)->pluck('id')->toArray();
                $users = User::where($notifywhere)->get();


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
                        'message' => 'Hot Work Permit ' . $ptw->permit_id . ' Rejected by ' . getUsername($approve->created_by) . 'Please update and resubmit.',
                        'icon' => 'public/assets/images/icon/permit_to_work.png',
                        'id' => $ptw->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('ptw/hotwork_permit/view/' . encryptId($ptw->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $insert_array = array(
                    'permit_type' => 1,
                    'ptw_id' => $id,
                    'sub_permit_id' => $ptw->sub_permit,
                    'from_status' => 5,
                    'to_status' => $ptw_status,
                    'is_reject' => $is_reject,
                    'remarks' => $request->workapproval_remarks,
                    'approved_by' => Auth::id(),
                );
                $this->statuslog->create($insert_array);
            }
            return redirect(admin_url('ptw/hotwork_permit/list'));
        } catch (Exception $ex) {

            dd($ex);
            report($ex);


            return redirect(admin_url('ptw/hotwork_permit/list'));
        }
    }
    public function approvereject2(Request $request)
    {

        try {

            $id = $request->permit_id;
            $ptw = $this->safetypermit->find($id);

            if ($request->has('approve')) {
                $is_reject = 0;
                $ptw_status = 7;
            }
            $approve =   $this->approvereject->store3($ptw_status);
            $this->approverejectfile->store3($approve, $ptw_status);

            $this->safetypermit->permitstatus($ptw_status, $id);


            $mailsubject = 'Engineer Approved';

            $Assignedusers = User::whereRaw('FIND_IN_SET(' . ROLE_SHIFT_SUPERINTENDENT . ', role)')
                ->orWhere('id', $ptw->created_by)
                ->select('name', 'email')
                ->get()
                ->unique('email');

            if (count($Assignedusers) > 0) {

                foreach ($Assignedusers as $user) {

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
            // $CreaterUserId = Auth::id();
            $UserId =  User::whereRaw('FIND_IN_SET(' . ROLE_SHIFT_SUPERINTENDENT . ', role)')->pluck('id')->toArray();
            $assigned_user = array_merge([$ptw->created_by], $UserId);
            $assigned_user = array_unique($assigned_user);

            $notificationData = array(
                'notification_type' => 1,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => 'Hot Work Permit ' . $ptw->permit_id . ' Approved by ' . getUsername($approve->created_by),
                    'icon' => 'public/assets/images/icon/permit_to_work.png',
                    'id' => $ptw->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('ptw/hotwork_permit/view/' . encryptId($ptw->id)),
                'assigned_user' => array_to_string($assigned_user),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $insert_array = array(
                'permit_type' => 1,
                'ptw_id' => $id,
                'sub_permit_id' => $ptw->sub_permit,
                'from_status' => 6,
                'to_status' => $ptw_status,
                'is_reject' => $is_reject,
                'remarks' => $request->work_aprovalremarks,
                'approved_by' => Auth::id(),
            );
            $this->statuslog->create($insert_array);


            return redirect(admin_url('ptw/hotwork_permit/list'));
        } catch (Exception $ex) {

            dd($ex);
            report($ex);


            return redirect(admin_url('ptw/hotwork_permit/list'));
        }
    }
    public function approvereject3(Request $request)
    {

        try {

            $id = $request->permit_id;
            $ptw = $this->safetypermit->find($id);

            if ($request->has('approve')) {
                $is_reject = 0;
                $ptw_status = 8;
            }
            $approve =   $this->approvereject->store4($ptw_status);
            $this->approverejectfile->store4($approve, $ptw_status);

            $this->safetypermit->permitstatus($ptw_status, $id);


            $mailsubject = 'Work Completion / Suspension Completed';
            $user_role = ROLE_SHIFT_SUPERINTENDENT;


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
                    'message' => 'Hot Work Permit ' . $ptw->permit_id . ' Approved by ' . getUsername($approve->created_by),
                    'icon' => 'public/assets/images/icon/permit_to_work.png',
                    'id' => $ptw->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('ptw/hotwork_permit/view/' . encryptId($ptw->id)),
                'assigned_user' => array_to_string($userids),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $insert_array = array(
                'permit_type' => 1,
                'ptw_id' => $id,
                'sub_permit_id' => $ptw->sub_permit,
                'from_status' => 7,
                'to_status' => $ptw_status,
                'is_reject' => $is_reject,
                'remarks' => $request->cont_remarks,
                'approved_by' => Auth::id(),
            );
            $this->statuslog->create($insert_array);


            return redirect(admin_url('ptw/hotwork_permit/list'));
        } catch (Exception $ex) {

            dd($ex);
            report($ex);


            return redirect(admin_url('ptw/hotwork_permit/list'));
        }
    }


    public function approvereject4(Request $request)
    {

        try {

            $id = $request->permit_id;
            $ptw = $this->safetypermit->find($id);

            if ($request->has('approve')) {
                $is_reject = 0;
                $ptw_status = 9;
            }
            $approve =   $this->approvereject->store5($ptw_status);
            $this->approverejectfile->store5($approve, $ptw_status);
            $electrical =    $this->electrical->store($ptw_status);
            $this->electricalupload->store($electrical, $ptw_status);

            $this->safetypermit->permitstatus($ptw_status, $id);


            $mailsubject = 'Permit Closure Completed';
            $Assignedusers = User::whereRaw('FIND_IN_SET(' . ROLE_SHIFT_SUPERINTENDENT . ', role)')
                ->orWhere('id', $ptw->created_by)
                ->select('name', 'email')
                ->get()
                ->unique('email');

            if (count($Assignedusers) > 0) {

                foreach ($Assignedusers as $user) {

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

            $UserId =  User::whereRaw('FIND_IN_SET(' . ROLE_SHIFT_SUPERINTENDENT . ', role)')->pluck('id')->toArray();
            $assigned_user = array_merge([$ptw->created_by], $UserId);
            $assigned_user = array_unique($assigned_user);

            $notificationData = array(
                'notification_type' => 1,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => 'Hot Work Permit ' . $ptw->permit_id . ' Approved by ' . getUsername($approve->created_by),
                    'icon' => 'public/assets/images/icon/permit_to_work.png',
                    'id' => $ptw->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('ptw/hotwork_permit/view/' . encryptId($ptw->id)),
                'assigned_user' => array_to_string($assigned_user),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $insert_array = array(
                'permit_type' => 1,
                'ptw_id' => $id,
                'sub_permit_id' => $ptw->sub_permit,
                'from_status' => 8,
                'to_status' => $ptw_status,
                'is_reject' => $is_reject,
                'remarks' => $request->superintendent_remarks,
                'approved_by' => Auth::id(),
            );
            $this->statuslog->create($insert_array);


            return redirect(admin_url('ptw/hotwork_permit/list'));
        } catch (Exception $ex) {

            dd($ex);
            report($ex);


            return redirect(admin_url('ptw/hotwork_permit/list'));
        }
    }

    public function permitextensionold(Request $request)
    {
        try {

            $id = $request->permit_id;
            $ptw = $this->safetypermit->find($id);
            $is_reject = 0;
            $ptw_status = 10;

            $extension =   $this->extension->store($ptw_status);

            $this->safetypermit->permitstatus($ptw_status, $id);
            $this->safetypermit->permitCompletion($id);

            if ($is_reject == 0) {
                $mailsubject = 'Permit Extended';

                $user_role = ROLE_SHIFT_SUPERINTENDENT;


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
                        'message' => 'Lifting Work Permit ' . $ptw->permit_id . ' Extended by ' . getUsername($extension->created_by),
                        'icon' => 'public/assets/images/icon/permit_to_work.png',
                        'id' => $ptw->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('ptw/hotwork_permit/view/' . encryptId($ptw->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
                $insert_array = array(
                    'permit_type' => 1,
                    'ptw_id' => $request->permit_id,
                    'sub_permit_id' => null,
                    'from_status' => 9,
                    'to_status' => $ptw_status,
                    'is_reject' => 0,
                    'remarks' => null,
                    'approved_by' => Auth::id(),
                );
                $this->statuslog->create($insert_array);
            }


            return redirect(admin_url('ptw/hotwork_permit/list'));
        } catch (Exception $ex) {
            dd($ex);
            report($ex);


            return redirect(admin_url('ptw/hotwork_permit/list'));
        }
    }

    public function permitextensionapprovalold(Request $request)
    {

        try {

            $id = $request->permit_id;
            $ptw = $this->safetypermit->find($id);

            if ($request->has('approve')) {
                $is_reject = 0;
                $ptw_status = 11;
            } else if ($request->has('reject')) {
                $is_reject = 1;
                $ptw_status = 12;
            }

            $approve =   $this->approvereject->store6($ptw_status);
            $this->approverejectfile->store6($approve, $ptw_status);

            $this->safetypermit->permitstatus($ptw_status, $id);

            if ($is_reject == 0) {
                $mailsubject = 'Superintendent Approved Permit Extension';
                $notifywhere = array(
                    'id' => $ptw->created_by,
                );


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
                        'message' => 'Hot / Cold Work Permit' . $ptw->permit_id . ' Approved by ' . getUsername($approve->created_by),
                        'icon' => 'public/assets/images/icon/permit_to_work.png',
                        'id' => $ptw->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('ptw/hotwork_permit/view/' . encryptId($ptw->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $insert_array = array(
                    'permit_type' => 1,
                    'ptw_id' => $request->permit_id,
                    'from_status' => 10,
                    'to_status' => $ptw_status,
                    'is_reject' => $is_reject,
                    'remarks' => $request->work_approval_remark,
                    'approved_by' => Auth::id(),
                );
                $this->statuslog->create($insert_array);
            } else {
                $mailsubject = 'Superintendent Rejected';
                $notifywhere = array(
                    'id' => $ptw->created_by,
                );

                $userids = User::where($notifywhere)->pluck('id')->toArray();
                $users = User::where($notifywhere)->get();


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
                        'message' => 'Hot / Cold Work Permit' . $ptw->permit_id . ' Rejected by ' . getUsername($approve->created_by) . 'Please update and resubmit.',
                        'icon' => 'public/assets/images/icon/permit_to_work.png',
                        'id' => $ptw->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('ptw/hotwork_permit/view/' . encryptId($ptw->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $insert_array = array(
                    'permit_type' => 1,
                    'ptw_id' => $request->permit_id,
                    'from_status' => 10,
                    'to_status' => $ptw_status,
                    'is_reject' => $is_reject,
                    'remarks' => $request->superintendent_remark,
                    'approved_by' => Auth::id(),
                );
                $this->statuslog->create($insert_array);
            }

            return redirect(admin_url('ptw/hotwork_permit/list'));
        } catch (Exception $ex) {
            dd($ex);
            report($ex);


            return redirect(admin_url('ptw/hotwork_permit/list'));
        }
    }

    public function permitExtension(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $hot_permit = $this->safetypermit->selectOne($id);

            $location =  $hot_permit->location;
            $locationlist = $this->location->where('status', 1)->get();

            $getpermitextension = $this->extension->getpermitextension($id);
            $getPermitExtensionsupervisor = $this->approvereject->getPermitExtensionsupervisor($id);
            $getPermitExtensionsuperintendednt = $this->approvereject->getPermitExtensionsuperintendednt($id);

            $data = array(
                'locationlist' => $locationlist,
                'location' => $location,
                'hot_permit' => $hot_permit,
                'getpermitextension' => $getpermitextension,
                'getPermitExtensionsupervisor' => $getPermitExtensionsupervisor,
                'getPermitExtensionsuperintendednt' => $getPermitExtensionsuperintendednt,
            );
            return view('ptw.main.hotptw.permitextension', $data);
        } catch (Exception $ex) {

            dd($ex);
            report($ex);
        }
    }
    public function permitExtensionCil(Request $request)
    {

        try {

            $id = $request->permit_id;
            $ptw = $this->safetypermit->find($id);

            if ($request->has('approve')) {
                $is_reject = 0;
                $ptw_status = 15;
            }
            $approve =   $this->extension->store($ptw_status, $id);
            $this->safetypermit->permitstatus($ptw_status, $id);
            $this->safetypermit->permit_extended($id);
            $this->safetypermit->permitCompletion($id, $ptw_status);
            $this->safetypermit->permit_extended_status($id, $ptw_status);

            $mailsubject = 'Permit Extended';
            $user_role = ROLE_SUPERVISOR;


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
                    'message' => 'Hot Work Permit Extension'  . $ptw->permit_id . ' Submitted by ' . getUsername($approve->created_by),
                    'icon' => 'public/assets/images/icon/permit_to_work.png',
                    'id' => $ptw->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('ptw/hotwork_permit/view/' . encryptId($ptw->id)),
                'assigned_user' => array_to_string($userids),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $insert_array = array(
                'permit_type' => 1,
                'ptw_id' => $id,
                'sub_permit_id' => $ptw->sub_permit,
                'from_status' => 9,
                'to_status' => $ptw_status,
                'is_reject' => $is_reject,
                'remarks' => null,
                'approved_by' => Auth::id(),
            );
            $this->statuslog->create($insert_array);


            return redirect(admin_url('ptw/hotwork_permit/list'));
        } catch (Exception $ex) {

            dd($ex);
            report($ex);


            return redirect(admin_url('ptw/hotwork_permit/list'));
        }
    }
    public function permitExtensionsupervisor(Request $request)
    {


        try {
            $id = $request->permit_id;
            $ptw = $this->safetypermit->find($id);

            if ($request->has('approve')) {
                $is_reject = 0;
                $ptw_status = 15;
            } else if ($request->has('reject')) {
                $is_reject = 1;
                $ptw_status = 16;
            }
            $approve =   $this->approvereject->store8($ptw_status);
            $this->approverejectfile->store8($approve, $ptw_status);
            $this->safetypermit->permitstatus($ptw_status, $id);

            // dd($approve);

            $mailsubject = 'Permit Extendion Approved By CIL Supervisor';
            $user_role = ROLE_SHIFT_SUPERINTENDENT;


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
                    'message' => 'Hot Work Permit Extension'  . $ptw->permit_id . ' Approved by ' . getUsername($approve->created_by),
                    'icon' => 'public/assets/images/icon/permit_to_work.png',
                    'id' => $ptw->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('ptw/hotwork_permit/view/' . encryptId($ptw->id)),
                'assigned_user' => array_to_string($userids),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $insert_array = array(
                'permit_type' => 1,
                'ptw_id' => $id,
                'sub_permit_id' => $ptw->sub_permit,
                'from_status' => 14,
                'to_status' => $ptw_status,
                'is_reject' => $is_reject,
                'remarks' => $request->supervisor_remarks,
                'approved_by' => Auth::id(),
            );
            $this->statuslog->create($insert_array);
            return redirect(admin_url('ptw/hotwork_permit/list'));
        } catch (Exception $ex) {

            dd($ex);
            report($ex);


            return redirect(admin_url('ptw/hotwork_permit/list'));
        }
    }

    public function permitExtensionsuperintendent(Request $request)
    {

        try {
            $id = $request->permit_id;
            $ptw = $this->safetypermit->find($id);

            if ($request->has('approve')) {
                $is_reject = 0;
                $ptw_status = 8;
            } else if ($request->has('reject')) {
                $is_reject = 1;
                $ptw_status = 17;
            }
            $approve =   $this->approvereject->store9($ptw_status);
            $this->approverejectfile->store9($approve, $ptw_status);
            $this->safetypermit->permitCompletion($id, $ptw_status);
            $this->safetypermit->permitstatus($ptw_status, $id);
            $this->safetypermit->permit_extended_status($ptw_status, $id);

            $mailsubject = 'Permit Extendion Approved By Shift Superintendent';
            $user_role = ROLE_SHIFT_SUPERINTENDENT;


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
                    'message' => 'Hot Work Permit Extension'  . $ptw->permit_id . ' Approved by ' . getUsername($approve->created_by),
                    'icon' => 'public/assets/images/icon/permit_to_work.png',
                    'id' => $ptw->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('ptw/hotwork_permit/view/' . encryptId($ptw->id)),
                'assigned_user' => array_to_string($userids),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $insert_array = array(
                'permit_type' => 1,
                'ptw_id' => $id,
                'sub_permit_id' => $ptw->sub_permit,
                'from_status' => 15,
                'to_status' => $ptw_status,
                'is_reject' => $is_reject,
                'remarks' => $request->superintendent_remarks,
                'approved_by' => Auth::id(),
            );
            $this->statuslog->create($insert_array);
            return redirect(admin_url('ptw/hotwork_permit/list'));
        } catch (Exception $ex) {

            dd($ex);
            report($ex);


            return redirect(admin_url('ptw/hotwork_permit/list'));
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

    public function list(Request $request)
    {
        $conpanyId = decryptId($request->companyId);
        $uauc_notification = $this->safetypermit->ajaxList($conpanyId);
        return response()->json($uauc_notification);
    }
}
