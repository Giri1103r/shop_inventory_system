<?php

namespace App\Models\OhcManagement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MedicineIssuance extends Model
{
    protected $table = 'ohc_management_medicine_issuance';
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

    public function updates($id)
    {
        $request = request();

        foreach ($request->medicine_id as $index => $medicine) {
            $update_data = [
                'reference_id' => $id,
                'medicine_id' => ($medicine),
                'quantity' => $request->quantity[$index],
                'available_quantity' => $request->available_quantity[$index],
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];


            $existingRecord = self::where('reference_id', $id)
                ->where('medicine_id', $medicine)->where('trash', 'NO')
                ->first();

            if ($existingRecord) {
                $existingRecord->update($update_data);
            } else {
                self::create($update_data);
            }
        }
    }


    public function selectOne($id)
    {

        $data = $this->select(
            'ohc_management_medicine_issuance.*'
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

        return $this->where('id',  $ids)->update($update_data);
    }

    public function firstdata($id)
    {

        $data = $this->select(
            'ohc_management_medicine_issuance.*'
        )->where('id', $id)->where('trash', 'NO')
            ->first();

        return $data;
    }



    public function getissuedDate($selectedYear, $selectedMonth, $ids)
    {
        return $this
            ->join('ohc_master_medicine', 'ohc_management_medicine_issuance.medicine_id', '=', 'ohc_master_medicine.id')
            ->whereIn('reference_id', $ids)
            ->whereYear('ohc_management_medicine_issuance.created_at', $selectedYear)
            ->whereMonth('ohc_management_medicine_issuance.created_at', $selectedMonth)
            ->select('ohc_master_medicine.medicine as medicine_name', 'ohc_management_medicine_issuance.created_at', 'quantity')
            ->get();
    }

    public function getYearlyissuedDate($selectedYear, $ids)
    {
        return $this
            ->join('ohc_master_medicine', 'ohc_management_medicine_issuance.medicine_id', '=', 'ohc_master_medicine.id')
            ->whereIn('reference_id', $ids)
            ->whereYear('ohc_management_medicine_issuance.created_at', $selectedYear)
            ->select('ohc_master_medicine.medicine as medicine_name', 'ohc_management_medicine_issuance.created_at', 'quantity')
            ->get();
    }
}
