<?php

namespace App\Models\OhcManagement\Opd;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FirstAid extends Model
{
    protected $table = 'ohc_opd_first_aid';
    protected $primaryKey = 'id';

    protected $fillable = [
        'emp_id',
        'is_outside_employee',
        'emp_name',
        'date_of_incident',
        'time_of_incident',
        'treatment_provided',
        'medicine_id',
        'treatment_start_time',
        'treatment_end_time',
        'first_aider_name',
        'follow_up_required',
        'cheif_complaint',
        'hospital_id',
        'referred_to',
        'remarks',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('ohc_opd_first_aid.*');
        $user = Auth::user();
        $userRole = string_to_array($user->role);
        $empId = $user->employee_id;

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (!empty($request->search) && isset($request->search['value']) && $request->search['value'] !== '') {
            $search = $request->search['value'];
            $formattedDate = null;
            if (\DateTime::createFromFormat('d-m-Y', $search) !== false) {
                $formattedDate = \Carbon\Carbon::createFromFormat('d-m-Y', $search)->format('Y-m-d');
            }
            $query->where(function ($query) use ($search,  $formattedDate) {
                $query
                    ->orWhere('emp_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('first_aider_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('time_of_incident', 'LIKE', '%' . $search . '%')
                    ->orWhere('remarks', 'LIKE', '%' . $search . '%')
                    ->orWhere('emp_name', 'LIKE', '%' . $search . '%');
                if ($formattedDate) {
                    $query->orWhere('date_of_incident', 'LIKE', '%' . $formattedDate . '%');
                }
            });
        }
        if (in_array(ROLE_ADMIN, $userRole) || CheckUserRole(ROLE_DASHBOARD_VIEWER) || in_array(ROLE_SUPERADMIN, $userRole) || in_array(ROLE_EHS_HEAD, $userRole)) {
            $query->orderBy('ohc_opd_first_aid.id', 'DESC');
        } else {
            $query->where('ohc_opd_first_aid.created_by', Auth::id());
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_opd_first_aid.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_opd_first_aid.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_opd_first_aid.created_at', '<=', $endDate);
        }
        // if ($request->has('company_name') && $request->company_name) {

        //     $query->where('ohc_management_opd_patient.company_id', decryptId($request->company_name));
        // }
        if ($request->has('fromDate') && !empty($request->fromDate)) {
            $datepickersearch = DBdateformat($request->fromDate);

            $query->where(function ($query) use ($datepickersearch) {
                $query->whereDate('ohc_opd_first_aid.created_at', '>=', $datepickersearch);
            });
        }

        if ($request->has('toDate') && !empty($request->toDate)) {
            $enddatepickersearch = DBdateformat($request->toDate);

            $query->where(function ($query) use ($enddatepickersearch) {
                $query->whereDate('ohc_opd_first_aid.created_at', '<=', $enddatepickersearch);
            });
        }
        if ($request->has('emp_id') && $request->emp_id) {

            $query = $query->where('ohc_opd_first_aid.emp_id', $request->emp_id);
        }
        if ($request->has('emp_name') && $request->emp_name) {

            $query = $query->where('ohc_opd_first_aid.emp_name', $request->emp_name);
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('ohc_opd_first_aid.status', decryptId($request->status));
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

        if ($request->has('medicine_id')) {
            $decryptedRoleIds = array_map(function ($encryptedId) {
                return $encryptedId;
            }, $request->medicine_id);

            $commaSeparatedRoles = implode(',', $decryptedRoleIds);
        }
        if ($request->has('is_outside_worker') == 1) {
            $employeeId =  $request->outside_emp_id;
        } else {
            $employeeId =   $request->emp_id;
        }
        $insert_array = [
            'is_outside_employee' => $request->has('is_outside_worker') ? 1 : 0,
            'emp_id' =>  $employeeId,
            'emp_name' => $request->emp_name,
            'date_of_incident' => DBdateformat($request->date_of_incident),
            'time_of_incident' => $request->time_of_incident,
            'treatment_provided' => $request->treatment_provided,
            'treatment_start_time' => $request->treatment_start_time,
            'medicine_id' => $commaSeparatedRoles,
            'treatment_end_time' => $request->treatment_end_time,
            'first_aider_name' => $request->first_aider_name,
            'follow_up_required' => $request->follow_up,
            'referred_to' => $request->refered_to,
            'hospital_id' => decryptId($request->hospital_id),
            'cheif_complaint' => $request->cheif_complaint,
            'remarks' => $request->remarks,
            'created_by' => Auth::id(),
        ];

        return $this->create($insert_array);
    }

    public function updates($id)
    {
        $request = request();
        if ($request->has('medicine_id')) {
            $decryptedRoleIds = array_map(function ($encryptedId) {
                return $encryptedId;
            }, $request->medicine_id);

            $commaSeparatedRoles = implode(',', $decryptedRoleIds);
        }
        if ($request->has('is_outside_worker') == 1) {
            $employeeId =  $request->outside_emp_id;
        } else {
            $employeeId =   $request->emp_id;
        }
        $update_array = [
            'emp_id' =>  $employeeId,
            'is_outside_employee' => $request->has('is_outside_worker') ? 1 : 0,
            'emp_name' => $request->emp_name,
            'date_of_incident' => DBdateformat($request->date_of_incident),
            'time_of_incident' => $request->time_of_incident,
            'treatment_provided' => $request->treatment_provided,
            'treatment_start_time' => $request->treatment_start_time,
            'treatment_end_time' => $request->treatment_end_time,
            'first_aider_name' => $request->first_aider_name,
            'follow_up_required' => $request->follow_up,
            'medicine_id' => $commaSeparatedRoles,
            'referred_to' => $request->refered_to,
            'hospital_id' => decryptId($request->hospital_id),
            'cheif_complaint' => $request->cheif_complaint,
            'remarks' => $request->remarks,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),

        ];
        return $this->where('id', $id)->update($update_array);
    }

    public function Selectone($id)
    {
        $data =  $this->where('ohc_opd_first_aid.id', $id)
            ->select('ohc_opd_first_aid.*')
            ->where('ohc_opd_first_aid.trash', 'No')
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
        $search = '';
        $user = Auth::user();
        $userRole = string_to_array($user->role);
        $empId = $user->employee_id;
        $query = $this->select('ohc_opd_first_aid.*');
        if (!empty($request->search) && isset($request->search['value']) && $request->search['value'] !== '') {
            $search = $request->search['value'];
            $formattedDate = null;
            if (\DateTime::createFromFormat('d-m-Y', $search) !== false) {
                $formattedDate = \Carbon\Carbon::createFromFormat('d-m-Y', $search)->format('Y-m-d');
            }
            $query->where(function ($query) use ($search,  $formattedDate) {
                $query
                    ->orWhere('emp_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('first_aider_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('time_of_incident', 'LIKE', '%' . $search . '%')
                    ->orWhere('remarks', 'LIKE', '%' . $search . '%')
                    ->orWhere('emp_name', 'LIKE', '%' . $search . '%');
                if ($formattedDate) {
                    $query->orWhere('date_of_incident', 'LIKE', '%' . $formattedDate . '%');
                }
            });
        }
        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)) {
            $query->orderBy('ohc_opd_first_aid.id', 'DESC');
        } else {
            $query->where('ohc_opd_first_aid.created_by', Auth::id());
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_opd_first_aid.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_opd_first_aid.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_opd_first_aid.created_at', '<=', $endDate);
        }
        if ($request->has('emp_id') && $request->emp_id) {

            $query = $query->where('ohc_opd_first_aid.emp_id', $request->emp_id);
        }
        if ($request->has('emp_name') && $request->emp_name) {

            $query = $query->where('ohc_opd_first_aid.emp_name', $request->emp_name);
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('ohc_opd_first_aid.status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    public function getTotalRecords()
    {
        $request = request();

        $query = $this->where('ohc_opd_first_aid.status', 1);

        if ($request->has('CompanyId') && $request->CompanyId) {
            $query->where('ohc_opd_first_aid.company_id', decryptId($request->CompanyId));
        }

        if ($request->has('Fromdate') && $request->Fromdate) {
            $query->where('ohc_opd_first_aid.created_at', '>=', DBdateformat($request->Fromdate));
        }

        if ($request->has('Todate') && $request->Todate) {
            $query->where('ohc_opd_first_aid.created_at', '<=', DBdateformat($request->Todate));
        }

        return $query->count();
    }
}
