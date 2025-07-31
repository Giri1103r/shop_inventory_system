<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class HooterInspectionDetails extends Model
{
    protected $table = 'inspection_fire_hooter_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'inspection_id',
        'department',
        'exact_location',
        'sr_no',
        'resource_code',
        'quantity',
        'blinking_light',
        'connection',
        'audiobility',
        'condition_of_hooter',
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
        $check_items = $request->check_items;
        $quantity = $request->quantity;
        $blinking_light = $request->blinking_light ?? [];
        $connection = $request->connection ?? [];
        $auditbility = $request->auditbility ?? [];
        $remarks = $request->remarks;
        $resource_code = $request->resource_code;
        $exact_location = $request->exact_location;

        foreach ($sr_no as $index => $sr_no_value) {
            $data = array(
                'inspection_id' => $id,
                'sr_no' => $sr_no_value,
                'resource_code' => $resource_code[$index],
                'department' => decryptId($department[$index]),
                'remarks' => $remarks[$index],
                'exact_location' => $exact_location[$index],
                'condition_of_hooter' => $check_items[$index],
                'quantity' => $quantity[$index],
                'blinking_light' => isset($blinking_light[$index]) && $blinking_light[$index] === 'YES' ? 1 : 0,
                'audiobility' => isset($auditbility[$index]) && $auditbility[$index] === 'YES' ? 1 : 0,
                'connection' => isset($connection[$index]) && $connection[$index] === 'YES' ? 1 : 0,
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
        $check_items = $request->check_items;
        $quantity = $request->quantity;
        $blinking_light = $request->blinking_light ?? [];
        $connection = $request->connection ?? [];
        $auditbility = $request->auditbility ?? [];
        $remarks = $request->remarks;
        $resource_code = $request->resource_code;

        foreach ($sr_no as $index => $sr_no_value) {
            $data = array(
                'inspection_id' => $id,
                'sr_no' => $sr_no_value,
                'resource_code' => $resource_code[$index],
                'department' => $department[$index],
                'remarks' => $remarks[$index],
                'condition_of_hooter' => $check_items[$index],
                'quantity' => $quantity[$index],
                'blinking_light' => isset($blinking_light[$index]) && $blinking_light[$index] === 'YES' ? 1 : 0,
                'audiobility' => isset($auditbility[$index]) && $auditbility[$index] === 'YES' ? 1 : 0,
                'connection' => isset($connection[$index]) && $connection[$index] === 'YES' ? 1 : 0,
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
        static::addGlobalScope(new TrashScope('inspection_fire_hooter_details'));
    }
}
