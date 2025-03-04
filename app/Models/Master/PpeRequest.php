<?php

namespace App\Models\Master;

use App\Models\User;
use App\Scopes\TrashScope;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PpeRequest extends Model
{
    protected $table = 'ppe_pperequest';
    protected $primaryKey = 'id';

    protected $fillable = [
        'emp_id',
        'emp_name',
        'ppe_name',
        'department',
        'request_for',
        'unit_id',
        'item_code',
        'ppe_type',
        'approve_status',
        'employee_reason',
        'ppe_image',
        'employee_remarks',
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
        $user = Auth::user();
        $userRole = string_to_array($user->role);
        $empId = $user->employee_id;
        $query = $this->select('ppe_pperequest.*','ppe_pperequest.created_at as ppe_created_at','masters_department.department_name',  'inventory2.*', 'ppe_pperequest.id As ppe_request_id')
            ->join('masters_department', 'ppe_pperequest.department', '=', 'masters_department.id')

            ->join('ppe_stock_inventory as inventory2', 'ppe_pperequest.item_code', '=', 'inventory2.id')

            ->where('inventory2.trash', 'NO')
            ->where('masters_department.trash', 'NO')
            ->where('ppe_pperequest.trash', 'NO');


        if (in_array(ROLE_EHS_OFFICER, $userRole)) {
            $query->orderBy('ppe_pperequest.id', 'DESC');
        } elseif (in_array(ROLE_HOD, $userRole)) {
            $departmentId = $user->department_id;
            $query->where('ppe_pperequest.department', $departmentId)
                ->orderBy('ppe_pperequest.id', 'DESC');
        } elseif (in_array(ROLE_STORE_MANAGER, $userRole)) {
            $query->orderBy('ppe_pperequest.id', 'DESC');
        } elseif (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)) {
        } else {
            $query->where('ppe_pperequest.created_by',Auth::id());
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

        if ($request->has('approve_status') && $request->approve_status) {
            $approveStatus = (int) $request->approve_status;
            if ($approveStatus === (int) STATUS_HOD_APPROVED) {
                $query->where('ppe_pperequest.approve_status', STATUS_EHS_APPROVAL_PENDING);
            } elseif ($approveStatus === (int) STATUS_USER_APPLIED) {
                $query->where('ppe_pperequest.approve_status', STATUS_HOD_APPROVAL_PENDING);
            } else {
                $query->where('ppe_pperequest.approve_status', $approveStatus);
            }
        }

        if ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ppe_pperequest.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ppe_pperequest.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ppe_pperequest.created_at', [$startDate, $endDate]);
        }

        $org_total_counts = $query->count();

        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }
        $query->orderBy('ppe_pperequest.id', 'DESC');
        $data = $query->get();
        $total_records = $data->count();

        return [
            'data' => $data,
            'total_records' => $org_total_counts,
            'filter_records' => $total_records,
        ];
    }





    public function store()
    {
        $request = request();

        $destinationPath = 'uploads/ppe_files';

        if (!File::exists(public_path($destinationPath))) {
            File::makeDirectory(public_path($destinationPath), 0777, true, true);
        }

        $ppe_file_path = null;

        if ($request->hasFile('ppe_file')) {
            $ppe_file = $request->file('ppe_file');

            $ppe_file_name = time() . '_' . $ppe_file->getClientOriginalName();
            $ppe_file->move(public_path($destinationPath), $ppe_file_name);

            $ppe_file_path = $destinationPath . '/' . $ppe_file_name;
        }
        // dd($request->all());
        
        $insert_array = array(
            'emp_id' => $request->emp_id,
            'emp_name' => $request->emp_name,
            'department' => Auth::user()->department_id,
            'unit_id' => Auth::user()->unit_id,
            'request_for' => $request->request_for,
            'item_code' => $request->item_code,
            'ppe_type' => $request->ppe_type_id,
            'ppe_name' => $request->ppe_name,
            'employee_reason' => $request->reason,
            'ppe_image' => $ppe_file_path,
            'employee_remarks' => $request->remarks,
            'approve_status' => STATUS_HOD_APPROVAL_PENDING,
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

    public function updates($id)
    {
        $request = request();

        $update_array = array(
            'emp_id' => $request->emp_id,
            'emp_name' => $request->emp_name,
            'department' => Auth::user()->department_id,
            'item_code' => $request->item_code,
            'ppe_type' => $request->ppe_type,
            'ppe_name' => $request->ppe_name,
            'employee_reason' => $request->reason,
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

    public function updatestoremanager($storeStatus, $id)
    {
        return $this->where('id', $id)->update($storeStatus);
    }

    public function findDepartment($id)
    {
        return $this->where('id', $id)->select('department')->first();
    }

    public function getrequestemail($empId)
    {
        return User::where('id', $empId)->pluck('email')->first();
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
        $user = Auth::user();


        if ($user->role == 9) {
            $employeeId = $user->employee_id;


            $laststatus = PpeRequest::where('emp_id', $employeeId)
                ->where('status', '=', 1)
                ->orderBy('id', 'DESC')
                ->first();

            return $laststatus;
        }
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

        $lastsixmonths = PpeRequest::where('department', CHEMICAL_DEPARTMENT)
            ->where('emp_id', $employeeId)
            ->orderBy('id', 'DESC')
            ->first();
        return $lastsixmonths;
    }


    public function userdata()
    {
        $userId = Auth::user()->employee_id;

        return PpeRequest::where('emp_id', $userId)->where('approve_status', '!=', STATUS_HOD_APPROVAL_PENDING)->where('approve_status', '!=', STATUS_EHS_APPROVAL_PENDING)->get();
    }

    public function getuserdata($empId)
    {
        return PpeRequest::where('emp_id', $empId)->orderBy('id', 'DESC')->get();
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

        $user = Auth::user();
        $empId = $user->employee_id;
        $userRole = $user->role;
        $userRole = string_to_array($userRole);

        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole) || in_array(ROLE_EHS_OFFICER, $userRole)) {
        } elseif (in_array(ROLE_HOD, $userRole)) {
            $departmentId = $user->department_id;
            $query->where('ppe_pperequest.department', $departmentId);
        } else {
            $query->where('emp_id', $empId);
        }

        if ($request->has('emp_id') && $request->emp_id) {
            $query->where('emp_id', 'LIKE', '%' . $request->emp_id . '%');
        }
        if ($request->has('emp_name') && $request->emp_name) {
            $query->where('emp_name', 'LIKE', '%' . $request->emp_name . '%');
        }

        if ($request->has('approve_status') && $request->approve_status) {
            $approveStatus = (int) $request->approve_status;
            if ($approveStatus === (int) STATUS_HOD_APPROVED) {
                $query->where('ppe_pperequest.approve_status', STATUS_EHS_APPROVAL_PENDING);
            } elseif ($approveStatus === (int) STATUS_USER_APPLIED) {
                $query->where('ppe_pperequest.approve_status', STATUS_HOD_APPROVAL_PENDING);
            } else {
                $query->where('ppe_pperequest.approve_status', $approveStatus);
            }
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

        return $query->orderBy('id', 'DESC')->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ppe_pperequest'));
    }

    public function getPperequestUnit($unitIds)
    {
        $unitData = $this->whereIn('ppe_pperequest.unit_id', $unitIds->pluck('id'))
            ->join('masters_unit', 'masters_unit.id', '=', 'ppe_pperequest.unit_id')
            ->selectRaw('masters_unit.unit_name, ppe_pperequest.unit_id, COUNT(ppe_pperequest.id) as total')
            ->groupBy('ppe_pperequest.unit_id', 'masters_unit.unit_name')
            ->get();

        return $unitData;
    }
    public function getRequestChartData($units, $fromDate, $toDate, $unitName)
    {

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


        foreach ($units as $unit) {

            $query = $this->newQuery();


            if ($fromDate) {
                $query = $query->where('ppe_pperequest.created_at', '>=', $fromDate);
            }

            if ($toDate) {
                $query = $query->where('ppe_pperequest.created_at', '<=', $toDate);
            }


            if ($unitName) {
                $query = $query->where('ppe_pperequest.unit_id', $unitName);
            } else {
                $query = $query->where('ppe_pperequest.unit_id', $unit->id);
            }


            $unitData = $query->where('ppe_pperequest.unit_id', $unit->id)
                ->groupBy('approve_status')
                ->selectRaw('approve_status, COUNT(*) as count')
                ->get()
                ->keyBy('approve_status');


            $approvedCount = $unitData->get('8')->count ?? 0;
            $rejectedCount = ($unitData->get('3')->count ?? 0) + ($unitData->get('6')->count ?? 0);


            $data[] = [
                'unit_name' => $unit->unit_name,
                'total' => $unitData->sum('count'),
                'approved' => $approvedCount,
                'rejected' => $rejectedCount,
            ];
        }

        return $data;
    }

    public function statusCount($type = '', $params = [])
    {
        $query = $this->where('ppe_pperequest.trash', 'NO');

        if (isset($params['ppe_pperequest.from_date']) && isset($params['ppe_pperequest.to_date'])) {
            $query->whereBetween('ppe_pperequest.created_at', [DBdateformat($params['ppe_pperequest.from_date']), DBdateformat($params['ppe_pperequest.to_date'])]);
        } elseif (isset($params['from_date'])) {
            $query->where('ppe_pperequest.created_at', '>=', DBdateformat($params['ppe_pperequest.from_date']));
        } elseif (isset($params['to_date'])) {
            $query->where('ppe_pperequest.created_at', '<=', DBdateformat($params['ppe_pperequest.to_date']));
        } else if (isset($params['unit'])) { // Changed to check for 'unit' directly
            $query->where('ppe_pperequest.unit_id', $params['unit']); // Corrected key
        }

        if (!empty($type)) {
            if (is_array($type)) {
                $query->whereIn('ppe_pperequest.approve_status', $type);
            } else {
                $query->where('ppe_pperequest.approve_status', $type);
            }
        }

        return $query->count();
    }


    public function monthwiserequest()
    {
        $request = request();


        $query = self::query();


        if ($request->Fromdate && $request->Todate) {
            $query->whereBetween('ppe_pperequest.from_date', [DBdateformat($request->Fromdate), DBdateformat($request->Todate)]);
        } elseif ($request->Fromdate) {
            $query->where('ppe_pperequest.from_date', '>=', DBdateformat($request->Fromdate));
        } elseif ($request->Todate) {
            $query->where('ppe_pperequest.from_date', '<=', DBdateformat($request->Todate));
        }


        $results = $query->selectRaw(
            'YEAR(from_date) as year,
             MONTH(from_date) as month,
             COUNT(*) as total_count,
             SUM(CASE WHEN approve_status IN (1,4) THEN 1 ELSE 0 END) as pending_count,
             SUM(CASE WHEN approve_status =IN (3,6)THEN 1 ELSE 0 END) as rejected_count,
             SUM(CASE WHEN approve_status = 8 THEN 1 ELSE 0 END) as completed_count'
        )
            ->groupBy('year', 'month')
            ->orderByRaw('year ASC, month ASC') // Ensure chronological order
            ->get();

        return $results;
    }
}
