<?php

namespace App\Jobs\Ohc;

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
use App\Models\OhcManagement\Master\HospitalDetails;
use App\Models\OhcManagement\Master\Vendor;
use Illuminate\Support\Facades\Session;

class ImportFirstAidLocationjob implements ShouldQueue
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
                if (count($row) == 5) {
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
                    trim($row[0]) != 'SNo' ||
                    trim($row[1]) != 'Unit Name' ||
                    trim($row[2]) != 'Department Name' ||
                    trim($row[3]) != 'Excat Location' ||
                    trim($row[4]) != 'Station Master (Employee id)'
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
            $hospitalName = trim($row[1]);
            $mobileNumber = trim($row[2]);
            $telNumber = trim($row[3]);
            $hospitalAddress = trim($row[4]);

            // Validate column data
            if ($hospitalName == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Hospital name is missing',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            // Check if hospital already exists
            $hospitalDetailsExist = HospitalDetails::where('hospital_name', $hospitalName)->get();
            if ($hospitalDetailsExist->isNotEmpty()) {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Hospital Name Already Exist',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            if ($mobileNumber == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Mobile Number is missing',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            $mobilenoExist = HospitalDetails::where('mobile_no', $mobileNumber)->get();

            // Check: Mobile already exists
            if ($mobilenoExist->isNotEmpty()) {
                $error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Mobile Number Already Exist',
                ];
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            // Check: Mobile number should be exactly 10 digits
            if (!preg_match('/^\d{10}$/', $mobileNumber)) {
                $error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Mobile number must be exactly 10 digits',
                ];
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            // Check: Telephone number should be 7 to 15 digits only
            if (!preg_match('/^\d{7,15}$/', $telNumber)) {
                $error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Telephone number must be between 7 to 15 digits',
                ];
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            if ($hospitalAddress == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Address is missing',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            // Prepare data for insertion
            $data = array(
                'hospital_name' => $hospitalName,
                'mobile_no' => $mobileNumber,
                'tel_no' => $telNumber,
                'address' => $hospitalAddress,
                'created_by' => $this->details['user_id']
            );


            HospitalDetails::create($data);

            // Increment the counter
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
