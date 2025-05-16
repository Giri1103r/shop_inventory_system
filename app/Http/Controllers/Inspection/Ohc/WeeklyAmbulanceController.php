<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use App\Mail\Inspection\Ohc\OccupationalHealthEmail;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Master\ChecklistOptionType;
use App\Models\Inspection\Master\ChecklistSubTypeData;
use App\Models\Inspection\Master\ChecklistSubTypeDataName;
use App\Models\Inspection\Master\ChecklistType;
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


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;


class WeeklyAmbulanceController extends Controller
{
    private $weekly_ambulance_details;
    private $weekly_ambulance_inspection_checklist;
    private $upload_log;
    private $unit;
    private $shift;
    private $department;
    private $user;
    private $checklist_type;
    private $sub_type_data;
    private $sub_type_data_name;
    private $questionery;
    private $signature;
    private $location;
    private $inspection_ohc_status_log;
    private $document_reference;



    public function __construct()
    {
        $this->weekly_ambulance_details = new WeeklyAmbulance();

        $this->upload_log = new UploadLog();
        $this->unit = new Unit();
        $this->department = new Department();
        $this->document_reference = new InspectionStaticDocno();
        $this->shift = new Shift();
        $this->checklist_type = new ChecklistType();
        $this->sub_type_data = new ChecklistSubTypeData();
        $this->sub_type_data_name = new ChecklistSubTypeDataName();
        $this->questionery = new ChecklistOptionType();
        $this->signature = new OhcSignature();
        $this->location = new Location();
        $this->inspection_ohc_status_log = new InspectionOhcStatuslog();
        $this->user = new User();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {

                    $data = $this->weekly_ambulance_details->list();

                    $datatables = Datatables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->inspection_id) . "' data-type='1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->inspection_id) . "' data-type='0'>In-Active</span>";
                            }
                            return $text;
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('inspection_created_at', function ($row) {
                            return Displaydateformat($row->inspection_created_at);
                        })
                        ->addColumn('unit', function ($row) {
                            return ($row->unit_name);
                        })
                        ->addColumn('location', function ($row) {
                            return ($row->location_name);
                        })
                        ->addColumn('inspection_created_by', function ($row) {
                            return getUsername($row->inspection_created_by);
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

                            $btn .=  '<a href="' . admin_url('ohc/weekly-ambulance/inspection/checklist/view/' . encryptId($row->inspection_id)) . '" class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a>';

                            if ($row->approve_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('ohc/weekly-ambulance/inspection/checklist/verification/' . encryptId($row->inspection_id)) . '/ehs" class="me-1" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if (($row->approve_status == WAITING_FOR_CAPA_ACTION || $row->approve_status == L2_MANAGER_REJECTED || $row->approve_status == EHS_OFFICER_REJECTED || $row->approve_status == L1_MANAGER_REJECTED) && (CheckUserRole(ROLE_FIRE_ASSOCIATES) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('ohc/weekly-ambulance/inspection/checklist/verification/' . encryptId($row->inspection_id)) . '/capa" class="" title="' . __('inspection.capa_action') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->approve_status == WAITING_FOR_CAPA_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('ohc/weekly-ambulance/inspection/checklist/verification/' . encryptId($row->inspection_id)) . '/ehsVerify" class="me-1" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->approve_status == WAITING_FOR_L1_VERIFICATION && (CheckUserRole(ROLE_L1_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('ohc/weekly-ambulance/inspection/checklist/verification/' . encryptId($row->inspection_id)) . '/level-one-manager" class="me-1" title="' . __('inspection.l1_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->approve_status == WAITING_FOR_L2_VERIFICATION && (CheckUserRole(ROLE_L2_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('ohc/weekly-ambulance/inspection/checklist/verification/' . encryptId($row->inspection_id)) . '/level-two-manager" class="me-1" title="' . __('inspection.l2_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }

                            $btn .= '<a href="' . admin_url('ohc/weekly-ambulance/inspection/checklist/generalpdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';
                            $btn .= '<a href="' . admin_url('ohc/weekly-ambulance/inspection/checklist/generalExcel/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
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
        $location = $this->location->getLocationname();
        $shift = $this->shift->getShiftname();
        $unit = $this->unit->getunit();
        $data = array(
            'unit' => $unit,
            'shift' => $shift,
            'location' => $location,
        );
        return view('inspection.inspection_ohc.weekly_ambulance.list', $data);
    }



    public function Add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $shift = $this->shift->getShiftname();
            $checklist_details = getCheckListQuestion(WEEKLY_AMBULANCE_INSPECTION_CHECKLIST);
            $options =  getoption(WEEKLY_AMBULANCE_INSPECTION_CHECKLIST);
            $getoption = string_to_array($options->type);
            $location = $this->location->getLocationname();
            $signature_upload = $this->user->getSignature();
            $document_no = $this->document_reference->selectUsingName('WeeklyAmbulanceInspectionChecklist');
            if (count($checklist_details) <= 0) {
                Session::flash('success', __('inspection.checklist_add'));
                return redirect(admin_url('inspection/master/checklist-sub-type-data/add'));
            }
            $data = array(
                'unit' => $unit,
                'shift' => $shift,
                'checklist_details' =>  $checklist_details,
                'location' => $location,
                'document_no' => $document_no,
                'getoption' => $getoption,
                'signature_upload' => $signature_upload,


            );
            return view('inspection.inspection_ohc.weekly_ambulance.add', $data);
        } catch (Exception $ex) {
             report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/weekly-ambulance/inspection/checklist/list'));
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
                    'remarks' => ($request->remarks),
                ];

                $weekly_ambulance_details = $this->weekly_ambulance_details->store($responses);
                $data = [
                    'type' => OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST,
                    'from_status' => OHC_CREATION,
                    'to_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                    'reference_id' => $weekly_ambulance_details->id,
                    'remarks' =>  "",
                    'approved_by' => null,
                    'created_by' => Auth::id(),

                ];
                $id = $weekly_ambulance_details->id;
                $this->inspection_ohc_status_log->store($data);

                $signature = $this->signature->requestorsignatureUpload(OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST, $id);

                $ehsOfficer = GetEHSOfficer();
                $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
                $mailsubject = 'INSPECTION - OHC';
                $notificationData = array(
                    'notification_type' => OHC_INSPECTION,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => "Fire Associate create the Weekly Ambulance Inspection Checklist",
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $weekly_ambulance_details->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('ohc/weekly-ambulance/inspection/checklist/view/' . encryptId($weekly_ambulance_details->id)),
                    'assigned_user' => array_to_string($ehsOfficers),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $title = 'Fire Associate create the Weekly Ambulance Inspection Checklist';
                foreach ($ehsOfficers as $user) {
                    $email_id = getUseremail($user);
                    $url = admin_url('ohc/weekly-ambulance/inspection/checklist/verification/' . encryptId($id) . '/ehs');
                    $details = array(
                        'ohc_type' => 'Weekly Ambulance Inspection Checklist',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'url' => $url,
                        'data' => $weekly_ambulance_details
                    );
                    Mail::to($email_id)->queue(new OccupationalHealthEmail($details));
                }



                Session::flash('success', __('Your data Created Successfully.!'));
            } catch (Exception $ex) {
                 report($ex);
                Session::flash('error', __('common.message_error'));
            }

            return redirect(admin_url('ohc/weekly-ambulance/inspection/checklist/list'));
        } catch (Exception $ex) {
             report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/weekly-ambulance/inspection/checklist/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $weekAmbualance = $this->weekly_ambulance_details->WeekambulanceSelectone($id);

                $inspectionCkeclist = json_decode($weekAmbualance);
                $checklist_details = getCheckListQuestion(WEEKLY_AMBULANCE_INSPECTION_CHECKLIST);
                $options =  getoption(WEEKLY_AMBULANCE_INSPECTION_CHECKLIST);
                $getoption = string_to_array($options->type);

                $type = OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST;
                $statuslog = $this->inspection_ohc_status_log->getStatuslog($id, $type);

                $requestorsignature =  $weekAmbualance->created_by;

                $document_no = $this->document_reference->selectUsingName('WeeklyAmbulanceInspectionChecklist');
                $requestor_signature = $this->signature->requestorSignature($id, $requestorsignature, $type);

                $data = array(
                    'weekAmbualance' => $weekAmbualance,
                    'inspectionCkeclist' => $inspectionCkeclist,
                    'checklist_details' =>  $checklist_details,
                    'getoption' => $getoption,
                    'requestorsignature' => $requestor_signature,
                    'statuslog' => $statuslog,
                    'document_no' => $document_no,
                );
            }
            return view('inspection.inspection_ohc.weekly_ambulance.view', $data);
        } catch (Exception $ex) {
             report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/weekly-ambulance/inspection/checklist/list'));
        }
    }

