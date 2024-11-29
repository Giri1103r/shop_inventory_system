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

class ImportPpeTypeJob
{


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
       
        $cond_error_datas = [];


         try {
             foreach ($xlsx->rows() as $row) {
                 $sno = trim($row[0]);
                 $ppename = trim($row[1]);

                 // Header column validation
                 if ($i == 1) {
                     if (count($row) == 2) {
                         if ($sno != 'Sno' || $ppename != 'PPE Type') {
                             $cond_error_datas[] = [
                                 'upload_id' => $this->details['log_id'],
                                 'line_no' => $i,
                                 'error' => 'Header Column Name Not Match',
                             ];
                             Session::flash('error', 'Header Column Name Not Match');
                             break;
                         }
                         $i++;
                         continue;
                     } else {
                         $cond_error_datas[] = [
                             'upload_id' => $this->details['log_id'],
                             'line_no' => $i,
                             'error' => 'Header Column Not Match',
                         ];
                         Session::flash('error', 'Header Column Not Match');
                         break;
                     }
                 }

                 if ($ppename == '') {
                     $cond_error_datas[] = [
                         'upload_id' => $this->details['log_id'],
                         'line_no' => $i,
                         'error' => 'PPE Type is missing',
                     ];
                     Session::flash('error', 'PPE Type type is missing');
                     $i++;
                     continue;
                 } else {
                     $companyExist = PpeType::where('ppe_type', $ppename)->get();
                     if (count($companyExist) > 0) {
                         $cond_error_datas[] = [
                             'upload_id' => $this->details['log_id'],
                             'line_no' => $i,
                             'error' => 'PPE type Already Exist',
                         ];
                         Session::flash('error', 'PPE Type Already Exist');
                         $i++;
                         continue;
                     }
                 }

                 $data = [
                     'ppe_type' => $ppename,
                     'created_by' => Auth::id(),
                 ];

                 PpeType::create($data);
                 $i++;
             }
         } catch (Exception $ex) {
             $cond_error_datas[] = [
                 'upload_id' => $this->details['log_id'],
                 'line_no' => 'N/A',
                 'error' => 'Invalid Data',
             ];
         }

         if (count($cond_error_datas) > 0) {
             UploadLogError::insert($cond_error_datas);
         }

         // Final upload status update
         $final_update_array = ['upload_status' => count($cond_error_datas) > 0 ? 2 : 1]; // 2 for errors, 1 for success
         UploadLog::where('id', $this->details['log_id'])->update($final_update_array);
     }
    }

