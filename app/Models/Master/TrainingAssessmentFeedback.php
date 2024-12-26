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
        'assessment',
        'feedback',
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


    // public function store()
    // {
    //     $request = request();
    //     $insertData = [];

    //     foreach ($request->attendance_id as $index => $attendanceId) {
    //         $insertData[] = [
    //             'training_schedule_id' => decryptId($request->training_schedule_id),
    //             'attendance_id' => $attendanceId,
    //             'emp_name' => $request->emp_name[$index],
    //             'assessment' => $request->assessment[$index], // Indexed value
    //             'feedback' => $request->feedback[$index],    // Indexed value
    //             'status' => 1,
    //             'created_by' => Auth::id(),
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ];
    //     }

    //     return $this->insert($insertData);
    // }

    public function store()
    {
        $request = request();
        $insertData = [];

        foreach ($request->attendance_id as $index => $attendanceId) {
            $trainingScheduleId = decryptId($request->training_schedule_id);

            // Check if the combination of attendance_id and emp_name already exists
            $exists = $this->where('training_schedule_id', $trainingScheduleId)
                ->where('attendance_id', $attendanceId)
                ->where('emp_name', $request->emp_name[$index])
                ->exists();

            if (!$exists) {
                $insertData[] = [
                    'training_schedule_id' => $trainingScheduleId,
                    'attendance_id' => $attendanceId,
                    'emp_name' => $request->emp_name[$index],
                    'assessment' => $request->assessment[$index], // Indexed value
                    'feedback' => $request->feedback[$index],    // Indexed value
                    'status' => 1,
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert only unique data
        if (!empty($insertData)) {
            return $this->insert($insertData);
        }

        return true; // Return true if no new data was inserted
    }


    public function getAttendanceList($training_schedule)
    {
        $data = $this->select('training_assessment_feedback.*', 'training_masters_topic.topic_name', 'training_schedule.from_date', 'training_schedule.to_date', 'training_schedule.venue_id', 'training_masters_venue.name_of_the_conference_hall')
            ->leftJoin('training_masters_topic', 'training_assessment_feedback.topic_id', '=', 'training_masters_topic.id')
            ->leftJoin('training_nomination_process', 'training_assessment_feedback.nomination_id', '=', 'training_nomination_process.id')
            ->leftJoin('training_schedule', 'training_assessment_feedback.training_schedule_id', '=', 'training_schedule.id')
            ->leftJoin('training_masters_venue', 'training_schedule.venue_id', '=', 'training_masters_venue.id')
            ->where('training_assessment_feedback.status', 1)->where('training_assessment_feedback.training_schedule_id', $training_schedule)
            ->get();
        return $data;
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('training_assessment_feedback'));
    }
}
