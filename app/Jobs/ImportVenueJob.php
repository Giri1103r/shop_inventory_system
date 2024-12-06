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


use App\Models\Master\Venue;
use App\Models\Master\Unit;
use Illuminate\Support\Facades\Session;

class ImportVenueJob
// class ImportVenueJob
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


            /*
         * Header column validation
         */

            if ($i == 1) {

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
                if (
                    trim($row['0']) != 'SNo' ||
                    trim($row['1']) != 'Conference Hall Name' ||
                    trim($row['2']) != 'Unit' ||
                    trim($row['3']) != 'Capacity' ||
                    trim($row['4']) != 'Projector/LCD Availability'
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
            $name_of_the_conference_hall = trim($row['1']);
            $unit_name = trim($row['2']);
            $capacity = trim($row['3']);
            $projector_or_lcd_availability = trim($row['4']);
            /* Column data validation */
            if ($name_of_the_conference_hall == '') {

                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Conference Hall Name is missing',
                );

                $cond_error_datas[] = $cond_error_data;

                $i++;
                continue;
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

                    if (count($unitExist) <= 0) {
                        $cond_error_data = array(
                            'upload_id' => $this->details['log_id'],
                            'line_no' => $i,
                            'error' => 'Invalid Unit name',
                        );
                        UploadLogError::insert($cond_error_data);
                        $i++;
                        continue;
                    }
                    $unitId = $unitExist['0']->id;
                } catch (\Exception $ex) {
                    report($ex);
                }
            }
            $unitExist = Unit::select('id')->where('unit_name', $unit_name)->first();
            if ($unitExist) {

                $venueExist = Venue::where('name_of_the_conference_hall', $name_of_the_conference_hall)->where('unit_id', $unitExist->id)->exists();
                try {

                    if ($venueExist) {
                        $cond_error_data = array(
                            'upload_id' => $this->details['log_id'],
                            'line_no' => $i,
                            'error' => 'Conference Hall Name Already Exist',
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
            } else {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Unit not found',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }


            if ($capacity == '') {
                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Capacity is missing',
                );

                $cond_error_datas[] = $cond_error_data;

                $i++;
                continue;
            } elseif (!is_numeric($capacity)) { // Check if capacity is not numeric
                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Capacity must be numeric',
                );

                $cond_error_datas[] = $cond_error_data;

                $i++;
                continue;
            }

            if ($projector_or_lcd_availability == '') {
                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Projector/LCD Availability is missing',
                );

                $cond_error_datas[] = $cond_error_data;

                $i++;
                continue;
            }



            $data = array(
                'name_of_the_conference_hall' => $name_of_the_conference_hall,
                'unit_id' => $unitId,
                'capacity' => $capacity,
                'projector_or_lcd_availability' => $projector_or_lcd_availability,
                'created_by' => $this->details['user_id']
            );

            Venue::create($data);

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
