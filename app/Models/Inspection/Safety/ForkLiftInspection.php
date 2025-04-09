<?php

namespace App\Models\Inspection\Safety;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class ForkLiftInspection extends Model
{
    protected $table = 'inspection_safety_forklift_inspection';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'document_reference_id',
        'inspection_date',
        'observation_status',
        'approval_remarks',
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


    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_safety_forklift_inspection.*', 'inspection_static_docno.*', 'inspection_safety_forklift_inspection.id as inspection_id')
            ->leftJoin('inspection_static_docno', 'inspection_safety_forklift_inspection.document_reference_id', '=', 'inspection_static_docno.id');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {});
        }


        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->whereDate('inspection_safety_forklift_inspection.inspection_date', '=', DBdateformat($request->issue_date));
        }

        if (isset($request->obsrevation_status) && $request->obsrevation_status) {
            $query = $query->where('inspection_safety_forklift_inspection.observation_status', decryptId($request->obsrevation_status));
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "rev_data":
                    $query->orderBy('inspection_safety_forklift_inspection.rev_data', $columnorder);
                    break;
                case "issue_date":
                    $query = $query->orderBy('inspection_safety_forklift_inspection.issue_date', $columnorder);
                    break;
                case "doc_no":
                    $query = $query->orderBy('inspection_safety_forklift_inspection.doc_no', $columnorder);
                    break;
                case "inspection_status":
                    $query = $query->orderBy('inspection_safety_forklift_inspection.inspection_status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_safety_forklift_inspection.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_safety_forklift_inspection.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_safety_forklift_inspection.id', 'DESC');
                    break;
            }
        }

        $data_count = $query;
        $total_records = $data_count->count();

        if (isset($request->length) && $request->length != -1) {
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
        $data = array(
            'document_reference_id' => decryptId($request->document_reference_id),
            'inspection_date' => DBdateformat($request->inspection_date),
            'created_by' => Auth::id(),
            'observation_status' => OBSERVATION_PENDING,
        );

        return $this->create($data);
    }


    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_safety_forklift_inspection.*', 'inspection_static_docno.*', 'inspection_safety_forklift_inspection.id as inspection_id')
            ->leftJoin('inspection_static_docno', 'inspection_safety_forklift_inspection.document_reference_id', '=', 'inspection_static_docno.id')
            ->leftJoin('inspection_safety_forklift_inspection_details', 'inspection_safety_forklift_inspection.id', '=', 'inspection_safety_forklift_inspection_details.inspection_id');

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('doc_no LIKE "%' . $search . '%"');
                $query->orWhereRaw('issue_date LIKE "%' . $search . '%"');
                $query->orWhereRaw('rev_data LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->whereDate('inspection_safety_forklift_inspection.inspection_date', '=', DBdateformat($request->issue_date));
        }

        if (isset($request->obsrevation_status) && $request->obsrevation_status) {
            $query = $query->where('inspection_safety_forklift_inspection.observation_status', decryptId($request->obsrevation_status));
        }

        $query->orderBy('inspection_safety_forklift_inspection.id', 'DESC');

        $data =   $query->get();

        if ($data) {
            return $data = $data->groupBy('inspection_id');
        } else {
            return $data;
        }
    }


    public function selectOne($id)
    {
        return $this->where('id', $id)->first();
    }
    public function GetLastMonthObservation($id)
    {
        $data = $this->where('id', $id)->first();
        $current_month = $data->month;
        $current_year = $data->created_at->year;

        $month = Carbon::parse($current_month)->month;
        $last_month = $month - 1;

        if ($last_month == 0) {
            $last_month = 12;
            $current_year -= 1;
        }

        $last_month_name = Carbon::createFromFormat('m', $last_month)->format('F');

        $last_month_record = $this->where('month', $last_month_name)
            ->whereYear('created_at', $current_year)
            ->get();

        return $last_month_record;
    }

    public function approvalSubmit($id, $status, $remarks)
    {
        $request = Request();
        if ($status == 1) {
            $update_array = [
                'updated_by' => Auth::id(),
                'observation_status' => OBSERVATION_APPROVED,
                'approval_remarks' => $remarks,
            ];
        } else {
            $update_array = [
                'updated_by' => Auth::id(),
                'observation_status' => OBSERVATION_REJECTED,
                'approval_remarks' => $remarks,
            ];
        }
        $this->where('id', $id)->update($update_array);
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_safety_forklift_inspection'));
    }
}
