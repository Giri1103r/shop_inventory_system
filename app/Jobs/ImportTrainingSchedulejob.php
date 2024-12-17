<?php

namespace App\Jobs;


use Illuminate\Foundation\Queue\Queueable;


use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;


use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


use App\Models\Master\Unit;
use App\Models\Master\Department;
use App\Models\Master\Topic;
use App\Models\Master\Employee;
use App\Models\Master\TrainingMatrix;
use App\Models\Master\TrainingSchedule;
use App\Models\Master\Venue;
use App\Models\UploadLog;
use App\Models\UploadLogError;
use DateTime;
use Illuminate\Support\Facades\Session;
use Shuchkin\SimpleXLSX;

// class ImportTrainingSchedulejob
class ImportTrainingSchedulejob implements ShouldQueue

{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $details;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details)
    {


        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $i = 1;
        $update_array = array(
            'upload_status' => 1,
        );
        UploadLog::where('id', $this->details['log_id'])
            ->update($update_array);

        $xlsx = SimpleXLSX::parse($this->details['path']);

        $cond_error_datas = [];

        foreach ($xlsx->rows() as $row) {
            if ($i == 1) {
                if (count($row) === 9) {
                } else {
                    $error_data = array(
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Header Column not match',
                    );
                    $cond_error_datas[] = $error_data;
                    $i++;
                    break;
                }
                if (
                    trim($row['0']) != 'SNo' ||
                    trim($row['1']) != 'From Date' ||
                    trim($row['2']) != 'To Date' ||
                    trim($row['3']) != 'Topic Name' ||
                    trim($row['4']) != 'Trainer Name' ||
                    trim($row['5']) != 'Unit' ||
                    trim($row['6']) != 'Department' ||
                    trim($row['7']) != 'Target Trainees' ||
                    trim($row['8']) != 'Venue'
                ) {
                    $error_data = array(
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Header Column Name Not Match',
                    );
                    $cond_error_datas[] = $error_data;
                    $i++;
                    continue;
                }

                $i++;
                continue;
            }

            $sno = trim($row['0']);
            $from_date = trim($row['1']);
            $to_date = trim($row['2']);
            $topic_name = trim($row['3']);
            $trainer_name = trim($row['4']);
            $unit_name = trim($row['5']);
            $department_name = trim($row['6']);
            $target_trainee = trim($row['7']);
            $venue = trim($row['8']);

            $fromDate = DateTime::createFromFormat('Y-m-d H:i:s', $from_date);
            $formattedFromDate = $fromDate->format('Y-m-d');

            $toDate = DateTime::createFromFormat('Y-m-d H:i:s', $to_date);
            $formattedToDate = $toDate->format('Y-m-d');

            if ($formattedFromDate && $formattedToDate) {
                if ( $formattedToDate >= $formattedFromDate ) {
                } else {
                    $cond_error_data = array(
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'To Date cannot be earlier than From Date',
                    );
                    $cond_error_datas[] = $cond_error_data;
                    $i++;
                    continue;
                }
            } else {
                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Invalid date format',
                );
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }


            $topicId = Topic::where('topic_name', $topic_name)->pluck('id')->first();

            if (empty($topicId)) {
                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Training Topic is missing',
                );
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }

            $venueId = Venue::where('name_of_the_conference_hall', $venue)->pluck('id')->first();

            if (empty($venueId)) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Venue not found',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }

            $trainerId = Employee::where('emp_name', $trainer_name)->pluck('id')->first();

            if (empty($trainerId)) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Trainer is missing',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }

            $unitId = Unit::where('unit_name', $unit_name)->pluck('id')->first();
            $departmentId = Department::where('department_name', $department_name)->pluck('id')->first();

            if (empty($unitId)) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Unit is missing',
                ];
                $i++;
                continue;
            }

            if (empty($departmentId)) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Target Department is missing',
                ];
                $i++;
                continue;
            }

            $unitDepartment = Department::where('unit_id', $unitId)->where('department_name', $department_name)->exists();
            if (!$unitDepartment) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'For Specified Unit Department Not',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }

            $existingSchedule = TrainingSchedule::where('topic_id', $topicId)
                ->where('trainer_id', $trainerId)
                ->where('venue_id', $venueId)
                ->where('unit_id', $unitId)
                ->where('department_id',$departmentId)
                ->where('from_date', '<=', $toDate)
                ->where('to_date', '>=', $fromDate)
                ->exists();

            if ($existingSchedule) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Schedule conflict: For the Sheduled date Topic Name and Trainer, Unit, Department, Venue  is already taken ',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }

            if (!ctype_digit($target_trainee) || intval($target_trainee) < 0) {
                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Target Trainee must be a non-negative integer',
                );
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }



            $data = [
                'topic_id' => $topicId,
                'trainer_id' => $trainerId,
                'from_date' => $fromDate->format('Y-m-d'),
                'to_date' => $toDate->format('Y-m-d'),
                'target_trainees' => $target_trainee,
                'venue_id' => $venueId,
                'unit_id' => $unitId,
                'department_id' => $departmentId,
                'created_by' => $this->details['user_id'],
            ];

            TrainingSchedule::create($data);
            $i++;
        }

        if (count($cond_error_datas) > 0) {
            UploadLogError::insert($cond_error_datas);
            $final_update_array = array(
                'upload_status' => 3,
            );
            Session::flash('error', 'Failed to upload. Please check the upload log.');
        } else {
            $final_update_array = array(
                'upload_status' => 2,
            );
            Session::flash('success', 'Upload completed successfully.');
        }

        UploadLog::where('id', $this->details['log_id'])->update($final_update_array);
    }

}

