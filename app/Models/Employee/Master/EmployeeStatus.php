<?php

namespace App\Models\Employee\Master;

use App\Scopes\TrashScope;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeStatus extends Model
{
    use  HasFactory;


    protected $table = 'masters_employee_status';
    protected $primaryKey = 'id';

    protected $fillable = [
        'est_code',
        'employee_status_name',
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
        $query = $this->select('masters_employee_status.*');

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('masters_employee_status.est_code', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_employee_status.employee_status_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('est_code') && $request->est_code) {
            $query = $query->where('masters_employee_status.est_code', 'LIKE', '%' . $request->est_code . '%');
        }

        if ($request->has('employee_status_name') && $request->employee_status_name) {
            $query = $query->where('masters_employee_status.employee_status_name', 'LIKE', '%' . $request->employee_status_name . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('masters_employee_status.status', decryptId($request->status));
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

    public function UniqueCheck($employee_status_name)
    {

        return $this->where('employee_status_name', $employee_status_name)->get();
    }

    public function ExistuniqueCheck($employee_status_name, $id)
    {

        return $this->where('employee_status_name', $employee_status_name)
            ->where('id', '!=', $id)
            ->get();
    }

    public function store()
    {
        $request = request();

        /* est_code is filled in the created() hook, once the auto increment id is known. */
        $insert_array = array(
            'est_code' => '',
            'employee_status_name' => $request->employee_status_name,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'employee_status_name' => $request->employee_status_name,
        );
        return $this->where('id', $id)->update($update_array);
    }

    public function ajaxList($blood_group_id = '')
    {
        $query = $this->select('id', 'employee_status_name')->where('status', 1);

        if ($blood_group_id != '') {
            $query = $query->orWhere('id', $blood_group_id);
        }

        $datas = $query->get();

        $list = [];
        foreach ($datas as $data) {
            $listvalue = [];
            $listvalue['id'] = encryptId($data->id);
            $listvalue['name'] = $data->employee_status_name;
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
        $query = $this->select('masters_employee_status.*');

        if ($request->has('est_code') && $request->est_code) {
            $query = $query->where('masters_employee_status.est_code', 'LIKE', '%' . $request->est_code . '%');
        }

        if ($request->has('employee_status_name') && $request->employee_status_name) {
            $query = $query->where('masters_employee_status.employee_status_name', 'LIKE', '%' . $request->employee_status_name . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('masters_employee_status.status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');
        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select('masters_employee_status.*')
            ->where('masters_employee_status.id', $id)
            ->first();

        return $data;
    }

    public function getEmployeeStatus()
    {
        return $this->where('status', 1)->get();
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('masters_employee_status'));

        static::created(function ($model) {

            $uniqueId = 'EST-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['est_code' => $uniqueId]);
        });
    }
}
