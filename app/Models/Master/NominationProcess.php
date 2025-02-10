<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;
use Exception;

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
        'emp_worker',
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

    // public function storeOrUpdate()
    // {
    //     $request = request();
    //     $employees = $request->input('employee');
    //     if (!empty($employees) && is_array($employees)) {
    //         foreach ($employees as $employeeData) {
    //             $topic_id = DB::table('training_masters_topic')->select('id')
    //                 ->where('topic_name', $employeeData['last_training_topic'])
    //                 ->first();


    //             $lastTrainingAttendedOn = ($employeeData['last_training_attended_on'] === 'No data' || empty($employeeData['last_training_attended_on']))
    //                 ? null
    //                 : DBdateformat($employeeData['last_training_attended_on']);

    //             $lastTrainingTopic = ($employeeData['last_training_topic'] === 'No data') ? null : $employeeData['last_training_topic'];
    //             if (!empty($employeeData)) {
    //                 if (empty($employeeData['id'])) {
    //                     $insertArray = [
    //                         'training_schedule_id' => decryptId($request->training_schedule_id),
    //                         'emp_worker' => $employeeData['emp_worker'],
    //                         'employee_id' => $employeeData['emp_id'],
    //                         'emp_name' => $employeeData['emp_name'],
    //                         'email' => $employeeData['email'],
    //                         'department_id' => $employeeData['department_id'],
    //                         'employee_type' => $employeeData['employee_type'],
    //                         'last_training_attended_on' => $lastTrainingAttendedOn,
    //                         'topic_id' => $topic_id->id ?? null,
    //                         'created_by' => Auth::id(),
    //                     ];
    //                     $this->create($insertArray);
    //                 } else {
    //                     $trainingScheduleId = decryptId($request->training_schedule_id);
    //                     dd($employeeData, $trainingScheduleId, $employeeData['id']);
    //                     $conditions = [
    //                         'id' => $employeeData['id'],
    //                         'training_schedule_id' => $trainingScheduleId,
    //                     ];

    //                     $updateArray = [
    //                         'employee_id' => $employeeData['emp_id'],
    //                         'emp_worker' => $employeeData['emp_worker'],
    //                         'emp_name' => $employeeData['emp_name'],
    //                         'email' => $employeeData['email'],
    //                         'department_id' => $employeeData['department_id'],
    //                         'employee_type' => $employeeData['employee_type'],
    //                         'last_training_attended_on' => $lastTrainingAttendedOn ?? null,
    //                         'topic_id' => $topic_id->id ?? null,
    //                         'updated_by' => Auth::id(),
    //                     ];
    //                     $this->where($conditions)->update($updateArray);
    //                 }
    //             }
    //         }
    //     }
    // }
    public function storeOrUpdate()
    {
        $request = request();
        $employees = $request->input('employee');

        if (!empty($employees) && is_array($employees)) {
            foreach ($employees as $employeeData) {
                try {
                    $trainingScheduleId = decryptId($request->training_schedule_id);

                    // Retrieve topic ID if provided
                    $topic_id = null;
                    if (!empty($employeeData['last_training_topic']) && $employeeData['last_training_topic'] !== 'No Data') {
                        $topicRecord = DB::table('training_masters_topic')->select('id')
                            ->where('topic_name', $employeeData['last_training_topic'])
                            ->first();
                        $topic_id = $topicRecord->id ?? null;
                    }

                    // Format last training attended date
                    $lastTrainingAttendedOn = null;
                    if (!empty($employeeData['last_training_attended_on']) && $employeeData['last_training_attended_on'] !== 'No Data') {
                        $lastTrainingAttendedOn = DBdateformat($employeeData['last_training_attended_on']);
                    }

                    // Prepare data for insert/update
                    $data = [
                        'training_schedule_id' => $trainingScheduleId,
                        'emp_worker' => $employeeData['emp_worker'],
                        'employee_id' => $employeeData['emp_id'],
                        'emp_name' => $employeeData['emp_name'],
                        'email' => $employeeData['email'],
                        'department_id' => $employeeData['department_id'],
                        'employee_type' => $employeeData['employee_type'],
                        'last_training_attended_on' => $lastTrainingAttendedOn ?? '',
                        'topic_id' => $topic_id ?? '',
                    ];
    dd($data);
                    // Check if updating or inserting a new record
                    if (empty($employeeData['id'])) {
                        // Insert new record
                        $data['created_by'] = Auth::id();
                        $this->create($data);
                    } else {
                        $this->where('id', $employeeData['id'])
                            ->where('training_schedule_id', $trainingScheduleId)
                            ->update(array_merge($data, ['updated_by' => Auth::id()]));
                    }
                } catch (Exception $e) {
                    \Log::error('Error in storeOrUpdate: ' . $e->getMessage());
                }
            }
        }
    }



    public function getNomination($training_schedule)
    {
        return $this->select(
            'training_nomination_process.*',
            DB::raw('CASE 
                    WHEN training_nomination_process.emp_worker = 1 THEN masters_employee.emp_id 
                    WHEN training_nomination_process.emp_worker = 2 THEN masters_work.emp_id 
                    ELSE NULL 
                END AS emp_id'),
            'masters_employee.id as emp_master_id',
            'masters_work.id as worker_id',
            'masters_employee.login_id',
            'masters_department.department_name',
            'training_masters_topic.topic_name',
            'training_schedule.from_date',
            'training_schedule.to_date',
            'training_schedule.venue_id',
            'training_schedule.start_time',
            'training_schedule.end_time',
            'training_masters_venue.name_of_the_conference_hall'
        )
            ->leftJoin('masters_work', 'training_nomination_process.employee_id', '=', 'masters_work.id')
            ->leftJoin('masters_employee', 'training_nomination_process.employee_id', '=', 'masters_employee.id')
            ->leftJoin('masters_department', 'training_nomination_process.department_id', '=', 'masters_department.id')
            ->leftJoin('training_masters_topic', 'training_nomination_process.topic_id', '=', 'training_masters_topic.id')
            ->leftJoin('training_schedule', 'training_nomination_process.training_schedule_id', '=', 'training_schedule.id')
            ->leftJoin('training_masters_venue', 'training_schedule.venue_id', '=', 'training_masters_venue.id')
            ->where('training_nomination_process.training_schedule_id', $training_schedule)
            ->get();
    }


    public function selectOne($id)
    {

        $data = $this->select(
            'training_nomination_process.*',
            'masters_work.emp_id',
            'masters_work.emp_name',
            'masters_employee.emp_id',
            'masters_employee.emp_name',
            'masters_employee.email',
            'masters_department.department_name',
            'training_masters_topic.topic_name'
        )
            ->leftJoin('masters_work', 'training_nomination_process.employee_id', '=', 'masters_work.id')
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
            'masters_work.emp_id',
            'masters_work.emp_name',
            'masters_employee.emp_id',
            'masters_employee.emp_name',
            'masters_employee.email',
            'masters_department.department_name',
            'training_masters_topic.topic_name'
        )
            ->leftJoin('masters_work', 'training_nomination_process.employee_id', '=', 'masters_work.id')
            ->leftJoin('masters_employee', 'training_nomination_process.employee_id', '=', 'masters_employee.id')
            ->leftJoin('masters_department', 'training_nomination_process.department_id', '=', 'masters_department.id')
            ->leftJoin('training_masters_topic', 'training_nomination_process.topic_id', '=', 'training_masters_topic.id');
        if (!empty($request->search)) {
            $query->where(function ($q) use ($search) {
                $q->orWhere('masters_employee.emp_id', 'LIKE', "%{$search}%")
                    ->orWhere('masters_work.emp_id', 'LIKE', "%{$search}%")
                    ->orWhere('masters_work.emp_name', 'LIKE', "%{$search}%")
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
            $query->where('masters_work.emp_name', 'LIKE', "%{$request->emp_name}%");
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
