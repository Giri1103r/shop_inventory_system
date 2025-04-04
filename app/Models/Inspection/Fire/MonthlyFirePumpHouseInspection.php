<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class MonthlyFirePumpHouseInspection extends Model
{
    protected $table = 'inspection_fire_monthly_fire_pumphouse';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'document_reference_id',
        'date_of_inspection',
        'shift',
        'unit',
        'resource_code',
        'sr_no',
        'description',
        'remarks',
        'inspection_status',
        'capa_recomendation',
        'capa_remarks',
        'level_one_manager_remarks',
        'level_two_manager_remarks',
        'checked_by',
        'verified_by',
        'approved_by',
        'l1_manager_verified_by',
        'l2_manager_verified_by',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'responses',
        'capa_ehs_remarks'
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_fire_monthly_fire_pumphouse.*', 'inspection_shift_option.*', 'masters_unit.*', 'inspection_fire_monthly_fire_pumphouse.id as inspection_id')
            ->leftJoin('inspection_shift_option', 'inspection_fire_monthly_fire_pumphouse.shift', '=', 'inspection_shift_option.id')
            ->leftJoin('masters_unit', 'inspection_fire_monthly_fire_pumphouse.unit', '=', 'masters_unit.id');
            

        $org_total =  $query;
        $org_total_counts = $org_total->count();



        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];

            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('masters_unit.unit_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_shift_option.shift LIKE "%' . $search . '%"');
            });
        }

    
        if (isset($request->unit) && $request->unit) {
            $query = $query->where('inspection_fire_monthly_fire_pumphouse.unit', 'LIKE', '%' . decryptId($request->unit) . '%');
        }
        if (isset($request->shift) && $request->shift) {
            $query = $query->where('inspection_fire_monthly_fire_pumphouse.shift', 'LIKE', '%' . decryptId($request->shift) . '%');
        }
        if (isset($request->date_of_inspection) && $request->date_of_inspection) {
            $query = $query->where('inspection_fire_monthly_fire_pumphouse.date_of_inspection', 'LIKE', '%' . DBdateformat($request->date_of_inspection) . '%');
        }
        if (isset($request->next_due) && $request->next_due) {
            $query = $query->where('inspection_fire_monthly_fire_pumphouse.next_due', 'LIKE', '%' . DBdateformat($request->next_due) . '%');
        }


        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_fire_monthly_fire_pumphouse.inspection_status', decryptId($request->inspection_status));
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "inspection_status":
                    $query = $query->orderBy('inspection_fire_monthly_fire_pumphouse.inspection_status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_fire_monthly_fire_pumphouse.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_fire_monthly_fire_pumphouse.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_fire_monthly_fire_pumphouse.id', 'DESC');
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

        $mergedResponses = [];

        foreach ($request->checklist as $sub_type_id => $checklist_items) {
            foreach ($checklist_items as $checklist_id => $value) {
                $mergedResponses[$sub_type_id][$checklist_id] = [
                    'response' => $value,
                    'remark' => $request->remarks[$sub_type_id][$checklist_id] ?? null,
                ];
            }
        }

        $responsesJson = json_encode($mergedResponses);

        $responses = $request->checklist;
        $remarks = $request->remarks;
        $respones = json_encode($responses);
        $insert_array = [
            'document_reference_id' => decryptId($request->document_reference_id),
            'date_of_inspection' => DBdateformat($request->inspection_date),
            'shift' => decryptId($request->shift),
            'unit' => decryptId($request->unit_id),
            'resource_code' => $request->resource_code,
            'created_by' => Auth::id(),
            'responses' => $responsesJson,
            'inspection_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
        ];
        return $this->create($insert_array);
    }

    public function selectOne($id)
    {
        return  $this->where('id', $id)->first();
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

    public function UniqueCheck($data)
    {
        $unique =  $this->where('resource_code',  $data)->get();
        if (count($unique) > 0) {
            return false;
        }
        return true;
    }

    public function ExistuniqueCheck($data)
    {
        $unique =  $this->where('resource_code',  $data['category_name'])
            ->where('id', '!=', ($data['id']))
            ->get();

        if (count($unique) > 0) {
            return false;
        }
        return true;
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_fire_monthly_fire_pumphouse.*');
        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('doc_no LIKE "%' . $search . '%"');
                $query->orWhereRaw('issue_date LIKE "%' . $search . '%"');
                $query->orWhereRaw('revision_data LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->document_number) && $request->document_number) {
            $query = $query->where('inspection_fire_monthly_fire_pumphouse.doc_no', 'LIKE', '%' . $request->document_number . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->where('inspection_fire_monthly_fire_pumphouse.issue_date', 'LIKE', '%' . $request->issue_date . '%');
        }
        if (isset($request->rev_date) && $request->rev_date) {
            $query = $query->where('inspection_fire_monthly_fire_pumphouse.revision_data', 'LIKE', '%' . $request->rev_date . '%');
        }
        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_fire_monthly_fire_pumphouse.inspection_status', decryptId($request->inspection_status));
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_fire_monthly_fire_pumphouse'));
    }
}
