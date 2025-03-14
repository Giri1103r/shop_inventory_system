<?php

namespace App\Models\Inspection\Master;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class ChecklistSubType extends Model
{

    protected $table = 'inspection_master_checklist_subtype';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'subcategory_id',
        'category_id',
        'subcategory_name',
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
        $query = $this->select('inspection_master_checklist_subtype.*', 'inspection_master_checklist_type.category_name');
        $query = $query->leftJoin('inspection_master_checklist_type', 'inspection_master_checklist_subtype.category_id', '=', 'inspection_master_checklist_type.id');
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('subcategory_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('category_id', 'LIKE', '%' . $search . '%');
            });
        }
        if (isset($request->subcategory_id) && $request->subcategory_id) {
            $query = $query->where('inspection_master_checklist_subtype.subcategory_id', 'LIKE', '%' . $request->subcategory_id . '%');
        }
        if (isset($request->subcategory_name) && $request->subcategory_name) {
            $query = $query->where('inspection_master_checklist_subtype.subcategory_name', 'LIKE', '%' . $request->subcategory_name . '%');
        }
        if (isset($request->category_id) && $request->category_id) {
            $query = $query->where('inspection_master_checklist_subtype.category_id', 'LIKE', '%' . decryptId($request->category_id) . '%');
        }

        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_master_checklist_subtype.status', 'LIKE', '%' . decryptId($request->status) . '%');
        }


        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('inspection_master_checklist_subtype.id', 'DESC');

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
            'category_id' => decryptId($request->category_id),
            'subcategory_name' => $request->subcategory_name,
            'created_by' => Auth::id(),
        ];
        return self::create($insert_array);
    }


    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'category_id' => decryptId($request->category_id),
            'subcategory_name' => $request->subcategory_name,
            'updated_by' => Auth::id()
        );
        return $this->where('id', $id)->update($update_array);
    }

    public function selectOne($id)
    {

        $data = $this->select(
            'inspection_master_checklist_subtype.*',
            'inspection_checklist_files.file_path',
            'inspection_master_checklist_type.category_name'
        )
            ->where('inspection_master_checklist_subtype.id', $id)->leftjoin('inspection_checklist_files', 'inspection_checklist_files.checklist_id', '=', 'inspection_master_checklist_subtype.id')->leftjoin('inspection_master_checklist_type', 'inspection_master_checklist_type.id', '=', 'inspection_master_checklist_subtype.category_id')
            ->first();

        return $data;
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_master_checklist_subtype.*', 'inspection_master_checklist_type.category_name');
        $query = $this->leftjoin('inspection_master_checklist_type', 'inspection_master_checklist_type.id', '=', 'inspection_master_checklist_subtype.category_id');

        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query->orWhereRaw('subcategory_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('category_id LIKE "%' . $search . '%"');
            });
        }
        if (isset($request->subcategory_id) && $request->subcategory_id) {
            $query = $query->where('inspection_master_checklist_subtype.subcategory_id', 'LIKE', '%' . $request->subcategory_id . '%');
        }
        if (isset($request->subcategory_name) && $request->subcategory_name) {
            $query = $query->where('inspection_master_checklist_subtype.subcategory_name', 'LIKE', '%' . $request->subcategory_name . '%');
        }
        if (isset($request->category_id) && $request->category_id) {
            $query = $query->where('inspection_master_checklist_subtype.category_id', 'LIKE', '%' . decryptId($request->category_id) . '%');
        }

        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_master_checklist_subtype.status', 'LIKE', '%' . decryptId($request->status) . '%');
        }

        $query->orderBy('inspection_master_checklist_subtype.id', 'DESC');

        return  $query->get();
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


    public function ajaxList($subTypeId, $checklistTypeId = '')
    {
        $query = $this->select('id', 'subcategory_name')->where('status', 1);

        if ($checklistTypeId != '') {
            $query->where('category_id', $checklistTypeId);
        }
        if (!empty($checklistTypeId) && !empty($subTypeId)) {
            $query = $query->where('category_id', $checklistTypeId)->where('status', 1)->orWhere(function ($query) use ($subTypeId, $checklistTypeId) {
                $query->where('category_id', $checklistTypeId)->where('id', $subTypeId);
            });
        }
        $datas = $query->get();

        $list = [];
        foreach ($datas as $data) {
            $listvalue = [];
            $listvalue['id'] = encryptId($data->id);
            $listvalue['name'] = $data->subcategory_name;
            $list[] = $listvalue;
        }

        return $list;
    }


    public function statuschange($id)
    {
        $request = request();
        // dd($id);
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

    public function statuschange_all($id)
    {
        $request = request();
        $datas = $this->where('category_id', $id)->get();
        $type = $request->types;
        foreach ($datas as $data) {
            if ($type == 1) {
                $update_data = array(
                    'status' => 0,
                );
            } else {
                $update_data = array(
                    'status' => 1,
                );
            }
            $data->update($update_data);
        }
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_master_checklist_subtype'));

        static::created(function ($model) {

            $uniqueId = 'SUBCAT-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['subcategory_id' => $uniqueId]);
        });
    }
}
