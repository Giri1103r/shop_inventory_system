<?php

namespace App\Models\Inspection\Safety;

use Illuminate\Database\Eloquent\Model;

class ForkliftInspectionDetails extends Model
{
    protected $table = 'inspection_safety_forklift_inspection_details';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'inspection_id',
        'department_id',
        'unit_id',
        'identification_no',
        'observation',
        'corrective_preventive_action',
        'responsibility',
        'date_of_compliance',
        'observation_status',
        'remarks',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];
}
