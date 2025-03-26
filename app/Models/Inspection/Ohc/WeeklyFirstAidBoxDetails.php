<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;

class WeeklyFirstAidBoxDetails extends Model
{
    protected $table = 'inspection_ohc_weekly_first_aid_box_inspection_checklist_details';
    protected $primaryKey = 'id';

    protected $fillable = [
        'reference_id',
        'medicine_id',
        'available_quantity',
        'freeze_quantity',
        'material_expiry',
        'remarks',
        'created_by',
        'updated_by'
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];
    
}
