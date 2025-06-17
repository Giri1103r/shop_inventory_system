<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class CertifiedFireFighter extends Model
{

    protected $table = 'inspection_fire_certified_fire_fighter';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'doc_no_id',
        'fire_id',
        'sr_no',
        'emp_name',
        'department_id',
        'unit_id',
        'emp_code',
        'emp_phone',
        'emp_status',
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

    public function store($fireId)
    {
        $request = request();

        $sr_no = $request->sr_no;
        $unit_id = $request->unit_id;
        $department_id = $request->department_id;
        $emp_name = $request->emp_name;
        $emp_code = $request->emp_code;
        $emp_phone = $request->emp_phone;
        $emp_status = $request->emp_status;

        foreach ($sr_no as $index => $srNo) {
            $data = [
                'doc_no_id' => decryptId($request->docNo_id),
                'fire_id' => $fireId,
                'sr_no' => $srNo,
                'unit_id' => decryptId($unit_id[$index]),
                'department_id' => decryptId($department_id[$index]),
                'emp_name' => decryptId($emp_name[$index]),
                'emp_code' => $emp_code[$index],
                'emp_phone' => $emp_phone[$index],
                'emp_status' => decryptId($emp_status[$index]),
                'created_by' => Auth::id(),
            ];

            $this->create($data);
        }
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_fire_certified_fire_fighter.*', 'masters_department.department_name',  'inspection_fire_table.fire_no');
        $query = $query->leftJoin('inspection_fire_table', 'inspection_fire_certified_fire_fighter.fire_id', '=', 'inspection_fire_table.id');
        $query = $query->leftJoin('masters_department', 'inspection_fire_certified_fire_fighter.department_id', '=', 'masters_department.id');

        $query->orderBy('id', 'DESC');
        return  $query->get();
    }

    public function selectOne($fireId)
    {
        $data = $this->select('inspection_fire_certified_fire_fighter.*', 'masters_department.department_name', 'inspection_fire_table.fire_no')
            ->leftJoin('masters_department', 'inspection_fire_certified_fire_fighter.department_id', '=', 'masters_department.id')->leftJoin('inspection_fire_table', 'inspection_fire_certified_fire_fighter.fire_id', '=', 'inspection_fire_table.id')
            ->where('inspection_fire_certified_fire_fighter.fire_id', $fireId)->where('inspection_fire_certified_fire_fighter.status', 1)
            ->get();
        return $data;
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

        return $this->where('fire_id', $id)->update($update_data);
    }

    public function selectFireId($id)
    {
        return $this->where('fire_id', $id)->get();
    }
}
