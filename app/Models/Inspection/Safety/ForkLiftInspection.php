<?php

namespace App\Models\Inspection\Safety;

use Illuminate\Database\Eloquent\Model;

class ForkLiftInspection extends Model
{
    protected $table = 'inspection_safety_forklift_inspection';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'doc_no',
        'issue_date',
        'revision_data',
        'date_of_inspection',
        'inspection_status',
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
