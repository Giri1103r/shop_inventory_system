<?php

namespace App\Models\Inspection\Ohc;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FirstAiderList extends Model
{
    protected $table = 'inspection_ohc_first_aider';

    protected $primaryKey = 'id';

    protected $fillable = [
        'document_reference_id',
        'last_updated_date',
        'next_review_date',
        'created_by',
        'updated_by',
        'status',
        'trash',
        'created_at',
        'updated_at',
    ];



    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_ohc_first_aider.*');

        $query = $this->select(
            'inspection_ohc_first_aider.*',
            'inspection_static_docno.*',
            'inspection_ohc_first_aider.id as inspection_id',
            'inspection_ohc_first_aider.created_at as inspection_created_at',
            'inspection_ohc_first_aider.status as inspection_status',
        )
            ->leftJoin(
                'inspection_static_docno',
                'inspection_ohc_first_aider.document_reference_id',
                '=',
                'inspection_static_docno.id'
            );

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
        if (isset($request->next_review_date) && $request->next_review_date) {
            $query = $query->whereDate('inspection_ohc_first_aider.next_review_date', DBdateformat($request->next_review_date));
        }
        if (isset($request->last_updated_date) && $request->last_updated_date) {
            $query = $query->whereDate('inspection_ohc_first_aider.last_updated_date', DBdateformat($request->last_updated_date));
        }
        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_ohc_first_aider.status',   decryptId($request->status));
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_first_aider.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_first_aider.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_ohc_first_aider.created_at', [$startDate, $endDate]);
        }
        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "rev_date":
                    $query->orderBy('inspection_ohc_first_aider.revision_date', $columnorder);
                    break;
                case "issue_date":
                    $query = $query->orderBy('inspection_ohc_first_aider.issue_date', $columnorder);
                    break;
                case "document_number":
                    $query = $query->orderBy('inspection_ohc_first_aider.doc_no', $columnorder);
                    break;
                case "status":
                    $query = $query->orderBy('inspection_ohc_first_aider.status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_ohc_first_aider.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_ohc_first_aider.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_ohc_first_aider.id', 'DESC');
                    break;
            }
        }


        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('inspection_ohc_first_aider.id', 'DESC');

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

            'next_review_date' => !empty($request->next_review_date) ? DBdateformat($request->next_review_date) : null,
            'last_updated_date' => !empty($request->last_updated_date) ? DBdateformat($request->last_updated_date) : null,
            'document_reference_id' => decryptId($request->document_reference_id),
            'created_by' => Auth::id(),
        ];

        return self::create($insert_array);
    }

    public function statuschange($id)
    {
        $request = request();

        $type = $request->types;
        if ($type == 1) {
            $update_data = array(
                'status' => 0,
            );
        } else {
            $update_data = array(
                'status' => 1,
            );
        }

        return $this->where('inspection_ohc_first_aider.id', $id)->update($update_data);
    }

    public function Selectone($id)
    {
        return $this->where('id', $id)->first();
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select(
            'inspection_ohc_first_aider.*',
        );

        if (isset($request->next_review_date) && $request->next_review_date) {

            $query = $query->where('inspection_ohc_first_aider.next_review_date', DBdateformat($request->next_review_date));
        }
        if (isset($request->last_updated_date) && $request->last_updated_date) {
            $query = $query->where('inspection_ohc_first_aider.last_updated_date', DBdateformat($request->last_updated_date));
        }

        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_ohc_first_aider.status',   decryptId($request->status));
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_first_aider.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_first_aider.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_ohc_first_aider.created_at', [$startDate, $endDate]);
        }
        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "rev_date":
                    $query->orderBy('inspection_ohc_first_aider.revision_date', $columnorder);
                    break;
                case "issue_date":
                    $query = $query->orderBy('inspection_ohc_first_aider.issue_date', $columnorder);
                    break;
                case "document_number":
                    $query = $query->orderBy('inspection_ohc_first_aider.doc_no', $columnorder);
                    break;
                case "status":
                    $query = $query->orderBy('inspection_ohc_first_aider.status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_ohc_first_aider.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_ohc_first_aider.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_ohc_first_aider.id', 'DESC');
                    break;
            }
        }


        $query->orderBy('inspection_ohc_first_aider.id', 'DESC');

        return  $query->get();
    }

    // for API  list
    public function listApi()
    {
        $request = request();
        $search = '';

        $query = $this->select('inspection_ohc_first_aider.*');
        $org_total =  $query;

        $user = Auth::user();
        $userRole = string_to_array($user->role);
        $empId = $user->employee_id;



        $org_total_counts = $org_total->count();

        if ($request->search != null || $request->search != '') {
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_ohc_first_aider.next_review_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_ohc_first_aider.last_updated_date', 'LIKE', '%' . $search . '%');
            });
        }


        $data = $query->orderby('inspection_ohc_first_aider.id')->get();
        $first_aider_data = $data->toArray();
        $data_array = [];
        $refined_data = [];


        foreach ($first_aider_data as $index => $data) {
            $data_array['id'] = $data['id'];
            $data_array['next_review_date'] = Displaydateformat($data['next_review_date']);
            $data_array['last_updated_date'] = Displaydateformat($data['last_updated_date']);
            $data_array['status'] = ($data['status'] == 1) ? 'Active' : 'Inactive';
            $data_array['created_at'] = Displaydateformat($data['created_at']);
            $data_array['created_by'] = getUsername($data['created_by']);
            $refined_data[$index] = $data_array;
        }
        return $refined_data;
    }
}
