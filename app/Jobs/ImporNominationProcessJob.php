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
        // dd($xlsx);
        $cond_error_datas = [];

        foreach ($xlsx->rows() as $row) {

            /*
         * Header column validation
         */

            if ($i == 1) {

                if (count($row) === 6) {
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
                    trim($row['1']) != 'Employee ID' ||
                    trim($row['2']) != 'Employee Name' ||
                    trim($row['3']) != 'Email ID' ||
                    trim($row['4']) != 'Department' ||
                    trim($row['5']) != 'Employee Type'
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
            $emp_id = trim($row['1']);
            $emp_name = trim($row['2']);
            $email = trim($row['3']);
            $department_id = trim($row['4']);
            $employee_type = trim($row['5']);
            /* Column data validation */

            if ($emp_id == '') {

                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Employee ID is missing',
                );

                $cond_error_datas[] = $cond_error_data;

                $i++;
                continue;
            }

            if (empty($emp_name)) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Employee Name is missing',
                ];

                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }

            if (empty($email)) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Email ID is missing',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }

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

            if (empty($employee_type)) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Employee Type is missing',
                ];

                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }


            $empExist = Employee::select('id', 'emp_id', 'emp_name', 'email', 'department', 'employee_status')
                ->where('emp_id', $emp_id)
                ->where('user_role', '!=', 1)
                ->where('id', '!=', $this->details['trainerId'])
                ->where('status', 1)
                ->first();

            if (!$empExist) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Invalid Employee ID',
                ];
                $i++;
                continue;
            }

            // Validate employee details
            if ($empExist->emp_name !== $emp_name) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Employee Name does not match the Employee ID',
                ];
                $i++;
                continue;
            }
            if ($empExist->email !== $email) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Email ID does not match the Employee ID',
                ];
                $i++;
                continue;
            }


            if ($empExist->employee_status !== $employee_type) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Employee Type does not match the Employee ID',
                ];
                $i++;
                continue;
            }

            // Validate department
            $deptExist = Department::select('id', 'department_name')
                ->where('department_name', $department_id)
                ->where('status', 1)
                ->first();

            if (!$deptExist) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Invalid Department',
                ];
                $i++;
                continue;
            }

            // Check for existing nomination process
            $nominationProcessExist = NominationProcess::where('training_schedule_id', $this->details['trainingScheduleIid'])
                ->where('employee_id', $empExist->id)
                ->where('status', 1)
                ->first();
            if ($nominationProcessExist) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Nomination Process for this Employee ID already exists',
                ];
                $i++;
                continue;
            }

            // Fetch last training details
            $lastTraining = TrainingAttendance::select('training_masters_topic.id', 'training_attendance.attendance_date')
                ->leftJoin('training_masters_topic', 'training_attendance.topic_id', '=', 'training_masters_topic.id')
                ->where('training_attendance.email', $empExist->email)
                ->where('training_attendance.attendance_status', 1)
                ->orderBy('training_attendance.attendance_date', 'desc')
                ->first();

            $lastTrainingDate = optional($lastTraining)->attendance_date ?? null;
            $lastTrainingTopic = optional($lastTraining)->id ?? null;

            // Create nomination process
            $data = [
                'training_schedule_id' => $this->details['trainingScheduleIid'],
                'employee_id' => $empExist->id,
                'emp_name' => $empExist->emp_name,
                'email' => $empExist->email,
                'department_id' => $deptExist->id,
                'employee_type' => $employee_type,
                'last_training_attended_on' => $lastTrainingDate,
                'topic_id' => $lastTrainingTopic,
                'created_by' => $this->details['user_id'],
            ];
            $nomination = NominationProcess::create($data);

            $i++;
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
