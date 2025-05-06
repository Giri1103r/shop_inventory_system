<?php

namespace App\Models\Inspection\Ohc\Master;

use Illuminate\Database\Eloquent\Model;

class PersonalDetails extends Model
{
    protected $table = 'inspection_ohc_physical_examination_clinical';

    protected $primaryKey = 'id';

    protected $fillable = [
        'personal_details',
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

    public function personalDetails(){
        return $this->where('status',1)->get();
    }
}
