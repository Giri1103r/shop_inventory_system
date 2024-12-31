<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use DateTime;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrainingSchedule extends Model
{
    use  HasFactory;


    protected $table = 'training_schedule';
    protected $primaryKey = 'id';

    protected $fillable = [
        'from_date',
        'to_date',
        'topic_id',
        'trainer_id',
        'unit_id',
        'department_id',
        'venue_id',
        'target_trainees',
        'training_man_hours',
        'training_status',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('training_schedule.*', 'masters_unit.unit_name', 'masters_employee.emp_name', 'masters_department.department_name', 'training_masters_topic.topic_name', 'training_masters_venue.name_of_the_conference_hall');
        $query = $query->leftJoin('masters_unit', 'training_schedule.unit_id', '=', 'masters_unit.id');
        $query = $query->leftJoin('masters_employee', 'training_schedule.trainer_id', '=', 'masters_employee.id');
        $query = $query->leftJoin('masters_department', 'training_schedule.department_id', '=', 'masters_department.id');
        $query = $query->leftJoin('training_masters_topic', 'training_schedule.topic_id', '=', 'training_masters_topic.id');
        $query = $query->leftJoin('training_masters_venue', 'training_schedule.venue_id', '=', 'training_masters_venue.id');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        /**
         * Role Based list view condition start
         */

        if (CheckUserRole(ROLE_SUPERADMIN)) {
            $query->where('training_schedule.trash', 'NO');
        } elseif (CheckUserRole(ROLE_TRAINER)) {
            $trainer = DB::table('masters_employee')
                ->select('id', 'emp_id')
                ->where('emp_id', Auth::user()->employee_id)
                ->first();
            if ($trainer) {
                $query->where('training_schedule.trainer_id', $trainer->id)
                    ->where('training_schedule.trash', 'NO');
            }
        } elseif (Auth::user()->role != ROLE_TRAINER || Auth::user()->role != ROLE_SUPERADMIN) {
            $nomination = DB::table('masters_employee')
                ->select('id', 'emp_id')
                ->where('emp_id', Auth::user()->employee_id)
                ->first();

            if ($nomination) { 
                $query->where(function ($q) use ($nomination) { 
                    $q->whereExists(function ($subQuery) use ($nomination) {
                        $subQuery->select(DB::raw(1))
                            ->from('training_nomination_process')
                            ->whereColumn('training_nomination_process.training_schedule_id', 'training_schedule.id')
                            ->where('training_nomination_process.employee_id', $nomination->id); // Check if user is nominated
                    });
                })->where('training_schedule.trash', 'NO');
            }
        }

        /**
         * Role Based list view condition end
         */
        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query->Where('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_employee.emp_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_department.department_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('training_masters_topic.topic_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('training_masters_venue.name_of_the_conference_hall', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('from_date') && $request->from_date) {
            $fromDate = Carbon::createFromFormat('d-m-Y H:i', $request->from_date)->format('Y-m-d H:i:s');
            $query = $query->where('training_schedule.from_date', '>=', $fromDate);
        }

        if ($request->has('to_date') && $request->to_date) {
            $toDate = Carbon::createFromFormat('d-m-Y H:i', $request->to_date)->format('Y-m-d H:i:s');
            $query = $query->where('training_schedule.to_date', '<=', $toDate);
        }

        if ($request->has('topic_id') && $request->topic_id) {
            $query = $query->where('training_schedule.topic_id', decryptId($request->topic_id));
        }
        if ($request->has('trainer_id') && $request->trainer_id) {
            $query = $query->where('training_schedule.trainer_id', decryptId($request->trainer_id));
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('training_schedule.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('department_id') && $request->department_id) {
            $query = $query->where('training_schedule.department_id', decryptId($request->department_id));
        }
        if ($request->has('venue_id') && $request->venue_id) {
            $query = $query->where('training_schedule.venue_id', decryptId($request->venue_id));
        }

        if ($request->has('status') && $request->status) {
            $query = $query->where('training_schedule.status', 'LIKE', '%' . decryptId($request->status) . '%');
        }
        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('id', 'DESC');

        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }

        $data = $query->get();

        $datas = array(
            'data' => $data,
            'total_records' => $org_total_counts,
            'filter_records' => $total_records,
        );
        return $datas;
    }

    public function getUniqueSchedule($fromDate, $toDate, $topicId, $trainerId, $unitId, $departmentId, $venueId)
    {
        $fromdate = (new DateTime($fromDate))->format('Y-m-d');
        $todate = (new DateTime($toDate))->format('Y-m-d');

        $conflicts = [];

        if ($fromdate && $todate) {
            $query = TrainingSchedule::where(function ($query) use ($topicId, $trainerId, $unitId, $departmentId, $venueId, $fromdate, $todate) {
                $query->where('topic_id', '=', $topicId)
                    ->orWhere('trainer_id', '=', $trainerId)
                    ->orWhere('unit_id', '=', $unitId)
                    ->orWhere('department_id', '=', $departmentId)
                    ->orWhere('venue_id', '=', $venueId);
            })
                ->where('from_date', '<=', $todate)
                ->where('to_date', '>=', $fromdate);

            if ($query->exists()) {
                $schedules = $query->get();
                foreach ($schedules as $schedule) {
                    if ($schedule->topic_id == $topicId) {
                        $conflicts['topic_id'] = 'The selected topic is already scheduled within this date range.';
                    }
                    if ($schedule->trainer_id == $trainerId) {
                        $conflicts['trainer_id'] = 'The selected trainer is already scheduled within this date range.';
                    }
                    if ($schedule->unit_id == $unitId) {
                        $conflicts['unit_id'] = 'The selected unit is already scheduled within this date range.';
                    }
                    if ($schedule->department_id == $departmentId) {
                        $conflicts['department_id'] = 'The selected department is already scheduled within this date range.';
                    }
                    if ($schedule->venue_id == $venueId) {
                        $conflicts['venue_id'] = 'The selected venue is already scheduled within this date range.';
                    }
                }
            }
        }

        return $conflicts;
    }

    public function getExistUniqueSchedule($fromDate, $toDate, $topicId, $trainerId, $unitId, $departmentId, $venueId, $ids)
    {
        $fromdate = (new DateTime($fromDate))->format('Y-m-d');
        $todate = (new DateTime($toDate))->format('Y-m-d');

        $conflicts = [];

        if ($fromdate && $todate) {
            $query = TrainingSchedule::where(function ($query) use ($topicId, $trainerId, $unitId, $departmentId, $venueId, $fromdate, $todate, $ids) {
                $query->where(function ($query) use ($topicId, $trainerId, $unitId, $departmentId, $venueId) {
                    $query->where('topic_id', '=', $topicId)
                        ->orWhere('trainer_id', '=', $trainerId)
                        ->orWhere('unit_id', '=', $unitId)
                        ->orWhere('department_id', '=', $departmentId)
                        ->orWhere('venue_id', '=', $venueId);
                })
                    ->where('from_date', '<=', $todate)
                    ->where('to_date', '>=', $fromdate)
                    ->where('id', '!=', $ids); // Exclude the current record
            });

            if ($query->exists()) {
                $schedules = $query->get();
                foreach ($schedules as $schedule) {
                    if ($schedule->topic_id == $topicId) {
                        $conflicts['topic_id'] = 'The selected topic is already scheduled within this date range.';
                    }
                    if ($schedule->trainer_id == $trainerId) {
                        $conflicts['trainer_id'] = 'The selected trainer is already scheduled within this date range.';
                    }
                    if ($schedule->unit_id == $unitId) {
                        $conflicts['unit_id'] = 'The selected unit is already scheduled within this date range.';
                    }
                    if ($schedule->department_id == $departmentId) {
                        $conflicts['department_id'] = 'The selected department is already scheduled within this date range.';
                    }
                    if ($schedule->venue_id == $venueId) {
                        $conflicts['venue_id'] = 'The selected venue is already scheduled within this date range.';
                    }
                }
            }
        }

        return $conflicts;
    }

    public function ExistuniqueCheck($data)
    {
        return $this->where($data['param'],  $data['value'])
            ->where('id', '!=', decryptId($data['id']))
            ->get();
    }

    public function store()
    {
        $request = request();

        $insert_array = array(
            'from_date' => DBdatetimeformat($request->from_date),
            'to_date' => DBdatetimeformat($request->to_date),
            'topic_id' => decryptId($request->topic_id),
            'trainer_id' => decryptId($request->trainer_id),
            'venue_id' => decryptId($request->venue_id),
            'unit_id' => decryptId($request->unit_id),
            'department_id' => decryptId($request->department_id),
            'target_trainees' => $request->target_trainees,
            'training_status' => 1,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }
    public function updateStatus($trainingScheduleId, $training_status)
    {
        $request = request();

        $update_array = array(
            'training_status' => $training_status,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        );
        return $this->where('id', $trainingScheduleId)->update($update_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'from_date' => DBdatetimeformat($request->from_date),
            'to_date' => DBdatetimeformat($request->to_date),
            'topic_id' => decryptId($request->topic_id),
            'trainer_id' => decryptId($request->trainer_id),
            'venue_id' => decryptId($request->venue_id),
            'unit_id' => decryptId($request->unit_id),
            'department_id' => decryptId($request->department_id),
            'target_trainees' => $request->target_trainees,
            'training_man_hours' => $request->training_man_hours ?? '',
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        );

        return $this->where('id', $id)->update($update_array);
    }

    public function statuschange($id)
    {
        $request = request();

        $type = $request->types;
        if ($type == 1) {
            $update_data = array(
                'status' => 0,
            );
        } else {
            $update_data = array(
                'status' => 1,
            );
        }

        return $this->where('id', $id)->update($update_data);
    }
    public function calculateTrainingHours()
    {
        if ($this->from_date && $this->to_date) {
            $fromDateTime = Carbon::parse($this->from_date);
            $toDateTime = Carbon::parse($this->to_date);

            if ($fromDateTime->isSameDay($toDateTime)) {
                if ($fromDateTime->format('H:i') !== '00:00' || $toDateTime->format('H:i') !== '00:00') {
                    $hours = $fromDateTime->diffInMinutes($toDateTime) / 60;
                    return round($hours, 2);
                } else {
                    return 8;
                }
            } else {
                $days = $fromDateTime->diffInDays($toDateTime) + 1;
                return $days * 8;
            }
        }

        return 0;
    }

    public function deleterecord($id)
    {

        $update_data = array(
            'status' => 0,
            'trash' => 'YES',
        );

        return $this->where('id', $id)->update($update_data);
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('training_schedule.*', 'masters_unit.unit_name', 'masters_employee.emp_name', 'masters_department.department_name', 'training_masters_topic.topic_name', 'training_masters_venue.name_of_the_conference_hall');
        $query = $query->leftJoin('masters_unit', 'training_schedule.unit_id', '=', 'masters_unit.id');
        $query = $query->leftJoin('masters_employee', 'training_schedule.trainer_id', '=', 'masters_employee.id');
        $query = $query->leftJoin('masters_department', 'training_schedule.department_id', '=', 'masters_department.id');
        $query = $query->leftJoin('training_masters_topic', 'training_schedule.topic_id', '=', 'training_masters_topic.id');
        $query = $query->leftJoin('training_masters_venue', 'training_schedule.venue_id', '=', 'training_masters_venue.id');


        /**
         * Role Based list view condition start
         */

        if (CheckUserRole(ROLE_SUPERADMIN)) {
            $query->where('training_schedule.trash', 'NO');
        } elseif (CheckUserRole(ROLE_TRAINER)) {
            $trainer = DB::table('masters_employee')
                ->select('id', 'emp_id')
                ->where('emp_id', Auth::user()->employee_id)
                ->first();
            if ($trainer) {
                $query->where('training_schedule.trainer_id', $trainer->id)
                    ->where('training_schedule.trash', 'NO');
            }
        } elseif (Auth::user()->role == ROLE_USER) {
            $nomination = DB::table('masters_employee')
                ->select('id', 'emp_id')
                ->where('emp_id', Auth::user()->employee_id)
                ->first();

            if ($nomination) {
                $query->where(function ($q) use ($nomination) { 
                    $q->whereExists(function ($subQuery) use ($nomination) {
                        $subQuery->select(DB::raw(1))
                            ->from('training_nomination_process')
                            ->whereColumn('training_nomination_process.training_schedule_id', 'training_schedule.id')
                            ->where('training_nomination_process.employee_id', $nomination->id); 
                    });
                })->where('training_schedule.trash', 'NO');
            }
        }
        /**
         * Role Based list view condition end
         */
        if (!empty($request->search)) {
            $query->where(function ($subQuery) use ($request) {
                $search = $request->search;
                $subQuery->Where('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_employee.emp_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_department.department_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('training_masters_topic.topic_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('training_masters_venue.name_of_the_conference_hall', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('from_date') && $request->from_date) {
            $fromDate = Carbon::createFromFormat('d-m-Y H:i', $request->from_date)->format('Y-m-d H:i:s');
            $query = $query->where('training_schedule.from_date', '>=', $fromDate);
        }

        if ($request->has('to_date') && $request->to_date) {
            $toDate = Carbon::createFromFormat('d-m-Y H:i', $request->to_date)->format('Y-m-d H:i:s');
            $query = $query->where('training_schedule.to_date', '<=', $toDate);
        }
        if ($request->has('topic_id') && $request->topic_id) {
            $query = $query->where('training_schedule.topic_id', decryptId($request->topic_id));
        }
        if ($request->has('trainer_id') && $request->trainer_id) {
            $query = $query->where('training_schedule.trainer_id', decryptId($request->trainer_id));
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('training_schedule.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('department_id') && $request->department_id) {
            $query = $query->where('training_schedule.department_id', decryptId($request->department_id));
        }
        if ($request->has('venue_id') && $request->venue_id) {
            $query = $query->where('training_schedule.venue_id', decryptId($request->venue_id));
        }

        if ($request->has('status') && $request->status) {
            $query = $query->where('training_schedule.status', 'LIKE', '%' . decryptId($request->status) . '%');
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
    }
   
    public function selectOne($id)
    {

        $data = $this->select('training_schedule.*', 'masters_unit.unit_name', 'masters_employee.emp_name' , 'masters_employee.login_id', 'masters_employee.email', 'masters_department.department_name', 'training_masters_topic.topic_name', 'training_masters_venue.name_of_the_conference_hall')->leftJoin('masters_unit', 'training_schedule.unit_id', '=', 'masters_unit.id')->leftJoin('training_masters_topic', 'training_schedule.topic_id', '=', 'training_masters_topic.id')->leftJoin('masters_department', 'training_schedule.department_id', '=', 'masters_department.id')->leftJoin('masters_employee', 'training_schedule.trainer_id', '=', 'masters_employee.id')->leftJoin('training_masters_venue', 'training_schedule.venue_id', '=', 'training_masters_venue.id')
            ->where('training_schedule.id', $id)
            ->first();

        return $data;
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('training_schedule'));
    }
}
