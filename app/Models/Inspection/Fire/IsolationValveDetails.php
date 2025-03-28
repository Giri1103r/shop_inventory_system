<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class IsolationValveDetails extends Model
{
    protected $table = 'inspection_fire_isolation_valve_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'inspection_id',
        'location_isv',
        'sr_no',
        'resource_code',
        'size_isv',
        'isv_status',
        'wheel_operation',
        'leakage',
        'type',
        'open',
        'close',
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
        $location_isv = $request->location_isv;
        $resource_code = $request->resource_code;
        $size_isv = $request->size_isv;
        $wheel_operation = $request->wheel_operation;
        $leakage = $request->leakage;
        $type = $request->type;
        $open = $request->open;
        $close = $request->close;
        $status_isv = $request->isv_status;
        $remarks = $request->remarks;
        
        foreach ($sr_no as $index => $sr_no_value) {
            $data = array(
                'inspection_id' => $id,
                'sr_no' => $sr_no_value,
                'location_isv' => $location_isv[$index],
                'resource_code' => $resource_code[$index],
                'size_isv' => $size_isv[$index],
                'isv_status' => decryptId($status_isv[$index]),
                'wheel_operation' => decryptId($wheel_operation[$index]),
                'leakage' => decryptId($leakage[$index]),
                'type' => decryptId($type[$index]),
                'open' => decryptId($open[$index]),
                'close' => decryptId($close[$index]),
                'remarks' => $remarks,
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
        static::addGlobalScope(new TrashScope('inspection_fire_isolation_valve_details'));
    }
}
