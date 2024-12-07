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
use Illuminate\Support\Facades\Session;

//  class ImportCompanyJob

class ImportCompanyJob implements ShouldQueue
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

        foreach ($xlsx->rows() as $row) {



            // Check if it's the first row (header row)
            if ($i == 1) {
                if (count($row) === 4) {

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
                if (
                    trim($row[0]) != 'SNo' ||
                    trim($row[1]) != 'Company Name' ||
                    trim($row[2]) != 'Short Name' ||
                    trim($row[3]) != 'Address'
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

            // Trim and assign column values to variables
            $sno = trim($row[0]);
            $companyname = trim($row[1]);
            $company_short_name = trim($row[2]);
            $company_address = trim($row[3]);

            // Validate column data
            if ($companyname == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Company name is missing',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            // Check if company already exists
            $companyExist = Company::where('company_name', $companyname)->get();
            if ($companyExist->isNotEmpty()) {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Company Name Already Exist',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            if ($company_short_name == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Company Short name is missing',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            if ($company_address == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Company Address is missing',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            // Prepare data for insertion
            $data = array(
                'company_name' => $companyname,
                'short_name' => $company_short_name,
                'address' => $company_address,
                'created_by' => $this->details['user_id']
            );

            // Insert data into the Company table
            Company::create($data);

            // Increment the counter
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


