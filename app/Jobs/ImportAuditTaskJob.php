<?php

namespace App\Jobs;

use App\Models\Inspection\audit\Master\Task;

use Illuminate\Foundation\Queue\Queueable;
use App\Models\UploadLog;
use Shuchkin\SimpleXLSX;
use App\Models\UploadLogError;
use Illuminate\Support\Facades\Session;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;


use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportAuditTaskJob implements ShouldQueue
// class ImportAuditTaskJob
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
                    trim($row['1']) != 'Task Name'
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
            $task_name = trim($row['1']);

            if ($task_name == '') {
                $cond_error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Task name is missing',
                );
                $cond_error_datas[] = $cond_error_data;
                $i++;
                continue;
            }

            $data = [
                'task_auto_id' => getsequence('audit_task'),
                'task_name' => $task_name,
                'created_by' => $this->details['user_id'],
            ];

            Task::create($data);
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
