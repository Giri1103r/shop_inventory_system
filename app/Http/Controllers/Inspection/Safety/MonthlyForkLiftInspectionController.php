<?php

namespace App\Http\Controllers\Inspection\Safety;

use Exception;
use App\Models\UploadLog;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Master\Location;
use App\Models\Master\ForkLiftType;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Inspection\Master\Shift;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\Master\Frequency;
use App\Http\Controllers\Admin\AdminController;
use App\Mail\Inspection\Safety\SafetyInspection;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Safety\SafetyStatusLog;
use App\Models\Inspection\Safety\SignatureUpload;
use Spatie\IcalendarGenerator\ValueObjects\RRule;
use App\Models\Inspection\Safety\MonthlyForkLiftInspection;

class MonthlyForkLiftInspectionController extends Controller
{
    private $forklift;
    private $forklift_type;
    private $upload_log;
    private $shift;
    private $location;
    private $unit;
    private $frequency;
    private $statusLog;
    private $signature;
    private $document_reference;

    public function __construct()
    {
        $this->forklift = new MonthlyForkLiftInspection();
        $this->upload_log = new UploadLog();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->unit = new Unit();
        $this->frequency = new Frequency();
        $this->forklift_type = new ForkLiftType();
        $this->statusLog = new SafetyStatusLog();
        $this->document_reference = new InspectionStaticDocno();
        $this->signature = new SignatureUpload();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->forklift->list();
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
                        ->addColumn('date_of_inspection', function ($row) {
                            return Displaydateformat($row->date_of_inspection);
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
                            $btn = '<a href="' . admin_url('safety/forklift-inspection/monthly/view/' . encryptId($row->inspection_id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            if ($row->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($row->inspection_id)) . '/ehs" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if (($row->inspection_status == WAITING_FOR_CAPA_ACTION || $row->inspection_status == L2_MANAGER_REJECTED || $row->inspection_status == EHS_OFFICER_REJECTED || $row->inspection_status == L1_MANAGER_REJECTED) && (CheckUserRole(ROLE_FIRE_ASSOCIATES) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($row->inspection_id)) . '/capa" class="" title="' . __('inspection.capa_action') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_CAPA_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($row->inspection_id)) . '/ehsVerify" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L1_VERIFICATION && (CheckUserRole(ROLE_L1_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($row->inspection_id)) . '/level-one-manager" class="" title="' . __('inspection.l1_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L2_VERIFICATION && (CheckUserRole(ROLE_L2_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($row->inspection_id)) . '/level-two-manager" class="" title="' . __('inspection.l2_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            $btn .= '<a href="' . admin_url('safety/forklift-inspection/monthly/exportViewPdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'inspection_status', 'issue_date', 'date_of_inspection'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {
                    dd($ex);
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
        return view('inspection.Safety.forklift_inspection_monthly.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $checklistQuestions = getCheckListQuestion(FORKLIFT_INSPECTION_MONTHLY_CHECKLIST);
            $options =  getoption(FORKLIFT_INSPECTION_MONTHLY_CHECKLIST);
            $shift  = $this->shift->select('id', 'shift')->where('status', '1')->get();
            $getoption = string_to_array($options->type);
            $location = $this->location->getLocationName();
            $unit = $this->unit->getUnit();
            $frequency = $this->frequency->getFrequency();
            $forklifts = $this->forklift_type->getForkLift();
            $document_no = $this->document_reference->selectUsingName('MonthlyForkliftInspectionChecklist');
            $data = array(
                'checklist_details' => $checklistQuestions,
                'shift' => $shift,
                'getoption' => $getoption,
                'locations' => $location,
                'units' => $unit,
                'frequency' => $frequency,
                'forklifts' => $forklifts,
                'document_no' => $document_no,
            );
            return view('inspection.Safety.forklift_inspection_monthly.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        }
    }

    public function store(Request $request)
    {
        try {

            $rules = [
                'doc_no' => 'required',
                'issue_date' => 'required',
                'inspection_date' => 'required',
                'location_id' => 'required',
                'shift_id' => 'required',
                'next_due' => 'required',
                'unit_id' => 'required',
                'frequency_id' => 'required',
                'identification_no' => 'required',
                'forklift_type' => 'required',
                'capacity' => 'required',
                'signature_upload' => [
                    function ($attribute, $value, $fail) {
                        $user = Auth::user();
                        if (!$user || !$user->signature_upload) {
                            if (empty($value)) {
                                $fail('Signature is required.');
                            }
                        }
                    }
                ],
            ];

            $messages = [
                'doc_no.required' => 'Document number is required.',
                'issue_date.required' => 'Issue Date is Required',
                'inspection_date.required' => 'Inspection  Date is Required',
                'location_id.required' => 'Location is Required',
                'shift_id.required' => 'Shift is Required',
                'next_due.required' => 'Next due date is Required',
                'unit_id.required' => 'Unit is Required',
                'frequency_id.required' => 'Frequency is Required',
                'forklift_type.required' => 'Forklift Type is Required',
                'capacity.required' => 'Capacity is Required',
                'identification_no.required' => 'Identification Number is Required',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $forklift_inspection = $this->forklift->store();
            $id = $forklift_inspection->id;
            $signature_update = $this->signature->signatureUpload(MONTHLY_FORKLIFT_INSPECTION, $forklift_inspection->id);
            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'SAFETY INSPECTION';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Monthly ForkLift Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $forklift_inspection->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safety/forklift-inspection/monthly/view/' . encryptId($forklift_inspection->id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Monthly ForkLift Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($id) . '/ehs');
                $details = array(
                    'safety_type' => 'Monthly Forklift Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $forklift_inspection
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }

            $insert_array = [
                'type' => MONTHLY_FORKLIFT_INSPECTION,
                'inspection_id' => $forklift_inspection->id,
                'from_status' => 0,
                'to_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'created_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);


            Session::flash('success', __('common.created_msg'));
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->forklift->selectOne($id);
            $status_log = $this->statusLog->selectOne($id, MONTHLY_FORKLIFT_INSPECTION);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);
            $data = [
                'inspection_details' => $inspection_details,
                'status_log' => $status_log,
                'document_no' => $document_no,
            ];
            return view('inspection.Safety.forklift_inspection_monthly.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        }
    }

    public function approvals(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $approval_type = $request->employee_type;
            $inspection_details = $this->forklift->selectOne($id);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);
            $data = [
                'inspection_details' => $inspection_details,
                'approval_type' => $approval_type,
                'document_no' => $document_no,
            ];
            return view('inspection.Safety.forklift_inspection_monthly.approval', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        }
    }

    public function EHSOfficerSubmit(Request $request)
    {
        try {
            $request = Request();
            $id = decryptId($request->id);
            $inspection_updates = $this->forklift->EHSOfficerUpdate($id);
            $signature_update = $this->signature->signatureUpload(MONTHLY_FORKLIFT_INSPECTION, $id);
            $inspection_details = $this->forklift->selectOne($id);
            if ($request->is_passed == 1) {
                $message = 'ForkLift Inspeciton Approved Successfully';
                $web_link =   admin_url('safety/forklift-inspection/monthly/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
            } else {
                $message = 'Inspection Recommended for the CAPA Action';
                $web_link =   admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = WAITING_FOR_CAPA_ACTION;
            }
            $userIds = [
                'users' => $inspection_details->created_by,
            ];
            $mailsubject = 'SAFETY INSPECTION';
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
            $url = admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($id) . '/ehs');
            $details = array(
                'safety_type' => 'Monthly Forklift Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new SafetyInspection($details));

            $insert_array = [
                'type' => MONTHLY_FORKLIFT_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('Something Went Wrong!'));
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        }
    }

    public function CAPASubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $forklift_inspection = $this->forklift->capaSubmit($id);
            $inspection_details = $this->forklift->selectOne($id);
            $signature_update = $this->signature->signatureUpload(MONTHLY_FORKLIFT_INSPECTION, $id);
            $ehsOfficers = $inspection_details->verified_by;
            $userIds = [
                'users' => $ehsOfficers,
            ];
            $mailsubject = 'Safety Inspection';
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
                'web_link' =>  admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($inspection_details->id)) . '/ehsVerify',
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $user = $inspection_details->verified_by;
            $email_id = getUseremail($user);
            $url = admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($id) . '/ehsVerify');
            $details = array(
                'safety_type' => 'Monthly Forklift Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => 'CAPA Action Completed by the Fire Associates',
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new SafetyInspection($details));

            $insert_array = [
                'type' => MONTHLY_FORKLIFT_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_ACTION,
                'to_status' => WAITING_FOR_CAPA_VERIFICATION,
                'created_by' => Auth::id(),
                'remarks' => $request->capa_remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('safety/forklift-inspecttion/monthly/list'));
        }
    }

    public function CAPAVerifySubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->remarks;
            $forklift_inspection = $this->forklift->capaVerifySubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(MONTHLY_FORKLIFT_INSPECTION, $id);
            $inspection_details = $this->forklift->selectOne($id);
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = WAITING_FOR_L1_VERIFICATION;
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = [$inspection_details->created_by];
                $to_status = EHS_OFFICER_REJECTED;
            }
            $mailsubject = 'SAFETY INSPECTION';
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
                    'safety_type' => 'Monthly Forklift Inspection',
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
                'type' => MONTHLY_FORKLIFT_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        }
    }

    public function levelOneManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_one_manager;
            $forklift_inspection = $this->forklift->levelOneManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(MONTHLY_FORKLIFT_INSPECTION, $id);
            $inspection_details = $this->forklift->selectOne($id);
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/level-two-manager');
                $user = GetLevelTwoManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
                $to_status = WAITING_FOR_L2_VERIFICATION;
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = [$inspection_details->created_by];
                $to_status = L1_MANAGER_REJECTED;
            }
            $mailsubject = 'SAFETY INSPECTION';
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
                    'safety_type' => 'Monthly Forklift Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }

            $insert_array = [
                'type' => MONTHLY_FORKLIFT_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L1_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_one_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        }
    }

