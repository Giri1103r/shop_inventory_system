<?php

namespace App\Models\Inspection\Fire;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class CartridgeTypeFireExtinguisher extends Model
{
    protected $table = 'inspection_cartridge_type_fire_extinguisher';

    protected $fillable = [
        'id',
        'document_reference_id',
        'observation_needed',
        'inspection_date',
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
        'is_passed',
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
        'ehs_officer_verified_at',
        'l1_manager_updated_at',
        'l2_manager_updated_at',
        'fire_associate_updated_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];


    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select(
            'inspection_cartridge_type_fire_extinguisher.*',
            'inspection_shift_option.*',
            'inspection_static_docno.*',
            'masters_unit.*',
            'masters_location.*',
            'inspection_frequency_option.*',
            'inspection_cartridge_type_fire_extinguisher.id as fire_co_type_id'
        )
            ->leftJoin('masters_location', 'inspection_cartridge_type_fire_extinguisher.location', '=', 'masters_location.id')
            ->leftJoin('inspection_shift_option', 'inspection_cartridge_type_fire_extinguisher.shift', '=', 'inspection_shift_option.id')
            ->leftJoin('masters_unit', 'inspection_cartridge_type_fire_extinguisher.unit', '=', 'masters_unit.id')
            ->leftJoin('inspection_frequency_option', 'inspection_cartridge_type_fire_extinguisher.frequency', '=', 'inspection_frequency_option.id')
            ->leftJoin('inspection_static_docno', 'inspection_cartridge_type_fire_extinguisher.document_reference_id', '=', 'inspection_static_docno.id');

        if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_OFFICER) || CheckUserRole(ROLE_L1_MANAGER) || CheckUserRole(ROLE_L2_MANAGER)) {
        } else if (CheckUserRole(ROLE_FIRE_ASSOCIATES)) {
            $query->where('inspection_cartridge_type_fire_extinguisher.created_by', Auth::id());
        }

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];

            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('masters_location.location_name LIKE "%' . $search . '%"');
                $query->orWhereRaw("DATE_FORMAT(inspection_cartridge_type_fire_extinguisher.inspection_date, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
                $query->orWhereRaw("DATE_FORMAT(inspection_cartridge_type_fire_extinguisher.next_due, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
                $query->orWhereRaw('masters_unit.unit_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_shift_option.shift LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_frequency_option.frequency_name LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->location) && $request->location) {
            $query = $query->where('inspection_cartridge_type_fire_extinguisher.location', 'LIKE', '%' . decryptId($request->location) . '%');
        }
        if (isset($request->frequency) && $request->frequency) {
            $query = $query->where('inspection_cartridge_type_fire_extinguisher.frequency', 'LIKE', '%' . decryptId($request->frequency) . '%');
        }
        if (isset($request->unit) && $request->unit) {
            $query = $query->where('inspection_cartridge_type_fire_extinguisher.unit', 'LIKE', '%' . decryptId($request->unit) . '%');
        }
        if (isset($request->shift) && $request->shift) {
            $query = $query->where('inspection_cartridge_type_fire_extinguisher.shift', 'LIKE', '%' . decryptId($request->shift) . '%');
        }
        if (isset($request->location) && $request->location) {
            $query = $query->where('inspection_cartridge_type_fire_extinguisher.location', 'LIKE', '%' . decryptId($request->location) . '%');
        }
        if (isset($request->frequency) && $request->frequency) {
            $query = $query->where('inspection_cartridge_type_fire_extinguisher.frequency', 'LIKE', '%' . decryptId($request->frequency) . '%');
        }
        if (isset($request->inspection_date) && $request->inspection_date) {
            $query = $query->where('inspection_cartridge_type_fire_extinguisher.inspection_date', 'LIKE', '%' . DBdateformat($request->inspection_date) . '%');
        }
        if (isset($request->next_due) && $request->next_due) {
            $query = $query->where('inspection_cartridge_type_fire_extinguisher.next_due', 'LIKE', '%' . DBdateformat($request->next_due) . '%');
        }

        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_cartridge_type_fire_extinguisher.inspection_status', decryptId($request->inspection_status));
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_cartridge_type_fire_extinguisher.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_cartridge_type_fire_extinguisher.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_cartridge_type_fire_extinguisher.created_at', [$startDate, $endDate]);
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "inspection_status":
                    $query = $query->orderBy('inspection_cartridge_type_fire_extinguisher.inspection_status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_cartridge_type_fire_extinguisher.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_cartridge_type_fire_extinguisher.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_cartridge_type_fire_extinguisher.id', 'DESC');
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
            'location' => decryptId($request->location_id),
            'shift' => decryptId($request->shift_id),
            'next_due' => DBdateformat($request->next_due),
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
        $query = $this->select(
            'inspection_cartridge_type_fire_extinguisher.*',
            'inspection_cartridge_type_fire_extinguisher_details.*',
            'inspection_shift_option.*',
            'inspection_static_docno.*',
            'masters_unit.*',
            'masters_location.*',
            'inspection_frequency_option.*',
            'inspection_cartridge_type_fire_extinguisher.created_by as checked_by',
            'inspection_cartridge_type_fire_extinguisher.id as fire_id',
            'inspection_cartridge_type_fire_extinguisher_details.type as extinguisher_type'
        )
            ->leftJoin('masters_location', 'inspection_cartridge_type_fire_extinguisher.location', '=', 'masters_location.id')
            ->leftJoin('inspection_shift_option', 'inspection_cartridge_type_fire_extinguisher.shift', '=', 'inspection_shift_option.id')
            ->leftJoin('masters_unit', 'inspection_cartridge_type_fire_extinguisher.unit', '=', 'masters_unit.id')
            ->leftJoin('inspection_frequency_option', 'inspection_cartridge_type_fire_extinguisher.frequency', '=', 'inspection_frequency_option.id')
            ->leftJoin('inspection_static_docno', 'inspection_cartridge_type_fire_extinguisher.document_reference_id', '=', 'inspection_static_docno.id')
            ->leftJoin('inspection_cartridge_type_fire_extinguisher_details', 'inspection_cartridge_type_fire_extinguisher.id', '=', 'inspection_cartridge_type_fire_extinguisher_details.inspection_id');

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('masters_location.location_name LIKE "%' . $search . '%"');
                $query->orWhereRaw("DATE_FORMAT(inspection_cartridge_type_fire_extinguisher.inspection_date, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
                $query->orWhereRaw("DATE_FORMAT(inspection_cartridge_type_fire_extinguisher.next_due, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
                $query->orWhereRaw('masters_unit.unit_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_shift_option.shift LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_frequency_option.frequency_name LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->location) && $request->location) {
            $query = $query->where('inspection_cartridge_type_fire_extinguisher.location', 'LIKE', '%' . decryptId($request->location) . '%');
        }
        if (isset($request->frequency) && $request->frequency) {
            $query = $query->where('inspection_cartridge_type_fire_extinguisher.frequency', 'LIKE', '%' . decryptId($request->frequency) . '%');
        }
        if (isset($request->unit) && $request->unit) {
            $query = $query->where('inspection_cartridge_type_fire_extinguisher.unit', 'LIKE', '%' . decryptId($request->unit) . '%');
        }
        if (isset($request->shift) && $request->shift) {
            $query = $query->where('inspection_cartridge_type_fire_extinguisher.shift', 'LIKE', '%' . decryptId($request->shift) . '%');
        }
        if (isset($request->location) && $request->location) {
            $query = $query->where('inspection_cartridge_type_fire_extinguisher.location', 'LIKE', '%' . decryptId($request->location) . '%');
        }
        if (isset($request->inspection_date) && $request->inspection_date) {
            $query = $query->where('inspection_cartridge_type_fire_extinguisher.inspection_date', 'LIKE', '%' . DBdateformat($request->inspection_date) . '%');
        }
        if (isset($request->next_due) && $request->next_due) {
            $query = $query->where('inspection_cartridge_type_fire_extinguisher.next_due', 'LIKE', '%' . DBdateformat($request->next_due) . '%');
        }
        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_cartridge_type_fire_extinguisher.inspection_status', decryptId($request->inspection_status));
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_cartridge_type_fire_extinguisher.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_cartridge_type_fire_extinguisher.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_cartridge_type_fire_extinguisher.created_at', [$startDate, $endDate]);
        }
        $query->orderBy('inspection_cartridge_type_fire_extinguisher.id', 'DESC');

        return $query->get()->groupBy('inspection_id');

        return  $query->get();
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
                'ehs_officer_verified_at' => Carbon::now(),
            ];
            $this->where('id', $id)->update($update_array);
        } else {
            $update_array = [
                'verified_by' => Auth::id(),
                'inspection_status' => WAITING_FOR_CAPA_ACTION,
                'updated_by' => Auth::id(),
                'is_passed' => $request->is_passed,
                'capa_recomendation' => $request->remarks,
                'ehs_officer_verified_at' => Carbon::now(),
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
            'fire_associate_updated_at' => Carbon::now(),
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
                'ehs_officer_verified_at' => Carbon::now(),
            ];
            $this->where('id', $id)->update($update_array);
        } else {
            $update_array = [
                'verified_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'inspection_status' => EHS_OFFICER_REJECTED,
                'capa_ehs_remarks' => $remarks,
                'ehs_officer_verified_at' => Carbon::now(),
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
                'l1_manager_updated_at' => Carbon::now(),
            ];
            $this->where('id', $id)->update($update_array);
        } else {
            $update_array = [
                'l1_manager_verified_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'inspection_status' => L1_MANAGER_REJECTED,
                'level_one_manager_remarks' => $remarks,
                'l1_manager_updated_at' => Carbon::now(),
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
                'l2_manager_updated_at' => Carbon::now(),
            ];
            $this->where('id', $id)->update($update_array);
        } else {
            $update_array = [
                'l2_manager_verified_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'inspection_status' => L2_MANAGER_REJECTED,
                'level_two_manager_remarks' => $remarks,
                'l2_manager_updated_at' => Carbon::now(),
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
        static::addGlobalScope(new TrashScope('inspection_cartridge_type_fire_extinguisher'));
    }
}
