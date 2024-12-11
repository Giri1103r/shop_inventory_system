<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Statuslog extends Model
{
    protected $table = 'ppe_statuslog';
    protected $primaryKey = 'id';

    protected $fillable = [
        'type',
        'reference_id',
        'from_status',
        'to_status',
        'remarks',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];

    public function store($updateData,$empDetails){
        $insert_data =[
            'type'=>TYPE_PPE_REQUEST,
            'reference_id'=>$empDetails->id,
            'from_status'=>$empDetails->approve_status,
            'to_status'=>$updateData['approve_status'],
            'remarks'=>$updateData['approve_msg'],
            'created_by'=>Auth::id(),

        ];
        return $this->create( $insert_data);
    }

    public function storeEhsStatus($updateEhsData,$empDetails){
        $insert_data =[
            'type'=>TYPE_PPE_REQUEST,
            'reference_id'=>$empDetails->id,
            'from_status'=>$empDetails->ehs_approve_status,
            'to_status'=>$updateEhsData['ehs_approve_status'],
            'remarks'=>$updateEhsData['remarks'],
            'created_by'=>Auth::id(),

        ];
        return $this->create( $insert_data);
    }
    public function storeexemptionstatus($updateData,$emp_details){
        $insert_data =[
            'type'=>TYPE_PPE_EXEMPTION,
            'reference_id'=>$emp_details->id,
            'from_status'=>$emp_details->approve_status,
            'to_status'=>$updateData['approve_status'],
            'remarks'=>$updateData['remarks'],
            'created_by'=>Auth::id(),

        ];
        return $this->create( $insert_data);
    }

    public function getstatusdetails($id){
        $data = Statuslog::where('reference_id', $id)->where('type', TYPE_PPE_REQUEST)->get();
        return $data;
    }
}
