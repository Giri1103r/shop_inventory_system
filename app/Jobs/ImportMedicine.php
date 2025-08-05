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
use App\Models\OhcManagement\Master\Medicine;

class ImportMedicine
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

            if ($i == 1) {
                if (count($row) === 5) {
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
                    trim($row[0]) != 'SNO' ||
                    trim($row[1]) != 'Medicine Name' ||
                    trim($row[2]) != 'Pack' ||
                    trim($row[3]) != 'Threshold Limit' ||
                    trim($row[4]) != 'Remarks'
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
                $i++;
                continue;
            }

            $sno = trim($row[0]);
            $medicine_name = trim($row[1]);
            $pack = trim($row[2]);
            $threshold_limit = trim($row[2]);
            $remarks = trim($row[2]);

            if ($medicine_name == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Medicine Name is missing',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            $incident_type_nameExists = Medicine::where('medicine', $medicine_name)->where('status', 1)->get();
            if ($incident_type_nameExists->isNotEmpty()) {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Medicine Already Exist',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            if ($pack == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Pack name is missing',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            if ($threshold_limit == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Threshold Limit is missing',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            $user = $this->details['user'];
            $role = string_to_array($user->role);
            $allowed_roles = [ROLE_SUPERADMIN, ROLE_EHS_HEAD];

            if (!empty(array_intersect($role, $allowed_roles))) {
                $approve_status = STATUS_OHC_EHS_HEAD_APPROVED;
            }else{
                $approve_status = STATUS_OHC_EHS_HEAD_APPROVAL_PENDING;
            }


            $data = array(
                'medicine' => $medicine_name,
                'pack' => $pack,
                'threshold_limit' => $threshold_limit,
                'remarks' => $remarks,
                'approve_status' => $approve_status,
                'created_by' => $this->details['user_id']
            );

            Medicine::create($data);

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
