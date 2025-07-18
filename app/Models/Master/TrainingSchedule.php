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
        'start_time',
        'end_time',
        'training_hrs_perday',
        'topic_id',
        'trainer_id',
        'company_id',
        'unit_id',
        'department_id',
        'venue_id',
        'target_trainees',
        'training_man_hours',
        'approver_emp_id',
        'approver_name',
        'date',
        'remark',
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

        if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_DASHBOARD_VIEWER)) {
            $query->where('training_schedule.trash', 'NO');
        } elseif (CheckUserRole(ROLE_ADMIN)) {
            $query->where('training_schedule.trash', 'NO');
        } elseif (CheckUserRole(ROLE_EHS_HEAD)) {
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
        } elseif (Auth::user()->role != ROLE_TRAINER || Auth::user()->role != ROLE_EHS_HEAD || Auth::user()->role != ROLE_SUPERADMIN || Auth::user()->role != ROLE_ADMIN) {
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
            $fromDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->format('Y-m-d');
            $query = $query->where('training_schedule.from_date', '>=', $fromDate);
        }

        if ($request->has('to_date') && $request->to_date) {
            $toDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->format('Y-m-d');
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
        if ($request->has('company_id') && $request->company_id) {
            $query = $query->where('training_schedule.company_id', decryptId($request->company_id));
        }
        if ($request->has('department_id') && $request->department_id) {
            $query = $query->where('training_schedule.department_id', decryptId($request->department_id));
        }
        if ($request->has('company_name') && $request->company_name) {

            $query->where('training_schedule.company_id', decryptId($request->company_name));
        }
        if ($request->has('fromDate') && !empty($request->fromDate)) {
            $datepickersearch = DBdateformat($request->fromDate);

            $query->where(function ($query) use ($datepickersearch) {
                $query->whereDate('training_schedule.created_at', '>=', $datepickersearch);
            });
        }

        if ($request->has('toDate') && !empty($request->toDate)) {
            $enddatepickersearch = DBdateformat($request->toDate);

            $query->where(function ($query) use ($enddatepickersearch) {
                $query->whereDate('training_schedule.created_at', '<=', $enddatepickersearch);
            });
        }
        if ($request->has('training_department') && $request->training_department) {
            $query = $query->where('training_schedule.department_id', ($request->training_department));
        }
        if ($request->has('venue_id') && $request->venue_id) {
            $query = $query->where('training_schedule.venue_id', decryptId($request->venue_id));
        }

        if ($request->has('status') && $request->status) {
            $query = $query->where('training_schedule.status', 'LIKE', '%' . decryptId($request->status) . '%');
        }

        if ($request->has('dashboard_openCloseStatus') && $request->dashboard_openCloseStatus) {
            $openCloseStatus = decryptId($request->dashboard_openCloseStatus);
            if ($openCloseStatus == "1") {
                $query = $query->where('training_schedule.training_status', '!=', 8);
            } else {
                $query = $query->where('training_schedule.training_status', 8);
            }
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

    public function vpApproval($id, $training_status)
    {
        $request = request();

        $update_array = array(
            'date' => DBdateformat($request->date),
            'approver_emp_id' => $request->approver_emp_id,
            'approver_name' => $request->approver_name,
            'remark' => $request->remark,
            'training_status' => $training_status,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        );
        return $this->where('id', $id)->update($update_array);
    }
    public function store()
    {
        $request = request();

        $startTime = new DateTime($request->start_time);
        $endTime = new DateTime($request->end_time);

        $interval = $startTime->diff($endTime);
        $trainingHrsPerDay = $interval->h + ($interval->i / 60);

        $insert_array = array(
            'from_date' => DBdateformat($request->from_date),
            'to_date' => DBdateformat($request->to_date),
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'training_hrs_perday' =>  $trainingHrsPerDay,
            'topic_id' => decryptId($request->topic_id),
            'trainer_id' => decryptId($request->trainer_id),
            'venue_id' => decryptId($request->venue_id),
            'unit_id' => decryptId($request->unit_id),
            'company_id' => decryptId($request->company_id),
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

    public function updateTrainingManHours($trainingScheduleId, $totalManHours)
    {

        $update_array = array(
            'training_man_hours' => $totalManHours,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        );
        return $this->where('id', $trainingScheduleId)->update($update_array);
    }

    public function updates($id)
    {

        $request = request();
        $startTime = new DateTime($request->start_time);
        $endTime = new DateTime($request->end_time);

        $interval = $startTime->diff($endTime);
        $trainingHrsPerDay = $interval->h + ($interval->i / 60);
        $update_array = array(
            'from_date' => DBdateformat($request->from_date),
            'to_date' => DBdateformat($request->to_date),
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'training_hrs_perday' =>  $trainingHrsPerDay,
            'topic_id' => decryptId($request->topic_id),
            'trainer_id' => decryptId($request->trainer_id),
            'venue_id' => decryptId($request->venue_id),
            'unit_id' => decryptId($request->unit_id),
            'department_id' => decryptId($request->department_id),
            'target_trainees' => $request->target_trainees,
            'training_man_hours' => $request->training_man_hours ?? '',
            'training_status' => TRAINING_RESCHEDULE_APPROVAL,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        );

        return $this->where('id', $id)->update($update_array);
    }
    public function monthwiseTrainingCountData()
    {
        $request = request();

        // Start query
        $query = self::query();

        // Apply company Filter
        if ($request->CompanyId) {
            $company_id = decryptId($request->CompanyId);
            $query->where('training_schedule.company_id', $company_id);
        }

        // Date Filters
        if ($request->Fromdate && $request->Todate) {
            $query->whereBetween('training_schedule.created_at', [
                DBdateformat($request->Fromdate),
                DBdateformat($request->Todate) . ' 23:59:59'
            ]);
        } elseif ($request->Fromdate) {
            $query->where('training_schedule.created_at', '>=', DBdateformat($request->Fromdate));
        } elseif ($request->Todate) {
            $query->where('training_schedule.created_at', '<=', DBdateformat($request->Todate) . ' 23:59:59');
        }
        // Select and group data by year and month
        $results = $query->selectRaw(
            'YEAR(from_date) as year,
             MONTH(from_date) as month,
             COUNT(*) as total_count,
             SUM(CASE WHEN training_status IN (1, 2, 4, 5) THEN 1 ELSE 0 END) as pending_count,
             SUM(CASE WHEN training_status = 3 THEN 1 ELSE 0 END) as rejected_count,
             SUM(CASE WHEN training_status IN (6, 7) THEN 1 ELSE 0 END) as inprogress_count,
             SUM(CASE WHEN training_status = 8 THEN 1 ELSE 0 END) as completed_count'
        )
            ->groupBy('year', 'month')
            ->orderByRaw('year ASC, month ASC') // Ensure chronological order
            ->get();

        return $results;
    }


    // Department wise schedule count in the dashboard
    public function getDepartmentData()
    {
        $request = request();

        $query = $this->leftJoin('masters_department', 'training_schedule.department_id', '=', 'masters_department.id');


        if ($request->CompanyId) {
            $company_id = decryptId($request->CompanyId);
            $query->where('training_schedule.company_id', $company_id);
        }


        if ($request->Fromdate && $request->Todate) {
            $query->whereBetween('training_schedule.created_at', [
                DBdateformat($request->Fromdate),
                DBdateformat($request->Todate) . ' 23:59:59'
            ]);
        } elseif ($request->Fromdate) {
            $query->where('training_schedule.created_at', '>=', DBdateformat($request->Fromdate));
        } elseif ($request->Todate) {
            $query->where('training_schedule.created_at', '<=', DBdateformat($request->Todate) . ' 23:59:59');
        }

        return $query->selectRaw('training_schedule.department_id, COUNT(*) as count')
            ->groupBy('training_schedule.department_id')
            ->orderBy('count', 'desc')
            ->get();
    }

    public function getTrainingCount()
    {
        $request = request();
        $query = $this->where('training_schedule.status', '1');

        // Apply company Filter
        if ($request->CompanyId) {
            $company_id = decryptId($request->CompanyId);
            $query->where('training_schedule.company_id', $company_id);
        }

        // Date Filters
        if ($request->Fromdate && $request->Todate) {
            $query->whereBetween('training_schedule.created_at', [
                DBdateformat($request->Fromdate),
                DBdateformat($request->Todate) . ' 23:59:59'
            ]);
        } elseif ($request->Fromdate) {
            $query->where('training_schedule.created_at', '>=', DBdateformat($request->Fromdate));
        } elseif ($request->Todate) {
            $query->where('training_schedule.created_at', '<=', DBdateformat($request->Todate) . ' 23:59:59');
        }

        // Select and aggregate the counts
        $results = $query->selectRaw(
            'COUNT(*) as total_count,
             SUM(CASE WHEN training_status IN (1, 2, 4, 5) THEN 1 ELSE 0 END) as pending_count,
             SUM(CASE WHEN training_status = 3 THEN 1 ELSE 0 END) as rejected_count,
             SUM(CASE WHEN training_status IN (6, 7) THEN 1 ELSE 0 END) as inprogress_count,
             SUM(CASE WHEN training_status = 8 THEN 1 ELSE 0 END) as completed_count'
        )->first();

        return $results;
    }

    public function statusCount($type = '', $params = [])
    {
        $query = $this->where('training_schedule.trash', 'NO');

        if (isset($params['from_date']) && isset($params['to_date'])) {
            $query->whereBetween('created_at', [DBdateformat($params['from_date']), DBdateformat($params['to_date'])]);
        } elseif (isset($params['from_date'])) {
            $query->where('created_at', '>=', DBdateformat($params['from_date']));
        } elseif (isset($params['to_date'])) {
            $query->where('created_at', '<=', DBdateformat($params['to_date']));
        }

        if (CheckUserRole(ROLE_ADMIN) || CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_HEAD)) {
            // No additional restrictions for these roles
        } elseif (CheckUserRole(ROLE_TRAINER)) {
            $trainer = DB::table('masters_employee')
                ->select('id', 'emp_id')
                ->where('emp_id', Auth::user()->employee_id)
                ->first();
            if ($trainer) {
                $query->where('trainer_id', $trainer->id);
            }
        } else {
            $nomination = DB::table('masters_employee')
                ->select('id', 'emp_id')
                ->where('emp_id', Auth::user()->employee_id)
                ->first();
            if ($nomination) {
                $query->whereExists(function ($subQuery) use ($nomination) {
                    $subQuery->select(DB::raw(1))
                        ->from('training_nomination_process')
                        ->whereColumn('training_nomination_process.training_schedule_id', 'training_schedule.id')
                        ->where('training_nomination_process.employee_id', $nomination->id);
                });
            }
        }

        if (!empty($type)) {
            if (is_array($type)) {
                $query->whereIn('training_status', $type);
            } else {
                $query->where('training_status', $type);
            }
        }

        return $query->count();
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
        } elseif (CheckUserRole(ROLE_ADMIN)) {
            $query->where('training_schedule.trash', 'NO');
        } elseif (CheckUserRole(ROLE_EHS_HEAD)) {
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
            $fromDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->format('Y-m-d');
            $query = $query->where('training_schedule.from_date', '>=', $fromDate);
        }

        if ($request->has('to_date') && $request->to_date) {
            $toDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->format('Y-m-d');
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
        if ($request->has('company_id') && $request->company_id) {
            $query = $query->where('training_schedule.company_id', decryptId($request->company_id));
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

        $data = $this->select('training_schedule.*', 'masters_unit.unit_name', 'masters_employee.emp_name', 'masters_employee.login_id', 'masters_employee.email', 'masters_department.department_name', 'training_masters_topic.topic_name', 'training_masters_venue.name_of_the_conference_hall')->leftJoin('masters_unit', 'training_schedule.unit_id', '=', 'masters_unit.id')->leftJoin('training_masters_topic', 'training_schedule.topic_id', '=', 'training_masters_topic.id')->leftJoin('masters_department', 'training_schedule.department_id', '=', 'masters_department.id')->leftJoin('masters_employee', 'training_schedule.trainer_id', '=', 'masters_employee.id')->leftJoin('training_masters_venue', 'training_schedule.venue_id', '=', 'training_masters_venue.id')
            ->where('training_schedule.id', $id)
            ->first();

        return $data;
    }

    public function getTrainigCompletionCountData($request)
    {
        $query = DB::table('training_schedule')
            ->select(
                DB::raw('COUNT(id) as training_total_count'),
                DB::raw('SUM(CASE WHEN training_status = 8 THEN 1 ELSE 0 END) as closed_count'),
                DB::raw('SUM(CASE WHEN training_status != 8 THEN 1 ELSE 0 END) as open_count'),
                DB::raw('ROUND(SUM(CASE WHEN training_status = 8 THEN 1 ELSE 0 END) * 100.0 / COUNT(id), 2) as closed_percentage'),
                DB::raw('ROUND(SUM(CASE WHEN training_status != 8 THEN 1 ELSE 0 END) * 100.0 / COUNT(id), 2) as open_percentage')
            );

        // Apply company Filter
        if ($request->CompanyId) {
            $company_id = decryptId($request->CompanyId);
            $query->where('company_id', $company_id);
        }

        // Date Filters
        if ($request->Fromdate && $request->Todate) {
            $query->whereBetween('created_at', [
                DBdateformat($request->Fromdate),
                DBdateformat($request->Todate) . ' 23:59:59'
            ]);
        } elseif ($request->Fromdate) {
            $query->where('created_at', '>=', DBdateformat($request->Fromdate));
        } elseif ($request->Todate) {
            $query->where('created_at', '<=', DBdateformat($request->Todate) . ' 23:59:59');
        }

        return $query->first(); // only one row
    }
    public function GetTrainingHoursDepartmentData($request)
    {
        $query = DB::table('training_schedule')
            ->join('training_masters_topic', 'training_schedule.topic_id', '=', 'training_masters_topic.id')
            ->join('masters_department', 'training_schedule.department_id', '=', 'masters_department.id')
            ->select(
                'training_masters_topic.topic_name',
                'masters_department.department_name',
                'masters_department.id as department_id', // ✅ Include this
                DB::raw('ROUND(SUM(TIMESTAMPDIFF(MINUTE, start_time, end_time)) / 60, 2) as total_hours')
            )
            ->whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->groupBy('training_masters_topic.topic_name', 'masters_department.department_name', 'masters_department.id');

        // Apply company Filter
        if ($request->CompanyId) {
            $company_id = decryptId($request->CompanyId);
            $query->where('training_schedule.company_id', $company_id);
        }

        // Date Filters
        if ($request->Fromdate && $request->Todate) {
            $query->whereBetween('training_schedule.created_at', [
                DBdateformat($request->Fromdate),
                DBdateformat($request->Todate) . ' 23:59:59'
            ]);
        } elseif ($request->Fromdate) {
            $query->where('training_schedule.created_at', '>=', DBdateformat($request->Fromdate));
        } elseif ($request->Todate) {
            $query->where('training_schedule.created_at', '<=', DBdateformat($request->Todate) . ' 23:59:59');
        }

        return $query->get();
    }

    // card total of shcedule in the dashboard
    public function getTotalRecords()
    {
        $request = request();

        $query = $this->where('training_schedule.trash','No');

        if ($request->has('CompanyId') && $request->CompanyId) {
            $query->where('training_schedule.company_id', decryptId($request->CompanyId));
        }

        if ($request->has('Fromdate') && $request->Fromdate) {
            $query->where('training_schedule.created_at', '>=', DBdateformat($request->Fromdate));
        }

        if ($request->has('Todate') && $request->Todate) {
            $query->where('training_schedule.created_at', '<=', DBdateformat($request->Todate));
        }

        return $query->count();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('training_schedule'));
    }
}
