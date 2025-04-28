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
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\SimpleExcel\SimpleExcelWriter;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\Inspection\Master\Frequency;
use App\Mail\Inspection\Fire\FireInspection;
use App\Models\Inspection\Fire\FireStatusLog;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Models\Inspection\Fire\FireFileUpload;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Fire\FireAlarmInspection;
use App\Models\Inspection\Fire\FireSignatureUpload;
use App\Models\Inspection\Fire\FireCheckListFollowUp;
use App\Models\Inspection\Fire\FireAlarmInspectionDetails;

class FireAlarmController extends Controller
{
    private $fire_alarm;
    private $fire_alarm_details;
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
        $this->fire_alarm = new FireAlarmInspection();
        $this->fire_alarm_details = new FireAlarmInspectionDetails();
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
                    $data =  $this->fire_alarm->list();
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
                            $btn = '<a href="' . admin_url('fire/fire-alarm-inspection/view/' . encryptId($row->inspection_id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            if ($row->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-alarm-inspection/verification/' . encryptId($row->inspection_id)) . '/ehs" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if (($row->inspection_status == WAITING_FOR_CAPA_ACTION || $row->inspection_status == L2_MANAGER_REJECTED || $row->inspection_status == EHS_OFFICER_REJECTED || $row->inspection_status == L1_MANAGER_REJECTED) && (CheckUserRole(ROLE_FIRE_ASSOCIATES) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-alarm-inspection/verification/' . encryptId($row->inspection_id)) . '/capa" class="" title="' . __('inspection.capa_action') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_CAPA_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-alarm-inspection/verification/' . encryptId($row->inspection_id)) . '/ehsVerify" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L1_VERIFICATION && (CheckUserRole(ROLE_L1_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-alarm-inspection/verification/' . encryptId($row->inspection_id)) . '/level-one-manager" class="" title="' . __('inspection.l1_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L2_VERIFICATION && (CheckUserRole(ROLE_L2_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-alarm-inspection/verification/' . encryptId($row->inspection_id)) . '/level-two-manager" class="" title="' . __('inspection.l2_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            $btn .= '<a href="' . admin_url('fire/fire-alarm-inspection/exportViewPdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                    </a>';

                            $btn .= '<a href="' . admin_url('fire/fire-alarm-inspection/export/excel/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="Excel"> <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i></a>';
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
        return view('inspection.fire.fire_alarm.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $location = $this->location->getLocationName();
            $unit = $this->unit->getUnit();
            $frequency = $this->frequency->getFrequency();
            $shifts = $this->shift->getShiftname();
            $department = $this->department->getdepartment();
            $document_no = $this->document_reference->selectUsingName('FireAlarmInspection');


            $data = array(
                'locations' => $location,
                'units' => $unit,
                'frequency' => $frequency,
                'shifts' => $shifts,
                'department' => $department,
                'document_no' => $document_no,
            );

            return view('inspection.fire.fire_alarm.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/fire-alarm-inspection/list'));
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
            $rules = [
                'issue_date' => 'required',
                'rev_date' => 'required',
                'inspection_date' => 'required',
                'location_id' => 'required',
                'shift_id' => 'required',
                'next_due' => 'required',
                'unit_id' => 'required',
                'frequency_id' => 'required',
                'sr_no.*' => 'required',
                'department.*' => 'required',
                'quantity.*' => 'required',
                'resource_code.*' => 'required',
                'glass.*' => 'required',
                'hammer.*' => 'required',
                'mannual_call_point.*' => 'required',
                'approach.*' => 'required',
                'observation.*' => 'required',
                'remarks.*' => 'required',
            ];

            $messages = [
                'issue_date.required' => 'Issue Date is required',
                'rev_date.required' => 'Revision Data is required',
                'inspection_date.required' => 'Inspection Date is required',
                'location_id.required' => 'Location is required',
                'shift_id.required' => 'Shift is required',
                'next_due.required' => 'Next due date is required',
                'unit_id.required' => 'Unit is required',
                'department.*.required' => 'Department is required',
                'quantity.*.required' => 'Quantity is required',
                'resource_code.*.required' => 'Resource Code is required',
                'glass.*.required' => 'Status of Glass is required',
                'hammer.*.required' => 'Status Of Hammer is required',
                'mannual_call_point.*.required' => 'Mannual Call Point is required',
                'leakage.*.required' => 'Leakage Status is required',
                'approach.*.required' => 'Approach is required',
                'observation.required' => 'Observation is required',
                'remarks.*.required' => 'Remarks is required',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $inspection = $this->fire_alarm->store();
            $inspection_type = FIRE_ALARM_INSPECTION;
            $id = $inspection->id;

            $inspection_details = $this->fire_alarm_details->store($id);
            $inspection_file = $this->files->file_upload($inspection_type, $id);

            $checklist_store = $this->checklist_follow->store($inspection_type, $id);

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
                    'message' => "Fire Associate create the Fire Alarm Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('fire/fire-alarm-inspection/view/' . encryptId($id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Fire Alarm Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('fire/fire-alarm-inspection/verification/' . encryptId($id) . '/ehs');
                $details = array(
                    'fire_type' => 'Fire Alarm Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => FIRE_ALARM_INSPECTION,
                'inspection_id' => $id,
                'from_status' => 0,
                'to_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'created_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', 'Your data added successfully');
            if ($inspection->observation == 1) {
                return redirect(admin_url('fire/checklist-observation/add/' . encryptId($inspection_type) . '/' . encryptId($id)));
            } else {
                return redirect(admin_url('fire/fire-alarm-inspection/list'));
            }
        } catch (Exception $ex) {
            report($ex);
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/fire-alarm-inspection/list'));
        }
    }

    public function View(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_type = FIRE_ALARM_INSPECTION;

            $inspection = $this->fire_alarm->selectOne($id);
            $inspection_details = $this->fire_alarm_details->GetDetails($inspection->id);
            $inspection_image = $this->files->GetFile($inspection_type, $id);
            $status_log = $this->statusLog->selectOne($id, FIRE_ALARM_INSPECTION);
            $document_no = $this->document_reference->selectOne($inspection->document_reference_id);


            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'inspection_image' => $inspection_image,
                'status_log' => $status_log,
                'document_no' => $document_no,
            );
            return view('inspection.fire.fire_alarm.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/fire-alarm-inspection/list'));
        }
    }

    public function Approvals(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_type = FIRE_ALARM_INSPECTION;

            $inspection = $this->fire_alarm->selectOne($id);
            $inspection_details = $this->fire_alarm_details->GetDetails($inspection->id);
            $inspection_image = $this->files->GetFile($inspection_type, $id);
            $status_log = $this->statusLog->selectOne($id, FIRE_ALARM_INSPECTION);
            $document_no = $this->document_reference->selectOne($inspection->document_reference_id);


            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'inspection_image' => $inspection_image,
                'status_log' => $status_log,
                'document_no' => $document_no,
            );
            return view('inspection.fire.fire_alarm.approve', $data);
        } catch (Exception $ex) {
            report($ex);
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/fire-alarm-inspection/list'));
        }
    }

    public function EHSOfficerSubmit(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $inspection_updates = $this->fire_alarm->EHSOfficerUpdate($id);
            $signature_update = $this->signature->signatureUpload(FIRE_ALARM_INSPECTION);
            $inspection_details = $this->fire_alarm->selectOne($id);
            if ($request->is_passed == 1) {
                $message = 'Fire Alarm Inspection Approved Successfully';
                $web_link =   admin_url('fire/fire-alarm-inspection/verification/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
            } else {
                $message = 'Inspection Recommended for the CAPA Action';
                $web_link =   admin_url('fire/fire-alarm-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
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
            $url = admin_url('fire/fire-alarm-inspection/verification/' . encryptId($id) . '/capa');
            $details = array(
                'fire_type' => 'Fire Alarm Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new FireInspection($details));

            $insert_array = [
                'type' => FIRE_ALARM_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-alarm-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            report($ex);
            Session::flash('error', 'Something Went Wrong!');
            return redirect(admin_url('fire/fire-alarm-inspection/list'));
        }
    }

    public function CAPASubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $fire_alarm_inspection = $this->fire_alarm->capaSubmit($id);
            $inspection_details = $this->fire_alarm->selectOne($id);
            $signature_update = $this->signature->signatureUpload(FIRE_ALARM_INSPECTION);
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
                'web_link' =>  admin_url('fire/fire-alarm-inspection/verification/' . encryptId($inspection_details->id)) . '/ehsVerify',
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $user = $inspection_details->verified_by;
            $email_id = getUseremail($user);
            $url = admin_url('fire/fire-alarm-inspection/verification/' . encryptId($id) . '/ehs');
            $details = array(
                'fire_type' => 'Fire Alarm Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => 'CAPA Action Completed by the Fire Associates',
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new FireInspection($details));

            $insert_array = [
                'type' => FIRE_ALARM_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_ACTION,
                'to_status' => WAITING_FOR_CAPA_VERIFICATION,
                'created_by' => Auth::id(),
                'remarks' => $request->capa_remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-alarm-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/fire-alarm-inspection/list'));
        }
    }

    public function CAPAVerifySubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->remarks;
            $fire_alarm_inspection = $this->fire_alarm->capaVerifySubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(FIRE_ALARM_INSPECTION);
            $inspection_details = $this->fire_alarm->selectOne($id);
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('fire/fire-alarm-inspection/verification/' . encryptId($inspection_details->id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = WAITING_FOR_L1_VERIFICATION;
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('fire/fire-alarm-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
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
                    'fire_type' => 'Fire Alarm Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => FIRE_ALARM_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-alarm-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/fire-alarm-inspection/list'));
        }
    }

    public function levelOneManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_one_manager;
            $fire_alarm_inspection = $this->fire_alarm->levelOneManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(FIRE_ALARM_INSPECTION);
            $inspection_details = $this->fire_alarm->selectOne($id);
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('fire/fire-alarm-inspection/verification/' . encryptId($inspection_details->id) . '/level-two-manager');
                $user = GetLevelTwoManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by], [$inspection_details->verified_by]);
                $to_status = WAITING_FOR_L2_VERIFICATION;
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/fire-alarm-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
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
                    'fire_type' => 'Fire Alarm Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => FIRE_ALARM_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L1_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_one_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-alarm-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/fire-alarm-inspection/list'));
        }
    }

