<?php

namespace App\Models\Inspection\Safety;

use App\Scopes\TrashScope;
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
            'inspection_safety_ohs_report.id as inspection_id'
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
        $description = $request->description;
        $fire_pump_details = $request->fire_pump_details;

        foreach ($description as $key => $value) {
            $quantity_details[$key] = [
                'description' => $request->description[$key],
                'unit - 1' => $request->unit_1[$key],
                'unit - 2' => $request->unit_2[$key],
                'unit - 3' => $request->unit_3[$key],
                'unit - 4' => $request->unit_4[$key],
                'total_quantity' => $request->total_quantity[$key],
            ];
        }
        $quantity_details = json_encode($quantity_details);

        foreach ($fire_pump_details as $key => $index) {
            $fire_water_pump_details[$key] = [
                'fire_pump_details' => $value,
                'fire_pump_details_unit_1' => $request->fire_pump_details_unit_1[$key],
                'fire_pump_details_unit_2' => $request->fire_pump_details_unit_2[$key],
                'fire_pump_details_unit_3' => $request->fire_pump_details_unit_3[$key],
                'fire_pump_details_unit_4' => $request->fire_pump_details_unit_4[$key],
            ];
        }
        $fire_water_pump_details = json_encode($fire_water_pump_details);

        $insert_array = [
            'document_reference_id' => decryptId($request->document_reference_id),
            'inspection_date' => DBdateformat($request->inspection_date),
            'updated_frequency' => ($request->updated_frequency),
            'quantity_details' => $quantity_details,
            'fire_water_pump_details' => $fire_water_pump_details,
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
