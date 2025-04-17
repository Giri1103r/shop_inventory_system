<?php

namespace App\Models\Permit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Statuslog extends Model
{
    protected $table = 'ptw_status_log';
    protected $primaryKey = 'id';

    protected $fillable = [
        'permit_id',
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



    public function selectOne($id)
    {

        $data =  $this->select('ptw_status_log.*','ptw_status.to_status','ptw_status.status_name','ptw_status.id as status_id')->leftjoin('ptw_status', 'ptw_status.id', '=', 'ptw_status_log.to_status')
            ->where('ptw_status_log.permit_id', $id)
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
