<?php

namespace App\Models\IMS\Master;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hira extends Model
{
    use  HasFactory;


    protected $table = 'ims_master_hira';
    protected $primaryKey = 'id';

    protected $fillable = [
        'incident_id',
        'accident_id',
        'fire_id',
        'hiramoc_id',
        'sr_no',
        'services',
        'narration',
        'hazard_description',
        'hazard_type',
        'severity',
        'risk_consequence',
        'likelihood',
        'risk_levels',
        'current_controls',
        'type_controls',
        'legal_req',
        'risk_rating',
        'additionl_control',
        'nature_change',
        'implement_change',
        'control_change',
        'hira_status',
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
        $query = $this->select('ims_master_hira.*', 'ims_master_hira_status.status_name', 'ims_master_hira_status.bg_color');
        $query = $query->leftJoin('ims_master_hira_status', 'ims_master_hira_status.id', '=', 'ims_master_hira.hira_status');
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('ims_master_hira.sr_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('ims_master_hira.services', 'LIKE', '%' . $search . '%')
                    ->orWhere('ims_master_hira.hazard_type', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('sr_no') && $request->sr_no) {
            $query = $query->where('sr_no',  $request->sr_no);
        }
        if ($request->has('services') && $request->services) {
            $query = $query->where('services', 'LIKE', $request->services);
        }
        if ($request->has('hazard_type') && $request->hazard_type) {
            $query = $query->where('hazard_type', 'LIKE', $request->hazard_type);
        }
        if ($request->has('from_date') && $request->from_date) {
            $fromDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay();
            $query = $query->where('ims_master_hira.created_at', '>=', $fromDate);
        }

        if ($request->has('to_date') && $request->to_date) {
            $toDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay();
            $query = $query->where('ims_master_hira.created_at', '<=', $toDate);
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('ims_master_hira.status', decryptId($request->status));
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

    public function uniqueCheck($services)
    {
        return $this->where('services', $services)
            ->exists();
    }

    public function existUniqueCheck($services, $id)
    {
        return $this->where(function ($query) use ($services) {
            $query->where('services', $services); // Fixed here
        })
            ->where('id', '!=', $id)
            ->exists();
    }
    public function store()
    {
        $request = request();
        if (Auth::id() == ROLE_SUPERADMIN) {
            $hira_status = 2;
        } else {
            $hira_status = 1;
        }
        $insert_array = array(
            'incident_id' => decryptId($request->incident_id),
            'accident_id' => decryptId($request->accident_id),
            'fire_id' => decryptId($request->fire_id),
            'hiramoc_id' => decryptId($request->hiramoc_id),
            'sr_no' => $request->sr_no,
            'services' => $request->services,
            'narration' => $request->narration,
            'hazard_description' => $request->hazard_description,
            'hazard_type' => $request->hazard_type,
            'severity' => $request->severity,
            'risk_consequence' => $request->risk_consequence,
            'likelihood' => $request->likelihood,
            'risk_levels' => $request->risk_levels,
            'current_controls' => $request->current_controls,
            'type_controls' => $request->type_controls,
            'legal_req' => $request->legal_req,
            'risk_rating' => $request->risk_rating,
            'additionl_control' => $request->additionl_control,
            'nature_change' => $request->nature_change,
            'implement_change' => $request->implement_change,
            'control_change' => $request->control_change,
            'hira_status' => $hira_status,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'sr_no' => $request->sr_no,
            'services' => $request->services,
            'narration' => $request->narration,
            'hazard_description' => $request->hazard_description,
            'hazard_type' => $request->hazard_type,
            'severity' => $request->severity,
            'risk_consequence' => $request->risk_consequence,
            'likelihood' => $request->likelihood,
            'risk_levels' => $request->risk_levels,
            'current_controls' => $request->current_controls,
            'type_controls' => $request->type_controls,
            'legal_req' => $request->legal_req,
            'risk_rating' => $request->risk_rating,
            'additionl_control' => $request->additionl_control,
            'nature_change' => $request->nature_change,
            'implement_change' => $request->implement_change,
            'control_change' => $request->control_change,
            'updated_by' => Auth::id()
        );
        return $this->where('id', $id)->update($update_array);
    }
    public function updateStatus($id, $hira_status)
    {
        $request = request();

        $update_array = array(
            'hira_status' => $hira_status,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        );
        return $this->where('id', decryptId($id))->update($update_array);
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
        $query = $this->select('ims_master_hira.*');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('sr_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('services', 'LIKE', '%' . $search . '%')
                    ->orWhere('hazard_type', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('sr_no') && $request->sr_no) {
            $query = $query->where('sr_no',  $request->sr_no);
        }
        if ($request->has('services') && $request->services) {
            $query = $query->where('services', 'LIKE', $request->services);
        }
        if ($request->has('hazard_type') && $request->hazard_type) {
            $query = $query->where('hazard_type', 'LIKE', $request->hazard_type);
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
            'ims_master_hira.*'
        )
            ->where('ims_master_hira.id', $id)
            ->first();

        return $data;
    }




    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ims_master_hira'));

        static::created(function ($model) {

            $uniqueId = 'HIRA-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['sr_no' => $uniqueId]);
        });
    }
}
