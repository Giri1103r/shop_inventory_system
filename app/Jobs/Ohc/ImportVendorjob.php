<?php

namespace App\Jobs\Ohc;

use Shuchkin\SimpleXLSX;
use App\Models\Administration\UploadLog;
use Illuminate\Bus\Queueable;
use App\Models\Master\Factory;
use App\Models\Administration\UploadLogError;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Master\Company;
use App\Models\OhcManagement\Master\Vendor;
use Illuminate\Support\Facades\Session;

// class ImportVendorjob implements ShouldQueue
class ImportVendorjob
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
                    trim($row[1]) != 'Vendor Name' ||
                    trim($row[2]) != 'License Number' ||
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
            $vendorName = trim($row[1]);
            $LicenseNumber = trim($row[2]);
            $VendorAddress = trim($row[3]);

            // Validate column data
            if ($vendorName == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Vendor name is missing',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            // Check if vendor already exists
            $VendorExit = Vendor::where('vendor_name', $vendorName)->get();
            if ($VendorExit->isNotEmpty()) {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Vendor Name Already Exist',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            if ($LicenseNumber == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'License Number is missing',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            $licenseExist = Vendor::where('license_no', $LicenseNumber)->get();
            if ($licenseExist->isNotEmpty()) {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'License Number Already Exist',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }
            if ($VendorAddress == '') {
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
                'vendor_name' => $vendorName,
                'license_no' => $LicenseNumber,
                'address' => $VendorAddress,
                'created_by' => $this->details['user_id']
            );


            Vendor::create($data);

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
