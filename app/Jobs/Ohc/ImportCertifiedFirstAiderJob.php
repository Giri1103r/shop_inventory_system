<?php

namespace App\Jobs\Ohc;

use Mpdf\Tag\Tr;
use Shuchkin\SimpleXLSX;
use App\Models\UploadLog;
use App\Models\Master\Unit;
use App\Models\Master\Work;
use App\Models\UploadLogError;
use App\Models\Master\Employee;
use App\Models\Master\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Session;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\OhcManagement\Master\CertifiedFirstAider;

// class ImportCertifiedFirstAiderJob implements ShouldQueue
class ImportCertifiedFirstAiderJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $details;


    /**
     * Create a new job instance.
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
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

            // Check if it's the first row (header row)
            if ($i == 1) {
                if (count($row) === 5) {
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
                    trim($row[0]) != 'S.No' ||
                    trim($row[1]) != 'Unit' ||
                    trim($row[2]) != 'Department' ||
                    trim($row[3]) != 'Employee/Worker Code' ||
                    trim($row[4]) != 'Address'

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
                // Move to the next row after processing the header
                $i++;
                continue;
            }

            $sno = trim($row[0]);
            $unit_name = trim($row[1]);
            $department_name = trim($row[2]);
            $emp_work_code = trim($row[3]);
            $address = trim($row[4]);

            if ($unit_name == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Unit name is missing',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            if ($department_name == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Department Name is missing',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            if ($emp_work_code == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Employee Worker code is missing',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            if ($department_name == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Department Name is missing',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }


            if ($address == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Address is missing',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            // get unit_id
            $unit_id = Unit::where('unit_name', $unit_name)->pluck('id')->first();

            // get department_id
            $department_name_id = Department::where('department_name', $department_name)->where('unit_id', $unit_id)->pluck('id')->first();

            //    get employee name
            $employee = Employee::where('emp_id', $emp_work_code)
                ->select('emp_name', 'mobile_no')
                ->first();

            if (!$employee) {
                $employee = Work::where('emp_id', $emp_work_code)
                    ->select('emp_name', 'mobile_no')
                    ->first();
            }

            // create
            $data = array(
                'unit_id'         => $unit_id,
                'department_id'   => $department_name_id,
                'emp_id'     => $emp_work_code,
                'certifier_name' => $employee->emp_name,
                'mobile_no' => $employee->mobile_no,
                'address' => $address,
                'created_by' => $this->details['user_id'],
            );


            CertifiedFirstAider::create($data);

            $i++;
        }

        // Check if there were any errors
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
