<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use App\Mail\Inspection\Ohc\MedicineRequistionFdoEmail;
use Illuminate\Http\Request;
use App\Models\Master\Department;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use App\Models\Inspection\Master\Shift;

use App\Models\Inspection\Ohc\MedicineRequistionFdoChecklist;
use App\Models\Inspection\Ohc\MedicineRequistionSlipfdodetails;
use App\Models\OhcManagement\Report\Inventory;
use App\Models\UploadLog;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Models\Inspection\Ohc\InspectionOhcStatuslog;
use Illuminate\Support\Facades\Mail;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\InspectionStaticDocno;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class MedicalRequisitionSlipSecurityGateController extends Controller
{

    private $upload_log;
    private $unit;
    private $shift;
    private $department;
    private $user;
    private $medicine_requisition_fdo_checklist;
    private $inventory;
    private $medicine_requisition_fdo_details;
    private $signature;
    private $inspection_ohc_status_log;
    private $document_reference;
    private $location;
    public function __construct()
    {

        $this->upload_log = new UploadLog();
        $this->unit = new Unit();
        $this->department = new Department();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->medicine_requisition_fdo_checklist = new MedicineRequistionFdoChecklist();
        $this->inventory = new Inventory();
        $this->user = new User();
        $this->signature = new OhcSignature();
        $this->inspection_ohc_status_log = new InspectionOhcStatuslog();
        $this->document_reference = new InspectionStaticDocno();
        $this->medicine_requisition_fdo_details = new MedicineRequistionSlipfdodetails();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->medicine_requisition_fdo_details->list();

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
                            return ($row->unit_name);
                        })
                        ->addColumn('department', function ($row) {
                            return ($row->department_name);
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
                            $btn .= '<a href="' . admin_url('ohc/medical-requisition-slip/fdo-security-gate/view/' . encryptId($row->inspection_id)) . '" class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a>';

                            if (((checkUserRole(ROLE_SAFETY_OFFICER) && $row->approve_status == SAFETY_OFFICER_APPROVAL_PENDING) || (checkUserRole(ROLE_SUPERADMIN) && $row->approve_status == SAFETY_OFFICER_APPROVAL_PENDING)) || ((checkUserRole(ROLE_MEDICAL_ASSISTANT) && $row->approve_status == SAFETY_OFFICER_APPROVAL_PENDING) || (checkUserRole(ROLE_SUPERADMIN) && $row->approve_status == SAFETY_OFFICER_APPROVAL_PENDING))) {
                                $btn .= '<a href="' . admin_url('ohc/medical-requisition-slip/fdo-security-gate/approval/view/' . encryptId($row->inspection_id)) . '" class="me-1" title="Action"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }

                            $btn .= '<a href="' . admin_url('ohc/medical-requisition-slip/fdo-security-gate/generalpdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';
                            $btn .= '<a href="' . admin_url('ohc/medical-requisition-slip/fdo-security-gate/generalExcel/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
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
        $data = array(
            'unit' => $unit,


        );
        return view('inspection.inspection_ohc.medical_requisition_slip_security_gate.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $shift = $this->shift->getShiftname();
            $medicine = $this->inventory->getstockdata();
            $signature_upload = $this->user->getSignature();
            $location = $this->location->getLocationname();
            $document_no = $this->document_reference->selectUsingName('MedicalRequisitionSlipFdoSecurityGate');
            $data = array(
                'unit' => $unit,
                'shift' => $shift,
                'medicine' => $medicine,
                'document_no' => $document_no,
                'signature_upload' => $signature_upload,

            );
            return view('inspection.inspection_ohc.medical_requisition_slip_security_gate.add', $data);
        } catch (Exception $ex) {
             report($ex);
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
                $medicine_requisition_fdo_details = $this->medicine_requisition_fdo_details->store();
                $id = ($medicine_requisition_fdo_details->id);
                $signature = $this->signature->requestorsignatureUpload(OHC_TYPE_MEDICINE_REQUISTION_FDO, $id);
                $medicine_requisition_fdo_checklist = $this->medicine_requisition_fdo_checklist->store($medicine_requisition_fdo_details);
                $data = [
                    'type' => OHC_TYPE_MEDICINE_REQUISTION_FDO,
                    'from_status' => OHC_CREATION,
                    'to_status' => SAFETY_OFFICER_APPROVAL_PENDING,
                    'reference_id' => $medicine_requisition_fdo_details->id,
                    'remarks' => "",
                    'approved_by' => null,
                    'created_by' => Auth::id(),

                ];
                $id = $medicine_requisition_fdo_details->id;
                $this->inspection_ohc_status_log->store($data);

                // Safety Officer

                $getsafetyofficer = getSafetyOfficer();
                $getsafetyofficers = $getsafetyofficer->pluck('id')->toArray();
                $getsafetyofficerEmail = $getsafetyofficer->pluck('email')->toArray();

                // medical officer

                $getmedicalassistant = getMedicalAssistant();
                $getmedicalassistantEmail = $getmedicalassistant->pluck('email')->toArray();
                $getmedicalassistants = $getmedicalassistant->pluck('id')->toArray();
                // Select One
                $medicine_requisition_fdo_details = $this->medicine_requisition_fdo_details->Selectone($id);
                $medicine_requisition_fdo_checklist_details = $this->medicine_requisition_fdo_checklist->Selectone($id);
                // notification and email

                $title = "Medical Requisition Slip- Fdo & Security Gate";
                $mailsubject = "Medical Requisition Slip- Fdo & Security Gate";
                $details = array(
                    'ohc_type' => 'Medical Requisition Slip- Fdo & Security Gate',
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'data' => $medicine_requisition_fdo_details,
                    'checklist' =>   $medicine_requisition_fdo_checklist_details
                );

                $recipients = array_merge($getsafetyofficerEmail, $getmedicalassistantEmail);
                if (!empty($recipients)) {
                    Mail::to($recipients)->queue(new MedicineRequistionFdoEmail($details));
                }

                $notificationData = array(
                    'notification_type' => OHC_INSPECTION,
                    'module_type' => 2,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => "Requestor Created the medicine requisition slip Fdo & Security Gate",
                        'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $id,
                        'module' => 1,
                    )),
                    'web_link' => admin_url('ohc/medical-requisition-slip/fdo-security-gate/approval/view/' . encryptId($id)), // Fixed concatenation
                    'assigned_user' => array_to_string(array_merge($getmedicalassistants, $getsafetyofficers)), // Fixed missing parenthesis
                    'created_by' => Auth::id(),
                );

                notificationSave($notificationData);

                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                 report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medical-requisition-slip/fdo-security-gate/list'));
        } catch (Exception $ex) {

             report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-requisition-slip/fdo-security-gate/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $medicine_requisition_fdo_details = $this->medicine_requisition_fdo_details->Selectone($id);
                // dd(  $medicine_requisition_fdo_details)
                $medicine_requisition_fdo_checklist_details = $this->medicine_requisition_fdo_checklist->Selectone($id);
                $type = OHC_TYPE_MEDICINE_REQUISTION_FDO;
                $safetytype = SAFETY_OFFICER_APPROVAL_PENDING;
                $statuslog = $this->inspection_ohc_status_log->getStatuslog($id, $type);

                $safetyofficer = $this->inspection_ohc_status_log->safetyofficer($id, $safetytype, $type);
                $safetyofficersignature = null;
                $requestorsignature =  $medicine_requisition_fdo_details->created_by;
                if (!empty($safetyofficer) && !empty($safetyofficer->approved_by)) {
                    $safetyofficersignature = $this->signature->safetyofficersignature($id, $safetyofficer, $type);
                }

                $requestor_signature = $this->signature->requestorSignature($id, $requestorsignature, $type);
                $signatureview = $this->user->where('id', $requestorsignature)->first();
                $approversignatureview = null; // Initialize the variable

                if (!empty($safetyofficer) && !empty($safetyofficer->approved_by)) {
                    $approversignatureview = $this->user->where('id', $safetyofficer->approved_by)->first();
                }
                $document_no = $this->document_reference->selectUsingName('MedicalRequisitionSlipFdoSecurityGate');
                $data = array(
                    'medicinerequisition' => $medicine_requisition_fdo_details,
                    'medicine_requisition_fdo_checklist' => $medicine_requisition_fdo_checklist_details,
                    'statuslog' => $statuslog,
                    'safetyofficer' => $safetyofficer,
                    'requestorsignature' => $requestor_signature,
                    'safetyofficersignature' => $safetyofficersignature,
                    'approversignatureview' => $approversignatureview,
                    'signatureview' => $signatureview,
                    'document_no' => $document_no,

                );
            }
            return view('inspection.inspection_ohc.medical_requisition_slip_security_gate.view', $data);
        } catch (Exception $ex) {
             report($ex);
        }
    }

    public function approval(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $medicine_requisition_fdo_details = $this->medicine_requisition_fdo_details->Selectone($id);
                $medicine_requisition_fdo_checklist_details = $this->medicine_requisition_fdo_checklist->Selectone($id);
                $type = OHC_TYPE_MEDICINE_REQUISTION_FDO;
                $safetytype = SAFETY_OFFICER_APPROVAL_PENDING;
                $statuslog = $this->inspection_ohc_status_log->getStatuslog($id, $type);

                $safetyofficer = $this->inspection_ohc_status_log->safetyofficer($id, $safetytype, $type);
                $safetyofficersignature = null;
                $requestorsignature =  $medicine_requisition_fdo_details->created_by;
                if (!empty($safetyofficer) && !empty($safetyofficer->approved_by)) {
                    $safetyofficersignature = $this->signature->safetyofficersignature($id, $safetyofficer, $type);
                }

                $requestor_signature = $this->signature->requestorSignature($id, $requestorsignature, $type);
                $signatureview = $this->user->where('id', $requestorsignature)->first();
                $approversignatureview = null; // Initialize the variable

                if (!empty($safetyofficer) && !empty($safetyofficer->approved_by)) {
                    $approversignatureview = $this->user->where('id', $safetyofficer->approved_by)->first();
                }

                $document_no = $this->document_reference->selectUsingName('MedicalRequisitionSlipFdoSecurityGate');
                $data = array(
                    'medicinerequisition' => $medicine_requisition_fdo_details,
                    'medicine_requisition_fdo_checklist' => $medicine_requisition_fdo_checklist_details,
                    'statuslog' => $statuslog,
                    'safetyofficer' => $safetyofficer,
                    'requestorsignature' => $requestor_signature,
                    'safetyofficersignature' => $safetyofficersignature,
                    'approversignatureview' => $approversignatureview,
                    'signatureview' => $signatureview,
                    'document_no' => $document_no,
                );
            }
            return view('inspection.inspection_ohc.medical_requisition_slip_security_gate.approval', $data);
        } catch (Exception $ex) {
             report($ex);
        }
    }

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $medicine_requisition_fdo_details = $this->medicine_requisition_fdo_details->Selectone($id);
                $medicine_requisition_fdo_checklist_details = $this->medicine_requisition_fdo_checklist->Selectone($id);
                $type = OHC_TYPE_MEDICINE_REQUISTION_FDO;
                $safetytype = SAFETY_OFFICER_APPROVAL_PENDING;
                $statuslog = $this->inspection_ohc_status_log->getStatuslog($id, $type);

                $safetyofficer = $this->inspection_ohc_status_log->safetyofficer($id, $safetytype, $type);
                $safetyofficersignature = null;
                $requestorsignature =  $medicine_requisition_fdo_details->created_by;
                if (!empty($safetyofficer) && !empty($safetyofficer->approved_by)) {
                    $safetyofficersignature = $this->signature->safetyofficersignature($id, $safetyofficer, $type);
                }

                $requestor_signature = $this->signature->requestorSignature($id, $requestorsignature, $type);
                $signatureview = $this->user->where('id', $requestorsignature)->first();
                $approversignatureview = null;

                if (!empty($safetyofficer) && !empty($safetyofficer->approved_by)) {
                    $approversignatureview = $this->user->where('id', $safetyofficer->approved_by)->first();
                }

                $document_no = $this->document_reference->selectUsingName('MedicalRequisitionSlipFdoSecurityGate');
            }

            $data = [
                'medicine_requisition_fdo_details' => $medicine_requisition_fdo_details,
                'medicine_requisition_fdo_checklist' => $medicine_requisition_fdo_checklist_details,
                'statuslog' => $statuslog,
                'safetyofficer' => $safetyofficer,
                'requestorsignature' => $requestor_signature,
                'document_no' => $document_no,
                'safetyofficersignature' => $safetyofficersignature,
                'approversignatureview' => $approversignatureview,
                'signatureview' => $signatureview,
                'pagetitle' => "Medical Requisition Slip - FDO & Security Gate",
            ];

            $property = [
                'tempDir' => storage_path('app/public/pdf/temp/'),
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            // Load HTML from the Blade view
            $html = view('inspection.inspection_ohc.medical_requisition_slip_security_gate.viewpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "Medical_Requisition_Slip_FDO_Security_Gate.pdf";

            return $mpdf->Output($filename, 'D');
        } catch (\Exception $ex) {
             report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
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

                $details = $this->medicine_requisition_fdo_details->Selectone($id);
                $data = [
                    'type' => OHC_TYPE_MEDICINE_REQUISTION_FDO,
                    'from_status' => SAFETY_OFFICER_APPROVAL_PENDING,
                    'to_status' => $approveStatus,
                    'reference_id' => $id,
                    'remarks' => $request->remarks,
                    'created_by' =>  $details->created_by,
                    'approved_by' => Auth::id(),

                ];

                $signature_update = $this->signature->signatureUpload(OHC_TYPE_MEDICINE_REQUISTION_FDO);
                $this->inspection_ohc_status_log->store($data);
                $this->medicine_requisition_fdo_details->safetyofficerapprovalupdate($id, $nextStatus);
                $details = $this->medicine_requisition_fdo_details->Selectone($id);
                if ($request->action == "approve") {
                    $userIds = [
                        'users' => $details->created_by,
                    ];
                    $mailsubject = 'Medical Requisition Slip- Fdo & Security Gate Approved';
                    $notificationData = array(
                        'notification_type' => OHC_INSPECTION,
                        'module_type' => 2,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode(array(
                            'title' => $mailsubject,
                            'message' => "Safety Officer Approved by the medicine requistion slip Fdo & Security Gate",
                            'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $id,
                            'module' => 1,
                        )),
                        'web_link' =>  admin_url('ohc/medical-requisition-slip/fdo-security-gate/list'),
                        'assigned_user' => array_to_string($userIds),
                        'created_by' => Auth::id(),
                    );
                    notificationSave($notificationData);
                    $title = "Medical Requisition Slip- Fdo & Security Gate";
                    $user = $details->created_by;
                    $email_id = getUseremail($user);
                    $medicine_requisition_fdo_details = $this->medicine_requisition_fdo_details->Selectone($id);
                    $medicine_requisition_fdo_checklist_details = $this->medicine_requisition_fdo_checklist->Selectone($id);
                    $details = array(
                        'ohc_type' => 'Medical Requisition Slip- Fdo & Security Gate was Approved',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'data' => $medicine_requisition_fdo_details,
                        'checklist' =>   $medicine_requisition_fdo_checklist_details
                    );
                    Mail::to($email_id)->queue(new MedicineRequistionFdoEmail($details));
                } else if ($request->action == "reject") {
                    $userIds = [
                        'users' => $details->created_by,
                    ];
                    $mailsubject = 'Medical Requisition Slip- Fdo & Security Gate Rejected';
                    $notificationData = array(
                        'notification_type' => OHC_INSPECTION,
                        'module_type' => 2,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode(array(
                            'title' => $mailsubject,
                            'message' => "Safety Officer Rejected by the medicine requistion slip Fdo & Security Gate",
                            'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $id,
                            'module' => 1,
                        )),
                        'web_link' =>  admin_url('ohc/medical-requisition-slip/fdo-security-gate/list'),
                        'assigned_user' => array_to_string($userIds),
                        'created_by' => Auth::id(),
                    );
                    notificationSave($notificationData);
                    $title = "Medical Requisition Slip- Fdo & Security Gate ";
                    $user = $details->created_by;
                    $email_id = getUseremail($user);
                    $medicine_requisition_fdo_details = $this->medicine_requisition_fdo_details->Selectone($id);
                    $medicine_requisition_fdo_checklist_details = $this->medicine_requisition_fdo_checklist->Selectone($id);
                    $details = array(
                        'ohc_type' => 'Medical Requisition Slip- Fdo & Security Gate was  Rejected',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'data' => $medicine_requisition_fdo_details,
                        'checklist' =>   $medicine_requisition_fdo_checklist_details
                    );
                    Mail::to($email_id)->queue(new MedicineRequistionFdoEmail($details));
                }


                Session::flash('success', 'Your data has been Responded successfully!');
            } catch (Exception $ex) {
                 report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medical-requisition-slip/fdo-security-gate/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-requisition-slip/fdo-security-gate/list'));
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->medicine_requisition_fdo_details->exportdata();

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
          foreach( $allData as $details){

            $currentRow = $row;

            $medicine_requisition_fdo_details = $this->medicine_requisition_fdo_details->Selectone($details->id);
            $medicine_requisition_fdo_checklist_details = $this->medicine_requisition_fdo_checklist->Selectone($details->id);
            $document_no = $this->document_reference->selectOne($medicine_requisition_fdo_details->document_reference_id);
            $safetyofficerSignature = GetOHCSignature($medicine_requisition_fdo_details->approved_by, $details->id, OHC_TYPE_MEDICINE_REQUISTION_FDO);
            $RequestorSignature = GetOHCSignature($medicine_requisition_fdo_details->created_by, $details->id, OHC_TYPE_MEDICINE_REQUISTION_FDO);

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
            $sheet->setCellValue("G{$currentRow}", "MEDICAL REQUISITION ISSUE SLIP PN INTERNATIONAL PVT. LTD.");

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


            $sheet->mergeCells("A" . ($currentRow + 3) . ":G" . ($currentRow + 3));
            $richText1 = new RichText();
            $richText1->createTextRun(' DEPARTMENT:- ')->getFont()->setBold(true);
            $richText1->createText(getdepartment($medicine_requisition_fdo_details->department));
            $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

            $sheet->mergeCells("H" . ($currentRow + 3) . ":N" . ($currentRow + 3));
            $richText2 = new RichText();
            $richText2->createTextRun('UNIT :-  ')->getFont()->setBold(true);
            $richText2->createText(getUnitname($medicine_requisition_fdo_details->unit));
            $sheet->getCell("H" . ($currentRow + 3))->setValue($richText2);

            $sheet->mergeCells("O" . ($currentRow + 3) . ":S" . ($currentRow + 3));
            $richText2 = new RichText();
            $richText2->createTextRun('DATE :-  ')->getFont()->setBold(true);
            $richText2->createText(Displaydateformat($medicine_requisition_fdo_details->date));
            $sheet->getCell("O" . ($currentRow + 3))->setValue($richText2);

            $sheet->getStyle("A" . ($currentRow + 3) . ":S" . ($currentRow + 3))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $headerRow = $currentRow + 4;

            $sheet->mergeCells("A$headerRow:C$headerRow")->setCellValue("A$headerRow", "SERIAL NO");
            $sheet->mergeCells("D$headerRow:I$headerRow")->setCellValue("D$headerRow", "NAME OF THE MEDICINE");
            $sheet->mergeCells("J$headerRow:N$headerRow")->setCellValue("J$headerRow", "QUANTITY");
            $sheet->mergeCells("O$headerRow:S$headerRow")->setCellValue("O$headerRow", "REMARKS");

            $sheet->getStyle("A$headerRow:S$headerRow")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font' => ['bold' => true],
            ]);


            $inspectionRow = $headerRow + 1;
            foreach ($medicine_requisition_fdo_checklist_details as $index => $detail) {
                $sheet->mergeCells("A$inspectionRow:C$inspectionRow")->setCellValue("A$inspectionRow", $index + 1);
                $medicineName = getMedicinename($detail->medicine_id) ?? '';
                $material_expiry = Displaydateformat($detail->material_expiry) ?? '';
                $sheet->mergeCells("D$inspectionRow:I$inspectionRow")->setCellValue("D$inspectionRow", $medicineName);
                $sheet->mergeCells("J$inspectionRow:N$inspectionRow")->setCellValue("J$inspectionRow", $detail->quantity ?? '');
                $sheet->mergeCells("O$inspectionRow:S$inspectionRow")->setCellValue("O$inspectionRow", $detail->remarks ?? '');

                $sheet->getStyle("A$inspectionRow:S$inspectionRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $inspectionRow++;
            }

            $signatureStartRow = $inspectionRow;
            $signatureEndRow = $signatureStartRow + 3;
            $labelRow = $signatureEndRow + 1;
            $imageHeight = 60;

            // Requestor Signature
            if (file_exists($RequestorSignature)) {
                $sheet->mergeCells("A$signatureStartRow:J" . ($signatureStartRow + 2));

                $drawing = new Drawing();
                $drawing->setName('CREATOR SIGNATURE');
                $drawing->setPath($RequestorSignature);
                $drawing->setCoordinates("A$signatureStartRow");
                $drawing->setOffsetX(100);
                $drawing->setOffsetY(5);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
                $sheet->getRowDimension($signatureStartRow + 2)->setRowHeight(40);
                // Label + Name
                $sheet->setCellValue("A" . ($signatureStartRow + 3), "CREATOR SIGNATURE: " . getUserName($medicine_requisition_fdo_details->created_by));
                $sheet->mergeCells("A" . ($signatureStartRow + 3) . ":J" . ($signatureStartRow + 3));

                $sheet->getStyle("A$signatureStartRow:J" . ($signatureStartRow + 3))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }

            if (file_exists($safetyofficerSignature)) {
                $sheet->mergeCells("K$signatureStartRow:S" . ($signatureStartRow + 2));

                $drawing = new Drawing();
                $drawing->setName('SAFETY OFFICER SIGNATURE');
                $drawing->setPath($safetyofficerSignature);
                $drawing->setCoordinates("M$signatureStartRow");
                $drawing->setOffsetX(100);
                $drawing->setOffsetY(5);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
                $sheet->getRowDimension($signatureStartRow + 2)->setRowHeight(40);
                // Label + Name
                $sheet->setCellValue("K" . ($signatureStartRow + 3), "SAFETY OFFICER SIGNATURE: " . getUserName($medicine_requisition_fdo_details->approved_by));
                $sheet->mergeCells("K" . ($signatureStartRow + 3) . ":S" . ($signatureStartRow + 3));

                $sheet->getStyle("K$signatureStartRow:S" . ($signatureStartRow + 3))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }

            $row =   $signatureStartRow + 8;
          }
          $fileName = 'Medical Requisition Slip- Fdo & Security Gate.xlsx';
          $writer = new Xlsx($spreadsheet);

          return response()->streamDownload(function () use ($writer) {
              $writer->save('php://output');
          }, $fileName, [
              'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
          ]);
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-requisition-slip/fdo-security-gate/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->medicine_requisition_fdo_details->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }

            foreach( $allData as $details){
                $document_no = $this->document_reference->selectOne($details->document_reference_id);

            }


            $header = [
                __("common.sno"),
                'Document Number',
                'Review date',
                'Issued Date',
                'Unit',
                'Department',
                'Approve Status',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'document_no' => $document_no,
                'pagetitle' => "Medical Requisition Slip- Fdo & Security Gate",
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

            $view = view('inspection.inspection_ohc.medical_requisition_slip_security_gate.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Medical Requisition Slip- Fdo & Security Gate.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

             report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-requisition-slip/fdo-security-gate/list'));
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
            $medicine_requisition_fdo_details = $this->medicine_requisition_fdo_details->Selectone($id);
            $medicine_requisition_fdo_checklist_details = $this->medicine_requisition_fdo_checklist->Selectone($id);
            $document_no = $this->document_reference->selectOne($medicine_requisition_fdo_details->document_reference_id);
            $safetyofficerSignature = GetOHCSignature($medicine_requisition_fdo_details->approved_by, $id, OHC_TYPE_MEDICINE_REQUISTION_FDO);
            $RequestorSignature = GetOHCSignature($medicine_requisition_fdo_details->created_by, $id, OHC_TYPE_MEDICINE_REQUISTION_FDO);
            $row = 1;
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
            $sheet->setCellValue("G{$currentRow}", "MEDICAL REQUISITION ISSUE SLIP PN INTERNATIONAL PVT. LTD.");

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


            $sheet->mergeCells("A" . ($currentRow + 3) . ":G" . ($currentRow + 3));
            $richText1 = new RichText();
            $richText1->createTextRun(' DEPARTMENT:- ')->getFont()->setBold(true);
            $richText1->createText(getdepartment($medicine_requisition_fdo_details->department));
            $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);

            $sheet->mergeCells("H" . ($currentRow + 3) . ":N" . ($currentRow + 3));
            $richText2 = new RichText();
            $richText2->createTextRun('UNIT :-  ')->getFont()->setBold(true);
            $richText2->createText(getUnitname($medicine_requisition_fdo_details->unit));
            $sheet->getCell("H" . ($currentRow + 3))->setValue($richText2);

            $sheet->mergeCells("O" . ($currentRow + 3) . ":S" . ($currentRow + 3));
            $richText2 = new RichText();
            $richText2->createTextRun('DATE :-  ')->getFont()->setBold(true);
            $richText2->createText(Displaydateformat($medicine_requisition_fdo_details->date));
            $sheet->getCell("O" . ($currentRow + 3))->setValue($richText2);

            $sheet->getStyle("A" . ($currentRow + 3) . ":S" . ($currentRow + 3))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $headerRow = $currentRow + 4;

            $sheet->mergeCells("A$headerRow:C$headerRow")->setCellValue("A$headerRow", "SERIAL NO");
            $sheet->mergeCells("D$headerRow:I$headerRow")->setCellValue("D$headerRow", "NAME OF THE MEDICINE");
            $sheet->mergeCells("J$headerRow:N$headerRow")->setCellValue("J$headerRow", "QUANTITY");
            $sheet->mergeCells("O$headerRow:S$headerRow")->setCellValue("O$headerRow", "REMARKS");

            $sheet->getStyle("A$headerRow:S$headerRow")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font' => ['bold' => true],
            ]);


            $inspectionRow = $headerRow + 1;
            foreach ($medicine_requisition_fdo_checklist_details as $index => $detail) {
                $sheet->mergeCells("A$inspectionRow:C$inspectionRow")->setCellValue("A$inspectionRow", $index + 1);
                $medicineName = getMedicinename($detail->medicine_id) ?? '';
                $material_expiry = Displaydateformat($detail->material_expiry) ?? '';
                $sheet->mergeCells("D$inspectionRow:I$inspectionRow")->setCellValue("D$inspectionRow", $medicineName);
                $sheet->mergeCells("J$inspectionRow:N$inspectionRow")->setCellValue("J$inspectionRow", $detail->quantity ?? '');
                $sheet->mergeCells("O$inspectionRow:S$inspectionRow")->setCellValue("O$inspectionRow", $detail->remarks ?? '');

                $sheet->getStyle("A$inspectionRow:S$inspectionRow")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $inspectionRow++;
            }

            $signatureStartRow = $inspectionRow;
            $signatureEndRow = $signatureStartRow + 3;
            $labelRow = $signatureEndRow + 1;
            $imageHeight = 60;

            // Requestor Signature
            if (file_exists($RequestorSignature)) {
                $sheet->mergeCells("A$signatureStartRow:J" . ($signatureStartRow + 2));

                $drawing = new Drawing();
                $drawing->setName('CREATOR SIGNATURE');
                $drawing->setPath($RequestorSignature);
                $drawing->setCoordinates("A$signatureStartRow");
                $drawing->setOffsetX(100);
                $drawing->setOffsetY(5);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
                $sheet->getRowDimension($signatureStartRow + 2)->setRowHeight(40);
                // Label + Name
                $sheet->setCellValue("A" . ($signatureStartRow + 3), "CREATOR SIGNATURE: " . getUserName($medicine_requisition_fdo_details->created_by));
                $sheet->mergeCells("A" . ($signatureStartRow + 3) . ":J" . ($signatureStartRow + 3));

                $sheet->getStyle("A$signatureStartRow:J" . ($signatureStartRow + 3))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }

            if (file_exists($safetyofficerSignature)) {
                $sheet->mergeCells("K$signatureStartRow:S" . ($signatureStartRow + 2));

                $drawing = new Drawing();
                $drawing->setName('SAFETY OFFICER SIGNATURE');
                $drawing->setPath($safetyofficerSignature);
                $drawing->setCoordinates("M$signatureStartRow");
                $drawing->setOffsetX(100);
                $drawing->setOffsetY(5);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
                $sheet->getRowDimension($signatureStartRow + 2)->setRowHeight(40);
                // Label + Name
                $sheet->setCellValue("K" . ($signatureStartRow + 3), "SAFETY OFFICER SIGNATURE: " . getUserName($medicine_requisition_fdo_details->approved_by));
                $sheet->mergeCells("K" . ($signatureStartRow + 3) . ":S" . ($signatureStartRow + 3));

                $sheet->getStyle("K$signatureStartRow:S" . ($signatureStartRow + 3))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }

            $fileName = 'Medical Requisition Slip- Fdo & Security Gate.xlsx';
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
