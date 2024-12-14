<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrainingAttendance extends Model
{
    use  HasFactory;


    protected $table = 'training_attendance';
    protected $primaryKey = 'id';

    protected $fillable = [
        'training_schedule_id',
        'nomination_id',
        'emp_name',
        'from_date',
        'to_date',
        'topic_id',
        'attendance_date',
        'attendance_status',
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



    public function storeOrUpdate()
    {
        $request = request();
        $attendanceData = [];
        foreach ($request->nomination_id as $index => $nominationId) {
            $attendanceData[] = [
                'training_schedule_id' => decryptId($request->training_schedule_id),
                'nomination_id' => $nominationId,
                'attendance_date' => DBdateformat($request->attendance_date),
                'attendance_status' => $request->attendance_status[$index] ?? '0',
                'emp_name' => $request->emp_name[$index],
                'from_date' => DBdatetimeformat($request->from_date),
                'to_date' => DBdatetimeformat($request->to_date),
                'topic_id' => $request->topic_id,
                'status' => 1,
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        return  $this->insert($attendanceData);
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

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('training_attendance'));
    }
}
