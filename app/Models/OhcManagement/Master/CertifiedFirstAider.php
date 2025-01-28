<?php

namespace App\Models\OhcManagement\Master;


use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CertifiedFirstAider extends Model
{
    use  HasFactory;


    protected $table = 'ohc_master_certified_first_aider';
    protected $primaryKey = 'id';

    protected $fillable = [
        'unit_id',
        'department_id',
        'emp_id',
        'certifier_name',
        'mobile_no',
        'address',
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
        $query = $this->select('ohc_master_certified_first_aider.*', 'masters_department.department_name', 'masters_unit.unit_name')
        ->join('masters_department', 'ohc_master_certified_first_aider.department_id', '=', 'masters_department.id')
        ->join('masters_unit', 'ohc_master_certified_first_aider.unit_id', '=', 'masters_unit.id')
        ->where('masters_department.trash', 'NO')
        ->where('masters_unit.trash', 'NO');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('company_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('company_name', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('ohc_master_certified_first_aider.status', decryptId($request->status));
        }
        if ($request->has('unit_id') && $request->unit_id) {

            $query = $query->where('ohc_master_certified_first_aider.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('department_id') && $request->department_id) {

            $query = $query->where('ohc_master_certified_first_aider.department_id', decryptId($request->department_id));
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_master_certified_first_aider.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_master_certified_first_aider.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_master_certified_first_aider.created_at', '<=', $endDate);
        }
        if ($request->has('certifier_name') && $request->certifier_name) {

            $query = $query->where('ohc_master_certified_first_aider.certifier_name', $request->certifier_name);
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

    public function uniqueCheck($emp_id, $mobile_no)
    {
        return $this->where('emp_id', $emp_id)
                    ->orWhere('mobile_no', $mobile_no)
                    ->exists();
    }

    public function existUniqueCheck($emp_id, $mobile_no, $id)
    {
        return $this->where(function ($query) use ($emp_id, $mobile_no) {
                        $query->where('emp_id', $emp_id)
                              ->orWhere('mobile_no', $mobile_no);
                    })
                    ->where('id', '!=', $id)
                    ->exists();
    }

    public function store()
    {
        $request = request();

        $insert_array = array(
            'unit_id'         =>decryptId($request-> unit_id),
            'department_id'   =>decryptId($request->department_id) ,
            'emp_id'     =>$request-> emp_id,
            'certifier_name' =>$request->certifier_name,
            'mobile_no' =>$request->mobile_no ,
            'address'=>$request->address,
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
            'emp_id'     =>$request-> emp_id,
            'certifier_name' =>$request->certifier_name,
            'mobile_no' =>$request->mobile_no ,
            'address'=>$request->address,
            'updated_by'=>Auth::id(),
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
        $query = $this->select('ohc_master_certified_first_aider.*');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhere('unit_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('department_id', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('ohc_master_certified_first_aider.status', decryptId($request->status));
        }
        if ($request->has('unit_id') && $request->unit_id) {

            $query = $query->where('ohc_master_certified_first_aider.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('department_id') && $request->department_id) {

            $query = $query->where('ohc_master_certified_first_aider.department_id', decryptId($request->department_id));
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_master_certified_first_aider.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_master_certified_first_aider.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_master_certified_first_aider.created_at', '<=', $endDate);
        }
        if ($request->has('certifier_name') && $request->certifier_name) {

            $query = $query->where('ohc_master_certified_first_aider.certifier_name', $request->certifier_name);
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select(
            'ohc_master_certified_first_aider.*'
        )
            ->where('ohc_master_certified_first_aider.id', $id)
            ->first();

        return $data;
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ohc_master_certified_first_aider'));

        static::created(function ($model) {

            $uniqueId = 'CMP-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['company_id' => $uniqueId]);
        });
    }
}
