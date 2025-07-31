<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class HydrantRiserInspectionDetails extends Model
{

    protected $table = 'inspection_fire_hydrant_riser_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'inspection_id',
        'location_check_id',
        'hydrant_no',
        'lugs_id',
        'rubber_washer',
        'check_nut',
        'spindle_wheel',
        'blank_cap',
        'female_coupling',
        'lever',
        'flow_test',
        'physical_condition',
        'condition_of_ivs',
        'approach',
        'remarks',
        'status',
        'trash',
        'created_by',
        'updated_by',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];


    public function selectOne($id)
    {
        return $this->where('inspection_id', $id)->where('status', 1)->where('trash', 'NO')->first();
    }



    public function store($id)
    {
        $request = request();

        $exact_location = $request->exact_location;
        $hydrant_no = $request->hydrant_no;
        $lugs = $request->lugs;
        $rubber_washer = $request->rubber_washer;
        $check_nut = $request->check_nut;
        $spindle_wheel = $request->spindle_wheel;
        $blank_cap = $request->blank_cap;
        $female_coupling = $request->female_coupling;
        $lever = $request->lever;
        $flow_test = $request->flow_test;
        $physical_condition = $request->physical_condition;
        $condition_of_ivs = $request->condition_of_ivs;
        $approach = $request->approach;
        $remarks = $request->remarks;

        foreach ($exact_location as $index => $location_id) {
            $data = [
                'inspection_id' => $id,
                'location_check_id' => ($exact_location),
                'hydrant_no' => $hydrant_no[$index],
                'lugs_id' => decryptId($lugs[$index]),
                'rubber_washer' => decryptId($rubber_washer[$index]),
                'check_nut' => decryptId($check_nut[$index]),
                'spindle_wheel' => decryptId($spindle_wheel[$index]),
                'blank_cap' => decryptId($blank_cap[$index]),
                'female_coupling' => decryptId($female_coupling[$index]),
                'lever' => decryptId($lever[$index]),
                'flow_test' => decryptId($flow_test[$index]),
                'physical_condition' => decryptId($physical_condition[$index]),
                'condition_of_ivs' => decryptId($condition_of_ivs[$index]),
                'approach' => decryptId($approach[$index]),
                'remarks' => $remarks[$index],
                'created_by' => Auth::id(),
            ];

            $this->create($data);
        }

        return back()->with('success', 'Data saved successfully');
    }

     public function storeApi($id)
    {
        $request = request();

        $location = $request->location_check_id;
        $hydrant_no = $request->hydrant_no;
        $lugs = $request->lugs;
        $rubber_washer = $request->rubber_washer;
        $check_nut = $request->check_nut;
        $spindle_wheel = $request->spindle_wheel;
        $blank_cap = $request->blank_cap;
        $female_coupling = $request->female_coupling;
        $lever = $request->lever;
        $flow_test = $request->flow_test;
        $physical_condition = $request->physical_condition;
        $condition_of_ivs = $request->condition_of_ivs;
        $approach = $request->approach;
        $remarks = $request->remarks;

        foreach ($location as $index => $location_id) {
            $data = [
                'inspection_id' => $id,
                'location_check_id' => $location_id,
                'hydrant_no' => $hydrant_no[$index],
                'lugs_id' => $lugs[$index],
                'rubber_washer' => $rubber_washer[$index],
                'check_nut' => $check_nut[$index],
                'spindle_wheel' => $spindle_wheel[$index],
                'blank_cap' => $blank_cap[$index],
                'female_coupling' => $female_coupling[$index],
                'lever' => $lever[$index],
                'flow_test' => $flow_test[$index],
                'physical_condition' => $physical_condition[$index],
                'condition_of_ivs' => $condition_of_ivs[$index],
                'approach' => $approach[$index],
                'remarks' => $remarks[$index],
                'created_by' => Auth::id(),
            ];

            $this->create($data);
        }

        return back()->with('success', 'Data saved successfully');
    }


    public function GetDetails($id)
    {
        return $this->where('inspection_id', $id)->where('status', 1)->where('trash', 'NO')->get();
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_fire_hydrant_riser_details'));
    }

}
