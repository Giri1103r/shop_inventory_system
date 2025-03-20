<?php

namespace App\Http\Controllers\Inspection;

use App\Http\Controllers\Controller;
use App\Mail\Inspection\MSDS\MSDSEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Inspection\MSDSDetails;
use App\Models\Inspection\MSDSCheckList;
use Exception;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\MSDSStatusLog;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Admin\AdminController;

class MSDSController extends Controller
{
 
    private $msdsDetails;
    private $msdsCheckList;
    private $statusLog;
    private $admin;

    public function __construct()
    {
        $this->msdsDetails = new MSDSDetails();
        $this->msdsCheckList = new MSDSCheckList();
        $this->statusLog = new MSDSStatusLog();
        $this->admin = new AdminController();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->msdsDetails->list();
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
                            $btn = '<a href="' . admin_url('msds/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            if ($row->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('msds/verification/' . encryptId($row->id)) . '/ehs" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if (($row->inspection_status == WAITING_FOR_CAPA_ACTION || $row->inspection_status == L2_MANAGER_REJECTED || $row->inspection_status == EHS_OFFICER_REJECTED || $row->inspection_status == L1_MANAGER_REJECTED) && (CheckUserRole(ROLE_FIRE_ASSOCIATES) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('msds/verification/' . encryptId($row->id)) . '/capa" class="" title="' . __('inspection.capa_action') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_CAPA_VERIFICATION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('msds/verification/' . encryptId($row->id)) . '/ehsVerify" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L1_VERIFICATION && (CheckUserRole(ROLE_L1_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('msds/verification/' . encryptId($row->id)) . '/level-one-manager" class="" title="' . __('inspection.l1_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ($row->inspection_status == WAITING_FOR_L2_VERIFICATION && (CheckUserRole(ROLE_L2_MANAGER) || isAdmin())) {
                                $btn .= '<a href="' . admin_url('msds/verification/' . encryptId($row->id)) . '/level-two-manager" class="" title="' . __('inspection.l2_manager_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            $btn .= '<a href="' . admin_url('msds/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                                <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                            </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'inspection_status', 'created_by', 'status'])
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

        $data = [];

        return view('inspection.msds.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $data = array();
            return view('inspection.msds.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {

        try {
            $rules = [
                'document_number' => 'required',
                'issue_date' => 'required',
                'revision_date' => 'required',
                'serial_number' => 'required',
                'item_code' => 'required',
                'name_of_chemical' => 'required',
                'msds_availability_status' => 'required',
                'remark' => 'required',

            ];
            $messages = [
                'document_number.required' => __('Document Number is required'),
                'issue_date.required' => __('Issue Date is required'),
                'revision_date.required' => __('Revision Date is required'),
                'serial_number.required' => __('Serial Number is required'),
                'item_code.required' => __('Item Code is required'),
                'name_of_chemical.required' => __('Name of Chemical is required'),
                'msds_availability_status.required' => __('MSDS Availability Status is required'),
                'remark.required' => __('Remark is required'),

            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

               $msds = $this->msdsDetails->store();
               $msdsId = $msds->id;
               $this->msdsCheckList->store($msdsId);

               $mailsubject = 'MSDS Inspection completed by fire associate';
               $ehsOfficer = GetEHSOfficer();
               $message = 'MSDS Inspection completed by fire associate';

               if (count($ehsOfficer) > 0) {
                    foreach ($ehsOfficer as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            $data = $this->msdsDetails->selectOne($msdsId);

                            $data = array(
                                'data' => $data,
                                'mail_subject' => 'MSDS Inspection',
                                'message' => $message,
                            );

                            Mail::to($email_id)->queue(new MSDSEmail($data));

                        }
                    }
                }

                $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
                $notificationData = array(
                    'notification_type' => MSDS_INSPECTION,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => $message,
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $msds->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('msds/view/' . encryptId($msds->id)),
                    'assigned_user' => array_to_string($ehsOfficers),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('msds/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('msds/list'));
        }
    }

    public function StatusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->msdsDetails->statuschange($id);

            $this->msdsCheckList->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Your status  has changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $status_log = $this->statusLog->selectOne($id);
                $msdsDetails = $this->msdsDetails->find($id);
                $inspection_details = $this->msdsDetails->selectOne($id);
                $msdsCheckList = $this->msdsCheckList->selectOne($id);

                $data = array(
                    'msdsDetails' => $msdsDetails,
                    'msdsCheckList' => $msdsCheckList  ?? [],
                    'status_log' => $status_log,
                    'inspection_details' => $inspection_details,
                );
            }
            return view('inspection.msds.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function approvals(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $msdsDetails = $this->msdsDetails->find($id);
            $inspection_details = $this->msdsDetails->selectOne($id);
            $msdsCheckList = $this->msdsCheckList->selectOne($id);

            $data = [
                'inspection_details' => $inspection_details,
                'msdsDetails' => $msdsDetails,
                'msdsCheckList' => $msdsCheckList,
            ];

            return view('inspection.msds.approval', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('msds/list'));
        }
    }

    public function EHSOfficerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_updates = $this->msdsDetails->EHSOfficerUpdate($id);
            $inspection_details = $this->msdsDetails->selectOne($id);
            $signature_update = $this->admin->signatureUpload($request);
            if ($request->is_passed == 1) {
                $message = 'MSDS Inspeciton Approved Successfully';
                $web_link =   admin_url('msds/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
            } else {
                $message = 'Inspection Recommended for the CAPA Action';
                $web_link =   admin_url('msds/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = WAITING_FOR_CAPA_ACTION;
            }
            $userIds = [
                'users' => $inspection_details->created_by,
            ];
            $mailsubject = 'SAFETY INSPECTION';
            $notificationData = array(
                'notification_type' => MSDS_INSPECTION,
                'module_type' => 2,
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
            $insert_array = [
                'msds_details_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);

            $ehsOfficer = GetEHSOfficer();

            if (count($ehsOfficer) > 0) {
                 foreach ($ehsOfficer as $user) {

                     $email_id = $user->email;

                     if ($email_id != '' || $email_id != null) {
                         $data = $this->msdsDetails->selectOne($id);

                         $data = array(
                            'data' => $data,
                            'mail_subject' => 'MSDS Inspection',
                            'message' => $message,
                        );


                        Mail::to($email_id)->queue(new MSDSEmail($data));
                     }
                 }
             }

            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('msds/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('Something Went Wrong!'));
            return redirect(admin_url('msds/list'));
        }
    }

    public function CAPASubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $forklift_inspection = $this->msdsDetails->capaSubmit($id);
            $inspection_details = $this->msdsDetails->selectOne($id);
            $signature_update = $this->admin->signatureUpload($request);
            $ehsOfficers = $inspection_details->verified_by;
            $userIds = [
                'users' => $ehsOfficers,
            ];
            $mailsubject = 'Safety Inspection';
            $notificationData = array(
                'notification_type' => MSDS_INSPECTION,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "CAPA Action completed by Fire Associates",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_details->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('msds/verification/' . encryptId($inspection_details->id)) . '/ehsVerify',
                'assigned_user' => array_to_string($userIds),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);
            $insert_array = [
                'msds_details_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_ACTION,
                'to_status' => WAITING_FOR_CAPA_VERIFICATION,
                'created_by' => Auth::id(),
                'remarks' => $request->capa_remarks,
            ];
            $this->statusLog->create($insert_array);

            $message = 'CAPA Action completed by Fire Associates';
            $ehsOfficer = GetEHSOfficer();

            if (count($ehsOfficer) > 0) {
                 foreach ($ehsOfficer as $user) {

                     $email_id = $user->email;

                     if ($email_id != '' || $email_id != null) {
                         $data = $this->msdsDetails->selectOne($id);

                         $data = array(
                            'data' => $data,
                            'mail_subject' => 'MSDS Inspection',
                            'message' => $message,
                        );

                        Mail::to($email_id)->queue(new MSDSEmail($data));
                     }
                 }
             }

            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('msds/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('msds/list'));
        }
    }

    public function CAPAVerifySubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->remarks;
            $forklift_inspection = $this->msdsDetails->capaVerifySubmit($id, $status, $remarks);
            $inspection_details = $this->msdsDetails->selectOne($id);
            $signature_update = $this->admin->signatureUpload($request);
            if ($status == 1) {
                $message = 'CAPA Action Verified Successfully';
                $web_link =   admin_url('msds/verification/' . encryptId($inspection_details->id) . '/level-one-manager');
                $user = GetLevelOneManager();
                $users = $user ? $user->pluck('id')->toArray() : 1;
                $to_status = WAITING_FOR_L1_VERIFICATION;
            } else {
                $message = 'EHS Officer Rejected the CAPA Action';
                $web_link =   admin_url('msds/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = EHS_OFFICER_REJECTED;
            }
            $userIds = [
                'users' => $users,
            ];
            $mailsubject = 'SAFETY INSPECTION';
            $notificationData = array(
                'notification_type' => MSDS_INSPECTION,
                'module_type' => 1,
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
            $insert_array = [
                'msds_details_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_CAPA_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->statusLog->create($insert_array);

            $ehsOfficer = GetEHSOfficer();

            if (count($ehsOfficer) > 0) {
                 foreach ($ehsOfficer as $user) {

                     $email_id = $user->email;

                     if ($email_id != '' || $email_id != null) {
                         $data = $this->msdsDetails->selectOne($id);

                         $data = array(
                            'data' => $data,
                            'mail_subject' => 'MSDS Inspection',
                            'message' => $message,
                        );

                        Mail::to($email_id)->queue(new MSDSEmail($data));
                     }
                 }
             }

            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('msds/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('msds/list'));
        }
    }

    public function levelOneManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_one_manager;
            $forklift_inspection = $this->msdsDetails->levelOneManagerSubmit($id, $status, $remarks);
            $inspection_details = $this->msdsDetails->selectOne($id);
            $signature_update = $this->admin->signatureUpload($request);
            if ($status == 1) {
                $message = 'Level One Manager Verified Successfully';
                $web_link =   admin_url('msds/verification/' . encryptId($inspection_details->id) . '/level-two-manager');
                $user = GetLevelTwoManager();
                $users = $user ? $user->pluck('id')->toArray() : 1;
                $to_status = WAITING_FOR_L2_VERIFICATION;
            } else {
                $message = 'Level One Manager Rejected the CAPA Action';
                $web_link =   admin_url('msds/verification/' . encryptId($inspection_details->id) . '/capa');
                $users = $inspection_details->created_by;
                $to_status = L1_MANAGER_REJECTED;
            }
            $userIds = [
                'users' => $users,
            ];
            $mailsubject = 'SAFETY INSPECTION';
            $notificationData = array(
                'notification_type' => MSDS_INSPECTION,
                'module_type' => 1,
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
            $insert_array = [
                'msds_details_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L1_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_one_manager,
            ];
            $this->statusLog->create($insert_array);

            $ehsOfficer = GetEHSOfficer();

            if (count($ehsOfficer) > 0) {
                 foreach ($ehsOfficer as $user) {

                     $email_id = $user->email;

                     if ($email_id != '' || $email_id != null) {
                         $data = $this->msdsDetails->selectOne($id);

                         $data = array(
                            'data' => $data,
                            'mail_subject' => 'MSDS Inspection',
                            'message' => $message,
                        );

                        Mail::to($email_id)->queue(new MSDSEmail($data));
                     }
                 }
             }

            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('msds/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('msds/list'));
        }
    }

    public function levelTwoManagerSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->level_two_manager;
            $forklift_inspection = $this->msdsDetails->levelTwoManagerSubmit($id, $status, $remarks);
            $inspection_details = $this->msdsDetails->selectOne($id);
            $signature_update = $this->admin->signatureUpload($request);
            if ($status == 1) {
                $message = 'MSDS Inspeciton Approved Successfully!';
                $web_link =   admin_url('msds/view/' . encryptId($inspection_details->id));
                $to_status = INSPECTION_APPROVED;
            } else {
                $message = 'Level Two Manager Rejected the CAPA Action';
                $web_link =   admin_url('msds/verification/' . encryptId($inspection_details->id) . '/capa');
                $to_status = L2_MANAGER_REJECTED;
            }
            $users = $inspection_details->created_by;
            $userIds = [
                'users' => $users,
            ];
            $mailsubject = 'SAFETY INSPECTION';
            $notificationData = array(
                'notification_type' => MSDS_INSPECTION,
                'module_type' => 1,
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
            $insert_array = [
                'msds_details_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_L2_VERIFICATION,
                'to_status' => $to_status,
                'approved_by' => Auth::id(),
                'remarks' => $request->level_two_manager,
            ];
            $this->statusLog->create($insert_array);

            $ehsOfficer = GetEHSOfficer();

            if (count($ehsOfficer) > 0) {
                 foreach ($ehsOfficer as $user) {

                     $email_id = $user->email;

                     if ($email_id != '' || $email_id != null) {
                         $data = $this->msdsDetails->selectOne($id);

                        $data = array(
                            'data' => $data,
                            'mail_subject' => 'MSDS Inspection',
                            'message' => $message,
                        );

                        Mail::to($email_id)->queue(new MSDSEmail($data));
                     }
                 }
             }

            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('msds/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('msds/list'));
        }
    }


    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->msdsDetails->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Issue Date',
                'Revision Date',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->document_number;
                $export[] =  $data->issue_date;
                $export[] = $data->revision_date;
                $export[] = getInspectionStatus($data->inspection_status);
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('MSDS.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('msds/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->msdsDetails->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Issue Date',
                'Revision Date',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "MSDS Details",
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

            $view = view('inspection.msds.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "MSDS.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('msds/list'));
        }
    }

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $status_log = $this->statusLog->selectOne($id);
                $msdsDetails = $this->msdsDetails->find($id);
                $inspection_details = $this->msdsDetails->selectOne($id);
                $msdsCheckList = $this->msdsCheckList->selectOne($id);

                $data = [
                    'status_log' => $status_log,
                    'msdsDetails' => $msdsDetails,
                    'inspection_details' => $inspection_details,
                    'msdsCheckList' => $msdsCheckList,
                    'pagetitle' => "MSDS Details",
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

            $html = view('inspection.msds.generalpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "MSDS Details.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }

    public function edit($id)
    {
        try {
            $id = decryptId($id);

            $msdsDetails = $this->msdsDetails->find($id);

            $msdsCheckList = $this->msdsCheckList->selectOne($id);

            $data = [
                'msdsDetails' => $msdsDetails,
                'msdsCheckList' => $msdsCheckList  ?? [],
            ];

            return view('inspection.msds.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function update(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $rules = [
                'document_number' => 'required',
                'issue_date' => 'required',
                'revision_date' => 'required',
                'item_code' => 'required',
                'name_of_chemical' => 'required',
                'msds_availability_status' => 'required',
                'remark' => 'required',
            ];
            $messages = [
                'document_number.required' => __('Document Number is required'),
                'issue_date.required' => __('Issue Date is required'),
                'revision_date.required' => __('Revision Date is required'),
                'item_code.required' => __('Item Code is required'),
                'name_of_chemical.required' => __('Name of Chemical is required'),
                'msds_availability_status.required' => __('MSDS Availability Status is required'),
                'remark.required' => __('Remark is required'),
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

               $msds = $this->msdsDetails->updates($id);
                $msds_details = $this->msdsDetails->selectOne($id);
               $msdsId = $msds_details->id;

               $this->msdsCheckList->updates($msdsId);

               Session::flash('success', __('common.updated_msg'));
            } catch (Exception $ex) {
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('msds/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('msds/list'));
        }
    }


}
