<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;

class FirstAidRecordStatusLog extends Model
{
    protected $table = 'ohc_first_aid_record_status_log';
    protected $primaryKey = 'id';

    protected $fillable = [
        'first_aid_record_details_id',
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
       return $this->where('first_aid_record_details_id	', $id)->get();
    }
}
