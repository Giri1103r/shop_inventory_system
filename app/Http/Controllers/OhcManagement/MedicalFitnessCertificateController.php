<?php


namespace App\Http\Controllers\OhcManagement;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Jobs\Ohc\ImportRequisitionjob;
use App\Mail\Ohc\FitnessEmail;
use App\Mail\Ohc\MedicineRequisitionEmail;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\Unit;
use App\Models\OhcManagement\Master\Medicine;
use App\Models\OhcManagement\Master\Vendor;
use App\Models\OhcManagement\MedicalFitnessCertificate;
use App\Models\OhcManagement\MedicineRequisition;
use App\Models\OhcManagement\UserMedicineRequisition;
use App\Models\UploadLog;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;
use App\Models\OhcManagement\MedicineStock;
use App\Models\OhcManagement\OhcStatuslog;
use App\Models\OhcManagement\Report\Inventory;
use App\Models\User;
use Illuminate\Support\Facades\File;


class MedicalFitnessCertificateController extends Controller
{

    private $medical_fitness_certificate;
    private $ohc_status;

    public function __construct()
    {

        $this->medical_fitness_certificate = new MedicalFitnessCertificate();
        $this->ohc_status = new OhcStatuslog();
    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->medical_fitness_certificate->list();
                    $datatables = DataTables::of($data['data'])
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

                        ->addColumn('approve_status', function ($row) {


                            if ($row->approve_status == STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING) {
                                $text = "<span class='badge bg-info' style='font-size: 1.0em;'>Doctor Approval Pending</span>";
                            } else if ($row->approve_status == STATUS_OHC_MEDICAL_DOCTOR_APPROVED) {
                                $text = "<span class='badge bg-success' style='font-size: 1.0em;'>Doctor Approved</span>";
                            } else if ($row->approve_status == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING) {
                                $text = "<span class='badge bg-info' style='font-size: 1.0em;'>EHS Head Approval Pending</span>";
                            } else if ($row->approve_status == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVED) {
                                $text = "<span class='badge bg-success' style='font-size: 1.0em;'>EHS Head Approved</span>";
                            } else if ($row->approve_status == STATUS_OHC_MEDICAL_DOCTOR_REJECTED) {
                                $text = "<span class='badge bg-danger' style='font-size: 1.0em;'>Doctor Rejected</span>";
                            }
                            return $text;
                        })
                        ->editColumn('date', function ($row) {
                            return displaydateformat($row->date);
                        })
                        ->editColumn('unit_id', function ($row) {
                            return getUnitname($row->unit_id);
                        })
                        ->editColumn('department_id', function ($row) {
                            return getDepartment($row->department_id);
                        })
                        ->editColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                            $btn .= '<a href="' . admin_url('ohc/medical-fitness/view/' . encryptId($row->id)) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            if ((CheckUserPermission('edit') && $row->approve_status == STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING) || ($row->created_by == Auth::id() &&  $row->approve_status == STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING)) {
                                $btn .= '<a href="' . admin_url('ohc/medical-fitness/edit/' . encryptId($row->id)) . '" class="" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a> ';
                            }
                            if ((checkUserRole(ROLE_SUPERADMIN) && $row->approve_status == STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING) || (checkUserRole(ROLE_DOCTOR) && $row->approve_status == STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING)  || ((checkUserRole(ROLE_EHS_HEAD) && $row->approve_status == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING) || (checkUserRole(ROLE_SUPERADMIN) && $row->approve_status == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING))) {
                                $btn .= '<a href="' . admin_url('ohc/medical-fitness/approval/view/' . encryptId($row->id)) . '" class="" title="Action"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }


                            $btn .= '<a href="' . admin_url('ohc/medical-fitness/generalpdf/' . encryptId($row->id)) . '" class="" title="PDF"> <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i></a> ';
                            return $btn;
                        })

