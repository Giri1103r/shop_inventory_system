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

class Tax extends Model
{
    use  HasFactory;


    protected $table = 'master_tax';
    protected $primaryKey = 'id';

    protected $fillable = [
        'tax_id',
        'tax_name',
        'hsn_code',
        'tax_percentage',
        'cgst_percentage',
        'sgst_percentage',
        'igst_percentage',
        'cess_percentage ',
        'effective_from',
        'effective_to',
        'remarks',
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
        $query = $this->select('master_tax.*');
        $data_count = $query;
        $total_records = $data_count->count();

        if ($request->has('tax_id') && $request->tax_id) {
            $query = $query->where('master_tax.tax_id', 'LIKE', '%' . $request->tax_id . '%');
        }
        if ($request->has('tax_name') && $request->tax_name) {
            $query = $query->where('master_tax.tax_name', 'LIKE', '%' . $request->tax_name . '%');
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('master_tax.status', decryptId($request->status));
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
            'tax_name' => $request->tax_name,
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
            'tax_name' => $request->tax_name,
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

    public function UniqueCheck($uom)
    {
        return $this->where(function ($q) use ($uom) {
            $q->where('tax_name', $uom);
        });
    }
    public function ExistuniqueCheck($uom, $id)
    {
        return $this->where(function ($q) use ($uom) {
            $q->where('tax_name', $uom);
        })
            ->where('id', '!=', $id);
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('master_tax.*');


        if ($request->has('tax_id') && $request->tax_id) {
            $query = $query->where('master_tax.tax_id', 'LIKE', '%' . $request->tax_id . '%');
        }
        if ($request->has('tax_name') && $request->tax_name) {
            $query = $query->where('master_tax.tax_name', 'LIKE', '%' . $request->tax_name . '%');
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('master_tax.status', decryptId($request->status));
        }
       
        $query->orderBy('id', 'DESC');

        return $query;
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('master_tax'));
        static::created(function ($model) {
            $uniqueId =  'TAX -' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['tax_id' => $uniqueId]);
        });
    }
}
