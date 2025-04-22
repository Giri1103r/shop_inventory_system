<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrainingAssessmentFeedback extends Model
{
    use  HasFactory;


    protected $table = 'training_assessment_feedback';
    protected $primaryKey = 'id';

    protected $fillable = [
        'training_schedule_id',
        'attendance_id',
        'emp_name',
        'email',
        'attended_status',
        'mark',
        'assessment',
        'feedback',
        'feedback_send_status',
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


    public function store()
    {
        $request = request();
        $insertData = [];
        foreach ($request->attendance_id as $index => $attendanceId) {
            $trainingScheduleId = decryptId($request->training_schedule_id);

            $exists = $this->where('training_schedule_id', $trainingScheduleId)
                ->where('attendance_id', $attendanceId)
                ->where('emp_name', $request->emp_name[$index])
                ->where('email', $request->email[$index])
                ->exists();
            if (!$exists) {
                $insertData[] = [
                    'training_schedule_id' => $trainingScheduleId,
                    'attendance_id' => $attendanceId,
                    'emp_name' => $request->emp_name[$index],
                    'email' => $request->email[$index],
                    'attended_status' => $request->attended_status[$index],
                    'mark' => isset($request->mark[$index]) ? $request->mark[$index] : '',
                    'assessment' => isset($request->assessment[$index]) ? $request->assessment[$index] : null,
                    'feedback' => isset($request->feedback[$index]) ? $request->feedback[$index] : null,
                    'status' => 1,
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($insertData)) {
            return $this->insert($insertData);
        }

        return true;
    }

    public function store_api()
    {
        $request = request();
        $data = $request->input('data');
        $insertData = [];
        foreach ($data as $item) {
      

            $trainingScheduleId = $item['training_schedule_id'];
            $attendanceId = $item['attendance_id'];
            $empName = $item['emp_name'];
            $email = $item['email'];
            $checked =  $item['checked'] ?? 0;
            $mark = $item['mark'] ?? '';
            $assessment = $item['assessment'] ?? '';
            $feed_back = $item['feed_back'] ?? '';


            $exists = $this->where('training_schedule_id', $trainingScheduleId)
                ->where('attendance_id', $attendanceId)
                ->where('emp_name', $empName)
                ->where('email', $email)
                ->exists();

            if (!$exists) {
                $insertData[] = [
                    'training_schedule_id' => $trainingScheduleId,
                    'attendance_id' => $attendanceId,
                    'emp_name' => $empName,
                    'email' => $email,
                    'attended_status' =>  $checked ,
                    'mark' => $mark,
                    'assessment' => $assessment,
                    'feedback' => $feed_back,
                    'status' => 1,
                    'created_by' => Auth::id(),
                ];
            }
        }

        if (!empty($insertData)) {
            return $this->insert($insertData);
        }

        return true;
    }



    public function updateStatus($trainingScheduleId)
    {
        $request = request();

        $update_array = array(
            'feedback_send_status' => 1,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        );
        $this->where('training_schedule_id', $trainingScheduleId)->update($update_array);
        return $this->where('training_schedule_id', $trainingScheduleId)->first();
    }

    public function getAssessmentList($trainingScheduleId)
    {
        return $this->select('training_assessment_feedback.*', 'masters_employee.emp_id', 'masters_employee.login_id')
            ->leftJoin('masters_employee', 'training_assessment_feedback.email', '=', 'masters_employee.email')
            ->where('training_assessment_feedback.status', 1)
            ->where('training_assessment_feedback.training_schedule_id', $trainingScheduleId)
            ->get();
    }
    public function getAssessment($trainingScheduleId)
    {
        return $this->select('training_assessment_feedback.*')
            ->where('training_assessment_feedback.status', 1)
            ->where('training_assessment_feedback.training_schedule_id', $trainingScheduleId)
            ->get();
    }
    public function getAssessmentByEmp($trainingScheduleId)
    {
        return $this->select('training_assessment_feedback.*', 'masters_employee.emp_id', 'masters_employee.login_id')
            ->leftJoin('masters_employee', 'training_assessment_feedback.email', '=', 'masters_employee.email')
            ->where('training_assessment_feedback.status', 1)
            ->where('training_assessment_feedback.training_schedule_id', $trainingScheduleId)->where('masters_employee.emp_id', Auth::user()->employee_id)
            ->get();
    }
    public function getemployee($trainingScheduleId)
    {
        return $this->select('training_assessment_feedback.*', 'masters_employee.emp_id', 'masters_employee.login_id')
            ->leftJoin('masters_employee', 'training_assessment_feedback.email', '=', 'masters_employee.email')
            ->where('training_assessment_feedback.status', 1)
            ->where('training_assessment_feedback.training_schedule_id', $trainingScheduleId)
            ->where('training_assessment_feedback.attended_status', 1)
            ->get();
    }
    public function getWorkers($training_schedule_id)
    {
        return $this->select('training_assessment_feedback.*', 'training_attendance.emp_id')
            ->leftJoin('training_attendance', 'training_assessment_feedback.attendance_id', '=', 'training_attendance.id')
            ->where('training_assessment_feedback.status', 1)
            ->where('training_assessment_feedback.attended_status', 1)
            ->where('training_assessment_feedback.training_schedule_id', $training_schedule_id)
            ->whereNull('training_assessment_feedback.email')
            ->get();
    }

    public function getempId($id)
    {
        return $this->select('training_assessment_feedback.*', 'masters_employee.emp_id')
            ->leftJoin('masters_employee', 'training_assessment_feedback.email', '=', 'masters_employee.email')
            ->where('training_assessment_feedback.status', 1)
            ->where('training_assessment_feedback.id', $id)
            ->where('training_assessment_feedback.attended_status', 1)
            ->first();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('training_assessment_feedback'));
    }
}
