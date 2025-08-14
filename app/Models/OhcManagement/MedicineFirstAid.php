<?php

namespace App\Models\OhcManagement;

use App\Models\OhcManagement\Report\Inventory;
use App\Scopes\TrashScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MedicineFirstAid extends Model
{
    protected $table = 'ohc_management_medicine_first_aid';
    protected $primaryKey = 'id';

    protected $fillable = [
        'reference_id',
        'medicine_id',
        'quantity',
        'available_quantity',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];
    public function store($user_medicine_issuance)
    {
        $request = request();

        $insertedData = [];

        foreach ($request->medicine_id as $index => $medicine) {
            $insert_array = [
                'reference_id' => $user_medicine_issuance->id,
                'medicine_id' => decryptId($medicine),
                'quantity' => $request->quantity[$index],
                'available_quantity' => $request->available_quantity[$index],
                'created_by' => Auth::id(),
            ];


            $insertedData[] = $this->create($insert_array);
        }

        return $insertedData;
    }

    public function updates($id, $user_medicine_first_aid)
    {
        $request = request();

        foreach ($request->medicine_id as $index => $medicine_id) {
            $unitId = $user_medicine_first_aid->unit_id;

            // Fetch inventory data
            $inventory = Inventory::where('medicine_id', $medicine_id)
                ->where('unit_id', $unitId)
                ->first();

            $availableQuantity = $inventory ? $inventory->balance : 0;

            // Prepare data for update or insert
            $update_data = [
                'reference_id' => $id,
                'medicine_id' => $medicine_id,
                'quantity' => $request->quantity[$index],
                'available_quantity' => $availableQuantity,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];

            // Check if record exists
            $existingRecord = self::where('reference_id', $id)
                ->where('medicine_id', $medicine_id)
                ->where('trash', 'NO')
                ->first();

            if ($existingRecord) {
                // Update the existing record
                $existingRecord->update($update_data);
            } else {
                // Update inventory before creating a new record
                Inventory::where('medicine_id', $medicine_id)
                    ->where('unit_id', $unitId)
                    ->increment('total_first_aid', $request->quantity[$index]);

                Inventory::where('medicine_id', $medicine_id)
                    ->where('unit_id', $unitId)
                    ->decrement('balance', $request->quantity[$index]);

                // Create a new record
                self::create($update_data);
            }
        }
    }


    public function selectOne($id)
    {

        $data = $this->select(
            'ohc_management_medicine_first_aid.*'
        )->where('reference_id', $id)->where('trash', 'NO')
            ->get();

        return $data;
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
            'ohc_management_medicine_first_aid.*'
        )->where('id', $ids)->where('trash', 'NO')
            ->first();

        return $data;
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ohc_management_medicine_first_aid'));
    }
}
