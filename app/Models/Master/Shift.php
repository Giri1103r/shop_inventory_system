<?php

namespace App\Models\Master;

use App\Scopes\TrashScope;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shift extends Model
{
    use  HasFactory;


    protected $table = 'masters_shift';
    protected $primaryKey = 'id';
    protected $fillable = [
        'shift_code',
        'shift_name',
        'start_time',
        'end_time',
        'total_hours',
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
        $query = $this->select('masters_shift.*');

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('masters_shift.shift_code', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_shift.shift_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('shift_code') && $request->shift_code) {
            $query = $query->where('masters_shift.shift_code', 'LIKE', '%' . $request->shift_code . '%');
        }

        if ($request->has('shift_name') && $request->shift_name) {
            $query = $query->where('masters_shift.shift_name', 'LIKE', '%' . $request->shift_name . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('masters_shift.status', decryptId($request->status));
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

    public function UniqueCheck($shift_name)
    {

        return $this->where('shift_name', $shift_name)->get();
    }

    public function ExistuniqueCheck($shift_name, $id)
    {

        return $this->where('shift_name', $shift_name)
            ->where('id', '!=', $id)
            ->get();
    }

    public function store()
    {
        $request = request();

        /* shift_code is NOT NULL in the table, the real code is generated
           in the created() hook once the auto increment id is known. */
        $insert_array = array(
            'shift_code' => '',
            'shift_name' => $request->shift_name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'total_hours' => $request->total_hours,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'shift_name' => $request->shift_name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'total_hours' => $request->total_hours,
        );
        return $this->where('id', $id)->update($update_array);
    }

    public function ajaxList($shift_id = '')
    {
        $query = $this->select('id', 'shift_name')->where('status', 1);

        if ($shift_id != '') {
            $query = $query->orWhere('id', $shift_id);
        }

        $datas = $query->get();

        $list = [];
        foreach ($datas as $data) {
            $listvalue = [];
            $listvalue['id'] = encryptId($data->id);
            $listvalue['name'] = $data->shift_name;
            $list[] = $listvalue;
        }

        return $list;
    }

    public function multipleAjaxList($preselectedIds)
    {
        $query = $this->select('id', 'shift_name')
            ->where(function ($query) use ($preselectedIds) {
                $query->where('status', 1);
                if (!empty($preselectedIds)) {
                    $query->orWhereIn('id', $preselectedIds);
                }
            });

        $datas = $query->get();

        $list = [];
        foreach ($datas as $data) {
            $list[] = [
                'id' => encryptId($data->id),
                'name' => $data->shift_name,
            ];
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
        $query = $this->select('masters_shift.*');

        if ($request->has('shift_code') && $request->shift_code) {
            $query = $query->where('masters_shift.shift_code', 'LIKE', '%' . $request->shift_code . '%');
        }

        if ($request->has('shift_name') && $request->shift_name) {
            $query = $query->where('masters_shift.shift_name', 'LIKE', '%' . $request->shift_name . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('masters_shift.status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');
        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select('masters_shift.*')
            ->where('masters_shift.id', $id)
            ->first();

        return $data;
    }

    public function getShift()
    {
        return Shift::where('status', '!=', 0)->get();
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('masters_shift'));

        static::created(function ($model) {

            $uniqueId = 'SHF-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['shift_code' => $uniqueId]);
        });
    }
}
