<?php

namespace App\Models\IMS\Incident;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IMSStatus extends Model
{
    use  HasFactory;


    protected $table = 'ims_incident_status';
    protected $primaryKey = 'id';

    protected $fillable = [
        'to_status',
        'status_name',
        'bg_color',
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
