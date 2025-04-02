<?php

namespace App\Models\Inspection;

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
                    ->orWhere('status', 'LIKE', '%' . $search . '%');
            });
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


    public function selectUsingName($type)
    {
        $data = $this->select('inspection_static_docno.*')->where('type', $type)
            ->first();
        return $data;
    }

    public function selectOne($id)
    {
        return $this->where('id', $id)->first();
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




    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_static_docno'));
    }
}
