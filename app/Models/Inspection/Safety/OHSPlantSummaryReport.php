<?php

namespace App\Models\Inspection\Safety;

use App\Scopes\TrashScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class OHSPlantSummaryReport extends Model
{


    protected $table = 'inspection_safety_ohs_report';
    protected $primarykey = 'id';
    protected $fillable = [
        'id',
        'document_reference_id',
        'inspection_date',
        'updated_frequency',
        'quantity_details',
        'fire_water_pump_details',
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
        $query = $this->select(
            'inspection_safety_ohs_report.*',
            'inspection_static_docno.*',
            'inspection_safety_ohs_report.id as inspection_id',
             'inspection_safety_ohs_report.created_at as inspection_created_at'
        )
            ->leftJoin(
                'inspection_static_docno',
                'inspection_safety_ohs_report.document_reference_id',
                '=',
                'inspection_static_docno.id'
            );



        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {});
        }

        if ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_safety_ohs_report.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_safety_ohs_report.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_safety_ohs_report.created_at', [$startDate, $endDate]);
        }
        if (isset($request->inspection_date) && $request->inspection_date) {
            $query = $query->whereDate('inspection_safety_ohs_report.inspection_date', '=', DBdateformat($request->inspection_date));
        }
        if (isset($request->frequency) && $request->frequency) {
            $query = $query->where('inspection_safety_ohs_report.updated_frequency', '=', ($request->frequency));
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "status":
                    $query = $query->orderBy('inspection_safety_ohs_report.inspection_status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_safety_ohs_report.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_safety_ohs_report.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_safety_ohs_report.id', 'DESC');
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

        $quantity_details = [];
        $descriptions = $request->description ?? [];

        foreach ($descriptions as $key => $desc) {
            $item = ['description' => $desc];

            foreach ($request->all() as $field => $values) {
                if (preg_match('/^unit_\d+$/', $field) && isset($values[$key])) {
                    $item[$field] = $values[$key];
                }
            }

            if (isset($request->total_quantity[$key])) {
                $item['total_quantity'] = $request->total_quantity[$key];
            }

            $quantity_details[$key] = $item;
        }

        $fire_water_pump_details = [];
        $fire_pump_details = $request->fire_pump_details ?? [];

        foreach ($fire_pump_details as $key => $desc) {
            $item = ['fire_pump_details' => $desc];

            foreach ($request->all() as $field => $values) {
                if (preg_match('/^fire_pump_details_unit_\d+$/', $field) && isset($values[$key])) {
                    $item[$field] = $values[$key];
                }
            }

            $fire_water_pump_details[$key] = $item;
        }

        $insert_array = [
            'document_reference_id' => decryptId($request->document_reference_id),
            'inspection_date' => DBdateformat($request->inspection_date),
            'updated_frequency' => $request->updated_frequency,
            'quantity_details' => json_encode($quantity_details),
            'fire_water_pump_details' => json_encode($fire_water_pump_details),
            'created_by' => Auth::id(),
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
        $query = $this->select(
            'inspection_safety_ohs_report.*',
            'inspection_safety_ohs_report.created_by as checked_by',
            'inspection_static_docno.*',
            'inspection_safety_ohs_report.id as inspection_id'
        )
            ->leftJoin(
                'inspection_static_docno',
                'inspection_safety_ohs_report.document_reference_id',
                '=',
                'inspection_static_docno.id'
            );

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {});
        }
  if ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_safety_ohs_report.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_safety_ohs_report.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_safety_ohs_report.created_at', [$startDate, $endDate]);
        }

        if (isset($request->inspection_date) && $request->inspection_date) {
            $query = $query->whereDate('inspection_safety_ohs_report.issue_date', '=', DBdateformat($request->inspection_date));
        }

        if (isset($request->frequency) && $request->frequency) {
            $query = $query->whereDate('inspection_safety_ohs_report.updated_frequency', '=', ($request->frequency));
        }

        $query->orderBy('inspection_safety_ohs_report.id', 'DESC');

        return  $query->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_safety_ohs_report'));
    }
}
