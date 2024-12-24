<?php

namespace App\Jobs;

use App\Models\Master\PpeType;
use App\Models\UploadLog;
use App\Models\UploadLogError;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Shuchkin\SimpleXLSX;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
class ImportPpeTypeJob implements ShouldQueue
// class ImportPpeTypeJob
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * Create a new job instance.
     */

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
    public function handle()
    {
        $i = 1;
        $update_array = array(
            'upload_status' => 1,
        );

        UploadLog::where('id', $this->details['log_id'])
            ->update($update_array);

        $xlsx = SimpleXLSX::parse($this->details['path']);


        foreach ($xlsx->rows() as $row) {




            // Header column validation
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
                    trim($row[0]) != 'Sno' ||
                    trim($row[1]) != 'PPE Type'

                ) {
                    $error_data_1 = array(
                        'upload_id' => $this->details['log_id'],
                        'line_no' =>  $i,
                        'error' => 'Header Column Name Not Match',
                    );
                    $cond_error_datas[] = $error_data_1;
                    $i++;
                    break;
                }
                $i++;
                continue;
            }




            $sno = trim($row[0]);
            $ppename = trim($row[1]);

            if ($ppename == '') {
                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'PPE Type is missing',
                );

                $cond_error_datas[] = $cond_error_data;

                $i++;
                continue;
            } else {
                $companyExist = PpeType::where('ppe_type', $ppename)->get();
                if (count($companyExist) > 0) {
                    $cond_error_data = array(
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'PPE type Already Exist',
                    );

                    $cond_error_datas[] = $cond_error_data;

                    $i++;
                    continue;
                }
            }

            $data = [
                'ppe_type' => $ppename,
                'created_by' => Auth::id(),
                'created_at'=>now(),
            ];
           

            PpeType::create($data);
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
