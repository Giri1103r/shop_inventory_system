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
use App\Models\Master\TrainingAttendance;
use App\Models\Master\TrainingStatuslog;


use App\Models\Master\TrainingSchedule;
use App\Models\Master\NominationProcess;
use App\Models\Master\Department;
use App\Models\Master\Work;
use App\Models\Master\Employee;
use App\Models\Master\Topic;
use Illuminate\Support\Facades\Session;

class ImporNominationProcessJob  implements ShouldQueue
// class ImporNominationProcessJob
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
        $data_count = 0;
        foreach ($xlsx->rows() as $row) {

            /*
         * Header column validation
         */

            if ($i == 1) {

                if (count($row) === 4) {
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
                    trim($row['1']) != 'Department' ||
                    trim($row['2']) != 'Employee/Worker' ||
                    trim($row['3']) != 'Employee/Worker ID'
                ) {
                    $error_data_1 = array(
                        'upload_id' => $this->details['log_id'],
                        'line_no' =>  $i,
                        'error' => 'Header Column Name Not Match',
                    );
                    $cond_error_datas[] = $error_data_1;
                    $i++;
                    break;
                }
                $i++;
                continue;
            }

            $sno = trim($row['0']);
            $department_id = trim($row['1']);
            $emp_worker = strtolower(trim($row[2]));
            $emp_id = trim($row['3']);


            /* Column data validation */
            if (empty($department_id)) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Department is missing',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }
            if ($emp_worker == '') {

                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Employee/Worker is missing',
                );

                $cond_error_datas[] = $cond_error_data;

                $i++;
                continue;
            }
            if ($emp_id == '') {

                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Employee/Worker ID is missing',
                );

                $cond_error_datas[] = $cond_error_data;

                $i++;
                continue;
            }
            // Convert Employee/Worker text to numeric values
            if (in_array($emp_worker, ['employee'])) {
                $emp_worker = 1;
            } elseif (in_array($emp_worker, ['worker'])) {
                $emp_worker = 2;
            } else {
                $cond_error_datas[] = ['upload_id' => $this->details['log_id'], 'line_no' => $i, 'error' => 'Invalid Employee/Worker Type'];
                $i++;
                continue;
            }




            // Validate employee existence
            if ($emp_worker == 1) { // Employee
                $empExist = Employee::select(
                    'id',
                    'emp_id',
                    'emp_name',
                    'email',
                    'employee_status',
                    'department'
                )
                    ->where('user_role', '!=', 1)
                    ->where('user_role', '!=', 10)
                    ->where('user_role', '!=', 2)
                    ->where('status', 1)
                    ->where('emp_id', $emp_id)
                    ->where('id', '!=', $this->details['trainerId'])
                    ->first();
            } else { // Worker
                $empExist = Work::select('id', 'emp_id', 'emp_name', 'wfemptype', 'department')
                    ->where('status', 1)
                    ->where('emp_id', $emp_id)
                    ->first();
            }

            // Validate department
            $deptExist = Department::select('id', 'department_name')
                ->where('id', $empExist->department)
                ->where('status', 1)
                ->first();

            if (!$deptExist) {
                $cond_error_datas[] = ['upload_id' => $this->details['log_id'], 'line_no' => $i, 'error' => 'Invalid Department'];
                $i++;
                continue;
            }

            if (!$empExist) {
                $cond_error_datas[] = ['upload_id' => $this->details['log_id'], 'line_no' => $i, 'error' => 'Invalid Employee ID'];
                $i++;
                continue;
            }

            // Check for existing nomination
            $nominationProcessExist = NominationProcess::where('training_schedule_id', $this->details['trainingScheduleIid'])
                ->where('department_id', $deptExist->id)
                ->where('employee_id', $empExist->id)
                ->where('status', 1)
                ->first();

            if ($nominationProcessExist) {
                $cond_error_datas[] = ['upload_id' => $this->details['log_id'], 'line_no' => $i, 'error' => 'Nomination Process for this Department-based Employee ID already exists'];
                $i++;
                continue;
            }

            // Fetch last training details
            $lastTraining = TrainingAttendance::select('training_masters_topic.id', 'training_masters_topic.topic_name', 'training_attendance.attendance_date')
                ->leftJoin('training_masters_topic', 'training_attendance.topic_id', '=', 'training_masters_topic.id')
                ->where('training_attendance.emp_id',$empExist->emp_id)
                ->where('training_attendance.attendance_status', 1)
                ->orderBy('training_attendance.attendance_date', 'desc')
                ->first();


            $lastTrainingDate = optional($lastTraining)->attendance_date ?? null;
            $lastTrainingTopic = optional($lastTraining)->id ?? null;

            $employeeType = ($emp_worker == 1) ? ($empExist->employee_status ?? null) : ($empExist->wfemptype ?? null);


            // Create nomination process
            $data = [
                'training_schedule_id' => $this->details['trainingScheduleIid'],
                'emp_worker' => $emp_worker,
                'employee_id' => $empExist->id,
                'emp_name' => $empExist->emp_name,
                'email' => $empExist->email ?? '',
                'department_id' => $deptExist->id,
                'employee_type' => $employeeType,
                'last_training_attended_on' => $lastTrainingDate,
                'topic_id' => $lastTrainingTopic,
                'created_by' => $this->details['user_id'],
            ];
            $nomination = NominationProcess::create($data);
            dd($nomination);
            $data_count++;
            $i++;
        }

        if ($data_count > 50) {
            $cond_error_datas[] = [
                'upload_id' => $this->details['log_id'],
                'line_no' => 0,
                'error' => 'The total number of rows (excluding header) must not exceed 50.',
            ];
        }

        // Log errors or mark as successful
        if (count($cond_error_datas) > 0) {
            UploadLogError::insert($cond_error_datas);
            UploadLog::where('id', $this->details['log_id'])->update(['upload_status' => 3]);
            Session::flash('error', 'Failed to upload. Please check the upload log.');
        } else {
            $training_status = TRAINING_NOMINATION_COMPLETED;
            TrainingSchedule::where('id', $this->details['trainingScheduleIid'])->update([
                'training_status' => $training_status,
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ]);

            TrainingStatuslog::where('id', $this->details['trainingScheduleIid'])->update([
                'training_schedule_id' => $this->details['trainingScheduleIid'],
                'training_status' => $training_status,
                'remarks' => null,
                'created_by' => Auth::id(),
            ]);
            UploadLog::where('id', $this->details['log_id'])->update(['upload_status' => 2]);
            Session::flash('success', 'Upload completed successfully.');
        }
    }
}
