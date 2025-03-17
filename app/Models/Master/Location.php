<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use  HasFactory;


    protected $table = 'masters_location';
    protected $primaryKey = 'id';
    protected $fillable = [
        'location_id',
        'company_id',
        'location_name',
        'status',
        'trash',
        'created_by',
        'updated_by',
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
        $query = $this->select('masters_location.*', 'company_management.company_name');
        $query = $query->leftJoin('company_management', 'masters_location.company_id', '=', 'company_management.id');
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('location_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('location_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('company_management.company_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('location_id') && $request->location_id) {
            $query = $query->where('location_id', 'LIKE', '%' . $request->location_id . '%');
        }
        if ($request->has('company_id') && $request->company_id) {
            $query = $query->where('masters_location.company_id', decryptId($request->company_id));
        }
        if ($request->has('location_name') && $request->location_name) {
            $query = $query->where('location_name', 'LIKE', '%' . $request->location_name . '%');
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('masters_location.status', decryptId($request->status));
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

    public function UniqueCheck($location_name, $company_id)
    {

        return $this->where('location_name',  $location_name)->where('company_id', $company_id)->get();
    }

    public function ExistuniqueCheck($location_name, $company_id, $id)
    {
        return $this->where('location_name',  $location_name)->where('company_id', $company_id)
            ->where('id', '!=', $id)
            ->get();
    }

    public function store()
    {
        $request = request();

        $insert_array = array(
            'location_id' => $request->location_id,
            'company_id' => decryptId($request->company_id),
            'location_name' => $request->location_name,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'location_id' => $request->location_id,
            'company_id' => decryptId($request->company_id),
            'location_name' => $request->location_name,
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
        $query = $this->select('masters_location.*', 'company_management.company_name');
        $query = $query->leftJoin('company_management', 'masters_location.company_id', '=', 'company_management.id');

        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('location_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('location_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('company_management.company_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('location_id') && $request->location_id) {
            $query = $query->where('location_id', 'LIKE', '%' . $request->location_id . '%');
        }
        if ($request->has('company_id') && $request->company_id) {
            $query = $query->where('masters_location.company_id', decryptId($request->company_id));
        }
        if ($request->has('location_name') && $request->location_name) {
            $query = $query->where('location_name', 'LIKE', '%' . $request->location_name . '%');
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('masters_location.status', decryptId($request->status));
        }

        $query->orderBy('id', 'DESC');

        return  $query->get();
    }


    public function selectOne($id)
    {

        $data = $this->select('masters_location.*', 'company_management.company_name')->leftJoin('company_management', 'masters_location.company_id', '=', 'company_management.id')
            ->where('masters_location.id', $id)
            ->first();

        return $data;
    }

    public function ajaxList($locationId , $companyId = '')
    {
        $query = $this->select('id', 'location_name')->where('status', 1);

        if ($companyId != '') {
            $query->where('company_id', $companyId);
        }
        if (!empty($companyId) && !empty($locationId)) {
            $query = $query->where('company_id', $companyId)->where('status', 1)->orWhere(function ($query) use ($locationId, $companyId) {
                $query->where('company_id', $companyId)->where('id', $locationId);
            });
        }
        $datas = $query->get();

        $list = [];
        foreach ($datas as $data) {
            $listvalue = [];
            $listvalue['id'] = encryptId($data->id);
            $listvalue['name'] = $data->location_name;
            $list[] = $listvalue;
        }

        return $list;
    }

    public function ajaxallList($companyId = '')
    {
        $query = $this->select('id', 'location_name')->where('status', 1);

        if ($companyId != '') {

            $query = $query->where('company_id', $companyId);
        }

        $datas = $query->get();

        $list = [];
        foreach ($datas as $data) {
            $listvalue = [];
            $listvalue['id'] = encryptId($data->id);
            $listvalue['name'] = $data->location_name;
            $list[] = $listvalue;
        }
        return $list;
    }

    public function getLocation(){
        return $this->where('status',1)->get();
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('masters_location'));

        static::created(function ($model) {

            $uniqueId = 'LOC-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['location_id' => $uniqueId]);
        });
    }

}
