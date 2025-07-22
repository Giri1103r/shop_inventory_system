<?php

namespace App\Models\Master;

use App\Models\User;
use App\Scopes\TrashScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

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
        'location_id',
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

        $today = now()->toDateString(); // Gets current date as 'YYYY-MM-DD'

        $this->whereRaw("STR_TO_DATE(to_date, '%d-%m-%Y') < ?", [$today])
            ->where('status', 1)
            ->where('trash', 'NO')
            ->update(['status' => 0]);


        $search = '';
        $query = $this->select('ppe_ppeexemption.*');

        $user = Auth::user();
        $empId = $user->employee_id;
        $userRole = $user->role;

        $userRole = string_to_array($userRole);
        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole) || CheckUserRole(ROLE_DASHBOARD_VIEWER)) {
        } else if (in_array(ROLE_EHS_HEAD, $userRole)) {
            $query->whereIn('ppe_ppeexemption.approve_status', [STATUS_EHS_APPROVAL_PENDING, STATUS_EHS_APPROVED, STATUS_EHS_REJECTED]);
        } elseif (in_array(ROLE_HOD, $userRole)) {
            $departmentId = $user->department_id;
            $query->where('ppe_ppeexemption.department', $departmentId);
        } elseif (in_array(ROLE_EHS_OFFICER, $userRole)) {
            $query
                ->orderBy('ppe_ppeexemption.id', 'DESC');
        } elseif (in_array(ROLE_STORE_MANAGER, $userRole)) {
            $query
                ->orderBy('ppe_ppeexemption.id', 'DESC');
        } else {
            $query->where('ppe_ppeexemption.created_by', Auth::id());
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

        if ($request->has('unit_id') && $request->unit_id) {
            $query->where('ppe_ppeexemption.unit',  decryptId($request->unit_id));
        }
        if ($request->has('company_id') && $request->company_id) {

            $query->where('ppe_ppeexemption.company',  decryptId($request->company_id));
        }
        if ($request->has('location_id') && $request->location_id) {
            $query->where('ppe_ppeexemption.location_id',  decryptId($request->location_id));
        }
        if ($request->has('department_id') && $request->department_id) {
            $query->where('ppe_ppeexemption.department',  decryptId($request->department_id));
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

        if ($request->has('fromDate') && !empty($request->fromDate)) {
            $datepickersearch = DBdateformat($request->fromDate);

            $query->where(function ($query) use ($datepickersearch) {
                $query->whereDate('ppe_ppeexemption.created_at', '>=', $datepickersearch);
            });
        }

        if ($request->has('company_name') && $request->company_name) {

            $query->where('ppe_ppeexemption.company', decryptId($request->company_name));
        }
        if ($request->has('toDate') && !empty($request->toDate)) {
            $enddatepickersearch = DBdateformat($request->toDate);

            $query->where(function ($query) use ($enddatepickersearch) {
                $query->whereDate('ppe_ppeexemption.created_at', '<=', $enddatepickersearch);
            });
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('ppe_ppeexemption.status', decryptId($request->status));
        }

        $data_count = $query->count();
        $total_records = $data_count;

        $query->orderBy('ppe_ppeexemption.id', 'DESC');

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

        if ($request->request_for == 1) {

            $employee = User::where('employee_id', $request->emp_id)
                ->select('*')
                ->first();

            $unit = $employee->unit_id;
            $department = $employee->department_id;

            $company = $employee->company_id;
            $location = $employee->location_id;
        } elseif ($request->request_for == 2) {
            $work = Work::where('emp_id', $request->emp_id)
                ->select('*')
                ->first();

            $unit = $work->unit;
            $department = $work->department;
            $company = $work->company;
            $location = $work->location;
        } else {
            $unit = Auth::user()->unit_id;
            $department = Auth::user()->department_id;
            $location = Auth::user()->location_id;
            $company = Auth::user()->company_id;
        }

        $insert_array = [
            'emp_id' => $request->emp_id,
            'emp_name' => $request->emp_name,
            'department' => $department,
            'unit' => $unit,
            'company' => $company,
            'location_id' => $location,
            'request_for' => $request->request_for,
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
            'company' => $request->company,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'reason' => $request->reason,
            'approve_status' => STATUS_EHS_APPROVAL_PENDING,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),

        ];
        return $this->where('id', $id)->update($update_array);
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
                'updated_by' => Auth::id(),
            );
        } else {
            $update_data = array(
                'status' => 1,
                'updated_by' => Auth::id(),
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
    public function updateapproval_api($updateData, $id)
    {
        return $this->where('id', $id)->update($updateData);
    }

    // public function getExpirestatus()
    // {
    //     $date = Carbon::now();


    //     $this->where('to_date', '>', $date)->update(['status' => 0]);
    // }



    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('ppe_ppeexemption.*');

        $user = Auth::user();
        $empId = $user->employee_id;
        $userRole = $user->role;

        $userRole = string_to_array($userRole);
        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole) || in_array(ROLE_EHS_HEAD, $userRole)) {
        } elseif (in_array(ROLE_HOD, $userRole)) {
            $departmentId = $user->department_id;
            $query->where('ppe_ppeexemption.department', $departmentId);
        } else {
            $query->where('ppe_ppeexemption.emp_id', $empId);
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
        if ($request->has('unit_id') && $request->unit_id) {
            $query->where('ppe_ppeexemption.unit',  decryptId($request->unit_id));
        }
        if ($request->has('company_id') && $request->company_id) {

            $query->where('ppe_ppeexemption.company',  decryptId($request->company_id));
        }
        if ($request->has('location_id') && $request->location_id) {
            $query->where('ppe_ppeexemption.location_id',  decryptId($request->location_id));
        }
        if ($request->has('department_id') && $request->department_id) {
            $query->where('ppe_ppeexemption.department',  decryptId($request->department_id));
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

        return  $query->orderBy('id', 'DESC')->get();
    }

    public function findDepartment($department, $id)
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


    public function monthwiseexemption()
    {
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

    public function getTotalRecords()
    {
        $request = request();

        $query = $this->where('ppe_ppeexemption.trash', 'No');

        if ($request->has('CompanyId') && $request->CompanyId) {
            $query->where('ppe_ppeexemption.company', decryptId($request->CompanyId));
        }

        if ($request->has('Fromdate') && $request->Fromdate) {
            $query->where('ppe_ppeexemption.created_at', '>=', DBdateformat($request->Fromdate));
        }

        if ($request->has('Todate') && $request->Todate) {
            $query->where('ppe_ppeexemption.created_at', '<=', DBdateformat($request->Todate));
        }

        return $query->count();
    }
}
