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
        'unit_id',
        'department_id',
        'discard_date',
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
            'ohc_management_discard.id as discard_id',
            'ohc_management_discard_medicine.id as medicine_id',
            'ohc_management_discard.*',
            'ohc_management_discard_medicine.*'
        )
        ->join('ohc_management_discard_medicine', 'ohc_management_discard.id', '=', 'ohc_management_discard_medicine.req_id')
        ->where('ohc_management_discard_medicine.trash', 'NO')
        ->where('ohc_management_discard.trash', 'NO')
        ->orderBy('discard_id', 'desc')
        ->limit(10)
        ->offset(0);



        if ($request->search['value'] != null) {
            $search = $request->search['value'];
            $query->where(function ($query) use ($search) {
                $query->orWhere('unit_id', 'LIKE', '%' . $search . '%');
            });
        }





        if ($request->has('unit_id') && $request->unit_id) {

            $query = $query->where('ohc_management_discard.unit_id', decryptId($request->unit_id));

        }


        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_management_discard.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_management_discard.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_management_discard.created_at', '<=', $endDate);
        }


        $org_total_counts = $query->count();

        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }
        $query->orderBy('discard_id', 'DESC');
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

        $insert_array = [
            'unit_id' =>Auth::user()->unit_id,
            'department_id' =>decryptId($request->department_id),
            'discard_date' => DBdateformat($request->discard_date),
            'created_by' => Auth::id(),
        ];

        return $this->create($insert_array);
    }
    public function updates($id)
    {

        $request = request();

        $update_array = array(
          'unit_id' =>Auth::user()->unit_id,
            'department_id' =>Auth::user()->department_id,
            'discard_date' => DBdateformat($request->discard_date),
            'req_id' => $request->req_id,
            'updated_by' => Auth::id(),
        );

        return $this->where('id', $id)->update($update_array);
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
        $query = $this->select(
            'ohc_management_discard.id as discard_id',
            'ohc_management_discard_medicine.id as medicine_id',
            'ohc_management_discard.*',
            'ohc_management_discard_medicine.*'
        )
        ->join('ohc_management_discard_medicine', 'ohc_management_discard.id', '=', 'ohc_management_discard_medicine.req_id')
        ->where('ohc_management_discard_medicine.trash', 'NO')
        ->where('ohc_management_discard.trash', 'NO')
        ->orderBy('discard_id', 'desc')
        ->limit(10)
        ->offset(0);

        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhere('unit_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('department_id', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('unit_id') && $request->unit_id) {

            $query = $query->where('ohc_management_discard.unit_id', decryptId($request->unit_id));

        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_management_discard.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_management_discard.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_management_discard.created_at', '<=', $endDate);
        }


        $query->orderBy('discard_id', 'DESC');

        return  $query->get();
    }


    public function approvereject($id, $data)
    {
        return $this->where('id', $id)->update(['approve_status' => $data['approve_status'],
        'approved_by'=>Auth::id(),
    ]);
    }
    // status closed

    public function updatestatus($id){
        return $this->where('id',$id)->update(['approve_status'=>STATUS_OHC_CLOSE]);
    }

    // sending the data to email


}
