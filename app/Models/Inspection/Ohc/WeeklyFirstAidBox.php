<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;

class WeeklyFirstAidBox extends Model
{

    protected $table = 'inspection_ohc_weekly_first_aid_box_inspection_checklist';
    protected $primaryKey = 'id';

    protected $fillable = [
        'doc_no',
        'issue_date',
        'revision_date',
        'date_of_inspection',
        'location',
        'first_aid_box_no',
        'first_aider',
        'shift',
        'unit',
        'created_by',
        'updated_by',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];
    
}
