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
                    'mark' => $request->mark[$index], 
                    'assessment' => $request->assessment[$index], 
                    'feedback' => $request->feedback[$index],   
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
    public function getemployee($trainingScheduleId)
    {
        return $this->select('training_assessment_feedback.*', 'masters_employee.emp_id', 'masters_employee.login_id')
            ->leftJoin('masters_employee', 'training_assessment_feedback.email', '=', 'masters_employee.email')
            ->where('training_assessment_feedback.status', 1)
            ->where('training_assessment_feedback.training_schedule_id', $trainingScheduleId)
            ->where('training_assessment_feedback.attended_status', 1)
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
