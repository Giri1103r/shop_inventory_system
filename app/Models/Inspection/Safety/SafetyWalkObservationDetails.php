<?php

namespace App\Models\Inspection\Safety;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class SafetyWalkObservationDetails extends Model
{
    protected $table = 'inspection_safety_walk_observation_details';

    protected $fillable = [
        'id',
        'safety_walk_observation_id',
        'location',
        'observation',
        'observation_date',
        'recomended_action',
        'responsibility',
        'date_of_compliance',
        'observation_status',
        'remarks',
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

    public function store($id)
    {
        $request = request();

        $location = $request->location;
        $observation = $request->observation;
        $recomended_action = $request->recomended_action;
        $responsibility = $request->emp_id;
        $date_of_compliance = $request->date_of_compliance;
        $observation_status = $request->observation_status;
        $remarks = $request->remarks;
        $date_of_observation = $request->date_of_observation;


        foreach ($location as $index => $sr_no_value) {
            $data = array(
                'safety_walk_observation_id' => $id,
                'sr_no' => $sr_no_value,
                'location' => decryptId($location[$index]),
                'observation' => $observation[$index],
                'recomended_action' => $recomended_action[$index],
                'responsibility' => $responsibility[$index],
                'date_of_compliance' => DBdateformat($date_of_compliance[$index]),
                'observation_status' => decryptId($observation_status[$index]),
                'observation_date' => DBdateformat($date_of_observation[$index]),
                'remarks' => $remarks[$index],
                'created_by' => Auth::id(),
            );
            $data =  $this->create($data);
            $safetyFiles = new SafetyWalkObservationFile();
            $safetyFiles->store($data->id, $index, $request->checklist_file);
        }
    }

    public function GetDetails($id)
    {
        return $this->where('safety_walk_observation_id', $id)->get();
    }

    public function GetLastMonthDetails($lastMonthObservationDetails)
    {
        if (count($lastMonthObservationDetails) > 0) {
            foreach ($lastMonthObservationDetails as $lastMonthObservationDetail) {
                $lastMonth[] = $this->where('safety_walk_observation_id', $lastMonthObservationDetail->id)->get();
            }
            return $lastMonth;
        }
        return false;
    }
}
