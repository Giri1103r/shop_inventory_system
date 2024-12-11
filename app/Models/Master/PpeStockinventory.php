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

        if ($request->has('ppe_status') && $request->ppe_status) {
            $query->where('status', decryptId($request->ppe_status));
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
        foreach ($data as $item) {
            $insert_array = [
                'org' => isset($item['ORG']) ? $item['ORG'] : null,
                'inventory_item_id' => isset($item['INVENTORY_ITEM_ID']) ? $item['INVENTORY_ITEM_ID'] : null,
                'item_code' => isset($item['ITEM_CODE']) ? $item['ITEM_CODE'] : null,
                'sub' => isset($item['UOM']) ? $item['UOM'] : null,
                'uom' => isset($item['SUB']) ? $item['SUB'] : null,
                'quantity' => isset($item['QTY']) ? $item['QTY'] : null,
                'item_description' => isset($item['ITEM_DESCRIPTION']) ? $item['ITEM_DESCRIPTION'] : null,
                'created_by' => Auth::id(),
            ];

            $this->create($insert_array);
        }

        return true;
    }

    public function getquantity($itemCode, $action)
    {
        $request = request();
        $currentQuantity = PpeStockinventory::where('item_code', $itemCode)->first();
        if ($action == 'approve') {
            $newQuantity = $currentQuantity->quantity - 1;
            $this->where('item_code', $itemCode)->update(['quantity' => $newQuantity]);

            return $newQuantity;
        }

        return $currentQuantity;
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

        if ($request->has('ppe_status') && $request->ppe_status) {
            $query->where('status', decryptId($request->ppe_status));
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
