<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class WeeklyAmbulance extends Model
{
    protected $table = 'inspection_ohc_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'ohc_type',
        'doc_no',
        'issue_date',
        'revision_date',
        'date_of_inspection',
        'location',
        'shift',
        'next_due',
        'unit',
        'frequency',
        'checked_by',
        'verified_by',
        'approved_by',
        'l1_manager_verification',
        'l2_manager_verification',
        'created_by',
        'updated_by',
        'status',
        'trash',
        'created_at',
        'updated_at'
    ];



    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_ohc_details.*');

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



        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('inspection_ohc_details.id', 'DESC');

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
            'ohc_type'=>decryptId($request->ohc_type),
            'doc_no'=>$request->document_no,
            'issue_date'=>DBdateformat($request->issue_date),
            'shift'=>decryptId($request->shift),
            'location'=>decryptId($request->location_id),
            'unit'=>decryptId($request->unit_id),
            'next_due'=>DBdateformat($request->next_due_on),
            'revision_date'=>$request->review_date,
            'date_of_inspection'=>DBdateformat($request->date_of_inspection),
            'created_by' => Auth::id(),
        ];
      
        return self::create($insert_array);
    }
}
