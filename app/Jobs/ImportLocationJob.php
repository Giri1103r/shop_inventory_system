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
use Illuminate\Support\Facades\Session;

class ImportLocationJob implements ShouldQueue
// class ImportLocationJob
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

            // Check if it's the first row (header row)
            if ($i == 1) {

                // Check if the header row has 4 or fewer columns
                if (count($row) === 3) {

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

                // Validate header column names
                if (
                    trim($row['0']) != 'SNo' ||
                    trim($row['1']) != 'Company Name' ||
                    trim($row['2']) != 'Location Name'
                ) {
                    $error_data = array(
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Header Column Name Not Match',
                    );
                    $cond_error_datas[] = $error_data;
                    // No need to continue processing further rows if header is invalid
                    break;
                }

                // Move to the next row after processing the header
                $i++;
                continue;
            }

            $sno = trim($row['0']);
            $companyname = trim($row['1']);
            $location_name = trim($row['2']);

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

            if (empty($location_name)) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Location name is missing',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }

            $companyExist = Company::select('id')->where('company_name', $companyname)->first();

            if ($companyExist) {
                $locationExist = Location::where('location_name', $location_name)
                    ->where('company_id', $companyExist->id)
                    ->exists();

                if ($locationExist) {
                    $cond_error_data = [
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Location Name Already Exists for this Company',
                    ];
                    $cond_error_datas[] = $cond_error_data;
                    $i++;
                    continue;
                }
            } else {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Company not found',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }

            $data = [
                'company_id' => $companyExist->id,
                'location_name' => $location_name,
                'created_by' => $this->details['user_id'],
            ];

            Location::create($data);
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
