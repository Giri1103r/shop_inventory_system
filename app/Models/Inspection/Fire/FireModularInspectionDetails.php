<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class FireModularInspectionDetails extends Model
{
    protected $table = 'inspection_fire_modular_inspection_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'inspection_id',
        'department',
        'location',
        'resource_code',
        'types_of_equipment',
        'capacity_of_equipment',
        'working_temperature',
        'sprinkler_head',
        'neck_ring',
        'cylinder_pressure',
        'remarks',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'status',
        'trash',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];

    public function selectOne($id)
    {
        return $this->where('inspection_id', $id)->where('status', 1)->where('trash', 'NO')->first();
    }

    public function store($id)
    {
        $request = request();

        $department = $request->department;
        $location = $request->location;
        $cylinder_pressure = $request->cylinder_pressure;
        $neck_ring = $request->neck_ring;
        $sprinkler_head = $request->sprinkler_head;
        $working_temperature = $request->working_temperature;
        $capacity_of_equipment = $request->capacity_of_equipment;
        $types_of_equipment = $request->types_of_equipment;
        $remarks = $request->remarks;
        $resource_code = $request->resource_code;

        foreach ($location as $index => $location) {
            $data = array(
                'inspection_id' => $id,
                'department' => decryptId($department[$index]),
                'location' => decryptId($location),
                'cylinder_pressure' => $cylinder_pressure[$index],
                'neck_ring' => ($neck_ring[$index]),
                'remarks' => $remarks[$index],
                'sprinkler_head' => $sprinkler_head[$index],
                'types_of_equipment' => $types_of_equipment[$index],
                'capacity_of_equipment' => $capacity_of_equipment[$index],
                'working_temperature' => $working_temperature[$index],
                'resource_code' => $resource_code[$index],
                'created_by' => Auth::id(),
            );



            $this->create($data);
        }
    }

    public function GetDetails($id)
    {
        return $this->where('inspection_id', $id)->where('status', 1)->where('trash', 'NO')->get();
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_fire_modular_inspection_details'));
    }
}
