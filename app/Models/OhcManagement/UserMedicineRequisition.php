<?php

namespace App\Models\OhcManagement;

use App\Http\Controllers\Master\DepartmentController;
use App\Models\Master\Department;
use App\Models\Master\Unit;
use App\Scopes\TrashScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserMedicineRequisition extends Model
{
    protected $table = 'ohc_management_user_medicine_requisition';
    protected $primaryKey = 'id';

    protected $fillable = [
        'req_id',
        'unit_id',
        'department_id',
        'request_date',
        'status',
        'approve_status',
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
        $query = $this->select('ohc_management_user_medicine_requisition.*',);


        if ($request->search['value'] != null) {
            $search = $request->search['value'];


            $formattedDate = null;
            if (\DateTime::createFromFormat('d-m-Y', $search)) {
                $formattedDate = \Carbon\Carbon::createFromFormat('d-m-Y', $search)->format('Y-m-d');
            }

            $query->where(function ($query) use ($search, $formattedDate) {
                $query->orWhere('req_id', 'LIKE', '%' . $search . '%');

                if ($formattedDate) {
                    $query->orWhere('request_date', 'LIKE', '%' . $formattedDate . '%');
                }
            });
        }



        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)) {
        } elseif (in_array(ROLE_EHS_HEAD, $userRole)) {
        } else {
            $query->where('ohc_management_user_medicine_requisition.created_by', Auth::id());
        }



        if ($request->has('req_id') && $request->req_id) {

            $query = $query->where('ohc_management_user_medicine_requisition.req_id', $request->req_id);
        }
        if ($request->has('unit_id') && $request->unit_id) {

            $query = $query->where('ohc_management_user_medicine_requisition.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('request_date') && $request->request_date) {

            $query = $query->where('ohc_management_user_medicine_requisition.request_date', DBdateformat($request->request_date));
        }
        if ($request->has('department_id') && $request->department_id) {

            $query = $query->where('ohc_management_user_medicine_requisition.department_id', decryptId($request->department_id));
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_management_user_medicine_requisition.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_management_user_medicine_requisition.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_management_user_medicine_requisition.created_at', '<=', $endDate);
        }
        if ($request->filled('status')) {
            $query->where('ohc_management_user_medicine_requisition.approve_status',  $request->status . '%');
        }

        $totalFilteredRecords = $query->count();
        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }

        $query->orderBy('id', 'DESC');
        $data = $query->get();


        $org_total_counts = $this->count();

        return [
            'data' => $data,
            'total_records' => $org_total_counts,
            'filter_records' => $totalFilteredRecords,
        ];
    }

    public function store()
    {
        $request = request();

        $insert_array = [
            'unit_id' => Auth::user()->unit_id,
            'department_id' => Auth::user()->department_id,
            'request_date' => DBdateformat($request->request_date),
            'req_id' => $request->req_id,
            'approve_status' => STATUS_OHC_EHS_HEAD_APPROVAL_PENDING,
            'created_by' => Auth::id(),
        ];

        return $this->create($insert_array);
    }
    public function updates($id)
    {

        $request = request();
        $unit = Unit::where('unit_name', $request->unit_id)->where('status', 1)->first();
        $department = Department::where('unit_id', $unit->id)->where('department_name', $request->department_id)->where('status', 1)->first();

        $update_array = array(
            'unit_id' => $unit->id ?? null,
            'department_id' => $department->id ?? null,
            'request_date' => DBdateformat($request->request_date),
            'req_id' => $request->req_id,
            'updated_by' => Auth::id(),
        );

        return $this->where('id', $id)->update($update_array);
    }

    public function selectOne($id)
    {

        $data = $this->select(
            'ohc_management_user_medicine_requisition.*'
        )
            ->where('ohc_management_user_medicine_requisition.id', $id)
            ->first();

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

        return $this->where('id', $id)->update($update_data);
    }

    public function exportdata()
    {
        $request = request();

        $user = Auth::user();
        $userRole = string_to_array($user->role);
        $empId = $user->employee_id;
        $search = '';
        $query = $this->select('ohc_management_user_medicine_requisition.*');

        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)) {
        } elseif (in_array(ROLE_PARAMEDICS, $userRole)) {
        } else {
            $query->where('ohc_management_user_medicine_requisition.created_by', Auth::id());
        }

        if (!empty($request->search) && isset($request->search['value']) && $request->search['value'] !== '') {
            $search = $request->search['value'];


            $formattedDate = null;
            if (\DateTime::createFromFormat('d-m-Y', $search)) {
                $formattedDate = \Carbon\Carbon::createFromFormat('d-m-Y', $search)->format('Y-m-d');
            }

            $query->where(function ($query) use ($search, $formattedDate) {
                $query->orWhere('req_id', 'LIKE', '%' . $search . '%');

                if ($formattedDate) {
                    $query->orWhere('request_date', 'LIKE', '%' . $formattedDate . '%');
                }
            });
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('ohc_management_user_medicine_requisition.approve_status', ($request->status));
        }
        if ($request->has('req_id') && $request->req_id) {

            $query = $query->where('ohc_management_user_medicine_requisition.req_id', $request->req_id);
        }
        if ($request->has('unit_id') && $request->unit_id) {

            $query = $query->where('ohc_management_user_medicine_requisition.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('request_date') && $request->request_date) {

            $query = $query->where('ohc_management_user_medicine_requisition.request_date', DBdateformat($request->request_date));
        }
        if ($request->has('department_id') && $request->department_id) {

            $query = $query->where('ohc_management_user_medicine_requisition.department_id', decryptId($request->department_id));
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_management_user_medicine_requisition.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_management_user_medicine_requisition.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_management_user_medicine_requisition.created_at', '<=', $endDate);
        }


        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ohc_management_user_medicine_requisition'));

        static::created(function ($model) {

            $uniqueId = 'REQ-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['req_id' => $uniqueId]);
        });
    }

    public function approvereject($id, $data)
    {
        return $this->where('id', $id)->update([
            'approve_status' => $data['approve_status'],
            'approved_by' => Auth::id(),
        ]);
    }
    // status closed

    public function updatestatus($id)
    {
        return $this->where('id', $id)->update(['approve_status' => STATUS_OHC_CLOSE]);
    }

    // sending the data to email


}
