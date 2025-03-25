<?php

namespace App\Models\Inspection\Environment;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Environment extends Model
{

    protected $table = 'inspection_environment_table';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'type',
        'environment_no',
        'approval_status',
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

    public function list($type)
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_environment_table.*')->where('type', $type);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->where('environment_no', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('environment_no') && $request->environment_no) {
            $query = $query->where('id', decryptId($request->environment_no));
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

    public function store($env_no, $type)
    {
        $request = request();
        $insert_array = [
            'type' => $type,
            'environment_no' => $env_no,
            'approval_status' => 1,
            'created_by' => Auth::id(),
        ];
        return $this->create($insert_array);
    }




    public function exportdata($type)
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_environment_table.*')->where('type', $type);
        // dd($query);

        if ($request->has('environment_no') && $request->environment_no) {
            $query = $query->where('id', decryptId($request->environment_no));
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');
        return  $query->get();
    }

    public function selectOne($id, $type)
    {
        $data = $this->select('inspection_environment_table.*')->where('type', $type)->where('status', 1)
            ->first();
        return $data;
    }

    public function statuschange($id, $envtype)
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

        return $this->where('type', $envtype)->where('id', $id)->update($update_data);
    }


    // protected static function booted()
    // {
    //     static::addGlobalScope(new TrashScope('inspection_environment_table'));

    //     static::created(function ($model) {

    //         $uniqueId = 'AMBIENT-NOISE-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
    //         $model->update(['environment_no' => $uniqueId]);
    //     });
    // }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_environment_table'));

        static::created(function ($model) {
            $prefix = 'GENERIC'; 
            if ($model->type == 'AMBIENT') {
                $prefix = 'AMBIENT-NOISE';
            } elseif ($model->type == 'WORK') {
                $prefix = 'WORK-NOISE';
            }

            $uniqueId = $prefix . '-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);

            // Update the model
            $model->update(['environment_no' => $uniqueId]);
        });
    }
}
