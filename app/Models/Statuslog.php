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
            'to_status' => STATUS_USER_APPLIED,
           'remarks' => !empty($pperequest->employee_reason) ? $pperequest->employee_reason : $pperequest->employee_remarks,
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
            'from_status' =>$updateData['approve_status'],
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
            'from_status' => STATUS_EHS_APPROVAL_PENDING,
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
            'from_status' => STATUS_EHS_APPROVAL_PENDING,
            'to_status' => STATUS_USER_APPLIED,
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

    // PPE Request Storemanager

    public function storemangerstatus($updateStatus, $empDetails){
        $insert_data = [
            'type' => TYPE_PPE_REQUEST,
            'reference_id' => $empDetails->id,
            'from_status' => STATUS_EHS_APPROVED,
            'to_status' => $updateStatus['approve_status'],
            'remarks' => $updateStatus['remarks'],
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
        $data = Statuslog::where('reference_id', $id)->where('type', TYPE_PPE_REQUEST)->where('trash','NO')->where('status',1)->get();
        return $data;
    }

    public function getstatuslogdata($id){
        $data = Statuslog::where('reference_id', $id)->where('type', TYPE_PPE_REQUEST)
        ->where('to_status','=' ,STATUS_HOD_APPROVED)
        ->orderBy('id', 'DESC')
        ->first();
        return $data;
    }

    public function getehsstatuslogdetails($id) {
        $data = Statuslog::where('reference_id', $id)
            ->where('type', TYPE_PPE_REQUEST)
            ->where('to_status', '=', STATUS_EHS_APPROVED)
            ->orderBy('id', 'DESC')
            ->first();

        return $data;
    }


    public function statuslogdetails( $id){
        $data = Statuslog::where('reference_id', $id)->where('type', TYPE_PPE_REQUEST)->latest('id')->first();
        return $data;
    }



    public function getehsheadstatuslog($id){
      return $this->where('reference_id',$id)->where('type',TYPE_PPE_EXEMPTION)->where('to_status',STATUS_EHS_APPROVED)->first();
    }

    public function gethodstatuslog($id)
    {
        return $this->where('reference_id', $id)->where('type', TYPE_PPE_REQUEST)->where('from_status', STATUS_HOD_APPROVED)->first();
    }
    public function getehsstatuslog($id)
    {
        return $this->where('reference_id', $id)->where('type', TYPE_PPE_REQUEST)->where('from_status', STATUS_EHS_APPROVAL_PENDING)->first();
    }

    public function getsmstatuslog($id)
    {
        return $this->where('reference_id', $id)->where('type', TYPE_PPE_REQUEST)->where('from_status', STATUS_EHS_APPROVED)->first();
    }
}
