<?php

namespace App\Models\Employee\Master;

use App\Scopes\TrashScope;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeType extends Model
{
    use  HasFactory;


    protected $table = 'masters_employee_type';
    protected $primaryKey = 'id';

    protected $fillable = [
        'ety_code',
        'employee_type_name',
        'status',
        'trash',
        'created_by',
        'created_at',
        'updated_at'

    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('masters_employee_type.*');

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('masters_employee_type.ety_code', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_employee_type.employee_type_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('ety_code') && $request->ety_code) {
            $query = $query->where('masters_employee_type.ety_code', 'LIKE', '%' . $request->ety_code . '%');
        }

        if ($request->has('employee_type_name') && $request->employee_type_name) {
            $query = $query->where('masters_employee_type.employee_type_name', 'LIKE', '%' . $request->employee_type_name . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('masters_employee_type.status', decryptId($request->status));
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

    public function UniqueCheck($employee_type_name)
    {

        return $this->where('employee_type_name', $employee_type_name)->get();
    }

    public function ExistuniqueCheck($employee_type_name, $id)
    {

        return $this->where('employee_type_name', $employee_type_name)
            ->where('id', '!=', $id)
            ->get();
    }

    public function store()
    {
        $request = request();

        /* ety_code is filled in the created() hook, once the auto increment id is known. */
        $insert_array = array(
            'ety_code' => '',
            'employee_type_name' => $request->employee_type_name,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'employee_type_name' => $request->employee_type_name,
        );
        return $this->where('id', $id)->update($update_array);
    }

    public function ajaxList($blood_group_id = '')
    {
        $query = $this->select('id', 'employee_type_name')->where('status', 1);

        if ($blood_group_id != '') {
            $query = $query->orWhere('id', $blood_group_id);
        }

        $datas = $query->get();

        $list = [];
        foreach ($datas as $data) {
            $listvalue = [];
            $listvalue['id'] = encryptId($data->id);
            $listvalue['name'] = $data->employee_type_name;
            $list[] = $listvalue;
        }

        return $list;
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
        $query = $this->select('masters_employee_type.*');

        if ($request->has('ety_code') && $request->ety_code) {
            $query = $query->where('masters_employee_type.ety_code', 'LIKE', '%' . $request->ety_code . '%');
        }

        if ($request->has('employee_type_name') && $request->employee_type_name) {
            $query = $query->where('masters_employee_type.employee_type_name', 'LIKE', '%' . $request->employee_type_name . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('masters_employee_type.status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');
        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select('masters_employee_type.*')
            ->where('masters_employee_type.id', $id)
            ->first();

        return $data;
    }

    public function getEmployeeType()
    {
        return $this->where('status', 1)->get();
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('masters_employee_type'));

        static::created(function ($model) {

            $uniqueId = 'ETY-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['ety_code' => $uniqueId]);
        });
    }
}
