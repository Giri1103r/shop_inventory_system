<?php

namespace App\Models\KPI;

use App\Scopes\TrashScope;
use Google\Rpc\Context\AttributeContext\Request;
use Illuminate\Support\Facades\DB;
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
            $query = $query->where('kpi_hsc_inputs.calendar_year', ($request->year));
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

    public function LaggingLine()
    {
        $request = request();

        $query = DB::table('kpi_hsc_inputs as hsc')
            ->select(
                'hsc.*',
                'lagging.*',
                'master.*',
                'lagging.value as lagging_value',
                'master.value as lagging_label',
                'hsc.created_at as created_at',
                DB::raw('YEAR(hsc.created_at) as created_year'),
                DB::raw('(SELECT COUNT(*) FROM kpi_hsc_inputs_lagging WHERE lagging_id = lagging.lagging_id) as lagging_count')
            )
            ->leftJoin('kpi_hsc_inputs_lagging as lagging', 'hsc.id', '=', 'lagging.hsc_inputs_id')
            ->leftJoin('kpi_master_leading_lagging as master', 'lagging.lagging_id', '=', 'master.id')
            ->where('master.status', 1)
            ->where('master.trash', 'NO');


        if ($request->CompanyId != null) {
            $company_id = decryptId($request->CompanyId);
            $query->where('ims_initial_incident.company_id', $company_id);
        }

        // Date Filters
        if ($request->Fromdate && $request->Todate) {
            $query->whereBetween('ims_initial_incident.created_at', [
                DBdateformat($request->Fromdate),
                DBdateformat($request->Todate) . ' 23:59:59'
            ]);
        } elseif ($request->Fromdate) {
            $query->where('ims_initial_incident.created_at', '>=', DBdateformat($request->Fromdate));
        } elseif ($request->Todate) {
            $query->where('ims_initial_incident.created_at', '<=', DBdateformat($request->Todate) . ' 23:59:59');
        }

        $results = $query->get();

        if (count($results) > 0) {
            $dataByYear = [];

            foreach ($results as $data) {
                $year = $data->calendar_year;
                $label = $data->lagging_label;
                $value = $data->lagging_value;

                if (!isset($dataByYear[$label])) {
                    $dataByYear[$label] = [];
                }

                if (!isset($dataByYear[$label][$year])) {
                    $dataByYear[$label][$year] = 0;
                }

                $dataByYear[$label][$year] += $value;
            }

            return $dataByYear;
        }

        return false;
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('kpi_hsc_inputs'));
    }
}
