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
use App\Models\OhcManagement\MedicineIssuance;
use App\Models\OhcManagement\Report\Inventory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

// class ImportIssuancejob implements ShouldQueue
class ImportIssuancejob
{

    // use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $details;
    protected $medicnieissuance;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details,$medicnieissuance)
    {


        $this->details = $details;
        $this->medicnieissuance = $medicnieissuance;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $i = 1;

        $update_array = ['upload_status' => 1];

        UploadLog::where('id', $this->details['log_id'])->update($update_array);

        $xlsx = SimpleXLSX::parse($this->details['path']);

        $cond_error_datas = [];
        $medicine_issuance = [];

        foreach ($xlsx->rows() as $row) {
            $sno = trim($row['0']);
            $medicinename = trim($row['1']);
            $availablequantity = trim($row['2']);
            $quantity = trim($row['3']);

            // Header validation
            if ($i == 1) {
                if (count($row) == 4) {
                    if (
                        $sno != 'SNo' ||
                        $medicinename != 'Medicine Name' ||
                        $availablequantity != 'Available Quantity' ||
                        $quantity != 'Quantity'
                    ) {
                        $cond_error_datas[] = [
                            'upload_id' => $this->details['log_id'],
                            'line_no' => $i,
                            'error' => 'Header Column Name Not Match',
                        ];
                        $i++;
                        continue; // Instead of break
                    }
                } else {
                    $cond_error_datas[] = [
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Header Column Not Match',
                    ];
                    $i++;
                    continue; // Instead of break
                }
                $i++;
                continue;
            }

            // Column data validation
            if (empty($medicinename)) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Name is missing',
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

            // Check if medicine exists
            $medicine = Medicine::whereRaw('LOWER(medicine) = ?', [$medicinename])
                ->where('status', 1)
                ->first();

            if (!$medicine) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Medicine not found in database: ' . $medicinename,
                ];
                $i++;
                continue;
            }

            // Try issuing medicine
            try {
                $issued_medicine = MedicineIssuance::create([
                    'reference_id' => $this->medicnieissuance->id,
                    'medicine_id' => $medicine->id,
                    'available_quantity' => $availablequantity,
                    'quantity' => $quantity,
                    'created_by' => Auth::id(),
                ]);
                $medicine_issuance[] = $issued_medicine;
            } catch (\Exception $e) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Failed to issue medicine: ' . $e->getMessage(),
                ];
                $i++;
                continue;
            }

            $i++;
        }

        // Update inventory for successful entries
        foreach ($medicine_issuance as $medicine) {
            $medicine_id = $medicine->medicine_id;
            $unitId = $this->medicnieissuance->unit_id;
            $issuedQuantity = $medicine->quantity;

            Inventory::where('medicine_id', $medicine_id)->where('unit_id', 1)->decrement('balance', $issuedQuantity);
            Inventory::where('medicine_id', $medicine_id)->where('unit_id', 1)->increment('total_issue', $issuedQuantity);
            Inventory::where('medicine_id', $medicine_id)->where('unit_id', $unitId)->increment('total_purchase', $issuedQuantity);
            Inventory::where('medicine_id', $medicine_id)->where('unit_id', $unitId)->increment('balance', $issuedQuantity);
            Inventory::where('medicine_id', $medicine_id)->where('unit_id', $unitId)->increment('total_received', $issuedQuantity);

        }

        if (!empty($cond_error_datas)) {
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
