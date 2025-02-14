<?php

namespace App\Models\OhcManagement\Opd;

use App\Models\Master\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

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
        $query = $this->select('ohc_management_opd_patient.*', 'ohc_management_opd_patient_status.patient_status', 'ohc_management_opd_patient_suggested_by.suggested_by')
            ->join('ohc_management_opd_patient_status', 'ohc_management_opd_patient.patient_status', '=', 'ohc_management_opd_patient_status.id')

            ->join('ohc_management_opd_patient_suggested_by', 'ohc_management_opd_patient.suggested_by', '=', 'ohc_management_opd_patient_suggested_by.id');

        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('emp_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('emp_name', 'LIKE', '%' . $search . '%');
            });
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

    public function store()
    {
        $request = request();


        $is_outside_employee = $request->has('is_outside_employee') ? 1 : 0;
        $first_aid_treatment = $request->has('first_aid_treatment') ? 1 : 0; // Fixed typo
        $is_refered = $request->has('is_refered') ? 1 : 0;
        $vital_checkup = $request->has('vital_checkup') ? 1 : 0;
        $department = Department::where('department_name', $request->department_id)->first();
        $insert_array = [
            'is_outside_employee' => $is_outside_employee,
            'unit_id' => ($request->unit_id),
            'department_id' =>  $department['id'],
            'company_name' => $request->company_name,
            'emp_id' => $request->emp_id,
            'gender' => $request->gender,
            'emp_name' => $request->emp_name,
            'mobile_no' => $request->mobile_no,
            'time' => $request->time,
            'emergency_contact' => $request->emergency_contact,
            'address' => $request->address,
            'date' => DBdateformat($request->date),
            'vital_checkup' => $vital_checkup,
            'suggested_by' => $request->suggested_by,
            'cheif_complaint' => $request->cheif_complaint,
            'first_aid_treatment' => $first_aid_treatment,
            'treatment' => $request->treatment,
            'is_refered' => $is_refered,
            'patient_status' => $request->patient_status,
            'fitness_certificate' => $request->fitness_certificate,
            'closed_description' => $request->closed_description,
            'suggested_details' => $request->details,
            'created_by' => Auth::id(),
            'dob' => DBdateformat($request->dob)
        ];

        return $this->create($insert_array);
    }

    public function close($id, $remarks)
    {
        return $this->where('id', $id)->update(['cancel_remarks' => $remarks]);
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

   
}
