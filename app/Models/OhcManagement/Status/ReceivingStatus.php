<?php

namespace App\Models\OhcManagement\Status;

use Illuminate\Database\Eloquent\Model;

class ReceivingStatus extends Model
{
    protected $table = 'ohc_management_status';
    protected $primaryKey = 'id';

    protected $fillable = [

        'to_satus',
        'from_status',
        'status',
        'trash',
        'created_by',
        'updated_by',

    ];

    public function getStatus(){
        return $this->where('status',1)->get();
    }
}
