<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use App\Mail\Inspection\Ohc\DailyDepartmentFirstAidbox as OhcDailyDepartmentFirstAidbox;
use App\Mail\Inspection\Ohc\DailyDepartmentFirstAidboxEmail;
use App\Mail\Inspection\Ohc\OccupationalHealthEmail;
use App\Models\Inspection\InspectionStaticDocno;
use Illuminate\Http\Request;
use App\Models\Master\Department;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\Ohc\InspectionOhcStatuslog;

use App\Models\Inspection\Ohc\DailyDepartmentFirstAidBox;
use App\Models\Inspection\Ohc\DailyDepartmentFirstAidBoxDetails;
use App\Models\Inspection\Ohc\Master\FirstAidEquipment;
use App\Models\OhcManagement\Master\CertifiedFirstAider;
use App\Models\OhcManagement\Master\FirstAidLocation;
use App\Models\OhcManagement\Report\Inventory;
use App\Models\UploadLog;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inspection\Ohc\OhcSignature;
use Spatie\SimpleExcel\SimpleExcelWriter;



use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class DailyDepartmentFirstAidBoxController extends Controller
{
    private $upload_log;
    private $unit;
    private $shift;
    private $department;
    private $daily_department_first_aid_box_details;
    private $user;
    private $daily_department_first_aid_box;
    private $First_aid;
    private $certified_First_aid;
    private $inspection_ohc_status_log;
    private $signature;
    private $document_reference;
    private $inventory;
    private $medicine;

    private $location;
    public function __construct()
    {

        $this->upload_log = new UploadLog();
        $this->unit = new Unit();
        $this->department = new Department();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->daily_department_first_aid_box_details = new DailyDepartmentFirstAidBoxDetails();
        $this->First_aid = new FirstAidLocation();
        $this->medicine = new FirstAidEquipment();
        $this->certified_First_aid = new CertifiedFirstAider();
        $this->signature = new OhcSignature();
        $this->document_reference = new InspectionStaticDocno();
        $this->daily_department_first_aid_box = new DailyDepartmentFirstAidBox();
        $this->inventory = new Inventory();
        $this->user = new User();
        $this->inspection_ohc_status_log = new InspectionOhcStatuslog();
    }


    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->daily_department_first_aid_box_details->list();

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
                        ->addColumn('inspection_created_at', function ($row) {
                            return Displaydateformat($row->inspection_created_at);
                        })
                        ->addColumn('unit', function ($row) {
                            return getUnitname($row->unit);
                        })
                        ->addColumn('department', function ($row) {
                            return getDepartment($row->department);
                        })
                        ->addColumn('shift', function ($row) {
                            return getshift($row->shift);
                        })
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('approve_status', function ($row) {
                            $text = '';
                            switch ($row->approve_status) {
                                case MEDICAL_ASSISTANT_APPROVAL_PENDING:
                                    $text = "<span class='badge bg-info rounded' style='font-size: 1.0em;'>Floor Manager / Medical Assistant Approval Pending</span>";
                                    break;
                                case MEDICAL_ASSISTANT_APPROVED:
                                    $text = "<span class='badge bg-success rounded' style='font-size: 1.0em;'>Floor Manager/ Medical Assistant Approved</span>";
                                    break;
                                case MEDICAL_ASSISTANT_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>Floor Manager/ Medical Assistant Rejected</span>";
                                    break;

                                default:
                                    $text = "<span class='badge rounded-pill text-bg-warning'>Unknown</span>";
                            }
                            return $text;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn .= '<a href="' . admin_url('ohc/first-aid-box/daily-departmental/view/' . encryptId($row->inspection_id)) . '" class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a>';

                            if ((checkUserRole(ROLE_SUPERADMIN) && $row->approve_status == MEDICAL_ASSISTANT_APPROVAL_PENDING) || (checkUserRole(ROLE_MEDICAL_ASSISTANT) && $row->approve_status == MEDICAL_ASSISTANT_APPROVAL_PENDING) || (checkUserRole(ROLE_FLOOR_MANAGER) && $row->approve_status == MEDICAL_ASSISTANT_APPROVAL_PENDING)) {
                                $btn .= '<a href="' . admin_url('ohc/first-aid-box/daily-departmental/approval/view/' . encryptId($row->inspection_id)) . '" class="me-1" title="Action"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }

                            $btn .= '<a href="' . admin_url('ohc/first-aid-box/daily-departmental/generalpdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';
                            $btn .= '<a href="' . admin_url('ohc/first-aid-box/daily-departmental/generalExcel/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="Excel">
                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                     </a>';
                            return   $btn;
                        })
                        ->rawColumns(['action', 'issue_date', 'created_by', 'approve_status', 'issue_date'])
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
        $unit = $this->unit->getunit();
        $shift = $this->shift->getShiftname();
        $data = array(
            'unit' => $unit,
            'shift' => $shift,



        );
        return view('inspection.inspection_ohc.daily_department_first_aid_box.list', $data);
    }



    public function Add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $shift = $this->shift->getShiftname();
            $medicine = $this->inventory->getstockdata();
            $signature_upload = $this->user->getSignature();
            $First_aid = $this->certified_First_aid->getFirsaid();
            $document_no = $this->document_reference->selectUsingName('DailyDepartmentalFirstAidBox');
            $location = $this->location->getLocationname();
            $medicines = $this->medicine->getFirstAidData();
            $data = array(
                'unit' => $unit,
                'shift' => $shift,
                'medicine' => $medicine,
                'document_no' => $document_no,
                'signature_upload' => $signature_upload,
                'First_aid' => $First_aid,
                'medicines' => $medicines,


            );
            return view('inspection.inspection_ohc.daily_department_first_aid_box.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('common.message_error'));
            return redirect(admin_url('ohc/first-aid-box/daily-departmental/list'));
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'unit_id' => 'required',
                'department_id' => 'required',
                'review_date' => 'required',

            ];
            $messages = [
                'department_id.required' => 'Please select a Deparment.',
                'unit_id.required' => 'Please select a unit.',
                'review_date.required' => 'Please select the expiry date.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                // Store user medicine requisition
                $daily_department_first_aid_box_details = $this->daily_department_first_aid_box_details->store();


                $data = [
                    'type' => OHC_TYPE_DAILY_DEPARTMENT_FIRST_AID_BOX,
                    'from_status' => OHC_CREATION,
                    'to_status' => MEDICAL_ASSISTANT_APPROVAL_PENDING,
                    'reference_id' => $daily_department_first_aid_box_details->id,
                    'remarks' => "",
                    'approved_by' => null,
                    'created_by' => Auth::id(),

                ];
                $id = $daily_department_first_aid_box_details->id;
                $this->inspection_ohc_status_log->store($data);

                // Safety Officer

                $getfloormanager = getfloormanager();
                $getfloormanagers = $getfloormanager->pluck('id')->toArray();
                $getfloormanagerEmail = $getfloormanager->pluck('email')->toArray();

                // medical officer

                $getmedicalassistant = getMedicalAssistant();
                $getmedicalassistantEmail = $getmedicalassistant->pluck('email')->toArray();
                $getmedicalassistants = $getmedicalassistant->pluck('id')->toArray();
                // Select One
                $daily_department_first_aid_box_details = $this->daily_department_first_aid_box_details->Selectone($id);
                $daily_department_first_aid_box = $this->daily_department_first_aid_box->Selectone($id);
                $document_no = $this->document_reference->Selectone($daily_department_first_aid_box_details->document_reference_id);
                // notification and email
                if (!empty($getmedicalassistant) || !empty($getfloormanager)) {
                    $title = "Daily Department First Aid Box";
                    $mailsubject = "Daily Department First Aid Box";
                    $details = array(
                        'ohc_type' => 'Daily Department First Aid Box',
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'data' => $daily_department_first_aid_box_details,
                        'document_no' => $document_no,
                        'checklist' =>   $daily_department_first_aid_box
                    );

                    $recipients = array_merge($getfloormanagerEmail, $getmedicalassistantEmail);
                    if (!empty($recipients)) {
                        Mail::to($recipients)->queue(new DailyDepartmentFirstAidboxEmail($details));
                    }

                    $notificationData = array(
                        'notification_type' => OHC_INSPECTION,
                        'module_type' => 1,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode(array(
                            'title' => $mailsubject,
                            'message' => "Requestor Created the Daily Departmental First Aid box",
                            'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $id,
                            'module' => 1,
                        )),
                        'web_link' => admin_url('ohc/first-aid-box/daily-departmental/approval/view/' . encryptId($id)), // Fixed concatenation
                        'assigned_user' => array_to_string(array_merge($getmedicalassistants,   $getfloormanagers)), // Fixed missing parenthesis
                        'created_by' => Auth::id(),
                    );

                    notificationSave($notificationData);
                }


                Session::flash('success', __('common.created_msg'));
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', __('common.message_error'));
            }

            return redirect(admin_url('ohc/first-aid-box/daily-departmental/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', __('common.message_error'));
            return redirect(admin_url('ohc/first-aid-box/daily-departmental/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $medicinerequisition = $this->daily_department_first_aid_box_details->Selectone($id);
                $daily_department_first_aid_box = $this->daily_department_first_aid_box->Selectone($id);

                // status log
                $inspection_data = json_decode($medicinerequisition->checklist, true);

                $type = OHC_TYPE_DAILY_DEPARTMENT_FIRST_AID_BOX;
                $statuslog = $this->inspection_ohc_status_log->getStatuslog($id, $type);

                $floortype = MEDICAL_ASSISTANT_APPROVAL_PENDING;
                $floormanger = $this->inspection_ohc_status_log->floormanger($id, $floortype, $type);
                // FOOR MANGER SIGNATURE
                $floormanagersignature = null;
                if (!empty($floormanger) && !empty($floormanger->approved_by)) {
                    $floormanagersignature = $this->signature->floormanagersignature($id, $floormanger, $type);
                }
                // REQUESTOR SIGNATURE
                $requestorsignature =   $medicinerequisition->created_by;
                $requestor_signature = $this->signature->requestorSignature($id, $requestorsignature, $type);
                $signatureview = $this->user->where('id', $requestorsignature)->first();
                // APPROVAL SIGNATURE
                $floorapproversignatureview = null; // Initialize the variable

                if (!empty($floormanger) && !empty($floormanger->approved_by)) {
                    $floorapproversignatureview = $this->user->where('id', $floormanger->approved_by)->first();
                }

                $document_no = $this->document_reference->selectUsingName('DailyDepartmentalFirstAidBox');
                $data = array(
                    'medicinerequisition' => $medicinerequisition,
                    'daily_department_first_aid_box' => $daily_department_first_aid_box,
                    'statuslog' => $statuslog,
                    'floormanger' => $floormanger,
                    'floorapproversignatureview' => $floorapproversignatureview,
                    'inspection_data' => $inspection_data,
                    'signatureview' => $signatureview,
                    'floormanagersignature' => $floormanagersignature,
                    'document_no' => $document_no,
                );
            }
            return view('inspection.inspection_ohc.daily_department_first_aid_box.view', $data);
        } catch (Exception $ex) {
            report($ex);

            Session::flash('error', __('common.message_error'));
            return redirect(admin_url('ohc/first-aid-box/daily-departmental/list'));
        }
    }

    public function approval(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $medicinerequisition = $this->daily_department_first_aid_box_details->Selectone($id);
                $daily_department_first_aid_box = $this->daily_department_first_aid_box->Selectone($id);

                // status log
                $inspection_data = json_decode($medicinerequisition->checklist, true);
                $type = OHC_TYPE_DAILY_DEPARTMENT_FIRST_AID_BOX;
                $statuslog = $this->inspection_ohc_status_log->getStatuslog($id, $type);

                $floortype = MEDICAL_ASSISTANT_APPROVAL_PENDING;
                $floormanger = $this->inspection_ohc_status_log->floormanger($id, $floortype, $type);
                // FOOR MANGER SIGNATURE
                $floormanagersignature = null;
                if (!empty($floormanger) && !empty($floormanger->approved_by)) {
                    $floormanagersignature = $this->signature->floormanagersignature($id, $floormanger, $type);
                }
                // REQUESTOR SIGNATURE
                $requestorsignature =   $medicinerequisition->created_by;
                $requestor_signature = $this->signature->requestorSignature($id, $requestorsignature, $type);
                $signatureview = $this->user->where('id', $requestorsignature)->first();
                // APPROVAL SIGNATURE
                $floorapproversignatureview = null; // Initialize the variable

                if (!empty($floormanger) && !empty($floormanger->approved_by)) {
                    $floorapproversignatureview = $this->user->where('id', $floormanger->approved_by)->first();
                }
                $document_no = $this->document_reference->selectUsingName('DailyDepartmentalFirstAidBox');
                $data = array(
                    'medicinerequisition' => $medicinerequisition,
                    'daily_department_first_aid_box' => $daily_department_first_aid_box,
                    'statuslog' => $statuslog,
                    'floormanger' => $floormanger,
                    'floorapproversignatureview' => $floorapproversignatureview,
                    'signatureview' => $signatureview,
                    'floormanagersignature' => $floormanagersignature,
                    'inspection_data' => $inspection_data,
                    'document_no' => $document_no,
                );
            }
            return view('inspection.inspection_ohc.daily_department_first_aid_box.approval', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('common.message_error'));
            return redirect(admin_url('ohc/first-aid-box/daily-departmental/list'));
        }
    }

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $medicinerequisition = $this->daily_department_first_aid_box_details->Selectone($id);
                $daily_department_first_aid_box = $this->daily_department_first_aid_box->Selectone($id);

                // status log

                $type = OHC_TYPE_DAILY_DEPARTMENT_FIRST_AID_BOX;
                $statuslog = $this->inspection_ohc_status_log->getStatuslog($id, $type);

                $floortype = MEDICAL_ASSISTANT_APPROVAL_PENDING;
                $floormanger = $this->inspection_ohc_status_log->floormanger($id, $floortype, $type);
                // FOOR MANGER SIGNATURE
                $floormanagersignature = null;
                if (!empty($floormanger) && !empty($floormanger->approved_by)) {
                    $floormanagersignature = $this->signature->floormanagersignature($id, $floormanger, $type);
                }
                // REQUESTOR SIGNATURE
                $requestorsignature =   $medicinerequisition->created_by;
                $requestor_signature = $this->signature->requestorSignature($id, $requestorsignature, $type);
                $signatureview = $this->user->where('id', $requestorsignature)->first();
                // APPROVAL SIGNATURE
                $floorapproversignatureview = null; // Initialize the variable

                if (!empty($floormanger) && !empty($floormanger->approved_by)) {
                    $floorapproversignatureview = $this->user->where('id', $floormanger->approved_by)->first();
                }
                $document_no = $this->document_reference->selectUsingName('DailyDepartmentalFirstAidBox');
            }
            $data = [
                'medicinerequisition' => $medicinerequisition,
                'daily_department_first_aid_box' => $daily_department_first_aid_box,
                'statuslog' => $statuslog,
                'floormanger' => $floormanger,
                'floorapproversignatureview' => $floorapproversignatureview,
                'signatureview' => $signatureview,
                'floormanagersignature' => $floormanagersignature,
                'document_no' => $document_no,
                'pagetitle' => "Daily Department First Aid Box",
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

            $html = view('inspection.inspection_ohc.daily_department_first_aid_box.viewpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "Daily Department First Aid Box.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('common.message_error'));
            return redirect(admin_url('ohc/first-aid-box/daily-departmental/list'));
        }
    }

    public function floormanagerapproval(Request $request)
    {
        try {
            $id = decryptId($request->id);


            try {

                if ($request->action === "approve") {
                    $approveStatus = MEDICAL_ASSISTANT_APPROVED;
                    $nextStatus = MEDICAL_ASSISTANT_APPROVED;
                } elseif ($request->action == "reject") {
                    $approveStatus = MEDICAL_ASSISTANT_REJECTED;
                    $nextStatus = MEDICAL_ASSISTANT_REJECTED;
                }
                $details = $this->daily_department_first_aid_box_details->Selectone($id);
                $document_no = $this->document_reference->selectOne( $details->document_reference_id);
                $data = [
                    'type' => OHC_TYPE_DAILY_DEPARTMENT_FIRST_AID_BOX,
                    'from_status' =>  MEDICAL_ASSISTANT_APPROVAL_PENDING,
                    'to_status' => $approveStatus,
                    'reference_id' => $id,
                    'remarks' => $request->floor_remarks,
                    'created_by' =>  $details->created_by,
                    'approved_by' => Auth::id(),
                ];

                // $signature_update = $this->signature->signatureUpload(OHC_TYPE_DAILY_DEPARTMENT_FIRST_AID_BOX);
                $this->inspection_ohc_status_log->store($data);

                $this->daily_department_first_aid_box_details->floormanagerapprovalupdate($id, $nextStatus);
                if ($request->action == "approve") {
                    $userIds = [
                        'users' => $details->created_by,
                    ];
                    $mailsubject = 'Daily Department First Aid Box Approved';
                    $notificationData = array(
                        'notification_type' => OHC_INSPECTION,
                        'module_type' => 1,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode(array(
                            'title' => $mailsubject,
                            'message' => "Floor Manager /Medical Assistant Approved the Daily Departmental First Aid Box",
                            'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $id,
                            'module' => 1,
                        )),
                        'web_link' =>  admin_url('ohc/first-aid-box/daily-departmental/list'),
                        'assigned_user' => array_to_string($userIds),
                        'created_by' => Auth::id(),
                    );
                    notificationSave($notificationData);
                    $title = "Daily Departmental First Aid Box";
                    $user = $details->created_by;
                    $email_id = getUseremail($user);

                    $details = array(
                        'ohc_type' => 'Daily Departmental First Aid Box was Floor Manager/ Medical assistant Approved',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'document_no' => $document_no,
                        'data' => $details
                    );
                    Mail::to($email_id)->queue(new DailyDepartmentFirstAidboxEmail($details));
                } elseif ($request->action == "reject") {
                    $userIds = [
                        'users' => $details->created_by,
                    ];
                    $mailsubject = 'Daily Departmental First Aid Box';
                    $notificationData = array(
                        'notification_type' => OHC_INSPECTION,
                        'module_type' => 1,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode(array(
                            'title' => $mailsubject,
                            'message' => "Floor Manager / Medical assistant rejected the Daily Departmental First Aid Box",
                            'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $id,
                            'module' => 1,
                        )),
                        'web_link' =>  admin_url('ohc/first-aid-box/daily-departmental/list'),
                        'assigned_user' => array_to_string($userIds),
                        'created_by' => Auth::id(),
                    );
                    notificationSave($notificationData);
                    $title = "Daily Departmental First Aid Box";
                    $user = $details->created_by;
                    $email_id = getUseremail($user);

                    $details = array(
                        'ohc_type' => 'Daily Departmental First Aid Box was Floor Manager/ Medical assistant Rejected',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'document_no' => $document_no,
                        'data' => $details
                    );
                    Mail::to($email_id)->queue(new DailyDepartmentFirstAidboxEmail($details));
                }



                Session::flash('success', 'Your data has been Responded successfully!');
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', __('common.message_error'));
            }

            return redirect(admin_url('ohc/first-aid-box/daily-departmental/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', __('common.message_error'));
            return redirect(admin_url('ohc/first-aid-box/daily-departmental/list'));
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->daily_department_first_aid_box_details->exportdata();
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
                $document_no = $this->document_reference->selectOne($details->document_reference_id);

                $medicinerequisition = $this->daily_department_first_aid_box_details->Selectone($details->id);
                $daily_department_first_aid_box = $this->daily_department_first_aid_box->Selectone($details->id);
                $CreatorSignature = GetOHCSignature($medicinerequisition->created_by, $details->id, OHC_TYPE_DAILY_DEPARTMENT_FIRST_AID_BOX);

                $floorManagerSignature = GetOHCSignature($medicinerequisition->verified_by, $details->id, OHC_TYPE_DAILY_DEPARTMENT_FIRST_AID_BOX);

                $inspection_data = json_decode($details->checklist, true);


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
                $sheet->setCellValue("G{$currentRow}", "DAILY DEPARTMENTAL FIRST-AID BOX INSPECTION CHECKLIST");
                $sheet->getStyle("G{$currentRow}:M{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);


                // document number


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
                $richText1->createText(Displaydateformat($medicinerequisition->date));
                $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

                $sheet->mergeCells("H" . ($currentRow + 3) . ":N" . ($currentRow + 3));
                $richText2 = new RichText();
                $richText2->createTextRun('FIRST AID BOX NO :-  ')->getFont()->setBold(true);
                $richText2->createText(($medicinerequisition->first_aid_box_no));
                $sheet->getCell("H" . ($currentRow + 3))->setValue($richText2);

                $sheet->mergeCells("O" . ($currentRow + 3) . ":S" . ($currentRow + 3));
                $richText2 = new RichText();
                $richText2->createTextRun('SHIFT :-  ')->getFont()->setBold(true);
                $richText2->createText(getShift($medicinerequisition->shift));
                $sheet->getCell("O" . ($currentRow + 3))->setValue($richText2);

                $sheet->getStyle("A" . ($currentRow + 3) . ":S" . ($currentRow + 3))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->mergeCells("A" . ($currentRow + 4) . ":G" . ($currentRow + 4));
                $richText1 = new RichText();
                $richText1->createTextRun(' DATE OF INSPECTION:- ')->getFont()->setBold(true);
                $richText1->createText(getDepartment($medicinerequisition->department));
                $sheet->getCell("A" . ($currentRow + 4))->setValue($richText1);

                $sheet->mergeCells("H" . ($currentRow + 4) . ":N" . ($currentRow + 4));
                $richText2 = new RichText();
                $richText2->createTextRun('UNIT :-  ')->getFont()->setBold(true);
                $richText2->createText(getUnitname($medicinerequisition->unit));
                $sheet->getCell("H" . ($currentRow + 4))->setValue($richText2);

                $sheet->mergeCells("O" . ($currentRow + 4) . ":S" . ($currentRow + 4));
                $richText2 = new RichText();
                $richText2->createTextRun('NAME OF THE FIRST AIDER:-  ')->getFont()->setBold(true);
                $richText2->createText(getFirstAider($medicinerequisition->first_aider));
                $sheet->getCell("O" . ($currentRow + 4))->setValue($richText2);

                $sheet->getStyle("A" . ($currentRow + 4) . ":S" . ($currentRow + 4))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $headerRow = $currentRow + 5;

                $sheet->mergeCells("A$headerRow:C$headerRow")->setCellValue("A$headerRow", "SERIAL NO");
                $sheet->mergeCells("D$headerRow:G$headerRow")->setCellValue("D$headerRow", "NAME OF THE MEDICINE");
                $sheet->mergeCells("H$headerRow:J$headerRow")->setCellValue("H$headerRow", "FREEZE QUANTITY");
                $sheet->mergeCells("K$headerRow:M$headerRow")->setCellValue("K$headerRow", "AVAILABLE QUANTITY");
                $sheet->mergeCells("N$headerRow:P$headerRow")->setCellValue("N$headerRow", "MATERIAL EXPIRY");
                $sheet->mergeCells("Q$headerRow:S$headerRow")->setCellValue("Q$headerRow", "REMARKS");

                $sheet->getStyle("A$headerRow:S$headerRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'font' => ['bold' => true],
                ]);

                $inspectionRow = $headerRow + 1;



                foreach ($inspection_data as $index => $detail) {
                    $sheet->mergeCells("A$inspectionRow:C$inspectionRow")->setCellValue("A$inspectionRow", $index + 1);

                    $medicineName = getMedicinename($detail['medicine_id'] ?? '') ?? '';
                    $material_expiry = Displaydateformat($detail['material_expiry'] ?? '') ?? '';

                    $sheet->mergeCells("D$inspectionRow:G$inspectionRow")->setCellValue("D$inspectionRow", $medicineName);
                    $sheet->mergeCells("H$inspectionRow:J$inspectionRow")->setCellValue("H$inspectionRow", $detail['freeze_quantity'] ?? '');
                    $sheet->mergeCells("K$inspectionRow:M$inspectionRow")->setCellValue("K$inspectionRow", $detail['available_quantity'] ?? '');
                    $sheet->mergeCells("N$inspectionRow:P$inspectionRow")->setCellValue("N$inspectionRow", Displaydateformat($detail['expired_date'] ?? ''));
                    $sheet->mergeCells("Q$inspectionRow:S$inspectionRow")->setCellValue("Q$inspectionRow", $detail['remarks'] ?? '');

                    $sheet->getStyle("A$inspectionRow:S$inspectionRow")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $inspectionRow++;
                }

                $signatureStartRow = $inspectionRow;
                $signatureRow = $signatureStartRow;

                $sheet->getRowDimension($signatureRow)->setRowHeight(30);

                $sheet->mergeCells("A{$signatureRow}:I{$signatureRow}");
                $sheet->mergeCells("J{$signatureRow}:S{$signatureRow}");

                $sheet->getStyle("A{$signatureRow}:S{$signatureRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);
                // Prepared
                $richText = new RichText();
                $name = getUsername($details->created_by);

                if (!empty($name)) {
                    $richText->createTextRun("FIRST AIDER NAME: " . $name)->getFont()->setBold(true);
                } else {
                    $richText->createTextRun("Inspection has not been Prepared Yet")->getFont()->setBold(true);
                }

                $sheet->getCell("A{$signatureRow}")->setValue($richText);

                // Verified
                $richText = new RichText();
                $name = getUsername($details->verified_by);

                if (!empty($name)) {
                    $richText->createTextRun("FLOOR MANAGER NAME : " . $name)->getFont()->setBold(true);
                } else {
                    $richText->createTextRun("Inspection has not been Verified Yet")->getFont()->setBold(true);
                }

                $sheet->getCell("J{$signatureRow}")->setValue($richText);



                // if (file_exists($CreatorSignature)) {
                //     $sheet->mergeCells("A$signatureStartRow:I" . ($signatureStartRow + 2));

                //     $drawing = new Drawing();
                //     $drawing->setName('Creator Signature');
                //     $drawing->setPath($CreatorSignature);
                //     $drawing->setCoordinates("A$signatureStartRow");
                //     $drawing->setOffsetX(100);
                //     $drawing->setOffsetY(5);
                //     $drawing->setWidth(70);
                //     $drawing->setHeight(70);
                //     $drawing->setWorksheet($sheet);
                //     $sheet->getRowDimension($signatureStartRow + 2)->setRowHeight(40);

                //     // Label + Name
                //     $sheet->setCellValue("A" . ($signatureStartRow + 3), "First Aider Signature: " . getUserName($medicinerequisition->created_by));
                //     $sheet->mergeCells("A" . ($signatureStartRow + 3) . ":I" . ($signatureStartRow + 3));

                //     $sheet->getStyle("A$signatureStartRow:I" . ($signatureStartRow + 3))->applyFromArray([
                //         'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                //         'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                //     ]);
                // }

                // if (file_exists($floorManagerSignature)) {
                //     $sheet->mergeCells("J$signatureStartRow:S" . ($signatureStartRow + 2));

                //     $drawing = new Drawing();
                //     $drawing->setName('Floor Manager Signature');
                //     $drawing->setPath($floorManagerSignature);
                //     $drawing->setCoordinates("J$signatureStartRow");
                //     $drawing->setOffsetX(100);
                //     $drawing->setOffsetY(5);
                //     $drawing->setWidth(70);
                //     $drawing->setHeight(70);
                //     $drawing->setWorksheet($sheet);
                //     $sheet->getRowDimension($signatureStartRow + 2)->setRowHeight(40);

                //     // Label + Name
                //     $sheet->setCellValue("J" . ($signatureStartRow + 3), "Floor Manager/Medical Assistant Signature: " . getUserName($medicinerequisition->created_by));
                //     $sheet->mergeCells("J" . ($signatureStartRow + 3) . ":S" . ($signatureStartRow + 3));

                //     $sheet->getStyle("J$signatureStartRow:S" . ($signatureStartRow + 3))->applyFromArray([
                //         'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                //         'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                //     ]);
                // }


                $row =   $signatureRow + 4;
            }


            // Requestor Signature


            $fileName = 'daily department first aid box.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', __('common.message_error'));
            return redirect(admin_url('ohc/first-aid-box/daily-departmental/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->daily_department_first_aid_box_details->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }
            foreach ($allData as $details) {
                $document_no = $this->document_reference->SelectOne($details->document_reference_id);
            }


            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }



            $data = array(

                'content' => $allData,
                'document_no' => $document_no,
                'pagetitle' => "Daily Department First Aid Box",
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

            $view = view('inspection.inspection_ohc.daily_department_first_aid_box.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Daily Department First Aid Box.pdf";
            $mpdf->Output($filename, 'I');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', __('common.message_error'));
            return redirect(admin_url('ohc/first-aid-box/daily-departmental/list'));
        }
    }

    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $document_no = $this->document_reference->selectUsingName('DailyDepartmentalFirstAidBox');

            $medicinerequisition = $this->daily_department_first_aid_box_details->Selectone($id);
            $daily_department_first_aid_box = $this->daily_department_first_aid_box->Selectone($id);
            $CreatorSignature = GetOHCSignature($medicinerequisition->created_by, $id, OHC_TYPE_DAILY_DEPARTMENT_FIRST_AID_BOX);
            $inspection_data = json_decode($medicinerequisition->checklist, true);
            $floorManagerSignature = GetOHCSignature($medicinerequisition->verified_by, $id, OHC_TYPE_DAILY_DEPARTMENT_FIRST_AID_BOX);

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

            // Title Section
            $sheet->mergeCells("G{$currentRow}:M" . ($currentRow + 2));
            $sheet->setCellValue("G{$currentRow}", "DAILY DEPARTMENTAL FIRST-AID BOX INSPECTION CHECKLIST");
            $sheet->getStyle("G{$currentRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);


            // document number


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
            $richText1->createText(Displaydateformat($medicinerequisition->date));
            $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

            $sheet->mergeCells("H" . ($currentRow + 3) . ":N" . ($currentRow + 3));
            $richText2 = new RichText();
            $richText2->createTextRun('FIRST AID BOX NO :-  ')->getFont()->setBold(true);
            $richText2->createText(($medicinerequisition->first_aid_box_no));
            $sheet->getCell("H" . ($currentRow + 3))->setValue($richText2);

            $sheet->mergeCells("O" . ($currentRow + 3) . ":S" . ($currentRow + 3));
            $richText2 = new RichText();
            $richText2->createTextRun('SHIFT :-  ')->getFont()->setBold(true);
            $richText2->createText(getShift($medicinerequisition->shift));
            $sheet->getCell("O" . ($currentRow + 3))->setValue($richText2);

            $sheet->getStyle("A" . ($currentRow + 3) . ":S" . ($currentRow + 3))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells("A" . ($currentRow + 4) . ":G" . ($currentRow + 4));
            $richText1 = new RichText();
            $richText1->createTextRun(' DEPARTMENT:- ')->getFont()->setBold(true);
            $richText1->createText(getDepartment($medicinerequisition->department));
            $sheet->getCell("A" . ($currentRow + 4))->setValue($richText1);

            $sheet->mergeCells("H" . ($currentRow + 4) . ":N" . ($currentRow + 4));
            $richText2 = new RichText();
            $richText2->createTextRun('UNIT :-  ')->getFont()->setBold(true);
            $richText2->createText(getUnitname($medicinerequisition->unit));
            $sheet->getCell("H" . ($currentRow + 4))->setValue($richText2);

            $sheet->mergeCells("O" . ($currentRow + 4) . ":S" . ($currentRow + 4));
            $richText2 = new RichText();
            $richText2->createTextRun('NAME OF THE FIRST AIDER:-  ')->getFont()->setBold(true);
            $richText2->createText(getFirstAider($medicinerequisition->first_aider));
            $sheet->getCell("O" . ($currentRow + 4))->setValue($richText2);

            $sheet->getStyle("A" . ($currentRow + 4) . ":S" . ($currentRow + 4))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $headerRow = $currentRow + 5;

            $sheet->mergeCells("A$headerRow:C$headerRow")->setCellValue("A$headerRow", "SERIAL NO");
            $sheet->mergeCells("D$headerRow:G$headerRow")->setCellValue("D$headerRow", "NAME OF THE MEDICINE");
            $sheet->mergeCells("H$headerRow:J$headerRow")->setCellValue("H$headerRow", "FREEZE QUANTITY");
            $sheet->mergeCells("K$headerRow:M$headerRow")->setCellValue("K$headerRow", "AVAILABLE QUANTITY");
            $sheet->mergeCells("N$headerRow:P$headerRow")->setCellValue("N$headerRow", "MATERIAL EXPIRY");
            $sheet->mergeCells("Q$headerRow:S$headerRow")->setCellValue("Q$headerRow", "REMARKS");

            $sheet->getStyle("A$headerRow:S$headerRow")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font' => ['bold' => true],
            ]);

            $inspectionRow = $headerRow + 1;

            foreach ($inspection_data as $index => $detail) {
                $sheet->mergeCells("A$inspectionRow:C$inspectionRow")->setCellValue("A$inspectionRow", $index + 1);

                $medicineName = getMedicinename($detail['medicine_id'] ?? '') ?? '';
                $material_expiry = Displaydateformat($detail['material_expiry'] ?? '') ?? '';

                $sheet->mergeCells("D$inspectionRow:G$inspectionRow")->setCellValue("D$inspectionRow", $medicineName);
                $sheet->mergeCells("H$inspectionRow:J$inspectionRow")->setCellValue("H$inspectionRow", $detail['freeze_quantity'] ?? '');
                $sheet->mergeCells("K$inspectionRow:M$inspectionRow")->setCellValue("K$inspectionRow", $detail['available_quantity'] ?? '');
                $sheet->mergeCells("N$inspectionRow:P$inspectionRow")->setCellValue("N$inspectionRow", Displaydateformat($detail['expired_date'] ?? ''));
                $sheet->mergeCells("Q$inspectionRow:S$inspectionRow")->setCellValue("Q$inspectionRow", $detail['remarks'] ?? '');

                $sheet->getStyle("A$inspectionRow:S$inspectionRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $inspectionRow++;
            }



            $signatureStartRow = $inspectionRow;
            $signatureRow = $signatureStartRow;


            $sheet->getRowDimension($signatureRow)->setRowHeight(30);

            $sheet->mergeCells("A{$signatureRow}:I{$signatureRow}");
            $sheet->mergeCells("J{$signatureRow}:S{$signatureRow}");

            $sheet->getStyle("A{$signatureRow}:S{$signatureRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);
            // Prepared
            $richText = new RichText();
            $name = getUsername($medicinerequisition->created_by);

            if (!empty($name)) {
                $richText->createTextRun("FIRST AIDER NAME: " . $name)->getFont()->setBold(true);
            } else {
                $richText->createTextRun("Inspection has not been Prepared Yet")->getFont()->setBold(true);
            }

            $sheet->getCell("A{$signatureRow}")->setValue($richText);

            // Verified
            $richText = new RichText();
            $name = getUsername($medicinerequisition->verified_by);

            if (!empty($name)) {
                $richText->createTextRun("FLOOR MANAGER NAME : " . $name)->getFont()->setBold(true);
            } else {
                $richText->createTextRun("Inspection has not been Verified Yet")->getFont()->setBold(true);
            }

            $sheet->getCell("J{$signatureRow}")->setValue($richText);



            // if (file_exists($CreatorSignature)) {
            //     $sheet->mergeCells("A$signatureStartRow:I" . ($signatureStartRow + 2));

            //     $drawing = new Drawing();
            //     $drawing->setName('Creator Signature');
            //     $drawing->setPath($CreatorSignature);
            //     $drawing->setCoordinates("A$signatureStartRow");
            //     $drawing->setOffsetX(100);
            //     $drawing->setOffsetY(5);
            //     $drawing->setWidth(70);
            //     $drawing->setHeight(70);
            //     $drawing->setWorksheet($sheet);
            //     $sheet->getRowDimension($signatureStartRow + 2)->setRowHeight(40);

            //     // Label + Name
            //     $sheet->setCellValue("A" . ($signatureStartRow + 3), "First Aider Signature: " . getUserName($medicinerequisition->created_by));
            //     $sheet->mergeCells("A" . ($signatureStartRow + 3) . ":I" . ($signatureStartRow + 3));

            //     $sheet->getStyle("A$signatureStartRow:I" . ($signatureStartRow + 3))->applyFromArray([
            //         'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            //         'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            //     ]);
            // }

            // if (file_exists($floorManagerSignature)) {
            //     $sheet->mergeCells("J$signatureStartRow:S" . ($signatureStartRow + 2));

            //     $drawing = new Drawing();
            //     $drawing->setName('Floor Manager Signature');
            //     $drawing->setPath($floorManagerSignature);
            //     $drawing->setCoordinates("J$signatureStartRow");
            //     $drawing->setOffsetX(100);
            //     $drawing->setOffsetY(5);
            //     $drawing->setWidth(70);
            //     $drawing->setHeight(70);
            //     $drawing->setWorksheet($sheet);
            //     $sheet->getRowDimension($signatureStartRow + 2)->setRowHeight(40);

            //     // Label + Name
            //     $sheet->setCellValue("J" . ($signatureStartRow + 3), "Floor Manager/Medical Assistant Signature: " . getUserName($medicinerequisition->created_by));
            //     $sheet->mergeCells("J" . ($signatureStartRow + 3) . ":S" . ($signatureStartRow + 3));

            //     $sheet->getStyle("J$signatureStartRow:S" . ($signatureStartRow + 3))->applyFromArray([
            //         'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            //         'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            //     ]);
            // }







            $fileName = 'daily department first aid box.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('common.message_error'));
            return redirect(admin_url('ohc/first-aid-box/daily-departmental/list'));
        }
    }
}
