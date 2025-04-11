<?php

namespace App\Models\Inspection\RRAA;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\TrashScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class RRAADetails extends Model
{
    use  HasFactory;

    protected $table = 'inspection_rraa';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'document_reference_id',
        'serial_number',
        'category',
        'ohs_compliance_index',
        'frequency',
        'scope',
        'responsibility',
        'authority',
        'accountability',
        'remark',
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
        $query = $this->select('inspection_rraa.*', 'inspection_master_checklist_type.category_name', 'inspection_frequency_option.frequency_name')
                ->leftJoin('inspection_master_checklist_type', 'inspection_rraa.category', '=', 'inspection_master_checklist_type.id')
                ->leftJoin('inspection_frequency_option', 'inspection_rraa.frequency', '=', 'inspection_frequency_option.id');

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhereRaw('inspection_master_checklist_type.category_name LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('inspection_rraa.ohs_compliance_index LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('inspection_frequency_option.frequency_name LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('inspection_rraa.scope LIKE ?', ['%' . $search . '%']);
            });
        }

        if ($request->has('category') && $request->category) {
            $query = $query->where('inspection_rraa.category', 'LIKE', '%' . decryptId($request->category) . '%');
        }
        if (isset($request->ohs_compliance_index) && $request->ohs_compliance_index) {
            $query = $query->where('inspection_rraa.ohs_compliance_index', 'LIKE', '%' . $request->ohs_compliance_index . '%');
        }
        if ($request->has('frequency') && $request->frequency) {
            $query = $query->where('inspection_rraa.frequency', 'LIKE', '%' . decryptId($request->frequency) . '%');
        }
        if (isset($request->scope) && $request->scope) {
            $query = $query->where('inspection_rraa.scope', 'LIKE', '%' . $request->scope . '%');
        }

        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('inspection_rraa.id', 'DESC');

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

    public function store()
    {
        $request = request();
        $insertedData = [];

        foreach ($request->scope as $index => $Scope) {
            $insert_array = array(
                'document_reference_id' => decryptId($request->document_reference_id),
                'serial_number' =>$request->serial_number[$index],
                'category' =>decryptId($request->category[$index]),
                'ohs_compliance_index' =>$request->ohs_compliance_index[$index],
                'frequency' =>decryptId($request->frequency[$index]),
                'scope' => $Scope,
                'responsibility' =>$request->emp_id[$index],
                'authority' => $request->authority[$index],
                'accountability' => $request->accountability[$index],
                'remark' => $request->remark[$index],
                'created_by' => Auth::id(),
            );

            $insertedData []=  $this->create($insert_array);

        }

        return $insertedData;
    }

    public function selectOne($id)
    {
        return $this->where('id', $id)->where('status', 1)->where('trash', 'NO')->first();
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_rraa.*', 'inspection_master_checklist_type.category_name', 'inspection_frequency_option.frequency_name')
                ->leftJoin('inspection_master_checklist_type', 'inspection_rraa.category', '=', 'inspection_master_checklist_type.id')
                ->leftJoin('inspection_frequency_option', 'inspection_rraa.frequency', '=', 'inspection_frequency_option.id');

        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhereRaw('inspection_master_checklist_type.category_name LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('inspection_rraa.ohs_compliance_index LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('inspection_frequency_option.frequency_name LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('inspection_rraa.scope LIKE ?', ['%' . $search . '%']);
            });
        }
        if ($request->has('category') && $request->category) {
            $query = $query->where('inspection_rraa.category', 'LIKE', '%' . decryptId($request->category) . '%');
        }
        if (isset($request->ohs_compliance_index) && $request->ohs_compliance_index) {
            $query = $query->where('inspection_rraa.ohs_compliance_index', 'LIKE', '%' . $request->ohs_compliance_index . '%');
        }
        if ($request->has('frequency') && $request->frequency) {
            $query = $query->where('inspection_rraa.frequency', 'LIKE', '%' . decryptId($request->frequency) . '%');
        }
        if (isset($request->scope) && $request->scope) {
            $query = $query->where('inspection_rraa.scope', 'LIKE', '%' . $request->scope . '%');
        }

        $query->orderBy('inspection_rraa.id', 'DESC');

        return  $query->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_rraa'));
    }
}
