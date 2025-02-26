<?php

namespace App\Models\OhcManagement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Discard extends Model
{
    protected $table = 'ohc_management_discard_medicine';
    protected $primaryKey = 'id';

    protected $fillable = [
        'req_id',
        'medicine_id',
        'available_quantity',
        'quantity',
        'remarks',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];
    public function store($user_medicine_requisition)
    {
        $request = request();


        foreach ($request->medicine_id as $index => $medicine) {

            $insert_array = [
                'req_id' => $user_medicine_requisition->id,
                'medicine_id' => decryptId($medicine),
                'available_quantity' => $request->available_quantity[$index],
                'quantity' => $request->quantity[$index],
                'remarks' => $request->remarks[$index],
                'created_by' => Auth::id(),
            ];


            $this->create($insert_array);
        }
    }
    public function updates($ids,$id)
    {
        $request = request();


            $update_data = [
                'req_id' => $id,
                'medicine_id' => decryptId($request->medicine_id),
                'available_quantity' => $request->available_quantity,
                'quantity' => $request->quantity,
                'remarks' => $request->remarks,
                'created_by' => Auth::id(),
            ];

            return $this->where('id', $ids)->update($update_data);

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

        $data  = $this->select('ohc_management_discard_medicine.*')->where('req_id', $id)
            ->get();
        return $data;
    }

    public function firstdata($id)
    {

        $data  = $this->select('ohc_management_discard_medicine.*')->where('id', $id)
            ->first();
        return $data;
    }
}
