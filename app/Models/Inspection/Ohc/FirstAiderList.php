<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FirstAiderList extends Model
{
    protected $table = 'inspection_ohc_first_aider';

    protected $primaryKey = 'id';

    protected $fillable = [
        'doc_no',
        'issue_date',
        'revision_date',
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
            $query = $query->where('inspection_ohc_first_aider.doc_no', $request->document_number );
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->where('inspection_ohc_first_aider.issue_date',  $request->issue_date );
        }
        if (isset($request->rev_date) && $request->rev_date) {
            $query = $query->where('inspection_ohc_first_aider.revision_date',  $request->rev_date );
        }
        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_ohc_first_aider.status',   decryptId($request->status) );
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
            'doc_no' => $request->document_no,
            'issue_date' => DBdateformat($request->issue_date),
            'next_review_date' => !empty($request->next_review_date) ? DBdateformat($request->next_review_date) : null,
            'last_updated_date' => !empty($request->last_updated_date) ? DBdateformat($request->last_updated_date) : null,
            'revision_date' => $request->review_date,
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

        return $this->where('id', $id)->update($update_data);
    }

    public function Selectone($id)
    {
        return $this->where('id', $id)->first();
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_ohc_first_aider.*');
        if (isset($request->document_number) && $request->document_number) {
            $query = $query->where('inspection_ohc_first_aider.doc_no', $request->document_number );
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->where('inspection_ohc_first_aider.issue_date',  $request->issue_date );
        }
        if (isset($request->rev_date) && $request->rev_date) {
            $query = $query->where('inspection_ohc_first_aider.revision_date',  $request->rev_date );
        }
        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_ohc_first_aider.status',   decryptId($request->status) );
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


        $query->orderBy('id', 'DESC');

        return  $query->get();
    }
}
