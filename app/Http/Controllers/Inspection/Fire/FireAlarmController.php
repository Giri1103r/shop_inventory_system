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
                            $btn = '<a href="' . admin_url('fire/fire-alarm-inspection/view/' . encryptId($row->inspection_id)) . '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            if ($row->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-alarm-inspection/verification/' . encryptId($row->inspection_id)) . '/ehs" class="me-1" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if (($row->inspection_status == WAITING_FOR_CAPA_ACTION || $row->inspection_status == L2_MANAGER_REJECTED || $row->inspection_status == EHS_OFFICER_REJECTED || $row->inspection_status == L1_MANAGER_REJECTED) && (CheckUserRole(ROLE_FIRE_ASSOCIATES) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-alarm-inspection/verification/' . encryptId($row->inspection_id)) . '/capa" class="me-1" title="' . __('inspection.capa_action') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_CAPA_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-alarm-inspection/verification/' . encryptId($row->inspection_id)) . '/ehsVerify" class="me-1" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L1_VERIFICATION && (CheckUserRole(ROLE_L1_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-alarm-inspection/verification/' . encryptId($row->inspection_id)) . '/level-one-manager" class="me-1" title="' . __('inspection.l1_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L2_VERIFICATION && (CheckUserRole(ROLE_L2_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-alarm-inspection/verification/' . encryptId($row->inspection_id)) . '/level-two-manager" class="me-1" title="' . __('inspection.l2_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            $btn .= '<a href="' . admin_url('fire/fire-alarm-inspection/exportViewPdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                    </a>';

                            $btn .= '<a href="' . admin_url('fire/fire-alarm-inspection/export/excel/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="Excel"> <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i></a>';
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
                'exact_location.*' => 'required',
                'quantity.*' => 'required',
                'resource_code.*' => 'required',
                'glass.*' => 'required',
                'hammer.*' => 'required',
                'mannual_call_point.*' => 'required',
                'approach.*' => 'required',
                'observation.*' => 'required',

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
                'exact_location.*.required' => 'Exact Location is required',
                'hammer.*.required' => 'Status Of Hammer is required',
                'mannual_call_point.*.required' => 'Mannual Call Point is required',
                'leakage.*.required' => 'Leakage Status is required',
                'approach.*.required' => 'Approach is required',
                'observation.required' => 'Observation is required',

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

            // $checklist_store = $this->checklist_follow->store($inspection_type, $id);

            $signature_update = $this->signature->CheckedBySignature($id, $inspection_type);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'Fire Alarm Inspection';
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
            $mailsubject = 'Fire Alarm Inspection';
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
            $mailsubject = 'Fire Alarm Inspection';
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
                $users = [$inspection_details->created_by];
                $to_status = EHS_OFFICER_REJECTED;
            }

            $mailsubject = 'Fire Alarm Inspection';
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
                $users = [$inspection_details->created_by];
                $to_status = L1_MANAGER_REJECTED;
            }

            $mailsubject = 'Fire Alarm Inspection';
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

            $mailsubject = 'Fire Alarm Inspection';
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

                $titleRow = $row;

                // Insert logo
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
                $sheet->mergeCells("C{$titleRow}:I" . ($titleRow + 2));
                $sheet->setCellValue("C{$titleRow}", "FIRE ALARM INSPECTION CHECKLIST");

                $sheet->getStyle("A{$titleRow}:I" . ($titleRow + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Document reference
                $docMap = [
                    $titleRow     => ['Doc. No.',     $document_no->doc_no ?? ''],
                    $titleRow + 1 => ['Issue Dt.',    Displaydateformat($document_no->issue_date ?? '')],
                    $titleRow + 2 => ['Rev. & Dt.',   $document_no->rev_dt ?? ''],
                ];

                foreach ($docMap as $r => [$label, $value]) {
                    $sheet->setCellValue("J{$r}", $label);
                    $sheet->mergeCells("K{$r}:L{$r}");
                    $sheet->setCellValue("K{$r}", $value);
                    $sheet->getStyle("J{$r}:L{$r}")->applyFromArray([
                        'font'      => ['bold' => true],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    ]);
                }

                $headerInfoRow = $titleRow + 3;

                $sheet->mergeCells("A{$headerInfoRow}:D{$headerInfoRow}")->setCellValue("A{$headerInfoRow}", "Date of Inspection:- " . Displaydateformat($inspection_detail->date_of_inspection));
                $sheet->mergeCells("E{$headerInfoRow}:H{$headerInfoRow}")->setCellValue("E{$headerInfoRow}", "Location:- " . getLocationname($inspection_detail->location));
                $sheet->mergeCells("I{$headerInfoRow}:L{$headerInfoRow}")->setCellValue("I{$headerInfoRow}", "Shift:- " . getShift($inspection_detail->shift));

                $headerInfoRow++;

                $sheet->mergeCells("A{$headerInfoRow}:D{$headerInfoRow}")->setCellValue("A{$headerInfoRow}", "Next Due Date:- " . Displaydateformat($inspection_detail->next_due));
                $sheet->mergeCells("E{$headerInfoRow}:H{$headerInfoRow}")->setCellValue("E{$headerInfoRow}", "Unit:- " . getUnitname($inspection_detail->unit));
                $sheet->mergeCells("I{$headerInfoRow}:L{$headerInfoRow}")->setCellValue("I{$headerInfoRow}", "Frequency:- " . getFrequencyname($inspection_detail->frequency));

                $sheet->getStyle("A" . ($titleRow + 3) . ":L{$headerInfoRow}")->applyFromArray([
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['wrapText' => true],
                ]);

                $headerRow = $headerInfoRow + 1;

                // Table Headers
                $sheet->mergeCells("A{$headerRow}:A" . ($headerRow + 1))->setCellValue("A{$headerRow}", "SR.NO");
                $sheet->mergeCells("B{$headerRow}:B" . ($headerRow + 1))->setCellValue("B{$headerRow}", "DEPARTMENT");
                $sheet->mergeCells("C{$headerRow}:C" . ($headerRow + 1))->setCellValue("C{$headerRow}", "EXACT LOCATION");
                $sheet->mergeCells("D{$headerRow}:D" . ($headerRow + 1))->setCellValue("D{$headerRow}", "RESOURCE CODE");
                $sheet->mergeCells("E{$headerRow}:I{$headerRow}")->setCellValue("E{$headerRow}", "CHECK ITEMS");
                $sheet->setCellValue("E" . ($headerRow + 1), "QUANTITY");
                $sheet->setCellValue("F" . ($headerRow + 1), "GLASS");
                $sheet->setCellValue("G" . ($headerRow + 1), "HAMMER");
                $sheet->setCellValue("H" . ($headerRow + 1), "MANNUAL CALL POINT");
                $sheet->setCellValue("I" . ($headerRow + 1), "APPROACH");

                $sheet->mergeCells("J{$headerRow}:L" . ($headerRow + 1))->setCellValue("J{$headerRow}", "REMARKS");

                $sheet->getStyle("A{$headerRow}:L" . ($headerRow + 1))->applyFromArray([
                    'font'      => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $dataRow = $headerRow + 2;
                $sr = 1;

                $statusMap     = [YES => 'YES', 0 => 'NO'];
                $function_map  = [FUNCTIONAL => 'Functional', NON_FUNCTIONAL => 'Non-Functional'];
                $present_map   = [PRESENT => 'Present', MISSING => 'Missing'];

                foreach ($groupedDetails as $detail) {
                    $sheet->setCellValue("A{$dataRow}", $sr);
                    $sheet->setCellValue("B{$dataRow}", GetDeptName($detail['department'] ?? ''));
                    $sheet->setCellValue("C{$dataRow}", $detail['exact_location'] ?? '');
                    $sheet->setCellValue("D{$dataRow}", $detail['resource_code'] ?? '');
                    $sheet->setCellValue("E{$dataRow}", $detail['quantity'] ?? '');
                    $sheet->setCellValue("F{$dataRow}", $function_map[$detail['glass']] ?? $detail['glass']);
                    $sheet->setCellValue("G{$dataRow}", $function_map[$detail['hammer']] ?? $detail['hammer']);
                    $sheet->setCellValue("H{$dataRow}", $present_map[$detail['mannual_call_point']] ?? $detail['mannual_call_point']);
                    $sheet->setCellValue("I{$dataRow}", $detail['approach'] ?? '');
                    $sheet->mergeCells("J{$dataRow}:L{$dataRow}")->setCellValue("J{$dataRow}", $detail['remarks'] ?? '');

                    $sheet->getStyle("A{$dataRow}:L{$dataRow}")->applyFromArray([
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    ]);

                    $dataRow++;
                    $sr++;
                }

                // Signature Block
                $signatureRow = $dataRow;
                $sheet->getRowDimension($signatureRow)->setRowHeight(80);

                $sheet->mergeCells("A{$signatureRow}:D{$signatureRow}")->setCellValue("A{$signatureRow}", "Prepared By:\n" . getUsername($inspection_detail->checked_by));
                $sheet->mergeCells("E{$signatureRow}:H{$signatureRow}")->setCellValue("E{$signatureRow}", "Verified By:\n" . (getUsername($inspection_detail->verified_by) ?? 'Inspection not yet completed'));
                $sheet->mergeCells("I{$signatureRow}:L{$signatureRow}")->setCellValue("I{$signatureRow}", "Approved By:\n" . (getUsername($inspection_detail->approved_by) ?? 'Approval pending'));

                $sheet->getStyle("A{$signatureRow}:L{$signatureRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $row = $signatureRow + 6;
            }

            // Auto-size all columns and enable wrap
            foreach (range('A', 'L') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $fileName = 'Fire Alarm Inspection.xlsx';
            $tempPath = storage_path("app/public/{$fileName}");
            $writer = new Xlsx($spreadsheet);
            $writer->save($tempPath);

            return response()->download($tempPath)->deleteFileAfterSend(true);
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
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
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

            foreach (range('A', 'M') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
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
            $sheet->mergeCells("C1:I3")->setCellValue("C1", "FIRE ALARM INSPECTION CHECKLIST");
            $sheet->mergeCells("K1:L1")->setCellValue("K1", "Doc. No.: " . ($document_no->doc_no ?? ''));
            $sheet->mergeCells("K2:L2")->setCellValue("K2", "Issue Dt.: " . Displaydateformat($document_no->issue_date ?? ''));
            $sheet->mergeCells("K3:L3")->setCellValue("K3", "Rev. & Dt.: " . ($document_no->rev_dt ?? ''));

            $sheet->getStyle("C1:I3")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $sheet->getStyle("K1:L3")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // Inspection Info Rows
            $sheet->mergeCells("A4:D4")->setCellValue("A4", "Date of Inspection:- " . Displaydateformat($inspection->date_of_inspection));
            $sheet->mergeCells("E4:H4")->setCellValue("E4", "Location :- " . getLocationname($inspection->location));
            $sheet->mergeCells("I4:L4")->setCellValue("I4", "Shift:- " . getShift($inspection->shift));
            $sheet->mergeCells("A5:D5")->setCellValue("A5", "Next Due date:- " . Displaydateformat($inspection->next_due));
            $sheet->mergeCells("E5:H5")->setCellValue("E5", "Unit:- " . getUnitname($inspection->unit));
            $sheet->mergeCells("I5:L5")->setCellValue("I5", "Frequency:- " . getFrequencyname($inspection->frequency));
            $sheet->getStyle("A4:L5")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // Header
            $sheet->mergeCells("A6:A7")->setCellValue("A6", "SR.NO");
            $sheet->mergeCells("B6:B7")->setCellValue("B6", "DEPARTMENT");
            $sheet->mergeCells("C6:C7")->setCellValue("C6", "EXACT LOCATION");
            $sheet->mergeCells("D6:D7")->setCellValue("D6", "RESOURCE CODE");
            $sheet->mergeCells("E6:I6")->setCellValue("E6", "CHECK ITEMS");
            $sheet->setCellValue("E7", "QUANTITY");
            $sheet->setCellValue("F7", "GLASS");
            $sheet->setCellValue("G7", "HAMMER");
            $sheet->setCellValue("H7", "MANNUAL CALL POINT");
            $sheet->setCellValue("I7", "APPROACH");
            $sheet->mergeCells("J6:L7")->setCellValue("J6", "REMARKS");

            $sheet->getStyle("A6:L7")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $row = 8;
            $sr = 1;
            $function_map = [FUNCTIONAL => 'Functional', NON_FUNCTIONAL => 'Non-Functional'];
            $present_map = [PRESENT => 'Present', MISSING => 'Missing'];

            foreach ($inspection_data as $detail) {
                $sheet->setCellValue("A{$row}", $sr);
                $sheet->setCellValue("B{$row}", GetDeptName($detail['department'] ?? ''));
                $sheet->setCellValue("C{$row}", $detail['exact_location'] ?? '');
                $sheet->setCellValue("D{$row}", $detail['resource_code'] ?? '');
                $sheet->setCellValue("E{$row}", $detail['quantity'] ?? '');
                $sheet->setCellValue("F{$row}", $function_map[$detail['glass']] ?? $detail['glass']);
                $sheet->setCellValue("G{$row}", $function_map[$detail['hammer']] ?? $detail['hammer']);
                $sheet->setCellValue("H{$row}", $present_map[$detail['mannual_call_point']] ?? $detail['mannual_call_point']);
                $sheet->setCellValue("I{$row}", $detail['approach'] ?? '');
                $sheet->mergeCells("J{$row}:L{$row}")->setCellValue("J{$row}", $detail['remarks'] ?? '');

                $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sr++;
                $row++;
            }

            $signatureRow = $row;
            $sheet->mergeCells("A{$signatureRow}:D{$signatureRow}")->setCellValue("A{$signatureRow}", "Prepared By: " . getUsername($inspection->created_by));
            $sheet->mergeCells("E{$signatureRow}:H{$signatureRow}")->setCellValue("E{$signatureRow}", "Verified By: " . getUsername($inspection->updated_by));
            $sheet->mergeCells("I{$signatureRow}:L{$signatureRow}")->setCellValue("I{$signatureRow}", "Approved By: " . getUsername($inspection->approved_by));

            $sheet->getStyle("A{$signatureRow}:L{$signatureRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Fire Alarm Inspection.xlsx';
            $filePath = storage_path("app/public/{$fileName}");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/fire-alarm-inspection/list'));
        }
    }
}
