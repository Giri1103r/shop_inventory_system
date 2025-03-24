<?php

namespace App\Models\Inspection\GembaWalk;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GembaWalkStatus extends Model
{
    use  HasFactory;
    protected $table = 'inspection_gemba_walk_status';

    protected $primaryKey = 'id';

    protected $fillable = [
        'to_status',
        'status_name',
        'bg_color',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'updated_at',
        'created_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];
}
