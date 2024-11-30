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


use App\Models\Master\Company;
use App\Models\Master\Unit;
use App\Models\Master\Location;
use App\Models\Master\Department;
use App\Models\User;
use App\Models\UploadLog;
use App\Jobs\ImportCompanyJob;


class CompanyController extends Controller
{

    private $company;
    private $unit;
    private $location;
    private $department;
    private $user;
    private $uploadlog;


    public function __construct()
    {

        $this->company = new Company();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->department = new Department();
        $this->user = new User();
        $this->uploadlog = new UploadLog();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->company->list();

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
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            if (CheckUserPermission('view')) {
                                $btn = '<a href="' . admin_url('company/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            if (CheckUserPermission('edit')) {
                                $btn .= '<a href="' . admin_url('company/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            }
                            // if (CheckUserRole(ROLE_ADMIN)) {
                            //     $btn .= '<a href="' . admin_url('company/passwordchange/' . encryptId($row->id)) . '" class="key-icon" title="passwordchange"><i class="fas fa-key"></i> ';
                            // }
                            // if (CheckUserPermission('delete')) {
                            //     $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '"  data-login_id="' . encryptId($row->login_id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            // }
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {

                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
        $data = array();

        return view('master.company.list', $data);
    }

    public function Add(Request $request)
    {

        try {

            $data = array();
            return view('master.company.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'company_id' => 'required',
                'company_name' => 'required',
                'short_name' => 'required',
                'address' => 'required',
            ];
            $messages = [
                'company_id.required' => 'Please enter Company ID',
                'company_name.required' => 'Please enter Company Name',
                'short_name.required' => 'Please enter Short Name',
                'address.required' => 'Please enter Company Address',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                // $userDetails = $this->user->companystore();

                // $id = $userDetails->id;
                $company = $this->company->store();
                // if ($userDetails->email != '' || $userDetails->email != null) {

                //     $empdetails =  $this->user->selectOne($id);

                //     $emp  = $empdetails->toArray();

                //     Mail::to($empdetails->email)->queue(new EmployeeRegisterEmail($emp));
                // }

                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('company/list'));
        } catch (Exception $ex) {


            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('company/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $company = $this->company->selectOne($id);

                $data = array(
                    'company' => $company,
                );
            }
            return view('master.company.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);


            $company = $this->company->find($id);
            $data = array(
                'company' => $company,
            );


            return view('master.company.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'company_id' => 'required',
                'company_name' => 'required',
                'short_name' => 'required',
                'address' => 'required',
            ];
            $messages = [
                'company_id.required' => 'Please enter Company ID',
                'company_name.required' => 'Please enter Company Name',
                'short_name.required' => 'Please enter Short Name',
                'address.required' => 'Please enter Company Address',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->company->updates($id);

            // $company = $this->company->find($id);
            // $this->user->companyUpdate($company->login_id);

            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('company/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('company/list'));
        }
    }

    public function PasswordUpdate(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $company = $this->company->find($id);

            $data = array(
                'company' => $company,
            );

            return view('master.company.passwordupdate', $data);
        } catch (Exception $error) {

            report($error);
        }
    }

    // public function PasswordUpdateSubmit(Request $request)
    // {
    //     try {
    //         $id = decryptId($request->id);

    //         $rules = [
    //             'emp_id' => 'required',
    //             'emp_name' => 'required',
    //             'password' => 'required|confirmed',
    //         ];
    //         $messages = [
    //             'emp_id.required' => 'Please enter Employee No',
    //             'emp_name.required' => 'Please enter Employee Name',
    //             'password.required' => 'Please enter the Password',
    //             'password_confirmation.required' => 'Please enter the Confirm Password',
    //             'password.confirmed' => 'The password confirmation does not match.',
    //         ];

    //         $validator = Validator::make($request->all(), $rules, $messages);

    //         if ($validator->fails()) {

    //             return redirect()->back()->withErrors($validator)->withInput();
    //         }
    //         $company = $this->company->find($id);
    //         $this->user->passwordUpdate($company->login_id);
    //         Session::flash('success', 'Company password updated successfully!');
    //         return redirect(admin_url('company/list'));
    //     } catch (Exception $ex) {
    //         report($ex);
    //         Session::flash('error', 'Something went wrong, Please try after sometimes!');
    //         return redirect(admin_url('company/list'));
    //     }
    // }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $company_name = $request->company_name;
            $id = $request->id;
            if ($id == '') {
                $record = $this->company->uniqueCheck($company_name);
            } else {
                $id = decryptId($id);
                $record = $this->company->ExistuniqueCheck($company_name, $id);
            }
            if ($record->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->company->statuschange($id);
            // $company =  $this->company->selectOne($id);
            // $this->user->statuschange($company->login_id);

            return response()->json(['status' => 'success', 'msg' => 'Company status changed'], 200);
        } catch (Exception $ex) {

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
            $this->company->deleterecord($id);
            return response()->json(['status' => 'success', 'msg' => 'Company deleted successfully'], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }
 
    public function Import(Request $request)
    {
        $data = array();
        return view('master.company.import', $data);
    }
    public function ImportSubmit(Request $request)
    {
        try {
            $file = $request->file('company_upload');

            $rules = [
                'company_upload' => 'required',
            ];
            $messages = [
                'company_upload.required' => 'Please upload a file',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            if ($file != null) {

                $uploadpath = 'public/uploads/company';

                $folderPath = public_path('uploads/company');

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
                    'upload_type' => 1,
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

                // dispatch(new ImportCompanyJob($details));
                   dispatch((new ImportCompanyJob($details))->onQueue('company'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', __('Company uploaded sucessfully'));
            return redirect(admin_url('company/list'));
        } catch (Exception $ex) {

            Session::flash('error', __('Company upload failed'));
            return redirect(admin_url('company/list'));
        }
    }
    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->company->exportdata();

            $header = [
                __("common.sno"),
                'Company ID',
                'Company Name',
                'Short Name',
                'Address',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->company_id;
                $export[] =  $data->company_name;
                $export[] =  $data->short_name;
                $export[] =  $data->address;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Company Details.xlsx')
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

            $allData = $this->company->exportdata();

            $header = [
                __("common.sno"),
                'Company ID',
                'Company Name',
                'Short Name',
                'Address',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Company Details",
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

            $view = view('master.company.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Company.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
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
