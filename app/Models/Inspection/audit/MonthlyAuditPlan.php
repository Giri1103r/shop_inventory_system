<?php

namespace App\Models\Inspection\audit;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

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
        $query = $this->select('inspection_audit_monthly_audit_plan.*', 'masters_unit.unit_name', 'inspection_audit_master_task.task_name')
            ->leftJoin('masters_unit', 'masters_unit.id', '=', 'inspection_audit_monthly_audit_plan.unit_id')
            ->leftJoin('inspection_audit_master_task', 'inspection_audit_master_task.id', '=', 'inspection_audit_monthly_audit_plan.task_id');
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
        if ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_audit_monthly_audit_plan.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_audit_monthly_audit_plan.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_audit_monthly_audit_plan.created_at', [$startDate, $endDate]);
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
        // if ($request->has('company_name') && $request->company_name) {

        //     $query->where('inspection_audit_monthly_audit_plan.company_id', decryptId($request->company_name));
        // }
        if ($request->has('fromDate') && !empty($request->fromDate)) {
            $datepickersearch = DBdateformat($request->fromDate);

            $query->where(function ($query) use ($datepickersearch) {
                $query->whereDate('inspection_audit_monthly_audit_plan.created_at', '>=', $datepickersearch);
            });
        }

        if ($request->has('toDate') && !empty($request->toDate)) {
            $enddatepickersearch = DBdateformat($request->toDate);

            $query->where(function ($query) use ($enddatepickersearch) {
                $query->whereDate('inspection_audit_monthly_audit_plan.created_at', '<=', $enddatepickersearch);
            });
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

    public function listApi()
    {
        $request = request();
        $perPage = $request->input('per_page', 10);
        $search = '';
        $query = $this->select('inspection_audit_monthly_audit_plan.*', 'masters_unit.unit_name', 'inspection_audit_master_task.task_name', 'inspection_audit_monthly_audit_plan.id as inspection_auidt_id')
            ->leftJoin('masters_unit', 'masters_unit.id', '=', 'inspection_audit_monthly_audit_plan.unit_id')
            ->leftJoin('inspection_audit_master_task', 'inspection_audit_master_task.id', '=', 'inspection_audit_monthly_audit_plan.task_id');
        $org_total =  $query;
        $org_total_counts = $org_total->count();
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_audit_monthly_audit_plan.auditee_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_audit_master_task.task_name', 'LIKE', '%' . $search . '%');
            });
        }

        $paginatedData = $query->orderBy('inspection_audit_monthly_audit_plan.id')->paginate($perPage);
        $inspection_data = $paginatedData->toArray();

        if (empty($inspection_data['data'])) {
            return $this->sendError('No records found.', [], 404);
        }

        $inspection_data_array = [];
        $refined_data = [];

        foreach ($inspection_data['data'] as $index => $datas) {
            $inspection_data_array['id'] = $datas['inspection_auidt_id'];
            $inspection_data_array['auditee_name'] = $datas['auditee_name'];
            $inspection_data_array['unit_name'] = $datas['unit_name'];
            $inspection_data_array['task_name'] = $datas['task_name'];
            $inspection_data_array['compliance_category_id'] = getCategoryType($datas['compliance_category_id']);
            $inspection_data_array['created_by'] = getUsername($datas['created_by']);
            $inspection_data_array['created_at'] = Displaydateformat($datas['created_at']);

            $refined_data[$index] = $inspection_data_array;
        }

        $response = [
            'per_page' => $paginatedData->perPage(),
            'current_page' => $paginatedData->currentPage(),
            'from' => $paginatedData->firstItem(),
            'to' => $paginatedData->lastItem(),
            'total' => $paginatedData->total(),
            'total_page' => $paginatedData->lastPage(),
            'list' => $refined_data,
        ];

        return $response;
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

    public function storeApi()
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
                'unit_id' => $unit_id[$index],
                'task_id' => $task_name[$index],
                'compliance_category_id' => $compliance_category[$index],
                'reference_doc_no' => $reference_doc_no[$index],
                'frequency_id' => $frequency_id[$index],
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
        $query = $this->select('inspection_audit_monthly_audit_plan.*', 'masters_unit.unit_name', 'inspection_audit_master_task.task_name')
            ->leftJoin('masters_unit', 'masters_unit.id', '=', 'inspection_audit_monthly_audit_plan.unit_id')
            ->leftJoin('inspection_audit_master_task', 'inspection_audit_master_task.id', '=', 'inspection_audit_monthly_audit_plan.task_id');


        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_audit_monthly_audit_plan.auditee_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_audit_master_task.task_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_audit_monthly_audit_plan.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_audit_monthly_audit_plan.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_audit_monthly_audit_plan.created_at', [$startDate, $endDate]);
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

        $data =  $query->get();

        $query = $data->groupBy('unit_name');

        return $query;
    }

    public function getTotalRecords()
    {
        $request = request();

        $query = $this->where('inspection_audit_monthly_audit_plan.status', 1);

        // if ($request->has('CompanyId') && $request->CompanyId) {
        //     $query->where('inspection_audit_assessment.company_id', decryptId($request->CompanyId));
        // }

        if ($request->has('Fromdate') && $request->Fromdate) {
            $query->where('inspection_audit_monthly_audit_plan.created_at', '>=', DBdateformat($request->Fromdate));
        }

        if ($request->has('Todate') && $request->Todate) {
            $query->where('inspection_audit_monthly_audit_plan.created_at', '<=', DBdateformat($request->Todate));
        }

        return $query->count();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_audit_monthly_audit_plan'));
    }
}
