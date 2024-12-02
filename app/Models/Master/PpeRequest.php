<?php

namespace App\Models\Master;

use App\Models\User;
use App\Scopes\TrashScope;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PpeRequest extends Model
{
    protected $table = 'ppe_pperequest';
    protected $primaryKey = 'id';

    protected $fillable = [
        'emp_id',
        'emp_name',
        'ppe_name',
        'department',
        'ppe_type',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('ppe_pperequest.*');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('ppe_name', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('ppe_name') && $request->ppe_name) {
            $query = $query->where('ppe_name', 'LIKE', '%' . $request->ppe_name . '%');
        }
        if ($request->has('ppe_type') && $request->ppe_type) {
            $query = $query->where('ppe_type', 'LIKE', '%' . $request->ppe_type . '%');
        }
        if ($request->has('ppe_status') && $request->ppe_status) {

            $query = $query->where('status', decryptId($request->ppe_status));
        }

        $data_count = $query->count();
        $total_records = $data_count;

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
            'emp_id' => $request->emp_id,
            'emp_name' => $request->emp_name,
            'department' => $request->department,
            'ppe_type' => $request->ppe_type,
            'ppe_name' => $request->ppe_name,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
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

    public function updates($id)
    {
        $request = request();

        $update_array = array(
            'emp_id' => $request->emp_id,
            'emp_name' => $request->emp_name,
            'department' => $request->department,
            'ppe_type' => $request->ppe_type,
            'ppe_name' => $request->ppe_name,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id()
        );
        return $this->where('id', $id)->update($update_array);
    }

    public function updateapproval($updateData, $id)
    {
        return $this->where('id', $id)->update($updateData);
    }

    public function findDepartment($id)
    {
        return $this->where('id', $id)->select('department')->first();
    }

    public function getrequestemail($empId)
    {
        return User::where('employee_id', $empId)->pluck('email')->first();
    }

    public function getdepartmenthod($departmentId)
    {

        $hod =User::where('department_id', $departmentId)
            ->whereRaw('FIND_IN_SET(?, role)', [4])
            ->pluck('email')
            ->first();
        return $hod;
    }

    public function getstoremanager()
    {
        return User::select('email')
            ->whereRaw('FIND_IN_SET(?, role)', [5])
            ->pluck('email')
            ->first();
    }


    public function selectOne($id)
    {

        $data = $this->select('ppe_pperequest.*')
            ->where('ppe_pperequest.id', $id)
            ->first();

        return $data;
    }

    public function lastPpeRequest()
    {
        $employeeId = Auth::user()->employee_id;
        $departmentId = Auth::user()->department_id;
        $lastyear = PpeRequest::where('department', $departmentId)
            ->where('emp_id', $employeeId)
            ->orderBy('id', 'DESC')
            ->first();
        return $lastyear;
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('ppe_pperequest.*');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhereRaw('ppe_pperequest.emp_id LIKE "%' . $search . '%"');
            });
        }

        if ($request->has('ppe_type') && $request->ppe_type) {
            $query = $query->where('ppe_pperequest.ppe_type', 'LIKE', '%' . $request->ppe_type . '%');
        }
        if ($request->has('ppe_name') && $request->ppe_name) {
            $query = $query->where('ppe_pperequest.ppe_name', 'LIKE', '%' . $request->ppe_name . '%');
        }
        if ($request->has('ppe_status') && $request->ppe_status) {

            $query = $query->where('ppe_pperequest.status', decryptId($request->ppe_status));
        }

        return  $query->orderBy('id', 'DESC')->get();
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ppe_pperequest'));
    }
}
