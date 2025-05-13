<?php

namespace App\Models\Inspection\Fire;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class FireMockDrillInspection extends Model
{
    protected $table = 'inspection_fire_mock_drill_observation';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'document_reference_id',
        'date_of_closure',
        'inspection_date',
        'inspection_status',
        'checked_by',
        'verified_by',
        'approved_by',
        'description',
        'remarks',
        'inspection_status',
        'capa_recomendation',
        'capa_remarks',
        'level_one_manager_remarks',
        'level_two_manager_remarks',
        'capa_ehs_remarks',
        'l1_manager_verified_by',
        'l2_manager_verified_by',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'status',
        'trash',
    ];

    protected $attribute = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_fire_mock_drill_observation.*', 'inspection_static_docno.*', 'inspection_fire_mock_drill_observation.id as inspection_id','inspection_fire_mock_drill_observation.created_by as inspection_created_by')
            ->leftJoin('inspection_static_docno', 'inspection_fire_mock_drill_observation.document_reference_id', '=', 'inspection_static_docno.id');

        if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_OFFICER) || CheckUserRole(ROLE_L1_MANAGER) || CheckUserRole(ROLE_L2_MANAGER)) {
        } else if (CheckUserRole(ROLE_FIRE_ASSOCIATES)) {
            $query->where('inspection_fire_mock_drill_observation.created_by', Auth::id());
        }

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {});
        }
        if (isset($request->inspection_date) && $request->inspection_date) {
            $query = $query->where('inspection_fire_mock_drill_observation.inspection_date',  DBdateformat($request->inspection_date));
        }
        if (isset($request->rev_date) && $request->rev_date) {
            $query = $query->where('inspection_fire_mock_drill_observation.revision_data',  $request->rev_date);
        }

        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_fire_mock_drill_observation.inspection_status', decryptId($request->inspection_status));
        }

         if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_fire_mock_drill_observation.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_fire_mock_drill_observation.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_fire_mock_drill_observation.created_at', [$startDate, $endDate]);
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "revision_data":
                    $query->orderBy('inspection_fire_mock_drill_observation.revision_data', $columnorder);
                    break;
                case "issue_date":
                    $query = $query->orderBy('inspection_fire_mock_drill_observation.issue_date', $columnorder);
                    break;
                case "document_number":
                    $query = $query->orderBy('inspection_fire_mock_drill_observation.document_number', $columnorder);
                    break;
                case "inspection_status":
                    $query = $query->orderBy('inspection_fire_mock_drill_observation.inspection_status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_fire_mock_drill_observation.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_fire_mock_drill_observation.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_fire_mock_drill_observation.id', 'DESC');
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
            'inspection_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
            'created_by' => Auth::id(),
            'checked_by' => Auth::id(),
        );
        return $this->create($data);
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_fire_mock_drill_observation.*', 'inspection_static_docno.*', 'inspection_fire_mock_drill_observation_details.*', 'inspection_static_docno.*', 'inspection_fire_mock_drill_observation.id as fire_id', 'inspection_fire_mock_drill_observation.created_by as checked_by', 'inspection_fire_mock_drill_observation.updated_by as verified_by')
            ->leftJoin('inspection_fire_mock_drill_observation_details', 'inspection_fire_mock_drill_observation.id', '=', 'inspection_fire_mock_drill_observation_details.inspection_id')
            ->leftJoin('inspection_static_docno', 'inspection_fire_mock_drill_observation.document_reference_id', '=', 'inspection_static_docno.id');


        if (isset($request->inspection_date) && $request->inspection_date) {
            $query = $query->where('inspection_fire_mock_drill_observation.inspection_date',  DBdateformat($request->inspection_date));
        }

        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_fire_mock_drill_observation.inspection_status', decryptId($request->inspection_status));
        }

         if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_fire_mock_drill_observation.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_fire_mock_drill_observation.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_fire_mock_drill_observation.created_at', [$startDate, $endDate]);
        }

        $query->orderBy('inspection_fire_mock_drill_observation.id', 'DESC');

        $data = $query->get();

        if ($data) {
            return $data->groupBy('fire_id');
        } else {
            return $data;
        }
    }



    public function EHSOfficerUpdate($id)
    {

        $request = request();
        if ($request->is_passed == 1) {
            $update_array = [
                'verified_by' => Auth::id(),
                'approved_by' => Auth::id(),
                'inspection_status' => INSPECTION_APPROVED,
                'updated_by' => Auth::id(),
                'remarks' => $request->remarks,
                'date_of_closure' => DBdateformat(Carbon::now()),
            ];
            $this->where('id', $id)->update($update_array);
        } else {
            $update_array = [
                'verified_by' => Auth::id(),
                'inspection_status' => WAITING_FOR_CAPA_ACTION,
                'updated_by' => Auth::id(),
                'capa_recomendation' => $request->remarks,
            ];
            $this->where('id', $id)->update($update_array);
        }
    }

    public function capaSubmit($id)
    {
        $request = request();
        $update_array = [
            'capa_remarks' => $request->capa_remarks,
            'updated_by' => Auth::id(),
            'inspection_status' => WAITING_FOR_CAPA_VERIFICATION,
        ];
        $this->where('id', $id)->update($update_array);
    }

    public function capaVerifySubmit($id, $status, $remarks)
    {
        $request = request();
        if ($status == 1) {
            $update_array = [
                'verified_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'inspection_status' => WAITING_FOR_L1_VERIFICATION,
                'capa_ehs_remarks' => $remarks,
            ];
            $this->where('id', $id)->update($update_array);
        } else {
            $update_array = [
                'verified_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'inspection_status' => EHS_OFFICER_REJECTED,
                'capa_ehs_remarks' => $remarks,
            ];
            $this->where('id', $id)->update($update_array);
        }
    }

    public function levelOneManagerSubmit($id, $status, $remarks)
    {
        if ($status == 1) {
            $update_array = [
                'l1_manager_verified_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'inspection_status' => WAITING_FOR_L2_VERIFICATION,
                'level_one_manager_remarks' => $remarks,
            ];
            $this->where('id', $id)->update($update_array);
        } else {
            $update_array = [
                'l1_manager_verified_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'inspection_status' => L1_MANAGER_REJECTED,
                'level_one_manager_remarks' => $remarks,
            ];
            $this->where('id', $id)->update($update_array);
        }
    }

    public function levelTwoManagerSubmit($id, $status, $remarks)
    {
        if ($status == 1) {
            $update_array = [
                'l2_manager_verified_by' => Auth::id(),
                'approved_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'inspection_status' => INSPECTION_APPROVED,
                'level_two_manager_remarks' => $remarks,
                'date_of_closure' => DBdateformat(Carbon::now()),
            ];
            $this->where('id', $id)->update($update_array);
        } else {
            $update_array = [
                'l2_manager_verified_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'inspection_status' => L2_MANAGER_REJECTED,
                'level_two_manager_remarks' => $remarks,
            ];
            $this->where('id', $id)->update($update_array);
        }
    }


    public function selectOne($id)
    {
        return $this->where('id', $id)->where('status', 1)->where('trash', 'NO')->first();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_fire_mock_drill_observation'));
    }
}
