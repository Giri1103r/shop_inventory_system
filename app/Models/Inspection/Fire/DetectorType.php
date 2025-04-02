<?php

namespace App\Models\Inspection\Fire;

use Illuminate\Database\Eloquent\Model;

class DetectorType extends Model
{
    protected $table = 'inspection_fire_detector_type';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'detector_type',
        'status',
        'trash',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function getDetectorType(){
        return $this->get();
    }
}
