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
use App\Models\Inspection\Fire\FireStatusLog;
use App\Models\Inspection\Fire\FireFileUpload;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Fire\FireSignatureUpload;
use App\Models\Inspection\Fire\FireCheckListFollowUp;
use App\Models\Inspection\Fire\FireMockDrillInspection;
use App\Models\Inspection\Fire\FireMockDrillInspectionDetails;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FireMockDrillInspectionController extends Controller
{
    private $fire_mock_drill_inspection;
    private $fire_mock_drill_inspection_details;
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
        $this->fire_mock_drill_inspection = new FireMockDrillInspection();
        $this->fire_mock_drill_inspection_details = new FireMockDrillInspectionDetails();
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
                    $data =  $this->fire_mock_drill_inspection->list();
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
                            $btn = '<a href="' . admin_url('fire/fire-mock-drill-observation/view/' . encryptId($row->inspection_id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            if ($row->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-mock-drill-observation/verification/' . encryptId($row->inspection_id)) . '/ehs" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if (($row->inspection_status == WAITING_FOR_CAPA_ACTION || $row->inspection_status == L2_MANAGER_REJECTED || $row->inspection_status == EHS_OFFICER_REJECTED || $row->inspection_status == L1_MANAGER_REJECTED) && (CheckUserRole(ROLE_FIRE_ASSOCIATES) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-mock-drill-observation/verification/' . encryptId($row->inspection_id)) . '/capa" class="" title="' . __('inspection.capa_action') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_CAPA_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-mock-drill-observation/verification/' . encryptId($row->inspection_id)) . '/ehsVerify" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L1_VERIFICATION && (CheckUserRole(ROLE_L1_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-mock-drill-observation/verification/' . encryptId($row->inspection_id)) . '/level-one-manager" class="" title="' . __('inspection.l1_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L2_VERIFICATION && (CheckUserRole(ROLE_L2_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-mock-drill-observation/verification/' . encryptId($row->inspection_id)) . '/level-two-manager" class="" title="' . __('inspection.l2_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            $btn .= '<a href="' . admin_url('fire/fire-mock-drill-observation/exportViewPdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                                <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                            </a>';

                            $btn .= '<a href="' . admin_url('fire/fire-mock-drill-observation/generalExcel/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="Excel"> <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i></a>';
 
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

        $data = array();
        return view('inspection.fire.fire_mock_drill_inspection.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $location = $this->location->getLocationName();
            $unit = $this->unit->getUnit();
            $frequency = $this->frequency->getFrequency();
            $shifts = $this->shift->getShiftname();
            $department = $this->department->getdepartment();
            $document_no = $this->document_reference->selectUsingName('FireMockDrillObservation');


            $data = array(
                'locations' => $location,
                'units' => $unit,
                'frequency' => $frequency,
                'shifts' => $shifts,
                'department' => $department,
                'document_no' => $document_no,
            );

            return view('inspection.fire.fire_mock_drill_inspection.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/fire-mock-drill-observation/list'));
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

            $inspection = $this->fire_mock_drill_inspection->store();
            $inspection_type = FIRE_MOCK_DRILL_INSPECION;
            $id = $inspection->id;
            $inspection_details = $this->fire_mock_drill_inspection_details->store($id);
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
                    'message' => "Fire Associate create the Fire Mock Drill Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('fire/fire-mock-drill-observation/view/' . encryptId($id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Fire Mock Drill Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('fire/fire-mock-drill-observation/verification/' . encryptId($id) . '/ehs');
                $details = array(
                    'fire_type' => 'Fire Mock Drill Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => FIRE_MOCK_DRILL_INSPECION,
                'inspection_id' => $id,
                'from_status' => 0,
                'to_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'created_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', 'Your data added successfully');
            return redirect(admin_url('fire/fire-mock-drill-observation/list'));
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/fire-mock-drill-observation/list'));
        }
    }

    public function View(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_type = FIRE_MOCK_DRILL_INSPECION;

            $inspection = $this->fire_mock_drill_inspection->selectOne($id);
            $inspection_details = $this->fire_mock_drill_inspection_details->GetDetails($inspection->id);
            $status_log = $this->statusLog->selectOne($id, FIRE_MOCK_DRILL_INSPECION);
            $document_no = $this->document_reference->selectOne($inspection->document_reference_id);


            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'status_log' => $status_log,
                'document_no' => $document_no,
            );
            return view('inspection.fire.fire_mock_drill_inspection.view', $data);
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/fire-mock-drill-observation/list'));
        }
    }

    public function Approvals(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_type = FIRE_MOCK_DRILL_INSPECION;

            $inspection = $this->fire_mock_drill_inspection->selectOne($id);
            $inspection_details = $this->fire_mock_drill_inspection_details->GetDetails($inspection->id);
            $inspection_image = $this->files->GetFile($inspection_type, $id);
            $status_log = $this->statusLog->selectOne($id, FIRE_MOCK_DRILL_INSPECION);
            $document_no = $this->document_reference->selectOne($inspection->document_reference_id);


            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'inspection_image' => $inspection_image,
                'status_log' => $status_log,
                'document_no' => $document_no,

            );
            return view('inspection.fire.fire_mock_drill_inspection.approval', $data);
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/fire-mock-drill-observation/list'));
        }
    }

    public function EHSOfficerSubmit(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $inspection_updates = $this->fire_mock_drill_inspection->EHSOfficerUpdate($id);
            $signature_update = $this->signature->signatureUpload(FIRE_MOCK_DRILL_INSPECION);
            $inspection_details = $this->fire_mock_drill_inspection->selectOne($id);
            if ($request->is_passed == 1) {
                $message = 'Fire Mock Drill Inspection Approved Successfully';
                $web_link =   admin_url('fire/fire-mock-drill-observation/verification/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
            } else {
                $message = 'Inspection Recommended for the CAPA Action';
                $web_link =   admin_url('fire/fire-mock-drill-observation/verification/' . encryptId($inspection_details->id) . '/capa');
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
            $url = admin_url('fire/fire-mock-drill-observation/verification/' . encryptId($id) . '/capa');
            $details = array(
                'fire_type' => 'Fire Mock Drill Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new FireInspection($details));

            $insert_array = [
                'type' => FIRE_MOCK_DRILL_INSPECION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-mock-drill-observation/list'));
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something Went Wrong!');
            return redirect(admin_url('fire/fire-mock-drill-observation/list'));
        }
    }

    public function CAPASubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $FIRE_MOCK_DRILL_INSPECION = $this->fire_mock_drill_inspection->capaSubmit($id);
            $inspection_details = $this->fire_mock_drill_inspection->selectOne($id);
            $signature_update = $this->signature->signatureUpload(FIRE_MOCK_DRILL_INSPECION);
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
                'web_link' =>  admin_url('fire/fire-mock-drill-observation/verification/' . encryptId($inspection_details->id)) . '/ehsVerify',
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $user = $inspection_details->verified_by;
            $email_id = getUseremail($user);
            $url = admin_url('fire/fire-mock-drill-observation/verification/' . encryptId($id) . '/ehs');
            $details = array(
                'fire_type' => 'Fire Mock Drill Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => 'CAPA Action Completed by the Fire Associates',
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new FireInspection($details));

            $insert_array = [
                'type' => FIRE_MOCK_DRILL_INSPECION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_ACTION,
                'to_status' => WAITING_FOR_CAPA_VERIFICATION,
                'created_by' => Auth::id(),
                'remarks' => $request->capa_remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-mock-drill-observation/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/fire-mock-drill-observation/list'));
        }
    }

    public function CAPAVerifySubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->remarks;
            $FIRE_MOCK_DRILL_INSPECION = $this->fire_mock_drill_inspection->capaVerifySubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(FIRE_MOCK_DRILL_INSPECION);
            $inspection_details = $this->fire_mock_drill_inspection->selectOne($id);
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('fire/fire-mock-drill-observation/verification/' . encryptId($inspection_details->id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = WAITING_FOR_L1_VERIFICATION;
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('fire/fire-mock-drill-observation/verification/' . encryptId($inspection_details->id) . '/capa');
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
                    'fire_type' => 'Fire Mock Drill Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => FIRE_MOCK_DRILL_INSPECION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-mock-drill-observation/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/fire-mock-drill-observation/list'));
        }
    }

    public function levelOneManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_one_manager;
            $FIRE_MOCK_DRILL_INSPECION = $this->fire_mock_drill_inspection->levelOneManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(FIRE_MOCK_DRILL_INSPECION);
            $inspection_details = $this->fire_mock_drill_inspection->selectOne($id);
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('fire/fire-mock-drill-observation/verification/' . encryptId($inspection_details->id) . '/level-two-manager');
                $user = GetLevelTwoManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by], [$inspection_details->verified_by]);
                $to_status = WAITING_FOR_L2_VERIFICATION;
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/fire-mock-drill-observation/verification/' . encryptId($inspection_details->id) . '/capa');
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
                    'fire_type' => 'Fire Mock Drill Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => FIRE_MOCK_DRILL_INSPECION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L1_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_one_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-mock-drill-observation/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/fire-mock-drill-observation/list'));
        }
    }

    public function levelTwoManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_two_manager;
            $FIRE_MOCK_DRILL_INSPECION = $this->fire_mock_drill_inspection->levelTwoManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(FIRE_MOCK_DRILL_INSPECION);
            $inspection_details = $this->fire_mock_drill_inspection->selectOne($id);
            if ($status == 1) {
                $message = 'Fire Mock Drill Inspection Approved Successfully!';
                $web_link =   admin_url('fire/fire-mock-drill-observation/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by], [$inspection_details->l2_manager_verified_by]);
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/fire-mock-drill-observation/verification/' . encryptId($inspection_details->id) . '/capa');
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
                    'fire_type' => 'Fire Mock Drill Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => FIRE_MOCK_DRILL_INSPECION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L2_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_two_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-mock-drill-observation/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/fire-mock-drill-observation/list'));
        }
    }

    // public function ExportExcel(Request $request)
    // {
    //     try {
    //         $allData = $this->fire_mock_drill_inspection->exportdata();
    //         dd($allData);
    //         if ($allData->isEmpty()) {
    //             return redirect()->back()->with('error', 'No data found');
    //         }

    //         $header = [
    //             __("common.sno"),
    //             'Document Number',
    //             'Issue Date',
    //             'Revision Date',
    //             __("inspection.inspection_status"),
    //             __("common.created_by"),
    //             __("common.created_date"),
    //         ];

    //         $i = 1;
    //         foreach ($allData as $data) {

    //             $export = [];
    //             $export[] =  $i;
    //             $export[] =  $data->doc_no;
    //             $export[] =  $data->issue_date;
    //             $export[] = $data->revision_data;
    //             $export[] =  getInspectionStatus($data->inspection_status);;
    //             $export[] =  getusername($data->created_by);
    //             $export[] =  Displaydateformat($data->created_at);
    //             $exportData[] = $export;
    //             $i++;
    //         }

    //         $writer = SimpleExcelWriter::streamDownload('Fire Mock Drill Inspection.xlsx')
    //             ->addHeader($header)
    //             ->addRows(
    //                 $exportData
    //             );
    //     } catch (Exception $ex) {
    //         report($ex);
    //         Session::flash('error', 'Something went wrong !');
    //         return redirect(admin_url('fire/fire-mock-drill-observation/list'));
    //     }
    // }

    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->fire_mock_drill_inspection->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            foreach (range('A', 'L') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }
            $row = 1;

            foreach ($allData as $groupedDetails) {

                $inspection_detail = $groupedDetails->first();
            
                $document_no = $this->document_reference->selectOne($inspection_detail->document_reference_id);
            
                $prepared_by_signature = GetFireSignature($inspection_detail->checked_by, $inspection_detail->fire_id, DETECTOR_INSPECTION);
                $verified_by_signature = GetFireSignature($inspection_detail->verified_by, $inspection_detail->fire_id, DETECTOR_INSPECTION);
                $approved_by_signature = GetFireSignature($inspection_detail->approved_by, $inspection_detail->fire_id, DETECTOR_INSPECTION);
            
                $titleRow = $row;
            
                // Logo
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
            
                $sheet->mergeCells("C{$titleRow}:J" . ($titleRow + 2));
                $sheet->setCellValue("C{$titleRow}", "MOCK DRILL OBSERVATION FOLLOW UP SHEET PN INTERNATIONAL PVT. LTD.");
                $sheet->getStyle("C{$titleRow}:J" . ($titleRow + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
            
                $sheet->setCellValue("K{$titleRow}", "Doc. No.");
                $sheet->setCellValue("L{$titleRow}", $document_no->doc_no ?? '');
            
                $sheet->setCellValue("K" . ($titleRow + 1), "Issue Dt.");
                $sheet->setCellValue("L" . ($titleRow + 1), Displaydateformat($document_no->issue_date ?? ''));
            
                $sheet->setCellValue("K" . ($titleRow + 2), "Rev. & Dt.");
                $sheet->setCellValue("L" . ($titleRow + 2), $document_no->rev_dt ?? '');
            
                $sheet->getStyle("K{$titleRow}:L" . ($titleRow + 2))->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            
                $headerRow = $titleRow + 3;
            
                $headers = [
                    'SL. NO.',
                    'OBSERVATION',
                    'DATE OF OBSERVATION',
                    'SHIFT',
                    'UNIT',
                    'RECOMMENDED CORRECTIVE & PREVENTIVE ACTION',
                    'ACTION TAKEN ',
                    'RESPONSIBILITY',
                    'TARGET DATE OF COMPLIANCE',
                    'DATE OF CLOSURE',
                    'STATUS',
                    'REMARK',
                ];
            
                $col = 'A';
                foreach ($headers as $header) {
                    $sheet->setCellValue("{$col}{$headerRow}", $header);
                    $sheet->getStyle("{$col}{$headerRow}")->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFEFEFEF'],
                        ],
                    ]);
                    $col++;
                }
            
                $dataRow = $headerRow + 1;
                $sr = 1;
            
                foreach ($groupedDetails as $detail) {
                    $sheet->setCellValue("A{$dataRow}", $sr);
                    $sheet->setCellValue("B{$dataRow}", $detail['observation'] ?? '');
                    $sheet->setCellValue("C{$dataRow}", Displaydateformat($detail['date_of_observation']) ?? '');
                    $sheet->setCellValue("D{$dataRow}", getShift($detail['shift_id']) ?? '');
                    $sheet->setCellValue("E{$dataRow}", getUnitname($detail['unit_id']) ?? '');
                    $sheet->setCellValue("F{$dataRow}", $detail['capa_remarks'] ?? '');
                    $sheet->setCellValue("G{$dataRow}", $detail['action_taken'] ?? '');
                    $sheet->setCellValue("H{$dataRow}", getUsername($detail['emp_id']) ?? '');
                    $sheet->setCellValue("I{$dataRow}", Displaydateformat($detail['date_of_compliance']) ?? '');
                    $sheet->setCellValue("J{$dataRow}", $detail['date_of_clousure'] ? Displaydateformat($detail['date_of_clousure']) : 'The Action was not Completed');
                    $sheet->setCellValue("K{$dataRow}", $detail->status == '1' ? 'Active' : 'InActive');
                    $sheet->setCellValue("L{$dataRow}", $detail['remarks'] ?? '');
            
                    $sheet->getStyle("A{$dataRow}:L{$dataRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
            
                    $sr++;
                    $dataRow++;
                }
            
                // Signature section
                $signatureRowStart = $dataRow;
                $sheet->getRowDimension($signatureRowStart)->setRowHeight(80);
            
                $sheet->mergeCells("A{$signatureRowStart}:D{$signatureRowStart}");
                $sheet->getStyle("A{$signatureRowStart}:D{$signatureRowStart}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);
                if (file_exists($prepared_by_signature)) {
                    $drawing = new Drawing();
                    $drawing->setName('Prepared Signature');
                    $drawing->setDescription('Prepared By');
                    $drawing->setPath($prepared_by_signature);
                    $drawing->setCoordinates("C{$signatureRowStart}");
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);
                    $drawing->setHeight(40);
                    $drawing->setWorksheet($sheet);
                    $sheet->setCellValue("A{$signatureRowStart}", "\n\n\nPrepared By:\n" . getUsername($inspection_detail->created_by));
                } else {
                    $sheet->setCellValue("A{$signatureRowStart}", "Prepared By:\nInspection not yet started");
                }
            
                $sheet->mergeCells("E{$signatureRowStart}:H{$signatureRowStart}");
                $sheet->getStyle("E{$signatureRowStart}:H{$signatureRowStart}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);
                if (file_exists($verified_by_signature)) {
                    $drawing = new Drawing();
                    $drawing->setName('Verified Signature');
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
            
                $sheet->mergeCells("I{$signatureRowStart}:L{$signatureRowStart}");
                $sheet->getStyle("I{$signatureRowStart}:L{$signatureRowStart}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);
                
                if (file_exists($approved_by_signature)) {
                    $drawing = new Drawing();
                    $drawing->setName('Approved Signature');
                    $drawing->setDescription('Approved By');
                    $drawing->setPath($approved_by_signature);
                    $drawing->setCoordinates("J{$signatureRowStart}");
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);
                    $drawing->setHeight(40);
                    $drawing->setWorksheet($sheet);
                    $sheet->setCellValue("I{$signatureRowStart}", "\n\n\nApproved By:\n" . getUsername($inspection_detail->approved_by));
                } else {
                    $sheet->setCellValue("I{$signatureRowStart}", "Approved By:\nApproval pending");
                }
            
                $lastRow = $signatureRowStart;
                $sheet->getStyle("A{$titleRow}:L{$lastRow}")->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            
                $row = $signatureRowStart + 6;
            }
            

            $writer = new Xlsx($spreadsheet);
            $filename = 'Catridge Type Fire Extinguisher.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
        } catch (\Exception $e) {
            dd($e);
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/detector-inspection/list'));
        }
    }

    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->fire_mock_drill_inspection->exportdata();
            $inspection_type = FIRE_MOCK_DRILL_INSPECION;

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } else if (count($allData) > 20) {
                return redirect()->back()->with('error', __('inspection.excess_error'));
            }


            $data = array(

                'content' => $allData,
                'pagetitle' => "Fire Mock Drill Inspection",
                'inspection_type' => $inspection_type,
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

            $view = view('inspection.fire.fire_mock_drill_inspection.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Fire Exitnguisher Inspection.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/fire-mock-drill-observation/list'));
        }
    }

    public function ExportViewPDF(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $status_log = $this->statusLog->selectOne($id, FIRE_MOCK_DRILL_INSPECION);
                $forklift_details = $this->fire_mock_drill_inspection->selectOne($id);
                $inspection = $this->fire_mock_drill_inspection_details->GetDetails($forklift_details->id);
                $document_no = $this->document_reference->selectOne($forklift_details->document_reference_id);
                $approved_by = GetFireSignature($forklift_details->approved_by, $forklift_details->id, FIRE_MOCK_DRILL_INSPECION);
                $verified_by = GetFireSignature($forklift_details->verified_by, $forklift_details->id, FIRE_MOCK_DRILL_INSPECION);
                $checked_by = GetFireSignature($forklift_details->checked_by, $forklift_details->id, FIRE_MOCK_DRILL_INSPECION);

                $data = [
                    'status_log' => $status_log,
                    'forklift_details' => $forklift_details,
                    'pagetitle' => "Fire Mock Drill Inspection",
                    'inspection' => $inspection,
                    'document_no' => $document_no,
                    'approved_by' => $approved_by,
                    'verified_by' => $verified_by,
                    'checked_by' => $checked_by,
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

            $html = view('inspection.fire.fire_mock_drill_inspection.viewPdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Fire Mock Drill Inspection.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/fire-mock-drill-observation/list'));
        }
    }


    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            $fire_mock_drill = $this->fire_mock_drill_inspection->find($id);
            $inspection_data = $this->fire_mock_drill_inspection_details->GetDetails($fire_mock_drill->id);
            $document_no = $this->document_reference->selectOne($fire_mock_drill->document_reference_id);
            $prepared_by_signature = GetFireSignature($fire_mock_drill->created_by, $fire_mock_drill->id, FIRE_MOCK_DRILL_INSPECION);
            $verified_by_signature = GetFireSignature($fire_mock_drill->updated_by, $fire_mock_drill->id, FIRE_MOCK_DRILL_INSPECION);
            $approved_by_signature = GetFireSignature($fire_mock_drill->approved_by, $fire_mock_drill->id, FIRE_MOCK_DRILL_INSPECION);
    
            foreach (range('A', 'L') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
    
            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }
    
            // Add logo image
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
            $sheet->getStyle('A1:B3')->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
    
            $sheet->mergeCells("C1:J3");
            $sheet->setCellValue("C1", "MOCK DRILL OBSERVATION FOLLOW UP SHEET PN INTERNATIONAL PVT. LTD.");
            $sheet->getStyle("C1:J3")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
    
            $row = 1;

            $labelMap = [

                'K1' => ['value' => 'Doc. No.', 'valueCell' => 'L1', 'data' => $document_no->doc_no],
                'K2' => ['value' => 'Issue Dt.', 'valueCell' => 'L2', 'data' => Displaydateformat($document_no->issue_date)],
                'K3' => ['value' => 'Rev. & Dt.', 'valueCell' => 'L3', 'data' => $document_no->rev_dt],

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
            
           
    
            $headers = [
                'SL. NO.',
                'OBSERVATION',
                'DATE OF OBSERVATION',
                'SHIFT',
                'UNIT',
                'RECOMMENDED CORRECTIVE & PREVENTIVE ACTION',
                'ACTION TAKEN ',
                'RESPONSIBILITY',
                'TARGET DATE OF COMPLIANCE',
                'DATE OF CLOSURE',
                'STATUS',
                'REMARK',
                
            ];

            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue("{$col}4", $header);
                $sheet->getStyle("{$col}4")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $col++;
            }
    
            $row = 5;
            $sr = 1;
                foreach ($inspection_data as $detail) {

                    $sheet->setCellValue("A{$row}", $sr);
                    $sheet->setCellValue("B{$row}", $detail['observation'] ?? '');
                    $sheet->setCellValue("C{$row}", Displaydateformat($detail['date_of_observation']) ?? '');
                    $sheet->setCellValue("D{$row}", getShift($detail['shift_id']) ?? '');
                    $sheet->setCellValue("E{$row}", getUnitname($detail['unit_id']) ?? '');
                    $sheet->setCellValue("F{$row}", $detail['capa_remarks'] ?? '');
                    $sheet->setCellValue("G{$row}", $detail['action_taken'] ?? '');
                    $sheet->setCellValue("H{$row}", getUsername($detail['emp_id']) ?? '');
                    $sheet->setCellValue("I{$row}", Displaydateformat($detail['date_of_compliance']) ?? '');
                    $sheet->setCellValue("J{$row}", $detail['date_of_clousure'] ? Displaydateformat($detail['date_of_clousure']) : 'The Action was not Completed');
                    $sheet->setCellValue("K{$row}", $detail->status == '1' ? 'Active' : 'InActive');
                    $sheet->setCellValue("L{$row}", $detail['remarks'] ?? '');

                    $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                
                    $sr++;
                    $row++;
                }
            
    
            $signatureRow = $row;
            $sheet->getRowDimension($signatureRow)->setRowHeight(80);
    
            $sheet->mergeCells("A{$signatureRow}:D{$signatureRow}");
            $sheet->getStyle("A{$signatureRow}:D{$signatureRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);
            if (file_exists($prepared_by_signature)) {
                $drawing = new Drawing();
                $drawing->setName('Prepared Signature');
                $drawing->setDescription('Prepared By');
                $drawing->setPath($prepared_by_signature);
                $drawing->setCoordinates("B{$signatureRow}");
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setHeight(40);
                $drawing->setWorksheet($sheet);
                $sheet->setCellValue("A{$signatureRow}", "\n\n\nPrepared By:\n" . getUsername($fire_mock_drill->created_by));
            } else {
                $sheet->setCellValue("A{$signatureRow}", "Prepared By:\nInspection not yet started");
            }
    
            $sheet->mergeCells("E{$signatureRow}:H{$signatureRow}");
            $sheet->getStyle("E{$signatureRow}:H{$signatureRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);
            if (file_exists($verified_by_signature)) {
                $drawing = new Drawing();
                $drawing->setName('Verified Signature');
                $drawing->setDescription('Verified By');
                $drawing->setPath($verified_by_signature);
                $drawing->setCoordinates("F{$signatureRow}");
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setHeight(40);
                $drawing->setWorksheet($sheet);
                $sheet->setCellValue("E{$signatureRow}", "\n\n\nVerified By:\n" . getUsername($fire_mock_drill->updated_by));
            } else {
                $sheet->setCellValue("E{$signatureRow}", "Verified By:\nInspection not yet completed");
            }
    
            $sheet->mergeCells("I{$signatureRow}:L{$signatureRow}");
            $sheet->getStyle("I{$signatureRow}:L{$signatureRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);
            if (file_exists($approved_by_signature)) {
                $drawing = new Drawing();
                $drawing->setName('Approved Signature');
                $drawing->setDescription('Approved By');
                $drawing->setPath($approved_by_signature);
                $drawing->setCoordinates("J{$signatureRow}");
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setHeight(40);
                $drawing->setWorksheet($sheet);
                $sheet->setCellValue("I{$signatureRow}", "\n\n\nApproved By:\n" . getUsername($fire_mock_drill->approved_by));
            } else {
                $sheet->setCellValue("I{$signatureRow}", "Approved By:\nApproval pending");
            }
    
            $writer = new Xlsx($spreadsheet);
            $fileName = 'Fire Mock Drill Observation.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);
    
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
        dd($e);
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/fire-extinguisher/cartridge/list'));
        }
    }
}
