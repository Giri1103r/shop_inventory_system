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
        return $this->create($insert_data);
    }

    public function ehsverifydata($id){
        return $this->where('reference_id',$id)->where('type',TYPE_OHC_MEDICINE_RECEIVING)->where('from_status',STATUS_OHC_PARAMEDIES_REQUEST)->first();
    }
}
