<?php

namespace App\Models\Inspection\Ohc;

use App\Scopes\TrashScope;
use Illuminate\Database\Eloquent\Model;

class FloorStretcher extends Model
{
    protected $table = 'inspection_ohc_floorstretcher_checklist';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'issue_date',
        'frequency',
        'unit',
        'shift',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'status',
        'trash',
    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_ohc_floorstretcher_checklist.*','masters_unit.*','inspection_frequency_option.*','inspection_shift_option.*','inspection_ohc_floorstretcher_checklist.id as checklist_id')
                    ->leftJoin('masters_unit','inspection_ohc_floorstretcher_checklist.unit','=','masters_unit.id')
                    ->leftJoin('inspection_frequency_option','inspection_ohc_floorstretcher_checklist.frequency','=','inspection_frequency_option.id')
                    ->leftJoin('inspection_shift_option','inspection_ohc_floorstretcher_checklist.shift','=','inspection_shift_option.id');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('inspection_frequency_option.frequency_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('masters_unit.unit_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_shift_option.shift LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->frequency) && $request->frequency) {
            $query = $query->where('inspection_ohc_floorstretcher_checklist.frequency', 'LIKE', '%' . $request->frequency . '%');
        }
        if (isset($request->unit) && $request->unit) {
            $query = $query->where('inspection_ohc_floorstretcher_checklist.unit', 'LIKE', '%' . $request->unit . '%');
        }
        if (isset($request->rev_date) && $request->rev_date) {
            $query = $query->where('inspection_ohc_floorstretcher_checklist.shift', 'LIKE', '%' . $request->rev_date . '%');
        }

        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_ohc_floorstretcher_checklist.inspection_status', decryptId($request->inspection_status));
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "shift":
                    $query->orderBy('inspection_ohc_floorstretcher_checklist.shift', $columnorder);
                    break;
                case "unit":
                    $query = $query->orderBy('inspection_ohc_floorstretcher_checklist.unit', $columnorder);
                    break;
                case "frequency":
                    $query = $query->orderBy('inspection_ohc_floorstretcher_checklist.frequency', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_ohc_floorstretcher_checklist.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_ohc_floorstretcher_checklist.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_ohc_floorstretcher_checklist.id', 'DESC');
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


    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_ohc_floorstretcher_checklist.*');
        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('frequency LIKE "%' . $search . '%"');
                $query->orWhereRaw('unit LIKE "%' . $search . '%"');
                $query->orWhereRaw('shift LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->frequency) && $request->frequency) {
            $query = $query->where('inspection_ohc_floorstretcher_checklist.frequency', 'LIKE', '%' . $request->frequency . '%');
        }
        if (isset($request->unit) && $request->unit) {
            $query = $query->where('inspection_ohc_floorstretcher_checklist.unit', 'LIKE', '%' . $request->unit . '%');
        }
        if (isset($request->rev_date) && $request->rev_date) {
            $query = $query->where('inspection_ohc_floorstretcher_checklist.shift', 'LIKE', '%' . $request->rev_date . '%');
        }

        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_ohc_floorstretcher_checklist.inspection_status', decryptId($request->inspection_status));
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_ohc_floorstretcher_checklist'));
    }
}
