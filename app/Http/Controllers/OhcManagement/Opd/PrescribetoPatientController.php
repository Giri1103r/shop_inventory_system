<?php

namespace App\Http\Controllers\OhcManagement\Opd;

use App\Http\Controllers\Controller;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\Unit;
use App\Models\Master\Work;
use App\Models\OhcManagement\Master\CertifiedFirstAider;
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

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use phpseclib3\File\ASN1\Maps\CertificateIssuer;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Symfony\Component\Console\Completion\Suggestion;
use Yajra\DataTables\Facades\DataTables;

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
                            return ($row->suggested_by);
                        })
                        ->editColumn('patient_status', function ($row) {

                            if ($row->patient_status == 'Open') {
                                return  "<span class='badge bg-info' style='font-size: 1.0em;'>Open</span>";
                            } elseif ($row->patient_status == 'Close') {
                                return "<span class='badge bg-success' style='font-size: 1.0em;'>Close</span>";
                            } elseif ($row->patient_status == 'Cancel') {
                                return "<span class='badge bg-danger' style='font-size: 1.0em;'>Cancel</span>";
                            } else if ($row->patient_status == null) {
                                return "";
                            }
                        })

                        ->editColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                            $btn .= '<a href="' . admin_url('ohc/prescribe-to-patient/view/' . encryptId($row->id)) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                            $btn .= '<a href="' . admin_url('ohc/prescribe-to-patient/edit/' . encryptId($row->id)) . '" class="" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a> ';
                            // }
                            // $btn .= '<a href="javascript:void(0);" data-id="' . encryptId($row->id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger"></i></a> ';

                            $btn .= '<a href="' . admin_url('ohc/prescribe-to-patient/generalpdf/' . encryptId($row->id)) . '" class="" title="PDF"> <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i></a> ';

                            $btn .= '<a href="javascript:void(0);" data-id="' . encryptId($row->id) . '" class="Close" title="Cancel" style="color: #e21e23;margin-right: 5px;"><i class="fa fa-times-circle"></i></a> ';
                            return $btn;
                        })

                        ->rawColumns(['action', 'date', 'vital_checkup', 'created_by', 'patient_status', 'fitness_certificate'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);

                    return response()->json($datatables->getData());
                } catch (Exception $ex) {
                    dd($ex);
                    return response()->json(['status' => 'error', 'msg' => __('ppe.please_try_after_some_time')], 406);
                }
            }
        }
        $patientstatus = $this->patient_status->getpatientstatus();

        $unit = $this->unit->getunit();
        $data = array(
            'patientstatus' => $patientstatus,

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
            $medicine  = $this->medicine_stock->getMedicinestockdata();
            $data = array(
                'unit' => $unit,
                'suggestedBy' => $suggestedBy,
                'reffered' => $reffered,
                'patientstatus' => $patientstatus,
                'medicine' => $medicine

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

            foreach ($firstaid as $medicine) {
                $medicine_id = $medicine->medicine_id;
                $unitId = Auth::user()->unit_id;
                $issuedQuantity = $medicine->quantity;


                $medicine_stock = $this->medicine_stock
                    ->where('id', $medicine_id)
                    ->where('unit_id', $unitId)
                    ->first();

                if ($medicine_stock) {
                    $medicine_stock->decrement('quantity', $issuedQuantity);
                }


                $inventory = $this->inventory
                    ->where('medicine_id', $medicine_id)
                    ->where('unit_id', $unitId)
                    ->first();

                if ($inventory) {
                    $inventory->update(['total_prescribe' => $issuedQuantity]);
                    $inventory->decrement('balance', $issuedQuantity);
                }
            }

            Session::flash('success', __('Your data has been created successfully'));

            return redirect(admin_url('ohc/prescribe-to-patient/list'));
        } catch (Exception $ex) {
            dd($ex);  // Debugging
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
            $medicine  = $this->medicine_stock->getMedicinestockdata();
            $suggestedname = $this->suggestedBy->getsuggestedname();
            $data = array(
                'unit' => $unit,
                'suggestedBy' => $suggestedBy,
                'reffered' => $reffered,
                'patientstatus' => $patientstatus,
                'medicine' => $medicine,
                'opdpatient' => $opdpatient,
                'opd_firstaid' => $opd_firstaid,
                'isreffered' => $isreffered,

            );
            // dd( $data);
            return view('ohcmanagement.ohc-opd.prescribe-to-patient.edit', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/prescribe-to-patient/list'));
        }
    }


    // view

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
            dd($ex);
            Session::flash('error', 'Something went wrong please try again after some time');
            return redirect(admin_url('ohc/prescribe-to-patient/list'));
        }
    }

    //
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

            $opd_patient = $this->opd_patient->updates($id);

            $firstaid = $this->opd_firstaid->updates($id);

            $isreffered = $this->isreffered->updates($id);



            Session::flash('success', __('Your data has been created successfully'));

            return redirect(admin_url('ohc/prescribe-to-patient/list'));
        } catch (Exception $ex) {
            dd($ex);  // Debugging
            Session::flash('error', 'Something went wrong. Please try again after some time');
            return redirect(admin_url('ohc/prescribe-to-patient/list'));
        }
    }

    // export pdf
    public function exportExcel()
    { {

            try {

                $allData = $this->opd_patient->exportdata();

                if ($allData->isEmpty()) {
                    return redirect()->back()->with('error', 'No data found');
                }

                $header = [
                    __("common.sno"),

                    'Employee Name',
                    'Problem',
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

                dd($ex);
            }
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
                'Employee Name',
                'Problem',
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

            dd($ex);
        }
    }

    // Fetch of employee and worker name

    public function fetchemployeename(Request $request)
    {
        $name = $request->input('search');

        $employee_code = $this->employee->where('emp_id', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->limit(10)
            ->get();

        $work = $this->work->where('emp_id', 'like', '%' . $name . '%')
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
        $employee = Employee::select('emp_name', 'department', 'mobile_no')
            ->where('emp_id', $emp_id)
            ->first();

        if (!$employee) {
            $employee = Work::select('emp_name', 'department', 'mobile_no')
                ->where('emp_id', $emp_id)
                ->first();
        }

        if ($employee) {
            return response()->json([
                'employee' => $employee,
                'departments' => $this->department->select('department_name')->where('status', '1')->where('id', $employee->department)
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

            $this->opd_firstaid->deleterecord($id);
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
        }
        $data = [
            'opdpatient' => $opdpatient,
            'opd_firstaid' => $opd_firstaid,
            'isreffered' => $isreffered,
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
        return $mpdf->Output($filename, 'I');
    }

    public function employeename(Request $request){
        $name = $request->input('search');

        $employee_code = $this->employee->where('emp_id', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->limit(10)
            ->get();

        $work = $this->work->where('emp_id', 'like', '%' . $name . '%')
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
