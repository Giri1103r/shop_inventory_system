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
use App\Models\Inspection\Fire\FireStatusLog;
use App\Models\Inspection\Fire\FireFileUpload;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Fire\FireSignatureUpload;
use App\Models\Inspection\Fire\FireCheckListFollowUp;
use App\Models\Inspection\Fire\HydrantRiserInspection;
use App\Models\Inspection\Fire\HydrantRiserInspectionDetails;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class HydrantRiserInspectionContoller extends Controller
{
    private $hydrant;
    private $hydrant_checklist;
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
        $this->hydrant = new HydrantRiserInspection();
        $this->hydrant_checklist = new HydrantRiserInspectionDetails();
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
                    $data =  $this->hydrant->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->fire_hydrant_riser_id) . "' data-type = '1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->fire_hydrant_riser_id) . "' data-type = '0'>In-Active</span>";
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
                            $btn = '<a href="' . admin_url('fire/hydrant-riser-inspection/view/' . encryptId($row->fire_hydrant_riser_id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            if ($row->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/hydrant-riser-inspection/verification/' . encryptId($row->fire_hydrant_riser_id)) . '/ehs" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if (($row->inspection_status == WAITING_FOR_CAPA_ACTION || $row->inspection_status == L2_MANAGER_REJECTED || $row->inspection_status == EHS_OFFICER_REJECTED || $row->inspection_status == L1_MANAGER_REJECTED) && (CheckUserRole(ROLE_FIRE_ASSOCIATES) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/hydrant-riser-inspection/verification/' . encryptId($row->fire_hydrant_riser_id)) . '/capa" class="" title="' . __('inspection.capa_action') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_CAPA_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/hydrant-riser-inspection/verification/' . encryptId($row->fire_hydrant_riser_id)) . '/ehsVerify" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L1_VERIFICATION && (CheckUserRole(ROLE_L1_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/hydrant-riser-inspection/verification/' . encryptId($row->fire_hydrant_riser_id)) . '/level-one-manager" class="" title="' . __('inspection.l1_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L2_VERIFICATION && (CheckUserRole(ROLE_L2_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/hydrant-riser-inspection/verification/' . encryptId($row->fire_hydrant_riser_id)) . '/level-two-manager" class="" title="' . __('inspection.l2_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            $btn .= '<a href="' . admin_url('fire/hydrant-riser-inspection/exportViewPdf/' . encryptId($row->fire_hydrant_riser_id)) . '" style="margin-right: 5px;" title="PDF"> <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i> </a>';

                            $btn .= '<a href="' . admin_url('fire/hydrant-riser-inspection/generalExcel/' . encryptId($row->fire_hydrant_riser_id)) . '" style="margin-right: 5px;" title="Excel"> <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i></a>';

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
        return view('inspection.fire.hydrant_riser.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $location = $this->location->getLocationName();
            $unit = $this->unit->getUnit();
            $frequency = $this->frequency->getFrequency();
            $shifts = $this->shift->getShiftname();
            $department = $this->department->getdepartment();
            $document_no = $this->document_reference->selectUsingName('HydrantAndRiser');

            $data = array(
                'locations' => $location,
                'units' => $unit,
                'frequency' => $frequency,
                'shifts' => $shifts,
                'department' => $department,
                'document_no' => $document_no,
            );

            return view('inspection.fire.hydrant_riser.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/hydrant-riser-inspection/list'));
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
                'inspection_date' => 'required',
                'location_id' => 'required',
                'shift_id' => 'required',
                'next_due' => 'required',
                'unit_id' => 'required',
                'frequency_id' => 'required',

                'location_check_id.*' => 'required',
                'hydrant_no.*' => 'required',
                'lugs.*' => 'required',
                'rubber_washer.*' => 'required',
                'check_nut.*' => 'required',
                'spindle_wheel.*' => 'required',
                'blank_cap.*' => 'required',
                'female_coupling.*' => 'required',
                'lever.*' => 'required',
                'flow_test.*' => 'required',
                'physical_condition.*' => 'required',
                'condition_of_ivs.*' => 'required',
                'approach.*' => 'required',
                'remarks.*' => 'required',

                'observation_needed' => 'required',
                'device_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ];

            $messages = [

                'inspection_date.required' => 'Inspection Date is required.',
                'location_id.required' => 'Location is required.',
                'shift_id.required' => 'Shift is required.',
                'next_due.required' => 'Next Due Date is required.',
                'unit_id.required' => 'Unit is required.',
                'frequency_id.required' => 'Frequency is required.',

                'location_check_id.*.required' => 'Location Check is required.',
                'hydrant_no.*.required' => 'Hydrant No is required.',
                'lugs.*.required' => 'Lugs value is required.',
                'rubber_washer.*.required' => 'Rubber Washer value is required.',
                'check_nut.*.required' => 'Check Nut value is required.',
                'spindle_wheel.*.required' => 'Spindle Wheel value is required.',
                'blank_cap.*.required' => 'Blank Cap value is required.',
                'female_coupling.*.required' => 'Female Coupling value is required.',
                'lever.*.required' => 'Lever value is required.',
                'flow_test.*.required' => 'Flow Test result is required.',
                'physical_condition.*.required' => 'Physical Condition is required.',
                'condition_of_ivs.*.required' => 'Condition of IVs is required.',
                'approach.*.required' => 'Approach value is required.',
                'remarks.*.required' => 'Remarks are required.',

                'observation_needed.required' => 'Observation is required.',
                'device_image.image' => 'The uploaded file must be an image.',
                'device_image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg.',
                'device_image.max' => 'The image size must not exceed 2 MB.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            $inspection = $this->hydrant->store();
            $inspection_type = HYDRANT_RISER;
            $id = $inspection->id;

            $inspection_details = $this->hydrant_checklist->store($id);
            $inspection_file = $this->files->file_upload($inspection_type, $id);
            // $checklist_store = $this->checklist_follow->store($inspection_type, $id);
            $signature_update = $this->signature->CheckedBySignature($id, $inspection_type);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'HYDRANT AND RISER';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Hydrant and Riser Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('fire/hydrant-riser-inspection/view/' . encryptId($id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Hydrant and Riser Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('fire/hydrant-riser-inspection/verification/' . encryptId($id) . '/ehs');
                $details = array(
                    'fire_type' => 'Hydrant and Riser Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => HYDRANT_RISER,
                'inspection_id' => $id,
                'from_status' => 0,
                'to_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'created_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', 'Your data added successfully');
            // return redirect(admin_url('fire/hydrant-riser-inspection/list'));
            if ($inspection->observation_needed == 1) {
                return redirect(admin_url('fire/checklist-observation/add/' . encryptId($inspection_type) . '/' . encryptId($id)));
            } else {
                return redirect(admin_url('fire/hydrant-riser-inspection/list'));
            }
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/hydrant-riser-inspection/list'));
        }
    }

    public function View(Request $request)
    {
        // dd(11);
        try {

            $id = decryptId($request->id);
            $inspection_type = HYDRANT_RISER;
            $inspection = $this->hydrant->selectOne($id);
            $inspection_details = $this->hydrant_checklist->GetDetails($inspection->id);
            $inspection_image = $this->files->GetFile($inspection_type, $id);
            $status_log = $this->statusLog->selectOne($id, HYDRANT_RISER);
            $document_no = $this->document_reference->selectOne($inspection->document_reference_id);

            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'inspection_image' => $inspection_image,
                'status_log' => $status_log,
                'document_no' => $document_no,
            );
            return view('inspection.fire.hydrant_riser.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/hydrant-riser-inspection/list'));
        }
    }

    public function Approvals(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_type = HYDRANT_RISER;

            $inspection = $this->hydrant->selectOne($id);
            $inspection_details = $this->hydrant_checklist->GetDetails($inspection->id);
            $inspection_image = $this->files->GetFile($inspection_type, $id);
            $status_log = $this->statusLog->selectOne($id, HYDRANT_RISER);
            $document_no = $this->document_reference->selectOne($inspection->document_reference_id);


            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'inspection_image' => $inspection_image,
                'status_log' => $status_log,
                'document_no' => $document_no,
            );
            return view('inspection.fire.hydrant_riser.approve', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/hydrant-riser-inspection/list'));
        }
    }

    public function EHSOfficerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_updates = $this->hydrant->EHSOfficerUpdate($id);
            $signature_update = $this->signature->signatureUpload(HYDRANT_RISER);
            $inspection_details = $this->hydrant->selectOne($id);
            if ($request->is_passed == 1) {
                $message = 'HYDRANT AND RISER Inspeciton Approved Successfully';
                $web_link =   admin_url('fire/hydrant-riser-inspection/verification/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
            } else {
                $message = 'Inspection Recommended for the CAPA Action';
                $web_link =   admin_url('fire/hydrant-riser-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = WAITING_FOR_CAPA_ACTION;
            }
            $userIds = [
                'users' => $inspection_details->created_by,
            ];
            $mailsubject = 'HYDRANT AND RISER';
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
            // dd($email_id);
            $url = admin_url('fire/hydrant-riser-inspection/verification/' . encryptId($id) . '/capa');
            $details = array(
                'fire_type' => 'HYDRANT AND RISER',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new FireInspection($details));

            $insert_array = [
                'type' => HYDRANT_RISER,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/hydrant-riser-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went Wrong!');
            return redirect(admin_url('fire/hydrant-riser-inspection/list'));
        }
    }

    public function CAPASubmit(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $hydrant_inspection = $this->hydrant->capaSubmit($id);
            $inspection_details = $this->hydrant->selectOne($id);
            $signature_update = $this->signature->signatureUpload(HYDRANT_RISER);
            $ehsOfficers = $inspection_details->verified_by;
            $userIds = [
                'users' => $ehsOfficers,
            ];
            $mailsubject = 'HYDRANT AND RISER';
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
                'web_link' =>  admin_url('fire/hydrant-riser-inspection/verification/' . encryptId($inspection_details->id)) . '/ehsVerify',
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $user = $inspection_details->verified_by;
            $email_id = getUseremail($user);
            $url = admin_url('fire/hydrant-riser-inspection/verification/' . encryptId($id) . '/ehs');
            $details = array(
                'fire_type' => 'HYDRANT AND RISER Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => 'CAPA Action Completed by the Fire Associates',
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new FireInspection($details));

            $insert_array = [
                'type' => HYDRANT_RISER,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_ACTION,
                'to_status' => WAITING_FOR_CAPA_VERIFICATION,
                'created_by' => Auth::id(),
                'remarks' => $request->capa_remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/hydrant-riser-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/hydrant-riser-inspection/list'));
        }
    }

    public function CAPAVerifySubmit(Request $request)
    {
        // dd($request);
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->remarks;
            $safety_gallery_inspection = $this->hydrant->capaVerifySubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(HYDRANT_RISER);
            $inspection_details = $this->hydrant->selectOne($id);
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('fire/hydrant-riser-inspection/verification/' . encryptId($inspection_details->id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = WAITING_FOR_L1_VERIFICATION;
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('fire/hydrant-riser-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = [$inspection_details->created_by];
                $to_status = EHS_OFFICER_REJECTED;
            }

            $mailsubject = 'HYDRANT AND RISER';
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
                    'fire_type' => 'HYDRANT AND RISER',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => HYDRANT_RISER,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/hydrant-riser-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/hydrant-riser-inspection/list'));
        }
    }

    public function levelOneManagerSubmit(Request $request)
    {
        // dd($request->all());
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_one_manager;
            $safety_gallery_inspection = $this->hydrant->levelOneManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(HYDRANT_RISER);
            $inspection_details = $this->hydrant->selectOne($id);
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('fire/hydrant-riser-inspection/verification/' . encryptId($inspection_details->id) . '/level-two-manager');
                $user = GetLevelTwoManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by], [$inspection_details->verified_by]);
                $to_status = WAITING_FOR_L2_VERIFICATION;
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/hydrant-riser-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = L1_MANAGER_REJECTED;
            }

            $mailsubject = 'HYDRANT AND RISER';
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
                // 'assigned_user' => array_to_string($users),
                'assigned_user' => array_to_string((array)$users),

                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            foreach ($users as $user) {
                $title = $message;
                $email_id = getUseremail($user);
                $url = $web_link;
                $details = array(
                    'fire_type' => 'HYDRANT AND RISER',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => HYDRANT_RISER,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L1_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_one_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/hydrant-riser-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/hydrant-riser-inspection/list'));
        }
    }

    public function levelTwoManagerSubmit(Request $request)
    {
        // dd($request->all());
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_two_manager;
            $safety_gallery_inspection = $this->hydrant->levelTwoManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(HYDRANT_RISER);
            $inspection_details = $this->hydrant->selectOne($id);
            if ($status == 1) {
                $message = 'HYDRANT AND RISER Inspeciton Approved Successfully!';
                $web_link =   admin_url('fire/hydrant-riser-inspection/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/hydrant-riser-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = L2_MANAGER_REJECTED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);

            }

            $mailsubject = 'HYDRANT AND RISER';
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
                    'fire_type' => 'Hydrant And Riser',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => HYDRANT_RISER,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L2_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_two_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/hydrant-riser-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/hydrant-riser-inspection/list'));
        }
    }



    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->hydrant->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $row = 1;

            foreach ($allData as $groupedDetails) {

                $inspection_detail = $groupedDetails->first();
                $document_no = $this->document_reference->selectOne($inspection_detail->document_reference_id);

                $prepared_by_signature = GetFireSignature($inspection_detail->created_by, $inspection_detail->hydrant_parent_id, HYDRANT_RISER);
                $verified_by_signature = GetFireSignature($inspection_detail->hydrant_updated_by, $inspection_detail->hydrant_parent_id, HYDRANT_RISER);
                $approved_by_signature = GetFireSignature($inspection_detail->approved_by, $inspection_detail->hydrant_parent_id, HYDRANT_RISER);


                $titleRow = $row;

                $logoPath = public_path('assets/images/logo-dark.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo');
                    $drawing->setDescription('Company Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates('B' . $titleRow);
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);
                    $drawing->setHeight(60);
                    $drawing->setWorksheet($sheet);
                }

                $sheet->mergeCells("A{$titleRow}:C" . ($titleRow + 2));
                $sheet->getStyle("A{$titleRow}:C" . ($titleRow + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->mergeCells("D{$titleRow}:K" . ($titleRow + 2));
                $sheet->setCellValue("D{$titleRow}", "HYDRANT & RISER INSPECTION CHECKLIST PN INTERNATIONAL PVT. LTD.");
                $sheet->getStyle("C{$titleRow}:K" . ($titleRow + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->mergeCells("L{$titleRow}:M{$titleRow}")->setCellValue("L{$titleRow}", "Doc. No.");
                $sheet->mergeCells("L" . ($titleRow + 1) . ":M" . ($titleRow + 1))->setCellValue("L" . ($titleRow + 1), "Issue Dt.");
                $sheet->mergeCells("L" . ($titleRow + 2) . ":M" . ($titleRow + 2))->setCellValue("L" . ($titleRow + 2), "Rev. & Dt.");

                $sheet->mergeCells("N{$titleRow}:O{$titleRow}")->setCellValue("N{$titleRow}", $document_no->doc_no ?? '');
                $sheet->mergeCells("N" . ($titleRow + 1) . ":O" . ($titleRow + 1))->setCellValue("N" . ($titleRow + 1), Displaydateformat($document_no->issue_date ?? ''));
                $sheet->mergeCells("N" . ($titleRow + 2) . ":O" . ($titleRow + 2))->setCellValue("N" . ($titleRow + 2), $document_no->rev_dt ?? '');

                $sheet->getStyle("L{$titleRow}:O" . ($titleRow + 2))->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                ]);


                $headerInfoRow = $titleRow + 3;

                $sheet->mergeCells("A{$headerInfoRow}:E{$headerInfoRow}")->setCellValue("A{$headerInfoRow}", "Date of Inspection:- " . Displaydateformat($inspection_detail->date_of_inspection));
                $sheet->mergeCells("F{$headerInfoRow}:K{$headerInfoRow}")->setCellValue("F{$headerInfoRow}", "Location:- " . getLocationname($inspection_detail->location));
                $sheet->mergeCells("L{$headerInfoRow}:O{$headerInfoRow}")->setCellValue("L{$headerInfoRow}", "Shift:- " . getShift($inspection_detail->shift_id));
                $sheet->getStyle("A{$headerInfoRow}:O{$headerInfoRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $headerInfoRow++;

                $sheet->mergeCells("A{$headerInfoRow}:E{$headerInfoRow}")->setCellValue("A{$headerInfoRow}", "Next Due Date:- " . Displaydateformat($inspection_detail->next_due));
                $sheet->mergeCells("F{$headerInfoRow}:K{$headerInfoRow}")->setCellValue("F{$headerInfoRow}", "Unit:- " . getUnitname($inspection_detail->unit));
                $sheet->mergeCells("L{$headerInfoRow}:O{$headerInfoRow}")->setCellValue("L{$headerInfoRow}", "Frequency:- " . getFrequencyname($inspection_detail->frequency));
                $sheet->getStyle("A{$headerInfoRow}:O{$headerInfoRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $headerInfoRow++;

                $columnWidths = [
                    'A' => 5,
                    'B' => 15,
                    'C' => 10,
                    'D' => 10,
                    'E' => 10,
                    'F' => 10,
                    'G' => 15,
                    'H' => 10,
                    'I' => 15,
                    'J' => 15,
                    'K' => 15,
                    'L' => 15,
                    'M' => 10,
                    'N' => 10,
                    'O' => 10,
                ];

                foreach ($columnWidths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                $headerStart = $headerInfoRow;

                $sheet->mergeCells("A{$headerStart}:A" . ($headerStart + 2))->setCellValue("A{$headerStart}", "SL");
                $sheet->mergeCells("B{$headerStart}:B" . ($headerStart + 2))->setCellValue("B{$headerStart}", "LOCATION");
                $sheet->mergeCells("C{$headerStart}:C" . ($headerStart + 2))->setCellValue("C{$headerStart}", "HYDRANT NO.");

                $sheet->mergeCells("D{$headerStart}:L{$headerStart}")->setCellValue("D{$headerStart}", "CHECK ITEMS");

                $sheet->mergeCells("D" . ($headerStart + 1) . ":I" . ($headerStart + 1))->setCellValue("D" . ($headerStart + 1), "CONDITION OF LANDING VALVE");
                $sheet->setCellValue("D" . ($headerStart + 2), "LUGS");
                $sheet->setCellValue("E" . ($headerStart + 2), "RUBBER WASHER");
                $sheet->setCellValue("F" . ($headerStart + 2), "CHECK NUT");
                $sheet->setCellValue("G" . ($headerStart + 2), "SPINDLE WHEEL");
                $sheet->setCellValue("H" . ($headerStart + 2), "BLANK CAP");
                $sheet->setCellValue("I" . ($headerStart + 2), "FEMALE COUPLING");

                $sheet->mergeCells("J" . ($headerStart + 1) . ":K" . ($headerStart + 1))->setCellValue("J" . ($headerStart + 1), "CONDITION OF ISV");

                $sheet->setCellValue("J" . ($headerStart + 2), "LEVER");

                $sheet->setCellValue("K" . ($headerStart + 2), "FLOW TEST");

                $sheet->mergeCells("L" . ($headerStart + 1) . ":L" . ($headerStart + 2))->setCellValue("L" . ($headerStart + 1), "APPROACH");
                $sheet->mergeCells("M{$headerStart}:O" . ($headerStart + 2))->setCellValue("M{$headerStart}", "REMARKS");

                $sheet->getStyle("A{$headerStart}:O" . ($headerStart + 2))->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $dataRow = $headerStart + 3;
                $sr = 1;

                foreach ($groupedDetails as $detail) {
                    $sheet->setCellValue("A{$dataRow}", $sr);
                    $sheet->setCellValue("B{$dataRow}", getLocationname($detail['location_check_id'] ?? ''));
                    $sheet->setCellValue("C{$dataRow}", $detail['hydrant_no'] ?? '');
                    $sheet->setCellValue("D{$dataRow}", ($detail['lugs_id'] ?? '') === '1' ? 'Present' : 'Missing');
                    $sheet->setCellValue("E{$dataRow}", ($detail['rubber_washer'] ?? '') === '1' ? 'Intact' : 'Damaged');
                    $sheet->setCellValue("F{$dataRow}", ($detail['check_nut'] ?? '') === '1' ? 'Present' : 'Missing');
                    $sheet->setCellValue("G{$dataRow}", ($detail['spindle_wheel'] ?? '') === '1' ? 'Functional' : 'Non-Functional');
                    $sheet->setCellValue("H{$dataRow}", ($detail['blank_cap'] ?? '') === '1' ? 'Present' : 'Missing');
                    $sheet->setCellValue("I{$dataRow}", ($detail['female_coupling'] ?? '') === '1' ? 'Functional' : 'Non-Functional');
                    $sheet->setCellValue("J{$dataRow}", ($detail['lever'] ?? '') === '1' ? 'Functional' : 'Non-Functional');
                    $sheet->setCellValue("K{$dataRow}", $detail['flow_test'] ?? '');
                    $sheet->setCellValue("L{$dataRow}", $detail['approach'] ?? '');
                    $sheet->mergeCells("M{$dataRow}:O{$dataRow}")->setCellValue("M{$dataRow}", $detail['remarks'] ?? '');

                    $sheet->getStyle("A{$dataRow}:O{$dataRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sr++;
                    $dataRow++;
                }

                $signatureRowStart = $dataRow;
                $sheet->getRowDimension($signatureRowStart)->setRowHeight(80);


                // Prepared By
                $sheet->mergeCells("A{$signatureRowStart}:E{$signatureRowStart}");
                $sheet->getStyle("A{$signatureRowStart}:E{$signatureRowStart}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ],
                ]);

                if (file_exists($prepared_by_signature)) {
                    $drawing = new Drawing();
                    $drawing->setName('Prepared Signature');
                    $drawing->setDescription('Prepared By');
                    $drawing->setPath($prepared_by_signature);
                    $drawing->setCoordinates("C{$signatureRowStart}");
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);
                    $drawing->setHeight(40);
                    $drawing->setWorksheet($sheet);
                    $sheet->setCellValue("A{$signatureRowStart}", "\n\n\nPrepared By:\n" . getUsername($inspection_detail->created_by));
                } else {
                    $sheet->setCellValue("A{$signatureRowStart}", "Prepared By:\nInspection not yet started");
                }

                // Verified By
                $sheet->mergeCells("F{$signatureRowStart}:J{$signatureRowStart}");
                $sheet->getStyle("F{$signatureRowStart}:J{$signatureRowStart}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ],
                ]);

                if (file_exists($verified_by_signature)) {
                    $drawing = new Drawing();
                    $drawing->setName('Verified Signature');
                    $drawing->setDescription('Verified By');
                    $drawing->setPath($verified_by_signature);
                    $drawing->setCoordinates("H{$signatureRowStart}");
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);
                    $drawing->setHeight(40);
                    $drawing->setWorksheet($sheet);
                    $sheet->setCellValue("F{$signatureRowStart}", "\n\n\nVerified By:\n" . getUsername($inspection_detail->updated_by));
                } else {
                    $sheet->setCellValue("F{$signatureRowStart}", "Verified By:\nInspection not yet completed");
                }

                // Approved By
                $sheet->mergeCells("K{$signatureRowStart}:O{$signatureRowStart}");
                $sheet->getStyle("K{$signatureRowStart}:O{$signatureRowStart}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ],
                ]);

                if (file_exists($approved_by_signature)) {
                    $drawing = new Drawing();
                    $drawing->setName('Approved Signature');
                    $drawing->setDescription('Approved By');
                    $drawing->setPath($approved_by_signature);
                    $drawing->setCoordinates("M{$signatureRowStart}");
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);
                    $drawing->setHeight(40);
                    $drawing->setWorksheet($sheet);
                    $sheet->setCellValue("K{$signatureRowStart}", "\n\n\nApproved By:\n" . getUsername($inspection_detail->approved_by));
                } else {
                    $sheet->setCellValue("K{$signatureRowStart}", "Approved By:\nApproval pending");
                }

                $row = $signatureRowStart + 7;

                $sheet->getStyle("A{$titleRow}:O{$signatureRowStart}")->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['argb' => '000000']],
                    ],
                ]);

            }

            // Output the file as usual
            $writer = new Xlsx($spreadsheet);
            $filename = 'Hydrant_Riser_Inspection.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Export error: ' . $e->getMessage());
        }
    }


    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->hydrant->exportdata();
            $inspection_type = HYDRANT_RISER;

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }

            $data = array(
                'content' => $allData,
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

            $view = view('inspection.fire.hydrant_riser.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Hydrant And Riser Inspection.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/hydrant-riser-inspection/list'));
        }
    }

    public function ExportViewPDF(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $status_log = $this->statusLog->selectOne($id, HYDRANT_RISER);
                $hydrant_details = $this->hydrant->selectOne($id);
                $inspection = $this->hydrant_checklist->GetDetails($hydrant_details->id);
                $document_no = $this->document_reference->selectOne($hydrant_details->document_reference_id);

                $data = [
                    'status_log' => $status_log,
                    'hydrant_details' => $hydrant_details,
                    'document_no' => $document_no,
                    'pagetitle' => "Hydrant And Riser Inspection",
                    'inspection' => $inspection,
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

            $html = view('inspection.fire.hydrant_riser.viewPdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Hydrant And Riser Inspection.pdf";
            return $mpdf->Output($filename, 'i');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/hydrant-riser-inspection/list'));
        }
    }


    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $hydrant_details = $this->hydrant->selectOne($id);
            $inspection_data = $this->hydrant_checklist->GetDetails($hydrant_details->id);
            $document_no = $this->document_reference->selectOne($hydrant_details->document_reference_id);


            $prepared_by_signature = GetFireSignature($hydrant_details->created_by, $hydrant_details->id, HYDRANT_RISER);
            $verified_by_signature = GetFireSignature($hydrant_details->updated_by, $hydrant_details->id, HYDRANT_RISER);
            $approved_by_signature = GetFireSignature($hydrant_details->approved_by, $hydrant_details->id, HYDRANT_RISER);


            foreach (range('A', 'O') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            // Add logo
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

            // Header and Document Info
            $sheet->mergeCells('A1:B3');
            $sheet->mergeCells("C1:K3");
            $sheet->setCellValue("C1", "HYDRANT & RISER INSPECTION CHECKLIST PN INTERNATIONAL PVT. LTD.");
            $sheet->getStyle("C1:K3")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $sheet->getStyle('A1:B3')->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $labelMap = [
                'L1' => ['merge' => 'L1:M1', 'text' => 'Doc. No.', 'valueCell' => 'N1:O1', 'data' => $document_no->doc_no],
                'L2' => ['merge' => 'L2:M2', 'text' => 'Issue Dt.', 'valueCell' => 'N2:O2', 'data' => Displaydateformat($document_no->issue_date)],
                'L3' => ['merge' => 'L3:M3', 'text' => 'Rev. & Dt.', 'valueCell' => 'N3:O3', 'data' => $document_no->rev_dt],
            ];

            foreach ($labelMap as $labelCell => $info) {
                $sheet->mergeCells($info['merge']);
                $sheet->mergeCells($info['valueCell']);
                $sheet->setCellValue($labelCell, $info['text']);
                $sheet->setCellValue(explode(':', $info['valueCell'])[0], $info['data']);

                $sheet->getStyle($info['merge'])->applyFromArray([
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


            // Info section
            $sheet->mergeCells("A4:E4")->setCellValue("A4", "Date of Inspection:- " . Displaydateformat($hydrant_details->date_of_inspection));
            $sheet->mergeCells("F4:K4")->setCellValue("F4", "Location :- " . getLocationname($hydrant_details->location));
            $sheet->mergeCells("L4:O4")->setCellValue("L4", "Shift:- " . getShift($hydrant_details->shift_id));
            $sheet->mergeCells("A5:E5")->setCellValue("A5", "Next Due date:- " . Displaydateformat($hydrant_details->next_due));
            $sheet->mergeCells("F5:K5")->setCellValue("F5", "Unit:- " . getUnitname($hydrant_details->unit));
            $sheet->mergeCells("L5:O5")->setCellValue("L5", "Frequency:- " . getFrequencyname($hydrant_details->frequency));
            $sheet->getStyle("A4:O5")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // Table headers
            $sheet->mergeCells("A6:A8")->setCellValue("A6", "SL");
            $sheet->mergeCells("B6:B8")->setCellValue("B6", "LOCATION");
            $sheet->mergeCells("C6:C8")->setCellValue("C6", "HYDRANT NO.");
            $sheet->mergeCells("D6:K6")->setCellValue("D6", "CHECK ITEMS");
            $sheet->mergeCells("D7:I7")->setCellValue("D7", "CONDITION OF LANDING VALVE");
            $sheet->mergeCells("J7:K7")->setCellValue("J7", "CONDITION OF ISV");
            $sheet->setCellValue("D8", "LUGS");
            $sheet->setCellValue("E8", "RUBBER WASHER");
            $sheet->setCellValue("F8", "CHECK NUT");
            $sheet->setCellValue("G8", "SPINDLE WHEEL");
            $sheet->setCellValue("H8", "BLANK CAP");
            $sheet->setCellValue("I8", "FEMALE COUPLING");
            $sheet->setCellValue("J8", "LEVER");
            $sheet->setCellValue("K8", "FLOW TEST");
            $sheet->mergeCells("L6:L8")->setCellValue("L6", "APPROACH");
            $sheet->mergeCells("M6:O8")->setCellValue("M6", "REMARKS");
            $sheet->getStyle("A6:O8")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // Data rows
            $row = 9;
            $sr = 1;
            foreach ($inspection_data as $detail) {
                $sheet->setCellValue("A$row", $sr);
                $sheet->setCellValue("B$row", getLocationname($detail['location_check_id'] ?? ''));
                $sheet->setCellValue("C$row", $detail['hydrant_no'] ?? '');
                $sheet->setCellValue("D$row", ($detail['lugs_id'] ?? '') === '1' ? 'Present' : 'Missing');
                $sheet->setCellValue("E$row", ($detail['rubber_washer'] ?? '') === '1' ? 'Intact' : 'Damaged');
                $sheet->setCellValue("F$row", ($detail['check_nut'] ?? '') === '1' ? 'Present' : 'Missing');
                $sheet->setCellValue("G$row", ($detail['spindle_wheel'] ?? '') === '1' ? 'Functional' : 'Non-Functional');
                $sheet->setCellValue("H$row", ($detail['blank_cap'] ?? '') === '1' ? 'Present' : 'Missing');
                $sheet->setCellValue("I$row", ($detail['female_coupling'] ?? '') === '1' ? 'Functional' : 'Non-Functional');
                $sheet->setCellValue("J$row", ($detail['lever'] ?? '') === '1' ? 'Functional' : 'Non-Functional');
                $sheet->setCellValue("K$row", ($detail['flow_test'] ?? ''));
                $sheet->setCellValue("L$row", $detail['approach'] ?? '');
                $sheet->mergeCells("M$row:O$row")->setCellValue("M$row", $detail['remarks'] ?? '');
                $sheet->getStyle("A$row:O$row")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sr++;
                $row++;
            }

            $signatureRowStart = $row;

            $sheet->getRowDimension($signatureRowStart)->setRowHeight(100);

                    // Prepared By (with name inside the signature block)
            $sheet->mergeCells("A{$signatureRowStart}:D{$signatureRowStart}");
            $sheet->setCellValue("A{$signatureRowStart}", "Inspected and Checked By:\n" . getUsername($hydrant_details->created_by));
            $sheet->getStyle("A{$signatureRowStart}:D{$signatureRowStart}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_BOTTOM,
                    'wrapText' => true,
                ],
            ]);
            if (file_exists($prepared_by_signature)) {
                $drawing = new Drawing();
                $drawing->setName('Prepared Signature');
                $drawing->setDescription('Prepared By');
                $drawing->setPath($prepared_by_signature);
                $drawing->setCoordinates("B{$signatureRowStart}");
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
            }

            // Verified By
            $sheet->mergeCells("E{$signatureRowStart}:J{$signatureRowStart}");
            $sheet->setCellValue("E{$signatureRowStart}", "Verified By:\n" . getUsername($hydrant_details->updated_by));
            $sheet->getStyle("E{$signatureRowStart}:J{$signatureRowStart}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_BOTTOM,
                    'wrapText' => true,
                ],
            ]);
            if (file_exists($verified_by_signature)) {
                $drawing = new Drawing();
                $drawing->setName('Verified Signature');
                $drawing->setDescription('Verified By');
                $drawing->setPath($verified_by_signature);
                $drawing->setCoordinates("G{$signatureRowStart}");
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
            }

            // Approved By
            $sheet->mergeCells("K{$signatureRowStart}:O{$signatureRowStart}");
            $sheet->setCellValue("K{$signatureRowStart}", "Approved By:\n" . getUsername($hydrant_details->approved_by));
            $sheet->getStyle("K{$signatureRowStart}:O{$signatureRowStart}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_BOTTOM,
                    'wrapText' => true,
                ],
            ]);
            if (file_exists($approved_by_signature)) {
                $drawing = new Drawing();
                $drawing->setName('Approved Signature');
                $drawing->setDescription('Approved By');
                $drawing->setPath($approved_by_signature);
                $drawing->setCoordinates("M{$signatureRowStart}");
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
            }


            // Set row height for name row
            $sheet->getRowDimension($signatureRowStart + 1)->setRowHeight(30);


            // Download Excel
            $writer = new Xlsx($spreadsheet);
            $fileName = 'Hydrant & Riser Inspection Checklist.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            dd($e);
            return back()->with('error', $e->getMessage());
        }
    }
}
