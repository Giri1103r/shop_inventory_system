<?php

namespace App\Models\Inspection\MSDS;

use App\Scopes\TrashScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class MSDS extends Model
{
    protected $table = 'inspection_msds';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'location_id',
        'exact_location',
        'unit_id',
        'department_id',
        'document_reference_id',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function list()
    {
        $request = request();
        $search = '';

        $query = $this
            ->select(
                'inspection_msds.*',
                'masters_location.location_name',
                'masters_unit.unit_name',
                'masters_department.department_name'
            )
            ->leftJoin('masters_location', 'masters_location.id', '=', 'inspection_msds.location_id')
            ->leftJoin('masters_unit', 'masters_unit.id', '=', 'inspection_msds.unit_id')
            ->leftJoin('masters_department', 'masters_department.id', '=', 'inspection_msds.department_id')
            ->where('inspection_msds.trash', 'NO');


        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query->orWhere('location_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('department_name', 'LIKE', '%' . $search . '%');
            });
        }
        if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_OFFICER) || CheckUserRole(ROLE_L1_MANAGER) || CheckUserRole(ROLE_L2_MANAGER)) {
        } else if (CheckUserRole(ROLE_INSPECTION_CREATOR)) {
        }

        if ($request->has('location_id') && $request->location_id) {
            $query = $query->where('inspection_msds.location_id', 'LIKE', '%' . decryptId($request->location_id) . '%');
        }
        if ($request->has('department_id') && $request->department_id) {
            $query = $query->where('inspection_msds.department_id', 'LIKE', '%' . decryptId($request->department_id) . '%');
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('inspection_msds.unit_id', 'LIKE', '%' . decryptId($request->unit_id) . '%');
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_msds.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_msds.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_msds.created_at', [$startDate, $endDate]);
        }
        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('id', 'DESC');

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
    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this
            ->select(
                'inspection_msds.*',
                'masters_location.location_name',
                'masters_unit.unit_name',
                'masters_department.department_name',
                'inspection_msds_details.*',
                'inspection_msds.id as msdsid',
            )
            ->leftJoin('masters_location', 'masters_location.id', '=', 'inspection_msds.location_id')
            ->leftJoin('masters_unit', 'masters_unit.id', '=', 'inspection_msds.unit_id')
            ->leftJoin('masters_department', 'masters_department.id', '=', 'inspection_msds.department_id')
            ->leftJoin('inspection_msds_details', 'inspection_msds_details.msds_id', '=', 'inspection_msds.id')
            ->where('inspection_msds.trash', 'NO');


        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhere('location_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('department_name', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('location_id') && $request->location_id) {
            $query = $query->where('inspection_msds.location_id', 'LIKE', '%' . decryptId($request->location_id) . '%');
        }
        if ($request->has('department_id') && $request->department_id) {
            $query = $query->where('inspection_msds.department_id', 'LIKE', '%' . decryptId($request->department_id) . '%');
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('inspection_msds.unit_id', 'LIKE', '%' . decryptId($request->unit_id) . '%');
        }
        if ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_msds.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_msds.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_msds.created_at', [$startDate, $endDate]);
        }
        $query->orderBy('inspection_msds.id', 'DESC');

        $data =   $query->get();
        if ($data) {
            return $data->groupBy('msds_id');
        }
    }

    public function store()
    {
        $request = request();
        $insert_array = array(
            'location_id' => decryptId($request->location_id),
            'exact_location' => ($request->exact_location),
            'department_id' => decryptId($request->department_id),
            'unit_id' => decryptId($request->unit_id),
            'document_reference_id' => decryptId($request->document_reference_id),
            'created_by' => Auth::id(),
        );
        return $this->create($insert_array);
    }


    public function selectOne($id)
    {
        return $this->where('id', $id)->where('status', 1)->where('trash', 'NO')->first();
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_msds'));
    }
}
