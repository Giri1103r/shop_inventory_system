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
use App\Models\Master\TrainingAssessmentFeedback;
use App\Models\Master\TrainingStatuslog;
use App\Models\Master\TrainingFeedback;
use App\Models\User;
use App\Models\Master\NominationProcess;
use App\Models\UploadLog;
use App\Mail\Training\TrainingStartedEmail;
use App\Mail\Training\TrainingFeedbackMail;
use App\Mail\Training\TrainingApprovalEmail;
use App\Mail\Training\TrainingRejectedEmail;
use App\Mail\Training\TrainingScheduledEmail;
use Illuminate\Support\Facades\Session;

class TrainingScheduleController extends Controller
{

    private $user;
    private $training_feedback;
    private $uploadlog;
    private $department;
    private $employee;
    private $topic;
    private $venue;
    private $training_attendance;
    private $training_assessment_feedback;
    private $training_schedule;
    private $unit;
    private $nomination_process;
    private $training_statuslog;



    public function __construct()
    {

        $this->training_assessment_feedback = new TrainingAssessmentFeedback();
        $this->training_statuslog = new TrainingStatuslog();
        $this->training_feedback = new TrainingFeedback();
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
                            if (Auth::user()->role == ROLE_SUPERADMIN || Auth::user()->role == ROLE_ADMIN) {
                                $text = "<span style='color:red'>In-Active<span>";
                                if ($row->status == 1) {
                                    $text = "<span style='color:green;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->id) . "' data-type = '1' >Active<span>";
                                } else if ($row->status == 0) {
                                    $text = "<span style='color:red;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->id) . "' data-type = '0' >In-Active<span>";
                                }
                                return $text;
                            } else {
                                $text = "<span style='color:red'>In-Active<span>";
                                if ($row->status == 1) {
                                    $text = "<span style='color:green;' data-id='" . encryptId($row->id) . "' data-type = '1' >Active<span>";
                                } else if ($row->status == 0) {
                                    $text = "<span style='color:red;'  data-id='" . encryptId($row->id) . "' data-type = '0' >In-Active<span>";
                                }
                                return $text;
                            }
                        })
                        ->addColumn('from_date', function ($row) {
                            return Displaydateformat($row->from_date);
                        })
                        ->addColumn('to_date', function ($row) {
                            return Displaydateformat($row->to_date);
                        })
                        ->addColumn('created_at', function ($row) {
                            return Displaydatetimeformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            // dd($row->training_status);
                            if (($row->training_status == NEW_TRAINING_SCHEDULE || $row->training_status == TRAINING_RESCHEDULE_APPROVAL) && (in_array(ROLE_EHS_HEAD, getUserRoleId(Auth::id())) || in_array(ROLE_SUPERADMIN, getUserRoleId(Auth::id())))) {
                                $btn .= '<a href="' . admin_url('training_schedule/ehs_approval/' . encryptId($row->id)) . '" title="EHS Head Approval">
                                            <i class="fa-solid fa-check-to-slot" aria-hidden="true" style="color: #000000;"></i>
                                         </a> ';
                            }

                            if (($row->training_status == VP_APPROVE || $row->training_status == TRAINING_NOMINATION_COMPLETED) && (in_array(ROLE_ADMIN, getUserRoleId(Auth::id())) || in_array(ROLE_SUPERADMIN, getUserRoleId(Auth::id())))) {
                                $btn .= '<a href="' . admin_url('training_schedule/nominationProcess/' . encryptId($row->id)) . '" title="Nomination">
                                            <i class="fa fa-calendar" style="color: #0013ff;"></i>
                                         </a> ';
                            }
                            if (in_array(ROLE_TRAINER, getUserRoleId(Auth::id())) || in_array(ROLE_SUPERADMIN, getUserRoleId(Auth::id()))) {
                                if ($row->training_status == TRAINING_NOMINATION_COMPLETED) {
                                    $btn .= '<a href="' . admin_url('training_schedule/start/' . encryptId($row->id)) . '" title="Start Training">
                                                <i class="fa fa-play-circle" style="color: green;"></i>
                                             </a> ';
                                }
                                $attendance = TrainingAttendance::select('training_schedule_id', 'attendance_date')
                                    ->where('training_schedule_id', $row->id)
                                    ->first();

                                if ($attendance) {
                                    $attendanceDate = \Carbon\Carbon::parse($attendance->attendance_date);
                                    $fromDate = \Carbon\Carbon::parse($row->from_date)->startOfDay();
                                    $toDate = \Carbon\Carbon::parse($row->to_date)->endOfDay();

                                    if ($attendanceDate->between($fromDate, $toDate)) {
                                        if ($row->training_status == TRAINING_START) {
                                            $btn .= '<a href="' . admin_url('training_schedule/end/' . encryptId($row->id)) . '" title="Training Completed">
                                                    <i class="fa fa-check-circle" style="color: #5541b0;"></i>
                                                 </a> ';
                                        }
                                    }
                                }
                            }
                            if (in_array(ROLE_TRAINER, getUserRoleId(Auth::id())) || in_array(ROLE_SUPERADMIN, getUserRoleId(Auth::id()))) {

                                $attendanceDates = TrainingAttendance::select('attendance_date')
                                    ->where('training_schedule_id', $row->id)
                                    ->pluck('attendance_date')
                                    ->map(function ($date) {
                                        return \Carbon\Carbon::parse($date)->format('Y-m-d');
                                    })
                                    ->toArray();

                                $fromDate = \Carbon\Carbon::parse($row->from_date)->format('Y-m-d');
                                $toDate = \Carbon\Carbon::parse($row->to_date)->format('Y-m-d');

                                $allDatesCovered = in_array($fromDate, $attendanceDates) && in_array($toDate, $attendanceDates);

                                if (!$allDatesCovered) {
                                    if ($row->training_status == TRAINING_START) {
                                        $btn .= '<a href="' . admin_url('training_schedule/attendance/' . encryptId($row->id)) . '" title="Attendance">
                                                    <i class="fas fa-portrait" style="color: #811378;font-size: 16px;"></i>
                                                 </a> ';
                                    }
                                }
                            }
                            if ($row->training_status == TRAINING_FEEDBACK_ADMIN_APPROVE && (in_array(ROLE_ADMIN, getUserRoleId(Auth::id())) || in_array(ROLE_SUPERADMIN, getUserRoleId(Auth::id())))) {

                                $btn .= '<a href="' . admin_url('training/feedback_approve/' . encryptId($row->id)) . '"  class="feedbackicon" title="feedback"><i class="fa-solid fa-comments" aria-hidden="true" style="color:rgb(13, 163, 244);"></i> </a> ';
                            }


                            if (($row->training_status == TRAINING_FEEDBACK_ADMIN_APPROVE || $row->training_status == TRAINING_COMPLETED) && (in_array(ROLE_TRAINER, getUserRoleId(Auth::id())) || in_array(ROLE_ADMIN, getUserRoleId(Auth::id())) || in_array(ROLE_SUPERADMIN, getUserRoleId(Auth::id())))) {
                                $btn .= '<a href="' . admin_url('training_schedule/pdf/' . encryptId($row->id)) . '"  class="pdficon" title="Pdf"><i class="fas fa-file-pdf" aria-hidden="true" style="color: #e21e23;"></i> </a> ';
                            }


                            if (CheckUserPermission('view')) {
                                $btn .= '<a href="' . admin_url('training_schedule/view/' . encryptId($row->id)) . '" title="View">
                                            <i class="fa-solid fa-eye"></i>
                                         </a> ';
                            }


                            if (CheckUserPermission('edit')  && $row->training_status == VP_REJECTED && (in_array(ROLE_TRAINER, getUserRoleId(Auth::id())) || in_array(ROLE_ADMIN, getUserRoleId(Auth::id())) || in_array(ROLE_SUPERADMIN, getUserRoleId(Auth::id())))) {
                                $btn .= '<a href="' . admin_url('training_schedule/edit/' . encryptId($row->id)) . '" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                         </a> ';
                            }
                            // if (CheckUserPermission('delete') && $row->training_status == NEW_TRAINING_SCHEDULE && (in_array(ROLE_TRAINER, getUserRoleId(Auth::id()))  || in_array(ROLE_ADMIN, getUserRoleId(Auth::id())) || in_array(ROLE_SUPERADMIN, getUserRoleId(Auth::id())))) {
                            //     $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '"  data-login_id="' . encryptId($row->login_id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            // }
                            return $btn;
                        })
                        ->rawColumns(['to_date', 'from_date', 'action', 'created_date', 'created_by', 'status']);
                    if (Auth::user()->role != ROLE_USER) {
                        $datatables->setFilteredRecords($data['filter_records'])
                            ->setTotalRecords($data['total_records']);
                    }

                    return $datatables->skipPaging()->make(true);
                } catch (Exception $ex) {
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
        $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
        $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
        $topicList  = $this->topic->select('id', 'topic_name')->where('status', '1')->get();
        $employeeList  = $this->employee->select('id', 'emp_name')->whereRaw('FIND_IN_SET(' . ROLE_TRAINER . ', user_role)')->where('status', '1')->get();

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
            $employeeList  = $this->employee->select('id', 'emp_name')->whereRaw('FIND_IN_SET(' . ROLE_TRAINER . ', user_role)')->where('status', '1')->get();

            $data = array(
                'departmentList' => $departmentList,
                'unitList' => $unitList,
                'topicList' => $topicList,
                'employeeList' => $employeeList,
            );
            return view('master.training_schedule.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Uniquecheck(Request $request)
    {
        $ids = decryptId($request->input('id'));
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $topicId = decryptId($request->input('topicId'));
        $trainerId = decryptId($request->input('trainerId'));
        $unitId = decryptId($request->input('unitId'));
        $departmentId = decryptId($request->input('departmentId'));
        $venueId = decryptId($request->input('venueId'));

        if (empty($ids)) {
            $conflicts = $this->training_schedule->getUniqueSchedule($fromDate, $toDate, $topicId, $trainerId, $unitId, $departmentId, $venueId);
        } else {
            $conflicts = $this->training_schedule->getExistUniqueSchedule($fromDate, $toDate, $topicId, $trainerId, $unitId, $departmentId, $venueId, $ids);
        }

        return response()->json(['conflicts' => $conflicts]);
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

                $training =  $this->training_schedule->store();
                $training_status = NEW_TRAINING_SCHEDULE;
                $statuslog =  $this->training_statuslog->storestatus($training->id, $training_status);
                if ($training) {
                    $trainingSchedule = $this->training_schedule->selectOne($training->id);
                    $ehs_role = ROLE_EHS_HEAD;

                    $ehs_details = User::select('id', 'role', 'name', 'employee_id', 'email')
                        ->whereRaw('FIND_IN_SET(' . $ehs_role . ', role)')
                        ->get();

                    if (!empty($trainingSchedule)) {
                        $mailsubject = 'New Training Scheduled';

                        /**
                         * Send Email Notifications
                         */
                        if ($ehs_details->isNotEmpty()) {
                            foreach ($ehs_details as $ehs_detail) {
                                if (!empty($ehs_detail->email)) { // Corrected email validation
                                    $trainingArray = [
                                        'name' => $ehs_detail->name,
                                        'from_date' => Displaydateformat($trainingSchedule->from_date),
                                        'to_date' => Displaydateformat($trainingSchedule->to_date),
                                        'start_time' => Displaytimeformat($trainingSchedule->start_time),
                                        'end_time' => Displaytimeformat($trainingSchedule->end_time),
                                        'topic_name' => $trainingSchedule->topic_name,
                                        'unit' => $trainingSchedule->unit_name,
                                        'department' => $trainingSchedule->department_name,
                                        'venue' => $trainingSchedule->name_of_the_conference_hall,
                                        'mail_subject' => $mailsubject,
                                    ];

                                    // Queue email
                                    Mail::to($ehs_detail->email)->queue(new TrainingApprovalEmail($trainingArray));
                                }
                            }
                        }

                        /**
                         * Send Web Notifications
                         */
                        $ehsids = $ehs_details->pluck('id')->toArray();
                        if (!empty($ehsids)) {
                            $img = admin_url('public/assets/icons/training.png');
                            $notificationData = [
                                'notification_type' => 2,
                               'module' => 4,
                                'notification_message' => $mailsubject,
                                'mobile_notification' => json_encode([
                                    'title' => $mailsubject,
                                    'message' => 'A new training schedule has been created by ' . getUsername($trainingSchedule->created_by),
                                    'icon' => $img,
                                    'module' => 4,
                                ]),
                                'web_link' => 'training_schedule/ehs_approval/' . encryptId($trainingSchedule->id),
                                'assigned_user' => array_to_string($ehsids),
                                'created_by' => Auth::id(),
                            ];

                            // Save notification
                            notificationSave($notificationData);
                        }
                    }
                }
                Session::flash('success', 'Your data has been created successfully');
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
            $training_status = TRAINING_START;

            $training = $this->training_schedule->updateStatus($trainingScheduleId, $training_status);
            $statuslog =  $this->training_statuslog->storestatus($trainingScheduleId, $training_status);
            if ($training) {
                $nominees = $this->nomination_process->getNomination($trainingScheduleId);

                if ($nominees->isNotEmpty()) {
                    $mailsubject = 'Training Started';
                    $assignedUsers = [];

                    /**
                     * Send email notification
                     */
                    $nomineesWithEmail = $nominees->filter(function ($nominee) {
                        return !empty($nominee->email);
                    });

                    foreach ($nomineesWithEmail  as $nominee) {
                        $nomineeArray = [
                            'emp_name' => $nominee->emp_name,
                            'from_date' => Displaydateformat($nominee->from_date),
                            'to_date' => Displaydateformat($nominee->to_date),
                            'start_time' => Displaytimeformat($nominee->start_time),
                            'end_time' => Displaytimeformat($nominee->end_time),
                            'topic_name' => $nominee->topic_name,
                            'venue' => $nominee->name_of_the_conference_hall,
                            'mail_subject' => $mailsubject,
                        ];
                        Mail::to($nominee->email)->queue(new TrainingStartedEmail($nomineeArray));
                    }

                    /**
                     * Send Web notification
                     */
                    $assignedUsers = $nominees->pluck('login_id')->filter()->toArray(); // Ensure it's not null

                    $img = admin_url('public/assets/icons/traning.png');
                    if (!empty($assignedUsers)) {
                        $notificationData = [
                            'notification_type' => 2,
                           'module' => 4,
                            'notification_message' => $mailsubject,
                            'mobile_notification' => json_encode([
                                'title' => $mailsubject,
                                'message' => 'Training on the topic ' . getTopic($nominee->topic_id) . ' has been started by ' . getUsername(Auth::id()),
                                'icon' =>  $img,
                                'module' => 4,
                            ]),
                            'web_link' => 'training_schedule/view/' . encryptId($trainingScheduleId),
                            'assigned_user' => array_to_string($assignedUsers),
                            'created_by' => Auth::id(),
                        ];

                        notificationSave($notificationData);
                    }
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
            report($ex);
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

                $attendanceDate = DBdateformat($request->attendance_date);

                $trainingScheduleId = decryptId($request->training_schedule_id);

                $trainingHrsPerDay = $this->training_schedule
                    ->where('id', $trainingScheduleId)
                    ->value('training_hrs_perday');

                $presentCount = $this->training_attendance
                    ->where('training_schedule_id', $trainingScheduleId)
                    ->where('attendance_date', $attendanceDate)
                    ->where('attendance_status', 1)
                    ->count();

                $totalManHoursForDay = $presentCount * $trainingHrsPerDay;

                $existingTrainingSchedule = $this->training_schedule
                    ->select('training_man_hours')
                    ->where('id', $trainingScheduleId)
                    ->first();

                if ($existingTrainingSchedule->training_man_hours) {
                    $currentTotalManHours = $existingTrainingSchedule->training_man_hours;
                    $newTotalManHours = $currentTotalManHours + $totalManHoursForDay;
                } else {
                    $newTotalManHours = $totalManHoursForDay;
                }
                $this->training_schedule->updateTrainingManHours($trainingScheduleId, $newTotalManHours);

                Session::flash('success', 'Attendance has been saved successfully!');
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

    public function checkUniqueAttendanceDate(Request $request)
    {
        $attendanceExists = $this->training_attendance->where('training_schedule_id', decryptId($request->training_schedule_id))
            ->whereDate('attendance_date', DBdateformat($request->attendance_date))
            ->exists();

        if ($attendanceExists) {
            return response()->json(false);
        }

        return response()->json(true);
    }

    public function checkUniqueNomination(Request $request)
    {
        $empId = $request->input('emp_id');
        $trainingScheduleId = decryptId($request->training_schedule_id);

        $exists =  $this->nomination_process
            ->where('training_schedule_id', $trainingScheduleId)
            ->where('employee_id', $empId)
            ->exists();

        if ($exists) {
            return response()->json(false);
        }

        return response()->json(true);
    }
    public function feedbackLinkPage($id, $trainingScheduleId)
    {
        try {
            $feedback_id = decryptId($id);
            $training_schedule_id = decryptId($trainingScheduleId);

            $trainingAssessmentFeedback = $this->training_assessment_feedback->getempId($feedback_id);
            $training_schedule = $this->training_schedule->selectOne($training_schedule_id);
            $exists = $this->training_feedback->where('training_assessment_feedback_id', $feedback_id)->exists();
            // dd($exists);
            if (!$exists) {
                $data = [
                    'feedback_id' => $feedback_id,
                    'training_schedule' => $training_schedule,
                    'trainingAssessmentFeedback' => $trainingAssessmentFeedback,
                ];
                return view('master.training_schedule.feedbacklink', $data);
            } else {
                if (Auth::user()->role != ROLE_TRAINER  &&  Auth::user()->role != ROLE_SUPERADMIN &&  Auth::user()->role != ROLE_ADMIN) {
                    $userAttendanceList = $this->training_attendance
                        ->where('status', 1)
                        ->where('emp_id', Auth::user()->employee_id)
                        ->where('training_schedule_id', $training_schedule_id)
                        ->get();

                    $data = [
                        'training_schedule' => $training_schedule,
                        'userAttendanceList' => $userAttendanceList,
                    ];
                }
                return view('master.training_schedule.view', $data);
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(['error' => 'Something went wrong. Please try again later!']);
        }
    }

    public function feedbackLinkSubmit(Request $request)
    {
        try {
            $rules = [
                'trainer_feedback' => 'required',
                'training_feedback' => 'required',
            ];

            $messages = [
                'trainer_feedback.required' => 'Please provide feedback about the trainer.',
                'training_feedback.required' => 'Please provide feedback about the training.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $this->training_feedback->store();

            return redirect(admin_url('training_schedule/list'))->with('success', 'Your feedback has been submitted successfully.');
        } catch (Exception $ex) {
            dd($ex);
            return redirect(admin_url('training_schedule/list'))->withErrors(['error' => 'Something went wrong. Please try again later!']);
        }
    }
    public function workerFeedbackLinkPage($trainingScheduleId)
    {
        try {
            $training_schedule_id = decryptId($trainingScheduleId);

            $empIds = $this->training_assessment_feedback->getWorkers($training_schedule_id);

            $training_schedule = $this->training_schedule->selectOne($training_schedule_id);


            $data = [
                'training_schedule' => $training_schedule,
                'empIds' => $empIds,
            ];
            return view('master.training_schedule.worker_feedbacklink', $data);
        } catch (Exception $ex) {
            dd($ex);
            return redirect()->back()->withErrors(['error' => 'Something went wrong. Please try again later!']);
        }
    }
    public function checkUniqueworkerId(Request $request)
    {
        $workerIdExists = $this->training_feedback
            ->where('training_schedule_id', decryptId($request->training_schedule_id))
            ->where('emp_id', $request->emp_id)
            ->exists();

        return response()->json(!$workerIdExists);
    }

    public function workerFeedbackLinkSubmit(Request $request)
    {
        try {
            $rules = [
                'emp_id' => 'required',
                'trainer_feedback' => 'required',
                'training_feedback' => 'required',
            ];

            $messages = [
                'emp_id.required' => 'Select Worker Id.',
                'trainer_feedback.required' => 'Please provide feedback about the trainer.',
                'training_feedback.required' => 'Please provide feedback about the training.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->training_feedback->store();

            return redirect(admin_url('training_schedule/list'))->with('success', 'Your feedback has been submitted successfully.');
        } catch (Exception $ex) {
            report($ex);
            return redirect(admin_url('training_schedule/list'))->withErrors(['error' => 'Something went wrong. Please try again later!']);
        }
    }

    public function adminApprove($id)
    {
        try {
            $trainingScheduleId = decryptId($id);
            $training_schedule = $this->training_schedule->selectOne($trainingScheduleId);
            $trainingAssessmentList = $this->training_assessment_feedback->getAssessment($trainingScheduleId);


            $data = [
                'training_schedule' => $training_schedule,
                'trainingAssessmentList' => $trainingAssessmentList,
            ];

            return view('master.training_schedule.adminapprove', $data);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(['error' => 'Something went wrong. Please try again later!']);
        }
    }

    public function adminfeedbackApprove(Request $request)
    {
        try {
            $trainingScheduleId = decryptId($request->training_schedule_id);

            $training = $this->training_assessment_feedback->updateStatus($trainingScheduleId);
            $training_status = TRAINING_COMPLETED;

            $this->training_schedule->updateStatus($trainingScheduleId, $training_status);
            $statuslog =  $this->training_statuslog->storestatus($trainingScheduleId, $training_status);

            if ($training->feedback_send_status == 1) {
                $trainingAttendEmp = $this->training_assessment_feedback->getemployee($trainingScheduleId);

                if ($trainingAttendEmp->isNotEmpty()) {
                    $mailSubject = 'Training Feedback';
                    $img = admin_url('public/assets/icons/traning.png');

                    foreach ($trainingAttendEmp as $emp) {
                        $id = encryptId($emp->id);
                        $scheduleIdEncrypted = encryptId($trainingScheduleId);
                        $feedbackLink = url("training/feedback_link/$id/$scheduleIdEncrypted");

                        if (!empty($emp->email)) {
                            $nomineeArray = [
                                'emp_name' => $emp->emp_name,
                                'link' => $feedbackLink,
                                'mail_subject' => $mailSubject,
                            ];
                            Mail::to($emp->email)->queue(new TrainingFeedbackMail($nomineeArray));
                        }

                        $empIds = $emp->login_id;
                        if (!empty($empIds)) {
                            // Prepare a single notification
                            $notificationData = [
                                'notification_type' => 2,
                               'module' => 4,
                                'notification_message' => $mailSubject,
                                'mobile_notification' => json_encode([
                                    'title' => $mailSubject,
                                    'message' => $mailSubject,
                                    'icon' => $img,
                                    'module' => 4,
                                ]),
                                'web_link' => $feedbackLink,
                                'assigned_user' => $empIds,
                                'created_by' => Auth::id(),
                            ];

                            notificationSave($notificationData); // Save one notification at a time
                        }
                    }
                }

                Session::flash('success', 'Feedback links sent successfully to attendees!');
            }

            return redirect(admin_url('training_schedule/list'));
        } catch (\Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong. Please try again later!');
            return redirect()->back();
        }
    }

    public function endTraining($id)
    {
        try {
            $trainingScheduleId = decryptId($id);

            $training_schedule = $this->training_schedule->selectOne($trainingScheduleId);
            $nominationProcessList = $this->nomination_process->getNomination($trainingScheduleId);
            $trainingAttendanceList = $this->training_attendance
                ->where('status', 1)
                ->where('training_schedule_id', $trainingScheduleId)
                ->get();

            // Normalize Employee Names
            $attendedEmployees = $trainingAttendanceList
                ->where('attendance_status', 1)
                ->pluck('emp_name')
                ->map(fn($name) => strtolower(trim($name)))
                ->unique();

            $allEmployees = $trainingAttendanceList
                ->pluck('emp_name')
                ->map(fn($name) => strtolower(trim($name)))
                ->unique();

            $nonAttendedEmployees = $allEmployees->diff($attendedEmployees);

            $data = [
                'training_schedule' => $training_schedule,
                'nominationProcessList' => $nominationProcessList,
                'trainingAttendanceList' => $trainingAttendanceList,
                'attendedEmployees' => $attendedEmployees,
                'nonAttendedEmployees' => $nonAttendedEmployees,
            ];

            return view('master.training_schedule.endtraining', $data);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(['error' => 'Something went wrong. Please try again later!']);
        }
    }


    public function endTrainingStore(Request $request)
    {
        try {
            $trainingScheduleId = decryptId($request->training_schedule_id);
            $training_status = TRAINING_FEEDBACK_ADMIN_APPROVE;

            $this->training_assessment_feedback->store();
            $this->training_schedule->updateStatus($trainingScheduleId, $training_status);
            $statuslog =  $this->training_statuslog->storestatus($trainingScheduleId, $training_status);
            Session::flash('success', 'Training has ended successfully!');
            return redirect(admin_url('training_schedule/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong. Please try again later!');
            return redirect()->back();
        }
    }
    public function View(Request $request)
    {
        try {

            $id = decryptId($request->id);
            if (Auth::check()) {
                $training_schedule = $this->training_schedule->selectOne($id);
                $nominationProcessList = $this->nomination_process->getNomination($training_schedule->id);
                $trainingAssessmentList = $this->training_assessment_feedback->getAssessment($training_schedule->id);
                $rejectedlog = $this->training_statuslog->where('training_schedule_id', $training_schedule->id)->where('training_status', 3)->get();
                $trainingFeedbackList = collect();

                foreach ($trainingAssessmentList as $assessment) {
                    $feedbackList = $this->training_feedback->getfeedbackList($assessment->id);
                    $trainingFeedbackList = $trainingFeedbackList->merge($feedbackList);
                }

                $attendanceDate = $request->attendance_date;

                $trainingAttendanceList = $this->training_attendance
                    ->where('status', 1)
                    ->where('training_schedule_id', $training_schedule->id)
                    ->when($attendanceDate, function ($query, $attendanceDate) {
                        return $query->whereDate('attendance_date', DBdateformat($attendanceDate));
                    })
                    ->get();

                $workerIds = $this->training_assessment_feedback->getWorkers($training_schedule->id)->pluck('emp_id')
                ->toArray();;


                // Check how many workers have given feedback
                $workerFeedbackCount = $this->training_feedback
                    ->whereIn('emp_id', $workerIds)
                    ->where('training_feedback.status', 1)
                    ->count();

                // If all workers gave feedback, hide button
                $showWorkerFeedbackButton = count($workerIds) > 0 && count($workerIds) > $workerFeedbackCount;


                $data = [
                    'training_schedule' => $training_schedule,
                    'nominationProcessList' => $nominationProcessList,
                    'trainingAttendanceList' => $trainingAttendanceList,
                    'trainingAssessmentList' => $trainingAssessmentList,
                    'trainingFeedbackList' => $trainingFeedbackList,
                    'rejectedlog' => $rejectedlog,
                    'showWorkerFeedbackButton' => $showWorkerFeedbackButton,
                ];

                if (Auth::user()->role != ROLE_TRAINER  &&  Auth::user()->role != ROLE_SUPERADMIN &&  Auth::user()->role != ROLE_ADMIN &&  Auth::user()->role != ROLE_EHS_HEAD) {

                    $trainingAssessmentList = $this->training_assessment_feedback->getAssessmentByEmp($training_schedule->id);
                    foreach ($trainingAssessmentList as $assessment) {
                        $feedbackList = $this->training_feedback->getfeedbackList($assessment->id);
                        $trainingFeedbackList = $trainingFeedbackList->merge($feedbackList);
                    }
                    $data = [
                        'training_schedule' => $training_schedule,
                        'trainingAssessmentList' => $trainingAssessmentList,
                    ];
                }
            }
            return view('master.training_schedule.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong. Please try again later!');
            return redirect()->back();
        }
    }
    public function filterAttendance(Request $request)
    {
        try {
            $trainingScheduleId = decryptId($request->training_schedule_id);
            $attendanceDate = $request->attendance_date;

            $trainingAttendanceList = $this->training_attendance
                ->where('status', 1)
                ->where('training_schedule_id', $trainingScheduleId)
                ->when($attendanceDate, function ($query, $attendanceDate) {
                    return $query->whereDate('attendance_date', DBdateformat($attendanceDate));
                })
                ->get();

            $html = view('master.training_schedule.training_attendance_table', compact('trainingAttendanceList'))->render();

            return response()->json(['html' => $html]);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }

    public function vpApproval(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $training_schedule = $this->training_schedule->selectOne($id);
                $approveexists = $this->training_schedule
                    ->where('id', $id)
                    ->where(function ($query) {
                        $query->where('training_status', 1)
                            ->orWhere('training_status', 4);
                    })
                    ->first();
                // $ehs_detail = $this->user->select('id', 'role', 'name', 'employee_id', 'email')->whereRaw("FIND_IN_SET(6, role) > 0")->first();

                // if (empty($ehs_detail) || empty($ehs_detail->name)) {
                //     $ehs_detail = $this->user
                //         ->select('id', 'role', 'name', 'employee_id', 'email')
                //         ->where('role', 1)
                //         ->first();
                // }

                if ($approveexists) {
                    $data = array(
                        'training_schedule' => $training_schedule,
                        // 'ehs_detail' => $ehs_detail,
                    );
                    return view('master.training_schedule.vpapproval', $data);
                } else {
                    $rejectedlog = $this->training_statuslog->where('training_schedule_id', $id)->where('training_status', 3)->get();
                    $data = [
                        'training_schedule' => $training_schedule,
                        'rejectedlog' => $rejectedlog,
                    ];

                    return view('master.training_schedule.vpapprovalview', $data);
                }
            }
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function vpApprovalStore(Request $request)
    {
        try {

            $rules = [
                'date' => 'required',
                'remark' => 'required',
            ];

            $messages = [
                'date.required' => 'Please select a date.',
                'remark.required' => 'Please provide a remark.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                $id = decryptId($request->id);

                if ($request->action == "approve") {
                    $training_status = VP_APPROVE;
                    $training =  $this->training_schedule->vpApproval($id, $training_status);
                    $statuslog =  $this->training_statuslog->storestatus($id, $training_status);

                    if ($training) {
                        $trainingSchedule = $this->training_schedule->selectOne($id);
                        if (!empty($trainingSchedule)) {
                            $mailsubject = 'New Training Scheduled';

                            /**
                             * Send email notification
                             */
                            if (!empty($trainingSchedule->email)) {
                                $trainingArray = [
                                    'emp_name' => $trainingSchedule->emp_name,
                                    'from_date' => Displaydateformat($trainingSchedule->from_date),
                                    'to_date' => Displaydateformat($trainingSchedule->to_date),
                                    'start_time' => Displaytimeformat($trainingSchedule->start_time),
                                    'end_time' => Displaytimeformat($trainingSchedule->end_time),
                                    'topic_name' => $trainingSchedule->topic_name,
                                    'unit' => $trainingSchedule->unit_name,
                                    'department' => $trainingSchedule->department_name,
                                    'venue' => $trainingSchedule->name_of_the_conference_hall,
                                    'mail_subject' => $mailsubject,
                                ];

                                Mail::to($trainingSchedule->email)->queue(new TrainingScheduledEmail($trainingArray));
                            }


                            /**
                             * Send Web notification
                             */
                            $assigned_users = $trainingSchedule->login_id;
                            $img = admin_url('public/assets/icons/traning.png');
                            if (!empty($assigned_users)) {
                                $notificationData = [
                                    'notification_type' => 2,
                                   'module' => 4,
                                    'notification_message' => $mailsubject,
                                    'mobile_notification' => json_encode([
                                        'title' => $mailsubject,
                                        'message' => 'A new training schedule has been created by ' . getUsername($trainingSchedule->created_by),
                                        'icon' =>  $img,
                                        'module' => 4,
                                    ]),
                                    'web_link' => 'training_schedule/view/' . encryptId($trainingSchedule->id),
                                    'assigned_user' => $assigned_users,
                                    'created_by' => Auth::id(),
                                ];

                                notificationSave($notificationData);
                            }
                        }
                    }
                } elseif ($request->action == "reject") {
                    $training_status = VP_REJECTED;
                    $training =  $this->training_schedule->vpApproval($id, $training_status);
                    $statuslog =  $this->training_statuslog->storestatus($id, $training_status);
                    if ($training) {
                        $trainingSchedule = $this->training_schedule->selectOne($id);

                        if (!empty($trainingSchedule)) {
                            $admin_role = ROLE_ADMIN;

                            $adminDetails = User::select('email', 'name', 'id')
                                ->whereRaw('FIND_IN_SET(' . $admin_role . ', role)')
                                ->get();

                            if ($adminDetails->isNotEmpty()) {
                                $mailsubject = 'Training Rejected by EHS Head';

                                /**
                                 * Send Email Notification
                                 */
                                foreach ($adminDetails as $admin) {
                                    if (!empty($admin->email)) {
                                        $trainingArray = [
                                            'name' => $admin->name,
                                            'from_date' => Displaydateformat($trainingSchedule->from_date),
                                            'to_date' => Displaydateformat($trainingSchedule->to_date),
                                            'start_time' => Displaytimeformat($trainingSchedule->start_time),
                                            'end_time' => Displaytimeformat($trainingSchedule->end_time),
                                            'topic_name' => $trainingSchedule->topic_name,
                                            'unit' => $trainingSchedule->unit_name,
                                            'department' => $trainingSchedule->department_name,
                                            'venue' => $trainingSchedule->name_of_the_conference_hall,
                                            'remark' => $trainingSchedule->remark,
                                            'mail_subject' => $mailsubject,
                                        ];

                                        Mail::to($admin->email)->queue(new TrainingRejectedEmail($trainingArray));
                                    }
                                }

                                /**
                                 * Send Web Notification
                                 */
                                $adminIds = $adminDetails->pluck('id')->toArray();

                                if (!empty($adminIds)) {
                                    $img = admin_url('public/assets/icons/training.png');
                                    $notificationData = [
                                        'notification_type' => 2,
                                       'module' => 4,
                                        'notification_message' => $mailsubject,
                                        'mobile_notification' => json_encode([
                                            'title' => $mailsubject,
                                            'message' => 'Training rejected by EHS Head ' . getUsername(Auth::id()),
                                            'icon' => $img,
                                            'module' => 4,
                                        ]),
                                        'web_link' => 'training_schedule/view/' . encryptId($trainingSchedule->id),
                                        'assigned_user' => array_to_string($adminIds),
                                        'created_by' => Auth::id(),
                                    ];

                                    // Save the notification
                                    notificationSave($notificationData);
                                }
                            }
                        }
                    }
                }
                Session::flash('success', $request->action == "approve"
                    ? 'Your data has been Approved successfully'
                    : 'Your data has been Rejected');
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
    public function nominationProcess(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $training_schedule = $this->training_schedule->selectOne($id);
                $departmentList = $this->department
                    ->select('masters_department.id', 'masters_unit.unit_name', 'masters_department.department_name')
                    ->leftJoin('masters_unit', 'masters_department.unit_id', '=', 'masters_unit.id')
                    ->where('masters_department.status', '1')
                    ->get();
                $topicList  = $this->topic->select('id', 'topic_name')->where('status', '1')->get();
                $employeeList = Employee::select('id', 'emp_id', 'emp_name', 'email', 'department', 'employee_status')
                    ->where('id', '!=', $training_schedule->trainer_id)
                    ->where('user_role', '!=', 1)->where('user_role', '!=', 6)->where('user_role', '!=', 2)
                    ->where('status', 1)
                    ->get();
                $nominationProcessList = $this->nomination_process->getNomination($training_schedule->id);
                $data = array(
                    'departmentList' => $departmentList,
                    'topicList' => $topicList,
                    'employeeList' => $employeeList,
                    'training_schedule' => $training_schedule,
                    'nominationProcessList' => $nominationProcessList,
                );
            }
            return view('master.training_schedule.nomination', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $topicList  = $this->topic->select('id', 'topic_name')->where('status', '1')->get();
            $employeeList  = $this->employee->select('id', 'emp_name')->whereRaw('FIND_IN_SET(' . ROLE_TRAINER . ', user_role)')->where('status', '1')->get();
            $training_schedule = $this->training_schedule->find($id);
            $data = array(
                'departmentList' => $departmentList,
                'unitList' => $unitList,
                'topicList' => $topicList,
                'employeeList' => $employeeList,
                'training_schedule' => $training_schedule,

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

            $training = $this->training_schedule->updates($id);
            $training_status = TRAINING_RESCHEDULE_APPROVAL;
            $statuslog =  $this->training_statuslog->storestatus($id, $training_status);
            if ($training) {
                $trainingSchedule = $this->training_schedule->selectOne($id);
                $ehs_role = ROLE_EHS_HEAD;

                $ehs_details = User::select('id', 'role', 'name', 'employee_id', 'email')
                    ->whereRaw('FIND_IN_SET(' . $ehs_role . ', role)')
                    ->get();

                if (!empty($trainingSchedule)) {
                    $mailsubject = 'Training Rescheduled';

                    /**
                     * Send email notification
                     */
                    if ($ehs_details->isNotEmpty()) {
                        foreach ($ehs_details as $ehs_detail) {
                            if (!empty($ehs_detail->email)) {
                                $trainingArray = [
                                    'name' => $ehs_detail->name,
                                    'from_date' => Displaydateformat($trainingSchedule->from_date),
                                    'to_date' => Displaydateformat($trainingSchedule->to_date),
                                    'start_time' => Displaytimeformat($trainingSchedule->start_time),
                                    'end_time' => Displaytimeformat($trainingSchedule->end_time),
                                    'topic_name' => $trainingSchedule->topic_name,
                                    'unit' => $trainingSchedule->unit_name,
                                    'department' => $trainingSchedule->department_name,
                                    'venue' => $trainingSchedule->name_of_the_conference_hall,
                                    'mail_subject' => $mailsubject,
                                ];

                                Mail::to($ehs_detail->email)->queue(new TrainingApprovalEmail($trainingArray));
                            }
                        }
                    }


                    /**
                     * Send Web notification
                     */
                    $ehsids = $ehs_details->pluck('id')->toArray();
                    if (!empty($ehsids)) {
                        $img = admin_url('public/assets/icons/traning.png');
                        $notificationData = [
                            'notification_type' => 2,
                           'module' => 4,
                            'notification_message' => $mailsubject,
                            'mobile_notification' => json_encode([
                                'title' => $mailsubject,
                                'message' => 'A training reschedule has been created by ' . getUsername($trainingSchedule->created_by),
                                'icon' =>  $img,
                                'module' => 4,
                            ]),
                            'web_link' => 'training_schedule/ehs_approval/' . encryptId($trainingSchedule->id),
                            'assigned_user' => array_to_string($ehsids),
                            'created_by' => Auth::id(),
                        ];
                        notificationSave($notificationData);
                    }
                }
            }
            Session::flash('success', 'Your data has been updated successfully');
            return redirect(admin_url('training_schedule/list'));
        } catch (Exception $ex) {
            report($ex);
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
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $training_attendance = $this->training_attendance->where('training_schedule_id', $id)->exists();
            $nomination_process = $this->nomination_process->where('training_schedule_id', $id)->exists();

            if ($training_attendance || $nomination_process) {
                return response()->json(['status' => 'error', 'msg' => 'module_exits'], 406);
            }
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
                    'upload_type' => 8,
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

                // dispatch(new ImportTrainingSchedulejob($details));
                dispatch((new ImportTrainingSchedulejob($details))->onQueue('training_schedule'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', __('Your data has been uploaded sucessfully'));
            return redirect(admin_url('training_schedule/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('Training Schedule upload failed'));
            return redirect(admin_url('training_schedule/list'));
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->training_schedule->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'From Date',
                'To Date',
                'Start Time',
                'End Time',
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
                $export[] =  Displaydateformat($data->from_date);
                $export[] =  Displaydateformat($data->to_date);
                $export[] =  Displaytimeformat($data->start_time);
                $export[] =  Displaytimeformat($data->end_time);
                $export[] =  $data->topic_name;
                $export[] =  $data->emp_name;
                $export[] =  $data->unit_name;
                $export[] =  $data->department_name;
                $export[] =  $data->target_trainees;
                $export[] =  $data->name_of_the_conference_hall;
                $export[] =  $data->training_man_hours ?? '-';
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

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }


            $header = [
                __("common.sno"),
                'From Date',
                'To Date',
                'Start Time',
                'End Time',
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

            report($ex);
        }
    }

    public function certificateView($trainingScheduleId, $id)
    {
        try {
            $trainingId = decryptId($trainingScheduleId);
            $training_schedule = $this->training_schedule->selectOne($trainingId);
            $userAttendPass = $this->training_assessment_feedback->where('id', decryptId($id))
                ->where('attended_status', 1)
                ->where('status', 1)
                ->where('training_schedule_id', $training_schedule->id)
                ->first();
            $data = [
                'training_details' => $training_schedule,
                'userAttendPass' => $userAttendPass,
            ];

            $property = [
                'tempDir' => 'public/pdf/temp/',
                'mode' => 'utf-8',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'default_font' => 'arial',
            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $view = view('master.training_schedule.certificate', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);
            $filename = "Certificate.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['error' => 'Something went wrong while generating the PDF.']);
        }
    }
    public function exportViewPdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $training_schedule = $this->training_schedule->selectOne($id);
            $nominationProcessList = $this->nomination_process->getNomination($training_schedule->id);

            $attendanceDate = $request->attendance_date;

            $trainingAttendanceList = $this->training_attendance
                ->where('status', 1)
                ->where('training_schedule_id', $training_schedule->id)
                ->when($attendanceDate, function ($query, $attendanceDate) {
                    return $query->whereDate('attendance_date', DBdateformat($attendanceDate));
                })
                ->get();
            $trainingAssessmentList = $this->training_assessment_feedback->getAssessment($training_schedule->id);
            // Initialize an empty collection for training feedback
            $trainingFeedbackList = collect();
            foreach ($trainingAssessmentList as $assessment) {
                $feedbackList = $this->training_feedback->getfeedbackList($assessment->id);
                $trainingFeedbackList = $trainingFeedbackList->merge($feedbackList);
            }
            $rejectedlog = $this->training_statuslog->where('training_schedule_id', $training_schedule->id)->where('training_status', 3)->get();
            $data = [
                'training_schedule' => $training_schedule,
                'nominationProcessList' => $nominationProcessList,
                'trainingAttendanceList' => $trainingAttendanceList,
                'trainingAssessmentList' => $trainingAssessmentList,
                'trainingFeedbackList' => $trainingFeedbackList,
                'rejectedlog' => $rejectedlog,
            ];

            $property = [
                'tempDir' => 'public/pdf/temp/',
                'mode' => 'utf-8',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'default_font' => 'arial',
            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $view = view('master.training_schedule.training_pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);
            $filename = "Training.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['error' => 'Something went wrong while generating the PDF.']);
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
