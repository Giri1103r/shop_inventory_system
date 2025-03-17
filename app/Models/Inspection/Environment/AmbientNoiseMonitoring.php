<?php

namespace App\Models\Inspection\Environment;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class AmbientNoiseMonitoring extends Model
{

    protected $table = 'inspection_ambient_noise_monitoring';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'ambient_noise_id',
        'doc_no',
        'issue_date',
        'rev_dt',
        'location_id',
        'unit_id',
        'noise_level_dba',
        'date_of_monitoring',
        'next_due_date_of_monitoring',
        'noise_level_dba_day',
        'noise_level_dba_night',
        'date_of_monitoring_day',
        'date_of_monitoring_night',
        'next_due_date_of_monitoring_date',
        'next_due_date_of_monitoring_night',
        'act_rule_id',
        'remark',
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
        $query = $this->select('inspection_ambient_noise_monitoring.*', 'masters_location.location_name', 'masters_unit.unit_name');
        $query = $query->leftJoin('masters_location', 'masters_department.location_id', '=', 'masters_location.id');
        $query = $query->leftJoin('masters_unit', 'masters_department.unit_id', '=', 'masters_unit.id');
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('masters_location.location_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_ambient_noise_monitoring.ambient_noise_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('ambient_noise_id') && $request->ambient_noise_id) {
            $query = $query->where('inspection_ambient_noise_monitoring.ambient_noise_id', decryptId($request->ambient_noise_id));
        }
        if ($request->has('location_id') && $request->location_id) {
            $query = $query->where('inspection_ambient_noise_monitoring.location_id', decryptId($request->location_id));
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('inspection_ambient_noise_monitoring.unit_id', decryptId($request->unit_id));
        }

        if ($request->has('status') && $request->status) {
            $query = $query->where('inspection_ambient_noise_monitoring.status', decryptId($request->status));
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
        $query = $this->select('inspection_ambient_noise_monitoring.*', 'masters_location.location_name', 'masters_unit.unit_name');
        $query = $query->leftJoin('masters_location', 'masters_department.location_id', '=', 'masters_location.id');
        $query = $query->leftJoin('masters_unit', 'masters_department.unit_id', '=', 'masters_unit.id');
        // dd($query);

        if ($request->has('ambient_noise_id') && $request->ambient_noise_id) {
            $query = $query->where('inspection_ambient_noise_monitoring.ambient_noise_id', decryptId($request->ambient_noise_id));
        }
        if ($request->has('location_id') && $request->location_id) {
            $query = $query->where('inspection_ambient_noise_monitoring.location_id', decryptId($request->location_id));
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('inspection_ambient_noise_monitoring.unit_id', decryptId($request->unit_id));
        }

        if ($request->has('status') && $request->status) {
            $query = $query->where('inspection_ambient_noise_monitoring.status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');
        return  $query->get();
    }

    public function selectOne($id)
    {
        $data = $this->select('inspection_ambient_noise_monitoring.*', 'masters_location.location_name', 'masters_unit.unit_name')
            ->leftJoin('masters_location', 'masters_department.location_id', '=', 'masters_location.id')
            ->leftJoin('masters_unit', 'masters_department.unit_id', '=', 'masters_unit.id')->where('inspection_ambient_noise_monitoring.id', $id)
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
        static::addGlobalScope(new TrashScope('inspection_ambient_noise_monitoring'));

        static::created(function ($model) {

            $uniqueId = 'SUBCAT-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['subcategory_id' => $uniqueId]);
        });
    }
}
