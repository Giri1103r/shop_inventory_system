<?php

namespace App\Models\Inspection\Fire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class FireMockDrillInspectionDetails extends Model
{
    protected $table = 'inspection_fire_mock_drill_observation_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'inspection_id',
        'observation',
        'date_of_observation',
        'shift_id',
        'unit_id',
        'capa_remarks',
        'action_taken',
        'emp_id',
        'date_of_compliance',
        'date_of_clousure',
        'observation_status',
        'remarks',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'status',
        'trash',
    ];

    protected $attribute = [
        'status' => 1,
        'trash' => 'NO',
    ];


    public function store($id)
    {
        $request = request();


        $observation = $request->observation;
        $date_of_observation = $request->date_of_observation;
        $shift = $request->shift;
        $unit = $request->unit;
        $corrective_action = $request->corrective_action;
        $action_taken = $request->action_taken;
        $emp_id = $request->emp_id;
        $date_of_compliance = $request->date_of_compliance;
        $observation_status = $request->observation_status;
        $remarks = $request->remarks;


        foreach ($observation as $index => $sr_no_value) {
            $data = array(
                'fire_mock_id' => $id,
                'observation' => ($observation[$index]),
                'date_of_observation' => DBdateformat($date_of_observation[$index]),
                'shift_id' => decryptId($shift[$index]),
                'unit_id' =>  decryptId($unit[$index]),
                'capa_remarks' => $corrective_action[$index],
                'action_taken' => $action_taken[$index],
                'emp_id' => ($emp_id[$index]),
                'date_of_compliance' => DBdateformat($date_of_compliance[$index]),
                'observation_status' => decryptId($observation_status[$index]),
                'remarks' => $remarks[$index],
                'created_by' => Auth::id(),
            );
            $data =  $this->create($data);
        }
    }

    public function GetDetails($id)
    {
        return $this->where('inspection_id', $id)->get();
    }
}
