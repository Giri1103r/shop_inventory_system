<?php

namespace App\Models\IMS\Incident;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Statuslog extends Model
{
    protected $table = 'ims_incident_status_log';
    protected $primaryKey = 'id';

    protected $fillable = [
        'ims_type',
        'ims_id',
        'from_status',
        'to_status',
        'remarks',
        'status',
        'trash',
        'approved_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];



    public function selectOne($id,$type)
    {

        $data =  $this->select('ims_incident_status_log.*','ims_incident_status.to_status','ims_incident_status.status_name')->leftjoin('ims_incident_status', 'ims_incident_status.id', '=', 'ims_incident_status_log.to_status')
            ->where('ims_incident_status_log.ims_id', $id)->where('ims_incident_status_log.ims_type', $type)
            ->get();

        return $data;
    }
    public function getstatusdetails($id)
    {
        $data = Statuslog::where('reference_id', $id)->where('type', TYPE_PPE_REQUEST)->get();
        return $data;
    }

    public function getexemptionstatusdetails($id)
    {
        $data = Statuslog::where('reference_id', $id)->where('type', TYPE_PPE_EXEMPTION)->get();
        return $data;
    }
}
