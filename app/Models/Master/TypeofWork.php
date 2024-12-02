<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TypeofWork extends Model
{
    use  HasFactory;


    protected $table = 'masters_ptw_typeofwork';
    protected $primaryKey = 'id';

    protected $fillable = [
        'work_name',
        'description',
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
        $query = $this->select('masters_ptw_typeofwork.*');
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('checklist', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('checklist') && $request->checklist) {
            $query = $query->where('checklist', 'LIKE', '%' . $request->checklist . '%');
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

        return $this->where('checklist',  $data)->get();
    }

    public function ExistuniqueCheck($data,$id)
    {
        return $this->where('checklist',  $data)
        ->where('id', '!=', $id)
        ->get();
    }

    public function store()
    {
        $request = request();

        $insert_array = array(
            'work_name' => $request->work_name,
            'description' => $request->description,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'work_name' => $request->work_name,
            'description' => $request->description,
            'updated_by' => Auth::id()
        );

        // dd($update_array);
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
        $query = $this->select('masters_ptw_typeofwork.*');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhereRaw('checklist LIKE "%' . $search . '%"');
            });
        }

        if ($request->has('checklist') && $request->checklist) {
            $query = $query->where('checklist', 'LIKE', '%' . $request->checklist . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('masters_ptw_typeofwork.status', decryptId($request->status));
        }

        return  $query->get();
    }

    // public function selectOne($id)
    // {

    //     $data = $this->select(
    //         'masters_ptw_typeofwork.*'
    //     )
    //         ->where('masters_ptw_typeofwork.id', $id)
    //         ->first();

    //     return $data;
    // }

    public function selectOne($id)
    {

        $data =  $this->select('masters_ptw_typeofwork.*', 'masters_ptw_typeofwork_upload.file_path',)->leftjoin('masters_ptw_typeofwork_upload', 'masters_ptw_typeofwork_upload.typeofwork_id', '=', 'masters_ptw_typeofwork.id')
            ->where('masters_ptw_typeofwork.id', $id)
            ->first();

        return $data;
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('masters_ptw_typeofwork'));

        // static::created(function ($model) {

        //     $uniqueId = 'CMP-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
        //     $model->update(['company_id' => $uniqueId]);
        // });
    }
}
