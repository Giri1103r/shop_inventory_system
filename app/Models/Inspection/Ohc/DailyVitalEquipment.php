<?php

namespace App\Models\Inspection\Ohc;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class DailyVitalEquipment extends Model
{
    protected $table = 'ohc_daily_vital_equipment_checklist';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'doc_no',
        'issue_date',
        'revision_data',
        'date_of_inspection',
        'shift',
        'unit',
        'sr_no',
        'responses',
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
        $query = $this->select('ohc_daily_vital_equipment_checklist.*');

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('doc_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('issue_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('revision_data', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('doc_no') && $request->doc_no) {
            $query = $query->where('doc_no', 'LIKE', '%' . $request->doc_no . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->whereDate('issue_date', '=', DBdateformat($request->issue_date));
        }
        if ($request->has('revision_data') && $request->revision_data) {
            $query = $query->where('revision_data', 'LIKE', '%' . $request->revision_data . '%');
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

        $mergedResponses = [];

        foreach ($request->checklist as $sub_type_id => $checklist_items) {
            foreach ($checklist_items as $checklist_id => $value) {
                $mergedResponses[$sub_type_id][$checklist_id] = [
                    'response' => $value,
                    'remark' => $request->remarks[$sub_type_id][$checklist_id] ?? null,
                    'quantity' => $request->quantity[$sub_type_id][$checklist_id] ?? null,
                ];
            }
        }   
        $responsesJson = json_encode($mergedResponses);

        $responses = $request->checklist;
        $quantity = $request->quantity;
        $respones = json_encode($responses);
        $insert_array = [
            'issue_date' => DBdateformat($request->issue_date),
            'revision_data' => $request->revision_data,
            'doc_no' => $request->doc_no,
            'date_of_inspection' => DBdateformat($request->inspection_date),
            'shift' => decryptId($request->shift),
            'unit' => decryptId($request->unit_id),
            'created_by' => Auth::id(),
            'responses' => $responsesJson,
        ];
        
        return $this->create($insert_array);
    }

    public function selectOne($id)
    {
        return  $this->where('id', $id)->first();
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('ohc_daily_vital_equipment_checklist.*');
        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('doc_no LIKE "%' . $search . '%"');
                $query->orWhereRaw('issue_date LIKE "%' . $search . '%"');
                $query->orWhereRaw('revision_data LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->doc_no) && $request->doc_no) {
            $query = $query->where('ohc_daily_vital_equipment_checklist.doc_no', 'LIKE', '%' . $request->doc_no . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->where('ohc_daily_vital_equipment_checklist.issue_date', 'LIKE', '%' . $request->issue_date . '%');
        }
        if (isset($request->revision_data) && $request->revision_data) {
            $query = $query->where('ohc_daily_vital_equipment_checklist.revision_data', 'LIKE', '%' . $request->revision_data . '%');
        }
        
        $query->orderBy('id', 'DESC');

        return  $query->get();
    }
}
