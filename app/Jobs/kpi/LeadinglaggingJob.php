<?php

namespace App\Jobs\kpi;

use App\Models\KPI\LeadingLagging;
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
use App\Models\Master\PpeTypeMaster;
use App\Models\OhcManagement\Master\Vendor;
use Illuminate\Support\Facades\Session;

// class LeadinglaggingJob implements ShouldQueue
class LeadinglaggingJob
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
        $uploadId = $this->details['log_id'];

        // Mark upload as in-progress
        UploadLog::where('id', $uploadId)->update(['upload_status' => 1]);

        $xlsx = SimpleXLSX::parse($this->details['path']);
        $cond_error_datas = [];

        foreach ($xlsx->rows() as $row) {
            // Header Validation
            if ($i === 1) {
                if (count($row) !== 3) {
                    $cond_error_datas[] = [
                        'upload_id' => $uploadId,
                        'line_no' => $i,
                        'error' => 'Header column count does not match.',
                    ];
                    break;
                }

                if (
                    trim($row[0]) !== 'SNo' ||
                    trim($row[1]) !== 'Type' ||
                    trim($row[2]) !== 'Value'
                ) {
                    $cond_error_datas[] = [
                        'upload_id' => $uploadId,
                        'line_no' => $i,
                        'error' => 'Header column names do not match.',
                    ];
                    break;
                }

                $i++;
                continue;
            }

            // Validate row structure (should have at least 3 columns)
            if (!is_array($row) || count($row) < 3) {
                $cond_error_datas[] = [
                    'upload_id' => $uploadId,
                    'line_no' => $i,
                    'error' => 'Invalid row structure.',
                ];
                $i++;
                continue;
            }

            // Trim and assign values
            $sno = trim($row[0]);
            $typeInput = trim($row[1]);
            $value = trim($row[2]);

            // Validate required fields
            if ($typeInput === '') {
                $cond_error_datas[] = [
                    'upload_id' => $uploadId,
                    'line_no' => $i,
                    'error' => 'Type is missing.',
                ];
                $i++;
                continue;
            }

            if ($value === '') {
                $cond_error_datas[] = [
                    'upload_id' => $uploadId,
                    'line_no' => $i,
                    'error' => 'Value is missing.',
                ];
                $i++;
                continue;
            }

            // Convert type
            if (strcasecmp($typeInput, 'Leading') === 0) {
                $type = 1;
            } elseif (strcasecmp($typeInput, 'Lagging') === 0) {
                $type = 2;
            } else {
                $cond_error_datas[] = [
                    'upload_id' => $uploadId,
                    'line_no' => $i,
                    'error' => 'Invalid Type: must be Leading or Lagging.',
                ];
                $i++;
                continue;
            }

            // Prepare and insert
            $data = [
                'type' => $type,
                'value' => $value,
                'created_by' => $this->details['user_id'],
            ];

            try {
                LeadingLagging::create($data);
            } catch (\Exception $e) {
                $cond_error_datas[] = [
                    'upload_id' => $uploadId,
                    'line_no' => $i,
                    'error' => 'DB Error: ' . $e->getMessage(),
                ];
            }

            $i++;
        }

        // Final status update
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
