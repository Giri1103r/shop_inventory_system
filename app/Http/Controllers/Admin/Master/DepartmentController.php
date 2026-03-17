<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

use Str;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
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


    public function __construct()
    {

        $this->company = new Company();
        $this->user = new User();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->department = new Department();
        $this->uploadlog = new UploadLog();
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
                                $btn = '<a href="' . admin_url('master/department/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            if (CheckUserPermission('edit')) {
                                $btn .= '<a href="' . admin_url('master/department/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            }
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
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
        $companyList  = $this->company->getcompany();
        $data = array(
            'companyList' => $companyList,
        );
        return view('admin.master.department.list', $data);
    }

    public function Add(Request $request)
    {

        try {
            $companyList  = $this->company->getcompany();
            $locationList  = $this->location->getlocation();
            $unitList  = $this->unit->getunit();
            $data = array(
                'companyList' => $companyList,
                'locationList' => $locationList,
                'unitList' => $unitList,
            );
            return view('admin.master.department.add', $data);
        } catch (Exception $ex) {
            report($ex);
                Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/department/list'));
        }
    }

    public function Store(Request $request)
    {
        try {
            DB::beginTransaction();

            $rules = [

                'company_id' => 'required',
                'location_id' => 'required',
                'unit_id' => 'required',
                'department_name' => 'required',
            ];
            $messages = [

                'company_id.required' => 'Please Select Company ',
                'location_id.required' => 'Please Select Location ',
                'unit_id.required' => 'Please Select Unit',
                'department_name.required' => 'Please Enter Department Name',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            $this->department->store();
            DB::commit();

            Session::flash('success', 'Your data has been created successfully!');
            return redirect(admin_url('master/department/list'));
        } catch (Exception $ex) {

            report($ex);
            DB::rollBack();
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/department/list'));
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
            return view('admin.master.department.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $department = $this->department->find($id);

            $companyList  = $this->company->getcompany();
            $locationList  = $this->location->getlocation();
            $unitList  = $this->unit->getunit();
            $data = array(
                'companyList' => $companyList,
                'locationList' => $locationList,
                'unitList' => $unitList,
                'department' => $department,
            );

            return view('admin.master.department.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = decryptId($request->id);
            $rules = [
               
                'company_id' => 'required',
                'location_id' => 'required',
                'unit_id' => 'required',
                'department_name' => 'required',
            ];
            $messages = [

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
            DB::commit();

            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('master/department/list'));
        } catch (Exception $ex) {
            report($ex);
            DB::rollBack();
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/department/list'));
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->department->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Department status changed'], 200);
        } catch (Exception $ex) {
            report($ex);
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
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }


    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->department->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("common.department_id"),
                __("common.company"),
                __("common.location"),
                __("common.unit"),
                __("common.department"),
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
                $export[] =  $data->unit_name;
                $export[] =  $data->department_name;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Department Master.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/department/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->department->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("common.department_id"),
                __("common.company"),
                __("common.location"),
                __("common.unit"),
                __("common.department"),
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

            $view = view('admin.master.department.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Department Master.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/department/list'));
        }
    }
    public function Import(Request $request)
    {
        $data = array();
        return view('admin.master.department.import', $data);
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
                    'upload_type' => 4,
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

                dispatch(new ImportdepartmentJob($details));
                // dispatch((new ImportdepartmentJob($details))->onQueue('department'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', __('common.file_upload_success_msg'));

            return redirect(admin_url('master/department/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('success', __('common.file_upload_success_msg'));
            return redirect(admin_url('master/department/list'));
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
    public function multipleList(Request $request, $unit_id)
    {
        $unit_id = decryptId($unit_id);
        $preselectedIds = array_map('decryptId', $request->input('preselected_ids', []));

        $departments = $this->department->multipleAjaxList($unit_id, $preselectedIds);

        return response()->json($departments);
    }

    public function list(Request $request, $unit_id)
    {
        $unit_id = decryptId($unit_id);
        $id = decryptId($request->id);
        $departments = $this->department->ajaxList($id, $unit_id);

        return response()->json($departments);
    }
    public function alllist(Request $request)
    {
        $unitID = decryptId($request->unit_id);
        $unit = $this->department->ajaxallList($unitID);
        return response()->json($unit);
    }


    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $unit_id = decryptId($request->unit_id);
            $company_id = decryptId($request->company_id);
            $location_id = decryptId($request->location_id);
            $department_name = $request->department_name;
            $id = $request->id;
            if ($id == '') {

                // dd('sdcds');
                $record = $this->department->uniqueCheck($company_id, $location_id, $unit_id, $department_name);
            } else {

                // dd('sdcgsed');
                $id = decryptId($id);
                $record = $this->department->ExistuniqueCheck($company_id, $location_id, $unit_id, $department_name, $id);
            }
            if ($record->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }
}
