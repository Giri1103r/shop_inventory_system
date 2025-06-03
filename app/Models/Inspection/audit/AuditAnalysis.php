<?php

namespace App\Models\Inspection\audit;

use App\Scopes\TrashScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class AuditAnalysis extends Model
{

    protected $table = 'inspection_audit_analysis';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'audit_analysis_id',
        'audit_analysis',
        'docNo_id',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_audit_analysis.*');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('audit_analysis_id LIKE "%' . $search . '%"');
            });
        }

        if ($request->has('audit_analysis_id') && $request->audit_analysis_id) {
            $query = $query->where('id', decryptId($request->audit_analysis_id));
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('status', decryptId($request->status));
        }
        if ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_audit_analysis.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_audit_analysis.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_audit_analysis.created_at', [$startDate, $endDate]);
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
            'audit_analysis_id' => $request->audit_analysis_id,
            'audit_analysis' => $request->audit_analysis,
            'docNo_id' => decryptId($request->docNo_id),
            'created_by' => Auth::id(),
        );

        return $this->create($insert_array);
    }

    public function store_api($staticDocno)
    {
        $request = request();

        $insert_array = array(
            'audit_analysis_id' => $request->audit_analysis_id,
            'audit_analysis' => $request->audit_analysis,
            'docNo_id' =>  $staticDocno->id,
            'created_by' => Auth::id(),
        );

        return $this->create($insert_array);
    }


    public function selectOne($id)
    {
        $data = $this->select('inspection_audit_analysis.*')->where('id', $id)->first();
        return $data;
    }

    public function exportdata()
    {
        $request = request();

        $query = $this->select('inspection_audit_analysis.*', 'inspection_audit_analysis_checklist.*', 'masters_unit.*', 'masters_department.*')
            ->leftJoin('inspection_audit_analysis_checklist', 'inspection_audit_analysis.id', '=', 'inspection_audit_analysis_checklist.audit_analysis_id')
            ->leftJoin('masters_unit', 'inspection_audit_analysis_checklist.unit_id', '=', 'masters_unit.id')
            ->leftJoin('masters_department', 'inspection_audit_analysis_checklist.department_id', '=', 'masters_department.id');

        if ($request->has('audit_analysis_id') && $request->audit_analysis_id) {
            $query->where('inspection_audit_analysis.id', decryptId($request->audit_analysis_id));
        }
        if ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_audit_analysis.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_audit_analysis.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_audit_analysis.created_at', [$startDate, $endDate]);
        }
        if ($request->has('status') && $request->status) {
            $query->where('inspection_audit_analysis.status', decryptId($request->status));
        }

        $data = $query->orderBy('inspection_audit_analysis.id', 'desc')->get();

        return $data->groupBy('audit_analysis_id');
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


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_audit_analysis'));
        static::created(function ($model) {

            $uniqueId = 'AUDIT-ANALYSIS--' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['audit_analysis_id' => $uniqueId]);
        });
    }
}
