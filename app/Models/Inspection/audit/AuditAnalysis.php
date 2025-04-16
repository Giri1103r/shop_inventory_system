<?php

namespace App\Models\Inspection\audit;

use App\Scopes\TrashScope;
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
  

    public function selectOne($id)
    {
        $data = $this->select('inspection_audit_analysis.*')->where('id', $id)->first();
        return $data;
    }

    public function exportdata()
    {
        $request = request();
        $search = '';

        $query = $this->select('inspection_audit_analysis.*');

        if ($request->has('audit_analysis_id') && $request->audit_analysis_id) {
            $query = $query->where('id', decryptId($request->audit_analysis_id));
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('status', decryptId($request->status));
        }

        return $query->orderBy('id', 'desc')->get();
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
