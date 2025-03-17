<?php

namespace App\Models\Inspection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class GembaWalkChecklistFile extends Model
{
    use  HasFactory;

    protected $table = 'inspection_gemba_walk_checklist_files';

    protected $fillable = [
        'gemba_walk_id',
        'gemba_walk_checklist_id',
        'file_path',
        'file_name',
        'file_orgname',
        'file_extension',
        'created_by',
        'updated_by',
        'status',
        'trash'
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];
}
