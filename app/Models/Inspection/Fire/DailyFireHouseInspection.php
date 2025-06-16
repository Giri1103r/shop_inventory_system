<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class DailyFireHouseInspection extends Model
{
    protected $table = 'inspection_daily_fire_pump_checklist';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'inspection_id',
        'document_reference_id',
        'date_of_inspection',
        'unit_id',
        'shift_id',
        'checklist',
        'date',
        'note',
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
        $query = $this->select('inspection_daily_fire_pump_checklist.*', 'inspection_daily_fire_pump_checklist.id as fire_id', 'masters_unit.unit_name', 'inspection_shift_option.shift')->leftjoin('masters_unit', 'masters_unit.id', '=', 'inspection_daily_fire_pump_checklist.unit_id')->leftjoin('inspection_shift_option', 'inspection_shift_option.id', '=', 'inspection_daily_fire_pump_checklist.shift_id');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('shift LIKE "%' . $search . '%"');
                $query->orWhereRaw('unit_name LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->unit_id) && $request->unit_id) {
            $query = $query->where('inspection_daily_fire_pump_checklist.unit_id', 'LIKE', '%' . decryptId($request->unit_id) . '%');
        }
        if (isset($request->shift_id) && $request->shift_id) {
            $query = $query->where('inspection_daily_fire_pump_checklist.shift_id', decryptId($request->shift_id));
        }
         if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_daily_fire_pump_checklist.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_daily_fire_pump_checklist.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_daily_fire_pump_checklist.created_at', [$startDate, $endDate]);
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


        foreach ($request->checklist as $subTypeId => $checklists) {
            foreach ($checklists as $checklistId => $response) {
                $structuredChecklist[$checklistId] = [
                    'response' => $response,
                    'pump_no' => $request->pump_no[$checklistId] ?? 'N/A',
                    'remarks' => $request->remarks[$checklistId] ?? '',
                    'sub_type_id' => $checklistId
                ];
            }
        }

        $insert_array = [
            'document_reference_id' => decryptId($request->document_reference_id),
            'date_of_inspection' => DBdateformat($request->date_of_inspection),
            'unit_id' => decryptId($request->unit_id),
            'shift_id' => decryptId($request->shift_id),
            'checklist' => json_encode($structuredChecklist),
            'date' => DBdateformat($request->date),
            'note' => $request->note,
            'created_by' => Auth::id(),
        ];

        return $this->create($insert_array);
    }


    public function selectOne($id)
    {
        $data =   $this->select('inspection_daily_fire_pump_checklist.*' , 'masters_unit.unit_name','inspection_static_docno.doc_no as document_no','inspection_static_docno.issue_date as issuedate','inspection_static_docno.rev_dt as rev_date')
        ->leftjoin('masters_unit', 'masters_unit.id', '=', 'inspection_daily_fire_pump_checklist.unit_id')
        ->leftjoin('inspection_static_docno', 'inspection_static_docno.id', '=', 'inspection_daily_fire_pump_checklist.document_reference_id')
        ->where('inspection_daily_fire_pump_checklist.id', $id)->first();

        return $data;
    }

    public function selectDataForPdf($id){
        return $this->where('id', $id)->first();
    }
    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_daily_fire_pump_checklist.*', 'masters_unit.unit_name', 'inspection_shift_option.shift')->leftjoin('masters_unit', 'masters_unit.id', '=', 'inspection_daily_fire_pump_checklist.unit_id')->leftjoin('inspection_shift_option', 'inspection_shift_option.id', '=', 'inspection_daily_fire_pump_checklist.shift_id');

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('shift LIKE "%' . $search . '%"');
                $query->orWhereRaw('unit_name LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->unit_id) && $request->unit_id) {
            $query = $query->where('inspection_daily_fire_pump_checklist.unit_id', 'LIKE', '%' . decryptId($request->unit_id) . '%');
        }
        if (isset($request->shift_id) && $request->shift_id) {
            $query = $query->where('inspection_daily_fire_pump_checklist.shift_id', decryptId($request->shift_id));
        }
         if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_daily_fire_pump_checklist.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_daily_fire_pump_checklist.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_daily_fire_pump_checklist.created_at', [$startDate, $endDate]);
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
        static::addGlobalScope(new TrashScope('inspection_daily_fire_pump_checklist'));

        static::created(function ($model) {

            $uniqueId = 'DAILY-FIRE-HOUSE-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['inspection_id' => $uniqueId]);
        });
    }
}