    public function levelTwoManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_two_manager;
            $forklift_inspection = $this->forklift->levelTwoManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(MONTHLY_FORKLIFT_INSPECTION, $id);
            $inspection_details = $this->forklift->selectOne($id);
            if ($status == 1) {
                $message = 'ForkLift Inspeciton Approved Successfully!';
                $web_link =   admin_url('safety/forklift-inspection/monthly/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by], [$inspection_details->l2_manager_verified_by]);
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = L2_MANAGER_REJECTED;
            }

            $mailsubject = 'SAFETY INSPECTION';
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
                    'safety_type' => 'Monthly Forklift Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }

            $insert_array = [
                'type' => MONTHLY_FORKLIFT_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L2_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_two_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        }
    }


    public function ExportExcel(Request $request)
    {

        try {
            $allData = $this->forklift->exportdata();
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

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->doc_no;
                $export[] =  $data->issue_date;
                $export[] = $data->revision_data;
                $export[] =  getInspectionStatus($data->inspection_status);;
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);
                $exportData[] = $export;
                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Monthly ForkLift Inspection.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('safety/fork-lift-inspection/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->forklift->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }elseif(count($allData) > 20){
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }

            $data = array(
                'content' => $allData,
                'pagetitle' => "Monthly ForkLift Inspection",
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

            $view = view('inspection.Safety.forklift_inspection_monthly.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Monthly Forklift Inspection.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('safety/forklift-inspection/list'));
        }
    }

    public function exportViewPdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $status_log = $this->statusLog->selectOne($id, MONTHLY_FORKLIFT_INSPECTION);
                $forklift_details = $this->forklift->selectOne($id);
                $document_no = $this->document_reference->selectOne($forklift_details->document_reference_id);

                $data = [
                    'status_log' => $status_log,
                    'forklift_details' => $forklift_details,
                    'pagetitle' => "Monthly ForkLift Inspection",
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

            $html = view('inspection.safety.forklift_inspection_monthly.viewpdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "ForkLift Inspection.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }
}
