<?php

namespace App\Http\Controllers\Inspection\Fire;

use Exception;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Master\Employee;
use App\Models\Inspection\Fire\FireCheckListFollowUp;
use App\Models\Inspection\Fire\FireCheckListFollowUpObservation;
use App\Models\Inspection\Master\ChecklistType;
use App\Models\Inspection\Master\ChecklistSubType;
use App\Models\Inspection\Master\ChecklistSubTypeDataName;
use App\Models\Inspection\Master\ChecklistSubTypeData;
use App\Models\Inspection\Master\ChecklistOptionType;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Master\Unit;
use App\Models\Master\Department;
use App\Models\Inspection\Fire\FireSignatureUpload;
use App\Models\Inspection\Fire\FireStatusLog;
use App\Models\Inspection\Fire\ObservationWhyWhyAnalysis;
use App\Mail\Inspection\Fire\FireInspection;
use App\Models\User;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class ChecklistObservationFollowupController extends Controller
{

    private $checklist_type;
    private $checklist_subtype;
    private $checklist_subtypedata;
    private $checklist_subtypename;
    private $followup;
    private $upload_log;
    private $checklist_option;
    private $shift;
    private $static_docno;
    private $unit;
    private $department;
    private $signature;
    private $followupObservation;
    private $statusLog;
    private $whywhyanalysis;

    public function __construct()
    {
        $this->followup = new FireCheckListFollowUp();
        $this->followupObservation = new FireCheckListFollowUpObservation();
        $this->checklist_type = new ChecklistType();
        $this->checklist_subtype = new ChecklistSubType();
        $this->checklist_option = new ChecklistOptionType();
        $this->checklist_option = new ChecklistOptionType();
        $this->checklist_subtypename = new ChecklistSubTypeDataName();
        $this->checklist_subtypedata = new ChecklistSubTypeData();
        $this->shift = new Shift();
        $this->static_docno = new InspectionStaticDocno();
        $this->unit = new Unit();
        $this->signature = new FireSignatureUpload();
        $this->department = new Department();
        $this->statusLog = new FireStatusLog();
        $this->whywhyanalysis = new ObservationWhyWhyAnalysis();
    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =    $this->followup->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {

                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type = '1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type = '0'>In-Active</span>";
                            }
                            // }
                            return $text;
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('date_of_inspection', function ($row) {
                            return Displaydateformat($row->date_of_inspection);
                        })
                        ->addColumn('observation_status', function ($row) {
                            $text = '';
                            switch ($row->observation_status) {
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
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>LEVEL 2 OFFICER REJECTED - Waiting For EHS Verification</span>";
                                    break;
                                case L1_MANAGER_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>LEVEL 1 OFFICER REJECTED - Waiting For EHS Verification</span>";
                                    break;
                                case EHS_OFFICER_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>EHS OFFICER REJECTED - Waiting For EHS Verification</span>";
                                    break;
                                default:
                                    $text = "<span class='badge rounded-pill text-bg-warning'>Unknown</span>";
                            }
                            return $text;
                        })

                        ->addColumn('inspection_type', function ($row) {
                            $text = '';
                            switch ($row->inspection_type) {
                                case HOOTER_INSPECTION:
                                    $text = 'Hooter Inspection';
                                    break;
                                case EMERGENCY_LIGHT_INSPECTION:
                                    $text = 'Emergency Light Inspection';
                                    break;
                                case MONTHLY_FIRE_PUMP:
                                    $text = 'Monthly Fire Pump';
                                    break;
                                case FIRE_MOCK_DRILL_INSPECION:
                                    $text = 'Fire Mock Drill Inspection';
                                    break;
                                case FIRE_EXTINGUISHER_INSPECTION:
                                    $text = 'Fire Extinguisher Inspection';
                                    break;
                                case ISOLATION_VALVE_INSPECTION:
                                    $text = 'Isolation Valve Inspection';
                                    break;
                                case FIRE_ALARM_INSPECTION:
                                    $text = 'Fire Alarm Inspection';
                                    break;
                                case SPRINKLAR_SYSTEM_INSPECTION:
                                    $text = 'Sprinkler System Inspection';
                                    break;
                                case SAND_BUCKET_INSPECTION:
                                    $text = 'Sand Bucket Inspection';
                                    break;
                                case DETECTOR_INSPECTION:
                                    $text = 'Detector Inspection';
                                    break;
                                case FIRE_PA_SYSTEM_INSPECTION:
                                    $text = 'Fire PA System Inspection';
                                    break;
                                case DAILY_FIRE_PUMP:
                                    $text = 'Daily Fire Pump';
                                    break;
                                case CO_TYPE_FIRE_EXTINGUISHER_INSPECTION:
                                    $text = 'CO Type Fire Extinguisher Inspection';
                                    break;
                                case HOSE_BOX_INSPECTION:
                                    $text = 'Hose Box Inspection';
                                    break;
                                case CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION:
                                    $text = 'Cartridge Type Fire Extinguisher Inspection';
                                    break;
                                case HOSE_REEL_INSPECTION:
                                    $text = 'Hose Reel Inspection';
                                    break;
                                case FIRE_MODULAR_INSPECTION:
                                    $text = 'Fire Modular Inspection';
                                    break;
                                case HYDRANT_RISER:
                                    $text = 'Hydrant Riser';
                                    break;
                                case OBSERVATION_FOLLOWUP:
                                    $text = 'Observation Follow-up';
                                    break;
                                case OBSERVATION_FOLLOWUP:
                                    $text = 'Observation Follow-up';
                                    break;
                                case GEMBA_WALK:
                                    $text = 'Observation Follow-up';
                                    break;
                                default:
                                    $text = 'Unknown';
                            }
                            return $text;
                        })

                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('fire/checklist-observation/view/' . encryptId($row->inspectionid) . '/' . encryptId($row->observationid)) .  '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                            // $btn .= '<a href="' . admin_url('inspection/master/checklist-sub-type/edit/' . encryptId($row->id)) . '" class="edit-icon " title="' . __('common.edit') . '"><i class="fa-solid fa-pen-to-square"></i> ';
                            // }

                            if (($row->observation_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && ((CheckUserRole(ROLE_EHS_OFFICER)) || isAdmin())) || ($row->observation_status == WAITING_FOR_CAPA_ACTION && ((CheckUserRole(ROLE_FIRE_ASSOCIATES) && ($row->responsible_person_id == Auth::id())) || isAdmin())) || ($row->observation_status == WAITING_FOR_CAPA_VERIFICATION && ((CheckUserRole(ROLE_EHS_OFFICER)) || isAdmin())) || ($row->observation_status == WAITING_FOR_L1_VERIFICATION && ((CheckUserRole(ROLE_L1_MANAGER)) || isAdmin()))  || ($row->observation_status == WAITING_FOR_L2_VERIFICATION && ((CheckUserRole(ROLE_L2_MANAGER)) || isAdmin()))  || ($row->observation_status == EHS_OFFICER_REJECTED && ($row->ehs_verify_by == Auth::id() || isAdmin())) || ($row->observation_status == L1_MANAGER_REJECTED && ($row->ehs_verify_by == Auth::id() || isAdmin())) || ($row->observation_status == L2_MANAGER_REJECTED && ($row->ehs_verify_by == Auth::id() || isAdmin()))) {
                                $btn .= '<a href="' . admin_url('fire/checklist-observation/verification/' . encryptId($row->inspectionid) . '/' . encryptId($row->observationid)) . '" class="me-1" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            $btn .= '<a href="' . admin_url('fire/checklist-observation/generalpdf/' . encryptId($row->inspectionid) . '/' . encryptId($row->observationid)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';
                            $btn .= '<a href="' . admin_url('fire/checklist-observation/generalExcel/' . encryptId($row->inspection_id) . '/' . encryptId($row->observationid)) . '"  style="margin-right: 5px;" title="Excel">
                               <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                            </a>';

                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'observation_status', 'inspection_type'])
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
        $checklist_types  = $this->checklist_type->select('id', 'category_name')->where('status', '1')->get();
        $data = array(
            'checklist_types' => $checklist_types,

        );
        return view('inspection.fire.observationFollowup.list', $data);
    }

    public function add(Request $request, $inspection_type, $inspection_id)

    {
        try {
            $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();

            $staticDocno  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                ['type', "ChecklistObservationFollowup"],
                ['status', '1']
            ])->first();

            $data = array(
                'staticDocno' => $staticDocno,
                'inspection_type' => $inspection_type,
                'inspection_id' => $inspection_id,
                'departmentList' => $departmentList,
                'unitList' => $unitList,
            );
            return view('inspection.fire.observationFollowup.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('fire/checklist-observation/list'));
        }
    }

    public function store(Request $request)
    {

        try {
            try {
                $inspection = $this->followup->store();
                $inspection_id = $inspection->id;
                $this->followupObservation->store($inspection_id);
                $inspection_details = $this->followupObservation->getInspection($inspection->id);
                $ehsOfficer = GetEHSOfficer();
                $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
                $mailsubject = 'Observation FollowUp';
                $notificationData = array(
                    'notification_type' => FIRE_INSPECTION,
                    'module_type' => 3,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => "Fire Associate create the Observation",
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $inspection_id,
                        'module' => 1,
                    )),

                    'web_link' =>  admin_url('fire/checklist-observation/verification/' . encryptId($inspection_id) . '/' . encryptId($inspection_details->id)),
                    'assigned_user' => array_to_string($ehsOfficers),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $title = 'Fire Associate create the Observation FollowUp';
                foreach ($ehsOfficers as $user) {
                    $email_id = getUseremail($user);
                    $url = admin_url('fire/checklist-observation/verification/' . encryptId($inspection_id));
                    $details = array(
                        'fire_type' => 'Observation Followup',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'url' => $url,
                        'data' => $inspection
                    );
                    Mail::to($email_id)->queue(new FireInspection($details));
                }

                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {

                report($ex);
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('fire/checklist-observation/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('fire/checklist-observation/list'));
        }
    }


    public function view($id, $observationid, Request $request)
    {
        try {
            if (Auth::check()) {

                $observation = $this->followup->selectOne(decryptId($id), decryptId($observationid));

                $document_no = $this->static_docno->selectOne($observation->documnet_reference_id);

                $data = array(
                    'observation' => $observation,
                    'document_no' => $document_no,
                );
                // dd($data);
            }
            return view('inspection.fire.observationFollowup.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/checklist-observation/list'));
        }
    }



    public function employeename(Request $request)
    {
        $name = $request->input('search');

        $employees = Employee::where(function ($query) use ($name) {
            $query->where('emp_name', 'like', '%' . $name . '%')
                ->orWhere('emp_id', 'like', '%' . $name . '%');
        })
            ->whereRaw('FIND_IN_SET(?, user_role)', [18])
            ->where('status', 1)
            ->limit(10)
            ->get();

        return response()->json(
            $employees->map(function ($employee) {
                return [
                    'id' => encryptId($employee->login_id),
                    'text' => $employee->emp_name . ' - ' . $employee->emp_id,
                ];
            })
        );
    }



    public function Approvals(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $observationid = decryptId($request->observationid);

            $inspection_type = DETECTOR_INSPECTION;

            $observation = $this->followup->selectOne($id, $observationid);

            // $status_log = $this->statusLog->selectOne($id, DETECTOR_INSPECTION);


            $data = array(
                'observation' => $observation,
                // 'status_log' => $status_log,
            );

            return view('inspection.fire.observationFollowup.approve', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/checklist-observation/list'));
        }
    }

    public function EHSOfficerSubmit(Request $request)
    {



        try {
            $id = decryptId($request->id);
            $observationid = decryptId($request->observation_id);
            $to_status = WAITING_FOR_CAPA_ACTION;
            $this->whywhyanalysis->store($id, $observationid);
            $inspection_updates = $this->followupObservation->EHSOfficerUpdate($observationid);
            $this->followupObservation->statusUpdate($observationid, $to_status);
            $inspection_details = $this->followup->selectOne($id, $observationid);
            $mailsubject = 'CAPA Assigned';
            $employees = User::where('id', $request->responsible_person_id)->get(['name', 'email', 'id']);


            foreach ($employees as $employee) {
                $email_id = $employee->email;

                if (!empty($email_id)) { // Corrected email validation
                    $details = array(
                        'fire_type' => 'Observation Followup',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $mailsubject,
                        'data' => $inspection_details
                    );
                    Mail::to($email_id)->queue(new FireInspection($details));
                }
            }

            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => $mailsubject,
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_details->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('fire/checklist-observation/verification/' . encryptId($id) . '/' . encryptId($observationid)),
                'assigned_user' => decryptId($request->responsible_person_id),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);
            $insert_array = [
                'type' => OBSERVATION_FOLLOWUP,
                'inspection_id' => $inspection_details->observationid,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/checklist-observation/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went Wrong!');
            return redirect(admin_url('fire/checklist-observation/list'));
        }
    }

    public function CAPASubmit(Request $request)
    {

        try {
            $to_status = WAITING_FOR_CAPA_VERIFICATION;
            $id = decryptId($request->id);
            $observationid = decryptId($request->observation_id);

            $inspection_updates = $this->followupObservation->capaUpdate($observationid);
            $this->followupObservation->statusUpdate($observationid, $to_status);
            $inspection_details = $this->followup->selectOne($id, $observationid);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'Observation FollowUp';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Observation",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('fire/checklist-observation/verification/' . encryptId($id) . '/' . encryptId($observationid)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Observation FollowUp';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('fire/checklist-observation/verification/' . encryptId($id));
                $details = array(
                    'fire_type' => 'Observation Followup',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }
            $insert_array = [
                'type' => OBSERVATION_FOLLOWUP,
                'inspection_id' => $inspection_details->observationid,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/checklist-observation/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went Wrong!');
            return redirect(admin_url('fire/checklist-observation/list'));
        }
    }
    public function CAPAVerifySubmit(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $observationid = decryptId($request->observation_id);
            $remarks = $request->remarks;
            $status = $request->has('approved') ? 1 : 0;
            $inspection_details = $this->followup->selectOne($id, $observationid);
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('fire/checklist-observation/verification/' . encryptId($inspection_details->id));
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = WAITING_FOR_L1_VERIFICATION;
                $mailsubject = 'Observation FollowUp';
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
                    'web_link' =>  admin_url('fire/checklist-observation/verification/' . encryptId($id) . '/' . encryptId($observationid)),
                    'assigned_user' => array_to_string($users),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                foreach ($users as $user) {
                    $title = $message;
                    $email_id = getUseremail($user);
                    $url = $web_link;
                    $details = array(
                        'fire_type' => 'Observation FollowUp',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'url' => $url,
                        'data' => $inspection_details
                    );
                    Mail::to($email_id)->queue(new FireInspection($details));
                }
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('fire/checklist-observation/verification/' . encryptId($inspection_details->id));
                $users = $inspection_details->ehs_verify_by;
                $to_status = EHS_OFFICER_REJECTED;
                $mailsubject = 'Observation FollowUp';
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
                    'web_link' =>  admin_url('fire/checklist-observation/list'),
                    'assigned_user' => $users,
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $title = $message;
                $email_id = getUseremail($users);
                $url = $web_link;

                $details = array(
                    'fire_type' => 'Observation FollowUp',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );

                Mail::to($email_id)->queue(new FireInspection($details));
            }
            $inspection_updates = $this->followupObservation->capaVerifySubmit($observationid, $status, $remarks);
            $signature_update = $this->signature->observationFollowUp(OBSERVATION_FOLLOWUP, $observationid);
            $this->followupObservation->statusUpdate($observationid, $to_status);




            $insert_array = [
                'type' => OBSERVATION_FOLLOWUP,
                'inspection_id' => $inspection_details->observationid,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/checklist-observation/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something Went Wrong!');
            return redirect(admin_url('fire/checklist-observation/list'));
        }
    }

    public function levelOneManagerSubmit(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $observationid = decryptId($request->observation_id);
            $remarks = $request->level_one_manager;
            $status = $request->has('approved') ? 1 : 0;
            $inspection_details = $this->followup->selectOne($id, $observationid);
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('fire/checklist-observation/verification/' . encryptId($id));
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = WAITING_FOR_L2_VERIFICATION;

                $mailsubject = 'Observation FollowUp';
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
                    'web_link' =>  admin_url('fire/checklist-observation/verification/' . encryptId($id) . '/' . encryptId($observationid)),
                    'assigned_user' => array_to_string($users),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                foreach ($users as $user) {
                    $title = $message;
                    $email_id = getUseremail($user);
                    $url = $web_link;
                    $details = array(
                        'fire_type' => 'Observation FollowUp',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'url' => $url,
                        'data' => $inspection_details
                    );
                    Mail::to($email_id)->queue(new FireInspection($details));
                }
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/checklist-observation/verification/' . encryptId($id));
                $users = $inspection_details->ehs_verify_by;
                $to_status = L1_MANAGER_REJECTED;

                $mailsubject = 'Observation FollowUp';
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
                    'web_link' =>  admin_url('fire/checklist-observation/list'),
                    'assigned_user' => $users,
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $title = $message;
                $email_id = getUseremail($users);
                $url = $web_link;

                $details = array(
                    'fire_type' => 'Observation FollowUp',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );

                Mail::to($email_id)->queue(new FireInspection($details));
            }
            $inspection_updates = $this->followupObservation->levelOneManagerSubmit($observationid, $remarks);
            $signature_update = $this->signature->observationFollowUp(OBSERVATION_FOLLOWUP, $observationid);
            $this->followupObservation->statusUpdate($observationid, $to_status);




            $insert_array = [
                'type' => OBSERVATION_FOLLOWUP,
                'inspection_id' => $observationid,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/checklist-observation/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went Wrong!');
            return redirect(admin_url('fire/checklist-observation/list'));
        }
    }

    public function levelTwoManagerSubmit(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $observationid = decryptId($request->observation_id);
            $remarks = $request->level_two_manager;
            $status = $request->has('approved') ? 1 : 0;
            $inspection_details = $this->followup->selectOne($id, $observationid);
            if ($status == 1) {
                $message = 'Observation FollowUp Approved Successfully!';
                $web_link =   admin_url('fire/checklist-observation/verification/' . encryptId($id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = INSPECTION_APPROVED;
                $mailsubject = 'Observation FollowUp';
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
                    'web_link' =>  admin_url('fire/checklist-observation/verification/' . encryptId($id) . '/' . encryptId($inspection_details->inspection_id)),
                    'assigned_user' => array_to_string($users),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                foreach ($users as $user) {
                    $title = $message;
                    $email_id = getUseremail($user);
                    $url = $web_link;
                    $details = array(
                        'fire_type' => 'Observation FollowUp',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'url' => $url,
                        'data' => $inspection_details
                    );
                    Mail::to($email_id)->queue(new FireInspection($details));
                }
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/checklist-observation/verification/' . encryptId($id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = L2_MANAGER_REJECTED;

                $mailsubject = 'Observation FollowUp';
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
                    'web_link' =>  admin_url('fire/checklist-observation/list'),
                    'assigned_user' => $users,
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $title = $message;
                $email_id = getUseremail($users);
                $url = $web_link;

                $details = array(
                    'fire_type' => 'Observation FollowUp',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );

                Mail::to($email_id)->queue(new FireInspection($details));
            }
            $inspection_updates = $this->followupObservation->levelTwoManagerSubmit($observationid, $remarks);
            $signature_update = $this->signature->observationFollowUp(OBSERVATION_FOLLOWUP, $observationid);
            $this->followupObservation->statusUpdate($observationid, $to_status);




            $insert_array = [
                'type' => OBSERVATION_FOLLOWUP,
                'inspection_id' => $observationid,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/checklist-observation/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went Wrong!');
            return redirect(admin_url('fire/checklist-observation/list'));
        }
    }
    public function statusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $this->followup->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'checklist Observation status changed'], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }


    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $observationid = decryptId($request->observationid);
            if (Auth::check()) {
                $observation = $this->followup->selectOne(($id), ($observationid));
                $status_log = $this->statusLog->selectOne($observationid, OBSERVATION_FOLLOWUP);
            }
            $data = [
                'observation' => $observation,
                'status_log' => $status_log,
                'pagetitle' => "checklist observation follow up sheet.",
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

            $html = view('inspection.fire.observationFollowup.viewpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "checklist observation follow up sheet.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/checklist-observation/list'));
        }
    }

    public function ExportExcel(Request $request)
    {

        try {
            $allData = $this->followup->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Observation Id',
                'Inspection Type',
                'Serial Number',
                'Date of Inspection',
                'Approve Status',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                switch ($data->inspection_type) {
                    case HOOTER_INSPECTION:
                        $inspectionType = 'Hooter Inspection';
                        break;
                    case EMERGENCY_LIGHT_INSPECTION:
                        $inspectionType = 'Emergency Light Inspection';
                        break;
                    case MONTHLY_FIRE_PUMP:
                        $inspectionType = 'Monthly Fire Pump';
                        break;
                    case FIRE_MOCK_DRILL_INSPECION:
                        $inspectionType = 'Fire Mock Drill Inspection';
                        break;
                    case FIRE_EXTINGUISHER_INSPECTION:
                        $inspectionType = 'Fire Extinguisher Inspection';
                        break;
                    case ISOLATION_VALVE_INSPECTION:
                        $inspectionType = 'Isolation Valve Inspection';
                        break;
                    case FIRE_ALARM_INSPECTION:
                        $inspectionType = 'Fire Alarm Inspection';
                        break;
                    case SPRINKLAR_SYSTEM_INSPECTION:
                        $inspectionType = 'Sprinkler System Inspection';
                        break;
                    case SAND_BUCKET_INSPECTION:
                        $inspectionType = 'Sand Bucket Inspection';
                        break;
                    case DETECTOR_INSPECTION:
                        $inspectionType = 'Detector Inspection';
                        break;
                    case FIRE_PA_SYSTEM_INSPECTION:
                        $inspectionType = 'Fire PA System Inspection';
                        break;
                    case DAILY_FIRE_PUMP:
                        $inspectionType = 'Daily Fire Pump';
                        break;
                    case CO_TYPE_FIRE_EXTINGUISHER_INSPECTION:
                        $inspectionType = 'CO Type Fire Extinguisher Inspection';
                        break;
                    case HOSE_BOX_INSPECTION:
                        $inspectionType = 'Hose Box Inspection';
                        break;
                    case CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION:
                        $inspectionType = 'Cartridge Type Fire Extinguisher Inspection';
                        break;
                    case HOSE_REEL_INSPECTION:
                        $inspectionType = 'Hose Reel Inspection';
                        break;
                    case FIRE_MODULAR_INSPECTION:
                        $inspectionType = 'Fire Modular Inspection';
                        break;
                    case HYDRANT_RISER:
                        $inspectionType = 'Hydrant Riser';
                        break;
                    case OBSERVATION_FOLLOWUP:
                        $inspectionType = 'Observation Follow-up';
                        break;
                    case OBSERVATION_FOLLOWUP:
                        $inspectionType = 'Observation Follow-up';
                        break;
                    case GEMBA_WALK:
                        $inspectionType = 'Observation Follow-up';
                        break;
                    default:
                        $inspectionType = 'Unknown';
                }

                $export = [];
                $export[] =  $i;
                $export[] =  $data->observation_id;
                $export[] =  $inspectionType;
                $export[] =  $data->sr_no;
                $export[] = Displaydateformat($data->date_of_inspection);
                $export[] =  getInspectionstatus($data->observation_status);
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);
                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Checklist observation.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/checklist-observation/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            ini_set("pcre.backtrack_limit", "5000000");

            $allData = $this->followup->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Observation Id',
                'Inspection Type',
                'Serial Number',
                'Date of Inspection',
                'Approve Status',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Checklist observation",
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

            $view = view('inspection.fire.observationFollowup.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Checklist observation.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/checklist-observation/list'));
        }
    }

    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $observationid = decryptId($request->observationid);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $data = $this->followup->selectOne($id, $observationid);
            $document_no = $this->static_docno->selectOne($data->document_reference_id);
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
            $sheet->mergeCells("G{$currentRow}:S" . ($currentRow + 2));
            $sheet->setCellValue("G{$currentRow}", "CHECKLIST OBSERVATION FOLLOW UP SHEET");
            $sheet->getStyle("G{$currentRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $sheet->mergeCells("T$currentRow:V$currentRow")->setCellValue("T$currentRow", 'Doc. No.');
            $sheet->mergeCells("T" . ($currentRow + 1) . ":V" . ($currentRow + 1))->setCellValue("T" . ($currentRow + 1), 'Issue Dt.');
            $sheet->mergeCells("T" . ($currentRow + 2) . ":V" . ($currentRow + 2))->setCellValue("T" . ($currentRow + 2), 'Rev. & Dt.');

            $sheet->mergeCells("W$currentRow:X$currentRow")->setCellValue("W$currentRow", $document_no->doc_no);
            $sheet->mergeCells("W" . ($currentRow + 1) . ":X" . ($currentRow + 1))->setCellValue("W" . ($currentRow + 1), Displaydateformat($document_no->issue_date));
            $sheet->mergeCells("W" . ($currentRow + 2) . ":X" . ($currentRow + 2))->setCellValue("W" . ($currentRow + 2), $document_no->rev_dt);

            $sheet->getStyle("T$currentRow:X" . ($currentRow + 2))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font' => ['bold' => true],
            ]);


            $headerRow = $currentRow + 3;
            $secondRow = $headerRow + 1;


            // First-level vertical headers
            $sheet->mergeCells("A$headerRow:A$secondRow")->setCellValue("A$headerRow", "SERIAL NO");
            $sheet->mergeCells("B$headerRow:B$secondRow")->setCellValue("B$headerRow", "UNIT");
            $sheet->mergeCells("C$headerRow:C$secondRow")->setCellValue("C$headerRow", "DEPARTMENT / LOCATION");
            $sheet->mergeCells("D$headerRow:D$secondRow")->setCellValue("D$headerRow", "NAME OF EQUIPMENT");


            $sheet->mergeCells("E$headerRow:E$secondRow")->setCellValue("E$headerRow", "RESOURCE CODE OF EQUIPMENT");


            $sheet->mergeCells("F$headerRow:H$secondRow")->setCellValue("F$headerRow", "OBSERVATION ");
            $sheet->mergeCells("I$headerRow:I$secondRow")->setCellValue("I$headerRow", "DATE OF OBSERVATION / INSPECTION");
            $sheet->mergeCells("J$headerRow:J$secondRow")->setCellValue("J$headerRow", "OBSERVATION OF THE MONTH");

            $sheet->mergeCells("K$headerRow:Q$headerRow")->setCellValue("K$headerRow", "WHY WHY ANALYSIS");
            $sheet->mergeCells("K$secondRow:K$secondRow")->setCellValue("K$secondRow", "WHY 1");
            $sheet->mergeCells("L$secondRow:L$secondRow")->setCellValue("L$secondRow", "WHY 2");
            $sheet->mergeCells("M$secondRow:M$secondRow")->setCellValue("M$secondRow", "WHY 3");
            $sheet->mergeCells("N$secondRow:N$secondRow")->setCellValue("N$secondRow", "WHY 4");
            $sheet->mergeCells("O$secondRow:O$secondRow")->setCellValue("O$secondRow", "WHY 5");
            $sheet->mergeCells("P$secondRow:P$secondRow")->setCellValue("P$secondRow", "MAIN ROOT CAUSE");
            $sheet->mergeCells("Q$secondRow:Q$secondRow")->setCellValue("Q$secondRow", "CORRECTIVE & PREVENTIVE ACTION TAKEN");
            $sheet->mergeCells("R$headerRow:R$secondRow")->setCellValue("R$headerRow", "DATE OF CORRECTIVE & PREVENTIVE ACTION TAKEN");
            $sheet->mergeCells("S$headerRow:S$secondRow")->setCellValue("S$headerRow", "RESPONSIBILITY");
            $sheet->mergeCells("T$headerRow:T$secondRow")->setCellValue("T$headerRow", "TIMELINE");
            $sheet->mergeCells("U$headerRow:U$secondRow")->setCellValue("U$headerRow", "DATE OF CLOSURE ");
            $sheet->mergeCells("V$headerRow:V$secondRow")->setCellValue("V$headerRow", "STATUS");
            $sheet->mergeCells("W$headerRow:X$secondRow")->setCellValue("W$headerRow", "REMARK");
            foreach (range('A', 'X') as $columnID) {
                $sheet->getColumnDimension($columnID)->setWidth(20);
            }
            $sheet->getStyle("A$headerRow:X$secondRow")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ],
                'font' => ['bold' => true],
            ]);

            $dataStartRow = $secondRow + 1;

            $sheet->setCellValue("A$dataStartRow", $data->sr_no ?? '');
            $sheet->setCellValue("B$dataStartRow", getunitname($data->unit_id) ?? '');
            $sheet->setCellValue("C$dataStartRow", getDepartment($data->department_id) ?? '');
            $sheet->setCellValue("D$dataStartRow", $data->equipment_name ?? '');
            $sheet->setCellValue("E$dataStartRow", $data->equipment_code ?? '');
            $sheet->mergeCells("F$dataStartRow:H$dataStartRow")->setCellValue("F$dataStartRow", $data->observation ?? '');


            $sheet->setCellValue("I$dataStartRow", displaydateformat($data->date ?? ''));
            $sheet->setCellValue("J$dataStartRow", $data->month ?? '');

            $sheet->setCellValue("K$dataStartRow", $data->why_1 ?? '-');
            $sheet->setCellValue("L$dataStartRow", $data->why_2 ?? '-');
            $sheet->setCellValue("M$dataStartRow", $data->why_3 ?? '-');
            $sheet->setCellValue("N$dataStartRow", $data->why_4 ?? '-');
            $sheet->setCellValue("O$dataStartRow", $data->why_5 ?? '-');
            $sheet->setCellValue("P$dataStartRow", $data->main_root_cause ?? '-');
            $sheet->setCellValue("Q$dataStartRow", $data->corrective_action ?? '-');

            $sheet->setCellValue("R$dataStartRow", displaydateformat($data->ehs_verified_date) ?? '-');
            $sheet->setCellValue("S$dataStartRow", getusername($data->responsible_person_id) ?? '-');
            $sheet->setCellValue("T$dataStartRow", displaydateformat($data->target_date) ?? '-');
            $sheet->setCellValue("U$dataStartRow", displaydateformat($data->closed_date) ?? '-');
            $sheet->setCellValue("V$dataStartRow", getActiveOrInactive($data->status) ?? '-');
            $sheet->mergeCells("w$dataStartRow:x$dataStartRow")->setCellValue("w$dataStartRow", $data->remarks ?? '');



            $sheet->getStyle("A$dataStartRow:X$dataStartRow")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ],
                'font' => ['bold' => true],
            ]);

            $row = $dataStartRow;

            $signatureRowStart = $dataStartRow + 1;
            $sheet->getRowDimension($signatureRowStart)->setRowHeight(30);

            // Prepared By
            $sheet->mergeCells("A{$signatureRowStart}:H{$signatureRowStart}");
            $sheet->getStyle("A{$signatureRowStart}:H{$signatureRowStart}")->applyFromArray([
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);

            $richText = new RichText();
            $name = getUsername($data->inspection_created_by);

            if (!empty($name)) {
                $richText->createTextRun("Prepared By :" . $name)->getFont()->setBold(true);
            } else {
                $richText->createTextRun("Inspection has not been Prepared Yet")->getFont()->setBold(true);
            }

            $sheet->getCell("A{$signatureRowStart}")->setValue($richText);

            // Verified By
            $sheet->mergeCells("I{$signatureRowStart}:N{$signatureRowStart}");
            $sheet->getStyle("I{$signatureRowStart}:N{$signatureRowStart}")->applyFromArray([
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);

            $richText = new RichText();
            $name = getUsername($data->responsible_person_id);

            if (!empty($name)) {
                $richText->createTextRun("Verified By :" . $name)->getFont()->setBold(true);
            } else {
                $richText->createTextRun("Inspection has not been Verified Yet")->getFont()->setBold(true);
            }

            $sheet->getCell("I{$signatureRowStart}")->setValue($richText);

            // Approved By
            $sheet->mergeCells("O{$signatureRowStart}:X{$signatureRowStart}");
            $sheet->getStyle("O{$signatureRowStart}:X{$signatureRowStart}")->applyFromArray([
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);
            $richText = new RichText();
            $name = getUsername($data->approved_by);

            if (!empty($name)) {
                $richText->createTextRun("Approved By :" . $name)->getFont()->setBold(true);
            } else {
                $richText->createTextRun("Inspection has not been Approved Yet")->getFont()->setBold(true);
            }

            $sheet->getCell("O{$signatureRowStart}")->setValue($richText);
            $fileName = 'checklist_follow_up.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/checklist-observation/list'));
        }
    }
}
