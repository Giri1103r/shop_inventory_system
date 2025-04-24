<?php

namespace App\Models\Inspection\Fire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inspection\InspectionStaticDocno;

class FireCheckListFollowUpObservation extends Model
{
    protected $table = 'inspection_fire_observation';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'inspection_id',
        'sr_no',
        'inspection_type',
        'inspection_id',
        'unit_id',
        'department_id',
        'equipment_name',
        'equipment_code',
        'observation',
        'date',
        'month',
        'observation_status',
        'responsible_person_id',
        'target_date',
        'ehs_verify_by',
        'ehs_verified_date',
        'closed_date',
        'capa_date',
        'capa_status',
        'capa_remarks',
        'ehs_capa_verified_by',
        'ehs_capa_verified_date',
        'ehs_capa_remarks',
        'level_one_manager_remarks',
        'level_two_manager_remarks',
        'verified_by',
        'approved_by',
        'l1_manager_verified_by',
        'l1_manager_verified_date',
        'l2_manager_verified_by',
        'l2_manager_verified_date',
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
        $query = $this->select('inspection_fire_checklist_follow.*');
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

    public function store($inspection_id)
    {
        $request = request();
        $data = [];

        foreach ($request->obs as $index => $obs) {
            $insert_array = [
                'inspection_id' => $inspection_id,
                'sr_no' => $obs['serial_number'] ?? null,
                'unit_id' => $obs['unit_id'] ?? null,
                'department_id' => $obs['department_id'] ?? null,
                'equipment_name' => $obs['equipment_name'] ?? null,
                'equipment_code' => $obs['equipment_code'] ?? null,
                'observation' => $obs['observation'] ?? null,
                'date' => DBdateformat($obs['date']) ?? null,
                'month' => $obs['month'] ?? null,
                'observation_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'created_by' => Auth::id(),
            ];

            $data[] = $this->create($insert_array);
        }

        return $data;
    }

    public function EHSOfficerUpdate($id)
    {
        $request = request();
        $update_array = array(
            'responsible_person_id' => decryptId($request->responsible_person_id),
            'target_date' => DBdateformat($request->target_date),
            'ehs_verified_date' =>  DBdateformat($request->date),
            'ehs_verify_by' => Auth::id(),
        );
        return $this->where('id', $id)->update($update_array);
    }
    public function capaUpdate($id)
    {
        $request = request();
        $update_array = array(
            'closed_date' => DBdateformat($request->closed_date),
            'capa_date' => DBdateformat($request->date),
            'capa_status' => decryptId($request->capa_status),
            'capa_remarks' => $request->capa_remarks,
        );
        return $this->where('id', $id)->update($update_array);
    }

    public function capaVerifySubmit($id, $status, $remarks)
    {
        $request = request();
        $update_array = [
            'ehs_capa_verified_by' => Auth::id(),
            'ehs_capa_verified_date' =>  DBdateformat($request->date),
            'ehs_capa_remarks' => $remarks,
        ];
        $this->where('id', $id)->update($update_array);
    }

    public function levelOneManagerSubmit($id,$remarks)
    {
        $request = request();
        $update_array = [
            'l1_manager_verified_by' => Auth::id(),
            'l1_manager_verified_date' =>  DBdateformat($request->date),
            'level_one_manager_remarks' => $remarks,
        ];
        $this->where('id', $id)->update($update_array);
    }

    public function levelTwoManagerSubmit($id,$remarks)
    {
        $request = request();
        $update_array = [
            'l2_manager_verified_by' => Auth::id(),
            'l2_manager_verified_date' =>  DBdateformat($request->date),
            'level_two_manager_remarks' => $remarks,
        ];
        $this->where('id', $id)->update($update_array);
    }

    public function statusUpdate($id, $observation_status)
    {
        $request = request();
        $update_array = array(
            'observation_status' => $observation_status,
        );
        return $this->where('id', $id)->update($update_array);
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
}
