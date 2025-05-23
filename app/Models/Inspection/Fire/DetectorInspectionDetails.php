<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class DetectorInspectionDetails extends Model
{
    protected $table = 'inspection_fire_detector_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'inspection_id',
        'department',
        'sr_no',
        'resource_code',
        'detector_type',
        'physical_condition',
        'cable_condition',
        'response_indicator',
        'working_status',
        'remarks',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'status',
        'trash',
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
        $department = $request->department;
        $detector_type = $request->detector_type;
        $physical_condition = $request->physical_condition;
        $cable_condition = $request->cable_condition;
        $working_status = $request->working_status;
        $remarks = $request->remarks;
        $resource_code = $request->resource_code;
        $response_indicator = $request->response_indicator;

        foreach ($department as $index => $department) {
            $data = array(
                'inspection_id' => $id,
                'resource_code' => $resource_code[$index],
                'department' => decryptId($department),
                'detector_type' => decryptId($detector_type[$index]),
                'physical_condition' => decryptId($physical_condition[$index]),
                'cable_condition' => decryptId($cable_condition[$index]),
                'working_status' => decryptId($working_status[$index]),
                'remarks' => $remarks[$index],
                'response_indicator' => decryptId($response_indicator[$index]),
                'created_by' => Auth::id(),
            );
            $this->create($data);
        }
    }
    public function store_api($id)
    {
        $request = request();
        $department = $request->department;
        $detector_type = $request->detector_type;
        $physical_condition = $request->physical_condition;
        $cable_condition = $request->cable_condition;
        $working_status = $request->working_status;
        $remarks = $request->remarks;
        $resource_code = $request->resource_code;
        $response_indicator = $request->response_indicator;

        foreach ($department as $index => $department) {
            $data = array(
                'inspection_id' => $id,
                'resource_code' => $resource_code[$index],
                'department' => ($department),
                'detector_type' => ($detector_type[$index]),
                'physical_condition' => ($physical_condition[$index]),
                'cable_condition' => ($cable_condition[$index]),
                'working_status' => ($working_status[$index]),
                'remarks' => $remarks[$index],
                'response_indicator' => ($response_indicator[$index]),
                'created_by' => Auth::id(),
            );
            $this->create($data);
        }
    }

    public function GetDetails($id)
    {
        return $this->where('inspection_id', $id)->where('status', 1)->where('trash', 'NO')->get();
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_fire_detector_details'));
    }
}
