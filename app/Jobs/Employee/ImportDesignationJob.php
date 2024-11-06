<?php

namespace App\Jobs\Employee;

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
use App\Models\Master\Designation;

 class ImportDesignationJob implements ShouldQueue
//class ImportDesignationJob 
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


        $xlsx = SimpleXLSX::parse(private_storage($this->details['path']));
        $cond_error_datas = [];
       
        foreach ($xlsx->rows() as $row) {
          
            $sno = trim($row['0']);
            $designation_name = trim($row['1']);

            /*
             * Header column validation
             */
            if ($i == 1) {

                if (count($row) == 2) {

                    if (
                        $sno != 'S.No' ||
                        $designation_name != 'Designation Name'
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



            if ($designation_name == '') {

                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Designation name is missing',
                );

                $cond_error_datas[] = $cond_error_data;

                $i++;
                continue;
            } else {

                $designationExist = Designation::where('designation', $designation_name)->get();
                try {

                    if (count($designationExist) > 0) {
                        $cond_error_data = array(
                            'upload_id' => $this->details['log_id'],
                            'line_no' => $i,
                            'error' => 'Designation Name Already Exist',
                        );
                        $cond_error_datas[] = $cond_error_data;
                        $i++;
                        continue;
                    }

                } catch (\Exception $ex) {
                    $cond_error_data = array(
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Invalid Data',
                    );
                    $cond_error_datas[] = $cond_error_data;
                }
            }


            $data = array(
                'designation' => $designation_name,
                'created_by' => $this->details['user_id']
            );

            Designation::create($data);

            $i++;
        }

        if(count($cond_error_datas) > 0){
            UploadLogError::insert($cond_error_datas);
        }

        $final_update_array = array(
            'upload_status' => 2,
        );

        UploadLog::where('id', $this->details['log_id'])->update($final_update_array);
    }
}
