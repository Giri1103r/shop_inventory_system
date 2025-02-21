<?php

namespace App\Models\OhcManagement\Opd;

use Illuminate\Database\Eloquent\Model;

class InjuredCondition extends Model
{
    protected $table = 'ohc_opd_injured_condition';
    protected $primaryKey = 'id';

    protected $fillable = [

        'injured_condtion',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function getinjurred(){
        return $this->where('trash','NO')->where('status',1)->get();
    }

}
