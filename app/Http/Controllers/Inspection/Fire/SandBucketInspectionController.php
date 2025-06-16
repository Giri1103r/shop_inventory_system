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
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\Master\Frequency;
use App\Mail\Inspection\Fire\FireInspection;
use App\Models\Inspection\Fire\DetectorType;
use App\Models\Inspection\Fire\FireStatusLog;
use App\Models\Inspection\Fire\FireFileUpload;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Fire\DetectorInspection;
use App\Models\Inspection\Fire\FireSignatureUpload;
use App\Models\Inspection\Fire\FireCheckListFollowUp;
use App\Models\Inspection\Fire\DetectorInspectionDetails;
use App\Models\Inspection\Fire\SandBucketInspection;
use App\Models\Inspection\Fire\SandBucketInspectionDetails;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SandBucketInspectionController extends Controller
{

    private $detector;
    private $sandbucket_details;
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
        $this->detector = new SandBucketInspection();
        $this->sandbucket_details = new SandBucketInspectionDetails();
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
                    $data =  $this->detector->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->sand_bucket_id) . "' data-type = '1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->sand_bucket_id) . "' data-type = '0'>In-Active</span>";
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
                            $btn = '<a href="' . admin_url('fire/fire-sand-bucket-inspection/view/' . encryptId($row->sand_bucket_id)) . '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            if ($row->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-sand-bucket-inspection/verification/' . encryptId($row->sand_bucket_id)) . '/ehs" class="me-1" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if (($row->inspection_status == WAITING_FOR_CAPA_ACTION || $row->inspection_status == L2_MANAGER_REJECTED || $row->inspection_status == EHS_OFFICER_REJECTED || $row->inspection_status == L1_MANAGER_REJECTED) && (CheckUserRole(ROLE_FIRE_ASSOCIATES) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-sand-bucket-inspection/verification/' . encryptId($row->sand_bucket_id)) . '/capa" class="me-1" title="' . __('inspection.capa_action') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_CAPA_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-sand-bucket-inspection/verification/' . encryptId($row->sand_bucket_id)) . '/ehsVerify" class="me-1" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L1_VERIFICATION && (CheckUserRole(ROLE_L1_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-sand-bucket-inspection/verification/' . encryptId($row->sand_bucket_id)) . '/level-one-manager" class="me-1" title="' . __('inspection.l1_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L2_VERIFICATION && (CheckUserRole(ROLE_L2_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-sand-bucket-inspection/verification/' . encryptId($row->sand_bucket_id)) . '/level-two-manager" class="me-1" title="' . __('inspection.l2_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            $btn .= '<a href="' . admin_url('fire/fire-sand-bucket-inspection/exportViewPdf/' . encryptId($row->sand_bucket_id)) . '" style="margin-right: 5px;" title="PDF">
                                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                                    </a>';
                            $btn .= '<a href="' . admin_url('fire/fire-sand-bucket-inspection/generalExcel/' . encryptId($row->sand_bucket_id)) . '" style="margin-right: 5px;" title="Excel">
                                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                                    </a>';
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
        return view('inspection.fire.sand_bucket_inspection.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $location = $this->location->getLocationName();
            $unit = $this->unit->getUnit();
            $frequency = $this->frequency->getFrequency();
            $shifts = $this->shift->getShiftname();
            $department = $this->department->getdepartment();
            $document_no = $this->document_reference->selectUsingName('FireSandBucketInspection');

            $data = array(
                'locations' => $location,
                'units' => $unit,
                'frequency' => $frequency,
                'shifts' => $shifts,
                'department' => $department,
                'document_no' => $document_no,
            );

            return view('inspection.fire.sand_bucket_inspection.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/fire-sand-bucket-inspection/list'));
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
                'location.*' => 'required',
                'fire_sand_bucket_stand_no.*' => 'required',
                'fire_sand_bucket_no.*' => 'required',
                'condition.*' => 'required',
                'fire_bucket_condition.*' => 'required',
                'paint_condition.*' => 'required',
                'qualtiy_quantity_sand.*' => 'required',
                'approach.*' => 'required',
            ];

            $messages = [
                'issue_date.required' => 'Issue Date is required.',
                'rev_date.required' => 'Revision Date is required.',
                'inspection_date.required' => 'Inspection Date is required.',
                'location_id.required' => 'Location is required.',
                'shift_id.required' => 'Shift is required.',
                'next_due.required' => 'Next Due Date is required.',
                'unit_id.required' => 'Unit is required.',
                'frequency_id.required' => 'Frequency is required.',
                'location.*.required' => 'Department is required.',
                'fire_sand_bucket_stand_no.*.required' => 'Resource Code is required.',
                'fire_sand_bucket_no.*.required' => 'Fire Sand Bucket Number is required.',
                'condition.*.required' => 'Condition is required.',
                'fire_bucket_condition.*.required' => 'Fire Bucket Condition is required.',
                'paint_condition.*.required' => 'Paint Condition is required.',
                'qualtiy_quantity_sand.*.required' => 'Quality/Quantity of Sand is required.',
                'approach.*.required' => 'Approach is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $inspection = $this->detector->store();
            $inspection_type = SAND_BUCKET_INSPECTION;
            $id = $inspection->id;
            $inspection_details = $this->sandbucket_details->store($id);
            $inspection_file = $this->files->file_upload($inspection_type, $id);
            // $signature_update = $this->signature->CheckedBySignature($id, $inspection_type);
            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'SAND BUCKET INSPECTION';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Sand Bucket Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('fire/fire-sand-bucket-inspection/view/' . encryptId($id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);
            $title = 'Fire Associate create the Sand Bucket Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('fire/fire-sand-bucket-inspection/verification/' . encryptId($id) . '/ehs');
                $details = array(
                    'fire_type' => 'Sand Bucket Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => SAND_BUCKET_INSPECTION,
                'inspection_id' => $id,
                'from_status' => 0,
                'to_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'created_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', 'Your data added successfully');
            if (decryptId($request->observation_needed) == 1) {
                return redirect(admin_url('fire/checklist-observation/add/' . encryptId($inspection_type) . '/' . encryptId($id)));
            } else {
                return redirect(admin_url('fire/fire-sand-bucket-inspection/list'));
            }
        } catch (Exception $ex) {
            report($ex);

            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/fire-sand-bucket-inspection/list'));
        }
    }

    public function View(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_type = SAND_BUCKET_INSPECTION;
            $inspection = $this->detector->selectOne($id);
            $inspection_details = $this->sandbucket_details->GetDetails($inspection->id);
            $inspection_image = $this->files->GetFile($inspection_type, $id);
            $status_log = $this->statusLog->selectOne($id, SAND_BUCKET_INSPECTION);
            $document_no = $this->document_reference->selectOne($inspection->document_reference_id);

            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'inspection_image' => $inspection_image,
                'status_log' => $status_log,
                'document_no' => $document_no,
            );
            return view('inspection.fire.sand_bucket_inspection.view', $data);
        } catch (Exception $ex) {
            report($ex);

            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/fire-sand-bucket-inspection/list'));
        }
    }

    public function Approvals(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_type = SAND_BUCKET_INSPECTION;

            $inspection = $this->detector->selectOne($id);
            $inspection_details = $this->sandbucket_details->GetDetails($inspection->id);
            $inspection_image = $this->files->GetFile($inspection_type, $id);
            $status_log = $this->statusLog->selectOne($id, SAND_BUCKET_INSPECTION);
            $document_no = $this->document_reference->selectOne($inspection->document_reference_id);


            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'inspection_image' => $inspection_image,
                'status_log' => $status_log,
                'document_no' => $document_no,
            );
            return view('inspection.fire.sand_bucket_inspection.approve', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/fire-sand-bucket-inspection/list'));
        }
    }

    public function EHSOfficerSubmit(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $inspection_updates = $this->detector->EHSOfficerUpdate($id);
            // $signature_update = $this->signature->signatureUpload(SAND_BUCKET_INSPECTION);
            $inspection_details = $this->detector->selectOne($id);
            if ($request->is_passed == 1) {
                $message = 'Detector Inspeciton Approved Successfully';
                $web_link =   admin_url('fire/fire-sand-bucket-inspection/verification/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
            } else {
                $message = 'Inspection Recommended for the CAPA Action';
                $web_link =   admin_url('fire/fire-sand-bucket-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = WAITING_FOR_CAPA_ACTION;
            }
            $userIds = [
                'users' => $inspection_details->created_by,
            ];
            $mailsubject = 'SAND BUCKET INSPECTION';
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
            $url = admin_url('fire/fire-sand-bucket-inspection/verification/' . encryptId($id) . '/capa');
            $details = array(
                'fire_type' => 'Sand Bucket Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new FireInspection($details));

            $insert_array = [
                'type' => SAND_BUCKET_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-sand-bucket-inspection/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something Went Wrong!');
            return redirect(admin_url('fire/fire-sand-bucket-inspection/list'));
        }
    }

    public function CAPASubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $safety_gallery_inspection = $this->detector->capaSubmit($id);
            $inspection_details = $this->detector->selectOne($id);
            // $signature_update = $this->signature->signatureUpload(SAND_BUCKET_INSPECTION);
            $ehsOfficers = $inspection_details->verified_by;
            $userIds = [
                'users' => $ehsOfficers,
            ];
            $mailsubject = 'SAND BUCKET INSPECTION';
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
                'web_link' =>  admin_url('fire/fire-sand-bucket-inspection/verification/' . encryptId($inspection_details->id)) . '/ehsVerify',
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $user = $inspection_details->verified_by;
            $email_id = getUseremail($user);
            $url = admin_url('fire/fire-sand-bucket-inspection/verification/' . encryptId($id) . '/ehs');
            $details = array(
                'fire_type' => 'Safety Gallery Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => 'CAPA Action Completed by the Fire Associates',
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new FireInspection($details));

            $insert_array = [
                'type' => SAND_BUCKET_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_ACTION,
                'to_status' => WAITING_FOR_CAPA_VERIFICATION,
                'created_by' => Auth::id(),
                'remarks' => $request->capa_remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-sand-bucket-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/fire-sand-bucket-inspection/list'));
        }
    }

    public function CAPAVerifySubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->remarks;
            $safety_gallery_inspection = $this->detector->capaVerifySubmit($id, $status, $remarks);
            // $signature_update = $this->signature->signatureUpload(SAND_BUCKET_INSPECTION);
            $inspection_details = $this->detector->selectOne($id);
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('fire/fire-sand-bucket-inspection/verification/' . encryptId($inspection_details->id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = WAITING_FOR_L1_VERIFICATION;
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('fire/fire-sand-bucket-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = EHS_OFFICER_REJECTED;
            }

            $mailsubject = 'SAND BUCKET INSPECTION';
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
                    'fire_type' => 'Sand Bucket Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => SAND_BUCKET_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-sand-bucket-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/fire-sand-bucket-inspection/list'));
        }
    }

    public function levelOneManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_one_manager;
            $safety_gallery_inspection = $this->detector->levelOneManagerSubmit($id, $status, $remarks);
            // $signature_update = $this->signature->signatureUpload(SAND_BUCKET_INSPECTION);
            $inspection_details = $this->detector->selectOne($id);
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('fire/fire-sand-bucket-inspection/verification/' . encryptId($inspection_details->id) . '/level-two-manager');
                $user = GetLevelTwoManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by], [$inspection_details->verified_by]);
                $to_status = WAITING_FOR_L2_VERIFICATION;
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/fire-sand-bucket-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = L1_MANAGER_REJECTED;
            }

            $mailsubject = 'SAND BUCKET INSPECTION';
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
                    'fire_type' => 'Sand Bucket Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => SAND_BUCKET_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L1_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_one_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-sand-bucket-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/fire-sand-bucket-inspection/list'));
        }
    }

    public function levelTwoManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_two_manager;
            $safety_gallery_inspection = $this->detector->levelTwoManagerSubmit($id, $status, $remarks);
            // $signature_update = $this->signature->signatureUpload(SAND_BUCKET_INSPECTION);
            $inspection_details = $this->detector->selectOne($id);
            if ($status == 1) {
                $message = 'detector Inspeciton Approved Successfully!';
                $web_link =   admin_url('fire/fire-sand-bucket-inspection/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/fire-sand-bucket-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = L2_MANAGER_REJECTED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
            }

            $mailsubject = 'SAND BUCKET INSPECTION';
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
                    'fire_type' => 'Sand Bucket Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => SAND_BUCKET_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L2_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_two_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-sand-bucket-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/fire-sand-bucket-inspection/list'));
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

            foreach (range('A', 'K') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $row = 1;

            foreach ($allData as $detector) {
                $detector = $detector->first();
                $inspection_data = $this->sandbucket_details->GetDetails($detector->fire_id);

                $document_no = $this->document_reference->selectOne($detector->document_reference_id);

                $prepared_by_signature = GetFireSignature($detector->checked_by, $detector->fire_id, SAND_BUCKET_INSPECTION);

                $verified_by_signature = GetFireSignature($detector->verified_by, $detector->fire_id, SAND_BUCKET_INSPECTION);
                $approved_by_signature = GetFireSignature($detector->approved_by, $detector->fire_id, SAND_BUCKET_INSPECTION);

                $logoPath = public_path('assets/images/logo-dark.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo');
                    $drawing->setDescription('Company Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates("A$row");
                    $drawing->setHeight(60);
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);
                    $drawing->setWorksheet($sheet);
                }

                $titleRow = $row;

                $sheet->mergeCells("A{$row}:B" . ($row + 2));
                $sheet->mergeCells("C{$row}:I" . ($row + 2));
                $sheet->setCellValue("C{$row}", 'FIRE SAND BUCKET & STAND INSPECTION CHECKLIST');
                $sheet->getStyle("C{$row}:I" . ($row + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $sheet->getStyle("A{$row}:B" . ($row + 2))->applyFromArray([
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->setCellValue("J{$row}", 'Doc. No.');
                $sheet->setCellValue("K{$row}", $document_no->doc_no ?? '');
                $sheet->setCellValue("J" . ($row + 1), 'Issue Dt.');
                $sheet->setCellValue("K" . ($row + 1), Displaydateformat($document_no->issue_date ?? ''));
                $sheet->setCellValue("J" . ($row + 2), 'Rev. & Dt.');
                $sheet->setCellValue("K" . ($row + 2), $document_no->rev_dt ?? '');

                $sheet->getStyle("J{$row}:K" . ($row + 2))->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                ]);

                $row += 3;

                $sheet->mergeCells("A{$row}:D{$row}")->setCellValue("A{$row}", "DATE OF INSPECTION:- " . Displaydateformat($detector->date_of_inspection));
                $sheet->mergeCells("E{$row}:G{$row}")->setCellValue("E{$row}", "LOCATION :- " . $detector->location_name);
                $sheet->mergeCells("H{$row}:K{$row}")->setCellValue("H{$row}", "SHIFT:- " . $detector->shift);

                $row++;

                $sheet->mergeCells("A{$row}:D{$row}")->setCellValue("A{$row}", "NEXT DUE:- " . Displaydateformat($detector->next_due));
                $sheet->mergeCells("E{$row}:G{$row}")->setCellValue("E{$row}", "UNIT:- " . $detector->unit_name);
                $sheet->mergeCells("H{$row}:K{$row}")->setCellValue("H{$row}", "FREQUENCY:- " . $detector->frequency_name);

                $sheet->getStyle("A" . ($row - 1) . ":K{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $row++;

                $sheet->mergeCells("A{$row}:A" . ($row + 2))->setCellValue("A{$row}", 'SR. NO');
                $sheet->mergeCells("B{$row}:B" . ($row + 2))->setCellValue("B{$row}", 'LOCATION');
                $sheet->mergeCells("C{$row}:I{$row}")->setCellValue("C{$row}", 'CHECK ITEMS');
                $sheet->mergeCells("J{$row}:K" . ($row + 2))->setCellValue("J{$row}", 'REMARK');

                $row++;
                $sheet->mergeCells("C{$row}:C" . ($row + 1))->setCellValue("C{$row}", 'FIRE BUCKET STAND NO.');
                $sheet->mergeCells("D{$row}:D" . ($row + 1))->setCellValue("D{$row}", 'FIRE BUCKET NO.');
                $sheet->mergeCells("E{$row}:G{$row}")->setCellValue("E{$row}", 'CONDITION');
                $sheet->mergeCells("H{$row}:H" . ($row + 1))->setCellValue("H{$row}", 'QUALITY AND QUANTITY OF SAND');
                $sheet->mergeCells("I{$row}:I" . ($row + 1))->setCellValue("I{$row}", 'APPROACH');

                $row++;
                $sheet->setCellValue("E{$row}", 'FIRE BUCKET');
                $sheet->setCellValue("F{$row}", 'FIRE BUCKET STAND');
                $sheet->setCellValue("G{$row}", 'PAINT');

                $sheet->getStyle("A" . ($row - 2) . ":K{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $row++;

                $sr = 1;
                $statusMap = [
                    GOOD => 'GOOD',
                    FAIR => 'FAIR',
                    POOR => 'POOR',
                ];

                foreach ($inspection_data as $detail) {

                    $sheet->setCellValue("A{$row}", $sr);
                    $sheet->setCellValue("B{$row}", getLocationname($detail['location']) ?? '');
                    $sheet->setCellValue("C{$row}", $detail['fire_bucket_stand_no'] ?? '');
                    $sheet->setCellValue("D{$row}", $detail['fire_bucket_no'] ?? '');
                    $sheet->setCellValue("E{$row}", $statusMap[$detail['fire_bucket_condition']] ?? ($detail['fire_bucket_condition'] ?? ''));
                    $sheet->setCellValue("F{$row}", $statusMap[$detail['condition']] ?? ($detail['condition'] ?? ''));
                    $sheet->setCellValue("G{$row}", $statusMap[$detail['paint_condition']] ?? ($detail['paint_condition'] ?? ''));
                    $sheet->setCellValue("H{$row}", $statusMap[$detail['sand_quantity']] ?? ($detail['sand_quantity'] ?? ''));
                    $sheet->setCellValue("I{$row}", $detail['approach'] ?? '');
                    $sheet->mergeCells("J{$row}:K{$row}")->setCellValue("J{$row}", $detail['remarks'] ?? '');

                    $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $sr++;
                    $row++;
                }

                $signatureRow = $row;
                $sheet->getRowDimension($signatureRow)->setRowHeight(30);

                $sheet->mergeCells("A{$signatureRow}:D{$signatureRow}");
                $sheet->mergeCells("E{$signatureRow}:G{$signatureRow}");
                $sheet->mergeCells("H{$signatureRow}:K{$signatureRow}");

                $sheet->getStyle("A{$signatureRow}:K{$signatureRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['wrapText' => true, 'horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_BOTTOM],
                ]);

                $preparedBy = getUsername($detector->checked_by) ?: 'INSPECTION HAS NOT BEEN PREPARED YET';
                $verifiedBy = getUsername($detector->verified_by) ?: 'INSPECTION HAS NOT BEEN VERIFIED YET';
                $approvedBy = getUsername($detector->approved_by) ?: 'INSPECTION HAS NOT BEEN APPROVED YET';

                $sheet->setCellValue("A{$signatureRow}", "Prepared By:\n" . $preparedBy);
                $sheet->setCellValue("E{$signatureRow}", "Verified By:\n" . $verifiedBy);
                $sheet->setCellValue("H{$signatureRow}", "Approved By:\n" . $approvedBy);

                $row += 5;

                $lastRow = $signatureRow;

                $sheet->getStyle("A{$titleRow}:K{$lastRow}")->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Sand Bucket Inspection.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/fire-sand-bucket-inspection/list'));
        }
    }


    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->detector->exportdata();
            $inspection_type = SAND_BUCKET_INSPECTION;

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } else if (count($allData) > 20) {
                return redirect()->back()->with('error', __('inspection.excess_error'));
            }


            $data = array(
                'content' => $allData,
                'pagetitle' => "Sand Bucket Inspection",
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

            $view = view('inspection.fire.sand_bucket_inspection.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Sand Bucket Inspection.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/fire-sand-bucket-inspection/list'));
        }
    }

    public function ExportViewPDF(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $status_log = $this->statusLog->selectOne($id, SAND_BUCKET_INSPECTION);
                $forklift_details = $this->detector->selectOne($id);
                $inspection = $this->sandbucket_details->GetDetails($forklift_details->id);
                $document_no = $this->document_reference->selectOne($forklift_details->document_reference_id);
                $approved_by = GetFireSignature($forklift_details->approved_by, $forklift_details->id, SAND_BUCKET_INSPECTION);
                $verified_by = GetFireSignature($forklift_details->verified_by, $forklift_details->id, SAND_BUCKET_INSPECTION);
                $checked_by = GetFireSignature($forklift_details->checked_by, $forklift_details->id, SAND_BUCKET_INSPECTION);
                $data = [
                    'status_log' => $status_log,
                    'forklift_details' => $forklift_details,
                    'document_no' => $document_no,
                    'pagetitle' => "Sand Bucket Inspection",
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

            $html = view('inspection.fire.sand_bucket_inspection.viewPdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Sand Bucket Inspection.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/fire-sand-bucket-inspection/list'));
        }
    }


    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $detector = $this->detector->selectOne($id);
            $inspection_data = $this->sandbucket_details->GetDetails($detector->id);
            $document_no = $this->document_reference->selectOne($detector->document_reference_id);

            $prepared_by_signature = GetFireSignature($detector->created_by, $detector->id, SAND_BUCKET_INSPECTION);
            $verified_by_signature = GetFireSignature($detector->updated_by, $detector->id, SAND_BUCKET_INSPECTION);
            $approved_by_signature = GetFireSignature($detector->approved_by, $detector->id, SAND_BUCKET_INSPECTION);

            foreach (range('A', 'K') as $col) {
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
                $drawing->setHeight(60);
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setWorksheet($sheet);
            }

            $sheet->mergeCells('A1:B3');
            $sheet->mergeCells('C1:I3');
            $sheet->setCellValue('C1', 'FIRE SAND BUCKET & STAND INSPECTION CHECKLIST');
            $sheet->getStyle('C1:I3')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $sheet->getStyle('A1:B3')->applyFromArray([
                'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $labelMap = [
                'J1' => ['value' => 'Doc. No.', 'valueCell' => 'K1', 'data' => $document_no->doc_no],
                'J2' => ['value' => 'Issue Dt.', 'valueCell' => 'K2', 'data' => Displaydateformat($document_no->issue_date)],
                'J3' => ['value' => 'Rev. & Dt.', 'valueCell' => 'K3', 'data' => $document_no->rev_dt],
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

            $sheet->mergeCells('A4:D4')->setCellValue('A4', "DATE OF INSPECTION:- " . Displaydateformat($detector->date_of_inspection));
            $sheet->mergeCells('E4:G4')->setCellValue('E4', "LOCATION :- " . getLocationname($detector->location));
            $sheet->mergeCells('H4:K4')->setCellValue('H4', "SHIFT:- " . getShift($detector->shift));

            $sheet->mergeCells('A5:D5')->setCellValue('A5', "NEXT DUE:- " . Displaydateformat($detector->next_due));
            $sheet->mergeCells('E5:G5')->setCellValue('E5', "UNIT:- " . getUnitname($detector->unit));
            $sheet->mergeCells('H5:K5')->setCellValue('H5', "FREQUENCY:- " . getFrequencyname($detector->frequency));

            $sheet->getStyle('A4:K5')->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells('A6:A8')->setCellValue('A6', 'SR. NO');
            $sheet->mergeCells('B6:B8')->setCellValue('B6', 'LOCATION');
            $sheet->mergeCells('C6:I6')->setCellValue('C6', 'CHECK ITEMS');
            $sheet->mergeCells('J6:K8')->setCellValue('J6', 'REMARK');

            $sheet->mergeCells('C7:C8')->setCellValue('C7', 'FIRE BUCKET STAND NO.');
            $sheet->mergeCells('D7:D8')->setCellValue('D7', 'FIRE BUCKET NO.');
            $sheet->mergeCells('E7:G7')->setCellValue('E7', 'CONDITION');
            $sheet->mergeCells('H7:H8')->setCellValue('H7', 'QUALITY AND QUANTITY OF SAND');
            $sheet->mergeCells('I7:I8')->setCellValue('I7', 'APPROACH');

            $sheet->setCellValue('E8', 'FIRE BUCKET');
            $sheet->setCellValue('F8', 'FIRE BUCKET STAND');
            $sheet->setCellValue('G8', 'PAINT');

            $sheet->getStyle('A6:K8')->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $row = 9;
            $sr = 1;
            foreach ($inspection_data as $detail) {
                $sheet->setCellValue("A$row", $sr);
                $sheet->setCellValue("B$row", getLocationname($detail['location'] ?? ''));
                $sheet->setCellValue("C$row", $detail['fire_bucket_stand_no'] ?? '');

                $fireBucket = $detail['fire_bucket_condition'] ?? '';
                $fire_bucket_stand = $detail['condition'] ?? '';
                $paintCondition = $detail['paint_condition'] ?? '';
                $sandQuantity = $detail['sand_quantity'] ?? '';
                $approach = $detail['approach'] ?? '';
                $remark = $detail['remarks'] ?? '';

                $statusMap = [
                    GOOD => 'GOOD',
                    FAIR => 'FAIR',
                    POOR => 'POOR',
                ];

                $sheet->setCellValue("D$row", $detail['fire_bucket_no'] ?? '');
                $sheet->setCellValue("E$row", $statusMap[$fireBucket] ?? $fireBucket);
                $sheet->setCellValue("F$row", $statusMap[$fire_bucket_stand] ?? $fire_bucket_stand);
                $sheet->setCellValue("G$row", $statusMap[$paintCondition] ?? $paintCondition);
                $sheet->setCellValue("H$row", $statusMap[$sandQuantity] ?? $sandQuantity);
                $sheet->setCellValue("I$row", $approach);
                $sheet->mergeCells("J$row:K$row")->setCellValue("J$row", $remark);

                $sheet->getStyle("A$row:K$row")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sr++;
                $row++;
            }

            $signatureRow = $row;
            $sheet->getRowDimension($signatureRow)->setRowHeight(30);

            $sheet->mergeCells("A{$signatureRow}:D{$signatureRow}");
            $sheet->mergeCells("E{$signatureRow}:G{$signatureRow}");
            $sheet->mergeCells("H{$signatureRow}:K{$signatureRow}");

            $sheet->getStyle("A{$signatureRow}:K{$signatureRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'wrapText' => true,
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_BOTTOM
                ],
            ]);


            $preparedBy = getUsername($detector->created_by) ?: 'INSPECTION HAS NOT BEEN PREPARED YET';
            $verifiedBy = getUsername($detector->updated_by) ?: 'INSPECTION HAS NOT BEEN VERIFIED YET';
            $approvedBy = getUsername($detector->approved_by) ?: 'INSPECTION HAS NOT BEEN APPROVED YET';

            $sheet->setCellValue("A{$signatureRow}", "Prepared By:\n" . $preparedBy);
            $sheet->setCellValue("E{$signatureRow}", "Verified By:\n" . $verifiedBy);
            $sheet->setCellValue("H{$signatureRow}", "Approved By:\n" . $approvedBy);

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Sand Bucket Inspection.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/fire-sand-bucket-inspection/list'));
        }
    }
}
