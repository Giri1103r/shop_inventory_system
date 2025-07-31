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
use App\Models\Inspection\Fire\FireSignatureUpload;
use App\Models\Inspection\Fire\FireCheckListFollowUp;
use App\Models\Inspection\Fire\SprinklarSystemInspection;
use App\Models\Inspection\Fire\SprinklarSystemInspectionDetails;

class SprinklarSystemController extends Controller
{
    private $sprinklar_system;
    private $sprinklar_system_details;
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
        $this->sprinklar_system = new SprinklarSystemInspection();
        $this->sprinklar_system_details = new SprinklarSystemInspectionDetails();
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
                    $data =  $this->sprinklar_system->list();
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
                            $btn = '<a href="' . admin_url('fire/sprinkler-inspection/view/' . encryptId($row->inspection_id)) . '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            if ($row->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/sprinkler-inspection/verification/' . encryptId($row->inspection_id)) . '/ehs" class="me-1" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if (($row->inspection_status == WAITING_FOR_CAPA_ACTION || $row->inspection_status == L2_MANAGER_REJECTED || $row->inspection_status == EHS_OFFICER_REJECTED || $row->inspection_status == L1_MANAGER_REJECTED) && (CheckUserRole(ROLE_FIRE_ASSOCIATES) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/sprinkler-inspection/verification/' . encryptId($row->inspection_id)) . '/capa" class="me-1" title="' . __('inspection.capa_action') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_CAPA_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/sprinkler-inspection/verification/' . encryptId($row->inspection_id)) . '/ehsVerify" class="me-1" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L1_VERIFICATION && (CheckUserRole(ROLE_L1_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/sprinkler-inspection/verification/' . encryptId($row->inspection_id)) . '/level-one-manager" class="me-1" title="' . __('inspection.l1_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L2_VERIFICATION && (CheckUserRole(ROLE_L2_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/sprinkler-inspection/verification/' . encryptId($row->inspection_id)) . '/level-two-manager" class="me-1" title="' . __('inspection.l2_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            $btn .= '<a href="' . admin_url('fire/sprinkler-inspection/exportViewPdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                    </a>';
                            $btn .= '<a href="' . admin_url('fire/sprinkler-inspection/export/excel/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="Excel"> <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i></a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'inspection_status', 'next_due', 'date_of_inspection'])
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
        return view('inspection.fire.sprinklar_system.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $location = $this->location->getLocationName();
            $unit = $this->unit->getUnit();
            $frequency = $this->frequency->getFrequency();
            $shifts = $this->shift->getShiftname();
            $department = $this->department->getdepartment();
            $document_no = $this->document_reference->selectUsingName('SprinklerInspection');

            $data = array(
                'locations' => $location,
                'units' => $unit,
                'frequency' => $frequency,
                'shifts' => $shifts,
                'department' => $department,
                'document_no' => $document_no,
            );

            return view('inspection.fire.sprinklar_system.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
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
                'water_leakage.*' => 'required',
                'painting.*' => 'required',
                'exact_location.*' => 'required',
                'qbd.*' => 'required',
                'condition_of_flow_meter.*' => 'required',
                'main_isolation.*' => 'required',
                'drain_condition.*' => 'required',
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
                'water_leakage.*.required' => 'Status of Water Leakage is required',
                'painting.*.required' => 'Status Of Painting is required',
                'qbd.*.required' => 'Quality By Design Condition is required',
                'condition_of_flow_meter.*.required' => 'Condition of flow meter Status is required',
                'main_isolation.*.required' => 'Main Isolation Valve Status is required',
                'drain_condition.*.required' => 'Drain Isolation Valve Status is required',
                'exact_location.*.required' => 'Exact Location is required',
                'observation.required' => 'Observation is required',
                'remarks.*.required' => 'Remarks is required',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $inspection = $this->sprinklar_system->store();
            $inspection_type = SPRINKLAR_SYSTEM_INSPECTION;
            $id = $inspection->id;

            $inspection_details = $this->sprinklar_system_details->store($id);
            $inspection_file = $this->files->file_upload($inspection_type, $id);

            // $checklist_store = $this->checklist_follow->store($inspection_type, $id);

            $signature_update = $this->signature->CheckedBySignature($id, $inspection_type);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'Fire Sprinklar System Inspection';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Sprinklar System Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('fire/sprinkler-inspection/view/' . encryptId($id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Sprinklar System Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('fire/sprinkler-inspection/verification/' . encryptId($id) . '/ehs');
                $details = array(
                    'fire_type' => 'Sprinklar System Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => SPRINKLAR_SYSTEM_INSPECTION,
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
                return redirect(admin_url('fire/sprinkler-inspection/list'));
            }
        } catch (Exception $ex) {
            report($ex);

            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }

    public function View(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_type = SPRINKLAR_SYSTEM_INSPECTION;

            $inspection = $this->sprinklar_system->selectOne($id);
            $inspection_details = $this->sprinklar_system_details->GetDetails($inspection->id);
            $inspection_image = $this->files->GetFile($inspection_type, $id);
            $status_log = $this->statusLog->selectOne($id, SPRINKLAR_SYSTEM_INSPECTION);
            $document_no = $this->document_reference->selectOne($inspection->document_reference_id);

            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'inspection_image' => $inspection_image,
                'status_log' => $status_log,
                'document_no' => $document_no,
            );
            return view('inspection.fire.sprinklar_system.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }

    public function Approvals(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_type = SPRINKLAR_SYSTEM_INSPECTION;

            $inspection = $this->sprinklar_system->selectOne($id);
            $inspection_details = $this->sprinklar_system_details->GetDetails($inspection->id);
            $inspection_image = $this->files->GetFile($inspection_type, $id);
            $status_log = $this->statusLog->selectOne($id, SPRINKLAR_SYSTEM_INSPECTION);
            $document_no = $this->document_reference->selectOne($inspection->document_reference_id);

            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'inspection_image' => $inspection_image,
                'status_log' => $status_log,
                'document_no' => $document_no,
            );
            return view('inspection.fire.sprinklar_system.approve', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }

    public function EHSOfficerSubmit(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $inspection_updates = $this->sprinklar_system->EHSOfficerUpdate($id);
            $signature_update = $this->signature->signatureUpload(SPRINKLAR_SYSTEM_INSPECTION);
            $inspection_details = $this->sprinklar_system->selectOne($id);
            if ($request->is_passed == 1) {
                $message = 'Sprinklar System Inspection Approved Successfully';
                $web_link =   admin_url('fire/sprinkler-inspection/verification/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
            } else {
                $message = 'Inspection Recommended for the CAPA Action';
                $web_link =   admin_url('fire/sprinkler-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = WAITING_FOR_CAPA_ACTION;
            }
            $userIds = [
                'users' => $inspection_details->created_by,
            ];
            $mailsubject = 'Fire Sprinklar System Inspection';
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
            $url = admin_url('fire/sprinkler-inspection/verification/' . encryptId($id) . '/capa');
            $details = array(
                'fire_type' => 'Sprinklar System Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new FireInspection($details));

            $insert_array = [
                'type' => SPRINKLAR_SYSTEM_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went Wrong!');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }

    public function CAPASubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $sprinklar_system_inspection = $this->sprinklar_system->capaSubmit($id);
            $inspection_details = $this->sprinklar_system->selectOne($id);
            $signature_update = $this->signature->signatureUpload(SPRINKLAR_SYSTEM_INSPECTION);
            $ehsOfficers = $inspection_details->verified_by;
            $userIds = [
                'users' => $ehsOfficers,
            ];
            $mailsubject = 'Fire Sprinklar System Inspection';
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
                'web_link' =>  admin_url('fire/sprinkler-inspection/verification/' . encryptId($inspection_details->id)) . '/ehsVerify',
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $user = $inspection_details->verified_by;
            $email_id = getUseremail($user);
            $url = admin_url('fire/sprinkler-inspection/verification/' . encryptId($id) . '/ehs');
            $details = array(
                'fire_type' => 'Sprinklar System Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => 'CAPA Action Completed by the Fire Associates',
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new FireInspection($details));

            $insert_array = [
                'type' => SPRINKLAR_SYSTEM_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_ACTION,
                'to_status' => WAITING_FOR_CAPA_VERIFICATION,
                'created_by' => Auth::id(),
                'remarks' => $request->capa_remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }

    public function CAPAVerifySubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->remarks;
            $sprinklar_system_inspection = $this->sprinklar_system->capaVerifySubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(SPRINKLAR_SYSTEM_INSPECTION);
            $inspection_details = $this->sprinklar_system->selectOne($id);
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('fire/sprinkler-inspection/verification/' . encryptId($inspection_details->id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = WAITING_FOR_L1_VERIFICATION;
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('fire/sprinkler-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = [$inspection_details->created_by];
                $to_status = EHS_OFFICER_REJECTED;
            }

            $mailsubject = 'Fire Sprinklar System Inspection';
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
                    'fire_type' => 'Sprinklar System Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => SPRINKLAR_SYSTEM_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }

    public function levelOneManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_one_manager;
            $sprinklar_system_inspection = $this->sprinklar_system->levelOneManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(SPRINKLAR_SYSTEM_INSPECTION);
            $inspection_details = $this->sprinklar_system->selectOne($id);
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('fire/sprinkler-inspection/verification/' . encryptId($inspection_details->id) . '/level-two-manager');
                $user = GetLevelTwoManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by], [$inspection_details->verified_by]);
                $to_status = WAITING_FOR_L2_VERIFICATION;
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/sprinkler-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = [$inspection_details->created_by];
                $to_status = L1_MANAGER_REJECTED;
            }

            $mailsubject = 'Fire Sprinklar System Inspection';
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
                    'fire_type' => 'Sprinklar System Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => SPRINKLAR_SYSTEM_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L1_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_one_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }

    public function levelTwoManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_two_manager;
            $sprinklar_system_inspection = $this->sprinklar_system->levelTwoManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(SPRINKLAR_SYSTEM_INSPECTION);
            $inspection_details = $this->sprinklar_system->selectOne($id);
            if ($status == 1) {
                $message = 'Sprinklar System Inspection Approved Successfully!';
                $web_link =   admin_url('fire/sprinkler-inspection/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/sprinkler-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = L2_MANAGER_REJECTED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
            }

            $mailsubject = 'Fire Sprinklar System Inspection';
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
                    'fire_type' => 'Sprinklar System Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => SPRINKLAR_SYSTEM_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L2_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_two_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }

    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->sprinklar_system->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $row = 1;

            // Apply text wrap throughout the sheet
            $spreadsheet->getDefaultStyle()->getAlignment()->setWrapText(true);

            foreach ($allData as $groupedDetails) {
                $inspection_detail = $groupedDetails->first();
                $document_no = $this->document_reference->selectOne($inspection_detail->document_reference_id);

                $titleRow = $row;

                // Logo
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

                // Title
                $sheet->mergeCells("A{$titleRow}:C" . ($titleRow + 2));
                $sheet->mergeCells("D{$titleRow}:K" . ($titleRow + 2));
                $sheet->setCellValue("D{$titleRow}", "SPRINKLER SYSTEM INSPECTION CHECKLIST");

                $sheet->getStyle("A{$titleRow}:K" . ($titleRow + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Document Info: J heading, K–L value
                $sheet->setCellValue("L{$titleRow}", "Doc. No.");
                $sheet->mergeCells("M{$titleRow}:N{$titleRow}");
                $sheet->setCellValue("M{$titleRow}", $document_no->doc_no ?? '');

                $sheet->setCellValue("L" . ($titleRow + 1), "Issue Dt.");
                $sheet->mergeCells("M" . ($titleRow + 1) . ":N" . ($titleRow + 1));
                $sheet->setCellValue("M" . ($titleRow + 1), Displaydateformat($document_no->issue_date ?? ''));

                $sheet->setCellValue("L" . ($titleRow + 2), "Rev. & Dt.");
                $sheet->mergeCells("M" . ($titleRow + 2) . ":N" . ($titleRow + 2));
                $sheet->setCellValue("M" . ($titleRow + 2), $document_no->rev_dt ?? '');

                $sheet->getStyle("L{$titleRow}:N" . ($titleRow + 2))->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                ]);

                // Header info rows
                $headerInfoRow = $titleRow + 3;
                $sheet->mergeCells("A{$headerInfoRow}:D{$headerInfoRow}")->setCellValue("A{$headerInfoRow}", "Date of Inspection:- " . Displaydateformat($inspection_detail->date_of_inspection));
                $sheet->mergeCells("E{$headerInfoRow}:I{$headerInfoRow}")->setCellValue("E{$headerInfoRow}", "Location:- " . getLocationname($inspection_detail->location));
                $sheet->mergeCells("J{$headerInfoRow}:N{$headerInfoRow}")->setCellValue("J{$headerInfoRow}", "Shift:- " . $inspection_detail->shift);

                $sheet->getStyle("A{$headerInfoRow}:N{$headerInfoRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $headerInfoRow++;

                $sheet->mergeCells("A{$headerInfoRow}:D{$headerInfoRow}")->setCellValue("A{$headerInfoRow}", "Next Due Date:- " . Displaydateformat($inspection_detail->next_due));
                $sheet->mergeCells("E{$headerInfoRow}:I{$headerInfoRow}")->setCellValue("E{$headerInfoRow}", "Unit:- " . getUnitname($inspection_detail->unit));
                $sheet->mergeCells("J{$headerInfoRow}:N{$headerInfoRow}")->setCellValue("J{$headerInfoRow}", "Frequency:- " . getFrequencyname($inspection_detail->frequency));

                $sheet->getStyle("A{$headerInfoRow}:N{$headerInfoRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $headerInfoRow++;

                // Column widths
                $columnWidths = [
                    'A' => 5,
                    'B' => 15,
                    'C' => 15,
                    'D' => 10,
                    'E' => 15,
                    'F' => 12,
                    'G' => 12,
                    'H' => 20,
                    'I' => 20,
                    'J' => 20,
                    'K' => 20,
                    'L' => 12,
                    'M' => 15,
                    'N' => 15
                ];
                foreach ($columnWidths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                // Headers (with EXACT LOCATION)
                $headerStart = $headerInfoRow;
                $sheet->mergeCells("A{$headerStart}:A" . ($headerStart + 1))->setCellValue("A{$headerStart}", "SR.NO");
                $sheet->mergeCells("B{$headerStart}:B" . ($headerStart + 1))->setCellValue("B{$headerStart}", "DEPARTMENT");
                $sheet->mergeCells("C{$headerStart}:C" . ($headerStart + 1))->setCellValue("C{$headerStart}", "EXACT LOCATION");
                $sheet->mergeCells("D{$headerStart}:D" . ($headerStart + 1))->setCellValue("D{$headerStart}", "QUANTITY");
                $sheet->mergeCells("E{$headerStart}:E" . ($headerStart + 1))->setCellValue("E{$headerStart}", "RESOURCE CODE");

                $sheet->mergeCells("F{$headerStart}:K{$headerStart}")->setCellValue("F{$headerStart}", "CHECK ITEMS");
                $sheet->setCellValue("F" . ($headerStart + 1), "WATER LEAKAGE");
                $sheet->setCellValue("G" . ($headerStart + 1), "PAINTING");
                $sheet->setCellValue("H" . ($headerStart + 1), "QBD");
                $sheet->setCellValue("I" . ($headerStart + 1), "FLOW METER");
                $sheet->setCellValue("J" . ($headerStart + 1), "MAIN ISOLATION VALVE");
                $sheet->setCellValue("K" . ($headerStart + 1), "DRAIN VALVE");

                $sheet->mergeCells("L{$headerStart}:N" . ($headerStart + 1))->setCellValue("L{$headerStart}", "REMARKS");

                $sheet->getStyle("A{$headerStart}:N" . ($headerStart + 1))->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Data rows
                $dataRow = $headerStart + 2;
                $sr = 1;

                foreach ($groupedDetails as $detail) {
                    $sheet->setCellValue("A{$dataRow}", $sr);
                    $sheet->setCellValue("B{$dataRow}", GetDeptName($detail['department']) ?? '');
                    $sheet->setCellValue("C{$dataRow}", $detail['exact_location'] ?? '');
                    $sheet->setCellValue("D{$dataRow}", $detail['quantity'] ?? '');
                    $sheet->setCellValue("E{$dataRow}", $detail['resource_code'] ?? '');
                    $sheet->setCellValue("F{$dataRow}", $detail['water_leakage'] ?? '');
                    $sheet->setCellValue("G{$dataRow}", ($detail['painting'] ?? '') == OK ? 'Ok' : 'Not OK');
                    $sheet->setCellValue("H{$dataRow}", ($detail['qbd'] ?? '') == OK ? 'Ok' : 'Not OK');
                    $sheet->setCellValue("I{$dataRow}", ($detail['condition_of_flow_meter'] ?? '') == OK ? 'Ok' : 'Not OK');
                    $sheet->setCellValue("J{$dataRow}", ($detail['main_isolation'] ?? '') == OK ? 'Ok' : 'Not OK');
                    $sheet->setCellValue("K{$dataRow}", ($detail['drain_condition'] ?? '') == OK ? 'Ok' : 'Not OK');
                    $sheet->mergeCells("L{$dataRow}:N{$dataRow}");
                    $sheet->setCellValue("L{$dataRow}", $detail['remarks'] ?? '');

                    $sheet->getStyle("A{$dataRow}:N{$dataRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sr++;
                    $dataRow++;
                }

                // Signature row
                $signatureRowStart = $dataRow;
                $sheet->getRowDimension($signatureRowStart)->setRowHeight(80);

                $sheet->mergeCells("A{$signatureRowStart}:E{$signatureRowStart}")->setCellValue("A{$signatureRowStart}", "Prepared By:\n" . getUsername($inspection_detail->checked_by));
                $sheet->mergeCells("F{$signatureRowStart}:I{$signatureRowStart}")->setCellValue("F{$signatureRowStart}", "Verified By:\n" . getUsername($inspection_detail->verified_by));
                $sheet->mergeCells("J{$signatureRowStart}:N{$signatureRowStart}")->setCellValue("J{$signatureRowStart}", "Approved By:\n" . getUsername($inspection_detail->approved_by));

                $sheet->getStyle("A{$signatureRowStart}:N{$signatureRowStart}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $row = $signatureRowStart + 6;
            }

            $fileName = 'Sprinkler System Inspection.xlsx';
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $tempFile = storage_path("app/public/{$fileName}");
            $writer->save($tempFile);

            return response()->download($tempFile)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }


    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->sprinklar_system->exportdata();

            $inspection_type = SPRINKLAR_SYSTEM_INSPECTION;
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }



            $data = array(
                'inspection_type' => $inspection_type,
                'content' => $allData,
                'pagetitle' => "Sprinklar System Inspection",
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

            $view = view('inspection.fire.sprinklar_system.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Sprinklar System Inspection Inspection.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);

            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }

    public function ExportViewPDF(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $inspection_type = SPRINKLAR_SYSTEM_INSPECTION;
                $status_log = $this->statusLog->selectOne($id, SPRINKLAR_SYSTEM_INSPECTION);
                $forklift_details = $this->sprinklar_system->selectOne($id);
                $inspection = $this->sprinklar_system_details->GetDetails($forklift_details->id);
                $document_no = $this->document_reference->selectOne($forklift_details->document_reference_id);

                $approved_by = GetFireSignature($forklift_details->approved_by, $forklift_details->id, $inspection_type);
                $verified_by = GetFireSignature($forklift_details->verified_by, $forklift_details->id, $inspection_type);
                $checked_by = GetFireSignature($forklift_details->checked_by, $forklift_details->id, $inspection_type);
                $data = [
                    'status_log' => $status_log,
                    'forklift_details' => $forklift_details,
                    'pagetitle' => "Sprinklar System Inspection",
                    'inspection' => $inspection,
                    'document_no' => $document_no,
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

            $html = view('inspection.fire.sprinklar_system.viewPdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Sprinklar System Inspection.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }

    public function GeneralExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $inspection = $this->sprinklar_system->selectOne($id);
            $inspection_data = $this->sprinklar_system_details->GetDetails($inspection->id);
            $document_no = $this->document_reference->selectOne($inspection->document_reference_id);

            $prepared_by_signature = GetFireSignature($inspection->created_by, $inspection->id, SPRINKLAR_SYSTEM_INSPECTION);
            $verified_by_signature = GetFireSignature($inspection->updated_by, $inspection->id, SPRINKLAR_SYSTEM_INSPECTION);
            $approved_by_signature = GetFireSignature($inspection->approved_by, $inspection->id, SPRINKLAR_SYSTEM_INSPECTION);

            foreach (range('A', 'N') as $col) {
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
                $drawing->setCoordinates('B1');
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
            }

            $sheet->mergeCells('A1:C3');
            $sheet->mergeCells('D1:K3');
            $sheet->setCellValue('D1', 'SPRINKLAR SYSTEM INSPECTION CHECKLIST');
            $sheet->getStyle('D1:K3')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $sheet->getStyle('A1:B3')->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            // Document info
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

            // Meta info
            $sheet->mergeCells('A4:D4')->setCellValue('A4', 'Date of Inspection:- ' . Displaydateformat($inspection->date_of_inspection));
            $sheet->mergeCells('E4:I4')->setCellValue('E4', 'Location :- ' . getLocationname($inspection->location));
            $sheet->mergeCells('J4:N4')->setCellValue('J4', 'Shift:- ' . getShift($inspection->shift));
            $sheet->mergeCells('A5:D5')->setCellValue('A5', 'Next Due date:- ' . Displaydateformat($inspection->next_due));
            $sheet->mergeCells('E5:I5')->setCellValue('E5', 'Unit:- ' . getUnitname($inspection->unit));
            $sheet->mergeCells('J5:N5')->setCellValue('J5', 'Frequency:- ' . getFrequencyname($inspection->frequency));

            $sheet->getStyle('A4:N5')->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // Headers
            $sheet->mergeCells('A6:A7')->setCellValue('A6', 'SR.NO');
            $sheet->mergeCells('B6:B7')->setCellValue('B6', 'DEPARTMENT/LOCATION');
            $sheet->mergeCells('C6:C7')->setCellValue('C6', 'EXACT LOCATION');
            $sheet->mergeCells('D6:D7')->setCellValue('D6', 'QUANTITY');
            $sheet->mergeCells('E6:E7')->setCellValue('E6', 'RESOURCE CODE');
            $sheet->mergeCells('F6:K6')->setCellValue('F6', 'CHECK ITEMS');

            $sheet->setCellValue('F7', 'WATER LEAKAGE IN PIPE');
            $sheet->setCellValue('G7', 'PAINTING');
            $sheet->setCellValue('H7', 'QBD CONDITION');
            $sheet->setCellValue('I7', 'CONDITION OF FLOW METER');
            $sheet->setCellValue('J7', 'MAIN ISOLATION VALVE CONDITION');
            $sheet->setCellValue('K7', 'DRAIN VALVE CONDITION');

            $sheet->mergeCells('L6:N7')->setCellValue('L6', 'REMARKS');

            $sheet->getStyle('A6:N7')->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $row = 8;
            $sr = 1;

            foreach ($inspection_data as $detail) {
                $sheet->setCellValue("A$row", $sr);
                $sheet->setCellValue("B$row", GetDeptName($detail['department']) ?? '');
                $sheet->setCellValue("C$row", $detail['exact_location'] ?? '');
                $sheet->setCellValue("D$row", $detail['quantity'] ?? '');
                $sheet->setCellValue("E$row", $detail['resource_code'] ?? '');
                $sheet->setCellValue("F$row", $detail['water_leakage'] ?? '');
                $sheet->setCellValue("G$row", ($detail['painting'] ?? '') == OK ? 'Ok' : 'Not OK');
                $sheet->setCellValue("H$row", ($detail['qbd'] ?? '') == OK ? 'Ok' : 'Not OK');
                $sheet->setCellValue("I$row", ($detail['condition_of_flow_meter'] ?? '') == OK ? 'Ok' : 'Not OK');
                $sheet->setCellValue("J$row", ($detail['main_isolation'] ?? '') == OK ? 'Ok' : 'Not OK');
                $sheet->setCellValue("K$row", ($detail['drain_condition'] ?? '') == OK ? 'Ok' : 'Not OK');
                $sheet->mergeCells("L{$row}:N{$row}")->setCellValue("L{$row}", $detail['remarks'] ?? '');

                $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sr++;
                $row++;
            }

            // Signature Section
            $signatureRowStart = $row;
            $sheet->getRowDimension($signatureRowStart)->setRowHeight(80);

            $sheet->mergeCells("A{$signatureRowStart}:E{$signatureRowStart}")
                ->setCellValue("A{$signatureRowStart}", "Prepared By: " . getUsername($inspection->created_by));
            $sheet->mergeCells("F{$signatureRowStart}:I{$signatureRowStart}")
                ->setCellValue("F{$signatureRowStart}", $inspection->verified_by ? "Verified By: " . getUsername($inspection->updated_by) : "Verified By: Inspection not yet completed");
            $sheet->mergeCells("J{$signatureRowStart}:N{$signatureRowStart}")
                ->setCellValue("J{$signatureRowStart}", $inspection->approved_by ? "Approved By: " . getUsername($inspection->approved_by) : "Approved By: Approval pending");

            $sheet->getStyle("A{$signatureRowStart}:N{$signatureRowStart}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Sprinkler Inspection.xlsx';
            $filePath = storage_path("app/public/{$fileName}");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/sprinkler-inspection/list'));
        }
    }
}
