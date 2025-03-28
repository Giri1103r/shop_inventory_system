<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class FireAlarmInspectionDetails extends Model
{
    protected $table = 'inspection_fire_fire_alarm_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'inspection_id',
        'sr_no',
        'resource_code',
        'department',
        'quantity',
        'glass',
        'hammer',
        'mannual_call_point',
        'approach',
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
        $department = $request->department;
        $resource_code = $request->resource_code;
        $quantity = $request->quantity;
        $hammer = $request->hammer;
        $mannual_call_point = $request->mannual_call_point;
        $approach = $request->approach;
        $glass = $request->glass;
        $remarks = $request->remarks;
        
        foreach ($sr_no as $index => $sr_no_value) {
            $data = array(
                'inspection_id' => $id,
                'sr_no' => $sr_no_value,
                'department' => decryptId($department[$index]),
                'resource_code' => $resource_code[$index],
                'quantity' => $quantity[$index],
                'glass' => decryptId($glass[$index]),
                'hammer' => decryptId($hammer[$index]),
                'mannual_call_point' => decryptId($mannual_call_point[$index]),
                'approach' => $approach[$index],
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
        static::addGlobalScope(new TrashScope('inspection_fire_fire_valve_details'));
    }
}
