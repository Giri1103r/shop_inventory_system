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
use App\Models\Master\TrainingSchedule;
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
    private $training_schedule;
    private $nomination_process;


    public function __construct()
    {
        $this->training_schedule = new TrainingSchedule();
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
                 $this->nomination_process->storeOrUpdate();

                 $trainingScheduleId = decryptId($request->training_schedule_id);

                if( $trainingScheduleId){
                    $training_status = 2 ;
                    $this->training_schedule->updateStatus($trainingScheduleId,$training_status);
                }
                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('training_schedule/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('training_schedule/list'));
        }
    }
    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $nomination_process = $this->nomination_process->selectOne($id);
                
                $data = array(
                    'nomination_process' => $nomination_process,
                );
            }
            return view('master.training.nomination_process.view', $data);
        } catch (Exception $ex) {
            dd($ex);
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
    public function delete($id)
    {
        try {
            $nominationProcess = $this->nomination_process->findOrFail($id);
            $update_data = array(
                'status' => 0,
                'trash' => 'YES',
            );

            $nominationProcess->update($update_data);

            return response()->json(['status' => 'success', 'msg' => 'Your data has been deleted successfully'], 200);
        } catch (Exception $ex) {
            dd($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }
    public function Import(Request $request,$training_schedule_id)
    {
        $decryptedId = decryptId($training_schedule_id);

        $data = [
            'training_schedule_id' => $decryptedId,
        ];
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
                    "trainingScheduleIid" => $request->training_schedule_id,
                    "path" => $path,
                ];

                // dispatch(new ImporNominationProcessJob($details));
                   dispatch((new ImporNominationProcessJob($details))->onQueue('nomination_process'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', __('Your data has been uploaded sucessfully'));
            return redirect(admin_url('training_schedule/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', __('Nomination Process upload failed'));
            return redirect(admin_url('training_schedule/list'));
        }
    }
    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->nomination_process->exportdata();

            $header = [
                __("common.sno"),
                'Employee ID',
                'Employee Name',
                'Email ID',
                'Department',
                'Employee Type',
                'Last training attended on (Date)',
                'Last Training Attended on (Topic)',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->emp_id;
                $export[] =  $data->emp_name;
                $export[] =  $data->email;
                $export[] =  $data->department_name;
                $export[] =  $data->employee_type;
                $export[] =  displayDateformat($data->last_training_attended_on);
                $export[] =  $data->topic_name;
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
                'Employee ID',
                'Employee Name',
                'Email ID',
                'Department',
                'Employee Type',
                'Last training attended on (Date)',
                'Last Training Attended on (Topic)',
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

            $view = view('master.training.nomination_process.pdf', $data);
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
