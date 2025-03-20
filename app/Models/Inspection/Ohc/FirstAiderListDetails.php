<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FirstAiderListDetails extends Model
{
    protected $table = 'inspection_ohc_first_aider_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'reference_id',
        'emp_id',
        'designation_id',
        'department_id',
        'unit_id',
        'mobile_no',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function store($first_aider)
    {
        $request = request();


        foreach ($request->emp_name as $index => $emp_id) {

            $insert_array = [
                'reference_id' =>   $first_aider->id,
                'emp_id' => ($emp_id),
                'designation_id' => $request->designation_id[$index],
                'department_id' => decryptId($request->department_id[$index]),
                'unit_id' => decryptId($request->unit_id[$index]),
                'mobile_no' => $request->mobile_no[$index],
                'created_by' => Auth::id(),
            ];


            $this->create($insert_array);
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

        return $this->where('reference_id', $id)->update($update_data);
    }

    public function Selectone($id)
    {
        return $this->where('reference_id', $id)->get();
    }
}
