<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class HoseReelHoseDetails extends Model
{
    protected $table = 'inspection_fire_hose_reel_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'inspection_id',
        'location',
        'sr_no',
        'department',
        'length',
        'nozzle',
        'hose',
        'flow',
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
        $department = $request->department;
        $length = $request->length;
        $nozzle = $request->nozzle;
        $hose = $request->hose;
        $condition = $request->condition;
        $flow = $request->flow;
        $approach = $request->approach;
        $remarks = $request->remarks;
        
        foreach ($sr_no as $index => $sr_no_value) {
            $data = array(
                'inspection_id' => $id,
                'sr_no' => $sr_no_value,
                'location' => decryptId($location[$index]),
                'department' => decryptId($department[$index]),
                'length' => $length[$index],
                'nozzle' => decryptId($nozzle[$index]),
                'hose' => decryptId($hose[$index]),
                'flow' => decryptId($flow[$index]),    
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
        static::addGlobalScope(new TrashScope('inspection_fire_hose_reel_details'));
    }
}
