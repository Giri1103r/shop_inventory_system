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
        'status_of_hose',
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
        $location = $request->exact_location;
        $department = $request->department;
        $length = $request->length;
        $nozzle = $request->nozzle;
        $hose = $request->hose;
        $status_of_hose = $request->status_of_hose;
        $flow = $request->flow;
        $approach = $request->approach;
        $remarks = $request->remarks;

        foreach ($sr_no as $index => $sr_no_value) {
            $data = array(
                'inspection_id' => $id,
                'sr_no' => $sr_no_value,
                'location' => ($location[$index]),
                'department' => decryptId($department[$index]),
                'length' => $length[$index],
                'status_of_hose' => decryptId($status_of_hose[$index]),
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

    public function storeApi($id)
    {
        $request = request();

        $sr_no = $request->sr_no;
        $location = $request->location;
        $department = $request->department;
        $length = $request->length;
        $nozzle = $request->nozzle;
        $hose = $request->hose;
        $status_of_hose = $request->status_of_hose;
        $flow = $request->flow;
        $approach = $request->approach;
        $remarks = $request->remarks;

        foreach ($sr_no as $index => $sr_no_value) {
            $data = array(
                'inspection_id' => $id,
                'sr_no' => $sr_no_value,
                'location' => $location[$index],
                'department' => $department[$index],
                'length' => $length[$index],
                'status_of_hose' => $status_of_hose[$index],
                'nozzle' => $nozzle[$index],
                'hose' => $hose[$index],
                'flow' => $flow[$index],
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
