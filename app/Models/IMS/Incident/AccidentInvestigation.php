<?php

namespace App\Models\IMS\Incident;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccidentInvestigation extends Model
{
    use  HasFactory;


    protected $table = 'ims_accident_investigation';
    protected $primaryKey = 'id';

    protected $fillable = [
        'accident_id',
        'witness_id',
        'is_damaged',
        'root_cause_analysis',
        'action_taken',
        'is_treatment',
        'details',
        'corrective_preventive_action',
        'responsible_person_id',
        'target_date',
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
        'trash' => 'NO',
    ];


    public function store()
    {
        $request = request();

        $decryptedWitnessIds = array_map('decryptId', $request->witness_id);

        $commaSeparatedWitness = implode(',', $decryptedWitnessIds);

        $decryptedDamaged = array_map('decryptId', $request->is_damaged);

        $commaSeparatedDamaged = implode(',', $decryptedDamaged);

        $insert_array = array(
            'accident_id' => decryptId($request->accident_id),
            'witness_id' => $commaSeparatedWitness,
            'is_damaged' => $commaSeparatedDamaged,
            'root_cause_analysis' => $request->root_cause_analysis,
            'action_taken' => $request->action_taken,
            'is_treatment' => $request->is_treatment,
            'details' => $request->details,
            'corrective_preventive_action' => $request->corrective_preventive_action,
            'responsible_person_id' => decryptId($request->responsible_person_id),
            'target_date' => DBdateformat($request->target_date),
            'remark' => $request->remark,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }


}
