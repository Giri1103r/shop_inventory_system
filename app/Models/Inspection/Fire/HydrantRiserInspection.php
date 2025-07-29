<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class HydrantRiserInspection extends Model
{
    protected $table = 'inspection_fire_hydrant_riser';

    protected $fillable = [
        'id',
        'document_reference_id',
        'date_of_inspection',
        'location',
        'exact_location',
        'shift_id',
        'next_due',
        'observation_needed',
        'unit',
        'frequency',
        'checked_by',
        'verified_by',
        'approved_by',
        'description',
        'remarks',
        'is_passed',
        'inspection_status',
        'capa_recomendation',
        'capa_remarks',
        'level_one_manager_remarks',
        'level_two_manager_remarks',
        'capa_ehs_remarks',
        'l1_manager_verified_by',
        'l2_manager_verified_by',
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
        $query = $this->select('inspection_fire_hydrant_riser.*', 'inspection_shift_option.*', 'masters_unit.*', 'masters_location.*', 'inspection_frequency_option.*', 'inspection_fire_hydrant_riser.id as fire_hydrant_riser_id')
            ->leftJoin('masters_location', 'inspection_fire_hydrant_riser.location', '=', 'masters_location.id')
            ->leftJoin('inspection_shift_option', 'inspection_fire_hydrant_riser.shift_id', '=', 'inspection_shift_option.id')
            ->leftJoin('masters_unit', 'inspection_fire_hydrant_riser.unit', '=', 'masters_unit.id')
            ->leftJoin('inspection_frequency_option', 'inspection_fire_hydrant_riser.frequency', '=', 'inspection_frequency_option.id')
            ->leftJoin('inspection_static_docno', 'inspection_fire_hydrant_riser.document_reference_id', '=', 'inspection_static_docno.id');

        if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_OFFICER) || CheckUserRole(ROLE_L1_MANAGER) || CheckUserRole(ROLE_L2_MANAGER)) {
        } else if (CheckUserRole(ROLE_FIRE_ASSOCIATES)) {
            $query->where('inspection_fire_hydrant_riser.created_by', Auth::id());
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

        if (isset($request->location) && $request->location) {
            $query = $query->where('inspection_fire_hydrant_riser.location', 'LIKE', '%' . decryptId($request->location) . '%');
        }
        if (isset($request->frequency) && $request->frequency) {
            $query = $query->where('inspection_fire_hydrant_riser.frequency', 'LIKE', '%' . decryptId($request->frequency) . '%');
        }
        if (isset($request->unit) && $request->unit) {
            $query = $query->where('inspection_fire_hydrant_riser.unit', 'LIKE', '%' . decryptId($request->unit) . '%');
        }
        if (isset($request->shift) && $request->shift) {
            $query = $query->where('inspection_fire_hydrant_riser.shift_id', 'LIKE', '%' . decryptId($request->shift) . '%');
        }
        if (isset($request->inspection_date) && $request->inspection_date) {
            $query = $query->where('inspection_fire_hydrant_riser.date_of_inspection', DBdateformat($request->inspection_date));
        }
        if (isset($request->next_due) && $request->next_due) {
            $query = $query->where('inspection_fire_hydrant_riser.next_due', 'LIKE', '%' . DBdateformat($request->next_due) . '%');
        }
        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_fire_hydrant_riser.inspection_status', 'LIKE', '%' . decryptId($request->inspection_status) . '%');
        }

        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_fire_hydrant_riser.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_fire_hydrant_riser.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_fire_hydrant_riser.created_at', [$startDate, $endDate]);
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "inspection_status":
                    $query = $query->orderBy('inspection_fire_hydrant_riser.inspection_status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_fire_hydrant_riser.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_fire_hydrant_riser.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_fire_hydrant_riser.id', 'DESC');
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

    public function listApi()
    {
        $request = request();
        $perPage = $request->input('per_page', 10);
        $search = '';

        $query = $this->select('inspection_fire_hydrant_riser.*', 'inspection_shift_option.*', 'masters_unit.*', 'masters_location.*', 'inspection_frequency_option.*', 'inspection_fire_hydrant_riser.id as fire_hydrant_riser_id')
            ->leftJoin('masters_location', 'inspection_fire_hydrant_riser.location', '=', 'masters_location.id')
            ->leftJoin('inspection_shift_option', 'inspection_fire_hydrant_riser.shift_id', '=', 'inspection_shift_option.id')
            ->leftJoin('masters_unit', 'inspection_fire_hydrant_riser.unit', '=', 'masters_unit.id')
            ->leftJoin('inspection_frequency_option', 'inspection_fire_hydrant_riser.frequency', '=', 'inspection_frequency_option.id')
            ->leftJoin('inspection_static_docno', 'inspection_fire_hydrant_riser.document_reference_id', '=', 'inspection_static_docno.id');

        if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_OFFICER) || CheckUserRole(ROLE_L1_MANAGER) || CheckUserRole(ROLE_L2_MANAGER)) {
        } else if (CheckUserRole(ROLE_FIRE_ASSOCIATES)) {
            $query->where('inspection_fire_hydrant_riser.created_by', Auth::id());
        }

        $org_total =  $query;
        $org_total_counts = $org_total->count();


        if (isset($request->search) && isset($request->search) && $request->search != '') {
            $search = $request->search;

            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('masters_location.location_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('masters_unit.unit_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_shift_option.shift LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_frequency_option.frequency_name LIKE "%' . $search . '%"');
            });
        }
        $paginatedData = $query->orderBy('inspection_fire_hydrant_riser.id', 'DESC')->paginate($perPage);

        $inspection_data = $paginatedData->toArray();


        if (empty($inspection_data['data'])) {
            return $this->sendError('No records found.', [], 404);
        }

        $refined_data = [];
        foreach ($inspection_data['data'] as $index => $datas) {

            $data_array = [];
            $data_array['id'] = $datas['fire_hydrant_riser_id'];
            $data_array['date_of_inspection'] = Displaydateformat($datas['date_of_inspection']);
            $data_array['next_due'] = Displaydateformat($datas['next_due']);
            $data_array['location_name'] = $datas['location_name'];
            $data_array['shift'] = $datas['shift'];
            $data_array['unit_name'] = $datas['unit_name'];
            $data_array['inspection_status'] = GetStatusValue($datas['inspection_status']);

            $refined_data[$index] = $data_array;
        }

        $response = [
            'per_page' => $paginatedData->perPage(),
            'current_page' => $paginatedData->currentPage(),
            'from' => $paginatedData->firstItem(),
            'to' => $paginatedData->lastItem(),
            'total' => $paginatedData->total(),
            'total_page' => $paginatedData->lastPage(),
            'list' => $refined_data,
        ];

        return $response;
    }

    public function store()
    {
        $request = request();

        $data = array(
            'doc_no' => $request->doc_no,
            'document_reference_id' => decryptId($request->document_reference_id),
            'date_of_inspection' => DBdateformat($request->inspection_date),
            'location' => decryptId($request->location_id),
            'exact_location' => $request->exact_location,
            'shift_id' => decryptId($request->shift_id),
            'next_due' => DBdateformat($request->next_due),
            // 'observation' => $request->observation,
            'observation_needed' => decryptId($request->observation_needed),
            'unit' => decryptId($request->unit_id),
            'frequency' => decryptId($request->frequency_id),
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
        $query = $this->select('inspection_fire_hydrant_riser.*', 'inspection_shift_option.*', 'masters_unit.*', 'masters_location.*', 'inspection_frequency_option.*', 'inspection_fire_hydrant_riser_details.*', 'inspection_static_docno.*', 'inspection_fire_hydrant_riser.id as hydrant_parent_id', 'inspection_fire_hydrant_riser.updated_by as hydrant_updated_by')
            ->leftJoin('inspection_fire_hydrant_riser_details', 'inspection_fire_hydrant_riser_details.inspection_id', '=', 'inspection_fire_hydrant_riser.id')
            ->leftJoin('masters_location', 'inspection_fire_hydrant_riser.location', '=', 'masters_location.id')
            ->leftJoin('inspection_shift_option', 'inspection_fire_hydrant_riser.shift_id', '=', 'inspection_shift_option.id')
            ->leftJoin('masters_unit', 'inspection_fire_hydrant_riser.unit', '=', 'masters_unit.id')
            ->leftJoin('inspection_frequency_option', 'inspection_fire_hydrant_riser.frequency', '=', 'inspection_frequency_option.id')
            ->leftJoin('inspection_static_docno', 'inspection_fire_hydrant_riser.document_reference_id', '=', 'inspection_static_docno.id');


        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('masters_location.location_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('masters_unit.unit_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_shift_option.shift LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_frequency_option.frequency_name LIKE "%' . $search . '%"');
            });
        }


        if (isset($request->location) && $request->location) {
            $query = $query->where('inspection_fire_hydrant_riser.location', 'LIKE', '%' . decryptId($request->location) . '%');
        }
        if (isset($request->frequency) && $request->frequency) {
            $query = $query->where('inspection_fire_hydrant_riser.frequency', 'LIKE', '%' . decryptId($request->frequency) . '%');
        }
        if (isset($request->unit) && $request->unit) {
            $query = $query->where('inspection_fire_hydrant_riser.unit', 'LIKE', '%' . decryptId($request->unit) . '%');
        }
        if (isset($request->shift) && $request->shift) {
            $query = $query->where('inspection_fire_hydrant_riser.shift_id', 'LIKE', '%' . decryptId($request->shift) . '%');
        }

        if (isset($request->inspection_date) && $request->inspection_date) {
            $query = $query->where('inspection_fire_hydrant_riser.date_of_inspection', 'LIKE', '%' . DBdateformat($request->inspection_date) . '%');
        }
        if (isset($request->next_due) && $request->next_due) {
            $query = $query->where('inspection_fire_hydrant_riser.next_due', 'LIKE', '%' . DBdateformat($request->next_due) . '%');
        }
        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_fire_hydrant_riser.inspection_status', 'LIKE', '%' . decryptId($request->inspection_status) . '%');
        }

        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_fire_hydrant_riser.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_fire_hydrant_riser.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_fire_hydrant_riser.created_at', [$startDate, $endDate]);
        }

        $query->orderBy('inspection_fire_hydrant_riser.id', 'DESC');
        $results = $query->get();
        $query = $results->groupBy('inspection_id');

        return  $query;
    }



    public function EHSOfficerUpdate($id)
    {

        $request = request();
        if ($request->is_passed == 1) {
            $update_array = [
                'verified_by' => Auth::id(),
                'approved_by' => Auth::id(),
                'is_passed' => $request->is_passed,
                'inspection_status' => INSPECTION_APPROVED,
                'updated_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->where('id', $id)->update($update_array);
        } else {
            $update_array = [
                'verified_by' => Auth::id(),
                'is_passed' => $request->is_passed,
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


    public function selectOne($id)
    {
        return $this->where('id', $id)->where('status', 1)->where('trash', 'NO')->first();
    }

    public function getInspectionDetails($id)
    {
        $data = $this->select('inspection_fire_hydrant_riser.*', 'inspection_static_docno.doc_no', 'inspection_static_docno.issue_date', 'inspection_static_docno.rev_dt')
            ->leftJoin('inspection_static_docno', 'inspection_static_docno.id', '=', 'inspection_fire_hydrant_riser.document_reference_id')
            ->where('inspection_fire_hydrant_riser.id', $id)
            ->first();

        return $data;
    }

    public function storeApi()
    {
        $request = request();
        $data = array(
            'doc_no' => $request->doc_no,
            'document_reference_id' => $request->document_reference_id,
            'date_of_inspection' => DBdateformat($request->inspection_date),
            'location' => $request->location_id,
            'shift_id' => $request->shift_id,
            'next_due' => DBdateformat($request->next_due),
            // 'observation' => $request->observation,
            'observation_needed' => $request->observation_needed,
            'unit' => $request->unit_id,
            'frequency' => $request->frequency_id,
            'inspection_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
            'created_by' => Auth::id(),
            'checked_by' => Auth::id(),
        );

        return $this->create($data);
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_fire_hydrant_riser'));
    }
}
