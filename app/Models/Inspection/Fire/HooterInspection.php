<?php

namespace App\Models\Inspection\Fire;

use Illuminate\Database\Eloquent\Model;

class HooterInspection extends Model
{
    protected $table = 'inspection_fire_hooter';

    protected $fillable = [
        'id',
        'doc_no',
        'issue_date',
        'revision_date',
        'date_of_inspection',
        'location',
        'shift',
        'next_due',
        'frequency',
        'checked_by',
        'verified_by',
        'approved_by',
        'description',
        'remarks',
        'inspection_status',
        'capa_recomendation',
        'capa_remarks',
        'level_one_manager_remarks',
        'level_two_manager_remarks',
        'capa_ehs_remarks',
        'l1_manager_verification',
        'l2_manager_verification',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];
}
