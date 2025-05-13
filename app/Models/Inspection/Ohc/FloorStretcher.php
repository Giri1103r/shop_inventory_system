<?php

namespace App\Models\Inspection\Ohc;

use App\Scopes\TrashScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
        'responses',
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
        $query = $this->select('inspection_ohc_floorstretcher_checklist.*', 'masters_unit.*', 'inspection_frequency_option.*', 'inspection_shift_option.*', 'inspection_ohc_floorstretcher_checklist.id as checklist_id' ,  'inspection_ohc_floorstretcher_checklist.created_at as inspection_created_at')
            ->leftJoin('masters_unit', 'inspection_ohc_floorstretcher_checklist.unit', '=', 'masters_unit.id')
            ->leftJoin('inspection_frequency_option', 'inspection_ohc_floorstretcher_checklist.frequency', '=', 'inspection_frequency_option.id')
            ->leftJoin('inspection_shift_option', 'inspection_ohc_floorstretcher_checklist.shift', '=', 'inspection_shift_option.id');
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
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_floorstretcher_checklist.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_floorstretcher_checklist.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_ohc_floorstretcher_checklist.created_at', [$startDate, $endDate]);
        }
        if (isset($request->frequency) && $request->frequency) {
            $query = $query->where('inspection_ohc_floorstretcher_checklist.frequency', decryptId($request->frequency));
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->where('inspection_ohc_floorstretcher_checklist.issue_date', DBdateformat($request->issue_date));
        }
        if (isset($request->unit) && $request->unit) {
            $query = $query->where('inspection_ohc_floorstretcher_checklist.unit', decryptId($request->unit));
        }
        if (isset($request->shift_id) && $request->shift_id) {
            $query = $query->where('inspection_ohc_floorstretcher_checklist.shift', decryptId($request->shift_id));
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

    public function store()
    {
        $request = request();

        $json_data = [
            'resource_code' => $request->resource_code,
            'response' => $request->response,
            'remarks' => $request->remarks,
        ];

        $data = array(
            'issue_date' => DBdateformat($request->inspection_date),
            'unit' => decryptId($request->unit_id),
            'shift' => decryptId($request->shift_id),
            'frequency' => decryptId($request->frequency_id),
            'responses' => json_encode($json_data),
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
            $query = $query->where('inspection_ohc_floorstretcher_checklist.frequency', decryptId($request->frequency));
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->where('inspection_ohc_floorstretcher_checklist.issue_date', DBdateformat($request->issue_date));
        }
        if (isset($request->unit) && $request->unit) {
            $query = $query->where('inspection_ohc_floorstretcher_checklist.unit', decryptId($request->unit));
        }
        if (isset($request->shift_id) && $request->shift_id) {
            $query = $query->where('inspection_ohc_floorstretcher_checklist.shift', decryptId($request->shift_id));
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_floorstretcher_checklist.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_floorstretcher_checklist.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_ohc_floorstretcher_checklist.created_at', [$startDate, $endDate]);
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_ohc_floorstretcher_checklist'));
    }
}
