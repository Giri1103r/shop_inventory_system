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

    // PPE REQUEST-REQUESTOR STORE
    public function storestatus($pperequest, $id)
    {
        $insert_data = [
            'type' => TYPE_PPE_REQUEST,
            'reference_id' => $id,
            'from_status' => $pperequest->approve_status,
            'to_status' => $pperequest->approve_status,
            'remarks' => $pperequest->employee_reason,
            'created_by' => Auth::id(),
        ];
        return $this->create($insert_data);
    }

    // PPE REQUEST-HOD STORE

    public function store($updateData, $empDetails)
    {
        $insert_data = [
            'type' => TYPE_PPE_REQUEST,
            'reference_id' => $empDetails->id,
            'from_status' => $empDetails->approve_status,
            'to_status' => $updateData['approve_status'],
            'remarks' => $updateData['remarks'],
            'created_by' => Auth::id(),

        ];

        return $this->create($insert_data);
    }




    // PPE REQUEST-EHS STORE

    public function storeEhsStatus($updateEhsData, $empDetails)
    {
        $insert_data = [
            'type' => TYPE_PPE_REQUEST,
            'reference_id' => $empDetails->id,
            'from_status' => $empDetails->ehs_approve_status,
            'to_status' => $updateEhsData['approve_status'],
            'remarks' => $updateEhsData['remarks'],
            'created_by' => Auth::id(),

        ];
        return $this->create($insert_data);
    }

    // PPE EXEMPTION REQUESTOR STORE

    public function exemptionstatus($ppeexemption)
    {
        $insert_data = [
            'type' => TYPE_PPE_EXEMPTION,
            'reference_id' => $ppeexemption->id,
            'from_status' => $ppeexemption->approve_status,
            'to_status' => $ppeexemption->approve_status,
            'remarks' => $ppeexemption->reason,
            'created_by' => Auth::id(),

        ];
        return $this->create($insert_data);
    }

    // PPE EXEMPTION EHS-STORE
    public function storeexemptionstatus($updateData, $emp_details)
    {
        $insert_data = [
            'type' => TYPE_PPE_EXEMPTION,
            'reference_id' => $emp_details->id,
            'from_status' => $emp_details->approve_status,
            'to_status' => $updateData['approve_status'],
            'remarks' => $updateData['remarks'],
            'created_by' => Auth::id(),

        ];
        return $this->create($insert_data);
    }

    public function getstatusdetails($id)
    {
        $data = Statuslog::where('reference_id', $id)->where('type', TYPE_PPE_REQUEST)->latest('id')->first();
        return $data;
    }

    public function statuslog($id){
        $data = Statuslog::where('reference_id', $id)->where('type', TYPE_PPE_REQUEST)->get();
        return $data;
    }

    public function statuslogdetails( $id){
        $data = Statuslog::where('reference_id', $id)->where('type', TYPE_PPE_REQUEST)->latest('id')->first();
        return $data;
    }


    public function getexemptionstatusdetails($id)
    {
        $data = Statuslog::where('reference_id', $id)->where('type', TYPE_PPE_EXEMPTION)->get();
        return $data;
    }
}
