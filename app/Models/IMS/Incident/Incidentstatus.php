<?php

namespace App\Models\IMS\Incident;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


use App\Scopes\TrashScope;

class Incidentstatus extends Model
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
        'created_at',
        'updated_at'

    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ims_incident_status'));
    }
}
