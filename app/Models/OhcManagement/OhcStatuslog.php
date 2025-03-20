<?php

namespace App\Models\OhcManagement;

use App\Models\User;
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

    // medicne log

    public function medicinelog($id)
    {
        $request = request();
        $user = User::where('status', 1)->where('id',Auth::id())->first();
        $approveStatus = ($user && ($user->role == ROLE_SUPERADMIN || $user->role == ROLE_EHS_HEAD))
            ? STATUS_OHC_EHS_HEAD_APPROVED
            : STATUS_OHC_EHS_HEAD_APPROVAL_PENDING;
        $insert_data = [
            'type' => TYPE_OHC_MEDICINE,
            'reference_id' => $id,
            'from_status' => STATUS_OHC_MEDICINE_REQUEST,
            'to_status' =>   $approveStatus,
            'remarks' =>  $request->remarks,
            'created_by' => Auth::id(),
        ];
        return $this->create($insert_data);
    }
    // medicine approval
    public function medicineapproval($id, $updateData)
    {
        $request = request();
        $insert_data = [
            'type' => TYPE_OHC_MEDICINE,
            'reference_id' => $id,
            'from_status' => STATUS_OHC_EHS_HEAD_APPROVAL_PENDING,
            'to_status' => $updateData['approve_status'],
            'remarks' =>  $request->remarks,
            'created_by' => Auth::id(),
        ];
        return $this->create($insert_data);
    }

    // status log for the medicine

    public function getmedicinestatuslog($id)
    {
        return $this->where('reference_id', $id)->where('type', TYPE_OHC_MEDICINE)->get();
    }



    // Medicine Receiving

    public function medicinestockstore($id)
    {
        $request = request();
        $insert_data = [
            'type' => TYPE_OHC_MEDICINE_RECEIVING,
            'reference_id' => $id,
            'from_status' => STATUS_OHC_STOCK_REQUEST,
            'to_status' =>  STATUS_OHC_EHS_VERIFICATION_PENDING,
            'remarks' =>  $request->remarks,
            'created_by' => Auth::id(),
        ];
        // dd($insert_data );
        return $this->create($insert_data);
    }
    // EHS verification pending

    public function ehsverificationstatuslog($id, $ehsverifydata)
    {
        $request = request();
        $insert_data = [
            'type' => TYPE_OHC_MEDICINE_RECEIVING,
            'reference_id' => $id,
            'from_status' => STATUS_OHC_EHS_VERIFICATION_PENDING,
            'to_status' => STATUS_OHC_EHS_VERIFIED,
            'remarks' =>  $request->remarks,
            'created_by' => Auth::id(),
        ];
        // dd($insert_data );
        return $this->create($insert_data);
    }
    // EHS Verification is approved
    public function ehsverificationapprovedstatuslog($id, $ehsverifydata)
    {
        $request = request();
        $insert_data = [
            'type' => TYPE_OHC_MEDICINE_RECEIVING,
            'reference_id' => $id,
            'from_status' => STATUS_OHC_EHS_VERIFIED,
            'to_status' => $ehsverifydata['approve_status'],
            'remarks' =>  $request->remarks,
            'created_by' => Auth::id(),
        ];
        // dd($insert_data );
        return $this->create($insert_data);
    }


    // L1 EHS Verification

    public function l1ehsstatuslog($id, $ehsverifydata)
    {
        $request = request();
        $insert_data = [
            'type' => TYPE_OHC_MEDICINE_RECEIVING,
            'reference_id' => $id,
            'from_status' => STATUS_OHC_L1_EHS_VERIFICATION_PENDING,
            'to_status' => STATUS_OHC_L1_EHS_VERIFIED,
            'remarks' =>  $request->ehs_remarks,
            'created_by' => Auth::id(),
        ];
        return $this->create($insert_data);
    }

    public function l1ehsverificationapprovedstatuslog($id, $ehsverifydata)
    {
        $request = request();
        $insert_data = [
            'type' => TYPE_OHC_MEDICINE_RECEIVING,
            'reference_id' => $id,
            'from_status' => STATUS_OHC_L1_EHS_VERIFIED,
            'to_status' => $ehsverifydata['approve_status'],
            'remarks' =>  $request->ehs_remarks,
            'created_by' => Auth::id(),
        ];
        // dd($insert_data );
        return $this->create($insert_data);
    }

    // EHS Head Approval

    public function ehsheadstatuslog($id, $updateStatus)
    {
        $request = request();
        $insert_data = [
            'type' => TYPE_OHC_MEDICINE_RECEIVING,
            'reference_id' => $id,
            'from_status' => STATUS_OHC_AGM_APPROVAL_PENDING,
            'to_status' => STATUS_OHC_AGM_APPROVED,
            'remarks' =>  $request->ehs_head_remarks,
            'created_by' => Auth::id(),
        ];
        return $this->create($insert_data);
    }
    public function ehsheadapprovedstatuslog($id, $updateStatus)
    {
        $request = request();
        $insert_data = [
            'type' => TYPE_OHC_MEDICINE_RECEIVING,
            'reference_id' => $id,
            'from_status' => STATUS_OHC_AGM_APPROVED,
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
            'remarks' =>  $request->remarks,
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
            'from_status' => STATUS_OHC_REQUISITION_STOCK_REQUEST,
            'to_status' => STATUS_OHC_REQUISITION_EHS_HEAD_APPROVAL_PENDING,
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
            'from_status' => STATUS_OHC_REQUISITION_EHS_HEAD_APPROVAL_PENDING,
            'to_status' => $data['approve_status'],
            'remarks' => $request->remarks,
            'created_by' => Auth::id(),
        ];

        return $this->create($insert_data);
    }



    // Medicine Requistion Close Status

    public function updatecloseStatus($id)
    {
        $request = request();
        $insert_data = [
            'type' => TYPE_OHC_MEDICINE_REQUISITION,
            'reference_id' => $id,
            'from_status' => STATUS_OHC_REQUISITION_EHS_HEAD_APPROVED,
            'to_status' => STATUS_OHC_CLOSE,
            'remarks' => $request->remarks,
            'created_by' => Auth::id(),
        ];
        return $this->create($insert_data);
    }

    // medical fitness

    public function medicalfitnessstore($id)
    {
        $request = request();
        $insert_data = [
            'type' => TYPE_OHC_MEDICAL_FITNESS,
            'reference_id' => $id,
            'from_status' => STATUS_OHC_MEDICAL_PARAMEDICS_REQUEST,
            'to_status' =>  STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING,
            'remarks' =>  $request->remarks,
            'created_by' => Auth::id(),
        ];
        // dd($insert_data );
        return $this->create($insert_data);
    }

    public function doctorverificationstatuslog($id, $ehsverifydata)
    {
        $request = request();
        $insert_data = [
            'type' => TYPE_OHC_MEDICAL_FITNESS,
            'reference_id' => $id,
            'from_status' => STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING,
            'to_status' => STATUS_OHC_MEDICAL_DOCTOR_APPROVED,
            'remarks' =>  $request->remarks,
            'created_by' => Auth::id(),
        ];
        // dd($insert_data );
        return $this->create($insert_data);
    }


    public function ehsheadverificationapprovedstatuslog($id, $ehsheadverifydata)
    {
        $request = request();
        $insert_data = [
            'type' => TYPE_OHC_MEDICAL_FITNESS,
            'reference_id' => $id,
            'from_status' => STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING,
            'to_status' => $ehsheadverifydata['approve_status'],
            'remarks' =>  $request->remarks,
            'created_by' => Auth::id(),
        ];

        return $this->create($insert_data);
    }
    // get medicine log
    public function medicalfitnesslog($id)
    {
        return $this->where('reference_id', $id)->where('type', TYPE_OHC_MEDICAL_FITNESS)->get();
    }
    public function doctorapprovalview($id)
    {
        return $this->where('reference_id', $id)->where('type', TYPE_OHC_MEDICAL_FITNESS)->where('from_status', STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING)->first();
    }
    // EHS Verification
    public function ehsverifydata($id)
    {
        return $this->where('reference_id', $id)->where('type', TYPE_OHC_MEDICINE_RECEIVING)->where('from_status', STATUS_OHC_EHS_VERIFICATION_PENDING)->first();
    }
    //  L1 EHS Verification
    public function ehsL1verifydata($id)
    {
        return $this->where('reference_id', $id)->where('type', TYPE_OHC_MEDICINE_RECEIVING)->where('from_status', STATUS_OHC_L1_EHS_VERIFICATION_PENDING)->first();
    }

    public function ehsheadverifydata($id)
    {
        return $this->where('reference_id', $id)->where('type', TYPE_OHC_MEDICINE_RECEIVING)->where('from_status', STATUS_OHC_AGM_APPROVAL_PENDING)->first();
    }


    public function medicineReceivingStockData($id)
    {
        return $this->where('reference_id', $id)->where('type', TYPE_OHC_MEDICINE_RECEIVING)->get();
    }

    // Medicine Rquisition Status Log Data for the View

    public function getMedicineRequisitionLog($id)
    {
        return $this->where('reference_id', $id)->where('type', TYPE_OHC_MEDICINE_REQUISITION)->get();
    }
}
