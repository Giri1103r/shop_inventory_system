<?php

namespace App\Jobs;

use App\Models\Inspection\Fire\FireExtinguisherType;
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

class ImportFireExtinguisherTypejob
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
        $update_array = [
            'upload_status' => 1,
        ];

        UploadLog::where('id', $this->details['log_id'])->update($update_array);

        $xlsx = SimpleXLSX::parse($this->details['path']);
        $cond_error_datas = [];

        foreach ($xlsx->rows() as $row) {

            // Check header row
            if ($i === 1) {
                if (count($row) !== 2) {
                    $cond_error_datas[] = [
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Header Column count does not match expected count (4)',
                    ];
                    break;
                }

                if (
                    trim($row[0]) !== 'SNo' ||
                    trim($row[1]) !== 'Fire Extinguisher Type'

                ) {
                    $cond_error_datas[] = [
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Header Column Name does not match expected names',
                    ];
                    $i++;
                    continue;
                }

                $i++;
                continue;
            }

            // Trim and assign values
            $sno = trim($row[0]);
           $fireExtinguisher = trim($row[1]);

            // Validation
            if ($fireExtinguisher === '') {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Fire Extinguisher Type is missing',
                ];
                $i++;
                continue;
            }

            // Check if already exists
            $companyExist = FireExtinguisherType::where('fire_extinguisher_name',$fireExtinguisher)->exists();
            if ($companyExist) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Fire Extinguisher Type already exists',
                ];
                $i++;
                continue;
            }

            // Prepare and insert
            try {
                FireExtinguisherType::create([
                    'fire_extinguisher_name' =>$fireExtinguisher,
                    'created_by' => $this->details['user_id'],
                ]);
            } catch (\Exception $e) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Insert error: ' . $e->getMessage(),
                ];
            }

            $i++;
        }

        // Final status update
        if (count($cond_error_datas) > 0) {
            UploadLogError::insert($cond_error_datas);
            $final_update_array = ['upload_status' => 3];
            Session::flash('error', 'Failed to upload. Please check the upload log.');
        } else {
            $final_update_array = ['upload_status' => 2];
            Session::flash('success', 'Upload completed successfully.');
        }

        UploadLog::where('id', $this->details['log_id'])->update($final_update_array);
    }
}
