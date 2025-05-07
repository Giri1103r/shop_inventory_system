<?php

namespace App\Http\Controllers\Inspection\Safety;

use Exception;
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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\SimpleExcel\SimpleExcelWriter;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Http\Controllers\Admin\AdminController;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Mail\Inspection\Safety\SafetyInspection;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Safety\SafetyStatusLog;
use App\Models\Inspection\Safety\SignatureUpload;
use App\Models\Inspection\Safety\SafetyGalleryInspection;

class SafetyGalleryInsepctionController extends Controller
{
    private $safetygallery;
    private $shift;
    private $location;
    private $unit;
    private $frequency;
    private $statusLog;
    private $signature;
    private $document_reference;


    public function __construct()
    {
        $this->safetygallery = new SafetyGalleryInspection();
        $this->location = new Location();
        $this->shift = new Shift();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->unit = new Unit();
        $this->statusLog = new SafetyStatusLog();
        $this->signature = new SignatureUpload();
        $this->document_reference = new InspectionStaticDocno();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->safetygallery->list();
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
                        ->addColumn('inspection_date', function ($row) {
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
                            $btn = '<a href="' . admin_url('safety/safety-gallery-inspection/view/' . encryptId($row->inspection_id)) . '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            if ($row->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/safety-gallery-inspection/verification/' . encryptId($row->inspection_id)) . '/ehs" class="me-1" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if (($row->inspection_status == WAITING_FOR_CAPA_ACTION || $row->inspection_status == L2_MANAGER_REJECTED || $row->inspection_status == EHS_OFFICER_REJECTED || $row->inspection_status == L1_MANAGER_REJECTED) && (CheckUserRole(ROLE_FIRE_ASSOCIATES) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/safety-gallery-inspection/verification/' . encryptId($row->inspection_id)) . '/capa" class="me-1" title="' . __('inspection.capa_action') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_CAPA_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/safety-gallery-inspection/verification/' . encryptId($row->inspection_id)) . '/ehsVerify" class="me-1" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L1_VERIFICATION && (CheckUserRole(ROLE_L1_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/safety-gallery-inspection/verification/' . encryptId($row->inspection_id)) . '/level-one-manager" class="me-1" title="' . __('inspection.l1_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L2_VERIFICATION && (CheckUserRole(ROLE_L2_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/safety-gallery-inspection/verification/' . encryptId($row->inspection_id)) . '/level-two-manager" class="me-1" title="' . __('inspection.l2_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            $btn .= '<a href="' . admin_url('safety/safety-gallery-inspection/exportViewPdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';
                            $btn .= '<a href="' . admin_url('safety/safety-gallery-inspection/generalexcel/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="EXCEL">
                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
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
                    report($ex);
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }

        $location = $this->location->getLocationName();
        $unit = $this->unit->getUnit();
        $shifts = $this->shift->getShiftname();

        $data = array(
            'locations' => $location,
            'units' => $unit,
            'shifts' => $shifts,
        );

        return view('inspection.Safety.safety_gallery_inspection.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $checklistQuestions = getCheckListQuestion(SAFETY_GALLERY_INSPECTION_CHECKLIST);
            $options =  getoption(SAFETY_GALLERY_INSPECTION_CHECKLIST);
            $getoption = string_to_array($options->type);
            $location = $this->location->getLocationName();
            $unit = $this->unit->getUnit();
            $document_no = $this->document_reference->selectUsingName('SafetyGalleryInspection');

            $data = array(
                'checklist_details' => $checklistQuestions,
                'getoption' => $getoption,
                'locations' => $location,
                'units' => $unit,
                'document_no' => $document_no,
            );
            return view('inspection.Safety.safety_gallery_inspection.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/safety-gallery-inspection/list'));
        }
    }

    public function store(Request $request)
    {
        try {

            $rules = [
                'doc_no' => 'required',
                'issue_date' => 'required',
                'resource_code' => 'required',
                'location_id' => 'required',
                'unit_id' => 'required',
                'inspection_date' => 'required',
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
                'resource_code.required' => 'Resource code is Required',
                'location_id.required' => 'Location is Required',
                'inspection_date.required' => 'Inspection Date is Required',
                'unit_id.required' => 'Unit is Required',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $safety_gallery_inspection = $this->safetygallery->store();
            $id = $safety_gallery_inspection->id;
            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $signature_update = $this->signature->signatureUpload(SAFETY_GALLERY_INSPECTION, $safety_gallery_inspection->id);
            $mailsubject = 'Safety Gallery inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Safety Gallery Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $safety_gallery_inspection->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safety/safety-gallery-inspection/view/' . encryptId($safety_gallery_inspection->id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Safety Gallery Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('safety/safety-gallery-inspection/verification/' . encryptId($id) . '/ehs');
                $details = array(
                    'safety_type' => 'Safety Gallery Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $safety_gallery_inspection
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }

            $insert_array = [
                'type' => SAFETY_GALLERY_INSPECTION,
                'inspection_id' => $safety_gallery_inspection->id,
                'from_status' => 0,
                'to_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'created_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.created_msg'));
            return redirect(admin_url('safety/safety-gallery-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/safety-gallery-inspection/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->safetygallery->selectOne($id);
            $status_log = $this->statusLog->selectOne($id, SAFETY_GALLERY_INSPECTION);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);

            $data = [
                'inspection_details' => $inspection_details,
                'status_log' => $status_log,
                'document_no' => $document_no,

            ];
            return view('inspection.Safety.safety_gallery_inspection.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/safety-gallery-inspection/list'));
        }
    }

    public function approvals(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->safetygallery->selectOne($id);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);

            $data = [
                'inspection_details' => $inspection_details,
                'document_no' => $document_no,

            ];
            return view('inspection.Safety.safety_gallery_inspection.approval', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/safety-gallery-inspection/list'));
        }
    }

    public function EHSOfficerSubmit(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $inspection_updates = $this->safetygallery->EHSOfficerUpdate($id);
            $signature_update = $this->signature->signatureUpload(SAFETY_GALLERY_INSPECTION, $id);
            $inspection_details = $this->safetygallery->selectOne($id);
            if ($request->is_passed == 1) {
                $message = 'Safetygallery Inspeciton Approved Successfully';
                $web_link =   admin_url('safety/safety-gallery-inspection/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
            } else {
                $message = 'Inspection Recommended for the CAPA Action';
                $web_link =   admin_url('safety/safety-gallery-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = WAITING_FOR_CAPA_ACTION;
            }
            $userIds = [
                'users' => $inspection_details->created_by,
            ];
            $mailsubject = 'Safety Gallery inspection';
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
            $url = admin_url('safety/safety-gallery-inspection/monthly/verification/' . encryptId($id) . '/capa');
            $details = array(
                'safety_type' => 'Safety Gallery Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new SafetyInspection($details));

            $insert_array = [
                'type' => SAFETY_GALLERY_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('safety/safety-gallery-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('Something Went Wrong!'));
            return redirect(admin_url('safety/safety-gallery-inspection/list'));
        }
    }

    public function CAPASubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $safety_gallery_inspection = $this->safetygallery->capaSubmit($id);
            $inspection_details = $this->safetygallery->selectOne($id);
            $signature_update = $this->signature->signatureUpload(SAFETY_GALLERY_INSPECTION, $id);
            $ehsOfficers = $inspection_details->verified_by;
            $userIds = [
                'users' => $ehsOfficers,
            ];
            $mailsubject = 'Safety Gallery inspection';
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
                'web_link' =>  admin_url('safety/safety-gallery-inspection/verification/' . encryptId($inspection_details->id)) . '/ehsVerify',
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $user = $inspection_details->verified_by;
            $email_id = getUseremail($user);
            // $url = admin_url('safety/forklift-inspection/monthly/verification/' . encryptId($id) . '/ehs');
            $details = array(
                'safety_type' => 'Safety Gallery Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => 'CAPA Action Completed by the Fire Associates',
                // 'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new SafetyInspection($details));

            $insert_array = [
                'type' => SAFETY_GALLERY_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_ACTION,
                'to_status' => WAITING_FOR_CAPA_VERIFICATION,
                'created_by' => Auth::id(),
                'remarks' => $request->capa_remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('safety/safety-gallery-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('safety/safety-gallery-inspection/list'));
        }
    }

    public function CAPAVerifySubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->remarks;
            $safety_gallery_inspection = $this->safetygallery->capaVerifySubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(SAFETY_GALLERY_INSPECTION, $id);
            $inspection_details = $this->safetygallery->selectOne($id);
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('safety/safety-gallery-inspection/verification/' . encryptId($inspection_details->id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = WAITING_FOR_L1_VERIFICATION;
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('safety/safety-gallery-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = [$inspection_details->created_by];
                $to_status = EHS_OFFICER_REJECTED;
            }

            $mailsubject = 'Safety Gallery inspection';
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
                    'safety_type' => 'Safety Gallery Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }

            $insert_array = [
                'type' => SAFETY_GALLERY_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('safety/safety-gallery-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('safety/safety-gallery-inspection/list'));
        }
    }

    public function levelOneManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_one_manager;
            $safety_gallery_inspection = $this->safetygallery->levelOneManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(SAFETY_GALLERY_INSPECTION, $id);
            $inspection_details = $this->safetygallery->selectOne($id);
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('safety/safety-gallery-inspection/verification/' . encryptId($inspection_details->id) . '/level-two-manager');
                $user = GetLevelTwoManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by], [$inspection_details->verified_by]);
                $to_status = WAITING_FOR_L2_VERIFICATION;
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('safety/safety-gallery-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = [$inspection_details->created_by];
                $to_status = L1_MANAGER_REJECTED;
            }

            $mailsubject = 'Safety Gallery inspection';
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
                    'safety_type' => 'Safety Gallery Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }

            $insert_array = [
                'type' => SAFETY_GALLERY_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L1_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_one_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('safety/safety-gallery-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('safety/safety-gallery-inspection/list'));
        }
    }

    public function levelTwoManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_two_manager;
            $safety_gallery_inspection = $this->safetygallery->levelTwoManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(SAFETY_GALLERY_INSPECTION, $id);
            $inspection_details = $this->safetygallery->selectOne($id);
            if ($status == 1) {
                $message = 'Safety Gallery Inspeciton Approved Successfully!';
                $web_link =   admin_url('safety/safety-gallery-inspection/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('safety/safety-gallery-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = L2_MANAGER_REJECTED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);

            }

            $mailsubject = 'Safety Gallery inspection';
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
                    'safety_type' => 'Safety Gallery Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }

            $insert_array = [
                'type' => SAFETY_GALLERY_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L2_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_two_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('safety/safety-gallery-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('safety/safety-gallery-inspection/list'));
        }
    }

    public function UniqueCheck(Request $request)
    {
        if ($request->ajax()) {
            $resource_code = $request->resource_code;
            $id = decryptId($request->id);
            if ($request->id == '') {
                $isUnique = $this->safetygallery->UniqueCheck($resource_code);
                return response()->json($isUnique);
            } else {
                $data = [
                    'category_name' => $resource_code,
                    'id' => $id,
                ];
                $isUnique = $this->safetygallery->existUniqueCheck($data);
                return response()->json($isUnique);
            }
        }
    }

    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->safetygallery->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $currentRow = 1;

            foreach (range('A', 'P') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            foreach ($allData as $inspection) {

                $startRow = $currentRow;
                $user_responses = json_decode($inspection->responses, true);
                $inspection_type = SAFETY_GALLERY_INSPECTION;

                $createdBySig = GetSafetySignature($inspection->checked_by, $inspection->inspection_id, $inspection_type);
                $verifiedBySig = GetSafetySignature($inspection->verified_by, $inspection->inspection_id, $inspection_type);
                $approvedBySig = GetSafetySignature($inspection->approved_by, $inspection->inspection_id, $inspection_type);
                $document_no = $this->document_reference->selectOne($inspection->document_reference_id);

                $leftLogoPath = public_path('assets/images/logo-dark.png');
                $sheet->getRowDimension($currentRow)->setRowHeight(40);

                if (file_exists($leftLogoPath)) {
                    $sheet->mergeCells("A$currentRow:D" . ($currentRow + 2));
                    $drawing = new Drawing();
                    $drawing->setPath($leftLogoPath);
                    $drawing->setCoordinates("A{$currentRow}");
                    $drawing->setOffsetX(80);
                    $drawing->setHeight(70);
                    $drawing->setWorksheet($sheet);
                    $sheet->getStyle("A$currentRow:D" . ($currentRow + 2))->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                }

                $sheet->mergeCells("L{$currentRow}:M{$currentRow}")->setCellValue("L{$currentRow}", 'Doc. No.');
                $sheet->mergeCells("L" . ($currentRow + 1) . ":M" . ($currentRow + 1))->setCellValue("L" . ($currentRow + 1), 'Issue Dt.');
                $sheet->mergeCells("L" . ($currentRow + 2) . ":M" . ($currentRow + 2))->setCellValue("L" . ($currentRow + 2), 'Rev. & Dt.');
                $sheet->mergeCells("N{$currentRow}:P{$currentRow}")->setCellValue("N{$currentRow}", $document_no->doc_no ?? '-');
                $sheet->mergeCells("N" . ($currentRow + 1) . ":P" . ($currentRow + 1))->setCellValue("N" . ($currentRow + 1), Displaydateformat($document_no->issue_date ?? null));
                $sheet->mergeCells("N" . ($currentRow + 2) . ":P" . ($currentRow + 2))->setCellValue("N" . ($currentRow + 2), $document_no->rev_dt ?? '-');
                $sheet->getStyle("L{$currentRow}:P" . ($currentRow + 2))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->mergeCells("E{$currentRow}:K" . ($currentRow + 2));
                $sheet->setCellValue("E{$currentRow}", "SAFETY GALLERY INSPECTION CHECKLIST PN INTERNATIONAL PVT. LTD.");
                $sheet->getStyle("E{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $currentRow += 3;

                $sheet->mergeCells("A$currentRow:G$currentRow")->setCellValue("A$currentRow", "DATE OF INSPECTION :- " . Displaydateformat($inspection->date_of_inspection));
                $sheet->mergeCells("H$currentRow:P$currentRow")->setCellValue("H$currentRow", "LOCATION :- " . getLocationname($inspection->location));
                $sheet->getStyle("A$currentRow:P$currentRow")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $currentRow++;

                $sheet->mergeCells("A$currentRow:G$currentRow")->setCellValue("A$currentRow", "RESOURCE CODE :- " . ($inspection->resource_code));
                $sheet->mergeCells("H$currentRow:P$currentRow")->setCellValue("H$currentRow", "UNIT :- " . getUnitname($inspection->unit ?? '-'));
                $sheet->getStyle("A$currentRow:P$currentRow")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $currentRow++;

                $sheet->mergeCells("A$currentRow:E$currentRow")->setCellValue("A$currentRow", "SR. NO.");
                $sheet->mergeCells("F$currentRow:K$currentRow")->setCellValue("F$currentRow", "CHECK ITEMS\n(DESCRIPTION / STATION / REMARKS)");
                $sheet->mergeCells("L$currentRow:M$currentRow")->setCellValue("L$currentRow", "STATUS (YES/NO)");
                $sheet->mergeCells("N$currentRow:P$currentRow")->setCellValue("N$currentRow", "REMARK");
                $sheet->getStyle("A$currentRow:P$currentRow")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle("F$currentRow:K$currentRow")->getAlignment()->setWrapText(true);
                $currentRow++;

                $srNo = 1;
                foreach ($user_responses as $detail) {
                    $sheet->mergeCells("A$currentRow:E$currentRow")->setCellValue("A$currentRow", $srNo++);
                    $sheet->mergeCells("F$currentRow:K$currentRow")->setCellValue("F$currentRow", GetChecklistTypeDate($detail['question_id']));

                    $symbolCell = "L$currentRow";
                    $tick = strtoupper(trim($detail['answer'] ?? '')) === 'YES' ? '✔️' : '❌';
                    $tickColor = strtoupper(trim($detail['answer'] ?? '')) === 'YES' ? '00B050' : 'FF0000';

                    $sheet->mergeCells("L$currentRow:M$currentRow")->setCellValue($symbolCell, $tick);
                    $sheet->getStyle($symbolCell)->getFont()->getColor()->setARGB($tickColor);
                    $sheet->getStyle($symbolCell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Remarks
                    $sheet->mergeCells("N$currentRow:P$currentRow")->setCellValue("N$currentRow", $detail['remarks'] ?? '-');
                    $sheet->getStyle("F$currentRow:K$currentRow")->getAlignment()->setWrapText(true);
                    $sheet->getStyle("N$currentRow:P$currentRow")->getAlignment()->setWrapText(true);
                    $sheet->getRowDimension($currentRow)->setRowHeight(-1);

                    $sheet->getStyle("A$currentRow:P$currentRow")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $currentRow++;
                }

                $signatureRow = $currentRow;
                $sheet->getRowDimension($signatureRow)->setRowHeight(60);

                $approvedByName = getUserName($inspection->checked_by);
                $sheet->mergeCells("A$signatureRow:E$signatureRow")->setCellValue("A$signatureRow", "CHECKED AND PREPARED BY: $approvedByName");
                if (file_exists($createdBySig)) {
                    $drawing = new Drawing();
                    $drawing->setPath($createdBySig);
                    $drawing->setCoordinates("B{$signatureRow}");
                    $drawing->setOffsetX(60);
                    $drawing->setOffsetY(10);
                    $drawing->setHeight(50);
                    $drawing->setWorksheet($sheet);
                }

                $approvedByName = getUserName($inspection->verified_by);
                $sheet->mergeCells("F$signatureRow:K$signatureRow")->setCellValue("F$signatureRow", "VERIFIED BY: $approvedByName");
                if (file_exists($verifiedBySig)) {
                    $drawing = new Drawing();
                    $drawing->setPath($verifiedBySig);
                    $drawing->setCoordinates("F{$signatureRow}");
                    $drawing->setOffsetX(60);
                    $drawing->setOffsetY(10);
                    $drawing->setHeight(50);
                    $drawing->setWorksheet($sheet);
                }

                $approvedByName = getUserName($inspection->approved_by);
                $sheet->mergeCells("L$signatureRow:P$signatureRow")->setCellValue("L$signatureRow", "APPROVED BY: $approvedByName");
                if (file_exists($approvedBySig)) {
                    $drawing = new Drawing();
                    $drawing->setPath($approvedBySig);
                    $drawing->setCoordinates("M{$signatureRow}");
                    $drawing->setOffsetX(60);
                    $drawing->setOffsetY(10);
                    $drawing->setHeight(50);
                    $drawing->setWorksheet($sheet);
                }

                $sheet->getRowDimension($signatureRow)->setRowHeight(60);
                $sheet->getStyle("A$signatureRow:P$signatureRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'wrapText' => true,
                        'indent' => 1,
                    ],
                ]);

                $sheet->getStyle("A{$startRow}:P{$currentRow}")->applyFromArray([
                    'borders' => [
                        'top'    => ['borderStyle' => Border::BORDER_THICK],
                        'bottom' => ['borderStyle' => Border::BORDER_THICK],
                        'left'   => ['borderStyle' => Border::BORDER_THICK],
                        'right'  => ['borderStyle' => Border::BORDER_THICK],
                    ],
                ]);
                $currentRow = $signatureRow + 4;
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'SAFETY GALLERY Inspection.xlsx';
            $filePath = storage_path("app/public/{$fileName}");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('safety/safety-gallery-inspection/list'));
        }
    }


    public function ExportPdf(Request $request)
    {

        try {
            $allData = $this->safetygallery->exportdata();
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
                'pagetitle' => "Safety Gallery Inspection",
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

            $view = view('inspection.Safety.safety_gallery_inspection.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Safety Gallery Inspection.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('safety/safety-gallery-inspection/list'));
        }
    }


    public function exportViewPdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $status_log = $this->statusLog->selectOne($id, SAFETY_GALLERY_INSPECTION);
                $forklift_details = $this->safetygallery->selectOne($id);
                $document_no = $this->document_reference->selectOne($forklift_details->document_reference_id);

                $data = [
                    'status_log' => $status_log,
                    'forklift_details' => $forklift_details,
                    'pagetitle' => "Safety Gallery Inspection",
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

            $html = view('inspection.safety.safety_gallery_inspection.viewpdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Safety Gallery Inspection.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
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

            $forklift = $this->safetygallery->selectOne($id);
            $user_response = json_decode($forklift->responses, true);
            $inspection_type = SAFETY_GALLERY_INSPECTION;
            $inspection_created_by = GetSafetySignature($forklift->created_by, $forklift->id, $inspection_type);
            $inspection_verified_by = GetSafetySignature($forklift->verified_by, $forklift->id, $inspection_type);
            $inspection_approved_by = GetSafetySignature($forklift->approved_by, $forklift->id, $inspection_type);
            $document_no = $this->document_reference->selectOne($forklift->document_reference_id);

            $sheet->getDefaultColumnDimension()->setWidth(14);
            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $sheet->mergeCells("A1:C3");
            $sheet->getStyle("A1:C3")->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ]
            ]);
            $logoPath = public_path('assets/images/logo-dark.png');
            if (file_exists($logoPath)) {
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setPath($logoPath);
                $drawing->setCoordinates("B1");
                $drawing->setOffsetX(25);
                $drawing->setOffsetY(10);
                $drawing->setWidth(90);
                $drawing->setHeight(50);
                $drawing->setWorksheet($sheet);
            }

            $sheet->mergeCells("D1:H3")->setCellValue("D1", "Safety Gallery Inspection CHECKLIST PN INTERNATIONAL PVT. LTD.");
            $sheet->getStyle("D1")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $sheet->getRowDimension(1)->setRowHeight(20);

            $sheet->mergeCells("I1:K1")->setCellValue("I1", 'Doc. No.');
            $sheet->mergeCells("I2:K2")->setCellValue("I2", 'Issue Dt.');
            $sheet->mergeCells("I3:K3")->setCellValue("I3", 'Rev. & Dt.');
            $sheet->mergeCells("L1:M1")->setCellValue("L1", $document_no->doc_no);
            $sheet->mergeCells("L2:M2")->setCellValue("L2", Displaydateformat($document_no->issue_date));
            $sheet->mergeCells("L3:M3")->setCellValue("L3", $document_no->rev_dt);
            $sheet->getStyle("I1:M3")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells("A4:F4")->setCellValue("A4", "DATE OF INSPECTION :- " . Displaydateformat($forklift->date_of_inspection));
            $sheet->mergeCells("G4:M4")->setCellValue("G4", "LOCATION :- " . getLocationname($forklift->location));
            $sheet->mergeCells("A5:F5")->setCellValue("A5", "RESOURCE CODE :- " . ($forklift->resource_code));
            $sheet->mergeCells("G5:M5")->setCellValue("G5", "UNIT :- " . getUnitname($forklift->unit ?? '-'));

            $sheet->getStyle("A4:M5")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells("A6:B6")->setCellValue("A6", "SR. NO.");
            $sheet->mergeCells("C6:I6")->setCellValue("C6", "CHECK ITEMS");
            $sheet->mergeCells("J6:K6")->setCellValue("J6", "STATUS (YES/NO)");
            $sheet->mergeCells("L6:M6")->setCellValue("L6", "REMARK");
            $sheet->getStyle("A6:M6")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $row = 7;
            $srNo = 1;
            foreach ($user_response as $questions) {
                $sheet->mergeCells("A{$row}:B{$row}")->setCellValue("A{$row}", $srNo);
                $sheet->mergeCells("C{$row}:I{$row}")->setCellValue("C{$row}", GetChecklistTypeDate($questions['question_id']));

                $statusSymbol = '-';
                $statusColor = null;
                $responseText = $questions['answer'] ?? '';
                if ($responseText === 'YES') {
                    $statusSymbol = '✓';
                    $statusColor = '008000';
                } elseif (in_array($responseText, ['NO', 'N/A'])) {
                    $statusSymbol = 'X';
                    $statusColor = 'FF0000';
                } else {
                    $statusSymbol = $questions['response'] ?? '-';
                }

                $sheet->mergeCells("J{$row}:K{$row}")->setCellValue("J{$row}", $statusSymbol);
                if ($statusColor) {
                    $sheet->getStyle("J{$row}")->applyFromArray([
                        'font' => ['color' => ['rgb' => $statusColor]],
                    ]);
                }

                $sheet->mergeCells("L{$row}:M{$row}")->setCellValue("L{$row}", $questions['remarks'] ?? '-');

                $sheet->getStyle("A{$row}:M{$row}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $row++;
                $srNo++;
            }

            $sheet->mergeCells("A{$row}:D{$row}")->setCellValue("A{$row}", "CHECKED AND PREPARED BY :- ");
            $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            if (file_exists($inspection_created_by)) {
                $drawing = new Drawing();
                $drawing->setName('CHECKED AND PREPARED BY');
                $drawing->setPath($inspection_created_by);
                $drawing->setCoordinates("B{$row}");
                $drawing->setOffsetX(80);
                $drawing->setOffsetY(15);
                $drawing->setWidth(120);
                $drawing->setHeight(50);
                $drawing->setWorksheet($sheet);
                $sheet->getRowDimension($row)->setRowHeight(60);
            }

            $sheet->mergeCells("E{$row}:I{$row}")->setCellValue("E{$row}", "VERIFIED BY :- ");
            $sheet->getStyle("E{$row}:I{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            if (file_exists($inspection_verified_by)) {
                $drawing = new Drawing();
                $drawing->setName('VERIFIED BY');
                $drawing->setPath($inspection_verified_by);
                $drawing->setCoordinates("F{$row}");
                $drawing->setOffsetX(80);
                $drawing->setOffsetY(15);
                $drawing->setWidth(120);
                $drawing->setHeight(50);
                $drawing->setWorksheet($sheet);
                $sheet->getRowDimension($row)->setRowHeight(60);
            }

            $sheet->mergeCells("J{$row}:M{$row}")->setCellValue("J{$row}", "APPROVED BY :- ");
            $sheet->getStyle("J{$row}:M{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            if (file_exists($inspection_approved_by)) {
                $drawing = new Drawing();
                $drawing->setName('APPROVED BY');
                $drawing->setPath($inspection_approved_by);
                $drawing->setCoordinates("K{$row}");
                $drawing->setOffsetX(80);
                $drawing->setOffsetY(15);
                $drawing->setWidth(120);
                $drawing->setHeight(50);
                $drawing->setWorksheet($sheet);
                $sheet->getRowDimension($row)->setRowHeight(60);
            }


            $writer = new Xlsx($spreadsheet);
            $fileName = 'Safety Gallery Inspection.xlsx';
            $filePath = storage_path("app/public/{$fileName}");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        }
    }
}
