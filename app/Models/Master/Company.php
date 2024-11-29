<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use  HasFactory;


    protected $table = 'company_management';
    protected $primaryKey = 'id';

    protected $fillable = [
        'company_id',
        'company_name',
        'short_name',
        'address',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('company_management.*');
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('company_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('company_name', 'LIKE', '%' . $search . '%');
            });
        }


        if ($request->has('company_id') && $request->company_id) {
            $query = $query->where('company_id', 'LIKE', '%' . $request->company_id . '%');
        }
        if ($request->has('company_name') && $request->company_name) {
            $query = $query->where('company_name', 'LIKE', '%' . $request->company_name . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('status', decryptId($request->status));
        }
        $data_count = $query->count();
        $total_records = $data_count;

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

        return $this->where('company_name',  $data)->get();
    }

    public function ExistuniqueCheck($data, $id)
    {
        return $this->where('company_name',  $data)
            ->where('id', '!=', $id)
            ->get();
    }

    public function store()
    {
        $request = request();

        $insert_array = array(
            'company_id' => $request->company_id,
            'company_name' => $request->company_name,
            'short_name' => $request->short_name,
            'address' => $request->address,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'company_id' => $request->company_id,
            'company_name' => $request->company_name,
            'short_name' => $request->short_name,
            'address' => $request->address,
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
        $query = $this->select('company_management.*');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhere('company_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('company_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('company_id') && $request->company_id) {
            $query = $query->where('company_id', 'LIKE', '%' . $request->company_id . '%');
        }
        if ($request->has('company_name') && $request->company_name) {
            $query = $query->where('company_name', 'LIKE', '%' . $request->company_name . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('company_management.status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select(
            'company_management.*'
        )
            ->where('company_management.id', $id)
            ->first();

        return $data;
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('company_management'));

        static::created(function ($model) {

            $uniqueId = 'CMP-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['company_id' => $uniqueId]);
        });
    }
}
