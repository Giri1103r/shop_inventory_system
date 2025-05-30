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
use App\Models\Master\Work;
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
    private $employee;
    private $work;



    public function __construct()
    {

        $this->firstaidlocation = new FirstAidLocation();
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
                            return Displaydateformat($row->created_at);
                        })
                        ->editColumn('department_id', function ($row) {
                            return $row->department_name;
                        })
                        ->editColumn('unit_id', function ($row) {
                            return $row->unit_name;
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })

                        ->addColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                            $btn = '<a href="' . admin_url('ohc/first-aid-location/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                            $btn .= '<a href="' . admin_url('ohc/first-aid-location/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
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
        $unit = $this->unit->getunit();
        $data = [
            'unit' => $unit
        ];

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
                'first_aid_box_no' => 'required',

            ];
            $messages = [
                'unit_id.required' => 'Unit is required',
                'department_id.required' => 'Department is required',
                'location_id.required' => 'Location Name is required',
                'station_master.required' => 'Station Master is required',
                'station_number.required' => 'Station number is required',
                'first_aid_box_no.required' => 'First Aid Box number is required',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $firstaidlocation = $this->firstaidlocation->store();
            Session::flash('success', 'Your data has been Created successfully!');
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

            $departmentList = $this->department->getdepartment();
            $firstaidlocation = $this->firstaidlocation->find($id);
            $unit = $this->unit->getunit();
            $employeeList = $this->employee->getEmployeefulldata();
            $data = array(
                'firstaidlocation' => $firstaidlocation,
                'unit' => $unit,
                'departmentList' => $departmentList,
                'employeeList' => $employeeList

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
                'first_aid_box_no' => 'required',

            ];
            $messages = [
                'unit_id.required' => 'Unit is required',
                'department_id.required' => 'Department is required',
                'location_id.required' => 'Location Name is required',
                'station_master.required' => 'Station Master is required',
                'station_number.required' => 'Station number is required',
                'first_aid_box_no.required' => 'First Aid Box number is required',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
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
            $location_id = $request->location_id;
            $id = $request->id;
            if ($id == '') {
                $record = $this->firstaidlocation->uniqueCheck($location_id);
            } else {
                $id = decryptId($id);
                $record = $this->firstaidlocation->ExistuniqueCheck($location_id, $id);
            }
            if ($record->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }

    public function StationNumberUniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $station_number = $request->station_number;
            $id = $request->id;
            if ($id == '') {
                $record = $this->firstaidlocation->stationnumberuniqueCheck($station_number);
            } else {
                $id = decryptId($id);
                $record = $this->firstaidlocation->stationnumberexistUniqueCheck($station_number, $id);
            }
            if ($record->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }



    public function FirstAidUniquecheck(Request $request)
    {
        if ($request->ajax()) {

            $unit_id = decryptId($request->unit_id);
            $department = decryptId($request->department_id);
            $first_aid_box_no = $request->first_aid_box_no;
            $id = $request->id;
            if ($id == '') {


                $record = $this->firstaidlocation->firstaidboxuniqueCheck($first_aid_box_no,  $department,  $unit_id);
            } else {

                // dd('sdcgsed');
                $id = decryptId($id);
                $record = $this->firstaidlocation->firstaidboxexistUniqueCheck($first_aid_box_no,  $department,  $unit_id, $id);
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


            return response()->json(['status' => 'success', 'msg' => 'Your Status has changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }



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
                'First Aid Box Number',
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
                $export[] = $data->location_id;
                $export[] =  $data->station_master;
                $export[] =  $data->station_number;
                $export[] =  $data->first_aid_box_no;
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
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aid-location/list'));
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
                'First Aid Box Number',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "First Aid Location Details",
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

            $filename = "first aider location .pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aid-location/list'));
        }
    }

    public function employeename(Request $request)
    {
        $name = $request->input('search');

        $employee_code = $this->employee->where('emp_id', 'like', '%' . $name . '%')
            ->orwhere('emp_name', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->limit(10)
            ->get();

        $work = $this->work->where('emp_name', 'like', '%' . $name . '%')
            ->orwhere('emp_id', 'like', '%' . $name . '%')
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


    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('firstaidlocation');

        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        //return Response::download($filePath, $customFileName);
        return redirect(url($filePath));
    }
}
