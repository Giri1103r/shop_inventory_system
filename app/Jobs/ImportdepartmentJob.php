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


use App\Models\Master\Location;
use App\Models\Master\Company;
use App\Models\Master\Department;
use App\Models\Master\Unit;
use Illuminate\Support\Facades\Session;

class ImportdepartmentJob implements ShouldQueue
// class ImportdepartmentJob
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
         // Header Column Validation
                if (count($row) === 5) {

                }else{
                    $error_data = array(
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Header Column not match',
                    );
                    $cond_error_datas[] = $error_data;
                    $i++;
                    break;
                }
                // Header row validation

                if (
                    trim($row[0])  != 'SNo' ||
                    trim($row[1]) != 'Company Name' ||
                    trim($row[2]) != 'Location Name' ||
                    trim($row[3]) != 'Unit Name' ||
                    trim($row[4]) != 'Department Name'
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
            $companyname = trim($row['1']);
            $locationname = trim($row['2']);
            $unitname = trim($row['3']);
            $department_name = trim($row['4']);


            /* Column data validation */
            if ($companyname == '') {

                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Company name is missing',
                );

                $cond_error_datas[] = $cond_error_data;

                $i++;
                continue;
            }
            // else {

            //     $companyExist = Company::where('company_name', $companyname)->get();
            //     try {

            //         if (count($companyExist) <= 0) {
            //             $cond_error_data = array(
            //                 'upload_id' => $this->details['log_id'],
            //                 'line_no' => $i,
            //                 'error' => 'Invalid Company name',
            //             );
            //             UploadLogError::insert($cond_error_data);
            //             $i++;
            //             continue;
            //         }
            //         $companyId = $companyExist['0']->id;
            //     } catch (\Exception $ex) {
            //         report($ex);
            //     }
            // }
            if ($locationname == '') {

                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Location name is missing',
                );

                $cond_error_datas[] = $cond_error_data;

                $i++;
                continue;
            }
            // else {

            //     $locationExist = Location::where('location_name', $locationname)->get();
            //     try {

            //         if (count($locationExist) <= 0) {
            //             $cond_error_data = array(
            //                 'upload_id' => $this->details['log_id'],
            //                 'line_no' => $i,
            //                 'error' => 'Invalid Location name',
            //             );
            //             UploadLogError::insert($cond_error_data);
            //             $i++;
            //             continue;
            //         }
            //         $locationId = $locationExist['0']->id;
            //     } catch (\Exception $ex) {
            //         report($ex);
            //     }
            // }
            if ($unitname == '') {

                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Unit name is missing',
                );

                $cond_error_datas[] = $cond_error_data;

                $i++;
                continue;
            }
            // else {

            //     $unitExist = Unit::where('unit_name', $unitname)->get();
            //     try {

            //         if (count($unitExist) <= 0) {
            //             $cond_error_data = array(
            //                 'upload_id' => $this->details['log_id'],
            //                 'line_no' => $i,
            //                 'error' => 'Invalid Unit name',
            //             );
            //             UploadLogError::insert($cond_error_data);
            //             $i++;
            //             continue;
            //         }
            //         $unitId = $unitExist['0']->id;
            //     } catch (\Exception $ex) {
            //         report($ex);
            //     }
            // }
            // Validate company existence
            $companyExist = Company::select('id')->where('company_name', $companyname)->first();
            if (!$companyExist) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Invalid company name',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }


            $locationExist = Location::select('id')
                ->where('location_name', $locationname)
                ->where('company_id', $companyExist->id)
                ->first();

            if (!$locationExist) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Invalid location name',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }

            $unitExist = Unit::select('id')
                ->where('unit_name', $unitname)
                ->where('company_id', $companyExist->id)
                ->where('location_id', $locationExist->id)
                ->first();

            if (!$unitExist) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Invalid unit name',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }
            $departmentExist = Department::where('department_name', $department_name)
                ->where('company_id', $companyExist->id)
                ->where('location_id', $locationExist->id)
                ->where('unit_id', $unitExist->id)
                ->exists();

            if ($departmentExist) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Department Name Already Exists for the specified Company, Location, and Unit',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }

            $data = [
                'company_id' => $companyExist->id,
                'location_id' => $locationExist->id,
                'unit_id' => $unitExist->id,
                'department_name' => $department_name,
                'created_by' => $this->details['user_id'],
            ];


            Department::create($data);

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
