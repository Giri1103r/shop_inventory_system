<?php

namespace App\Models\Inspection\Fire\Master;

use Illuminate\Database\Eloquent\Model;

class LightType extends Model
{
    protected $table = 'inspection_fire_master_type_of_light';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
       'condition',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',

    ];

    public function gettypeoflight(){
        return $this->where('status',1)->get();
    }
}
