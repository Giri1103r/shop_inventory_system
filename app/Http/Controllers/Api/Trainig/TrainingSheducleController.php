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
}
