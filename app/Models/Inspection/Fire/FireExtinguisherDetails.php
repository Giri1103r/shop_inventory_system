<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class FireExtinguisherDetails extends Model
{
    protected $table = 'inspection_fire_fire_extinguisher_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'inspection_id',
        'department',
        'location',
        'sr_no',
        'description',
        'type',
        'capacity',
        'quantity',
        'cylinder_pressure',
        'discharge_tube',
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
        $department = $request->department;
        $description = $request->description;
        $location = $request->location;
        $quantity = $request->quantity;
        $type = $request->type;
        $capacity = $request->capacity;
        $cylinder_pressure = $request->cylinder_pressure ?? [];
        $discharge_tube = $request->discharge_tube ?? [];
        $approach = $request->approach ?? [];
        $safety_pin = $request->safety_pin ?? [];
        $remarks = $request->remarks;

        foreach ($sr_no as $index => $sr_no_value) {
            $data = array(
                'inspection_id' => $id,
                'sr_no' => $sr_no_value,
                'description' => $description[$index],
                'department' => decryptId($department[$index]),
                'location' => decryptId($location[$index]),
                'remarks' => $remarks[$index],
                'type' => decryptId($type[$index]),
                'quantity' => $quantity[$index],
                'capacity' => $capacity[$index],
                'cylinder_pressure' => $cylinder_pressure[$index],
                'discharge_tube' => decryptId($discharge_tube[$index]),
                'safety_pin' => decryptId($safety_pin[$index]),
                'approach' => $approach[$index],
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
        static::addGlobalScope(new TrashScope('inspection_fire_fire_extinguisher_details'));
    }
}
