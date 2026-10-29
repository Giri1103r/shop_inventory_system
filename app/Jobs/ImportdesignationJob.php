<?php

namespace App\Jobs;


use Shuchkin\SimpleXLSX;
use App\Models\Administration\UploadLog;
use Illuminate\Bus\Queueable;

use App\Models\Administration\UploadLogError;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\Master\Designation;
use Illuminate\Support\Facades\Session;

class ImportdesignationJob implements ShouldQueue
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
                // Header Column Validation
                if (count($row) !== 2) {
                    $error_data = array(
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Header Column not match',
                    );
                    $cond_error_datas[] = $error_data;
                    $i++;
                    break;
                }
                // Header row validation

                if (
                    trim($row[0]) != 'SNo' ||
                    trim($row[1]) != 'Designation Name'
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


            $designation_name = trim($row['1']);


            /* Column data validation */
            if ($designation_name == '') {

                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Designation name is missing',
                );

                $cond_error_datas[] = $cond_error_data;

                $i++;
                continue;
            }

            $designationExist = Designation::where('designation_name', $designation_name)->exists();

            if ($designationExist) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Designation Name Already Exists',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }

            $data = [
                'des_code' => '',
                'designation_name' => $designation_name,
                'created_by' => $this->details['user_id'],
            ];


            Designation::create($data);

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
