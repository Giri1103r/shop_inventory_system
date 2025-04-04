<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class CoTypeFireExtinguisherDetails extends Model
{

    protected $table = 'inspection_co_type_fire_extinguisher_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'inspection_id',
        'sr_no',
        'fire_point_no',
        'department',
        'location',
        'type',
        'capacity',
        'quantity',
        'discharge_tube',
        'discharge_horn',
        'weight_of_co2_in_fe',
        'safety_pin',
        'approach',
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

        $sr_no = $request->sr_no;
        $fire_point_no = $request->fire_point_no;
        $department = $request->department;
        $location = $request->location;
        $type = $request->type;
        $capacity = $request->capacity;
        $quantity = $request->quantity;
        $discharge_tube = $request->discharge_tube;
        $discharge_horn = $request->discharge_horn;
        $weight_of_co2_in_fe = $request->weight_of_co2_in_fe;
        $safety_pin = $request->safety_pin;
        $approach = $request->approach;
        $remarks = $request->remarks;

        foreach ($sr_no as $index => $sr_no_value) {
            $data = array(
                'inspection_id' => $id,
                'sr_no' => $sr_no_value,
                'fire_point_no' => $fire_point_no[$index],
                'department' => decryptId($department[$index]),
                'location' => decryptId($location[$index]),
                'type' => decryptId($type[$index]),
                'capacity' => $capacity[$index],
                'quantity' => $quantity[$index],
                'discharge_tube' => decryptId($discharge_tube[$index]),
                'discharge_horn' => decryptId($discharge_horn[$index]),
                'weight_of_co2_in_fe' => $weight_of_co2_in_fe[$index],
                'safety_pin' => decryptId($safety_pin[$index]),
                'approach' => $approach[$index],
                'remarks' => $remarks[$index],
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
        static::addGlobalScope(new TrashScope('inspection_co_type_fire_extinguisher_details'));
    }

}
