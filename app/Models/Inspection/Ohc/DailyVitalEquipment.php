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
        'document_reference_id',
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
        $query = $this->select('ohc_daily_vital_equipment_checklist.*', 'inspection_shift_option.*', 'masters_unit.*')
            ->leftJoin('inspection_shift_option', 'ohc_daily_vital_equipment_checklist.shift', '=', 'inspection_shift_option.id')
            ->leftJoin('masters_unit', 'ohc_daily_vital_equipment_checklist.unit', '=', 'masters_unit.id');

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('ohc_daily_vital_equipment_checklist.date_of_inspection', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_shift_option.shift', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('unit') && $request->unit) {
            $query = $query->where('ohc_daily_vital_equipment_checklist.unit','LIKE', '%' . decryptId($request->unit) . '%');
        }
        if (isset($request->date_of_inspection) && $request->date_of_inspection) {
            $query = $query->whereDate('ohc_daily_vital_equipment_checklist.date_of_inspection', '=', DBdateformat($request->date_of_inspection));
        }
        if ($request->has('shift') && $request->shift) {
            $query = $query->where('ohc_daily_vital_equipment_checklist.shift', 'LIKE', '%' . decryptId($request->shift) . '%');
        }
        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('ohc_daily_vital_equipment_checklist.id', 'DESC');

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
            'document_reference_id' => decryptId($request->document_reference_id),
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
        $query = $this->select('ohc_daily_vital_equipment_checklist.*', 'inspection_shift_option.*', 'masters_unit.*')
            ->leftJoin('inspection_shift_option', 'ohc_daily_vital_equipment_checklist.shift', '=', 'inspection_shift_option.id')
            ->leftJoin('masters_unit', 'ohc_daily_vital_equipment_checklist.unit', '=', 'masters_unit.id');

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query
                ->orWhere('ohc_daily_vital_equipment_checklist.date_of_inspection', 'LIKE', '%' . $search . '%')
                ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                ->orWhere('inspection_shift_option.shift', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('unit') && $request->unit) {
            $query = $query->where('ohc_daily_vital_equipment_checklist.unit','LIKE', '%' . decryptId($request->unit) . '%');
        }
        if (isset($request->date_of_inspection) && $request->date_of_inspection) {
            $query = $query->whereDate('ohc_daily_vital_equipment_checklist.date_of_inspection', '=', DBdateformat($request->date_of_inspection));
        }
        if ($request->has('shift') && $request->shift) {
            $query = $query->where('ohc_daily_vital_equipment_checklist.shift', 'LIKE', '%' . decryptId($request->shift) . '%');
        }

        $query->orderBy('ohc_daily_vital_equipment_checklist.id', 'DESC');

        return  $query->get();
    }
}
