<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use  HasFactory;


    protected $table = 'masters_department';
    protected $primaryKey = 'id';
    protected $fillable = [
        'department_id',
        'location_id',
        'company_id',
        'unit_id',
        'department_name',
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
        $query = $this->select('masters_department.*', 'company_management.company_name',  'masters_location.location_name', 'masters_unit.unit_name');
        $query = $query->leftJoin('company_management', 'masters_department.company_id', '=', 'company_management.id');
        $query = $query->leftJoin('masters_location', 'masters_department.location_id', '=', 'masters_location.id');
        $query = $query->leftJoin('masters_unit', 'masters_department.unit_id', '=', 'masters_unit.id');
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();
        
        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('department_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('department_id') && $request->department_id) {
            $query = $query->where('department_id', 'LIKE', '%' . $request->department_id . '%');
        }
        if ($request->has('company_id') && $request->company_id) {
            $query = $query->where('masters_department.company_id', decryptId($request->company_id));
        }
        if ($request->has('location_id') && $request->location_id) {
            $query = $query->where('masters_department.location_id', decryptId($request->location_id));
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('masters_department.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('department_name') && $request->department_name) {
            $query = $query->where('department_name', 'LIKE', '%' . $request->department_name . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('status', decryptId($request->status));
        }
        $data_count = $query->count();
        $total_records = $data_count;

        $query->orderBy('id','DESC');

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

        return $this->where($data['param'],  $data['value'])->get();
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
            'department_id' => $request->department_id,
            'location_id' => decryptId($request->location_id),
            'company_id' => decryptId($request->company_id),
            'unit_id' => decryptId($request->unit_id),
            'department_name' => $request->department_name,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'department_id' => $request->department_id,
            'location_id' => decryptId($request->location_id),
            'company_id' => decryptId($request->company_id),
            'unit_id' => decryptId($request->unit_id),
            'department_name' => $request->department_name,
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
        $query = $this->select('masters_department.*', 'company_management.company_name',  'masters_location.location_name', 'masters_unit.unit_name');
        $query = $query->leftJoin('company_management', 'masters_department.company_id', '=', 'company_management.id');
        $query = $query->leftJoin('masters_location', 'masters_department.location_id', '=', 'masters_location.id');
        $query = $query->leftJoin('masters_unit', 'masters_department.unit_id', '=', 'masters_unit.id');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhereRaw('department_name LIKE "%' . $search . '%"');
            });
        }
        if ($request->has('department_name') && $request->department_name) {
            $query = $query->where('department_name', 'LIKE', '%' . $request->department_name . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('masters_department.status', decryptId($request->status));
        }

        return  $query->get();
    }

    public function selectOne($id)
    {
    
        $data = $this->select('masters_department.*', 'company_management.company_name',  'masters_location.location_name', 'masters_unit.unit_name')->leftJoin('company_management', 'masters_department.company_id', '=', 'company_management.id')->leftJoin('masters_location', 'masters_department.location_id', '=', 'masters_location.id')->leftJoin('masters_unit', 'masters_department.unit_id', '=', 'masters_unit.id')
            ->where('masters_department.id', $id)
            ->first();

        return $data;
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('masters_department'));

        static::created(function ($model) {

            $uniqueId = 'DEP-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['department_id' => $uniqueId]);
        });
    }
}
