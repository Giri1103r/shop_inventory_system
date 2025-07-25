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
use App\Models\Inspection\Master\ChecklistType;
use App\Models\Inspection\Master\ChecklistSubType;
use App\Models\Inspection\Master\ChecklistSubTypeData;
use App\Models\Inspection\Master\ChecklistSubTypeDataName;

class ImportChecklistSubTypeData
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
                    trim($row[1]) != 'Checklist Type Name' ||
                    trim($row[2]) != 'Checklist Sub-Type Name' ||
                    trim($row[3]) != 'Checklist Sub-Type Data Name' ||
                    trim($row[4]) != 'Description'
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
            $checklist_sub_type = trim($row[2]);
            $checklist_sub_type_data = trim($row[3]);
            $description = trim($row[4]);

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


            if ($checklist_sub_type == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Checklist Sub Type is missing',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }
            $checklistsubtypeExists = ChecklistSubType::where('subcategory_name', $checklist_sub_type)
                ->where('category_id', $category_id)
                ->first();
            if (!$checklistsubtypeExists) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Invalid Checklist Sub Type',
                ];
                $i++;
                continue;
            }
            $sub_category_id = $checklistsubtypeExists->id;


            if ($checklist_sub_type_data == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Checklist Sub Type is missing',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            $checklistsubtypedataList = ChecklistSubTypeData::where('checklist_type_id', $category_id)
                ->where('checklist_sub_type_id', $sub_category_id)
                ->get();

            foreach ($checklistsubtypedataList as $checklistsubtypedata) {
                $checklistsubtypedatanameExists = ChecklistSubTypeDataName::where('checklist_sub_type_data_id', $checklistsubtypedata->id)
                    ->where('name', $checklist_sub_type_data)
                    ->exists();

                if ($checklistsubtypedatanameExists) {
                    $error_data = [
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Sub Type Data Name Already Exist',
                    ];
                    $cond_error_datas[] = $error_data;
                    $i++;
                    continue;
                }
            }

            $checklist_subtype_data = array(
                'checklist_type_id' => $category_id,
                'checklist_sub_type_id' => $sub_category_id,
                'created_by' => $this->details['user_id']
            );
            $checklistsubtypedata = ChecklistSubTypeData::create($checklist_subtype_data);

            $checklistsubtypedataname = [
                'description' => $description,
                'name' => $checklist_sub_type_data,
                'checklist_sub_type_data_id' => $checklistsubtypedata->id,
                'created_by' => $this->details['user_id'],
            ];

            ChecklistSubTypeDataName::create($checklistsubtypedataname);

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
