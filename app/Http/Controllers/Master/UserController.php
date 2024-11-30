<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\File;

use Str;
use PDF;
use Mail;
use Illuminate\Support\Facades\Auth;
use Session;
use Exception;
use DataTables;
use Response;

use App\Models\Master\Designation;
use App\Models\Master\Company;
use App\Models\Master\Department;
use App\Models\Master\UserRole;

use App\Models\User;
use App\Models\UploadLog;

use App\Jobs\ImportEmployeeJob;
use App\Mail\EmployeeRegisterEmail;

class UserController extends Controller
{

    private $designation;
    private $company;
    private $department;
    private $Role;
    private $user;
    private $uploadlog;

    public function __construct()
    {

        // $this->designation = new designataion();
        $this->company = new Company();
        $this->department = new department();
        $this->Role = new UserRole();
        $this->user = new User();
        $this->uploadlog = new UploadLog();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {

                    $data =  $this->user->list();
                    $datatables = Datatables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active<span>";
                            if ($row->status == 1) {
                                $text = "<span  class= 'statusChange' data-id='" . encryptId($row->id) . "' data-type = '1' >Active<span>";
                            } else if ($row->status == 0) {
                                $text = "<span class= 'statusChange' data-id='" . encryptId($row->id) . "' data-type = '0' >In-Active<span>";
                            }
                            return $text;
                        })
                        ->addColumn('user_id', function ($row) {
                            return $row->employee_id;
                        })
                        ->addColumn('user_name', function ($row) {
                            return $row->name;
                        })
                        ->addColumn('user_role', function ($row) {
                            return $row->role_name;
                        })

                        ->addColumn('created_at', function ($row) {
                            return Displaydatetimeformat($row->created_at);
                        })

                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            // $btn = '<a href="' . admin_url('administration/users/view/' . encryptId($row->id)) . '"   class="view-icon" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // $btn .= '<a href="' . admin_url('administration/users/edit/' . encryptId($row->id)) . '" class="edit-icon" title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            // if (CheckUserRole(ROLE_ADMIN)) {
                            //     $btn .= '<a href="' . admin_url('administration/users/passwordchange/' . encryptId($row->id)) . '" class="key-icon" title="passwordchange"><i class="fas fa-key"></i> ';
                            // }
                            // $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '"  data-login_id="' . encryptId($row->login_id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            // return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {


