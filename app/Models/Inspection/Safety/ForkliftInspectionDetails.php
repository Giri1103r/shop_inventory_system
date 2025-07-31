<?php

namespace App\Models\Inspection\Safety;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class ForkliftInspectionDetails extends Model
{
    protected $table = 'inspection_safety_forklift_inspection_details';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'inspection_id',
        'department_id',
        'unit_id',
        'identification_no',
        'exact_location',
        'observation',
        'correction_preventive_action',
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
        'trash' => 'NO',
    ];

    public function store($id)
    {
        $request = request();



        $department = $request->department;
        $unit = $request->unit;
        $identification_no = $request->identification_no;
        $observation = $request->observation;
        $corrective_action = $request->corrective_action;
        $emp_id = $request->emp_id;
        $date_of_compliance = $request->date_of_compliance;
        $observation_status = $request->observation_status;
        $remarks = $request->remarks;
        $exact_location = $request->exact_location;


        foreach ($department as $index => $sr_no_value) {
            $data = array(
                'inspection_id' => $id,
                'department_id' => decryptId($department[$index]),
                'unit_id' =>  decryptId($unit[$index]),
                'identification_no' => $identification_no[$index],
                'exact_location' => $exact_location[$index],
                'observation' => $observation[$index],
                'correction_preventive_action' => ($corrective_action[$index]),
                'responsibility' => ($emp_id[$index]),
                'date_of_compliance' => DBdateformat($date_of_compliance[$index]),
                'observation_status' => decryptId($observation_status[$index]),
                'remarks' => $remarks[$index],
                'created_by' => Auth::id(),
            );
            $data =  $this->create($data);
        }
    }

    public function store_api($id)
    {
        $request = request();
        $department = $request->department;
        $unit = $request->unit;
        $identification_no = $request->identification_no;
        $observation = $request->observation;
        $corrective_action = $request->corrective_action;
        $emp_id = $request->emp_id;
        $date_of_compliance = $request->date_of_compliance;
        $observation_status = $request->observation_status;
        $remarks = $request->remarks;


        foreach ($department as $index => $sr_no_value) {
            $data = array(
                'inspection_id' => $id,
                'department_id' => ($department[$index]),
                'unit_id' =>  ($unit[$index]),
                'identification_no' => $identification_no[$index],
                'observation' => $observation[$index],
                'correction_preventive_action' => ($corrective_action[$index]),
                'responsibility' => ($emp_id[$index]),
                'date_of_compliance' => DBdateformat($date_of_compliance[$index]),
                'observation_status' => ($observation_status[$index]),
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

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_safety_forklift_inspection_details'));
    }
}
