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
use App\Models\Master\Employee;
use App\Models\Master\UserRole;

use App\Models\Master\Department;

use App\Models\Master\Designation;
use App\Mail\EmployeeRegisterEmail;
use App\Models\Master\EmployeeType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\Master\CompanyEmployee;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;


use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class ImportEmployeeJob
//class ImportEmployeeJob
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

        $xlsx = SimpleXLSX::parse(private_storage($this->details['path']));

        $cond_error_datas = [];

        foreach ($xlsx->rows() as $row) {

            /*
         * Header column validation
         */
            if ($i == 1) {

                    if (
                       trim($row['0']) != 'S.No' ||
                       trim($row['1']) != 'Employee ID' ||
                       trim($row['2']) != 'First Name' ||
                       trim($row['3']) != 'Last Name' ||
                      trim($row['4']) != 'Email' ||
                       trim($row['5']) != 'Mobile' ||
                       trim($row['6']) != 'SOS Number'
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

             if (count($row) < 7) {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Header Column Not Match',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            $sno = trim($row['0']);
            $employee_id = trim($row['1']);
            $employee_first_name = trim($row['2']);
            $employee_last_name = trim($row['3']);
            $employee_email = trim($row['4']);
            $employee_mobile = trim($row['5']);
            $employee_sos_number = trim($row['6']);


            /* Column data validation */
            if (!is_numeric($sno) || $sno <= 0) {
                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'S.No must be a positive number',
                );
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }

            if (
                empty($employee_first_name) ||
                empty($employee_last_name) ||
                empty($employee_email) ||
                empty($employee_mobile)
            ) {
                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Required field(s) missing',
                );
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }


            $employeeExist = CompanyEmployee::where('employee_email', $employee_email)
                ->orWhere('employee_id', $employee_id)
                ->first();


            $userExist = User::where('email', $employee_email)
                ->orWhere('emp_id', $employee_id)
                ->first();

            if ($employeeExist || $userExist) {
                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Employee or User Already Exists',
                );
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }

            $Userdata = array(
                'name' => $employee_first_name . ' ' . $employee_last_name,
                'first_name' => $employee_first_name,
                'last_name' => $employee_last_name,
                'email' => $employee_email,
                'role' => 3,
                'user_type' => 3,
                'emp_id' => $employee_id,
                'username' => $employee_email,
                'password' => Hash::make("User@" . trim($employee_id)),
                'mobile' => $employee_mobile,
                'created_by' => Auth::id()
            );

            $userDetails = User::create($Userdata)->id;
            $login_id = $userDetails->id;
            $data = array(
                'company_id' => $this->details['company_id'],
                'employee_id' => $employee_id,
                'employee_first_name' => $employee_first_name,
                'employee_last_name' => $employee_last_name,
                'employee_email' => $employee_email,
                'employee_mobile' => $employee_mobile,
                'employee_sos_number' => $employee_sos_number,
                'login_id' => $login_id,
                'created_by' => $this->details['user_id']
            );


            CompanyEmployee::create($data);

            if ($userDetails->email != '' || $userDetails->email != null) {

                $empdetails =  User::find($login_id);

                $emp  = $empdetails->toArray();

                Mail::to($empdetails->email)->queue(new EmployeeRegisterEmail($emp));
            }


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
