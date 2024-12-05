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

        $user = Auth::user();
        $empId = $user->employee_id;
        $userRole = $user->role;
        $userRole = string_to_array($userRole);

        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)) {
        } elseif (in_array(ROLE_HOD, $userRole)) {
            $departmentId = $user->department_id;
            $query->where('ppe_pperequest.department', $departmentId);
        } else {
            $query->where('emp_id', $empId);
        }

        if ($request->search['value'] != null) {
            $search = $request->search['value'];
            $query->where(function ($query) use ($search) {
                $query->orWhere('emp_name', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('emp_id') && $request->emp_id) {
            $query->where('emp_id', 'LIKE', '%' . $request->emp_id . '%');
        }
        if ($request->has('emp_name') && $request->emp_name) {
            $query->where('emp_name', 'LIKE', '%' . $request->emp_name . '%');
        }
        if ($request->has('ppe_status') && $request->ppe_status) {
            $query->where('status', decryptId($request->ppe_status));
        }

        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('created_at', '<=', $endDate);
        }


        $data_count = $query->count();
        $total_records = $data_count;

        $query->orderBy('id', 'DESC');

        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }

        $data = $query->get();

        $datas = [
            'data' => $data,
            'total_records' => $org_total_counts,
            'filter_records' => $total_records,
        ];

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


    public function updateehsapproval($updateEhsData, $id)
    {
        return $this->where('id', $id)->update($updateEhsData);
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

        $hod = User::where('department_id', $departmentId)
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

    public function laststatus()
    {
        $employeeId = Auth::user()->employee_id;
        $laststatus = PpeRequest::where('emp_id', $employeeId)
            ->orderBy('id', 'DESC')
            ->where('status', '=', 1)
            ->first();
        return $laststatus;
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

    public function lastsixmonthrequest()
    {
        $employeeId = Auth::user()->employee_id;

        $lastsixmonths = PpeRequest::where('department', 41)
            ->where('emp_id', $employeeId)
            ->orderBy('id', 'DESC')
            ->first();
        return $lastsixmonths;
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

        if ($request->has('emp_id') && $request->emp_id) {
            $query->where('emp_id', 'LIKE', '%' . $request->emp_id . '%');
        }
        if ($request->has('emp_name') && $request->emp_name) {
            $query->where('emp_name', 'LIKE', '%' . $request->emp_name . '%');
        }
        if ($request->has('ppe_status') && $request->ppe_status) {
            $query->where('status', decryptId($request->ppe_status));
        }

        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('created_at', '<=', $endDate);
        }

        return  $query->orderBy('id', 'DESC')->get();
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ppe_pperequest'));
    }
}
