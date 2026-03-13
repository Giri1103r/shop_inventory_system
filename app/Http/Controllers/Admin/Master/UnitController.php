<?php

namespace App\Http\Controllers\Admin\Master;

use App\Models\Master\TrainingMatrix;
use App\Models\Master\TrainingSchedule;
use App\Http\Controllers\Controller;
use App\Jobs\ImportLocationJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\File;
use Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Models\Master\Company;
use App\Models\Master\Unit;
use App\Models\Master\Location;
use App\Models\Master\Department;
use App\Models\User;
use App\Models\UploadLog;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{

    private $company;

    private $location;

    private $department;
    private $unit;
    private $uploadlog;

    public function __construct()
    {

        $this->company = new Company();
        $this->user = new User();

        $this->department = new Department();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->uploadlog = new UploadLog();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->unit->list();

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
                                $btn = '<a href="' . admin_url('master/unit/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            if (CheckUserPermission('edit')) {
                                $btn .= '<a href="' . admin_url('master/unit/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            }
                            if (CheckUserPermission('delete')) {
                                $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '"  data-login_id="' . encryptId($row->login_id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            }
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
        return view('admin.master.unit.list', $data);
    }

    public function Add(Request $request)
    {

        try {
            $companyList  = $this->company->getcompany();
            $data = array(
                'companyList' => $companyList,
            );

            return view('admin.master.unit.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/unit/list'));
        }
    }

    public function Store(Request $request)
    {
        try {
            DB::beginTransaction();

            $rules = [

                'location_id' => 'required',
                'company_id' => 'required',
                'unit_name' => 'required',
            ];
            $messages = [

                'location_id.required' => 'Please Select Location ',
                'company_id.required' => 'Please Select Company ',
                'unit_name.required' => 'Please Enter Unit Name',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $this->unit->store();
            DB::commit();
            Session::flash('success', __('common.created_msg'));
            return redirect(admin_url('master/unit/list'));
        } catch (Exception $ex) {
            report($ex);
            DB::rollBack();
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/unit/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $unit = $this->unit->selectOne($id);

                $data = array(
                    'unit' => $unit,
                );
            }
            return view('admin.master.unit.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/unit/list'));
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $unit = $this->unit->find($id);

            $companyList  = $this->company->getcompany();
            $locationList  = $this->location->getlocation();


            $data = array(
                'companyList' => $companyList,
                'locationList' => $locationList,
                'unit' => $unit,
            );

            return view('admin.master.unit.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/unit/list'));
        }
    }

    public function Update(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = decryptId($request->id);
            $rules = [

                'location_id' => 'required',
                'company_id' => 'required',
                'unit_name' => 'required',
            ];
            $messages = [

                'location_id.required' => 'Please Select Location ',
                'company_id.required' => 'Please Select Company ',
                'unit_name.required' => 'Please Enter Unit Name',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->unit->updates($id);
            DB::commit();

            Session::flash('success', __('common.updated_msg'));

            return redirect(admin_url('master/unit/list'));
        } catch (Exception $ex) {
            report($ex);
            DB::rollBack();
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/unit/list'));
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->unit->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Unit status changed'], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $department = $this->department->where('unit_id', $id)->exists();
            // $training_schedule = $this->training_schedule->where('unit_id', $id)->exists();
            // $training_matrix = $this->training_matrix->where('unit_id', $id)->exists();

            if ($department) {
                return response()->json(['status' => 'error', 'msg' => 'module_exits'], 406);
            }
            $this->unit->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => 'Unit deleted successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }


    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->unit->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("common.unit_id"),
                __("common.company"),
                __("common.locaiton"),
                __("common.unit"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->unit_id;
                $export[] =  $data->location_name;
                $export[] =  $data->company_name;
                $export[] =  $data->unit_name;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Unit Master.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/unit/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->unit->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("common.unit_id"),
                __("common.company"),
                __("common.locaiton"),
                __("common.unit"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Unit Details",
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

            $view = view('admin.master.unit.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Unit Master.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/unit/list'));
        }
    }
    public function Import(Request $request)
    {
        $data = array();
        return view('admin.master.unit.import', $data);
    }
    public function ImportSubmit(Request $request)
    {
        try {
            $file = $request->file('unit_upload');

            $rules = [
                'unit_upload' => 'required',
            ];
            $messages = [
                'unit_upload.required' => 'Please upload a file',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            if ($file != null) {

                $uploadpath = 'public/uploads/unit';

                $folderPath = public_path('uploads/unit');

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
                    'upload_type' => 3,
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

                // dispatch(new ImportUnitJob($details));
                dispatch((new ImportUnitJob($details))->onQueue('unit'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', __('common.file_upload_success_msg'));

            return redirect(admin_url('master/unit/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('common.file_upload_fails_msg'));

            return redirect(admin_url('master/unit/list'));
        }
    }


    public function list(Request $request, $locationId)
    {
        $locationId = decryptId($locationId);
        $id = decryptId($request->id);
        $unit = $this->unit->ajaxList($id, $locationId);

        return response()->json($unit);
    }

    public function unitData(Request $request, $companyId)
    {
        $companyId = decryptId($companyId);
        $id = decryptId($request->id);
        $unit = $this->unit->unitajaxList($id, $companyId);

        return response()->json($unit);
    }
    public function alllist(Request $request)
    {
        $locationId = decryptId($request->location_id);
        $unit = $this->unit->ajaxallList($locationId);
        return response()->json($unit);
    }
    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('unit');

        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        //return Response::download($filePath, $customFileName);
        return redirect(url($filePath));
    }



    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $unit_name = $request->unit_name;
            $company_id = decryptId($request->company_id);
            $location_id = decryptId($request->location_id);
            $id = $request->id;
            if ($id == '') {
                $record = $this->unit->uniqueCheck($unit_name, $location_id, $company_id);
            } else {
                $id = decryptId($id);
                $record = $this->unit->ExistuniqueCheck($unit_name, $location_id, $company_id, $company_id, $id);
            }
            if ($record->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }
}
