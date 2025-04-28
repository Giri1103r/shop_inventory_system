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
use App\Models\Inspection\Fire\CartridgeTypeFireExtinguisher;
use App\Models\Inspection\Fire\CartridgeTypeFireExtinguisherDetails;
use App\Models\Inspection\Fire\FireStatusLog;
use App\Models\Inspection\Fire\FireFileUpload;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Fire\FireSignatureUpload;
use App\Models\Inspection\Fire\FireExtinguisherType;
use App\Models\Inspection\Fire\FireCheckListFollowUp;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CartridgeTypeFireExtinguisherController extends Controller
{
    private $cartridge_type;
    private $cartridge_type_details;
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
        $this->cartridge_type = new CartridgeTypeFireExtinguisher();
        $this->cartridge_type_details = new CartridgeTypeFireExtinguisherDetails();
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
                    $data =  $this->cartridge_type->list();
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
                            $btn = '<a href="' . admin_url('fire/fire-extinguisher/cartridge/view/' . encryptId($row->fire_co_type_id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            if ($row->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-extinguisher/cartridge/verification/' . encryptId($row->fire_co_type_id)) . '/ehs" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if (($row->inspection_status == WAITING_FOR_CAPA_ACTION || $row->inspection_status == L2_MANAGER_REJECTED || $row->inspection_status == EHS_OFFICER_REJECTED || $row->inspection_status == L1_MANAGER_REJECTED) && (CheckUserRole(ROLE_FIRE_ASSOCIATES) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-extinguisher/cartridge/verification/' . encryptId($row->fire_co_type_id)) . '/capa" class="" title="' . __('inspection.capa_action') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_CAPA_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-extinguisher/cartridge/verification/' . encryptId($row->fire_co_type_id)) . '/ehsVerify" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L1_VERIFICATION && (CheckUserRole(ROLE_L1_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-extinguisher/cartridge/verification/' . encryptId($row->fire_co_type_id)) . '/level-one-manager" class="" title="' . __('inspection.l1_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L2_VERIFICATION && (CheckUserRole(ROLE_L2_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/fire-extinguisher/cartridge/verification/' . encryptId($row->fire_co_type_id)) . '/level-two-manager" class="" title="' . __('inspection.l2_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            $btn .= '<a href="' . admin_url('fire/fire-extinguisher/cartridge/exportViewPdf/' . encryptId($row->fire_co_type_id)) . '" style="margin-right: 5px;" title="PDF">
                                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                                    </a>';

                            $btn .= '<a href="' . admin_url('fire/fire-extinguisher/cartridge/generalExcel/' . encryptId($row->fire_co_type_id)) . '" style="margin-right: 5px;" title="Excel"> <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i></a>';

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
        return view('inspection.fire.cartridge_type_fire_extinguisher.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $location = $this->location->getLocationName();
            $unit = $this->unit->getUnit();
            $frequency = $this->frequency->getFrequency();
            $shifts = $this->shift->getShiftname();
            $department = $this->department->getdepartment();
            $document_no = $this->document_reference->selectUsingName('CartridgeTypeFireExtinguisher');
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

            return view('inspection.fire.cartridge_type_fire_extinguisher.add', $data);
        } catch (Exception $ex) {
            report($ex);
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/fire-extinguisher/cartridge/list'));
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
                'handle.*' => 'required',
                'wheel.*' => 'required',
                'weight_of_cartidge.*' => 'required',
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
                'handle.*' => 'Handle is required.',
                'wheel.*' => 'wheel is required.',
                'weight_of_cartidge.*' => 'Weight of Cartridge is required.',
                'safety_pin.*.required' => 'Safety Pin is required.',
                'approach.*.required' => 'Approach is required.',
                'remarks.*.required' => 'Remarks are required.',
                // 'observation.required' => 'Observation is  required.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $inspection = $this->cartridge_type->store();
            $inspection_type = CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION;
            $id = $inspection->id;

            $inspection_details = $this->cartridge_type_details->store($id);
            $inspection_file = $this->files->file_upload($inspection_type, $id);

            // $checklist_store = $this->checklist_follow->store($inspection_type, $id);
            $signature_update = $this->signature->CheckedBySignature($id, $inspection_type);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'CARTRIDGE TYPE FIRE INSPECTION';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Cartridge Type Fire Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('fire/fire-extinguisher/cartridge/view/' . encryptId($id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Cartridge Type Fire Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('fire/fire-extinguisher/cartridge/verification/' . encryptId($id) . '/ehs');
                $details = array(
                    'fire_type' => 'Cartridge Type Fire Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION,
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
                return redirect(admin_url('fire/fire-extinguisher/cartridge/list'));
            }
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/fire-extinguisher/cartridge/list'));
        }
    }

    public function View(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_type = CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION;
            $inspection = $this->cartridge_type->selectOne($id);
            $inspection_details = $this->cartridge_type_details->GetDetails($inspection->id);
            $inspection_image = $this->files->GetFile($inspection_type, $id);

            $status_log = $this->statusLog->selectOne($id, CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION);
            $document_no = $this->document_reference->selectOne($inspection->document_reference_id);

            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'inspection_image' => $inspection_image,
                'status_log' => $status_log,
                'document_no' => $document_no,
            );
            return view('inspection.fire.cartridge_type_fire_extinguisher.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/fire-extinguisher/cartridge/list'));
        }
    }

    public function Approvals(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_type = CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION;

            $inspection = $this->cartridge_type->selectOne($id);
            $inspection_details = $this->cartridge_type_details->GetDetails($inspection->id);
            $inspection_image = $this->files->GetFile($inspection_type, $id);
            $status_log = $this->statusLog->selectOne($id, CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION);
            $document_no = $this->document_reference->selectOne($inspection->document_reference_id);


            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'inspection_image' => $inspection_image,
                'status_log' => $status_log,
                'document_no' => $document_no,
            );
            return view('inspection.fire.cartridge_type_fire_extinguisher.approve', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/fire-extinguisher/cartridge/list'));
        }
    }

    public function EHSOfficerSubmit(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $inspection_updates = $this->cartridge_type->EHSOfficerUpdate($id);
            $signature_update = $this->signature->signatureUpload(CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION);
            $inspection_details = $this->cartridge_type->selectOne($id);
            if ($request->is_passed == 1) {
                $message = 'Cartridge Type Fire Inspeciton Approved Successfully';
                $web_link =   admin_url('fire/fire-extinguisher/cartridge/verification/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
            } else {
                $message = 'Inspection Recommended for the CAPA Action';
                $web_link =   admin_url('fire/fire-extinguisher/cartridge/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = WAITING_FOR_CAPA_ACTION;
            }
            $userIds = [
                'users' => $inspection_details->created_by,
            ];
            $mailsubject = 'CARTRIDGE TYPE FIRE INSPECTION';
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
            $url = admin_url('fire/fire-extinguisher/cartridge/verification/' . encryptId($id) . '/capa');
            $details = array(
                'fire_type' => 'Cartridge Type Fire Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new FireInspection($details));

            $insert_array = [
                'type' => CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-extinguisher/cartridge/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went Wrong!');
            return redirect(admin_url('fire/fire-extinguisher/cartridge/list'));
        }
    }

    public function CAPASubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $safety_gallery_inspection = $this->cartridge_type->capaSubmit($id);
            $inspection_details = $this->cartridge_type->selectOne($id);
            $signature_update = $this->signature->signatureUpload(CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION);
            $ehsOfficers = $inspection_details->verified_by;
            $userIds = [
                'users' => $ehsOfficers,
            ];
            $mailsubject = 'CARTRIDGE TYPE FIRE INSPECTION';
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
                'web_link' =>  admin_url('fire/fire-extinguisher/cartridge/verification/' . encryptId($inspection_details->id)) . '/ehsVerify',
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $user = $inspection_details->verified_by;
            $email_id = getUseremail($user);
            $url = admin_url('fire/fire-extinguisher/cartridge/verification/' . encryptId($id) . '/ehs');
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
                'type' => CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_ACTION,
                'to_status' => WAITING_FOR_CAPA_VERIFICATION,
                'created_by' => Auth::id(),
                'remarks' => $request->capa_remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-extinguisher/cartridge/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/fire-extinguisher/cartridge/list'));
        }
    }

    public function CAPAVerifySubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->remarks;
            $safety_gallery_inspection = $this->cartridge_type->capaVerifySubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION);
            $inspection_details = $this->cartridge_type->selectOne($id);
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('fire/fire-extinguisher/cartridge/verification/' . encryptId($inspection_details->id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = WAITING_FOR_L1_VERIFICATION;
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('fire/fire-extinguisher/cartridge/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = EHS_OFFICER_REJECTED;
            }

            $mailsubject = 'CARTRIDGE TYPE FIRE INSPECTION';
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
                    'fire_type' => 'Cartridge Type Fire Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-extinguisher/cartridge/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/fire-extinguisher/cartridge/list'));
        }
    }

    public function levelOneManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_one_manager;
            $safety_gallery_inspection = $this->cartridge_type->levelOneManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION);
            $inspection_details = $this->cartridge_type->selectOne($id);
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('fire/fire-extinguisher/cartridge/verification/' . encryptId($inspection_details->id) . '/level-two-manager');
                $user = GetLevelTwoManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by], [$inspection_details->verified_by]);
                $to_status = WAITING_FOR_L2_VERIFICATION;
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/fire-extinguisher/cartridge/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = L1_MANAGER_REJECTED;
            }

            $mailsubject = 'CARTRIDGE TYPE FIRE INSPECTION';
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
                    'fire_type' => 'Cartridge Type Fire Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L1_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_one_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-extinguisher/cartridge/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/fire-extinguisher/cartridge/list'));
        }
    }

    public function levelTwoManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_two_manager;
            $safety_gallery_inspection = $this->cartridge_type->levelTwoManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION);
            $inspection_details = $this->cartridge_type->selectOne($id);
            if ($status == 1) {
                $message = 'Cartridge Type Fire Inspeciton Approved Successfully!';
                $web_link =   admin_url('fire/fire-extinguisher/cartridge/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/fire-extinguisher/cartridge/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = L2_MANAGER_REJECTED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
            }

            $mailsubject = 'CARTRIDGE TYPE FIRE INSPECTION';
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
                    'fire_type' => 'Cartridge Type Fire Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L2_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_two_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/fire-extinguisher/cartridge/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/fire-extinguisher/cartridge/list'));
        }
    }


    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->cartridge_type->exportdata();

            $inspection_type = CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION;
            $document_no = $this->document_reference->selectUsingName('CartridgeTypeFireExtinguisher');

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error', __('inspection.excess_error'));
            }

            $data = array(
                'content' => $allData,
                'document_no' => $document_no,
                'inspection_type' => $inspection_type,
                'pagetitle' => "Cartridge Type Fire Inspection",
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

            $view = view('inspection.fire.cartridge_type_fire_extinguisher.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Cartridge Type Fire Inspection.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/fire-extinguisher/cartridge/list'));
        }
    }

    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->cartridge_type->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            foreach (range('A', 'P') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }
            $row = 1;

            foreach ($allData as $groupedDetails) {

                $inspection_detail = $groupedDetails->first();
                $document_no = $this->document_reference->selectOne($inspection_detail->document_reference_id);

                $prepared_by_signature = GetFireSignature($inspection_detail->checked_by, $inspection_detail->fire_id, CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION);
                $verified_by_signature = GetFireSignature($inspection_detail->verified_by, $inspection_detail->fire_id, CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION);
                $approved_by_signature = GetFireSignature($inspection_detail->approved_by, $inspection_detail->fire_id, CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION);

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

                $sheet->mergeCells("C{$titleRow}:N" . ($titleRow + 2));
                $sheet->setCellValue("C{$titleRow}", "FIRE EXTINGUISHER INSPECTION CHECKLIST (CARTIDGE TYPE) PN INTERNATIONAL PVT. LTD.");

                $sheet->getStyle("C{$titleRow}:N" . ($titleRow + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->setCellValue("O{$titleRow}", "Doc. No.");
                $sheet->setCellValue("P{$titleRow}", $document_no->doc_no ?? '');

                $sheet->setCellValue("O" . ($titleRow + 1), "Issue Dt.");
                $sheet->setCellValue("P" . ($titleRow + 1), Displaydateformat($document_no->issue_date ?? ''));

                $sheet->setCellValue("O" . ($titleRow + 2), "Rev. & Dt.");
                $sheet->setCellValue("P" . ($titleRow + 2), $document_no->rev_dt ?? '');

                $sheet->getStyle("O{$titleRow}:P" . ($titleRow + 2))->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $headerInfoRow = $titleRow + 3;

                $sheet->mergeCells("A{$headerInfoRow}:E{$headerInfoRow}")->setCellValue("A{$headerInfoRow}", "Date of Inspection:- " . Displaydateformat($inspection_detail->inspection_date));
                $sheet->mergeCells("F{$headerInfoRow}:M{$headerInfoRow}")->setCellValue("F{$headerInfoRow}", "Location:- " . getLocationname($inspection_detail->location));
                $sheet->mergeCells("N{$headerInfoRow}:P{$headerInfoRow}")->setCellValue("N{$headerInfoRow}", "Shift:- " . $inspection_detail->shift);
                $sheet->getStyle("A{$headerInfoRow}:P{$headerInfoRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $headerInfoRow++;

                $sheet->mergeCells("A{$headerInfoRow}:E{$headerInfoRow}")->setCellValue("A{$headerInfoRow}", "Next Due Date:- " . Displaydateformat($inspection_detail->next_due));
                $sheet->mergeCells("F{$headerInfoRow}:M{$headerInfoRow}")->setCellValue("F{$headerInfoRow}", "Unit:- " . getUnitname($inspection_detail->unit));
                $sheet->mergeCells("N{$headerInfoRow}:P{$headerInfoRow}")->setCellValue("N{$headerInfoRow}", "Frequency:- " . getFrequencyname($inspection_detail->frequency));
                $sheet->getStyle("A{$headerInfoRow}:P{$headerInfoRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $headerInfoRow++;

                $headerStart = $headerInfoRow;

                $sheet->mergeCells("A{$headerStart}:A" . ($headerStart + 2))->setCellValue("A{$headerStart}", "SR. NO");
                $sheet->mergeCells("B{$headerStart}:B" . ($headerStart + 2))->setCellValue("B{$headerStart}", "FIRE POINT NO.");
                $sheet->mergeCells("C{$headerStart}:D" . ($headerStart + 2))->setCellValue("C{$headerStart}", "DEPARTMENT");
                $sheet->mergeCells("E{$headerStart}:F" . ($headerStart + 2))->setCellValue("E{$headerStart}", "LOCATION");
                $sheet->mergeCells("G{$headerStart}:O{$headerStart}")->setCellValue("G{$headerStart}", "CHECK ITEMS");
                $sheet->mergeCells("P{$headerStart}:P" . ($headerStart + 2))->setCellValue("P{$headerStart}", "REMARK");

                $sheet->mergeCells("G" . ($headerStart + 1) . ":I" . ($headerStart + 1))->setCellValue("G" . ($headerStart + 1), "DESCRIPTION");
                $sheet->mergeCells("J" . ($headerStart + 1) . ":L" . ($headerStart + 1))->setCellValue("J" . ($headerStart + 1), "CONDITION");
                $sheet->mergeCells("M" . ($headerStart + 1) . ":M" . ($headerStart + 2))->setCellValue("M" . ($headerStart + 1), "WEIGHT OF CARTIDGE");
                $sheet->mergeCells("N" . ($headerStart + 1) . ":N" . ($headerStart + 2))->setCellValue("N" . ($headerStart + 1), "SAFETY PIN");
                $sheet->mergeCells("O" . ($headerStart + 1) . ":O" . ($headerStart + 2))->setCellValue("O" . ($headerStart + 1), "APPROACH");

                $sheet->setCellValue("G" . ($headerStart + 2), "TYPE");
                $sheet->setCellValue("H" . ($headerStart + 2), "CAPACITY");
                $sheet->setCellValue("I" . ($headerStart + 2), "QUANTITY");
                $sheet->setCellValue("J" . ($headerStart + 2), "DISCHARGE TUBE");
                $sheet->setCellValue("K" . ($headerStart + 2), "HANDLE");
                $sheet->setCellValue("L" . ($headerStart + 2), "WHEEL");

                $sheet->getStyle("A{$headerStart}:P" . ($headerStart + 2))->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);


                $dataRow = $headerStart + 3;
                $sr = 1;

                foreach ($groupedDetails as $detail) {
                    $sheet->setCellValue("A{$dataRow}", $sr);
                    $sheet->setCellValue("B{$dataRow}", $detail['fire_point_no'] ?? '');
                    $sheet->mergeCells("C{$dataRow}:D{$dataRow}")->setCellValue("C{$dataRow}", getDepartment($detail['department']) ?? '');
                    $sheet->mergeCells("E{$dataRow}:F{$dataRow}")->setCellValue("E{$dataRow}", getLocationname($detail['location']) ?? '');
                    $sheet->setCellValue("G{$dataRow}", getExtinguisherTypeName($detail['extinguisher_type']) ?? '');
                    $sheet->setCellValue("H{$dataRow}", $detail['capacity'] ?? '');
                    $sheet->setCellValue("I{$dataRow}", $detail['quantity'] ?? '');

                    $dischargeTubeStatus = $detail['discharge_tube'] ?? '';
                    $sheet->setCellValue("J{$dataRow}", $dischargeTubeStatus == FUNCTIONAL ? __('inspection.functional') : ($dischargeTubeStatus == NON_FUNCTIONAL ? __('inspection.non_functional') : ''));

                    $handleStatus = $detail['handle'] ?? '';
                    $sheet->setCellValue("K{$dataRow}", $handleStatus == GOOD ? 'GOOD' : ($handleStatus == DAMAGED ? 'DAMAGED' : ''));

                    $wheelStatus = $detail['wheel'] ?? '';
                    $sheet->setCellValue("L{$dataRow}", $wheelStatus == FUNCTIONAL ? __('inspection.functional') : ($wheelStatus == NON_FUNCTIONAL ? __('inspection.non_functional') : ''));

                    $sheet->setCellValue("M{$dataRow}", $detail['weight_of_cartidge'] ?? '');

                    $safetyPinStatus = $detail['safety_pin'] ?? '';
                    $sheet->setCellValue("N{$dataRow}", $safetyPinStatus == PRESENT ? __('inspection.present') : ($safetyPinStatus == MISSING ? __('inspection.missing') : ''));

                    $sheet->setCellValue("O{$dataRow}", $detail['approach'] ?? '');
                    $sheet->setCellValue("P{$dataRow}", $detail['remarks'] ?? '');

                    $sheet->getStyle("A{$dataRow}:P{$dataRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $sr++;
                    $dataRow++;
                }



                $signatureRowStart = $dataRow;
                $sheet->getRowDimension($signatureRowStart)->setRowHeight(80);

                $sheet->mergeCells("A{$signatureRowStart}:E{$signatureRowStart}");
                $sheet->getStyle("A{$signatureRowStart}:E{$signatureRowStart}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                if (file_exists($prepared_by_signature)) {
                    $drawing = new Drawing();
                    $drawing->setName('Signature');
                    $drawing->setDescription('Prepared By');
                    $drawing->setPath($prepared_by_signature);
                    $drawing->setCoordinates("C{$signatureRowStart}");
                    $drawing->setOffsetX(10);
                    $drawing->setOffsetY(5);
                    $drawing->setHeight(40);
                    $drawing->setWorksheet($sheet);

                    $sheet->setCellValue("A{$signatureRowStart}", "\n\n\nPrepared By:\n" . getUsername($inspection_detail->checked_by));
                } else {
                    $sheet->setCellValue("A{$signatureRowStart}", "Prepared By:\nInspection not yet started");
                }

                $sheet->mergeCells("F{$signatureRowStart}:L{$signatureRowStart}");
                $sheet->getStyle("F{$signatureRowStart}:L{$signatureRowStart}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                if (file_exists($verified_by_signature)) {
                    $drawing = new Drawing();
                    $drawing->setName('Signature');
                    $drawing->setDescription('Verified By');
                    $drawing->setPath($verified_by_signature);
                    $drawing->setCoordinates("I{$signatureRowStart}");
                    $drawing->setOffsetX(50);
                    $drawing->setOffsetY(5);
                    $drawing->setHeight(40);
                    $drawing->setWorksheet($sheet);

                    $sheet->setCellValue("F{$signatureRowStart}", "\n\n\nVerified By:\n" . getUsername($inspection_detail->updated_by));
                } else {
                    $sheet->setCellValue("F{$signatureRowStart}", "Verified By:\nInspection not yet completed");
                }

                $sheet->mergeCells("M{$signatureRowStart}:P{$signatureRowStart}");
                $sheet->getStyle("M{$signatureRowStart}:P{$signatureRowStart}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                if (file_exists($approved_by_signature)) {
                    $drawing = new Drawing();
                    $drawing->setName('Signature');
                    $drawing->setDescription('Approved By');
                    $drawing->setPath($approved_by_signature);
                    $drawing->setCoordinates("N{$signatureRowStart}");
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);
                    $drawing->setHeight(40);
                    $drawing->setWorksheet($sheet);

                    $sheet->setCellValue("M{$signatureRowStart}", "\n\n\nApproved By:\n" . getUsername($inspection_detail->approved_by));
                } else {
                    $sheet->setCellValue("M{$signatureRowStart}", "Approved By:\nApproval pending");
                }


                $row = $signatureRowStart + 6;
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'Catridge Type Fire Extinguisher.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/pa-system-inspection/list'));
        }
    }

    public function ExportViewPDF(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $status_log = $this->statusLog->selectOne($id, CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION);
                $forklift_details = $this->cartridge_type->selectOne($id);
                $inspection = $this->cartridge_type_details->GetDetails($forklift_details->id);
                $document_no = $this->document_reference->selectOne($forklift_details->document_reference_id);

                $data = [
                    'status_log' => $status_log,
                    'forklift_details' => $forklift_details,
                    'document_no' => $document_no,
                    'pagetitle' => "Cartridge Type Fire Inspection",
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

            $html = view('inspection.fire.cartridge_type_fire_extinguisher.viewPdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Cartridge Type Fire Inspection.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/fire-extinguisher/cartridge/list'));
        }
    }

    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $cartridge_type = $this->cartridge_type->find($id);



            $inspection_data = $this->cartridge_type_details->GetDetails($cartridge_type->id);
            $document_no = $this->document_reference->selectOne($cartridge_type->document_reference_id);
            $prepared_by_signature = GetFireSignature($cartridge_type->created_by, $cartridge_type->id, CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION);
            $verified_by_signature = GetFireSignature($cartridge_type->updated_by, $cartridge_type->id, CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION);
            $approved_by_signature = GetFireSignature($cartridge_type->approved_by, $cartridge_type->id, CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION);

            foreach (range('A', 'P') as $col) {
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
            $sheet->getStyle('A1:B3')->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $sheet->mergeCells("C1:N3");
            $sheet->setCellValue("C1", "FIRE EXTINGUISHER INSPECTION CHECKLIST (CARTIDGE TYPE) PN INTERNATIONAL PVT. LTD.");
            $sheet->getStyle("C1:N3")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $labelMap = [
                'O1' => ['value' => 'Doc. No.', 'valueCell' => 'P1', 'data' => $document_no->doc_no],
                'O2' => ['value' => 'Issue Dt.', 'valueCell' => 'P2', 'data' => Displaydateformat($document_no->issue_date)],
                'O3' => ['value' => 'Rev. & Dt.', 'valueCell' => 'P3', 'data' => $document_no->rev_dt],
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

            $sheet->mergeCells("A4:E4")->setCellValue("A4", "Date of Inspection:- " . Displaydateformat($cartridge_type->inspection_date));
            $sheet->mergeCells("F4:M4")->setCellValue("F4", "Location :- " . getLocationname($cartridge_type->location));
            $sheet->mergeCells("N4:P4")->setCellValue("N4", "Shift:- " . getShift($cartridge_type->shift));
            $sheet->mergeCells("A5:E5")->setCellValue("A5", "Next Due date:- " . Displaydateformat($cartridge_type->next_due));
            $sheet->mergeCells("F5:M5")->setCellValue("F5", "Unit:- " . getUnitname($cartridge_type->unit));
            $sheet->mergeCells("N5:P5")->setCellValue("N5", "Frequency:- " . getFrequencyname($cartridge_type->frequency));
            $sheet->getStyle("A4:P5")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // Table Header
            $sheet->mergeCells("A6:A8")->setCellValue("A6", "SR. NO");
            $sheet->mergeCells("B6:B8")->setCellValue("B6", "FIRE POINT NO.");
            $sheet->mergeCells("C6:D8")->setCellValue("C6", "DEPARTMENT");
            $sheet->mergeCells("E6:F8")->setCellValue("E6", "LOCATION");

            $sheet->mergeCells("G6:O6")->setCellValue("G6", "CHECK ITEMS");

            $sheet->mergeCells("G7:I7")->setCellValue("G7", "DESCRIPTION");
            $sheet->mergeCells("J7:L7")->setCellValue("J7", "CONDITION");

            $sheet->setCellValue("G8", "TYPE");
            $sheet->setCellValue("H8", "CAPACITY");
            $sheet->setCellValue("I8", "QUANTITY");
            $sheet->setCellValue("J8", "DISCHARGE TUBE");
            $sheet->setCellValue("K8", "HANDLE");
            $sheet->setCellValue("L8", "WHEEL");

            $sheet->mergeCells("M7:M8")->setCellValue("M7", "WEIGHT OF CARTIDGE");
            $sheet->mergeCells("N7:N8")->setCellValue("N7", "SAFETY PIN");
            $sheet->mergeCells("O7:O8")->setCellValue("O7", "APPROACH");
            $sheet->mergeCells("P6:P8")->setCellValue("P6", "REMARK");


            $sheet->getStyle("A6:P8")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $row = 9;
            $sr = 1;
            foreach ($inspection_data as $detail) {

                $sheet->setCellValue("A{$row}", $sr);
                $sheet->setCellValue("B{$row}", $detail['fire_point_no'] ?? '');
                $sheet->mergeCells("C{$row}:D{$row}")->setCellValue("C{$row}", getDepartment($detail['department']) ?? '');
                $sheet->mergeCells("E{$row}:F{$row}")->setCellValue("E{$row}", getLocationname($detail['location']) ?? '');
                $sheet->setCellValue("G{$row}", getExtinguisherTypeName($detail['type']) ?? '');
                $sheet->setCellValue("H{$row}", $detail['capacity'] ?? '');
                $sheet->setCellValue("I{$row}", $detail['quantity'] ?? '');

                $dischargeTubeStatus = $detail['discharge_tube'] ?? '';
                if ($dischargeTubeStatus == FUNCTIONAL) {
                    $sheet->setCellValue("J{$row}", __('inspection.functional'));
                } elseif ($dischargeTubeStatus == NON_FUNCTIONAL) {
                    $sheet->setCellValue("J{$row}", __('inspection.non_functional'));
                } else {
                    $sheet->setCellValue("J{$row}", '');
                }

                $handleStatus = $detail['handle'] ?? '';
                if ($handleStatus == GOOD) {
                    $sheet->setCellValue("K{$row}", 'GOOD');
                } elseif ($handleStatus == DAMAGED) {
                    $sheet->setCellValue("K{$row}", 'DAMAGED');
                } else {
                    $sheet->setCellValue("K{$row}", '');
                }

                $wheelStatus = $detail['wheel'] ?? '';
                if ($wheelStatus == FUNCTIONAL) {
                    $sheet->setCellValue("L{$row}", __('inspection.functional'));
                } elseif ($wheelStatus == NON_FUNCTIONAL) {
                    $sheet->setCellValue("L{$row}", __('inspection.non_functional'));
                } else {
                    $sheet->setCellValue("L{$row}", '');
                }

                $sheet->setCellValue("M{$row}", $detail['weight_of_cartidge'] ?? '');

                $safetyPinStatus = $detail['safety_pin'] ?? '';
                if ($safetyPinStatus == PRESENT) {
                    $sheet->setCellValue("N{$row}", __('inspection.present'));
                } elseif ($safetyPinStatus == MISSING) {
                    $sheet->setCellValue("N{$row}", __('inspection.missing'));
                } else {
                    $sheet->setCellValue("N{$row}", '');
                }

                $sheet->setCellValue("O{$row}", $detail['approach'] ?? '');
                $sheet->setCellValue("P{$row}", $detail['remarks'] ?? '');

                $sheet->getStyle("A{$row}:P{$row}")->applyFromArray([
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
                $sheet->setCellValue("A{$signatureRow}", "\n\n\nPrepared By:\n" . getUsername($cartridge_type->created_by));
            } else {
                $sheet->setCellValue("A{$signatureRow}", "Prepared By:\nInspection not yet started");
            }

            $sheet->mergeCells("F{$signatureRow}:L{$signatureRow}");
            $sheet->getStyle("F{$signatureRow}:L{$signatureRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);
            if (file_exists($verified_by_signature)) {
                $drawing = new Drawing();
                $drawing->setName('Verified Signature');
                $drawing->setDescription('Verified By');
                $drawing->setPath($verified_by_signature);
                $drawing->setCoordinates("H{$signatureRow}");
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setHeight(40);
                $drawing->setWorksheet($sheet);
                $sheet->setCellValue("F{$signatureRow}", "\n\n\nVerified By:\n" . getUsername($cartridge_type->updated_by));
            } else {
                $sheet->setCellValue("F{$signatureRow}", "Verified By:\nInspection not yet completed");
            }

            $sheet->mergeCells("M{$signatureRow}:P{$signatureRow}");
            $sheet->getStyle("M{$signatureRow}:P{$signatureRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);
            if (file_exists($approved_by_signature)) {
                $drawing = new Drawing();
                $drawing->setName('Approved Signature');
                $drawing->setDescription('Approved By');
                $drawing->setPath($approved_by_signature);
                $drawing->setCoordinates("N{$signatureRow}");
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setHeight(40);
                $drawing->setWorksheet($sheet);
                $sheet->setCellValue("M{$signatureRow}", "\n\n\nApproved By:\n" . getUsername($cartridge_type->approved_by));
            } else {
                $sheet->setCellValue("M{$signatureRow}", "Approved By:\nApproval pending");
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Catridge Type Fire Extinguisher.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            dd($e);
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/fire-extinguisher/cartridge/list'));
        }
    }
}
