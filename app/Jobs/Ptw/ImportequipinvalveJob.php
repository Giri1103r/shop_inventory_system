<?php

namespace App\Jobs\Ptw;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Validator;


use Shuchkin\SimpleXLSX;

use App\Models\User;
use App\Models\UploadLog;
use App\Models\UploadLogError;
use App\Models\Master\EquipInvalve;
use Illuminate\Support\Facades\Session;

//  class ImportequipinvalveJob implements ShouldQueue
class ImportequipinvalveJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function handle()
    {
        $i = 1;

        // Update the upload log to indicate the job is being processed
        $update_array = [
            'upload_status' => 1,
        ];

        UploadLog::where('id', $this->details['log_id'])->update($update_array);

        $xlsx = SimpleXLSX::parse($this->details['path']);
        $cond_error_datas = [];

        foreach ($xlsx->rows() as $row) {
            $sno = trim($row['0']);
            $equip_involve = trim($row['1']);

            // Header column validation
            if ($i == 1) {
                if (count($row) == 2) {
                    if ($sno != 'SNo' || $equip_involve != 'Name') {
                        $error_data_1 = [
                            'upload_id' => $this->details['log_id'],
                            'line_no' => $i,
                            'error' => 'Header Column Name Not Match',
                        ];
                        $cond_error_datas[] = $error_data_1;
                        break; // Stop processing further rows if header is incorrect
                    }
                    $i++;
                    continue;
                } else {
                    $error_data_1 = [
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Header Column Not Match',
                    ];
                    $cond_error_datas[] = $error_data_1;
                    break; // Stop processing further rows if header is incorrect
                }
            }

            // Column data validation
            if ($equip_involve == '') {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Name is missing',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }

            // Check if equipment already exists
            $equip_involveExist = EquipInvalve::where('equip_involve', $equip_involve)->exists();
            if ($equip_involveExist) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Name is already Exists',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue; // Skip to the next row if the name already exists
            }

            // If no error, proceed to insert the data
            $data = [
                'equip_involve' => $equip_involve,
                'created_by' => $this->details['user_id'],
            ];
            EquipInvalve::create($data);

            $i++;
        }

        // If there were errors, insert them into the error log and update the upload status
        if (count($cond_error_datas) > 0) {
            UploadLogError::insert($cond_error_datas);
            $final_update_array = [
                'upload_status' => 3, // Mark as failed
            ];
            Session::flash('error', 'Failed to upload. Please check the upload log.');
        } else {
            // If no errors, mark the upload as successful
            $final_update_array = [
                'upload_status' => 2, // Mark as successful
            ];
            Session::flash('success', 'Upload completed successfully.');
        }

        // Update the upload log with the final status
        UploadLog::where('id', $this->details['log_id'])->update($final_update_array);
    }
}

