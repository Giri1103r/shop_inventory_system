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
        'category_id',
        'subcategory_name',
        'questionary',
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
        $query = $this->select('inspection_master_checklist_subtype.*');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('category_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('category_id LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->category_name) && $request->category_name) {
            $query = $query->where('inspection_master_checklist_subtype.category_name', 'LIKE', '%' . $request->category_name . '%');
        }
        if (isset($request->category_id) && $request->category_id) {
            $query = $query->where('inspection_master_checklist_subtype.category_id', 'LIKE', '%' . $request->category_id . '%');
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "category_name":
                    $query->orderBy('inspection_master_checklist_subtype.category_name', $columnorder);
                    break;
                case "category_id":
                    $query = $query->orderBy('inspection_master_checklist_subtype.category_id', $columnorder);
                    break;
                case "status":
                    $query = $query->orderBy('inspection_master_checklist_subtype.status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_master_checklist_subtype.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_master_checklist_subtype.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_master_checklist_subtype.id', 'DESC');
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
        $insert_array = [
            'category_id' => decryptId($request->category_id),
            'subcategory_name' => $request->subcategory_name,
            'created_by' => Auth::id(),
        ];
        return self::create($insert_array);
    }

    public function selectOne($id)
    {
        return $this->where('id', $id)->first();
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


    public function ajaxList($subTypeId , $checklistTypeId = '')
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


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_master_checklist_subtype'));

        static::created(function ($model) {

            $uniqueId = 'SUBCAT-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['subcategory_id' => $uniqueId]);
        });
    }
}
