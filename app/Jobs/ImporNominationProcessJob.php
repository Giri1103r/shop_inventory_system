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


use App\Models\Master\NominationProcess;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\Topic;
use Illuminate\Support\Facades\Session;

// class ImporNominationProcessJob  implements ShouldQueue
class ImporNominationProcessJob
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

                if (count($row) === 8) {
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
                    trim($row['5']) != 'Employee Type' ||
                    trim($row['6']) != 'Last Taining Attended on (Date)' ||
                    trim($row['7']) != 'Last Training Attended on (Topic)'
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
            $last_training_attended_on = trim($row['6']);
            $topic_id = trim($row['7']);

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
            if (empty($last_training_attended_on)) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Last Taining Attended on (Date) is missing',
                ];

                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }
            if (empty($topic_id)) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Last Training Attended on (Topic) is missing',
                ];

                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }

            // Validate date format (d-m-Y)
            if (!DBdateformat($last_training_attended_on)) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Invalid date format for Last Training Attended on (Date). Expected format: d-m-Y',
                ];
                $i++;
                continue;
            }


            $empExist = Employee::select('id', 'emp_id', 'emp_name', 'email', 'department', 'employee_status')
                ->where('emp_id', $emp_id)
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

            // Department Validation
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

            if ($empExist->department != $deptExist->id) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Employee does not belong to the specified Department',
                ];
                $i++;
                continue;
            }


            // Topic Validation
            $topicExist = Topic::select('id', 'topic_name')
                ->where('topic_name', $topic_id)
                ->where('status', 1)
                ->first();

            if (!$topicExist) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Invalid Topic',
                ];
                $i++;
                continue;
            }
            $nominationProcessExist = NominationProcess::where('emp_id', $empExist->id)->where('status', 1)->first();
            if ($nominationProcessExist) {

                if ($nominationProcessExist->emp_id !== $emp_id) {
                    $cond_error_datas[] = [
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Nomination Process for this Employee ID already exists',
                    ];
                    $i++;
                    continue;
                }

                if ($nominationProcessExist->emp_name !== $emp_name) {
                    $cond_error_datas[] = [
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Nomination Process for this Employee Name already exists for this Employee ID',
                    ];
                    $i++;
                    continue;
                }
                if ($nominationProcessExist->email !== $email) {
                    $cond_error_datas[] = [
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Nomination Process for this Email ID already exists for this Employee ID',
                    ];
                    $i++;
                    continue;
                }
              
            }
            // Create the unit if it doesn't exist
            $data = [
                'emp_id' => $empExist->id,
                'emp_name' => $empExist->emp_name,
                'email' => $empExist->email,
                'department_id' => $empExist->department,
                'employee_type' => $employee_type,
                'last_training_attended_on' => DBdateformat($last_training_attended_on),
                'topic_id' => $topicExist->id,
                'created_by' => $this->details['user_id'],
            ];

            NominationProcess::create($data);


            $i++;
        }


        // Log errors or mark as successful
        if (count($cond_error_datas) > 0) {
            UploadLogError::insert($cond_error_datas);
            UploadLog::where('id', $this->details['log_id'])->update(['upload_status' => 3]);
            Session::flash('error', 'Failed to upload. Please check the upload log.');
        } else {
            UploadLog::where('id', $this->details['log_id'])->update(['upload_status' => 2]);
            Session::flash('success', 'Upload completed successfully.');
        }
    }
}