    public function levelTwoManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_two_manager;
            $fire_alarm_inspection = $this->fire_alarm->levelTwoManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(FIRE_ALARM_INSPECTION);
            $inspection_details = $this->fire_alarm->selectOne($id);
            if ($status == 1) {
                $message = 'Fire Alarm Inspection Approved Successfully!';
                $web_link =   admin_url('fire/fire-alarm-inspection/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/fire-alarm-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = L2_MANAGER_REJECTED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);

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
                    'fire_type' => 'Fire Alarm Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => FIRE_ALARM_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L2_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_two_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-alarm-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/fire-alarm-inspection/list'));
        }
    }

    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->fire_alarm->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $row = 1;

            foreach ($allData as $groupedDetails) {
                $inspection_detail = $groupedDetails->first();
                $document_no = $this->document_reference->selectOne($inspection_detail->document_reference_id);
                $prepared_by_signature = GetFireSignature($inspection_detail->checked_by, $inspection_detail->fire_id, FIRE_ALARM_INSPECTION);
                $verified_by_signature = GetFireSignature($inspection_detail->verified_by, $inspection_detail->fire_id, FIRE_ALARM_INSPECTION);
                $approved_by_signature = GetFireSignature($inspection_detail->approved_by, $inspection_detail->fire_id, FIRE_ALARM_INSPECTION);

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
                $sheet->setCellValue("C{$titleRow}", "FIRE ALARM INSPECTION CHECKLIST PN INTERNATIONAL PVT. LTD.");

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
                    'H' => 15,
                    'I' => 15,
                    'J' => 15,
                    'K' => 15,
                ];

                foreach ($columnWidths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                $headerStart = $headerInfoRow;

                $sheet->mergeCells("A{$headerStart}:A" . ($headerStart + 1))->setCellValue("A{$headerStart}", "SR.NO");
                $sheet->mergeCells("B{$headerStart}:B" . ($headerStart + 1))->setCellValue("B{$headerStart}", "DEPARTMENT");
                $sheet->mergeCells("C{$headerStart}:C" . ($headerStart + 1))->setCellValue("C{$headerStart}", "RESOURCE CODE");
                $sheet->mergeCells("D{$headerStart}:H{$headerStart}")->setCellValue("D{$headerStart}", "CHECK ITEMS");

                $sheet->setCellValue("D" . ($headerStart + 1), "QUANTITY");
                $sheet->setCellValue("E" . ($headerStart + 1), "GLASS");
                $sheet->setCellValue("F" . ($headerStart + 1), "HAMMER");
                $sheet->setCellValue("G" . ($headerStart + 1), "MANNUAL CALL POINT");
                $sheet->setCellValue("H" . ($headerStart + 1), "APPROACH");

                $sheet->mergeCells("I{$headerStart}:K" . ($headerStart + 1))->setCellValue("I{$headerStart}", "REMARKS");

                $sheet->getStyle("A{$headerStart}:K" . ($headerStart + 1))->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $dataRow = $headerStart + 2;
                $sr = 1;

                $statusMap = [YES => 'YES', 0 => 'NO'];
                $function_map = [FUNCTIONAL => 'Functional', NON_FUNCTIONAL => 'Non-Functional'];
                $present_map = [PRESENT => 'Present', MISSING => 'Missing'];

                foreach ($groupedDetails as $detail) {
                    $sheet->setCellValue("A$dataRow", $sr);
                    $sheet->setCellValue("B$dataRow", GetDeptName($detail['department'] ?? ''));
                    $sheet->setCellValue("C$dataRow", $detail['resource_code'] ?? '');

                    $quantity = $detail['quantity'] ?? '';
                    $glass = $detail['glass'] ?? '';
                    $hammer = $detail['hammer'] ?? '';
                    $mannual_call_point = $detail['mannual_call_point'] ?? '';
                    $approach = $detail['approach'] ?? '';
                    $remark = $detail['remarks'] ?? '';



                    $sheet->setCellValue("D$dataRow", $quantity ?? $quantity);
                    $sheet->setCellValue("E$dataRow", $function_map[$glass] ?? $glass);
                    $sheet->setCellValue("F$dataRow", $function_map[$hammer] ?? $hammer);
                    $sheet->setCellValue("G$dataRow", $present_map[$mannual_call_point] ?? $mannual_call_point);
                    $sheet->setCellValue("H$dataRow", $approach ?? $approach);
                    $sheet->mergeCells("I$dataRow:K$dataRow")->setCellValue("I$dataRow", $remark);

                    $sheet->getStyle("A{$dataRow}:K{$dataRow}")->applyFromArray([
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

                    $sheet->setCellValue("E{$signatureRowStart}", "\n\n\nVerified By:\n" . getUsername($inspection_detail->verified_by));
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
            }

            $fileName = 'Fire Alarm Inspection.xlsx';
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $tempFile = storage_path("app/public/{$fileName}");
            $writer->save($tempFile);

            return response()->download($tempFile)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/fire-alarm-inspection/list'));
        }
    }

    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->fire_alarm->exportdata();
            $inspection_type = FIRE_ALARM_INSPECTION;
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $data = array(
                'inspection_type' => $inspection_type,
                'content' => $allData,
                'pagetitle' => "Fire Alarm Inspection",
            );

            $property = [
                'tempDir' => 'public/pdf/temp/',
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'orientation' => 'L'

            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $view = view('inspection.fire.fire_alarm.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Fire Alarm Inspection.pdf";
            $mpdf->Output($filename, 'I');
        } catch (Exception $ex) {
            report($ex);
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/fire-alarm-inspection/list'));
        }
    }

    public function ExportViewPDF(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $inspection_type = FIRE_ALARM_INSPECTION;
                $status_log = $this->statusLog->selectOne($id, FIRE_ALARM_INSPECTION);
                $forklift_details = $this->fire_alarm->selectOne($id);
                $inspection = $this->fire_alarm_details->GetDetails($forklift_details->id);
                $document_no = $this->document_reference->selectOne($forklift_details->document_reference_id);

                $approved_by = GetFireSignature($forklift_details->approved_by, $forklift_details->id, $inspection_type);
                $verified_by = GetFireSignature($forklift_details->verified_by, $forklift_details->id, $inspection_type);
                $checked_by = GetFireSignature($forklift_details->checked_by, $forklift_details->id, $inspection_type);

                $data = [
                    'status_log' => $status_log,
                    'forklift_details' => $forklift_details,
                    'pagetitle' => "Fire Alarm Inspection",
                    'inspection' => $inspection,
                    'document_no' => $document_no,
                    'inspection_type' => $inspection_type,
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

            $html = view('inspection.fire.fire_alarm.viewPdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Fire Alarm Inspection.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/fire-alarm-inspection/list'));
        }
    }

    public function GeneralExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $inspection         = $this->fire_alarm->selectOne($id);
            $inspection_data    = $this->fire_alarm_details->GetDetails($inspection->id);
            $document_no        = $this->document_reference->selectOne($inspection->document_reference_id);

            $prepared_by_signature = GetFireSignature($inspection->created_by, $inspection->id, FIRE_ALARM_INSPECTION);
            $verified_by_signature = GetFireSignature($inspection->updated_by, $inspection->id, FIRE_ALARM_INSPECTION);
            $approved_by_signature = GetFireSignature($inspection->approved_by, $inspection->id, FIRE_ALARM_INSPECTION);

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
            $sheet->setCellValue("C1", "FIRE ALARM INSPECTION CHECKLIST PN INTERNATIONAL PVT. LTD.");
            $sheet->getStyle("C1:I3")->applyFromArray([
                'font'       => ['bold' => true, 'size' => 14],
                'alignment'  => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders'    => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $sheet->getStyle('A1:B3')->applyFromArray([
                'alignment'  => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'    => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $labelMap = [
                'J1' => ['value' => 'Doc. No.',     'valueCell' => 'K1', 'data' => $document_no->doc_no],
                'J2' => ['value' => 'Issue Dt.',    'valueCell' => 'K2', 'data' => Displaydateformat($document_no->issue_date)],
                'J3' => ['value' => 'Rev. & Dt.',   'valueCell' => 'K3', 'data' => $document_no->rev_dt],
            ];

            foreach ($labelMap as $labelCell => $info) {
                $sheet->setCellValue($labelCell, $info['value']);
                $sheet->setCellValue($info['valueCell'], $info['data']);

                $sheet->getStyle($labelCell)->applyFromArray([
                    'font'      => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                ]);
                $sheet->getStyle($info['valueCell'])->applyFromArray([
                    'font'      => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                ]);
            }

            $sheet->mergeCells("A4:D4")->setCellValue("A4", "Date of Inspection:- " . Displaydateformat($inspection->date_of_inspection));
            $sheet->mergeCells("E4:H4")->setCellValue("E4", "Location :- " . getLocationname($inspection->location));
            $sheet->mergeCells("I4:K4")->setCellValue("I4", "Shift:- " . getShift($inspection->shift));
            $sheet->mergeCells("A5:D5")->setCellValue("A5", "Next Due date:- " . Displaydateformat($inspection->next_due));
            $sheet->mergeCells("E5:H5")->setCellValue("E5", "Unit:- " . getUnitname($inspection->unit));
            $sheet->mergeCells("I5:K5")->setCellValue("I5", "Frequency:- " . getFrequencyname($inspection->frequency));
            $sheet->getStyle("A4:K5")->applyFromArray([
                'font'      => ['bold' => true],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells("A6:A7")->setCellValue("A6", "SR.NO");
            $sheet->mergeCells("B6:B7")->setCellValue("B6", "DEPARTMENT");
            $sheet->mergeCells("C6:C7")->setCellValue("C6", "RESOURCE CODE");
            $sheet->mergeCells("D6:H6")->setCellValue("D6", "CHECK ITEMS");
            $sheet->setCellValue("D7", "QUANTITY");
            $sheet->setCellValue("E7", "GLASS");
            $sheet->setCellValue("F7", "HAMMER");
            $sheet->setCellValue("G7", "MANNUAL CALL POINT");
            $sheet->setCellValue("H7", "APPROACH");
            $sheet->mergeCells("I6:K7")->setCellValue("I6", "REMARKS");

            $sheet->getStyle("A6:K7")->applyFromArray([
                'font'      => ['bold' => true],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $row = 8;
            $sr  = 1;
            foreach ($inspection_data as $detail) {
                $sheet->setCellValue("A$row", $sr);
                $sheet->setCellValue("B$row", GetDeptName($detail['department'] ?? ''));
                $sheet->setCellValue("C$row", $detail['resource_code'] ?? '');

                $quantity = $detail['quantity'] ?? '';
                $glass = $detail['glass'] ?? '';
                $hammer = $detail['hammer'] ?? '';
                $mannual_call_point = $detail['mannual_call_point'] ?? '';
                $approach = $detail['approach'] ?? '';
                $remark = $detail['remarks'] ?? '';

                $statusMap = [YES => 'YES', 0 => 'NO'];
                $function_map = [FUNCTIONAL => 'Functional', NON_FUNCTIONAL => 'Non-Functional'];
                $present_map = [PRESENT => 'Present', MISSING => 'Missing'];

                $sheet->setCellValue("D$row", $quantity ?? $quantity);
                $sheet->setCellValue("E$row", $function_map[$glass] ?? $glass);
                $sheet->setCellValue("F$row", $function_map[$hammer] ?? $hammer);
                $sheet->setCellValue("G$row", $present_map[$mannual_call_point] ?? $mannual_call_point);
                $sheet->setCellValue("H$row", $approach ?? $approach);
                $sheet->mergeCells("I$row:K$row")->setCellValue("I$row", $remark);

                $sheet->getStyle("A$row:K$row")->applyFromArray([
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sr++;
                $row++;
            }

            $signatureRowStart = $row;
            $sheet->getRowDimension($signatureRowStart)->setRowHeight(80);

            // Prepared By
            $sheet->mergeCells("A{$signatureRowStart}:D{$signatureRowStart}");
            $sheet->getStyle("A{$signatureRowStart}:D{$signatureRowStart}")->applyFromArray([
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
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
                $sheet->setCellValue("A{$signatureRowStart}", "\n\n\nPrepared By:\n" . getUsername($inspection->created_by));
            } else {
                $sheet->setCellValue("A{$signatureRowStart}", "Prepared By:\nInspection not yet started");
            }

            // Verified By
            $sheet->mergeCells("E{$signatureRowStart}:H{$signatureRowStart}");
            $sheet->getStyle("E{$signatureRowStart}:H{$signatureRowStart}")->applyFromArray([
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
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
                $sheet->setCellValue("E{$signatureRowStart}", "\n\n\nVerified By:\n" . getUsername($inspection->updated_by));
            } else {
                $sheet->setCellValue("E{$signatureRowStart}", "Verified By:\nInspection not yet completed");
            }

            // Approved By
            $sheet->mergeCells("I{$signatureRowStart}:K{$signatureRowStart}");
            $sheet->getStyle("I{$signatureRowStart}:K{$signatureRowStart}")->applyFromArray([
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
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
                $sheet->setCellValue("I{$signatureRowStart}", "\n\n\nApproved By:\n" . getUsername($inspection->approved_by));
            } else {
                $sheet->setCellValue("I{$signatureRowStart}", "Approved By:\nApproval pending");
            }

            $writer   = new Xlsx($spreadsheet);
            $fileName = 'Hooter Inspection.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            dd($e);
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/fire-alarm-inspection/list'));
        }
    }
}
