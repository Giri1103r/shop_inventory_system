<?php

namespace App\Jobs;

use App\Models\IMS\Master\IncidentType;
use Shuchkin\SimpleXLSX;
use App\Models\UploadLog;
use App\Models\Master\Company;
use App\Models\UploadLogError;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Session;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportIncidentType
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
                    trim($row[1]) != 'Incident Type Name' ||
                    trim($row[2]) != 'Incident Type Short Name'
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
            $incident_type_name = trim($row[1]);
            $incident_type_short_name = trim($row[2]);

            if ($incident_type_name == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Incident Type Name is missing',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            $incident_type_nameExists = IncidentType::where('incident_type_name', $incident_type_name)->get();
            if ($incident_type_nameExists->isNotEmpty()) {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Incident Type Already Exist',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            if ($incident_type_short_name == '') {
                $error_data = array(
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Incident Type Short name is missing',
                );
                $cond_error_datas[] = $error_data;
                $i++;
                continue;
            }

            $data = array(
                'incident_type_name' => $incident_type_name,
                'short_name' => $incident_type_short_name,
                'created_by' => $this->details['user_id']
            );

            IncidentType::create($data);

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
