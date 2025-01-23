<?php

namespace App\Http\Controllers\OhcManagement\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Unit;
use App\Models\Master\Location;
use App\Models\Master\Department;
use App\Models\User;
use App\Models\UploadLog;
use App\Jobs\ImportfirstaidlocationJob;
use App\Models\Master\Employee;
use App\Models\OhcManagement\Master\FirstAidLocation;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FirstAidLocationController extends Controller
{

    private $firstaidlocation;
    private $unit;
    private $location;
    private $department;
    private $user;
    private $uploadlog;


    public function __construct()
    {

        $this->firstaidlocation = new FirstAidLocation();
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

                    $data =  $this->firstaidlocation->list();

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
                                $btn = '<a href="' . admin_url('ohc/first-aid-location/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            if (CheckUserPermission('edit')) {
                                $btn .= '<a href="' . admin_url('ohc/first-aid-location/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
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
                  dd($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
        $data = array();

        return view('ohcmanagement.master.first_aider_location.list', $data);
    }

    public function Add(Request $request)
    {

        try {
            $unit = $this->unit->getunit();
            $data = [
                'unit' => $unit
            ];
            return view('ohcmanagement.master.first_aider_location.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {
            $rules = [
                'unit_id' => 'required',
                'department_id' => 'required',
                'location_id' => 'required',
                'station_master' => 'required',
                'station_number' => 'required',

            ];
            $messages = [
              'unit_id.required'=>'Unit is required',
                'department_id.required'=>'Department is required',
                'location_id.required'=>'Location Name is required',
                'station_master.required'=>'Station Master is required',
                'station_number.required'=>'Station number is required',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {


                $firstaidlocation = $this->firstaidlocation->store();


                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/first-aid-location/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aid-location/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $firstaidlocation = $this->firstaidlocation->selectOne($id);

                $data = array(
                    'firstaidlocation' => $firstaidlocation,
                );
            }
            return view('ohcmanagement.master.first_aider_location.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);


            $firstaidlocation = $this->firstaidlocation->find($id);
            $data = array(
                'firstaidlocation' => $firstaidlocation,
            );


            return view('ohcmanagement.master.first_aider_location.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'unit_id' => 'required',
                'department_id' => 'required',
                'location_id' => 'required',
                'station_master' => 'required',
                'station_number' => 'required',

            ];
            $messages = [
              'unit_id.required'=>'Unit is required',
                'department_id.required'=>'Department is required',
                'location_id.required'=>'Location Name is required',
                'station_master.required'=>'Station Master is required',
                'station_number.required'=>'Station number is required',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                dd($validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->firstaidlocation->updates($id);


            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('ohc/first-aid-location/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aid-location/list'));
        }
    }


    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $firstaidlocation_name = $request->firstaidlocation_name;
            $id = $request->id;
            if ($id == '') {
                $record = $this->firstaidlocation->uniqueCheck($firstaidlocation_name);
            } else {
                $id = decryptId($id);
                $record = $this->firstaidlocation->ExistuniqueCheck($firstaidlocation_name, $id);
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

            $this->firstaidlocation->statuschange($id);
            // $firstaidlocation =  $this->firstaidlocation->selectOne($id);
            // $this->user->statuschange($firstaidlocation->login_id);

            return response()->json(['status' => 'success', 'msg' => 'Your Status has changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }
    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $location = $this->location->where('firstaidlocation_id', $id)->exists();
            $unit = $this->unit->where('firstaidlocation_id', $id)->exists();
            $department = $this->department->where('firstaidlocation_id', $id)->exists();

            if ($location || $unit || $department) {
                return response()->json(['status' => 'error', 'msg' => 'module_exits'], 406);
            }
            $this->firstaidlocation->deleterecord($id);
            return response()->json(['status' => 'success', 'msg' => 'firstaidlocation deleted successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    // public function Import(Request $request)
    // {
    //     $data = array();
    //     return view('ohcmanagement.master.first_aider_location.import', $data);
    // }
    // public function ImportSubmit(Request $request)
    // {
    //     try {
    //         $file = $request->file('firstaidlocation_upload');

    //         $rules = [
    //             'firstaidlocation_upload' => 'required',
    //         ];
    //         $messages = [
    //             'firstaidlocation_upload.required' => 'Please upload a file',
    //         ];

    //         $validator = Validator::make($request->all(), $rules, $messages);
    //         if ($validator->fails()) {
    //             return redirect()->back()->withErrors($validator)->withInput();
    //         }


    //         if ($file != null) {

    //             $uploadpath = 'public/uploads/firstaidlocation';

    //             $folderPath = public_path('uploads/firstaidlocation');

    //             if (!File::exists($folderPath)) {

    //                 File::makeDirectory($folderPath, 0755, true);
    //             }

    //             $filenewname = time() . Str::random('10') . '.' . $file->getClientOriginalExtension();

    //             $fileName = $file->getClientOriginalName();
    //             $fileSize = $file->getSize();

    //             $fileExt = $file->getClientOriginalExtension();

    //             $file->move($uploadpath, $filenewname);

    //             $path = $uploadpath . "/" . $filenewname;
    //             $user_id = Auth::id();

    //             $insert_data = array(
    //                 'upload_type' => 1,
    //                 'upload_status' => 0,
    //                 'file_name' => $filenewname,
    //                 'file_orgname' => $fileName,
    //                 'file_path' => $path,
    //                 'file_size' => $fileSize,
    //                 'file_extension' => $fileExt,
    //                 'created_by' => $user_id,
    //             );

    //             $insert_id =  $this->uploadlog->create($insert_data)->id;



    //             $details = [
    //                 "user_id" => $user_id,
    //                 "log_id" => $insert_id,
    //                 "path" => $path,
    //             ];

    //             // dispatch(new ImportfirstaidlocationJob($details));
    //                dispatch((new ImportfirstaidlocationJob($details))->onQueue('firstaidlocation'));
    //         }

    //         $insert_data['log_id'] = $insert_id;
    //         $insert_data['Uploded_by'] = Auth::user()->toArray();

    //         Session::flash('success', __('firstaidlocation uploaded sucessfully'));
    //         return redirect(admin_url('firstaidlocation/list'));
    //     } catch (Exception $ex) {
    //         report($ex);
    //         Session::flash('error', __('firstaidlocation upload failed'));
    //         return redirect(admin_url('firstaidlocation/list'));
    //     }
    // }
    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->firstaidlocation->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Unit',
                'Deparment',
                'Location',
                'Station Master',
                'Station Number',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  getUnitname($data->unit_id);
                $export[] =  getDepartment($data->department_id);
                $export[] =  getLocationName($data->location_id);
                $export[] =  $data->station_master;
                $export[] =  $data->station_number;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('first aid location.xlsx')
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

            $allData = $this->firstaidlocation->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Unit',
                'Deparment',
                'Location',
                'Station Master',
                'Station Number',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "first aid location Details",
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

            $view = view('ohcmanagement.master.first_aider_location.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "firstaidlocation Master.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }

    public function employeename(Request $request)
    {
        $search = $request->input('search');

        $employees = Employee::where('status', 1)
            ->where(function ($query) use ($search) {
                $query->where('emp_name', 'like', '%' . $search . '%')
                      ->orWhere('emp_id', 'like', '%' . $search . '%');
            })
            ->limit(10)
            ->get();

        return response()->json(
            $employees->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'text' => $employee->emp_name . ' - ' . $employee->emp_id,
                ];
            })
        );
    }


    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('firstaidlocation');

        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        //return Response::download($filePath, $customFileName);
        return redirect(url($filePath));
    }
}
