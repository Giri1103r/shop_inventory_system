<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrainingFeedback extends Model
{
    use  HasFactory;


    protected $table = 'training_feedback';
    protected $primaryKey = 'id';

    protected $fillable = [
        'training_schedule_id',
        'training_assessment_feedback_id',
        'emp_id',
        'emp_worker_type',
        'emp_name',
        'trainer_feedback',
        'training_feedback',
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

        $employee = DB::table('training_attendance')
            ->select('id', 'emp_id', 'emp_name')
            ->where('emp_id', $request->emp_id)
            ->first();
        // Fetch training assessment feedback ID
        $training_assessment_feedback = DB::table('training_assessment_feedback')
            ->select('id', 'attendance_id', 'training_schedule_id')
            ->where('attendance_id', $employee->id)
            ->where('training_schedule_id', decryptId($request->training_schedule_id))
            ->first();

        $training_assessment_feedback_id = $training_assessment_feedback ? $training_assessment_feedback->id : null;

        $insert_array = [
            'training_schedule_id' => decryptId($request->training_schedule_id),
            'training_assessment_feedback_id' => $training_assessment_feedback_id ?? decryptId($request->training_assessment_feedback_id),
            'emp_worker_type' => $request->emp_worker_type,
            'emp_id' => $request->emp_id,
            'emp_name' => $employee->emp_name ?? Auth::user()->name,
            'trainer_feedback' => $request->trainer_feedback,
            'training_feedback' => $request->training_feedback,
            'created_by' => Auth::id(),
        ];
        return $this->create($insert_array);
    }

    public function getfeedbackList($id)
    {
        return $this->select('training_feedback.*')
            ->where('training_feedback.status', 1)
            ->where('training_feedback.training_assessment_feedback_id', $id)
            ->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('training_feedback'));
    }
}
