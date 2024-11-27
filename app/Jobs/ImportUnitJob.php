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
use App\Models\Master\Unit;

class ImportUnitJob implements ShouldQueue
// class ImportUnitJob
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
            $sno = trim($row['0']);
            $companyname = trim($row['1']);
            $locationname = trim($row['2']);
            $unit_name = trim($row['3']);

            /*
         * Header column validation
         */

            if ($i == 1) {

                if (count($row) == 4) {
                    if (
                        $sno != 'SNo' ||
                        $companyname != 'Company Name' ||
                        $locationname != 'Location Name'||
                        $unit_name != 'Unit Name'
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
                } else {
                    $error_data_1 = array(
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Header Column Not Match',
                    );
                    $cond_error_datas[] = $error_data_1;
                    $i++;
                    break;
                }
            }

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
            } else {

                $companyExist = Company::where('company_name', $companyname)->get();
                try {

                    if (count($companyExist) <= 0) {
                        $cond_error_data = array(
                            'upload_id' => $this->details['log_id'],
                            'line_no' => $i,
                            'error' => 'Invalid Company name',
                        );
                        UploadLogError::insert($cond_error_data);
                        $i++;
                        continue;
                    }
                    $companyId = $companyExist['0']->id;
                } catch (\Exception $ex) {
                    report($ex);
                }
            }
            if ($locationname == '') {

                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Location name is missing',
                );

                $cond_error_datas[] = $cond_error_data;

                $i++;
                continue;
            } else {

                $locationExist = Location::where('location_name', $locationname)->get();
                try {

                    if (count($locationExist) <= 0) {
                        $cond_error_data = array(
                            'upload_id' => $this->details['log_id'],
                            'line_no' => $i,
                            'error' => 'Invalid Location name',
                        );
                        UploadLogError::insert($cond_error_data);
                        $i++;
                        continue;
                    }
                    $locationId = $locationExist['0']->id;
                } catch (\Exception $ex) {
                    report($ex);
                }
            }


            if ($unit_name == '') {

                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Unit name is missing',
                );

                $cond_error_datas[] = $cond_error_data;

                $i++;
                continue;
            } else {

                $unitExist = Unit::where('unit_name', $unit_name)->get();
                try {

                    if (count($unitExist) > 0) {
                        $cond_error_data = array(
                            'upload_id' => $this->details['log_id'],
                            'line_no' => $i,
                            'error' => 'Unit Name Already Exist',
                        );
                        $cond_error_datas[] = $cond_error_data;
                        $i++;
                        continue;
                    }

                } catch (\Exception $ex) {
                    $cond_error_data = array(
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Invalid Data',
                    );
                    $cond_error_datas[] = $cond_error_data;
                }
            }



            $data = array(
                'company_id' => $companyId,
                'location_id' => $locationId,
                'unit_name' => $unit_name,
                'created_by' => $this->details['user_id']
            );
            Unit::create($data);
            // SmpsFactory::create($data);

            $i++;
        }

        if (count($cond_error_datas) > 0) {
            UploadLogError::insert($cond_error_datas);
        }

        $final_update_array = array(
            'upload_status' => 2,
        );
        UploadLog::where('id', $this->details['log_id'])->update($final_update_array);
    }
}
