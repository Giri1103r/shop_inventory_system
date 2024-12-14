<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Jobs\ImportTrainingSchedulejob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\File;
use App\Models\Master\Unit;
use Illuminate\Support\Str;
use PDF;
use Mail;
use Illuminate\Support\Facades\Auth;
use Exception;
use Yajra\DataTables\DataTables;
use Response;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\Topic;
use App\Models\Master\Venue;
use App\Models\Master\TrainingSchedule;
use App\Models\Master\TrainingAttendance;
use App\Models\User;
use App\Models\Master\NominationProcess;
use App\Models\UploadLog;
use App\Mail\Training\TrainingStartedEmail;
use Illuminate\Support\Facades\Session;

class TrainingScheduleController extends Controller
{

    private $user;
    private $uploadlog;
    private $department;
    private $employee;
    private $topic;
    private $venue;
    private $training_attendance;
    private $training_schedule;
    private $unit;
    private $nomination_process;



    public function __construct()
    {

        $this->training_attendance = new TrainingAttendance();
        $this->training_schedule = new TrainingSchedule();
        $this->topic = new Topic();
        $this->employee = new Employee();
        $this->venue = new Venue();
        $this->department = new Department();
        $this->user = new User();
        $this->unit = new Unit();
        $this->uploadlog = new UploadLog();
        $this->nomination_process = new NominationProcess();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->training_schedule->list();
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
                        ->addColumn('from_date', function ($row) {
                            return Displaydatetimeformat($row->from_date);
                        })
                        ->addColumn('to_date', function ($row) {
                            return Displaydatetimeformat($row->to_date);
                        })
                        ->addColumn('created_at', function ($row) {
                            return Displaydatetimeformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            if ($row->training_status == 1) {
                                $btn .= '<a href="' . admin_url('training_schedule/nominationProcess/' . encryptId($row->id)) . '" title="Nomination">
                                            <i class="fa fa-calendar" ></i>
                                         </a> ';
                            }
                            if ($row->training_status == 3) {
                                $btn .= '<a href="' . admin_url('training_schedule/attendance/' . encryptId($row->id)) . '" title="Attendance">
                                            <i class="fas fa-portrait" style="color: black;font-size: 15px;"></i>
                                         </a> ';
                            }
                            if (CheckUserPermission('view')) {
                                $btn .= '<a href="' . admin_url('training_schedule/view/' . encryptId($row->id)) . '" title="View">
                                            <i class="fa-solid fa-eye"></i>
                                         </a> ';
                            }
                            if (in_array(ROLE_TRAINER, getUserRoleId(Auth::id())) || in_array(ROLE_SUPERADMIN, getUserRoleId(Auth::id()))) {
                                if ($row->training_status == 2) {
                                    $btn .= '<a href="' . admin_url('training_schedule/start/' . encryptId($row->id)) . '" title="Start Training">
                                                <i class="fa fa-play-circle" style="color: green;"></i>
                                             </a> ';
                                } elseif ($row->training_status == 3) {
                                    $btn .= '<a href="' . admin_url('training_schedule/end/' . encryptId($row->id)) . '" title="End Training">
                                                <i class="fa fa-stop-circle" style="color: red;"></i>
                                             </a> ';
                                }
                            }
                            if (CheckUserPermission('edit')  && $row->training_status == 2) {
                                $btn .= '<a href="' . admin_url('training_schedule/edit/' . encryptId($row->id)) . '" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                         </a> ';
                            }

                            return $btn;
                        })
                        ->rawColumns(['to_date', 'from_date', 'action', 'created_date', 'created_by', 'status'])
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
        $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
        $topicList  = $this->topic->select('id', 'topic_name')->where('status', '1')->get();
        $employeeList  = $this->employee->select('id', 'emp_name')->where('user_role', ROLE_TRAINER)->where('status', '1')->get();

        $data = array(
            'departmentList' => $departmentList,
            'unitList' => $unitList,
            'topicList' => $topicList,
            'employeeList' => $employeeList,
        );

        return view('master.training_schedule.list', $data);
    }

