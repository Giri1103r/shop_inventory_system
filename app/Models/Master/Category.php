<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Master\Location;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Log;

use App\Models\User;

class Category extends Model
{
    use  HasFactory;


    protected $table = 'master_category';
    protected $primaryKey = 'id';

    protected $fillable = [
        'category_id',
        'category_code',
        'category_name',
        'description',
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
        $query = $this->select('master_category.*');
        $data_count = $query;
        $total_records = $data_count->count();

        if ($request->has('category_id') && $request->category_id) {
            $query = $query->where('master_category.category_id', 'LIKE', '%' . $request->category_id . '%');
        }
        if ($request->has('category_code') && $request->category_code) {
            $query = $query->where('master_category.category_code', 'LIKE', '%' . $request->category_code . '%');
        }

        if ($request->has('category_name') && $request->category_name) {
            $query = $query->where('master_category.category_name', ($request->category_name));
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('master_category.status', decryptId($request->status));
        }
        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }
        $query->orderBy('id', 'DESC');
        $org_total =  $query;
        $org_total_counts = $org_total->count();
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

        $create_array = [
            'category_code' => $request->category_code,
            'category_name' => $request->category_name,
            'description' => $request->description,
            'created_by' => Auth::id(),
        ];

        $data = $this->create($create_array);
        return $data;
    }
    public function updates($id)
    {
        $request = request();

        $update_array = [
            'category_code' => $request->category_code,
            'category_name' => $request->category_name,
            'description' => $request->description,
            'created_by' => Auth::id(),
        ];

        $data = $this->where('id', $id)->update($update_array);
        return $data;
    }
    public function selectOne($id)
    {
        $data = $this->where('id', $id)->first();
        return $data;
    }

    public function getalldata()
    {
        $data = $this->where('status', 1)->where('trash', 'No')->get();

        return $data;
    }

    public function UniqueCheck($category, $category_name)
    {
        return $this->where(function ($q) use ($category, $category_name) {
            $q->where('category_code', $category)
                ->orWhere('category_name', $category_name);
        });
    }
    public function ExistuniqueCheck($category, $category_name, $id)
    {
        return $this->where(function ($q) use ($category, $category_name) {
            $q->where('category_code', $category)
                ->orWhere('category_name', $category_name);
        })
            ->where('id', '!=', $id);
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('master_category.*');


        if ($request->has('category_id') && $request->category_id) {
            $query = $query->where('master_category.category_id', 'LIKE', '%' . $request->category_id . '%');
        }
        if ($request->has('category_code') && $request->category_code) {
            $query = $query->where('master_category.category_code', 'LIKE', '%' . $request->category_code . '%');
        }

        if ($request->has('category_name') && $request->category_name) {
            $query = $query->where('master_category.category_name', ($request->category_name));
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('master_category.status', decryptId($request->status));
        }
       
        $query->orderBy('id', 'DESC');

        return $query;
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('master_category'));
        static::created(function ($model) {
            $uniqueId =  'CAT -' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['category_id' => $uniqueId]);
        });
    }
}
