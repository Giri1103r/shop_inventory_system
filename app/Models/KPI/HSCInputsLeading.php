<?php

namespace App\Models\KPI;

use Carbon\Carbon;
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


    public function getChart1()
    {
        $request = Request();
        $year = Carbon::now()->format('Y');
        $query = $this->select('kpi_hsc_inputs_leading.*', 'kpi_hsc_inputs.*')
            ->leftJoin('kpi_hsc_inputs', 'kpi_hsc_inputs_leading.hsc_inputs_id', 'kpi_hsc_inputs.id')
            ->where('leading_id', LEADING_CATEGORY_1)
            ->where('calendar_year', $year);

        if ($request->CompanyId) {
            $company_id = decryptId($request->CompanyId);
            $query->where('kpi_hsc_inputs.company_id', $company_id);
        }

        if ($request->Fromdate && $request->Todate) {
            $query->whereBetween('kpi_hsc_inputs.created_at', [
                DBdateformat($request->Fromdate),
                DBdateformat($request->Todate) . ' 23:59:59'
            ]);
        } elseif ($request->Fromdate) {
            $query->where('kpi_hsc_inputs.created_at', '>=', DBdateformat($request->Fromdate));
        } elseif ($request->Todate) {
            $query->where('kpi_hsc_inputs.created_at', '<=', DBdateformat($request->Todate) . ' 23:59:59');
        }
        return $query->get();
    }

    public function selectUsingLeading($id)
    {
        return $this->where('hsc_inputs_id', $id)->get();
    }
}
