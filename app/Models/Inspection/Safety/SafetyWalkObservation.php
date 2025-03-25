<?php

namespace App\Models\Inspection\Safety;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class SafetyWalkObservation extends Model
{

    protected $table = 'inspection_safety_walk_observation';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'doc_no',
        'issue_date',
        'revision_data',
        'date',
        'shift_id',
        'month',
        'unit',
        'safety_walk_taken_by',
        'approval_remarks',
        'observation_status',
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
        $query = $this->select('inspection_safety_walk_observation.*');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('doc_no LIKE "%' . $search . '%"');
                $query->orWhereRaw('issue_date LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->document_number) && $request->document_number) {
            $query = $query->where('inspection_safety_walk_observation.doc_no', 'LIKE', '%' . $request->document_number . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->whereDate('inspection_forklift_inpsection_monthly.issue_date', '=', DBdateformat($request->issue_date));
        }

        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_safety_walk_observation.inspection_status', decryptId($request->inspection_status));
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "revision_data":
                    $query->orderBy('inspection_safety_walk_observation.revision_data', $columnorder);
                    break;
                case "issue_date":
                    $query = $query->orderBy('inspection_safety_walk_observation.issue_date', $columnorder);
                    break;
                case "doc_no":
                    $query = $query->orderBy('inspection_safety_walk_observation.doc_no', $columnorder);
                    break;
                case "inspection_status":
                    $query = $query->orderBy('inspection_safety_walk_observation.inspection_status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_safety_walk_observation.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_safety_walk_observation.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_safety_walk_observation.id', 'DESC');
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
            'doc_no' => $request->doc_no,
            'issue_date' => DBdateformat($request->issue_date),
            'revision_data' => $request->rev_date,
            'date' => DBdateformat($request->inspection_date),
            'month' => $request->month,
            'safety_walk_taken_by' => decryptId($request->shift_id),
            'unit' => decryptId($request->unit),
            'created_by' => Auth::id(),
            'shift_id' => decryptId($request->shift_id),
            'observation_status' => OBSERVATION_PENDING,
        );

        return $this->create($data);
    }


    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_safety_walk_observation.*');
        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('doc_no LIKE "%' . $search . '%"');
                $query->orWhereRaw('issue_date LIKE "%' . $search . '%"');
                $query->orWhereRaw('revision_data LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->document_number) && $request->document_number) {
            $query = $query->where('inspection_safety_walk_observation.doc_no', 'LIKE', '%' . $request->document_number . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->whereDate('inspection_forklift_inpsection_monthly.issue_date', '=', DBdateformat($request->issue_date));
        }
        if (isset($request->rev_date) && $request->rev_date) {
            $query = $query->where('inspection_safety_walk_observation.revision_data', 'LIKE', '%' . $request->rev_date . '%');
        }

        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_safety_walk_observation.inspection_status', decryptId($request->inspection_status));
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
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
}
