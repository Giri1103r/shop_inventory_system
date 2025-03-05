<?php

namespace App\Models\IMS\Incident;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiskAnalysis extends Model
{
    use  HasFactory;


    protected $table = 'ims_master_incident_riskanalysis';
    protected $primaryKey = 'id';

    protected $fillable = [
        'incident_id',
        'accident_id',
        'fire_id',
        'risk_level',
        'description_ca',
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


    public function store()
    {
        $request = request();

        $insert_array = array(
            'incident_id' => decryptId($request->incident_id) ?? null,
            'accident_id' => decryptId($request->accident_id) ?? null ,
            'fire_id' => decryptId($request->fire_incident_id) ?? null ,
            'risk_level' => $request->risk_level,
            'description_ca' => $request->description_ca,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

}
