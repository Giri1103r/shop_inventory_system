<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class HoseBoxInspection extends Model
{
    protected $table = 'inspection_fire_hose_box';

    protected $fillable = [
        'id',
        'document_reference_id',
        'date_of_inspection',
        'location',
        'shift',
        'next_due',
        'observation',
        'unit',
        'frequency',
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
        $query = $this->select('inspection_fire_hose_box.*', 'inspection_shift_option.*', 'masters_unit.*', 'masters_location.*', 'inspection_frequency_option.*', 'inspection_fire_hose_box.id as inspection_id')
            ->leftJoin('masters_location', 'inspection_fire_hose_box.location', '=', 'masters_location.id')
            ->leftJoin('inspection_shift_option', 'inspection_fire_hose_box.shift', '=', 'inspection_shift_option.id')
            ->leftJoin('masters_unit', 'inspection_fire_hose_box.unit', '=', 'masters_unit.id')
            ->leftJoin('inspection_frequency_option', 'inspection_fire_hose_box.frequency', '=', 'inspection_frequency_option.id');

        if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_OFFICER) || CheckUserRole(ROLE_L1_MANAGER) || CheckUserRole(ROLE_L2_MANAGER)) {
        } else if (CheckUserRole(ROLE_FIRE_ASSOCIATES)) {
            $query->where('inspection_fire_hose_box.created_by', Auth::id());
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
            $query = $query->where('inspection_fire_hose_box.location', 'LIKE', '%' . decryptId($request->location) . '%');
        }
        if (isset($request->frequency) && $request->frequency) {
            $query = $query->where('inspection_fire_hose_box.frequency', 'LIKE', '%' . decryptId($request->frequency) . '%');
        }
        if (isset($request->unit) && $request->unit) {
            $query = $query->where('inspection_fire_hose_box.unit', 'LIKE', '%' . decryptId($request->unit) . '%');
        }
        if (isset($request->shift) && $request->shift) {
            $query = $query->where('inspection_fire_hose_box.shift', 'LIKE', '%' . decryptId($request->shift) . '%');
        }
        if (isset($request->location) && $request->location) {
            $query = $query->where('inspection_fire_hose_box.location', 'LIKE', '%' . decryptId($request->location) . '%');
        }
        if (isset($request->frequency) && $request->frequency) {
            $query = $query->where('inspection_fire_hose_box.frequency', 'LIKE', '%' . decryptId($request->frequency) . '%');
        }
        if (isset($request->inspection_date) && $request->inspection_date) {
            $query = $query->where('inspection_fire_hose_box.date_of_inspection', 'LIKE', '%' . DBdateformat($request->inspection_date) . '%');
        }
        if (isset($request->next_due) && $request->next_due) {
            $query = $query->where('inspection_fire_hose_box.next_due', 'LIKE', '%' . DBdateformat($request->next_due) . '%');
        }
        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_fire_hose_box.inspection_status', decryptId($request->inspection_status));
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_fire_hose_box.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_fire_hose_box.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_fire_hose_box.created_at', [$startDate, $endDate]);
        }

        $query = $query->orderBy('inspection_fire_hose_box.id', 'DESC');

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
        $search = $request->input('search', '');

        $query = $this->select('inspection_fire_hose_box.*', 'inspection_shift_option.*', 'masters_unit.*', 'masters_location.*', 'inspection_frequency_option.*', 'inspection_fire_hose_box.id as inspection_id', 'inspection_static_docno.*')
            ->leftJoin('masters_location', 'inspection_fire_hose_box.location', '=', 'masters_location.id')
            ->leftJoin('inspection_shift_option', 'inspection_fire_hose_box.shift', '=', 'inspection_shift_option.id')
            ->leftJoin('masters_unit', 'inspection_fire_hose_box.unit', '=', 'masters_unit.id')
            ->leftJoin('inspection_static_docno', 'inspection_fire_hose_box.document_reference_id', '=', 'inspection_static_docno.id')
            ->leftJoin('inspection_frequency_option', 'inspection_fire_hose_box.frequency', '=', 'inspection_frequency_option.id');


        if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_OFFICER) || CheckUserRole(ROLE_L1_MANAGER) || CheckUserRole(ROLE_L2_MANAGER)) {
        } else if (CheckUserRole(ROLE_FIRE_ASSOCIATES)) {
            $query->where('inspection_fire_hose_box.created_by', Auth::id());
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->orWhereRaw('masters_location.location_name LIKE "%' . $search . '%"')
                    ->orWhereRaw('masters_unit.unit_name LIKE "%' . $search . '%"')
                    ->orWhereRaw('inspection_shift_option.shift LIKE "%' . $search . '%"')
                    ->orWhereRaw('inspection_frequency_option.frequency_name LIKE "%' . $search . '%"');
            });
        }

        $paginatedData = $query->orderBy('inspection_fire_hose_box.id', 'DESC')->paginate($perPage);

        $refined_data = [];
        foreach ($paginatedData as $data) {
            $data->date_of_inspection = Displaydateformat($data->date_of_inspection);
            $data->issue_date = Displaydateformat($data->issue_date);
            $data->next_due = Displaydateformat($data->next_due);
            $data->inspection_status = GetStatusValue($data->inspection_status);
            $data->status = ($data->status == 1) ? 'Active' : 'In-Active';
            $data->created_by = getUsername($data->created_by);
            $data->created_at = Displaydateformat($data->created_at);
            $refined_data[] = $data;
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
            'document_reference_id' => decryptId($request->document_reference_id),
            'date_of_inspection' => DBdateformat($request->inspection_date),
            'location' => decryptId($request->location_id),
            'shift' => decryptId($request->shift_id),
            'next_due' => DBdateformat($request->next_due),
            'observation' => decryptId($request->observation),
            'unit' => decryptId($request->unit_id),
            'frequency' => decryptId($request->frequency_id),
            'inspection_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
            'created_by' => Auth::id(),
            'checked_by' => Auth::id(),
        );

        return $this->create($data);
    }

    public function storeApi()
    {
        $request = request();

        $data = array(
            'document_reference_id' => $request->document_reference_id,
            'date_of_inspection' => DBdateformat($request->inspection_date),
            'location' => $request->location_id,
            'shift' => $request->shift_id,
            'next_due' => DBdateformat($request->next_due),
            'observation' => $request->observation,
            'unit' => $request->unit_id,
            'frequency' => $request->frequency_id,
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
        $query = $this->select('inspection_fire_hose_box.*', 'inspection_shift_option.*', 'masters_unit.*', 'masters_location.*', 'inspection_frequency_option.*', 'inspection_fire_hose_box.id as fire_id', 'inspection_fire_hose_box.created_by as checked_by', 'inspection_static_docno.*', 'inspection_fire_hose_box_details.*')
            ->leftJoin('masters_location', 'inspection_fire_hose_box.location', '=', 'masters_location.id')
            ->leftJoin('inspection_shift_option', 'inspection_fire_hose_box.shift', '=', 'inspection_shift_option.id')
            ->leftJoin('masters_unit', 'inspection_fire_hose_box.unit', '=', 'masters_unit.id')
            ->leftJoin('inspection_static_docno', 'inspection_fire_hose_box.document_reference_id', '=', 'inspection_static_docno.id')
            ->leftJoin('inspection_fire_hose_box_details', 'inspection_fire_hose_box.id', '=', 'inspection_fire_hose_box_details.inspection_id')
            ->leftJoin('inspection_frequency_option', 'inspection_fire_hose_box.frequency', '=', 'inspection_frequency_option.id');

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
            $query = $query->where('inspection_fire_hose_box.location', 'LIKE', '%' . decryptId($request->location) . '%');
        }
        if (isset($request->frequency) && $request->frequency) {
            $query = $query->where('inspection_fire_hose_box.frequency', 'LIKE', '%' . decryptId($request->frequency) . '%');
        }
        if (isset($request->unit) && $request->unit) {
            $query = $query->where('inspection_fire_hose_box.unit', 'LIKE', '%' . decryptId($request->unit) . '%');
        }
        if (isset($request->shift) && $request->shift) {
            $query = $query->where('inspection_fire_hose_box.shift', 'LIKE', '%' . decryptId($request->shift) . '%');
        }
        if (isset($request->location) && $request->location) {
            $query = $query->where('inspection_fire_hose_box.location', 'LIKE', '%' . decryptId($request->location) . '%');
        }
        if (isset($request->frequency) && $request->frequency) {
            $query = $query->where('inspection_fire_hose_box.frequency', 'LIKE', '%' . decryptId($request->frequency) . '%');
        }
        if (isset($request->inspection_date) && $request->inspection_date) {
            $query = $query->where('inspection_fire_hose_box.date_of_inspection', 'LIKE', '%' . DBdateformat($request->inspection_date) . '%');
        }
        if (isset($request->next_due) && $request->next_due) {
            $query = $query->where('inspection_fire_hose_box.next_due', 'LIKE', '%' . DBdateformat($request->next_due) . '%');
        }
        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_fire_hose_box.inspection_status', decryptId($request->inspection_status));
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_fire_hose_box.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_fire_hose_box.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_fire_hose_box.created_at', [$startDate, $endDate]);
        }
        $query->orderBy('inspection_fire_hose_box.id', 'DESC');

        return  $query->get()->groupBy('inspection_id');
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


    public function selectOne($id)
    {
        return $this->where('id', $id)->where('status', 1)->where('trash', 'NO')->first();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_fire_hose_box'));
    }
}
