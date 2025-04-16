<?php

namespace App\Models\OhcManagement\Status;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MedicineLog extends Model
{
    protected $table = 'ohc_medicine_log';
    protected $primaryKey = 'id';

    protected $fillable = [
        'creator_id',
        'unit_id',
        'medicine_id',
        'quantity',
        'type',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];

    public function store($user_medicine_issuance_unit)
    {
        $request = request();

        $insertedData = [];

        foreach ($request->medicine_id as $index => $medicine) {
            $insert_array = [
                'creator_id' => $user_medicine_issuance_unit->id,
                'medicine_id' => decryptId($medicine),
                'unit_id' =>1,
                'type' => TYPE_OHC_ISSUANCE,
                'quantity' => $request->quantity[$index],
                'created_by' => Auth::id(),
            ];

            $insertedData[] = $this->create($insert_array);
        }

        return $insertedData;
    }

    public function updates($creatorlog)
    {
        $request = request();

        foreach ($request->medicine_id as $index => $medicine) {
            $update_data = [
                'creator_id' =>  $creatorlog->id,
                'medicine_id' => ($medicine),
                'unit_id' =>1,
                'quantity' => $request->quantity[$index],
                'type' => TYPE_OHC_ISSUANCE,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];


            $existingRecord = self::where('creator_id',$creatorlog->id)
                ->where('medicine_id', $medicine)->where('trash', 'NO')
                ->first();

            if ($existingRecord) {
                $existingRecord->update($update_data);
            } else {
                self::create($update_data);
            }
        }
    }


    public function getissuedDate($selectedYear, $selectedMonth, $ids)
    {
        $data =    $this
            ->join('ohc_master_medicine', 'ohc_medicine_log.medicine_id', '=', 'ohc_master_medicine.id')
            ->whereIn('creator_id', $ids)
            ->whereYear('ohc_medicine_log.created_at', $selectedYear)
            ->whereMonth('ohc_medicine_log.created_at', $selectedMonth)
            ->select('ohc_master_medicine.medicine as medicine_name', 'ohc_medicine_log.created_at', 'quantity')
            ->get();

        return $data;
    }
    public function getYearlyissuedDate($selectedYear, $ids)
    {
        return $this
            ->join('ohc_master_medicine', 'ohc_medicine_log.medicine_id', '=', 'ohc_master_medicine.id')
            ->whereIn('creator_id', $ids)
            ->whereYear('ohc_medicine_log.created_at', $selectedYear)
            ->select('ohc_master_medicine.medicine as medicine_name', 'ohc_medicine_log.created_at', 'quantity')
            ->get();
    }




    public function getquantity($id)
    {
        return $this->where('creator_id', $id)->where('type', TYPE_OHC_ISSUANCE)->get();
    }
}
