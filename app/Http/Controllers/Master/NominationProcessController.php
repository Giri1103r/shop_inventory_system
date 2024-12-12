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
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\Topic;
use App\Models\Master\NominationProcess;
use App\Models\User;
use App\Models\UploadLog;
use App\Jobs\ImporNominationProcessJob;


class NominationProcessController extends Controller
{

    private $user;
    private $uploadlog;
    private $department;
    private $employee;
    private $topic;
    private $nomination_process;


    public function __construct()
    {

        $this->topic = new Topic();
        $this->employee = new Employee();
        $this->nomination_process = new NominationProcess();
        $this->department = new Department();
        $this->user = new User();
        $this->uploadlog = new UploadLog();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->nomination_process->list();
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

                        ->addColumn('last_training_attended_on', function ($row) {
                            return Displaydateformat($row->last_training_attended_on);
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
                                $btn = '<a href="' . admin_url('nomination_process/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            if (CheckUserPermission('edit')) {
                                $btn .= '<a href="' . admin_url('nomination_process/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            }
                            return $btn;
                        })
                        ->rawColumns(['last_training_attended_on', 'action', 'created_date', 'created_by', 'status'])
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
        $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
        $topicList  = $this->topic->select('id', 'topic_name')->where('status', '1')->get();
        $employeeList  = $this->employee->select('id', 'emp_id')->where('user_role', ROLE_USER)->where('status', '1')->get();
        $data = array(
            'departmentList' => $departmentList,
            'topicList' => $topicList,
            'employeeList' => $employeeList,
        );

        return view('master.training.nomination_process.list', $data);
    }

    public function Add(Request $request)
    {

        try {

            $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
            $topicList  = $this->topic->select('id', 'topic_name')->where('status', '1')->get();
            $employeeList = Employee::select('id', 'emp_id', 'emp_name', 'email', 'department', 'employee_status')->where('user_role', ROLE_USER)->where('status', 1)->get();

            $data = array(
                'departmentList' => $departmentList,
                'topicList' => $topicList,
                'employeeList' => $employeeList,
            );
            return view('master.training.nomination_process.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }
    public function fetchEmployeeDetails($emp_id)
    {
        $employee = Employee::select('emp_name', 'email', 'department', 'employee_status')
            ->where('id', $emp_id)
            ->first();

        $departments = $this->department->select('id', 'department_name')->where('status', '1')->get();

        return response()->json([
            'employee' => $employee,
            'departments' => $departments
        ]);
    }

    public function Store(Request $request)
    {
        try {
            $rules = [
                'employee.*.emp_id' => 'required',
                // 'employee.*.last_training_attended_on' => 'required',
                'employee.*.topic_id' => 'required',
                'employee.*.department_id' => 'required',
            ];

            $messages = [
                'employee.*.emp_id.required' => 'Please select an Employee ID.',
                // 'employee.*.last_training_attended_on.required' => 'Please select the last training attended date.',
                'employee.*.topic_id.required' => 'Please select a topic.',
                'employee.*.department_id.required' => 'Please select a department.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            try {
                $id = decryptId($request->id);
                $this->nomination_process->storeOrUpdate($id);
                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('nomination_process/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('nomination_process/list'));
        }
    }
    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $nomination_process = $this->nomination_process->selectOne($id);

                // Calculate training hours
                $training_hours = $nomination_process ? $nomination_process->calculateTrainingHours() : 0;

                $data = array(
                    'nomination_process' => $nomination_process,
                    'training_hours' => $training_hours,
                );
            }
            return view('master.training.nomination_process.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
            $topicList  = $this->topic->select('id', 'topic_name')->where('status', '1')->get();
            $employeeList = Employee::select('id', 'emp_id', 'emp_name', 'email', 'department', 'employee_status')
                ->where('user_role', ROLE_USER)
                ->where('status', 1)
                ->get();

            $nominationProcessList = $this->nomination_process->where('id', $id)->get();

            $data = [
                'departmentList' => $departmentList,
                'topicList' => $topicList,
                'employeeList' => $employeeList,
                'nominationProcessList' => $nominationProcessList,
            ];

            return view('master.training.nomination_process.edit', $data);
        } catch (Exception $error) {
            return back()->withErrors(['error' => $error->getMessage()]);
        }
    }


    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'employee.*.emp_id' => 'required',
                // 'employee.*.last_training_attended_on' => 'required',
                'employee.*.topic_id' => 'required',
                'employee.*.department_id' => 'required',
            ];

            $messages = [
                'employee.*.emp_id.required' => 'Please select an Employee ID.',
                // 'employee.*.last_training_attended_on.required' => 'Please select the last training attended date.',
                'employee.*.topic_id.required' => 'Please select a topic.',
                'employee.*.department_id.required' => 'Please select a department.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->nomination_process->updates($id);
            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('nomination_process/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('nomination_process/list'));
        }
    }


    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->nomination_process->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Nomination Process status changed'], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->nomination_process->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => 'Nomination Process deleted successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }
    public function Import(Request $request)
    {
        $data = array();
        return view('master.training.nomination_process.import', $data);
    }
    public function ImportSubmit(Request $request)
    {
        try {
            $file = $request->file('nomination_process_upload');

            $rules = [
                'nomination_process_upload' => 'required',
            ];
            $messages = [
                'nomination_process_upload.required' => 'Please upload a file',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            if ($file != null) {

                $uploadpath = 'public/uploads/nomination_process';

                $folderPath = public_path('uploads/nomination_process');

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
                    'upload_type' => 9,
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

                dispatch(new ImporNominationProcessJob($details));
                //    dispatch((new ImporNominationProcessJob($details))->onQueue('nomination_process'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', __('Your data has been uploaded sucessfully'));
            return redirect(admin_url('nomination_process/list'));
        } catch (Exception $ex) {

            Session::flash('error', __('Nomination Process upload failed'));
            return redirect(admin_url('nomination_process/list'));
        }
    }
    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->nomination_process->exportdata();

            $header = [
                __("common.sno"),
                'From Date',
                'To Date',
                'Training Topic',
                'Trainer',
                'Unit',
                'Department',
                'Target Trainees',
                'Venue/Location',
                'Training Man Hours',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  Displaydatetimeformat($data->from_date);
                $export[] =  Displaydatetimeformat($data->to_date);
                $export[] =  $data->topic_name;
                $export[] =  $data->emp_name;
                $export[] =  $data->unit_name;
                $export[] =  $data->department_name;
                $export[] =  $data->target_trainees;
                $export[] =  $data->name_of_the_conference_hall;
                $export[] =  $data->training_man_hours;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Nomination Process.xlsx')
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

            $allData = $this->nomination_process->exportdata();
            $header = [
                __("common.sno"),
                'From Date',
                'To Date',
                'Training Topic',
                'Trainer',
                'Unit',
                'Department',
                'Target Trainees',
                'Venue/Location',
                'Training Man Hours',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Nomination Process",
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

            $view = view('master.nomination_process.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Nomination Process.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            dd($ex);
        }
    }

    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('nomination_process');
        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        //return Response::download($filePath, $customFileName);
        return redirect(url($filePath));
    }
}
