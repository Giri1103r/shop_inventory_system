<?php

namespace App\Models\Master;

use App\Scopes\TrashScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PpeType extends Model
{
    protected $table = 'masters_ppetype';
    protected $primaryKey = 'id';

    protected $fillable = [

        'ppe_id',
        'ppe_type',
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
        $query = $this->select('masters_ppetype.*');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('ppe_type', 'LIKE', '%' . $search . '%')
                     ->orWhere('ppe_id', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('ppe_id') && $request->ppe_id) {
            $query = $query->where('ppe_id', 'LIKE', '%' . $request->ppe_id . '%');
        }
        if ($request->has('ppe_type') && $request->ppe_type) {
            $query = $query->where('ppe_type', 'LIKE', '%' . $request->ppe_type . '%');
        }
        if ($request->has('ppe_status') && $request->ppe_status) {

            $query = $query->where('status', decryptId($request->ppe_status));
        }

        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('created_at', '<=', $endDate);
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

    public function uniqueCheck($data)
    {
        return $this->where($data['param'], $data['value'])->get();
    }


    public function ExistuniqueCheck($data)
    {
        return $this->where($data['param'],  $data['value'])
            ->where('id', '!=', decryptId($data['id']))
            ->get();
    }

    public function store()
    {
        $request = request();



        $insert_array = array(

            'ppe_type' => $request->ppe_type,

            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }
    public function updates($id)
    {
        $request = request();



        $update_array = array(

            'ppe_type' => $request->ppe_type,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id()
        );
        return $this->where('id', $id)->update($update_array);
    }
    public function selectOne($id)
    {

        $data = $this->select('masters_ppetype.*')
            ->where('masters_ppetype.id', $id)
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
        $query = $this->select('masters_ppetype.*');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhereRaw('ppe_name LIKE "%' . $search . '%"');
            });
        }

        if ($request->has('ppe_id') && $request->ppe_id) {
            $query = $query->where('ppe_id', 'LIKE', '%' . $request->ppe_id . '%');
        }
        if ($request->has('ppe_type') && $request->ppe_type) {
            $query = $query->where('ppe_type', 'LIKE', '%' . $request->ppe_type . '%');
        }
        if ($request->has('ppe_status') && $request->ppe_status) {

            $query = $query->where('status', decryptId($request->ppe_status));
        }

        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('created_at', '<=', $endDate);
        }

        return  $query->orderBy('id', 'DESC')->get();
    }
    public function getPpetypedata(){
        return PpeType::where('trash','NO')->where('status','!=',0)->get();
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('masters_ppetype'));
        static::created(function ($model) {

            $uniqueId = 'PPE_TYPE-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['ppe_id' => $uniqueId]);
        });
    }
}
