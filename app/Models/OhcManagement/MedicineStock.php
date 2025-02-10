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
        'pack_id',
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
            'masters_unit.unit_name'
        )
        ->join('masters_unit', 'ohc_management_medicine_stock_inventory.unit_id', '=', 'masters_unit.id')
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

            $query = $query->where('ohc_management_medicine_stock_inventory.medicine_id', $request->medicine_id);
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

        if ($request->has('expire_date') && $request->expire_date) {

            $query = $query->where('ohc_management_medicine_stock_inventory.expire_date', DBdateformat($request->expire_date));
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('ohc_management_medicine_stock_inventory.status', decryptId($request->status));
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
    public function store($medicineDetails)
    {
        $request = request();


        $insert_array = [
            'unit_id' => decryptId($request->unit_id),
            'medicine_id' =>$medicineDetails['medicine'],
            'quantity' => $request->quantity,
            'threshold_limit' => $request->threshold_limit,
            'hsn_number'=>$medicineDetails['hsn'],
            'expire_date' =>DBdateformat($request->expire_date),
            'pack_id' =>$request->pack_id,
            'status'=>0,
            'approve_status'=>STATUS_OHC_MEDICINE_APPROVAL_PENDING,
            'created_by' => Auth::id(),
        ];

        $data = $this->create($insert_array);
        return $data;
    }
    public function updates($id, $medicineDetails )
    {
        $request = request();

        $update_data = [
            'unit_id' => decryptId($request->unit_id),
            'medicine_id' =>  $medicineDetails['medicine'],
            'quantity' => $request->quantity,
            'threshold_limit' => $request->threshold_limit ??  $request->threshold_limit_id,
            'hsn_number'=>$medicineDetails['hsn'],
            'expire_date' =>DBdateformat($request->expire_date),
            'pack_id' =>$request->pack_id,
            'status'=>0,
            'approve_status'=>STATUS_OHC_MEDICINE_APPROVAL_PENDING,
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
        )->where('id',$id)

            ->first();
        return $data;
    }


    public function UniqueCheck($medicine_id, $unit_id, )
    {

        return $this->where('medicine_id', $medicine_id)->where('unit_id', $unit_id)->get();
    }

    public function ExistuniqueCheck($medicine_id,  $unit_id,  $id)
    {

        return $this->where('medicine_id', $medicine_id)->where('unit_id', $unit_id)
            ->where('id', '!=', $id)
            ->get();
    }



    public function exportdata()
    {
        $request = request();


        $query = $this->select(
            'ohc_management_medicine_stock_inventory.*'
        );



        if (isset($request->search) && is_array($request->search) && !empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($query) use ($search) {
                $query->orWhere('ohc_management_medicine_stock_inventory.unit_id', 'LIKE', '%' . $search . '%');
            });
        }

        $query->orderBy('id', 'DESC');


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
        return $this->where('status',1)->whereColumn('quantity','<','threshold_limit')->select('id','medicine_id','unit_id','pack_id')->get();
    }
    // public function getMedicinestockdata(){
    //     return $this->where('status',1)->where('trash','NO')->whereColumn('quantity','>','threshold_limit')->get();
    // }
    public function getAvailableQuantity( $id){
        return $this->where('id',$id)->where('trash','NO')->where('status',1)->select('quantity')->first();
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ohc_management_medicine_stock_inventory'));
    }

    public function ajaxList($unitId = '')
    {
        $query = $this->select('id', 'medicine_id','pack_id')->where('status', 1)->whereColumn('quantity','<','threshold_limit');


        if (!empty($unitId)) {
            $query = $query->where(function ($q) use ($unitId) {
                $q->where('unit_id', $unitId)->where('status', 1);
            });
        }
        $datas = $query->get();

        $list = [];
        foreach ($datas as $data) {
            $listvalue = [];
            $listvalue['id'] = encryptId($data->id);
            $listvalue['name'] = $data->medicine_id;
            $listvalue['pack'] = $data->pack_id;
            $list[] = $listvalue;
        }

        return $list;
    }

    public function getPackDetails(){
        return $this->where('status',1)->where('trash','NO')->get();
    }
}
