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

use Illuminate\Support\Facades\DB;

use App\Models\Master\Venue;
use App\Models\Master\TrainingSchedule;
use App\Models\User;
use App\Models\Master\Employee;
use App\Models\Master\Topic;
use App\Models\UploadLog;
use App\Jobs\ImportCompanyJob;


class TrainingCalendarController extends Controller
{

    private $user;
    private $uploadlog;
    private $venue;
    private $employee;
    private $topic;
    private $training_schedule;


    public function __construct()
    {

        $this->training_schedule = new TrainingSchedule();
        $this->topic = new Topic();
        $this->employee = new Employee();
        $this->venue = new Venue();
        $this->user = new User();
        $this->uploadlog = new UploadLog();
    }



    public function index(Request $request)
    {
        if (Auth::check()) {
            $topicList  = $this->topic->select('id', 'topic_name')->where('status', '1')->get();
            $employeeList  = $this->employee->select('id', 'emp_name')->where('user_role', ROLE_TRAINER)->where('status', '1')->get();
            $data = array(
                'topicList' => $topicList,
                'employeeList' => $employeeList,
            );

            return view('master.training.training_calendar.list', $data);
        }
    }

    public function trainingShow(Request $request)
    {
        try {
            $query = $this->training_schedule
                ->select(
                    'training_schedule.*',
                    'training_masters_topic.topic_name',
                    'training_masters_venue.name_of_the_conference_hall',
                    'masters_employee.emp_name'
                )
                ->leftJoin('training_masters_topic', 'training_schedule.topic_id', '=', 'training_masters_topic.id')
                ->leftJoin('training_masters_venue', 'training_schedule.venue_id', '=', 'training_masters_venue.id')
                ->leftJoin('masters_employee', 'training_schedule.trainer_id', '=', 'masters_employee.id')
                ->whereBetween('from_date', [$request->start, $request->end]);

            if (CheckUserRole(ROLE_SUPERADMIN)) {
                $query->where('training_schedule.trash', 'NO');
            } elseif (CheckUserRole(ROLE_TRAINER)) {
                $trainer = DB::table('masters_employee')
                    ->select('id')
                    ->where('emp_id', Auth::user()->employee_id)
                    ->first();

                if ($trainer) {
                    $query->where('training_schedule.trainer_id', $trainer->id)
                        ->where('training_schedule.trash', 'NO');
                }
            }

            if ($request->filled('topic_id')) {
                $query->where('training_schedule.topic_id', decryptId($request->topic_id));
            }

            if ($request->filled('trainer_id')) {
                $query->where('training_schedule.trainer_id', decryptId($request->trainer_id));
            }

            $events = $query->get()->map(function ($event) {
                return [
                    'id' => encryptId($event->id),
                    'title' => 'Topic: ' . $event->topic_name,
                    'start' => $event->from_date,
                    'end' => $event->to_date,
                    'extendedProps' => [
                        'trainer_name' => $event->emp_name,
                        'venue_name' => $event->name_of_the_conference_hall,
                        'status' => $event->status,
                    ],
                ];
            });

            return response()->json($events);
        } catch (Exception $ex) {
            dd($ex->getMessage());
            return response()->json(['error' => 'Something went wrong. Please try again later.'], 500);
        }
    }


    public function updateEvent(Request $request, $id)
    {
        $event = TrainingSchedule::findOrFail($id);
        $event->update([
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'venue_id' => $request->venue_id,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Event updated successfully']);
    }
}
