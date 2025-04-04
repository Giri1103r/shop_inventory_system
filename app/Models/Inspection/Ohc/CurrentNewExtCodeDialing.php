<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CurrentNewExtCodeDialing extends Model
{
    protected $table = 'inspection_ohc_current_new_ext_code_dailing';

    protected $primaryKey = 'id';

    protected $fillable = [
        'unit_id',
        'department_id',
        'emp_name_id',
        'number',
        'created_by',
        'updated_by',
        'status',
        'trash',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];


    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_ohc_current_new_ext_code_dailing.*', 'masters_department.department_name','masters_unit.unit_name','masters_employee.emp_name')
                        ->leftJoin('masters_department', 'masters_department.id', '=', 'inspection_ohc_current_new_ext_code_dailing.department_id')
                        ->leftJoin('masters_unit', 'masters_unit.id', '=', 'inspection_ohc_current_new_ext_code_dailing.unit_id')
                        ->leftJoin('masters_employee', 'masters_employee.id', '=', 'inspection_ohc_current_new_ext_code_dailing.emp_name_id')
                        ->where('inspection_ohc_current_new_ext_code_dailing.trash', 'NO');

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('masters_department.department_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_employee.emp_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_ohc_current_new_ext_code_dailing.number', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('inspection_ohc_current_new_ext_code_dailing.unit_id', 'LIKE', '%' . decryptId($request->unit_id) . '%');
        }

        if ($request->has('department_id') && $request->department_id) {
            $query = $query->where('inspection_ohc_current_new_ext_code_dailing.department_id', 'LIKE', '%' . decryptId($request->department_id) . '%');
        }
        
        if ($request->has('emp_name_id') && $request->emp_name_id) {
            $query = $query->where('inspection_ohc_current_new_ext_code_dailing.emp_name_id', 'LIKE', '%' . decryptId($request->emp_name_id) . '%');
        }

        if ($request->has('number') && $request->number) {
            $query = $query->where('inspection_ohc_current_new_ext_code_dailing.number', 'LIKE', '%' . $request->number . '%');
        }
       
        if ($request->has('status') && $request->status) {

            $query = $query->where('inspection_ohc_current_new_ext_code_dailing.status', decryptId($request->status));
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
        $unit = $request->unit_id;
        $department = $request->department_id;
        $emp_id = $request->emp_name;
        $number = $request->number;
        
        foreach ($unit as $index => $unit) {
            $data = array(
                'unit_id' => decryptId($unit),
                'department_id' => decryptId($department[$index]),
                'emp_name_id' => decryptId($emp_id[$index]),
                'number' => $number[$index],
                'created_by' => Auth::id(),
            );
            
            $this->create($data);
        }
    }

    public function selectOne($id){
        return $this->select('inspection_ohc_current_new_ext_code_dailing.*', 'masters_department.department_name','masters_unit.unit_name','masters_employee.emp_name')
                        ->leftJoin('masters_department', 'masters_department.id', '=', 'inspection_ohc_current_new_ext_code_dailing.department_id')
                        ->leftJoin('masters_unit', 'masters_unit.id', '=', 'inspection_ohc_current_new_ext_code_dailing.unit_id')
                        ->leftJoin('masters_employee', 'masters_employee.id', '=', 'inspection_ohc_current_new_ext_code_dailing.emp_name_id')->where('inspection_ohc_current_new_ext_code_dailing.id',$id)->first();
        
    }

    public function updates($id)
    {
        $request = request();
        $update_array = array(
            'unit_id' => decryptId($request->unit_id),
            'department_id' => decryptId($request->department_id),
            'emp_name_id' => decryptId($request->emp_name),
            'number' => $request->number,
            'updated_by' => Auth::id()
        );
        return $this->where('id', $id)->update($update_array);
    }


    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_ohc_current_new_ext_code_dailing.*', 'masters_department.department_name','masters_unit.unit_name','masters_employee.emp_name')
        ->leftJoin('masters_department', 'masters_department.id', '=', 'inspection_ohc_current_new_ext_code_dailing.department_id')
        ->leftJoin('masters_unit', 'masters_unit.id', '=', 'inspection_ohc_current_new_ext_code_dailing.unit_id')
        ->leftJoin('masters_employee', 'masters_employee.id', '=', 'inspection_ohc_current_new_ext_code_dailing.emp_name_id');

        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('masters_department.department_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_employee.emp_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_ohc_current_new_ext_code_dailing.number', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('inspection_ohc_current_new_ext_code_dailing.unit_id', 'LIKE', '%' . decryptId($request->unit_id) . '%');
        }

        if ($request->has('department_id') && $request->department_id) {
            $query = $query->where('inspection_ohc_current_new_ext_code_dailing.department_id', 'LIKE', '%' . decryptId($request->department_id) . '%');
        }
        
        if ($request->has('emp_name_id') && $request->emp_name_id) {
            $query = $query->where('inspection_ohc_current_new_ext_code_dailing.emp_name_id', 'LIKE', '%' . decryptId($request->emp_name_id) . '%');
        }

        if ($request->has('number') && $request->number) {

            $query = $query->where('inspection_ohc_current_new_ext_code_dailing.number', 'LIKE', '%' . $request->number . '%');

        }
       
        if ($request->has('status') && $request->status) {

            $query = $query->where('inspection_ohc_current_new_ext_code_dailing.status', decryptId($request->status));
        }

        $query->orderBy('id', 'DESC');
        return  $query->get();
    }

    public function UniqueCheck($unit_id,$department_id,$emp_name_id,$number)
    {
        return $this->where('unit_id', $unit_id)->where('department_id', $department_id)->where('emp_name_id', $emp_name_id)->where('number', $number)->get();
    }

    public function ExistuniqueCheck($unit_id,$department_id,$emp_name_id,$number,$id)
    {
        return $this->where('unit_id', $unit_id)->where('department_id', $department_id)->where('emp_name_id', $emp_name_id)->where('number', $number)
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

    public function deleterecord($id)
    {

        $update_data = array(
            'status' => 0,
            'trash' => 'YES',
        );

        return $this->where('id', $id)->update($update_data);
    }
}
