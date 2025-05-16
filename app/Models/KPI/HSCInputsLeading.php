<?php

namespace App\Models\KPI;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Google\Rpc\Context\AttributeContext\Request;

class HSCInputsLeading extends Model
{
    protected $table = 'kpi_hsc_inputs_leading';
    protected $fillable = [
        'id',
        'hsc_inputs_id',
        'leading_id',
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
        foreach ($request->leading_input as $index => $value) {
            $insert_array = [
                'hsc_inputs_id' => $id,
                'leading_id' => $index,
                'value' => $value,
                'created_by' => Auth::id(),
            ];
            $this->create($insert_array);
        }
    }
    public function updates($id)
    {
        $request = Request();
        foreach ($request->leading_input as $index => $value) {
            $insert_array = [
                'hsc_inputs_id' => $id,
                'leading_id' => $index,
                'value' => $value,
                'updated_by' => Auth::id(),
            ];
            $this->where('hsc_inputs_id', $id)->update($insert_array);
        }
    }

    public function selectUsingLeading($id)
    {
        return $this->where('hsc_inputs_id', $id)->get();
    }
}
