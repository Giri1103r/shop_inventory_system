<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use App\Scopes\TrashScope;

class SafetyPettyChecklist extends Model
{
    use  HasFactory;

    protected $table = 'inspection_ohc_safety_petty_logbook_checklist';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'safety_petty_logbook_details_id',
        'serial_number',
        'employee_name',
        'employee_code',
        'department',
        'unit',
        'date',
        'amount',
        'description',
        'amount_given_by',
        'amount_received_by',
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

    public function store()
    {
        $request = request();

        $insert_array = array(
            'document_reference_id' => decryptId($request->document_reference_id),
            'serial_number' =>$request->serial_number,
            'employee_name' => $request->emp_id,
            'employee_code' => $request->employee_code,
            'department' => decryptId($request->department_id),
            'unit' => decryptId($request->unit_id),
            'date' => DBdateformat($request->date),
            'amount' => $request->amount,
            'description' => $request->description,
            'amount_given_by' => $request->amnt_givenby_id,
            'amount_received_by' => $request->amnt_receivedby_id,
            'remark' => $request->remark,
            'created_by' => Auth::id(),
        );

        $insertedData =  $this->create($insert_array);

        return $insertedData;
    }

    public function selectOne($id)
    {
        return $this->where('safety_petty_logbook_details_id', $id)->first();
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
        return $this->where('safety_petty_logbook_details_id', $id)->update($update_data);
    }

    public function UniqueCheck($data)
    {
        return $this->where('employee_code',  $data)->get();
    }

    public function ExistuniqueCheck($data, $id)
    {
        return $this->where('employee_code',  $data)
            ->where('id', '!=', $id)
            ->get();
    }
}
