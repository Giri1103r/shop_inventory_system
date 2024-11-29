<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\File;
use App\Models\Master\TrainingMatrix;
use App\Models\Master\TrainingSchedule;
use Str;
use PDF;
use Mail;
use Illuminate\Support\Facades\Auth;
use Session;
use Exception;
use DataTables;
use Response;
use App\Jobs\ImportdepartmentJob;


use App\Models\Master\Company;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use App\Models\Master\Department;
use App\Models\User;
use App\Models\UploadLog;


class DepartmentController extends Controller
{

    private $company;
    private $user;
    private $department;
    private $location;
    private $unit;
    private $uploadlog;
    private $training_schedule;
    private $training_matrix;

    public function __construct()
    {

        $this->company = new Company();
        $this->user = new User();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->department = new Department();
        $this->uploadlog = new UploadLog();
        $this->training_matrix = new TrainingMatrix();
        $this->training_schedule = new TrainingSchedule();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->department->list();

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
                                $btn = '<a href="' . admin_url('department/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            if (CheckUserPermission('edit')) {
                                $btn .= '<a href="' . admin_url('department/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            }
                            if (CheckUserPermission('delete')) {
                                $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '"  data-login_id="' . encryptId($row->login_id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            }
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status'])
                        ->setFilteredRecords($data['total_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {

                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
        $companyList  = $this->company->where('status', '1')->get();

        $data = array(
            'companyList' => $companyList,
        );
        return view('master.department.list', $data);
    }

    public function Add(Request $request)
    {

        try {
            $companyList  = $this->company->select('id', 'company_name')->where('status', '1')->get();
            $locationList  = $this->location->select('id', 'location_name')->where('status', '1')->get();
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $data = array(
                'companyList' => $companyList,
                'locationList' => $locationList,
                'unitList' => $unitList,
            );
            return view('master.department.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'department_id' => 'required',
                'company_id' => 'required',
                'location_id' => 'required',
                'unit_id' => 'required',
                'department_name' => 'required',
            ];
            $messages = [
                'department_id.required' => 'Please enter Department ID',
                'company_id.required' => 'Please Select Company ',
                'location_id.required' => 'Please Select Location ',
                'unit_id.required' => 'Please Select Unit',
                'department_name.required' => 'Please Enter Department Name',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                $this->department->store();
                Session::flash('success', 'Department added successfully!');
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('department/list'));
        } catch (Exception $ex) {


            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('department/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $department = $this->department->selectOne($id);

                $data = array(
                    'department' => $department,
                );
            }
            return view('master.department.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $department = $this->department->find($id);

            $companyList  = $this->company->select('id', 'company_name')->where('status', '1')->get();
            $locationList  = $this->location->select('id', 'location_name')->where('status', '1')->get();
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $data = array(
                'companyList' => $companyList,
                'locationList' => $locationList,
                'unitList' => $unitList,
                'department' => $department,
            );

            return view('master.department.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'department_id' => 'required',
                'company_id' => 'required',
                'location_id' => 'required',
                'unit_id' => 'required',
                'department_name' => 'required',
            ];
            $messages = [
                'department_id.required' => 'Please enter Department ID',
                'company_id.required' => 'Please Select Company ',
                'location_id.required' => 'Please Select Location ',
                'unit_id.required' => 'Please Select Unit',
                'department_name.required' => 'Please Enter Department Name',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->department->updates($id);

            Session::flash('success', 'Department updated successfully!');
            return redirect(admin_url('department/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('department/list'));
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->department->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Department status changed'], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);
            // $training_schedule = $this->training_schedule->where('department_id', $id)->exists();
            // $training_matrix = $this->training_matrix->where('department_id', $id)->exists();

            // if ($training_schedule || $training_matrix) {
            //     return response()->json(['status' => 'error', 'msg' => 'module_exits'], 406);
            // }
            $this->department->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => 'Department deleted successfully'], 200);
        } catch (Exception $ex) {
            dd($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }


    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->department->exportdata();

            $header = [
                __("common.sno"),
                'Department Id',
                'Company Name',
                'Location Name',
                'Unit Name',
                'Department Name',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->department_id;
                $export[] =  $data->company_name;
                $export[] =  $data->location_name;
                $export[] =  $data->unit_id;
                $export[] =  $data->department_name;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Department Details.xlsx')
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

            $allData = $this->department->exportdata();

            $header = [
                __("common.sno"),
                'Department Id',
                'Company Name',
                'Location Name',
                'Unit Name',
                'Department Name',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Department Details",
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

            $view = view('master.department.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Department.pdf";
            $mpdf->Output($filename, 'I');
        } catch (Exception $ex) {

            report($ex);
        }
    }
    public function Import(Request $request)
    {
        $data = array();
        return view('master.department.import', $data);
    }
    public function ImportSubmit(Request $request)
    {
        try {
            $file = $request->file('department_upload');

            $rules = [
                'department_upload' => 'required',
            ];
            $messages = [
                'department_upload.required' => 'Please upload a file',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            if ($file != null) {

                $uploadpath = 'public/uploads/department';

                $folderPath = public_path('uploads/department');

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

                // dispatch(new ImportdepartmentJob($details));
                   dispatch((new ImportdepartmentJob($details))->onQueue('department'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', __('Department uploaded sucessfully'));
            return redirect(admin_url('department/list'));
        } catch (Exception $ex) {

            Session::flash('error', __('Department upload failed'));
            return redirect(admin_url('department/list'));
        }
    }
    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('department');

        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        //return Response::download($filePath, $customFileName);
        return redirect(url($filePath));
    }

    public function list(Request $request)
    {
        $unit_id = decryptId($request->unit_id);
        $id = decryptId($request->id);
        $departments = $this->department->ajaxList($unit_id, $id);

        return response()->json($departments);
    }
    public function alllist(Request $request)
    {
        $unitID = decryptId($request->unit_id);
        $unit = $this->department->ajaxallList($unitID);
        return response()->json($unit);
    }
}
