<?php

namespace App\Models\Inspection\MSDS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use App\Scopes\TrashScope;

class MSDSDetails extends Model
{

    use  HasFactory;

    protected $table = 'inspection_msds';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'document_reference_id',
        'serial_number',
        'item_code',
        'name_of_chemical',
        'msds_availability_status',
        'remark',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];

    public function list()
    {
        $request = request();
        $search = '';

        $query = $this->select('inspection_msds.*');

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('item_code', 'LIKE', '%' . $search . '%')
                    ->orWhere('name_of_chemical', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('item_code') && $request->item_code) {
            $query = $query->where('item_code', 'LIKE', '%' . $request->item_code . '%');
        }
        if (isset($request->name_of_chemical) && $request->name_of_chemical) {
            $query = $query->where('name_of_chemical', 'LIKE', '%' . $request->name_of_chemical . '%');
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

        $insertedData = [];

        foreach ($request->item_code as $index => $itemCode) {
            $insert_array = array(
                'document_reference_id' => decryptId($request->document_reference_id),
                'serial_number' =>$request->serial_number[$index],
                'item_code' => $itemCode,
                'name_of_chemical' => $request->name_of_chemical[$index],
                'msds_availability_status' => decryptId($request->msds_availability_status[$index]),
                'remark' => $request->remark[$index],
                'created_by' => Auth::id(),
            );


            $insertedData []=  $this->create($insert_array);

        }

        return $insertedData;
    }

    public function selectOne($id)
    {
        return $this->where('id', $id)->first();
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_msds.*');

        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhere('item_code', 'LIKE', '%' . $search . '%')
                    ->orWhere('name_of_chemical', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('item_code') && $request->item_code) {
            $query = $query->where('item_code', 'LIKE', '%' . $request->item_code . '%');
        }
        if (isset($request->name_of_chemical) && $request->name_of_chemical) {
            $query = $query->where('name_of_chemical', 'LIKE', '%' . $request->name_of_chemical . '%');
        }

        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_msds'));
    }
}
