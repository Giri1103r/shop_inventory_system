<?php

namespace App\Models\Inspection\MSDS;

use Illuminate\Database\Eloquent\Model;

class MSDSStatusLog extends Model
{
    protected $table = 'inspection_msds_status_log';
    protected $primaryKey = 'id';

    protected $fillable = [
        'msds_details_id',
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
       return $this->where('msds_details_id', $id)->get();
    }
}
