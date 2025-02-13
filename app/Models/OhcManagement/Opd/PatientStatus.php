<?php

namespace App\Models\OhcManagement\Opd;

use Illuminate\Database\Eloquent\Model;

class PatientStatus extends Model
{
    protected $table = 'ohc_management_opd_patient_status';
    protected $primaryKey = 'id';

    protected $fillable = [
        'patient_status',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function getpatientstatus(){
        return $this->where('trash','NO')->where('status',1)->get();
    }
}
