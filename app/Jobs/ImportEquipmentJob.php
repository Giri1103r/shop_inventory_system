<?php

namespace App\Jobs;

use Shuchkin\SimpleXLSX;
use App\Models\UploadLog;
use App\Models\UploadLogError;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Session;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Inspection\Safety\Master\Equipment;

class ImportEquipmentJob implements ShouldQueue
// class ImportEquipmentJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $details;
    /**
     * Create a new job instance.
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
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

            if ($i == 1) {
                if (count($row) === 2) {
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
                    trim($row['0']) != 'S.No' ||
                    trim($row['1']) != 'Equipment Name'
                ) {
                    $error_data = array(
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Header Column Name Not Match',
                    );
                    $cond_error_datas[] = $error_data;
                    break;
                }
                $i++;
                continue;
            }

            $sno = trim($row['0']);
            $equipment_name = trim($row['1']);

            if ($equipment_name == '') {
                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Equipment name is missing',
                );
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            } else {
                $equipment_Exists = Equipment::where('equipment_name', $equipment_name)->first();
                if ($equipment_Exists) {
                    $cond_error_data = [
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Equipment Name Already Exists!',
                    ];
                    $cond_error_datas[] = $cond_error_data;
                    $i++;
                    continue;
                }
            }
            $data = [
                'equipment_name' => $equipment_name,
                'created_by' => $this->details['user_id'],
            ];

            Equipment::create($data);
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
