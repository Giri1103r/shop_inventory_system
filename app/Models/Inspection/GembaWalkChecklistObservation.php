<?php

namespace App\Models\Inspection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Support\Facades\Auth;

class GembaWalkChecklistObservation extends Model
{
    use  HasFactory;

    protected $table = 'inspection_gemba_walk_checklist_observations';

    protected $fillable = [
        'gemba_walk_id',
        'gemba_walk_checklist_id',
        'observation',
        'status',
        'trash',
        'created_by',
        'updated_by'
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];


  




}
