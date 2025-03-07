<?php

namespace App\Models\OhcManagement;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MedicalFitnessCertificate extends Model
{
    protected $table = 'ohc_management_medical_fitness_certificate';
    protected $primaryKey = 'id';

    protected $fillable = [
        'emp_id',
        'emp_name',
        'date',
        'file',
        'remarks',
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
        $empId = $user->employee_id;
        $query = $this->select('ohc_management_medical_fitness_certificate.*');

        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)) {

        }elseif(in_array(ROLE_PARAMEDICS, $userRole)){

        }
        elseif(in_array(ROLE_DOCTOR, $userRole)){

        } elseif(in_array(ROLE_EHS_HEAD, $userRole)){

        }
         else {
            $query->where('ohc_management_medical_fitness_certificate.created_by',Auth::id());
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

            $query = $query->where('ohc_management_medical_fitness_certificate.emp_id', decryptId($request->emp_id));
        }
        if ($request->has('emp_name') && $request->emp_name) {

            $query = $query->where('ohc_management_medical_fitness_certificate.emp_name', decryptId($request->emp_name));
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


        $org_total_counts = $query->count();

        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }
        $query->orderBy('id', 'DESC');
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




    }




    public function selectOne($id)
    {

        $data = $this->select(
            'ohc_management_medical_fitness_certificate.*')->where('id',$id)->where('trash','NO')
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


}
