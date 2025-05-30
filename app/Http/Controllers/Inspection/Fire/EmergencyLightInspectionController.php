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
use PhpOffice\PhpSpreadsheet\RichText\RichText;

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

        $location = $this->location->getLocationName();
        $unit = $this->unit->getUnit();
        $frequency = $this->frequency->getFrequency();
        $shifts = $this->shift->getShiftname();
        $data = array(
            'locations' => $location,
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
            report($ex);
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
            $mailsubject = 'Fire Emergency Light Inspection';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Emergency Light Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('fire/emergency-light-inspection/view/' . encryptId($id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Emergency Light Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('fire/emergency-light-inspection/verification/' . encryptId($id) . '/ehs');
                $details = array(
                    'fire_type' => 'Emergency Light Inspection',
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

            if ($inspection->observation == 1) {
                Session::flash('success', 'Your data added successfully');
                return redirect(admin_url('fire/checklist-observation/add/' . encryptId($inspection_type) . '/' . encryptId($id)));
            } else {
                Session::flash('success', 'Your data added successfully');
                return redirect(admin_url('fire/emergency-light-inspection/list'));
            }
            Session::flash('success', 'Your data added successfully');
            return redirect(admin_url('fire/emergency-light-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
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
            report($ex);
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
            report($ex);
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
                $message = 'Emergency Light Inspeciton Approved Successfully';
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
            $mailsubject = 'Fire Emergency Light Inspection';
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
            $mailsubject = 'Fire Emergency Light Inspection';
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
                $users = [$inspection_details->created_by];
                $to_status = EHS_OFFICER_REJECTED;
            }

            $mailsubject = 'Fire Emergency Light Inspection';
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
                $users = [$inspection_details->created_by];
                $to_status = L1_MANAGER_REJECTED;
            }

            $mailsubject = 'Fire Emergency Light Inspection';
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
            report($ex);
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
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/emergency-light-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = L2_MANAGER_REJECTED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
            }

            $mailsubject = 'Fire Emergency Light Inspection';
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



    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $data = $this->emergency_light->selectOne($id);
            $inspection_type =  EMERGENCY_LIGHT_INSPECTION;
            $emergency_light_details = $this->emergency_light_details->selectOne($id);
            $document_no = $this->document_reference->selectUsingName('EmergencyLightInspection');
            $inspection_image = $this->files->GetFile($inspection_type, $id);
            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $row = 1;
            $currentRow = $row;
            $logoPath = public_path('assets/images/logo-dark.png');
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
            $sheet->mergeCells("G{$currentRow}:M" . ($currentRow + 2));
            $sheet->setCellValue("G{$currentRow}", "EMERGENCY LIGHT INSPECTION CHECKLIST");
            $sheet->getStyle("G{$currentRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $sheet->mergeCells("N$currentRow:P$currentRow")->setCellValue("N$currentRow", 'Doc. No.');
            $sheet->mergeCells("N" . ($currentRow + 1) . ":P" . ($currentRow + 1))->setCellValue("N" . ($currentRow + 1), 'Issue Dt.');
            $sheet->mergeCells("N" . ($currentRow + 2) . ":P" . ($currentRow + 2))->setCellValue("N" . ($currentRow + 2), 'Rev. & Dt.');

            $sheet->mergeCells("Q$currentRow:U$currentRow")->setCellValue("Q$currentRow", $document_no->doc_no);
            $sheet->mergeCells("Q" . ($currentRow + 1) . ":U" . ($currentRow + 1))->setCellValue("Q" . ($currentRow + 1), Displaydateformat($document_no->issue_date));
            $sheet->mergeCells("Q" . ($currentRow + 2) . ":U" . ($currentRow + 2))->setCellValue("Q" . ($currentRow + 2), $document_no->rev_dt);

            $sheet->getStyle("N$currentRow:U" . ($currentRow + 2))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font' => ['bold' => true],
            ]);

            $sheet->mergeCells("A" . ($currentRow + 3) . ":G" . ($currentRow + 3));
            $richText1 = new RichText();
            $richText1->createTextRun(' DATE OF INSPECTION:- ')->getFont()->setBold(true);
            $richText1->createText(Displaydateformat($data->date_of_inspection));
            $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

            $sheet->mergeCells("H" . ($currentRow + 3) . ":N" . ($currentRow + 3));
            $richText2 = new RichText();
            $richText2->createTextRun('LOCATION :-  ')->getFont()->setBold(true);
            $richText2->createText(getLocationname($data->location));
            $sheet->getCell("H" . ($currentRow + 3))->setValue($richText2);

            $sheet->mergeCells("O" . ($currentRow + 3) . ":U" . ($currentRow + 3));
            $richText2 = new RichText();
            $richText2->createTextRun('SHIFT :-  ')->getFont()->setBold(true);
            $richText2->createText(getShift($data->shift));
            $sheet->getCell("O" . ($currentRow + 3))->setValue($richText2);

            $sheet->getStyle("A" . ($currentRow + 3) . ":U" . ($currentRow + 3))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells("A" . ($currentRow + 4) . ":G" . ($currentRow + 4));
            $richText1 = new RichText();
            $richText1->createTextRun(' NEXT DUE ON :- ')->getFont()->setBold(true);
            $richText1->createText(Displaydateformat($data->next_due));
            $sheet->getCell("A" . ($currentRow + 4))->setValue($richText1);

            $sheet->mergeCells("H" . ($currentRow + 4) . ":N" . ($currentRow + 4));
            $richText2 = new RichText();
            $richText2->createTextRun('UNIT :-  ')->getFont()->setBold(true);
            $richText2->createText(getUnitname($data->unit));
            $sheet->getCell("H" . ($currentRow + 4))->setValue($richText2);

            $sheet->mergeCells("O" . ($currentRow + 4) . ":U" . ($currentRow + 4));
            $richText2 = new RichText();
            $richText2->createTextRun('FREQUENCY :  ')->getFont()->setBold(true);
            $richText2->createText(getFrequencyname($data->frequency));
            $sheet->getCell("O" . ($currentRow + 4))->setValue($richText2);

            $sheet->getStyle("A" . ($currentRow + 4) . ":U" . ($currentRow + 4))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $headerRow = $currentRow + 5;
            $secondRow = $headerRow + 1;
            $thirdRow = $headerRow + 2;

            // First-level vertical headers
            $sheet->mergeCells("A$headerRow:A$thirdRow")->setCellValue("A$headerRow", "SERIAL NO");
            $sheet->mergeCells("B$headerRow:B$thirdRow")->setCellValue("B$headerRow", "DEPARTMENT");
            $sheet->mergeCells("C$headerRow:C$thirdRow")->setCellValue("C$headerRow", "LOCATION");
            $sheet->mergeCells("D$headerRow:D$thirdRow")->setCellValue("D$headerRow", "EMERGENCY LIGHT NUMBER");

            // First-level group header
            $sheet->mergeCells("E$headerRow:R$headerRow")->setCellValue("E$headerRow", "CHECK ITEMS");

            // Second-level group headers
            $sheet->mergeCells("E$secondRow:J$secondRow")->setCellValue("E$secondRow", "DESCRIPTION");
            $sheet->mergeCells("K$secondRow:R$secondRow")->setCellValue("K$secondRow", "CONDITION OF LIGHT");

            // Third-level leaf headers (each spanning 2 columns horizontally)
            $sheet->mergeCells("E$thirdRow:F$thirdRow")->setCellValue("E$thirdRow", "TYPE");
            $sheet->mergeCells("G$thirdRow:H$thirdRow")->setCellValue("G$thirdRow", "CAPACITY (VOLTS)");
            $sheet->mergeCells("I$thirdRow:J$thirdRow")->setCellValue("I$thirdRow", "QUANTITY");
            $sheet->mergeCells("K$thirdRow:L$thirdRow")->setCellValue("K$thirdRow", "LIGHT CONDITION");
            $sheet->mergeCells("M$thirdRow:N$thirdRow")->setCellValue("M$thirdRow", "SWITCH CONDITION");
            $sheet->mergeCells("O$thirdRow:P$thirdRow")->setCellValue("O$thirdRow", "POWER SUPPLY");

            // Final vertical column
            // $sheet->mergeCells("Q$headerRow:R$thirdRow")->setCellValue("Q$thirdRow", "STATUS");
            $sheet->mergeCells("Q$thirdRow:R$thirdRow")->setCellValue("Q$thirdRow", "STATUS");

            $sheet->mergeCells("S$headerRow:U$thirdRow")->setCellValue("S$headerRow", "REMARKS");


            $sheet->getStyle("A$headerRow:U$thirdRow")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ],
                'font' => ['bold' => true],
            ]);


            // Apply border and alignment styling
            $sheet->getStyle("A$headerRow:S" . ($headerRow + 2))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'font' => ['bold' => true],
            ]);

            $dataStartRow = $thirdRow + 1;

            foreach ($emergency_light_details as $index => $detail) {
                $row = $dataStartRow;

                $sheet->setCellValue("A$row", $detail->sr_no ?? '');
                $sheet->setCellValue("B$row", getDepartment($detail->department) ?? '');
                $sheet->setCellValue("C$row", $detail->location ?? '');
                $sheet->setCellValue("D$row", $detail->emergency_of_light ?? '');

                // Merge cells for consistency with header and set value in the first column
                $sheet->mergeCells("E$row:F$row")->setCellValue("E$row", GetTypeofLight($detail->type_of_light) ?? '');
                $sheet->mergeCells("G$row:H$row")->setCellValue("G$row", $detail->capacity ?? '');
                $sheet->mergeCells("I$row:J$row")->setCellValue("I$row", $detail->quantity ?? '');
                $sheet->mergeCells("K$row:L$row")->setCellValue("K$row", getLightCondition($detail->light_condition ?? ''));
                $sheet->mergeCells("M$row:N$row")->setCellValue("M$row", getLightCondition($detail->switch_condition));
                $sheet->mergeCells("O$row:P$row")->setCellValue("O$row", GetPowerSuply($detail->power_supply) ?? '');
                $sheet->mergeCells("Q$row:R$row")->setCellValue("Q$row", getFireLightInspectionStatus($detail->fire_status) ?? '');
                $sheet->mergeCells("S$row:U$row")->setCellValue("S$row", $detail->remarks ?? '');

                // Apply alignment and borders if needed
                $sheet->getStyle("A$row:U$row")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ]
                ]);
                $dataStartRow++;
            }
            $row = $dataStartRow;

            $signatureRowStart = $dataStartRow;
            $sheet->getRowDimension($signatureRowStart)->setRowHeight(30);

            // Prepared By
            $sheet->mergeCells("A{$signatureRowStart}:H{$signatureRowStart}");
            $sheet->getStyle("A{$signatureRowStart}:H{$signatureRowStart}")->applyFromArray([
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);

            $richText = new RichText();
            $name = getUsername($data->created_by);

            if (!empty($name)) {
                $richText->createTextRun("Prepared By :" . $name)->getFont()->setBold(true);
            } else {
                $richText->createTextRun("Inspection has not been Prepared Yet")->getFont()->setBold(true);
            }

            $sheet->getCell("A{$signatureRowStart}")->setValue($richText);

            // Verified By
            $sheet->mergeCells("I{$signatureRowStart}:N{$signatureRowStart}");
            $sheet->getStyle("I{$signatureRowStart}:N{$signatureRowStart}")->applyFromArray([
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);

            $richText = new RichText();
            $name = getUsername($data->verified_by);

            if (!empty($name)) {
                $richText->createTextRun("Verified By :" . $name)->getFont()->setBold(true);
            } else {
                $richText->createTextRun("Inspection has not been Verified Yet")->getFont()->setBold(true);
            }

            $sheet->getCell("I{$signatureRowStart}")->setValue($richText);

            // Approved By
            $sheet->mergeCells("O{$signatureRowStart}:U{$signatureRowStart}");
            $sheet->getStyle("O{$signatureRowStart}:U{$signatureRowStart}")->applyFromArray([
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);
            $richText = new RichText();
            $name = getUsername($data->approved_by);

            if (!empty($name)) {
                $richText->createTextRun("Approved By :" . $name)->getFont()->setBold(true);
            } else {
                $richText->createTextRun("Inspection has not been Approved Yet")->getFont()->setBold(true);
            }

            $sheet->getCell("O{$signatureRowStart}")->setValue($richText);

            // $CreatorSignature = GetSignature($data->created_by, $id, EMERGENCY_LIGHT_INSPECTION);
            // $VerifiedSignature = GetSignature($data->verified_by, $id, EMERGENCY_LIGHT_INSPECTION);
            // $ApprovedSignature = GetSignature($data->approved_by, $id, EMERGENCY_LIGHT_INSPECTION);

            // $sheet->mergeCells("A$row:H" . ($row + 2));
            // if (file_exists($CreatorSignature)) {

            //     $drawing = new Drawing();
            //     $drawing->setName('Creator Signature');
            //     $drawing->setPath($CreatorSignature);
            //     $drawing->setCoordinates("C$row");
            //     $drawing->setOffsetX(100);
            //     $drawing->setOffsetY(5);
            //     $drawing->setWidth(70);
            //     $drawing->setHeight(70);
            //     $drawing->setWorksheet($sheet);
            //     $sheet->getRowDimension($row + 2)->setRowHeight(40);
            // } else {
            //     $sheet->setCellValue("A{$row}", "Inspection has not been  started");
            // }
            // // Label + Name
            // $sheet->setCellValue("A" . ($row + 3), "Checked By: " . getUserName($data->created_by));
            // $sheet->mergeCells("A" . ($row + 3) . ":H" . ($row + 3));

            // $sheet->getStyle("A$row:H" . ($row + 3))->applyFromArray([
            //     'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            //     'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            // ]);

            // $sheet->mergeCells("I$row:N" . ($row + 2));
            // if (file_exists($VerifiedSignature)) {

            //     $drawing = new Drawing();
            //     $drawing->setName('Verified Signature');
            //     $drawing->setPath($VerifiedSignature);
            //     $drawing->setCoordinates("K$row");
            //     $drawing->setOffsetX(100);
            //     $drawing->setOffsetY(5);
            //     $drawing->setWidth(70);
            //     $drawing->setHeight(70);
            //     $drawing->setWorksheet($sheet);
            //     $sheet->getRowDimension($row + 2)->setRowHeight(40);
            // } else {
            //     $sheet->setCellValue("I{$row}", "Inspection has not been  started");
            // }
            // // Label + Name
            // $sheet->setCellValue("I" . ($row + 3), "Verified By: " . getUserName($data->verified_by));
            // $sheet->mergeCells("I" . ($row + 3) . ":N" . ($row + 3));

            // $sheet->getStyle("I$row:N" . ($row + 3))->applyFromArray([
            //     'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            //     'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            // ]);

            // $sheet->mergeCells("O$row:U" . ($row + 2));
            // if (file_exists($ApprovedSignature)) {

            //     $drawing = new Drawing();
            //     $drawing->setName('Approved Signature');
            //     $drawing->setPath($ApprovedSignature);
            //     $drawing->setCoordinates("O$row");
            //     $drawing->setOffsetX(100);
            //     $drawing->setOffsetY(5);
            //     $drawing->setWidth(70);
            //     $drawing->setHeight(70);
            //     $drawing->setWorksheet($sheet);
            //     $sheet->getRowDimension($row + 2)->setRowHeight(60);
            // } else {
            //     $sheet->setCellValue("O{$row}", "Inspection has not been  started");
            // }
            // // Label + Name
            // $sheet->setCellValue("O" . ($row + 3), "Approved By: " . getUserName($data->approved_by));
            // $sheet->mergeCells("O" . ($row + 3) . ":U" . ($row + 3));

            // $sheet->getStyle("O$row:U" . ($row + 3))->applyFromArray([
            //     'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            //     'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            // ]);

            $fileName = 'emergency_light_inspection.xlsx';
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
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }
            foreach ($allData as $details) {
                $document_no = $this->document_reference->SelectOne($details->document_reference_id);
            }


            $data = array(

                'content' => $allData,
                'document_no' => $document_no,
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

            $view = view('inspection.fire.emergency_light_inspection.pdf', $data);
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

    public function ExportExcel(Request $request)
    {
        try {

            $allData = $this->emergency_light->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $row = 1;
            $currentRow = $row;
            foreach ($allData as $details) {
                $currentRow = $row;
                $document_no = $this->document_reference->SelectOne($details->document_reference_id);
                $data = $this->emergency_light->selectOne($details->id);
                $inspection_type =  EMERGENCY_LIGHT_INSPECTION;
                $emergency_light_details = $this->emergency_light_details->selectOne($details->id);

                $inspection_image = $this->files->GetFile($inspection_type, $details->id);

                $logoPath = public_path('assets/images/logo-dark.png');
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
                $sheet->mergeCells("G{$currentRow}:M" . ($currentRow + 2));
                $sheet->setCellValue("G{$currentRow}", "EMERGENCY LIGHT INSPECTION CHECKLIST");
                $sheet->getStyle("G{$currentRow}:M{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->mergeCells("N$currentRow:P$currentRow")->setCellValue("N$currentRow", 'Doc. No.');
                $sheet->mergeCells("N" . ($currentRow + 1) . ":P" . ($currentRow + 1))->setCellValue("N" . ($currentRow + 1), 'Issue Dt.');
                $sheet->mergeCells("N" . ($currentRow + 2) . ":P" . ($currentRow + 2))->setCellValue("N" . ($currentRow + 2), 'Rev. & Dt.');

                $sheet->mergeCells("Q$currentRow:U$currentRow")->setCellValue("Q$currentRow", $document_no->doc_no);
                $sheet->mergeCells("Q" . ($currentRow + 1) . ":U" . ($currentRow + 1))->setCellValue("Q" . ($currentRow + 1), Displaydateformat($document_no->issue_date));
                $sheet->mergeCells("Q" . ($currentRow + 2) . ":U" . ($currentRow + 2))->setCellValue("Q" . ($currentRow + 2), $document_no->rev_dt);

                $sheet->getStyle("N$currentRow:U" . ($currentRow + 2))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'font' => ['bold' => true],
                ]);

                $sheet->mergeCells("A" . ($currentRow + 3) . ":G" . ($currentRow + 3));
                $richText1 = new RichText();
                $richText1->createTextRun(' DATE OF INSPECTION:- ')->getFont()->setBold(true);
                $richText1->createText(Displaydateformat($data->date_of_inspection));
                $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

                $sheet->mergeCells("H" . ($currentRow + 3) . ":N" . ($currentRow + 3));
                $richText2 = new RichText();
                $richText2->createTextRun('LOCATION :-  ')->getFont()->setBold(true);
                $richText2->createText(getLocationname($data->location));
                $sheet->getCell("H" . ($currentRow + 3))->setValue($richText2);

                $sheet->mergeCells("O" . ($currentRow + 3) . ":U" . ($currentRow + 3));
                $richText2 = new RichText();
                $richText2->createTextRun('SHIFT :-  ')->getFont()->setBold(true);
                $richText2->createText(getShift($data->shift));
                $sheet->getCell("O" . ($currentRow + 3))->setValue($richText2);

                $sheet->getStyle("A" . ($currentRow + 3) . ":U" . ($currentRow + 3))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->mergeCells("A" . ($currentRow + 4) . ":G" . ($currentRow + 4));
                $richText1 = new RichText();
                $richText1->createTextRun(' NEXT DUE ON :- ')->getFont()->setBold(true);
                $richText1->createText(Displaydateformat($data->next_due));
                $sheet->getCell("A" . ($currentRow + 4))->setValue($richText1);

                $sheet->mergeCells("H" . ($currentRow + 4) . ":N" . ($currentRow + 4));
                $richText2 = new RichText();
                $richText2->createTextRun('UNIT :-  ')->getFont()->setBold(true);
                $richText2->createText(getUnitname($data->unit));
                $sheet->getCell("H" . ($currentRow + 4))->setValue($richText2);

                $sheet->mergeCells("O" . ($currentRow + 4) . ":U" . ($currentRow + 4));
                $richText2 = new RichText();
                $richText2->createTextRun('FREQUENCY :  ')->getFont()->setBold(true);
                $richText2->createText(getFrequencyname($data->frequency));
                $sheet->getCell("O" . ($currentRow + 4))->setValue($richText2);

                $sheet->getStyle("A" . ($currentRow + 4) . ":U" . ($currentRow + 4))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $headerRow = $currentRow + 5;
                $secondRow = $headerRow + 1;
                $thirdRow = $headerRow + 2;

                // First-level vertical headers
                $sheet->mergeCells("A$headerRow:A$thirdRow")->setCellValue("A$headerRow", "SERIAL NO");
                $sheet->mergeCells("B$headerRow:B$thirdRow")->setCellValue("B$headerRow", "DEPARTMENT");
                $sheet->mergeCells("C$headerRow:C$thirdRow")->setCellValue("C$headerRow", "LOCATION");
                $sheet->mergeCells("D$headerRow:D$thirdRow")->setCellValue("D$headerRow", "EMERGENCY LIGHT NUMBER");

                // First-level group header
                $sheet->mergeCells("E$headerRow:R$headerRow")->setCellValue("E$headerRow", "CHECK ITEMS");

                // Second-level group headers
                $sheet->mergeCells("E$secondRow:J$secondRow")->setCellValue("E$secondRow", "DESCRIPTION");
                $sheet->mergeCells("K$secondRow:R$secondRow")->setCellValue("K$secondRow", "CONDITION OF LIGHT");

                // Third-level leaf headers (each spanning 2 columns horizontally)
                $sheet->mergeCells("E$thirdRow:F$thirdRow")->setCellValue("E$thirdRow", "TYPE");
                $sheet->mergeCells("G$thirdRow:H$thirdRow")->setCellValue("G$thirdRow", "CAPACITY (VOLTS)");
                $sheet->mergeCells("I$thirdRow:J$thirdRow")->setCellValue("I$thirdRow", "QUANTITY");
                $sheet->mergeCells("K$thirdRow:L$thirdRow")->setCellValue("K$thirdRow", "LIGHT CONDITION");
                $sheet->mergeCells("M$thirdRow:N$thirdRow")->setCellValue("M$thirdRow", "SWITCH CONDITION");
                $sheet->mergeCells("O$thirdRow:P$thirdRow")->setCellValue("O$thirdRow", "POWER SUPPLY");

                // Final vertical column
                $sheet->mergeCells("Q$thirdRow:R$thirdRow")->setCellValue("Q$thirdRow", "STATUS");
                $sheet->mergeCells("S$headerRow:U$thirdRow")->setCellValue("S$headerRow", "REMARKS");


                $sheet->getStyle("A$headerRow:U$thirdRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ],
                    'font' => ['bold' => true],
                ]);


                // Apply border and alignment styling
                $sheet->getStyle("A$headerRow:S" . ($headerRow + 2))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'font' => ['bold' => true],
                ]);

                $dataStartRow = $thirdRow + 1;

                foreach ($emergency_light_details as $index => $detail) {
                    $row = $dataStartRow;

                    $sheet->setCellValue("A$row", $detail->sr_no ?? '');
                    $sheet->setCellValue("B$row", getDepartment($detail->department) ?? '');
                    $sheet->setCellValue("C$row", $detail->location ?? '');
                    $sheet->setCellValue("D$row", $detail->emergency_of_light ?? '');

                    // Merge cells for consistency with header and set value in the first column
                    $sheet->mergeCells("E$row:F$row")->setCellValue("E$row", GetTypeofLight($detail->type_of_light) ?? '');
                    $sheet->mergeCells("G$row:H$row")->setCellValue("G$row", $detail->capacity ?? '');
                    $sheet->mergeCells("I$row:J$row")->setCellValue("I$row", $detail->quantity ?? '');
                    $sheet->mergeCells("K$row:L$row")->setCellValue("K$row", getLightCondition($detail->light_condition ?? ''));
                    $sheet->mergeCells("M$row:N$row")->setCellValue("M$row", getLightCondition($detail->switch_condition));
                    $sheet->mergeCells("O$row:P$row")->setCellValue("O$row", GetPowerSuply($detail->power_supply) ?? '');
                    $sheet->mergeCells("Q$row:R$row")->setCellValue("Q$row", getFireLightInspectionStatus($detail->fire_status) ?? '');
                    $sheet->mergeCells("S$row:U$row")->setCellValue("S$row", $detail->remarks ?? '');

                    // Apply alignment and borders if needed
                    $sheet->getStyle("A$row:U$row")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ]
                    ]);
                    $dataStartRow++;
                }
                // $row = $dataStartRow;

                $signatureRowStart = $dataStartRow;
                $sheet->getRowDimension($signatureRowStart)->setRowHeight(30);

                // Prepared By
                $sheet->mergeCells("A{$signatureRowStart}:H{$signatureRowStart}");
                $sheet->getStyle("A{$signatureRowStart}:H{$signatureRowStart}")->applyFromArray([
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);

                $richText = new RichText();
                $name = getUsername($data->created_by);

                if (!empty($name)) {
                    $richText->createTextRun("Prepared By :" . $name)->getFont()->setBold(true);
                } else {
                    $richText->createTextRun("Inspection has not been Prepared Yet")->getFont()->setBold(true);
                }

                $sheet->getCell("A{$signatureRowStart}")->setValue($richText);

                // Verified By
                $sheet->mergeCells("I{$signatureRowStart}:N{$signatureRowStart}");
                $sheet->getStyle("I{$signatureRowStart}:N{$signatureRowStart}")->applyFromArray([
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);

                $richText = new RichText();
                $name = getUsername($data->verified_by);

                if (!empty($name)) {
                    $richText->createTextRun("Verified By :" . $name)->getFont()->setBold(true);
                } else {
                    $richText->createTextRun("Inspection has not been Verified Yet")->getFont()->setBold(true);
                }

                $sheet->getCell("I{$signatureRowStart}")->setValue($richText);

                // Approved By
                $sheet->mergeCells("O{$signatureRowStart}:U{$signatureRowStart}");
                $sheet->getStyle("O{$signatureRowStart}:U{$signatureRowStart}")->applyFromArray([
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);
                $richText = new RichText();
                $name = getUsername($data->approved_by);

                if (!empty($name)) {
                    $richText->createTextRun("Approved By :" . $name)->getFont()->setBold(true);
                } else {
                    $richText->createTextRun("Inspection has not been Approved Yet")->getFont()->setBold(true);
                }

                $sheet->getCell("O{$signatureRowStart}")->setValue($richText);

                $row = $signatureRowStart + 5;
            }

            $fileName = 'emergency_light_inspection.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
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
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/emergency-light-inspection/list'));
        }
    }
}
