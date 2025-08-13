<?php

namespace App\Models\Inspection\audit\Master;

use App\Scopes\TrashScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class ComplianceCategory extends Model
{
    protected $table = 'inspection_audit_master_compliance_category';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'compliance_category',
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
        $query = $this->select('inspection_audit_master_compliance_category.*');
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('compliance_category', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('compliance_category') && $request->compliance_category) {
            $query = $query->where('compliance_category', 'LIKE', '%' . $request->compliance_category . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('status', decryptId($request->status));
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_audit_master_compliance_category.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_audit_master_compliance_category.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_audit_master_compliance_category.created_at', [$startDate, $endDate]);
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

    public function UniqueCheck($data)
    {

        return $this->where('compliance_category',  $data)->get();
    }

    public function ExistuniqueCheck($data, $id)
    {
        return $this->where('compliance_category',  $data)
            ->where('id', '!=', $id)
            ->get();
    }

    public function store()
    {
        $request = request();

        $insert_array = array(
            'compliance_category' => $request->compliance_category,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'compliance_category' => $request->compliance_category,
            'updated_by' => Auth::id()
        );
        return $this->where('id', $id)->update($update_array);
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

    public function deleterecord($id)
    {

        $update_data = array(
            'status' => 0,
            'trash' => 'YES',
        );

        return $this->where('id', $id)->update($update_data);
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_audit_master_compliance_category.*');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhereRaw('compliance_category LIKE "%' . $search . '%"');
            });
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_audit_master_compliance_category.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_audit_master_compliance_category.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_audit_master_compliance_category.created_at', [$startDate, $endDate]);
        }
        if ($request->has('compliance_category') && $request->compliance_category) {
            $query = $query->where('compliance_category', 'LIKE', '%' . $request->compliance_category . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('inspection_audit_master_compliance_category.status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');
        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select(
            'inspection_audit_master_compliance_category.*'
        )
            ->where('inspection_audit_master_compliance_category.id', $id)
            ->first();

        return $data;
    }

    public function GetCategory()
    {
        $data =  $this->get();
        return $data;
    }

    // api list

    public function listApi()
    {
        $request = request();
        $per_page = $perPage = $request->input('per_page', 10);
        $search = '';
        $query = $this->select('inspection_audit_master_compliance_category.*')->where('status', 1)->where('trash', 'NO');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_audit_master_compliance_category.compliance_category', 'LIKE', '%' . $search . '%');
            });
        }

        $paginatedData = $query->orderBy('inspection_audit_master_compliance_category.id')->paginate($per_page);

        $inspection_data = $paginatedData->toArray();

        $task_data = [];
        $refined_data = [];

        foreach ($inspection_data['data'] as $index => $data) {
            $task_data['id'] = $data['id'];
            $task_data['compliance_category'] = $data['compliance_category'];
            $refined_data[$index] = $task_data;
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

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_audit_master_compliance_category'));
    }
}
