<?php

namespace App\Models\KPI;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class LeadingLagging extends Model
{
    protected $table = 'kpi_master_leading_lagging';
    protected $fillable = [
        'id',
        'type',
        'value',
        'updated_by',
        'created_by',
        'trash',
        'trash',
        'updated_at',
        'created_at',
    ];
    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('kpi_master_leading_lagging.*');

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('value', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('type') && $request->type) {
            $query = $query->where('type', 'LIKE', '%' . decryptId($request->type) . '%');
        }
        if ($request->has('value') && $request->value) {
            $query = $query->where('value', 'LIKE', '%' . ($request->value) . '%');
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('kpi_master_leading_lagging.status', decryptId($request->status));
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

    public function UniqueCheck($value, $type)
    {

        return $this->where('value',  $value)->where('type', $type)->get();
    }

    public function ExistuniqueCheck($value, $type, $id)
    {
        return $this->where('value',  $value)->where('type', $type)
            ->where('id', '!=', $id)
            ->get();
    }

    public function store()
    {
        $request = request();
        $insert_array = array(

            'type' => decryptId($request->type),
            'value' => $request->value,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'type' => decryptId($request->type),
            'value' => $request->value,
            'updated_by' => Auth::id()
        );
        return $this->where('id', $id)->update($update_array);
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
        $query = $this->select('kpi_master_leading_lagging.*');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('value', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('type') && $request->type) {
            $query = $query->where('type', 'LIKE', '%' . decryptId($request->type) . '%');
        }
        if ($request->has('value') && $request->value) {
            $query = $query->where('value', 'LIKE', '%' . ($request->value) . '%');
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('kpi_master_leading_lagging.status', decryptId($request->status));
        }


        $query->orderBy('id', 'DESC');

        return  $query->get();
    }


    public function selectOne($id)
    {
        return   $this->where('id', $id)->first();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('kpi_master_leading_lagging'));

        static::created(function ($model) {

            $uniqueId = 'LOC-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['location_id' => $uniqueId]);
        });
    }
}
