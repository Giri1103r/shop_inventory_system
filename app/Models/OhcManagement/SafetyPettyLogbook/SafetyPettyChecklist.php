<?php

namespace App\Models\OhcManagement\SafetyPettyLogbook;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use App\Scopes\TrashScope;

class SafetyPettyChecklist extends Model
{
    use  HasFactory;

    protected $table = 'ohc_safety_petty_logbook_checklist';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'safety_petty_logbook_details_id',
        'serial_number',
        'employee_name',
        'employee_code',
        'department',
        'unit',
        'date',
        'amount',
        'description',
        'amount_given_by',
        'amount_received_by',
        'remark',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ]; 

}
