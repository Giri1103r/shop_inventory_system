<?php

namespace App\Http\Controllers\Inspection\GembaWalk;

use Exception;
use App\Models\User;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Master\Employee;
use App\Models\Master\Location;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\GembaWalk\GembaWalkMail;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\GembaWalk\GembaWalk;
use App\Models\Inspection\GembaWalk\GembaWalkChecklist;
use App\Models\Inspection\GembaWalk\GembaWalkStatusLog;
use App\Models\Inspection\GembaWalk\GembaWalkChecklistFile;
use App\Models\Inspection\GembaWalk\GembaWalkInspectionEhsFile;
use App\Models\Inspection\GembaWalk\GembaWalkInspectionEhsApproval;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Master\Shift;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GembaWalkController extends Controller
{
    private $location;
    private $unit;
    private $employee;
    private $gembaWalk;
    private $gembaWalkCheckList;
    private $gembaWalkChecklistFile;
    private $gembaWalkInspectionEhsAprroval;
    private $gembaWalkInspectionEhsFile;
    private $user;
    private $statusLog;
    private $document_reference;
    private $shift;





    public function __construct()
    {
        $this->location = new Location();
        $this->unit = new Unit();
        $this->employee = new Employee();
        $this->gembaWalk = new GembaWalk();
        $this->gembaWalkCheckList = new GembaWalkChecklist();
        $this->gembaWalkChecklistFile = new GembaWalkChecklistFile();
        $this->gembaWalkInspectionEhsAprroval = new GembaWalkInspectionEhsApproval();
        $this->gembaWalkInspectionEhsFile = new GembaWalkInspectionEhsFile();
        $this->user = new User();
        $this->statusLog = new GembaWalkStatusLog();
        $this->document_reference = new InspectionStaticDocno();
        $this->shift = new Shift();
    }



    public function index(Request $request)
    {
        try {
            if (Auth::check()) {
                if ($request->ajax()) {
                    try {
                        $data =  $this->gembaWalk->list();
                        $datatables = Datatables::of($data['data'])
                            ->addIndexColumn()
                            ->addColumn('status', function ($row) {
                                $text = "<span style='color:red'>In-Active<span>";
                                if ($row->status == 1) {
                                    $text = "<span style='color:green;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->id) . "' data-type = '1' >Active<span>";
                                } else if ($row->status == 0) {
                                    $text = "<span style='color:red;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->id) . "' data-type = '0' >In-Active<span>";
                                }
                                return $text;
                            })
                            ->addColumn('date', function ($row) {
                                return Displaydateformat($row->date);
                            })
                            ->addColumn('shift', function ($row) {
                                return getShift($row->shift_id);
                            })
                            ->addColumn('created_at', function ($row) {
                                return Displaydateformat($row->created_at);
                            })
                            ->addColumn('created_by', function ($row) {
                                return getUsername($row->created_by);
                            })
                            ->addColumn('gemba_walk_status', function ($row) {
                                return "<span class='" . $row->bg_color . "' >" . $row->status_name . "</span>";
                            })
                            ->addColumn('action', function ($row) {
                                $btn = '<a href="' . admin_url('inspection/gemba-walk/view/' . encryptId($row->id)) . '" title="View"><i class="fa-solid fa-eye"></i></a> ';

                                if ($row->gemba_walk_status == GEMBA_WALK_INSPECTION_WAITING_FOR_CAPA_ACTION && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                    $btn .= '<a href="' . admin_url('inspection/gemba-walk/capa-verification/' . encryptId($row->id)) . '" title="' . __('CAPA Action') . '"><i class="fa-solid fa-check-to-slot text-danger"></i></a> ';
                                }

                                if (($row->gemba_walk_status == GEMBA_WALK_INSPECTION_WAITING_FOR_FLOOR_MANAGER_VERIFICATION || $row->gemba_walk_status == GEMBA_WALK_INSPECTION_REJECTED) && (CheckUserRole(ROLE_FLOOR_MANAGER) || isAdmin())) {
                                    $btn .= '<a href="' . admin_url('inspection/gemba-walk/floor-manager/' . encryptId($row->id)) . '" title="' . 'Floor Manager Action' . '"><i class="fa-solid fa-check-to-slot text-danger"></i></a> ';
                                }

                                if ($row->gemba_walk_status == GEMBA_WALK_INSPECTION_WAITING_FOR_EHS_OFFICER_VERIFICATION  && (CheckUserRole(ROLE_EHS_OFFICER) || isAdmin())) {
                                    $btn .= '<a href="' . admin_url('inspection/gemba-walk/ehs-officer/' . encryptId($row->id)) . '" title="' . 'EHS Officer Action' . '"><i class="fa-solid fa-check-to-slot text-primary"></i></a> ';
                                }

                                $btn .= '<a href="' . admin_url('inspection/gemba-walk/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF"> <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i></a>';
                                $btn .= '<a href="' . admin_url('inspection/gemba-walk/generalExcel/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF"> <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i></a>';

                                return $btn;
                            })
                            ->rawColumns(['action', 'created_date', 'created_by', 'date', 'shift', 'gemba_walk_status'])
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
            $shift = $this->shift->getShiftname();
            $data = array(
                'shift' => $shift,

            );

            return view('inspection.gembaWalk.list', $data);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'An error occurred while processing your request. Please try again later.'], 500);
        }
    }



    public function Add(Request $request)
    {
        try {
            $locationList = $this->location->getLocationName();
            $unitList = $this->unit->getUnitList();
            $employeeList = $this->employee->getEmployeeList();
            $document_no = $this->document_reference->selectUsingName('GembaWalk');
            $shift = $this->shift->getShiftname();


            $data = array(
                'locationList' => $locationList,
                'unitList' => $unitList,
                'employeeList' => $employeeList,
                'document_no' => $document_no,
                'shift' => $shift,

            );
            return view('inspection.gembaWalk.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        // dd($request->all());
        try {
            $rules = [
                'document_no' => 'required',
                'document_upload_date' => 'required',
                'document_revision_date' => 'required',
                'observation_needed' => 'required',
                'gemba_walk.*.location_id' => 'required',
                'gemba_walk.*.unit_id' => 'required',
                'gemba_walk.*.date_of_observation' => 'required',
                'gemba_walk.*.observation_type' => 'required|integer',
                'gemba_walk.*.checklist_description' => 'nullable|string',
                'gemba_walk.*.hazard' => 'nullable|string',
                'gemba_walk.*.checklist_capa' => 'nullable|string',
                'gemba_walk.*.date_of_compliance' => 'nullable',
                'gemba_walk.*.responsibility_id' => 'nullable',
                'gemba_walk.*.current_status' => 'nullable|string',
                'gemba_walk.*.checklist_remark' => 'nullable|string',
            ];

            $messages = [
                'document_no.required' => 'Document number is required.',
                'document_upload_date.required' => 'Please provide the document upload date.',
                'document_revision_date.required' => 'Please provide the document revision date.',
                'observation_needed.required' => 'Please provide the observation.',
                'gemba_walk.*.location_id.required' => 'Location ID is required.',
                'gemba_walk.*.unit_id.required' => 'Unit ID is required.',
                'gemba_walk.*.date_of_observation.required' => 'Date of observation is required.',
                'gemba_walk.*.date_of_observation.date_format' => 'Date of observation must be in the format dd-mm-yyyy.',
                'gemba_walk.*.observation_type.required' => 'Observation type is required.',
                'gemba_walk.*.observation_type.integer' => 'Observation type must be a number.',
                'gemba_walk.*.checklist_description.string' => 'Checklist description must be a valid text.',
                'gemba_walk.*.hazard.string' => 'Hazard must be a valid text.',
                'gemba_walk.*.checklist_capa.string' => 'Checklist CAPA must be a valid text.',
                'gemba_walk.*.date_of_compliance.date_format' => 'Date of compliance must be in the format dd-mm-yyyy.',
                'gemba_walk.*.checklist_remark.string' => 'Checklist remark must be a valid text.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $gembaWalk = $this->gembaWalk->store();
            $inspection_type = GEMBA_WALK;

            $gembaWalk_singnature = $this->gembaWalkChecklistFile->storeSignature($gembaWalk->id);
            $gembaWalkChecklist = $this->gembaWalkCheckList->store($gembaWalk->id);

            $user_role = ROLE_EHS_OFFICER;
            $to_status = GEMBA_WALK_INSPECTION_WAITING_FOR_CAPA_ACTION;
            $mailsubject = 'Gemba Walk Report has been submitted';
            $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
            $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();
            if (count($users) > 0) {
                foreach ($users as $user) {
                    $email_id = $user->email;
                    $id = $gembaWalk->id;

                    if (!empty($email_id)) {
                        $gembaWalk_details = $this->gembaWalk->selectMail($id);

                        if ($gembaWalk_details) {
                            $gembaWalk_array = $gembaWalk_details->toArray();

                            $gembaWalk_array['name'] = $user->name;
                            $gembaWalk_array['email_id'] = $email_id;
                            $gembaWalk_array['mail_subject'] = $mailsubject;

                            Mail::to($gembaWalk_array['email_id'])->queue(new GembaWalkMail($gembaWalk_array));
                        }
                    }
                }
            }


            $notificationData = array(
                'notification_type' => 9,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => 'Gemba Walk Report ' . $gembaWalk->gemba_walk_auto_id . ' submitted by ' . getUsername($gembaWalk->created_by),
                    'icon' =>  admin_url('public/assets/icons/accident.png'),
                    'id' => $gembaWalk->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('inspection/gemba-walk/list'),
                'assigned_user' => array_to_string($userids),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $insert_array = array(
                'gemba_walk_id' => $gembaWalk->id,
                'from_status' => GEMBA_WALK_INSPECTION_START,
                'to_status' => $to_status,
                'is_reject' => null,
                'remarks' => null,
                'approved_by' => Auth::id(),
            );
            $this->statusLog->create($insert_array);

            Session::flash('success', 'Your data has been created successfully!');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
        }
        if ($gembaWalk->observation_needed == 1) {
            return redirect(admin_url('fire/checklist-observation/add/' . encryptId($inspection_type) . '/' . encryptId($id)));
        } else {
            return redirect(admin_url('inspection/gemba-walk/list'));
        }
    }

    public function view($id)
    {
        try {
            if (Auth::check()) {
                $id = decryptId($id);

                $gembaWalk_details = $this->gembaWalk->selectOne($id);
                $getUserId = $this->gembaWalk->getUserId($id);
                $type = GEMBA_WALK;
                $gembaWalk_approved_singnature = GetSignature($getUserId->created_by, $id, $type);
                $gembaWalk_verified_singnature = GetSignature($getUserId->updated_by, $id, $type);
                $status_log = $this->statusLog->selectOne($id);
                $gembaWalk_ehs_capa_details = $this->gembaWalkInspectionEhsAprroval->getEHSCapaReview($id);
                $gembaWalk_ehs_floor_manager_details = $this->gembaWalkInspectionEhsAprroval->getEHSFloormanagerReview($id);
                $gembaWalk_ehs_verificatioin_details = $this->gembaWalkInspectionEhsAprroval->getEHSOfficerReview($id);
                $document_no = $this->document_reference->selectOne($getUserId->document_reference_id);



                $data = array(
                    'gembaWalk_details' => $gembaWalk_details,
                    'gembaWalk_approved_singnature' => $gembaWalk_approved_singnature,
                    'gembaWalk_verified_singnature' => $gembaWalk_verified_singnature,
                    'status_log' => $status_log,
                    'gembaWalk_ehs_capa_details' => $gembaWalk_ehs_capa_details,
                    'gembaWalk_ehs_floor_manager_details' => $gembaWalk_ehs_floor_manager_details,
                    'gembaWalk_ehs_verificatioin_details' => $gembaWalk_ehs_verificatioin_details,
                    'document_no' => $document_no,


                );
            }
            // dd($data);
            return view('inspection.gembaWalk.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function approvals($id)

    {
        try {
            $id = decryptId($id);
            if (Auth::check()) {
                $gembaWalk_details = $this->gembaWalk->selectOne($id);
                $getUserId = $this->gembaWalk->getUserId($id);
                $type = GEMBA_WALK;
                $gembaWalk_approved_singnature = GetSignature($getUserId->created_by, $id, $type);
                $document_no = $this->document_reference->selectOne($getUserId->document_reference_id);



                $data = array(
                    'gembaWalk_details' => $gembaWalk_details,
                    'gembaWalk_approved_singnature' => $gembaWalk_approved_singnature,
                    'document_no' => $document_no,

                );
            }
            return view('inspection.gembaWalk.approval', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }


    public function CAPASubmit(Request $request)
    {
        try {

            $rules = [
                'officer_name' => 'required',
                'capa_date' => 'required|after_or_equal:today',
            ];

            $messages = [
                'officer_name.required' => 'The Officer Name is required.',
                'capa_date.required' => 'The CAPA Date is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            if ($request->is_passed == "1") {
                $capa_type = GEMBA_WALK_INSPECTION_PASS_L1;

                $gembaWalk_id = decryptId($request->id);
                $gembaWalk_ehs = $this->gembaWalkInspectionEhsAprroval->capaSubmit($gembaWalk_id, $capa_type);
                $ehs_id = $gembaWalk_ehs->id;

                $upload_status = GEMBA_WALK_INSPECTION_EHS_FILE_TYPE_1;
                $gembaWalk_inspection_file = $this->gembaWalkInspectionEhsFile->capaFileSubmit($gembaWalk_id, $ehs_id, $upload_status);

                $gembaWalk_status = GEMBA_WALK_INSPECTION_WAITING_FOR_FLOOR_MANAGER_VERIFICATION;


                $gembaWalk_status = $this->gembaWalk->updateStatus($gembaWalk_id, $gembaWalk_status);
                $to_status = GEMBA_WALK_INSPECTION_WAITING_FOR_FLOOR_MANAGER_VERIFICATION;
                //mail
                $mailsubject = 'Gemba Walk CAPA Action  Report has been Submitted';
                $user_roles = [ROLE_FLOOR_MANAGER, ROLE_EHS_OFFICER, ROLE_UNIT_HEAD, ROLE_EHS_HEAD];
                $userids = [];
                $users = collect();

                foreach ($user_roles as $user_role) {
                    $roleUsers = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')
                        ->get();

                    $userids = array_merge($userids, $roleUsers->pluck('id')->toArray());
                    $users = $users->merge($roleUsers);
                }

                $userids = array_unique($userids);

                $gembaWalk_details = $this->gembaWalk->selectmail($gembaWalk_id);
                $gembaWalk_status = $this->gembaWalkInspectionEhsAprroval->getStatus($gembaWalk_id);

                if ($users->count() > 0) {
                    foreach ($users as $user) {
                        $email_id = $user->email;

                        if (!empty($email_id)) {
                            $gembaWalk_details = $this->gembaWalk->selectmail($gembaWalk_id);
                            $gembaWalk = $gembaWalk_details->toArray();

                            $gembaWalk['name'] = $user->name;
                            $gembaWalk['email_id'] = $email_id;
                            $gembaWalk['mail_subject'] = $mailsubject;

                            // Send email
                            Mail::to($gembaWalk['email_id'])->queue(new GembaWalkMail($gembaWalk));
                        }
                    }
                }

                $notificationData = array(
                    'notification_type' => 9,
                    'module_type' => 3,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'CAPA Action  ' . $gembaWalk_details->gemba_walk_auto_id . ' Submmited by ' . getUsername($gembaWalk_details->created_by),
                        'icon' => admin_url('public/assets/icons/permit_to_work.png'),
                        'id' => $gembaWalk_details->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('inspection/gemba-walk/list'),
                    'assigned_user' => implode(',', $userids),
                    'created_by' => Auth::id(),
                );

                notificationSave($notificationData);

                $insert_array = array(
                    'gemba_walk_id' => $gembaWalk_details->id,
                    'from_status' => GEMBA_WALK_INSPECTION_WAITING_FOR_CAPA_ACTION,
                    'to_status' => $to_status,
                    'is_reject' => $gembaWalk_status->capa,
                    'remarks' => $gembaWalk_status->remarks,
                    'approved_by' => Auth::id(),
                );

                $this->statusLog->create($insert_array);
            } else {

                $gembaWalk_status = GEMBA_WALK_INSPECTION_CLOSED;
                $capa_type = GEMBA_WALK_INSPECTION_PASS;
                $gembaWalk_id = decryptId($request->id);
                $gembaWalk_ehs = $this->gembaWalkInspectionEhsAprroval->capaSubmit($gembaWalk_id, $capa_type);
                $ehs_id = $gembaWalk_ehs->id;

                $gembaWalk_singnature = $this->gembaWalkChecklistFile->storeVerifiedSignature($gembaWalk_id);
                $gembaWalk_status = $this->gembaWalk->updateStatus($gembaWalk_id, $gembaWalk_status);

                //mail
                $mailsubject = 'Gemba Walk has been Approved';
                $user_roles = [ROLE_FLOOR_MANAGER, ROLE_EHS_OFFICER, ROLE_UNIT_HEAD, ROLE_EHS_HEAD];
                $userids = [];
                $users = collect();

                foreach ($user_roles as $user_role) {
                    $roleUsers = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')
                        ->get();

                    $userids = array_merge($userids, $roleUsers->pluck('id')->toArray());
                    $users = $users->merge($roleUsers);
                }

                $userids = array_unique($userids);
                $gembaWalk_details = $this->gembaWalk->selectmail($gembaWalk_id);
                $gembaWalk_status = $this->gembaWalkInspectionEhsAprroval->getApproveStatus($gembaWalk_id);
                $to_status = GEMBA_WALK_INSPECTION_CLOSED;

                if ($users->count() > 0) {
                    foreach ($users as $user) {
                        $email_id = $user->email;
                        if (!empty($email_id)) {
                            $gembaWalk_details = $this->gembaWalk->selectmail($gembaWalk_id);
                            $gembaWalk = $gembaWalk_details->toArray();
                            $gembaWalk['name'] = $user->name;
                            $gembaWalk['email_id'] = $email_id;
                            $gembaWalk['mail_subject'] = $mailsubject;
                            Mail::to($gembaWalk['email_id'])->queue(new GembaWalkMail($gembaWalk));
                        }
                    }
                }

                $notificationData = array(
                    'notification_type' => 9,
                    'module_type' => 3,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Gemba Walk ' . $gembaWalk_details->gemba_walk_auto_id . ' has been Approved by ' . getUsername($gembaWalk_details->created_by),
                        'icon' => admin_url('public/assets/icons/permit_to_work.png'),
                        'id' => $gembaWalk_details->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('inspection/gemba-walk/list'),
                    'assigned_user' => implode(',', $userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $insert_array = array(
                    'gemba_walk_id' => $gembaWalk_details->id,
                    'from_status' => GEMBA_WALK_INSPECTION_WAITING_FOR_CAPA_ACTION,
                    'to_status' => $to_status,
                    'is_reject' => $gembaWalk_status->capa,
                    'remarks' =>  $gembaWalk_status->remarks,
                    'approved_by' => Auth::id(),
                );

                $this->statusLog->create($insert_array);
            }

            Session::flash('success', __('inspection.capa_action_success_msg'));
            return redirect(admin_url('inspection/gemba-walk/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('inspection/gemba-walk/list'));
        }
    }

    public function review($id)
    {
        try {
            if (Auth::check()) {
                $gembaWalk_id = decryptId($id);
                $gembaWalk_details = $this->gembaWalk->selectOne($gembaWalk_id);
                $getUserId = $this->gembaWalk->getUserId($gembaWalk_id);
                $type = GEMBA_WALK;
                $gembaWalk_approved_singnature = GetSignature($getUserId->created_by, $id, $type);
                $gembaWalk_ehs_capa_details = $this->gembaWalkInspectionEhsAprroval->getEHSCapaReview($gembaWalk_id);
                $floorID = $this->gembaWalkInspectionEhsAprroval->select('id')->where('type', 2)->where('gemba_walk_id', $gembaWalk_id)->where('status', 1)->first();
                $document_no = $this->document_reference->selectOne($getUserId->document_reference_id);

                $data = array(
                    'gembaWalk_details' => $gembaWalk_details,
                    'gembaWalk_ehs_capa_details' => $gembaWalk_ehs_capa_details,
                    'floorID' => $floorID,
                    'gembaWalk_approved_singnature' => $gembaWalk_approved_singnature,
                    'document_no' => $document_no,


                );
            }
            return view('inspection.gembaWalk.approval', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function capaReviewSubmit(Request $request)
    {
        try {
            $rules = [
                'officer_name' => 'required',
                'capa_date' => 'required',
                'capa_remark' => 'required',
            ];

            $messages = [
                'officer_name.required' => 'The Officer Name is required.',
                'capa_date.required' => 'The CAPA Date is required.',
                'capa_remark.required' => 'The CAPA Date is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $gembaWalk_id = decryptId($request->id);

            $upload_status = GEMBA_WALK_INSPECTION_EHS_FILE_TYPE_2;
            $gembaWalk_status = GEMBA_WALK_INSPECTION_WAITING_FOR_EHS_OFFICER_VERIFICATION;
            $capa_type = GEMBA_WALK_INSPECTION_PASS_L2;


            $ehsOfficer_verify = $this->gembaWalkInspectionEhsAprroval->capaSubmit($gembaWalk_id, $capa_type);
            $ehs_id = $ehsOfficer_verify->id;
            $gembaWalk_inspection_file = $this->gembaWalkInspectionEhsFile->capaFileSubmit($gembaWalk_id, $ehs_id, $upload_status);

            $gemba_Walk_status = $this->gembaWalk->updateStatus($gembaWalk_id, $gembaWalk_status);

            $user_role = ROLE_EHS_OFFICER;

            $mailsubject = 'Gemba Walk Report has been Verified by Floor Manager';
            $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
            $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();
            $gembaWalk_status = $this->gembaWalkInspectionEhsAprroval->getFloorApproveStatus($gembaWalk_id);
            $to_status = GEMBA_WALK_INSPECTION_WAITING_FOR_EHS_OFFICER_VERIFICATION;

            if (count($users) > 0) {
                foreach ($users as $user) {
                    $email_id = $user->email;

                    if (!empty($email_id)) {
                        $gembaWalk_details = $this->gembaWalk->selectMail($gembaWalk_id);

                        if ($gembaWalk_details) {
                            $gembaWalk_array = $gembaWalk_details->toArray();

                            $gembaWalk_array['name'] = $user->name;
                            $gembaWalk_array['email_id'] = $email_id;
                            $gembaWalk_array['mail_subject'] = $mailsubject;

                            Mail::to($gembaWalk_array['email_id'])->queue(new GembaWalkMail($gembaWalk_array));
                        }
                    }
                }
            }


            $notificationData = array(
                'notification_type' => 9,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => 'Gemba Walk Report ' . $gembaWalk_details->gemba_walk_auto_id . ' submitted by ' . getUsername($gembaWalk_details->created_by),
                    'icon' =>  admin_url('public/assets/icons/accident.png'),
                    'id' => $gembaWalk_details->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('inspection/gemba-walk/list'),
                'assigned_user' => array_to_string($userids),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $insert_array = array(
                'gemba_walk_id' => $gembaWalk_details->id,
                'from_status' => GEMBA_WALK_INSPECTION_WAITING_FOR_FLOOR_MANAGER_VERIFICATION,
                'to_status' => $to_status,
                'is_reject' => null,
                'remarks' => $gembaWalk_status->remarks,
                'approved_by' => Auth::id(),
            );

            $this->statusLog->create($insert_array);

            Session::flash('success', 'Floor Manager Verification successfully');
            return redirect(admin_url('inspection/gemba-walk/list'));
        } catch (Exception $ex) {
            report($ex);
        }
    }


    public function ehsOfficerReview($id)
    {
        try {
            if (Auth::check()) {
                $gembaWalk_id = decryptId($id);
                $gembaWalk_details = $this->gembaWalk->selectOne($gembaWalk_id);
                $gembaWalk_ehs_capa_details = $this->gembaWalkInspectionEhsAprroval->getEHSCapaReview($gembaWalk_id);
                $gembaWalk_ehs_floor_manager_details = $this->gembaWalkInspectionEhsAprroval->getEHSFloormanagerReview($gembaWalk_id);
                $getUserId = $this->gembaWalk->getUserId($gembaWalk_id);
                $type = GEMBA_WALK;
                $gembaWalk_approved_singnature = GetSignature($getUserId->created_by, $id, $type);
                $ehsId = $this->gembaWalkInspectionEhsAprroval->select('id')->where('type', 3)->where('gemba_walk_id', $gembaWalk_id)->where('status', 1)->first();
                $document_no = $this->document_reference->selectOne($getUserId->document_reference_id);

                $data = array(
                    'gembaWalk_details' => $gembaWalk_details,
                    'gembaWalk_ehs_capa_details' => $gembaWalk_ehs_capa_details,
                    'gembaWalk_ehs_floor_manager_details' => $gembaWalk_ehs_floor_manager_details,
                    'gembaWalk_approved_singnature' => $gembaWalk_approved_singnature,
                    'ehsId' => $ehsId,
                    'document_no' => $document_no,
                );
            }
            return view('inspection.gembaWalk.approval', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }


    public function ehsReviewSubmit(Request $request)
    {
        try {
            $rules = [
                'officer_name' => 'required',
                'capa_date' => 'required',
                'capa_remark' => 'required',
            ];

            $messages = [
                'officer_name.required' => 'The Officer Name is required.',
                'capa_date.required' => 'The CAPA Date is required.',
                'capa_remark.required' => 'The CAPA Date is required.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $gembaWalk_id = decryptId($request->id);


            if ($request->input('action') == 'approve') {

                $capa_type = GEMBA_WALK_INSPECTION_PASS;
                $gembaWalk_status = GEMBA_WALK_INSPECTION_CLOSED;

                $gembaWalk_ehs = $this->gembaWalkInspectionEhsAprroval->capaSubmit($gembaWalk_id, $capa_type);

                $gembaWalk_singnature = $this->gembaWalkChecklistFile->storeVerifiedSignature($gembaWalk_id);
                $gembaWalk_status = $this->gembaWalk->updateStatus($gembaWalk_id, $gembaWalk_status);

                // mail
                $mailsubject = 'Gemba Walk has been Approved';
                $user_roles = [ROLE_FLOOR_MANAGER, ROLE_EHS_OFFICER, ROLE_UNIT_HEAD, ROLE_EHS_HEAD];
                $userids = [];
                $users = collect();

                foreach ($user_roles as $user_role) {
                    $roleUsers = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')
                        ->get();

                    $userids = array_merge($userids, $roleUsers->pluck('id')->toArray());
                    $users = $users->merge($roleUsers);
                }

                $userids = array_unique($userids);
                $get_ehs_review = $this->gembaWalkInspectionEhsAprroval->getApproveStatus($gembaWalk_id);
                $to_status = GEMBA_WALK_INSPECTION_CLOSED;

                if ($users->count() > 0) {
                    foreach ($users as $user) {
                        $email_id = $user->email;

                        if (!empty($email_id)) {
                            $gembaWalk_details = $this->gembaWalk->selectmail($gembaWalk_id);
                            $gembaWalk = $gembaWalk_details->toArray();

                            $gembaWalk['name'] = $user->name;
                            $gembaWalk['email_id'] = $email_id;
                            $gembaWalk['mail_subject'] = $mailsubject;

                            // Send email
                            Mail::to($gembaWalk['email_id'])->queue(new GembaWalkMail($gembaWalk));
                        }
                    }
                }

                $notificationData = array(
                    'notification_type' => 9,
                    'module_type' => 3,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Gemba Walk Report ' . $gembaWalk_ehs->gemba_walk_auto_id . 'has been Approved' . getUsername($gembaWalk_ehs->created_by),
                        'icon' => admin_url('public/assets/icons/permit_to_work.png'),
                        'id' => $gembaWalk_ehs->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('inspection/gemba-walk/list'),
                    'assigned_user' => implode(',', $userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $insert_array = array(
                    'gemba_walk_id' => $gembaWalk_details->id,
                    'from_status' => GEMBA_WALK_INSPECTION_WAITING_FOR_EHS_OFFICER_VERIFICATION,
                    'to_status' => $to_status,
                    'is_reject' => null,
                    'remarks' => $get_ehs_review->remarks,
                    'approved_by' => Auth::id(),
                );

                $this->statusLog->create($insert_array);
            } elseif ($request->input('action') == 'reject') {

                $capa_type = GEMBA_WALK_INSPECTION_FAIL;
                $gembaWalk_status = GEMBA_WALK_INSPECTION_REJECTED;

                $gembaWalk_ehs = $this->gembaWalkInspectionEhsAprroval->capaSubmit($gembaWalk_id, $capa_type);

                $gembaWalk_status = $this->gembaWalk->updateStatus($gembaWalk_id, $gembaWalk_status);

                $mailsubject = 'GembaWalk Report has been Rejected by EHS Officer';
                $user_roles = [ROLE_FLOOR_MANAGER, ROLE_EHS_OFFICER, ROLE_UNIT_HEAD, ROLE_EHS_HEAD];
                $userids = [];
                $users = collect();

                foreach ($user_roles as $user_role) {
                    $roleUsers = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')
                        ->get();

                    $userids = array_merge($userids, $roleUsers->pluck('id')->toArray());
                    $users = $users->merge($roleUsers);
                }

                $userids = array_unique($userids);
                $get_ehs_review = $this->gembaWalkInspectionEhsAprroval->getEHSReview($gembaWalk_id);
                $to_status = GEMBA_WALK_INSPECTION_REJECTED;
                if ($users->count() > 0) {
                    foreach ($users as $user) {
                        $email_id = $user->email;

                        if (!empty($email_id)) {
                            $gembaWalk_details = $this->gembaWalk->selectmail($gembaWalk_id);
                            $gembaWalk = $gembaWalk_details->toArray();

                            $gembaWalk['name'] = $user->name;
                            $gembaWalk['email_id'] = $email_id;
                            $gembaWalk['mail_subject'] = $mailsubject;

                            // Send email
                            Mail::to($gembaWalk['email_id'])->queue(new GembaWalkMail($gembaWalk));
                        }
                    }
                }


                $notificationData = array(
                    'notification_type' => 9,
                    'module_type' => 3,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Gemba Walk Report ' . $gembaWalk_ehs->gemba_walk_auto_id . ' Cancelled by ' . getUsername($gembaWalk_ehs->created_by),
                        'icon' => admin_url('public/assets/icons/permit_to_work.png'),
                        'id' => $gembaWalk_ehs->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('inspection/gemba-walk/list'),
                    'assigned_user' => implode(',', $userids), // Assign all user IDs
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $insert_array = array(
                    'gemba_walk_id' => $gembaWalk_details->id,
                    'from_status' => GEMBA_WALK_INSPECTION_WAITING_FOR_EHS_OFFICER_VERIFICATION,
                    'to_status' => $to_status,
                    'is_reject' => null,
                    'remarks' => $get_ehs_review->remarks,
                    'approved_by' => Auth::id(),
                );

                $this->statusLog->create($insert_array);
            }

            Session::flash('success', '  EHS Officer Verication successfully');
            return redirect(admin_url('inspection/gemba-walk/list'));
        } catch (Exception $ex) {
            report($ex);
        }
    }



    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $gembaWalk_details = $this->gembaWalk->selectOne($id);
                $status_log = $this->statusLog->selectOne($id);
                $getUserId = $this->gembaWalk->getUserId($id);
                $type = GEMBA_WALK;
                $gembaWalk_approved_singnature = GetSignature($getUserId->created_by, $id, $type);
                $gembaWalk_verified_singnature = GetSignature($getUserId->updated_by, $id, $type);
                $gembaWalk_ehs_capa_details = $this->gembaWalkInspectionEhsAprroval->getEHSCapaReview($id);
                $gembaWalk_ehs_floor_manager_details = $this->gembaWalkInspectionEhsAprroval->getEHSFloormanagerReview($id);
                $gembaWalk_ehs_verificatioin_details = $this->gembaWalkInspectionEhsAprroval->getEHSOfficerReview($id);
                $document_no = $this->document_reference->selectOne($getUserId->document_reference_id);


                $data = [
                    'gembaWalk_details' => $gembaWalk_details,
                    'status_log' => $status_log,
                    'gembaWalk_ehs_capa_details' => $gembaWalk_ehs_capa_details,
                    'gembaWalk_ehs_floor_manager_details' => $gembaWalk_ehs_floor_manager_details,
                    'gembaWalk_ehs_verificatioin_details' => $gembaWalk_ehs_verificatioin_details,
                    'gembaWalk_approved_singnature' => $gembaWalk_approved_singnature,
                    'gembaWalk_verified_singnature' => $gembaWalk_verified_singnature,
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

            $html = view('inspection.gembaWalk.generalpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "Gemba Walk Details.pdf";
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
            $gembaWalk_details = $this->gembaWalk->selectOne($id);
            $getUserId = $this->gembaWalk->getUserId($id);
            $type = GEMBA_WALK;

            $gembaWalk_approved_singnature = GetSignature($getUserId->created_by, $id, $type);
            $gembaWalk_verified_singnature = GetSignature($getUserId->updated_by, $id, $type);
            $document_no = $this->document_reference->selectOne($getUserId->document_reference_id);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

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

            $sheet->mergeCells('A1:B3'); // Merge first

            $sheet->getStyle('A1:B3')->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);




            $sheet->mergeCells("C1:K3");
            $sheet->setCellValue("C1", "Gemba Walk Report");
            $sheet->getStyle("C1")->applyFromArray([
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '000000']]],

            ]);

            $gemba = $gembaWalk_details->first();

            $sheet->getRowDimension(4)->setRowHeight(30);

            $sheet->mergeCells("A4:G4");
            $sheet->setCellValue("A4", "Date: " . Displaydateformat($gemba->date));

            $sheet->mergeCells("H4:N4");
            $sheet->setCellValue("H4", "Shift: " . getShift($gemba->shift_id));

            $sheet->getStyle('A4:N4')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '000000']]],

            ]);


            $headerLabels = [
                'L1:M1' => 'Doc. No.',
                'L2:M2' => 'Issue Dt.',
                'L3:M3' => 'Rev. & Dt.',
            ];

            foreach ($headerLabels as $cellRange => $label) {
                $cell = explode(':', $cellRange)[0];
                $sheet->mergeCells($cellRange)->setCellValue($cell, $label);
                $sheet->getStyle($cell)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '000000']]],

                ]);
            }

            $sheet->setCellValue("N1", $document_no->doc_no);
            $sheet->setCellValue("N2", Displaydateformat($document_no->issue_date));
            $sheet->setCellValue("N3", $document_no->rev_dt);

            $sheet->getStyle("L1:N3")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '000000']]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $headers = [
                'Sr.',
                'Location',
                'Unit',
                'Date of Observation',
                'Observation Type',
                'Description',
                'Hazard',
                'Image',
                'CAPA',
                'Date of Compliance',
                'Responsible Person',
                'Status',
                'Remark',
                'Observation'
            ];

            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue("{$col}5", $header);
                $sheet->getStyle("{$col}5")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $col++;
            }

            $row = 6;
            $sr = 1;
            foreach ($gembaWalk_details as $data) {
                $sheet->setCellValue("A{$row}", $sr);
                $sheet->setCellValue("B{$row}", getLocationname($data->location_id ?? ''));
                $sheet->setCellValue("C{$row}", getUnitname($data->unit_id ?? ''));
                $sheet->setCellValue("D{$row}", displayDateFormat($data->date_of_observation ?? ''));
                $sheet->setCellValue("E{$row}", getObservationType($data->observation_type_id ?? ''));
                $sheet->setCellValue("F{$row}", $data->description ?? '');
                $sheet->setCellValue("G{$row}", $data->hazard ?? '');

                // Insert Image if exists
                if (!empty($data->file_path)) {
                    $imagePath = public_path($data->file_path);
                    if (file_exists($imagePath)) {
                        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawing->setPath($imagePath);
                        $drawing->setCoordinates("H{$row}");
                        $drawing->setOffsetX(5);
                        $drawing->setOffsetY(5);
                        $drawing->setWidth(80);
                        $drawing->setWorksheet($sheet);
                        $sheet->getRowDimension($row)->setRowHeight(90);
                        $sheet->getColumnDimension('H')->setWidth(20);
                    } else {
                        $sheet->setCellValue("H{$row}", 'Image not found');
                    }
                } else {
                    $sheet->setCellValue("H{$row}", 'No image');
                }

                $sheet->setCellValue("I{$row}", $data->capa ?? '');
                $sheet->setCellValue("J{$row}", displayDateFormat($data->date_of_compliance ?? ''));
                $sheet->setCellValue("K{$row}", getEmployeename($data->responsibility_id ?? ''));
                $sheet->setCellValue("L{$row}", getGembaWalkStatus($data->status ?? ''));
                $sheet->setCellValue("M{$row}", $data->remark ?? '');
                $sheet->setCellValue("N{$row}", $data->observation_needed == '1' ? 'YES' : 'NO');

                $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $row++;
                $sr++;
            }

            foreach (range('A', 'N') as $col) {
                if ($col !== 'H') {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }

            $signatureStartRow = $row;
            $signatureEndRow = $signatureStartRow + 3;
            $labelRow = $signatureEndRow + 1;
            $imageHeight = 60;

            // GembaWalk Approved Singnature
            $sheet->mergeCells("A{$signatureStartRow}:G{$signatureEndRow}");
            if (file_exists($gembaWalk_approved_singnature)) {
                $drawing = new Drawing();
                $drawing->setName('GembaWalk Approved Singnature');
                $drawing->setDescription('GembaWalk Approved Singnature');
                $drawing->setPath($gembaWalk_approved_singnature);
                $drawing->setCoordinates("D{$signatureStartRow}");
                $drawing->setOffsetX(130);
                $drawing->setOffsetY(10);
                $drawing->setWidth($imageHeight);
                $drawing->setHeight($imageHeight);
                $drawing->setWorksheet($sheet);
            }

            $sheet->mergeCells("A{$labelRow}:G{$labelRow}")->setCellValue("A{$labelRow}", "GembaWalk Approved Singnature");

            // GembaWalk Verified Singnature
            $sheet->mergeCells("H{$signatureStartRow}:N{$signatureEndRow}");
            if (file_exists($gembaWalk_verified_singnature)) {
                $drawing = new Drawing();
                $drawing->setName('GembaWalk Verified Singnature');
                $drawing->setDescription('GembaWalk Verified Singnature');
                $drawing->setPath($gembaWalk_verified_singnature);
                $drawing->setCoordinates("J{$signatureStartRow}");
                $drawing->setOffsetX(130);
                $drawing->setOffsetY(10);
                $drawing->setWidth($imageHeight);
                $drawing->setHeight($imageHeight);
                $drawing->setWorksheet($sheet);
            }

            $sheet->mergeCells("H{$labelRow}:N{$labelRow}")->setCellValue("H{$labelRow}", "GembaWalk Verified Singnature");

            $sheet->getStyle("A{$labelRow}:G{$labelRow}")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle("H{$labelRow}:N{$labelRow}")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle("A{$signatureStartRow}:G{$signatureEndRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle("H{$signatureStartRow}:N{$signatureEndRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $fileName = 'GembaWalk_Report.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Exception $e) {
            dd($e);
            report($e);
            return back()->with('error', 'Failed to export Gemba Walk data.');
        }
    }


    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->gembaWalk->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }



            $data = array(
                'content' => $allData,
                'pagetitle' => "Gemba Walk Details",
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

            $view = view('inspection.gembawalk.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Gemba Walk.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('inspection/gemba-walk/list'));
        }
    }

    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->gembaWalk->exportdata();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            for ($i = 1; $i <= 200; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }

            $row = 1;

            foreach ($allData as $groupedDetails) {
                $inspection_detail = $groupedDetails->first();
                $id = $inspection_detail->inspection_id;

                $gembaWalk_details = $this->gembaWalk->selectOne($id);
                $getUserId = $this->gembaWalk->getUserId($id);
                $type = GEMBA_WALK;

                $approvedSignature = GetSignature($getUserId->created_by ?? '', $id, $type);
                $verifiedSignature = GetSignature($getUserId->updated_by ?? '', $id, $type);
                $document_no = $this->document_reference->selectOne($getUserId->document_reference_id ?? '');
                $currentRow = $row;

                $logoPath = public_path('assets/images/logo-dark.png');
                if (file_exists($logoPath)) {
                    $sheet->mergeCells("A$currentRow:B" . ($currentRow + 2));

                    $drawing = new Drawing();
                    $drawing->setName('Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates('A' . $currentRow);
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);
                    $drawing->setHeight(60);
                    $drawing->setWorksheet($sheet);
                }

                $sheet->mergeCells("A{$currentRow}:B" . ($currentRow + 2));
                $sheet->getStyle("A{$currentRow}:B" . ($currentRow + 2))->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->mergeCells("C{$currentRow}:K" . ($currentRow + 2));
                $sheet->setCellValue("C{$currentRow}", "Gemba Walk Report");
                $sheet->getStyle("C{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $gemba = $gembaWalk_details->first();

                $sheet->mergeCells("A" . ($currentRow + 3) . ":G" . ($currentRow + 3));
                $sheet->mergeCells("H" . ($currentRow + 3) . ":N" . ($currentRow + 3));
                $sheet->setCellValue("A" . ($currentRow + 3), "Date: " . Displaydateformat($gemba->date));
                $sheet->setCellValue("H" . ($currentRow + 3), "Shift: " . getShift($gemba->shift_id));
                $sheet->getStyle("A" . ($currentRow + 3) . ":N" . ($currentRow + 3))->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->mergeCells("L{$currentRow}:M{$currentRow}")->setCellValue("L{$currentRow}", 'Doc. No.');
                $sheet->mergeCells("L" . ($currentRow + 1) . ":M" . ($currentRow + 1))->setCellValue("L" . ($currentRow + 1), 'Issue Dt.');
                $sheet->mergeCells("L" . ($currentRow + 2) . ":M" . ($currentRow + 2))->setCellValue("L" . ($currentRow + 2), 'Rev. & Dt.');

                $sheet->setCellValue("N{$currentRow}", $document_no->doc_no ?? '');
                $sheet->setCellValue("N" . ($currentRow + 1), Displaydateformat($document_no->issue_date ?? ''));
                $sheet->setCellValue("N" . ($currentRow + 2), $document_no->rev_dt ?? '');

                $sheet->getStyle("L{$currentRow}:N" . ($currentRow + 2))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '000000']]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $columnWidths = [
                    'A' => 5,
                    'B' => 20,
                    'C' => 15,
                    'D' => 18,
                    'E' => 18,
                    'F' => 15,
                    'G' => 20,
                    'H' => 20,
                    'I' => 15,
                    'J' => 20,
                    'K' => 25,
                    'L' => 15,
                    'M' => 25,
                    'N' => 15,
                ];

                foreach ($columnWidths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                $headers = ['Sr.', 'Location', 'Unit', 'Date of Observation', 'Observation Type', 'Description', 'Hazard', 'Image', 'CAPA', 'Date of Compliance', 'Responsible Person', 'Status', 'Remark', 'Observation'];
                $col = 'A';
                foreach ($headers as $header) {
                    $sheet->setCellValue("{$col}" . ($row + 4), $header);
                    $sheet->getStyle("{$col}" . ($row + 4))->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    $col++;
                }

                $detIL_row = $currentRow + 5;
                $sr = 1;
                foreach ($gembaWalk_details as $data) {
                    $sheet->setCellValue("A{$detIL_row}", $sr);
                    $sheet->setCellValue("B{$detIL_row}", getLocationname($data->location_id ?? ''));
                    $sheet->setCellValue("C{$detIL_row}", getUnitname($data->unit_id ?? ''));
                    $sheet->setCellValue("D{$detIL_row}", displayDateFormat($data->date_of_observation ?? ''));
                    $sheet->setCellValue("E{$detIL_row}", getObservationType($data->observation_type_id ?? ''));
                    $sheet->setCellValue("F{$detIL_row}", $data->description ?? '');
                    $sheet->setCellValue("G{$detIL_row}", $data->hazard ?? '');

                    if (!empty($data->file_path) && file_exists(public_path($data->file_path))) {
                        $drawing = new Drawing();
                        $drawing->setPath(public_path($data->file_path));
                        $drawing->setCoordinates("H{$detIL_row}");
                        $drawing->setOffsetX(5);
                        $drawing->setOffsetY(5);
                        $drawing->setWidth(80);
                        $drawing->setWorksheet($sheet);
                        $sheet->getRowDimension($detIL_row)->setRowHeight(90);
                    } else {
                        $sheet->setCellValue("H{$detIL_row}", 'No image');
                    }

                    $sheet->setCellValue("I{$detIL_row}", $data->capa ?? '');
                    $sheet->setCellValue("J{$detIL_row}", displayDateFormat($data->date_of_compliance ?? ''));
                    $sheet->setCellValue("K{$detIL_row}", getEmployeename($data->responsibility_id ?? ''));
                    $sheet->setCellValue("L{$detIL_row}", getGembaWalkStatus($data->status ?? ''));
                    $sheet->setCellValue("M{$detIL_row}", $data->remark ?? '');
                    $sheet->setCellValue("N{$detIL_row}", $data->observation_needed == '1' ? 'YES' : 'NO');

                    $sheet->getStyle("A{$detIL_row}:N{$detIL_row}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $detIL_row++;
                    $sr++;
                }

                // Signatures
                $signatureStartRow = $detIL_row;
                $signatureEndRow = $signatureStartRow + 3;
                $labelRow = $signatureEndRow + 1;
                $imageHeight = 60;
                $sheet->mergeCells("A{$signatureStartRow}:G{$signatureEndRow}");
                if (file_exists($approvedSignature)) {
                    $drawing = new Drawing();
                    $drawing->setName('GembaWalk Approved Singnature');
                    $drawing->setDescription('GembaWalk Approved Singnature');
                    $drawing->setPath($approvedSignature);
                    $drawing->setCoordinates("D{$signatureStartRow}");
                    $drawing->setOffsetX(130);
                    $drawing->setOffsetY(10);
                    $drawing->setWidth($imageHeight);
                    $drawing->setHeight($imageHeight);
                    $drawing->setWorksheet($sheet);
                }
                $sheet->mergeCells("A{$labelRow}:G{$labelRow}")->setCellValue("A{$labelRow}", "GembaWalk Approved Signature");
                $sheet->getStyle("A{$labelRow}:G{$labelRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->mergeCells("H{$signatureStartRow}:N{$signatureEndRow}");
                if (file_exists($verifiedSignature)) {
                    $drawing = new Drawing();
                    $drawing->setName('GembaWalk Verified Singnature');
                    $drawing->setDescription('GembaWalk Verified Singnature');
                    $drawing->setPath($verifiedSignature);
                    $drawing->setCoordinates("J{$signatureStartRow}");
                    $drawing->setOffsetX(130);
                    $drawing->setOffsetY(10);
                    $drawing->setWidth($imageHeight);
                    $drawing->setHeight($imageHeight);
                    $drawing->setWorksheet($sheet);
                }
                $sheet->mergeCells("H{$labelRow}:N{$labelRow}")->setCellValue("H{$labelRow}", "GembaWalk Verified Signature");
                $sheet->getStyle("H{$labelRow}:N{$labelRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->getStyle("A{$currentRow}:N{$labelRow}")->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['argb' => '000000']]
                    ]
                ]);



                $sheet->setBreak("A" . ($labelRow + 1), Worksheet::BREAK_ROW);
                $row = $labelRow + 7;
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'GembaWalk_Report_' . now()->format('Ymd_His') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            $writer->save("php://output");
            exit;
        } catch (\Exception $e) {
            dd($e);
            return back()->with('error', $e->getMessage());
        }
    }
}
