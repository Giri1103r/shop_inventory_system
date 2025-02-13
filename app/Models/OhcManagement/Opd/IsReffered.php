<?php

namespace App\Models\OhcManagement\Opd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class IsReffered extends Model
{
    protected $table = 'ohc_management_opd_patient_is_refered';
    protected $primaryKey = 'id';
    protected $fillable = [
        'opd_id  ',
        'hospital_name',
        'first_aider',
        'mobile_no',
        'refered_by_vechicle',
        'other_vechicle',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function store($opd_patient)
    {
        $request = request();

            $insert_array = [
                'opd_id' => $opd_patient->id,
                'hospital_name' =>  $request->hospital_name,
                'first_aider' => $request->first_aider,
                'mobile_no' => $request->is_reffered_mobile_no,
                'refered_by_vechicle'=> $request->vechicle,
                'other_vechicle'=> $request->other_vechicle,
                'created_by' => Auth::id(),
            ];


           $this->create($insert_array);
        }

}
