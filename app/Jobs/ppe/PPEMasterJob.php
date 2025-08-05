<?php

namespace App\Jobs\ppe;

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

// class PPEMasterJob implements ShouldQueue
class PPEMasterJob
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
                if (count($row) !== 6) {
                    $cond_error_datas[] = [
                        'upload_id' => $uploadId,
                        'line_no' => $i,
                        'error' => 'Header Column count does not match.',
                    ];
                    break;
                }

                if (
                    trim($row[0]) !== 'SNo' ||
                    trim($row[1]) !== 'Item Code' ||
                    trim($row[2]) !== 'PPE Name' ||
                    trim($row[3]) !== 'PPE Type' ||
                    trim($row[4]) !== 'PPE Standard' ||
                    trim($row[5]) !== 'Protection Category'
                ) {
                    $cond_error_datas[] = [
                        'upload_id' => $uploadId,
                        'line_no' => $i,
                        'error' => 'Header Column names do not match.',
                    ];
                    break;
                }

                $i++;
                continue;
            }

            // Skip invalid row structure
            if (!is_array($row) || count($row) < 6) {
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
            $itemCode = trim($row[1]);
            $ppeName = trim($row[2]);
            $ppeTypeText = trim($row[3]);
            $ppeStandard = trim($row[4]);
            $protectionCategory = trim($row[5]);

            // Validate required fields
            if ($itemCode === '') {
                $cond_error_datas[] = [
                    'upload_id' => $uploadId,
                    'line_no' => $i,
                    'error' => 'Item Code is missing.',
                ];
                $i++;
                continue;
            }

            if (PpeTypeMaster::where('item_code', $itemCode)->exists()) {
                $cond_error_datas[] = [
                    'upload_id' => $uploadId,
                    'line_no' => $i,
                    'error' => 'Item Code already exists.',
                ];
                $i++;
                continue;
            }

            if ($ppeName === '') {
                $cond_error_datas[] = [
                    'upload_id' => $uploadId,
                    'line_no' => $i,
                    'error' => 'PPE Name is missing.',
                ];
                $i++;
                continue;
            }

            if ($ppeTypeText === '') {
                $cond_error_datas[] = [
                    'upload_id' => $uploadId,
                    'line_no' => $i,
                    'error' => 'PPE Type is missing.',
                ];
                $i++;
                continue;
            }

            // Convert PPE Type to numeric value
            $ppeType = null;
            if (strcasecmp($ppeTypeText, 'Respiratory') === 0) {
                $ppeType = 1;
            } elseif (strcasecmp($ppeTypeText, 'Non Respiratory') === 0) {
                $ppeType = 2;
            } else {
                $cond_error_datas[] = [
                    'upload_id' => $uploadId,
                    'line_no' => $i,
                    'error' => 'Invalid PPE Type: must be Respiratory or Non Respiratory.',
                ];
                $i++;
                continue;
            }

            if ($ppeStandard === '') {
                $cond_error_datas[] = [
                    'upload_id' => $uploadId,
                    'line_no' => $i,
                    'error' => 'PPE Standard is missing.',
                ];
                $i++;
                continue;
            }

            if ($protectionCategory === '') {
                $cond_error_datas[] = [
                    'upload_id' => $uploadId,
                    'line_no' => $i,
                    'error' => 'Protection Category is missing.',
                ];
                $i++;
                continue;
            }

            // Prepare and insert
            $data = [
                'item_code' => $itemCode,
                'ppe_name' => $ppeName,
                'ppe_type' => $ppeType,
                'ppe_standard' => $ppeStandard,
                'ppe_category' => $protectionCategory,
                'created_by' => $this->details['user_id'],
            ];

            try {
                PpeTypeMaster::create($data);
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
