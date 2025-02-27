<?php

namespace App\Http\Controllers\OhcManagement\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Company;
use App\Models\Master\Unit;
use App\Models\Master\Location;
use App\Models\Master\Department;
use App\Models\User;
use App\Models\UploadLog;
use App\Jobs\ImportCompanyJob;
use App\Models\Master\Employee;
use App\Models\Master\Work;
use App\Models\OhcManagement\Master\EmployeeCumPatient;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EmployeecumPatientController extends Controller
{

    private $employeecumpatient;
    private $unit;
    private $location;
    private $department;
    private $user;
    private $uploadlog;
    private $employee;
    private $work;

    public function __construct()
    {

        $this->employeecumpatient = new EmployeeCumPatient();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->department = new Department();
        $this->user = new User();
        $this->uploadlog = new UploadLog();
        $this->employee = new Employee();
        $this->work = new Work();

    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->employeecumpatient->list();

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
                        ->addColumn('created_at', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('dob', function ($row) {
                            return  Displaydateformat($row->dob);
                        })
                        ->addColumn('employee_type', function ($row) {
                            return $row->employee_type_name;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                                $btn = '<a href="' . admin_url('ohc/employee-cum-patient/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                                $btn .= '<a href="' . admin_url('ohc/employee-cum-patient/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            // }

                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status','dob'])
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
        $data = array();

        return view('ohcmanagement.master.employee_cum_patient.list', $data);
    }

    public function Add(Request $request)
    {

        try {
            $employeeType = DB::table('ohc_master_employee_cum_patient_employee_type')->select('employee_type_name', 'id')->where('trash', 'No')->where('status', 1)->get();
            $data = [
                'employeeType' => $employeeType
            ];
            return view('ohcmanagement.master.employee_cum_patient.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'emp_name' => 'required',
                'employee_type' => 'required',
                'dob' => 'required',
                'address' => 'required|max:300',
            ];


            $messages = [
                'emp_name.required' => 'Employee name is required.',
                'employee_type.required' => 'Please select an employee type.',
                'dob.required' => 'Date of birth is required.',
                'address.required' => 'Address is required.',
                'address.max' => 'Address cannot exceed 300 characters.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                dd($validator->error());
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {


               $this->employeecumpatient->store();


                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/employee-cum-patient/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/employee-cum-patient/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $employeecumpatient = $this->employeecumpatient->selectOne($id);

                $data = array(
                    'employeecumpatient' => $employeecumpatient,
                );
            }
            return view('ohcmanagement.master.employee_cum_patient.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $employeeType = DB::table('ohc_master_employee_cum_patient_employee_type')->select('employee_type_name', 'id')->where('trash', 'No')->where('status', 1)->get();
            $employeeList=$this->employee->getEmployeefulldata();
            $employeecumpatient = $this->employeecumpatient->find($id);
            $data = array(
                'employeecumpatient' => $employeecumpatient,
                'employeeType' => $employeeType,
                'employeeList'=>$employeeList
            );


            return view('ohcmanagement.master.employee_cum_patient.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'emp_name' => 'required',
                'employee_type' => 'required',
                'dob' => 'required',
                'address' => 'required|max:300',
            ];


            $messages = [
                'emp_name.required' => 'Employee name is required.',
                'employee_type.required' => 'Please select an employee type.',
                'dob.required' => 'Date of birth is required.',
                'address.required' => 'Address is required.',
                'address.max' => 'Address cannot exceed 300 characters.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {

                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->employeecumpatient->updates($id);

            // $company = $this->company->find($id);
            // $this->user->companyUpdate($company->login_id);

            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('ohc/employee-cum-patient/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/employee-cum-patient/list'));
        }
    }


    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $emp_id = $request->emp_id;

            $id = $request->id;

            if (empty($id)) {
                $isUnique = $this->employeecumpatient->uniqueCheck($emp_id);
            } else {
                $id = decryptId($id);
                $isUnique = $this->employeecumpatient->existUniqueCheck($emp_id, $id);
            }

            if ($isUnique->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }

    public function EmployeeUniquecheck(Request $request)
    {
        if ($request->ajax()) {

            $emp_name = $request->emp_name;
            $id = $request->id;

            if (empty($id)) {
                $isUnique = $this->employeecumpatient->empuniqueCheck( $emp_name);
            } else {
                $id = decryptId($id);
                $isUnique = $this->employeecumpatient->empexistUniqueCheck($emp_name, $id);
            }

            if ($isUnique->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->employeecumpatient->statuschange($id);
            // $company =  $this->company->selectOne($id);
            // $this->user->statuschange($company->login_id);

            return response()->json(['status' => 'success', 'msg' => 'Your Status Changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }
    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $location = $this->location->where('company_id', $id)->exists();
            $unit = $this->unit->where('company_id', $id)->exists();
            $department = $this->department->where('company_id', $id)->exists();

            if ($location || $unit || $department) {
                return response()->json(['status' => 'error', 'msg' => 'module_exits'], 406);
            }
            $this->employeecumpatient->deleterecord($id);
            return response()->json(['status' => 'success', 'msg' => 'Company deleted successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }


    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->employeecumpatient->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Employee Name',
                'Employee Type',
                'Date of birth',
                'Address',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->emp_name;
                $export[] =  getEmployeeType($data->employee_type);
                $export[] =  $data->dob;
                $export[] =  $data->address;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Employee Cum Patient Details.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->employeecumpatient->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Employee Name',
                'Employee Type',
                'Date of birth',
                'Address',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Employee Cum Patient Details",
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

            $view = view('ohcmanagement.master.employee_cum_patient.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Employee cum patient.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }


    public function employeeid(Request $request)
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
    public function employeename(Request $request)
    {
        $emp_id = $request->input('empId');

        $employee = Employee::select('emp_name')
            ->where('emp_id', $emp_id)
            ->first();

        if (!$employee) {
            $employee = Work::select('emp_name')
                ->where('emp_id', $emp_id)
                ->first();
        }

        if ($employee) {
            return response()->json([
                'employee' => $employee,


            ]);
        } else {
            return response()->json([
                'message' => 'Employee not found'
            ], 404);
        }
    }

    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('company');

        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        //return Response::download($filePath, $customFileName);
        return redirect(url($filePath));
    }
}
