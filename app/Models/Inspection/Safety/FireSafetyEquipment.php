<?php

namespace App\Models\Inspection\Safety;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class FireSafetyEquipment extends Model
{
    protected $table = 'inspection_safety_equipment';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'doc_no',
        'issue_date',
        'revision_data',
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
        $query = $this->select('inspection_safety_equipment.*');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('doc_no LIKE "%' . $search . '%"');
                $query->orWhereRaw('issue_date LIKE "%' . $search . '%"');
                $query->orWhereRaw('revision_data LIKE "%' . $search . '%"');
            });
        }


        if (isset($request->document_number) && $request->document_number) {
            $query = $query->where('inspection_safety_equipment.doc_no', 'LIKE', '%' . $request->document_number . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->whereDate('inspection_forklift_inpsection_monthly.issue_date', '=', DBdateformat($request->issue_date));
        }
        if (isset($request->rev_date) && $request->rev_date) {
            $query = $query->where('inspection_safety_equipment.revision_data', 'LIKE', '%' . $request->rev_date . '%');
        }

        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_safety_equipment.status', decryptId($request->status));
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "revision_data":
                    $query->orderBy('inspection_safety_equipment.revision_data', $columnorder);
                    break;
                case "issue_date":
                    $query = $query->orderBy('inspection_safety_equipment.issue_date', $columnorder);
                    break;
                case "document_number":
                    $query = $query->orderBy('inspection_safety_equipment.doc_no', $columnorder);
                    break;
                case "status":
                    $query = $query->orderBy('inspection_safety_equipment.status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_safety_equipment.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_safety_equipment.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_safety_equipment.id', 'DESC');
                    break;
            }
        }

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

        $data = array(
            'doc_no' => $request->doc_no,
            'issue_date' => ($request->issue_date),
            'revision_data' => $request->rev_date,
            'created_by' => Auth::id(),
        );

        return $this->create($data);
    }

    public function selectOne($id)
    {
        return $this->where('id', $id)->where('status', 1)->where('trash', 'NO')->first();
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_safety_equipment.*');
        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('doc_no LIKE "%' . $search . '%"');
                $query->orWhereRaw('issue_date LIKE "%' . $search . '%"');
                $query->orWhereRaw('revision_data LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->document_number) && $request->document_number) {
            $query = $query->where('inspection_safety_equipment.doc_no', 'LIKE', '%' . $request->document_number . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->whereDate('inspection_forklift_inpsection_monthly.issue_date', '=', DBdateformat($request->issue_date));
        }
        if (isset($request->rev_date) && $request->rev_date) {
            $query = $query->where('inspection_safety_equipment.revision_data', 'LIKE', '%' . $request->rev_date . '%');
        }

        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_safety_equipment.inspection_status', decryptId($request->inspection_status));
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_safety_equipment'));
    }
}
