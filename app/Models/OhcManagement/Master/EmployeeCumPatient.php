<?php

namespace App\Models\OhcManagement\Master;


use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeCumPatient extends Model
{
    use  HasFactory;


    protected $table = 'ohc_master_employee_cum_patient';
    protected $primaryKey = 'id';

    protected $fillable = [
        'is_outside_worker',
        'emp_name',
        'address',
        'dob',
        'emp_id',
        'employee_type',
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
        $query = $this->select('ohc_master_employee_cum_patient.*', 'ohc_master_employee_cum_patient_employee_type.employee_type_name')
            ->join('ohc_master_employee_cum_patient_employee_type', 'ohc_master_employee_cum_patient.employee_type', '=', 'ohc_master_employee_cum_patient_employee_type.id')
            ->where('ohc_master_employee_cum_patient_employee_type.trash', 'NO');

        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('emp_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('employee_type', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('emp_name') && $request->emp_name) {
            $query = $query->where('ohc_master_employee_cum_patient.emp_name', 'LIKE', '%' . $request->emp_name . '%');
        }

        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_master_employee_cum_patient.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_master_employee_cum_patient.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_master_employee_cum_patient.created_at', '<=', $endDate);
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('ohc_master_employee_cum_patient.status', decryptId($request->status));
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

    public function uniqueCheck($emp_id, $emp_name)
    {
        return $this->where('emp_id', $emp_id)
                    ->orWhere('emp_name', $emp_name)
                    ->exists();
    }

    public function existUniqueCheck($emp_id, $emp_name, $id)
    {
        return $this->where(function ($query) use ($emp_id, $emp_name) {
                        $query->where('emp_id', $emp_id)
                              ->orWhere('emp_name', $emp_name);
                    })
                    ->where('id', '!=', $id)
                    ->exists();
    }

    public function store()
    {
        $request = request();

        $insert_array = array(
            'is_outside_worker' => $request->has('is_outside_worker') ? 1 : 0,
            'emp_name' => $request->emp_name,
            'address' => $request->address,
            'dob' => $request->dob,
            'emp_id' => $request->emp_id,
            'employee_type' => $request->employee_type,
            'created_by' => Auth::id(),
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'is_outside_worker' => $request->has('is_outside_worker') ? 1 : 0,
            'emp_name' => $request->emp_name,
            'address' => $request->address,
            'dob' => $request->dob,
            'emp_id' => $request->emp_id,
            'employee_type' => $request->employee_type,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
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
        $query = $this->select('ohc_master_employee_cum_patient.*');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhere('company_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('company_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('emp_name') && $request->emp_name) {
            $query = $query->where('ohc_master_employee_cum_patient.emp_name', 'LIKE', '%' . $request->emp_name . '%');
        }

        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_master_employee_cum_patient.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_master_employee_cum_patient.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_master_employee_cum_patient.created_at', '<=', $endDate);
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('ohc_master_employee_cum_patient.status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select(
            'ohc_master_employee_cum_patient.*'
        )
            ->where('ohc_master_employee_cum_patient.id', $id)
            ->first();

        return $data;
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ohc_master_employee_cum_patient'));

        static::created(function ($model) {

            $uniqueId = 'CMP-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['company_id' => $uniqueId]);
        });
    }
}
