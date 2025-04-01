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

        );

        if ($request->search['value'] != null) {
            $search = $request->search['value'];
            $query->where(function ($query) use ($search) {
                $query->orWhere('unit_id', 'LIKE', '%' . $search . '%');
            });
        }

        $org_total_counts = $query->count();

        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }
        $query->orderBy('id', 'desc');
        $data = $query->get();
        $total_records = $data->count();

        return [
            'data' => $data,
            'total_records' => $org_total_counts,
            'filter_records' => $total_records,
        ];
    }
    public function store($data)
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
    public function medicinediscard($id,  $quantity,  $remarks)
    {

        $data  = $this->select('ohc_management_expire_medicine.*')->where('id', $id)
            ->update(['approve_status'=>OHC_DISCARD_EHS_APPROVAL_PENDING,'quantity'=>$quantity,'remarks'=>$remarks]);
        return $data;
    }

    public function ehsheadapproval($id, $updateData)
    {

        $data  = $this->select('ohc_management_expire_medicine.*')->where('id', $id)
            ->update(['approve_status'=>$updateData['approve_status'],'approved_by'=>Auth::id()]);
        return $data;
    }


}

