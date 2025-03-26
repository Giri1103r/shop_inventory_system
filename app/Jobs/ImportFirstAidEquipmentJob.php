<?php

namespace App\Jobs;

use App\Models\Inspection\Ohc\Master\FirstAidEquipment;
use App\Models\OhcManagement\Master\Medicine;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\UploadLog;
use Shuchkin\SimpleXLSX;
use Illuminate\Support\Facades\Session;
use App\Models\UploadLogError;

// class ImportFirstAidEquipmentJob implements ShouldQueue
class ImportFirstAidEquipmentJob

{
    use Queueable;

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

            $sno = trim($row[0]);
            $medicine_name = trim($row[1]);
            $freeze_quantity = trim($row[2]);
           

            if ($i == 1) {
                if (count($row) <= 3) {
                    if (
                        $sno != 'S.No' ||
                        $medicine_name != 'Medicine Name' ||
                        $freeze_quantity != 'Freeze Quantity'
                    ) {
                        $error_data_1 = array(
                            'upload_id' => $this->details['log_id'],
                            'line_no' =>  $i,
                            'error' => 'Header Column Name Not Match',
                        );
                        Session::flash('error', 'Header Column Name Not Match!');
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
                    Session::flash('error', 'Header Column  Not Match!');
                    $cond_error_datas[] = $error_data_1;
                    $i++;
                    break;
                }
            }

            if ($medicine_name == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Medicine Name is missing',
                );
                Session::flash('error', 'Medicine Name is missing.');
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            $medicine_Exists = Medicine::where('medicine', $medicine_name)->get();
            try {
                if (count($medicine_Exists) <=0  ) {
                    $cond_error_data = array(
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Invalid Medicine Name',
                    );
                    Session::flash('error', 'Import unsuccessfull!, Please check the upload logs');
                    $cond_error_datas[] = $cond_error_data;
                    $i++;
                    continue;
                }
                $medicine_id =  $medicine_Exists['0']->id;
            } catch (\Exception $ex) {
                report($ex);
            }

            $Exist = FirstAidEquipment::where('medicine_id', $medicine_id)->get();
            try {
                if (count($Exist) > 0) {
                    $cond_error_data = array(
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Medicine Name Already Exist',
                    );
                    Session::flash('error', 'Import unsuccessfull!, Please check the upload logs');
                    $cond_error_datas[] = $cond_error_data;
                    UploadLogError::insert($cond_error_data);
                    $i++;
                    continue;
                }
            } catch (\Exception $ex) {
                report($ex);
            }


            if ($freeze_quantity == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Freeze Quantity is missing',
                );
                Session::flash('error', 'Freeze Quantity is missing.');
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }
            
            $data = [
                'medicine_id' => $medicine_id,
                'freeze_quantity'=>$freeze_quantity,
                'created_by' => $this->details['user_id'],
            ];

            FirstAidEquipment::create($data);
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
