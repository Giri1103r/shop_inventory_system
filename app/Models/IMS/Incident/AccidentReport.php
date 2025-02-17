<?php

namespace App\Models\IMS\Incident;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccidentReport extends Model
{
    use  HasFactory;


    protected $table = 'ims_initial_accident_report';
    protected $primaryKey = 'id';

    protected $fillable = [
        'accident_report_no',
        'date_and_time',
        'unit_id',
        'shift',
        'location_id',
        'exact_location',
        'designation',
        'department_id',
        'emp_code',
        'address_of_the_injuredperson',
        'accident_status',
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
        $query = $this->select('ims_initial_accident_report.*', 'masters_unit.unit_name', 'masters_employee.emp_id', 'masters_department.department_name',  'masters_location.location_name');
        $query = $query->leftJoin('masters_unit', 'ims_initial_accident_report.unit_id', '=', 'masters_unit.id');
        $query = $query->leftJoin('masters_employee', 'ims_initial_accident_report.emp_code', '=', 'masters_employee.emp_id');
        $query = $query->leftJoin('masters_department', 'ims_initial_accident_report.department_id', '=', 'masters_department.id');
        $query = $query->leftJoin('masters_location', 'ims_initial_accident_report.location_id', '=', 'masters_location.id');
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('accident_report_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_employee.emp_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_department.department_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_location.location_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('accident_report_no') && $request->accident_report_no) {
            $query = $query->where('accident_report_no',  $request->accident_report_no);
        }
        if ($request->has('emp_code') && $request->emp_code) {
            $query = $query->where('ims_initial_accident_report.emp_code', decryptId($request->emp_code));
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('ims_initial_accident_report.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('department_id') && $request->department_id) {
            $query = $query->where('ims_initial_accident_report.department_id', decryptId($request->department_id));
        }
        if ($request->has('location_id') && $request->location_id) {
            $query = $query->where('ims_initial_accident_report.location_id', decryptId($request->location_id));
        }
        if ($request->has('from_date') && $request->from_date) {
            $fromDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay();
            $query = $query->where('ims_initial_accident_report.created_at', '>=', $fromDate);
        }

        if ($request->has('to_date') && $request->to_date) {
            $toDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay();
            $query = $query->where('ims_initial_accident_report.created_at', '<=', $toDate);
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('ims_initial_accident_report.status', decryptId($request->status));
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


    public function store()
    {
        $request = request();

        $insert_array = array(
            'accident_report_no' => $request->accident_report_no,
            'date_and_time' => DBdatetimeformat($request->date_and_time),
            'unit_id' =>  decryptId($request->unit_id),
            'location_id' =>  decryptId($request->location_id),
            'department_id' =>  decryptId($request->department_id),
            'shift' => $request->shift,
            'exact_location' => $request->exact_location,
            'designation' => $request->designation,
            'emp_code' => $request->emp_code,
            'accident_status' => 1,
            'address_of_the_injuredperson' => $request->address_of_the_injuredperson,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();
        $update_array = array(
            'accident_report_no' => $request->accident_report_no,
            'date_and_time' => DBdatetimeformat($request->date_and_time),
            'unit_id' =>  decryptId($request->unit_id),
            'location_id' =>  decryptId($request->location_id),
            'department_id' =>  decryptId($request->department_id),
            'shift' => $request->shift,
            'exact_location' => $request->exact_location,
            'designation' => $request->designation,
            'emp_code' => $request->emp_code,
            'address_of_the_injuredperson' => $request->address_of_the_injuredperson,
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
        $query = $this->select('ims_initial_accident_report.*', 'masters_unit.unit_name', 'masters_employee.emp_id', 'masters_department.department_name',  'masters_location.location_name');
        $query = $query->leftJoin('masters_unit', 'ims_initial_accident_report.unit_id', '=', 'masters_unit.id');
        $query = $query->leftJoin('masters_employee', 'ims_initial_accident_report.emp_code', '=', 'masters_employee.emp_id');
        $query = $query->leftJoin('masters_department', 'ims_initial_accident_report.department_id', '=', 'masters_department.id');
        $query = $query->leftJoin('masters_location', 'ims_initial_accident_report.location_id', '=', 'masters_location.id');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('accident_report_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_employee.emp_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_department.department_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_location.location_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('accident_report_no') && $request->accident_report_no) {
            $query = $query->where('accident_report_no',  $request->accident_report_no);
        }
        if ($request->has('emp_code') && $request->emp_code) {
            $query = $query->where('ims_initial_accident_report.emp_code', decryptId($request->emp_code));
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('ims_initial_accident_report.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('department_id') && $request->department_id) {
            $query = $query->where('ims_initial_accident_report.department_id', decryptId($request->department_id));
        }
        if ($request->has('location_id') && $request->location_id) {
            $query = $query->where('ims_initial_accident_report.location_id', decryptId($request->location_id));
        }
        if ($request->has('from_date') && $request->from_date) {
            $fromDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay();
            $query = $query->where('ims_initial_accident_report.created_at', '>=', $fromDate);
        }

        if ($request->has('to_date') && $request->to_date) {
            $toDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay();
            $query = $query->where('ims_initial_accident_report.created_at', '<=', $toDate);
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('ims_initial_accident_report.status', decryptId($request->status));
        }

        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select('ims_initial_accident_report.*', 'masters_unit.unit_name', 'masters_employee.emp_id', 'masters_department.department_name',  'masters_location.location_name')->leftJoin('masters_unit', 'ims_initial_accident_report.unit_id', '=', 'masters_unit.id')
            ->leftJoin('masters_employee', 'ims_initial_accident_report.emp_code', '=', 'masters_employee.emp_id')
            ->leftJoin('masters_department', 'ims_initial_accident_report.department_id', '=', 'masters_department.id')
            ->leftJoin('masters_location', 'ims_initial_accident_report.location_id', '=', 'masters_location.id')
            ->where('ims_initial_accident_report.id', $id)
            ->first();

        return $data;
    }


    public function updateStatus($accidentReportId, $accident_status)
    {
        $request = request();

        $update_array = array(
            'accident_status' => $accident_status,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        );
        return $this->where('id', $accidentReportId)->update($update_array);
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ims_initial_accident_report'));

        static::created(function ($model) {

            $uniqueId = 'ACCIDENT-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['accident_report_no' => $uniqueId]);
        });
    }
}
