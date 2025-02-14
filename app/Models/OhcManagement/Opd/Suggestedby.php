<?php

namespace App\Models\OhcManagement\Opd;

use Illuminate\Database\Eloquent\Model;

class Suggestedby extends Model
{
    protected $table = 'ohc_management_opd_patient_suggested_by';
    protected $primaryKey = 'id';

    protected $fillable = [
        'suggested_by',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function getSuggestedBy(){
        return $this->where('trash','NO')->where('status',1)->get();
    }

}
