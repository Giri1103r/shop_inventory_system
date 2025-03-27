<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmergencyBuyerFirstAidChecklist extends Model
{
    use  HasFactory;

    protected $table = 'inspection_ohc_emergency_buyer_first_aid_bag_checklist_details';
    protected $primaryKey = 'id';

    protected $fillable = [
        'date_of_inspection',
        'location_first_aid_bag',
        'shift',
        'due_date',
        'unit_id',
        'frequency',
        'inspection_data',
        'remark_by',
        'created_by',
        'updated_by',
        'status',
        'trash',
    ];
    

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];
}
