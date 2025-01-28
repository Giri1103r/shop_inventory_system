<?php

namespace App\Models\OhcManagement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MedicineRequisition extends Model
{
    protected $table = 'ohc_management_medicine_requisition';
    protected $primaryKey = 'id';

    protected $fillable = [
        'req_id',
        'medicine_id',
        'quantity',
        'remarks',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];
    public function store($user_medicine_requisition){
        $request = request();


        foreach($request->medicine_id as $index => $medicine) {

            $insert_array = [
                'req_id' => $user_medicine_requisition->id,
                'medicine_id' => $medicine,
                'quantity' => $request->quantity[$index],
                'remarks' => $request->remarks[$index],
                'created_by' => Auth::id(),
            ];

            // Insert the data
            $this->create($insert_array);
        }
    }
    public function updates($id){
        $request = request();


        foreach($request->medicine_id as $index => $medicine) {

            $update_data = [
                'req_id' => $id,
                'medicine_id' => $medicine,
                'quantity' => $request->quantity[$index],
                'remarks' => $request->remarks[$index],
                'created_by' => Auth::id(),
            ];

            return $this->where('req_id', $id)->update($update_data);

        }
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

        return $this->where('req_id', $id)->update($update_data);
    }

    public function selectOne($id)
    {

        $data = $this->select(
            'ohc_management_medicine_requisition.*','ohc_master_medicine.medicine'
        )
            ->where('ohc_management_medicine_requisition.req_id', $id)
            ->join('ohc_master_medicine','ohc_management_medicine_requisition.medicine_id','=','ohc_master_medicine.id')
            ->get();

        return $data;
    }

}
