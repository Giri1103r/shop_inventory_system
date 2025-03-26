<?php

namespace App\Models\Inspection\Environment;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class InspectionStaticDocno extends Model
{

    protected $table = 'inspection_static_docno';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'type',
        'doc_no',
        'issue_date',
        'rev_dt',
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
        $query = $this->select('inspection_static_docno.*');
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('environment_id', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('environment_id') && $request->environment_id) {
            $query = $query->where('inspection_static_docno.environment_id', decryptId($request->environment_id));
        }
      
        if ($request->has('status') && $request->status) {
            $query = $query->where('inspection_static_docno.status', decryptId($request->status));
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
        return $this->create($insert_array);
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
        $query = $this->select('inspection_static_docno.*');
        // dd($query);

        if ($request->has('ambient_noise_id') && $request->ambient_noise_id) {
            $query = $query->where('inspection_static_docno.ambient_noise_id', decryptId($request->ambient_noise_id));
        }
      
        if ($request->has('status') && $request->status) {
            $query = $query->where('inspection_static_docno.status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');
        return  $query->get();
    }

    public function selectOne($id)
    {
        $data = $this->select('inspection_static_docno.*')
            ->first();
        return $data;
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
        static::addGlobalScope(new TrashScope('inspection_static_docno'));

        static::created(function ($model) {

            $uniqueId = 'SUBCAT-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['subcategory_id' => $uniqueId]);
        });
    }
}
