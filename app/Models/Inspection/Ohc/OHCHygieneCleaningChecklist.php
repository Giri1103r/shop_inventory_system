<?php

namespace App\Models\Inspection\Ohc;

use Carbon\Carbon;
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
        $query = $this->select('inspection_ohc_hygiene_checklist.*', 'inspection_shift_option.*', 'inspection_ohc_hygiene_checklist.id as inspection_id', 'inspection_ohc_hygiene_checklist.created_by as checked_by', 'inspection_ohc_hygiene_checklist.updated_by as verified_by' , 'inspection_ohc_hygiene_checklist.created_at as inspection_created_at',)
            ->leftjoin('inspection_shift_option', 'inspection_shift_option.id', '=', 'inspection_ohc_hygiene_checklist.shift_id');


        if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_NURSING_OFFICER)) {
        } else if (CheckUserRole(ROLE_CLEANER)) {
            $query->where('inspection_ohc_hygiene_checklist.created_by', Auth::id());
        }

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
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_hygiene_checklist.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_hygiene_checklist.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_ohc_hygiene_checklist.created_at', [$startDate, $endDate]);
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

        if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_NURSING_OFFICER)) {
        } else if (CheckUserRole(ROLE_CLEANER)) {
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

        return  $query->get();
    }

    public function selectOne($id)
    {
        return $this->where('id', $id)->first();
    }

    public function approvalSubmit($id, $to_status, $remarks)
    {
        $request = request();
        $update_array = [
            'nursing_officer_remarks' => $remarks,
            'updated_by' => Auth::id(),
            'checklist_status' => $to_status,
        ];
        $this->where('id', $id)->update($update_array);
    }
}
