<?php

namespace App\Models\OhcManagement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class OhcStatuslog extends Model
{
    protected $table = 'ohc_statuslog';
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

    public function medicineapproval($id)
    {
        $request = request();
        $insert_data = [
            'type' => TYPE_OHC_MEDICINE,
            'reference_id' => $id,
            'from_status' => STATUS_OHC_MEDICINE_APPROVAL_PENDING,
            'to_status' => STATUS_OHC_MEDICINE_APPROVED,
            'remarks' =>  $request->remarks,
            'created_by' => Auth::id(),
        ];
        return $this->create($insert_data);
    }

    // Medicine Receiving
    // EHS Verification
    public function medicinereceivingstatuslog($id,$updateStatus )
    {
        $request = request();
        $insert_data = [
            'type' => TYPE_OHC_MEDICINE_RECEIVING,
            'reference_id' => $id,
            'from_status' => STATUS_OHC_PARAMEDIES_REQUEST,
            'to_status' =>   $updateStatus['approve_status'] ,
            'remarks' =>  $request->remarks,
            'created_by' => Auth::id(),
        ];
        // dd($insert_data );
        return $this->create($insert_data);
    }
    // L1 EHS Verification

    public function ehsstatuslog($id)
    {
        $request = request();
        $insert_data = [
            'type' => TYPE_OHC_MEDICINE_RECEIVING,
            'reference_id' => $id,
            'from_status' => STATUS_OHC_L1_EHS_VERIFICATION_PENDING,
            'to_status' => STATUS_OHC_EHS_HEAD_APPROVAL_PENDING,
            'remarks' =>  $request->ehs_remarks,
            'created_by' => Auth::id(),
        ];
        return $this->create($insert_data);
    }

    // EHS Head Approval

    public function ehsheadstatuslog($id,$updateStatus)
    {
        $request = request();
        $insert_data = [
            'type' => TYPE_OHC_MEDICINE_RECEIVING,
            'reference_id' => $id,
            'from_status' => STATUS_OHC_EHS_HEAD_APPROVAL_PENDING,
            'to_status' => $updateStatus['approve_status'],
            'remarks' =>  $request->ehs_head_remarks,
            'created_by' => Auth::id(),
        ];
        return $this->create($insert_data);
    }
    //STOCK CLOSE
    public function stockstatuslog($id)
    {
        $request = request();
        $insert_data = [
            'type' => TYPE_OHC_MEDICINE_RECEIVING,
            'reference_id' => $id,
            'from_status' => STATUS_OHC_OPEN,
            'to_status' => STATUS_OHC_CLOSE,
            'remarks' =>  $request->stock_remarks,
            'created_by' => Auth::id(),
        ];
        return $this->create($insert_data);
    }

    // MEDICINE RECEVING DATA STORE

    public function storeMedicineRecevingdata($id)
    {
        $request = request();
        $insert_data = [
            'type' => TYPE_OHC_MEDICINE_REQUISITION,
            'reference_id' => $id,
            'from_status' => STATUS_OHC_STOCK_REQUEST,
            'to_status' => STATUS_OHC_PARAMEDICS_APPROVAL_PENDING,
            'remarks' =>  $request->stock_remarks,
            'created_by' => Auth::id(),
        ];
        // dd( $insert_data);
        return $this->create($insert_data);
    }

    // Paramedics Approve OR Reject
    public function paramedicsapprove($id, $data)
    {
        $request = request();
        $insert_data = [
            'type' => TYPE_OHC_MEDICINE_REQUISITION,
            'reference_id' => $id,
            'from_status' => STATUS_OHC_PARAMEDICS_APPROVAL_PENDING,
            'to_status' => $data['approve_status'],
            'remarks' => $request->stock_remarks,
            'created_by' => Auth::id(),
        ];

        return $this->create($insert_data);
    }

    // OHC Stock management Approval Log

    public function stockupdate($id)
    {
        $request = request();
        $insert_data = [
            'type' => TYPE_OHC_MEDICINE_STOCK,
            'reference_id' => $id,
            'from_status' => STATUS_OHC_MEDICINE_APPROVAL_PENDING,
            'to_status' => STATUS_OHC_EHS_HEAD_APPROVED,
            'remarks' => $request->remarks,
            'created_by' => Auth::id(),
        ];
        return $this->create($insert_data);
    }
    // EHS Verification
    public function ehsverifydata($id)
    {
        return $this->where('reference_id', $id)->where('type', TYPE_OHC_MEDICINE_RECEIVING)->where('from_status', STATUS_OHC_PARAMEDIES_REQUEST)->first();
    }
    // L1 EHS Verification
    public function ehsL1verifydata($id)
    {
        return $this->where('reference_id', $id)->where('type', TYPE_OHC_MEDICINE_RECEIVING)->where('from_status', STATUS_OHC_L1_EHS_VERIFICATION_PENDING)->first();
    }

    public function ehsheadverifydata($id)
    {
        return $this->where('reference_id', $id)->where('type', TYPE_OHC_MEDICINE_RECEIVING)->where('from_status', STATUS_OHC_EHS_HEAD_APPROVAL_PENDING)->first();
    }
    public function stockopen($id)
    {
        return $this->where('reference_id', $id)->where('type', TYPE_OHC_MEDICINE_RECEIVING)->where('from_status', STATUS_OHC_OPEN)->first();
    }

    // MEDICINE Requistion

    public function getStockrequestdata($id)
    {
        return $this->where('reference_id', $id)->where('type', TYPE_OHC_MEDICINE_REQUISITION)->where('from_status', STATUS_OHC_STOCK_REQUEST)->first();
    }
    public function getparamedicsapprovaldata($id)
    {
        return $this->where('reference_id', $id)->where('type', TYPE_OHC_MEDICINE_REQUISITION)->where('from_status', STATUS_OHC_PARAMEDICS_APPROVAL_PENDING)->first();
    }

    public function getmedicineopen($id)
    {
        return $this->where('reference_id', $id)->where('type', TYPE_OHC_MEDICINE_REQUISITION)->where('from_status', STATUS_OHC_PARAMEDICS_APPROVED)->first();
    }

    // Medicine Stocklog View

    public function medicinestockdata($id){

        return $this->where('reference_id',$id)->where('type',TYPE_OHC_MEDICINE_STOCK)->where('from_status',STATUS_OHC_MEDICINE_APPROVAL_PENDING)->first();
    }

    public function medicineReceivingStockData($id){
        return $this->where('reference_id',$id)->where('type',TYPE_OHC_MEDICINE_RECEIVING)->get();
    }
}
