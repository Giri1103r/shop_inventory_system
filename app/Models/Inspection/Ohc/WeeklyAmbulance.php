<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class WeeklyAmbulance extends Model
{
    protected $table = 'inspection_ohc_weekly_ambulance_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'document_no',
        'issue_date',
        'shift',
        'location_id',
        'unit_id',
        'next_due_on',
        'review_date',
        'date_of_inspection',
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

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_ohc_weekly_ambulance_details.*');

        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('subcategory_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('category_id', 'LIKE', '%' . $search . '%');
            });
        }
        if (isset($request->subcategory_id) && $request->subcategory_id) {
            $query = $query->where('inspection_ohc_weekly_ambulance_details.subcategory_id', 'LIKE', '%' . $request->subcategory_id . '%');
        }
        if (isset($request->subcategory_name) && $request->subcategory_name) {
            $query = $query->where('inspection_ohc_weekly_ambulance_details.subcategory_name', 'LIKE', '%' . $request->subcategory_name . '%');
        }
        if (isset($request->category_id) && $request->category_id) {
            $query = $query->where('inspection_ohc_weekly_ambulance_details.category_id', 'LIKE', '%' . decryptId($request->category_id) . '%');
        }

        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_ohc_weekly_ambulance_details.status', 'LIKE', '%' . decryptId($request->status) . '%');
        }


        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('inspection_ohc_weekly_ambulance_details.id', 'DESC');

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

    public function store()
    {
        $request = request();
        $insert_array = [
            'document_no'=>$request,
            'issue_date'=>$request,
            'shift'=>$request,
            'location_id'=>$request,
            'unit_id'=>$request,
            'next_due_on'=>$request,
            'review_date'=>$request,
            'date_of_inspection'=>$request,
            'created_by' => Auth::id(),
        ];
        return self::create($insert_array);
    }
}
