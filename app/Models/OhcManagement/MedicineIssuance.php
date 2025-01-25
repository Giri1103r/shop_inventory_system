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
    public function store($user_medicine_issuance){
        $request = request();


        foreach($request->medicine_id as $index => $medicine) {

            $insert_array = [
                'reference_id' => $user_medicine_issuance->id,
                'medicine_id' => $medicine,
                'quantity' => $request->quantity[$index],
                'available_quantity' => $request->available_quantity[$index],
                'created_by' => Auth::id(),
            ];

            // Insert the data
            $this->create($insert_array);
        }
    }
}