                    dd($ex);
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }

        $data = array();

        return view('master.user.list', $data);
    }

    public function Add(Request $request)
    {

        try {
            $rolelist = $this->Role->where('status', '1')->get();
            $company = $this->company->where('status', '1')->get();
            $data = array(
                'rolelist' => $rolelist,
                'company' => $company,
            );
            return view('master.user.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {

        try {

            $rules = [

                'employee_no' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'joining_date' => 'required',
                'email' => 'required|email',
                'phone' => 'required|numeric',
            ];
            $messages = [
                'employee_no.required' => 'Please enter Employee No',
                'first_name.required' => 'Please enter Employee First Name',
                'last_name.required' => 'Please enter Employee Last Name',
                'joining_date.required' => 'Please enter Joining Date',
                'email.required' => 'Please enter Email',
                'email.email' => 'Please enter Valid Email',
                'phone.required' => 'Please enter Phone',
                'phone.numeric' => 'Please enter Valid Phone',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {


                $userDetails = $this->user->store();

                $id = $userDetails->id;
                $employeeDetails = $this->user->store($id);
                if ($userDetails->email != '' || $userDetails->email != null) {

                    $empdetails =  $this->user->selectOne($id);

                    $emp  = $empdetails->toArray();

                    Mail::to($empdetails->email)->queue(new EmployeeRegisterEmail($emp));
                }

                Session::flash('success', 'Employee  added successfully!');
            } catch (Exception $ex) {
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }
            return redirect(admin_url('employee/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('employee/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {

                $employee = $this->user->selectOne($id);

                $data = array(
                    'employee' => $employee,
                );
            }
            return view('master.user.view', $data);
        } catch (Exception $ex) {
            report($ex);
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);


            $employee = $this->user->find($id);
            $data = array(
                'employee' => $employee
            );

            return view('master.user.edit', $data);
        } catch (Exception $error) {

            report($error);
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $rules = [

                'employee_no' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'joining_date' => 'required',
            ];
            $messages = [
                'employee_no.required' => 'Please enter Employee No',
                'first_name.required' => 'Please enter Employee First Name',
                'last_name.required' => 'Please enter Employee Last Name',
                'joining_date.required' => 'Please enter Joining Date',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->user->updates($id);

            $empdetails = $this->user->find($id);
            $this->user->userUpdate($empdetails->login_id);

            Session::flash('success', 'Employee updated successfully!');
            return redirect(admin_url('employee/list'));
        } catch (Exception $ex) {
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('employee/list'));
        }
    }

    public function PasswordUpdate(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $employee = $this->user->find($id);

            $data = array(
                'employee' => $employee,
            );

            return view('master.user.passwordupdate', $data);
        } catch (Exception $error) {

            report($error);
        }
    }

    public function PasswordUpdateSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $rules = [
                'emp_id' => 'required',
                'emp_name' => 'required',
                'password' => 'required|confirmed',
            ];
            $messages = [
                'emp_id.required' => 'Please enter Employee No',
                'emp_name.required' => 'Please enter Employee Name',
                'password.required' => 'Please enter the Password',
                'password_confirmation.required' => 'Please enter the Confirm Password',
                'password.confirmed' => 'The password confirmation does not match.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                dd($validator);
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $empdetails = $this->user->find($id);
            $this->user->passwordUpdate($empdetails->login_id);
            Session::flash('success', 'Employee password updated successfully!');
            return redirect(admin_url('employee/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('employee/list'));
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {

            $type = $request->type;
            $value = $request->value;
            switch ($type) {
                case 'employee_no':
                    $param = 'employee_no';
                    break;
                default:
                    $param = $type;
                    break;
            }

            $id = $request->id;

            if ($id == '') {
                $data = array(
                    'param' => $param,
                    'value' => $value,
                );

                $user = $this->user->UniqueCheck($data);
            } else {
                $data = array(
                    'param' => $param,
                    'value' => $value,
                    'id' => $id,
                );
                $user = $this->user->ExistuniqueCheck($data);
            }

            if ($user->count()) {
                return "false";
            }
            return "true";
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->user->statuschange($id);
            $empdetails =  $this->user->selectOne($id);
            $this->user->statuschange($empdetails->login_id);

            return response()->json(['status' => 'success', 'msg' => 'Employee status changed'], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $login_id = decryptId($request->login_id);
            if ($login_id) {
                $uauc_reportbyMapped = $this->uauc->where('reported_by', $login_id)->exists();
                $uauc_summaryperMapped = $this->uauc->where('area_owner_summary_id', $login_id)->exists();
                $uauc_affectperMapped = $this->uauc->where('person_affected_in_mtc_fac', $login_id)->exists();
                $uauc_caperMapped = $this->uauc_ca->where('responsible_id', $login_id)->exists();
                $approve_rejMapped = $this->approve_rej_log->where('user_id', $login_id)->exists();
                if ($uauc_reportbyMapped || $uauc_summaryperMapped || $uauc_affectperMapped || $uauc_caperMapped || $approve_rejMapped) {
                    return response()->json(['status' => 'error', 'msg' => 'module_exits'], 406);
                }
            }

            $this->user->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => 'Employee deleted successfully'], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function Import(Request $request)
    {
        $data = array();
        return view('master.user.import', $data);
    }

    public function ImportSubmit(Request $request)
    {
        try {
            $file = $request->file('employee_upload');

            $rules = [
                'employee_upload' => 'required',
            ];
            $messages = [
                'employee_upload.required' => 'Please upload a file',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            if ($file != null) {

                $uploadpath = 'public/uploads/employee';

                $folderPath = public_path('uploads/employee');

                if (!File::exists($folderPath)) {

                    File::makeDirectory($folderPath, 0755, true);
                }

                $filenewname = time() . Str::random('10') . '.' . $file->getClientOriginalExtension();

                $fileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();

                $fileExt = $file->getClientOriginalExtension();

                $file->move($uploadpath, $filenewname);

                $path = $uploadpath . "/" . $filenewname;
                $user_id = Auth::id();

                $insert_data = array(
                    'upload_type' => 8,
                    'upload_status' => 0,
                    'file_name' => $filenewname,
                    'file_orgname' => $fileName,
                    'file_path' => $path,
                    'file_size' => $fileSize,
                    'file_extension' => $fileExt,
                    'created_by' => $user_id,
                );

                $insert_id =  $this->uploadlog->create($insert_data)->id;



                $details = [
                    "user_id" => $user_id,
                    "log_id" => $insert_id,
                    "path" => $path,
                ];

                //dispatch(new ImportEmployeeJob($details));



                dispatch((new ImportEmployeeJob($details))->onQueue('empimport'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', 'Successfully Employee upload');
            return redirect(admin_url('employee/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Employee upload failed!');
            return redirect(admin_url('employee/list'));
        }
    }

    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('employee');

        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        //return Response::download($filePath, $customFileName);
        return redirect(url($filePath));
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->user->exportdata();

            // dd($allData);

            $header = [
                __("common.sno"),
                'User  ID',
                'User  Name',
                'Role',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];
            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->employee_id;
                $export[] =  $data->name;
                $export[] =  $data->role_name;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Users Details.xlsx')
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

            $allData = $this->user->exportdata();

            $header = [
                __("common.sno"),
                'User  ID',
                'User  Name',
                'Role',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "User Details",
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

            $view = view('master.user.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "master.user.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function getUser(Request $request)
    {

        $userrole = $request->userrole;
        $department = decryptId($request->department);

        $where = $whereIn = array();

        switch ($userrole) {
            case "job_owner":
                $whereIn = array(
                    ROLE_JOBOWNER
                );
                $where = array(
                    'emp_department_id' => $department,
                );
                break;
            default:
                $whereIn = array();
                $where = array();
                break;
        }

        $employee = $this->user->ajaxList($where, $whereIn);
        return response()->json($employee);
    }
}
