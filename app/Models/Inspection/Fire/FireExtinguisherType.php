<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FireExtinguisherType extends Model
{
    protected $table = 'inspection_fire_fire_extinguisher_type';

    protected $fillable = [
        'fire_extinguisher_id',
        'fire_extinguisher_name',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];


    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_fire_fire_extinguisher_type.*');
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('fire_extinguisher_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('fire_extinguisher_name', 'LIKE', '%' . $search . '%');
            });
        }


        if ($request->has('fire_extinguisher_id') && $request->fire_extinguisher_id) {
            $query = $query->where('fire_extinguisher_id', 'LIKE', '%' . $request->fire_extinguisher_id . '%');
        }
        if ($request->has('fire_extinguisher_name') && $request->fire_extinguisher_name) {
            $query = $query->where('fire_extinguisher_name', 'LIKE', '%' . $request->fire_extinguisher_name . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('status', decryptId($request->status));
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

    public function UniqueCheck($data)
    {

        return $this->where('fire_extinguisher_name',  $data)->get();
    }

    public function ExistuniqueCheck($data, $id)
    {
        return $this->where('fire_extinguisher_name',  $data)
            ->where('id', '!=', $id)
            ->get();
    }
    public function getTypes()
    {
        return $this->where('status', 1)->where('trash', 'NO')->get();
    }
    public function store()
    {
        $request = request();

        $insert_array = array(
            'fire_extinguisher_id' => $request->fire_extinguisher_id,
            'fire_extinguisher_name' => $request->fire_extinguisher_name,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'fire_extinguisher_id' => $request->fire_extinguisher_id,
            'fire_extinguisher_name' => $request->fire_extinguisher_name,
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
        $query = $this->select('inspection_fire_fire_extinguisher_type.*');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhere('company_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('company_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('company_id') && $request->company_id) {
            $query = $query->where('company_id', 'LIKE', '%' . $request->company_id . '%');
        }
        if ($request->has('company_name') && $request->company_name) {
            $query = $query->where('company_name', 'LIKE', '%' . $request->company_name . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select(
            'inspection_fire_fire_extinguisher_type.*'
        )
            ->where('inspection_fire_fire_extinguisher_type.id', $id)
            ->first();

        return $data;
    }
    public function getfireExtinguisher()
    {
        return FireExtinguisherType::where('trash', 'NO')->where('status', 1)->get();
    }




    public function GetApi()
    {
        $data = $this->where('status', 1)->where('trash', 'NO')->get();

        $refined_data = [];

        if (count($data) > 0) {
            foreach ($data as $index => $values) {
                $refined_data[$index] = [
                    'id' => $values->id,
                    'fire_extinguisher_name' => $values->name,
                ];
            }

            return $refined_data;
        }

        return false;
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_fire_fire_extinguisher_type'));

        static::created(function ($model) {

            $uniqueId = 'FET-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['fire_extinguisher_id' => $uniqueId]);
        });
    }
}
