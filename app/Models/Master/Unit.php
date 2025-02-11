<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use  HasFactory;


    protected $table = 'masters_unit';
    protected $primaryKey = 'id';
    protected $fillable = [
        'unit_id',
        'location_id',
        'company_id',
        'unit_name',
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
        $query = $this->select('masters_unit.*', 'company_management.company_name',  'masters_location.location_name');
        $query = $query->leftJoin('company_management', 'masters_unit.company_id', '=', 'company_management.id');
        $query = $query->leftJoin('masters_location', 'masters_unit.location_id', '=', 'masters_location.id');
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('unit_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('company_management.company_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_location.location_name', 'LIKE', '%' . $search . '%');
            });
        }


        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('unit_id', 'LIKE', '%' . $request->unit_id . '%');
        }
        if ($request->has('company_id') && $request->company_id) {
            $query = $query->where('masters_unit.company_id', decryptId($request->company_id));
        }
        if ($request->has('location_id') && $request->location_id) {
            $query = $query->where('masters_unit.location_id', decryptId($request->location_id));
        }
        if ($request->has('unit_name') && $request->unit_name) {
            $query = $query->where('masters_unit.unit_name', 'LIKE', '%' . $request->unit_name . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('masters_unit.status', decryptId($request->status));
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


    public function UniqueCheck($unit_name, $location_id, $company_id)
    {

        return $this->where('unit_name',  $unit_name)->where('location_id', $location_id)->where('company_id', $company_id)->get();
    }

    public function ExistuniqueCheck($unit_name, $location_id, $company_id, $id)
    {
        return $this->where('unit_name',  $unit_name)->where('location_id', $location_id)->where('company_id', $company_id)
            ->where('id', '!=', $id)
            ->get();
    }
    public function store()
    {
        $request = request();

        $insert_array = array(
            'unit_id' => $request->unit_id,
            'location_id' => decryptId($request->location_id),
            'company_id' => decryptId($request->company_id),
            'unit_name' => $request->unit_name,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'unit_id' => $request->unit_id,
            'location_id' => decryptId($request->location_id),
            'company_id' => decryptId($request->company_id),
            'unit_name' => $request->unit_name,
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
        $query = $this->select('masters_unit.*', 'company_management.company_name',  'masters_location.location_name');
        $query = $query->leftJoin('company_management', 'masters_unit.company_id', '=', 'company_management.id');
        $query = $query->leftJoin('masters_location', 'masters_unit.location_id', '=', 'masters_location.id');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('unit_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('company_management.company_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_location.location_name', 'LIKE', '%' . $search . '%');
            });
        }


        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('unit_id', 'LIKE', '%' . $request->unit_id . '%');
        }
        if ($request->has('company_id') && $request->company_id) {
            $query = $query->where('masters_unit.company_id', decryptId($request->company_id));
        }
        if ($request->has('location_id') && $request->location_id) {
            $query = $query->where('masters_unit.location_id', decryptId($request->location_id));
        }
        if ($request->has('unit_name') && $request->unit_name) {
            $query = $query->where('masters_unit.unit_name', 'LIKE', '%' . $request->unit_name . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('masters_unit.status', decryptId($request->status));
        }

        $query->orderBy('id', 'DESC');
        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select('masters_unit.*', 'company_management.company_name',  'masters_location.location_name')->leftJoin('company_management', 'masters_unit.company_id', '=', 'company_management.id')->leftJoin('masters_location', 'masters_unit.location_id', '=', 'masters_location.id')
            ->where('masters_unit.id', $id)
            ->first();

        return $data;
    }


    public function ajaxList($unit_id , $locationId = '')
    {
        $query = $this->select('id', 'unit_name')->where('status', 1);

        if ($locationId != '') {
            $query->where('location_id', $locationId);
        }
        if (!empty($locationId) && !empty($unit_id)) {
            $query = $query->where('location_id', $locationId)->where('status', 1)->orWhere(function ($query) use ($unit_id, $locationId) {
                $query->where('location_id', $locationId)->where('id', $unit_id);
            });
        }
        $datas = $query->get();

        $list = [];
        foreach ($datas as $data) {
            $listvalue = [];
            $listvalue['id'] = encryptId($data->id);
            $listvalue['name'] = $data->unit_name;
            $list[] = $listvalue;
        }

        return $list;
    }
    public function ajaxallList($locationId = '')
    {
        $query = $this->select('id', 'unit_name')->where('status', 1);

        if ($locationId != '') {

            $query = $query->where('location_id', $locationId);
        }

        $datas = $query->get();

        $list = [];
        foreach ($datas as $data) {
            $listvalue = [];
            $listvalue['id'] = encryptId($data->id);
            $listvalue['name'] = $data->unit_name;
            $list[] = $listvalue;
        }
        return $list;
    }
    public function getunit(){
        return Unit::where('trash','NO')->where('status','!=',0)->get();
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('masters_unit'));

        static::created(function ($model) {

            $uniqueId = 'UNIT-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['unit_id' => $uniqueId]);
        });
    }

    public function getuserunit(){
        $id =Auth::user()->unit_id;
        return Unit::where('trash','NO')->where('id','!=', $id  )->where('status','!=',0)->get();
    }
}
