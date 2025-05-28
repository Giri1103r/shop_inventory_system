<?php

namespace App\Models\Inspection\GembaWalk;

use Illuminate\Database\Eloquent\Model;

class GembaWalkStatusLog extends Model
{
    protected $table = 'inspection_gemba_walk_status_log';
    protected $primaryKey = 'id';

    protected $fillable = [
        'gemba_walk_id',
        'from_status',
        'to_status',
        'is_reject',
        'remarks',
        'status',
        'trash',
        'approved_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];

    public function selectOne($id)
    {
        return $this->select('inspection_gemba_walk_status_log.*', 'inspection_gemba_walk_status.status_name', 'inspection_gemba_walk_status.to_status')
            ->leftjoin('inspection_gemba_walk_status', 'inspection_gemba_walk_status.id', '=', 'inspection_gemba_walk_status_log.to_status')
            ->where('inspection_gemba_walk_status_log.gemba_walk_id', $id)
            ->get();
    }

    public function getDetails($id)
    {
        return $this->where('gemba_walk_id', $id)->get();
    }

    public function getFloormangerDetails($id)
    {
        return $this->where('gemba_walk_id', $id)->where('to_status', GEMBA_WALK_INSPECTION_WAITING_FOR_EHS_OFFICER_VERIFICATION)->where('status', 1)->first();
    }
}
