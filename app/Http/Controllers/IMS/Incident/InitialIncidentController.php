<?php

namespace App\Http\Controllers\IMS\Incident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\Master\Unit;
use App\Models\Master\Location;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\User;
use App\Models\UploadLog;
use App\Jobs\ImportvendorJob;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\IMS\Incident\InitialIncident;
use App\Models\IMS\Incident\IntialIncidentEvidencefile;
use App\Models\IMS\Master\IncidentType;

class InitialIncidentController extends Controller
{

    private $initialincident;
    private $initialincidentevidence;
    private $unit;
    private $location;
    private $inctype;
    private $employee;
    private $user;
    private $uploadlog;
    private $department;



    public function __construct()
    {

        $this->initialincident = new InitialIncident();
        $this->initialincidentevidence = new IntialIncidentEvidencefile();
        $this->inctype = new IncidentType();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->employee = new Employee();
        $this->department = new Department();
        $this->user = new User();
        $this->uploadlog = new UploadLog();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->initialincident->list();


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

                       
                        ->editColumn('status_batch', function ($row) {
                           
                            return "<span class='" . $row->bg_color . "' >" . $row->status_name . "</span>";
                        })
                        ->addColumn('unit_name', function ($row) {
                            return getUnitname($row->unit_id);
                        })
                        ->addColumn('created_at', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                            $btn = '<a href="' . admin_url('incident/initial-incident/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                            $btn .= '<a href="' . admin_url('incident/initial-incident/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            // }
                            if ($row->incident_status == 1) {
                                $btn .= '<a href="' . admin_url('incident/initial-incident/review/' . encryptId($row->id)) . '" class=" " title="Review"><i class="fa-solid fa-circle-check" style="color:rgb(0, 37, 132);"></i> ';
                            }
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status','status_batch'])
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

        return view('ims.initial.incident.list', $data);
    }

    public function Add(Request $request)
    {

        try {
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $locationList  = $this->location->select('id', 'location_name')->where('status', '1')->get();
            $incTypeList  = $this->inctype->select('id', 'incident_type_name')->where('status', '1')->get();
            $data = array(
                'unitList' => $unitList,
                'locationList' => $locationList,
                'incTypeList' => $incTypeList,
            );
            return view('ims.initial.incident.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }
    public function employeename(Request $request)
    {
        $name = $request->input('search');

        $employees = Employee::where('emp_name', 'like', '%' . $name . '%')
            ->orWhere('emp_id', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->limit(10)
            ->get();


        return response()->json(
            $employees->map(function ($employee) {
                return [
                    'id' => encryptId($employee->id),
                    'text' => $employee->emp_name . ' - ' . $employee->emp_id,
                ];
            })
        );
    }

    public function fetchEmployeeDetails($emp_id)
    {
        $emp_id = decryptId($emp_id);
        $employee = Employee::select('emp_name', 'emp_id', 'email', 'department', 'designation')
            ->where('id', $emp_id)
            ->first();
        if ($employee) {
            return response()->json([
                'employee' => $employee,
                'departments' => $this->department->select('id', 'department_name')->where('status', '1')->get()
            ]);
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [

                'incident_date_time' => 'required',
                'unit_id' => 'required',
                'shift' => 'required',
                'location_id' => 'required',
                'exact_location' => 'required',
                'iir_type' => 'required',
                'reported_name' => 'required',
                'designation' => 'required',
                'department' => 'required',
                'employee_code' => 'required',
                'time_of_reporting' => 'required',
                'reporting_media' => 'required',
                'brief_description' => 'required',
            ];
            $messages = [

                'incident_date_time.required' => 'Please enter Date and Time',
                'unit_id.required' => 'Please enter Unit',
                'shift.required' => 'Please enter Shift',
                'location_id.required' => 'Please enter Location',
                'exact_location.required' => 'Please enter Exact Location',
                'iir_type.required' => 'Please enter IIR Type',
                'reported_name.required' => 'Please enter Name',
                'designation.required' => 'Please enter Designation',
                'department.required' => 'Please enter Department',
                'employee_code.required' => 'Please enter Employee Code',
                'time_of_reporting.required' => 'Please enter Time of reporting',
                'reporting_media.required' => 'Please enter Reporting Media',
                'brief_description.required' => 'Please enter Brief Description',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {


                $initialincident =   $this->initialincident->store();
                $this->initialincidentevidence->store($initialincident);


                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {

                dd($ex);
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('incident/initial-incident/list'));
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/initial-incident/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $hira = $this->initialincident->selectOne($id);

                $data = array(
                    'hira' => $hira,
                );
            }
            return view('ims.initial.incident.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);


            $initialincident = $this->initialincident->selectOne($id);
            $initialincidentevidence = $this->initialincidentevidence->selectOne($id);

            // dd($initialincidentevidence);
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $locationList  = $this->location->select('id', 'location_name')->where('status', '1')->get();
            $incTypeList  = $this->inctype->select('id', 'incident_type_name')->where('status', '1')->get();
            $data = array(
                'unitList' => $unitList,
                'locationList' => $locationList,
                'incTypeList' => $incTypeList,
                'initialincident' => $initialincident,
                'initialincidentevidence' => $initialincidentevidence,
            );


            return view('ims.initial.incident.edit', $data);
        } catch (Exception $error) {
            dd($error);
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [

                'incident_date_time' => 'required',
                'unit_id' => 'required',
                'shift' => 'required',
                'location_id' => 'required',
                'exact_location' => 'required',
                'iir_type' => 'required',
                'reported_name' => 'required',
                'designation' => 'required',
                'department' => 'required',
                'employee_code' => 'required',
                'time_of_reporting' => 'required',
                'reporting_media' => 'required',
                'brief_description' => 'required',
            ];
            $messages = [

                'incident_date_time.required' => 'Please enter Date and Time',
                'unit_id.required' => 'Please enter Unit',
                'shift.required' => 'Please enter Shift',
                'location_id.required' => 'Please enter Location',
                'exact_location.required' => 'Please enter Exact Location',
                'iir_type.required' => 'Please enter IIR Type',
                'reported_name.required' => 'Please enter Name',
                'designation.required' => 'Please enter Designation',
                'department.required' => 'Please enter Department',
                'employee_code.required' => 'Please enter Employee Code',
                'time_of_reporting.required' => 'Please enter Time of reporting',
                'reporting_media.required' => 'Please enter Reporting Media',
                'brief_description.required' => 'Please enter Brief Description',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                dd($validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
            }


            $initialincident =   $this->initialincident->updates($id);
            $this->initialincidentevidence->updates($id);

            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('incident/initial-incident/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/initial-incident/list'));
        }
    }

    public function deleteEvidence($evidenceid)
    {
        $id = $evidenceid;
        $this->initialincidentevidence->deleterecord($evidenceid);
        return response()->json(['success' => true, 'message' => 'Evidence deleted successfully.']);
    }


    public function review(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $incident_report = $this->initialincident->selectOne($id);
                $initialincidentevidence = $this->initialincidentevidence->selectOne($id);
                // $employeeList  = $this->employee->select('id', 'emp_id', 'emp_name')->where('status', '1')->get();

                $mediaOptions = [
                    1 => 'Phone',
                    2 => 'Walkie Talkie',
                    3 => 'Extension',
                    4 => 'Others',
                ];

                // Convert stored values into readable labels
                $selectedMedia = isset($incident_report->reporting_media)
                    ? explode(',', $incident_report->reporting_media)
                    : [];

                $displayMedia = array_map(function ($media) use ($mediaOptions) {
                    return $mediaOptions[$media] ?? $media; // Default to the number if not found
                }, $selectedMedia);

                $data = array(
                    // 'employeeList' => $employeeList,
                    'incident_report' => $incident_report,
                    'displayMedia' => $displayMedia,
                    'initialincidentevidence' => $initialincidentevidence,
                );
            }
            return view('ims.initial.incident.review', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $vendor_name = $request->vendor_name;
            $license_no = $request->license_no;
            $id = $request->id;

            if (empty($id)) {
                $isUnique = !$this->hira->uniqueCheck($vendor_name, $license_no);
            } else {
                $id = decryptId($id);
                $isUnique = !$this->hira->existUniqueCheck($vendor_name, $license_no, $id);
            }

            return Response::json($isUnique);
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->hira->statuschange($id);
            return response()->json(['status' => 'success', 'msg' => 'Your status has changed successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }



    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->hira->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Sr. No',
                'Source, Situation, Act,Activity, Product,Services',
                'Type of Hazard',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->sr_no;
                $export[] =  $data->services;
                if ($data->hazard_type == 1) {
                    $export[] = 'P - Physical Hazard';
                } elseif ($data->hazard_type == 2) {
                    $export[] = 'C - Chemical Hazard';
                } elseif ($data->hazard_type == 3) {
                    $export[] = 'B - Behavioral Hazard';
                } elseif ($data->hazard_type == 4) {
                    $export[] = 'O - Other Hazard';
                }
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('HIRA.xlsx')
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

            $allData = $this->hira->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Sr. No',
                'Source, Situation, Act,Activity, Product,Services',
                'Type of Hazard',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "HIRA Details",
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

            $view = view('ims.initial.incident.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "HIRA.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }

    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('vendor');

        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        //return Response::download($filePath, $customFileName);
        return redirect(url($filePath));
    }
}
