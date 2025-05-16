<?php

namespace App\Models\KPI;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class HSCInputs extends Model
{
    protected $table = 'kpi_hsc_inputs';
    protected $fillable = [
        'id',
        'company_id',
        'location_id',
        'unit_id',
        'department_id',
        'financial_year',
        'calendar_year',
        'month',
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
        $query = $this->select('kpi_hsc_inputs.*', 'masters_location.location_name as location_name', 'masters_unit.unit_name as unit_name', 'company_management.company_name as company_name', 'masters_department.department_name as department_name')
            ->leftjoin('masters_location', 'kpi_hsc_inputs.location_id', '=', 'masters_location.id')
            ->leftjoin('masters_unit', 'kpi_hsc_inputs.unit_id', '=', 'masters_unit.id')
            ->leftjoin('company_management', 'kpi_hsc_inputs.company_id', '=', 'company_management.id')
            ->leftjoin('masters_department', 'kpi_hsc_inputs.department_id', '=', 'masters_department.id');

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('company_management.company_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_location.location_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_department.department_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('company_id') && $request->company_id) {
            $query = $query->where('kpi_hsc_inputs.company_id', decryptId($request->company_id));
        }
        if ($request->has('location_id') && $request->location_id) {
            $query = $query->where('kpi_hsc_inputs.location_id', decryptId($request->location_id));
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('kpi_hsc_inputs.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('department_id') && $request->department_id) {
            $query = $query->where('kpi_hsc_inputs.department_id', decryptId($request->department_id));
        }
        if ($request->has('month') && $request->month) {
            $query = $query->where('kpi_hsc_inputs.month', ($request->month));
        }
        if ($request->has('year') && $request->year) {
            $query = $query->where('kpi_hsc_inputs.year', ($request->year));
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('kpi_hsc_inputs.status', decryptId($request->status));
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

    public function UniqueCheck($company_id, $location_id, $unit_id, $department_id, $year, $month)
    {

        return $this->where('company_id',  $company_id)->where('location_id', $location_id)->where('unit_id', $unit_id)->where('department_id', $department_id)->where('calendar_year', $year)->where('month', $month)->get();
    }

    public function ExistuniqueCheck($company_id, $location_id, $unit_id, $department_id, $year, $month, $id)
    {
        return $this->where('company_id',  $company_id)->where('location_id', $location_id)->where('unit_id', $unit_id)->where('department_id', $department_id)->where('calendar_year', $year)->where('month', $month)
            ->where('id', '!=', $id)
            ->get();
    }

    public function store()
    {
        $request = request();
        $financialStartYear = explode('-', $request->financial_year)[0];
        $insert_array = array(
            'company_id' => decryptId($request->company_id),
            'location_id' => decryptId($request->location_id),
            'unit_id' => decryptId($request->unit_id),
            'department_id' => decryptId($request->department_id),
            'calendar_year' => $request->year,
            'month' => $request->month,
            'financial_year'  => $financialStartYear,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'company_id' => decryptId($request->company_id),
            'location_id' => decryptId($request->location_id),
            'unit_id' => decryptId($request->unit_id),
            'department_id' => decryptId($request->department_id),
            'calendar_year' => $request->year,
            'month' => $request->month,
            'financial_year' => $request->financial_year,
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
        $query = $this->select('kpi_hsc_inputs.*', 'masters_location.location_name as location_name', 'masters_unit.unit_name as unit_name', 'company_management.company_name as company_name', 'masters_department.department_name as department_name')
            ->leftjoin('masters_location', 'kpi_hsc_inputs.location_id', '=', 'masters_location.id')
            ->leftjoin('masters_unit', 'kpi_hsc_inputs.unit_id', '=', 'masters_unit.id')
            ->leftjoin('company_management', 'kpi_hsc_inputs.company_id', '=', 'company_management.id')
            ->leftjoin('masters_department', 'kpi_hsc_inputs.department_id', '=', 'masters_department.id');

        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('company_management.company_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_location.location_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_department.department_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('company_id') && $request->company_id) {
            $query = $query->where('kpi_hsc_inputs.company_id', decryptId($request->company_id));
        }
        if ($request->has('location_id') && $request->location_id) {
            $query = $query->where('kpi_hsc_inputs.location_id', decryptId($request->location_id));
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('kpi_hsc_inputs.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('department_id') && $request->department_id) {
            $query = $query->where('kpi_hsc_inputs.department_id', decryptId($request->department_id));
        }
        if ($request->has('month') && $request->month) {
            $query = $query->where('kpi_hsc_inputs.month', ($request->month));
        }
        if ($request->has('year') && $request->year) {
            $query = $query->where('kpi_hsc_inputs.year', ($request->year));
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('kpi_hsc_inputs.status', decryptId($request->status));
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
        static::addGlobalScope(new TrashScope('kpi_hsc_inputs'));
    }
}
