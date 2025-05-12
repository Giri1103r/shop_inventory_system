<?php

namespace App\Http\Controllers\Inspection\Fire;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inspection\Fire\FireStatusLog;
use App\Models\Inspection\Fire\FireFileUpload;
use App\Models\Inspection\InspectionStaticDocno;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Inspection\Master\Shift;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\Master\Frequency;
use App\Mail\Inspection\Fire\FireInspection;
use App\Models\Inspection\Fire\CoTypeFireExtinguisher;
use App\Models\Inspection\Fire\CoTypeFireExtinguisherDetails;
use App\Models\Inspection\Fire\FireCheckListFollowUp;
use App\Models\Inspection\Fire\FireExtinguisherType;
use App\Models\Inspection\Fire\FireSignatureUpload;
use App\Models\Master\Unit;
use App\Models\Master\Location;
use App\Models\Master\Department;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CoTypeFireExtinguisherController extends Controller
{
    private $co_type;
    private $co_type_details;
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
    private $fire_type;

    public function __construct()
    {
        $this->co_type = new CoTypeFireExtinguisher();
        $this->co_type_details = new CoTypeFireExtinguisherDetails();
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
        $this->fire_type = new FireExtinguisherType();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->co_type->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->fire_co_type_id) . "' data-type = '1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->fire_co_type_id) . "' data-type = '0'>In-Active</span>";
                            }
                            // }
                            return $text;
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('inspection_date', function ($row) {
                            return Displaydateformat($row->inspection_date);
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
                            $btn = '<a href="' . admin_url('fire/fire-extinguisher/co2/view/' . encryptId($row->fire_co_type_id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            if ($row->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-extinguisher/co2/verification/' . encryptId($row->fire_co_type_id)) . '/ehs" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if (($row->inspection_status == WAITING_FOR_CAPA_ACTION || $row->inspection_status == L2_MANAGER_REJECTED || $row->inspection_status == EHS_OFFICER_REJECTED || $row->inspection_status == L1_MANAGER_REJECTED) && (CheckUserRole(ROLE_FIRE_ASSOCIATES) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-extinguisher/co2/verification/' . encryptId($row->fire_co_type_id)) . '/capa" class="" title="' . __('inspection.capa_action') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_CAPA_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-extinguisher/co2/verification/' . encryptId($row->fire_co_type_id)) . '/ehsVerify" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L1_VERIFICATION && (CheckUserRole(ROLE_L1_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-extinguisher/co2/verification/' . encryptId($row->fire_co_type_id)) . '/level-one-manager" class="" title="' . __('inspection.l1_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L2_VERIFICATION && (CheckUserRole(ROLE_L2_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-extinguisher/co2/verification/' . encryptId($row->fire_co_type_id)) . '/level-two-manager" class="" title="' . __('inspection.l2_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            $btn .= '<a href="' . admin_url('fire/fire-extinguisher/co2/exportViewPdf/' . encryptId($row->fire_co_type_id)) . '" style="margin-right: 5px;" title="PDF">
                                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                                    </a>';
                            $btn .= '<a href="' . admin_url('fire/fire-extinguisher/co2/generalExcel/' . encryptId($row->fire_co_type_id)) . '" style="margin-right: 5px;" title="Excel">
                                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                                    </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'inspection_status', 'inspection_date', 'next_due', 'location', 'shift', 'frequency'])
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
        return view('inspection.fire.co_type_fire_extinguisher.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $location = $this->location->getLocationName();
            $unit = $this->unit->getUnit();
            $frequency = $this->frequency->getFrequency();
            $shifts = $this->shift->getShiftname();
            $department = $this->department->getdepartment();
            $document_no = $this->document_reference->selectUsingName('CO2TypeFireExtinguisher');
            $types = $this->fire_type->getTypes();

            $data = array(
                'locations' => $location,
                'units' => $unit,
                'frequency' => $frequency,
                'shifts' => $shifts,
                'department' => $department,
                'document_no' => $document_no,
                'types' => $types,
            );

            return view('inspection.fire.co_type_fire_extinguisher.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/fire-extinguisher/co2/list'));
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
                'fire_point_no.*' => 'required',
                'department.*' => 'required',
                'location.*' => 'required',
                'type.*' => 'required',
                'capacity.*' => 'required',
                'quantity.*' => 'required',
                'discharge_tube.*' => 'required',
                'discharge_horn.*' => 'required',
                'weight_of_co2_in_fe.*' => 'required',
                'safety_pin.*' => 'required',
                'approach.*' => 'required',
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
                'fire_point_no.*.required' => 'Fire Point No is required.',
                'department.*.required' => 'Department is required.',
                'location.*.required' => 'Location is required.',
                'type.*.required' => 'Fire Type is required.',
                'capacity.*.required' => 'capacity is required.',
                'quantity.*.required' => 'quantity is required.',
                'discharge_tube.*.required' => 'Discharge Tube is required.',
                'discharge_horn.*.required' => 'Discharge Horn is required.',
                'weight_of_co2_in_fe.*.required' => 'Weight of CO2 in FE is required.',
                'safety_pin.*.required' => 'Safety Pin is required.',
                'approach.*.required' => 'Approach is required.',
                'remarks.*.required' => 'Remarks are required.',
                // 'observation.required' => 'Observation is  required.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $inspection = $this->co_type->store();
            $inspection_type = CO_TYPE_FIRE_EXTINGUISHER_INSPECTION;
            $id = $inspection->id;

            $inspection_details = $this->co_type_details->store($id);
            $inspection_file = $this->files->file_upload($inspection_type, $id);

            // $checklist_store = $this->checklist_follow->store($inspection_type, $id);
            $signature_update = $this->signature->CheckedBySignature($id, $inspection_type);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'CO2 TYPE FIRE INSPECTION';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the CO2 Type Fire Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('fire/fire-extinguisher/co2/view/' . encryptId($id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the CO2 Type Fire Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('fire/fire-extinguisher/co2/verification/' . encryptId($id) . '/ehs');
                $details = array(
                    'fire_type' => 'CO2 Type Fire Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => CO_TYPE_FIRE_EXTINGUISHER_INSPECTION,
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
                return redirect(admin_url('fire/fire-extinguisher/co2/list'));
            }
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/fire-extinguisher/co2/list'));
        }
    }

    public function View(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_type = CO_TYPE_FIRE_EXTINGUISHER_INSPECTION;
            $inspection = $this->co_type->selectOne($id);
            $inspection_details = $this->co_type_details->GetDetails($inspection->id);
            $inspection_image = $this->files->GetFile($inspection_type, $id);

            $status_log = $this->statusLog->selectOne($id, CO_TYPE_FIRE_EXTINGUISHER_INSPECTION);
            $document_no = $this->document_reference->selectOne($inspection->document_reference_id);

            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'inspection_image' => $inspection_image,
                'status_log' => $status_log,
                'document_no' => $document_no,
            );
            return view('inspection.fire.co_type_fire_extinguisher.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/fire-extinguisher/co2/list'));
        }
    }

    public function Approvals(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_type = CO_TYPE_FIRE_EXTINGUISHER_INSPECTION;

            $inspection = $this->co_type->selectOne($id);
            $inspection_details = $this->co_type_details->GetDetails($inspection->id);
            $inspection_image = $this->files->GetFile($inspection_type, $id);
            $status_log = $this->statusLog->selectOne($id, CO_TYPE_FIRE_EXTINGUISHER_INSPECTION);
            $document_no = $this->document_reference->selectOne($inspection->document_reference_id);


            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'inspection_image' => $inspection_image,
                'status_log' => $status_log,
                'document_no' => $document_no,
            );
            return view('inspection.fire.co_type_fire_extinguisher.approve', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/fire-extinguisher/co2/list'));
        }
    }

    public function EHSOfficerSubmit(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $inspection_updates = $this->co_type->EHSOfficerUpdate($id);
            $signature_update = $this->signature->signatureUpload(CO_TYPE_FIRE_EXTINGUISHER_INSPECTION);
            $inspection_details = $this->co_type->selectOne($id);
            if ($request->is_passed == 1) {
                $message = 'CO2 Type Fire Inspeciton Approved Successfully';
                $web_link =   admin_url('fire/fire-extinguisher/co2/verification/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
            } else {
                $message = 'Inspection Recommended for the CAPA Action';
                $web_link =   admin_url('fire/fire-extinguisher/co2/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = WAITING_FOR_CAPA_ACTION;
            }
            $userIds = [
                'users' => $inspection_details->created_by,
            ];
            $mailsubject = 'CO2 TYPE FIRE INSPECTION';
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
            $url = admin_url('fire/fire-extinguisher/co2/verification/' . encryptId($id) . '/capa');
            $details = array(
                'fire_type' => 'CO2 Type Fire Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new FireInspection($details));

            $insert_array = [
                'type' => CO_TYPE_FIRE_EXTINGUISHER_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-extinguisher/co2/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went Wrong!');
            return redirect(admin_url('fire/fire-extinguisher/co2/list'));
        }
    }

    public function CAPASubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $safety_gallery_inspection = $this->co_type->capaSubmit($id);
            $inspection_details = $this->co_type->selectOne($id);
            $signature_update = $this->signature->signatureUpload(CO_TYPE_FIRE_EXTINGUISHER_INSPECTION);
            $ehsOfficers = $inspection_details->verified_by;
            $userIds = [
                'users' => $ehsOfficers,
            ];
            $mailsubject = 'CO2 TYPE FIRE INSPECTION';
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
                'web_link' =>  admin_url('fire/fire-extinguisher/co2/verification/' . encryptId($inspection_details->id)) . '/ehsVerify',
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $user = $inspection_details->verified_by;
            $email_id = getUseremail($user);
            $url = admin_url('fire/fire-extinguisher/co2/verification/' . encryptId($id) . '/ehs');
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
                'type' => CO_TYPE_FIRE_EXTINGUISHER_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_ACTION,
                'to_status' => WAITING_FOR_CAPA_VERIFICATION,
                'created_by' => Auth::id(),
                'remarks' => $request->capa_remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-extinguisher/co2/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/fire-extinguisher/co2/list'));
        }
    }

    public function CAPAVerifySubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->remarks;
            $safety_gallery_inspection = $this->co_type->capaVerifySubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(CO_TYPE_FIRE_EXTINGUISHER_INSPECTION);
            $inspection_details = $this->co_type->selectOne($id);
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('fire/fire-extinguisher/co2/verification/' . encryptId($inspection_details->id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = WAITING_FOR_L1_VERIFICATION;
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('fire/fire-extinguisher/co2/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = EHS_OFFICER_REJECTED;
            }

            $mailsubject = 'CO2 TYPE FIRE INSPECTION';
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
                    'fire_type' => 'CO2 Type Fire Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => CO_TYPE_FIRE_EXTINGUISHER_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-extinguisher/co2/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/fire-extinguisher/co2/list'));
        }
    }

    public function levelOneManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_one_manager;
            $safety_gallery_inspection = $this->co_type->levelOneManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(CO_TYPE_FIRE_EXTINGUISHER_INSPECTION);
            $inspection_details = $this->co_type->selectOne($id);
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('fire/fire-extinguisher/co2/verification/' . encryptId($inspection_details->id) . '/level-two-manager');
                $user = GetLevelTwoManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by], [$inspection_details->verified_by]);
                $to_status = WAITING_FOR_L2_VERIFICATION;
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/fire-extinguisher/co2/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = L1_MANAGER_REJECTED;
            }

            $mailsubject = 'CO2 TYPE FIRE INSPECTION';
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
                    'fire_type' => 'CO2 Type Fire Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => CO_TYPE_FIRE_EXTINGUISHER_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L1_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_one_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-extinguisher/co2/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/fire-extinguisher/co2/list'));
        }
    }

    public function levelTwoManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_two_manager;
            $safety_gallery_inspection = $this->co_type->levelTwoManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(CO_TYPE_FIRE_EXTINGUISHER_INSPECTION);
            $inspection_details = $this->co_type->selectOne($id);
            if ($status == 1) {
                $message = 'CO2 Type Fire Inspeciton Approved Successfully!';
                $web_link =   admin_url('fire/fire-extinguisher/co2/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/fire-extinguisher/co2/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = L2_MANAGER_REJECTED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
            }

            $mailsubject = 'CO2 TYPE FIRE INSPECTION';
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
                    'fire_type' => 'CO2 Type Fire Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => CO_TYPE_FIRE_EXTINGUISHER_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L2_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_two_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-extinguisher/co2/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/fire-extinguisher/co2/list'));
        }
    }

    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->co_type->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $row = 1;

            foreach ($allData as $detail) {

                    $inspection_detail = $detail->first();
                    $tableStartRow = $row;
                    $document_no = $this->document_reference->selectOne($inspection_detail->document_reference_id);

                    $prepared_by_signature = GetFireSignature($inspection_detail->checked_by, $inspection_detail->fire_id, CO_TYPE_FIRE_EXTINGUISHER_INSPECTION);

                    $verified_by_signature = GetFireSignature($inspection_detail->updated_by, $inspection_detail->fire_id, CO_TYPE_FIRE_EXTINGUISHER_INSPECTION);
                    $approved_by_signature = GetFireSignature($inspection_detail->approved_by, $inspection_detail->fire_id, CO_TYPE_FIRE_EXTINGUISHER_INSPECTION);

                    foreach (range('A', 'O') as $col) {
                        $sheet->getColumnDimension($col)->setAutoSize(true);
                    }

                    $logoPath = public_path('assets/images/logo-dark.png');
                    if (file_exists($logoPath)) {
                        $drawing = new Drawing();
                        $drawing->setName('Logo');
                        $drawing->setDescription('Company Logo');
                        $drawing->setPath($logoPath);
                        $drawing->setCoordinates("A$row");
                        $drawing->setOffsetX(5);
                        $drawing->setOffsetY(5);
                        $drawing->setHeight(60);
                        $drawing->setWorksheet($sheet);
                    }

                    $sheet->mergeCells("A{$row}:B" . ($row + 2));
                    $sheet->mergeCells("C{$row}:M" . ($row + 2));
                    $sheet->setCellValue("C{$row}", "FIRE EXTINGUISHER INSPECTION CHECKLIST (CO2 TYPE)");
                    $sheet->getStyle("C{$row}:M" . ($row + 2))->applyFromArray([
                        'font' => ['bold' => true, 'size' => 14],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    $sheet->getStyle("A{$row}:B" . ($row + 2))->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);

                    $labelMap = [
                        "N{$row}" => ['value' => 'Doc. No.', 'valueCell' => "O{$row}", 'data' => $document_no->doc_no],
                        "N" . ($row + 1) => ['value' => 'Issue Dt.', 'valueCell' => "O" . ($row + 1), 'data' => Displaydateformat($document_no->issue_date)],
                        "N" . ($row + 2) => ['value' => 'Rev. & Dt.', 'valueCell' => "O" . ($row + 2), 'data' => $document_no->rev_dt],
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

                    $row += 3;



                    $sheet->mergeCells("A{$row}:E{$row}")->setCellValue("A{$row}", "Date of Inspection:- " . Displaydateformat($inspection_detail->inspection_date));
                    $sheet->mergeCells("F{$row}:L{$row}")->setCellValue("F{$row}", "Location :- " . getLocationname($inspection_detail->location));
                    $sheet->mergeCells("M{$row}:O{$row}")->setCellValue("M{$row}", "Shift:- " . $inspection_detail->shift);

                    $row++;
                    $sheet->mergeCells("A{$row}:E{$row}")->setCellValue("A{$row}", "Next Due date:- " . Displaydateformat($inspection_detail->next_due));
                    $sheet->mergeCells("F{$row}:L{$row}")->setCellValue("F{$row}", "Unit:- " . getUnitname($inspection_detail->unit));
                    $sheet->mergeCells("M{$row}:O{$row}")->setCellValue("M{$row}", "Frequency:- " . getFrequencyname($inspection_detail->frequency));

                    $sheet->getStyle("A" . ($row - 1) . ":O{$row}")->applyFromArray([
                        'font' => ['bold' => true],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $row++;

                    $sheet->mergeCells("A{$row}:A" . ($row + 2))->setCellValue("A{$row}", "SR. NO");
                    $sheet->mergeCells("B{$row}:B" . ($row + 2))->setCellValue("B{$row}", "FIRE POINT NO.");
                    $sheet->mergeCells("C{$row}:D" . ($row + 2))->setCellValue("C{$row}", "DEPARTMENT");
                    $sheet->mergeCells("E{$row}:F" . ($row + 2))->setCellValue("E{$row}", "LOCATION");
                    $sheet->mergeCells("G{$row}:N{$row}")->setCellValue("G{$row}", "CHECK ITEMS");
                    $sheet->mergeCells("O{$row}:O" . ($row + 2))->setCellValue("O{$row}", "REMARK");

                    $row++;

                    $sheet->mergeCells("G{$row}:I{$row}")->setCellValue("G{$row}", "DESCRIPTION");
                    $sheet->mergeCells("J{$row}:N{$row}")->setCellValue("J{$row}", "CONDITION");

                    $row++;

                    $sheet->setCellValue("G{$row}", "TYPE");
                    $sheet->setCellValue("H{$row}", "CAPACITY");
                    $sheet->setCellValue("I{$row}", "QUANTITY");
                    $sheet->setCellValue("J{$row}", "DISCHARGE TUBE");
                    $sheet->setCellValue("K{$row}", "DISCHARGE HORN");
                    $sheet->setCellValue("L{$row}", "WEIGHT OF CO2 IN FE");
                    $sheet->setCellValue("M{$row}", "SAFETY PIN");
                    $sheet->setCellValue("N{$row}", "APPROACH");

                    $sheet->getStyle("A" . ($row - 2) . ":O{$row}")->applyFromArray([
                        'font' => ['bold' => true],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $row++;

                    $sr = 1;
                    foreach ($detail as $detail) {

                        $sheet->setCellValue("A{$row}", $sr++);
                        $sheet->setCellValue("B{$row}", $detail['fire_point_no']);
                        $sheet->mergeCells("C{$row}:D{$row}")->setCellValue("C{$row}", getDepartment($detail['department']));
                        $sheet->mergeCells("E{$row}:F{$row}")->setCellValue("E{$row}", getLocationname($detail['location']));
                        $sheet->setCellValue("G{$row}", getExtinguisherTypeName($detail['extinguisher_type']));
                        $sheet->setCellValue("H{$row}", $detail['capacity']);
                        $sheet->setCellValue("I{$row}", $detail['quantity']);

                        $dischargeTubeStatus = $detail['discharge_tube'] ?? '';
                        if ($dischargeTubeStatus == FUNCTIONAL) {
                            $sheet->setCellValue("J{$row}", __('inspection.functional'));
                        } elseif ($dischargeTubeStatus == NON_FUNCTIONAL) {
                            $sheet->setCellValue("J{$row}", __('inspection.non_functional'));
                        } else {
                            $sheet->setCellValue("J{$row}", '');
                        }

                        $dischargeHornStatus = $detail['discharge_horn'] ?? '';
                        if ($dischargeHornStatus == FUNCTIONAL) {
                            $sheet->setCellValue("K{$row}", __('inspection.functional'));
                        } elseif ($dischargeHornStatus == NON_FUNCTIONAL) {
                            $sheet->setCellValue("K{$row}", __('inspection.non_functional'));
                        } else {
                            $sheet->setCellValue("K{$row}", '');
                        }

                        $sheet->setCellValue("L{$row}", $detail['weight_of_co2_in_fe'] ?? '');

                        $safetyPinStatus = $detail['safety_pin'] ?? '';
                        if ($safetyPinStatus == PRESENT) {
                            $sheet->setCellValue("M{$row}", __('inspection.present'));
                        } elseif ($safetyPinStatus == MISSING) {
                            $sheet->setCellValue("M{$row}", __('inspection.missing'));
                        } else {
                            $sheet->setCellValue("M{$row}", '');
                        }

                        $sheet->setCellValue("N{$row}", $detail['approach'] ?? '');
                        $sheet->setCellValue("O{$row}", $detail['remarks'] ?? '');

                        $sheet->getStyle("A{$row}:O{$row}")->applyFromArray([
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        ]);

                        $row++;
                    }


                $signatureRow = $row;
                $sheet->getRowDimension($signatureRow)->setRowHeight(80);

                $sheet->mergeCells("A{$signatureRow}:E{$signatureRow}");
                $sheet->mergeCells("F{$signatureRow}:K{$signatureRow}");
                $sheet->mergeCells("L{$signatureRow}:O{$signatureRow}");

                $sheet->getStyle("A{$signatureRow}:O{$signatureRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);

                if (file_exists($prepared_by_signature)) {
                    $drawing = new Drawing();
                    $drawing->setName('Prepared Signature');
                    $drawing->setPath($prepared_by_signature);
                    $drawing->setCoordinates("C{$signatureRow}");
                    $drawing->setHeight(40);
                    $drawing->setOffsetY(5);
                    $drawing->setWorksheet($sheet);
                    $sheet->setCellValue("A{$signatureRow}", "\n\n\nPrepared By:\n" . getUsername($detail->checked_by));
                } else {
                    $sheet->setCellValue("A{$signatureRow}", "Prepared By:\nInspection not yet started");
                }

                if (file_exists($verified_by_signature)) {
                    $drawing = new Drawing();
                    $drawing->setName('Verified Signature');
                    $drawing->setPath($verified_by_signature);
                    $drawing->setCoordinates("I{$signatureRow}");
                    $drawing->setHeight(40);
                    $drawing->setOffsetY(5);
                    $drawing->setWorksheet($sheet);
                    $sheet->setCellValue("F{$signatureRow}", "\n\n\nVerified By:\n" . getUsername($detail->updated_by));
                } else {
                    $sheet->setCellValue("F{$signatureRow}", "Verified By:\nInspection not yet completed");
                }

                if (file_exists($approved_by_signature)) {
                    $drawing = new Drawing();
                    $drawing->setName('Approved Signature');
                    $drawing->setPath($approved_by_signature);
                    $drawing->setCoordinates("M{$signatureRow}");
                    $drawing->setHeight(40);
                    $drawing->setOffsetY(5);
                    $drawing->setWorksheet($sheet);
                    $sheet->setCellValue("L{$signatureRow}", "\n\n\nApproved By:\n" . getUsername($detail->approved_by));
                } else {
                    $sheet->setCellValue("L{$signatureRow}", "Approved By:\nApproval pending");
                }

                $row += 5;

                $sheet->getStyle("A{$tableStartRow}:O{$signatureRow}")->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'CO2 Type Fire Extinguisher.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/fire-extinguisher/co2/list'));
        }
    }


    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->co_type->exportdata();
            $inspection_type = CO_TYPE_FIRE_EXTINGUISHER_INSPECTION;
            $document_no = $this->document_reference->selectUsingName('CO2TypeFireExtinguisher');

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error', __('inspection.excess_error'));
            }


            $data = array(
                'content' => $allData,
                'document_no' => $document_no,
                'inspection_type' => $inspection_type,
                'pagetitle' => "CO2 Type Fire Inspection",
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

            $view = view('inspection.fire.co_type_fire_extinguisher.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "CO2 Type Fire Inspection.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/fire-extinguisher/co2/list'));
        }
    }

    public function ExportViewPDF(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $status_log = $this->statusLog->selectOne($id, CO_TYPE_FIRE_EXTINGUISHER_INSPECTION);
                $forklift_details = $this->co_type->selectOne($id);
                $inspection = $this->co_type_details->GetDetails($forklift_details->id);
                $document_no = $this->document_reference->selectOne($forklift_details->document_reference_id);

                $data = [
                    'status_log' => $status_log,
                    'forklift_details' => $forklift_details,
                    'document_no' => $document_no,
                    'pagetitle' => "CO2 Type Fire Inspection",
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

            $html = view('inspection.fire.co_type_fire_extinguisher.viewPdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "CO2 Type Fire Inspection.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/fire-extinguisher/co2/list'));
        }
    }


    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $co_type = $this->co_type->find($id);


            $inspection_data = $this->co_type_details->GetDetails($co_type->id);
            $document_no = $this->document_reference->selectOne($co_type->document_reference_id);

            $prepared_by_signature = GetFireSignature($co_type->created_by, $co_type->id, CO_TYPE_FIRE_EXTINGUISHER_INSPECTION);
            $verified_by_signature = GetFireSignature($co_type->updated_by, $co_type->id, CO_TYPE_FIRE_EXTINGUISHER_INSPECTION);
            $approved_by_signature = GetFireSignature($co_type->approved_by, $co_type->id, CO_TYPE_FIRE_EXTINGUISHER_INSPECTION);

            foreach (range('A', 'O') as $col) {
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
            $sheet->mergeCells("C1:M3");
            $sheet->setCellValue("C1", "FIRE EXTINGUISHER INSPECTION CHECKLIST (CO2 TYPE)");
            $sheet->getStyle("C1:M3")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $sheet->getStyle('A1:B3')->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $labelMap = [
                'N1' => ['value' => 'Doc. No.', 'valueCell' => 'O1', 'data' => $document_no->doc_no],
                'N2' => ['value' => 'Issue Dt.', 'valueCell' => 'O2', 'data' => Displaydateformat($document_no->issue_date)],
                'N3' => ['value' => 'Rev. & Dt.', 'valueCell' => 'O3', 'data' => $document_no->rev_dt],
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

            $sheet->mergeCells("A4:E4")->setCellValue("A4", "Date of Inspection:- " . Displaydateformat($co_type->inspection_date));
            $sheet->mergeCells("F4:L4")->setCellValue("F4", "Location :- " . getLocationname($co_type->location));
            $sheet->mergeCells("M4:O4")->setCellValue("M4", "Shift:- " . getShift($co_type->shift));
            $sheet->mergeCells("A5:E5")->setCellValue("A5", "Next Due date:- " . Displaydateformat($co_type->next_due));
            $sheet->mergeCells("F5:L5")->setCellValue("F5", "Unit:- " . getUnitname($co_type->unit));
            $sheet->mergeCells("M5:O5")->setCellValue("M5", "Frequency:- " . getFrequencyname($co_type->frequency));
            $sheet->getStyle("A4:O5")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells("A6:A8")->setCellValue("A6", "SR. NO");
            $sheet->mergeCells("B6:B8")->setCellValue("B6", "FIRE POINT NO.");
            $sheet->mergeCells("C6:D8")->setCellValue("C6", "DEPARTMENT");
            $sheet->mergeCells("E6:F8")->setCellValue("E6", "LOCATION");

            $sheet->mergeCells("G6:N6")->setCellValue("G6", "CHECK ITEMS");

            $sheet->mergeCells("G7:I7")->setCellValue("G7", "DESCRIPTION");
            $sheet->mergeCells("J7:N7")->setCellValue("J7", "CONDITION");

            $sheet->setCellValue("G8", "TYPE");
            $sheet->setCellValue("H8", "CAPACITY");
            $sheet->setCellValue("I8", "QUANTITY");
            $sheet->setCellValue("J8", "DISCHARGE TUBE");
            $sheet->setCellValue("K8", "DISCHARGE HORN");
            $sheet->setCellValue("L8", "WEIGHT OF CO2 IN FE");
            $sheet->setCellValue("M8", "SAFETY PIN");
            $sheet->setCellValue("N8", "APPROACH");

            $sheet->mergeCells("O6:O8")->setCellValue("O6", "REMARK");


            $sheet->getStyle("A6:O8")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $row = 9;
            $sr = 1;
            foreach ($inspection_data as $detail) {

                $sheet->setCellValue("A{$row}", $sr);
                $sheet->setCellValue("B{$row}", $detail['fire_point_no'] ?? '');
                $sheet->mergeCells("C$row:D$row")->setCellValue("C$row", getDepartment($detail['department']) ?? '');
                $sheet->mergeCells("E$row:F$row")->setCellValue("E$row", getLocationname($detail['location']) ?? '');
                $sheet->setCellValue("G{$row}", getExtinguisherTypeName($detail['type']) ?? '');
                $sheet->setCellValue("H{$row}", $detail['capacity'] ?? '');
                $sheet->setCellValue("I{$row}", $detail['quantity'] ?? '');

                $dischargeTubeStatus = $detail->discharge_tube ?? '';

                if ($dischargeTubeStatus == FUNCTIONAL) {
                    $sheet->setCellValue("J{$row}", __('inspection.functional'));
                    $sheet->getStyle("J{$row}");
                } elseif ($dischargeTubeStatus == NON_FUNCTIONAL) {
                    $sheet->setCellValue("J{$row}", __('inspection.non_functional'));
                    $sheet->getStyle("J{$row}");
                } else {
                    $sheet->setCellValue("J{$row}", '');
                }

                $dischargeHornStatus = $detail->discharge_horn ?? '';
                if ($dischargeHornStatus == FUNCTIONAL) {
                    $sheet->setCellValue("K{$row}", __('inspection.functional'));
                    $sheet->getStyle("K{$row}");
                } elseif ($dischargeHornStatus == NON_FUNCTIONAL) {
                    $sheet->setCellValue("K{$row}", __('inspection.non_functional'));
                    $sheet->getStyle("K{$row}");
                } else {
                    $sheet->setCellValue("K{$row}", '');
                }

                $sheet->setCellValue("L{$row}", $detail['weight_of_co2_in_fe'] ?? '');

                $safetyPinStatus = $detail->safety_pin ?? '';
                if ($safetyPinStatus == PRESENT) {
                    $sheet->setCellValue("M{$row}", __('inspection.present'));
                    $sheet->getStyle("M{$row}");
                } elseif ($safetyPinStatus == MISSING) {
                    $sheet->setCellValue("M{$row}", __('inspection.missing'));
                    $sheet->getStyle("M{$row}");
                } else {
                    $sheet->setCellValue("M{$row}", '');
                }

                $sheet->setCellValue("N{$row}", $detail['approach'] ?? '');
                $sheet->setCellValue("O{$row}", $detail['remarks'] ?? '');

                $sheet->getStyle("A{$row}:O{$row}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sr++;
                $row++;
            }

            $signatureRow = $row;
            $sheet->getRowDimension($signatureRow)->setRowHeight(80);

            $sheet->mergeCells("A{$signatureRow}:E{$signatureRow}");
            $sheet->getStyle("A{$signatureRow}:E{$signatureRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);
            if (file_exists($prepared_by_signature)) {
                $drawing = new Drawing();
                $drawing->setName('Prepared Signature');
                $drawing->setDescription('Prepared By');
                $drawing->setPath($prepared_by_signature);
                $drawing->setCoordinates("C{$signatureRow}");
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setHeight(40);
                $drawing->setWorksheet($sheet);
                $sheet->setCellValue("A{$signatureRow}", "\n\n\nPrepared By:\n" . getUsername($co_type->created_by));
            } else {
                $sheet->setCellValue("A{$signatureRow}", "Prepared By:\nInspection not yet started");
            }

            $sheet->mergeCells("F{$signatureRow}:K{$signatureRow}");
            $sheet->getStyle("F{$signatureRow}:K{$signatureRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);
            if (file_exists($verified_by_signature)) {
                $drawing = new Drawing();
                $drawing->setName('Verified Signature');
                $drawing->setDescription('Verified By');
                $drawing->setPath($verified_by_signature);
                $drawing->setCoordinates("I{$signatureRow}");
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setHeight(40);
                $drawing->setWorksheet($sheet);
                $sheet->setCellValue("F{$signatureRow}", "\n\n\nVerified By:\n" . getUsername($co_type->updated_by));
            } else {
                $sheet->setCellValue("F{$signatureRow}", "Verified By:\nInspection not yet completed");
            }

            $sheet->mergeCells("L{$signatureRow}:O{$signatureRow}");
            $sheet->getStyle("L{$signatureRow}:O{$signatureRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);
            if (file_exists($approved_by_signature)) {
                $drawing = new Drawing();
                $drawing->setName('Approved Signature');
                $drawing->setDescription('Approved By');
                $drawing->setPath($approved_by_signature);
                $drawing->setCoordinates("M{$signatureRow}");
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setHeight(40);
                $drawing->setWorksheet($sheet);
                $sheet->setCellValue("L{$signatureRow}", "\n\n\nApproved By:\n" . getUsername($co_type->approved_by));
            } else {
                $sheet->setCellValue("L{$signatureRow}", "Approved By:\nApproval pending");
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'CO2 Type Fire Extinguisher Inspection.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/fire-extinguisher/co2/list'));
        }
    }
}
