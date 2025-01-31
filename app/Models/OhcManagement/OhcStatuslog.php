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
    public function medicinereceivingstatuslog($id)
    {
        $request = request();
        $insert_data = [
            'type' => TYPE_OHC_MEDICINE_RECEIVING,
            'reference_id' => $id,
            'from_status' => STATUS_OHC_PARAMEDIES_REQUEST,
            'to_status' => STATUS_OHC_EHS_VERIFICATION_PENDING,
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

    public function ehsheadstatuslog($id)
    {
        $request = request();
        $insert_data = [
            'type' => TYPE_OHC_MEDICINE_RECEIVING,
            'reference_id' => $id,
            'from_status' => STATUS_OHC_EHS_HEAD_APPROVAL_PENDING,
            'to_status' => STATUS_OHC_OPEN,
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
}
