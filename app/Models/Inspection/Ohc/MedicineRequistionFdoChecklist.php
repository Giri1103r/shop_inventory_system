<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MedicineRequistionFdoChecklist extends Model
{
    protected $table = 'inspection_ohc_medicine_requisition_slip_fdo';

    protected $primaryKey = 'id';

    protected $fillable = [
        'reference_id',
        'medicine_id',
        'quantity',
        'remarks',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function store($medicine_requisition_fdo_details)
    {
        $request = request();


        foreach ($request->medicine_id as $index => $medicine) {

            $insert_array = [
                'reference_id' =>   $medicine_requisition_fdo_details->id,
                'medicine_id' => decryptId($medicine),
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
