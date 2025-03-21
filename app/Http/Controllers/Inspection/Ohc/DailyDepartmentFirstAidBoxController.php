<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use App\Mail\Inspection\Ohc\DailyDepartmentFirstAidbox as OhcDailyDepartmentFirstAidbox;
use Illuminate\Http\Request;
use App\Models\Master\Department;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\Ohc\InspectionOhcStatuslog;

use App\Models\Inspection\Ohc\DailyDepartmentFirstAidBox;
use App\Models\Inspection\Ohc\DailyDepartmentFirstAidBoxDetails;
use App\Models\OhcManagement\Master\CertifiedFirstAider;
use App\Models\OhcManagement\Master\FirstAidLocation;
use App\Models\OhcManagement\Report\Inventory;
use App\Models\UploadLog;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;


class DailyDepartmentFirstAidBoxController extends Controller
{
    private $upload_log;
    private $unit;
    private $shift;
    private $department;
    private $daily_department_first_aid_box_details;
    private $user;
    private $daily_department_first_aid_box;
    private $First_aid;
    private $certified_First_aid;
    private $inspection_ohc_status_log;

    private $inventory;


    private $location;
    public function __construct()
    {

        $this->upload_log = new UploadLog();
        $this->unit = new Unit();
        $this->department = new Department();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->daily_department_first_aid_box_details = new DailyDepartmentFirstAidBoxDetails();
        $this->First_aid = new FirstAidLocation();
        $this->certified_First_aid = new CertifiedFirstAider();

        $this->daily_department_first_aid_box = new DailyDepartmentFirstAidBox();
        $this->inventory = new Inventory();
        $this->user = new User();
        $this->inspection_ohc_status_log = new InspectionOhcStatuslog();

    }


    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->daily_department_first_aid_box_details->list();

