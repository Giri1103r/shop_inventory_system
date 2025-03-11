<?php

namespace App\Jobs\Ohc;

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
use App\Models\OhcManagement\Master\Medicine;
use App\Models\OhcManagement\MedicineRequisition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ImportRequisitionjob implements ShouldQueue
// class ImportRequisitionjob
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $details;

    protected $medicnieRequisition;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details, $medicnieRequisition)
    {


        $this->details = $details;
        $this->medicnieRequisition = $medicnieRequisition;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $i = 1;
        $update_array = [
            'upload_status' => 1,
        ];

        UploadLog::where('id', $this->details['log_id'])
            ->update($update_array);

        $xlsx = SimpleXLSX::parse($this->details['path']);
        $cond_error_datas = [];
        $medicine_names = [];

        $data_count = 0;

        foreach ($xlsx->rows() as $row) {
            $sno = trim($row[0]);
            $medicinename = trim($row[1]);
            $availablequantity = trim($row[2]);
            $quantity = trim($row[3]);
            $remarks = trim($row[4]);

            /**
             * Header column validation
             */
            if ($i == 1) {
                if (count($row) == 5) {
                    if (
                        $sno !== 'SNo' ||
                        $medicinename !== 'Medicine Name' ||
                        $availablequantity !== 'Available Quantity' ||
                        $quantity !== 'Quantity' ||
                        $remarks !== 'Remarks'
                    ) {
                        $cond_error_datas[] = [
                            'upload_id' => $this->details['log_id'],
                            'line_no' => $i,
                            'error' => 'Header Column Name Not Match',
                        ];
                        $i++;
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
                    $i++;
                    break;
                }
            }

            /**
             * Column data validation
             */
            if ($medicinename == '') {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Medicine Name is missing',
                ];
                $i++;
                continue;
            }

            if (in_array($medicinename, $medicine_names)) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Duplicate Medicine Name found: ' . $medicinename,
                ];
                $i++;
                continue;
            }

            if ($quantity > $availablequantity) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Quantity cannot exceed Available Quantity',
                ];
                $i++;
                continue;
            }

            $medicine = Medicine::where('medicine', $medicinename)->where('status', 1)->first();
            if (!$medicine) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Medicine not found in database: ' . $medicinename,
                ];
                $i++;
                continue;
            }

            $medicineExists = MedicineRequisition::where('medicine_id', $medicine->id)->where('reference_id', $this->medicnieRequisition->id)
                ->where('status', 1)
                ->exists();

            if ($medicineExists) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Medicine is already exist',
                ];
                $i++;
                continue;
            }

            // Add medicine name to the tracking array
            $medicine_names[] = $medicinename;

            MedicineRequisition::create([
                'req_id' => $this->medicnieRequisition->id,
                'medicine_id' => $medicine->id,
                'available_quantity' => $availablequantity,
                'quantity' => $quantity,
                'remarks' => $remarks,
                'created_by' => 1,
            ]);

            $data_count++; // Increase valid data count
            $i++;
        }

        // Ensure exactly 5 data rows
        if ($data_count > 5) {
            $cond_error_datas[] = [
                'upload_id' => $this->details['log_id'],
                'line_no' => 0,
                'error' => 'The total number of rows (excluding header) must not exceed 5.',
            ];
        }


        if (count($cond_error_datas) > 0) {
            UploadLogError::insert($cond_error_datas);
            $final_update_array = ['upload_status' => 3];
            Session::flash('error', 'Failed to upload. Please check the upload log.');
        } else {
            $final_update_array = ['upload_status' => 2];
            Session::flash('success', 'Upload completed successfully.');
        }

        UploadLog::where('id', $this->details['log_id'])->update($final_update_array);
    }
}
