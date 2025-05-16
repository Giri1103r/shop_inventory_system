<?php

namespace App\Models\KPI;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class HSCInputsLagging extends Model
{
    protected $table = 'kpi_hsc_inputs_lagging';
    protected $fillable = [
        'id',
        'hsc_inputs_id',
        'lagging_id',
        'value',
        'updated_by',
        'created_by',
        'trash',
        'trash',
        'updated_at',
        'created_at',
    ];
    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function store($id)
    {
        $request = Request();
        foreach ($request->lagging_input as $index => $value) {
            $update_array = [
                'hsc_inputs_id' => $id,
                'lagging_id' => $index,
                'value' => $value,
                'created_by' => Auth::id(),

            ];
            $this->create($update_array);
        }
    }
    public function updates($id)
    {
        $request = Request();
        foreach ($request->lagging_input as $index => $value) {
            $update_array = [
                'hsc_inputs_id' => $id,
                'lagging_id' => $index,
                'value' => $value,
                'updated_by' => Auth::id(),
            ];
            $this->where('hsc_inputs_id', $id)->update($update_array);
        }
    }
    public function selectUsingLagging($id)
    {
        return $this->where('hsc_inputs_id', $id)->get();
    }
}
