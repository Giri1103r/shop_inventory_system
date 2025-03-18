<?php

namespace App\Models\Inspection\Ohc;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class WeeklyAmbulance extends Model
{
    protected $table = 'inspection_ohc_weekly_ambulance_inspection_checklist';

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
        $query = $this->select('inspection_ohc_weekly_ambulance_inspection_checklist.*');

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
            $query = $query->where('inspection_ohc_weekly_ambulance_inspection_checklist.doc_no', 'LIKE', '%' . $request->document_number . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->where('inspection_ohc_weekly_ambulance_inspection_checklist.issue_date', 'LIKE', '%' . $request->issue_date . '%');
        }
        if (isset($request->rev_date) && $request->rev_date) {
            $query = $query->where('inspection_ohc_weekly_ambulance_inspection_checklist.revision_date', 'LIKE', '%' . $request->rev_date . '%');
        }
        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_ohc_weekly_ambulance_inspection_checklist.revision_date', 'LIKE', '%' .decryptId( $request->status) . '%');
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "rev_date":
                    $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.revision_date', $columnorder);
                    break;
                case "issue_date":
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.issue_date', $columnorder);
                    break;
                case "document_number":
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.doc_no', $columnorder);
                    break;
                case "status":
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.id', 'DESC');
                    break;
            }
        }


        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.id', 'DESC');

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

    public function store($responses)
    {

        $request = request();

        $insert_array = [
            'checklist' => json_encode($responses),
            'doc_no' => $request->document_no,
            'issue_date' => DBdateformat($request->issue_date),
            'shift' => decryptId($request->shift),
            'location' => decryptId($request->location_id),
            'unit' => decryptId($request->unit_id),
            'next_due' => DBdateformat($request->next_due_on),
            'revision_date' => $request->review_date,
            'date_of_inspection' => DBdateformat($request->date_of_inspection),
            'created_by' => Auth::id(),
        ];

        return self::create($insert_array);
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_ohc_weekly_ambulance_inspection_checklist.*');
        if (isset($request->document_number) && $request->document_number) {
            $query = $query->where('inspection_ohc_weekly_ambulance_inspection_checklist.doc_no', 'LIKE', '%' . $request->document_number . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->where('inspection_ohc_weekly_ambulance_inspection_checklist.issue_date', 'LIKE', '%' . $request->issue_date . '%');
        }
        if (isset($request->rev_date) && $request->rev_date) {
            $query = $query->where('inspection_ohc_weekly_ambulance_inspection_checklist.revision_date', 'LIKE', '%' . $request->rev_date . '%');
        }

        if (!empty($request->status)) {
            $status = decryptId($request->status);

                $query->where('inspection_ohc_weekly_ambulance_inspection_checklist.status', 'LIKE', '%' . $status . '%');

        }


        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "rev_date":
                    $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.revision_date', $columnorder);
                    break;
                case "issue_date":
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.issue_date', $columnorder);
                    break;
                case "document_number":
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.doc_no', $columnorder);
                    break;
                case "status":
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.id', 'DESC');
                    break;
            }
        }

        $query->orderBy('id', 'DESC');

        return  $query->get();
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
    public function WeekambulanceSelectone($id)
    {
        return $this->where('id', $id)->first();
    }
}
