<?php

namespace App\Http\Controllers\Inspection\Safety;

use Exception;
use App\Models\UploadLog;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Master\Location;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Inspection\Master\Shift;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Inspection\Master\Frequency;
use App\Models\Inspection\Master\ChecklistFile;
use App\Mail\Inspection\Safety\SafetyInspection;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Safety\SafetyStatusLog;
use App\Models\Inspection\Safety\SignatureUpload;
use App\Models\Inspection\Safety\EyeWashInspectionDetails;
use App\Models\Inspection\Safety\MonthlyEyeWashInspection;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class MonthlyEyeWashInspectionController extends Controller
{
    private $eye_wash;
    private $checklist_file;
    private $upload_log;
    private $shift;
    private $location;
    private $unit;
    private $frequency;
    private $statusLog;
    private $eye_wash_details;
    private $signature;
    private $document_reference;

    public function __construct()
    {
        $this->eye_wash = new MonthlyEyeWashInspection();
        $this->checklist_file = new ChecklistFile();
        $this->upload_log = new UploadLog();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->unit = new Unit();
        $this->frequency = new Frequency();
        $this->statusLog = new SafetyStatusLog();
        $this->eye_wash_details = new EyeWashInspectionDetails();
        $this->signature = new SignatureUpload();
        $this->document_reference = new InspectionStaticDocno();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->eye_wash->list();
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
                        ->addColumn('inspection_created_at', function ($row) {
                            return Displaydateformat($row->inspection_created_at);
                        })
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('date_of_inspection', function ($row) {
                            return Displaydateformat($row->date_of_inspection);
                        })
                        ->addColumn('next_due', function ($row) {
                            return Displaydateformat($row->next_due);
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
                            $btn = '<a href="' . admin_url('safety/eye-wash-inspection/monthly/view/' . encryptId($row->inspection_id)) . '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            if ($row->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/eye-wash-inspection/monthly/verification/' . encryptId($row->inspection_id)) . '/ehs" class="me-1" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if (($row->inspection_status == WAITING_FOR_CAPA_ACTION || $row->inspection_status == L2_MANAGER_REJECTED || $row->inspection_status == EHS_OFFICER_REJECTED || $row->inspection_status == L1_MANAGER_REJECTED) && (CheckUserRole(ROLE_FIRE_ASSOCIATES) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/eye-wash-inspection/monthly/verification/' . encryptId($row->inspection_id)) . '/capa" class="me-1" title="' . __('inspection.capa_action') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_CAPA_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/eye-wash-inspection/monthly/verification/' . encryptId($row->inspection_id)) . '/ehsVerify" class="me-1" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L1_VERIFICATION && (CheckUserRole(ROLE_L1_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/eye-wash-inspection/monthly/verification/' . encryptId($row->inspection_id)) . '/level-one-manager" class="me-1" title="' . __('inspection.l1_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L2_VERIFICATION && (CheckUserRole(ROLE_L2_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/eye-wash-inspection/monthly/verification/' . encryptId($row->inspection_id)) . '/level-two-manager" class="me-1" title="' . __('inspection.l2_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            $btn .= '<a href="' . admin_url('safety/eye-wash-inspection/monthly/exportViewPdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                                    </a>';
                            $btn .= '<a href="' . admin_url('safety/eye-wash-inspection/monthly/generalexcel/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="EXCEL">
                                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                                    </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'date_of_inspection', 'next_due', 'created_by', 'status', 'inspection_status', 'issue_date'])
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
        return view('inspection.Safety.eye_wash_inspection.list', $data);
    }

    public function Add(Request $request)
    {
        try {

            $location = $this->location->getLocationName();
            $unit = $this->unit->getUnit();
            $frequency = $this->frequency->getFrequency();
            $shifts = $this->shift->getShiftname();
            $document_no = $this->document_reference->selectUsingName('MonthlyEyeWashInspection');

            $data = array(
                'locations' => $location,
                'units' => $unit,
                'frequency' => $frequency,
                'shifts' => $shifts,
                'document_no' => $document_no,
            );
            return view('inspection.Safety.eye_wash_inspection.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return admin_url('safety/eye-wash-inspection/monthly/list');
        }
    }

    public function Store(Request $request)
    {
        try {


            $rules = [
                'inspection_date' => 'required',
                'location_id' => 'required',
                'shift_id' => 'required',
                'next_due' => 'required',
                'unit_id' => 'required',
                'frequency_id' => 'required',
                'sr_no.*' => 'required',
                'exact_location.*' => 'required',
                'resource_code.*' => 'required',
                'condition.*' => 'required',
                'value.*' => 'required',
                'hfsov.*' => 'required',
                'foot_pedal.*' => 'required',
                'eyewash_heads.*' => 'required',
                'receptacle.*' => 'required',
                'water.*' => 'required',
                'quality.*' => 'required',
                'pressure.*' => 'required',
                'temperature.*' => 'required',
            ];

            $messages = [
                'inspection_date.required' => 'Inspection Date is required',
                'location_id.required' => 'Location is required',
                'shift_id.required' => 'Shift is required',
                'next_due.required' => 'Next due date is required',
                'unit_id.required' => 'Unit is required',
                'exact_location.*.required' => 'Exact Location is required',
                'resource_code.*.required' => 'Resource code is required',
                'condition.*.required' => 'Condition is required',
                'value.*.required' => 'Valve is required',
                'hfsov.*.required' => 'Hand free stay open value is required',
                'foot_pedal.*.required' => 'Foot Pedal Value is required',
                'eyewash_heads.*.required' => 'Eye wash heads is required',
                'receptacle.*.required' => 'Receptable name is requried',
                'water.*.required' => 'Water quality is required',
                'quality.*.required' => 'Quality is required',
                'pressure.*.required' => 'Pressure is required',
                'temperature.*.required' => 'Temperature is required',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $store_eyewash_inspection = $this->eye_wash->store();
            $inspection_id = $store_eyewash_inspection->id;
            $inspection_details = $this->eye_wash->selectOne($inspection_id);
            $store_inspection_details = $this->eye_wash_details->store($inspection_id);
            // $signature_update = $this->signature->signatureUpload(EYE_WASH_INSPECTION, $store_eyewash_inspection->id);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'Monthly Eye Wash Inspection Inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Monthly Eye Wash Inspection Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safety/eyewash/monthly/verification/view/' . encryptId($inspection_id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Monthly Eye Wash Inspection Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('safety/eyewash/monthly/verification/verification/' . encryptId($inspection_id) . '/ehs');
                $details = array(
                    'safety_type' => 'Monthly Eye Wash Inspection Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }

            $insert_array = [
                'type' => EYE_WASH_INSPECTION,
                'inspection_id' => $inspection_id,
                'from_status' => 0,
                'to_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'created_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);

            Session::flash('success', 'Monthly Eye Wash Inspection Added Successfully');
            return redirect(admin_url('safety/eye-wash-inspection/monthly/list'));
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/eye-wash-inspection/monthly/list'));
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

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->eye_wash->selectOne($id);
            $inspection = $this->eye_wash_details->GetDetails($inspection_details->id);

            $status_log = $this->statusLog->selectOne($id, EYE_WASH_INSPECTION);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);
            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'status_log' => $status_log,
                'document_no' => $document_no,
            );

            return view('inspection.Safety.eye_wash_inspection.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/eye-wash-inspection/monthly/list'));
        }
    }

    public function Approvals(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $inspection_details = $this->eye_wash->selectOne($id);
            $inspection = $this->eye_wash_details->GetDetails($inspection_details->id);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);

            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'document_no' => $document_no,
            );

            return view('inspection.Safety.eye_wash_inspection.approve', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/eye-wash-inspection/monthly/list'));
        }
    }

    public function EHSOfficerSubmit(Request $request)
    {
        try {
            $request = Request();
            $id = decryptId($request->id);
            $inspection_updates = $this->eye_wash->EHSOfficerUpdate($id);
            // $signature_update = $this->signature->signatureUpload(EYE_WASH_INSPECTION, $id);
            $inspection_details = $this->eye_wash->selectOne($id);
            if ($request->is_passed == 1) {
                $message = 'eye_wash Inspeciton Approved Successfully';
                $web_link =   admin_url('safety/eye-wash-inspection/monthly/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
            } else {
                $message = 'Inspection Recommended for the CAPA Action';
                $web_link =   admin_url('safety/eye-wash-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = WAITING_FOR_CAPA_ACTION;
            }
            $userIds = [
                'users' => $inspection_details->created_by,
            ];
            $mailsubject = 'Monthly Eye Wash Inspection Inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
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
            $url = admin_url('safety/eye-wash-inspection/monthly/verification/' . encryptId($id) . '/ehs');
            $details = array(
                'safety_type' => 'Monthly eye_wash Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new SafetyInspection($details));

            $insert_array = [
                'type' => EYE_WASH_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('safety/eye-wash-inspection/monthly/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went Wrong !');
            return redirect(admin_url('safety/eye-wash-inspection/monthly/list'));
        }
    }

    public function CAPASubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $eye_wash_inspection = $this->eye_wash->capaSubmit($id);
            $inspection_details = $this->eye_wash->selectOne($id);
            // $signature_update = $this->signature->signatureUpload(EYE_WASH_INSPECTION, $id);
            $ehsOfficers = $inspection_details->verified_by;
            $userIds = [
                'users' => $ehsOfficers,
            ];

            $mailsubject = 'Monthly Eye Wash Inspection Inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "CAPA Action Completed by the Fire Associates",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_details->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safety/eye-wash-inspection/monthly/verification/' . encryptId($inspection_details->id)) . '/ehsVerify',
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $user = $inspection_details->verified_by;
            $email_id = getUseremail($user);
            $url = admin_url('safety/eye-wash-inspection/monthly/verification/' . encryptId($id) . '/ehsVerify');
            $details = array(
                'safety_type' => 'Monthly eye_wash Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => 'CAPA Action Completed by the Fire Associates',
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new SafetyInspection($details));

            $insert_array = [
                'type' => EYE_WASH_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_ACTION,
                'to_status' => WAITING_FOR_CAPA_VERIFICATION,
                'created_by' => Auth::id(),
                'remarks' => $request->capa_remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('safety/eye-wash-inspection/monthly/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('safety/eye_wash-inspecttion/monthly/list'));
        }
    }

    public function CAPAVerifySubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->remarks;
            $eye_wash_inspection = $this->eye_wash->capaVerifySubmit($id, $status, $remarks);
            // $signature_update = $this->signature->signatureUpload(EYE_WASH_INSPECTION, $id);
            $inspection_details = $this->eye_wash->selectOne($id);
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('safety/eye-wash-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = WAITING_FOR_L1_VERIFICATION;
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('safety/eye-wash-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = EHS_OFFICER_REJECTED;
            }
            $mailsubject = 'Monthly Eye Wash Inspection Inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
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

            foreach ($users as $user) {
                $title = $message;
                $email_id = getUseremail($user);
                $url = $web_link;
                $details = array(
                    'safety_type' => 'Monthly eye_wash Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }


            notificationSave($notificationData);
            $insert_array = [
                'type' => EYE_WASH_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('safety/eye-wash-inspection/monthly/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('safety/eye-wash-inspection/monthly/list'));
        }
    }

    public function levelOneManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_one_manager;
            $eye_wash_inspection = $this->eye_wash->levelOneManagerSubmit($id, $status, $remarks);
            // $signature_update = $this->signature->signatureUpload(EYE_WASH_INSPECTION, $id);
            $inspection_details = $this->eye_wash->selectOne($id);
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('safety/eye-wash-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/level-two-manager');
                $user = GetLevelTwoManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
                $to_status = WAITING_FOR_L2_VERIFICATION;
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('safety/eye-wash-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = L1_MANAGER_REJECTED;
            }
            $mailsubject = 'Monthly Eye Wash Inspection Inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
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
                    'safety_type' => 'Monthly eye_wash Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }

            $insert_array = [
                'type' => EYE_WASH_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L1_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_one_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('safety/eye-wash-inspection/monthly/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('safety/eye-wash-inspection/monthly/list'));
        }
    }

    public function levelTwoManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_two_manager;
            $eye_wash_inspection = $this->eye_wash->levelTwoManagerSubmit($id, $status, $remarks);
            // $signature_update = $this->signature->signatureUpload(EYE_WASH_INSPECTION, $id);
            $inspection_details = $this->eye_wash->selectOne($id);
            if ($status == 1) {
                $message = 'eye_wash Inspeciton Approved Successfully!';
                $web_link =   admin_url('safety/eye-wash-inspection/monthly/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('safety/eye-wash-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = L2_MANAGER_REJECTED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
            }

            $mailsubject = 'Monthly Eye Wash Inspection Inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
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
                    'safety_type' => 'Monthly eye_wash Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }

            $insert_array = [
                'type' => EYE_WASH_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L2_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_two_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('safety/eye-wash-inspection/monthly/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('safety/eye-wash-inspection/monthly/list'));
        }
    }


    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->eye_wash->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            foreach (range('A', 'M') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            for ($i = 1; $i <= 2000; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $logoPath = public_path('assets/images/logo-dark.png');
            $startRow = 1;
            $serial = 1;

            foreach ($allData as $eye_wash) {
                $inspection_data = $this->eye_wash_details->GetDetails($eye_wash->id);
                $document_no = $this->document_reference->selectOne($eye_wash->document_reference_id);

                $prepared_by_signature = GetSafetySignature($eye_wash->created_by, $eye_wash->id, EYE_WASH_INSPECTION);
                $verified_by_signature = GetSafetySignature($eye_wash->verified_by, $eye_wash->id, EYE_WASH_INSPECTION);
                $approved_by_signature = GetSafetySignature($eye_wash->approved_by, $eye_wash->id, EYE_WASH_INSPECTION);

                if (file_exists($logoPath)) {
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setName('Logo');
                    $drawing->setDescription('Company Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates('A' . $startRow);
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);
                    $drawing->setHeight(60);
                    $drawing->setWorksheet($sheet);
                }

                $sheet->mergeCells("C{$startRow}:K" . ($startRow + 2));
                $sheet->setCellValue("C{$startRow}", 'Monthly Eye Wash Inspection INSPECTION CHECKLIST .');
                $sheet->getStyle("C{$startRow}:K" . ($startRow + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->mergeCells("A{$startRow}:B" . ($startRow + 2));
                $sheet->getStyle("A{$startRow}:B" . ($startRow + 2))->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $labelMap = [
                    'L' . $startRow => ['value' => 'Doc. No.', 'valueCell' => 'M' . $startRow, 'data' => $document_no->doc_no ?? ''],
                    'L' . ($startRow + 1) => ['value' => 'Issue Dt.', 'valueCell' => 'M' . ($startRow + 1), 'data' => Displaydateformat($document_no->issue_date ?? '')],
                    'L' . ($startRow + 2) => ['value' => 'Rev. & Dt.', 'valueCell' => 'M' . ($startRow + 2), 'data' => $document_no->rev_dt ?? ''],
                ];

                foreach ($labelMap as $cell => $info) {
                    $sheet->setCellValue($cell, $info['value']);
                    $sheet->setCellValue($info['valueCell'], $info['data']);
                    $sheet->getStyle($cell)->applyFromArray([
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

                $startRow += 3;

                $sheet->mergeCells("A{$startRow}:D{$startRow}")->setCellValue("A{$startRow}", 'Date of Inspection:- ' . Displaydateformat($eye_wash->date_of_inspection));
                $sheet->mergeCells("E{$startRow}:I{$startRow}")->setCellValue("E{$startRow}", 'Location :- ' . getLocationname($eye_wash->location));
                $sheet->mergeCells("J{$startRow}:M{$startRow}")->setCellValue("J{$startRow}", 'Shift:- ' . getShift($eye_wash->shift));
                $startRow++;

                $sheet->mergeCells("A{$startRow}:D{$startRow}")->setCellValue("A{$startRow}", 'Next Due date:- ' . Displaydateformat($eye_wash->next_due));
                $sheet->mergeCells("E{$startRow}:I{$startRow}")->setCellValue("E{$startRow}", 'Unit:- ' . getUnitname($eye_wash->unit));
                $sheet->mergeCells("J{$startRow}:M{$startRow}")->setCellValue("J{$startRow}", 'Frequency:- ' . getFrequencyname($eye_wash->frequency));

                $sheet->getStyle("A" . ($startRow - 1) . ":M{$startRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $startRow++;

                $headerStart = $startRow;
                $sheet->mergeCells("A{$headerStart}:A" . ($headerStart + 2))->setCellValue("A{$headerStart}", 'SR. NO');
                $sheet->mergeCells("B{$headerStart}:B" . ($headerStart + 2))->setCellValue("B{$headerStart}", 'LOCATION');
                $sheet->mergeCells("C{$headerStart}:D" . ($headerStart + 2))->setCellValue("C{$headerStart}", 'RESOURCE CODE');

                $sheet->mergeCells("E{$headerStart}:L{$headerStart}")->setCellValue("E{$headerStart}", 'CHECK ITEMS');
                $sheet->mergeCells("E" . ($headerStart + 1) . ":G" . ($headerStart + 1))->setCellValue("E" . ($headerStart + 1), 'CONDITION');
                $sheet->mergeCells("H" . ($headerStart + 1) . ":L" . ($headerStart + 1))->setCellValue("H" . ($headerStart + 1), 'WATER');

                $sheet->setCellValue("E" . ($headerStart + 2), 'VALUE');
                $sheet->setCellValue("F" . ($headerStart + 2), 'HANDS-FREE STAY OPEN VALVE');
                $sheet->setCellValue("G" . ($headerStart + 2), 'FOOT PEDAL VALVE');
                $sheet->setCellValue("H" . ($headerStart + 2), 'Eye Wash & Head Shower');
                $sheet->setCellValue("I" . ($headerStart + 2), 'RECEPTACLE');
                $sheet->setCellValue("J" . ($headerStart + 2), 'Water QUALITY');
                $sheet->setCellValue("K" . ($headerStart + 2), 'PRESSURE');
                $sheet->setCellValue("L" . ($headerStart + 2), 'TEMPERATURE');

                $sheet->mergeCells("M{$headerStart}:M" . ($headerStart + 2))->setCellValue("M{$headerStart}", 'REMARKS');

                $sheet->getStyle("A{$headerStart}:M" . ($headerStart + 2))->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $startRow = $headerStart + 3;

                $sr = 1;
                foreach ($inspection_data as $detail) {
                    $sheet->setCellValue("A{$startRow}", $sr);
                    $sheet->setCellValue("B{$startRow}", ($detail['location']) ?? '');
                    $sheet->mergeCells("C{$startRow}:D{$startRow}")->setCellValue("C{$startRow}", $detail['resource_code'] ?? '');
                    $sheet->setCellValue("E{$startRow}", $detail['value'] ?? '');
                    $sheet->setCellValue("F{$startRow}", $detail['hand_free_stay_open_value'] ?? '');
                    $sheet->setCellValue("G{$startRow}", $detail['foot_pedal_value'] ?? '');
                    $sheet->setCellValue("H{$startRow}", $detail['eyewash_heads_value'] == '1' ? "OK" : "NOT OK");
                    $sheet->setCellValue("I{$startRow}", $detail['receptacle'] ?? '');
                    $qualityLabel = '';

                    if ($detail['water'] == GOOD) {
                        $qualityLabel = 'Good';
                    } elseif ($detail['water'] == FAIR) {
                        $qualityLabel = 'Fair';
                    } elseif ($detail['water'] == POOR) {
                        $qualityLabel = 'Poor';
                    }

                    $sheet->setCellValue("J{$startRow}", $qualityLabel);
                    $sheet->setCellValue("K{$startRow}", $detail['pressure'] ?? '');
                    $sheet->setCellValue("L{$startRow}", $detail['temperature']  == '1' ? "ABNORMAL" : 'NORMAL');
                    $sheet->setCellValue("M{$startRow}", $detail['remarks'] ?? '');

                    $sheet->getStyle("A{$startRow}:M{$startRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $startRow++;
                    $sr++;
                }

                $signatureRowStart = $startRow;
                $sheet->getRowDimension($signatureRowStart)->setRowHeight(80);

                $sheet->mergeCells("A{$signatureRowStart}:E{$signatureRowStart}");
                $sheet->getStyle("A{$signatureRowStart}:E{$signatureRowStart}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);

                if ($eye_wash->created_by) {
                    // $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    // $drawing->setName('Prepared');
                    // $drawing->setPath($prepared_by_signature);
                    // $drawing->setCoordinates("C{$signatureRowStart}");
                    // $drawing->setOffsetX(5);
                    // $drawing->setOffsetY(5);
                    // $drawing->setHeight(40);
                    // $drawing->setWorksheet($sheet);
                    $sheet->setCellValue("A{$signatureRowStart}", "\n\n\nPrepared By:\n" . getUsername($eye_wash->created_by));
                } else {
                    $sheet->setCellValue("A{$signatureRowStart}", "Prepared By:\nInspection not yet started");
                }

                $sheet->mergeCells("F{$signatureRowStart}:I{$signatureRowStart}");
                $sheet->getStyle("F{$signatureRowStart}:I{$signatureRowStart}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);

                if ($eye_wash->verified_by) {
                    // $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    // $drawing->setName('Verified');
                    // $drawing->setPath($verified_by_signature);
                    // $drawing->setCoordinates("G{$signatureRowStart}");
                    // $drawing->setOffsetX(5);
                    // $drawing->setOffsetY(5);
                    // $drawing->setHeight(40);
                    // $drawing->setWorksheet($sheet);
                    $sheet->setCellValue("F{$signatureRowStart}", "\n\n\nVerified By:\n" . getUsername($eye_wash->verified_by));
                } else {
                    $sheet->setCellValue("F{$signatureRowStart}", "Verified By:\nInspection not yet completed");
                }

                $sheet->mergeCells("J{$signatureRowStart}:M{$signatureRowStart}");
                $sheet->getStyle("J{$signatureRowStart}:M{$signatureRowStart}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);

                if ($eye_wash->approved_by) {
                    // $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    // $drawing->setName('Approved');
                    // $drawing->setPath($approved_by_signature);
                    // $drawing->setCoordinates("L{$signatureRowStart}");
                    // $drawing->setOffsetX(5);
                    // $drawing->setOffsetY(5);
                    // $drawing->setHeight(40);
                    // $drawing->setWorksheet($sheet);
                    $sheet->setCellValue("J{$signatureRowStart}", "\n\n\nApproved By:\n" . getUsername($eye_wash->approved_by));
                } else {
                    $sheet->setCellValue("J{$signatureRowStart}", "Approved By:\nApproval pending");
                }

                $startRow = $signatureRowStart + 5;
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $fileName = 'Monthly Eye Wash Inspection All.xlsx';
            $filePath = storage_path("app/public/{$fileName}");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/eye-wash-inspection/monthly/list'));
        }
    }


    public function ExportPdf(Request $request)
    {

        try {

            $content = $this->eye_wash->exportdata();
            $document_no = $this->document_reference->selectUsingName('MonthlyEyeWashInspection');
            if ($content->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $data = [
                'content' => $content,
                'document_no' => $document_no,
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

            $view = view('inspection.Safety.eye_wash_inspection.bulkpdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Monthly Eye Wash Inspection.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('safety/eye-wash-inspection/monthly/list'));
        }
    }


    public function exportViewPdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $status_log = $this->statusLog->selectOne($id, EYE_WASH_INSPECTION);
                $inspection_details = $this->eye_wash->selectOne($id);
                $inspection = $this->eye_wash_details->GetDetails($inspection_details->id);
                $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);
                $approved_by = GetSafetySignature($inspection_details->approved_by, $inspection_details->id, EYE_WASH_INSPECTION);
                $verified_by = GetSafetySignature($inspection_details->verified_by, $inspection_details->id, EYE_WASH_INSPECTION);
                $checked_by = GetSafetySignature($inspection_details->created_by, $inspection_details->id, EYE_WASH_INSPECTION);

                $document_no = $this->document_reference->selectUsingName('MonthlyEyeWashInspection');


                $data = [
                    'status_log' => $status_log,
                    'inspection_details' => $inspection_details,
                    'inspection' => $inspection,
                    'pagetitle' => "Monthly Eye Wash Inspection Inspection",
                    'document_no' => $document_no,
                    // 'approved_by' => $approved_by,
                    // 'verified_by' => $verified_by,
                    // 'checked_by' => $checked_by,
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

            $html = view('inspection.Safety.eye_wash_inspection.viewPdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Monthly Eye Wash Inspection Inspection.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/eye-wash-inspection/monthly/list'));
        }
    }


    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $eye_wash = $this->eye_wash->find($id);
            $inspection_data = $this->eye_wash_details->GetDetails($eye_wash->id);
            $document_no = $this->document_reference->selectOne($eye_wash->document_reference_id);

            $prepared_by_signature = GetSafetySignature($eye_wash->created_by, $eye_wash->id, EYE_WASH_INSPECTION);
            $verified_by_signature = GetSafetySignature($eye_wash->verified_by, $eye_wash->id, EYE_WASH_INSPECTION);
            $approved_by_signature = GetSafetySignature($eye_wash->approved_by, $eye_wash->id, EYE_WASH_INSPECTION);

            foreach (range('A', 'M') as $col) {
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
            $sheet->mergeCells('C1:K3');
            $sheet->setCellValue('C1', 'Monthly Eye Wash Inspection INSPECTION CHECKLIST .');
            $sheet->getStyle('C1:K3')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $sheet->getStyle('A1:B3')->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $labelMap = [
                'L1' => ['value' => 'Doc. No.', 'valueCell' => 'M1', 'data' => $document_no->doc_no],
                'L2' => ['value' => 'Issue Dt.', 'valueCell' => 'M2', 'data' => Displaydateformat($document_no->issue_date)],
                'L3' => ['value' => 'Rev. & Dt.', 'valueCell' => 'M3', 'data' => $document_no->rev_dt],
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

            $sheet->mergeCells('A4:D4')->setCellValue('A4', 'Date of Inspection:- ' . Displaydateformat($eye_wash->date_of_inspection));
            $sheet->mergeCells('E4:I4')->setCellValue('E4', 'Location :- ' . getLocationname($eye_wash->location));
            $sheet->mergeCells('J4:M4')->setCellValue('J4', 'Shift:- ' . getShift($eye_wash->shift));

            $sheet->mergeCells('A5:D5')->setCellValue('A5', 'Next Due date:- ' . Displaydateformat($eye_wash->next_due));
            $sheet->mergeCells('E5:I5')->setCellValue('E5', 'Unit:- ' . getUnitname($eye_wash->unit));
            $sheet->mergeCells('J5:M5')->setCellValue('J5', 'Frequency:- ' . getFrequencyname($eye_wash->frequency));

            $sheet->getStyle('A4:M5')->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells('A6:A8')->setCellValue('A6', 'SR. NO');
            $sheet->mergeCells('B6:B8')->setCellValue('B6', 'LOCATION');
            $sheet->mergeCells('C6:D8')->setCellValue('C6', 'RESOURCE CODE');

            $sheet->mergeCells('E6:L6')->setCellValue('E6', 'CHECK ITEMS');
            $sheet->mergeCells('E7:G7')->setCellValue('E7', 'CONDITION');
            $sheet->mergeCells('H7:L7')->setCellValue('H7', 'WATER');

            $sheet->setCellValue('E8', 'VALUE');
            $sheet->setCellValue('F8', 'HANDS-FREE STAY OPEN VALVE');
            $sheet->setCellValue('G8', 'FOOT PEDAL VALVE');
            $sheet->setCellValue('H8', 'Eye Wash & Head Shower');
            $sheet->setCellValue('I8', 'RECEPTACLE');
            $sheet->setCellValue('J8', 'Water QUALITY');
            $sheet->setCellValue('K8', 'PRESSURE');
            $sheet->setCellValue('L8', 'TEMPERATURE');

            $sheet->mergeCells('M6:M8')->setCellValue('M6', 'REMARKS');

            $sheet->getStyle('A6:M8')->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $row = 9;
            $sr = 1;
            foreach ($inspection_data as $detail) {
                $sheet->setCellValue("A{$row}", $sr);
                $sheet->setCellValue("B{$row}", ($detail['location']) ?? '');
                $sheet->mergeCells("C{$row}:D{$row}")->setCellValue("C{$row}", $detail['resource_code'] ?? '');

                $sheet->setCellValue("E{$row}", $detail['value'] ?? '');
                $sheet->setCellValue("F{$row}", $detail['hand_free_stay_open_value'] ?? '');
                $sheet->setCellValue("G{$row}", $detail['foot_pedal_value'] ?? '');
                $sheet->setCellValue("H{$row}", $detail['eyewash_heads_value'] == '1' ? "OK" : 'NOT OK');
                $sheet->setCellValue("I{$row}", $detail['receptacle'] ?? '');

                $qualityLabel = '';

                if ($detail['water'] == GOOD) {
                    $qualityLabel = 'Good';
                } elseif ($detail['water'] == FAIR) {
                    $qualityLabel = 'Fair';
                } elseif ($detail['water'] == POOR) {
                    $qualityLabel = 'Poor';
                }

                $sheet->setCellValue("J{$row}", $qualityLabel);

                $sheet->setCellValue("K{$row}", $detail['pressure'] ?? '');
                $sheet->setCellValue("L{$row}", $detail['temperature'] == '1' ? "ABNORMAL" : 'NORMAL');
                $sheet->setCellValue("M{$row}", $detail['remarks'] ?? '');

                $sheet->getStyle("A{$row}:M{$row}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sr++;
                $row++;
            }

            $signatureRowStart = $row;
            $sheet->getRowDimension($signatureRowStart)->setRowHeight(80);

            $sheet->mergeCells("A{$signatureRowStart}:E{$signatureRowStart}");
            $sheet->getStyle("A{$signatureRowStart}:E{$signatureRowStart}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);

            if ($eye_wash->created_by) {
                // $drawing = new Drawing();
                // $drawing->setName('Signature');
                // $drawing->setDescription('Prepared By');
                // $drawing->setPath($prepared_by_signature);
                // $drawing->setCoordinates("C{$signatureRowStart}");
                // $drawing->setOffsetX(5);
                // $drawing->setOffsetY(5);
                // $drawing->setHeight(40);
                // $drawing->setWorksheet($sheet);
                $sheet->setCellValue("A{$signatureRowStart}", "\n\n\nPrepared By:\n" . getUsername($eye_wash->created_by));
            } else {
                $sheet->setCellValue("A{$signatureRowStart}", "Prepared By:\nInspection not yet started");
            }

            $sheet->mergeCells("F{$signatureRowStart}:I{$signatureRowStart}");
            $sheet->getStyle("F{$signatureRowStart}:I{$signatureRowStart}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);

            if ($eye_wash->verified_by) {
                // $drawing = new Drawing();
                // $drawing->setName('Signature');
                // $drawing->setDescription('Verified By');
                // $drawing->setPath($verified_by_signature);
                // $drawing->setCoordinates("G{$signatureRowStart}");
                // $drawing->setOffsetX(5);
                // $drawing->setOffsetY(5);
                // $drawing->setHeight(40);
                // $drawing->setWorksheet($sheet);
                $sheet->setCellValue("F{$signatureRowStart}", "\n\n\nVerified By:\n" . getUsername($eye_wash->verified_by));
            } else {
                $sheet->setCellValue("F{$signatureRowStart}", "Verified By:\nInspection not yet completed");
            }

            $sheet->mergeCells("J{$signatureRowStart}:M{$signatureRowStart}");
            $sheet->getStyle("J{$signatureRowStart}:M{$signatureRowStart}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);

            if ($eye_wash->approved_by) {
                // $drawing = new Drawing();
                // $drawing->setName('Signature');
                // $drawing->setDescription('Approved By');
                // $drawing->setPath($approved_by_signature);
                // $drawing->setCoordinates("L{$signatureRowStart}");
                // $drawing->setOffsetX(5);
                // $drawing->setOffsetY(5);
                // $drawing->setHeight(40);
                // $drawing->setWorksheet($sheet);
                $sheet->setCellValue("J{$signatureRowStart}", "\n\n\nApproved By:\n" . getUsername($eye_wash->approved_by));
            } else {
                $sheet->setCellValue("J{$signatureRowStart}", "Approved By:\nApproval pending");
            }



            $writer = new Xlsx($spreadsheet);
            $fileName = 'Monthly Eye Wash Inspection.xlsx';
            $filePath = storage_path("app/public/{$fileName}");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/eye-wash-inspection/monthly/list'));
        }
    }
}
