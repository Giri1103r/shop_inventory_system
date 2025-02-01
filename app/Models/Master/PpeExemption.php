<?php

namespace App\Models\Master;

use App\Scopes\TrashScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PpeExemption extends Model
{
    protected $table = 'ppe_ppeexemption';
    protected $primaryKey = 'id';

    protected $fillable = [
        'emp_id',
        'emp_name',
        'request_for',
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
            ->join('masters_department', 'ppe_ppeexemption.department', '=', 'masters_department.id')
            ->join('masters_unit', 'ppe_ppeexemption.unit', '=', 'masters_unit.id')
            ->where('masters_department.trash', 'NO')
            ->where('masters_unit.trash', 'NO');

        $user = Auth::user();
        $empId = $user->employee_id;
        $userRole = $user->role;

        $userRole = string_to_array($userRole);
        if (in_array(ROLE_EHS_HEAD, $userRole)) {
            $query->whereIn('ppe_ppeexemption.approve_status', [STATUS_EHS_APPROVAL_PENDING, STATUS_EHS_APPROVED, STATUS_EHS_REJECTED]);
        } elseif (in_array(ROLE_HOD, $userRole)) {
            $departmentId = $user->department_id;
            $query->where('ppe_ppeexemption.department', $departmentId);
        } elseif (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)) {
        } elseif (in_array(ROLE_STORE_MANAGER, $userRole)) {
            $query
                  ->orderBy('ppe_ppeexemption.id', 'DESC');
        } else {
            $query->where('ppe_ppeexemption.created_by',Auth::id());
        }

        $org_total = $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];
            $query->where(function ($query) use ($search) {
                $query->orWhere('emp_name', 'LIKE', '%' . $search . '%');
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
            $toDate = $request->to_date;
            $query->where('ppe_ppeexemption.to_date', '<=', $toDate);
        }
        if ($request->has('approve_status') && $request->approve_status) {
            $approveStatus = (int) $request->approve_status;
            if ($approveStatus === (int) STATUS_USER_APPLIED) {
                $query->where('ppe_ppeexemption.approve_status', STATUS_HOD_APPROVAL_PENDING);
            } else {
                $query->where('ppe_ppeexemption.approve_status', $approveStatus);
            }
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('ppe_ppeexemption.status', decryptId($request->status));
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
            'company' => $request->company,
            'request_for'=>$request->request_for,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'approve_status' => STATUS_EHS_APPROVAL_PENDING,
            'reason' => $request->reason,
            'created_by' => Auth::id(),
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

        $user = Auth::user();

        if ($user->role == 9) {
            $employeeId = $user->employee_id;

            $laststatus = PpeExemption::where('emp_id', $employeeId)
                ->where('status', '=', 1)
                ->orderBy('id', 'DESC')
                ->first();
            return $laststatus;
        }
    }

    public function statuschange($id)
    {
        $request = request();

        $type = $request->types;
        if ($type == 1) {
            $update_data = array(
                'status' => 0,
                'updated_by'=>Auth::id(),
            );
        } else {
            $update_data = array(
                'status' => 1,
                'updated_by'=>Auth::id(),
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

    public function getExpirestatus()
    {
        $date = Carbon::now();


       $this->where('to_date', '<', $date)->update(['status' => 0]);
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
        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole )|| in_array(ROLE_EHS_HEAD, $userRole) ) {
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
        if ($request->has('approve_status') && $request->approve_status) {
            $approveStatus = (int) $request->approve_status;
            if ($approveStatus === (int) STATUS_USER_APPLIED) {
                $query->where('ppe_ppeexemption.approve_status', STATUS_HOD_APPROVAL_PENDING);
            } else {
                $query->where('ppe_ppeexemption.approve_status', $approveStatus);
            }
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('ppe_ppeexemption.status', decryptId($request->status));
        }

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
    public function getExemptionUnit($unitIds)
    {
        $unitData = $this->whereIn('unit', $unitIds->pluck('id'))
            ->join('masters_unit', 'masters_unit.id', '=', 'ppe_ppeexemption.unit')
            ->selectRaw('masters_unit.unit_name, ppe_ppeexemption.unit, COUNT(ppe_ppeexemption.id) as total')
            ->groupBy('ppe_ppeexemption.unit', 'masters_unit.unit_name')
            ->get();

        return $unitData;
    }


    public function getExemptionChartData($unitIds, $fromDate, $toDate, $unitName)
    {
        // Ensure date format conversion and handling
        if (!empty($fromDate)) {
            $fromDate = DBdateformat($fromDate);
        } else {
            $fromDate = null;
        }

        if (!empty($toDate)) {
            $toDate = DBdateformat($toDate);
        } else {
            $toDate = null;
        }




        $data = [];

        foreach ($unitIds as $unit) {
            $query = $this->newQuery();


            if ($fromDate) {
                $query = $query->where('ppe_ppeexemption.created_at', '>=', $fromDate);
            }

            if ($toDate) {
                $query = $query->where('ppe_ppeexemption.created_at', '<=', $toDate);
            }


            if ($unitName) {
                $query = $query->where('ppe_ppeexemption.unit', $unitName);
            } else {
                $query = $query->where('ppe_ppeexemption.unit', $unit->id);
            }


            // Group by approve_status and get the count
            $unitData = $query->where('unit', $unit->id)
                ->groupBy('approve_status')
                ->selectRaw('approve_status, COUNT(*) as count')
                ->get()
                ->keyBy('approve_status');

            $data[] = [
                'unit_name' => $unit->unit_name,
                'total' => $unitData->sum('count'),
                'approved' => $unitData->get('5')->count ?? 0,
                'rejected' => $unitData->get('6')->count ?? 0,
            ];
        }

        return $data;
    }

    public function statusCount($type = '', $params = [])
    {
        $query = $this->where('ppe_ppeexemption.trash', 'NO');

        if (isset($params['ppe_pperequest.from_date']) && isset($params['ppe_ppeexemption.to_date'])) {
            $query->whereBetween('ppe_ppeexemption.created_at', [DBdateformat($params['ppe_ppeexemption.from_date']), DBdateformat($params['ppe_ppeexemption.to_date'])]);
        } elseif (isset($params['from_date'])) {
            $query->where('ppe_ppeexemption.created_at', '>=', DBdateformat($params['ppe_ppeexemption.from_date']));
        } elseif (isset($params['to_date'])) {
            $query->where('ppe_ppeexemption.created_at', '<=', DBdateformat($params['ppe_ppeexemption.to_date']));
        } elseif (isset($params['unit'])) {
            $query->where('ppe_ppeexemption.unit',  $params['unit']);
        }

        if (!empty($type)) {
            if (is_array($type)) {
                $query->whereIn('ppe_ppeexemption.approve_status', $type);
            } else {
                $query->where('ppe_ppeexemption.approve_status', $type);
            }
        }

        return $query->count();
    }


    public function monthwiseexemption(){
        $request = request();


        $query = self::query();


        if ($request->Fromdate && $request->Todate) {
            $query->whereBetween('ppe_ppeexemption.from_date', [DBdateformat($request->Fromdate), DBdateformat($request->Todate)]);
        } elseif ($request->Fromdate) {
            $query->where('ppe_ppeexemption.from_date', '>=', DBdateformat($request->Fromdate));
        } elseif ($request->Todate) {
            $query->where('ppe_ppeexemption.from_date', '<=', DBdateformat($request->Todate));
        }


        $results = $query->selectRaw(
            'YEAR(from_date) as year,
             MONTH(from_date) as month,
             COUNT(*) as total_count,
             SUM(CASE WHEN approve_status = 4 THEN 1 ELSE 0 END) as pending_count,
             SUM(CASE WHEN approve_status = 6 THEN 1 ELSE 0 END) as rejected_count,
             SUM(CASE WHEN approve_status = 5 THEN 1 ELSE 0 END) as completed_count'
        )
            ->groupBy('year', 'month')
            ->orderByRaw('year ASC, month ASC')
            ->get();

        return $results;
    }

}
