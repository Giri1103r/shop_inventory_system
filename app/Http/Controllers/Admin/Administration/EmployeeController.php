<?php

namespace App\Http\Controllers\Admin\Master;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\File;

use Str;
use PDF;
use Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Exception;
use DataTables;
use Response;
use App\Jobs\ImportdepartmentJob;

use App\Mail\RestEmployeePasswordEmail;
use App\Models\Master\Bloodgroup;
use App\Models\Master\Company;
use App\Models\Master\Employee;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use App\Models\Master\Department;
use App\Models\User;
use App\Models\UploadLog;
use App\Models\Master\UserRole;
use Illuminate\Contracts\Session\Session as SessionSession;
use Illuminate\Support\Facades\Session as FacadesSession;

class EmployeeController extends Controller
{

    private $company;
    private $user;
    private $department;
    private $location;
    private $unit;
    private $uploadlog;
    private $employee;
    private $userrole;
    private $bloodgroup;


    public function __construct()
    {

        $this->company = new Company();
        $this->user = new User();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->department = new Department();
        $this->uploadlog = new UploadLog();
        $this->employee = new Employee();
        $this->userrole = new UserRole();
        $this->bloodgroup = new Bloodgroup();

    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->employee->list();

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
                            return Displaydatetimeformat($row->created_at);
                        })

                        ->addColumn('action', function ($row) {
                            $btn = '';
                            if (CheckUserPermission('view')) {
                                $btn = '<a href="' . admin_url('employee/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            if (CheckUserPermission('edit')) {
                                $btn .= '<a href="' . admin_url('employee/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            }
                            if (CheckUserRole(ROLE_ADMIN) || CheckUserRole(ROLE_SUPERADMIN)) {
                                $btn .= '<a href="' . admin_url('employee/passwordchange/' . encryptId($row->id)) . '" class="key-icon" title="passwordchange"><i class="fas fa-key"></i> ';
                            }

                            return $btn;
                        })
                        ->editColumn('unit', function ($row) {
                            return getUnitname($row->unit);
                        })
                        ->editColumn('department', function ($row) {
                            return getDepartment($row->department);
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status'])
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
        $companyList  = $this->company->where('status', '1')->get();
        $unit = $this->unit->getunit();
        $data = array(
            'companyList' => $companyList,
            'unit' => $unit,
        );
        return view('master.employee.list', $data);
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {

                $userrole  = $this->userrole->select('id', 'role_name')->where('status', '1')->get();
                $employee = $this->employee->selectOne($id);

                $data = array(
                    'employee' => $employee,
                    'userrole' => $userrole,
                );
            }
            return view('master.employee.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $employee = $this->employee->find($id);

            $companyList  = $this->company->select('id', 'company_name')->where('status', '1')->get();
            $userrole  = $this->userrole->select('id', 'role_name')->where('status', '1')->get();
            $bloodgroup=$this->bloodgroup->getBloodgroup();
            $data = array(
                'companyList' => $companyList,
                'employee' => $employee,
                'userrole' => $userrole,
                'bloodgroup'=>$bloodgroup,
            );


            return view('master.employee.edit', $data);
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
                'email' => 'required',
            ];
            $messages = [

                'emp_name.required' => 'Please Enter Employee Name',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $employee =  $this->employee->updates($id);
            $userUpdate =  $this->user->userUpdate($employee);


            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('employee/list'));
        } catch (Exception $ex) {


            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('employee/list'));
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->employee->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Employee status changed'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }
    public function PasswordUpdate(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $userrole  = $this->userrole->select('id', 'role_name')->where('status', '1')->get();
            $employee = $this->employee->selectOne($id);

            $data = array(
                'employee' => $employee,
                'userrole' => $userrole,
            );

            return view('master.employee.passwordupdate', $data);
        } catch (Exception $error) {

            report($error);
        }
    }

    public function PasswordUpdateSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $rules = [
                'password' => 'required|confirmed',
            ];
            $messages = [
                'password.required' => 'Please enter the Password',
                'password_confirmation.required' => 'Please enter the Confirm Password',
                'password.confirmed' => 'The password confirmation does not match.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {

                return redirect()->back()->withErrors($validator)->withInput();
            }
            $employee = $this->employee->find($id);
            $user = $this->user->passwordUpdate($employee->login_id);
            if ($user) {
                $updatedUser = $this->user->find($employee->login_id);
                $newpass =  $request->password;
                if ($updatedUser && !empty($updatedUser->email)) {
                    Mail::to($updatedUser->email)->queue(new RestEmployeePasswordEmail($updatedUser,$newpass));
                }
                Session::flash('success', 'Employee password updated successfully!');
                return redirect(admin_url('employee/list'));
            }
            Session::flash('success', 'Employee password updated successfully!');
            return redirect(admin_url('employee/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('employee/list'));
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->employee->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Employee Id',
                'Employee Name',
                'Email',
                'Phone Number',
                'Company Name',
                'Location Name',
                'Unit Name',
                'Department Name',
                'Employee Status',
                'Reporting Manager',
                __("common.status"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->emp_id;
                $export[] =  $data->emp_name;
                $export[] =  $data->email;
                $export[] =  $data->mobile_no;
                $export[] =  getCompanyname($data->company);
                $export[] =  getLocationname($data->location);
                $export[] =  getUnitname($data->unit);
                $export[] =  getDepartment($data->department);
                $export[] =  $data->employee_status;
                $export[] =  $data->reporting_manager;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Employee Master.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

          report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('employee/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->employee->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Employee Id',
                'Employee Name',
                'Email',
                'Phone Number',
                'Company Name',
                'Location Name',
                'Unit Name',
                'Department Name',
                'Employee Status',
                'Reporting Manager',
                __("common.status"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Employee Details",
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

            $view = view('master.employee.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Employee Master.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

          report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('employee/list'));
        }
    }


    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $email = $request->email;
            $id = $request->id;
            if ($id == '') {
                $record = $this->employee->uniqueCheck($email);
            } else {
                $id = decryptId($id);
                $record = $this->employee->ExistuniqueCheck($email, $id);
            }
            if ($record->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }

    public function companyajax(Request $request)
    {
        $companyID = decryptId($request->company_id);

        // Fetch unit list
        $unitList = $this->unit
            ->where('company_id', $companyID)
            ->where('status', 1)
            ->where('trash', 'NO')
            ->select('id', 'unit_name')
            ->get()
            ->map(function ($unit) {
                $unit->id = $unit->id;
                return $unit;
            });

        return response()->json([
            'unit' => $unitList,
        ]);
    }

    public function list(Request $request, $unit_id)
    {
        $unit_id = $unit_id;
        $id = $request->id;
        $departments = $this->department->where('unit_id',$unit_id)->select('id','department_name')  ->where('status', 1)
        ->where('trash', 'NO') ->get();

        return response()->json($departments);
    }
}
