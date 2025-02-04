<?php

namespace App\Models\OhcManagement;

use App\Scopes\TrashScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MedicineStock extends Model
{
    protected $table = 'ohc_management_medicine_stock_inventory';
    protected $primaryKey = 'id';

    protected $fillable = [
        'unit_id',
        'medicine_id',
        'quantity',
        'threshold_limit',
        'hsn_number',
        'expire_date',
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
        $query = $this->select(
            'ohc_management_medicine_stock_inventory.*',
            'medicine_table.medicine',
            'hsn_table.hsn',
            'masters_unit.unit_name'
        )
        ->join('masters_unit', 'ohc_management_medicine_stock_inventory.unit_id', '=', 'masters_unit.id')
        ->join('ohc_master_medicine as medicine_table', 'ohc_management_medicine_stock_inventory.medicine_id', '=', 'medicine_table.id')
        ->join('ohc_master_medicine as hsn_table', 'ohc_management_medicine_stock_inventory.hsn_number', '=', 'hsn_table.id')
        ->where('medicine_table.trash', 'NO')
        ->where('masters_unit.trash', 'NO');



        if ($request->search['value'] != null) {
            $search = $request->search['value'];
            $query->where(function ($query) use ($search) {
                $query->orWhere('unit_id', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('unit_id') && $request->unit_id) {

            $query = $query->where('ohc_management_medicine_stock_inventory.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('medicine_id') && $request->medicine_id) {

            $query = $query->where('ohc_management_medicine_stock_inventory.medicine_id', decryptId($request->medicine_id));
        }
        if ($request->has('threshold_limit_id') && $request->threshold_limit_id) {

            $query = $query->where('ohc_management_medicine_stock_inventory.threshold_limit_id',$request->threshold_limit_id);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_management_medicine_stock_inventory.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_management_medicine_stock_inventory.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_management_medicine_stock_inventory.created_at', '<=', $endDate);
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('ohc_master_first_aid_location.status', decryptId($request->status));
        }
        $org_total_counts = $query->count();

        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }
        $query->orderBy('ohc_management_medicine_stock_inventory.id', 'DESC');
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
            'unit_id' => decryptId($request->unit_id),
            'medicine_id' => decryptId($request->medicine_id),
            'quantity' => $request->quantity,
            'threshold_limit' => $request->threshold_limit,
            'hsn_number'=>decryptId($request->hsn_number),
            'expire_date' =>DBdateformat($request->expire_date),
            'status'=>0,
            'approve_status'=>STATUS_OHC_MEDICINE_APPROVAL_PENDING,
            'created_by' => Auth::id(),
        ];

        $data = $this->create($insert_array);
        return $data;
    }
    public function updates($id)
    {
        $request = request();


        $update_data = [
            'unit_id' => decryptId($request->unit_id),
            'medicine_id' => decryptId($request->medicine_id),
            'quantity' => $request->quantity,
            'threshold_limit' => $request->threshold_limit_id,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),

        ];

        return $this->where('id', $id)->update($update_data);
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

    public function selectOne($id)
    {

        $data = $this->select(
            'ohc_management_medicine_stock_inventory.*'
        )

            ->first();
        return $data;
    }


    public function uniqueCheck($medicine_name)
    {
        $medicine =  $this->where('medicine_id', $medicine_name)->exists();

        return $medicine;
    }
    public function existUniqueCheck($medicine_name, $id)
    {
        return $this->where('medicine_id', $medicine_name)
                    ->where('id', '!=', $id)
                    ->exists();
    }



    public function exportdata()
    {
        $request = request();


        $query = $this->select(
            'ohc_management_medicine_stock_inventory.*',
            'masters_unit.unit_name',
            'inventory1.*',
            'inventory2.*'
        )
        ->join('masters_unit', 'ohc_management_medicine_stock_inventory.unit_id', '=', 'masters_unit.id')
        ->join('ohc_master_medicine as inventory1', 'ohc_management_medicine_stock_inventory.medicine_id', '=', 'inventory1.id')
        ->join('ohc_master_medicine as inventory2', 'ohc_management_medicine_stock_inventory.threshold_limit', '=', 'inventory2.id')
        ->where('inventory1.trash', 'NO')
        ->where('inventory2.trash', 'NO')
        ->where('masters_unit.trash', 'NO');


        if (isset($request->search) && is_array($request->search) && !empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($query) use ($search) {
                $query->orWhere('ohc_management_medicine_stock_inventory.unit_id', 'LIKE', '%' . $search . '%');
            });
        }


        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('ohc_management_medicine_stock_inventory.unit_id', decryptId($request->unit_id));
        }


        if ($request->has('medicine_id') && $request->medicine_id) {
            $query = $query->where('ohc_management_medicine_stock_inventory.medicine_id', decryptId($request->medicine_id));
        }


        if ($request->has('threshold_limit_id') && $request->threshold_limit_id) {
            $query = $query->where('ohc_management_medicine_stock_inventory.threshold_limit_id', $request->threshold_limit_id);
        }


        if ($request->has('from_date') || $request->has('to_date')) {
            try {
                if ($request->has('from_date') && !empty($request->from_date)) {
                    $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)
                        ->startOfDay()
                        ->format('Y-m-d H:i:s');
                    $query->where('ohc_management_medicine_stock_inventory.created_at', '>=', $startDate);
                }

                if ($request->has('to_date') && !empty($request->to_date)) {
                    $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)
                        ->endOfDay()
                        ->format('Y-m-d H:i:s');
                    $query->where('ohc_management_medicine_stock_inventory.created_at', '<=', $endDate);
                }

                if ($request->has('from_date') && $request->has('to_date')) {
                    $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay();
                    $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay();
                    $query->whereBetween('ohc_management_medicine_stock_inventory.created_at', [$startDate, $endDate]);
                }
            } catch (\Exception $e) {
                Log::error('Date parsing error: ' . $e->getMessage());
            }
        }


        if ($request->has('status') && $request->status) {
            $query = $query->where('ohc_master_first_aid_location.status', decryptId($request->status));
        }


        $query->orderBy('ohc_management_medicine_stock_inventory.id', 'DESC');


        return $query->get();
    }
    public function approvalupdate($id)
    {
        $request = request();

        if ($request->action == 'approve') {
            return $this->where('id', $id)->update(['status' => 1, 'approve_status' => STATUS_OHC_CLOSE]);
        } elseif ($request->action == 'reject') {
            return $this->where('id', $id)->update(['trash' => 'YES', 'approve_status' => STATUS_OHC_CLOSE]);
        }

        return false;
    }
    public function getMedicineData(){
        return $this->whereColumn('quantity', '<', 'threshold_limit')->where('status',1)->get();
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ohc_management_medicine_stock_inventory'));
    }
}
