<?php

namespace App\Models\Inspection\Ohc\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FirstAidEquipment extends Model
{
    protected $table = 'inspection_ohc_master_first_aid_equipment';

    protected $primaryKey = 'id';

    protected $fillable = [
        'medicine_id',
        'freeze_quantity',
        'created_by',
        'updated_by',
        'status',
        'trash',
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
        $query = $this->select('inspection_ohc_master_first_aid_equipment.*', 'ohc_master_medicine.medicine')
            ->leftJoin('ohc_master_medicine', 'ohc_master_medicine.id', '=', 'inspection_ohc_master_first_aid_equipment.medicine_id')
            ->where('inspection_ohc_master_first_aid_equipment.trash','NO');

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                ->orWhere('ohc_master_medicine.medicine', 'LIKE', '%' . $search . '%')
                ->orWhere('inspection_ohc_master_first_aid_equipment.freeze_quantity', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('medicine_id') && $request->medicine_id) {
            $query = $query->where('medicine_id', 'LIKE', '%' . decryptId($request->medicine_id) . '%');
        }

        if ($request->has('freeze_quantity') && $request->freeze_quantity) {
            $query = $query->where('freeze_quantity', 'LIKE', '%' . $request->freeze_quantity . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('inspection_ohc_master_first_aid_equipment.status', decryptId($request->status));
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

        $insert_array = array(
            'medicine_id' => decryptId($request->medicine_id),
            'freeze_quantity' => $request->freeze_quantity,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }


    public function updates($id)
    {
        $request = request();
        $update_array = array(
            'medicine_id' => decryptId($request->medicine_id),
            'freeze_quantity' => $request->freeze_quantity,
            'updated_by' => Auth::id()
        );
        return $this->where('id', $id)->update($update_array);
    }

    public function UniqueCheck($medicine_id)
    {

        return $this->where('medicine_id', $medicine_id)->get();
    }

    public function ExistuniqueCheck($medicine_id, $id)
    {
        return $this->where('medicine_id', $medicine_id)
            ->where('id', '!=', $id)
            ->get();
    }

    public function selectOne($id)
    {
        $data =   $this->select('inspection_ohc_master_first_aid_equipment.*', 'ohc_master_medicine.medicine')
            ->leftJoin('ohc_master_medicine', 'ohc_master_medicine.id', '=', 'inspection_ohc_master_first_aid_equipment.medicine_id')
            ->where('inspection_ohc_master_first_aid_equipment.id', $id)
            ->first();
        return $data;
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

    public function deleterecord($id)
    {

        $update_data = array(
            'status' => 0,
            'trash' => 'YES',
        );

        return $this->where('id', $id)->update($update_data);
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query =   $this->select('inspection_ohc_master_first_aid_equipment.*', 'ohc_master_medicine.medicine')
            ->leftJoin('ohc_master_medicine', 'ohc_master_medicine.id', '=', 'inspection_ohc_master_first_aid_equipment.medicine_id');

            if ($request->search != null || $request->search != '') {
                $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                ->orWhere('ohc_master_medicine.medicine', 'LIKE', '%' . $search . '%')
                ->orWhere('inspection_ohc_master_first_aid_equipment.freeze_quantity', 'LIKE', '%' . $search . '%');

            });
        }

        if ($request->has('medicine_id') && $request->medicine_id) {
            $query = $query->where('medicine_id', 'LIKE', '%' . decryptId($request->medicine_id) . '%');
        }

        if ($request->has('freeze_quantity') && $request->freeze_quantity) {
            $query = $query->where('freeze_quantity', 'LIKE', '%' . $request->freeze_quantity . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('inspection_ohc_master_first_aid_equipment.status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');
        return  $query->get();
    }
}
