<?php

namespace App\Http\Controllers\Inspection\Ohc;
use App\Models\Inspection\Ohc\OccupationHealthInspection;
use App\Http\Controllers\Controller;
use App\Mail\Inspection\Safety\SafetyInspection;
use App\Models\Inspection\Master\ChecklistOptionType;
use App\Models\Inspection\Master\ChecklistSubTypeData;
use App\Models\Inspection\Master\ChecklistSubTypeDataName;
use App\Models\Inspection\Master\ChecklistType;
use App\Models\Inspection\Master\Frequency;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\Ohc\InspectionOhcStatuslog;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Models\Inspection\Ohc\WeeklyAmbulance;
use App\Models\Inspection\Ohc\WeeklyAmbulanceChecklist;
use App\Models\Master\Department;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use App\Models\UploadLog;
use App\Models\User;
use Exception;
use Illuminate\Container\Attributes\Database;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;

class OccupationHealthInspectionController extends Controller
{



        private $OhcDetails;
        private $user;
        private $occupation_inspection;
        private $frequency;
        private $upload_log;
        private $unit;
        private $shift;
        private $department;
        private $checklist_type;
        private $sub_type_data;
        private $sub_type_data_name;
        private $questionery;
        private $signature;
        private $location;
        private $inspection_ohc_status_log;
        public function __construct()
        {

            $this->upload_log = new UploadLog();
            $this->unit = new Unit();
            $this->department = new Department();
            $this->shift = new Shift();
            $this->location = new Location();
            $this->occupation_inspection = new OccupationHealthInspection();
            $this->user = new User();
            $this->frequency = new Frequency();
            $this->signature = new OhcSignature();
            $this->inspection_ohc_status_log = new InspectionOhcStatuslog();
        }
        public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {

                    $data = $this->occupation_inspection->list();

                    $datatables = Datatables::of($data['data'])
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
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('approve_status', function ($row) {
                            $text = '';
                            switch ($row->approve_status) {
                                case WAITING_FOR_EHS_OFFICER_VERIFICATION:
                                    $text = "<span class='badge bg-primary rounded' style='font-size: 1.0em;'>Waiting For EHS Officer Verification</span>";
                                    break;
                                case WAITING_FOR_CAPA_ACTION:
                                    $text = "<span class='badge bg-info rounded' style='font-size: 1.0em;'>Waiting For CAPA Action</span>";
                                    break;
                                case WAITING_FOR_CAPA_VERIFICATION:
                                    $text = "<span class='badge bg-warning rounded' style='font-size: 1.0em;'>Waiting For CAPA Verification</span>";
                                    break;
                                case WAITING_FOR_L1_VERIFICATION:
                                    $text = "<span class='badge bg-warning rounded' style='font-size: 1.0em;'>Waiting For Level-1 Manager Verification</span>";
                                    break;
                                case WAITING_FOR_L2_VERIFICATION:
                                    $text = "<span class='badge bg-warning rounded' style='font-size: 1.0em;'>Waiting For Level-2 Manager Verification</span>";
                                    break;
                                case INSPECTION_APPROVED:
                                    $text = "<span class='badge bg-success rounded' style='font-size: 1.0em;'>CLOSED</span>";
                                    break;
                                case L2_MANAGER_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>LEVEL 2 OFFICER REJECTED - WAITING FOR CAPA ACTION</span>";
                                    break;
                                case L1_MANAGER_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>LEVEL 1 OFFICER REJECTED - WAITING FOR CAPA ACTION</span>";
                                    break;
                                case EHS_OFFICER_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>EHS OFFICER REJECTED - WAITING FOR CAPA ACTION</span>";
                                    break;
                                default:
                                    $text = "<span class='badge rounded-pill text-bg-warning'>Unknown</span>";
                            }
                            return $text;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';

                            $btn .=  '<a href="' . admin_url('ohc/inspection/view/' . encryptId($row->id)) . '" class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a>';

                            if ($row->approve_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('ohc/inspection/verification/' . encryptId($row->id)) . '/ehs" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if (($row->approve_status == WAITING_FOR_CAPA_ACTION || $row->approve_status == L2_MANAGER_REJECTED || $row->approve_status == EHS_OFFICER_REJECTED || $row->approve_status == L1_MANAGER_REJECTED) && (CheckUserRole(ROLE_FIRE_ASSOCIATES) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('ohc/inspection/verification/' . encryptId($row->id)) . '/capa" class="" title="' . __('inspection.capa_action') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->approve_status == WAITING_FOR_CAPA_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('ohc/inspection/verification/' . encryptId($row->id)) . '/ehsVerify" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->approve_status == WAITING_FOR_L1_VERIFICATION && (CheckUserRole(ROLE_L1_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('ohc/inspection/verification/' . encryptId($row->id)) . '/level-one-manager" class="" title="' . __('inspection.l1_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->approve_status == WAITING_FOR_L2_VERIFICATION && (CheckUserRole(ROLE_L2_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('ohc/inspection/verification/' . encryptId($row->id)) . '/level-two-manager" class="" title="' . __('inspection.l2_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }

                            $btn .= '<a href="' . admin_url('ohc/inspection/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'approve_status'])
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

        return view('inspection.inspection_ohc.occupational_heath_inspection.list');
    }



    public function Add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $shift = $this->shift->getShiftname();
            $checklist_details = getCheckListQuestion(OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST);
            $options =  getoption(OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST);
            $getoption = string_to_array($options->type);
            $location = $this->location->getLocationname();
            $signature_upload = $this->user->getSignature();
            $data = array(
                'unit' => $unit,
                'shift' => $shift,
                'checklist_details' =>  $checklist_details,
                'location' => $location,
                'getoption' => $getoption,
                'signature_upload' => $signature_upload,

            );
            return view('inspection.inspection_ohc.occupational_heath_inspection.add', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'questionary_id' => 'required',
                'checklist_category' => 'required',
            ];
            $messages = [
                'checklist_category.required' => __('inspection.category_name'),
                'questionary_id.requred' => __('inspection.questionary'),
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            try {


                $responses = [
                    'check_item' => ($request->sub_type_id),
                    'status' => ($request->checklist_type_status),
                    'quantity' => ($request->quantity),
                    'remarks' => ($request->remarks),
                ];

                $occupation_inspection = $this->occupation_inspection->store($responses);
                $data = [
                    'type' => OHC_TYPE_OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST,
                    'from_status' => 0,
                    'to_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                    'reference_id' => $occupation_inspection->id,
                    'remarks' => "",
                    'approved_by' => null,
                    'created_by' => Auth::id(),

                ];
                $id = $occupation_inspection->id;
                $this->inspection_ohc_status_log->store($data);
                $signature = $this->signature->requestorsignatureUpload(OHC_TYPE_OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST, $id);

                $ehsOfficer = GetEHSOfficer();
                $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
                $mailsubject = 'INSPECTION - OHC';
                $notificationData = array(
                    'notification_type' => 1,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => "Fire Associate create the Occupational Health Center Inspection Checklist",
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $occupation_inspection->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('ohc/inspection/view/' . encryptId($occupation_inspection->id)),
                    'assigned_user' => array_to_string($ehsOfficers),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $title = 'Fire Associate create the Occupational Health Center Inspection Checklist';
                foreach ($ehsOfficers as $user) {
                    $email_id = getUseremail($user);
                    $url = admin_url('ohc/inspection/verification/' . encryptId($id) . '/ehs');
                    $details = array(
                        'safety_type' => 'Occupational Health Center Inspection Checklist',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'url' => $url,
                        'data' => $occupation_inspection
                    );
                    Mail::to($email_id)->queue(new SafetyInspection($details));
                }



                Session::flash('success', __('Your data Created Successfully.!'));
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', __('common.message_error'));
            }

            return redirect(admin_url('ohc/inspection/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/inspection/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
               $occupational_health_center = $this->occupation_inspection->Selectone($id);

                $inspectionCkeclist = json_decode($occupational_health_center);
                $checklist_details = getCheckListQuestion(OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST);
                $options =  getoption(OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST);
                $getoption = string_to_array($options->type);

                $type = OHC_TYPE_OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST;
                $statuslog = $this->inspection_ohc_status_log->getStatuslog($id, $type);

                $requestorsignature =  $occupational_health_center->created_by;


                $requestor_signature = $this->signature->requestorSignature($id, $requestorsignature, $type);
                $data = array(
                    'occupational_health_center' =>$occupational_health_center,
                    'inspectionCkeclist' => $inspectionCkeclist,
                    'checklist_details' =>  $checklist_details, 'requestorsignature' => $requestor_signature,
                    'getoption' => $getoption,
                    'statuslog' => $statuslog,
                    'signatureview'=> $requestor_signature
                );
            }
            return view('inspection.inspection_ohc.occupational_heath_inspection.view', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function approvals(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
               $occupational_health_center = $this->occupation_inspection->Selectone($id);

                $inspectionCkeclist = json_decode($occupational_health_center);
                $checklist_details = getCheckListQuestion(OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST);
                $options =  getoption(OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST);
                $getoption = string_to_array($options->type);
                $requestorsignature =  $occupational_health_center->created_by;

                $type = OHC_TYPE_OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST;
                $requestor_signature = $this->signature->requestorSignature($id, $requestorsignature, $type);

                $data = array(
                    'occupational_health_center' =>$occupational_health_center,
                    'inspectionCkeclist' => $inspectionCkeclist,
                    'checklist_details' =>  $checklist_details, 'requestorsignature' => $requestor_signature,
                    'getoption' => $getoption,
                    'signatureview'=> $requestor_signature

                );
            }
            return view('inspection.inspection_ohc.occupational_heath_inspection.approval', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function EHSOfficerSubmit(Request $request)
    {
        try {
            $request = Request();
            $id = decryptId($request->id);

            $signature_update = $this->signature->signatureUpload(OHC_TYPE_OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST);
           $occupational_health_center = $this->occupation_inspection->Selectone($id);
            if ($request->is_passed == 1) {
                $message = 'Occupational Health Center Inspection Checklist Approved Successfully';
                $web_link =   admin_url('ohc/inspection/view/' . encryptId($occupational_health_center->id));
                $to_status = INSPECTION_APPROVED;
            } else {
                $message = 'Inspection Recommended for the CAPA Action';
                $web_link =   admin_url('ohc/inspection/verification/' . encryptId($occupational_health_center->id) . '/capa');
                $to_status = WAITING_FOR_CAPA_ACTION;
            }

            $inspection_updates = $this->occupation_inspection->EHSOfficerUpdate($id);
            $userIds = [
                'users' =>$occupational_health_center->created_by,
            ];
            $mailsubject = 'Occupational Health Center Inspection Checklist';
            $notificationData = array(
                'notification_type' => 1,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => $message,
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' =>$occupational_health_center->id,
                    'module' => 1,
                )),
                'web_link' =>  $web_link,
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = $message;
            $user =$occupational_health_center->created_by;
            $email_id = getUseremail($user);
            $url = admin_url('ohc/inspection/verification/' . encryptId($id) . '/ehs');
            $details = array(
                'safety_type' => 'Occupational Health Center Inspection Checklist',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' =>$occupational_health_center
            );
            Mail::to($email_id)->queue(new SafetyInspection($details));

            $data = [
                'type' => OHC_TYPE_OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'reference_id' => $id,
                'remarks' =>  $request->remarks,
                'approved_by' => Auth::id(),
                'created_by' => Auth::id(),

            ];
            $this->inspection_ohc_status_log->store($data);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/inspection/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', __('Something Went Wrong!'));
            return redirect(admin_url('ohc/inspection/list'));
        }
    }

    public function CAPASubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $occupation_inspection_inspection = $this->occupation_inspection->capaSubmit($id);
            $weeklyAmbulance = $this->occupation_inspection->Selectone($id);
            $signature_update = $this->signature->signatureUpload(OHC_TYPE_OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST);
            $ehsOfficers = $weeklyAmbulance->verified_by;
            $userIds = [
                'users' => $ehsOfficers,
            ];
            $mailsubject = 'Occupational Health Center Inspection Checklist';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "CAPA Action Completed by the Fire Associates",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $weeklyAmbulance->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('ohc/inspection/verification/' . encryptId($weeklyAmbulance->id)) . '/ehsVerify',
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $user = $weeklyAmbulance->verified_by;
            $email_id = getUseremail($user);
            $url = admin_url('ohc/inspection/verification/' . encryptId($id) . '/ehsVerify');
            $details = array(
                'safety_type' => 'Occupational Health Center Inspection Checklist',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => 'CAPA Action Completed by the Fire Associates',
                'url' => $url,
                'data' => $weeklyAmbulance
            );
            Mail::to($email_id)->queue(new SafetyInspection($details));


            $data = [
                'type' => OHC_TYPE_OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST,
                'from_status' => WAITING_FOR_CAPA_ACTION,
                'to_status' => WAITING_FOR_CAPA_VERIFICATION,
                'reference_id' => $id,
                'remarks' =>  $request->capa_remarks,
                'approved_by' => Auth::id(),
                'created_by' => Auth::id(),

            ];
            $this->inspection_ohc_status_log->store($data);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/inspection/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('ohc/inspection/list'));
        }
    }

    public function CAPAVerifySubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->remarks;
            $occupation_inspection_inspection = $this->occupation_inspection->capaVerifySubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(OHC_TYPE_OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST);
            $inspection_details = $this->occupation_inspection->Selectone($id);
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('ohc/inspection/verification/' . encryptId($inspection_details->id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = WAITING_FOR_L1_VERIFICATION;
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('ohc/inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = EHS_OFFICER_REJECTED;
            }
            $mailsubject = 'Occupational Health Center Inspection Checklist';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => $message,
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_details->id,
                    'module' => 1,
                )),
                'web_link' =>  $web_link,
                'assigned_user' => array_to_string($users),
                'created_by' => Auth::id(),
            );

            foreach ($users as $user) {
                $title = $message;
                $email_id = getUseremail($user);
                $url = $web_link;
                $details = array(
                    'safety_type' => 'Occupational Health Center Inspection Checklist',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }


            notificationSave($notificationData);


            $data = [
                'type' => OHC_TYPE_OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' =>  $to_status,
                'reference_id' => $id,
                'remarks' =>   $remarks,
                'approved_by' => Auth::id(),
                'created_by' => Auth::id(),

            ];
            $this->inspection_ohc_status_log->store($data);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/inspection/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('ohc/inspection/list'));
        }
    }

    public function levelOneManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_one_manager;
            $occupation_inspection_inspection = $this->occupation_inspection->levelOneManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(OHC_TYPE_OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST);
            $weekAmbulance = $this->occupation_inspection->Selectone($id);
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('ohc/inspection/verification/' . encryptId($weekAmbulance->id) . '/level-two-manager');
                $user = GetLevelTwoManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$weekAmbulance->created_by], [$weekAmbulance->verified_by], [$weekAmbulance->l1_manager_verified_by]);
                $to_status = WAITING_FOR_L2_VERIFICATION;
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('ohc/inspection/verification/' . encryptId($weekAmbulance->id) . '/capa');
                $users = $weekAmbulance->created_by;
                $to_status = L1_MANAGER_REJECTED;
            }
            $mailsubject = 'Occupational Health Center Inspection Checklist';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => $message,
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $weekAmbulance->id,
                    'module' => 1,
                )),
                'web_link' =>  $web_link,
                'assigned_user' => array_to_string($users),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            foreach ($users as $user) {
                $title = $message;
                $email_id = getUseremail($user);
                $url = $web_link;
                $details = array(
                    'safety_type' => 'Occupational Health Center Inspection Checklist',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $weekAmbulance
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }



            $data = [
                'type' => OHC_TYPE_OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST,
                'from_status' => WAITING_FOR_L1_VERIFICATION,
                'to_status' =>  $to_status,
                'reference_id' => $id,
                'remarks' =>  $remarks,
                'approved_by' => Auth::id(),
                'created_by' => Auth::id(),

            ];
            $this->inspection_ohc_status_log->store($data);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/inspection/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('ohc/inspection/list'));
        }
    }

    public function levelTwoManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_two_manager;
            $occupation_inspection_inspection = $this->occupation_inspection->levelTwoManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(OHC_TYPE_OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST);
            $weeklyAmbulance = $this->occupation_inspection->Selectone($id);
            if ($status == 1) {
                $message = 'Occupational Health Center Inspection Checklist  Approved Successfully!';
                $web_link =   admin_url('ohc/inspection/view/' . encryptId($weeklyAmbulance->id));
                $to_status = INSPECTION_APPROVED;
                $users = array_merge([$weeklyAmbulance->created_by], [$weeklyAmbulance->verified_by], [$weeklyAmbulance->l1_manager_verified_by], [$weeklyAmbulance->l2_manager_verified_by]);
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('ohc/inspection/verification/' . encryptId($weeklyAmbulance->id) . '/capa');
                $to_status = L2_MANAGER_REJECTED;
            }

            $mailsubject = 'Occupational Health Center Inspection Checklist';
            $notificationData = array(
                'notification_type' => 1,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => $message,
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $weeklyAmbulance->id,
                    'module' => 1,
                )),
                'web_link' =>  $web_link,
                'assigned_user' => array_to_string($users),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            foreach ($users as $user) {
                $title = $message;
                $email_id = getUseremail($user);
                $url = $web_link;
                $details = array(
                    'safety_type' => 'Occupational Health Center Inspection Checklist',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $weeklyAmbulance
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }


            $data = [
                'type' => OHC_TYPE_OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST,
                'from_status' => WAITING_FOR_L2_VERIFICATION,
                'to_status' =>  $to_status,
                'reference_id' => $id,
                'remarks' =>  $remarks,
                'approved_by' => Auth::id(),
                'created_by' => Auth::id(),

            ];
            $this->inspection_ohc_status_log->store($data);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('ohc/inspection/list'));
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->occupation_inspection->statuschange($id);


            return response()->json(['status' => 'success', 'msg' => 'Your Status Changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->occupation_inspection->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Review date',
                'Issued Date',
                'Next Due On',
                'Date Of Inspection',
                'Shift',
                'Location',
                'Unit',
                'Approve Status',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->doc_no;
                $export[] =  $data->revision_date;
                $export[] =  Displaydateformat($data->issue_date);
                $export[] = Displaydateformat($data->next_due);
                $export[] = Displaydateformat($data->date_of_inspection);
                $export[] =  getShift($data->shift);
                $export[] =  getLocationname($data->location);
                $export[] =  getUnitname($data->unit);
                $export[] =  getInspectionStatus($data->approve_status);;
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Occupational Health Center inspection Checklist.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/inspection/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->occupation_inspection->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Review date',
                'Issued Date',
                'Next Due On',
                'Date Of Inspection',
                'Shift',
                'Location',
                'Unit',
                'Approve Status',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Occupational Health Center Inspection Checklist",
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

            $view = view('inspection.inspection_ohc.occupational_heath_inspection.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Occupational Health Center Inspection Checklist.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/inspection/list'));
        }
    }


    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $weeklyAmbulance = $this->occupation_inspection->Selectone($id);

                $type = OHC_TYPE_OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST;
                $statuslog = $this->inspection_ohc_status_log->getStatuslog($id, $type);

                $inspectionCkeclist = json_decode($weeklyAmbulance);
                $checklist_details = getCheckListQuestion(OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST);
                $options =  getoption(OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST);
                $getoption = string_to_array($options->type);
            }
            $data = [
                'weeklyAmbulance' => $weeklyAmbulance,
                'statuslog' => $statuslog,
                'inspectionCkeclist' => $inspectionCkeclist,
                'checklist_details' =>  $checklist_details,
                'getoption' => $getoption,
                'pagetitle' => "Occupational Health Center Inspection Checklist",
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

            $html = view('inspection.inspection_ohc.occupational_heath_inspection.viewpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "Occupational Health Center Inspection Checklist.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }
}
