<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use App\Mail\Inspection\Ohc\OccupationalHealthEmail;
use Illuminate\Http\Request;
use App\Models\Master\Department;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\Ohc\InspectionOhcStatuslog;
use App\Models\Inspection\Ohc\MedicineRequisitionSlipFloor;
use App\Models\Inspection\Ohc\MedicineRequistionSlipfloordetails;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Models\OhcManagement\Report\Inventory;
use App\Models\UploadLog;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inspection\Safety\SignatureUpload;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Ohc\Master\FirstAidEquipment;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class MedicalRequisitionSlipController extends Controller
{

    private $upload_log;
    private $unit;
    private $shift;
    private $department;
    private $user;
    private $medicine_requisition_floor_checklist;
    private $medicine_requisition_floor_details;
    private $inspection_ohc_status_log;
    private $signature;
    private $inventory;
    private $document_reference;
    private $freeze_medicine;

    private $location;
    public function __construct()
    {

        $this->upload_log = new UploadLog();
        $this->freeze_medicine = new FirstAidEquipment();
        $this->unit = new Unit();
        $this->department = new Department();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->medicine_requisition_floor_checklist = new MedicineRequisitionSlipFloor();
        $this->inventory = new Inventory();
        $this->user = new User();
        $this->inspection_ohc_status_log = new InspectionOhcStatuslog();
        $this->signature = new OhcSignature();
        $this->document_reference = new InspectionStaticDocno();
        $this->medicine_requisition_floor_details = new MedicineRequistionSlipfloordetails();
    }
    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->medicine_requisition_floor_details->list();

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
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('unit', function ($row) {
                            return getUnitname($row->unit);
                        })
                        ->addColumn('department', function ($row) {
                            return getDepartment($row->department);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('approve_status', function ($row) {
                            $text = '';
                            switch ($row->approve_status) {
                                case FLOOR_MANAGER_APPROVAL_PENDING:
                                    $text = "<span class='badge bg-info rounded' style='font-size: 1.0em;'>Floor Manager Approval Pending</span>";
                                    break;
                                case FLOOR_MANAGER_APPROVED:
                                    $text = "<span class='badge bg-success rounded' style='font-size: 1.0em;'>Floor Manager Approved</span>";
                                    break;
                                case FLOOR_MANAGER_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>Floor Manager Rejected</span>";
                                    break;
                                case SAFETY_OFFICER_APPROVAL_PENDING:
                                    $text = "<span class='badge bg-info rounded' style='font-size: 1.0em;'>Safety Officer Approval Pending</span>";
                                    break;
                                case SAFETY_OFFICER_APPROVED:
                                    $text = "<span class='badge bg-success rounded' style='font-size: 1.0em;'>Safety Officer Approved</span>";
                                    break;
                                case SAFETY_OFFICER_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>Safety Officer Rejected</span>";
                                    break;
                                default:
                                    $text = "<span class='badge rounded-pill text-bg-warning'>Unknown</span>";
                            }
                            return $text;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn .= '<a href="' . admin_url('ohc/medical-requisition-slip/view/' . encryptId($row->inspection_id)) . '" class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a>';

                            if ((checkUserRole(ROLE_SUPERADMIN) && $row->approve_status == FLOOR_MANAGER_APPROVAL_PENDING) || (checkUserRole(ROLE_FLOOR_MANAGER) && $row->approve_status == FLOOR_MANAGER_APPROVAL_PENDING)  || ((checkUserRole(ROLE_SAFETY_OFFICER) && $row->approve_status == SAFETY_OFFICER_APPROVAL_PENDING) || (checkUserRole(ROLE_SUPERADMIN) && $row->approve_status == SAFETY_OFFICER_APPROVAL_PENDING))) {
                                $btn .= '<a href="' . admin_url('ohc/medical-requisition-slip/approval/view/' . encryptId($row->inspection_id)) . '" class="me-1" title="Action"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }

                            $btn .= '<a href="' . admin_url('ohc/medical-requisition-slip/generalpdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';
                            $btn .= '<a href="' . admin_url('ohc/medical-requisition-slip/generalExcel/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="Excel">
                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                     </a>';
                            return   $btn;
                        })
                        ->rawColumns(['action', 'issue_date', 'created_by', 'approve_status', 'issue_date', 'unit', 'department'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);

                    return $datatables;
                } catch (Exception $ex) {
                     dd($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
        $unit = $this->unit->getunit();
        $data = array(
            'unit' => $unit,


        );
        return view('inspection.inspection_ohc.medical_requisition_slip.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $shift = $this->shift->getShiftname();
            $medicine = $this->freeze_medicine->getFirstAidData();
            $signature_upload = $this->user->getSignature();
            $location = $this->location->getLocationname();
            $document_no = $this->document_reference->selectUsingName('MedicalRequisitionSlipFloor');
            $data = array(
                'unit' => $unit,
                'shift' => $shift,
                'medicine' => $medicine,
                'document_no' => $document_no,
                'signature_upload' => $signature_upload,

            );
            return view('inspection.inspection_ohc.medical_requisition_slip.add', $data);
        } catch (Exception $ex) {
             report($ex);
        }
    }

    public function freezeQuantity(Request $request)
    {
        $medicineId = decryptId($request->medicineId);

        $response = $this->freeze_medicine->where('medicine_id', $medicineId)->where('status', 1)->first();
        return response()->json($response);
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
                $medicine_requisition_floor_details = $this->medicine_requisition_floor_details->store();
                $medicine_requisition_floor_checklist = $this->medicine_requisition_floor_checklist->store($medicine_requisition_floor_details);
                // signature
                $id = ($medicine_requisition_floor_details->id);
                $signature = $this->signature->requestorsignatureUpload(OHC_TYPE_MEDICINE_REQUISTION_FLOOR, $id);

                $data = [
                    'type' => OHC_TYPE_MEDICINE_REQUISTION_FLOOR,
                    'from_status' => OHC_CREATION,
                    'to_status' => FLOOR_MANAGER_APPROVAL_PENDING,
                    'reference_id' => $medicine_requisition_floor_details->id,
                    'remarks' => "",
                    'approved_by' => null,
                    'created_by' => Auth::id(),

                ];
                $id = $medicine_requisition_floor_details->id;
                $this->inspection_ohc_status_log->store($data);

                $getfloormanager = getFloormanager();
                $getfloormanagers = $getfloormanager->pluck('id')->toArray();
                $details = $this->medicine_requisition_floor_details->Selectone($id);
                $mailsubject = 'Medicine Requistion Slip Floor';
                $notificationData = array(
                    'notification_type' => OHC_INSPECTION,
                    'module_type' => 2,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => "Medicine Requistion Slip floor ",
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('ohc/medical-requisition-slip/approval/view/' . encryptId($id)),
                    'assigned_user' => array_to_string($getfloormanagers),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
                foreach ($getfloormanagers as $user) {
                    $email_id = getUseremail($user);
                    $title = "Medicine Requistion Slip Floor";
                    $details = array(
                        'ohc_type' => 'medicine requisition slip floor',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'data' => $details,

                    );
                    Mail::to($email_id)->queue(new OccupationalHealthEmail($details));
                }

                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                 report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medical-requisition-slip/list'));
        } catch (Exception $ex) {

             report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-requisition-slip/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {

                $medicinerequisition = $this->medicine_requisition_floor_details->Selectone($id);
                $medicine_requisition_floor_checklist = $this->medicine_requisition_floor_checklist->Selectone($id);
                $type = OHC_TYPE_MEDICINE_REQUISTION_FLOOR;
                $statuslog = $this->inspection_ohc_status_log->getStatuslog($id, $type);

                $floortype = FLOOR_MANAGER_APPROVAL_PENDING;
                $safetytype = SAFETY_OFFICER_APPROVAL_PENDING;

                $safetyofficer = $this->inspection_ohc_status_log->safetyofficer($id, $safetytype, $type);
                $floormanger = $this->inspection_ohc_status_log->floormanger($id, $floortype, $type);

                $safetyofficersignature = null;
                $floormanagersignature = null;
                if (!empty($safetyofficer) && !empty($safetyofficer->approved_by)) {
                    $safetyofficersignature = $this->signature->safetyofficersignature($id, $safetyofficer, $type);
                }
                if (!empty($floormanger) && !empty($floormanger->approved_by)) {
                    $floormanagersignature = $this->signature->floormanagersignature($id, $floormanger, $type);
                }

                $requestorsignature =   $medicinerequisition->created_by;
                $requestor_signature = $this->signature->requestorSignature($id, $requestorsignature, $type);
                $signatureview = $this->user->where('id', $requestorsignature)->first();

                $approversignatureview = null; // Initialize the variable

                if (!empty($safetyofficer) && !empty($safetyofficer->approved_by)) {
                    $approversignatureview = $this->user->where('id', $safetyofficer->approved_by)->first();
                }

                $floorapproversignatureview = null; // Initialize the variable

                if (!empty($floormanger) && !empty($floormanger->approved_by)) {
                    $floorapproversignatureview = $this->user->where('id', $floormanger->approved_by)->first();
                }
                $document_no = $this->document_reference->selectUsingName('MedicalRequisitionSlipFloor');
                $data = array(
                    'medicinerequisition' => $medicinerequisition,
                    'medicine_requisition_floor_checklist' => $medicine_requisition_floor_checklist,
                    'statuslog' => $statuslog,
                    'safetyofficer' => $safetyofficer,
                    'floormanger' => $floormanger,
                    'floorapproversignatureview' => $floorapproversignatureview,
                    'signatureview' => $signatureview,
                    'approversignatureview' => $approversignatureview,
                    'safetyofficersignature' => $safetyofficersignature,
                    'floormanagersignature' => $floormanagersignature,
                    'document_no' => $document_no,

                );
            }
            return view('inspection.inspection_ohc.medical_requisition_slip.view', $data);
        } catch (Exception $ex) {
             report($ex);
        }
    }

    public function approval(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $medicinerequisition = $this->medicine_requisition_floor_details->Selectone($id);
                $medicine_requisition_floor_checklist = $this->medicine_requisition_floor_checklist->Selectone($id);
                $type = OHC_TYPE_MEDICINE_REQUISTION_FLOOR;
                $statuslog = $this->inspection_ohc_status_log->getStatuslog($id, $type);

                $floortype = FLOOR_MANAGER_APPROVAL_PENDING;
                $safetytype = SAFETY_OFFICER_APPROVAL_PENDING;

                $safetyofficer = $this->inspection_ohc_status_log->safetyofficer($id, $safetytype, $type);
                $floormanger = $this->inspection_ohc_status_log->floormanger($id, $floortype, $type);

                $safetyofficersignature = null;
                $floormanagersignature = null;
                if (!empty($safetyofficer) && !empty($safetyofficer->approved_by)) {
                    $safetyofficersignature = $this->signature->safetyofficersignature($id, $safetyofficer, $type);
                }
                if (!empty($floormanger) && !empty($floormanger->approved_by)) {
                    $floormanagersignature = $this->signature->floormanagersignature($id, $floormanger, $type);
                }

                $requestorsignature =   $medicinerequisition->created_by;
                $requestor_signature = $this->signature->requestorSignature($id, $requestorsignature, $type);
                $signatureview = $this->user->where('id', $requestorsignature)->first();

                $approversignatureview = null; // Initialize the variable

                if (!empty($safetyofficer) && !empty($safetyofficer->approved_by)) {
                    $approversignatureview = $this->user->where('id', $safetyofficer->approved_by)->first();
                }

                $floorapproversignatureview = null; // Initialize the variable

                if (!empty($floormanger) && !empty($floormanger->approved_by)) {
                    $floorapproversignatureview = $this->user->where('id', $floormanger->approved_by)->first();
                }
                $document_no = $this->document_reference->selectUsingName('MedicalRequisitionSlipFloor');
                $data = array(
                    'medicinerequisition' => $medicinerequisition,
                    'medicine_requisition_floor_checklist' => $medicine_requisition_floor_checklist,
                    'statuslog' => $statuslog,
                    'safetyofficer' => $safetyofficer,
                    'floormanger' => $floormanger,
                    'floorapproversignatureview' => $floorapproversignatureview,
                    'signatureview' => $signatureview,
                    'approversignatureview' => $approversignatureview,
                    'safetyofficersignature' => $safetyofficersignature,
                    'floormanagersignature' => $floormanagersignature,
                    'document_no' => $document_no,


                );
            }
            return view('inspection.inspection_ohc.medical_requisition_slip.approval', $data);
        } catch (Exception $ex) {
             report($ex);
        }
    }


    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $medicinerequisition = $this->medicine_requisition_floor_details->Selectone($id);
                $medicine_requisition_floor_checklist = $this->medicine_requisition_floor_checklist->Selectone($id);
                $type = OHC_TYPE_MEDICINE_REQUISTION_FLOOR;
                $statuslog = $this->inspection_ohc_status_log->getStatuslog($id, $type);

                $floortype = FLOOR_MANAGER_APPROVAL_PENDING;
                $safetytype = SAFETY_OFFICER_APPROVAL_PENDING;

                $safetyofficer = $this->inspection_ohc_status_log->safetyofficer($id, $safetytype, $type);
                $floormanger = $this->inspection_ohc_status_log->floormanger($id, $floortype, $type);

                $safetyofficersignature = null;
                $floormanagersignature = null;
                if (!empty($safetyofficer) && !empty($safetyofficer->approved_by)) {
                    $safetyofficersignature = $this->signature->safetyofficersignature($id, $safetyofficer, $type);
                }
                if (!empty($floormanger) && !empty($floormanger->approved_by)) {
                    $floormanagersignature = $this->signature->floormanagersignature($id, $floormanger, $type);
                }

                $requestorsignature =   $medicinerequisition->created_by;
                $requestor_signature = $this->signature->requestorSignature($id, $requestorsignature, $type);
                $signatureview = $this->user->where('id', $requestorsignature)->first();

                $approversignatureview = null;

                if (!empty($safetyofficer) && !empty($safetyofficer->approved_by)) {
                    $approversignatureview = $this->user->where('id', $safetyofficer->approved_by)->first();
                }

                $floorapproversignatureview = null;

                if (!empty($floormanger) && !empty($floormanger->approved_by)) {
                    $floorapproversignatureview = $this->user->where('id', $floormanger->approved_by)->first();
                }

                $document_no = $this->document_reference->selectUsingName('MedicalRequisitionSlipFloor');
            }
            $data = [
                'medicinerequisition' => $medicinerequisition,
                'medicine_requisition_floor_checklist' => $medicine_requisition_floor_checklist,
                'statuslog' => $statuslog,
                'safetyofficer' => $safetyofficer,
                'floormanger' => $floormanger,
                'signatureview' => $signatureview,
                'floorapproversignatureview' => $floorapproversignatureview,
                'approversignatureview' => $approversignatureview,
                'safetyofficersignature' => $safetyofficersignature,
                'floormanagersignature' => $floormanagersignature,
                'document_no' => $document_no,
                'pagetitle' => "Medicine Requisition Slip Floor",
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

            $html = view('inspection.inspection_ohc.medical_requisition_slip.viewpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "Medicine Requisition Slip Floor.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
             report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }

    public function floormanagerapproval(Request $request)
    {
        try {
            $id = decryptId($request->id);


            try {

                if ($request->action === "approve") {
                    $approveStatus = FLOOR_MANAGER_APPROVED;
                    $nextStatus = SAFETY_OFFICER_APPROVAL_PENDING;
                } elseif ($request->action == "reject") {
                    $approveStatus = FLOOR_MANAGER_REJECTED;
                    $nextStatus = FLOOR_MANAGER_REJECTED;
                }
                $details = $this->medicine_requisition_floor_details->Selectone($id);
                $data = [
                    'type' => OHC_TYPE_MEDICINE_REQUISTION_FLOOR,
                    'from_status' => FLOOR_MANAGER_APPROVAL_PENDING,
                    'to_status' => $approveStatus,
                    'reference_id' => $id,
                    'remarks' => $request->floor_remarks,
                    'created_by' =>  $details->created_by,
                    'approved_by' => Auth::id(),
                ];

                $signature_update = $this->signature->signatureUpload(OHC_TYPE_MEDICINE_REQUISTION_FLOOR);
                $this->inspection_ohc_status_log->store($data);

                $this->medicine_requisition_floor_details->floormanagerapprovalupdate($id, $nextStatus);
                if ($request->action == "approve") {
                    $getsafetyofficer = getSafetyOfficer();
                    $getsafetyofficers = $getsafetyofficer->pluck('id')->toArray();
                    $getsafetyofficerEmail = $getsafetyofficer->pluck('email')->toArray();

                    // medical officer

                    $getmedicalassistant = getMedicalAssistant();
                    $getmedicalassistantEmail = $getmedicalassistant->pluck('email')->toArray();
                    $getmedicalassistants = $getmedicalassistant->pluck('id')->toArray();
                    $details = $this->medicine_requisition_floor_details->Selectone($id);
                    $mailsubject = 'Medicine Requistion Slip Floor approved';
                    $notificationData = array(
                        'notification_type' => OHC_INSPECTION,
                        'module_type' => 3,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode(array(
                            'title' => $mailsubject,
                            'message' => "Floor manager Approved the medicine requistion slip floor",
                            'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $id,
                            'module' => 1,
                        )),
                        'web_link' =>  admin_url('ohc/medical-requisition-slip/approval/view/' . encryptId($id)),
                        'assigned_user' => array_to_string(array_merge($getmedicalassistants, $getsafetyofficers)), // Fixed missing parenthesis
                        'created_by' => Auth::id(),
                    );
                    notificationSave($notificationData);
                    $title = "Medicine Requisition slip floor";
                    $mailsubject = "Medicine Requisition slip floor was Approved";
                    $details = array(
                        'ohc_type' => 'Medicine Requisition slip floor Floor manager was Approved',

                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'data' => $details
                    );
                    $recipients = array_merge($getsafetyofficerEmail, $getmedicalassistantEmail);
                    if (!empty($recipients)) {
                        Mail::to($recipients)->queue(new OccupationalHealthEmail($details));
                    }
                } else if ($request->action == "reject") {
                    $details = $this->medicine_requisition_floor_details->Selectone($id);
                    $userIds = [
                        'users' => $details->created_by,
                    ];
                    $mailsubject = 'Medicine Requistion Slip Floor  Floor manager Rejected';
                    $notificationData = array(
                        'notification_type' => OHC_INSPECTION,
                        'module_type' => 3,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode(array(
                            'title' => $mailsubject,
                            'message' => "Floor manager Rejected by the medicine requistion slip floor",
                            'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $id,
                            'module' => 1,
                        )),
                        'web_link' =>  admin_url('ohc/medical-requisition-slip/list'),
                        'assigned_user' => array_to_string($userIds),
                        'created_by' => Auth::id(),
                    );
                    notificationSave($notificationData);
                    $title = "Medicine Requistion Slip Floor";
                    $user = $details->created_by;
                    $email_id = getUseremail($user);

                    $details = array(
                        'ohc_type' => 'Medicine Requistion Slip Floor was Rejected',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'data' => $details
                    );
                    Mail::to($email_id)->queue(new OccupationalHealthEmail($details));
                }


                Session::flash('success', 'Your data has been Responded successfully!');
            } catch (Exception $ex) {
                 report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medical-requisition-slip/list'));
        } catch (Exception $ex) {

             report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-requisition-slip/list'));
        }
    }

    public function safetyofficerapproval(Request $request)
    {
        try {
            $id = decryptId($request->id);


            try {
                if ($request->action == "approve") {
                    $approveStatus = SAFETY_OFFICER_APPROVED;
                    $nextStatus = SAFETY_OFFICER_APPROVED;
                } else if ($request->action == "reject") {
                    $approveStatus = SAFETY_OFFICER_REJECTED;
                    $nextStatus = SAFETY_OFFICER_REJECTED;
                }

                $details = $this->medicine_requisition_floor_details->Selectone($id);
                $data = [
                    'type' => OHC_TYPE_MEDICINE_REQUISTION_FLOOR,
                    'from_status' => SAFETY_OFFICER_APPROVAL_PENDING,
                    'to_status' => $approveStatus,
                    'reference_id' => $id,
                    'remarks' => $request->remarks,
                    'created_by' =>  $details->created_by,
                    'approved_by' => Auth::id(),

                ];

                $signature_update = $this->signature->signatureUpload(OHC_TYPE_MEDICINE_REQUISTION_FLOOR);
                $this->inspection_ohc_status_log->store($data);
                $this->medicine_requisition_floor_details->safetyofficerapprovalupdate($id, $nextStatus);
                $details = $this->medicine_requisition_floor_details->Selectone($id);
                if ($request->action == "approve") {
                    $userIds = [
                        'users' => $details->created_by,
                    ];
                    $mailsubject = 'Medicine Requistion Slip Floor Approved';
                    $notificationData = array(
                        'notification_type' => OHC_INSPECTION,
                        'module_type' => 3,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode(array(
                            'title' => $mailsubject,
                            'message' => "Safety Officer Approved by the medicine requistion slip floor",
                            'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $id,
                            'module' => 1,
                        )),
                        'web_link' =>  admin_url('ohc/medical-requisition-slip/list'),
                        'assigned_user' => array_to_string($userIds),
                        'created_by' => Auth::id(),
                    );
                    notificationSave($notificationData);
                    $title = "Medicine Requistion Slip Floor";
                    $user = $details->created_by;
                    $email_id = getUseremail($user);

                    $details = array(
                        'ohc_type' => 'Medicine Requistion Slip Floor was Safety officer  / Medical assistant Approved',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'data' => $details
                    );
                    Mail::to($email_id)->queue(new OccupationalHealthEmail($details));
                } elseif ($request->action == "reject") {
                    $userIds = [
                        'users' => $details->created_by,
                    ];
                    $mailsubject = 'Medicine Requistion Slip Floor was Rejected';
                    $notificationData = array(
                        'notification_type' => OHC_INSPECTION,
                        'module_type' => 3,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode(array(
                            'title' => $mailsubject,
                            'message' => "Safety Officer rejected by the medicine requistion slip floor",
                            'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $id,
                            'module' => 1,
                        )),
                        'web_link' =>  admin_url('ohc/medical-requisition-slip/list'),
                        'assigned_user' => array_to_string($userIds),
                        'created_by' => Auth::id(),
                    );
                    notificationSave($notificationData);
                    $title = "Medicine Requistion Slip Floor was Rejected";
                    $user = $details->created_by;
                    $email_id = getUseremail($user);

                    $details = array(
                        'ohc_type' => 'Medicine Requistion Slip Floor was Safety officer  / Medical assistant Rejected',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'data' => $details
                    );
                    Mail::to($email_id)->queue(new OccupationalHealthEmail($details));
                }


                Session::flash('success', 'Your data has been Responded successfully!');
            } catch (Exception $ex) {
                 report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medical-requisition-slip/list'));
        } catch (Exception $ex) {

             report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-requisition-slip/list'));
        }
    }
    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->medicine_requisition_floor_details->exportdata();

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
                $medicinerequisition = $this->medicine_requisition_floor_details->Selectone($details->id);
                $medicine_requisition_floor_checklist = $this->medicine_requisition_floor_checklist->Selectone($details->id);
                $document_no = $this->document_reference->selectOne($details->document_reference_id);

                $CreatorSignature = GetOHCSignature($medicinerequisition->created_by, $details->id, OHC_TYPE_MEDICINE_REQUISTION_FLOOR);
                $safetyofficerSignature = GetOHCSignature($medicinerequisition->approved_by, $details->id, OHC_TYPE_MEDICINE_REQUISTION_FLOOR);
                $floorManagerSignature = GetOHCSignature($medicinerequisition->verified_by, $details->id, OHC_TYPE_MEDICINE_REQUISTION_FLOOR);
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
                $sheet->setCellValue("G{$currentRow}", "MEDICAL REQUISITION SLIP (मेडिकल मांग-पर्ची)
PN INTERNATIONAL PVT. LTD.");
                $sheet->getStyle("G{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Document Info
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
                // row

                $sheet->mergeCells("A" . ($currentRow + 3) . ":G" . ($currentRow + 3));
                $richText1 = new RichText();
                $richText1->createTextRun(' DEPARTMENT:- ')->getFont()->setBold(true);
                $richText1->createText(getdepartment($medicinerequisition->department));
                $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

                $sheet->mergeCells("H" . ($currentRow + 3) . ":N" . ($currentRow + 3));
                $richText2 = new RichText();
                $richText2->createTextRun('UNIT :-  ')->getFont()->setBold(true);
                $richText2->createText(getUnitname($medicinerequisition->unit));
                $sheet->getCell("H" . ($currentRow + 3))->setValue($richText2);

                $sheet->mergeCells("O" . ($currentRow + 3) . ":S" . ($currentRow + 3));
                $richText2 = new RichText();
                $richText2->createTextRun('DATE :-  ')->getFont()->setBold(true);
                $richText2->createText(Displaydateformat($medicinerequisition->date));
                $sheet->getCell("O" . ($currentRow + 3))->setValue($richText2);

                $sheet->getStyle("A" . ($currentRow + 3) . ":S" . ($currentRow + 3))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $headerRow = $currentRow + 4;

                $sheet->mergeCells("A$headerRow:C$headerRow")->setCellValue("A$headerRow", "SERIAL NO");
                $sheet->mergeCells("D$headerRow:H$headerRow")->setCellValue("D$headerRow", "NAME OF THE MEDICINE");
                $sheet->mergeCells("I$headerRow:K$headerRow")->setCellValue("I$headerRow", "FREEZE QUANTITY");
                $sheet->mergeCells("L$headerRow:N$headerRow")->setCellValue("L$headerRow", "QUANTITY");
                $sheet->mergeCells("O$headerRow:S$headerRow")->setCellValue("O$headerRow", "REMARKS");

                $sheet->getStyle("A$headerRow:S$headerRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'font' => ['bold' => true],
                ]);

                $inspectionRow = $headerRow + 1;
                foreach ($medicine_requisition_floor_checklist as $index => $detail) {
                    $sheet->mergeCells("A$inspectionRow:C$inspectionRow")->setCellValue("A$inspectionRow", $index + 1);
                    $medicineName = getMedicinename($detail->medicine_id) ?? '';
                    $material_expiry = Displaydateformat($detail->material_expiry) ?? '';
                    $sheet->mergeCells("D$inspectionRow:H$inspectionRow")->setCellValue("D$inspectionRow", $medicineName);
                    $sheet->mergeCells("I$inspectionRow:K$inspectionRow")->setCellValue("I$inspectionRow", $detail->freeze_quantity ?? '');
                    $sheet->mergeCells("L$inspectionRow:N$inspectionRow")->setCellValue("L$inspectionRow", $detail->quantity ?? '');
                    $sheet->mergeCells("O$inspectionRow:S$inspectionRow")->setCellValue("O$inspectionRow", $detail->remarks ?? '');

                    $sheet->getStyle("A$inspectionRow:S$inspectionRow")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $inspectionRow++;
                }

                $signatureStartRow = $inspectionRow;
                if (file_exists($CreatorSignature)) {
                    $sheet->mergeCells("A$signatureStartRow:F" . ($signatureStartRow + 2));

                    $drawing = new Drawing();
                    $drawing->setName('Creator Signature');
                    $drawing->setPath($CreatorSignature);
                    $drawing->setCoordinates("A$signatureStartRow");
                    $drawing->setOffsetX(100);
                    $drawing->setOffsetY(5);
                    $drawing->setWidth(70);
                    $drawing->setHeight(70);
                    $drawing->setWorksheet($sheet);
                    $sheet->getRowDimension($signatureStartRow + 2)->setRowHeight(40);
                    // Label + Name
                    $sheet->setCellValue("A" . ($signatureStartRow + 3), "CREATOR SIGNATURE: " . getUserName($medicinerequisition->created_by));
                    $sheet->mergeCells("A" . ($signatureStartRow + 3) . ":F" . ($signatureStartRow + 3));

                    $sheet->getStyle("A$signatureStartRow:F" . ($signatureStartRow + 3))->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                }

                if (file_exists($floorManagerSignature)) {
                    $sheet->mergeCells("G$signatureStartRow:L" . ($signatureStartRow + 2));

                    $drawing = new Drawing();
                    $drawing->setName('Verified Signature');
                    $drawing->setPath($floorManagerSignature);
                    $drawing->setCoordinates("G$signatureStartRow");
                    $drawing->setOffsetX(100);
                    $drawing->setOffsetY(5);
                    $drawing->setWidth(70);
                    $drawing->setHeight(70);
                    $drawing->setWorksheet($sheet);
                    $sheet->getRowDimension($signatureStartRow + 2)->setRowHeight(40);
                    // Label + Name
                    $sheet->setCellValue("G" . ($signatureStartRow + 3), "FLOOR MANAGER SIGNATURE: " . getUserName($medicinerequisition->verified_by));
                    $sheet->mergeCells("G" . ($signatureStartRow + 3) . ":L" . ($signatureStartRow + 3));

                    $sheet->getStyle("G$signatureStartRow:L" . ($signatureStartRow + 3))->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                }

                if (file_exists($safetyofficerSignature)) {
                    $sheet->mergeCells("M$signatureStartRow:S" . ($signatureStartRow + 2));

                    $drawing = new Drawing();
                    $drawing->setName('Approved Signature');
                    $drawing->setPath($safetyofficerSignature);
                    $drawing->setCoordinates("M$signatureStartRow");
                    $drawing->setOffsetX(100);
                    $drawing->setOffsetY(5);
                    $drawing->setWidth(70);
                    $drawing->setHeight(70);
                    $drawing->setWorksheet($sheet);
                    $sheet->getRowDimension($signatureStartRow + 2)->setRowHeight(60);
                    // Label + Name
                    $sheet->setCellValue("M" . ($signatureStartRow + 3), "SAFETY OFFICER SIGNATURE: " . getUserName($medicinerequisition->approved_by));
                    $sheet->mergeCells("M" . ($signatureStartRow + 3) . ":S" . ($signatureStartRow + 3));

                    $sheet->getStyle("M$signatureStartRow:S" . ($signatureStartRow + 3))->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                }

                $row =  $signatureStartRow + 5;
            }
            $fileName = 'Medical Requisition Slip.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (Exception $ex) {

             report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-requisition-slip/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->medicine_requisition_floor_details->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }

            foreach( $allData as $details){
                $document_no = $this->document_reference->selectOne($details->document_reference_id);

            }


            $data = array(
                'document_no' => $document_no,
                'content' => $allData,
                'pagetitle' => "Medicine Requisition Slip Floor",
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

            $view = view('inspection.inspection_ohc.medical_requisition_slip.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Medicine Requisition Slip Floor.pdf";
            $mpdf->Output($filename, 'i');
        } catch (Exception $ex) {

             report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-requisition-slip/list'));
        }
    }

    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $medicinerequisition = $this->medicine_requisition_floor_details->Selectone($id);
            $medicine_requisition_floor_checklist = $this->medicine_requisition_floor_checklist->Selectone($id);
            $row = 1;
            $document_no = $this->document_reference->selectOne($medicinerequisition->document_reference_id);

            $CreatorSignature = GetOHCSignature($medicinerequisition->created_by, $id, OHC_TYPE_MEDICINE_REQUISTION_FLOOR);
            $safetyofficerSignature = GetOHCSignature($medicinerequisition->approved_by, $id, OHC_TYPE_MEDICINE_REQUISTION_FLOOR);
            $floorManagerSignature = GetOHCSignature($medicinerequisition->verified_by, $id, OHC_TYPE_MEDICINE_REQUISTION_FLOOR);


            $currentRow = $row;


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
            $sheet->setCellValue("G{$currentRow}", "MEDICAL REQUISITION SLIP (मेडिकल मांग-पर्ची)
PN INTERNATIONAL PVT. LTD.");
            $sheet->getStyle("G{$currentRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            // Document Info
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
            // row

            $sheet->mergeCells("A" . ($currentRow + 3) . ":G" . ($currentRow + 3));
            $richText1 = new RichText();
            $richText1->createTextRun(' DEPARTMENT:- ')->getFont()->setBold(true);
            $richText1->createText(getdepartment($medicinerequisition->department));
            $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

            $sheet->mergeCells("H" . ($currentRow + 3) . ":N" . ($currentRow + 3));
            $richText2 = new RichText();
            $richText2->createTextRun('UNIT :-  ')->getFont()->setBold(true);
            $richText2->createText(getUnitname($medicinerequisition->unit));
            $sheet->getCell("H" . ($currentRow + 3))->setValue($richText2);

            $sheet->mergeCells("O" . ($currentRow + 3) . ":S" . ($currentRow + 3));
            $richText2 = new RichText();
            $richText2->createTextRun('DATE :-  ')->getFont()->setBold(true);
            $richText2->createText(Displaydateformat($medicinerequisition->date));
            $sheet->getCell("O" . ($currentRow + 3))->setValue($richText2);

            $sheet->getStyle("A" . ($currentRow + 3) . ":S" . ($currentRow + 3))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $headerRow = $currentRow + 4;

            $sheet->mergeCells("A$headerRow:C$headerRow")->setCellValue("A$headerRow", "SERIAL NO");
            $sheet->mergeCells("D$headerRow:H$headerRow")->setCellValue("D$headerRow", "NAME OF THE MEDICINE");
            $sheet->mergeCells("I$headerRow:K$headerRow")->setCellValue("I$headerRow", "FREEZE QUANTITY");
            $sheet->mergeCells("L$headerRow:N$headerRow")->setCellValue("L$headerRow", "QUANTITY");
            $sheet->mergeCells("O$headerRow:S$headerRow")->setCellValue("O$headerRow", "REMARKS");

            $sheet->getStyle("A$headerRow:S$headerRow")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font' => ['bold' => true],
            ]);

            $inspectionRow = $headerRow + 1;
            foreach ($medicine_requisition_floor_checklist as $index => $detail) {
                $sheet->mergeCells("A$inspectionRow:C$inspectionRow")->setCellValue("A$inspectionRow", $index + 1);
                $medicineName = getMedicinename($detail->medicine_id) ?? '';
                $material_expiry = Displaydateformat($detail->material_expiry) ?? '';
                $sheet->mergeCells("D$inspectionRow:H$inspectionRow")->setCellValue("D$inspectionRow", $medicineName);
                $sheet->mergeCells("I$inspectionRow:K$inspectionRow")->setCellValue("I$inspectionRow", $detail->freeze_quantity ?? '');
                $sheet->mergeCells("L$inspectionRow:N$inspectionRow")->setCellValue("L$inspectionRow", $detail->quantity ?? '');
                $sheet->mergeCells("O$inspectionRow:S$inspectionRow")->setCellValue("O$inspectionRow", $detail->remarks ?? '');

                $sheet->getStyle("A$inspectionRow:S$inspectionRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $inspectionRow++;
            }

            $signatureStartRow = $inspectionRow;
            if (file_exists($CreatorSignature)) {
                $sheet->mergeCells("A$signatureStartRow:F" . ($signatureStartRow + 2));

                $drawing = new Drawing();
                $drawing->setName('Creator Signature');
                $drawing->setPath($CreatorSignature);
                $drawing->setCoordinates("A$signatureStartRow");
                $drawing->setOffsetX(100);
                $drawing->setOffsetY(5);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
                $sheet->getRowDimension($signatureStartRow + 2)->setRowHeight(40);
                // Label + Name
                $sheet->setCellValue("A" . ($signatureStartRow + 3), "CREATOR SIGNATURE: " . getUserName($medicinerequisition->created_by));
                $sheet->mergeCells("A" . ($signatureStartRow + 3) . ":F" . ($signatureStartRow + 3));

                $sheet->getStyle("A$signatureStartRow:F" . ($signatureStartRow + 3))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }

            if (file_exists($floorManagerSignature)) {
                $sheet->mergeCells("G$signatureStartRow:L" . ($signatureStartRow + 2));

                $drawing = new Drawing();
                $drawing->setName('Verified Signature');
                $drawing->setPath($floorManagerSignature);
                $drawing->setCoordinates("G$signatureStartRow");
                $drawing->setOffsetX(100);
                $drawing->setOffsetY(5);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
                $sheet->getRowDimension($signatureStartRow + 2)->setRowHeight(40);
                // Label + Name
                $sheet->setCellValue("G" . ($signatureStartRow + 3), "FLOOR MANAGER SIGNATURE: " . getUserName($medicinerequisition->verified_by));
                $sheet->mergeCells("G" . ($signatureStartRow + 3) . ":L" . ($signatureStartRow + 3));

                $sheet->getStyle("G$signatureStartRow:L" . ($signatureStartRow + 3))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }

            if (file_exists($safetyofficerSignature)) {
                $sheet->mergeCells("M$signatureStartRow:S" . ($signatureStartRow + 2));

                $drawing = new Drawing();
                $drawing->setName('Approved Signature');
                $drawing->setPath($safetyofficerSignature);
                $drawing->setCoordinates("M$signatureStartRow");
                $drawing->setOffsetX(100);
                $drawing->setOffsetY(5);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
                $sheet->getRowDimension($signatureStartRow + 2)->setRowHeight(60);
                // Label + Name
                $sheet->setCellValue("M" . ($signatureStartRow + 3), "SAFETY OFFICER SIGNATURE: " . getUserName($medicinerequisition->approved_by));
                $sheet->mergeCells("M" . ($signatureStartRow + 3) . ":S" . ($signatureStartRow + 3));

                $sheet->getStyle("M$signatureStartRow:S" . ($signatureStartRow + 3))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }

            $fileName = 'Medical Requisition Slip.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/medical-requisition-slip/fdo-security-gate/list'));
        }
    }
}
