<?php

namespace App\Models\Inspection\Safety;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class MonthlyEyeWashInspection extends Model
{
    protected $table = 'inspection_monthly_eyewash';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
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
        $query = $this->select('inspection_monthly_eyewash.*');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('document_number LIKE "%' . $search . '%"');
                $query->orWhereRaw('issue_date LIKE "%' . $search . '%"');
                $query->orWhereRaw('revision_date LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->document_number) && $request->document_number) {
            $query = $query->where('inspection_monthly_eyewash.category_name', 'LIKE', '%' . $request->document_number . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->where('inspection_monthly_eyewash.category_name', 'LIKE', '%' . $request->issue_date . '%');
        }
        if (isset($request->rev_date) && $request->rev_date) {
            $query = $query->where('inspection_monthly_eyewash.category_id', 'LIKE', '%' . $request->rev_date . '%');
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "revision_date":
                    $query->orderBy('inspection_monthly_eyewash.revision_date', $columnorder);
                    break;
                case "issue_date":
                    $query = $query->orderBy('inspection_monthly_eyewash.issue_date', $columnorder);
                    break;
                case "document_number":
                    $query = $query->orderBy('inspection_monthly_eyewash.document_number', $columnorder);
                    break;
                case "status":
                    $query = $query->orderBy('inspection_monthly_eyewash.status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_monthly_eyewash.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_monthly_eyewash.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_monthly_eyewash.id', 'DESC');
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
            'issue_date' => Displaydateformat($request->issue_date),
            'revision_date' => $request->rev_date,
            'date_of_inspection' => Displaydateformat($request->inspection_date),
            'location' => decryptId($request->location_id),
            'shift' => decryptId($request->shift_id),
            'next_due' => Displaydateformat($request->next_due),
            'unit' => decryptId($request->unit_id),
            'frequency' => decryptId($request->frequency_id),
            'created_by' => Auth::id(),
            'checked_by' => Auth::id(),
        );

        return $this->create($data);
    }

    public function selectOne($id)
    {
        return $this->where('id', $id)->where('status', 1)->where('trash', 'NO')->first();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_monthly_eyewash'));
    }
}
