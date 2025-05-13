<?php

namespace App\Models\Inspection\Ohc;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class OccupationHealthInspection extends Model
{
    protected $table = 'inspection_ohc_occupational_health_inspection';


    protected $primaryKey = 'id';

    protected $fillable = [
        'checklist',
        'document_reference_id',
        'date_of_inspection',
        'location',
        'shift',
        'next_due',
        'unit',
        'frequency',
        'checked_by',
        'verified_by',
        'approved_by',
        'approve_status',
        'l1_manager_verified_by',
        'l2_manager_verified_by',
        'level_two_manager_remarks',
        'level_one_manager_remarks',
        'level_two_manager_remarks',
        'l1_manager_verified_by',
        'capa_remarks',
        'capa_recomendation',
        'capa_ehs_remarks',
        'remarks',
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


        $query = $this->select(
            'inspection_ohc_occupational_health_inspection.*',
            'inspection_static_docno.*',
            'masters_unit.*',
            'masters_location.*',
            'inspection_shift_option.*',
            'inspection_ohc_occupational_health_inspection.id as inspection_id',
            'inspection_ohc_occupational_health_inspection.created_at as inspection_created_at',
            'inspection_ohc_occupational_health_inspection.status as inspection_status',
        )
            ->leftJoin('masters_unit', 'inspection_ohc_occupational_health_inspection.unit', '=', 'masters_unit.id')
            ->leftJoin('masters_location', 'inspection_ohc_occupational_health_inspection.location', '=', 'masters_location.id')
            ->leftJoin('inspection_shift_option', 'inspection_ohc_occupational_health_inspection.shift', '=', 'inspection_shift_option.id')

            ->leftJoin(
                'inspection_static_docno',
                'inspection_ohc_occupational_health_inspection.document_reference_id',
                '=',
                'inspection_static_docno.id'
            );


        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_shift_option.shift', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_location.location_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_occupational_health_inspection.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_occupational_health_inspection.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_ohc_occupational_health_inspection.created_at', [$startDate, $endDate]);
        }
        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_ohc_occupational_health_inspection.approve_status', decryptId($request->status));
        }
        if (isset($request->unit_id) && $request->unit_id) {
            $query = $query->where('inspection_ohc_occupational_health_inspection.unit', decryptId($request->unit_id));
        }
        if (isset($request->shift) && $request->shift) {
            $query = $query->where('inspection_ohc_occupational_health_inspection.shift', decryptId($request->shift));
        }
        if (isset($request->location_id) && $request->location_id) {
            $query = $query->where('inspection_ohc_occupational_health_inspection.location', decryptId($request->location_id));
        }
        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "rev_date":
                    $query->orderBy('inspection_ohc_occupational_health_inspection.revision_date', $columnorder);
                    break;
                case "issue_date":
                    $query = $query->orderBy('inspection_ohc_occupational_health_inspection.issue_date', $columnorder);
                    break;
                case "document_number":
                    $query = $query->orderBy('inspection_ohc_occupational_health_inspection.doc_no', $columnorder);
                    break;
                case "status":
                    $query = $query->orderBy('inspection_ohc_occupational_health_inspection.status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_ohc_occupational_health_inspection.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_ohc_occupational_health_inspection.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_ohc_occupational_health_inspection.id', 'DESC');
                    break;
            }
        }


        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('inspection_ohc_occupational_health_inspection.id', 'DESC');

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

            'shift' => decryptId($request->shift),
            'frequency' => decryptId($request->frequency_id),
            'location' => decryptId($request->location_id),
            'unit' => decryptId($request->unit_id),
            'next_due' => DBdateformat($request->next_due_on),
            'document_reference_id' => decryptId($request->document_reference_id),
            'date_of_inspection' => DBdateformat($request->date_of_inspection),
            'approve_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
            'created_by' => Auth::id(),
        ];

        return self::create($insert_array);
    }


    public function EHSOfficerUpdate($id)
    {

        $request = request();
        if ($request->is_passed == 1) {
            $update_array = [
                'verified_by' => Auth::id(),
                'approved_by' => Auth::id(),
                'approve_status' => INSPECTION_APPROVED,
                'updated_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->where('id', $id)->update($update_array);
        } else {
            $update_array = [
                'verified_by' => Auth::id(),
                'approve_status' => WAITING_FOR_CAPA_ACTION,
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
            'approve_status' => WAITING_FOR_CAPA_VERIFICATION,
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
                'approve_status' => WAITING_FOR_L1_VERIFICATION,
                'capa_ehs_remarks' => $remarks,
            ];
            $this->where('id', $id)->update($update_array);
        } else {
            $update_array = [
                'verified_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'approve_status' => EHS_OFFICER_REJECTED,
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
                'approve_status' => WAITING_FOR_L2_VERIFICATION,
                'level_one_manager_remarks' => $remarks,
            ];
            $this->where('id', $id)->update($update_array);
        } else {
            $update_array = [
                'l1_manager_verified_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'approve_status' => L1_MANAGER_REJECTED,
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
                'approve_status' => INSPECTION_APPROVED,
                'level_two_manager_remarks' => $remarks,
            ];
            $this->where('id', $id)->update($update_array);
        } else {
            $update_array = [
                'l2_manager_verified_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'approve_status' => L2_MANAGER_REJECTED,
                'level_two_manager_remarks' => $remarks,
            ];
            $this->where('id', $id)->update($update_array);
        }
    }

    public function exportdata()
    {
        $request = request();
        $search = '';

        $query = $this->select(
            'inspection_ohc_occupational_health_inspection.*'
        );
        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_ohc_occupational_health_inspection.approve_status', decryptId($request->status));
        }
        if (isset($request->unit_id) && $request->unit_id) {
            $query = $query->where('inspection_ohc_occupational_health_inspection.unit', decryptId($request->unit_id));
        }
        if (isset($request->shift) && $request->shift) {
            $query = $query->where('inspection_ohc_occupational_health_inspection.shift', decryptId($request->shift));
        }
        if (isset($request->location_id) && $request->location_id) {
            $query = $query->where('inspection_ohc_occupational_health_inspection.location', decryptId($request->location_id));
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_occupational_health_inspection.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_occupational_health_inspection.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_ohc_occupational_health_inspection.created_at', [$startDate, $endDate]);
        }
        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "rev_date":
                    $query->orderBy('inspection_ohc_occupational_health_inspection.revision_date', $columnorder);
                    break;
                case "issue_date":
                    $query = $query->orderBy('inspection_ohc_occupational_health_inspection.issue_date', $columnorder);
                    break;
                case "document_number":
                    $query = $query->orderBy('inspection_ohc_occupational_health_inspection.doc_no', $columnorder);
                    break;
                case "status":
                    $query = $query->orderBy('inspection_ohc_occupational_health_inspection.status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_ohc_occupational_health_inspection.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_ohc_occupational_health_inspection.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_ohc_occupational_health_inspection.id', 'DESC');
                    break;
            }
        }

        $query->orderBy('inspection_ohc_occupational_health_inspection.id', 'DESC');

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
    public function Selectone($id)
    {
        return $this->where('id', $id)->first();
    }
}
