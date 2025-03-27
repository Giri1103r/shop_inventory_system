<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MonthlyFirstAidboxChecklist extends Model
{
    protected $table = 'inspection_ohc_monthly_first_box_audit_checklist';

    protected $primaryKey = 'id';
    protected $fillable = [
        'reference_id',
        'unit_id',
        'department_id',
        'first_aid_box_no',
        'first_aid_register_maintained',
        'first_aid_box_inspect_periodicity',
        'first_aid_box_checklist_periodicity',
        'medicine_requisition_slip_record',
        'first_aid_box_clean',
        'first_aid_box_sticker',
        'first_aid_material_index',
        'first_aid_box_freeze_quantity',
        'created_by',
        'updated_by',
        'status',
        'trash',
        'created_at',
        'updated_at',
    ];


    public function store($user_medicine_issuance)
    {
        $request = request();

        $insertedData = [];

        foreach ($request->medicine_id as $index => $medicine) {
            $insert_array = [
                'reference_id' => $user_medicine_issuance->id,
                'unit_id' => decryptId($medicine),
                'department_id' => decryptId($request->department_id[$index]),
                'first_aid_box_no' => decryptId($request->first_aid_box_no[$index]),
                'first_aid_register_maintained' => decryptId($request->first_aid_register_maintained[$index]),
                'first_aid_box_inspect_periodicity' => decryptId($request->first_aid_box_inspect_periodicity[$index]),
                'first_aid_box_checklist_periodicity' => decryptId($request->first_aid_box_checklist_periodicity[$index]),
                'medicine_requisition_slip_record' => decryptId($request->medicine_requisition_slip_record[$index]),
                'first_aid_box_clean' => decryptId($request->first_aid_box_clean[$index]),
                'first_aid_box_sticker' => decryptId($request->first_aid_box_sticker[$index]),
                'first_aid_material_index' => decryptId($request->first_aid_material_index[$index]),
                'first_aid_box_freeze_quantity' => decryptId($request->first_aid_box_freeze_quantity[$index]),
                'created_by' => Auth::id(),
            ];


            $insertedData[] = $this->create($insert_array);
        }

        return $insertedData;
    }

    public function selectOne($id){
        return $this->where('reference_id',$id)->where('status',1)->get();
    }
}
