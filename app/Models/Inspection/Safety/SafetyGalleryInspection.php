<?php

namespace App\Models\Inspection\Safety;

use App\Scopes\TrashScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class SafetyGalleryInspection extends Model
{
    protected $table = 'inspection_safety_gallery';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'document_reference_id',
        'date_of_inspection',
        'location',
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

        $query = $this->select('inspection_safety_gallery.*', 'masters_unit.*', 'masters_location.*', 'inspection_safety_gallery.id as inspection_id', 'inspection_safety_gallery.created_at as inspection_created_at')
            ->leftJoin('masters_location', 'inspection_safety_gallery.location', '=', 'masters_location.id')
            ->leftJoin('masters_unit', 'inspection_safety_gallery.unit', '=', 'masters_unit.id')
            ->leftJoin('inspection_static_docno', 'inspection_safety_gallery.document_reference_id', '=', 'inspection_static_docno.id');

        if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_OFFICER) || CheckUserRole(ROLE_L1_MANAGER) || CheckUserRole(ROLE_L2_MANAGER)) {
        } else if (CheckUserRole(ROLE_FIRE_ASSOCIATES)) {
            $query->where('inspection_safety_gallery.created_by', Auth::id());
        }

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('masters_location.location_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('masters_unit.unit_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_safety_gallery.resource_code LIKE "%' . $search . '%"');
            });
        }

        if ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_safety_gallery.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_safety_gallery.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_safety_gallery.created_at', [$startDate, $endDate]);
        }
        if (isset($request->location) && $request->location) {
            $query = $query->where('inspection_safety_gallery.location', 'LIKE', '%' . decryptId($request->location) . '%');
        }
        if (isset($request->resource_code) && $request->resource_code) {
            $query = $query->where('inspection_safety_gallery.resource_code', 'LIKE', '%' . ($request->resource_code) . '%');
        }
        if (isset($request->unit) && $request->unit) {
            $query = $query->where('inspection_safety_gallery.unit', 'LIKE', '%' . decryptId($request->unit) . '%');
        }
        if (isset($request->inspection_date) && $request->inspection_date) {
            $query = $query->whereDate('inspection_safety_gallery.date_of_inspection', '=', DBdateformat($request->inspection_date));
        }
        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_safety_gallery.inspection_status', decryptId($request->inspection_status));
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "rev_date":
                    $query->orderBy('inspection_safety_gallery.rev_date', $columnorder);
                    break;
                case "issue_date":
                    $query = $query->orderBy('inspection_safety_gallery.issue_date', $columnorder);
                    break;
                case "document_number":
                    $query = $query->orderBy('inspection_safety_gallery.doc_no', $columnorder);
                    break;
                case "status":
                    $query = $query->orderBy('inspection_safety_gallery.status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_safety_gallery.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_safety_gallery.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_safety_gallery.id', 'DESC');
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
            'unit' => decryptId($request->unit_id),
            'resource_code' => $request->resource_code,
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
            'unit' => ($request->unit_id),
            'resource_code' => $request->resource_code,
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
        $query = $this->select('inspection_safety_gallery.*', 'masters_unit.*', 'masters_location.*', 'inspection_static_docno.*', 'inspection_safety_gallery.id as inspection_id', 'inspection_safety_gallery.created_by as checked_by')
            ->leftJoin('masters_location', 'inspection_safety_gallery.location', '=', 'masters_location.id')
            ->leftJoin('masters_unit', 'inspection_safety_gallery.unit', '=', 'masters_unit.id')
            ->leftJoin('inspection_static_docno', 'inspection_safety_gallery.document_reference_id', '=', 'inspection_static_docno.id');


        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->where('masters_location.location_name LIKE "%' . $search . '%"');
                $query->where('masters_unit.unit_name LIKE "%' . $search . '%"');
                $query->where('masters_unit.resource_code LIKE "%' . $search . '%"');
            });
        }
        if ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_safety_gallery.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_safety_gallery.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_safety_gallery.created_at', [$startDate, $endDate]);
        }

        if (isset($request->location) && $request->location) {
            $query = $query->where('inspection_safety_gallery.location', 'LIKE', '%' . decryptId($request->location) . '%');
        }
        if (isset($request->resource_code) && $request->resource_code) {
            $query = $query->where('inspection_safety_gallery.resource_code', 'LIKE', '%' . ($request->resource_code) . '%');
        }
        if (isset($request->unit) && $request->unit) {
            $query = $query->where('inspection_safety_gallery.unit', 'LIKE', '%' . decryptId($request->unit) . '%');
        }
        if (isset($request->inspection_date) && $request->inspection_date) {
            $query = $query->whereDate('inspection_safety_gallery.date_of_inspection', '=', DBdateformat($request->inspection_date));
        }
        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_safety_gallery.inspection_status', decryptId($request->inspection_status));
        }

        $query->orderBy('inspection_safety_gallery.id', 'DESC');

        return  $query->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_safety_gallery'));
    }
}
