<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Illuminate\Database\Eloquent\Model;

class HooterInspectionDetails extends Model
{
    protected $table = 'inspection_fire_hooter_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'inspection_id',
        'department',
        'sr_no',
        'resource_code',
        'quantity',
        'blinking_light',
        'connection',
        'audiobility',
        'condition_of_hooter',
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
        return $this->where('inspection_id',$id)->where('status',1)->where('trash','NO')->first();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_fire_hooter_details'));
    }
}
