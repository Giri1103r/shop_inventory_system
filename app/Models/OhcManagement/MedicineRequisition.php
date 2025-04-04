<?php

namespace App\Models\OhcManagement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MedicineRequisition extends Model
{
    protected $table = 'ohc_management_medicine_requisition';
    protected $primaryKey = 'id';

    protected $fillable = [
        'req_id',
        'medicine_id',
        'available_quantity',
        'quantity',
        'remarks',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];
    public function store($user_medicine_requisition)
    {
        $request = request();


        foreach ($request->medicine_id as $index => $medicine) {

            $insert_array = [
                'req_id' => $user_medicine_requisition->id,
                'medicine_id' => decryptId($medicine),
                'available_quantity' => $request->available_quantity[$index],
                'quantity' => $request->quantity[$index],
                'remarks' => $request->remarks[$index],
                'created_by' => Auth::id(),
            ];


            $this->create($insert_array);
        }
    }

    public function updates($id)
    {
        $request = request();

        foreach ($request->medicine_id as $index => $medicine) {
            $decryptedMedicineId = decryptId($medicine);
            $update_data = [
                'req_id' => $id,
                'medicine_id' => ($decryptedMedicineId),
                'quantity' => $request->quantity[$index],
                'available_quantity' => $request->available_quantity[$index],
                'remarks' => $request->remarks[$index],
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];


            $existingRecord = self::where('req_id', $id)
                ->where('medicine_id', $decryptedMedicineId)->where('trash', 'NO')
                ->first();

            if ($existingRecord) {
                $existingRecord->update($update_data);
            } else {
                self::create($update_data);
            }
        }
    }
    public function statuschange($id)
    {
        $request = request();
        $type = $request->types;

        if ($type == 1) {
            $update_data = array(
                'status' => 0,
            );
        } else {
            $update_data = array(
                'status' => 1,
            );
        }

        return $this->where('req_id', $id)->update($update_data);
    }

    public function selectOne($id)
    {

        $data  = $this->select('ohc_management_medicine_requisition.*')->where('req_id', $id)->where('trash','NO')
            ->get();
        return $data;
    }

    public function deleterecord($ids){
        $update_data = array(
            'status' => 0,
            'trash' => 'YES',
        );

        return $this->where('id', $ids)->update($update_data);
    }
}
