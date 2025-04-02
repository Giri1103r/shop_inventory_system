<?php

namespace App\Models\OhcManagement;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ExpireMedicine extends Model
{
    protected $table = 'ohc_management_expire_medicine';
    protected $primaryKey = 'id';

    protected $fillable = [
        'medicine_id',
        'expire_date',
        'quantity',
        'batch_no',
        'remarks',
        'approve_status',
        'approved_by',
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
        $user = Auth::user();
        $userRole = string_to_array($user->role);
        $empId = $user->employee_id;

        $query = $this->select(
            'ohc_management_expire_medicine.*',
            'ohc_report_inventory.balance',
            'ohc_management_expire_medicine.medicine_id as medicine'
        )
            ->leftjoin('ohc_report_inventory', 'ohc_management_expire_medicine.medicine_id', '=', 'ohc_report_inventory.medicine_id')
            ->where('ohc_report_inventory.unit_id', '=', 1);

        if ($request->search['value'] != null) {
            $search = $request->search['value'];
            $query->where(function ($query) use ($search) {
                $query->orWhere('unit_id', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('medicine_id') && $request->medicine_id) {
            $query = $query->where('ohc_management_expire_medicine.medicine_id', $request->medicine_id);
        }

        if ($request->has('expire_date') && $request->expire_date) {
            $query = $query->where('ohc_management_expire_medicine.expire_date',  DBdateformat($request->expire_date));
        }
        $org_total_counts = $query->count();

        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }

        $query
      ->orderBy('ohc_management_expire_medicine.id', 'desc');
        $data = $query->get();
        $total_records = $data->count();

        return [
            'data' => $data,
            'total_records' => $org_total_counts,
            'filter_records' => $total_records,
        ];
    }


    public function store($data, $unitIds)
    {
        $request = request();

        $insert_array = [

            'medicine_id' => $data->medicine_id,
            'expire_date' => ($data->expire_date),
            'batch_no' => $data->batch_number,
            'created_by' => Auth::id(),
        ];


        return $this->create($insert_array);
    }

    public function statuschange($ids)
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

        return $this->where('id', $ids)->update($update_data);
    }

    public function selectOne($id)
    {

        $data  = $this->select('ohc_management_expire_medicine.*')->where('id', $id)
            ->first();
        return $data;
    }

    public function closediscard($id)
    {
        $request = request();
        $data  = $this->select('ohc_management_expire_medicine.*')->where('id', $id)
            ->update(['approve_status' => OHC_DISCARD_EHS_APPROVED,   'remarks' => $request->remarks,'status' => 0]);
        return $data;
    }
    public function medicinediscard($id,  $quantity,  $remarks)
    {
        $request = request();
        $unit = decryptId($request->unit_id);
        $data  = $this->select('ohc_management_expire_medicine.*')->where('id', $id)
            ->update(['approve_status' => OHC_DISCARD_EHS_APPROVAL_PENDING, 'quantity' => $quantity, 'remarks' => $remarks, 'unit_id' => $unit]);
        return $data;
    }

    public function ehsheadapproval($id, $updateData)
    {

        $data  = $this->select('ohc_management_expire_medicine.*')->where('id', $id)
            ->update(['approve_status' => $updateData['approve_status'], 'approved_by' => Auth::id()]);
        return $data;
    }
}
