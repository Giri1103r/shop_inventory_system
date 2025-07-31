<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class SprinklarSystemInspectionDetails extends Model
{
    protected $table = 'inspection_fire_sprinklar_system_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'inspection_id',
        'department',
        'exact_location',
        'resource_code',
        'quantity',
        'water_leakage',
        'painting',
        'qbd',
        'condition_of_flow_meter',
        'main_isolation',
        'drain_condition',
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

        $sr_no = $request->sr_no;
        $exact_location = $request->exact_location;
        $department = $request->department;
        $resource_code = $request->resource_code;
        $quantity = $request->quantity;
        $water_leakage = $request->water_leakage;
        $painting = $request->painting;
        $qbd = $request->qbd;
        $condition_of_flow_meter = $request->condition_of_flow_meter;
        $main_isolation = $request->main_isolation;
        $drain_condition = $request->drain_condition;
        $remarks = $request->remarks;

        foreach ($sr_no as $index => $sr_no_value) {
            $data = array(
                'inspection_id' => $id,
                'sr_no' => $sr_no_value,
                'department' => decryptId($department[$index]),
                'resource_code' => $resource_code[$index],
                'exact_location' => $exact_location[$index],
                'quantity' => $quantity[$index],
                'water_leakage' => decryptId($water_leakage[$index]),
                'painting' => decryptId($painting[$index]),
                'qbd' => decryptId($qbd[$index]),
                'condition_of_flow_meter' => decryptId($condition_of_flow_meter[$index]),
                'main_isolation' => decryptId($main_isolation[$index]),
                'drain_condition' => decryptId($drain_condition[$index]),
                'remarks' => $remarks[$index],
                'created_by' => Auth::id(),
            );

            $this->create($data);
        }
    }

    public function storeApi($id)
    {
        $request = request();

        $sr_no = $request->sr_no;
        $department = $request->department;
        $resource_code = $request->resource_code;
        $quantity = $request->quantity;
        $water_leakage = $request->water_leakage;
        $painting = $request->painting;
        $qbd = $request->qbd;
        $condition_of_flow_meter = $request->condition_of_flow_meter;
        $main_isolation = $request->main_isolation;
        $drain_condition = $request->drain_condition;
        $remarks = $request->remarks;

        foreach ($sr_no as $index => $sr_no_value) {
            $data = array(
                'inspection_id' => $id,
                'sr_no' => $sr_no_value,
                'department' => $department[$index],
                'resource_code' => $resource_code[$index],
                'quantity' => $quantity[$index],
                'water_leakage' => $water_leakage[$index],
                'painting' => $painting[$index],
                'qbd' => $qbd[$index],
                'condition_of_flow_meter' => $condition_of_flow_meter[$index],
                'main_isolation' => $main_isolation[$index],
                'drain_condition' => $drain_condition[$index],
                'remarks' => $remarks[$index],
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
        static::addGlobalScope(new TrashScope('inspection_fire_sprinklar_system_details'));
    }
}
