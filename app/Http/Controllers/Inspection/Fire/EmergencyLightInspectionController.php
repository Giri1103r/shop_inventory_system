<?php

namespace App\Http\Controllers\Inspection\Fire;

use Exception;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Master\Location;
use App\Models\Master\Department;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Inspection\Master\Shift;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\Master\Frequency;
use App\Mail\Inspection\Fire\FireInspection;
use App\Models\Inspection\Fire\EmergencyLightInspection;
use App\Models\Inspection\Fire\EmergencyLightInspectionDetails;
use App\Models\Inspection\Fire\FireStatusLog;
use App\Models\Inspection\Fire\FireFileUpload;
use App\Models\Inspection\Fire\FireExtinguisher;
use App\Models\Inspection\Fire\FireSignatureUpload;
use App\Models\Inspection\Fire\FireCheckListFollowUp;
use App\Models\Inspection\Fire\FireExtinguisherDetails;
use App\Models\Inspection\Fire\Master\ConditionLight;
use App\Models\Inspection\Fire\Master\FireStatus;
use App\Models\Inspection\Fire\Master\LightType;
use App\Models\Inspection\Fire\Master\PowerSuply;
use App\Models\Inspection\Fire\Master\Status;
use App\Models\Inspection\InspectionStaticDocno;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class EmergencyLightInspectionController extends Controller
{
    private $shift;
    private $location;
    private $unit;
    private $frequency;
    private $department;
    private $files;
    private $signature;
    private $statusLog;
    private $emergency_light;
    private $emergency_light_details;
    private $power_supply;
    private $fire_status;
    private $ligth_type;
    private $condition_light;
    private $checklist_follow;
    private $document_reference;
    public function __construct()
    {
        $this->department = new Department();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->unit = new Unit();
        $this->frequency = new Frequency();
        $this->files = new FireFileUpload();
        $this->signature = new FireSignatureUpload();
        $this->statusLog = new FireStatusLog();
        $this->power_supply = new PowerSuply();
        $this->fire_status = new FireStatus();
        $this->ligth_type = new LightType();
        $this->condition_light = new ConditionLight();
        $this->document_reference = new InspectionStaticDocno();
        $this->checklist_follow = new FireCheckListFollowUp();
        $this->emergency_light = new EmergencyLightInspection();
        $this->emergency_light_details = new EmergencyLightInspectionDetails();
    }
    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->emergency_light->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->inspection_id) . "' data-type = '1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->inspection_id) . "' data-type = '0'>In-Active</span>";
                            }
                            // }
                            return $text;
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('inspection_status', function ($row) {
                            $text = '';
                            switch ($row->inspection_status) {
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
                            $btn = '<a href="' . admin_url('fire/emergency-light-inspection/view/' . encryptId($row->inspection_id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            if ($row->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/emergency-light-inspection/verification/' . encryptId($row->inspection_id)) . '/ehs" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if (($row->inspection_status == WAITING_FOR_CAPA_ACTION || $row->inspection_status == L2_MANAGER_REJECTED || $row->inspection_status == EHS_OFFICER_REJECTED || $row->inspection_status == L1_MANAGER_REJECTED) && (CheckUserRole(ROLE_FIRE_ASSOCIATES) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/emergency-light-inspection/verification/' . encryptId($row->inspection_id)) . '/capa" class="" title="' . __('inspection.capa_action') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_CAPA_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/emergency-light-inspection/verification/' . encryptId($row->inspection_id)) . '/ehsVerify" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L1_VERIFICATION && (CheckUserRole(ROLE_L1_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/emergency-light-inspection/verification/' . encryptId($row->inspection_id)) . '/level-one-manager" class="" title="' . __('inspection.l1_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L2_VERIFICATION && (CheckUserRole(ROLE_L2_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/emergency-light-inspection/verification/' . encryptId($row->inspection_id)) . '/level-two-manager" class="" title="' . __('inspection.l2_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            $btn .= '<a href="' . admin_url('fire/emergency-light-inspection/exportViewPdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                    </a>';
                            $btn .= '<a href="' . admin_url('fire/emergency-light-inspection/exportViewExcel/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                   <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'inspection_status', 'issue_date'])
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
        $unit = $this->unit->getUnit();
        $frequency = $this->frequency->getFrequency();
        $shifts = $this->shift->getShiftname();
        $data = array(
            'units' => $unit,
            'frequency' => $frequency,
            'shifts' => $shifts,

        );
        return view('inspection.fire.emergency_light_inspection.list', $data);
    }
    public function Add(Request $request)
    {
        try {
            $location = $this->location->getLocationName();
            $unit = $this->unit->getUnit();
            $frequency = $this->frequency->getFrequency();
            $shifts = $this->shift->getShiftname();
            $department = $this->department->getdepartment();
            $condition_light = $this->condition_light->getConditionoflight();
            $ligth_type = $this->ligth_type->gettypeoflight();
            $fire_status = $this->fire_status->getStatus();
            $power_supply = $this->power_supply->getPowersupply();
            $document_no = $this->document_reference->selectUsingName('EmergencyLightInspection');
            $data = array(
                'locations' => $location,
                'units' => $unit,
                'frequency' => $frequency,
                'department' => $department,
                'shifts' => $shifts,
                'conditionLight' => $condition_light,
                'lightType' => $ligth_type,
                'fireStatus' => $fire_status,
                'powerSupply' => $power_supply,
                'document_no' => $document_no,
            );

            return view('inspection.fire.emergency_light_inspection.add', $data);
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/emergency-light-inspection/list'));
        }
    }

    public function GetDepartment(Request $request)
    {
        try {
            $department = $this->department->getdepartment();
            $departments = [];

            foreach ($department as $department) {
                $departments[] = [
                    'id' => encryptId($department->id),
                    'department_name' => $department->department_name,
                ];
            }

            return response()->json($departments);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['error' => 'Something went wrong !'], 406);
        }
    }

    public function Store(Request $request)
    {
        try {

            $inspection = $this->emergency_light->store();
            $inspection_type =  EMERGENCY_LIGHT_INSPECTION;
            $id = $inspection->id;

            $emergency_light_details = $this->emergency_light_details->store($id);
            $inspection_file = $this->files->file_upload($inspection_type, $id);



            $signature_update = $this->signature->CheckedBySignature($id, $inspection_type);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'FIRE INSPECTION';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Hooter Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('fire/emergency-light-inspection/view/' . encryptId($id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Hooter Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('fire/emergency-light-inspection/verification/' . encryptId($id) . '/ehs');
                $details = array(
                    'fire_type' => 'Hooter Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' =>  EMERGENCY_LIGHT_INSPECTION,
                'inspection_id' => $id,
                'from_status' => 0,
                'to_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'created_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);
            Session::flash('flash', 'Your data added successfully');
            return redirect(admin_url('fire/emergency-light-inspection/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/emergency-light-inspection/list'));
        }
    }

    public function View(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_type =  EMERGENCY_LIGHT_INSPECTION;

            $inspection = $this->emergency_light->selectOne($id);
            $inspection_details = $this->emergency_light_details->selectOne($inspection->id);
            $inspection_image = $this->files->GetFile($inspection_type, $id);
            $status_log = $this->statusLog->selectOne($id,  EMERGENCY_LIGHT_INSPECTION);
            $document_no = $this->document_reference->selectUsingName('EmergencyLightInspection');
            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'inspection_image' => $inspection_image,
                'status_log' => $status_log,
                'document_no' => $document_no,
            );
            return view('inspection.fire.emergency_light_inspection.view', $data);
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/emergency-light-inspection/list'));
        }
    }

    public function Approvals(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_type =  EMERGENCY_LIGHT_INSPECTION;
            $document_no = $this->document_reference->selectUsingName('EmergencyLightInspection');
            $inspection = $this->emergency_light->selectOne($id);
            $inspection_details = $this->emergency_light_details->selectOne($inspection->id);
            $inspection_image = $this->files->GetFile($inspection_type, $id);
            $status_log = $this->statusLog->selectOne($id,  EMERGENCY_LIGHT_INSPECTION);

            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'inspection_image' => $inspection_image,
                'document_no' => $document_no,
                'status_log' => $status_log,
            );
            return view('inspection.fire.emergency_light_inspection.approve', $data);
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/emergency-light-inspection/list'));
        }
    }

    public function EHSOfficerSubmit(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $inspection_updates = $this->emergency_light->EHSOfficerUpdate($id);
            $signature_update = $this->signature->signatureUpload(EMERGENCY_LIGHT_INSPECTION);
            $inspection_details = $this->emergency_light->selectOne($id);
            if ($request->is_passed == 1) {
                $message = 'Hooter Inspeciton Approved Successfully';
                $web_link =   admin_url('fire/emergency-light-inspection/verification/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
            } else {
                $message = 'Inspection Recommended for the CAPA Action';
                $web_link =   admin_url('fire/emergency-light-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = WAITING_FOR_CAPA_ACTION;
            }
            $userIds = [
                'users' => $inspection_details->created_by,
            ];
            $mailsubject = 'FIRE INSPECTION';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => $message,
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_details->id,
                    'module' => 1,
                )),
                'web_link' =>  $web_link,
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = $message;
            $user = $inspection_details->created_by;
            $email_id = getUseremail($user);
            $url = admin_url('fire/emergency-light-inspection/verification/' . encryptId($id) . '/capa');
            $details = array(
                'fire_type' => 'Emergency Light Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new FireInspection($details));

            $insert_array = [
                'type' =>  EMERGENCY_LIGHT_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/emergency-light-inspection/list'));
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something Went Wrong!');
            return redirect(admin_url('fire/emergency-light-inspection/list'));
        }
    }

    public function CAPASubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $safety_gallery_inspection = $this->emergency_light->capaSubmit($id);
            $inspection_details = $this->emergency_light->selectOne($id);
            $signature_update = $this->signature->signatureUpload(EMERGENCY_LIGHT_INSPECTION);
            $ehsOfficers = $inspection_details->verified_by;
            $userIds = [
                'users' => $ehsOfficers,
            ];
            $mailsubject = 'Fire Inspection';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "CAPA Action Completed by the Fire Associates",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_details->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('fire/emergency-light-inspection/verification/' . encryptId($inspection_details->id)) . '/ehsVerify',
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $user = $inspection_details->verified_by;
            $email_id = getUseremail($user);
            $url = admin_url('fire/emergency-light-inspection/verification/' . encryptId($id) . '/ehs');
            $details = array(
                'fire_type' => 'Emergency Light Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => 'CAPA Action Completed by the Fire Associates',
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new FireInspection($details));

            $insert_array = [
                'type' =>  EMERGENCY_LIGHT_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_ACTION,
                'to_status' => WAITING_FOR_CAPA_VERIFICATION,
                'created_by' => Auth::id(),
                'remarks' => $request->capa_remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/emergency-light-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/emergency-light-inspection/list'));
        }
    }

    public function CAPAVerifySubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->remarks;
            $safety_gallery_inspection = $this->emergency_light->capaVerifySubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(EMERGENCY_LIGHT_INSPECTION);
            $inspection_details = $this->emergency_light->selectOne($id);
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('fire/emergency-light-inspection/verification/' . encryptId($inspection_details->id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = WAITING_FOR_L1_VERIFICATION;
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('fire/emergency-light-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = EHS_OFFICER_REJECTED;
            }

            $mailsubject = 'FIRE INSPECTION';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 3,
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
            notificationSave($notificationData);

            foreach ($users as $user) {
                $title = $message;
                $email_id = getUseremail($user);
                $url = $web_link;
                $details = array(
                    'fire_type' => 'Emergency Light Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' =>  EMERGENCY_LIGHT_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/emergency-light-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/emergency-light-inspection/list'));
        }
    }

    public function levelOneManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_one_manager;
            $safety_gallery_inspection = $this->emergency_light->levelOneManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(EMERGENCY_LIGHT_INSPECTION);
            $inspection_details = $this->emergency_light->selectOne($id);
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('fire/emergency-light-inspection/verification/' . encryptId($inspection_details->id) . '/level-two-manager');
                $user = GetLevelTwoManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by], [$inspection_details->verified_by]);
                $to_status = WAITING_FOR_L2_VERIFICATION;
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/emergency-light-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = L1_MANAGER_REJECTED;
            }

            $mailsubject = 'FIRE INSPECTION';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 3,
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
            notificationSave($notificationData);

            foreach ($users as $user) {
                $title = $message;
                $email_id = getUseremail($user);
                $url = $web_link;
                $details = array(
                    'fire_type' => 'Emergency Light Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' =>  EMERGENCY_LIGHT_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L1_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_one_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/emergency-light-inspection/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/emergency-light-inspection/list'));
        }
    }

    public function levelTwoManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_two_manager;
            $safety_gallery_inspection = $this->emergency_light->levelTwoManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(EMERGENCY_LIGHT_INSPECTION);
            $inspection_details = $this->emergency_light->selectOne($id);
            if ($status == 1) {
                $message = 'Hooter Inspeciton Approved Successfully!';
                $web_link =   admin_url('fire/emergency-light-inspection/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by], [$inspection_details->l2_manager_verified_by]);
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/emergency-light-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = L2_MANAGER_REJECTED;
            }

            $mailsubject = 'FIRE INSPECTION';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 3,
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
            notificationSave($notificationData);
            foreach ($users as $user) {
                $title = $message;
                $email_id = getUseremail($user);
                $url = $web_link;
                $details = array(
                    'fire_type' => 'Emergency Light Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' =>  EMERGENCY_LIGHT_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L2_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_two_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/emergency-light-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/emergency-light-inspection/list'));
        }
    }



    public function ExportExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $data = $this->emergency_light->selectOne($id);

            $emergency_light_details = $this->emergency_light_details->selectOne($id);


            $logoPath = public_path('assets/images/logo-dark.png');
            if (file_exists($logoPath)) {
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Company Logo');
                $drawing->setPath($logoPath);
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setWidth(60);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
            }

            // Title
            $sheet->mergeCells("C1:J3");
            $sheet->setCellValue("C1", "EMERGENCY LIGHT INSPECTION CHECKLIST\nPN INTERNATIONAL PVT. LTD.");
            $sheet->getStyle("C1")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FF0000']]
            ]);

            // Document metadata
            $sheet->mergeCells("K1:L1")->setCellValue("K1", "Doc. No.");
            $sheet->mergeCells("K2:L2")->setCellValue("K2", "Issue Dt.");
            $sheet->mergeCells("K3:L3")->setCellValue("K3", "Rev. & Dt.");

            $sheet->setCellValue("M1", $data->doc_no);
            $sheet->setCellValue("M2", $data->issue_date);
            $sheet->setCellValue("M3", $data->revision_date);

            $sheet->getStyle("C1:M3")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '000000']]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getColumnDimension('K')->setWidth(15);
            $sheet->getColumnDimension('M')->setWidth(20);

            // Inspection info row

            $date_of_inspection = Displaydateformat($data->date_of_inspection);
            $location = getLocationname($data->location);
            $shift = getShift($data->shift);


            $sheet->mergeCells("A4:C4")->setCellValue("A4", "DATE OF INSPECTION :-");
            $sheet->setCellValue("D4", $date_of_inspection);

            $sheet->mergeCells("F4:H4")->setCellValue("F4", "LOCATION :-");
            $sheet->setCellValue("I4", $location);

            $sheet->mergeCells("K4:L4")->setCellValue("K4", "SHIFT :-");
            $sheet->setCellValue("M4", $shift);

            $sheet->getStyle("A4:M4")->applyFromArray([
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '000000']],
                    'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '000000']],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            $nextDue = Displaydateformat($data->next_due);
            $unitName = getUnitname($data->unit);
            $frequency = getFrequency($data->frequency);

            // Now set them to the cells
            // NEXT DUE
            $sheet->mergeCells("A5:C5")->setCellValue("A5", "NEXT DUE ON :-");
            $sheet->mergeCells("D5:D5")->setCellValue("D5", $nextDue);

            // UNIT
            $sheet->mergeCells("E5:F5")->setCellValue("E5", "UNIT :-");
            $sheet->mergeCells("G5:I5")->setCellValue("G5", $unitName);

            // FREQUENCY
            $sheet->mergeCells("J5:K5")->setCellValue("J5", "FREQUENCY :-");
            $sheet->mergeCells("L5:M5")->setCellValue("L5", $frequency);
            $sheet->getStyle("A5:M5")->applyFromArray([
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '000000']],
                    'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '000000']],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Table headers
            $sheet->mergeCells("A6:A8")->setCellValue("A6", "SERIAL NO");

            $sheet->mergeCells("B6:B8")->setCellValue("B6", "DEPARTMENT");
            $sheet->mergeCells("C6:C8")->setCellValue("C6", "LOCATION");
            $sheet->mergeCells("D6:D8")->setCellValue("D6", "EMERGENCY LIGHT NUMBER");

            $sheet->mergeCells("E6:K6")->setCellValue("E6", "CHECK ITEMS");
            $sheet->mergeCells("E7:G7")->setCellValue("E7", "DESCRIPTION");
            $sheet->mergeCells("H7:K7")->setCellValue("H7", "CONDITION OF LIGHT");

            $sheet->setCellValue("E8", "TYPE");
            $sheet->setCellValue("F8", "CAPACITY (VOLTS)");
            $sheet->setCellValue("G8", "QUANTITY");
            $sheet->setCellValue("H8", "LIGHT CONDITION");
            $sheet->setCellValue("I8", "SWITCH CONDITION");
            $sheet->setCellValue("J8", "POWER SUPPLY");
            $sheet->setCellValue("K8", "STATUS");

            $sheet->mergeCells("L6:M8")->setCellValue("L6", "REMARKS");

            $row = 9;

            foreach ($emergency_light_details as $index => $detail) {
                $sheet->setCellValue("A$row", $detail->sr_no ?? '');
                $sheet->setCellValue("B$row", getDepartment($detail->department) ?? '');
                $sheet->setCellValue("C$row", $detail->location ?? '');
                $sheet->setCellValue("D$row", $detail->emergency_of_light ?? '');

                // Description
                $sheet->setCellValue("E$row", GetTypeofLight($detail->type_of_light) ?? '');
                $sheet->setCellValue("F$row", $detail->capacity ?? '');
                $sheet->setCellValue("G$row", $detail->quantity ?? '');

                // Condition of light
                $sheet->setCellValue("H$row", $detail->light_condition ?? '');
                switch ($detail->light_condition) {
                    case 1:
                        $lightConditionText = 'Good';
                        break;
                    case 2:
                        $lightConditionText = 'Fair';
                        break;
                    case 3:
                        $lightConditionText = 'Poor';
                        break;
                    default:
                        $lightConditionText = '';
                }

                $sheet->setCellValue("H$row", $lightConditionText);
                switch ($detail->switch_condition) {
                    case 1:
                        $switchConditionText = 'Good';
                        break;
                    case 2:
                        $switchConditionText = 'Fair';
                        break;
                    case 3:
                        $switchConditionText = 'Poor';
                        break;
                    default:
                        $switchConditionText = '';
                }

                $sheet->setCellValue("I$row", $switchConditionText);
                $sheet->setCellValue("J$row", GetPowerSuply($detail->power_supply) ?? '');
                $sheet->setCellValue("K$row", getFireLightInspectionStatus($detail->fire_status) ?? '');

                $sheet->setCellValue("L$row", $detail->remarks ?? '');

                // Optionally merge L and M columns if needed
                $sheet->mergeCells("L$row:M$row");

                // Optional: Apply border or alignment styling to each row
                $sheet->getStyle("A$row:M$row")->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '000000']],
                        'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '000000']],
                    ],
                ]);

                $row++;
            }


            foreach (["A6:D8", "E6:K6", "E7:K7", "E8:K8", "L6:M8"] as $range) {
                $sheet->getStyle($range)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '000000']]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }


            $fileName = 'emergency_light_inspection_' . date('Ymd_His') . '.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/emergency-light-inspection/list'));
        }
    }


    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->emergency_light->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }
            $header = [
                __("common.sno"),
                'Document Number',
                'Issue Date',
                'Revision Date',
                __("inspection.inspection_status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Emergency Light Inspection",
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

            $view = view('inspection.fire.pdf.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Emergency Light Inspection.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/emergency-light-inspection/list'));
        }
    }

    public function ExportViewPDF(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $status_log = $this->statusLog->selectOne($id, EMERGENCY_LIGHT_INSPECTION);
                $emergency_light = $this->emergency_light->selectOne($id);
                $inspection = $this->emergency_light_details->selectOne($emergency_light->id);
                $document_no = $this->document_reference->selectUsingName('EmergencyLightInspection');
                $data = [
                    'status_log' => $status_log,
                    'emergency_light' => $emergency_light,
                    'pagetitle' => "Emergency Light Inspection",
                    'inspection' => $inspection,
                    'document_no' => $document_no,
                ];
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

            $html = view('inspection.fire.emergency_light_inspection.viewPdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Emergency light inspection.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/emergency-light-inspection/list'));
        }
    }
}
