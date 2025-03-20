<?php

namespace App\Models\Inspection\Safety;

use App\Scopes\TrashScope;
use Illuminate\Database\Eloquent\Model;

class EyeWashInspectionDetails extends Model
{
    protected $table = 'inspection_monthly_eyewash_details';

    protected $fillable = [
        'id',
        'sr_no',
        'location_id',
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

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_monthly_eyewash_details'));

    }
}
