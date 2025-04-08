<?php

namespace App\Models\Inspection\Fire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EmergencyLightInspectionDetails extends Model
{
    protected $table = 'inspection_fire_emergency_light_inspection_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'inspection_id',
        'sr_no',
        'location',
        'department',
        'condition_of_light',
        'emergency_of_light',
        'type_of_light',
        'capacity',
        'quantity',
        'light_condition',
        'power_supply',
        'switch_condition',
        'fire_status',
        'remarks',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function store($id)
    {
        $request = request();

        $sr_no = $request->sr_no;
        $department = $request->department;
        $location = $request->location;
        $quantity = $request->quantity;
        $capacity = $request->capacity;
        $condition_of_light = $request->condition_of_light;
        $type_of_light = $request->type_of_light;
        $emergency_light_number = $request->emergency_light_number;
        $power_supply = $request->power_supply;
        $light_condition = $request->light_condition;
        $switch_condition = $request->switch_condition;
        $status = $request->status;
        $remarks = $request->remarks;

        foreach ($sr_no as $index => $sr_no_value) {
            $data = array(
                'inspection_id' => $id,
                'sr_no' => $sr_no_value,
                'department' => decryptId($department[$index]),
                'location' => $location[$index],
                'quantity' => $quantity[$index],
                'capacity' => $capacity[$index],
                'switch_condition' => decryptId($switch_condition[$index]),
                'emergency_of_light' => ($emergency_light_number[$index]),
                'condition_of_light' => decryptId($condition_of_light[$index]),
                'type_of_light' => decryptId($type_of_light[$index]),
                'power_supply' => decryptId($power_supply[$index]),
                'light_condition' => decryptId($light_condition[$index]),
                'fire_status' => decryptId($status[$index]),
                'remarks' => $remarks[$index],
                'trash' => 'NO',
                'created_by' => Auth::id(),
            );

            $this->create($data);
        }
    }

    public function selectOne($id)
    {
        return $this->where('inspection_id', $id)->where('status', 1)->where('trash', 'NO')->get();
    }


}
