<?php

namespace App\Http\Controllers\OhcManagement\Opd;

use App\Http\Controllers\Controller;
use App\Mail\Ohc\PatientEmail;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\Unit;
use App\Models\Master\Work;
use App\Models\OhcManagement\Master\CertifiedFirstAider;
use App\Models\OhcManagement\Master\HospitalDetails;
use App\Models\OhcManagement\Master\Medicine;
use App\Models\OhcManagement\Master\Vendor;
use App\Models\OhcManagement\UserMedicineIssuance;
use App\Models\OhcManagement\MedicineIssuance;
use App\Models\OhcManagement\MedicineReceiving;
use App\Models\OhcManagement\MedicineStock;
use App\Models\OhcManagement\Opd\FirstAidTreatment;
use App\Models\OhcManagement\Opd\IsReffered;
use App\Models\OhcManagement\Opd\PatientStatus;
use App\Models\OhcManagement\Opd\PrescribetoPatient;
use App\Models\OhcManagement\Opd\ReferedVechicle;
use App\Models\OhcManagement\Opd\Suggestedby;
use App\Models\OhcManagement\UserMedicineRequisition;
use App\Models\UploadLog;
use App\Models\OhcManagement\Report\Inventory;
use App\Models\User;
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
use Illuminate\Support\Facades\Response;

class PrescribetoPatientController extends Controller
{

    private $medicine_stock;
    private $vendor;
    private $user_medicine_issuance;
    private $unit;
    private $department;
    private $medicine_issuance;
    private $medicine_receiving;
    private $work;
    private $employee;
    private $suggestedBy;
    private $refered_vechicle;
    private $patient_status;
    private $opd_patient;
    private $opd_firstaid;
    private $isreffered;
    private $inventory;
    private $hospital;

