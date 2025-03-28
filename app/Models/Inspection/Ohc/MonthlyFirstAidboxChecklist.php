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


    public function store($store)
    {
        $request = request();

        $insertedData = [];
        foreach ($request->unit_id as $index => $unit) {
            $department_id = $request->department_id[$index] ?? null;
            $first_aid_box_no = $request->first_aid_box[$index] ?? null;
            $first_aid_register_maintained = $request->first_aid_register_maintained[$index] ?? null;
            $first_aid_box_inspect_periodicity = $request->first_aid_inspect_periodicity[$index] ?? null;
            $first_aid_box_checklist_periodicity = $request->first_aid_checklist_periodicity[$index] ?? null;
            $medicine_requisition_slip_record = $request->medicine_requisition_slip_record[$index] ?? null;
            $first_aid_box_clean = $request->first_aid_clean[$index] ?? null;
            $first_aid_box_sticker = $request->first_aid_sticker[$index] ?? null;
            $first_aid_material_index = $request->first_aid_material_index[$index] ?? null;
            $first_aid_box_freeze_quantity = $request->first_aid_freeze_quantity[$index] ?? null;

            $insert_array = [
                'reference_id' => $store->id,
                'unit_id' => $unit ? decryptId($unit) : null,
                'department_id' => $department_id ? decryptId($department_id) : null,
                'first_aid_box_no' => $first_aid_box_no,
                'first_aid_register_maintained' => $first_aid_register_maintained ? decryptId($first_aid_register_maintained) : null,
                'first_aid_box_inspect_periodicity' => $first_aid_box_inspect_periodicity ? decryptId($first_aid_box_inspect_periodicity) : null,
                'first_aid_box_checklist_periodicity' => $first_aid_box_checklist_periodicity ? decryptId($first_aid_box_checklist_periodicity) : null,
                'medicine_requisition_slip_record' => $medicine_requisition_slip_record ? decryptId($medicine_requisition_slip_record) : null,
                'first_aid_box_clean' => $first_aid_box_clean ? decryptId($first_aid_box_clean) : null,
                'first_aid_box_sticker' => $first_aid_box_sticker ? decryptId($first_aid_box_sticker) : null,
                'first_aid_material_index' => $first_aid_material_index ? decryptId($first_aid_material_index) : null,
                'first_aid_box_freeze_quantity' => $first_aid_box_freeze_quantity ? decryptId($first_aid_box_freeze_quantity) : null,
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
