<?php

namespace App\Models\IMS\Incident;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IncidentInvestigation extends Model
{
    use  HasFactory;


    protected $table = 'ims_initial_incident_investigation';
    protected $primaryKey = 'id';

    protected $fillable = [
        'incident_id',
        'witness_id',
        'anything_damaged',
        'root_cause_analysis',
        'action_taken',
        'corrective_preventive_action',
        'responsible_person_id',
        'remark',
        'risk_analysis',
        'risk_analysis_remark',
        'investigation_date',
        'investigation_time',
        'main_root_cause',
        'leading_factors',
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

        $commaSeparatedDamaged = is_array($request->anything_damaged)
        ? implode(',', $request->anything_damaged)
        : $request->anything_damaged;
    
        $insert_array = array(
            'incident_id' => decryptId($request->incident_id),
            'witness_id' => !empty($request->witness_id) && is_array($request->witness_id)
                ? implode(',', array_map('decryptId', $request->witness_id))
                : null,
            'anything_damaged' => $commaSeparatedDamaged,
            'root_cause_analysis' => $request->root_cause ?? null,
            'action_taken' => $request->action_taken,
            'corrective_preventive_action' => $request->corrective_preventive_action,
            'remark' => $request->remark,
            'risk_analysis' => $request->risk_analysis,
            'risk_analysis_remark' => $request->risk_analysis_remark,
            'investigation_date' => DBdateformat($request->investigation_date),
            'investigation_time' => $request->investigation_time,
            'main_root_cause' => $request->main_root_cause,
            'leading_factors' => $request->leading_factors,
            'created_by' => Auth::id()
        );

        // dd($insert_array); // Debugging

        return $this->create($insert_array);
    }


    // protected static function booted()
    // {
    //     static::addGlobalScope(new TrashScope('ims_initial_incident_investigation'));

    //     static::created(function ($model) {

    //         $uniqueId = 'INCIDENT-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
    //         $model->update(['sr_no' => $uniqueId]);
    //     });
    // }
}