    public function __construct()
    {
        $this->medicine_stock = new MedicineStock();
        $this->vendor = new Vendor();
        $this->user_medicine_issuance = new UserMedicineIssuance();
        $this->medicine_issuance = new MedicineIssuance();
        $this->medicine_receiving = new MedicineReceiving();
        $this->employee = new Employee();
        $this->work = new Work();
        $this->suggestedBy = new Suggestedby();
        $this->refered_vechicle = new ReferedVechicle();
        $this->patient_status = new PatientStatus();
        $this->opd_patient = new PrescribetoPatient();
        $this->opd_firstaid = new FirstAidTreatment();
        $this->isreffered = new IsReffered();
        $this->unit = new Unit();
        $this->department = new Department();
        $this->inventory = new Inventory();
        $this->hospital = new HospitalDetails();
    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->opd_patient->list();



                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('vital_checkup', function ($row) {
                            if ($row->vital_checkup === null) {
                                return "";
                            } elseif ($row->vital_checkup == 1) {
                                return "<span>Yes</span>";
                            } elseif ($row->vital_checkup == 0) {
                                return "<span>No</span>";
                            }
                            return "";
                        })

                        ->addColumn('fitness_certificate', function ($row) {
                            if ($row->fitness_certificate === null) {
                                return "";
                            } elseif ($row->fitness_certificate == 1) {
                                return "<span>Required</span>";
                            } elseif ($row->fitness_certificate == 2) {
                                return "<span>Not Required</span>";
                            }
                            return "";
                        })

                        ->editColumn('unit_id', function ($row) {
                            return getUnitname($row->unit_id);
                        })
                        ->editColumn('department_id', function ($row) {
                            return getDepartment($row->department_id);
                        })
                        ->editColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->editColumn('date', function ($row) {
                            return displaydateformat($row->date);
                        })
                        ->editColumn('suggested_by', function ($row) {
                            return getSuggestedBy($row->suggested_by);
                        })
                        ->editColumn('patient_status', function ($row) {

                            if ($row->patient_status == '1') {
                                return  "<span class='badge bg-info' style='font-size: 1.0em;'>Open</span>";
                            } elseif ($row->patient_status == '2') {
                                return "<span class='badge bg-success' style='font-size: 1.0em;'>Close</span>";
                            } elseif ($row->patient_status == '3') {
                                return "<span class='badge bg-danger' style='font-size: 1.0em;'>Cancel</span>";
                            } else if ($row->patient_status == null) {
                                return "";
                            }
                        })

                        ->editColumn('action', function ($row) {
                            $btn = '';
                            if (CheckUserPermission('view')) {
                                $btn .= '<a href="' . admin_url('ohc/prescribe-to-patient/view/' . encryptId($row->id)) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            if (CheckUserPermission('edit') && $row->patient_status != 3 && $row->patient_status != 2) {
                                $btn .= '<a href="' . admin_url('ohc/prescribe-to-patient/edit/' . encryptId($row->id)) . '" class="" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a> ';
                            }
                            if ($row->patient_status !== 3) {
                                $btn .= '<a href="' . admin_url('ohc/prescribe-to-patient/generalpdf/' . encryptId($row->id)) . '" class="" title="PDF"> <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i></a> ';
                            }
                            if ($row->patient_status != 3 && $row->patient_status != 2) {
                                $btn .= '<a href="javascript:void(0);" data-id="' . encryptId($row->id) . '" class="cancel" title="Cancel" style="color: #e21e23;margin-right: 5px;"><i class="fa fa-times-circle"></i></a> ';
                            }
                            if ($row->fitness_certificate == 1) {
                                $btn .= ' <a href="' . asset('public/' . $row->file_upload) . '" target="_blank" title="medical_certificate">
                                            <i class="fas fa-file-alt r"></i>
                                          </a> ';
                            }

                            return $btn;
                        })

                        ->rawColumns(['action', 'date', 'vital_checkup', 'created_by', 'patient_status', 'fitness_certificate'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);

                    return response()->json($datatables->getData());
                } catch (Exception $ex) {
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => __('ppe.please_try_after_some_time')], 406);
                }
            }
        }
        $patientstatus = $this->patient_status->getpatientstatus();

        $unit = $this->unit->getunit();
        $companyId = $request->company_id;
        $fromdate = $request->fromDate;
        $toDate = $request->toDate;
        $data = array(
            'patientstatus' => $patientstatus,
  'fromdate' => $fromdate,
            'toDate' => $toDate,
            'companyId' => $companyId,
            'unit' => $unit
        );

        return view('ohcmanagement.ohc-opd.prescribe-to-patient.list', $data);
    }

    public function add()
    {
        try {
            $unit = $this->unit->getunit();
            $suggestedBy = $this->suggestedBy->getSuggestedBy();
            $reffered = $this->refered_vechicle->getreffered();
            $patientstatus = $this->patient_status->getpatientstatus();
            $medicine  = $this->inventory->getmedicineUnitwise();
            $departmentList = $this->department->getdepartment();
            $hospital = $this->hospital->getHospitalname();
            $data = array(
                'unit' => $unit,
                'suggestedBy' => $suggestedBy,
                'reffered' => $reffered,
                'patientstatus' => $patientstatus,
                'departmentList' => $departmentList,
                'medicine' => $medicine,
                'hospital' => $hospital,

            );
            return view('ohcmanagement.ohc-opd.prescribe-to-patient.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/prescribe-to-patient/list'));
        }
    }


    public function store(Request $request)
    {
        try {
            $rules = [
                'date' => 'required',
            ];

            $messages = [
                'date.required' => 'Date cannot be empty.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }




            $opd_patient = $this->opd_patient->store();

            $firstaid = $this->opd_firstaid->store($opd_patient);

            $isreffered = $this->isreffered->store($opd_patient);

            $id =  $opd_patient->id;
            $opdpatient = $this->opd_patient->selectOne($id);



            // mail notification
            $user_role = ROLE_EHS_HEAD;
            $mailsubject = 'OPD of the Patient is Submitted';
            $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
            $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();

            if ($users->isNotEmpty()) {
                foreach ($users as $user) {
                    $email_id = $user->email;

                    if (!empty($email_id)) {
                        $opdpatient = $this->opd_patient->selectOne($id);
                        $medicineDetails = $this->opd_firstaid->selectOne($id);
                        $isreffered = $this->isreffered->selectOne($id);

                        $hospitaldetails = $isreffered ? $isreffered->toArray() : [];
                        $emailDetails = $opdpatient ? $opdpatient->toArray() : [];

                        if (!empty($emailDetails) || !empty($medicineDetails) || !empty($hospitaldetails)) {
                            $emailDetails['name'] = $user->name;
                            $emailDetails['email_id'] = $email_id;
                            $emailDetails['mail_subject'] = $mailsubject;

                            Mail::to($emailDetails['email_id'])
                                ->queue(new PatientEmail($emailDetails, $medicineDetails, $hospitaldetails));
                        }
                    }
                }
            }

            // Web notification
            $notificationData = [
                'notification_type' => 4,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode([
                    'title' => $mailsubject,
                    'message' => "OPD Patient List Submitted by " . getUsername($opdpatient->created_by),
                    'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $opdpatient->id,
                    'module' => 1,
                ]),
                'web_link' => admin_url('ohc/prescribe-to-patient/list'),
                'assigned_user' => array_to_string($userids),
                'created_by' => Auth::id(),
            ];
            notificationSave($notificationData);


            foreach ($firstaid as $medicine) {
                $medicine_id = $medicine->medicine_id;
                $unitId = Auth::user()->unit_id;
                $issuedQuantity = $medicine->quantity;

                $inventory = $this->inventory
                    ->where('medicine_id', $medicine_id)
                    ->where('unit_id', $unitId)
                    ->first();

                if ($inventory) {
                    $inventory->increment('total_prescribe', $issuedQuantity);
                    $inventory->decrement('balance', $issuedQuantity);
                }
            }


            Session::flash('success', __('Your data has been created successfully'));

            return redirect(admin_url('ohc/prescribe-to-patient/list'));
        } catch (Exception $ex) {
            // report($ex);
            report($ex);  // Debugging
            Session::flash('error', 'Something went wrong. Please try again after some time');
            return redirect(admin_url('ohc/prescribe-to-patient/list'));
        }
    }

    //edit
    public function edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $opdpatient = $this->opd_patient->selectOne($id);
                $opd_firstaid = $this->opd_firstaid->Selectone($id);
                $isreffered = $this->isreffered->selectOne($id);
            }
            $unit = $this->unit->getunit();
            $suggestedBy = $this->suggestedBy->getSuggestedBy();
            $reffered = $this->refered_vechicle->getreffered();
            $patientstatus = $this->patient_status->getpatientstatus();
            $medicine  = $this->inventory->getmedicineUnitwise();
            $suggestedname = $this->suggestedBy->getsuggestedname();
            $departmentList = $this->department->getdepartment();
            $hospital = $this->hospital->getHospitalname();

            $data = array(
                'unit' => $unit,
                'suggestedBy' => $suggestedBy,
                'reffered' => $reffered,
                'patientstatus' => $patientstatus,
                'medicine' => $medicine,
                'opdpatient' => $opdpatient,
                'departmentList' => $departmentList,
                'opd_firstaid' => $opd_firstaid,
                'isreffered' => $isreffered,
                'hospital' => $hospital,

            );
            // dd( $data);
            return view('ohcmanagement.ohc-opd.prescribe-to-patient.edit', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/prescribe-to-patient/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $opdpatient = $this->opd_patient->selectOne($id);
                $opd_firstaid = $this->opd_firstaid->Selectone($opdpatient->id);
                $isreffered = $this->isreffered->selectOne($opdpatient->id);
            }
            $unit = $this->unit->getunit();
            $data = array(
                'opdpatient' => $opdpatient,
                'opd_firstaid' => $opd_firstaid,
                'isreffered' => $isreffered,

            );


            return view('ohcmanagement.ohc-opd.prescribe-to-patient.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong please try again after some time');
            return redirect(admin_url('ohc/prescribe-to-patient/list'));
        }
    }


    public function update(Request $request)
    {
        $id = decryptId($request->id);
        try {
            $rules = [
                'date' => 'required',
            ];

            $messages = [
                'date.required' => 'Date cannot be empty.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }



            $user_opd_patient = $this->opd_patient->selectOne($id);
            $user_opd_firstaid = $this->opd_firstaid->selectOne($id);

            $deletedPages = json_decode($request->deletedPage, true);

            if (!empty($deletedPages)) {
                foreach ($deletedPages as $encryptedId) {
                    $medicineIds = decryptId($encryptedId);

                    $medicine_first_aid_medicine = $this->opd_firstaid->firstdata($medicineIds);



                    $totalPrescribe =  $this->inventory->where('unit_id', Auth::user()->unit_id)
                        ->where('medicine_id', $medicine_first_aid_medicine->medicine_id)
                        ->decrement('total_prescribe', $medicine_first_aid_medicine->quantity);


                    $this->inventory->where('unit_id', Auth::user()->unit_id)
                        ->where('medicine_id', $medicine_first_aid_medicine->medicine_id)
                        ->increment('balance', $medicine_first_aid_medicine->quantity);

                    $update_data = $this->opd_firstaid
                        ->where('id', $medicineIds)->where('opd_id', $id)
                        ->update([
                            'status' => 0,
                            'trash'  => 'Yes'
                        ]);
                }
            }
            if ($user_opd_patient->first_aid_treatment === 1) {
                foreach ($request->medicine_id as $index => $medicine_id) {
                    $medicineRecord = $user_opd_firstaid->where('medicine_id', $medicine_id)->where('opd_id', $id)->first();

                    if (!$medicineRecord) {

                        continue;
                    }

                    $newQuantity = $request->quantity[$index];
                    $oldquantity = $medicineRecord->quantity;

                    if ($oldquantity > $newQuantity) {
                        $difference = $oldquantity - $newQuantity;

                        $this->inventory
                            ->where('medicine_id', $medicine_id)
                            ->where('unit_id', Auth::user()->unit_id)
                            ->decrement('total_prescribe', $difference);

                        $this->inventory
                            ->where('medicine_id', $medicine_id)
                            ->where('unit_id', Auth::user()->unit_id)
                            ->increment('balance', $difference);
                    } elseif ($oldquantity < $newQuantity) {
                        $difference = $newQuantity - $oldquantity;

                        $this->inventory
                            ->where('medicine_id', $medicine_id)
                            ->where('unit_id', Auth::user()->unit_id)
                            ->increment('total_prescribe', $difference);

                        $this->inventory
                            ->where('medicine_id', $medicine_id)
                            ->where('unit_id', Auth::user()->unit_id)
                            ->decrement('balance', $difference);
                    }
                }
            }



            $opd_patient = $this->opd_patient->updates($id);
            if ($user_opd_patient->first_aid_treatment === 1) {
                $firstaid = $this->opd_firstaid->updates($id);
            }
            $isreffered = $this->isreffered->updates($id);
            Session::flash('success', __('Your data has been updated successfully'));

            return redirect(admin_url('ohc/prescribe-to-patient/list'));
        } catch (Exception $ex) {
            report($ex);  // Debugging
            Session::flash('error', 'Something went wrong. Please try again after some time');
            return redirect(admin_url('ohc/prescribe-to-patient/list'));
        }
    }
    // unique check

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $emp_name = $request->emp_name;


            $id = $request->id;

            if (empty($id)) {
                $isUnique = $this->opd_patient->uniqueCheck($emp_name);
            } else {
                $id = decryptId($id);

                $isUnique = $this->opd_patient->existUniqueCheck($emp_name, $id);
            }

            if ($isUnique->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }

    // export pdf
    public function exportExcel()
    {

        try {

            $allData = $this->opd_patient->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),

                'Worker/Employee Name',
                'Worker/Employee Code',
                'Cheif Complaint',
                'Gender',
                'Unit',
                'Department',
                'Date',
                'Time',
                'Suggested By',
                'Treatment',
                'Check Up',
                'Patient Status',
                'Fitness Certificate',
                __("common.created_by"),
                'Cancel Remarks',
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] = $data->emp_name;
                $export[] = $data->emp_id;
                $export[] = $data->cheif_complaint;
                $export[] = $data->gender;
                $export[] =  getUnitname($data->unit_id);
                $export[] =  getDepartment($data->department_id);
                $export[] = displaydateformat($data->date);
                $export[] = ($data->time);
                $export[] = getSuggestedBy($data->suggested_by);
                $export[] = ($data->treatment);
                $export[] =  $data->vital_checkup == 1 ? 'Yes' : 'No';
                $export[] = getPatientStatus($data->patient_status);
                $export[] =  $data->fitness_certificate == 1 ? 'Required' : 'Not Required';
                $export[] =  getusername($data->created_by);
                $export[] =  ($data->cancel_remarks);


                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('opd patient .xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
        }
    }

    // Export pdf
    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->opd_patient->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Worker/Employee Name',
                'Worker/Employee Code',
                'Cheif Complaint',
                'Gender',
                'Unit',
                'Department',
                'Date',
                'Time',
                'Suggested By',
                'Treatment',
                'Check Up',
                'Patient Status',
                'Fitness Certificate',
                __("common.created_by"),
                'Cancel Remarks',
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "OPD Patient List",
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

            $view = view('ohcmanagement.ohc-opd.prescribe-to-patient.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "OPD Patient.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }

    // Fetch of employee and worker name

    public function fetchemployeename(Request $request)
    {
        $name = $request->input('search');

        $employee_code = $this->employee->where('emp_id', 'like', '%' . $name . '%')
            ->orwhere('emp_name', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->limit(10)
            ->get();

        $work = $this->work->where('emp_id', 'like', '%' . $name . '%')
            ->orwhere('emp_name', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->limit(10)
            ->get();


        $mergedResults = $employee_code->merge($work);

        return response()->json(
            $mergedResults->map(function ($employee) {
                return [
                    'id' => $employee->emp_id,
                    'text' => $employee->emp_id . ' - ' . $employee->emp_name,

                ];
            })
        );
    }


    // fetching the employee department and mobile number

    public function employeedetails($emp_id)
    {
        $employee = Employee::select('emp_name', 'department', 'mobile_no', 'unit')
            ->where('emp_id', $emp_id)
            ->first();

        if (!$employee) {
            $employee = Work::select('emp_name', 'department', 'mobile_no', 'unit')
                ->where('emp_id', $emp_id)
                ->first();
        }

        if ($employee) {
            return response()->json([
                'employee' => $employee,
                'departments' => $this->department->select('department_name')->where('status', '1')->where('id', $employee->department)
                    ->first(),
                'units' => $this->unit->select('unit_name')->where('status', '1')->where('id', $employee->unit)
                    ->first()
            ]);
        } else {
            return response()->json([
                'message' => 'Employee not found'
            ], 404);
        }
    }

    // fetching the first aiders

    public function firstaider(Request $request)
    {
        $search = $request->input('search');

        $employees = CertifiedFirstAider::where(function ($query) use ($search) {
            $query->where('certifier_name', 'like', '%' . $search . '%')
                ->orWhere('emp_id', 'like', '%' . $search . '%');
        })
            ->where('status', 1)
            ->limit(10)
            ->get();

        return response()->json(
            $employees->map(function ($employee) {
                return [
                    'id' => $employee->certifier_name,
                    'text' => $employee->certifier_name . ' - ' . $employee->emp_id,
                ];
            })
        );
    }

    // first aider mobile number

    public function firstaidernumber(Request $request)
    {
        $empID = $request->input('empId');
        $employee = CertifiedFirstAider::where('certifier_name', $empID)->where('trash', 'no')->where('status', 1)->first();
        return response()->json(
            $employee->mobile_no

        );
    }

    // cancel the patient

    public function cancel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $remarks = $request->remarks;
            $opd_patient = $this->opd_patient->find($id);
            $this->opd_patient->close($id, $remarks);
            return response()->json(['status' => 'success', 'msg' => __('Cancelled the OPD Patient Successfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('ptw.Please try After Some time')], 406);
        }
    }

    public function close(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $remarks = $request->remarks;
            $opd_patient = $this->opd_patient->find($id);
            $this->opd_patient->close($id, $remarks);
            return response()->json(['status' => 'success', 'msg' => __('Cancelled the OPD Patient Successfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('ptw.Please try After Some time')], 406);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $ids = decryptId($id);
            $data =    $this->opd_firstaid->firstdata($ids);

            $referenceId =   $data->reference_id;
            $user_opd_firstaid = $this->opd_patient->selectOne($referenceId);

            $this->inventory->where(
                'unit_id',
                Auth::user()->unit_id
            )
                ->where('medicine_id', $data->medicine_id)
                ->decrement('total_prescribe', $data->quantity);

            $this->inventory->where(
                'unit_id',
                Auth::user()->unit_id
            )
                ->where('medicine_id', $data->medicine_id)
                ->increment('balance', $data->quantity);
            $this->opd_firstaid->deleterecord($ids);
            return response()->json(['status' => 'success', 'msg' => 'Deleted successfully'], 200);
        } catch (Exception $ex) {
            return response()->json(['status' => 'error', 'msg' => 'Something went wrong'], 200);
        }
    }

    public function medicineslip(Request $request)
    {
        $id = decryptId($request->id);

        if (Auth::check()) {
            $opdpatient = $this->opd_patient->selectOne($id);
            $opd_firstaid = $this->opd_firstaid->Selectone($opdpatient->id);
            $isreffered = $this->isreffered->selectOne($opdpatient->id);
            $hospital = $this->hospital->selectOne($isreffered->hospital_name);
        }
        $data = [
            'opdpatient' => $opdpatient,
            'opd_firstaid' => $opd_firstaid,
            'isreffered' => $isreffered,
            'hospital' => $hospital,
            'pagetitle' => "Medicine Slip",
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

        $html = view('ohcmanagement.ohc-opd.prescribe-to-patient.medicineslip', $data)->render();
        $mpdf->WriteHTML($html);

        $filename = "Medicine Slip .pdf";
        return $mpdf->Output($filename, 'D');
    }

    public function employeename(Request $request)
    {
        $name = $request->input('search');

        $employee_code = $this->employee->where('emp_id', 'like', '%' . $name . '%')
            ->orwhere('emp_name', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->limit(10)
            ->get();

        $work = $this->work->where('emp_id', 'like', '%' . $name . '%')
            ->orwhere('emp_name', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->limit(10)
            ->get();


        $mergedResults = $employee_code->merge($work);

        return response()->json(
            $mergedResults->map(function ($employee) {
                return [
                    'id' => $employee->emp_name,
                    'text' => $employee->emp_id . ' - ' . $employee->emp_name,
                ];
            })
        );
    }
}
