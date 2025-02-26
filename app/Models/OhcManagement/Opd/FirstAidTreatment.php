<?php

namespace App\Models\OhcManagement\Opd;

use App\Models\OhcManagement\Report\Inventory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FirstAidTreatment extends Model
{
    protected $table = 'ohc_management_opd_patient_first_aid';
    protected $primaryKey = 'id';
    protected $fillable = [
        'opd_id',
        'medicine_id',
        'quantity',
        'available_quantity',
        'remarks',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function store($opd_patient)
    {
        $request = request();

        $insertedData = [];

        foreach ($request->medicine_id as $index => $medicine) {
            $insert_array = [
                'opd_id' => $opd_patient->id,
                'medicine_id' => decryptId($medicine),
                'quantity' => $request->quantity[$index],
                'available_quantity' => $request->available_quantity[$index],
                'remarks' => $request->remarks[$index],
                'created_by' => Auth::id(),
            ];


            $insertedData[] = $this->create($insert_array);
        }

        return $insertedData;
    }

    public function updates($id)
    {
        $request = request();

        foreach ($request->medicine_id as $index => $medicine) {
            $inventory = Inventory::where('medicine_id', $medicine)
                ->where('unit_id', Auth::user()->unit_id)
                ->first();

            $availableQuantity = $inventory ? $inventory->balance : 0;

            // Prepare data for update or insert
            $update_data = [
                'opd_id' => $id,
                'medicine_id' => $medicine,
                'quantity' => $request->quantity[$index],
                'available_quantity' => $availableQuantity,
                'remarks' => $request->remarks[$index],
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];

            // Check if record exists
            $existingRecord = self::where('opd_id', $id)
                ->where('medicine_id', $medicine)
                ->where('trash', 'NO')
                ->first();
            if ($existingRecord) {
                $existingRecord->update($update_data);
            } else {
                Inventory::where('medicine_id', $medicine)
                    ->where('unit_id', Auth::user()->unit_id)
                    ->increment('total_prescribe', $request->quantity[$index]);

                Inventory::where('medicine_id', $medicine)
                    ->where('unit_id', Auth::user()->unit_id)
                    ->decrement('balance', $request->quantity[$index]);

                // Create a new record

                self::create($update_data);
            }
        }
    }

    public function Selectone($id)
    {
        return $this->where('opd_id', $id)->where('status', 1)->where('trash', 'No')->get();
    }
    public function deleterecord($ids)
    {

        $update_data = array(
            'status' => 0,
            'trash' => 'YES',
        );

        return $this->where('id', $ids)->update($update_data);
    }

    public function firstdata($ids)
    {

        $data = $this->select(
            'ohc_management_opd_patient_first_aid.*'
        )->where('id', $ids)->where('trash', 'NO')
            ->first();

        return $data;
    }
}
