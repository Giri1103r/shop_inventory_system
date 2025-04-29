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
        'remarks',
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

    $sr_no = $request->sr_no ?? [];
    $location = $request->location ?? [];
    $resource_code = $request->resource_code ?? [];
    $condition = $request->condition ?? [];
    $value = $request->value ?? [];
    $hfsov = $request->hfsov ?? [];
    $foot_pedal = $request->foot_pedal ?? [];
    $eyewash_heads = $request->eyewash_heads ?? [];
    $water = $request->water ?? [];
    $quality = $request->quality ?? [];
    $pressure = $request->pressure ?? [];
    $temperature = $request->temperature ?? [];
    $receptacle = $request->receptacle ?? [];
    $remarks = $request->remarks ?? [];

    foreach ($sr_no as $index => $sr_no_value) {
        $data = [
            'inspection_id' => $id,
            'sr_no' => $sr_no_value,
            'location' => isset($location[$index]) ? decryptId($location[$index]) : null,
            'receptacle' => $receptacle[$index] ?? null,
            'resource_code' => $resource_code[$index] ?? null,
            'inspection_condition' => isset($condition[$index]) ? decryptId($condition[$index]) : null,
            'hand_free_stay_open_value' => $hfsov[$index] ?? null,
            'foot_pedal_value' => $foot_pedal[$index] ?? null,
            'eyewash_heads_value' => $eyewash_heads[$index] ?? null,
            'water' => isset($water[$index]) ? decryptId($water[$index]) : null,
            'quality' => $quality[$index] ?? null,
            'pressure' => $pressure[$index] ?? null,
            'temperature' => $temperature[$index] ?? null,
            'value' => $value[$index] ?? null,
            'remarks' => $remarks[$index] ?? null,
            'created_by' => Auth::id(),
        ];

        $this->create($data);
    }

    return response()->json(['status' => 'success', 'message' => 'Data saved successfully.']);
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
