<?php

namespace App\Models\Inspection\audit\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'inspection_audit_master_task';
    protected $primaryKey = 'id';

    protected $fillable = [
        'task_auto_id',
        'task_name',
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
        $query = $this->select('inspection_audit_master_task.*')->where('trash', 'NO');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_audit_master_task.task_auto_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_audit_master_task.task_name', 'LIKE', '%' . $search . '%');
            });
        }

        if (isset($request->task_id) && $request->task_id) {
            $query = $query->where('inspection_audit_master_task.task_auto_id', 'LIKE', '%' . $request->task_id . '%');
        }
        if (isset($request->task_name) && $request->task_name) {
            $query = $query->where('inspection_audit_master_task.task_name', 'LIKE', '%' . $request->task_name . '%');
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_audit_master_task.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_audit_master_task.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_audit_master_task.created_at', [$startDate, $endDate]);
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('inspection_audit_master_task.status', decryptId($request->status));
        }

        $query->orderBy('id', 'desc');

        $data_count = $query;
        $total_records = $data_count->count();

        if (isset($request->length) && $request->length != -1) {
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
        $insert_array = array(
            // 'task_auto_id' => getsequence('audit_task'),
            'task_name' => $request->task_name,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {
        $request = request();
        $update_array = array(
            'task_name' => $request->task_name,
            'updated_by' => Auth::id()
        );

        return $this->where('id', $id)->update($update_array);
    }

    public function selectOne($id)
    {
        return $this->where('id', $id)->first();
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_audit_master_task.*');


        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_audit_master_task.task_auto_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_audit_master_task.task_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_audit_master_task.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_audit_master_task.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_audit_master_task.created_at', [$startDate, $endDate]);
        }
        if (isset($request->task_id) && $request->task_id) {
            $query = $query->where('inspection_audit_master_task.task_auto_id', 'LIKE', '%' . $request->task_id . '%');
        }
        if (isset($request->task_name) && $request->task_name) {
            $query = $query->where('inspection_audit_master_task.task_name', 'LIKE', '%' . $request->task_name . '%');
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('inspection_audit_master_task.status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');
        return  $query->get();
    }


    public function statuschange($id)
    {
        $request = request();

        $type = $request->types;
        if ($type == 1) {
            $update_data = array(
                'status' => 0,
            );
        } else {
            $update_data = array(
                'status' => 1,
            );
        }

        return $this->where('id', $id)->update($update_data);
    }

    public function deleterecord($id)
    {

        $update_data = array(
            'status' => 0,
            'trash' => 'YES',
        );

        return $this->where('id', $id)->update($update_data);
    }

    public function UniqueCheck($data)
    {

        return $this->where('task_name',  $data)->get();
    }

    public function ExistuniqueCheck($data, $id)
    {
        return $this->where('task_name',  $data)
            ->where('id', '!=', $id)
            ->get();
    }


    public function getAuditTask()
    {
        return $this->where('trash', 'NO')->where('status', '!=', 0)->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_audit_master_task'));

        static::created(function ($model) {

            $uniqueId = 'AUDIT-TASK-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['task_auto_id' => $uniqueId]);
        });
    }
}
