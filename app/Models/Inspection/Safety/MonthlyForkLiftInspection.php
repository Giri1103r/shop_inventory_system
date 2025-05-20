<?php

namespace App\Models\Inspection\Safety;

use App\Scopes\TrashScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class MonthlyForkLiftInspection extends Model
{
    protected $table = 'inspection_forklift_inpsection_monthly';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'document_reference_id',
        'date_of_inspection',
        'location',
        'shift',
        'next_due',
        'unit',
        'frequency',
        'identification_no',
        'forklift_type',
        'capacity',
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
        'capa_ehs_remarks',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'responses',

    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_forklift_inpsection_monthly.*', 'inspection_shift_option.*', 'masters_unit.*', 'masters_location.*', 'inspection_frequency_option.*', 'inspection_forklift_inpsection_monthly.id as inspection_id', 'inspection_forklift_inpsection_monthly.created_at as inspection_created_at')
            ->leftJoin('masters_location', 'inspection_forklift_inpsection_monthly.location', '=', 'masters_location.id')
            ->leftJoin('inspection_shift_option', 'inspection_forklift_inpsection_monthly.shift', '=', 'inspection_shift_option.id')
            ->leftJoin('masters_unit', 'inspection_forklift_inpsection_monthly.unit', '=', 'masters_unit.id')
            ->leftJoin('inspection_frequency_option', 'inspection_forklift_inpsection_monthly.frequency', '=', 'inspection_frequency_option.id')
            ->leftJoin('inspection_static_docno', 'inspection_forklift_inpsection_monthly.document_reference_id', '=', 'inspection_static_docno.id');


        if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_OFFICER) || CheckUserRole(ROLE_L1_MANAGER) || CheckUserRole(ROLE_L2_MANAGER)) {
        } else if (CheckUserRole(ROLE_FIRE_ASSOCIATES)) {
            $query->where('inspection_forklift_inpsection_monthly.created_by', Auth::id());
        }

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('masters_location.location_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('masters_unit.unit_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_shift_option.shift LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_frequency_option.frequency_name LIKE "%' . $search . '%"');
            });
        }
        if ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_forklift_inpsection_monthly.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_forklift_inpsection_monthly.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_forklift_inpsection_monthly.created_at', [$startDate, $endDate]);
        }
        if (isset($request->location) && $request->location) {
            $query = $query->where('inspection_forklift_inpsection_monthly.location', 'LIKE', '%' . decryptId($request->location) . '%');
        }
        if (isset($request->frequency) && $request->frequency) {
            $query = $query->where('inspection_forklift_inpsection_monthly.frequency', 'LIKE', '%' . decryptId($request->frequency) . '%');
        }
        if (isset($request->unit) && $request->unit) {
            $query = $query->where('inspection_forklift_inpsection_monthly.unit', 'LIKE', '%' . decryptId($request->unit) . '%');
        }
        if (isset($request->shift) && $request->shift) {
            $query = $query->where('inspection_forklift_inpsection_monthly.shift', 'LIKE', '%' . decryptId($request->shift) . '%');
        }
        if (isset($request->inspection_date) && $request->inspection_date) {
            $query = $query->where('inspection_forklift_inpsection_monthly.date_of_inspection', 'LIKE', '%' . DBdateformat($request->inspection_date) . '%');
        }
        if (isset($request->next_due) && $request->next_due) {
            $query = $query->where('inspection_forklift_inpsection_monthly.next_due', 'LIKE', '%' . DBdateformat($request->next_due) . '%');
        }
        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_forklift_inpsection_monthly.inspection_status', 'LIKE', '%' . decryptId($request->inspection_status) . '%');
        }


        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "inspection_status":
                    $query = $query->orderBy('inspection_forklift_inpsection_monthly.inspection_status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_forklift_inpsection_monthly.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_forklift_inpsection_monthly.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_forklift_inpsection_monthly.id', 'DESC');
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
        $responses = $request->checklist;

        foreach ($responses as $index => $respones) {
            foreach ($respones as $question => $value) {
                $encoded_data[$question] = [
                    'question_id' => $question,
                    'answer' => $value,
                    'remarks' => $request->remarks[$index][$question],
                ];
            }
        }
        $respones = json_encode($encoded_data);
        $insert_array = [
            'document_reference_id' => decryptId($request->document_reference_id),
            'date_of_inspection' => DBdateformat($request->inspection_date),
            'location' => decryptId($request->location_id),
            'shift' => decryptId($request->shift_id),
            'next_due' => DBdateformat($request->next_due),
            'unit' => decryptId($request->unit_id),
            'frequency' => decryptId($request->frequency_id),
            'identification_no' => $request->identification_no,
            'forklift_type' => decryptId($request->forklift_type),
            'capacity' => $request->capacity,
            'created_by' => Auth::id(),
            'responses' => $respones,
            'inspection_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
        ];
        return $this->create($insert_array);
    }

    public function store_api()
    {
        $request = request();

        $responses = $request->checklist;

        foreach ($responses as $index => $respones) {
            foreach ($respones as $question => $value) {
                $encoded_data[$question] = [
                    'question_id' => $question,
                    'answer' => $value,
                    'remarks' => $request->remarks[$index][$question],
                ];
            }
        }
        $respones = json_encode($encoded_data);
        $insert_array = [
            'document_reference_id' => ($request->document_reference_id),
            'date_of_inspection' => DBdateformat($request->inspection_date),
            'location' => ($request->location_id),
            'shift' => ($request->shift_id),
            'next_due' => DBdateformat($request->next_due),
            'unit' => ($request->unit_id),
            'frequency' => ($request->frequency_id),
            'identification_no' => $request->identification_no,
            'forklift_type' => ($request->forklift_type),
            'capacity' => $request->capacity,
            'created_by' => Auth::id(),
            'responses' => $respones,
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

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_forklift_inpsection_monthly.*', 'inspection_shift_option.*', 'masters_unit.*', 'inspection_static_docno.*', 'masters_location.*', 'inspection_frequency_option.*', 'inspection_forklift_type.*', 'inspection_forklift_inpsection_monthly.id as inspection_id', 'inspection_forklift_inpsection_monthly.created_by as checked_by')
            ->leftJoin('masters_location', 'inspection_forklift_inpsection_monthly.location', '=', 'masters_location.id')
            ->leftJoin('inspection_shift_option', 'inspection_forklift_inpsection_monthly.shift', '=', 'inspection_shift_option.id')
            ->leftJoin('masters_unit', 'inspection_forklift_inpsection_monthly.unit', '=', 'masters_unit.id')
            ->leftJoin('inspection_frequency_option', 'inspection_forklift_inpsection_monthly.frequency', '=', 'inspection_frequency_option.id')
            ->leftJoin('inspection_static_docno', 'inspection_forklift_inpsection_monthly.document_reference_id', '=', 'inspection_static_docno.id')
            ->leftJoin('inspection_forklift_type', 'inspection_forklift_inpsection_monthly.forklift_type', '=', 'inspection_forklift_type.id');

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('masters_location.location_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('masters_unit.unit_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_shift_option.shift LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_frequency_option.frequency_name LIKE "%' . $search . '%"');
            });
        }

        if ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_forklift_inpsection_monthly.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_forklift_inpsection_monthly.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_forklift_inpsection_monthly.created_at', [$startDate, $endDate]);
        }
        if (isset($request->location) && $request->location) {
            $query = $query->where('inspection_forklift_inpsection_monthly.location', 'LIKE', '%' . decryptId($request->location) . '%');
        }
        if (isset($request->frequency) && $request->frequency) {
            $query = $query->where('inspection_forklift_inpsection_monthly.frequency', 'LIKE', '%' . decryptId($request->frequency) . '%');
        }
        if (isset($request->unit) && $request->unit) {
            $query = $query->where('inspection_forklift_inpsection_monthly.unit', 'LIKE', '%' . decryptId($request->unit) . '%');
        }
        if (isset($request->shift) && $request->shift) {
            $query = $query->where('inspection_forklift_inpsection_monthly.shift', 'LIKE', '%' . decryptId($request->shift) . '%');
        }
        if (isset($request->inspection_date) && $request->inspection_date) {
            $query = $query->where('inspection_forklift_inpsection_monthly.date_of_inspection', 'LIKE', '%' . DBdateformat($request->inspection_date) . '%');
        }
        if (isset($request->next_due) && $request->next_due) {
            $query = $query->where('inspection_forklift_inpsection_monthly.next_due', 'LIKE', '%' . DBdateformat($request->next_due) . '%');
        };
        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_forklift_inpsection_monthly.inspection_status', 'LIKE', '%' . decryptId($request->inspection_status) . '%');
        }

        $query->orderBy('inspection_forklift_inpsection_monthly.id', 'DESC');

        return  $query->get();
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_forklift_inpsection_monthly'));
    }
}
