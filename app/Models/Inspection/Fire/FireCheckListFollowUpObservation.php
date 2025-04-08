<?php

namespace App\Models\Inspection\Fire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inspection\InspectionStaticDocno;

class FireCheckListFollowUpObservation extends Model
{
    protected $table = 'inspection_fire_observation';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'inspection_id',
        'sr_no',
        'inspection_type',
        'inspection_id',
        'unit_id',
        'department_id',
        'equipment_name',
        'equipment_code',
        'observation',
        'date',
        'month',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',

    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_fire_checklist_follow.*');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('document_number LIKE "%' . $search . '%"');
                $query->orWhereRaw('issue_date LIKE "%' . $search . '%"');
                $query->orWhereRaw('revision_data LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->document_number) && $request->document_number) {
            $query = $query->where('inspection_fire_checklist_follow.document_number', 'LIKE', '%' . $request->document_number . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->where('inspection_fire_checklist_follow.issue_date', 'LIKE', '%' . $request->issue_date . '%');
        }
        if (isset($request->rev_date) && $request->rev_date) {
            $query = $query->where('inspection_fire_checklist_follow.revision_data', 'LIKE', '%' . $request->rev_date . '%');
        }

        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_fire_checklist_follow.inspection_status', decryptId($request->inspection_status));
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "revision_data":
                    $query->orderBy('inspection_fire_checklist_follow.revision_data', $columnorder);
                    break;
                case "issue_date":
                    $query = $query->orderBy('inspection_fire_checklist_follow.issue_date', $columnorder);
                    break;
                case "document_number":
                    $query = $query->orderBy('inspection_fire_checklist_follow.document_number', $columnorder);
                    break;
                case "inspection_status":
                    $query = $query->orderBy('inspection_fire_checklist_follow.inspection_status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_fire_checklist_follow.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_fire_checklist_follow.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_fire_checklist_follow.id', 'DESC');
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

    public function store($inspection_id)
    {
        $request = request();
        $data = [];

        foreach ($request->obs as $index => $obs) {
            $insert_array = [
                'inspection_id' => $inspection_id,
                'sr_no' => $obs['serial_number'] ?? null,
                'unit_id' => $obs['unit_id'] ?? null,
                'department_id' => $obs['department_id'] ?? null,
                'equipment_name' => $obs['equipment_name'] ?? null,
                'equipment_code' => $obs['equipment_code'] ?? null,
                'observation' => $obs['observation'] ?? null,
                'date' => DBdateformat($obs['date']) ?? null,
                'month' => $obs['month'] ?? null,
                'created_by' => Auth::id(),
            ];

            $data[] = $this->create($insert_array);
        }

        return $data;
    }


    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_fire_checklist_follow.*');
        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('document_number LIKE "%' . $search . '%"');
                $query->orWhereRaw('issue_date LIKE "%' . $search . '%"');
                $query->orWhereRaw('revision_data LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->document_number) && $request->document_number) {
            $query = $query->where('inspection_fire_checklist_follow.document_number', 'LIKE', '%' . $request->document_number . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->where('inspection_fire_checklist_follow.issue_date', 'LIKE', '%' . $request->issue_date . '%');
        }
        if (isset($request->rev_date) && $request->rev_date) {
            $query = $query->where('inspection_fire_checklist_follow.revision_data', 'LIKE', '%' . $request->rev_date . '%');
        }

        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_fire_checklist_follow.inspection_status', decryptId($request->inspection_status));
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
    }
}
