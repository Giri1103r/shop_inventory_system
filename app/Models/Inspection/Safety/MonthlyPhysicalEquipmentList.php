<?php

namespace App\Models\Inspection\Safety;

use Illuminate\Database\Eloquent\Model;

class MonthlyPhysicalEquipmentList extends Model
{
    protected $table = 'inspection_fire_equipment_inspection_list';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'equipment_name',
        'status',
        'trash',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function getEquipmentList()
    {
        return $this->get();
    }
}
