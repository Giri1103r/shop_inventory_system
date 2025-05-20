<?php

namespace App\Models\OhcManagement;

use App\Models\Master\Employee;
use App\Models\Master\Work;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class MedicalFitnessCertificate extends Model
{
    protected $table = 'ohc_management_medical_fitness_certificate';
    protected $primaryKey = 'id';

    protected $fillable = [
        'emp_id',
        'emp_name',
        'date',
        'company_id',
        'file',
        'remarks',
        'cheif_complaint',
        'approve_status',
        'approved_by',
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
        $companyId = $user->company_id;
        $empId = $user->employee_id;
        $query = $this->select('ohc_management_medical_fitness_certificate.*');

        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)) {
        } elseif (in_array(ROLE_PARAMEDICS, $userRole)) {
        } elseif (in_array(ROLE_DOCTOR, $userRole)) {
            $query->where('ohc_management_medical_fitness_certificate.company_id', $companyId);
        } elseif (in_array(ROLE_EHS_HEAD, $userRole)) {
        } else {
            $query->where('ohc_management_medical_fitness_certificate.created_by', Auth::id());
        }

        if ($request->search['value'] != null) {
            $search = $request->search['value'];
            $query->where(function ($query) use ($search) {
                $query->orWhere('unit_id', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('ohc_management_medical_fitness_certificate.approve_status', decryptId($request->status));
        }
        if ($request->has('emp_id') && $request->emp_id) {

            $query = $query->where('ohc_management_medical_fitness_certificate.emp_id', ($request->emp_id));
        }
        if ($request->has('emp_name') && $request->emp_name) {

            $query = $query->where('ohc_management_medical_fitness_certificate.emp_name', ($request->emp_name));
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_management_medical_fitness_certificate.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_management_medical_fitness_certificate.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_management_medical_fitness_certificate.created_at', '<=', $endDate);
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

        $destinationPath = 'uploads/ohc_management/medical_fitness';

        if (!File::exists(public_path($destinationPath))) {
            File::makeDirectory(public_path($destinationPath), 0777, true, true);
        }

        $ppe_file_path = null;

        if ($request->hasFile('file')) {
            $ohc_file = $request->file('file');

            $ohc_file_name = time() . '_' . $ohc_file->getClientOriginalName();
            $ohc_file->move(public_path($destinationPath), $ohc_file_name);

            $ohc_file_path = $destinationPath . '/' . $ohc_file_name;
        }
        $employee = Employee::where('emp_id', $request->emp_id)
            ->select('company')
            ->first();


        if (!$employee) {
            $employee = Work::where('emp_id', $request->emp_id)
                ->select('company')
                ->first();
        }


        if ($employee) {
            $company = $employee->company;
        }
        $insert_array = [
            'emp_id' => $request->emp_id,
            'emp_name' => $request->emp_name,
            'emp_name' => $request->emp_name,
            'company_id' => $company,
            'remarks' => $request->remarks,
            'cheif_complaint' => $request->cheif_complaint,
            'date' => DBdateformat($request->date),
            'file' => $ohc_file_path,
            'approve_status' => STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING,
            'created_by' => Auth::id(),
        ];

        return $this->create($insert_array);
    }


    public function updates($id)
    {
        $request = request();

        $destinationPath = 'uploads/ohc_file';
        $ohc_file_path = $request->input('existing_pre_image');

        if ($request->hasFile('file')) {
            $ohc_file = $request->file('file');
            $ohc_file_name = time() . '_' . $ohc_file->getClientOriginalName();

            while (File::exists(public_path($destinationPath . '/' . $ohc_file_name))) {
                $ohc_file_name = time() . '_' . uniqid() . '_' . $ohc_file->getClientOriginalName();
            }

            $ohc_file->move(public_path($destinationPath), $ohc_file_name);
            $ohc_file_path = $destinationPath . '/' . $ohc_file_name;

            if ($request->input('existing_pre_image') && File::exists(public_path($request->input('existing_pre_image')))) {
                File::delete(public_path($request->input('existing_pre_image')));
            }
        }

        $employee = Employee::where('emp_id', $request->emp_id)
            ->select('company')
            ->first();


        if (!$employee) {
            $employee = Work::where('emp_id', $request->emp_id)
                ->select('company')
                ->first();
        }


        if ($employee) {
            $company = $employee->company;
        }
        $update_array = array(
            'emp_id' => $request->emp_id,
            'emp_name' => $request->emp_name,
            'remarks' => $request->remarks,
            'cheif_complaint' => $request->cheif_complaint,
            'company_id' => $company,
            'date' => DBdateformat($request->date),
            'file' =>  $ohc_file_path,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id()
        );
        return $this->where('id', $id)->update($update_array);
    }

    public function selectOne($id)
    {

        $data = $this->select(
            'ohc_management_medical_fitness_certificate.*'
        )->where('id', $id)->where('trash', 'NO')
            ->first();

        return $data;
    }

    public function deleterecord($ids)
    {

        $update_data = array(
            'status' => 0,
            'trash' => 'YES',
        );

        return $this->where('id', $ids)->update($update_data);
    }

    public function doctorapproval($id, $doctorverifydata)
    {
        return $this->where('id', $id)->update(['approve_status' => $doctorverifydata['approve_status'], 'approved_by' => Auth::id(),]);
    }

    public function ehsheadapproval($id, $ehsheadverifydata)
    {
        return $this->where('id', $id)->update(['approve_status' => $ehsheadverifydata['approve_status'], 'approved_by' => Auth::id(),]);
    }

    public function exportdata()
    {
        $request = request();

        $query = $this->select('ohc_management_medical_fitness_certificate.*');

        if (!empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ohc_management_medical_fitness_certificate.emp_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('ohc_management_medical_fitness_certificate.emp_name', 'LIKE', '%' . $search . '%');
            });
        }

        // Additional filters
        if ($request->filled('status')) {
            $query->where('ohc_management_medical_fitness_certificate.approve_status', decryptId($request->status));
        }
        if ($request->filled('emp_id')) {
            $query->where('ohc_management_medical_fitness_certificate.emp_id', $request->emp_id . '%');
        }
        if ($request->filled('emp_name')) {
            $query->where('ohc_management_medical_fitness_certificate.emp_name',  $request->emp_name . '%');
        }

        // Date range filter
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $startDate = Carbon::parse($request->from_date)->startOfDay();
            $endDate = Carbon::parse($request->to_date)->endOfDay();
            $query->whereBetween('ohc_management_medical_fitness_certificate.created_at', [$startDate, $endDate]);
        } elseif ($request->filled('from_date')) {
            $startDate = Carbon::parse($request->from_date)->startOfDay();
            $query->where('ohc_management_medical_fitness_certificate.created_at', '>=', $startDate);
        } elseif ($request->filled('to_date')) {
            $endDate = Carbon::parse($request->to_date)->endOfDay();
            $query->where('ohc_management_medical_fitness_certificate.created_at', '<=', $endDate);
        }

        return $query->orderByDesc('id')->get();
    }
}
