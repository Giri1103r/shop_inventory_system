<?php

namespace App\Models\Inspection\Ohc\Master;

use Illuminate\Database\Eloquent\Model;

class FamilyHistory extends Model
{
    protected $table = 'inspection_ohc_physical_examination_family_history';

    protected $primaryKey = 'id';

    protected $fillable = [
        'family_history',
        'created_by',
        'updated_by',
        'status',
        'trash',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function familyHistory(){
        return $this->where('status',1)->get();
    }
}