                        ->rawColumns(['action', 'request_date', 'approve_status', 'unit_id', 'department_id'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);

                    return response()->json($datatables->getData());
                } catch (Exception $ex) {
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => __('ohc.please_try_after_some_time')], 406);
                }
            }
        }


        $data = array();

        return view('ohcmanagement.medical_fitness_certificate.list', $data);
    }

    public function add()
    {
        try {

            return view('ohcmanagement.medical_fitness_certificate.add');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-fitness/list'));
        }
    }

    public function store(Request $request)
    {
        try {

            $rules = [
                'emp_id' => 'required',
                'emp_name' => 'required',
                'date' => 'required',
                'remarks' => 'required',
                'company_id.required' => 'Date is required',

            ];


            $messages = [
                'emp_id.required' => 'Employee Id is required',
                'emp_name.required' => 'Emp Name is required',
                'date.required' => 'Date is required',
                'remarks.required' => 'remarks is required',

            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {


                $medicinefitness =  $this->medical_fitness_certificate->store();
                $id =  $medicinefitness->id;
                $this->ohc_status->medicalfitnessstore($id);
                $mailsubject = 'Medical Fitness Check';
                $user_role = ROLE_DOCTOR;

                $data = $this->medical_fitness_certificate->selectOne($id);
                $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->where('company_id', $data->company_id)->pluck('id')->toArray();
                $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->where('company_id', $data->company_id)->get();

                if (count($users) > 0) {

                    foreach ($users as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {

                            $data = $this->medical_fitness_certificate->selectOne($id);
                            $details  = $data->toArray();

                            $details['name'] = $user->name;
                            $details['email_id'] =  $email_id;
                            $details['mail_subject'] = $mailsubject;

                            Mail::to($details['email_id'])->queue(new FitnessEmail($details));
                        }
                    }
                }


                /**
                 * Send Web notification
                 */

                $notificationData = array(
                    'notification_type' => 4,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => ($data->emp_name) . 'is Submitted the Fitness Certificate for the Doctor Approval' . getUsername($data->created_by),
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $data->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('ohc/medical-fitness/approval/view/' . encryptId($data->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medical-fitness/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-fitness/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $medicalfitness = $this->medical_fitness_certificate->selectOne($id);
                $medicalfitnesslog = $this->ohc_status->medicalfitnesslog($id);
                $doctorapprovallog = $this->ohc_status->doctorapprovalview($id);
                $ehsheadlog = $this->ohc_status->fitnessehsheadlog($id);
                $data = array(
                    'medicalfitness' => $medicalfitness,
                    'medicalfitnesslog' => $medicalfitnesslog,
                    'doctorapprovallog' => $doctorapprovallog,
                    'ehsheadlog' => $ehsheadlog,

                );
            }
            return view('ohcmanagement.medical_fitness_certificate.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-fitness/list'));
        }
    }

    public function approval(Request $request)
    {
        $id = decryptId($request->id);
        if (Auth::check()) {
            $medicalfitness = $this->medical_fitness_certificate->selectOne($id);
            $doctorapprovalview = $this->ohc_status->doctorapprovalview($id);


            $data = array(
                'medicalfitness' => $medicalfitness,
                'doctorapprovalview' => $doctorapprovalview,
                'encryptid' => $id

            );
            // dd($data);
        }
        return view('ohcmanagement.medical_fitness_certificate.approvereject', $data);
    }

    public function doctorapproval(Request $request)
    {
        try {
            $id = decryptId($request->id);



            try {
                $action = $request->input('action');
                $remarks = $request->input('remarks');

                if ($action == 'approve') {
                    $doctorverifydata = [
                        'remarks' => $remarks,
                        'approve_status' =>  STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING
                    ];
                } else if ($action == 'reject') {
                    $doctorverifydata = [
                        'remarks' => $remarks,
                        'approve_status' =>  STATUS_OHC_MEDICAL_DOCTOR_REJECTED
                    ];
                }

                $this->medical_fitness_certificate->doctorapproval($id, $doctorverifydata);

                $this->ohc_status->doctorverificationstatuslog($id, $doctorverifydata);


                $data = $this->medical_fitness_certificate->selectOne($id);
                if ($action == 'approve') {
                    $mailsubject = 'Medical Fitness Check';
                    $user_role = ROLE_EHS_HEAD;


                    $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
                    $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();

                    if (count($users) > 0) {

                        foreach ($users as $user) {

                            $email_id = $user->email;

                            if ($email_id != '' || $email_id != null) {
                                $data = $this->medical_fitness_certificate->selectOne($id);
                                $details  = $data->toArray();

                                $details['name'] = $user->name;
                                $details['email_id'] =  $email_id;
                                $details['mail_subject'] = $mailsubject;

                                Mail::to($details['email_id'])->queue(new FitnessEmail($details));
                            }
                        }

                        /**
                         * Send Web notification
                         */
                        $notificationData = array(
                            'notification_type' => 4,
                            'module_type' => 1,
                            'notification_message' => $mailsubject,
                            'mobile_notification' => json_encode(array(
                                'title' => $mailsubject,
                                'message' => ($data->emp_name) . 'fitness check is approved by the ' . getUsername($data->approved_by),
                                'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                                'id' => $data->id,
                                'module' => 1,
                            )),
                            'web_link' =>  admin_url('ohc/medical-fitness/approval/view/' . encryptId($data->id)),
                            'assigned_user' => array_to_string($userids),
                            'created_by' => Auth::id(),
                        );
                        notificationSave($notificationData);
                    }
                }

                if ($action == 'reject') {
                    $mailsubject = 'Medical Fitness Check';
                    $data = $this->medical_fitness_certificate->selectOne($id);
                    $createdBy =   $data->created_by;
                    $user = User::where('id', $createdBy)->where('status', 1)->first();
                    $email_id = $user->email;

                    if ($email_id != '' || $email_id != null) {
                        $data = $this->medical_fitness_certificate->selectOne($id);
                        $details  = $data->toArray();

                        $details['name'] = $user->name;
                        $details['email_id'] =  $email_id;
                        $details['mail_subject'] = $mailsubject;

                        Mail::to($details['email_id'])->queue(new FitnessEmail($details));
                    }

                    $notificationData = array(
                        'notification_type' => 4,
                        'module_type' => 1,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode(array(
                            'title' => $mailsubject,
                            'message' => ($data->emp_name) . 'fitness check is rejected by the ' . getUsername($data->approved_by),
                            'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $data->id,
                            'module' => 1,
                        )),
                        'web_link' =>  admin_url('ohc/medical-fitness/list'),
                        'assigned_user' =>  $createdBy,
                        'created_by' => Auth::id(),
                    );
                    notificationSave($notificationData);
                }

                Session::flash('success', 'Your Request Has Responded Successfully');
                return redirect(admin_url('ohc/medical-fitness/list'));
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
                return redirect(admin_url('ohc/medical-fitness/list'));
            }
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-fitness/list'));
        }
    }

    public function ehsheadapproval(Request $request)
    {
        try {
            $id = decryptId($request->id);



            try {
                $action = $request->input('action');
                $remarks = $request->input('remarks');
                $approveStatus = $action == 'approve' ? STATUS_OHC_MEDICAL_EHS_HEAD_APPROVED : STATUS_OHC_EHS_REJECTED;
                $ehsheadverifydata = [
                    'remarks' => $remarks,
                    'approve_status' => $approveStatus
                ];
                $this->medical_fitness_certificate->ehsheadapproval($id, $ehsheadverifydata);


                if ($action == 'approve') {
                    $this->ohc_status->ehsheadverificationapprovedstatuslog($id, $ehsheadverifydata);
                }

                $data = $this->medical_fitness_certificate->selectOne($id);
                if ($action == 'approve') {
                    $mailsubject = 'Medical Fitness Check';


                    $data = $this->medical_fitness_certificate->selectOne($id);
                    $createdId = $data->created_by;
                    $user = User::where('id', $createdId)->first();
                    if ($user) {
                        $email_id = $user->email;
                    }

                    if ($email_id != '' || $email_id != null) {
                        $data = $this->medical_fitness_certificate->selectOne($id);
                        $details  = $data->toArray();

                        $details['name'] = $user->name;
                        $details['email_id'] =  $email_id;
                        $details['mail_subject'] = $mailsubject;

                        Mail::to($details['email_id'])->queue(new FitnessEmail($details));
                    }
                    /**
                     * Send Web notification
                     */

                    $notificationData = array(
                        'notification_type' => 4,
                        'module_type' => 1,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode(array(
                            'title' => $mailsubject,
                            'message' => ($data->emp_name) . 'fitness check is approved by the ' . getUsername($data->approved_by),
                            'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $data->id,
                            'module' => 1,
                        )),
                        'web_link' =>  admin_url('ohc/medical-fitness/view/' . encryptId($data->id)),
                        'assigned_user' => $createdId,
                        'created_by' => Auth::id(),
                    );
                    notificationSave($notificationData);
                }

                Session::flash('success', 'Your Request Has Responded Successfully');
                return redirect(admin_url('ohc/medical-fitness/list'));
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
                return redirect(admin_url('ohc/medical-fitness/list'));
            }
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-fitness/list'));
        }
    }
    public function Edit(Request $request)
    {
        try {

            $id = decryptId($request->id);
            if (Auth::check()) {
                $medicalfitness = $this->medical_fitness_certificate->selectOne($id);

                $data = array(
                    'medicalfitness' => $medicalfitness,
                );
            }

            return view('ohcmanagement.medical_fitness_certificate.edit', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-fitness/list'));
        }
    }
    public function update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'emp_id' => 'required',
                'emp_name' => 'required',
                'date' => 'required',
                'company_id' => 'required',
                'remarks' => 'required',


            ];


            $messages = [
                'emp_id.required' => 'Employee Id is required',
                'emp_name.required' => 'Emp Name is required',
                'date.required' => 'Date is required',
                'company_id.required' => 'Date is required',
                'remarks.required' => 'remarks is required',

            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {



                $this->medical_fitness_certificate->updates($id);

                $mailsubject = 'Medical Fitness Check';
                $user_role = ROLE_DOCTOR;


                $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
                $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();

                if (count($users) > 0) {
                    $data = $this->medical_fitness_certificate->selectOne($id);
                    $permitrray  = $data->toArray();

                    foreach ($users as $user) {
                        $email_id = $user->email;

                        if (!empty($email_id)) {
                            $permitrray['name'] = $user->name;
                            $permitrray['email_id'] =  $email_id;
                            $permitrray['mail_subject'] = $mailsubject;

                            Mail::to($email_id)->queue(new FitnessEmail($permitrray));
                        }
                    }

                    /**
                     * Send Web notification
                     */
                    $notificationData = array(
                        'notification_type' => 4,
                        'module_type' => 1,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode(array(
                            'title' => $mailsubject,
                            'message' => ($data->emp_name) . ' is Submitted the Fitness Certificate for the Doctor Approval by ' . getUsername($data->created_by),
                            'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $data->id,
                            'module' => 1,
                        )),
                        'web_link' => admin_url('ohc/medical-fitness/approval/view/' . encryptId($data->id)),
                        'assigned_user' => array_to_string($userids),
                        'created_by' => Auth::id(),
                    );
                    notificationSave($notificationData);

                    Session::flash('success', 'Your data has been updated successfully!');
                }

                Session::flash('success', 'Your data has been updated successfully!');
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medical-fitness/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-fitness/list'));
        }
    }



    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->medical_fitness_certificate->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Employee Code',
                'Employee Name',
                'Company Name',
                ' Date',
                 'Cheif Complaint',
                'Remarks',
                'From Status',
                'To Status',
                'Created_by',
                'Created_at'
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  ($data->emp_id);
                $export[] =  ($data->emp_name);
                $export[] =  getCompanyname($data->company_id);
                $export[] = displaydateformat($data->date);
                $export[] =  $data->cheif_complaint;
                $export[] =  $data->remarks;
                if ($data->approve_status == STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING) {
                    $export[] = 'Paramedicis Applied the fitness certificate';
                } elseif ($data->approve_status == STATUS_OHC_MEDICAL_DOCTOR_APPROVED) {
                    $export[] = 'Doctor Approval Pending';
                } elseif ($data->approve_status == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING) {
                    $export[] = 'Doctor Approved';
                } elseif ($data->approve_status == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVED) {
                    $export[] = 'EHS Head Approval Pending';
                } else {
                    $export[] = removeUnderScore(getStatus($data->approve_status));
                }
                if ($data->approve_status == STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING) {
                    $export[] = 'Doctor Approval Pending';
                } elseif ($data->approve_status == STATUS_OHC_MEDICAL_DOCTOR_APPROVED) {
                    $export[] = 'Doctor Approved';
                } elseif ($data->approve_status == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING) {
                    $export[] = 'EHS Head Approval Pending';
                } elseif ($data->approve_status == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVED) {
                    $export[] = 'EHS Head Approved';
                } elseif ($data->approve_status == STATUS_OHC_MEDICAL_DOCTOR_REJECTED) {
                    $export[] = 'Doctor Rejected';
                } else {
                    $export[] = removeUnderScore(getStatus($data->approve_status));
                }
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Medical Fitness .xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-fitness/list'));
        }
    }

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $medicinefitness = $this->medical_fitness_certificate->selectOne($id);
            }
            $medicalfitnesslog = $this->ohc_status->medicalfitnesslog($id);
            $doctorapprovallog = $this->ohc_status->doctorapprovalview($id);
            $ehsheadlog = $this->ohc_status->fitnessehsheadlog($id);

            $data = [
                'medicinefitness' => $medicinefitness,
                'medicalfitnesslog' => $medicalfitnesslog,
                'doctorapprovallog' => $doctorapprovallog,
                'ehsheadlog' => $ehsheadlog,
                'pagetitle' => "Medical Fitness Certificate",
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

            $html = view('ohcmanagement.medical_fitness_certificate.exportpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "Medical Fitness Certificate Details.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-fitness/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->medical_fitness_certificate->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Employee Code',
                'Employee Name',
                'Company Name',
                ' Date',
                'Cheif Complaint',
                'Remarks',
                'From Status',
                'To Status',
                'Created_by',
                'Created_at'
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Medical Fitness",
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

            $view = view('ohcmanagement.medical_fitness_certificate.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Medical Fitness .pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-fitness/list'));
        }
    }
}