    public function Add(Request $request)
    {

        try {

            $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $topicList  = $this->topic->select('id', 'topic_name')->where('status', '1')->get();
            $venueList  = $this->venue->select('id', 'name_of_the_conference_hall')->where('status', '1')->get();
            $employeeList  = $this->employee->select('id', 'emp_name')->where('user_role', ROLE_TRAINER)->where('status', '1')->get();

            $data = array(
                'departmentList' => $departmentList,
                'unitList' => $unitList,
                'topicList' => $topicList,
                'venueList' => $venueList,
                'employeeList' => $employeeList,
            );
            return view('master.training_schedule.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Uniquecheck(Request $request) {


        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $topicId = $request->input('topicId');
        $trainerId = $request->input('trainerId');
        $unitId = $request->input('unitId');
        $departmentId = $request->input('departmentId');
        $venueId = $request->input('venueId');

        $data = $this->training_schedule->getUniqueSchedule($fromDate, $toDate, $topicId, $trainerId, $unitId, $departmentId, $venueId);

        return response()->json([
            'exists' => $data
        ]);
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'topic_id' => 'required',
                'trainer_id' => 'required',
                'unit_id' => 'required',
                'department_id' => 'required',
            ];

            $messages = [
                'topic_id.required' => 'Please select a training topic.',
                'trainer_id.required' => 'Please select a trainer.',
                'unit_id.required' => 'Please select a unit name.',
                'department_id.required' => 'Please select a target department.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $this->training_schedule->store();

                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('training_schedule/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('training_schedule/list'));
        }
    }


    public function startTraining($id)
    {
        try {
            $trainingScheduleId = decryptId($id);
            $training_status = 3;

            $training = $this->training_schedule->updateStatus($trainingScheduleId, $training_status);

            if ($training) {
                $nominees = $this->nomination_process->getNomination($trainingScheduleId);

                if ($nominees->isNotEmpty()) {
                    $mailsubject = 'Training Started';
                    /**
                     * Send email notification
                     */
                    foreach ($nominees as $nominee) {
                        if (!empty($nominee->email)) {
                            $nomineeArray = [
                                'emp_name' => $nominee->emp_name,
                                'from_date' => Displaydatetimeformat($nominee->from_date),
                                'to_date' => Displaydatetimeformat($nominee->to_date),
                                'topic_name' => $nominee->topic_name,
                                'venue' => $nominee->name_of_the_conference_hall,
                                'mail_subject' => $mailsubject,
                            ];

                            Mail::to($nominee->email)->queue(new TrainingStartedEmail($nomineeArray));
                        }
                    }

                    /**
                     * Send Web notification
                     */
                    $assigned_users = $nominees->pluck('employee_id')->toArray();
                    $assigned_user_ids = array_unique($assigned_users);

                    $notificationData = [
                        'notification_type' => 1,
                        'module_type' => 1,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode([
                            'title' => $mailsubject,
                            'message' => 'Training Started for ' . getTopic($nominee->topic_id) . ' by ' . getUsername(Auth::id()),
                            'icon' => 'public/assets/images/icon/permit_to_work.png',
                            'module' => 1,
                        ]),
                        'assigned_user' => implode(',', $assigned_user_ids),
                        'created_by' => Auth::id(),
                    ];

                    notificationSave($notificationData);
                }

                Session::flash('success', 'Training has been started successfully!');
            }
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong. Please try again later!');
            return redirect(admin_url('training_schedule/list'));
        }

        return redirect(admin_url('training_schedule/list'));
    }

    public function attendance($id)
    {
        try {
            $trainingScheduleId = decryptId($id);
            $training_schedule = $this->training_schedule->selectOne($trainingScheduleId);
            $nominationProcessList = $this->nomination_process->getNomination($training_schedule->id);
            $data = array(
                'training_schedule' => $training_schedule,
                'nominationProcessList' => $nominationProcessList,
            );
            return view('master.training_schedule.attendance', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }
    public function storeAttendance(Request $request)
    {
        try {
            $rules = [
                'attendance_date' => 'required',
                'attendance_status' => 'required',
            ];

            $messages = [
                'attendance_date.required' => 'Please select a training topic.',
                'attendance_status.required' => 'Please select a trainer.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $this->training_attendance->storeOrUpdate();

                Session::flash('success', 'Attendance has been saved successfully!');
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
    public function endTraining($id)
    {
        try {
            $trainingScheduleId = decryptId($id);
            $training_status = 4;

            $training = $this->training_schedule->updateStatus($trainingScheduleId, $training_status);
            Session::flash('success', 'Training has ended successfully!');
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong. Please try again later!');
            return redirect(admin_url('training_schedule/list'));
        }

        return redirect(admin_url('training_schedule/list'));
    }


    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $training_schedule = $this->training_schedule->selectOne($id);
                $nominationProcessList = $this->nomination_process->getNomination($training_schedule->id);

                $data = array(
                    'training_schedule' => $training_schedule,
                    'nominationProcessList' => $nominationProcessList,
                );
            }
            return view('master.training_schedule.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function nominationProcess(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $training_schedule = $this->training_schedule->selectOne($id);
                $training_hours = $training_schedule ? $training_schedule->calculateTrainingHours() : 0;
                $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
                $topicList  = $this->topic->select('id', 'topic_name')->where('status', '1')->get();
                $employeeList = Employee::select('id', 'emp_id', 'emp_name', 'email', 'department', 'employee_status')->where('user_role', ROLE_USER)->where('status', 1)->get();
                $nominationProcessList = $this->nomination_process->getNomination($training_schedule->id);
                $data = array(
                    'departmentList' => $departmentList,
                    'topicList' => $topicList,
                    'employeeList' => $employeeList,
                    'training_schedule' => $training_schedule,
                    'training_hours' => $training_hours,
                    'nominationProcessList' => $nominationProcessList,
                );
            }
            return view('master.training_schedule.nomination', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $topicList  = $this->topic->select('id', 'topic_name')->where('status', '1')->get();
            $venueList  = $this->venue->select('id', 'name_of_the_conference_hall')->where('status', '1')->get();
            $employeeList  = $this->employee->select('id', 'emp_name')->where('user_role', ROLE_TRAINER)->where('status', '1')->get();
            $training_schedule = $this->training_schedule->find($id);
            $data = array(
                'departmentList' => $departmentList,
                'unitList' => $unitList,
                'topicList' => $topicList,
                'employeeList' => $employeeList,
                'training_schedule' => $training_schedule,
                'venueList' => $venueList,

            );

            return view('master.training_schedule.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'topic_id' => 'required',
                'trainer_id' => 'required',
                'unit_id' => 'required',
                'department_id' => 'required',
            ];

            $messages = [
                'topic_id.required' => 'Please select a training topic.',
                'trainer_id.required' => 'Please select a trainer.',
                'unit_id.required' => 'Please select a unit name.',
                'department_id.required' => 'Please select a target department.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->training_schedule->updates($id);
            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('training_schedule/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('training_schedule/list'));
        }
    }


    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->training_schedule->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Training Schedule status changed'], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->training_schedule->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => 'Training Schedule deleted successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }
    public function Import(Request $request)
    {
        $data = array();
        return view('master.training_schedule.import', $data);
    }
    public function ImportSubmit(Request $request)
    {
        try {
            $file = $request->file('training_schedule_upload');

            $rules = [
                'training_schedule_upload' => 'required',
            ];
            $messages = [
                'training_schedule_upload.required' => 'Please upload a file',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            if ($file != null) {

                $uploadpath = 'public/uploads/training_schedule';

                $folderPath = public_path('uploads/training_schedule');

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

                dispatch(new ImportTrainingSchedulejob($details));
                //    dispatch((new ImportVenueJob($details))->onQueue('empimport'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', __('Your data has been uploaded sucessfully'));
            return redirect(admin_url('training_schedule/list'));
        } catch (Exception $ex) {

            Session::flash('error', __('Training Schedule upload failed'));
            return redirect(admin_url('training_schedule/list'));
        }
    }
    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->training_schedule->exportdata();

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

            $writer = SimpleExcelWriter::streamDownload('Training Schedule.xlsx')
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

            $allData = $this->training_schedule->exportdata();
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
                'pagetitle' => "Training Schedule",
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

            $view = view('master.training_schedule.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Training Schedule.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            dd($ex);
        }
    }

    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('training_schedule');

        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        //return Response::download($filePath, $customFileName);
        return redirect(url($filePath));
    }
}
