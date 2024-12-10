<?php

namespace App\Jobs;


use DB;
use Str;
use Mail;
use App\Models\User;
use Shuchkin\SimpleXLSX;
use App\Models\UploadLog;
use Illuminate\Bus\Queueable;
use App\Models\Master\Factory;

use App\Models\UploadLogError;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


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
use Illuminate\Support\Facades\Session;

// class ImportTrainingMatrixJob implements ShouldQueue
class ImportTrainingMatrixJob
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
        // dd($xlsx);
        $cond_error_datas = [];

        foreach ($xlsx->rows() as $row) {

            if ($i == 1) {

                if (count($row) == 8) {
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

                // Validate header column names
                if (
                    trim($row['0']) != 'SNo' ||
                    trim($row['1']) != 'Training Topic' ||
                    trim($row['2']) != 'Trainer' ||
                    trim($row['3']) != 'Training Offered for' ||
                    trim($row['4']) != 'Unit' ||
                    trim($row['5']) != 'Target Department' ||
                    trim($row['6']) != 'Mode of training' ||
                    trim($row['7']) != 'Training Evaluation'
                ) {
                    $error_data = array(
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Header Column Name Not Match',
                    );
                    $cond_error_datas[] = $error_data;
                    break;
                }

                $i++;
                continue;
            }

            $sno = trim($row['0']);
            $topic_name = trim($row['1']);
            $trainer_name = trim($row['2']);
            $training_offered_for = trim($row['3']);
            $unit_name = trim($row['4']);
            $department_name = trim($row['5']);
            $mode_of_training = trim($row['6']);
            $training_evaluation = trim($row['7']);

            /* Column data validation */
            if ($topic_name == '') {
                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Training Topic is missing',
                );
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }

            if (empty($trainer_name)) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Trainer is missing',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }

            if (empty($training_offered_for)) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Training Offered is missing',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }

            if (strtolower($training_offered_for) == 'worker') {
                $training_offered_for_value = 1;
            } elseif (strtolower($training_offered_for) == 'executive') {
                $training_offered_for_value = 2;
            } else {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Training Offered for must be "Worker" or "Executive"',
                ];
                $i++;
                continue;
            }
            if (empty($unit_name)) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Unit is missing',
                ];
                $i++;
                continue;
            }

            if (empty($department_name)) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Target Department is missing',
                ];
                $i++;
                continue;
            }

            if (strtolower($mode_of_training) == 'online') {
                $mode_of_training_value = 1;
            } elseif (strtolower($mode_of_training) == 'offline') {
                $mode_of_training_value = 2;
            } else {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Mode of training must be "Online" or "Offline"',
                ];
                $i++;
                continue;
            }

            if (strtolower($training_evaluation) == 'yes') {
                $training_evaluation_value = 1;
            } elseif (strtolower($training_evaluation) == 'no') {
                $training_evaluation_value = 2;
            } else {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Training Evaluation must be "Yes" or "No"',
                ];
                $i++;
                continue;
            }

            if (empty($mode_of_training)) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Mode of training is missing',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }
            if (empty($training_evaluation)) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Training Evaluation is missing',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }
            $topicExist = Topic::where('topic_name', $topic_name)->first();
            // if ($topicExist->isNotEmpty()) {
            //     $error_data = array(
            //         'upload_id' => $this->details['log_id'],
            //         'line_no' => $i,
            //         'error' => 'Training Topic Already Exist',
            //     );
            //     $cond_error_datas[] = $error_data;
            //     $i++;
            //     continue;
            // } 
            if (!$topicExist) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Training Topic not found',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }

            $trainerExist = Employee::where('emp_name', $trainer_name)->where('user_role', 8)->get();

            // if ($trainerExist->isNotEmpty()) {
            //     $error_data = array(
            //         'upload_id' => $this->details['log_id'],
            //         'line_no' => $i,
            //         'error' => 'Trainer Already Exist',
            //     );
            //     $cond_error_datas[] = $error_data;
            //     $i++;
            //     continue;
            // }
            if (!$trainerExist) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Trainer not found',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }


            $unitExist = Unit::where('unit_name', $unit_name)->get();

            // if ($unitExist->isNotEmpty()) {
            //     $error_data = array(
            //         'upload_id' => $this->details['log_id'],
            //         'line_no' => $i,
            //         'error' => 'Unit Already Exist',
            //     );
            //     $cond_error_datas[] = $error_data;
            //     $i++;
            //     continue;
            // }
            if (!$unitExist) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Unit not found',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }

         
            // $unitsExist = Unit::select('id')->where('unit_name', $unit_name)->first();

            // if ($unitsExist) {
            //     $deptExist = Department::where('department_name', $department_name)
            //         ->where('unit_id', $unitsExist->id)
            //         ->exists();

            //     if ($deptExist) {
            //         $cond_error_data = [
            //             'upload_id' => $this->details['log_id'],
            //             'line_no' => $i,
            //             'error' => 'Department Already Exists for this Unit',
            //         ];
            //         $cond_error_datas[] = $cond_error_data;
            //         $i++;
            //         continue;
            //     }
            // } else {
            //     $cond_error_data = [
            //         'upload_id' => $this->details['log_id'],
            //         'line_no' => $i,
            //         'error' => 'Unit not found',
            //     ];
            //     $cond_error_datas[] = $cond_error_data;
            //     $i++;
            //     continue;
            // }

            $data = [
                'topic_name' => $topic_name,
                'trainer_name' => $trainer_name,
                'training_offered_for' => $training_offered_for_value,
                'unit_name' => $unit_name,
                'department_name' => $department_name,
                'mode_of_training' => $mode_of_training_value,
                'training_evaluation' => $training_evaluation_value,
                'created_by' => $this->details['user_id'],
            ];

            TrainingMatrix::create($data);
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
