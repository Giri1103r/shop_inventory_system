<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PpeStockinventory extends Model
{
    protected $table = 'ppe_stock_inventory';
    protected $primaryKey = 'id';

    protected $fillable = [
        'org',
        'item_code',
        'inventory_item_id',
        // 'item_name',
        'ppe_name',
        'uom',
        'sub',
        'quantity',
        'item_description',
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
        $query = $this->select('ppe_stock_inventory.*');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->has('item_code') && $request->item_code) {
            $query->where('item_code', 'LIKE', '%' . $request->item_code . '%');
        }
        if ($request->has('inventory_item_id') && $request->inventory_item_id) {
            $query->where('inventory_item_id', 'LIKE', '%' . $request->inventory_item_id . '%');
        }



        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ppe_stock_inventory.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ppe_stock_inventory.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ppe_stock_inventory.created_at', '<=', $endDate);
        }


        $data_count = $query->count();
        $total_records = $data_count;

        $query->orderBy('id', 'DESC');

        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }

        $data = $query->get();

        $datas = [
            'data' => $data,
            'total_records' => $org_total_counts,
            'filter_records' => $total_records,
        ];

        return $datas;
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

        $data = $this->select('ppe_stock_inventory.*')
            ->where('ppe_stock_inventory.id', $id)
            ->first();

        return $data;
    }



    public function store($data)
    {
        $itemcodes = PpeTypeMaster::where('status', 1)->get();
        $itemCodeMapping = $itemcodes->pluck('ppe_name', 'item_code')->toArray();

        $itemCode = $data['ITEM_CODE'] ?? null;
        $ppeName = $itemCodeMapping[$itemCode] ?? null;

        $insert_array = [
            'org' => $data['ORG'] ?? null,
            'inventory_item_id' => $data['INVENTORY_ITEM_ID'] ?? null,
            'item_code' => $itemCode,
            'ppe_name' => $ppeName,
            'sub' => $data['SUB'] ?? null,
            'uom' => $data['UOM'] ?? null,
            'quantity' => $data['QTY'] ?? null,
            'item_description' => $data['ITEM_DESCRIPTION'] ?? null,
            'created_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $this->updateOrInsert(['item_code' => $itemCode], $insert_array);
    }






    public function updates($id)
    {
        $request = request();
         $update_array =[
            'org'=>$request->org,
            'item_code'=>$request->item_code,
            'inventory_item_id'=>$request->item_inventory_id,
            'uom'=>$request->uom,
            'sub'=>$request->sub,
            'quantity'=>$request->quantity,
            'item_description'=>$request->item_description,
            // 'item_name'=>$request->item_name,
            'ppe_name'=>$request->ppe_name,
            'created_by'=>1,
            'updated_by'=>1,
         ];
       $this->where('id',$id)->update( $update_array);
    }


    public function updateQuantity($itemId, $newQuantity)
    {
        return $this->where('id', $itemId)->update(['quantity' => $newQuantity]);
    }

    // public function getquantity($itemCode, $action)
    // {
    //     $request = request();
    //     $currentQuantity = PpeStockinventory::where('item_code', $itemCode)->first();
    //     if ($action == 'approve') {
    //         $this->where('item_code', $itemCode)->update(['quantity' => $newQuantity]);

    //         return $newQuantity;
    //     }

    //     return $currentQuantity;
    // }

    public function getItemCode(){
        return $this->where('status',1)->where('trash','NO')->where('quantity','!=',0)->get();
    }

    public function ajaxlist($PPEtypeId)
    {
        $data =$this->where('id',$PPEtypeId)->select( 'id','item_code','ppe_name')
            ->get();

        return response()->json($data)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
    }

    public function getStockInventorydata($itemId){
        return  $this->where('id',$itemId)->where('status',1)->where('quantity','!=',0)->first();
    }

    public function Quantitydata($itemId){
        return  $this->where('id',$itemId)->where('status',1)->where('quantity','!=',0)->first();
    }


    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('ppe_stock_inventory.*');


        if ($request->search != null || $request->search != '') {
            $search = $request->search;
            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhereRaw('ppe_stock_inventory.item_code LIKE "%' . $search . '%"');
            });
        }


        if ($request->has('item_code') && $request->item_code) {
            $query->where('item_code', 'LIKE', '%' . $request->item_code . '%');
        }
        if ($request->has('inventory_item_id') && $request->inventory_item_id) {
            $query->where('inventory_item_id', 'LIKE', '%' . $request->inventory_item_id . '%');
        }



        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('created_at', '<=', $endDate);
        }

        return  $query->orderBy('id', 'DESC')->get();
    }
}
