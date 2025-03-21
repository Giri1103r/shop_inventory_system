<?php

namespace App\Models\OhcManagement\SafetyPettyLogbook;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use App\Scopes\TrashScope;

class SafetyPettyChecklist extends Model
{
    use  HasFactory;

    protected $table = 'ohc_safety_petty_logbook_checklist';

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

    public function store($sfty_petty_id)
    {
        $request = request();

        $insertedData = [];

        foreach ($request->employee_code as $index => $employeeCode) {
            $insert_array = array(
                'safety_petty_logbook_details_id' => $sfty_petty_id,
                'serial_number' =>$request->serial_number[$index],
                'employee_name' => $request->emp_id[$index],
                'employee_code' => $request->employee_code[$index],
                'department' => $request->department_id[$index],
                'unit' => $request->unit_id[$index],
                'date' => $request->date[$index],
                'amount' => $request->amount[$index],
                'description' => $request->description[$index],
                'amount_given_by' => $request->amount_given_by[$index],
                'amount_received_by' => $request->amount_received_by[$index],
                'remark' => $request->remark[$index],
                'created_by' => Auth::id(),
            );

            $insertedData []=  $this->create($insert_array);
            // dd( $insertedData);
        }

        return $insertedData;
    }

}
