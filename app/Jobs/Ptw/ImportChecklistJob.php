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
use App\Models\Master\Checklist;
use Illuminate\Support\Facades\Session;

//  class ImportsafeworkJob implements ShouldQueue
class ImportChecklistJob
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
        // $xlsx = SimpleXLSX::parse(private_storage($this->details['path']));

        // dd($xlsx);
        $cond_error_datas = [];

        foreach ($xlsx->rows() as $row) {

            $sno = trim($row['0']);
            $checklist = trim($row['1']);

            /*
             * Header column validation
             */
            if ($i == 1) {

                if (count($row) == 2) {

                    if (
                        $sno != 'SNo' ||
                        $checklist != 'Checklist'
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
                } else {
                    $error_data_1 = array(
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Header Column Not Match',
                    );

                    $cond_error_datas[] = $error_data_1;
                    $i++;
                    break;
                }
            }

            /* Column data validation */



            if ($checklist == '') {

                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Name is missing',
                );

                $cond_error_datas[] = $cond_error_data;

                $i++;
                continue;
            }

            $checklistExist = Checklist::where('checklist', $checklist)->exists();
            if ($checklistExist) {
                $cond_error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Name is already Exists',
                ];
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }




            $data = array(
                'checklist' => $checklist,
                'created_by' => $this->details['user_id']
            );

            Checklist::create($data);

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
