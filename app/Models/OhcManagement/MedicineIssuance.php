<?php

namespace App\Models\OhcManagement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MedicineIssuance extends Model
{
    protected $table = 'ohc_management_medicine_issuance';
    protected $primaryKey = 'id';

    protected $fillable = [
        'reference_id',
        'medicine_id',
        'quantity',
        'available_quantity',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];
    public function store($user_medicine_issuance){
        $request = request();


        foreach($request->medicine_id as $index => $medicine) {

            $insert_array = [
                'reference_id' => $user_medicine_issuance->id,
                'medicine_id' => ($medicine),
                'quantity' => $request->quantity[$index],
                'available_quantity' => $request->available_quantity[$index],
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
                'reference_id' => $id,
                'medicine_id' => $medicine,
                'quantity' => $request->quantity[$index],
                'available_quantity' => $request->available_quantity[$index],
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];


            return $this->where('reference_id', $id)->update($update_data);
        }
    }
    public function selectOne($id)
    {

        $data = $this->select(
            'ohc_management_medicine_issuance.*')->where('reference_id',$id)
            ->get();

        return $data;
    }

    public function deleterecord($id)
    {

        $update_data = array(
            'status' => 0,
            'trash' => 'YES',
        );

        return $this->where('id', $id)->update($update_data);
    }
}
