<?php

namespace App\Jobs;

use App\Models\UploadLog;
use App\Models\UploadLogError;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Session;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Shuchkin\SimpleXLSX;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Inspection\Master\ChecklistType;
use App\Models\Inspection\Master\ChecklistSubType;

class ImportChecklistSubtype
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $details;

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
    public function handle(): void
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
                if (count($row) === 3) {
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
                    trim($row[1]) != 'Checklist Type Name' ||
                    trim($row[2]) != 'Checklist Sub-Type Name'
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
            $checklist_type = trim($row[1]);
            $checklist_sub_tye = trim($row[2]);

            // Validate column data
            if ($checklist_type == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Checklist Type is missing',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            $checklistTypeExists = ChecklistType::where('category_name', $checklist_type)->first();
            if (!$checklistTypeExists) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Invalid Checklist Type',
                ];
                $i++;
                continue;
            }
            $category_id = $checklistTypeExists->id;


            if ($checklist_sub_tye == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Checklist Sub Type is missing',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }
            $checklistsubtypeExists = ChecklistSubType::where('subcategory_name', $checklist_sub_tye)
                ->where('category_id', $category_id)
                ->exists();

            if ($checklistsubtypeExists) {
                $error_data = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Sub Type Already Exist',
                ];
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }


            $data = array(
                'category_id' => $category_id,
                'subcategory_name' => $checklist_sub_tye,
                'created_by' => $this->details['user_id']
            );

            ChecklistSubType::create($data);

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
