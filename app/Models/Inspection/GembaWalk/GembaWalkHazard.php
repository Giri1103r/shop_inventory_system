<?php

namespace App\Models\Inspection\GembaWalk;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GembaWalkHazard extends Model
{
     use  HasFactory;
    protected $table = 'inspection_gemba_walk_hazard';

    protected $primaryKey = 'id';

    protected $fillable = [
        'hazard_name',
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

    public function getHazardName(){
        return $this->where('status',1)->get();
    }
}
