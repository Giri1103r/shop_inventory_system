<?php

namespace App\Http\Controllers\Api\Trainig;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class TrainingSheducleController extends BaseController
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

    public function topiclist()
    {
        try {
            if (Auth::check()) {
                $topiclist = Topic::select(
                    'id',
                    'topic_name',
                )

                    ->where('status', 1)
                    ->get();

                $success = [
                    'responsible_person' => $topiclist,
                ];



                return $this->sendResponse($success, 'Topic details');
            }

            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        } catch (Exception $ex) {
            Log::error('Employee Fetch Error: ' . $ex->getMessage());

            return $this->sendError('Something went wrong.', ['error' => $ex->getMessage()], 500);
        }
    }

    public function venuelist()
    {
        try {
            if (Auth::check()) {
                $topiclist = Venue::select(
                    'id',
                    'name_of_the_conference_hall',
                )

                    ->where('status', 1)
                    ->get();

                $success = [
                    'responsible_person' => $topiclist,
                ];



                return $this->sendResponse($success, 'Topic details');
            }

            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        } catch (Exception $ex) {
            Log::error('Employee Fetch Error: ' . $ex->getMessage());

            return $this->sendError('Something went wrong.', ['error' => $ex->getMessage()], 500);
        }
    }

    public function list()
    {
        if (Auth::user()) {
            $request = request();
            if ($request->has('search')) {
                if ($request->search != '' && $request->search != null) {
                    $search = $request->search;
                }
            }
            $traning_schedule_array = TrainingSchedule::select('training_schedule.*', 'masters_unit.unit_name', 'masters_employee.emp_name', 'masters_department.department_name', 'training_masters_topic.topic_name', 'training_masters_venue.name_of_the_conference_hall');
            $traning_schedule_array = $traning_schedule_array->leftJoin('masters_unit', 'training_schedule.unit_id', '=', 'masters_unit.id');
            $traning_schedule_array = $traning_schedule_array->leftJoin('masters_employee', 'training_schedule.trainer_id', '=', 'masters_employee.id');
            $traning_schedule_array = $traning_schedule_array->leftJoin('masters_department', 'training_schedule.department_id', '=', 'masters_department.id');
            $traning_schedule_array = $traning_schedule_array->leftJoin('training_masters_topic', 'training_schedule.topic_id', '=', 'training_masters_topic.id');
            $traning_schedule_array = $traning_schedule_array->leftJoin('training_masters_venue', 'training_schedule.venue_id', '=', 'training_masters_venue.id');
            $org_total =  $traning_schedule_array;
            $org_total_counts = $org_total->count();

            /**
             * Role Based list view condition start
             */

            if (CheckUserRole(ROLE_SUPERADMIN)) {
                $traning_schedule_array->where('training_schedule.trash', 'NO');
            } elseif (CheckUserRole(ROLE_ADMIN)) {
                $traning_schedule_array->where('training_schedule.trash', 'NO');
            } elseif (CheckUserRole(ROLE_EHS_HEAD)) {
                $traning_schedule_array->where('training_schedule.trash', 'NO');
            } elseif (CheckUserRole(ROLE_TRAINER)) {
                $trainer = DB::table('masters_employee')
                    ->select('id', 'emp_id')
                    ->where('emp_id', Auth::user()->employee_id)
                    ->first();
                if ($trainer) {
                    $traning_schedule_array->where('training_schedule.trainer_id', $trainer->id)
                        ->where('training_schedule.trash', 'NO');
                }
            } elseif (Auth::user()->role != ROLE_TRAINER || Auth::user()->role != ROLE_EHS_HEAD || Auth::user()->role != ROLE_SUPERADMIN || Auth::user()->role != ROLE_ADMIN) {
                $nomination = DB::table('masters_employee')
                    ->select('id', 'emp_id')
                    ->where('emp_id', Auth::user()->employee_id)
                    ->first();

                if ($nomination) {
                    $traning_schedule_array->where(function ($q) use ($nomination) {
                        $q->whereExists(function ($subQuery) use ($nomination) {
                            $subQuery->select(DB::raw(1))
                                ->from('training_nomination_process')
                                ->whereColumn('training_nomination_process.training_schedule_id', 'training_schedule.id')
                                ->where('training_nomination_process.employee_id', $nomination->id);
                        });
                    })->where('training_schedule.trash', 'NO');
                }
            }

            if (!empty($search)) {
                $searchDate = DBdateformat($search);

                $traning_schedule_array->where(function ($query) use ($searchDate) {
                    $query->orWhereDate('training_schedule.from_date', $searchDate)
                        ->orWhereDate('training_schedule.to_date', $searchDate);
                });
            }


            $traning_schedule_array = $traning_schedule_array->orderBy('training_schedule.id', 'DESC')->paginate($request->input('per_page', 10));

            $traning_schedule_list = $traning_schedule_array->toArray();

            if (empty($traning_schedule_list['data'])) {
                return $this->sendError('No records found.', [], 404);
            }


            $data_array = [];
            foreach ($traning_schedule_list['data'] as $listdata) {
                $data = [];
                $data['id'] = $listdata['id'] ?? '';
                $data['from_date'] = Displaydateformat($listdata['from_date'] ?? '');
                $data['to_date'] = Displaydateformat($listdata['to_date'] ?? '');
                $data['topic_name'] = $listdata['topic_name'] ?? '';
                $data['trainer_id'] = getEmployeename($listdata['trainer_id'] ?? '');
                $status = $listdata['training_status'] ?? null;
                $data['training_status'] =
                    in_array($status, [1, 2, 4, 5]) ? 'Training Pending' :
                    ($status == 8 ? 'Training Completed' :
                    (in_array($status, [6, 7]) ? 'Training in Progress' :
                    ($status == 3 ? 'Training Rejected' : 'Unknown Status')));
                $data['status'] = $listdata['status'] == 1 ? 'Active' : 'In-Active';
                $data['created_by'] = getUsername($listdata['created_by'] ?? '');
                $data['created_at'] = Displaydateformat($listdata['created_at'] ?? '');



                $data_array[] = $data;
            }


            $traning_schedule_details = [
                'per_page' => $traning_schedule_list['per_page'] ?? 0,
                'current_page' => $traning_schedule_list['current_page'] ?? 0,
                'from' => $traning_schedule_list['from'] ?? 0,
                'to' => $traning_schedule_list['to'] ?? 0,
                'total' => $traning_schedule_list['total'] ?? 0,
                'total_page' => $traning_schedule_list['last_page'] ?? 0,
                'list' => $data_array,
            ];


            $success = [
                'traning_schedule_details' => $traning_schedule_details
            ];

            return $this->sendResponse($success, 'Training Details');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }


    public function view(Request $request)
    {
        try {
            if (Auth::user()) {
                $id = $request->id;

                $training_schedule = $this->training_schedule->selectOne($id);
                $nominationProcessList = $this->nomination_process->getNomination($training_schedule->id);
                $trainingAssessmentList = $this->training_assessment_feedback->getAssessment($training_schedule->id);
                $attendanceDate = $request->attendance_date;
                $trainingAttendanceList = $this->training_attendance
                    ->where('status', 1)
                    ->where('training_schedule_id', $training_schedule->id)
                    ->when($attendanceDate, function ($query, $attendanceDate) {
                        return $query->whereDate('attendance_date', DBdateformat($attendanceDate));
                    })
                    ->get();
                $statusLog = $this->training_statuslog->where('training_schedule_id', $training_schedule->id)->where('training_status', 3)->get();
                $trainingAssessmentList = $this->training_assessment_feedback->getAssessment($training_schedule->id);
                // Status Log
                $EhsStatusLog = [];

                foreach ($statusLog as $log) {
                    $EhsStatusLog[] = [
                        'date' => Displaydateformat($log->created_at),
                        'remarks' => $log->remarks,
                    ];
                }
                // Nomination Process
                $nominationList = [];

                foreach ($nominationProcessList as $nomination) {
                    $nominationList[] = [
                        'id' => $nomination->id,
                        'training_schedule_id' => $nomination->training_schedule_id,
                        'emp_worker' => $nomination->emp_worker == 1 ? 'Employee' : 'Worker',
                        'employee_id' => $nomination->emp_id,
                        'department_id' => $nomination->department_name,
                        'employee_name' => $nomination->emp_name,
                        'email_id' => $nomination->email,
                        'employee_type' => $nomination->employee_type,
                        'last_training_attended_on' => $nomination->last_training_attended_on,
                        'last_training_attended_topic' => $nomination->topic_name,
                        'topic_id' => $nomination->topic_id,
                        'from_date' => Displaydateformat($nomination->from_date),
                        'to_date' => Displaydateformat($nomination->to_date),


                    ];
                }

                // training attendance list

                $AttendanceList = [];
                foreach ($trainingAttendanceList as $attendance) {
                    $AttendanceList[] = [
                        'attendance_id' => $attendance->id,
                        'training_schedule_id' => $attendance->training_schedule_id,
                        'attendance_date' => Displaydateformat($attendance->attendance_date),
                        'employee_name' => $attendance->emp_name,
                        'email' => $attendance->email,
                        'checked' => $attendance->attendance_status == 1 ? 'Yes' : 'No',
                    ];
                }
                // training assessment list
                $AssessmentList = [];

                foreach ($trainingAssessmentList as $assessment) {
                    $AssessmentList[] = [
                        'employee_name' => $assessment->emp_name,
                        'checked' => $assessment->attended_status == 1 ? 'Yes' : 'No',
                        'mark' => $assessment->mark,
                        'assessment' =>   $assessment->assessment == 1 ? 'Pass' : ($assessment->mark == 2 ? 'Fail' : 'Not Attended'),
                        'feed_back' => !empty($assessment->feedback) ? strip_tags($assessment->feedback) : '-' ,
                    ];
                }

                // training feed back

                $trainingFeedbackList = collect();

                foreach ($trainingAssessmentList as $assessment) {
                    $feedbackList = $this->training_feedback->getfeedbackList($assessment->id);
                    $trainingFeedbackList = $trainingFeedbackList->merge($feedbackList);
                }

                $feedBack = [];
                foreach ($trainingFeedbackList as $feedback) {
                    $feedBack[] = [
                        'employee_id' => $feedback->emp_id,
                        'employee_name' => $feedback->emp_name,
                        'trainer_feedback' => $feedback->trainer_feedback,
                        'training_feedback' => $feedback->training_feedback,
                    ];
                }
                $ehsData = [];

                if (!empty($EhsStatusLog)) {
                    $ehsData['rejection_log'] = $EhsStatusLog;
                }

                if (!empty($training_schedule->approver_name) && !empty($training_schedule->date) && !empty($training_schedule->remark)) {
                    $ehsData['approver_name'] = $training_schedule->approver_name;
                    $ehsData['date'] = Displaydateformat($training_schedule->date);
                    $ehsData['remarks'] = $training_schedule->remark;
                }

                $success = [

                    'id' => $training_schedule->id,
                    'from_date' => Displaydateformat($training_schedule->from_date),
                    'to_date' => Displaydateformat($training_schedule->to_date),
                    'start_time' => $training_schedule->start_time,
                    'end_time' => $training_schedule->end_time,
                    'topic_id' => $training_schedule->topic_name,
                    'trainer_id' => ($training_schedule->emp_name),
                    'unit_id' => ($training_schedule->unit_name),
                    'department_id' => ($training_schedule->department_name),
                    'target_trainees' => ($training_schedule->target_trainees),
                    'venue_id' => ($training_schedule->name_of_the_conference_hall),
                    'training_man_hours' => ($training_schedule->training_man_hours),
                    'training_status' => ($training_schedule->training_status == 1 ||
                        $training_schedule->training_status == 2 ||
                        $training_schedule->training_status == 4 ||
                        $training_schedule->training_status == 5) ? 'Training Pending' : ($training_schedule->training_status == 8 ? 'Training Completed' : ($training_schedule->training_status == 6 ||
                        $training_schedule->training_status == 7 ? 'Training in Progress' : ($training_schedule->training_status == 3 ? 'Training Rejected' : 'Unknown Status'))),
                    'status' =>  $training_schedule->status == 1 ? 'Active' : 'In-Active',
                    'created_by' => getusername($training_schedule->created_by),
                    'created_at' => Displaydateformat($training_schedule->created_at),
                    'ehs_head_approval_pending' => $ehsData,
                    'nomination_process' => $nominationList,
                    'training_attendance' => $AttendanceList,
                    'training_assessment' => $AssessmentList,
                    'training_feedback' => $feedBack,

                ];

                return $this->sendResponse($success, 'Training Details');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }


    public function storeAttendance(Request $request)
    {
        try {



            // Save or update attendance
            $success = $this->training_attendance->storeOrUpdate_api($request);

            $attendanceDate = DBdateformat($request->attendance_date);
            $trainingScheduleId = ($request->id);

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

            $newTotalManHours = $existingTrainingSchedule && $existingTrainingSchedule->training_man_hours
                ? $existingTrainingSchedule->training_man_hours + $totalManHoursForDay
                : $totalManHoursForDay;

            $this->training_schedule->updateTrainingManHours($trainingScheduleId, $newTotalManHours);

            return $this->sendResponse($success, 'Attendance Stored Successfully');
        } catch (Exception $ex) {
            dd($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    public function endTrainingStore(Request $request)
    {
        try {
            $trainingScheduleId = ($request->training_schedule_id);
            $training_status = TRAINING_FEEDBACK_ADMIN_APPROVE;

            $AssessmentStore = $this->training_assessment_feedback->store_api();
            $updateStatus = $this->training_schedule->updateStatus($trainingScheduleId, $training_status);
            $statuslog =  $this->training_statuslog->storestatus($trainingScheduleId, $training_status);
            $success =[
                'training_schedule'=>  $trainingScheduleId,
            ];
            return $this->sendResponse($success, 'Assessment update Successfully!');
        } catch (Exception $ex) {
            dd($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
