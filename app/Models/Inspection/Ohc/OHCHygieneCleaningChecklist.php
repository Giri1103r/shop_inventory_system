<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Google\Rpc\Context\AttributeContext\Request;

class OHCHygieneCleaningChecklist extends Model
{
    protected $table = 'inspection_ohc_hygiene_checklist';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'issue_date',
        'shift_id',
        'inspection_question',
        'inspection_value',
        'cleaner_remarks',
        'nursing_officer_remarks',
        'checklist_status',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'status',
        'trash',
    ];
    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_ohc_hygiene_checklist.*', 'inspection_shift_option.*', 'inspection_ohc_hygiene_checklist.id as inspection_id', 'inspection_ohc_hygiene_checklist.created_by as checked_by')
            ->leftjoin('inspection_shift_option', 'inspection_shift_option.id', '=', 'inspection_ohc_hygiene_checklist.shift_id');


        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('issue_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('shift', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('shift_id') && $request->shift_id) {
            $query = $query->where('shift_id', 'LIKE', '%' . decryptId($request->shift_id) . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->whereDate('issue_date', '=', DBdateformat($request->issue_date));
        }
        if ($request->has('inspection_status') && $request->inspection_status) {
            $query = $query->where('checklist_status', 'LIKE', '%' . decryptId($request->inspection_status) . '%');
        }
        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('inspection_ohc_hygiene_checklist.id', 'DESC');

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
        $request = Request();
        $insert_array = [
            'issue_date' => DBdateformat($request->issue_date),
            'shift_id' => decryptId($request->shift_id),
            'inspection_question' => $request->inspection_question,
            'inspection_value' => $request->inspection,
            'cleaner_remarks' => $request->remarks,
            'created_by' => Auth::id(),
            'checklist_status' => CLEANER_SUBMITTED_THE_CHECKLIST,
        ];
        return $this->create($insert_array);
    }


    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_ohc_hygiene_checklist.*', 'inspection_shift_option.*', 'inspection_ohc_hygiene_checklist.id as inspection_id', 'inspection_ohc_hygiene_checklist.created_by as checked_by', 'inspection_ohc_hygiene_checklist.updated_by as verified_by')
            ->leftjoin('inspection_shift_option', 'inspection_shift_option.id', '=', 'inspection_ohc_hygiene_checklist.shift_id');


        $org_total =  $query;
        $org_total_counts = $org_total->count();

        // if ($request->search['value'] != null || $request->search['value'] != '') {
        //     $search = $request->search['value'];

        //     $query->where(function ($query) use ($search) {
        //         $query
        //             ->orWhere('issue_date', 'LIKE', '%' . $search . '%')
        //             ->orWhere('shift', 'LIKE', '%' . $search . '%');
        //     });
        // }

        if ($request->has('shift_id') && $request->shift_id) {
            $query = $query->where('shift_id', 'LIKE', '%' . decryptId($request->shift_id) . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->whereDate('issue_date', '=', DBdateformat($request->issue_date));
        }
        if ($request->has('inspection_status') && $request->inspection_status) {
            $query = $query->where('checklist_status', 'LIKE', '%' . decryptId($request->inspection_status) . '%');
        }
        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('inspection_ohc_hygiene_checklist.id', 'DESC');

        return  $query->get();
    }

    public function selectOne($id)
    {
        return $this->where('id', $id)->first();
    }

    public function approvalSubmit()
    {
        $request = request();
        $update_array = [
            'nursing_officer_remarks' => $request->capa_remarks,
            'updated_by' => Auth::id(),
            'checklist_status' => NURSING_OFFICER_SUBMITTED_THE_CHECKLIST,
        ];
        $this->where('id', decryptId($request->id))->update($update_array);
    }
}
