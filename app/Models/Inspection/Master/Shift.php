<?php

namespace App\Models\Inspection\Master;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{

    protected $table = 'inspection_shift_option';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'shift',
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

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_audit'));
    
    }
}
