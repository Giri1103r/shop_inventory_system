<?php

namespace App\Models\Master;

use App\Scopes\TrashScope;
use Illuminate\Database\Eloquent\Model;

class ForkLiftType extends Model
{
    protected $table = 'inspection_forklift_type';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'forklift',
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
    public function getForkLift()
    {
        return $this->where('status', 1)->get();
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_forklift_type'));
    }
}
