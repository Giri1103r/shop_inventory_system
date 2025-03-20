<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use App\Mail\Inspection\Ohc\MedicineRequistionFloorEmail;
use Illuminate\Http\Request;
use App\Models\Master\Department;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\Ohc\InspectionOhcStatuslog;
use App\Models\Inspection\Ohc\MedicineRequisitionSlipFloor;
use App\Models\Inspection\Ohc\MedicineRequistionSlipfloordetails;
use App\Models\OhcManagement\Report\Inventory;
use App\Models\UploadLog;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;


class MedicalRequisitionSlipController extends Controller
{

    private $upload_log;
    private $unit;
    private $shift;
    private $department;
    private $user;
    private $medicine_requisition_floor_checklist;
    private $medicine_requisition_floor_details;
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
        $this->medicine_requisition_floor_checklist = new MedicineRequisitionSlipFloor();
        $this->inventory = new Inventory();
        $this->user = new User();
        $this->inspection_ohc_status_log = new InspectionOhcStatuslog();

        $this->medicine_requisition_floor_details = new MedicineRequistionSlipfloordetails();
    }
    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->medicine_requisition_floor_details->list();

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
                                case FLOOR_MANAGER_APPROVAL_PENDING:
                                    $text = "<span class='badge bg-info rounded' style='font-size: 1.0em;'>Floor Manager Approval Pending</span>";
                                    break;
                                case FLOOR_MANAGER_APPROVED:
                                    $text = "<span class='badge bg-success rounded' style='font-size: 1.0em;'>Floor Manager Approved</span>";
                                    break;
                                case FLOOR_MANAGER_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>Floor Manager Rejected</span>";
                                    break;
                                case SAFETY_OFFICER_APPROVAL_PENDING:
                                    $text = "<span class='badge bg-info rounded' style='font-size: 1.0em;'>Safety Officer Approval Pending</span>";
                                    break;
                                case SAFETY_OFFICER_APPROVED:
                                    $text = "<span class='badge bg-success rounded' style='font-size: 1.0em;'>Safety Officer Approved</span>";
                                    break;

                                default:
                                    $text = "<span class='badge rounded-pill text-bg-warning'>Unknown</span>";
                            }
                            return $text;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn .= '<a href="' . admin_url('ohc/medical-requisition-slip/view/' . encryptId($row->id)) . '" class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a>';
                            if ((checkUserRole(ROLE_SUPERADMIN) && $row->approve_status == FLOOR_MANAGER_APPROVAL_PENDING) || (checkUserRole(ROLE_FLOOR_MANAGER) && $row->approve_status == FLOOR_MANAGER_APPROVAL_PENDING)  || ((checkUserRole(ROLE_SAFETY_OFFICER) && $row->approve_status == SAFETY_OFFICER_APPROVAL_PENDING) || (checkUserRole(ROLE_SUPERADMIN) && $row->approve_status == SAFETY_OFFICER_APPROVAL_PENDING))) {
                            $btn .= '<a href="' . admin_url('ohc/medical-requisition-slip/approval/view/' . encryptId($row->id)) . '" class="" title="Action"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
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

        return view('inspection.inspection_ohc.medical_requisition_slip.list');
    }

    public function Add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $shift = $this->shift->getShiftname();
            $medicine = $this->inventory->getstockdata();
            $signature_upload = $this->user->getSignature();
            $location = $this->location->getLocationname();
            $data = array(
                'unit' => $unit,
                'shift' => $shift,
                'medicine' => $medicine,
                'signature_upload' => $signature_upload,

            );
            return view('inspection.inspection_ohc.medical_requisition_slip.add', $data);
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
                $medicine_requisition_floor_details = $this->medicine_requisition_floor_details->store();
                $medicine_requisition_floor_checklist = $this->medicine_requisition_floor_checklist->store($medicine_requisition_floor_details);
                $data = [
                    'type' => OHC_TYPE_MEDICINE_REQUISTION_FLOOR,
                    'from_status' => OHC_CREATION,
                    'to_status' => FLOOR_MANAGER_APPROVAL_PENDING,
                    'reference_id' => $medicine_requisition_floor_details->id,
                    'remarks' => "",
                    'approved_by' => null,
                    'created_by' => Auth::id(),

                ];
                $id = $medicine_requisition_floor_details->id;
                $this->inspection_ohc_status_log->store($data);
                $getfloormanager = getFloormanager();
                $getfloormanagers = $getfloormanager->pluck('id')->toArray();
                $details = $this->medicine_requisition_floor_details->Selectone($id);
                $mailsubject = 'Medicine Requistion Slip Floor';
                $notificationData = array(
                    'notification_type' => 1,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => "Medicine Requistion Slip floor ",
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('ohc/medical-requisition-slip/approval/view/' . encryptId($id)),
                    'assigned_user' => array_to_string($getfloormanagers),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
                foreach ($getfloormanagers as $user) {
                    $email_id = getUseremail($user);
                    $title = "Medicine Requistion Slip Floor";
                    $details = array(
                        'ohc_type' => 'medicine requisition slip floor',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'data' => $details
                    );
                    Mail::to($email_id)->queue(new MedicineRequistionFloorEmail($details));
                }

                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medical-requisition-slip/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-requisition-slip/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $medicinerequisition = $this->medicine_requisition_floor_details->Selectone($id);
                $medicine_requisition_floor_checklist = $this->medicine_requisition_floor_checklist->Selectone($id);
                $type = OHC_TYPE_MEDICINE_REQUISTION_FLOOR;
                $statuslog = $this->inspection_ohc_status_log->getStatuslog($id, $type);
                $floortype = FLOOR_MANAGER_APPROVAL_PENDING;
                $safetytype = SAFETY_OFFICER_APPROVAL_PENDING;
                $safetyofficer = $this->inspection_ohc_status_log->safetyofficer($id, $safetytype,$type);

                $floormanger = $this->inspection_ohc_status_log->floormanger($id, $floortype,$type);
                $data = array(
                    'medicinerequisition' => $medicinerequisition,
                    'medicine_requisition_floor_checklist' => $medicine_requisition_floor_checklist,
                    'statuslog' => $statuslog,
                    'safetyofficer' => $safetyofficer,
                    'floormanger' => $floormanger,
                );
            }
            return view('inspection.inspection_ohc.medical_requisition_slip.view', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function approval(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $medicinerequisition = $this->medicine_requisition_floor_details->Selectone($id);
                $medicine_requisition_floor_checklist = $this->medicine_requisition_floor_checklist->Selectone($id);
                $floortype = FLOOR_MANAGER_APPROVAL_PENDING;
                $type = OHC_TYPE_MEDICINE_REQUISTION_FLOOR;
                $safetytype = SAFETY_OFFICER_APPROVAL_PENDING;
                $safetyofficer = $this->inspection_ohc_status_log->safetyofficer($id, $safetytype,$type);

                $floormanger = $this->inspection_ohc_status_log->floormanger($id, $floortype,$type);
                $data = array(
                    'medicinerequisition' => $medicinerequisition,
                    'medicine_requisition_floor_checklist' => $medicine_requisition_floor_checklist,
                    'safetyofficer' => $safetyofficer,
                    'floormanger' => $floormanger,


                );
            }
            return view('inspection.inspection_ohc.medical_requisition_slip.approval', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function floormanagerapproval(Request $request)
    {
        try {
            $id = decryptId($request->id);


            try {

                if ($request->action === "approve") {
                    $approveStatus = FLOOR_MANAGER_APPROVED;
                    $nextStatus = SAFETY_OFFICER_APPROVAL_PENDING; // Next status after approval
                } elseif ($request->action == "reject") {
                    $approveStatus = FLOOR_MANAGER_REJECTED;
                    $nextStatus = FLOOR_MANAGER_REJECTED; // Final status if rejected
                }
                $details = $this->medicine_requisition_floor_details->Selectone($id);
                $data = [
                    'type' => OHC_TYPE_MEDICINE_REQUISTION_FLOOR,
                    'from_status' => FLOOR_MANAGER_APPROVAL_PENDING,
                    'to_status' => $approveStatus,
                    'reference_id' => $id,
                    'remarks' => $request->floor_remarks,
                    'created_by' =>  $details->created_by,
                    'approved_by' => Auth::id(),
                ];

                $this->inspection_ohc_status_log->store($data);

                $this->medicine_requisition_floor_details->floormanagerapprovalupdate($id, $nextStatus);
                if ($request->action == "approve") {
                    $safetyofficer = getSafetyOfficer();
                    $safetyofficers = $safetyofficer->pluck('id')->toArray();
                    $details = $this->medicine_requisition_floor_details->Selectone($id);
                    $mailsubject = 'Medicine Requistion Slip Floor approved';
                    $notificationData = array(
                        'notification_type' => 1,
                        'module_type' => 1,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode(array(
                            'title' => $mailsubject,
                            'message' => "Floor manager Approved the medicine requistion slip floor",
                            'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $id,
                            'module' => 1,
                        )),
                        'web_link' =>  admin_url('ohc/medical-requisition-slip/approval/view/' . encryptId($id)),
                        'assigned_user' => array_to_string($safetyofficers),
                        'created_by' => Auth::id(),
                    );
                    notificationSave($notificationData);
                    foreach ($safetyofficer as $user) {
                        $email_id = getUseremail($user);
                        $title = "Medicine Requistion Slip Floor";
                        $details = array(
                            'ohc_type' => 'medicine requisition slip floor',
                            'email' => $email_id,
                            'mail_subject' => $mailsubject,
                            'title' => $title,
                            'data' => $details
                        );
                        Mail::to($email_id)->queue(new MedicineRequistionFloorEmail($details));
                    }
                } else if ($request->action == "reject") {
                    $details = $this->medicine_requisition_floor_details->Selectone($id);
                    $userIds = [
                        'users' => $details->created_by,
                    ];
                    $mailsubject = 'Medicine Requistion Slip Floor Rejected';
                    $notificationData = array(
                        'notification_type' => 1,
                        'module_type' => 1,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode(array(
                            'title' => $mailsubject,
                            'message' => "Floor manager Rejected by the medicine requistion slip floor",
                            'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $id,
                            'module' => 1,
                        )),
                        'web_link' =>  admin_url('ohc/medical-requisition-slip/list'),
                        'assigned_user' => array_to_string($userIds),
                        'created_by' => Auth::id(),
                    );
                    notificationSave($notificationData);
                    $title = "Medicine Requistion Slip Floor";
                    $user = $details->created_by;
                    $email_id = getUseremail($user);

                    $details = array(
                        'ohc_type' => 'Medicine Requistion Slip Floor',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,

                        'data' => $details
                    );
                    Mail::to($email_id)->queue(new MedicineRequistionFloorEmail($details));
                }


                Session::flash('success', 'Your data has been Responded successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medical-requisition-slip/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-requisition-slip/list'));
        }
    }

    public function safetyofficerapproval(Request $request)
    {
        try {
            $id = decryptId($request->id);


            try {
                if ($request->action === "approve") {
                    $approveStatus = SAFETY_OFFICER_APPROVED;
                    $nextStatus = SAFETY_OFFICER_APPROVED;
                }

                $details = $this->medicine_requisition_floor_details->Selectone($id);
                $data = [
                    'type' => OHC_TYPE_MEDICINE_REQUISTION_FLOOR,
                    'from_status' => SAFETY_OFFICER_APPROVAL_PENDING,
                    'to_status' => $approveStatus,
                    'reference_id' => $id,
                    'remarks' => $request->remarks,
                    'created_by' =>  $details->created_by,
                    'approved_by' => Auth::id(),

                ];
                $this->inspection_ohc_status_log->store($data);
                $this->medicine_requisition_floor_details->safetyofficerapprovalupdate($id, $nextStatus);
                $details = $this->medicine_requisition_floor_details->Selectone($id);
                $userIds = [
                    'users' => $details->created_by,
                ];
                $mailsubject = 'Medicine Requistion Slip Floor Rejected';
                $notificationData = array(
                    'notification_type' => 1,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => "Safety Officer Approved by the medicine requistion slip floor",
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('ohc/medical-requisition-slip/list'),
                    'assigned_user' => array_to_string($userIds),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
                $title = "Medicine Requistion Slip Floor";
                $user = $details->created_by;
                $email_id = getUseremail($user);

                $details = array(
                    'ohc_type' => 'Medicine Requistion Slip Floor',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'data' => $details
                );
                Mail::to($email_id)->queue(new MedicineRequistionFloorEmail($details));
                Session::flash('success', 'Your data has been Responded successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medical-requisition-slip/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-requisition-slip/list'));
        }
    }
    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->medicine_requisition_floor_details->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Review date',
                'Issued Date',
                'Unit',
                'Department',
                'Date',
                'Approve Status',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->doc_no;
                $export[] =  $data->revision_date;
                $export[] =  Displaydateformat($data->issue_date);
                $export[] =  getUnitname($data->unit);
                $export[] =  getUnitname($data->department);
                $export[] = Displaydateformat($data->date);
                $export[] = getohcrequisitionfloorstatus($data->approve_status);
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Medicine Requisition Slip Floor.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-requisition-slip/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->medicine_requisition_floor_details->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Review date',
                'Issued Date',
                'Unit',
                'Department',
                'Date',
                'Approve Status',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Medicine Requisition Slip Floor",
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

            $view = view('inspection.inspection_ohc.medical_requisition_slip.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Medicine Requisition Slip Floor.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-requisition-slip/list'));
        }
    }
}
