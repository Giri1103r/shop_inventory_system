<?php

namespace App\Models\Inspection\Safety;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class FireSafetyEquipmentDetails extends Model
{
    protected $table = 'inspection_safety_equipment_details';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'safety_equipment_id',
        'standard_norms',
        'equipment_id',
        'item_code',
        'equipment_category',
        'measurement_unit',
        'minimum_order_level',
        'economic_order_quantity',
        'observation_status',
        'remark',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function UniqueCheck($data)
    {
        $unique =  $this->where('item_code',  $data)->get();
        if (count($unique) > 0) {
            return false;
        }
        return true;
    }

    public function ExistuniqueCheck($data)
    {
        $unique =  $this->where('item_code',  $data['category_name'])
            ->where('id', '!=', ($data['id']))
            ->get();

        if (count($unique) > 0) {
            return false;
        }
        return true;
    }

    public function store($id)
    {
        $request = request();


        $equipment_name = $request->equipment_name;
        $item_code = $request->item_code;
        $standard_norms = $request->standard_norms;
        $equipment_category = $request->equipment_category;
        $unit_of_measurement = $request->unit_of_measurement;
        $minimum_order_value = $request->minimum_order_value;
        $economic_order_quantity = $request->economic_order_quantity;
        $observation_status = ($request->observation_status);
        $remarks = $request->remarks;



        foreach ($item_code as $index => $item_code_value) {
            $data = array(
                'safety_equipment_id' => $id,
                'equipment_id' => decryptId($equipment_name[$index]),
                'item_code' => $item_code_value,
                'standard_norms' => decryptId($standard_norms[$index]),
                'equipment_category' => ($equipment_category[$index]),
                'measurement_unit' => $unit_of_measurement[$index],
                'economic_order_quantity' => $economic_order_quantity[$index],
                'minimum_order_level' => $minimum_order_value[$index],
                'observation_status' => decryptId($observation_status[$index]),
                'remark' => $remarks[$index],
                'created_by' => Auth::id(),
            );

            $this->create($data);
        }
    }



    public function GetDetails($id)
    {
        return $this->where('safety_equipment_id', $id)->where('status', 1)->where('trash', 'NO')->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_safety_equipment_details'));
    }
}
