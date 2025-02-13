<?php

namespace App\Models\OhcManagement\Opd;

use Illuminate\Database\Eloquent\Model;

class ReferedVechicle extends Model
{
    protected $table = 'ohc_management_opd_patient_refered_vechicle';
    protected $primaryKey = 'id';

    protected $fillable = [
        'refered_vechicle',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function getreffered(){
        return $this->where('trash','NO')->where('status',1)->get();
    }
}
