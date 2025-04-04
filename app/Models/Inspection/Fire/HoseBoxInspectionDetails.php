<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class HoseBoxInspectionDetails extends Model
{
    protected $table = 'inspection_fire_hose_box_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'inspection_id',
        'location',
        'sr_no',
        'hose_box_no',
        'hose_types',
        'quantity',
        'branch_quantity',
        'hose_box_key',
        'condition',
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
        $location = $request->location;
        $hose_box_no = $request->hose_box_no;
        $quantity = $request->quantity;
        $branch_quantity = $request->branch_quantity;
        $hose_types = $request->hose_types;
        $condition = $request->condition;
        $hose_box_key = $request->hose_box_key;
        $approach = $request->approach;
        $remarks = $request->remarks;
        
        foreach ($sr_no as $index => $sr_no_value) {
            $data = array(
                'inspection_id' => $id,
                'sr_no' => $sr_no_value,
                'location' => decryptId($location[$index]),
                'hose_box_no' => $hose_box_no[$index],
                'quantity' => $quantity[$index],
                'branch_quantity' => $branch_quantity[$index],
                'hose_types' => decryptId($hose_types[$index]),
                'hose_box_key' => decryptId($hose_box_key[$index]),
                'condition' => decryptId($condition[$index]),    
                'remarks' => $remarks[$index],
                'approach' => $approach[$index],
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
        static::addGlobalScope(new TrashScope('inspection_fire_hose_box_details'));
    }
}
