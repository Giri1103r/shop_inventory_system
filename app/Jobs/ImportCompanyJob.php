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


use App\Models\Master\Company;

class ImportCompanyJob implements ShouldQueue
// class ImportCompanyJob
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
            $company_short_name = trim($row['2']);
            $company_address = trim($row['3']);

            /*
         * Header column validation
         */

            if ($i == 1) {

                if (count($row) == 4) {
                    if (
                        $sno != 'SNo' ||
                        $companyname != 'Company Name' ||
                        $company_short_name != 'Short Name' ||
                        $company_address != 'Address' 
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

                    if (count($companyExist) > 0) {
                        $cond_error_data = array(
                            'upload_id' => $this->details['log_id'],
                            'line_no' => $i,
                            'error' => 'Company Name Already Exist',
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


            if ($company_short_name == '') {

                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Company Short name is missing',
                );

                $cond_error_datas[] = $cond_error_data;

                $i++;
                continue;
            } 


            
            if ($company_address == '') {

                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Company Address is missing',
                );

                $cond_error_datas[] = $cond_error_data;

                $i++;
                continue;
            } 


            $data = array(
                'company_name' => $companyname,
                'short_name' => $company_short_name,
                'address' => $company_address,
                'created_by' => $this->details['user_id']
            );
            Company::create($data);
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
