<?php

namespace App\Models\IMS\Master;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IncidentType extends Model
{
    use  HasFactory;


    protected $table = 'ims_master_incident_type';
    protected $primaryKey = 'id';

    protected $fillable = [
        'incident_type_id',
        'incident_type_name',
        'short_name',
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
        $query = $this->select('ims_master_incident_type.*');
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('incident_type_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('incident_type_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('short_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('incident_type_id') && $request->incident_type_id) {
            $query = $query->where('incident_type_id',  $request->incident_type_id);
        }
        if ($request->has('incident_type_name') && $request->incident_type_name) {
            $query = $query->where('incident_type_name', 'LIKE', $request->incident_type_name);
        }
        if ($request->has('short_name') && $request->short_name) {
            $query = $query->where('short_name', 'LIKE', $request->short_name);
        }
        if ($request->has('from_date') && $request->from_date) {
            $fromDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay();
            $query = $query->where('ims_master_incident_type.created_at', '>=', $fromDate);
        }
        
        if ($request->has('to_date') && $request->to_date) {
            $toDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay();
            $query = $query->where('ims_master_incident_type.created_at', '<=', $toDate);
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('ims_master_incident_type.status', decryptId($request->status));
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

    public function uniqueCheck($incident_type_name)
    {
        return $this->where('incident_type_name', $incident_type_name)
            ->exists();
    }

    public function existUniqueCheck($incident_type_name,$id)
    {
        return $this->where(function ($query) use ($incident_type_name) {
            $query->where('incident_type_name', $incident_type_name); // Fixed here
        })
            ->where('id', '!=', $id)
            ->exists();
    }
    public function store()
    {
        $request = request();

        $insert_array = array(
            'incident_type_id' => $request->incident_type_id,
            'incident_type_name' => $request->incident_type_name,
            'short_name' => $request->short_name,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'incident_type_id' => $request->incident_type_id,
            'incident_type_name' => $request->incident_type_name,
            'short_name' => $request->short_name,
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
        $query = $this->select('ims_master_incident_type.*');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('incident_type_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('incident_type_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('short_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('incident_type_id') && $request->incident_type_id) {
            $query = $query->where('incident_type_id',  $request->incident_type_id);
        }
        if ($request->has('incident_type_name') && $request->incident_type_name) {
            $query = $query->where('incident_type_name', 'LIKE', $request->incident_type_name);
        }
        if ($request->has('short_name') && $request->short_name) {
            $query = $query->where('short_name', 'LIKE', $request->short_name);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('created_at', '<=', $endDate);
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('status', decryptId($request->status));
        }

        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select(
            'ims_master_incident_type.*'
        )
            ->where('ims_master_incident_type.id', $id)
            ->first();

        return $data;
    }




    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ims_master_incident_type'));

        static::created(function ($model) {

            $uniqueId = 'INC-TYPE-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['incident_type_id' => $uniqueId]);
        });
    }
}
