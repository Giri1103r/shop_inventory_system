<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NominationProcess extends Model
{
    use  HasFactory;


    protected $table = 'training_nomination_process';
    protected $primaryKey = 'id';

    protected $fillable = [
        'training_schedule_id',
        'employee_id',
        'emp_name',
        'email',
        'department_id',
        'employee_type',
        'last_training_attended_on',
        'topic_id',
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
        $search = $request->search['value'] ?? null;

        $query = $this->select(
            'training_nomination_process.*',
            'masters_employee.emp_id',
            'masters_employee.emp_name',
            'masters_employee.email',
            'masters_department.department_name',
            'training_masters_topic.topic_name'
        )
            ->leftJoin('masters_employee', 'training_nomination_process.employee_id', '=', 'masters_employee.id')
            ->leftJoin('masters_department', 'training_nomination_process.department_id', '=', 'masters_department.id')
            ->leftJoin('training_masters_topic', 'training_nomination_process.topic_id', '=', 'training_masters_topic.id');

        $org_total_counts = $query->count();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->orWhere('masters_employee.emp_id', 'LIKE', "%{$search}%")
                    ->orWhere('masters_employee.emp_name', 'LIKE', "%{$search}%")
                    ->orWhere('masters_employee.email', 'LIKE', "%{$search}%")
                    ->orWhere('masters_department.department_name', 'LIKE', "%{$search}%")
                    ->orWhere('training_masters_topic.topic_name', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('employee_type') && $request->employee_type) {
            $query->where('employee_type', 'LIKE', "%{$request->employee_type}%");
        }

        if ($request->has('email') && $request->email) {
            $query->where('masters_employee.email', 'LIKE', "%{$request->email}%");
        }

        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('training_nomination_process.employee_id', decryptId($request->employee_id));
        }

        if ($request->has('emp_name') && $request->emp_name) {
            $query->where('masters_employee.emp_name', 'LIKE', "%{$request->emp_name}%");
        }
        if ($request->has('last_training_attended_on') && $request->last_training_attended_on) {
            $query->whereDate('training_nomination_process.last_training_attended_on', '=', DBdateformat($request->last_training_attended_on));
        }

        if ($request->has('status') && $request->status) {
            $query->where('training_nomination_process.status', decryptId($request->status));
        }


        $total_records = $query->count();

        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }

        $query->orderBy('id', 'DESC');

        $data = $query->get();

        $datas = array(
            'data' => $data,
            'total_records' => $org_total_counts,
            'filter_records' => $total_records,
        );
        return $datas;
    }

    public function storeOrUpdate()
    {
        $request = request();
        $employees = $request->input('employee');

        if (!empty($employees) && is_array($employees)) {
            foreach ($employees as $employeeData) {
                if (!empty($employeeData)) {
                    if (empty($employeeData['id'])) {
                        $insertArray = [
                            'training_schedule_id' => decryptId($request->training_schedule_id),
                            'employee_id' => $employeeData['emp_id'],
                            'emp_name' => $employeeData['emp_name'],
                            'email' => $employeeData['email'],
                            'department_id' => $employeeData['department_id'],
                            'employee_type' => $employeeData['employee_type'],
                            'last_training_attended_on' => DBdateformat($employeeData['last_training_attended_on']),
                            'topic_id' => decryptId($employeeData['topic_id']),
                            'created_by' => Auth::id(),
                        ];
                        $this->create($insertArray);
                    } else {
                        $trainingScheduleId = decryptId($request->training_schedule_id);

                        $conditions = [
                            'id' => $employeeData['id'],
                            'training_schedule_id' => $trainingScheduleId,
                        ];

                        $updateArray = [
                            'employee_id' => $employeeData['emp_id'],
                            'emp_name' => $employeeData['emp_name'],
                            'email' => $employeeData['email'],
                            'department_id' => $employeeData['department_id'],
                            'employee_type' => $employeeData['employee_type'],
                            'last_training_attended_on' => DBdateformat($employeeData['last_training_attended_on']),
                            'topic_id' => decryptId($employeeData['topic_id']),
                            'updated_by' => Auth::id(),
                        ];

                        $this->where($conditions)->update($updateArray);
                    }
                }
            }
        }
    }


    public function getNomination($training_schedule)
    {
        $data = $this->select('training_nomination_process.*', 'masters_employee.emp_id',  'masters_department.department_name', 'training_masters_topic.topic_name', 'training_schedule.from_date', 'training_schedule.to_date', 'training_schedule.venue_id', 'training_masters_venue.name_of_the_conference_hall')->leftJoin('masters_employee', 'training_nomination_process.employee_id', '=', 'masters_employee.id')
            ->leftJoin('masters_department', 'training_nomination_process.department_id', '=', 'masters_department.id')
            ->leftJoin('training_masters_topic', 'training_nomination_process.topic_id', '=', 'training_masters_topic.id')
            ->leftJoin('training_schedule', 'training_nomination_process.training_schedule_id', '=', 'training_schedule.id')
            ->leftJoin('training_masters_venue', 'training_schedule.venue_id', '=', 'training_masters_venue.id')
            ->where('training_nomination_process.status', 1)->where('training_nomination_process.training_schedule_id', $training_schedule)
            ->get();
        return $data;
    }

    public function selectOne($id)
    {

        $data = $this->select(
            'training_nomination_process.*',
            'masters_employee.emp_id',
            'masters_employee.emp_name',
            'masters_employee.email',
            'masters_department.department_name',
            'training_masters_topic.topic_name'
        )
            ->leftJoin('masters_employee', 'training_nomination_process.employee_id', '=', 'masters_employee.id')
            ->leftJoin('masters_department', 'training_nomination_process.department_id', '=', 'masters_department.id')
            ->leftJoin('training_masters_topic', 'training_nomination_process.topic_id', '=', 'training_masters_topic.id')
            ->where('training_nomination_process.id', $id)
            ->first();

        return $data;
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

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select(
            'training_nomination_process.*',
            'masters_employee.emp_id',
            'masters_employee.emp_name',
            'masters_employee.email',
            'masters_department.department_name',
            'training_masters_topic.topic_name'
        )
            ->leftJoin('masters_employee', 'training_nomination_process.employee_id', '=', 'masters_employee.id')
            ->leftJoin('masters_department', 'training_nomination_process.department_id', '=', 'masters_department.id')
            ->leftJoin('training_masters_topic', 'training_nomination_process.topic_id', '=', 'training_masters_topic.id');
        if (!empty($request->search)) {
            $query->where(function ($q) use ($search) {
                $q->orWhere('masters_employee.emp_id', 'LIKE', "%{$search}%")
                    ->orWhere('masters_employee.emp_name', 'LIKE', "%{$search}%")
                    ->orWhere('masters_employee.email', 'LIKE', "%{$search}%")
                    ->orWhere('masters_department.department_name', 'LIKE', "%{$search}%")
                    ->orWhere('training_masters_topic.topic_name', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('employee_type') && $request->employee_type) {
            $query->where('employee_type', 'LIKE', "%{$request->employee_type}%");
        }

        if ($request->has('email') && $request->email) {
            $query->where('masters_employee.email', 'LIKE', "%{$request->email}%");
        }

        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('training_nomination_process.employee_id', decryptId($request->employee_id));
        }

        if ($request->has('emp_name') && $request->emp_name) {
            $query->where('masters_employee.emp_name', 'LIKE', "%{$request->emp_name}%");
        }
        if ($request->has('last_training_attended_on') && $request->last_training_attended_on) {
            $query->whereDate('training_nomination_process.last_training_attended_on', '=', DBdateformat($request->last_training_attended_on));
        }

        if ($request->has('status') && $request->status) {
            $query->where('training_nomination_process.status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('training_nomination_process'));
    }
}
