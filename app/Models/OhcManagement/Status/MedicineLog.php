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

    public function store( $user_medicine_issuance){
        $request = request();

        $insertedData = [];

        foreach ($request->medicine_id as $index => $medicine) {
            $insert_array = [
                'creator_id' => $user_medicine_issuance->id,
                'medicine_id' => decryptId($medicine),
                'unit_id'=> $user_medicine_issuance->unit_id,
                'type'=>TYPE_OHC_ISSUANCE,
                'quantity' => $request->quantity[$index],
                'created_by' => Auth::id(),
            ];

            $insertedData[] = $this->create($insert_array);
        }

        return $insertedData;
    }

    public function getquantity($id)
    {
        return $this->where('creator_id',$id)->where('type',TYPE_OHC_ISSUANCE)->get();
    }
}
