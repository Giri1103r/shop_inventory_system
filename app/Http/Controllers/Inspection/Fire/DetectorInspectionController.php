<?php

namespace App\Http\Controllers\Inspection\Fire;

use Exception;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Master\Location;
use App\Models\Master\Department;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Inspection\Master\Shift;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\SimpleExcel\SimpleExcelWriter;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\Inspection\Master\Frequency;
use App\Mail\Inspection\Fire\FireInspection;
use App\Models\Inspection\Fire\DetectorType;
use App\Models\Inspection\Fire\FireStatusLog;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Models\Inspection\Fire\FireFileUpload;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Models\Inspection\InspectionStaticDocno;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Inspection\Fire\DetectorInspection;
use App\Models\Inspection\Fire\FireSignatureUpload;
use App\Models\Inspection\Fire\FireCheckListFollowUp;
use App\Models\Inspection\Fire\DetectorInspectionDetails;

class DetectorInspectionController extends Controller
{
    private $detector;
    private $detector_details;
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
    private $detector_type;

    public function __construct()
    {
        $this->detector = new DetectorInspection();
        $this->detector_details = new DetectorInspectionDetails();
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
        $this->detector_type = new DetectorType();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->detector->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->fire_detector_id) . "' data-type = '1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->fire_detector_id) . "' data-type = '0'>In-Active</span>";
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
                            return getUsername($row->checked_by);
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
                            $btn = '<a href="' . admin_url('fire/detector-inspection/view/' . encryptId($row->fire_detector_id)) . '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            if ($row->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/detector-inspection/verification/' . encryptId($row->fire_detector_id)) . '/ehs" class=" me-1" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if (($row->inspection_status == WAITING_FOR_CAPA_ACTION || $row->inspection_status == L2_MANAGER_REJECTED || $row->inspection_status == EHS_OFFICER_REJECTED || $row->inspection_status == L1_MANAGER_REJECTED) && (CheckUserRole(ROLE_FIRE_ASSOCIATES) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/detector-inspection/verification/' . encryptId($row->fire_detector_id)) . '/capa" class=" me-1" title="' . __('inspection.capa_action') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_CAPA_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/detector-inspection/verification/' . encryptId($row->fire_detector_id)) . '/ehsVerify" class=" me-1" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L1_VERIFICATION && (CheckUserRole(ROLE_L1_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/detector-inspection/verification/' . encryptId($row->fire_detector_id)) . '/level-one-manager" class=" me-1" title="' . __('inspection.l1_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L2_VERIFICATION && (CheckUserRole(ROLE_L2_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/detector-inspection/verification/' . encryptId($row->fire_detector_id)) . '/level-two-manager" class=" me-1" title="' . __('inspection.l2_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            $btn .= '<a href="' . admin_url('fire/detector-inspection/exportViewPdf/' . encryptId($row->fire_detector_id)) . '" style="margin-right: 5px;" title="PDF">
                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                    </a>';

                            $btn .= '<a href="' . admin_url('fire/detector-inspection/generalExcel/' . encryptId($row->fire_detector_id)) . '" style="margin-right: 5px;" title="Excel"> <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i></a>';

                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'inspection_status', 'date_of_inspection', 'next_due', 'location', 'shift', 'frequency'])
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
        return view('inspection.fire.detector_inspection.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $location = $this->location->getLocationName();
            $unit = $this->unit->getUnit();
            $frequency = $this->frequency->getFrequency();
            $shifts = $this->shift->getShiftname();
            $department = $this->department->getdepartment();
            $document_no = $this->document_reference->selectUsingName('DetectorInspection');
            $detector_type = $this->detector_type->getDetectorType();

            $data = array(
                'locations' => $location,
                'units' => $unit,
                'frequency' => $frequency,
                'shifts' => $shifts,
                'department' => $department,
                'document_no' => $document_no,
                'detector_types' => $detector_type,
            );

            return view('inspection.fire.detector_inspection.add', $data);
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/detector-inspection/list'));
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
                'department.*' => 'required',
                'resource_code.*' => 'required',
                'detector_type.*' => 'required',
                'physical_condition.*' => 'required',
                'cable_condition.*' => 'required',
                'response_indicator.*' => 'required',
                'working_status.*' => 'required',
                'remarks.*' => 'required',
                // 'observation' => 'required',
            ];

            $messages = [
                'issue_date.required' => 'Issue Date is required.',
                'rev_date.required' => 'Revision Date is required.',
                'inspection_date.required' => 'Inspection Date is required.',
                'location_id.required' => 'Location is required.',
                'shift_id.required' => 'Shift is required.',
                'frequency_id.required' => 'Frequency is required.',
                'next_due.required' => 'Next Due Date is required.',
                'unit_id.required' => 'Unit is required.',
                'department.*.required' => 'Department is required.',
                'resource_code.*.required' => 'Resource Code is required.',
                'detector_type.*.required' => 'Detector Type is required.',
                'physical_condition.*.required' => 'Physical Condition is required.',
                'cable_condition.*.required' => 'Cable Condition is required.',
                'response_indicator.*.required' => 'Response Indicator is required.',
                'working_status.*.required' => 'Working Status is required.',
                'remarks.*.required' => 'Remarks are required.',
                // 'observation*.required' => 'Observation is  required.',
                // 'observation_needed*.required' => 'Observation is  required.',
            ];


            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $inspection = $this->detector->store();
            $inspection_type = DETECTOR_INSPECTION;
            $id = $inspection->id;

            $inspection_details = $this->detector_details->store($id);
            $inspection_file = $this->files->file_upload($inspection_type, $id);
            // $checklist_store = $this->checklist_follow->store($inspection_type, $id);
            // $signature_update = $this->signature->CheckedBySignature($id, $inspection_type);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'Fire Detector Inspection';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Detector Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('fire/detector-inspection/view/' . encryptId($id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Detector Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('fire/detector-inspection/verification/' . encryptId($id) . '/ehs');
                $details = array(
                    'fire_type' => 'Detector Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => DETECTOR_INSPECTION,
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
                return redirect(admin_url('fire/detector-inspection/list'));
            }
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/detector-inspection/list'));
        }
    }

    public function View(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_type = DETECTOR_INSPECTION;
            $inspection = $this->detector->selectOne($id);
            $inspection_details = $this->detector_details->GetDetails($inspection->id);
            $inspection_image = $this->files->GetFile($inspection_type, $id);
            $status_log = $this->statusLog->selectOne($id, DETECTOR_INSPECTION);
            $document_no = $this->document_reference->selectOne($inspection->document_reference_id);

            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'inspection_image' => $inspection_image,
                'status_log' => $status_log,
                'document_no' => $document_no,
            );
            return view('inspection.fire.detector_inspection.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/detector-inspection/list'));
        }
    }

    public function Approvals(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_type = DETECTOR_INSPECTION;

            $inspection = $this->detector->selectOne($id);
            $inspection_details = $this->detector_details->GetDetails($inspection->id);
            $inspection_image = $this->files->GetFile($inspection_type, $id);
            $status_log = $this->statusLog->selectOne($id, DETECTOR_INSPECTION);
            $document_no = $this->document_reference->selectOne($inspection->document_reference_id);


            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'inspection_image' => $inspection_image,
                'status_log' => $status_log,
                'document_no' => $document_no,
            );
            return view('inspection.fire.detector_inspection.approve', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/detector-inspection/list'));
        }
    }

    public function EHSOfficerSubmit(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $inspection_updates = $this->detector->EHSOfficerUpdate($id);
            // $signature_update = $this->signature->signatureUpload(DETECTOR_INSPECTION);
            $inspection_details = $this->detector->selectOne($id);
            if ($request->is_passed == 1) {
                $message = 'Detector Inspeciton Approved Successfully';
                $web_link =   admin_url('fire/detector-inspection/verification/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
            } else {
                $message = 'Inspection Recommended for the CAPA Action';
                $web_link =   admin_url('fire/detector-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = WAITING_FOR_CAPA_ACTION;
            }
            $userIds = [
                'users' => $inspection_details->created_by,
            ];
            $mailsubject = 'Fire Detector Inspection';
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
            $url = admin_url('fire/detector-inspection/verification/' . encryptId($id) . '/capa');
            $details = array(
                'fire_type' => 'Detector Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new FireInspection($details));

            $insert_array = [
                'type' => DETECTOR_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/detector-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went Wrong!');
            return redirect(admin_url('fire/detector-inspection/list'));
        }
    }

    public function CAPASubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $safety_gallery_inspection = $this->detector->capaSubmit($id);
            $inspection_details = $this->detector->selectOne($id);
            // $signature_update = $this->signature->signatureUpload(DETECTOR_INSPECTION);
            $ehsOfficers = $inspection_details->verified_by;
            $userIds = [
                'users' => $ehsOfficers,
            ];
            $mailsubject = 'Fire Detector Inspection';
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
                'web_link' =>  admin_url('fire/detector-inspection/verification/' . encryptId($inspection_details->id)) . '/ehsVerify',
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $user = $inspection_details->verified_by;
            $email_id = getUseremail($user);
            $url = admin_url('fire/detector-inspection/verification/' . encryptId($id) . '/ehs');
            $details = array(
                'fire_type' => 'Fire Detector Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => 'CAPA Action Completed by the Fire Associates',
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new FireInspection($details));

            $insert_array = [
                'type' => DETECTOR_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_ACTION,
                'to_status' => WAITING_FOR_CAPA_VERIFICATION,
                'created_by' => Auth::id(),
                'remarks' => $request->capa_remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/detector-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/detector-inspection/list'));
        }
    }

    public function CAPAVerifySubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->remarks;
            $safety_gallery_inspection = $this->detector->capaVerifySubmit($id, $status, $remarks);
            // $signature_update = $this->signature->signatureUpload(DETECTOR_INSPECTION);
            $inspection_details = $this->detector->selectOne($id);
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('fire/detector-inspection/verification/' . encryptId($inspection_details->id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = WAITING_FOR_L1_VERIFICATION;
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('fire/detector-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = [$inspection_details->created_by];
                $to_status = EHS_OFFICER_REJECTED;
            }

            $mailsubject = 'Fire Detector Inspection';
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
                    'fire_type' => 'Detector Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => DETECTOR_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/detector-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/detector-inspection/list'));
        }
    }

    public function levelOneManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_one_manager;
            $safety_gallery_inspection = $this->detector->levelOneManagerSubmit($id, $status, $remarks);
            // $signature_update = $this->signature->signatureUpload(DETECTOR_INSPECTION);
            $inspection_details = $this->detector->selectOne($id);
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('fire/detector-inspection/verification/' . encryptId($inspection_details->id) . '/level-two-manager');
                $user = GetLevelTwoManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by], [$inspection_details->verified_by]);
                $to_status = WAITING_FOR_L2_VERIFICATION;
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/detector-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = [$inspection_details->created_by];
                $to_status = L1_MANAGER_REJECTED;
            }

            $mailsubject = 'Fire Detector Inspection';
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
                    'fire_type' => 'Detector Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => DETECTOR_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L1_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_one_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/detector-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/detector-inspection/list'));
        }
    }

    public function levelTwoManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_two_manager;
            $safety_gallery_inspection = $this->detector->levelTwoManagerSubmit($id, $status, $remarks);
            // $signature_update = $this->signature->signatureUpload(DETECTOR_INSPECTION);
            $inspection_details = $this->detector->selectOne($id);
            if ($status == 1) {
                $message = 'Detector Inspeciton Approved Successfully!';
                $web_link =   admin_url('fire/detector-inspection/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/detector-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = L2_MANAGER_REJECTED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
            }

            $mailsubject = 'Fire Detector Inspection';
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
                    'fire_type' => 'Detector Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => DETECTOR_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L2_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_two_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/detector-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/detector-inspection/list'));
        }
    }

    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->detector->exportdata();
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

                $sheet->mergeCells("C{$titleRow}:H" . ($titleRow + 2));
                $sheet->setCellValue("C{$titleRow}", "DETECTOR INSPECTION CHECKLIST");

                $sheet->getStyle("C{$titleRow}:H" . ($titleRow + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->mergeCells("I{$titleRow}:J{$titleRow}")->setCellValue("I{$titleRow}", "Doc. No.");
                $sheet->mergeCells("K{$titleRow}:L{$titleRow}")->setCellValue("K{$titleRow}", $document_no->doc_no ?? '');

                $sheet->mergeCells("I" . ($titleRow + 1) . ":J" . ($titleRow + 1))->setCellValue("I" . ($titleRow + 1), "Issue Dt.");
                $sheet->mergeCells("K" . ($titleRow + 1) . ":L" . ($titleRow + 1))->setCellValue("K" . ($titleRow + 1), Displaydateformat($document_no->issue_date ?? ''));

                $sheet->mergeCells("I" . ($titleRow + 2) . ":J" . ($titleRow + 2))->setCellValue("I" . ($titleRow + 2), "Rev. & Dt.");
                $sheet->mergeCells("K" . ($titleRow + 2) . ":L" . ($titleRow + 2))->setCellValue("K" . ($titleRow + 2), $document_no->rev_dt ?? '');

                $sheet->getStyle("I{$titleRow}:L" . ($titleRow + 2))->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_DOUBLE,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);



                $headerInfoRow = $titleRow + 3;

                $sheet->mergeCells("A{$headerInfoRow}:D{$headerInfoRow}")->setCellValue("A{$headerInfoRow}", "Date of Inspection:- " . Displaydateformat($inspection_detail->date_of_inspection));
                $sheet->mergeCells("E{$headerInfoRow}:H{$headerInfoRow}")->setCellValue("E{$headerInfoRow}", "Location:- " . getLocationname($inspection_detail->location));
                $sheet->mergeCells("I{$headerInfoRow}:L{$headerInfoRow}")->setCellValue("I{$headerInfoRow}", "Shift:- " . $inspection_detail->shift);
                $sheet->getStyle("A{$headerInfoRow}:L{$headerInfoRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $headerInfoRow++;

                $sheet->mergeCells("A{$headerInfoRow}:D{$headerInfoRow}")->setCellValue("A{$headerInfoRow}", "Next Due Date:- " . Displaydateformat($inspection_detail->next_due));
                $sheet->mergeCells("E{$headerInfoRow}:H{$headerInfoRow}")->setCellValue("E{$headerInfoRow}", "Unit:- " . getUnitname($inspection_detail->unit));
                $sheet->mergeCells("I{$headerInfoRow}:L{$headerInfoRow}")->setCellValue("I{$headerInfoRow}", "Frequency:- " . getFrequencyname($inspection_detail->frequency));
                $sheet->getStyle("A{$headerInfoRow}:L{$headerInfoRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $headerInfoRow++;


                $headerStart = $headerInfoRow;


                $sheet->mergeCells("A{$headerStart}:A" . ($headerStart + 1))->setCellValue("A{$headerStart}", "SR. NO");
                $sheet->mergeCells("B{$headerStart}:B" . ($headerStart + 1))->setCellValue("B{$headerStart}", "DEPARTMENT");
                $sheet->mergeCells("C{$headerStart}:C" . ($headerStart + 1))->setCellValue("C{$headerStart}", "RESOURCE CODE");
                $sheet->mergeCells("D{$headerStart}:E" . ($headerStart + 1))->setCellValue("D{$headerStart}", "TYPE OF DETECTOR");

                $sheet->mergeCells("F{$headerStart}:I{$headerStart}")->setCellValue("F{$headerStart}", "CHECK ITEMS");
                $sheet->mergeCells("J{$headerStart}:L" . ($headerStart + 1))->setCellValue("J{$headerStart}", "REMARK");

                $sheet->setCellValue("F" . ($headerStart + 1), "PHYSICAL CONDITION");
                $sheet->setCellValue("G" . ($headerStart + 1), "CABLE CONDITION");
                $sheet->setCellValue("H" . ($headerStart + 1), "RESPONSE INDICATOR");
                $sheet->setCellValue("I" . ($headerStart + 1), "WORKING STATUS");

                $sheet->getStyle("A{$headerStart}:L" . ($headerStart + 1))->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                foreach (range('A', 'L') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
                $sheet->getRowDimension($headerStart)->setRowHeight(25);
                $sheet->getRowDimension($headerStart + 1)->setRowHeight(22);


                $dataRow = $headerStart + 2;
                $sr = 1;

                foreach ($groupedDetails as $detail) {

                    $sheet->setCellValue("A{$dataRow}", $sr);
                    $sheet->setCellValue("B{$dataRow}", getDepartment($detail['department']) ?? '');
                    $sheet->setCellValue("C{$dataRow}", $detail['resource_code'] ?? '');
                    $sheet->mergeCells("D{$dataRow}:E{$dataRow}")->setCellValue("D{$dataRow}", getDetectorName($detail['detector_type']) ?? '');

                    if ($detail['physical_condition'] == 1) {
                        $sheet->setCellValue("F{$dataRow}", 'Good');
                    } elseif ($detail['physical_condition'] == 2) {
                        $sheet->setCellValue("F{$dataRow}", 'Fair');
                    } elseif ($detail['physical_condition'] == 3) {
                        $sheet->setCellValue("F{$dataRow}", 'Poor');
                    } else {
                        $sheet->setCellValue("F{$dataRow}", 'N/A');
                    }

                    if ($detail['cable_condition'] == 1) {
                        $sheet->setCellValue("G{$dataRow}", 'Good');
                    } elseif ($detail['cable_condition'] == 2) {
                        $sheet->setCellValue("G{$dataRow}", 'Fair');
                    } elseif ($detail['cable_condition'] == 3) {
                        $sheet->setCellValue("G{$dataRow}", 'Poor');
                    } else {
                        $sheet->setCellValue("G{$dataRow}", 'N/A');
                    }

                    if ($detail['response_indicator'] == 1) {
                        $sheet->setCellValue("H{$dataRow}", 'Working');
                    } elseif ($detail['response_indicator'] == 0) {
                        $sheet->setCellValue("H{$dataRow}", 'Not Working');
                    } else {
                        $sheet->setCellValue("H{$dataRow}", 'N/A');
                    }

                    if ($detail['working_status'] == 1) {
                        $sheet->setCellValue("I{$dataRow}", 'Operational');
                    } elseif ($detail['working_status'] == 0) {
                        $sheet->setCellValue("I{$dataRow}", 'Non Operational');
                    } else {
                        $sheet->setCellValue("I{$dataRow}", 'N/A');
                    }

                    $sheet->mergeCells("J{$dataRow}:L{$dataRow}")->setCellValue("j{$dataRow}", $detail['remarks'] ?? '');

                    $sheet->getStyle("A{$dataRow}:L{$dataRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $sr++;
                    $dataRow++;
                }

                $signatureRowStart = $dataRow;
                $sheet->getRowDimension($signatureRowStart)->setRowHeight(30);

                $preparedBy = getUsername($inspection_detail->created_by) ?: 'INSPECTION HAS NOT BEEN PREPARED YET';
                $verifiedBy = getUsername($inspection_detail->verified_by) ?: 'INSPECTION HAS NOT BEEN VERIFIED YET';
                $approvedBy = getUsername($inspection_detail->approved_by) ?: 'INSPECTION HAS NOT BEEN APPROVED YET';

                $sheet->mergeCells("A{$signatureRowStart}:C{$signatureRowStart}");
                $sheet->getStyle("A{$signatureRowStart}:C{$signatureRowStart}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ],
                ]);
                $sheet->setCellValue("A{$signatureRowStart}", "Prepared By:\n" . $preparedBy);

                $sheet->mergeCells("D{$signatureRowStart}:H{$signatureRowStart}");
                $sheet->getStyle("D{$signatureRowStart}:H{$signatureRowStart}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ],
                ]);
                $sheet->setCellValue("D{$signatureRowStart}", "Verified By:\n" . $verifiedBy);

                $sheet->mergeCells("I{$signatureRowStart}:L{$signatureRowStart}");
                $sheet->getStyle("I{$signatureRowStart}:L{$signatureRowStart}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ],
                ]);
                $sheet->setCellValue("I{$signatureRowStart}", "Approved By:\n" . $approvedBy);

                $row = $signatureRowStart + 6;
                $lastRow = $signatureRowStart;

                $sheet->getStyle("A{$titleRow}:L{$lastRow}")->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'Detector Inspection.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/detector-inspection/list'));
        }
    }

    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->detector->exportdata();
            $inspection_type = DETECTOR_INSPECTION;
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } else if (count($allData) > 20) {
                return redirect()->back()->with('error', __('inspection.excess_error'));
            }


            $data = array(

                'content' => $allData,
                'pagetitle' => "Detector Inspection",
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

            $view = view('inspection.fire.detector_inspection.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Detector Inspection.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/detector-inspection/list'));
        }
    }

    public function ExportViewPDF(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $status_log = $this->statusLog->selectOne($id, DETECTOR_INSPECTION);
                $forklift_details = $this->detector->selectOne($id);
                $inspection = $this->detector_details->GetDetails($forklift_details->id);
                $document_no = $this->document_reference->selectOne($forklift_details->document_reference_id);
                $approved_by = GetFireSignature($forklift_details->approved_by, $forklift_details->id, DETECTOR_INSPECTION);
                $verified_by = GetFireSignature($forklift_details->verified_by, $forklift_details->id, DETECTOR_INSPECTION);
                $checked_by = GetFireSignature($forklift_details->checked_by, $forklift_details->id, DETECTOR_INSPECTION);


                $data = [
                    'status_log' => $status_log,
                    'forklift_details' => $forklift_details,
                    'document_no' => $document_no,
                    'pagetitle' => "Detector Inspection",
                    'inspection' => $inspection,
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

            $html = view('inspection.fire.detector_inspection.viewPdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Detector Inspection.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/detector-inspection/list'));
        }
    }

    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $detector = $this->detector->find($id);
            $inspection_data = $this->detector_details->GetDetails($id);
            $document_no = $this->document_reference->selectOne($detector->document_reference_id);

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

            $sheet->mergeCells("C1:H3");
            $sheet->setCellValue("C1", "DETECTOR INSPECTION CHECKLIST");
            $sheet->getStyle("C1:H3")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $row = 1;

            $sheet->mergeCells("I{$row}:J{$row}")->setCellValue("I{$row}", "Doc. No.");
            $sheet->mergeCells("K{$row}:L{$row}")->setCellValue("K{$row}", $document_no->doc_no ?? '');

            $sheet->mergeCells("I" . ($row + 1) . ":J" . ($row + 1))->setCellValue("I" . ($row + 1), "Issue Dt.");
            $sheet->mergeCells("K" . ($row + 1) . ":L" . ($row + 1))->setCellValue("K" . ($row + 1), Displaydateformat($document_no->issue_date ?? ''));

            $sheet->mergeCells("I" . ($row + 2) . ":J" . ($row + 2))->setCellValue("I" . ($row + 2), "Rev. & Dt.");
            $sheet->mergeCells("K" . ($row + 2) . ":L" . ($row + 2))->setCellValue("K" . ($row + 2), $document_no->rev_dt ?? '');

            $sheet->getStyle("I{$row}:L" . ($row + 2))->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_DOUBLE,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ]);

            $sheet->mergeCells("A4:D4")->setCellValue("A4", "Date of Inspection:- " . Displaydateformat($detector->date_of_inspection));
            $sheet->mergeCells("E4:H4")->setCellValue("E4", "Location :- " . getLocationname($detector->location));
            $sheet->mergeCells("I4:L4")->setCellValue("I4", "Shift:- " . getShift($detector->shift));
            $sheet->mergeCells("A5:D5")->setCellValue("A5", "Next Due date:- " . Displaydateformat($detector->next_due));
            $sheet->mergeCells("E5:H5")->setCellValue("E5", "Unit:- " . getUnitname($detector->unit));
            $sheet->mergeCells("I5:L5")->setCellValue("I5", "Frequency:- " . getFrequencyname($detector->frequency));
            $sheet->getStyle("A4:L5")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells("A6:A7")->setCellValue("A6", "SR. NO");
            $sheet->mergeCells("B6:B7")->setCellValue("B6", "DEPARTMENT");
            $sheet->mergeCells("C6:C7")->setCellValue("C6", "RESOURCE CODE");
            $sheet->mergeCells("D6:E7")->setCellValue("D6", "TYPE OF DETECTOR");

            $sheet->mergeCells("F6:I6")->setCellValue("F6", "CHECK ITEMS");
            $sheet->setCellValue("F7", "PHYSICAL CONDITION");
            $sheet->setCellValue("G7", "CABLE CONDITION");
            $sheet->setCellValue("H7", "RESPONSE INDICATOR");
            $sheet->setCellValue("I7", "WORKING STATUS");

            $sheet->mergeCells("J6:L7")->setCellValue("J6", "REMARK");
            $sheet->getStyle("A6:L7")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $row = 8;
            $sr = 1;
            foreach ($inspection_data as $detail) {

                $sheet->setCellValue("A{$row}", $sr);
                $sheet->setCellValue("B{$row}", getDepartment($detail['department']) ?? '');
                $sheet->setCellValue("C{$row}", $detail['resource_code'] ?? '');
                $sheet->mergeCells("D{$row}:E{$row}")->setCellValue("D{$row}", getDetectorName($detail['detector_type']) ?? '');

                if ($detail['physical_condition'] == 1) {
                    $sheet->setCellValue("F{$row}", 'Good');
                } elseif ($detail['physical_condition'] == 2) {
                    $sheet->setCellValue("F{$row}", 'Fair');
                } elseif ($detail['physical_condition'] == 3) {
                    $sheet->setCellValue("F{$row}", 'Poor');
                } else {
                    $sheet->setCellValue("F{$row}", 'N/A');
                }

                if ($detail['cable_condition'] == 1) {
                    $sheet->setCellValue("G{$row}", 'Good');
                } elseif ($detail['cable_condition'] == 2) {
                    $sheet->setCellValue("G{$row}", 'Fair');
                } elseif ($detail['cable_condition'] == 3) {
                    $sheet->setCellValue("G{$row}", 'Poor');
                } else {
                    $sheet->setCellValue("G{$row}", 'N/A');
                }

                if ($detail['response_indicator'] == 1) {
                    $sheet->setCellValue("H{$row}", 'Working');
                } elseif ($detail['response_indicator'] == 0) {
                    $sheet->setCellValue("H{$row}", 'Not Working');
                } else {
                    $sheet->setCellValue("H{$row}", 'N/A');
                }

                if ($detail['working_status'] == 1) {
                    $sheet->setCellValue("I{$row}", 'Operational');
                } elseif ($detail['working_status'] == 0) {
                    $sheet->setCellValue("I{$row}", 'Non Operational');
                } else {
                    $sheet->setCellValue("I{$row}", 'N/A');
                }

                $sheet->mergeCells("J{$row}:L{$row}")->setCellValue("J{$row}", $detail['remarks'] ?? '');

                $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sr++;
                $row++;
            }


            $signatureRow = $row;
            $sheet->getRowDimension($signatureRow)->setRowHeight(30);

            $preparedBy = getUsername($detector->created_by) ?: 'INSPECTION HAS NOT BEEN PREPARED YET';
            $verifiedBy = getUsername($detector->updated_by) ?: 'INSPECTION HAS NOT BEEN VERIFIED YET';
            $approvedBy = getUsername($detector->approved_by) ?: 'INSPECTION HAS NOT BEEN APPROVED YET';

            $sheet->mergeCells("A{$signatureRow}:C{$signatureRow}");
            $sheet->getStyle("A{$signatureRow}:C{$signatureRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);
            $sheet->setCellValue("A{$signatureRow}", "Prepared By:\n" . $preparedBy);

            $sheet->mergeCells("D{$signatureRow}:H{$signatureRow}");
            $sheet->getStyle("D{$signatureRow}:H{$signatureRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);
            $sheet->setCellValue("D{$signatureRow}", "Verified By:\n" . $verifiedBy);

            $sheet->mergeCells("I{$signatureRow}:L{$signatureRow}");
            $sheet->getStyle("I{$signatureRow}:L{$signatureRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);
            $sheet->setCellValue("I{$signatureRow}", "Approved By:\n" . $approvedBy);


            $writer = new Xlsx($spreadsheet);
            $fileName = 'Detector Inspection Checklist.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/fire-extinguisher/cartridge/list'));
        }
    }
}
