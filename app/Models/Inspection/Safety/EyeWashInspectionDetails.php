<?php

namespace App\Models\Inspection\Safety;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class EyeWashInspectionDetails extends Model
{
    protected $table = 'inspection_monthly_eyewash_details';

    protected $fillable = [
        'id',
        'inspection_id',
        'sr_no',
        'location',
        'resource_code',
        'inspection_condition',
        'value',
        'hand_free_stay_open_value',
        'foot_pedal_value',
        'eyewash_heads_value',
        'receptacle',
        'water',
        'quality',
        'pressure',
        'temperature',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];

    public function store($id)
    {
        $request = request();

        $sr_no = $request->sr_no;
        $location = $request->location;
        $resource_code = $request->resource_code;
        $condition = $request->condition;
        $value = $request->value;
        $hfsov = $request->hfsov;
        $foot_pedal = $request->foot_pedal;
        $eyewash_heads = $request->eyewash_heads;
        $water = $request->water;
        $quality = $request->quality;
        $pressure = $request->pressure;
        $temperature = $request->temperature;
        $receptacle = $request->receptacle;
        $remarks = $request->remarks;

        foreach ($sr_no as $index => $sr_no_value) {
            $data = array(
                'inspection_id' => $id,
                'sr_no' => $sr_no_value,
                'location' => decryptId($location[$index]),
                'receptacle' => $receptacle[$index],
                'resource_code' => $resource_code[$index],
                'inspection_condition' => decryptId($condition[$index]),
                'hand_free_stay_open_value' => $hfsov[$index],
                'foot_pedal_value' => $foot_pedal[$index],
                'eyewash_heads_value' => $eyewash_heads[$index],
                'water' => decryptId($water[$index]),
                'quality' => $quality[$index],
                'pressure' => $pressure[$index],
                'temperature' => $temperature[$index],
                'value' => $value[$index],
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
        static::addGlobalScope(new TrashScope('inspection_monthly_eyewash_details'));
    }
}
