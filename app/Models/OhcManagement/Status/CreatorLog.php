<?php

namespace App\Models\OhcManagement\Status;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreatorLog extends Model
{
    protected $table = 'ohc_creator_log';
    protected $primaryKey = 'id';

    protected $fillable = [
        'type',
        'unit_id',
        'inventory_id',
        'department_id',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];

    public function store($user_medicine_issuance)
    {

        $insert_array = [
            'type' => TYPE_OHC_ISSUANCE,
            'unit_id' => 1,
            'inventory_id' => $user_medicine_issuance->id,
            'department_id' => 1,
            'created_by' => Auth::id(),
        ];
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $update_array = [
            'type' => TYPE_OHC_ISSUANCE,
            'unit_id' => 1,
            'inventory_id' => $id,
            'department_id' => 1,
            'created_by' => Auth::id(),
        ];
        return $this->where('inventory_id', $id)->update($update_array);
    }

    public function   selectOne($id)
    {
        $data  = $this->select('ohc_creator_log.*')->where('inventory_id', $id)
            ->first();
        return $data;
    }


    public function getunitdata($selectedYear, $selectedMonth, $selectedUnit)
    {
        return $this->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->where('status', 1)
            ->where('unit_id', $selectedUnit)
            ->pluck('id')
            ->toArray();
    }

    public function getYealyunitdata($selectedYear, $selectedUnit)
    {
        return $this->whereYear('created_at', $selectedYear)
            ->where('status', 1)
            ->where('unit_id', $selectedUnit)
            ->pluck('id')
            ->toArray();
    }
}
