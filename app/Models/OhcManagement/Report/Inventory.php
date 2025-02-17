<?php

namespace App\Models\OhcManagement\Report;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'ohc_report_inventory';
    protected $primaryKey = 'id';

    protected $fillable = [
        'medicine_id',
        'unit_id',
        'total_purchase',
        'total_issue',
        'total_first_aid',
        'total_prescribe',
        'total_received',
        'balance',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function list(){
        $request = request();
        $search = '';
        $query = $this->select('ohc_report_inventory.*');

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('medicine_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('unit_id', 'LIKE', '%' . $search . '%');
            });
        }

        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('id', 'DESC');

        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }

        $data = $query->get();

        $datas = array(
            'data' => $data,
            'total_records' => $org_total_counts,
            'filter_records' => $total_records,
        );
        return $datas;
    }

    public function store($details){
        $request = request();
        $insert_array = [
            'unit_id'=>$details->unit_id,
            'medicine_id'=>$details->medicine,
            'total_purchase'=>0,
            'total_issue'=>0,
            'total_received'=>0,
            'total_first_aid'=>0,
            'total_prescribe'=>0,
            'balance'=>0,
            'created_by'=>Auth::id(),
        ];
        return $this->create($insert_array);

    }
}
