<?php

namespace App\Models\OhcManagement\Opd;

use App\Models\Master\Department;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class PrescribetoPatient extends Model
{
    protected $table = 'ohc_management_opd_patient';
    protected $primaryKey = 'id';
    protected $fillable = [
        'is_outside_employee',
        'unit_id',
        'department_id',
        'company_name',
        'emp_id',
        'emp_name',
        'mobile_no',
        'time',
        'dob',
        'emergency_contact',
        'address',
        'date',
        'vital_checkup',
        'suggested_by',
        'suggested_details',
        'cheif_complaint',
        'first_aid_treatment',
        'treatment',
        'gender',
        'is_refered',
        'patient_status',
        'fitness_certificate',
        'closed_description',
        'cancel_remarks',
        'file_upload',
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
        $query = $this->select(
            'ohc_management_opd_patient.*'
        );
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

            $query->where(function ($query) use ($search, $formattedDate) {
                $query
                    ->orWhere('emp_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('mobile_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('emp_name', 'LIKE', '%' . $search . '%');


                if ($formattedDate) {
                    $query->orWhere('date', 'LIKE', '%' . $formattedDate . '%');
                }
            });
        }
        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)|| in_array(ROLE_EHS_HEAD, $userRole)) {
            $query->orderBy('ohc_management_opd_patient.id', 'DESC');
        } else {
            $query->where('ohc_management_opd_patient.created_by', Auth::id());
        }

        if ($request->has('emp_name') && $request->emp_name) {
            $query = $query->where('ohc_management_opd_patient.emp_name', 'LIKE', '%' . $request->emp_name . '%');
        }

        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_management_opd_patient.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_management_opd_patient.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_management_opd_patient.created_at', '<=', $endDate);
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('ohc_management_opd_patient.patient_status', ($request->status));
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

    // store

    public function store($department, $unit)
    {
        $request = request();


        if ($request->has('is_outside_worker') == 1) {
            $employeeId =  $request->outside_emp_id;
        } else {
            $employeeId =   $request->emp_id;
        }
        $destinationPath = 'uploads/ohc_management/opd/prescribe_to_patient';

        if (!File::exists(public_path($destinationPath))) {
            File::makeDirectory(public_path($destinationPath), 0777, true, true);
        }

        $ohc_file_path = null;

        if ($request->hasFile('file')) {

            $ohc_file = $request->file('file');

            $ohc_file_name = time() . '_' . $ohc_file->getClientOriginalName();
            $ohc_file->move(public_path($destinationPath), $ohc_file_name);

            $ohc_file_path = $destinationPath . '/' . $ohc_file_name;
        }
      
        $insert_array = [
            'is_outside_employee' => $request->has('is_outside_worker') ? 1 : 0,
            'unit_id' =>  $unit->id ?? null,
            'department_id' =>  $request->department_id ?? $department->id ?? null,
            'company_name' => $request->company_name,
            'emp_id' => $employeeId,
            'gender' => $request->gender,
            'emp_name' => $request->emp_name,
            'mobile_no' => $request->mobile_no,
            'time' => $request->time,
            'emergency_contact' => $request->emergency_contact,
            'address' => $request->address,
            'date' => DBdateformat($request->date),
            'vital_checkup' => $request->has('vital_checkup') ? 1 : 0,
            'suggested_by' => $request->suggested_by,
            'cheif_complaint' => $request->cheif_complaint,
            'first_aid_treatment' => $request->has('first_aid_treatment') ? 1 : 0,
            'treatment' => $request->treatment,
            'is_refered' => $request->has('is_reffered') ? 1 : 0,
            'patient_status' => $request->patient_status,
            'fitness_certificate' => $request->fitness_certificate,
            'closed_description' => $request->close_description,
            'suggested_details' => $request->details,
            'created_by' => Auth::id(),
            'file_upload'=> $ohc_file_path,
            'dob' => DBdateformat($request->dob)
        ];


        return $this->create($insert_array);
    }

    public function updates($id, $department,$unit)
    {
        $request = request();

        if ($request->has('is_outside_worker') == 1) {
            $employeeId =  $request->outside_emp_id;
        } else {
            $employeeId =   $request->emp_id;
        }

        $update_array = array(
            'is_outside_employee' => $request->has('is_outside_worker') ? 1 : 0,
            'unit_id' =>  $unit->id ?? null,
            'department_id' => $request->department_id ?? $department->id ?? null,
            'company_name' => $request->company_name,
            'emp_id' =>   $employeeId,
            'gender' => $request->gender,
            'emp_name' => $request->emp_name,
            'mobile_no' => $request->mobile_no,
            'time' => $request->time,
            'emergency_contact' => $request->emergency_contact,
            'address' => $request->address,
            'date' => DBdateformat($request->date),
            'vital_checkup' => $request->has('vital_checkup') ? 1 : 0,
            'suggested_by' => $request->suggested_by,
            'cheif_complaint' => $request->cheif_complaint,
            'first_aid_treatment' => $request->has('first_aid_treatment') ? 1 : 0,
            'treatment' => $request->treatment,
            'is_refered' => $request->has('is_reffered') ? 1 : 0,
            'patient_status' => $request->patient_status,
            'fitness_certificate' => $request->fitness_certificate,
            'closed_description' => $request->close_description,
            'suggested_details' => $request->details,
            'created_by' => Auth::id(),
            'dob' => DBdateformat($request->dob),
            'updated_by' => Auth::id()
        );
        return $this->where('id', $id)->update($update_array);
    }



    public function close($id, $remarks)
    {
        return $this->where('id', $id)->update([
            'cancel_remarks' => $remarks,
            'patient_status' => 3
        ]);
    }
    public function Selectone($id)
    {
        $data =  $this->where('ohc_management_opd_patient.id', $id)
            ->select('ohc_management_opd_patient.*')
            ->where('ohc_management_opd_patient.status', 1)
            ->where('ohc_management_opd_patient.trash', 'No')
            ->first();
        return $data;
    }

    public function uniqueCheck($emp_name)
    {

        return $this->where('emp_name', $emp_name)->get();
    }

    public function existUniqueCheck($emp_name, $id)
    {
        return $this->where('emp_name', $emp_name)
            ->where('id', '!=', $id)
            ->get();
    }
    public function HsnuniqueCheck($hsn)
    {

        return $this->where('hsn',  $hsn)->get();
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $user = Auth::user();
        $userRole = string_to_array($user->role);
        $empId = $user->employee_id;
        $query = $this->select(
            'ohc_management_opd_patient.*'
        );


        if (!empty($request->search) && isset($request->search['value']) && $request->search['value'] !== '') {
            $search = $request->search['value'];


            $formattedDate = null;
            if (\DateTime::createFromFormat('d-m-Y', $search) !== false) {
                $formattedDate = \Carbon\Carbon::createFromFormat('d-m-Y', $search)->format('Y-m-d');
            }

            $query->where(function ($query) use ($search, $formattedDate) {
                $query
                    ->orWhere('emp_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('mobile_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('emp_name', 'LIKE', '%' . $search . '%');


                if ($formattedDate) {
                    $query->orWhere('date', 'LIKE', '%' . $formattedDate . '%');
                }
            });
        }
        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)) {
            $query->orderBy('ohc_management_opd_patient.id', 'DESC');
        } else {
            $query->where('ohc_management_opd_patient.created_by', Auth::id());
        }
        if ($request->has('emp_name') && $request->emp_name) {
            $query = $query->where('ohc_management_opd_patient.emp_name', 'LIKE', '%' . $request->emp_name . '%');
        }

        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_management_opd_patient.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_management_opd_patient.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_management_opd_patient.created_at', '<=', $endDate);
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('ohc_management_opd_patient.patient_status', ($request->status));
        }
        $query->orderBy('ohc_management_opd_patient.id', 'DESC');

        return $query->get(); // Ensure this returns a Collection, not null
    }
}
