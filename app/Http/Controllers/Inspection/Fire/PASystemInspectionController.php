<?php

namespace App\Http\Controllers\Inspection\Fire;

use App\Http\Controllers\Controller;
use App\Mail\Inspection\Fire\FireInspection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\Fire\FireCheckListFollowUp;
use App\Models\Inspection\Fire\FireFileUpload;
use App\Models\Inspection\Fire\FireSignatureUpload;
use App\Models\Inspection\Fire\FireStatusLog;
use App\Models\Inspection\Fire\PASystemChecklist;
use App\Models\Inspection\Fire\PASystemInspection;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Master\Frequency;
use App\Models\Inspection\Master\Shift;
use App\Models\Master\Department;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use Exception;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class PASystemInspectionController extends Controller
{
    private $pa_system;
    private $pa_system_checklist;
    private $shift;
    private $location;
    private $unit;
    private $frequency;
    private $department;
    private $files;
    private $signature;
    private $statusLog;
    private $checklist_follow;
    private $document_reference;

    public function __construct()
    {
        $this->pa_system = new PASystemInspection();
        $this->pa_system_checklist = new PASystemChecklist();
        $this->department = new Department();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->unit = new Unit();
        $this->frequency = new Frequency();
        $this->files = new FireFileUpload();
        $this->signature = new FireSignatureUpload();
        $this->statusLog = new FireStatusLog();
        $this->checklist_follow = new FireCheckListFollowUp();
        $this->document_reference = new InspectionStaticDocno();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->pa_system->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->fire_pa_inspection_id) . "' data-type = '1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->fire_pa_inspection_id) . "' data-type = '0'>In-Active</span>";
                            }
                            // }
                            return $text;
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('date_of_inspection', function ($row) {
                            return Displaydateformat($row->date_of_inspection);
                        })
                        ->addColumn('next_due', function ($row) {
                            return Displaydateformat($row->next_due);
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
                            $btn = '<a href="' . admin_url('fire/pa-system-inspection/view/' . encryptId($row->fire_pa_inspection_id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            if ($row->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/pa-system-inspection/verification/' . encryptId($row->fire_pa_inspection_id)) . '/ehs" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if (($row->inspection_status == WAITING_FOR_CAPA_ACTION || $row->inspection_status == L2_MANAGER_REJECTED || $row->inspection_status == EHS_OFFICER_REJECTED || $row->inspection_status == L1_MANAGER_REJECTED) && (CheckUserRole(ROLE_FIRE_ASSOCIATES) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/pa-system-inspection/verification/' . encryptId($row->fire_pa_inspection_id)) . '/capa" class="" title="' . __('inspection.capa_action') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_CAPA_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/pa-system-inspection/verification/' . encryptId($row->fire_pa_inspection_id)) . '/ehsVerify" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L1_VERIFICATION && (CheckUserRole(ROLE_L1_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/pa-system-inspection/verification/' . encryptId($row->fire_pa_inspection_id)) . '/level-one-manager" class="" title="' . __('inspection.l1_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L2_VERIFICATION && (CheckUserRole(ROLE_L2_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/pa-system-inspection/verification/' . encryptId($row->fire_pa_inspection_id)) . '/level-two-manager" class="" title="' . __('inspection.l2_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            $btn .= '<a href="' . admin_url('fire/pa-system-inspection/exportViewPdf/' . encryptId($row->fire_pa_inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                                <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                            </a>';

                            $btn .= '<a href="' . admin_url('fire/pa-system-inspection/generalExcel/' . encryptId($row->fire_pa_inspection_id)) . '" style="margin-right: 5px;" title="Excel"> <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i></a>';

                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'inspection_status', 'date_of_inspection', 'next_due'])
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
        return view('inspection.fire.pa_system_inspection.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $location = $this->location->getLocationName();
            $unit = $this->unit->getUnit();
            $frequency = $this->frequency->getFrequency();
            $shifts = $this->shift->getShiftname();
            $department = $this->department->getdepartment();
            $document_no = $this->document_reference->selectUsingName('PASystemInspection');

            $data = array(
                'locations' => $location,
                'units' => $unit,
                'frequency' => $frequency,
                'shifts' => $shifts,
                'department' => $department,
                'document_no' => $document_no,
            );

            return view('inspection.fire.pa_system_inspection.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/pa-system-inspection/list'));
        }
    }

    public function GetLocations(Request $request)
    {
        try {
            $data = $this->location->getLocationName();
            $decryptedArray = [];
            foreach ($data as $data) {
                $decryptedArray[] = [
                    'id' => encryptId($data->id),
                    'location_name' => $data->location_name,
                ];
            }
            return response()->json($decryptedArray);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['error' => 'Please try again after sometimes'], 406);
        }
    }

    public function Store(Request $request)
    {
        try {

            $inspection = $this->pa_system->store();

            $inspection_type = FIRE_PA_SYSTEM_INSPECTION;
            $id = $inspection->id;

            $inspection_details = $this->pa_system_checklist->store($id);

            $inspection_file = $this->files->file_upload($inspection_type, $id);

            // $checklist_store = $this->checklist_follow->store($inspection_type, $id);

            $signature_update = $this->signature->CheckedBySignature($id, $inspection_type);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'FIRE PA SYSTEM INSPECTION';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Fire PA System Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('fire/pa-system-inspection/view/' . encryptId($id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Fire PA System Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('fire/pa-system-inspection/verification/' . encryptId($id) . '/ehs');
                $details = array(
                    'fire_type' => 'Fire PA System Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => FIRE_PA_SYSTEM_INSPECTION,
                'inspection_id' => $id,
                'from_status' => 0,
                'to_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'created_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', 'Your data added successfully');

            if ($inspection->observation_needed == 1) {
                return redirect(admin_url('fire/checklist-observation/add/' . encryptId($inspection_type) . '/' . encryptId($id)));
            } else {
                return redirect(admin_url('fire/pa-system-inspection/list'));
            }
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/pa-system-inspection/list'));
        }
    }

    public function View(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_type = FIRE_PA_SYSTEM_INSPECTION;

            $inspection = $this->pa_system->selectOne($id);
            $inspection_details = $this->pa_system_checklist->GetDetails($inspection->id);
            $inspection_image = $this->files->GetFile($inspection_type, $id);

            $status_log = $this->statusLog->selectOne($id, FIRE_PA_SYSTEM_INSPECTION);
            $document_no = $this->document_reference->selectOne($inspection->document_reference_id);

            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'inspection_image' => $inspection_image,
                'status_log' => $status_log,
                'document_no' => $document_no,
            );
            return view('inspection.fire.pa_system_inspection.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/pa-system-inspection/list'));
        }
    }

    public function Approvals(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_type = FIRE_PA_SYSTEM_INSPECTION;

            $inspection = $this->pa_system->selectOne($id);

            $inspection_details = $this->pa_system_checklist->GetDetails($inspection->id);
            $inspection_image = $this->files->GetFile($inspection_type, $id);
            $status_log = $this->statusLog->selectOne($id, FIRE_PA_SYSTEM_INSPECTION);
            $document_no = $this->document_reference->selectOne($inspection->document_reference_id);

            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'inspection_image' => $inspection_image,
                'status_log' => $status_log,
                'document_no' => $document_no,
            );
            return view('inspection.fire.pa_system_inspection.approve', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/pa-system-inspection/list'));
        }
    }

    public function EHSOfficerSubmit(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $inspection_updates = $this->pa_system->EHSOfficerUpdate($id);
            $signature_update = $this->signature->signatureUpload(FIRE_PA_SYSTEM_INSPECTION);
            $inspection_details = $this->pa_system->selectOne($id);
            if ($request->is_passed == 1) {
                $message = 'Fire PA System Inspection Approved Successfully';
                $web_link =   admin_url('fire/pa-system-inspection/verification/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
            } else {
                $message = 'Inspection Recommended for the CAPA Action';
                $web_link =   admin_url('fire/pa-system-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = WAITING_FOR_CAPA_ACTION;
            }
            $userIds = [
                'users' => $inspection_details->created_by,
            ];
            $mailsubject = 'FIRE PA SYSTEM INSPECTION';
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
            $url = admin_url('fire/pa-system-inspection/verification/' . encryptId($id) . '/capa');
            $details = array(
                'fire_type' => 'Fire PA System Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new FireInspection($details));

            $insert_array = [
                'type' => FIRE_PA_SYSTEM_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/pa-system-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went Wrong!');
            return redirect(admin_url('fire/pa-system-inspection/list'));
        }
    }

    public function CAPASubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $FIRE_PA_SYSTEM_INSPECTION = $this->pa_system->capaSubmit($id);
            $inspection_details = $this->pa_system->selectOne($id);
            $signature_update = $this->signature->signatureUpload(FIRE_PA_SYSTEM_INSPECTION);
            $ehsOfficers = $inspection_details->verified_by;
            $userIds = [
                'users' => $ehsOfficers,
            ];
            $mailsubject = 'Fire PA System Inspection';
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
                'web_link' =>  admin_url('fire/pa-system-inspection/verification/' . encryptId($inspection_details->id)) . '/ehsVerify',
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $user = $inspection_details->verified_by;
            $email_id = getUseremail($user);
            $url = admin_url('fire/pa-system-inspection/verification/' . encryptId($id) . '/ehs');
            $details = array(
                'fire_type' => 'Fire PA System Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => 'CAPA Action Completed by the Fire Associates',
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new FireInspection($details));

            $insert_array = [
                'type' => FIRE_PA_SYSTEM_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_ACTION,
                'to_status' => WAITING_FOR_CAPA_VERIFICATION,
                'created_by' => Auth::id(),
                'remarks' => $request->capa_remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/pa-system-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/pa-system-inspection/list'));
        }
    }

    public function CAPAVerifySubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->remarks;
            $FIRE_PA_SYSTEM_INSPECTION = $this->pa_system->capaVerifySubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(FIRE_PA_SYSTEM_INSPECTION);
            $inspection_details = $this->pa_system->selectOne($id);
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('fire/pa-system-inspection/verification/' . encryptId($inspection_details->id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = WAITING_FOR_L1_VERIFICATION;
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('fire/pa-system-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = EHS_OFFICER_REJECTED;
            }

            $mailsubject = 'FIRE PA SYSTEM INSPECTION';
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
                    'fire_type' => 'Fire PA System Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => FIRE_PA_SYSTEM_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/pa-system-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/pa-system-inspection/list'));
        }
    }

    public function levelOneManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_one_manager;
            $FIRE_PA_SYSTEM_INSPECTION = $this->pa_system->levelOneManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(FIRE_PA_SYSTEM_INSPECTION);
            $inspection_details = $this->pa_system->selectOne($id);
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('fire/pa-system-inspection/verification/' . encryptId($inspection_details->id) . '/level-two-manager');
                $user = GetLevelTwoManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by], [$inspection_details->verified_by]);
                $to_status = WAITING_FOR_L2_VERIFICATION;
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/pa-system-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = L1_MANAGER_REJECTED;
            }

            $mailsubject = 'FIRE PA SYSTEM INSPECTION';
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
                    'fire_type' => 'Fire PA System Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => FIRE_PA_SYSTEM_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L1_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_one_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/pa-system-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/pa-system-inspection/list'));
        }
    }

    public function levelTwoManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_two_manager;
            $FIRE_PA_SYSTEM_INSPECTION = $this->pa_system->levelTwoManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(FIRE_PA_SYSTEM_INSPECTION);
            $inspection_details = $this->pa_system->selectOne($id);
            if ($status == 1) {
                $message = 'Fire PA System Inspection Approved Successfully!';
                $web_link =   admin_url('fire/pa-system-inspection/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by], [$inspection_details->l2_manager_verified_by]);
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/pa-system-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = L2_MANAGER_REJECTED;
            }

            $mailsubject = 'FIRE PA SYSTEM INSPECTION';
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
                    'fire_type' => 'Fire PA System Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => FIRE_PA_SYSTEM_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L2_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_two_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/pa-system-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/pa-system-inspection/list'));
        }
    }

    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->pa_system->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $row = 1;

            foreach ($allData as $groupedDetails) {

                $inspection_detail = $groupedDetails->first();
                $document_no = $this->document_reference->selectOne($inspection_detail->document_reference_id);

                $prepared_by_signature = GetFireSignature($inspection_detail->checked_by, $inspection_detail->fire_pa_inspection_id, FIRE_PA_SYSTEM_INSPECTION);
                $verified_by_signature = GetFireSignature($inspection_detail->hydrant_updated_by, $inspection_detail->fire_pa_inspection_id, FIRE_PA_SYSTEM_INSPECTION);
                $approved_by_signature = GetFireSignature($inspection_detail->approved_by, $inspection_detail->fire_pa_inspection_id, FIRE_PA_SYSTEM_INSPECTION);

                $titleRow = $row;

                $logoPath = public_path('assets/images/logo-dark.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo');
                    $drawing->setDescription('Company Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates('A' . $titleRow);
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);
                    $drawing->setHeight(60);
                    $drawing->setWorksheet($sheet);
                }

                $sheet->mergeCells("A{$titleRow}:B" . ($titleRow + 2));
                $sheet->getStyle("A{$titleRow}:B" . ($titleRow + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->mergeCells("C{$titleRow}:I" . ($titleRow + 2));
                $sheet->setCellValue("C{$titleRow}", "PA SYSTEM INSPECTION CHECKLIST PN INTERNATIONAL PVT. LTD.");

                $sheet->getStyle("C{$titleRow}:I" . ($titleRow + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->setCellValue("J{$titleRow}", "Doc. No.");
                $sheet->setCellValue("K{$titleRow}", $document_no->doc_no ?? '');

                $sheet->setCellValue("J" . ($titleRow + 1), "Issue Dt.");
                $sheet->setCellValue("K" . ($titleRow + 1), Displaydateformat($document_no->issue_date ?? ''));

                $sheet->setCellValue("J" . ($titleRow + 2), "Rev. & Dt.");
                $sheet->setCellValue("K" . ($titleRow + 2), $document_no->rev_dt ?? '');

                $sheet->getStyle("J{$titleRow}:K" . ($titleRow + 2))->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $headerInfoRow = $titleRow + 3;

                $sheet->mergeCells("A{$headerInfoRow}:D{$headerInfoRow}")->setCellValue("A{$headerInfoRow}", "Date of Inspection:- " . Displaydateformat($inspection_detail->date_of_inspection));
                $sheet->mergeCells("E{$headerInfoRow}:H{$headerInfoRow}")->setCellValue("E{$headerInfoRow}", "Location:- " . getLocationname($inspection_detail->location));
                $sheet->mergeCells("I{$headerInfoRow}:K{$headerInfoRow}")->setCellValue("I{$headerInfoRow}", "Shift:- " . $inspection_detail->shift);
                $sheet->getStyle("A{$headerInfoRow}:K{$headerInfoRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $headerInfoRow++;

                $sheet->mergeCells("A{$headerInfoRow}:D{$headerInfoRow}")->setCellValue("A{$headerInfoRow}", "Next Due Date:- " . Displaydateformat($inspection_detail->next_due));
                $sheet->mergeCells("E{$headerInfoRow}:H{$headerInfoRow}")->setCellValue("E{$headerInfoRow}", "Unit:- " . getUnitname($inspection_detail->unit));
                $sheet->mergeCells("I{$headerInfoRow}:K{$headerInfoRow}")->setCellValue("I{$headerInfoRow}", "Frequency:- " . getFrequencyname($inspection_detail->frequency));
                $sheet->getStyle("A{$headerInfoRow}:K{$headerInfoRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $headerInfoRow++;

                $columnWidths = [
                    'A' => 5,
                    'B' => 15,
                    'C' => 10,
                    'D' => 10,
                    'E' => 10,
                    'F' => 10,
                    'G' => 15,
                    'H' => 10,
                    'I' => 15,
                    'J' => 15,
                    'K' => 15,
                ];

                foreach ($columnWidths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                $headerStart = $headerInfoRow;

                $sheet->mergeCells("A{$headerStart}:A" . ($headerStart + 1))->setCellValue("A{$headerStart}", "SL");
                $sheet->mergeCells("B{$headerStart}:B" . ($headerStart + 1))->setCellValue("B{$headerStart}", "LOCATION");
                $sheet->mergeCells("C{$headerStart}:I{$headerStart}")->setCellValue("C{$headerStart}", "CHECK ITEMS");

                $sheet->setCellValue("C" . ($headerStart + 1), "UNIT");
                $sheet->setCellValue("D" . ($headerStart + 1), "AUDIO QUALITY");
                $sheet->setCellValue("E" . ($headerStart + 1), "MIC CONDITION");
                $sheet->setCellValue("F" . ($headerStart + 1), "MIC QUANTITY");
                $sheet->setCellValue("G" . ($headerStart + 1), "PHYSICAL CONDITION");
                $sheet->setCellValue("H" . ($headerStart + 1), "CABLE CONDITION");
                $sheet->setCellValue("I" . ($headerStart + 1), "OPERATION");

                $sheet->mergeCells("J{$headerStart}:k" . ($headerStart + 1))->setCellValue("J{$headerStart}", "REMARKS");

                $sheet->getStyle("A{$headerStart}:k" . ($headerStart + 1))->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $dataRow = $headerStart + 2;
                $sr = 1;

                foreach ($groupedDetails as $detail) {
                    $sheet->setCellValue("A{$dataRow}", $sr);
                    $sheet->setCellValue("B{$dataRow}", getLocationname($detail['location'] ?? ''));
                    $sheet->setCellValue("C{$dataRow}", getUnitname($detail['unit'] ?? ''));

                    $audioQuality = $detail['audio_quality'] ?? '';
                    $micCondition = $detail['mic_condition'] ?? '';
                    $micQuantity = $detail['mic_quantity'] ?? '';
                    $physicalCondition = $detail['physical_condition'] ?? '';
                    $cableCondition = $detail['cable_condition'] ?? '';
                    $operation = $detail['operation'] ?? '';
                    $remark = $detail['remark'] ?? '';

                    $statusMap = [
                        GOOD => 'GOOD',
                        FAIR => 'FAIR',
                        POOR => 'POOR',
                    ];

                    $sheet->setCellValue("D{$dataRow}", $statusMap[$audioQuality] ?? $audioQuality);
                    $sheet->setCellValue("E{$dataRow}", $statusMap[$micCondition] ?? $micCondition);
                    $sheet->setCellValue("F{$dataRow}", $micQuantity . ' ' . ($statusMap[$micQuantity] ?? ''));
                    $sheet->setCellValue("G{$dataRow}", $statusMap[$physicalCondition] ?? $physicalCondition);
                    $sheet->setCellValue("H{$dataRow}", $statusMap[$cableCondition] ?? $cableCondition);
                    $sheet->setCellValue("I{$dataRow}", ($operation ?? '') == 1 ? 'Functional' : 'Non-Functional');
                    $sheet->mergeCells("J{$dataRow}:K{$dataRow}")->setCellValue("J{$dataRow}", $remark);

                    $sheet->getStyle("A{$dataRow}:k{$dataRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sr++;
                    $dataRow++;
                }
                $signatureRowStart = $dataRow;
                $sheet->getRowDimension($signatureRowStart)->setRowHeight(80);

                $sheet->mergeCells("A{$signatureRowStart}:D{$signatureRowStart}");
                $sheet->getStyle("A{$signatureRowStart}:D{$signatureRowStart}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                if (file_exists($prepared_by_signature)) {
                    $drawing = new Drawing();
                    $drawing->setName('Signature');
                    $drawing->setDescription('Prepared By');
                    $drawing->setPath($prepared_by_signature);
                    $drawing->setCoordinates("B{$signatureRowStart}");
                    $drawing->setOffsetX(60);
                    $drawing->setOffsetY(5);
                    $drawing->setHeight(40);
                    $drawing->setWorksheet($sheet);

                    $sheet->setCellValue("A{$signatureRowStart}", "\n\n\nPrepared By:\n" . getUsername($inspection_detail->checked_by));
                } else {
                    $sheet->setCellValue("A{$signatureRowStart}", "Prepared By:\nInspection not yet started");
                }

                $sheet->mergeCells("E{$signatureRowStart}:G{$signatureRowStart}");
                $sheet->getStyle("E{$signatureRowStart}:G{$signatureRowStart}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                if (file_exists($verified_by_signature)) {
                    $drawing = new Drawing();
                    $drawing->setName('Signature');
                    $drawing->setDescription('Verified By');
                    $drawing->setPath($verified_by_signature);
                    $drawing->setCoordinates("F{$signatureRowStart}");
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);
                    $drawing->setHeight(40);
                    $drawing->setWorksheet($sheet);

                    $sheet->setCellValue("E{$signatureRowStart}", "\n\n\nVerified By:\n" . getUsername($inspection_detail->updated_by));
                } else {
                    $sheet->setCellValue("E{$signatureRowStart}", "Verified By:\nInspection not yet completed");
                }

                $sheet->mergeCells("H{$signatureRowStart}:K{$signatureRowStart}");
                $sheet->getStyle("H{$signatureRowStart}:K{$signatureRowStart}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                if (file_exists($approved_by_signature)) {
                    $drawing = new Drawing();
                    $drawing->setName('Signature');
                    $drawing->setDescription('Approved By');
                    $drawing->setPath($approved_by_signature);
                    $drawing->setCoordinates("J{$signatureRowStart}");
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);
                    $drawing->setHeight(40);
                    $drawing->setWorksheet($sheet);

                    $sheet->setCellValue("H{$signatureRowStart}", "\n\n\nApproved By:\n" . getUsername($inspection_detail->approved_by));
                } else {
                    $sheet->setCellValue("H{$signatureRowStart}", "Approved By:\nApproval pending");
                }


                $row = $signatureRowStart + 6;

                $lastRow = $signatureRowStart;

                $sheet->getStyle("A{$titleRow}:K{$lastRow}")->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'PA System Inspection.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header('Cache-Control: max-age=0');
            $writer->save('php://output');

        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/pa-system-inspection/list'));
        }
    }

    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->pa_system->exportdata();

            $document_no = $this->document_reference->selectUsingName('PASystemInspection');

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }

            $data = array(
                'content' => $allData,
                'document_no' => $document_no,
                'pagetitle' => "Fire PA System Inspection",
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

            $view = view('inspection.Fire.pa_system_inspection.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Fire PA System.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/pa-system-inspection/list'));
        }
    }

    public function ExportViewPDF(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $status_log = $this->statusLog->selectOne($id, FIRE_PA_SYSTEM_INSPECTION);
                $forklift_details = $this->pa_system->selectOne($id);
                $inspection = $this->pa_system_checklist->GetDetails($forklift_details->id);
                $document_no = $this->document_reference->selectUsingName('PASystemInspection');

                $data = [
                    'status_log' => $status_log,
                    'forklift_details' => $forklift_details,
                    'pagetitle' => "Fire PA System Inspection",
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

            $html = view('inspection.fire.pa_system_inspection.viewPdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Fire PA System.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/pa-system-inspection/list'));
        }
    }

    public function generalExcel(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $pa_system = $this->pa_system->selectOne($id);
            $inspection_data = $this->pa_system_checklist->GetDetails($pa_system->id);
            $document_no = $this->document_reference->selectOne($pa_system->document_reference_id);

            $prepared_by_signature = GetFireSignature($pa_system->created_by, $pa_system->id, FIRE_PA_SYSTEM_INSPECTION);
            $verified_by_signature = GetFireSignature($pa_system->updated_by, $pa_system->id, FIRE_PA_SYSTEM_INSPECTION);
            $approved_by_signature = GetFireSignature($pa_system->approved_by, $pa_system->id, FIRE_PA_SYSTEM_INSPECTION);


            foreach (range('A', 'L') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $logoPath = public_path('assets/images/logo-dark.png');
            if (file_exists($logoPath)) {
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Company Logo');
                $drawing->setPath($logoPath);
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
            }

            $sheet->mergeCells('A1:B3');
            $sheet->mergeCells("C1:I3");
            $sheet->setCellValue("C1", "PA SYSTEM INSPECTION CHECKLIST PN INTERNATIONAL PVT. LTD.");
            $sheet->getStyle("C1:I3")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $sheet->getStyle('A1:B3')->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $labelMap = [

                'J1' => ['value' => 'Doc. No.', 'valueCell' => 'K1', 'data' => $document_no->doc_no],
                'J2' => ['value' => 'Issue Dt.', 'valueCell' => 'K2', 'data' => Displaydateformat($document_no->issue_date)],
                'J3' => ['value' => 'Rev. & Dt.', 'valueCell' => 'K3', 'data' => $document_no->rev_dt],

            ];

            foreach ($labelMap as $labelCell => $info) {
                $sheet->setCellValue($labelCell, $info['value']);
                $sheet->setCellValue($info['valueCell'], $info['data']);

                $sheet->getStyle($labelCell)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                ]);

                $sheet->getStyle($info['valueCell'])->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                ]);
            }

            $sheet->mergeCells("A4:D4")->setCellValue("A4", "Date of Inspection:- " . Displaydateformat($pa_system->date_of_inspection));
            $sheet->mergeCells("E4:H4")->setCellValue("E4", "Location :- " . getLocationname($pa_system->location));
            $sheet->mergeCells("I4:K4")->setCellValue("I4", "Shift:- " . getShift($pa_system->shift));
            $sheet->mergeCells("A5:D5")->setCellValue("A5", "Next Due date:- " . Displaydateformat($pa_system->next_due));
            $sheet->mergeCells("E5:H5")->setCellValue("E5", "Unit:- " . getUnitname($pa_system->unit));
            $sheet->mergeCells("I5:K5")->setCellValue("I5", "Frequency:- " . getFrequencyname($pa_system->frequency));
            $sheet->getStyle("A4:K5")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells("A6:A7")->setCellValue("A6", "SR.NO");
            $sheet->mergeCells("B6:B7")->setCellValue("B6", "LOCATION");
            $sheet->mergeCells("C6:I6")->setCellValue("C6", "CHECK ITEMS");
            $sheet->setCellValue("C7", "UNIT");
            $sheet->setCellValue("D7", "AUDIO QUALITY");
            $sheet->setCellValue("E7", "MIC CONDITION");
            $sheet->setCellValue("F7", "MIC QUANTITY");
            $sheet->setCellValue("G7", "PHYSICAL CONDITION");
            $sheet->setCellValue("H7", "CABLE CONDITION");
            $sheet->setCellValue("I7", "OPERATION");
            $sheet->mergeCells("J6:K7")->setCellValue("J6", "REMARKS");

            $sheet->getStyle("A6:K7")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $row = 8;
            $sr = 1;
            foreach ($inspection_data as $detail) {
                $sheet->setCellValue("A$row", $sr);
                $sheet->setCellValue("B$row", getLocationname($detail['location'] ?? ''));
                $sheet->setCellValue("C$row", getUnitname($detail['unit'] ?? ''));

                $audioQuality = $detail['audio_quality'] ?? '';
                $micCondition = $detail['mic_condition'] ?? '';
                $micQuantity = $detail['mic_quantity'] ?? '';
                $physicalCondition = $detail['physical_condition'] ?? '';
                $cableCondition = $detail['cable_condition'] ?? '';
                $operation = $detail['operation'] ?? '';
                $remark = $detail['remark'] ?? '';

                $statusMap = [
                    GOOD => 'GOOD',
                    FAIR => 'FAIR',
                    POOR => 'POOR',
                ];

                $sheet->setCellValue("D$row", $statusMap[$audioQuality] ?? $audioQuality);
                $sheet->setCellValue("E$row", $statusMap[$micCondition] ?? $micCondition);
                $sheet->setCellValue("F$row", $micQuantity);
                $sheet->setCellValue("G$row", $statusMap[$physicalCondition] ?? $physicalCondition);
                $sheet->setCellValue("H$row", $statusMap[$cableCondition] ?? $cableCondition);
                $sheet->setCellValue("I{$row}", ($operation ?? '') == 1 ? 'Functional' : 'Non-Functional');

                $sheet->mergeCells("J$row:K$row")->setCellValue("J$row", $remark);

                $sheet->getStyle("A$row:K$row")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sr++;
                $row++;
            }
            $signatureRowStart = $row;
            $sheet->getRowDimension($signatureRowStart)->setRowHeight(80);

            $sheet->mergeCells("A{$signatureRowStart}:D{$signatureRowStart}");
            $sheet->getStyle("A{$signatureRowStart}:D{$signatureRowStart}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);

            if (file_exists($prepared_by_signature)) {
                $drawing = new Drawing();
                $drawing->setName('Signature');
                $drawing->setDescription('Prepared By');
                $drawing->setPath($prepared_by_signature);
                $drawing->setCoordinates("C{$signatureRowStart}");
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setHeight(40);
                $drawing->setWorksheet($sheet);
                $sheet->setCellValue("A{$signatureRowStart}", "\n\n\nPrepared By:\n" . getUsername($pa_system->created_by));
            } else {
                $sheet->setCellValue("A{$signatureRowStart}", "Prepared By:\nInspection not yet started");
            }

            $sheet->mergeCells("E{$signatureRowStart}:H{$signatureRowStart}");
            $sheet->getStyle("E{$signatureRowStart}:H{$signatureRowStart}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);

            if (file_exists($verified_by_signature)) {
                $drawing = new Drawing();
                $drawing->setName('Signature');
                $drawing->setDescription('Verified By');
                $drawing->setPath($verified_by_signature);
                $drawing->setCoordinates("G{$signatureRowStart}");
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setHeight(40);
                $drawing->setWorksheet($sheet);
                $sheet->setCellValue("E{$signatureRowStart}", "\n\n\nVerified By:\n" . getUsername($pa_system->updated_by));
            } else {
                $sheet->setCellValue("E{$signatureRowStart}", "Verified By:\nInspection not yet completed");
            }

            $sheet->mergeCells("I{$signatureRowStart}:K{$signatureRowStart}");
            $sheet->getStyle("I{$signatureRowStart}:K{$signatureRowStart}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);

            if (file_exists($approved_by_signature)) {
                $drawing = new Drawing();
                $drawing->setName('Signature');
                $drawing->setDescription('Approved By');
                $drawing->setPath($approved_by_signature);
                $drawing->setCoordinates("J{$signatureRowStart}");
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setHeight(40);
                $drawing->setWorksheet($sheet);
                $sheet->setCellValue("I{$signatureRowStart}", "\n\n\nApproved By:\n" . getUsername($pa_system->approved_by));
            } else {
                $sheet->setCellValue("I{$signatureRowStart}", "Approved By:\nApproval pending");
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'PA System Inspection.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/pa-system-inspection/list'));
        }
    }
}
