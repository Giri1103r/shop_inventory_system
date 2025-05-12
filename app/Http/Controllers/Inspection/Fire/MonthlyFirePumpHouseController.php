<?php

namespace App\Http\Controllers\Inspection\Fire;

use Exception;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Master\Location;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Inspection\Master\Shift;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Mail\Inspection\Fire\FireInspection;
use App\Models\Inspection\Fire\FireStatusLog;
use App\Models\Inspection\Fire\FireSignatureUpload;
use App\Models\Inspection\Fire\MonthlyFirePumpHouseInspection;
use App\Models\Inspection\InspectionStaticDocno;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class MonthlyFirePumpHouseController extends Controller
{
    private $monthlyfirepump;
    private $shift;
    private $location;
    private $unit;
    private $frequency;
    private $statusLog;
    private $signature;
    private $document_reference;

    public function __construct()
    {
        $this->monthlyfirepump = new MonthlyFirePumpHouseInspection();
        $this->location = new Location();
        $this->unit = new Unit();
        $this->statusLog = new FireStatusLog();
        $this->signature = new FireSignatureUpload();
        $this->shift = new Shift();
        $this->document_reference = new InspectionStaticDocno();
    }
    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->monthlyfirepump->list();
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
                            $btn = '<a href="' . admin_url('fire/monthly-fire-pump-house-inspection/view/' . encryptId($row->inspection_id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            if ($row->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/monthly-fire-pump-house-inspection/verification/' . encryptId($row->inspection_id)) . '/ehs" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if (($row->inspection_status == WAITING_FOR_CAPA_ACTION || $row->inspection_status == L2_MANAGER_REJECTED || $row->inspection_status == EHS_OFFICER_REJECTED || $row->inspection_status == L1_MANAGER_REJECTED) && (CheckUserRole(ROLE_FIRE_ASSOCIATES) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/monthly-fire-pump-house-inspection/verification/' . encryptId($row->inspection_id)) . '/capa" class="" title="' . __('inspection.capa_action') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_CAPA_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/monthly-fire-pump-house-inspection/verification/' . encryptId($row->inspection_id)) . '/ehsVerify" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L1_VERIFICATION && (CheckUserRole(ROLE_L1_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/monthly-fire-pump-house-inspection/verification/' . encryptId($row->inspection_id)) . '/level-one-manager" class="" title="' . __('inspection.l1_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L2_VERIFICATION && (CheckUserRole(ROLE_L2_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('fire/monthly-fire-pump-house-inspection/verification/' . encryptId($row->inspection_id)) . '/level-two-manager" class="" title="' . __('inspection.l2_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            $btn .= '<a href="' . admin_url('fire/monthly-fire-pump-house-inspection/exportViewPdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                                    </a>';

                            $btn .= '<a href="' . admin_url('fire/monthly-fire-pump-house-inspection/generalExcel/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="Excel">
                                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                                    </a>';

                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'inspection_status', 'date_of_inspection'])
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

        $unit = $this->unit->getUnit();
        $shifts = $this->shift->getShiftname();

        $data = array(
            'units' => $unit,
            'shifts' => $shifts,
        );
        return view('inspection.fire.monthly_fire_pump_house.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $checklistQuestions = getCheckListQuestion(MONTHLY_FIRE_PUMPHOUSE_INSPECTION_CHECKLIST);

            $options =  getoption(MONTHLY_FIRE_PUMPHOUSE_INSPECTION_CHECKLIST);

            $getoption = string_to_array($options->type);
            $shifts = $this->shift->getShiftname();
            $unit = $this->unit->getUnit();
            $document_no = $this->document_reference->selectUsingName('MonthlyFirePumpHouseInspection');
            if (count($checklistQuestions) <= 0) {
                Session::flash('error', __('inspection.checklist_add'));
                return redirect()->back();
            }
            $data = array(
                'checklist_details' => $checklistQuestions,
                'getoption' => $getoption,
                'shifts' => $shifts,
                'units' => $unit,
                'document_no' => $document_no,
            );
            return view('inspection.fire.monthly_fire_pump_house.add', $data);
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/monthly-fire-pump-house-inspection/list'));
        }
    }

    public function Store(Request $request)
    {
        try {
            $monthly_fire_inspection = $this->monthlyfirepump->store();
            $id = $monthly_fire_inspection->id;
            $inspection_type = MONTHLY_FIRE_PUMP;

            $signature_update = $this->signature->CheckedBySignature($id,$inspection_type);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'Monthly Fire Pump House Inspection';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Monthly Fire Pump House Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $monthly_fire_inspection->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('fire/monthly-fire-pump-house-inspection/view/' . encryptId($monthly_fire_inspection->id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Monthly Fire Pump House Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('fire/monthly-fire-pump-house-inspection/verification/' . encryptId($id) . '/ehs');
                $details = array(
                    'fire_type' => 'Monthly Fire Pump House Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $monthly_fire_inspection
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => MONTHLY_FIRE_PUMP,
                'inspection_id' => $monthly_fire_inspection->id,
                'from_status' => 0,
                'to_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'created_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.created_msg'));
            if ($monthly_fire_inspection->observation_needed == 1) {
                return redirect(admin_url('fire/checklist-observation/add/' . encryptId($inspection_type) . '/' . encryptId($id)));
            } else {
                return redirect(admin_url('fire/monthly-fire-pump-house-inspection/list'));
            }
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/monthly-fire-pump-house-inspection/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->monthlyfirepump->selectOne($id);
            $status_log = $this->statusLog->selectOne($id, MONTHLY_FIRE_PUMP);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);
            $data = [
                'inspection_details' => $inspection_details,
                'status_log' => $status_log,
                'document_no' => $document_no,
            ];
            return view('inspection.fire.monthly_fire_pump_house.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/monthly-fire-pump-house-inspection/list'));
        }
    }

    public function Approvals(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->monthlyfirepump->selectOne($id);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);
            $data = [
                'inspection_details' => $inspection_details,
                'document_no' => $document_no,
            ];
            return view('inspection.fire.monthly_fire_pump_house.approve', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/monthly-fire-pump-house-inspection/list'));
        }
    }

    public function EHSOfficerSubmit(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $inspection_updates = $this->monthlyfirepump->EHSOfficerUpdate($id);
            $signature_update = $this->signature->signatureUpload(MONTHLY_FIRE_PUMP);
            $inspection_details = $this->monthlyfirepump->selectOne($id);
            if ($request->is_passed == 1) {
                $message = 'Monthly Fire Pump House Inspeciton Approved Successfully';
                $web_link =   admin_url('fire/monthly-fire-pump-house-inspection/verification/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
            } else {
                $message = 'Inspection Recommended for the CAPA Action';
                $web_link =   admin_url('fire/monthly-fire-pump-house-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = WAITING_FOR_CAPA_ACTION;
            }
            $userIds = [
                'users' => $inspection_details->created_by,
            ];
            $mailsubject = 'Monthly Fire Pump House Inspection';
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
            $url = admin_url('fire/monthly-fire-pump-house-inspection/verification/' . encryptId($id) . '/capa');
            $details = array(
                'fire_type' => 'Monthly Fire Pump House Inspection',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new FireInspection($details));

            $insert_array = [
                'type' => MONTHLY_FIRE_PUMP,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/monthly-fire-pump-house-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went Wrong!');
            return redirect(admin_url('fire/monthly-fire-pump-house-inspection/list'));
        }
    }

    public function CAPASubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $safety_gallery_inspection = $this->monthlyfirepump->capaSubmit($id);
            $inspection_details = $this->monthlyfirepump->selectOne($id);
            $signature_update = $this->signature->signatureUpload(SAFETY_GALLERY_INSPECTION);
            $ehsOfficers = $inspection_details->verified_by;
            $userIds = [
                'users' => $ehsOfficers,
            ];
            $mailsubject = 'Monthly Fire Pump House Inspection';
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
                'web_link' =>  admin_url('fire/monthly-fire-pump-house-inspection/verification/' . encryptId($inspection_details->id)) . '/ehsVerify',
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $user = $inspection_details->verified_by;
            $email_id = getUseremail($user);
            $url = admin_url('fire/monthly-fire-pump-house-inspection/verification/' . encryptId($id) . '/ehs');
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
                'type' => MONTHLY_FIRE_PUMP,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_ACTION,
                'to_status' => WAITING_FOR_CAPA_VERIFICATION,
                'created_by' => Auth::id(),
                'remarks' => $request->capa_remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/monthly-fire-pump-house-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/monthly-fire-pump-house-inspection/list'));
        }
    }

    public function CAPAVerifySubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->remarks;
            $safety_gallery_inspection = $this->monthlyfirepump->capaVerifySubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(SAFETY_GALLERY_INSPECTION);
            $inspection_details = $this->monthlyfirepump->selectOne($id);
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('fire/monthly-fire-pump-house-inspection/verification/' . encryptId($inspection_details->id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by]);
                $to_status = WAITING_FOR_L1_VERIFICATION;
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('fire/monthly-fire-pump-house-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = EHS_OFFICER_REJECTED;
            }

            $mailsubject = 'Monthly Fire Pump House Inspection';
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
                    'fire_type' => 'Monthly Fire Pump House Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => MONTHLY_FIRE_PUMP,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/monthly-fire-pump-house-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/monthly-fire-pump-house-inspection/list'));
        }
    }

    public function levelOneManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_one_manager;
            $safety_gallery_inspection = $this->monthlyfirepump->levelOneManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(SAFETY_GALLERY_INSPECTION);
            $inspection_details = $this->monthlyfirepump->selectOne($id);
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('fire/monthly-fire-pump-house-inspection/verification/' . encryptId($inspection_details->id) . '/level-two-manager');
                $user = GetLevelTwoManager();
                $users = $user ? $user->pluck('id')->toArray() : [];
                $users = array_merge($users, [$inspection_details->created_by], [$inspection_details->verified_by]);
                $to_status = WAITING_FOR_L2_VERIFICATION;
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/monthly-fire-pump-house-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = [
                    'users' => $inspection_details->created_by,
                ];

                $to_status = L1_MANAGER_REJECTED;
            }

            $mailsubject = 'Monthly Fire Pump House Inspection';
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
                    'fire_type' => 'Monthly Fire Pump House Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => MONTHLY_FIRE_PUMP,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L1_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_one_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/monthly-fire-pump-house-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/monthly-fire-pump-house-inspection/list'));
        }
    }

    public function levelTwoManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_two_manager;
            $safety_gallery_inspection = $this->monthlyfirepump->levelTwoManagerSubmit($id, $status, $remarks);
            $signature_update = $this->signature->signatureUpload(MONTHLY_FIRE_PUMP);
            $inspection_details = $this->monthlyfirepump->selectOne($id);
            if ($status == 1) {
                $message = 'Monthly Fire Pump House Inspeciton Approved Successfully!';
                $web_link =   admin_url('fire/monthly-fire-pump-house-inspection/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('fire/monthly-fire-pump-house-inspection/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = L2_MANAGER_REJECTED;
                $users = array_merge([$inspection_details->created_by], [$inspection_details->verified_by], [$inspection_details->l1_manager_verified_by]);
            }

            $mailsubject = 'Monthly Fire Pump House Inspection';
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
                    'fire_type' => 'Monthly Fire Pump House Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => MONTHLY_FIRE_PUMP,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L2_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_two_manager,
            ];
            $this->statusLog->create($insert_array);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('fire/monthly-fire-pump-house-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('fire/monthly-fire-pump-house-inspection/list'));
        }
    }

    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->monthlyfirepump->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $currentRow = 1;

            foreach (range('A', 'P') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            foreach ($allData as $inspection) {
                $startRow = $currentRow;
                $user_responses = json_decode($inspection->responses, true);
                $inspection_type = MONTHLY_FIRE_PUMP;

                $createdBySig = GetFireSignature($inspection->checked_by, $inspection->fire_id, $inspection_type);
                $verifiedBySig = GetFireSignature($inspection->updated_by, $inspection->fire_id, $inspection_type);
                $approvedBySig = GetFireSignature($inspection->approved_by, $inspection->fire_id, $inspection_type);
                $document_no = $this->document_reference->selectOne($inspection->document_reference_id);

                $leftLogoPath = public_path('assets/images/logo-dark.png');

                if (file_exists($leftLogoPath)) {
                    $sheet->mergeCells("A$currentRow:D" . ($currentRow + 2));
                    $drawing = new Drawing();
                    $drawing->setPath($leftLogoPath);
                    $drawing->setCoordinates("A{$currentRow}");
                    $drawing->setOffsetX(80);
                    $drawing->setHeight(70);
                    $drawing->setWorksheet($sheet);
                    $sheet->getStyle("A$currentRow:D" . ($currentRow + 2))->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                }

                $sheet->mergeCells("L{$currentRow}:M{$currentRow}")->setCellValue("L{$currentRow}", 'Doc. No.');
                $sheet->mergeCells("L" . ($currentRow + 1) . ":M" . ($currentRow + 1))->setCellValue("L" . ($currentRow + 1), 'Issue Dt.');
                $sheet->mergeCells("L" . ($currentRow + 2) . ":M" . ($currentRow + 2))->setCellValue("L" . ($currentRow + 2), 'Rev. & Dt.');
                $sheet->mergeCells("N{$currentRow}:P{$currentRow}")->setCellValue("N{$currentRow}", $document_no->doc_no ?? '-');
                $sheet->mergeCells("N" . ($currentRow + 1) . ":P" . ($currentRow + 1))->setCellValue("N" . ($currentRow + 1), Displaydateformat($document_no->issue_date ?? null));
                $sheet->mergeCells("N" . ($currentRow + 2) . ":P" . ($currentRow + 2))->setCellValue("N" . ($currentRow + 2), $document_no->rev_dt ?? '-');
                $sheet->getStyle("L{$currentRow}:P" . ($currentRow + 2))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->mergeCells("E{$currentRow}:K" . ($currentRow + 2));
                $sheet->setCellValue("E{$currentRow}", "MONTHLY FIRE PUMP HOUSE PHYSICAL INSPECTION CHECKLIST");
                $sheet->getStyle("E{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $currentRow += 3;

                $sheet->mergeCells("A$currentRow:E$currentRow")->setCellValue("A$currentRow", "DATE OF INSPECTION :- " . Displaydateformat($inspection->date_of_inspection));
                $sheet->mergeCells("F$currentRow:K$currentRow")->setCellValue("F$currentRow", "UNIT :- " . getUnitname($inspection->unit));
                $sheet->mergeCells("L$currentRow:P$currentRow")->setCellValue("L$currentRow", "SHIFT :- " . ($inspection->shift));
                $sheet->getStyle("A$currentRow:P$currentRow")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->getRowDimension($currentRow)->setRowHeight(30);

                $currentRow++;

                $sheet->mergeCells("A$currentRow:E$currentRow")->setCellValue("A$currentRow", "SR. NO.");
                $sheet->mergeCells("F$currentRow:K$currentRow")->setCellValue("F$currentRow", "CHECK ITEMS");
                $sheet->mergeCells("L$currentRow:M$currentRow")->setCellValue("L$currentRow", "STATUS (YES/NO)");
                $sheet->mergeCells("N$currentRow:P$currentRow")->setCellValue("N$currentRow", "REMARK");
                $sheet->getStyle("A$currentRow:P$currentRow")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle("F$currentRow:K$currentRow")->getAlignment()->setWrapText(true);

                $sheet->getRowDimension($currentRow)->setRowHeight(20);

                $currentRow++;

                $srNo = 1;
                foreach ($user_responses as $questionGroup) {
                    foreach ($questionGroup as $questionId => $response) {
                        $sheet->mergeCells("A$currentRow:E$currentRow")->setCellValue("A$currentRow", $srNo);
                        $sheet->mergeCells("F$currentRow:K$currentRow")->setCellValue("F$currentRow", GetChecklistTypeDate($questionId));

                        $statusSymbol = '-';
                        $statusColor = null;
                        $responseText = $response['response'] ?? '';

                        if ($responseText === 'YES') {
                            $statusSymbol = '✓';
                            $statusColor = '008000';
                        } elseif (in_array($responseText, ['NO', 'N/A'])) {
                            $statusSymbol = 'X';
                            $statusColor = 'FF0000';
                        }

                        $sheet->mergeCells("L$currentRow:M$currentRow")->setCellValue("L$currentRow", $statusSymbol);
                        $sheet->mergeCells("N$currentRow:P$currentRow")->setCellValue("N$currentRow", $response['remark'] ?? '-');

                        if ($statusColor) {
                            $sheet->getStyle("L$currentRow")->applyFromArray([
                                'font' => ['color' => ['rgb' => $statusColor]],
                            ]);
                        }

                        $sheet->getStyle("A$currentRow:P$currentRow")->applyFromArray([
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        ]);

                        $currentRow++;
                        $srNo++;
                    }
                }

                $sheet->getRowDimension($currentRow)->setRowHeight(80);

                $sheet->mergeCells("A{$currentRow}:E{$currentRow}")->setCellValue("A{$currentRow}", "CHECKED BY: " . getUsername($inspection->checked_by));
                $sheet->mergeCells("F{$currentRow}:K{$currentRow}")->setCellValue("F{$currentRow}", "VERIFIED BY: " . getUsername($inspection->updated_by));
                $sheet->mergeCells("L{$currentRow}:P{$currentRow}")->setCellValue("L{$currentRow}", "APPROVED BY: " . getUsername($inspection->approved_by));

                $sheet->getStyle("A{$currentRow}:P{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_BOTTOM],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                if (file_exists($createdBySig)) {
                    $drawing = new Drawing();
                    $drawing->setPath($createdBySig);
                    $drawing->setCoordinates("B{$currentRow}");
                    $drawing->setOffsetX(30);
                    $drawing->setOffsetY(10);
                    $drawing->setWidth(90);
                    $drawing->setWorksheet($sheet);
                } else {
                    $sheet->setCellValue("A{$currentRow}", "CHECKED BY:- \nInspection not yet started");
                }

                if (file_exists($verifiedBySig)) {
                    $drawing = new Drawing();
                    $drawing->setPath($verifiedBySig);
                    $drawing->setCoordinates("H{$currentRow}");
                    $drawing->setOffsetX(30);
                    $drawing->setOffsetY(10);
                    $drawing->setWidth(90);
                    $drawing->setWorksheet($sheet);
                } else {
                    $sheet->setCellValue("F{$currentRow}", "VERIFIED BY:- \nInspection not yet started");
                }

                if (file_exists($approvedBySig)) {
                    $drawing = new Drawing();
                    $drawing->setPath($approvedBySig);
                    $drawing->setCoordinates("N{$currentRow}");
                    $drawing->setOffsetX(30);
                    $drawing->setOffsetY(10);
                    $drawing->setWidth(90);
                    $drawing->setWorksheet($sheet);
                } else {
                    $sheet->setCellValue("L{$currentRow}", "APPROVED BY:- \nApproval pending");
                }

                $lastRow = $currentRow;

                $sheet->getStyle("A{$startRow}:P{$lastRow}")->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $currentRow += 4;

            }

            $fileName = 'Monthly Fire Pump Inspection.xlsx';
            $filePath = storage_path("app/public/{$fileName}");

            $writer = new Xlsx($spreadsheet);
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            report($e);
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->monthlyfirepump->exportdata();
            $inspection_type = MONTHLY_FIRE_PUMP;
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $data = array(
                'inspection_type' => $inspection_type,
                'content' => $allData,
                'pagetitle' => "Monthly Fire Pump House Inspection",
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

            $view = view('inspection.fire.monthly_fire_pump_house.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Monthly Fire Pump House Inspection.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/monthly-fire-pump-house-inspection/list'));
        }
    }


    public function exportViewPdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $status_log = $this->statusLog->selectOne($id, MONTHLY_FIRE_PUMP);
                $forklift_details = $this->monthlyfirepump->selectOne($id);
                $document_no = $this->document_reference->selectOne($forklift_details->document_reference_id);

                $data = [
                    'status_log' => $status_log,
                    'forklift_details' => $forklift_details,
                    'pagetitle' => "Monthly Fire Pump House Inspection",
                    'document_no' => $document_no,
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

            $html = view('inspection.fire.monthly_fire_pump_house.viewPdf',$data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Monthly Fire Pump House Inspection.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }

    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $monthlyfirepump = $this->monthlyfirepump->selectOne($id);
            $user_response = json_decode($monthlyfirepump->responses, true);
            $inspection_type = MONTHLY_FIRE_PUMP;
            $inspection_created_by = GetFireSignature($monthlyfirepump->created_by, $monthlyfirepump->id, $inspection_type);

            $inspection_verified_by = GetFireSignature($monthlyfirepump->updated_by, $monthlyfirepump->id, $inspection_type);
            $inspection_approved_by = GetSafetySignature($monthlyfirepump->approved_by, $monthlyfirepump->id, $inspection_type);
            $document_no = $this->document_reference->selectOne($monthlyfirepump->document_reference_id);

            $sheet->getDefaultColumnDimension()->setWidth(14);
            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            // Logo Area
            $sheet->mergeCells("A1:C3");
            $sheet->getStyle("A1:C3")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);

            $logoPath = public_path('assets/images/logo-dark.png');
            if (file_exists($logoPath)) {
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setPath($logoPath);
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(15);
                $drawing->setOffsetY(5);
                $drawing->setWidth(90);
                $drawing->setHeight(50);
                $drawing->setWorksheet($sheet);
            }

            // Title Area
            $sheet->mergeCells("D1:H3")->setCellValue("D1", "MONTHLY FIRE PUMP HOUSE PHYSICAL INSPECTION CHECKLIST");
            $sheet->getStyle("D1")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            // Document Details
            $sheet->mergeCells("I1:K1")->setCellValue("I1", 'Doc. No.');
            $sheet->mergeCells("I2:K2")->setCellValue("I2", 'Issue Dt.');
            $sheet->mergeCells("I3:K3")->setCellValue("I3", 'Rev. & Dt.');
            $sheet->mergeCells("L1:M1")->setCellValue("L1", $document_no->doc_no);
            $sheet->mergeCells("L2:M2")->setCellValue("L2", Displaydateformat($document_no->issue_date));
            $sheet->mergeCells("L3:M3")->setCellValue("L3", $document_no->rev_dt);

            $sheet->getStyle("I1:M3")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // Inspection Basic Info
            $sheet->mergeCells("A4:D4")->setCellValue("A4", "DATE OF INSPECTION :- " . Displaydateformat($monthlyfirepump->date_of_inspection));
            $sheet->mergeCells("E4:H4")->setCellValue("E4", "UNIT :- " . getUnitname($monthlyfirepump->unit ?? '-'));
            $sheet->mergeCells("I4:M4")->setCellValue("I4", "SHIFT :- " . getShiftname($monthlyfirepump->shift));
            $sheet->getStyle("A4:M4")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // Table Header
            $sheet->mergeCells("A5:B5")->setCellValue("A5", "SR. NO.");
            $sheet->mergeCells("C5:I5")->setCellValue("C5", "CHECK ITEMS");
            $sheet->mergeCells("J5:K5")->setCellValue("J5", "STATUS");
            $sheet->mergeCells("L5:M5")->setCellValue("L5", "REMARK");

            $sheet->getStyle("A5:M5")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $row = 6;
            $srNo = 1;
            foreach ($user_response as $questions) {
                foreach ($questions as $questionId => $questions) {

                    $sheet->mergeCells("A{$row}:B{$row}")->setCellValue("A{$row}", $srNo);
                    $sheet->mergeCells("C{$row}:I{$row}")->setCellValue("C{$row}", GetChecklistTypeDate($questionId));

                    $statusSymbol = '-';
                    $statusColor = null;
                    $responseText = $questions['response'] ?? '';

                    if ($responseText === 'YES') {
                        $statusSymbol = '✓';
                        $statusColor = '008000';
                    } elseif (in_array($responseText, ['NO', 'N/A'])) {
                        $statusSymbol = 'X';
                        $statusColor = 'FF0000';
                    } else {
                        $statusSymbol = '-';
                    }

                    $sheet->mergeCells("J{$row}:K{$row}")->setCellValue("J{$row}", $statusSymbol);
                    $sheet->mergeCells("L{$row}:M{$row}")->setCellValue("L{$row}", $questions['remark'] ?? '-'); // remark not remarks

                    if ($statusColor) {
                        $sheet->getStyle("J{$row}")->applyFromArray([
                            'font' => ['color' => ['rgb' => $statusColor]],
                        ]);
                    }

                    $sheet->getStyle("A{$row}:M{$row}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $row++;
                    $srNo++;

                }
            }

            // Signature Row
            $signatureRow = $row;

            $sheet->getRowDimension($signatureRow)->setRowHeight(80);

            $sheet->mergeCells("A{$signatureRow}:D{$signatureRow}")->setCellValue("A{$signatureRow}", "CHECKED AND PREPARED BY:-" . getUsername($monthlyfirepump->created_by));
            $sheet->mergeCells("E{$signatureRow}:I{$signatureRow}")->setCellValue("E{$signatureRow}", "VERIFIED BY:" . getUsername($monthlyfirepump->updated_by));
            $sheet->mergeCells("J{$signatureRow}:M{$signatureRow}")->setCellValue("J{$signatureRow}", "APPROVED BY:" . getUsername($monthlyfirepump->approved_by));

            $sheet->getStyle("A{$signatureRow}:M{$signatureRow}")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_BOTTOM],
            ]);

            if (file_exists($inspection_created_by)) {
                $drawing = new Drawing();
                $drawing->setName('Checked By');
                $drawing->setPath($inspection_created_by);
                $drawing->setCoordinates("B{$signatureRow}");
                $drawing->setOffsetX(40);
                $drawing->setOffsetY(20);
                $drawing->setWidth(100);
                $drawing->setHeight(40);
                $drawing->setWorksheet($sheet);
            }else {
                $sheet->setCellValue("A{$signatureRow}", "CHECKED BY:- \nInspection not yet started");
            }

            if (file_exists($inspection_verified_by)) {
                $drawing = new Drawing();
                $drawing->setName('Verified By');
                $drawing->setPath($inspection_verified_by);
                $drawing->setCoordinates("G{$signatureRow}");
                $drawing->setOffsetX(40);
                $drawing->setOffsetY(20);
                $drawing->setWidth(100);
                $drawing->setHeight(40);
                $drawing->setWorksheet($sheet);
            } else {
                $sheet->setCellValue("E{$signatureRow}", "VERIFIED BY:- \nInspection not yet started");
            }

            if (file_exists($inspection_approved_by)) {
                $drawing = new Drawing();
                $drawing->setName('Approved By');
                $drawing->setPath($inspection_approved_by);
                $drawing->setCoordinates("K{$signatureRow}");
                $drawing->setOffsetX(40);
                $drawing->setOffsetY(20);
                $drawing->setWidth(100);
                $drawing->setHeight(40);
                $drawing->setWorksheet($sheet);
            }else {
                $sheet->setCellValue("J{$signatureRow}", "APPROVED BY:- \nApproval pending");
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Monthly Fire Pump House Inspection.xlsx';
            $filePath = storage_path("app/public/{$fileName}");
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);

        } catch (\Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/monthly-fire-pump-house-inspection/list'));
        }
    }


}
