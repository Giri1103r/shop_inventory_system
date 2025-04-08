<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Google\Rpc\Context\AttributeContext\Request;

class MonthlyPhysicalInspection extends Model
{
    protected $table = 'inspection_fire_monthly_physical_inspection';

    protected $fillable = [
        'id',
        'document_reference_id',
        'date_of_inspection',
        'location',
        'unit',
        'inspected_data',
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
        $query = $this->select('inspection_fire_monthly_physical_inspection.*', 'masters_unit.*', 'masters_location.*', 'inspection_fire_monthly_physical_inspection.id as inspection_id')
            ->leftJoin('masters_location', 'inspection_fire_monthly_physical_inspection.location', '=', 'masters_location.id')
            ->leftJoin('masters_unit', 'inspection_fire_monthly_physical_inspection.unit', '=', 'masters_unit.id');


        $org_total =  $query;
        $org_total_counts = $org_total->count();



        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];

            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('masters_location.location_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('masters_unit.unit_name LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->location_id) && $request->location_id) {
            $query = $query->where('inspection_fire_monthly_physical_inspection.location', 'LIKE', '%' . decryptId($request->location_id) . '%');
        }

        if (isset($request->unit_id) && $request->unit_id) {
            $query = $query->where('inspection_fire_monthly_physical_inspection.unit', 'LIKE', '%' . decryptId($request->unit_id) . '%');
        }

        if (isset($request->inspection_date) && $request->inspection_date) {
            $query = $query->where('inspection_fire_monthly_physical_inspection.date_of_inspection', 'LIKE', '%' . DBdateformat($request->inspection_date) . '%');
        }



        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_fire_monthly_physical_inspection.inspection_status', decryptId($request->inspection_status));
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "inspection_status":
                    $query = $query->orderBy('inspection_fire_monthly_physical_inspection.inspection_status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_fire_monthly_physical_inspection.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_fire_monthly_physical_inspection.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_fire_monthly_physical_inspection.id', 'DESC');
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
        $request = Request();
        $ids = $request->id;

        foreach ($ids as $index =>  $id) {
            $id = decryptId($id);
            $inspected_data[$id] = [
                'id' => $id,
                'remarks' => $request->remarks[$index],
                'status' => $request->status[$index],
            ];
        }


        $inspected_data = json_encode($inspected_data);
        $insert_array = [
            'document_reference_id' => decryptId($request->document_reference_id),
            'date_of_inspection' => DBdateformat($request->inspection_date),
            'location' => decryptId($request->location_id),
            'unit' => decryptId($request->unit_id),
            'inspected_data' => $inspected_data,
            'created_by' => Auth::id(),
        ];
        return $this->create($insert_array);
    }

    public function  selectOne($id)
    {
        return $this->where('id', $id)->first();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_fire_monthly_physical_inspection'));
    }
}
