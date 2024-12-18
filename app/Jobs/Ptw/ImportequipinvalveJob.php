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

        $update_array = [
            'upload_status' => 1,
        ];

        UploadLog::where('id', $this->details['log_id'])->update($update_array);

        $xlsx = SimpleXLSX::parse($this->details['path']);
        $cond_error_datas = [];

        foreach ($xlsx->rows() as $row) {
            $sno = trim($row['0']);
            $equip_involve = trim($row['1']);

            if ($i == 1) {
                if (count($row) == 2) {
                    if ($sno != 'SNo' || $equip_involve != 'Equipment Involved') {
                        $error_data_1 = [
                            'upload_id' => $this->details['log_id'],
                            'line_no' => $i,
                            'error' => 'Header Column Name Not Match',
                        ];
                        $cond_error_datas[] = $error_data_1;
                        break;
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
                    break;
                }
            }

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

            $equip_involveExist = EquipInvalve::where('equip_involve', $equip_involve)->exists();
            if ($equip_involveExist) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Name is already Exists',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }

            $data = [
                'equip_involve' => $equip_involve,
                'created_by' => $this->details['user_id'],
            ];
            EquipInvalve::create($data);

            $i++;
        }

        if (count($cond_error_datas) > 0) {
            UploadLogError::insert($cond_error_datas);
            $final_update_array = [
                'upload_status' => 3,
            ];
            Session::flash('error', 'Failed to upload. Please check the upload log.');
        } else {
            $final_update_array = [
                'upload_status' => 2,
            ];
            Session::flash('success', 'Upload completed successfully.');
        }

        UploadLog::where('id', $this->details['log_id'])->update($final_update_array);
    }
}
