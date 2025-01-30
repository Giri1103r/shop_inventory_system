<?php

namespace App\Models\OhcManagement;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MedicineReceiving extends Model
{
    protected $table = 'ohc_management_medicine_receiving';
    protected $primaryKey = 'id';

    protected $fillable = [
        'medicine_id',
        'hsn_id',
        'pack_id',
        'quantity',
        'expire_date',
        'rate',
        'batch_number',
        'vendor_id',
        'approve_status',
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
        $query = $this->select('ohc_management_medicine_receiving.*', 'ohc_master_vendor.vendor_name', 'inventory1.*', 'inventory2.*', 'inventory3.*','ohc_management_medicine_receiving.id As ohc_management_medicine_receiving_id')
            ->join('ohc_master_vendor', 'ohc_management_medicine_receiving.vendor_id', '=', 'ohc_master_vendor.id')
            ->join('ohc_master_medicine as inventory1', 'ohc_management_medicine_receiving.medicine_id', '=', 'inventory1.id')
            ->join('ohc_master_medicine as inventory2', 'ohc_management_medicine_receiving.pack_id', '=', 'inventory2.id')
            ->join('ohc_master_medicine as inventory3', 'ohc_management_medicine_receiving.hsn_id', '=', 'inventory3.id')
            ->where('inventory1.trash', 'NO')
            ->where('inventory2.trash', 'NO')
            ->where('inventory3.trash', 'NO')
            ->where('ohc_master_vendor.trash', 'NO')
            ->where('ohc_management_medicine_receiving.trash', 'NO');


        if ($request->search['value'] != null) {
            $search = $request->search['value'];
            $query->where(function ($query) use ($search) {
                $query->orWhere('medicine_id', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('medicine_id') && $request->medicine_id) {
            $query = $query->where('ohc_management_medicine_receiving.medicine_id', 'LIKE', '%' . $request->medicine_id . '%');
        }
        if ($request->has('vendor_id') && $request->vendor_id) {
            $query = $query->where('ohc_management_medicine_receiving.vendor_id', 'LIKE', '%' . $request->vendor_id . '%');
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_management_medicine_receiving.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_management_medicine_receiving.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_management_medicine_receiving.created_at', '<=', $endDate);
        }


        $org_total_counts = $query->count();

        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }
        $query->orderBy('ohc_management_medicine_receiving.id', 'DESC');
        $data = $query->get();
        $total_records = $data->count();

        return [
            'data' => $data,
            'total_records' => $org_total_counts,
            'filter_records' => $total_records,
        ];
    }

    public function store(){
        $request = request();

        $insert_array = [
          'medicine_id'=>decryptId($request->medicine_id),
              'vendor_id'=>decryptId($request->vendor_id),
              'quantity'=>$request->quantity,
              'batch_number'=>$request->batch_number,
              'expire_date'=>DBdateformat($request->expire_date),
              'hsn_id'=>$request->hsn_id,
              'rate'=>$request->rate,
              'pack_id'=>decryptId($request->pack_id),
              'approve_status'=>STATUS_OHC_EHS_VERIFICATION_PENDING,
              'created_by'=>Auth::id(),
        ];

        return $this->create($insert_array);
    }
    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'medicine_id'=>decryptId($request->medicine_id),
            'vendor_id'=>decryptId($request->vendor_id),
            'quantity'=>$request->quantity,
            'batch_number'=>$request->batch_number,
            'expire_date'=>DBdateformat($request->expire_date),
            'hsn_id'=>$request->hsn_id,
            'rate'=>$request->rate,
            'pack_id'=>decryptId($request->pack_id),
            'updated_by'=>Auth::id(),
        );
        return $this->where('id', $id)->update($update_array);
    }
    public function selectOne($id)
    {

        $data = $this->select(
            'ohc_management_medicine_receiving.*'
        )
            ->where('ohc_management_medicine_receiving.id', $id)
            ->first();

        return $data;
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('ohc_management_medicine_receiving.*', 'ohc_master_vendor.vendor_name', 'inventory1.*', 'inventory2.*', 'inventory3.*','ohc_management_medicine_receiving.id As ohc_management_medicine_receiving_id')
        ->join('ohc_master_vendor', 'ohc_management_medicine_receiving.vendor_id', '=', 'ohc_master_vendor.id')
        ->join('ohc_master_medicine as inventory1', 'ohc_management_medicine_receiving.medicine_id', '=', 'inventory1.id')
        ->join('ohc_master_medicine as inventory2', 'ohc_management_medicine_receiving.pack_id', '=', 'inventory2.id')
        ->join('ohc_master_medicine as inventory3', 'ohc_management_medicine_receiving.hsn_id', '=', 'inventory3.id')
        ->where('inventory1.trash', 'NO')
        ->where('inventory2.trash', 'NO')
        ->where('inventory3.trash', 'NO')
        ->where('ohc_master_vendor.trash', 'NO')
        ->where('ohc_management_medicine_receiving.trash', 'NO');

        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhere('medicine_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('vendor_id', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('medicine_id') && $request->medicine_id) {
            $query = $query->where('ohc_management_medicine_receiving.medicine_id', 'LIKE', '%' . $request->medicine_id . '%');
        }
        if ($request->has('vendor_id') && $request->vendor_id) {
            $query = $query->where('ohc_management_medicine_receiving.vendor_id', 'LIKE', '%' . $request->vendor_id . '%');
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_management_medicine_receiving.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_management_medicine_receiving.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_management_medicine_receiving.created_at', '<=', $endDate);
        }
        $query->orderBy('ohc_management_medicine_receiving.id', 'DESC');

        return  $query->get();
    }

    public function statusupdate($id){
        $this->where('id',$id)->update(['approve_status'=>STATUS_OHC_L1_EHS_VERIFICATION_PENDING]);
    }
}
