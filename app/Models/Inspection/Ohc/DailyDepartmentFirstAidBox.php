<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DailyDepartmentFirstAidBox extends Model
{
    protected $table = 'inspection_ohc_daily_department_first_aid_box';

    protected $primaryKey = 'id';

    protected $fillable = [
        'reference_id',
        'medicine_id',
        'freeze_quantity',
        'available_quantity',
        'material_expiry',
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
                'reference_id' => $OhcDetails->id,
                'medicine_id' => decryptId($medicine),
                'freeze_quantity' => $request->freeze_quantity[$index] ?? 0,  // Default to 0 if missing
                'available_quantity' => $request->available_quantity[$index] ?? 0,
                'material_expiry' => isset($request->material_expiry[$index]) ? DBdateformat($request->material_expiry[$index]) : null,
                'quantity' => $request->quantity[$index] ?? 0,
                'remarks' => $request->remarks[$index] ?? '',
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
