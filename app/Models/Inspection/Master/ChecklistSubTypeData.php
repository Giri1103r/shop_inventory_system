<?php

namespace App\Models\Inspection\Master;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class ChecklistSubTypeData extends Model
{

    protected $table = 'inspection_master_checklist_sub_type_data';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'checklist_type_id',
        'checklist_sub_type_id',
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
        $query = $this->select('inspection_master_checklist_sub_type_data.*', 'inspection_master_checklist_type.category_name',  'inspection_master_checklist_subtype.subcategory_name');
        $query = $query->leftJoin('inspection_master_checklist_type', 'inspection_master_checklist_sub_type_data.checklist_type_id', '=', 'inspection_master_checklist_type.id');
        $query = $query->leftJoin('inspection_master_checklist_subtype', 'inspection_master_checklist_sub_type_data.checklist_sub_type_id', '=', 'inspection_master_checklist_subtype.id');
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_master_checklist_type.category_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_master_checklist_subtype.subcategory_name', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('checklist_type_id') && $request->checklist_type_id) {
            $query = $query->where('inspection_master_checklist_sub_type_data.checklist_type_id', decryptId($request->checklist_type_id));
        }
        if ($request->has('checklist_sub_type_id') && $request->checklist_sub_type_id) {
            $query = $query->where('inspection_master_checklist_sub_type_data.checklist_sub_type_id', decryptId($request->checklist_sub_type_id));
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('inspection_master_checklist_sub_type_data.unit_id', decryptId($request->unit_id));
        }

        if ($request->has('status') && $request->status) {
            $query = $query->where('inspection_master_checklist_sub_type_data.status', decryptId($request->status));
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

    public function store()
    {
        $request = request();
        $insert_array = [
            'checklist_type_id' => decryptId($request->checklist_type_id),
            'checklist_sub_type_id' => decryptId($request->checklist_sub_type_id),
            'created_by' => Auth::id(),
        ];
        return self::create($insert_array);
    }
    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'checklist_type_id' => decryptId($request->checklist_type_id),
            'checklist_sub_type_id' => decryptId($request->checklist_sub_type_id),
            'updated_by' => Auth::id()
        );
        return $this->where('id', $id)->update($update_array);
    }
    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_master_checklist_sub_type_data.*', 'inspection_master_checklist_type.category_name',  'inspection_master_checklist_subtype.subcategory_name');
        $query = $query->leftJoin('inspection_master_checklist_type', 'inspection_master_checklist_sub_type_data.checklist_type_id', '=', 'inspection_master_checklist_type.id');
        $query = $query->leftJoin('inspection_master_checklist_subtype', 'inspection_master_checklist_sub_type_data.checklist_sub_type_id', '=', 'inspection_master_checklist_subtype.id');
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_master_checklist_type.category_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_master_checklist_subtype.subcategory_name', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('checklist_type_id') && $request->checklist_type_id) {
            $query = $query->where('inspection_master_checklist_sub_type_data.checklist_type_id', decryptId($request->checklist_type_id));
        }
        if ($request->has('checklist_sub_type_id') && $request->checklist_sub_type_id) {
            $query = $query->where('inspection_master_checklist_sub_type_data.checklist_sub_type_id', decryptId($request->checklist_sub_type_id));
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('inspection_master_checklist_sub_type_data.unit_id', decryptId($request->unit_id));
        }

        if ($request->has('status') && $request->status) {
            $query = $query->where('inspection_master_checklist_sub_type_data.status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');
        return  $query->get();
    }

    public function selectOne($id)
    {
        $data = $this->select('inspection_master_checklist_sub_type_data.*', 'inspection_master_checklist_type.category_name',  'inspection_master_checklist_subtype.subcategory_name')
            ->leftJoin('inspection_master_checklist_type', 'inspection_master_checklist_sub_type_data.checklist_type_id', '=', 'inspection_master_checklist_type.id')
            ->leftJoin('inspection_master_checklist_subtype', 'inspection_master_checklist_sub_type_data.checklist_sub_type_id', '=', 'inspection_master_checklist_subtype.id')
            ->where('inspection_master_checklist_sub_type_data.id', $id)
            ->first();

        return $data;
    }
    public function UniqueCheck($subcategory_name, $category_id)
    {

        return $this->where('subcategory_name',  $subcategory_name)->where('category_id', $category_id)->get();
    }

    public function ExistuniqueCheck($subcategory_name, $category_id, $id)
    {
        return $this->where('subcategory_name',  $subcategory_name)->where('category_id', $category_id)
            ->where('id', '!=', $id)
            ->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_master_checklist_sub_type_data'));

        static::created(function ($model) {

            $uniqueId = 'SUBCAT-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['subcategory_id' => $uniqueId]);
        });
    }
}
