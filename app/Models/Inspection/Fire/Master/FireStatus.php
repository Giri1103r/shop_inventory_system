<?php

namespace App\Models\Inspection\Fire\Master;

use Illuminate\Database\Eloquent\Model;

class FireStatus extends Model
{
    protected $table = 'inspection_fire_master_status';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',

    ];

    public function getStatus()
    {
        return $this->where('status', 1)->get();
    }
}