                    $datatables = Datatables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type='1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type='0'>In-Active</span>";
                            }
                            return $text;
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('approve_status', function ($row) {
                            $text = '';
                            switch ($row->approve_status) {
                                case MEDICAL_ASSISTANT_APPROVAL_PENDING:
                                    $text = "<span class='badge bg-info rounded' style='font-size: 1.0em;'>Floor Manager / Medical Assistant Approval Pending</span>";
                                    break;
                                case MEDICAL_ASSISTANT_APPROVED:
                                    $text = "<span class='badge bg-success rounded' style='font-size: 1.0em;'>Floor Manager/ Medical Assistant Approved</span>";
                                    break;
                                case MEDICAL_ASSISTANT_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>Floor Manager/ Medical Assistant Rejected</span>";
                                    break;

                                default:
                                    $text = "<span class='badge rounded-pill text-bg-warning'>Unknown</span>";
                            }
                            return $text;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn .= '<a href="' . admin_url('ohc/first-aid-box/daily-departmental/view/' . encryptId($row->id)) . '" class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a>';

                            if ((checkUserRole(ROLE_SUPERADMIN) && $row->approve_status == MEDICAL_ASSISTANT_APPROVAL_PENDING) || (checkUserRole(ROLE_FLOOR_MANAGER) && $row->approve_status == MEDICAL_ASSISTANT_APPROVAL_PENDING)) {
                                $btn .= '<a href="' . admin_url('ohc/medical-requisition-slip/approval/view/' . encryptId($row->id)) . '" class="" title="Action"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }

                            $btn .= '<a href="' . admin_url('ohc/first-aid-box/daily-departmental/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';

                            return   $btn;
                        })
                        ->rawColumns(['action', 'issue_date', 'created_by', 'approve_status', 'issue_date'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);

                    return $datatables;
                } catch (Exception $ex) {
                    dd($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }

        return view('inspection.inspection_ohc.daily_department_first_aid_box.list');
    }



    public function Add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $shift = $this->shift->getShiftname();
            $medicine = $this->inventory->getstockdata();
            $signature_upload = $this->user->getSignature();
            $First_aid = $this->certified_First_aid->getFirsaid();

            $location = $this->location->getLocationname();
            $data = array(
                'unit' => $unit,
                'shift' => $shift,
                'medicine' => $medicine,
                'signature_upload' => $signature_upload,
                'First_aid' => $First_aid,


            );
            return view('inspection.inspection_ohc.daily_department_first_aid_box.add', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'unit_id' => 'required',
                'department_id' => 'required',
                'review_date' => 'required',

            ];
            $messages = [
                'department_id.required' => 'Please select a Deparment.',
                'unit_id.required' => 'Please select a unit.',
                'review_date.required' => 'Please select the expiry date.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                // Store user medicine requisition
                $daily_department_first_aid_box_details = $this->daily_department_first_aid_box_details->store();

                $daily_department_first_aid_box = $this->daily_department_first_aid_box->store($daily_department_first_aid_box_details);
                $data = [
                    'type' => OHC_TYPE_DAILY_DEPARTMENT_FIRST_AID_BOX,
                    'from_status' => OHC_CREATION,
                    'to_status' => MEDICAL_ASSISTANT_APPROVAL_PENDING,
                    'reference_id' => $daily_department_first_aid_box_details->id,
                    'remarks' => "",
                    'approved_by' => null,
                    'created_by' => Auth::id(),

                ];
                $id = $daily_department_first_aid_box_details->id;
                $this->inspection_ohc_status_log->store($data);

                 // Safety Officer

                 $getfloormanager = getfloormanager();
                 $getfloormanagers = $getfloormanager->pluck('id')->toArray();
                 $getfloormanagerEmail = $getfloormanager->pluck('email')->toArray();

                 // medical officer

                 $getmedicalassistant = getMedicalAssistant();
                 $getmedicalassistantEmail = $getmedicalassistant->pluck('email')->toArray();
                 $getmedicalassistants = $getmedicalassistant->pluck('id')->toArray();
                 // Select One
                 $daily_department_first_aid_box_details = $this->daily_department_first_aid_box_details->Selectone($id);
                 $daily_department_first_aid_box = $this->daily_department_first_aid_box->Selectone($id);
                 // notification and email

                 $title = "Medical Requisition Slip- Fdo & Security Gate";
                 $mailsubject = "Medical Requisition Slip- Fdo & Security Gate";
                 $details = array(
                     'ohc_type' => 'Medical Requisition Slip- Fdo & Security Gate',
                     'mail_subject' => $mailsubject,
                     'title' => $title,
                     'data' => $daily_department_first_aid_box_details,
                     'checklist' =>   $daily_department_first_aid_box
                 );

                 $recipients = array_merge($getfloormanagerEmail, $getmedicalassistantEmail);
                 if (!empty($recipients)) {
                     Mail::to($recipients)->queue(new OhcDailyDepartmentFirstAidbox($details));
                 }

                 $notificationData = array(
                     'notification_type' => 1,
                     'module_type' => 1,
                     'notification_message' => $mailsubject,
                     'mobile_notification' => json_encode(array(
                         'title' => $mailsubject,
                         'message' => "Requestor Created the medicine requisition slip Fdo & Security Gate",
                         'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                         'id' => $id,
                         'module' => 1,
                     )),
                     'web_link' => admin_url('ohc/medical-requisition-slip/fdo-security-gate/approval/view/' . encryptId($id)), // Fixed concatenation
                     'assigned_user' => array_to_string(array_merge($getmedicalassistants, $getsafetyofficers)), // Fixed missing parenthesis
                     'created_by' => Auth::id(),
                 );

                 notificationSave($notificationData);

                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/first-aid-box/daily-departmental/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aid-box/daily-departmental/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $medicinerequisition = $this->daily_department_first_aid_box_details->Selectone($id);
                $daily_department_first_aid_box = $this->daily_department_first_aid_box->Selectone($id);

                $data = array(
                    'medicinerequisition' => $medicinerequisition,
                    'daily_department_first_aid_box' => $daily_department_first_aid_box,
                );
            }
            return view('inspection.inspection_ohc.daily_department_first_aid_box.view', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }
}
