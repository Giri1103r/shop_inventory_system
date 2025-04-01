<?php

namespace App\Models\Inspection\Fire;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;

class PASystemChecklist extends Model
{
    protected $table = 'inspection_fire_pa_system_checklist';

    protected $fillable = [
        'id',
        'fire_pa_system_id',
        'sr_no',
        'location',
        'unit',
        'audio_quality',
        'mic_condition',
        'mic_quantity',
        'physical_condition',
        'cable_condition',
        'operation',
        'remark',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];


    public function selectOne($id)
    {
        return $this->where('fire_pa_system_id', $id)->where('status', 1)->where('trash', 'NO')->first();
    }

    public function store($id)
    {
        $request = request();

        // $sr_no = $request->sr_no;
        // $location = $request->location;
        // $unit_id = $request->unit_id;
        // $audio_quality = $request->audio_quality;
        // $mic_condition = $request->mic_condition;
        // $mic_quantity = $request->mic_quantity;
        // $physical_condition = $request->physical_condition;
        // $cable_condition = $request->cable_condition;
        // $operation = $request->operation;
        // $remark = $request->remark;
        $sr_no = $request->sr_no ?? [];
        $location = $request->location ?? [];
        $unit_id = $request->unit_id ?? [];
        $audio_quality = $request->audio_quality ?? [];
        $mic_condition = $request->mic_condition ?? [];
        $mic_quantity = $request->mic_quantity ?? [];
        $physical_condition = $request->physical_condition ?? [];
        $cable_condition = $request->cable_condition ?? [];
        $operation = $request->operation ?? [];
        $remark = $request->remark ?? [];

        foreach ($sr_no as $index => $sr_no_value) {
            $data = array(
                'fire_pa_system_id' => $id,
                'sr_no' => $sr_no_value,
                'unit' => decryptId($unit_id[$index]),
                'location' => decryptId($location[$index]),
                'audio_quality' => decryptId($audio_quality[$index]),
                'mic_condition' => decryptId($mic_condition[$index]),
                'mic_quantity' => $mic_quantity[$index],
                'physical_condition' => decryptId($physical_condition[$index]),
                'cable_condition' => decryptId($cable_condition[$index]),
                'operation' => decryptId($operation[$index]),
                'remark' => $remark[$index],
                'created_by' => Auth::id(),
            );

            $this->create($data);
        }
    }

    public function GetDetails($id)
    {
        return $this->where('fire_pa_system_id', $id)->where('status', 1)->where('trash', 'NO')->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_fire_pa_system_checklist'));
    }

}
