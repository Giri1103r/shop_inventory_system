<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class FirePreNocInspection extends Model
{
    protected $table = 'inspection_fire_pre_noc_checklist';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'inspection_id',
        'doc_no',
        'issue_date',
        'rev_dt',
        'block_based_statement',
        'block',
        'checklist',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'responses',

    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_fire_pre_noc_checklist.*');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('doc_no LIKE "%' . $search . '%"');
                $query->orWhereRaw('issue_date LIKE "%' . $search . '%"');
                $query->orWhereRaw('rev.dt LIKE "%' . $search . '%"');
            });
        }


        if (isset($request->inspection_id) && $request->inspection_id) {
            $query = $query->where('inspection_fire_pre_noc_checklist.inspection_id', 'LIKE', '%' . $request->inspection_id . '%');
        }
        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_fire_pre_noc_checklist.status', decryptId($request->status));
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
        $structuredChecklist = [];

        if (is_array($request->remarks)) {
            foreach ($request->remarks as $subTypeId => $checklists) {
                foreach ($checklists as $checklistId => $remark) {
                    $structuredChecklist[$checklistId] = [
                        'remarks' => $remark,
                        'sub_type_id' => $subTypeId
                    ];
                }
            }
        }

        $insert_array = [
            'doc_no' => $request->doc_no,
            'issue_date' => DBdateformat($request->issue_date),
            'rev_dt' => $request->rev_dt,
            'block_based_statement' => $request->block_based_statement,
            'block' => $request->block,
            'checklist' => json_encode($structuredChecklist),
            'created_by' => Auth::id(),
        ];

        return $this->create($insert_array);
    }



    public function selectOne($id)
    {
        $data =   $this->select('inspection_fire_pre_noc_checklist.*')->where('inspection_fire_pre_noc_checklist.id', $id)->first();
        return $data;
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_fire_pre_noc_checklist.*');
        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('doc_no LIKE "%' . $search . '%"');
                $query->orWhereRaw('issue_date LIKE "%' . $search . '%"');
                $query->orWhereRaw('revision_data LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->doc_no) && $request->doc_no) {
            $query = $query->where('inspection_fire_pre_noc_checklist.doc_no', 'LIKE', '%' . $request->doc_no . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->whereDate('inspection_fire_pre_noc_checklist.issue_date', '=', DBdateformat($request->issue_date));
        }
        if (isset($request->rev_date) && $request->rev_date) {
            $query = $query->where('inspection_fire_pre_noc_checklist.revision_data', 'LIKE', '%' . $request->rev_date . '%');
        }
        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_fire_pre_noc_checklist.inspection_status', decryptId($request->inspection_status));
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
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_fire_pre_noc_checklist'));

        static::created(function ($model) {

            $uniqueId = 'FIRE-PRENOC-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['inspection_id' => $uniqueId]);
        });
    }
}
