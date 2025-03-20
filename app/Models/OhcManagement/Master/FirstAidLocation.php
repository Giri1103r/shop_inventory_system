<?php

namespace App\Models\OhcManagement\Master;


use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FirstAidLocation extends Model
{
    use  HasFactory;


    protected $table = 'ohc_master_first_aid_location';
    protected $primaryKey = 'id';

    protected $fillable = [
        'unit_id',
        'department_id',
        'location_id',
        'station_master',
        'station_number',
        'first_aid_box_no',
        'status',
        'trash',
        'created_by',
        'updated_by',
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
        $query = $this->select('ohc_master_first_aid_location.*', 'masters_department.department_name', 'masters_unit.unit_name')
        ->join('masters_department', 'ohc_master_first_aid_location.department_id', '=', 'masters_department.id')
        ->join('masters_unit', 'ohc_master_first_aid_location.unit_id', '=', 'masters_unit.id')
        ->where('masters_department.trash', 'NO')
        ->where('masters_unit.trash', 'NO');

        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('ohc_master_first_aid_location.department_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('ohc_master_first_aid_location.unit_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('ohc_master_first_aid_location.status', 'LIKE', '%' . $search . '%')
                    ->orWhere('ohc_master_first_aid_location.location_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('station_number', 'LIKE', '%' . $search . '%');

            });
        }



        if ($request->has('status') && $request->status) {

            $query = $query->where('ohc_master_first_aid_location.status', decryptId($request->status));
        }
        if ($request->has('unit_id') && $request->unit_id) {

            $query = $query->where('ohc_master_first_aid_location.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('department_id') && $request->department_id) {

            $query = $query->where('ohc_master_first_aid_location.department_id', decryptId($request->department_id));
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_master_first_aid_location.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_master_first_aid_location.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_master_first_aid_location.created_at', '<=', $endDate);
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

    public function uniqueCheck($location_id)
    {

        return $this->where('location_id', $location_id)->get();
    }

    public function existUniqueCheck($location_id,$id)
    {
        return $this->where('location_id', $location_id)
            ->where('id', '!=', $id)
            ->get();
    }

    public function stationnumberuniqueCheck($station_number)
    {

        return $this->where('station_number', $station_number)->get();
    }

    public function stationnumberexistUniqueCheck($station_number,$id)
    {
        return $this->where('first_aid_box_no', $station_number)
            ->where('id', '!=', $id)
            ->get();
    }

    public function firstaidboxuniqueCheck($first_aid_box_no, $unit_id,$department)
    {

        return $this->where('first_aid_box_no', $first_aid_box_no)
        ->where('unit_id', $unit_id)
        ->where('department_id', $department)
        ->get();
    }

    public function firstaidboxexistUniqueCheck($first_aid_box_no, $unit_id,$department,$id)
    { return $this->where('first_aid_box_no', $first_aid_box_no)
        ->where('unit_id', $unit_id)
        ->where('department_id', $department)
        ->where('id', '!=', $id)
        ->get();
    }
   

    public function store()
    {
        $request = request();
    //    dd( $request->all());
        $insert_array = array(
            'unit_id'         =>decryptId($request-> unit_id),
            'department_id'   =>decryptId($request->department_id) ,
            'location_id'     =>$request-> location_id,
            'station_master' =>$request->station_master,
            'station_number' =>$request->station_number ,
            'first_aid_box_no' =>$request->first_aid_box_no ,

            'created_by'=>Auth::id(),

        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'unit_id'         =>decryptId($request-> unit_id),
            'department_id'   =>decryptId($request->department_id) ,
            'location_id'     =>$request-> location_id,
            'station_master' =>$request->station_master,
            'station_number' =>$request->station_number ,
            'first_aid_box_no' =>$request->first_aid_box_no ,
            'created_by'=>Auth::id(),
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
        $query = $this->select('.*');
        $query = $this->select('ohc_master_first_aid_location.*');


        if (!empty($request->search) && isset($request->search['value']) && $request->search['value'] !== '')  {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('ohc_master_first_aid_location.department_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('ohc_master_first_aid_location.unit_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('ohc_master_first_aid_location.status', 'LIKE', '%' . $search . '%')
                    ->orWhere('ohc_master_first_aid_location.location_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('station_number', 'LIKE', '%' . $search . '%');

            });
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('ohc_master_first_aid_location.status', decryptId($request->status));
        }
        if ($request->has('unit_id') && $request->unit_id) {

            $query = $query->where('ohc_master_first_aid_location.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('department_id') && $request->department_id) {

            $query = $query->where('ohc_master_first_aid_location.department_id', decryptId($request->department_id));
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_master_first_aid_location.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_master_first_aid_location.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_master_first_aid_location.created_at', '<=', $endDate);
        }

        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select(
            'ohc_master_first_aid_location.*'
        )
            ->where('ohc_master_first_aid_location.id', $id)
            ->first();

        return $data;
    }

    public function getFirsaid()
    {

        $data = $this->where('status',1)

            ->first();

        return $data;
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ohc_master_first_aid_location'));

        static::created(function ($model) {

            $uniqueId = 'CMP-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['company_id' => $uniqueId]);
        });
    }
}
