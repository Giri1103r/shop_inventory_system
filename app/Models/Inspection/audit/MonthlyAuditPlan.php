<?php

namespace App\Models\Inspection\audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MonthlyAuditPlan extends Model
{
    protected $table = 'inspection_audit_monthly_audit_plan';
    protected $primaryKey = 'id';

    protected $fillable = [
        'auditee_name',
        'unit_id',
        'task_id',
        'compliance_category_id',
        'reference_doc_no',
        'frequency_id',
        'audit_plan_status',
        'direct_in_direct',
        'points',
        'remarks',
        'created_by',
        'updated_by',
        'status',
        'trash',
        'created_at',
        'updated_at'
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function list()
    {
        $request = request();

        $search = '';
        $query = $this->select('inspection_audit_monthly_audit_plan.*','masters_unit.unit_name','inspection_audit_master_task.task_name')
                      ->leftJoin('masters_unit','masters_unit.id','=','inspection_audit_monthly_audit_plan.unit_id')
                      ->leftJoin('inspection_audit_master_task','inspection_audit_master_task.id','=','inspection_audit_monthly_audit_plan.task_id');
        $org_total =  $query;
        $org_total_counts = $org_total->count();
        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_audit_monthly_audit_plan.auditee_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_audit_master_task.task_name', 'LIKE', '%' . $search . '%');


            });
        }

        if ($request->has('auditee_name') && $request->auditee_name) {
            $query = $query->where('inspection_audit_monthly_audit_plan.auditee_name', 'LIKE', '%' . $request->auditee_name . '%');
        }

        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('inspection_audit_monthly_audit_plan.unit_id', 'LIKE', '%' . decryptId($request->unit_id) . '%');
        }

        if ($request->has('task_name') && $request->task_name) {
            $query = $query->where('inspection_audit_monthly_audit_plan.task_id', 'LIKE', '%' . decryptId($request->task_name) . '%');
        }

        if ($request->has('compliance_category') && $request->compliance_category) {
            $query = $query->where('inspection_audit_monthly_audit_plan.compliance_category_id', 'LIKE', '%' . decryptId($request->compliance_category) . '%');
        }
     


        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('id', 'DESC');

        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }

        $data = $query->get();

        $datas = array(
            'data' => $data,
            'total_records' => $org_total_counts,
            'filter_records' => $total_records,
        );
        return $datas;
        
    }

    public function store()
    {
        $request = request();

        $auditee_name = $request->auditee_name;
        $unit_id = $request->unit_id;
        $task_name = $request->task_name;
        $compliance_category = $request->compliance_category;
        $reference_doc_no = $request->reference_doc_no;
        $frequency_id = $request->frequency_id;
        $direct_in_direct = $request->direct_in_direct;
        $status = $request->status;
        $points = $request->points;
        $remark = $request->remark;

        foreach ($auditee_name as $index => $auditee_name) {
            $data = [
                'auditee_name' => $auditee_name,
                'unit_id' => decryptId($unit_id[$index]),
                'task_id' => decryptId($task_name[$index]),
                'compliance_category_id' => decryptId($compliance_category[$index]),
                'reference_doc_no' => $reference_doc_no[$index],
                'frequency_id' => decryptId($frequency_id[$index]),
                'direct_in_direct' => $direct_in_direct[$index],
                'audit_plan_status' => $status[$index],
                'points' => $points[$index],
                'remarks' => $remark[$index],
                'created_by' => Auth::id(),
            ];


            $this->create($data);
        }

        return back()->with('success', 'Data saved successfully');
    }

    public function selectOne($id)
    {
        return $this->where('id', $id)->first();
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_audit_monthly_audit_plan.*','masters_unit.unit_name','inspection_audit_master_task.task_name')
                      ->leftJoin('masters_unit','masters_unit.id','=','inspection_audit_monthly_audit_plan.unit_id')
                      ->leftJoin('inspection_audit_master_task','inspection_audit_master_task.id','=','inspection_audit_monthly_audit_plan.task_id');


        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_audit_monthly_audit_plan.auditee_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_audit_master_task.task_name', 'LIKE', '%' . $search . '%');


            });
        }

        if ($request->has('auditee_name') && $request->auditee_name) {
            $query = $query->where('inspection_audit_monthly_audit_plan.auditee_name', 'LIKE', '%' . $request->auditee_name . '%');
        }

        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('inspection_audit_monthly_audit_plan.unit_id', 'LIKE', '%' . decryptId($request->unit_id) . '%');
        }

        if ($request->has('task_name') && $request->task_name) {
            $query = $query->where('inspection_audit_monthly_audit_plan.task_id', 'LIKE', '%' . decryptId($request->task_name) . '%');
        }

        if ($request->has('compliance_category') && $request->compliance_category) {
            $query = $query->where('inspection_audit_monthly_audit_plan.compliance_category_id', 'LIKE', '%' . decryptId($request->compliance_category) . '%');
        }
        
        $query->orderBy('id', 'DESC');
        return  $query->get();
    }
    
}
