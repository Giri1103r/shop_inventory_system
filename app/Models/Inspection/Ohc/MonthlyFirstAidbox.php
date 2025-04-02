<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MonthlyFirstAidbox extends Model
{
    protected $table = 'inspection_ohc_monthly_first_aid_audit';

    protected $primaryKey = 'id';

    protected $fillable = [
        'checklist',
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
        'approve_status',
        'l1_manager_verified_by',
        'l2_manager_verified_by',
        'level_two_manager_remarks',
        'level_one_manager_remarks',
        'level_two_manager_remarks',
        'l1_manager_verified_by',
        'capa_remarks',
        'capa_recomendation',
        'capa_ehs_remarks',
        'remarks',
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
        $query = $this->select('inspection_ohc_monthly_first_aid_audit.*');

        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('doc_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('revision_date', 'LIKE', '%' . $search . '%');
            });
        }
        if (isset($request->document_number) && $request->document_number) {
            $query = $query->where('inspection_ohc_monthly_first_aid_audit.doc_no', 'LIKE', '%' . $request->document_number . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->where('inspection_ohc_monthly_first_aid_audit.issue_date', DBdateformat($request->issue_date));
        }
        if (isset($request->rev_date) && $request->rev_date) {
            $query = $query->where('inspection_ohc_monthly_first_aid_audit.revision_date', 'LIKE', '%' . $request->rev_date . '%');
        }
        if (isset($request->shift) && $request->shift) {
            $query = $query->where('inspection_ohc_monthly_first_aid_audit.shift',decryptId( $request->shift));
        }
        if (isset($request->frequency) && $request->frequency) {
            $query = $query->where('inspection_ohc_monthly_first_aid_audit.frequency',decryptId( $request->frequency));
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "rev_date":
                    $query->orderBy('inspection_ohc_monthly_first_aid_audit.revision_date', $columnorder);
                    break;
                case "issue_date":
                    $query = $query->orderBy('inspection_ohc_monthly_first_aid_audit.issue_date', $columnorder);
                    break;
                case "document_number":
                    $query = $query->orderBy('inspection_ohc_monthly_first_aid_audit.doc_no', $columnorder);
                    break;
                case "status":
                    $query = $query->orderBy('inspection_ohc_monthly_first_aid_audit.status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_ohc_monthly_first_aid_audit.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_ohc_monthly_first_aid_audit.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_ohc_monthly_first_aid_audit.id', 'DESC');
                    break;
            }
        }


        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('inspection_ohc_monthly_first_aid_audit.id', 'DESC');

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
        // dd($request->all());
        $insert_array = [

            'doc_no' => $request->document_no,
            'issue_date' => DBdateformat($request->issue_date),
            'shift' => decryptId($request->shift),
            'frequency' => decryptId($request->frequency),
            'revision_date' => $request->review_date,
            'date_of_inspection' => DBdateformat($request->date),
            'approve_status'=>WAITING_FOR_EHS_OFFICER_VERIFICATION,
            'created_by' => Auth::id(),
        ];

        return self::create($insert_array);
    }

    public function selectOne($id){
        return $this->where('id',$id)->where('status',1)->first();
    }

    public function exportdata()
    {
        $search = '';
        $request = Request();
        $query = $this->select('inspection_ohc_monthly_first_aid_audit.*');



        if (isset($request->document_number) && $request->document_number) {
            $query = $query->where('inspection_ohc_monthly_first_aid_audit.doc_no', 'LIKE', '%' . $request->document_number . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->where('inspection_ohc_monthly_first_aid_audit.issue_date', DBdateformat($request->issue_date));
        }
        if (isset($request->rev_date) && $request->rev_date) {
            $query = $query->where('inspection_ohc_monthly_first_aid_audit.revision_date', 'LIKE', '%' . $request->rev_date . '%');
        }
        if (isset($request->shift) && $request->shift) {
            $query = $query->where('inspection_ohc_monthly_first_aid_audit.shift',decryptId( $request->shift));
        }
        if (isset($request->frequency) && $request->frequency) {
            $query = $query->where('inspection_ohc_monthly_first_aid_audit.frequency',decryptId( $request->frequency));
        }




        return $query->orderBy('id', 'DESC')->get(); // Add `get()` here
    }

}
