<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class FirstAidMedicineInspection extends Model
{
    protected $table = 'inspection_ohc_first_aid_inspection';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'inspection_date',
        'next_due',
        'inspection_data',
        'inspection_status',
        'approval_remarks',
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

        $query = $this->select('inspection_ohc_first_aid_inspection.*');

        $org_total =  $query;
        $org_total_counts = $org_total->count();
        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_ohc_first_aid_inspection.inspection_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_ohc_first_aid_inspection.next_due', 'LIKE', '%' . $search . '%');
            });
        }
        $user = Auth::user();
        $userRole = string_to_array($user->role);
        $empId = $user->employee_id;
        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)  || in_array(ROLE_EHS_OFFICER, $userRole)  || in_array(ROLE_INSPECTION_CREATOR, $userRole) || in_array(ROLE_SAFETY_OFFICER, $userRole) || in_array(ROLE_MEDICAL_ASSISTANT, $userRole)) {
            $query->orderBy('inspection_ohc_first_aid_inspection.id', 'DESC');
        } else {
            $query->where('inspection_ohc_first_aid_inspection.created_by', Auth::id());
        }

        if ($request->has('inspection_date') && $request->inspection_date) {
            $formattedDate = DBdateformat($request->inspection_date);
            $query = $query->whereDate('inspection_ohc_first_aid_inspection.inspection_date', $formattedDate);
        }

        if ($request->has('next_due') && $request->next_due) {
            $formattedDate = DBdateformat($request->next_due);
            $query = $query->whereDate('inspection_ohc_first_aid_inspection.next_due', $formattedDate);
        }


        if ($request->has('status') && $request->status) {

            $query = $query->where('inspection_ohc_first_aid_inspection.inspection_status',  decryptId($request->status));
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
        $id = $request->id;
        foreach ($id as $index => $value) {
            $id = decryptId($value);
            $updated_medicine_checklist[$id] = [
                'medicine_id' => $id,
                'available_quantity' => $request->available_quantity[$index],
                'expired_date' => dbdateformat($request->expired_date[$index]),
                'emp_id' => $request->emp_id[$index],
                'remarks' => $request->remarks[$index],
            ];
        }
        $updated_medicine_checklist = json_encode($updated_medicine_checklist);
        $data = [
            'inspection_date' => DBdateformat($request->inspection_date),
            'next_due' => DBdateformat($request->next_due),
            'inspection_data' =>  $updated_medicine_checklist,
            'created_by' =>  Auth::id(),
            'inspection_status' => OBSERVATION_PENDING,
        ];
        return  $this->create($data);
    }

    public function selectOne($id)
    {
        return $this->where('id', $id)->first();
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_ohc_first_aid_inspection.*');
        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_ohc_first_aid_inspection.inspection_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_ohc_first_aid_inspection.next_due', 'LIKE', '%' . $search . '%');
            });
        }


        if ($request->has('inspection_date') && $request->inspection_date) {
            $formattedDate = DBdateformat($request->inspection_date);
            $query = $query->whereDate('inspection_ohc_first_aid_inspection.inspection_date', $formattedDate);
        }

        if ($request->has('next_due') && $request->next_due) {
            $formattedDate = DBdateformat($request->next_due);
            $query = $query->whereDate('inspection_ohc_first_aid_inspection.next_due', $formattedDate);
        }

        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_ohc_first_aid_inspection.inspection_status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    public function approvalSubmit($id, $status, $remarks)
    {
        $request = Request();
        if ($status == 1) {
            $update_array = [
                'updated_by' => Auth::id(),
                'inspection_status' => OBSERVATION_APPROVED,
                'approval_remarks' => $remarks,
            ];
        } else {
            $update_array = [
                'updated_by' => Auth::id(),
                'inspection_status' => OBSERVATION_REJECTED,
                'approval_remarks' => $remarks,
            ];
        }
        $this->where('id', $id)->update($update_array);
    }
}
