<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MedicineRequisitionSlipFloor extends Model
{
    protected $table = 'inspection_ohc_medicine_requisition_slip_floor';

    protected $primaryKey = 'id';

    protected $fillable = [
        'reference_id',
        'medicine_id',
        'freeze_quantity',
        'quantity',
        'remarks',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function store($OhcDetails)
    {
        $request = request();


        foreach ($request->medicine_id as $index => $medicine) {

            $insert_array = [
                'reference_id' =>   $OhcDetails->id,
                'medicine_id' => decryptId($medicine),
                'freeze_quantity' => $request->freeze_quantity[$index],
                'quantity' => $request->quantity[$index],
                'remarks' => $request->remarks[$index],
                'created_by' => Auth::id(),
            ];


            $this->create($insert_array);
        }
    }

    public function Selectone($id)
    {
        return $this->where('reference_id', $id)->get();
    }
}
