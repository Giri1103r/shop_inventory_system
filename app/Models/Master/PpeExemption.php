<?php

namespace App\Models\Master;

use App\Scopes\TrashScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PpeExemption extends Model
{
    protected $table = 'ppe_ppeexemption';
    protected $primaryKey = 'id';

    protected $fillable = [
        'emp_id',
        'emp_name',
        'department',
        'unit',
        'from_date',
        'to_date',
        'company',
        'reason',
        'remarks',
        'approved_by',
        'approved_at',
        'approve_status',
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
        $query = $this->select('ppe_ppeexemption.*', 'masters_department.department_name', 'masters_unit.unit_name')
        ->join('masters_department','ppe_ppeexemption.department', '=', 'masters_department.id')
        ->join('masters_unit','ppe_ppeexemption.unit', '=', 'masters_unit.id')
        ->where('masters_department.trash','NO')
        ->where('masters_unit.trash','NO');

        $user = Auth::user();
        $empId = $user->employee_id;
        $userRole = $user->role;

        $userRole = string_to_array($userRole);
        if (in_array(ROLE_EHS_OFFICER, $userRole)) {

            $query->whereIn('ppe_ppeexemption.approve_status', [STATUS_EHS_APPROVAL_PENDING, STATUS_EHS_APPROVED, STATUS_EHS_REJECTED]);
        } elseif (in_array(ROLE_HOD, $userRole)) {
            $departmentId = $user->department_id;
            $query->where('ppe_ppeexemption.department', $departmentId);
        } elseif (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)) {

        } else {
            $query->where('ppe_ppeexemption.emp_id', $empId);
        }

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('emp_name', 'LIKE', '%' . $search . '%');
            });
        }



        if ($request->has('emp_name') && $request->emp_name) {
            $query->where('ppe_ppeexemption.emp_name', 'LIKE', '%' . $request->emp_name . '%');
        }
        if ($request->has('emp_id') && $request->emp_id) {
            $query->where('ppe_ppeexemption.emp_id', 'LIKE', '%' . $request->emp_id . '%');
        }

        if ($request->has('department') && $request->department) {
            $query->where('ppe_ppeexemption.department', 'LIKE', '%' . $request->department . '%');
        }
        if ($request->has('unit') && $request->unit) {
            $query->where('ppe_ppeexemption.unit', 'LIKE', '%' . $request->unit . '%');
        }
        if ($request->has('company') && $request->company) {
            $query->where('ppe_ppeexemption.company', 'LIKE', '%' . $request->company . '%');
        }
        if ($request->has('from_date') && !empty($request->from_date)) {
            $fromDate = $request->from_date;
            $query->where('ppe_ppeexemption.from_date', '>=', $fromDate);
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $toDate =$request->to_date;
            $query->where('ppe_ppeexemption.to_date', '<=', $toDate);
        }
        if ($request->has('status') && $request->status) {
            $query->where('ppe_ppeexemption.status', decryptId($request->status));
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
        $insert_array = [
            'emp_id' => $request->emp_id,
            'emp_name' => $request->emp_name,
            'department' => Auth::user()->department_id,
            'unit' => $request->unit,
            'company'=>$request->company,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'approve_status' => STATUS_EHS_APPROVAL_PENDING,
            'reason' => $request->reason,
            'created_by'=>Auth::id(),

        ];
        return $this->create($insert_array);
    }

    public function updates($id)
    {
        $request = request();
        $update_array = [
            'emp_id' => $request->emp_id,
            'emp_name' => $request->emp_name,
            'department' => Auth::user()->department_id,
            'unit' => $request->unit,
            'company'=>$request->company,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'reason' => $request->reason,
            'approve_status' => STATUS_EHS_APPROVAL_PENDING,
            'created_by'=>Auth::id(),
            'updated_by'=>Auth::id(),

        ];
        return $this->where('id',$id)->update($update_array);
    }

    public function laststatus()
    {
        $employeeId = Auth::user()->employee_id;
        $laststatus = PpeExemption::where('emp_id', $employeeId)
            ->orderBy('id', 'DESC')
            ->where('status', '=', 1)
            ->first();
        return $laststatus;
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

    public function selectOne($id)
    {

        $data = $this->select('ppe_ppeexemption.*')
            ->where('ppe_ppeexemption.id', $id)
            ->first();

        return $data;
    }

    public function updateapproval($updateData, $id)
    {
        return $this->where('id', $id)->update($updateData);
    }


    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('ppe_ppeexemption.*');

        $user = Auth::user();
        $empId = $user->employee_id;
        $userRole = $user->role;

        $userRole = string_to_array($userRole);
        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole) ) {
        } elseif(in_array(ROLE_HOD, $userRole)){
           $departmentId = $user->department_id;
           $query->where('ppe_ppeexemption.department',$departmentId);
        }
        else {
            $query->where('ppe_ppeexemption.emp_id', $empId);
        }

        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhereRaw('ppe_pperequest.emp_id LIKE "%' . $search . '%"');
            });
        }

        if ($request->has('emp_name') && $request->emp_name) {
            $query->where('ppe_ppeexemption.emp_name', 'LIKE', '%' . $request->emp_name . '%');
        }
        if ($request->has('emp_id') && $request->emp_id) {
            $query->where('ppe_ppeexemption.emp_id', 'LIKE', '%' . $request->emp_id . '%');
        }
        if ($request->has('department') && $request->department) {
            $query->where('ppe_ppeexemption.department', 'LIKE', '%' . $request->department . '%');
        }
        if ($request->has('unit') && $request->unit) {
            $query->where('ppe_ppeexemption.unit', 'LIKE', '%' . $request->unit . '%');
        }
        if ($request->has('company') && $request->company) {
            $query->where('ppe_ppeexemption.company', 'LIKE', '%' . $request->company . '%');
        }
        if ($request->has('from_date') && !empty($request->from_date)) {
            $fromDate = $request->from_date;
            $query->where('ppe_ppeexemption.from_date', '>=', $fromDate);
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $toDate =$request->to_date;
            $query->where('ppe_ppeexemption.to_date', '<=', $toDate);
        }
        // if ($request->has('status') && $request->status) {
        //     $query->where('ppe_ppeexemption.status', decryptId($request->status));
        // }

        return  $query->orderBy('id', 'DESC')->get();
    }

    public function findDepartment($department ,$id)
    {

        return $this->where('id', $id)->where('department', $department)->pluck('department')->first();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ppe_ppeexemption'));
    }
}
