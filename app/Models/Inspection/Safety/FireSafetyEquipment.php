<?php

namespace App\Models\Inspection\Safety;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class FireSafetyEquipment extends Model
{
    protected $table = 'inspection_safety_equipment';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'document_reference_id',
        'standard_norms',
        'equipment_id',
        'item_code',
        'equipment_category',
        'measurement_unit',
        'minimum_order_level',
        'economic_order_quantity',
        'observation_status',
        'remark',
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
            'inspection_safety_equipment.*',
            'inspection_static_docno.*',
            'inspection_safety_master_equipment.*',
            'inspection_safety_equipment.id as inspection_id',
            'inspection_safety_equipment.status as equipment_status',
        )
            ->leftJoin(
                'inspection_static_docno',
                'inspection_safety_equipment.document_reference_id',
                '=',
                'inspection_static_docno.id'
            )->leftJoin(
                'inspection_safety_master_equipment',
                'inspection_safety_equipment.equipment_id',
                '=',
                'inspection_safety_master_equipment.id'
            );


        $org_total =  $query;
        $org_total_counts = $org_total->count();


        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('equipment_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('item_code LIKE "%' . $search . '%"');
            });
        }


        if (isset($request->equipment_name) && $request->equipment_name) {
            $query = $query->where('inspection_safety_equipment.equipment_id', 'LIKE', '%' . decryptId($request->equipment_name) . '%');
        }
        if (isset($request->item_code) && $request->item_code) {
            $query = $query->where('inspection_safety_equipment.item_code', '=', ($request->item_code));
        }
        if (isset($request->standard_norms) && $request->standard_norms) {
            $query = $query->where('inspection_safety_equipment.standard_norms', 'LIKE', '%' . decryptId($request->standard_norms) . '%');
        }

        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_safety_equipment.status', decryptId($request->status));
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "revision_data":
                    $query->orderBy('inspection_safety_equipment.revision_data', $columnorder);
                    break;
                case "issue_date":
                    $query = $query->orderBy('inspection_safety_equipment.issue_date', $columnorder);
                    break;
                case "document_number":
                    $query = $query->orderBy('inspection_safety_equipment.doc_no', $columnorder);
                    break;
                case "status":
                    $query = $query->orderBy('inspection_safety_equipment.status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_safety_equipment.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_safety_equipment.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_safety_equipment.id', 'DESC');
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

        $equipment_name = $request->equipment_name;
        $item_code = $request->item_code;
        $standard_norms = $request->standard_norms;
        $equipment_category = $request->equipment_category;
        $unit_of_measurement = $request->unit_of_measurement;
        $minimum_order_value = $request->minimum_order_value;
        $economic_order_quantity = $request->economic_order_quantity;
        $observation_status = ($request->observation_status);
        $remarks = $request->remarks;



        foreach ($item_code as $index => $item_code_value) {
            $data = array(
                'document_reference_id' => decryptId($request->document_reference_id),
                'equipment_id' => decryptId($equipment_name[$index]),
                'item_code' => $item_code_value,
                'standard_norms' => decryptId($standard_norms[$index]),
                'equipment_category' => ($equipment_category[$index]),
                'measurement_unit' => $unit_of_measurement[$index],
                'economic_order_quantity' => $economic_order_quantity[$index],
                'minimum_order_level' => $minimum_order_value[$index],
                'observation_status' => decryptId($observation_status[$index]),
                'remark' => $remarks[$index],
                'created_by' => Auth::id(),
            );
            $result =  $this->EquipmentUniqueCheck($data['equipment_id']);
            if ($result) {
                $this->create($data);
            }
        }
    }

    public function selectOne($id)
    {
        return $this->where('id', $id)->where('trash', 'NO')->first();
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select(
            'inspection_safety_equipment.*',
            'inspection_static_docno.*',
            'inspection_safety_master_equipment.*',
            'inspection_safety_equipment.id as inspection_id'
        )
            ->leftJoin(
                'inspection_static_docno',
                'inspection_safety_equipment.document_reference_id',
                '=',
                'inspection_static_docno.id'
            )->leftJoin(
                'inspection_safety_master_equipment',
                'inspection_safety_equipment.equipment_id',
                '=',
                'inspection_safety_master_equipment.id'
            );

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('equipment_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('item_code LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->equipment_name) && $request->equipment_name) {
            $query = $query->where('inspection_safety_equipment.equipment_id', 'LIKE', '%' . decryptId($request->equipment_name) . '%');
        }
        if (isset($request->item_code) && $request->item_code) {
            $query = $query->where('inspection_safety_equipment.item_code', '=', ($request->item_code));
        }
        if (isset($request->standard_norms) && $request->standard_norms) {
            $query = $query->where('inspection_safety_equipment.standard_norms', 'LIKE', '%' . decryptId($request->standard_norms) . '%');
        }

        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_safety_equipment.inspection_status', decryptId($request->inspection_status));
        }
        $query->orderBy('inspection_safety_equipment.id', 'DESC');

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

    public function UniqueCheck($item_code, $equipment_name)
    {
        $unique =  $this->where('equipment_id',  $equipment_name)->where('item_code', $item_code)->get();
        if (count($unique) > 0) {
            return false;
        }
        return true;
    }
    public function EquipmentUniqueCheck($equipment_name)
    {
        $unique =  $this->where('equipment_id',  $equipment_name)->where('status', '1')->get();
        if (count($unique) > 0) {
            return false;
        }
        return true;
    }

    public function ExistuniqueCheck($data)
    {
        $unique =  $this->where('resource_code',  $data['category_name'])
            ->where('id', '!=', ($data['id']))
            ->where('status', '1')
            ->get();

        if (count($unique) > 0) {
            return false;
        }
        return true;
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_safety_equipment'));
    }
}
