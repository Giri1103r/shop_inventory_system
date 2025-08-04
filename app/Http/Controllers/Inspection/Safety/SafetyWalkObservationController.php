<?php

namespace App\Http\Controllers\Inspection\Safety;

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
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Mail\Inspection\Safety\SafetyInspection;
use App\Mail\Inspection\Safety\SafetyWalkInspection;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Safety\SafetyStatusLog;
use App\Models\Inspection\Safety\SignatureUpload;
use App\Models\Inspection\Safety\SafetyWalkObservation;
use App\Models\Inspection\Safety\SafetyWalkObservationDetails;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

use function PHPSTORM_META\type;

class SafetyWalkObservationController extends Controller
{
    private $safety_walk;
    private $observation_details;
    private $shift;
    private $unit;
    private $location;
    private $signature;
    private $document_reference;
    private $statusLog;



    public function __construct()
    {
        $this->safety_walk = new SafetyWalkObservation();
        $this->observation_details = new SafetyWalkObservationDetails();
        $this->shift = new Shift();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->signature = new SignatureUpload();
        $this->document_reference = new InspectionStaticDocno();
        $this->statusLog = new SafetyStatusLog();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->safety_walk->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('observation_status', function ($row) {
                            switch ($row->observation_status) {
                                case RESPONSIBLE_PERSON_APPROVAL_PENDING:
                                    $text = "<span class='badge bg-primary rounded' style='font-size: 1.0em;'>WAITING FOR RESPONSIBLE PERSON ACTION</span>";
                                    break;
                                case SAFETY_WALK_EHS_OFFICER_PENDING:
                                    $text = "<span class='badge  bg-primary rounded' style='font-size: 1.0em;'>WAITING FOR EHS OFFICER APPROVAL</span>";
                                    break;
                                case SAFETY_WALK_EHS_OFFICER_APPROVED:
                                    $text = "<span class='badge bg-success rounded' style='font-size: 1.0em;'>CLOSED</span>";
                                    break;
                                case SAFETY_WALK_EHS_OFFICER_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>REJECTED BY EHS OFFICER</span>";
                                    break;
                                case SAFETY_WALK_EHS_OFFICER_ON_PROCESS:
                                    $text = "<span class='badge bg-warning rounded' style='font-size: 1.0em;'>WAITING FOR RE-VERIFICATION BY RESPONSIBLE PERSON</span>";
                                    break;
                                default:
                                    $text = "<span class='badge rounded-pill text-bg-warning'>Unknown</span>";
                            }
                            return $text;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('safety/safety-walk-observation/view/' . encryptId($row->inspection_id)) . '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('safety/safety-walk-observation/exportViewPdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                                    </a>';
                            $btn .= '<a href="' . admin_url('safety/safety-walk-observation/generalExcel/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="EXCEL">
                                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                                    </a>';

                            $responsibilityId = explode(',', $row->responsible_persion);
                            if (

                                (
                                    $row->observation_status == RESPONSIBLE_PERSON_APPROVAL_PENDING &&
                                    $row->observation_status != SAFETY_WALK_EHS_OFFICER_APPROVED &&
                                    $row->observation_status != SAFETY_WALK_EHS_OFFICER_REJECTED &&
                                    (isAdmin() || in_array(Auth::id(), $responsibilityId))
                                ) ||
                                (
                                    $row->observation_status == SAFETY_WALK_EHS_OFFICER_ON_PROCESS &&
                                    $row->observation_status != SAFETY_WALK_EHS_OFFICER_REJECTED &&
                                    $row->observation_status != SAFETY_WALK_EHS_OFFICER_APPROVED &&
                                    (isAdmin() || in_array(Auth::id(), $responsibilityId))
                                ) ||
                                (
                                    $row->observation_status == SAFETY_WALK_EHS_OFFICER_PENDING &&
                                    $row->observation_status != RESPONSIBLE_PERSON_APPROVAL_PENDING &&
                                    $row->observation_status != SAFETY_WALK_EHS_OFFICER_REJECTED &&
                                    $row->observation_status != SAFETY_WALK_EHS_OFFICER_APPROVED &&
                                    (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())
                                )
                            ) {
                                $btn .= '<a href="' . admin_url('safety/safety-walk-observation/approval/' . encryptId($row->inspection_id)) . '" class="" title="' . __('inspection.approval') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            return $btn;
                        })
                        ->addColumn('inspection_created_at', function ($row) {
                            return Displaydateformat($row->inspection_created_at);
                        })
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('inspection_date', function ($row) {
                            return Displaydateformat($row->date);
                        })
                        ->addColumn('safety_walk_taken_by', function ($row) {
                            return getUsername($row->safety_walk_taken_by);
                        })
                        ->rawColumns(['action', 'created_date', 'safety_walk_taken_by', 'observation_status', 'inspection_status', 'issue_date'])
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
        $shifts = $this->shift->getShiftname();

        $data = array(
            'locations' => $location,
            'units' => $unit,
            'shifts' => $shifts,
        );

        return view('inspection.Safety.safety_walk_observation.list', $data);
    }


    public function Add(Request $request)
    {
        try {
            $shift = $this->shift->getShiftname();
            $unit = $this->unit->getunit();
            $locations = $this->location->getLocationName();
            $document_no = $this->document_reference->selectUsingName('SafetyWalkObservationSheet');

            $data = array(
                'shift' => $shift,
                'unit' => $unit,
                'locations' => $locations,
                'document_no' => $document_no,
            );

            return view('inspection.Safety.safety_walk_observation.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        }
    }


    public function Store(Request $request)
    {
        try {
            $safety_walk_observations = $this->safety_walk->Store();
            // dd( $request->all());

            foreach ($safety_walk_observations as $details) {

                if ($details->observing_status == 1) {
                    $responsiblePerson = string_to_array(is_array($details->responsible_persion) ? implode(',', $details->responsible_persion) : ($details->responsible_persion ?? ''));

                    foreach ($responsiblePerson as $users) {
                        $email = getUseremail($users);

                        if (!empty($email)) {
                            $url = admin_url('safety/safety-walk-observation/approval/' . encryptId($details->id));
                            $mailsubject = 'Safety Walk Observation';
                            $title = 'SAFETY WALK OBSERVATION - Has been created';

                            $safetydetails = [
                                'safety_type' => 'Safety Walk Observation',
                                'email' => $email,
                                'mail_subject' => $mailsubject,
                                'title' => $title,
                                'data' => $details,
                            ];

                            // Send email
                            Mail::to($email)->queue(new SafetyWalkInspection($safetydetails));

                            // Send notification
                            $notificationData = [
                                'notification_type' => SAFETY_INSPECTION,
                                'module_type' => 7,
                                'notification_message' => $mailsubject,
                                'mobile_notification' => json_encode([
                                    'title' => $mailsubject,
                                    'message' => "Safety Walk Observation - Observation Has been Created",
                                    'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                                    'id' => '',
                                    'module' => 1,
                                ]),
                                'web_link' => admin_url('safety/safety-walk-observation/list'),
                                'assigned_user' => $users,
                                'created_by' => Auth::id(),
                            ];
                            notificationSave($notificationData);
                        }
                    }
                    $title = 'SAFETY WALK OBSERVATION - Has been created';
                    $ehsOfficer = GetEHSHead();
                    $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
                    $mailsubject = 'Safety Walk Observation';
                    $notificationData = array(
                        'notification_type' => SAFETY_INSPECTION,
                        'module_type' => 7,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode(array(
                            'title' => $mailsubject,
                            'message' => "Safety Walk Observation",
                            'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $details->id,
                            'module' => 1,
                        )),
                        'web_link' =>  admin_url('safety/safety-walk-observation/approval/' . encryptId($details->id)),
                        'assigned_user' => array_to_string($ehsOfficers),
                        'created_by' => Auth::id(),
                    );
                    foreach ($ehsOfficers as $user) {
                        $email_id = getUseremail($user);
                        $url = admin_url('safety/safety-walk-observation/approval/' . encryptId($details->id));

                        $safetydetails = [
                            'safety_type' => 'Safety Walk Observation',
                            'email' => $email,
                            'mail_subject' => $mailsubject,
                            'title' => $title,
                            'data' => $details,
                        ];
                        Mail::to($email_id)->queue(new SafetyWalkInspection($safetydetails));
                    }


                    $admin = GetAdmin();
                    $admins = $admin->pluck('id')->toArray();
                    $mailsubject = 'Safety Walk Observation';
                    $notificationData = array(
                        'notification_type' => SAFETY_INSPECTION,
                        'module_type' => 7,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode(array(
                            'title' => $mailsubject,
                            'message' => "Safety Walk Observation",
                            'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $details->id,
                            'module' => 1,
                        )),
                        'web_link' =>  admin_url('safety/safety-walk-observation/approval/' . encryptId($details->id)),
                        'assigned_user' => array_to_string($admins),
                        'created_by' => Auth::id(),
                    );
                    foreach ($admins as $user) {
                        $email_id = getUseremail($user);
                        $url = admin_url('safety/safety-walk-observation/approval/' . encryptId($details->id));

                        $safetydetails = [
                            'safety_type' => 'Safety Walk Observation',
                            'email' => $email,
                            'mail_subject' => $mailsubject,
                            'title' => $title,
                            'data' => $details,
                        ];
                        Mail::to($email_id)->queue(new SafetyWalkInspection($safetydetails));
                    }


                    $toStatus = WAITING_FOR_EHS_OFFICER_VERIFICATION;
                } else {

                    // $responsiblePerson = string_to_array($details->responsible_persion);
                    // foreach ($responsiblePerson as $users) {
                    //     $email = getUseremail($users);

                    //     if (!empty($email)) {
                    //         $url = admin_url('safety/safety-walk-observation/approval/' . encryptId($details->id));
                    //         $mailsubject = 'Safety Walk Observation';
                    //         $title = 'SAFETY WALK OBSERVATION - Has been created';

                    //         $safetydetails = [
                    //             'safety_type' => 'Safety Walk Observation',
                    //             'email' => $email,
                    //             'mail_subject' => $mailsubject,
                    //             'title' => $title,
                    //             'data' => $details,
                    //         ];

                    //         // Send email
                    //         Mail::to($email)->queue(new SafetyWalkInspection($safetydetails));

                    //         // Send notification
                    //         $notificationData = [
                    //             'notification_type' => SAFETY_INSPECTION,
                    //             'module_type' => 7,
                    //             'notification_message' => $mailsubject,
                    //             'mobile_notification' => json_encode([
                    //                 'title' => $mailsubject,
                    //                 'message' => "Safety Walk Observation - Observation Has been Created",
                    //                 'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                    //                 'id' => '',
                    //                 'module' => 1,
                    //             ]),
                    //             'web_link' => admin_url('safety/safety-walk-observation/list'),
                    //             'assigned_user' => $users,
                    //             'created_by' => Auth::id(),
                    //         ];
                    //         notificationSave($notificationData);
                    //     }
                    // }
                    $title = 'Safety Walk Observation - Closed';
                    $ehsOfficer = GetEHSHead();
                    $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
                    $mailsubject = 'Safety Walk Observation';
                    $notificationData = array(
                        'notification_type' => SAFETY_INSPECTION,
                        'module_type' => 7,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode(array(
                            'title' => $mailsubject,
                            'message' => "Safety Walk Observation",
                            'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $details->id,
                            'module' => 1,
                        )),
                        'web_link' =>  admin_url('safety/safety-walk-observation/approval/' . encryptId($$details->id)),
                        'assigned_user' => array_to_string($ehsOfficers),
                        'created_by' => Auth::id(),
                    );
                    foreach ($ehsOfficers as $user) {
                        $email_id = getUseremail($user);
                        $url = admin_url('safety/safety-walk-observation/approval/' . encryptId($details->id));

                        $safetydetails = [
                            'safety_type' => 'Safety Walk Observation',
                            'email' => $email_id,
                            'mail_subject' => $mailsubject,
                            'title' => $title,
                            'data' => $details,
                        ];
                        Mail::to($email_id)->queue(new SafetyWalkInspection($safetydetails));
                    }


                    $admin = GetAdmin();
                    $admins = $admin->pluck('id')->toArray();
                    $mailsubject = 'Safety Walk Observation';
                    $notificationData = array(
                        'notification_type' => SAFETY_INSPECTION,
                        'module_type' => 7,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode(array(
                            'title' => $mailsubject,
                            'message' => "Safety Walk Observation",
                            'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $details->id,
                            'module' => 1,
                        )),
                        'web_link' =>  admin_url('safety/safety-walk-observation/approval/' . encryptId($details->id)),
                        'assigned_user' => array_to_string($admins),
                        'created_by' => Auth::id(),
                    );
                    foreach ($admins as $user) {
                        $email_id = getUseremail($user);
                        $url = admin_url('safety/safety-walk-observation/approval/' . encryptId($details->id));

                        $safetydetails = [
                            'safety_type' => 'Safety Walk Observation',
                            'email' => $email_id,
                            'mail_subject' => $mailsubject,
                            'title' => $title,
                            'data' => $details,
                        ];
                        Mail::to($email_id)->queue(new SafetyWalkInspection($safetydetails));
                    }


                    $toStatus = WAITING_FOR_EHS_OFFICER_VERIFICATION;
                    $this->safety_walk->where('id', $details->id)->update([
                        'observation_status' => SAFETY_WALK_EHS_OFFICER_APPROVED
                    ]);
                    $toStatus = SAFETY_WALK_EHS_OFFICER_APPROVED;
                }

                // Status log
                $insert_array = [
                    'type' => SAFETY_WALK_OBSERVATION,
                    'inspection_id' => $details->id,
                    'from_status' => 0,
                    'to_status' => $toStatus,
                    'created_by' => Auth::id(),
                ];

                $this->statusLog->create($insert_array);
            }

            Session::flash('success', 'Safety Walk Observation added successfully!');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->safety_walk->selectOne($id);
            $inspection = $this->observation_details->GetDetails($inspection_details->id);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);
            $type =  SAFETY_WALK_OBSERVATION;
            $status_log = $this->statusLog->selectOne($id, $type);

            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'document_no' => $document_no,
                'status_log' => $status_log,
            );
            return view('inspection.Safety.safety_walk_observation.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        }
    }


    public function Approval(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->safety_walk->selectOne($id);
            $inspection = $this->observation_details->GetDetails($inspection_details->id);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);

            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'document_no' => $document_no,
            );

            return view('inspection.Safety.safety_walk_observation.approval', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        }
    }

    // responsible person approval

    public function firstapproval(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $remarks = $request->responsible_person_remarks;
            $date = $request->responsible_person_date;

            $inspection_details = $this->safety_walk->selectOne($id);

            if ($inspection_details->observation_status == RESPONSIBLE_PERSON_APPROVAL_PENDING) {
                $fromStatus = RESPONSIBLE_PERSON_APPROVAL_PENDING;
                $toStatus  = SAFETY_OFFICER_APPROVAL_PENDING;
            } elseif ($inspection_details->observation_status == SAFETY_WALK_EHS_OFFICER_ON_PROCESS) {
                $fromStatus = SAFETY_WALK_EHS_OFFICER_ON_PROCESS;
                $toStatus  = SAFETY_OFFICER_APPROVAL_PENDING;
            }

            $safetyDetails =  $this->safety_walk->approvalSubmit($id, $date, $remarks);
            $safetyDetails = $this->safety_walk->selectOne($id);

            $title = 'SAFETY WALK OBERVATION';
            $mailsubject = 'Safety Walk Observation';

            // --- EHS Officers ---
            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();

            $notificationData = [
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 7,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode([
                    'title' => $mailsubject,
                    'message' => "Safety Walk Observation",
                    'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $id,
                    'module' => 1,
                ]),
                'web_link' => admin_url('safety/safety-walk-observation/approval/' . encryptId($id)),
                'assigned_user' => implode(',', $ehsOfficers),
                'created_by' => Auth::id(),
            ];
            notificationSave($notificationData);

            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $details = [
                    'safety_type' => 'Safety Walk Observation',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => admin_url('safety/safety-walk-observation/approval/' . encryptId($id)),
                    'data' => $safetyDetails
                ];
                Mail::to($email_id)->queue(new SafetyWalkInspection($details));
            }

            // --- EHS Heads ---
            $ehsHead = GetEHSHead();
            $ehsHeads = $ehsHead->pluck('id')->toArray();

            $notificationData = [
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 7,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode([
                    'title' => $mailsubject,
                    'message' => "Safety Walk Observation",
                    'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_details->id,
                    'module' => 1,
                ]),
                'web_link' => admin_url('safety/safety-walk-observation/approval/' . encryptId($inspection_details->id)),
                'assigned_user' => implode(',', $ehsHeads),
                'created_by' => Auth::id(),
            ];
            notificationSave($notificationData);

            foreach ($ehsHeads as $user) {
                $email_id = getUseremail($user);
                $safetydetails = [
                    'safety_type' => 'Safety Walk Observation',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => admin_url('safety/safety-walk-observation/approval/' . encryptId($inspection_details->id)),
                    'data' => $safetyDetails,
                ];
                Mail::to($email_id)->queue(new SafetyWalkInspection($safetydetails));
            }

            // --- Admins ---
            $admin = GetAdmin();
            $admins = $admin->pluck('id')->toArray();

            $notificationData = [
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 7,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode([
                    'title' => $mailsubject,
                    'message' => "Safety Walk Observation",
                    'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_details->id,
                    'module' => 1,
                ]),
                'web_link' => admin_url('safety/safety-walk-observation/approval/' . encryptId($inspection_details->id)),
                'assigned_user' => implode(',', $admins),
                'created_by' => Auth::id(),
            ];
            notificationSave($notificationData);

            foreach ($admins as $user) {
                $email_id = getUseremail($user);
                $safetydetails = [
                    'safety_type' => 'Safety Walk Observation',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => admin_url('safety/safety-walk-observation/approval/' . encryptId($inspection_details->id)),
                    'data' => $safetyDetails,
                ];
                Mail::to($email_id)->queue(new SafetyWalkInspection($safetydetails));
            }

            // --- Status Log ---
            $insert_array = [
                'type' => SAFETY_WALK_OBSERVATION,
                'inspection_id' => $id,
                'from_status' => $fromStatus,
                'to_status' => SAFETY_WALK_EHS_OFFICER_PENDING,
                'remarks' => $remarks,
                'approved_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);

            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('safety/safety-walk-observation/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        }
    }


    // ehs officer approval
    public function finalapproval(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = '';

            if ($request->approved == 1) {
                $status = SAFETY_WALK_EHS_OFFICER_APPROVED;
            } elseif ($request->rejected == 2) {
                $status = SAFETY_WALK_EHS_OFFICER_REJECTED;
            } elseif ($request->on_process == 3) {
                $status = SAFETY_WALK_EHS_OFFICER_ON_PROCESS;
            }

            $remarks = $request->remarks;
            $date = $request->date;

            $this->safety_walk->ehsapproval($id, $status, $remarks, $date);
            $inspection_details = $this->safety_walk->selectOne($id);
            $title = 'SAFETY WALK OBSERVATION';
            $mailsubject = 'Safety Walk Observation';

            if ($request->approved == 1 || $request->rejected == 2) {
                // --- Notify EHS Officers ---
                $ehsOfficers = GetEHSOfficer()->pluck('id')->toArray();
                notificationSave([
                    'notification_type' => SAFETY_INSPECTION,
                    'module_type' => 7,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode([
                        'title' => $mailsubject,
                        'message' => "Safety Walk Observation",
                        'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $id,
                        'module' => 1,
                    ]),
                    'web_link' => admin_url('safety/safety-walk-observation/approval/' . encryptId($id)),
                    'assigned_user' => implode(',', $ehsOfficers),
                    'created_by' => Auth::id(),
                ]);

                foreach ($ehsOfficers as $user) {
                    $email = getUseremail($user);
                    Mail::to($email)->queue(new SafetyWalkInspection([
                        'safety_type' => 'Safety Walk Observation',
                        'email' => $email,
                        'mail_subject' => $mailsubject,
                        'title' => $title . ($request->approved == 1 ? ' - Has been Approved' : ' - Has been Rejected'),
                        'url' => admin_url('safety/safety-walk-observation/approval/' . encryptId($id)),
                        'data' => $inspection_details,
                    ]));
                }

                // --- Notify EHS Heads ---
                $ehsHeads = GetEHSHead()->pluck('id')->toArray();
                notificationSave([
                    'notification_type' => SAFETY_INSPECTION,
                    'module_type' => 7,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode([
                        'title' => $mailsubject,
                        'message' => "Safety Walk Observation",
                        'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $inspection_details->id,
                        'module' => 1,
                    ]),
                    'web_link' => admin_url('safety/safety-walk-observation/approval/' . encryptId($inspection_details->id)),
                    'assigned_user' => implode(',', $ehsHeads),
                    'created_by' => Auth::id(),
                ]);

                foreach ($ehsHeads as $user) {
                    $email = getUseremail($user);
                    Mail::to($email)->queue(new SafetyWalkInspection([
                        'safety_type' => 'Safety Walk Observation',
                        'email' => $email,
                        'mail_subject' => $mailsubject,
                        'title' => $title . ($request->approved == 1 ? ' - Has been Approved' : ' - Has been Rejected'),
                        'url' => admin_url('safety/safety-walk-observation/approval/' . encryptId($inspection_details->id)),
                        'data' => $inspection_details,
                    ]));
                }

                // --- Notify Admins ---
                $admins = GetAdmin()->pluck('id')->toArray();
                notificationSave([
                    'notification_type' => SAFETY_INSPECTION,
                    'module_type' => 7,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode([
                        'title' => $mailsubject,
                        'message' => "Safety Walk Observation",
                        'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $inspection_details->id,
                        'module' => 1,
                    ]),
                    'web_link' => admin_url('safety/safety-walk-observation/approval/' . encryptId($inspection_details->id)),
                    'assigned_user' => implode(',', $admins),
                    'created_by' => Auth::id(),
                ]);

                foreach ($admins as $user) {
                    $email = getUseremail($user);
                    Mail::to($email)->queue(new SafetyWalkInspection([
                        'safety_type' => 'Safety Walk Observation',
                        'email' => $email,
                        'mail_subject' => $mailsubject,
                        'title' => $title . ($request->approved == 1 ? ' - Has been Approved' : ' - Has been Rejected'),
                        'url' => admin_url('safety/safety-walk-observation/approval/' . encryptId($inspection_details->id)),
                        'data' => $inspection_details,
                    ]));
                }

                // --- Notify Responsible Persons ---
                $responsiblePersons = string_to_array(is_array($inspection_details->responsible_persion) ? implode(',', $inspection_details->responsible_persion) : ($inspection_details->responsible_persion ?? ''));

                foreach ($responsiblePersons as $userId) {
                    $email = getUseremail($userId);
                    if (!empty($email)) {
                        Mail::to($email)->queue(new SafetyWalkInspection([
                            'safety_type' => 'Safety Walk Observation',
                            'email' => $email,
                            'mail_subject' => $mailsubject,
                            'title' => $title . ($request->approved == 1 ? ' - Has been Approved' : ' - Has been Rejected'),
                            'url' => admin_url('safety/safety-walk-observation/approval/' . encryptId($inspection_details->id)),
                            'data' => $inspection_details,
                        ]));

                        notificationSave([
                            'notification_type' => SAFETY_INSPECTION,
                            'module_type' => 7,
                            'notification_message' => $mailsubject,
                            'mobile_notification' => json_encode([
                                'title' => $mailsubject,
                                'message' => "Safety Walk Observation - Observation Has been Created",
                                'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                                'id' => '',
                                'module' => 1,
                            ]),
                            'web_link' => admin_url('safety/safety-walk-observation/list'),
                            'assigned_user' => $userId,
                            'created_by' => Auth::id(),
                        ]);
                    }
                }
            } elseif ($request->on_process == 3) {
                // --- Re-verification Request ---
                $responsiblePersons = string_to_array(is_array($inspection_details->responsible_persion) ? implode(',', $inspection_details->responsible_persion) : ($inspection_details->responsible_persion ?? ''));

                foreach ($responsiblePersons as $userId) {
                    $email = getUseremail($userId);
                    if (!empty($email)) {
                        Mail::to($email)->queue(new SafetyWalkInspection([
                            'safety_type' => 'Safety Walk Observation',
                            'email' => $email,
                            'mail_subject' => $mailsubject,
                            'title' => $title . ' - Has been Sent For Re-Verification',
                            'url' => admin_url('safety/safety-walk-observation/approval/' . encryptId($inspection_details->id)),
                            'data' => $inspection_details,
                        ]));

                        notificationSave([
                            'notification_type' => SAFETY_INSPECTION,
                            'module_type' => 7,
                            'notification_message' => $mailsubject,
                            'mobile_notification' => json_encode([
                                'title' => $mailsubject,
                                'message' => "Safety Walk Observation - Observation Has been Created",
                                'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                                'id' => '',
                                'module' => 1,
                            ]),
                            'web_link' => admin_url('safety/safety-walk-observation/list'),
                            'assigned_user' => $userId,
                            'created_by' => Auth::id(),
                        ]);
                    }
                }
            }

            // --- Status Log ---
            $this->statusLog->create([
                'type' => SAFETY_WALK_OBSERVATION,
                'inspection_id' => $id,
                'from_status' => SAFETY_WALK_EHS_OFFICER_PENDING,
                'to_status' => $status,
                'remarks' => $remarks,
                'approved_by' => Auth::id(),
            ]);

            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('safety/safety-walk-observation/list'));
        } catch (Exception $ex) {
            report($ex); // You may want to log this instead in production
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        }
    }


    public function ExportExcel()
    {
        try {
            $allData = $this->safety_walk->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $currentRow = 1;

            foreach ($allData as $group) {
                foreach ($group as $inspection_details) {


                    $inspection_details = $inspection_details->first();
                    $document_no = $this->document_reference->selectUsingName('SafetyWalkObservationSheet');

                    // ====== LOGOS & HEADER ======
                    $logoLeftPath = public_path('assets/images/logo-dark.png');
                    if (file_exists($logoLeftPath)) {
                        $sheet->mergeCells("A$currentRow:F" . ($currentRow + 2));

                        $drawing = new Drawing();
                        $drawing->setName('Left Logo');
                        $drawing->setPath($logoLeftPath);
                        $drawing->setCoordinates("B$currentRow");
                        $drawing->setOffsetX(100);
                        $drawing->setOffsetY(15);
                        $drawing->setWidth(50);
                        $drawing->setHeight(50);
                        $drawing->setWorksheet($sheet);

                        $range = "A$currentRow:F" . ($currentRow + 2);
                        $sheet->getStyle($range)->applyFromArray([
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        ]);
                    }

                    $sheet->mergeCells("G{$currentRow}:M" . ($currentRow + 2));
                    $sheet->setCellValue("G{$currentRow}", "Safety Walk Observation Sheet");
                    $sheet->getStyle("G{$currentRow}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 14],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);

                    $logoRightPath = public_path('assets/images/safety_walk_logo.jpg');
                    if (file_exists($logoRightPath)) {
                        $sheet->mergeCells("N$currentRow:S" . ($currentRow + 2));
                        $drawing = new Drawing();
                        $drawing->setName('Right Logo');
                        $drawing->setPath($logoRightPath);
                        $drawing->setCoordinates("P$currentRow");
                        $drawing->setOffsetX(100);
                        $drawing->setOffsetY(15);
                        $drawing->setWidth(50);
                        $drawing->setHeight(25);
                        $drawing->setWorksheet($sheet);

                        $range = "N$currentRow:S" . ($currentRow + 2);
                        $sheet->getStyle($range)->applyFromArray([
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        ]);
                    }

                    $sheet->mergeCells("T$currentRow:V$currentRow")->setCellValue("T$currentRow", 'Doc. No.');
                    $sheet->mergeCells("T" . ($currentRow + 1) . ":V" . ($currentRow + 1))->setCellValue("T" . ($currentRow + 1), 'Issue Dt.');
                    $sheet->mergeCells("T" . ($currentRow + 2) . ":V" . ($currentRow + 2))->setCellValue("T" . ($currentRow + 2), 'Rev. & Dt.');

                    $sheet->mergeCells("W$currentRow:Y$currentRow")->setCellValue("W$currentRow", $document_no->doc_no);
                    $sheet->mergeCells("W" . ($currentRow + 1) . ":Y" . ($currentRow + 1))->setCellValue("W" . ($currentRow + 1), Displaydateformat($document_no->issue_date));
                    $sheet->mergeCells("W" . ($currentRow + 2) . ":Y" . ($currentRow + 2))->setCellValue("W" . ($currentRow + 2), $document_no->rev_dt);

                    $sheet->getStyle("T$currentRow:Y" . ($currentRow + 2))->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'font' => ['bold' => true],
                    ]);

                    // ====== DETAILS ======
                    $infoRow = $currentRow + 3;
                    $sheet->mergeCells("A$infoRow:F$infoRow");
                    $richText1 = new RichText();
                    $richText1->createTextRun(' DATE:- ')->getFont()->setBold(true);
                    $richText1->createText(Displaydateformat($inspection_details->date));
                    $sheet->getCell("A$infoRow")->setValue($richText1);

                    $sheet->mergeCells("G$infoRow:M$infoRow");
                    $richText2 = new RichText();
                    $richText2->createTextRun('SHIFT :-  ')->getFont()->setBold(true);
                    $richText2->createText(getShift($inspection_details->shift_id));
                    $sheet->getCell("G$infoRow")->setValue($richText2);

                    $sheet->getStyle("A$infoRow:M$infoRow")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $unitRow = $infoRow + 1;
                    $sheet->mergeCells("A$unitRow:F$unitRow");
                    $richText3 = new RichText();
                    $richText3->createTextRun('UNIT :-  ')->getFont()->setBold(true);
                    $richText3->createText(getUnitname($inspection_details->unit));
                    $sheet->getCell("A$unitRow")->setValue($richText3);

                    $sheet->mergeCells("G$unitRow:M$unitRow");
                    $richText4 = new RichText();
                    $richText4->createTextRun('MONTH :-  ')->getFont()->setBold(true);
                    $richText4->createText($inspection_details->month);
                    $sheet->getCell("G$unitRow")->setValue($richText4);

                    $sheet->getStyle("A$unitRow:M$unitRow")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $sheet->mergeCells("N{$infoRow}:Y{$unitRow}");
                    $richText5 = new RichText();
                    $richText5->createTextRun('Safety Walk Taken By (Name):- ')->getFont()->setBold(true);
                    $richText5->createText(getUsername($inspection_details->safety_walk_taken_by));
                    $sheet->getCell("N{$infoRow}")->setValue($richText5);

                    $sheet->getStyle("N{$infoRow}:Y{$unitRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    ]);

                    // ====== TABLE HEADERS ======
                    $headerRow = $unitRow + 1;
                    $sheet->mergeCells("A$headerRow:C$headerRow")->setCellValue("A$headerRow", 'Location');
                    $sheet->mergeCells("D$headerRow:F$headerRow")->setCellValue("D$headerRow", 'Exact Location');
                    $sheet->mergeCells("G$headerRow:I$headerRow")->setCellValue("G$headerRow", 'Observation Date');
                    $sheet->mergeCells("J$headerRow:L$headerRow")->setCellValue("J$headerRow", 'Observation');
                    $sheet->mergeCells("M$headerRow:O$headerRow")->setCellValue("M$headerRow", 'Picture');
                    $sheet->mergeCells("P$headerRow:R$headerRow")->setCellValue("P$headerRow", 'Responsible Person');
                    $sheet->mergeCells("S$headerRow:U$headerRow")->setCellValue("S$headerRow", 'Recommended Action');
                    $sheet->mergeCells("V$headerRow:W$headerRow")->setCellValue("V$headerRow", 'Observation Status');
                    $sheet->mergeCells("X$headerRow:Y$headerRow")->setCellValue("X$headerRow", 'Remarks');

                    $sheet->getStyle("A$headerRow:Y$headerRow")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                        'font' => ['bold' => true],
                    ]);

                    // ====== OBSERVATION DATA ROW ======
                    $dataRow = $headerRow + 1;
                    $sheet->mergeCells("A$dataRow:C$dataRow")->setCellValue("A$dataRow", getLocationname($inspection_details->location_id));
                    $sheet->mergeCells("D$dataRow:F$dataRow")->setCellValue("D$dataRow", $inspection_details->excat_location);
                    $sheet->mergeCells("G$dataRow:I$dataRow")->setCellValue("G$dataRow", Displaydateformat($inspection_details->observation_date));
                    $sheet->mergeCells("J$dataRow:L$dataRow")->setCellValue("J$dataRow", $inspection_details->observation);
                    $sheet->mergeCells("P$dataRow:R$dataRow")->setCellValue("P$dataRow", getUsername($inspection_details->responsible_persion));
                    $sheet->mergeCells("S$dataRow:U$dataRow")->setCellValue("S$dataRow", $inspection_details->recomended_action);
                    $sheet->mergeCells("V$dataRow:W$dataRow")->setCellValue("V$dataRow", $inspection_details->observing_status == 1 ? 'Active' : 'De-active');
                    $sheet->mergeCells("X$dataRow:Y$dataRow")->setCellValue("X$dataRow", $inspection_details->remarks);

                    $sheet->mergeCells("M$dataRow:O$dataRow");
                    $imagePath = GetSafetyWalkImage($inspection_details->id);
                    if (file_exists($imagePath)) {
                        $drawing = new Drawing();
                        $drawing->setPath($imagePath);
                        $drawing->setCoordinates("M{$dataRow}");
                        $drawing->setOffsetX(5);
                        $drawing->setOffsetY(5);
                        $drawing->setWidth(40);
                        $drawing->setHeight(40);
                        $drawing->setWorksheet($sheet);

                        $sheet->getRowDimension($dataRow)->setRowHeight(60);
                    }

                    $sheet->getStyle("A$dataRow:Y$dataRow")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    ]);

                    // ====== SIGNATURE ROW ======
                    $signRow = $dataRow + 1;
                    $sheet->getRowDimension($signRow)->setRowHeight(80);

                    $sheet->mergeCells("A$signRow:H$signRow")->setCellValue("A$signRow", "\n\n\nPrepared By:\n" . getUsername($inspection_details->created_by ?? ''));
                    $sheet->mergeCells("I$signRow:O$signRow")->setCellValue("I$signRow", "\n\n\nResponsible Person:\n" . getUsername($inspection_details->observer_person ?? ''));
                    $sheet->mergeCells("P$signRow:Y$signRow")->setCellValue("P$signRow", "\n\n\nApprover Person:\n" . getUsername($inspection_details->approver_id ?? ''));

                    $sheet->getStyle("A$signRow:Y$signRow")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    ]);

                    // Move to next block of rows for next observation
                    $currentRow = $signRow + 4; // Add buffer
                }
            }
            $writer = new Xlsx($spreadsheet);
            $fileName = 'Safety Walk Observations.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"{$fileName}\"");
            $writer->save('php://output');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect()->back();
        }
    }

    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->safety_walk->exportdata();
            $document_no = $this->document_reference->selectUsingName('SafetyWalkObservationSheet');

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $data = array(
                'content' => $allData,
                'document_no' => $document_no,
                'pagetitle' => "Safety Walk Observation",
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

            $view = view('inspection.Safety.safety_walk_observation.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Safety Walk Observation.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        }
    }

    // individual pdf
    public function exportViewPdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $inspection_details = $this->safety_walk->selectOne($id);
                $current_month_inspection = $this->observation_details->GetDetails($inspection_details->id);
                $last_month_inspection = $this->safety_walk->GetLastMonthObservation($id);
                $last_month_observation_details = $this->observation_details->GetLastMonthDetails($last_month_inspection);
                $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);
                $type =  SAFETY_WALK_OBSERVATION;
                $status_log = $this->statusLog->selectOne($id, $type);

                $data = [
                    'inspection_details' => $inspection_details,
                    'inspection' => $current_month_inspection,
                    'last_month_inspection' => $last_month_inspection,
                    'last_month_observation_details' => $last_month_observation_details,
                    'pagetitle' => "Safety Walk Observation",
                    'document_no' => $document_no,
                    'status_log' => $status_log
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

            $html = view('inspection.Safety.safety_walk_observation.viewPdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Safety Walk Observation.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        }
    }

    // invidiual excel

    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->safety_walk->selectOne($id);

            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $row = 1;
            $currentRow = $row;

            // Logo Section
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
            $sheet->mergeCells("G{$currentRow}:M" . ($currentRow + 2));
            $sheet->setCellValue("G{$currentRow}", "Safety Walk Observation Sheet");
            $sheet->getStyle("G{$currentRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $logoLeftPath = public_path('assets/images/safety_walk_logo.jpg');
            if (file_exists($logoLeftPath)) {
                $sheet->mergeCells("N$currentRow:S" . ($currentRow + 2));

                $drawing = new Drawing();
                $drawing->setName('Left Logo');
                $drawing->setPath($logoLeftPath);
                $drawing->setCoordinates("P$currentRow");
                $drawing->setOffsetX(100);
                $drawing->setOffsetY(15);
                $drawing->setWidth(70);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);

                $range = "N$currentRow:S" . ($currentRow + 2);
                $sheet->getStyle($range)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }

            // Document Info
            $sheet->mergeCells("T$currentRow:V$currentRow")->setCellValue("T$currentRow", 'Doc. No.');
            $sheet->mergeCells("T" . ($currentRow + 1) . ":V" . ($currentRow + 1))->setCellValue("T" . ($currentRow + 1), 'Issue Dt.');
            $sheet->mergeCells("T" . ($currentRow + 2) . ":V" . ($currentRow + 2))->setCellValue("T" . ($currentRow + 2), 'Rev. & Dt.');

            $sheet->mergeCells("W$currentRow:Y$currentRow")->setCellValue("W$currentRow", $document_no->doc_no);
            $sheet->mergeCells("W" . ($currentRow + 1) . ":Y" . ($currentRow + 1))->setCellValue("W" . ($currentRow + 1), Displaydateformat($document_no->issue_date));
            $sheet->mergeCells("W" . ($currentRow + 2) . ":Y" . ($currentRow + 2))->setCellValue("W" . ($currentRow + 2), $document_no->rev_dt);

            $sheet->getStyle("T$currentRow:Y" . ($currentRow + 2))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font' => ['bold' => true],
            ]);
            // general details

            // Review Dates
            $sheet->mergeCells("A" . ($currentRow + 3) . ":F" . ($currentRow + 3));
            $richText1 = new RichText();
            $richText1->createTextRun(' DATE:- ')->getFont()->setBold(true);
            $richText1->createText(Displaydateformat($inspection_details->date));
            $sheet->getCell("A" . ($currentRow + 3))->setValue($richText1);



            $sheet->mergeCells("G" . ($currentRow + 3) . ":M" . ($currentRow + 3));
            $richText2 = new RichText();
            $richText2->createTextRun('SHIFT :-  ')->getFont()->setBold(true);
            $richText2->createText(getShift($inspection_details->shift_id));
            $sheet->getCell("G" . ($currentRow + 3))->setValue($richText2);

            $sheet->getStyle("A" . ($currentRow + 3) . ":M" . ($currentRow + 3))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->mergeCells("A" . ($currentRow + 4) . ":F" . ($currentRow + 4));
            $richText2 = new RichText();
            $richText2->createTextRun('UNIT :-  ')->getFont()->setBold(true);
            $richText2->createText(getUnitname($inspection_details->unit));
            $sheet->getCell("A" . ($currentRow + 4))->setValue($richText2);



            $sheet->mergeCells("G" . ($currentRow + 4) . ":M" . ($currentRow + 4));
            $richText2 = new RichText();
            $richText2->createTextRun('MONTH :-  ')->getFont()->setBold(true);
            $richText2->createText(($inspection_details->month));
            $sheet->getCell("G" . ($currentRow + 4))->setValue($richText2);

            $sheet->getStyle("A" . ($currentRow + 4) . ":M" . ($currentRow + 4))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $startRow = $currentRow + 3;
            $endRow = $currentRow + 4;


            $sheet->mergeCells("N{$startRow}:Y{$endRow}");

            $richText1 = new RichText();
            $richText1->createTextRun('Safety Walk Taken By (Name):- ')->getFont()->setBold(true);
            $richText1->createText(getUsername($inspection_details->safety_walk_taken_by));

            $sheet->getCell("N{$startRow}")->setValue($richText1);

            $sheet->getStyle("N{$startRow}:Y{$endRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);
            $targetRow = $currentRow + 5;

            $sheet->mergeCells("A{$targetRow}:C{$targetRow}")->setCellValue("A{$targetRow}", 'Location');
            $sheet->mergeCells("D{$targetRow}:F{$targetRow}")->setCellValue("D{$targetRow}", 'Exact Location');
            $sheet->mergeCells("G{$targetRow}:I{$targetRow}")->setCellValue("G{$targetRow}", 'Observation Date');
            $sheet->mergeCells("J{$targetRow}:L{$targetRow}")->setCellValue("J{$targetRow}", 'Observation');
            $sheet->mergeCells("M{$targetRow}:O{$targetRow}")->setCellValue("M{$targetRow}", 'Picture');
            $sheet->mergeCells("P{$targetRow}:R{$targetRow}")->setCellValue("P{$targetRow}", 'Responsible Person');
            $sheet->mergeCells("S{$targetRow}:U{$targetRow}")->setCellValue("S{$targetRow}", 'Recommended Action');
            $sheet->mergeCells("V{$targetRow}:W{$targetRow}")->setCellValue("V{$targetRow}", 'Observation Status');
            $sheet->mergeCells("X{$targetRow}:Y{$targetRow}")->setCellValue("X{$targetRow}", 'Remarks');


            // Optional: center align and border
            $sheet->getStyle("A{$targetRow}:Y{$targetRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'font' => [
                    'bold' => true,
                ],
            ]);


            $targetRow = $currentRow + 6;

            // Merge and set values
            $sheet->mergeCells("A{$targetRow}:C{$targetRow}")->setCellValue("A{$targetRow}",  getLocationname($inspection_details->location_id));
            $sheet->mergeCells("D{$targetRow}:F{$targetRow}")->setCellValue("D{$targetRow}",  $inspection_details->excat_location);
            $sheet->mergeCells("G{$targetRow}:I{$targetRow}")->setCellValue("G{$targetRow}",  Displaydateformat($inspection_details->observation_date));
            $sheet->mergeCells("J{$targetRow}:L{$targetRow}")->setCellValue("J{$targetRow}",  $inspection_details->observation);
            $sheet->mergeCells("P{$targetRow}:R{$targetRow}")->setCellValue("P{$targetRow}",  getUsername($inspection_details->responsible_persion));
            $sheet->mergeCells("S{$targetRow}:U{$targetRow}")->setCellValue("S{$targetRow}",  $inspection_details->recomended_action);
            $sheet->mergeCells("V{$targetRow}:W{$targetRow}")->setCellValue("V{$targetRow}",  $inspection_details->observing_status == 1 ? 'Active' : 'De-active');
            $sheet->mergeCells("X{$targetRow}:Y{$targetRow}")->setCellValue("X{$targetRow}",  $inspection_details->remarks);


            $sheet->mergeCells("M{$targetRow}:O{$targetRow}");

            // Get image path
            $imagePath = GetSafetyWalkImage($inspection_details->id);

            if (file_exists($imagePath)) {
                $drawing = new Drawing();
                $drawing->setPath($imagePath);
                $sheet->getColumnDimension('M')->setWidth(15);
                $sheet->getColumnDimension('N')->setWidth(15);
                $sheet->getColumnDimension('O')->setWidth(15);


                $sheet->getRowDimension($targetRow)->setRowHeight(60);

                $drawing->setWidth(40);
                $drawing->setHeight(40);

                // Set anchor cell
                $drawing->setCoordinates("M{$targetRow}");

                // Optional: fine-tune placement
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);

                $drawing->setWorksheet($sheet);
            }

            // Style
            $sheet->getStyle("A{$targetRow}:Y{$targetRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);

            $signatureRowStart = $currentRow + 7;
            $sheet->getRowDimension($signatureRowStart)->setRowHeight(80);

            $sheet->mergeCells("A{$signatureRowStart}:H{$signatureRowStart}");
            $sheet->getStyle("A{$signatureRowStart}:H{$signatureRowStart}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);

            if ($inspection_details->created_by) {

                $sheet->setCellValue("A{$signatureRowStart}", "\n\n\nPrepared By:\n" . getUsername($inspection_details->created_by));
            } else {
                $sheet->setCellValue("A{$signatureRowStart}", "Prepared By:\nInspection not yet started");
            }

            $sheet->mergeCells("A{$signatureRowStart}:H{$signatureRowStart}");
            $sheet->getStyle("A{$signatureRowStart}:H{$signatureRowStart}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);

            $sheet->mergeCells("I{$signatureRowStart}:O{$signatureRowStart}");
            $sheet->getStyle("I{$signatureRowStart}:O{$signatureRowStart}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);

            if ($inspection_details->observer_person) {

                $sheet->setCellValue("I{$signatureRowStart}", "\n\n\nResponsible Person:\n" . getUsername($inspection_details->observer_person));
            } else {
                $sheet->setCellValue("I{$signatureRowStart}", "Prepared By:\nInspection not yet started");
            }



            $sheet->mergeCells("P{$signatureRowStart}:Y{$signatureRowStart}");
            $sheet->getStyle("P{$signatureRowStart}:Y{$signatureRowStart}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);

            if ($inspection_details->approver_id) {

                $sheet->setCellValue("P{$signatureRowStart}", "\n\n\nApprover Person:\n" . getUsername($inspection_details->approver_id));
            } else {
                $sheet->setCellValue("P{$signatureRowStart}", "Prepared By:\nInspection not yet started");
            }



            $writer = new Xlsx($spreadsheet);
            $fileName = 'Safety Walk Observation Sheet.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"{$fileName}\"");
            $writer->save('php://output');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        }
    }
}
