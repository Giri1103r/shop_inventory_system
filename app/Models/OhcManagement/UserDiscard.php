<?php

namespace App\Models\OhcManagement;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserDiscard extends Model
{
    protected $table = 'ohc_management_discard';
    protected $primaryKey = 'id';

    protected $fillable = [
        'medicine_id',
        'batch_no',
        'quantity',
        'expire_medicine',
        'expire_date',
        'approved_by',
        'approve_status',
        'unit_id',
        'remarks',
        'approver_remarks',
        'expire_id',
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
        $query = $this->select(
            'ohc_management_discard.*'

        );
        if ($request->search['value'] != null) {
            $search = $request->search['value'];
            $query->where(function ($query) use ($search) {
                $query->orWhere('unit_id', 'LIKE', '%' . $search . '%');
            });
        }

        $user = Auth::user();
        $userRole = string_to_array($user->role);
        $unit_id = ($user->unit_id);

        if ($request->has('unit_id') && $request->unit_id) {

            $query = $query->where('ohc_management_discard.unit_id', decryptId($request->unit_id));
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

    public function store($id, $remarks, $expire_medicine)
    {
        $request = request();
        $today = Carbon::today();

        $insert_array = [
            'medicine_id' => $expire_medicine->medicine_id,
            'quantity' => $request->quantity,
            'expire_date' =>  $today,
            'unit_id' =>  Auth::user()->unit_id,
            'expire_medicine_id' => $expire_medicine->id,
            'expire_id' => $id,
            'remarks' => $request->remarks,
            'approve_status' => OHC_DISCARD_EHS_APPROVAL_PENDING,
            'created_by' => Auth::id(),
        ];
        return $this->create($insert_array);
    }
    public function selectOne($id)
    {

        $data = $this->select(
            'ohc_management_discard.*'
        )
            ->where('ohc_management_discard.id', $id)
            ->first();

        return $data;
    }
    public function ehsapproval($id, $approveStatus)
    {
        return $this->where('id', $id)->update(['approve_status' => $approveStatus]);
    }
    public function ehsheadapproval($id, $updateData)
    {

        $data  = $this->select('ohc_management_discard.*')->where('id', $id)
            ->update(['approve_status' => $updateData['approve_status'], 'approver_remarks' => $updateData['remarks'], 'approved_by' => Auth::id()]);
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
        $unit_id = ($user->unit_id);



        $search = '';
        $query = $this->select(
            'ohc_management_discard.*'
        );

        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhere('unit_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('department_id', 'LIKE', '%' . $search . '%');
            });
        }

        $query->orderBy('ohc_management_discard.id', 'DESC');

        return  $query->get();
    }


    public function approvereject($id, $data)
    {
        return $this->where('id', $id)->update([
            'approve_status' => $data['approve_status'],
            'approved_by' => Auth::id(),
        ]);
    }


    public function updatestatus($id)
    {
        return $this->where('id', $id)->update(['approve_status' => STATUS_OHC_CLOSE]);
    }
}
