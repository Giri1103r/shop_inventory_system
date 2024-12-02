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
use App\Jobs\ImportLocationJob;


use App\Models\Master\Company;
use App\Models\Master\Unit;
use App\Models\Master\Location;
use App\Models\Master\Department;
use App\Models\User;
use App\Models\UploadLog;


class LocationController extends Controller
{

    private $company;
    private $user;
    private $department;
    private $unit;
    private $location;
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

                    $data =  $this->location->list();

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
                                $btn = '<a href="' . admin_url('location/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            if (CheckUserPermission('edit')) {
                                $btn .= '<a href="' . admin_url('location/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            }
                            // if (CheckUserPermission('delete')) {
                            //     $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '"  data-login_id="' . encryptId($row->login_id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            // }
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status'])
                        ->setFilteredRecords($data['total_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {
                    dd($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
        $companyList  = $this->company->where('status', '1')->get();
        $data = array(
            'companyList' => $companyList
        );
        return view('master.location.list', $data);
    }

    public function Add(Request $request)
    {

        try {
            $companyList  = $this->company->select('id', 'company_name')->where('status', '1')->get();
            $data = array(
                'companyList' => $companyList
            );
            return view('master.location.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'location_id' => 'required',
                'company_id' => 'required',
                'location_name' => 'required',
            ];
            $messages = [
                'location_id.required' => 'Please enter Location ID',
                'company_id.required' => 'Please enter Company Name',
                'location_name.required' => 'Please enter Location Name',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                $this->location->store();
                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('location/list'));
        } catch (Exception $ex) {


            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('location/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $location = $this->location->selectOne($id);

                $data = array(
                    'location' => $location,
                );
            }
            return view('master.location.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $companyList  = $this->company->select('id', 'company_name')->where('status', '1')->get();
            $location = $this->location->find($id);

            $data = array(
                'companyList' => $companyList,
                'location' => $location,
            );

            return view('master.location.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'location_id' => 'required',
                'company_id' => 'required',
                'location_name' => 'required',
            ];
            $messages = [
                'location_id.required' => 'Please enter Location ID',
                'company_id.required' => 'Please enter Company Name',
                'location_name.required' => 'Please enter Location Name',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->location->updates($id);

            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('location/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('location/list'));
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->location->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Location status changed'], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $department = $this->department->where('location_id', $id)->exists();
            $unit = $this->unit->where('location_id', $id)->exists();

            if ($unit || $department) {
                return response()->json(['status' => 'error', 'msg' => 'module_exits'], 406);
            }
            $this->location->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => 'Location deleted successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }


    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->location->exportdata();

            $header = [
                __("common.sno"),
                'Location ID',
                'Company Name',
                'Location Name',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->location_id;
                $export[] =  $data->company_name;
                $export[] =  $data->location_name;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Location Details.xlsx')
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

            $allData = $this->location->exportdata();

            $header = [
                __("common.sno"),
                'Location ID',
                'Company Name',
                'Location Name',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Location Details",
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

            $view = view('master.location.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Location.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }

    public function list(Request $request ,$companyId)
    {
        
        $companyId = decryptId($companyId);
        $id = decryptId($request->id);
        $locations = $this->location->ajaxList($companyId, $id);

        return response()->json($locations);
    }


    public function alllist(Request $request)
    {
        $companyId = decryptId($request->company_id);
        $locations = $this->location->ajaxallList($companyId);
        return response()->json($locations);
    }
    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('location');

        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        //return Response::download($filePath, $customFileName);
        return redirect(url($filePath));
    }
    public function Import(Request $request)
    {
        $data = array();
        return view('master.location.import', $data);
    }
    public function ImportSubmit(Request $request)
    {
        try {
            $file = $request->file('location_upload');

            $rules = [
                'location_upload' => 'required',
            ];
            $messages = [
                'location_upload.required' => 'Please upload a file',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            if ($file != null) {

                $uploadpath = 'public/uploads/location';

                $folderPath = public_path('uploads/location');

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
                    'upload_type' => 2,
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

                // dispatch(new ImportLocationJob($details));
                dispatch((new ImportLocationJob($details))->onQueue('location'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', __('Location uploaded sucessfully'));
            return redirect(admin_url('location/list'));
        } catch (Exception $ex) {

            Session::flash('error', __('Location upload failed'));
            return redirect(admin_url('location/list'));
        }
    }


    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $location_name = $request->location_name;
            $company_id = decryptId($request->company_id);
            $id = $request->id;
            if ($id == '') {
                $record = $this->location->uniqueCheck($location_name, $company_id);
            } else {
                $id = decryptId($id);
                $record = $this->location->ExistuniqueCheck($location_name, $company_id, $id);
            }
            if ($record->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }
}
