<?php

namespace App\Models\Inspection\Fire;

use Illuminate\Database\Eloquent\Model;

class FireStatusLog extends Model
{
    protected $table = 'inspection_fire_status_log';
    protected $primaryKey = 'id';

    protected $fillable = [
        'inspection_id',
        'type',
        'from_status',
        'to_status',
        'remarks',
        'approved_by',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function selectOne($id, $type)
    {
       return $this->where('inspection_id', $id)->where('type', $type)->get();
    }
}
