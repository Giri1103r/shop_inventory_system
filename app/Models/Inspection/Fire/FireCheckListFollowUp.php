<?php

namespace App\Models\Inspection\Fire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inspection\InspectionStaticDocno;
use App\Scopes\TrashScope;

class FireCheckListFollowUp extends Model
{
    protected $table = 'inspection_fire_checklist_follow';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'inspection_category',
        'inspection_type',
        'inspection_id',
        'observation_id',
        'document_reference_id',
        'date_of_inspection',
        'inspection_status',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',

    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_fire_checklist_follow.*', 'inspection_fire_observation.*', 'inspection_fire_checklist_follow.id as inspectionid', 'inspection_fire_observation.id as observationid')->leftjoin('inspection_fire_observation', 'inspection_fire_observation.inspection_id', '=', 'inspection_fire_checklist_follow.id');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('document_number LIKE "%' . $search . '%"');
                $query->orWhereRaw('issue_date LIKE "%' . $search . '%"');
                $query->orWhereRaw('revision_data LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->document_number) && $request->document_number) {
            $query = $query->where('inspection_fire_checklist_follow.document_number', 'LIKE', '%' . $request->document_number . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->where('inspection_fire_checklist_follow.issue_date', 'LIKE', '%' . $request->issue_date . '%');
        }
        if (isset($request->rev_date) && $request->rev_date) {
            $query = $query->where('inspection_fire_checklist_follow.revision_data', 'LIKE', '%' . $request->rev_date . '%');
        }

        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_fire_checklist_follow.inspection_status', decryptId($request->inspection_status));
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "revision_data":
                    $query->orderBy('inspection_fire_checklist_follow.revision_data', $columnorder);
                    break;
                case "issue_date":
                    $query = $query->orderBy('inspection_fire_checklist_follow.issue_date', $columnorder);
                    break;
                case "document_number":
                    $query = $query->orderBy('inspection_fire_checklist_follow.document_number', $columnorder);
                    break;
                case "inspection_status":
                    $query = $query->orderBy('inspection_fire_checklist_follow.inspection_status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_fire_checklist_follow.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_fire_checklist_follow.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_fire_checklist_follow.id', 'DESC');
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
            'inspection_type' => decryptId($request->inspection_type),
            'inspection_id' => decryptId($request->inspection_id),
            'document_reference_id' => decryptId($request->document_reference_id),
            'date_of_inspection' => DBdateformat($request->date_of_inspection),
            'inspection_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
            'created_by' => Auth::id(),
        );

        return $this->create($data);
    }


    public function selectOne($id, $observationid)
    {
        $data = $this->select('inspection_fire_checklist_follow.*', 'inspection_fire_observation.*', 'inspection_static_docno.*', 'inspection_fire_checklist_follow.id as inspectionid', 'inspection_fire_observation.id as observationid', 'inspection_fire_observation_whywhy.*', 'inspection_fire_signatureupload.file_path')
            ->leftjoin('inspection_fire_observation', 'inspection_fire_observation.inspection_id', '=', 'inspection_fire_checklist_follow.id')
            ->leftjoin('inspection_static_docno', 'inspection_static_docno.id', '=', 'inspection_fire_checklist_follow.document_reference_id')
            ->leftjoin('inspection_fire_observation_whywhy', 'inspection_fire_observation_whywhy.observation_id', '=', 'inspection_fire_observation.id')
            ->leftjoin('inspection_fire_signatureupload', 'inspection_fire_signatureupload.inspection_id', '=', 'inspection_fire_observation.id')
            ->where(function ($query) {
                $query->where('inspection_fire_observation_whywhy.status', 1)
                    ->orWhereNull('inspection_fire_observation_whywhy.status');
            })

            ->where('inspection_fire_checklist_follow.status', 1)
            ->where('inspection_fire_checklist_follow.id', $id)
            ->where('inspection_fire_observation.id', $observationid)->first();
        return $data;
    }


    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_fire_checklist_follow.*');
        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('document_number LIKE "%' . $search . '%"');
                $query->orWhereRaw('issue_date LIKE "%' . $search . '%"');
                $query->orWhereRaw('revision_data LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->document_number) && $request->document_number) {
            $query = $query->where('inspection_fire_checklist_follow.document_number', 'LIKE', '%' . $request->document_number . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->where('inspection_fire_checklist_follow.issue_date', 'LIKE', '%' . $request->issue_date . '%');
        }
        if (isset($request->rev_date) && $request->rev_date) {
            $query = $query->where('inspection_fire_checklist_follow.revision_data', 'LIKE', '%' . $request->rev_date . '%');
        }

        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_fire_checklist_follow.inspection_status', decryptId($request->inspection_status));
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_fire_checklist_follow'));

        static::created(function ($model) {

            $uniqueId = 'OBSERVATION-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['observation_id' => $uniqueId]);
        });
    }
}