    public function approvals(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $weekAmbualance = $this->weekly_ambulance_details->WeekambulanceSelectone($id);

                $inspectionCkeclist = json_decode($weekAmbualance);
                $checklist_details = getCheckListQuestion(WEEKLY_AMBULANCE_INSPECTION_CHECKLIST);
                $options =  getoption(WEEKLY_AMBULANCE_INSPECTION_CHECKLIST);
                $getoption = string_to_array($options->type);
                $type = OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST;
                $statuslog = $this->inspection_ohc_status_log->getStatuslog($id, $type);

                $requestorsignature =  $weekAmbualance->created_by;

                $document_no = $this->document_reference->selectUsingName('WeeklyAmbulanceInspectionChecklist');
                $requestor_signature = $this->signature->requestorSignature($id, $requestorsignature, $type);



                $data = array(
                    'weekAmbualance' => $weekAmbualance,
                    'inspectionCkeclist' => $inspectionCkeclist,
                    'checklist_details' =>  $checklist_details,
                    'getoption' => $getoption,
                    'requestorsignature' => $requestor_signature,
                    'document_no' => $document_no,

                );
            }
            return view('inspection.inspection_ohc.weekly_ambulance.approval', $data);
        } catch (Exception $ex) {
             report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/weekly-ambulance/inspection/checklist/list'));
        }
    }

    public function EHSOfficerSubmit(Request $request)
    {
        try {
            $request = Request();
            $id = decryptId($request->id);

            $signature_update = $this->signature->signatureUpload(OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST);
            $weekAmbualance = $this->weekly_ambulance_details->WeekambulanceSelectone($id);
            if ($request->is_passed == 1) {
                $message = 'Weekly Ambulance Inspection Checklist Approved Successfully';
                $web_link =   admin_url('ohc/weekly-ambulance/inspection/checklist/view/' . encryptId($weekAmbualance->id));
                $to_status = INSPECTION_APPROVED;
            } else {
                $message = 'Inspection Recommended for the CAPA Action';
                $web_link =   admin_url('ohc/weekly-ambulance/inspection/checklist/verification/' . encryptId($weekAmbualance->id) . '/capa');
                $to_status = WAITING_FOR_CAPA_ACTION;
            }

            $inspection_updates = $this->weekly_ambulance_details->EHSOfficerUpdate($id);
            $userIds = [
                'users' => $weekAmbualance->created_by,
            ];
            $mailsubject = 'Weekly Ambulance Inspection Checklist';
            $notificationData = array(
                'notification_type' => OHC_INSPECTION,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => $message,
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $weekAmbualance->id,
                    'module' => 1,
                )),
                'web_link' =>  $web_link,
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = $message;
            $user = $weekAmbualance->created_by;
            $email_id = getUseremail($user);
            $url = admin_url('ohc/weekly-ambulance/inspection/checklist/verification/' . encryptId($id) . '/ehs');
            $details = array(
                'ohc_type' => 'Weekly Ambulance Inspection Checklist',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $weekAmbualance
            );
            Mail::to($email_id)->queue(new OccupationalHealthEmail($details));

            $data = [
                'type' => OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'reference_id' => $id,
                'remarks' =>  $request->remarks,
                'approved_by' => Auth::id(),
                'created_by' => Auth::id(),

            ];
            $this->inspection_ohc_status_log->store($data);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/weekly-ambulance/inspection/checklist/list'));
        } catch (Exception $ex) {
             report($ex);
            Session::flash('error', __('Something Went Wrong!'));
            return redirect(admin_url('ohc/weekly-ambulance/inspection/checklist/list'));
        }
    }

    public function CAPASubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $weekly_ambulance_details_inspection = $this->weekly_ambulance_details->capaSubmit($id);
            $weeklyAmbulance = $this->weekly_ambulance_details->WeekambulanceSelectone($id);
            $signature_update = $this->signature->signatureUpload(OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST);
            $ehsOfficers = $weeklyAmbulance->verified_by;
            $userIds = [
                'users' => $ehsOfficers,
            ];
            $mailsubject = 'Weekly Ambulance Inspection Checklist';
            $notificationData = array(
                'notification_type' => OHC_INSPECTION,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "CAPA Action Completed by the Fire Associates",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $weeklyAmbulance->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('ohc/weekly-ambulance/inspection/checklist/verification/' . encryptId($weeklyAmbulance->id)) . '/ehsVerify',
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $user = $weeklyAmbulance->verified_by;
            $email_id = getUseremail($user);
            $url = admin_url('ohc/weekly-ambulance/inspection/checklist/verification/' . encryptId($id) . '/ehsVerify');
            $details = array(
                'ohc_type' => 'Weekly Ambulance Inspection Checklist',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => 'CAPA Action Completed by the Fire Associates',
                'url' => $url,
                'data' => $weeklyAmbulance
            );
            Mail::to($email_id)->queue(new OccupationalHealthEmail($details));


            $data = [
                'type' => OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST,
                'from_status' => WAITING_FOR_CAPA_ACTION,
                'to_status' => WAITING_FOR_CAPA_VERIFICATION,
                'reference_id' => $id,
                'remarks' =>  $request->capa_remarks,
                'approved_by' => Auth::id(),
                'created_by' => Auth::id(),

            ];
            $this->inspection_ohc_status_log->store($data);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/weekly-ambulance/inspection/checklist/list'));
        } catch (Exception $ex) {
             report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('ohc/weekly-ambulance/inspection/checklist/list'));
        }
    }

    public function CAPAVerifySubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->remarks;
            $weekly_ambulance_details_inspection = $this->weekly_ambulance_details->capaVerifySubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST);
            $inspection_details = $this->weekly_ambulance_details->WeekambulanceSelectone($id);
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('ohc/weekly-ambulance/inspection/checklist/verification/' . encryptId($inspection_details->id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = WAITING_FOR_L1_VERIFICATION;
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('ohc/weekly-ambulance/inspection/checklist/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = EHS_OFFICER_REJECTED;
            }
            $mailsubject = 'Weekly Ambulance Inspection Checklist';
            $notificationData = array(
                'notification_type' => OHC_INSPECTION,
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
                    'ohc_type' => 'Weekly Ambulance Inspection Checklist',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new OccupationalHealthEmail($details));
            }


            notificationSave($notificationData);


            $data = [
                'type' => OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' =>  $to_status,
                'reference_id' => $id,
                'remarks' =>   $remarks,
                'approved_by' => Auth::id(),
                'created_by' => Auth::id(),

            ];
            $this->inspection_ohc_status_log->store($data);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/weekly-ambulance/inspection/checklist/list'));
        } catch (Exception $ex) {
             report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('ohc/weekly-ambulance/inspection/checklist/list'));
        }
    }

    public function levelOneManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_one_manager;
            $weekly_ambulance_details_inspection = $this->weekly_ambulance_details->levelOneManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST);
            $weekAmbulance = $this->weekly_ambulance_details->WeekambulanceSelectone($id);
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('ohc/weekly-ambulance/inspection/checklist/verification/' . encryptId($weekAmbulance->id) . '/level-two-manager');
                $user = GetLevelTwoManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$weekAmbulance->created_by], [$weekAmbulance->verified_by], [$weekAmbulance->l1_manager_verified_by]);
                $to_status = WAITING_FOR_L2_VERIFICATION;
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('ohc/weekly-ambulance/inspection/checklist/verification/' . encryptId($weekAmbulance->id) . '/capa');
                $users = $weekAmbulance->created_by;
                $to_status = L1_MANAGER_REJECTED;
            }
            $mailsubject = 'Weekly Ambulance Inspection Checklist';
            $notificationData = array(
                'notification_type' => OHC_INSPECTION,
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
                    'ohc_type' => 'Weekly Ambulance Inspection Checklist',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $weekAmbulance
                );
                Mail::to($email_id)->queue(new OccupationalHealthEmail($details));
            }



            $data = [
                'type' => OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST,
                'from_status' => WAITING_FOR_L1_VERIFICATION,
                'to_status' =>  $to_status,
                'reference_id' => $id,
                'remarks' =>  $remarks,
                'approved_by' => Auth::id(),
                'created_by' => Auth::id(),

            ];
            $this->inspection_ohc_status_log->store($data);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/weekly-ambulance/inspection/checklist/list'));
        } catch (Exception $ex) {
             report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('ohc/weekly-ambulance/inspection/checklist/list'));
        }
    }

    public function levelTwoManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_two_manager;
            $weekly_ambulance_details_inspection = $this->weekly_ambulance_details->levelTwoManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST);

            $weeklyAmbulance = $this->weekly_ambulance_details->WeekambulanceSelectone($id);
            if ($status == 1) {
                $message = 'Weekly Ambulance Inspection Checklist  Approved Successfully!';
                $web_link =   admin_url('ohc/weekly-ambulance/inspection/checklist/view/' . encryptId($weeklyAmbulance->id));
                $to_status = INSPECTION_APPROVED;
                $users = array_merge([$weeklyAmbulance->created_by], [$weeklyAmbulance->verified_by], [$weeklyAmbulance->l1_manager_verified_by], [$weeklyAmbulance->l2_manager_verified_by]);
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('ohc/weekly-ambulance/inspection/checklist/verification/' . encryptId($weeklyAmbulance->id) . '/capa');
                $to_status = L2_MANAGER_REJECTED;
            }

            $mailsubject = 'Weekly Ambulance Inspection Checklist';
            $notificationData = array(
                'notification_type' => OHC_INSPECTION,
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
                    'ohc_type' => 'Weekly Ambulance Inspection Checklist',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $weeklyAmbulance
                );
                Mail::to($email_id)->queue(new OccupationalHealthEmail($details));
            }


            $data = [
                'type' => OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST,
                'from_status' => WAITING_FOR_L2_VERIFICATION,
                'to_status' =>  $to_status,
                'reference_id' => $id,
                'remarks' =>  $remarks,
                'approved_by' => Auth::id(),
                'created_by' => Auth::id(),

            ];
            $this->inspection_ohc_status_log->store($data);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/weekly-ambulance/inspection/checklist/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('ohc/weekly-ambulance/inspection/checklist/list'));
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->weekly_ambulance_details->statuschange($id);


            return response()->json(['status' => 'success', 'msg' => 'Your Status Changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->weekly_ambulance_details->exportdata();
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $row = 1;
            $currentRow = $row;

            foreach ($allData as $details) {
                $currentRow = $row;

                $weeklyAmbulance = $this->weekly_ambulance_details->WeekambulanceSelectone($details->inspection_id);
                $document_no = $this->document_reference->selectOne($weeklyAmbulance->document_reference_id);

                $logoLeftPath = public_path('assets/images/logo-dark.png');
                if (file_exists($logoLeftPath)) {
                    $sheet->mergeCells("A$currentRow:F" . ($currentRow + 2));

                    $drawing = new Drawing();
                    $drawing->setName('Left Logo');
                    $drawing->setPath($logoLeftPath);
                    $drawing->setCoordinates("B$currentRow");
                    $drawing->setOffsetX(100);
                    $drawing->setOffsetY(15);
                    $drawing->setWidth(70);
                    $drawing->setHeight(70);
                    $drawing->setWorksheet($sheet);

                    $range = "A$currentRow:F" . ($currentRow + 2);
                    $sheet->getStyle($range)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                }

                // Title Section
                $sheet->mergeCells("G{$currentRow}:M" . ($currentRow + 2));
                $sheet->setCellValue("G{$currentRow}", "WEEKLY AMBULANCE INSPECTION CHECKLIST");
                $sheet->getStyle("G{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Document Info
                $sheet->mergeCells("N$currentRow:P$currentRow")->setCellValue("N$currentRow", 'Doc. No.');
                $sheet->mergeCells("N" . ($currentRow + 1) . ":P" . ($currentRow + 1))->setCellValue("N" . ($currentRow + 1), 'Issue Dt.');
                $sheet->mergeCells("N" . ($currentRow + 2) . ":P" . ($currentRow + 2))->setCellValue("N" . ($currentRow + 2), 'Rev. & Dt.');

                $sheet->mergeCells("Q$currentRow:S$currentRow")->setCellValue("Q$currentRow", $document_no->doc_no);
                $sheet->mergeCells("Q" . ($currentRow + 1) . ":S" . ($currentRow + 1))->setCellValue("Q" . ($currentRow + 1), Displaydateformat($document_no->issue_date));
                $sheet->mergeCells("Q" . ($currentRow + 2) . ":S" . ($currentRow + 2))->setCellValue("Q" . ($currentRow + 2), $document_no->rev_dt);

                $sheet->getStyle("N$currentRow:S" . ($currentRow + 2))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'font' => ['bold' => true],
                ]);

                // Review Dates
                $sheet->mergeCells("A" . ($currentRow + 3) . ":G" . ($currentRow + 3));
                $richText1 = new RichText();
                $richText1->createTextRun(' DATE OF INSPECTION:- ')->getFont()->setBold(true);
                $richText1->createText(Displaydateformat($weeklyAmbulance->date_of_inspection));
                $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

                $sheet->mergeCells("H" . ($currentRow + 3) . ":N" . ($currentRow + 3));
                $richText2 = new RichText();
                $richText2->createTextRun('UNIT :-  ')->getFont()->setBold(true);
                $richText2->createText(getUnitname($weeklyAmbulance->unit));
                $sheet->getCell("H" . ($currentRow + 3))->setValue($richText2);

                $sheet->mergeCells("O" . ($currentRow + 3) . ":S" . ($currentRow + 3));
                $richText2 = new RichText();
                $richText2->createTextRun('SHIFT :-  ')->getFont()->setBold(true);
                $richText2->createText(getShift($weeklyAmbulance->shift));
                $sheet->getCell("O" . ($currentRow + 3))->setValue($richText2);

                $sheet->getStyle("A" . ($currentRow + 3) . ":S" . ($currentRow + 3))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // next row 5

                $sheet->mergeCells("A" . ($currentRow + 4) . ":I" . ($currentRow + 4));
                $richText1 = new RichText();
                $richText1->createTextRun(' NEXT DUE DATE OF INSPECTION :- ')->getFont()->setBold(true);
                $richText1->createText(Displaydateformat($weeklyAmbulance->next_due));
                $sheet->getCell("A" . ($currentRow + 4))->setValue($richText1);

                $sheet->mergeCells("J" . ($currentRow + 4) . ":S" . ($currentRow + 4));
                $richText2 = new RichText();
                $richText2->createTextRun('LOCATION :- ')->getFont()->setBold(true);
                $richText2->createText(getLocationname($weeklyAmbulance->location));
                $sheet->getCell("J" . ($currentRow + 4))->setValue($richText2);



                $sheet->getStyle("A" . ($currentRow + 4) . ":S" . ($currentRow + 3))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // Table Header
                $headerRow = $currentRow + 5;
                $sheet->mergeCells("A$headerRow:C$headerRow")->setCellValue("A$headerRow", "SERIAL NO");
                $sheet->mergeCells("D$headerRow:L$headerRow")->setCellValue("D$headerRow", "CHECK ITEMS");
                $sheet->mergeCells("M$headerRow:O$headerRow")->setCellValue("M$headerRow", "STATUS");
                $sheet->mergeCells("P$headerRow:S$headerRow")->setCellValue("P$headerRow", "REMARKS");

                $sheet->getStyle("A$headerRow:S$headerRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'font' => ['bold' => true],
                ]);

                $inspectionRow = $headerRow + 1;
                $srNo = 1;
                $decodedData = json_decode($weeklyAmbulance->checklist, true);
                $checkItems = $decodedData['check_item'] ?? [];
                $statuses = $decodedData['status'] ?? [];
                $remarks = $decodedData['remarks'] ?? [];

                $srNo = 1;
                foreach ($checkItems as $groupId => $checkPoints) {
                    $rowCount = count($checkPoints);
                    $firstRowInGroup = true;

                    foreach ($checkPoints as $checkPoint) {

                        if ($firstRowInGroup) {
                            $sheet->mergeCells("A$inspectionRow:A" . ($inspectionRow + $rowCount - 1))
                                ->setCellValue("A$inspectionRow", $srNo);

                            $sheet->mergeCells("B$inspectionRow:C" . ($inspectionRow + $rowCount - 1))
                                ->setCellValue("B$inspectionRow", getSubcategoryname($groupId));

                            $firstRowInGroup = false;
                            $srNo++;
                        }


                        $sheet->mergeCells("D$inspectionRow:L$inspectionRow")
                            ->setCellValue("D$inspectionRow", getSubcategoryDataname($checkPoint));




                        $statusIcon = '';
                        if (!empty($statuses[$checkPoint]) && $statuses[$checkPoint] == 'Ok') {
                            $statusIcon = '✓';
                        } elseif (!empty($statuses[$checkPoint]) && in_array($statuses[$checkPoint], ['Not-Ok', 'N/A'])) {
                            $statusIcon = 'X';
                        } else {
                            $statusIcon = '-';
                        }
                        $sheet->mergeCells("M$inspectionRow:O$inspectionRow")
                            ->setCellValue("M$inspectionRow", $statusIcon);


                        $sheet->mergeCells("P$inspectionRow:S$inspectionRow")
                            ->setCellValue("P$inspectionRow", $remarks[$checkPoint] ?? 'No Remarks');


                        $sheet->getStyle("A$inspectionRow:S$inspectionRow")->applyFromArray([
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                ],
                            ],
                        ]);

                        $inspectionRow++;
                    }
                }

                $row = $inspectionRow;

                $CreatorSignature = GetOHCSignature($weeklyAmbulance->created_by, $details->inspection_id, OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST);
                $VerifiedSignature = GetOHCSignature($weeklyAmbulance->verified_by, $details->inspection_id, OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST);
                $ApprovedSignature = GetOHCSignature($weeklyAmbulance->approved_by, $details->inspection_id, OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST);

                if (file_exists($CreatorSignature)) {
                    $sheet->mergeCells("A$row:F" . ($row + 2));

                    $drawing = new Drawing();
                    $drawing->setName('Creator Signature');
                    $drawing->setPath($CreatorSignature);
                    $drawing->setCoordinates("A$row");
                    $drawing->setOffsetX(100);
                    $drawing->setOffsetY(5);
                    $drawing->setWidth(70);
                    $drawing->setHeight(70);
                    $drawing->setWorksheet($sheet);
                    $sheet->getRowDimension($row + 2)->setRowHeight(40);
                    // Label + Name
                    $sheet->setCellValue("A" . ($row + 3), "Checked By: " . getUserName($weeklyAmbulance->created_by));
                    $sheet->mergeCells("A" . ($row + 3) . ":F" . ($row + 3));

                    $sheet->getStyle("A$row:F" . ($row + 3))->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                }

                if (file_exists($VerifiedSignature)) {
                    $sheet->mergeCells("G$row:L" . ($row + 2));

                    $drawing = new Drawing();
                    $drawing->setName('Verified Signature');
                    $drawing->setPath($VerifiedSignature);
                    $drawing->setCoordinates("G$row");
                    $drawing->setOffsetX(100);
                    $drawing->setOffsetY(5);
                    $drawing->setWidth(70);
                    $drawing->setHeight(70);
                    $drawing->setWorksheet($sheet);
                    $sheet->getRowDimension($row + 2)->setRowHeight(40);
                    // Label + Name
                    $sheet->setCellValue("G" . ($row + 3), "Verified By: " . getUserName($weeklyAmbulance->verified_by));
                    $sheet->mergeCells("G" . ($row + 3) . ":L" . ($row + 3));

                    $sheet->getStyle("G$row:L" . ($row + 3))->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                }

                if (file_exists($ApprovedSignature)) {
                    $sheet->mergeCells("M$row:S" . ($row + 2));

                    $drawing = new Drawing();
                    $drawing->setName('Approved Signature');
                    $drawing->setPath($ApprovedSignature);
                    $drawing->setCoordinates("M$row");
                    $drawing->setOffsetX(100);
                    $drawing->setOffsetY(5);
                    $drawing->setWidth(70);
                    $drawing->setHeight(70);
                    $drawing->setWorksheet($sheet);
                    $sheet->getRowDimension($row + 2)->setRowHeight(60);
                    // Label + Name
                    $sheet->setCellValue("M" . ($row + 3), "Approved By: " . getUserName($weeklyAmbulance->approved_by));
                    $sheet->mergeCells("M" . ($row + 3) . ":S" . ($row + 3));

                    $sheet->getStyle("M$row:S" . ($row + 3))->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                }
                $row = $row + 8;
            }
            $filename = 'Weekly Ambulance Inspection checklist.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (Exception $ex) {

             report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/weekly-ambulance/inspection/checklist/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->weekly_ambulance_details->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }
            $document_no = $this->document_reference->selectUsingName('WeeklyAmbulanceInspectionChecklist');

            $header = [
                __("common.sno"),
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
                'document_no' => $document_no,
                'pagetitle' => "Weekly Ambulance Inspection Checklist",
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

            $view = view('inspection.inspection_ohc.weekly_ambulance.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Weekly Ambulance Inspection Checklist.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

             report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/weekly-ambulance/inspection/checklist/list'));
        }
    }


    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $weeklyAmbulance = $this->weekly_ambulance_details->WeekambulanceSelectone($id);

                $type = OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST;
                $statuslog = $this->inspection_ohc_status_log->getStatuslog($id, $type);

                $inspectionCkeclist = json_decode($weeklyAmbulance);
                $checklist_details = getCheckListQuestion(WEEKLY_AMBULANCE_INSPECTION_CHECKLIST);
                $options =  getoption(WEEKLY_AMBULANCE_INSPECTION_CHECKLIST);
                $getoption = string_to_array($options->type);
                $document_no = $this->document_reference->selectUsingName('WeeklyAmbulanceInspectionChecklist');
            }
            $data = [
                'weeklyAmbulance' => $weeklyAmbulance,
                'statuslog' => $statuslog,
                'inspectionCkeclist' => $inspectionCkeclist,
                'checklist_details' =>  $checklist_details,
                'getoption' => $getoption,
                'document_no' => $document_no,
                'pagetitle' => "Weekly Ambulance Inspection Checklist",
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

            $html = view('inspection.inspection_ohc.weekly_ambulance.viewpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "Weekly Ambulance Inspection Checklist.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
             report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }


    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $row = 1;

            $weeklyAmbulance = $this->weekly_ambulance_details->WeekambulanceSelectone($id);
            $document_no = $this->document_reference->selectOne($weeklyAmbulance->document_reference_id);


            $currentRow = $row;

            // Logo Section
            $logoLeftPath = public_path('assets/images/logo-dark.png');
            if (file_exists($logoLeftPath)) {
                $sheet->mergeCells("A$currentRow:F" . ($currentRow + 2));

                $drawing = new Drawing();
                $drawing->setName('Left Logo');
                $drawing->setPath($logoLeftPath);
                $drawing->setCoordinates("B$currentRow");
                $drawing->setOffsetX(100);
                $drawing->setOffsetY(15);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);

                $range = "A$currentRow:F" . ($currentRow + 2);
                $sheet->getStyle($range)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }

            // Title Section
            $sheet->mergeCells("G{$currentRow}:M" . ($currentRow + 2));
            $sheet->setCellValue("G{$currentRow}", "WEEKLY AMBULANCE INSPECTION CHECKLIST");
            $sheet->getStyle("G{$currentRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            // Document Info
            $sheet->mergeCells("N$currentRow:P$currentRow")->setCellValue("N$currentRow", 'Doc. No.');
            $sheet->mergeCells("N" . ($currentRow + 1) . ":P" . ($currentRow + 1))->setCellValue("N" . ($currentRow + 1), 'Issue Dt.');
            $sheet->mergeCells("N" . ($currentRow + 2) . ":P" . ($currentRow + 2))->setCellValue("N" . ($currentRow + 2), 'Rev. & Dt.');

            $sheet->mergeCells("Q$currentRow:S$currentRow")->setCellValue("Q$currentRow", $document_no->doc_no);
            $sheet->mergeCells("Q" . ($currentRow + 1) . ":S" . ($currentRow + 1))->setCellValue("Q" . ($currentRow + 1), Displaydateformat($document_no->issue_date));
            $sheet->mergeCells("Q" . ($currentRow + 2) . ":S" . ($currentRow + 2))->setCellValue("Q" . ($currentRow + 2), $document_no->rev_dt);

            $sheet->getStyle("N$currentRow:S" . ($currentRow + 2))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font' => ['bold' => true],
            ]);

            // Review Dates
            $sheet->mergeCells("A" . ($currentRow + 3) . ":G" . ($currentRow + 3));
            $richText1 = new RichText();
            $richText1->createTextRun(' DATE OF INSPECTION:- ')->getFont()->setBold(true);
            $richText1->createText(Displaydateformat($weeklyAmbulance->date_of_inspection));
            $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

            $sheet->mergeCells("H" . ($currentRow + 3) . ":N" . ($currentRow + 3));
            $richText2 = new RichText();
            $richText2->createTextRun('UNIT :-  ')->getFont()->setBold(true);
            $richText2->createText(getUnitname($weeklyAmbulance->unit));
            $sheet->getCell("H" . ($currentRow + 3))->setValue($richText2);

            $sheet->mergeCells("O" . ($currentRow + 3) . ":S" . ($currentRow + 3));
            $richText2 = new RichText();
            $richText2->createTextRun('SHIFT :-  ')->getFont()->setBold(true);
            $richText2->createText(getShift($weeklyAmbulance->shift));
            $sheet->getCell("O" . ($currentRow + 3))->setValue($richText2);

            $sheet->getStyle("A" . ($currentRow + 3) . ":S" . ($currentRow + 3))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // next row 5

            $sheet->mergeCells("A" . ($currentRow + 4) . ":I" . ($currentRow + 4));
            $richText1 = new RichText();
            $richText1->createTextRun(' NEXT DUE DATE OF INSPECTION :- ')->getFont()->setBold(true);
            $richText1->createText(Displaydateformat($weeklyAmbulance->next_due));
            $sheet->getCell("A" . ($currentRow + 4))->setValue($richText1);

            $sheet->mergeCells("J" . ($currentRow + 4) . ":S" . ($currentRow + 4));
            $richText2 = new RichText();
            $richText2->createTextRun('LOCATION :- ')->getFont()->setBold(true);
            $richText2->createText(getLocationname($weeklyAmbulance->location));
            $sheet->getCell("J" . ($currentRow + 4))->setValue($richText2);



            $sheet->getStyle("A" . ($currentRow + 4) . ":S" . ($currentRow + 3))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // Table Header
            $headerRow = $currentRow + 5;
            $sheet->mergeCells("A$headerRow:C$headerRow")->setCellValue("A$headerRow", "SERIAL NO");
            $sheet->mergeCells("D$headerRow:L$headerRow")->setCellValue("D$headerRow", "CHECK ITEMS");
            $sheet->mergeCells("M$headerRow:O$headerRow")->setCellValue("M$headerRow", "STATUS");
            $sheet->mergeCells("P$headerRow:S$headerRow")->setCellValue("P$headerRow", "REMARKS");

            $sheet->getStyle("A$headerRow:S$headerRow")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font' => ['bold' => true],
            ]);

            $inspectionRow = $headerRow + 1;
            $srNo = 1;
            $decodedData = json_decode($weeklyAmbulance->checklist, true);
            $checkItems = $decodedData['check_item'] ?? [];
            $statuses = $decodedData['status'] ?? [];
            $remarks = $decodedData['remarks'] ?? [];

            $srNo = 1;
            foreach ($checkItems as $groupId => $checkPoints) {
                $rowCount = count($checkPoints);
                $firstRowInGroup = true;

                foreach ($checkPoints as $checkPoint) {

                    if ($firstRowInGroup) {
                        $sheet->mergeCells("A$inspectionRow:A" . ($inspectionRow + $rowCount - 1))
                            ->setCellValue("A$inspectionRow", $srNo);

                        $sheet->mergeCells("B$inspectionRow:C" . ($inspectionRow + $rowCount - 1))
                            ->setCellValue("B$inspectionRow", getSubcategoryname($groupId));

                        $firstRowInGroup = false;
                        $srNo++;
                    }


                    $sheet->mergeCells("D$inspectionRow:L$inspectionRow")
                        ->setCellValue("D$inspectionRow", getSubcategoryDataname($checkPoint));




                    $statusIcon = '';
                    if (!empty($statuses[$checkPoint]) && $statuses[$checkPoint] == 'Ok') {
                        $statusIcon = '✓';
                    } elseif (!empty($statuses[$checkPoint]) && in_array($statuses[$checkPoint], ['Not-Ok', 'N/A'])) {
                        $statusIcon = 'X';
                    } else {
                        $statusIcon = '-';
                    }
                    $sheet->mergeCells("M$inspectionRow:O$inspectionRow")
                        ->setCellValue("M$inspectionRow", $statusIcon);


                    $sheet->mergeCells("P$inspectionRow:S$inspectionRow")
                        ->setCellValue("P$inspectionRow", $remarks[$checkPoint] ?? 'No Remarks');


                    $sheet->getStyle("A$inspectionRow:S$inspectionRow")->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ],
                        ],
                    ]);

                    $inspectionRow++;
                }
            }

            $row = $inspectionRow;

            $CreatorSignature = GetOHCSignature($weeklyAmbulance->created_by, $id, OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST);
            $VerifiedSignature = GetOHCSignature($weeklyAmbulance->verified_by, $id, OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST);
            $ApprovedSignature = GetOHCSignature($weeklyAmbulance->approved_by, $id, OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST);

            if (file_exists($CreatorSignature)) {
                $sheet->mergeCells("A$row:F" . ($row + 2));

                $drawing = new Drawing();
                $drawing->setName('Creator Signature');
                $drawing->setPath($CreatorSignature);
                $drawing->setCoordinates("A$row");
                $drawing->setOffsetX(100);
                $drawing->setOffsetY(5);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
                $sheet->getRowDimension($row + 2)->setRowHeight(40);
                // Label + Name
                $sheet->setCellValue("A" . ($row + 3), "Checked By: " . getUserName($weeklyAmbulance->created_by));
                $sheet->mergeCells("A" . ($row + 3) . ":F" . ($row + 3));

                $sheet->getStyle("A$row:F" . ($row + 3))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }

            if (file_exists($VerifiedSignature)) {
                $sheet->mergeCells("G$row:L" . ($row + 2));

                $drawing = new Drawing();
                $drawing->setName('Verified Signature');
                $drawing->setPath($VerifiedSignature);
                $drawing->setCoordinates("G$row");
                $drawing->setOffsetX(100);
                $drawing->setOffsetY(5);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
                $sheet->getRowDimension($row + 2)->setRowHeight(40);
                // Label + Name
                $sheet->setCellValue("G" . ($row + 3), "Verified By: " . getUserName($weeklyAmbulance->verified_by));
                $sheet->mergeCells("G" . ($row + 3) . ":L" . ($row + 3));

                $sheet->getStyle("G$row:L" . ($row + 3))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }

            if (file_exists($ApprovedSignature)) {
                $sheet->mergeCells("M$row:S" . ($row + 2));

                $drawing = new Drawing();
                $drawing->setName('Approved Signature');
                $drawing->setPath($ApprovedSignature);
                $drawing->setCoordinates("M$row");
                $drawing->setOffsetX(100);
                $drawing->setOffsetY(5);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
                $sheet->getRowDimension($row + 2)->setRowHeight(60);
                // Label + Name
                $sheet->setCellValue("M" . ($row + 3), "Approved By: " . getUserName($weeklyAmbulance->approved_by));
                $sheet->mergeCells("M" . ($row + 3) . ":S" . ($row + 3));

                $sheet->getStyle("M$row:S" . ($row + 3))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }




            $filename = 'Weekly Ambulance Inspection checklist.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            return back()->with('error', 'Excel Export Failed: ' . $e->getMessage());
        }
    }
}
