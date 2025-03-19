<?php

namespace App\Models\Inspection;

use Illuminate\Database\Eloquent\Model;

class RRAAStatusLog extends Model
{
    protected $table = 'inspection_rraa_status_log';
    protected $primaryKey = 'id';

    protected $fillable = [
        'rraa_details_id',
        'from_status',
        'to_status',
        'remarks',
        'approved_by',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function selectOne($id)
    {
       return $this->where('rraa_details_id', $id)->get();
    }
}
