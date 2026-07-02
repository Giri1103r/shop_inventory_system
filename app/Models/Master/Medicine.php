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

class Medicine extends Model
{
    use  HasFactory;


    protected $table = 'master_medicine';
    protected $primaryKey = 'id';

    protected $fillable = [
        'medicine_id',
        'medicine_name',
        'generic_names',
        'category_id',
        'manufacture_id',
        'uom_id',
        'tax_id',
        'storage_condition',
        'package_size',
        'scheduled_type',
        'storage_condition',
        'reorder_level',
        'reorder_quantity',
        'mrp',
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
        $query = $this->select('master_medicine.*');
        $data_count = $query;
        $total_records = $data_count->count();

        if ($request->has('medicine_id') && $request->medicine_id) {
            $query = $query->where('master_medicine.medicine_id', 'LIKE', '%' . $request->medicine_id . '%');
        }
        if ($request->has('medicine_name') && $request->medicine_name) {
            $query = $query->where('master_medicine.medicine_name', 'LIKE', '%' . $request->medicine_name . '%');
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('master_medicine.status', decryptId($request->status));
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
            'medicine_name' => $request->medicine_name,
            'generic_names' => $request->generic_names,
            'category_id' => $request->category_id,
            'manufacture_id' => $request->manufacture_id,
            'uom_id' => $request->uom_id,
            'tax_id' => $request->tax_id,
            'storage_condition' => $request->storage_condition,
            'package_size' => $request->package_size,
            'scheduled_type' => $request->scheduled_type,
            'storage_condition' => $request->storage_condition,
            'reorder_level' => $request->reorder_level,
            'reorder_quantity' => $request->reorder_quantity,
            'mrp' => $request->mrp,
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
            'medicine_name' => $request->medicine_name,
            'generic_names' => $request->generic_names,
            'category_id' => $request->category_id,
            'manufacture_id' => $request->manufacture_id,
            'uom_id' => $request->uom_id,
            'tax_id' => $request->tax_id,
            'storage_condition' => $request->storage_condition,
            'package_size' => $request->package_size,
            'scheduled_type' => $request->scheduled_type,
            'storage_condition' => $request->storage_condition,
            'reorder_level' => $request->reorder_level,
            'reorder_quantity' => $request->reorder_quantity,
            'mrp' => $request->mrp,
            'description' => $request->description,
            
            'updated_by' => Auth::id(),
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

    public function UniqueCheck($medicine)
    {
        return $this->where(function ($q) use ($medicine) {
            $q->where('medicine_name', $medicine);
        });
    }
    public function ExistuniqueCheck($medicine, $id)
    {
        return $this->where(function ($q) use ($medicine) {
            $q->where('medicine_name', $medicine);
        })
            ->where('id', '!=', $id);
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('master_medicine.*');


        if ($request->has('medicine_id') && $request->medicine_id) {
            $query = $query->where('master_medicine.medicine_id', 'LIKE', '%' . $request->medicine_id . '%');
        }
        if ($request->has('medicine_name') && $request->medicine_name) {
            $query = $query->where('master_medicine.medicine_name', 'LIKE', '%' . $request->medicine_name . '%');
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('master_medicine.status', decryptId($request->status));
        }

        $query->orderBy('id', 'DESC');

        return $query;
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('master_medicine'));
        static::created(function ($model) {
            $uniqueId =  'MED -' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['medicine_id' => $uniqueId]);
        });
    }
}
