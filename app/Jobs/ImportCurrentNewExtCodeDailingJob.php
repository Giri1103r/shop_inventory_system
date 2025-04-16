<?php

namespace App\Jobs;

use App\Models\Inspection\Ohc\CurrentNewExtCodeDialing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\Unit;
use App\Models\UploadLog;
use Shuchkin\SimpleXLSX;
use App\Models\UploadLogError;
use Illuminate\Support\Facades\Session;
// class ImportCurrentNewExtCodeDailingJob implements ShouldQueue
class ImportCurrentNewExtCodeDailingJob
{
    // use Queueable;

   
    protected $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function handle(): void
    {
        $i = 1;

        $update_array = array(
            'upload_status' => 1
        );

        UploadLog::where('id', $this->details['log_id'])
            ->update($update_array);

        $xlsx = SimpleXLSX::parse($this->details['path']);
        $cond_error_datas = [];

        foreach ($xlsx->rows() as $row) {

            $sno = trim($row[0]);
            $unitName = trim($row[1]);
            $departmentName = trim($row[2]);
            $employeeName = trim($row[3]);
            $number = trim($row[4]);

            if ($i == 1) {
                if (count($row) <= 5) {
                    if (
                        $sno != 'S.No' ||
                        $unitName != 'Unit' ||
                        $departmentName != 'Department' ||
                        $employeeName != 'Employee Name' ||
                        $number != 'Number' 
                    ) {
                        $error_data_1 = array(
                            'upload_id' => $this->details['log_id'],
                            'line_no' =>  $i,
                            'error' => 'Header Column Name Not Match',
                        );
                        Session::flash('error', 'Header Column Name Not Match!');
                        $i++;
                        break;
                    }
                    $i++;
                    continue;
                } else {
                    $error_data_1 = array(
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Header Column Not Match',
                    );
                    Session::flash('error', 'Header Column  Not Match!');
                    $i++;
                    break;
                }
            }

            //  BU Name

            if ($unitName == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Unit Name is missing',
                );
                Session::flash('error', 'Unit Name is missing.');
                $i++;
                continue;
            }

            $unitExist = Unit::where('unit_name', $unitName)->get();

            try {
                if (count($unitExist) <= 0) {
                    $cond_error_data = array(
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Invalid Unit Name',
                    );
                    Session::flash('error', 'Import unsuccessfull!, Please check the upload logs');
                    $cond_error_datas[] = $cond_error_data;
                    $i++;
                    continue;
                }
                $unitId =  $unitExist['0']->id;
            } catch (\Exception $ex) {
                report($ex);
            }

            // line

            if ($departmentName == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Department name is missing',
                );
                Session::flash('error', 'Department name is missing.');
                $i++;
                continue;
            }

            $departmentExist = Department::where('department_name', $departmentName)->where('unit_id', $unitId)->get();

            try {
                
                if (count($departmentExist) <= 0) {
                    $cond_error_data = array(
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Invalid Departmnet Name',
                    );
                    Session::flash('error', 'Import unsuccessfull!, Please check the upload logs');
                    $cond_error_datas[] = $cond_error_data;
                    $i++;
                    continue;
                }
                $departmentId =  $departmentExist['0']->id;
            } catch (\Exception $ex) {
                report($ex);
            }

            /////type

            if ($employeeName == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Employee name is missing',
                );
                Session::flash('error', 'Employee name is missing.');
                $i++;
                continue;
            }

            $employeeExist = Employee::where('emp_name', $employeeName)->where('unit', $unitId)->where('department', $departmentId)->get();

            try {
                if (count($employeeExist) <= 0) {
                    $cond_error_data = array(
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Invalid Employee Name',
                    );
                    Session::flash('error', 'Import unsuccessfull!, Please check the upload logs');
                    $cond_error_datas[] = $cond_error_data;
                    $i++;
                    continue;
                }
                $employeeId =  $employeeExist['0']->id;
            } catch (\Exception $ex) {
                report($ex);
            }


            if ($number == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Number is missing',
                );
                Session::flash('error', 'Number is missing.');
                $i++;
                continue;
            }

            $numberExist = CurrentNewExtCodeDialing::where('number', $number)->where('unit_id', $unitId)->where('department_id', $departmentId)->where('emp_name_id', $employeeId)->get();

            try {
                if (count($numberExist) > 0) {
                    $cond_error_data = array(
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Number Already Exist',
                    );
                    Session::flash('error', 'Import unsuccessfull!, Please check the upload logs');
                    $cond_error_datas[] = $cond_error_data;
                    $i++;
                    continue;
                }
            } catch (\Exception $ex) {
                report($ex);
            }


            $data = [
                'unit_id' => $unitId,
                'department_id' => $departmentId,
                'emp_name_id' => $employeeId,
                'number' => $number,
                'created_by' => $this->details['user_id'],
            ];

            CurrentNewExtCodeDialing::create($data);

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
