<?php

namespace App\Models\Inspection\Fire\Master;

use Illuminate\Database\Eloquent\Model;

class PowerSuply extends Model
{
    protected $table = 'inspection_fire_master_power_supply';

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

    public function getPowersupply(){
        return $this->where('status',1)->get();
    }
}
