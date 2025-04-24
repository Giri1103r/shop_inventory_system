<?php

namespace App\Models\Inspection\Ohc;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use App\Scopes\TrashScope;

use Illuminate\Database\Eloquent\Model;

class SafetyPettyDetails extends Model
{
    use  HasFactory;

    protected $table = 'inspection_ohc_safety_petty_logbook';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'document_reference_id',
        'serial_number',
        'employee_name',
        'employee_code',
        'department',
        'unit',
        'date',
        'amount',
        'description',
        'amount_given_by',
        'amount_received_by',
        'remark',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];

    public function list()
    {
        $request = request();
        $search = '';

        $query = $this->select(
                'inspection_ohc_safety_petty_logbook.*',
                'masters_employee.emp_name',
                'masters_unit.unit_name',
                'masters_department.department_name',
                'inspection_ohc_safety_petty_logbook.id as safety_petty_id'
            )
            ->leftJoin('masters_employee', 'masters_employee.login_id', '=', 'inspection_ohc_safety_petty_logbook.employee_name')
            ->leftJoin('masters_unit', 'masters_unit.id', '=', 'inspection_ohc_safety_petty_logbook.unit')
            ->leftJoin('masters_department', 'masters_department.id', '=', 'inspection_ohc_safety_petty_logbook.department');

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('masters_employee.emp_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_employee.emp_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_department.department_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_ohc_safety_petty_logbook.employee_code', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('inspection_ohc_safety_petty_logbook.unit', 'LIKE', '%' . decryptId($request->unit_id) . '%');
        }
        if ($request->has('department_id') && $request->department_id) {
            $query = $query->where('inspection_ohc_safety_petty_logbook.department', 'LIKE', '%' . decryptId($request->department_id) . '%');
        }

        if ($request->has('emp_id') && $request->emp_id) {
            $query = $query->where('inspection_ohc_safety_petty_logbook.employee_name', 'LIKE', '%' . $request->emp_id . '%');
        }

        if ($request->has('employee_code') && $request->employee_code) {
            $query = $query->where('inspection_ohc_safety_petty_logbook.employee_code', 'LIKE', '%' . $request->employee_code . '%');
        }

        $data_count = $query;
        $total_records = $data_count->count();

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "created_by":
                    $query = $query->orderBy('inspection_ohc_safety_petty_logbook.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_ohc_safety_petty_logbook.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_ohc_safety_petty_logbook.id', 'DESC');
                    break;
            }
        }

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
        $insertedData = [];

        foreach ($request->amount as $index => $amount) {
            $insert_array = array(
                'document_reference_id' => decryptId($request->document_reference_id),
                'serial_number' =>$request->serial_number[$index],
                'employee_name' => $request->emp_id[$index],
                'employee_code' => $request->employee_code[$index],
                'department' => decryptId($request->department_id[$index]),
                'unit' => decryptId($request->unit_id[$index]),
                'date' => DBdateformat($request->date[$index]),
                'amount' => $request->amount[$index],
                'description' => $request->description[$index],
                'amount_given_by' => $amount,
                'amount_received_by' => $request->amnt_receivedby_id[$index],
                'remark' => $request->remark[$index],
                'created_by' => Auth::id(),
            );

            $insertedData[]=  $this->create($insert_array);

        }

        return $insertedData;
    }

    public function UniqueCheck($data)
    {
        return $this->where('employee_code',  $data)->get();
    }

    public function ExistuniqueCheck($data, $id)
    {
        return $this->where('employee_code',  $data)
            ->where('id', '!=', $id)
            ->get();
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

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select(
                    'inspection_ohc_safety_petty_logbook.*',
                    'masters_employee.emp_name',
                    'masters_unit.unit_name',
                    'masters_department.department_name',
                    'inspection_ohc_safety_petty_logbook.id as safety_petty_id'
                )
                ->leftJoin('masters_employee', 'masters_employee.login_id', '=', 'inspection_ohc_safety_petty_logbook.employee_name')
                ->leftJoin('masters_unit', 'masters_unit.id', '=', 'inspection_ohc_safety_petty_logbook.unit')
                ->leftJoin('masters_department', 'masters_department.id', '=', 'inspection_ohc_safety_petty_logbook.department');

        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query
                    ->orWhere('masters_employee.emp_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_employee.emp_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_department.department_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_ohc_safety_petty_logbook.employee_code', 'LIKE', '%' . $search . '%');

            });
        }

        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('inspection_ohc_safety_petty_logbook.unit', 'LIKE', '%' . decryptId($request->unit_id) . '%');
        }
        if ($request->has('department_id') && $request->department_id) {
            $query = $query->where('inspection_ohc_safety_petty_logbook.department', 'LIKE', '%' . decryptId($request->department_id) . '%');
        }

        if ($request->has('emp_id') && $request->emp_id) {
            $query = $query->where('inspection_ohc_safety_petty_logbook.employee_name', 'LIKE', '%' . $request->emp_id . '%');
        }

        if ($request->has('employee_code') && $request->employee_code) {
            $query = $query->where('inspection_ohc_safety_petty_logbook.employee_code', 'LIKE', '%' . $request->employee_code . '%');
        }
        $query->orderBy('inspection_ohc_safety_petty_logbook.id', 'DESC');

        return $query->get();
    }

    public function selectOne($id)
    {
        return $this->where('id', $id)->where('status', 1)->where('trash', 'NO')->first();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_ohc_safety_petty_logbook'));
    }

}
