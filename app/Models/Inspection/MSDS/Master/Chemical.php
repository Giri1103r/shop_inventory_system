<?php

namespace App\Models\Inspection\MSDS\Master;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Chemical extends Model
{
    protected $table = 'inspection_msds_master_chemicals';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'chemical',
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
        $query = $this->select('inspection_msds_master_chemicals.*');
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('chemical', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('chemical') && $request->chemical) {
            $query = $query->where('chemical', 'LIKE', '%' . $request->chemical . '%');
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

        return $this->where('chemical',  $data)->get();
    }

    public function ExistuniqueCheck($data, $id)
    {
        return $this->where('chemical',  $data)
            ->where('id', '!=', $id)
            ->get();
    }

    public function store()
    {
        $request = request();

        $insert_array = array(
            'chemical' => $request->chemical,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'chemical' => $request->chemical,
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
        $query = $this->select('inspection_msds_master_chemicals.*');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhereRaw('chemical LIKE "%' . $search . '%"');
            });
        }

        if ($request->has('chemical') && $request->chemical) {
            $query = $query->where('chemical', 'LIKE', '%' . $request->chemical . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('inspection_msds_master_chemicals.status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');
        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select(
            'inspection_msds_master_chemicals.*'
        )
            ->where('inspection_msds_master_chemicals.id', $id)
            ->first();

        return $data;
    }

    public function getChemicals()
    {
        $data =  $this->get();

        return $data;
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_msds_master_chemicals'));
    }
}
