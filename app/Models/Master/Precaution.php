<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Precaution extends Model
{
    use  HasFactory;


    protected $table = 'masters_ptw_precaution';
    protected $primaryKey = 'id';

    protected $fillable = [
        'precaution',
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
        $query = $this->select('masters_ptw_precaution.*');
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('precaution', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('precaution') && $request->precaution) {
            $query = $query->where('precaution', 'LIKE', '%' . $request->precaution . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('status', decryptId($request->status));
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

        return $this->where('precaution',  $data)->get();
    }

    public function ExistuniqueCheck($data,$id)
    {
        return $this->where('precaution',  $data)
        ->where('id', '!=', $id)
        ->get();
    }

    public function store()
    {
        $request = request();

        $insert_array = array(
            'precaution' => $request->precaution,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'precaution' => $request->precaution,
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
        $query = $this->select('masters_ptw_precaution.*');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhereRaw('precaution LIKE "%' . $search . '%"');
            });
        }

        if ($request->has('precaution') && $request->precaution) {
            $query = $query->where('precaution', 'LIKE', '%' . $request->precaution . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('masters_ptw_precaution.status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');
        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select(
            'masters_ptw_precaution.*'
        )
            ->where('masters_ptw_precaution.id', $id)
            ->first();

        return $data;
    }
    public function selectchecklist()
    {

        $data =  $this->select('masters_ptw_precaution.*')
            ->where('masters_ptw_precaution.status', '1')
            ->where('masters_ptw_precaution.trash', 'NO')
            ->get();

        return $data;
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('masters_ptw_precaution'));

        // static::created(function ($model) {

        //     $uniqueId = 'CMP-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
        //     $model->update(['company_id' => $uniqueId]);
        // });
    }
}
