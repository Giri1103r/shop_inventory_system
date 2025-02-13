<?php

namespace App\Models\OhcManagement\Opd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FirstAidTreatment extends Model
{
    protected $table = 'ohc_management_opd_patient_first_aid';
    protected $primaryKey = 'id';
    protected $fillable = [
        'opd_id  ',
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
                'created_by' => Auth::id(),
            ];


            $insertedData[] = $this->create($insert_array);
        }

        return $insertedData;
    }
}
