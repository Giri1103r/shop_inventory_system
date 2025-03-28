<?php

namespace App\Models\Inspection\ohc;

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
        $query = $this->select('inspection_ohc_hygiene_checklist.*');

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('document_number', 'LIKE', '%' . $search . '%')
                    ->orWhere('issue_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('revision_date', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('document_number') && $request->document_number) {
            $query = $query->where('document_number', 'LIKE', '%' . $request->document_number . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->whereDate('issue_date', '=', DBdateformat($request->issue_date));
        }
        if ($request->has('revision_date') && $request->revision_date) {
            $query = $query->where('revision_date', 'LIKE', '%' . $request->revision_date . '%');
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('status', decryptId($request->status));
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
        $this->create($insert_array);
    }
}
