<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Illuminate\Database\Eloquent\Model;

class HoseBoxType extends Model
{
    protected $table = 'inspection_fire_hose_box_type';

    protected $fillable = [
        'id',
        'name',
        'status',
        'trash',
    ];

    public function getTypes()
    {
        return $this->where('status',1)->where('trash','NO')->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_fire_hose_box_type'));
    }
}
