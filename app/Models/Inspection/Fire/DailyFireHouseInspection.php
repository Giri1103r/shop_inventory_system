<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
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
        $query = $this->select('inspection_daily_fire_pump_checklist.*', 'masters_unit.unit_name', 'inspection_shift_option.shift')->leftjoin('masters_unit', 'masters_unit.id', '=', 'inspection_daily_fire_pump_checklist.unit_id')->leftjoin('inspection_shift_option', 'inspection_shift_option.id', '=', 'inspection_daily_fire_pump_checklist.shift_id');
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
            $query = $query->where('inspection_daily_fire_pump_checklist.inspection_id', 'LIKE', '%' . $request->inspection_id . '%');
        }
        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_daily_fire_pump_checklist.status', decryptId($request->status));
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
                    'sub_type_id' => $subTypeId
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
        $data =   $this->select('inspection_daily_fire_pump_checklist.*', 'masters_unit.unit_name', 'inspection_fire_signatureupload.file_path','inspection_static_docno.doc_no as document_no','inspection_static_docno.issue_date as issuedate','inspection_static_docno.rev_dt as rev_date')
        ->leftjoin('masters_unit', 'masters_unit.id', '=', 'inspection_daily_fire_pump_checklist.unit_id')
        ->leftjoin('inspection_fire_signatureupload', 'inspection_daily_fire_pump_checklist.id', '=', 'inspection_fire_signatureupload.inspection_id')
        ->leftjoin('inspection_static_docno', 'inspection_static_docno.id', '=', 'inspection_daily_fire_pump_checklist.document_reference_id')
        ->where('inspection_fire_signatureupload.type', DAILY_FIRE_PUMP)
        ->where('inspection_daily_fire_pump_checklist.id', $id)->first();
        return $data;
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_daily_fire_pump_checklist.*');
        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('doc_no LIKE "%' . $search . '%"');
                $query->orWhereRaw('issue_date LIKE "%' . $search . '%"');
                $query->orWhereRaw('revision_data LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->doc_no) && $request->doc_no) {
            $query = $query->where('inspection_daily_fire_pump_checklist.doc_no', 'LIKE', '%' . $request->doc_no . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->whereDate('inspection_daily_fire_pump_checklist.issue_date', '=', DBdateformat($request->issue_date));
        }
        if (isset($request->rev_date) && $request->rev_date) {
            $query = $query->where('inspection_daily_fire_pump_checklist.revision_data', 'LIKE', '%' . $request->rev_date . '%');
        }
        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_daily_fire_pump_checklist.inspection_status', decryptId($request->inspection_status));
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
