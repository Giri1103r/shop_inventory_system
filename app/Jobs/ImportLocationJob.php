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

        
        dd('gsdcbhdc');
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
            $sno = trim($row['0']);
            $companyname = trim($row['1']);
            $location_name = trim($row['2']);

            /*
         * Header column validation
         */

            if ($i == 1) {

                if (count($row) == 3) {
                    if (
                        $sno != 'SNo' ||
                        $companyname != 'Company Name' ||
                        $location_name != 'Location Name'
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
        }
        dd($cond_error_datas,$this->details['log_id']);
        $final_update_array = array(
            'upload_status' => 2,
        );
        UploadLog::where('id', $this->details['log_id'])->update($final_update_array);
    }
}
