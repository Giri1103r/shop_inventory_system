<?php

namespace App\Models\Inspection;

use Illuminate\Database\Eloquent\Model;

class GembaWalkStatusLog extends Model
{
    protected $table = 'inspection_gemba_walk_status_log';
    protected $primaryKey = 'id';

    protected $fillable = [
        'gemba_walk_id',
        'from_status',
        'to_status',
        'is_reject',
        'remarks',
        'status',
        'trash',
        'approved_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];

    public function selectOne($id){
        return $this->select('*')->where('gemba_walk_id', $id)->get();
    }
    
}
