<?php

namespace App\Models\IMS\Incident;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InitialIncident extends Model
{
    use  HasFactory;


    protected $table = 'ims_initial_incident';
    protected $primaryKey = 'id';

    protected $fillable = [
        'sr_no',
        'incident_date_time',
        'unit_id',
        'shift',
        'location_id',
        'exact_location',
        'iir_type',
        'reported_name',
        'designation',
        'department',
        'employee_code',
        'time_of_reporting',
        'reporting_media',
        'reporting_media_others',
        'brief_description',
        'incident_status',
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
        $query = $this->select('ims_initial_incident.*', 'ims_incident_status.status_name', 'ims_incident_status.bg_color');
        $query = $query->leftJoin('ims_incident_status', 'ims_incident_status.id', '=', 'ims_initial_incident.incident_status');
        // $query = $query->leftJoin('ims_initial_incident_investigation', 'ims_initial_incident_investigation.incident_id', '=', 'ims_initial_incident.id');
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

    public function existUniqueCheck($incident_type_name, $id)
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
        if (is_array($request->reporting_media)) {
            $reporting_media = implode(',', array_map(function ($item) {
                return $item;
            }, $request->reporting_media));
        } else {

            $reporting_media = decryptId($request->reporting_media);
        }
        $insert_array = array(
            'incident_date_time' => DBdatetimeformat($request->incident_date_time),
            'unit_id' => decryptId($request->unit_id),
            'shift' => $request->shift,
            'location_id' => decryptId($request->location_id),
            'exact_location' => $request->exact_location,
            'iir_type' => $request->iir_type,
            'reported_name' => decryptId($request->reported_name),
            'designation' => $request->designation,
            'department' => $request->department,
            'employee_code' => $request->employee_code,
            'time_of_reporting' => $request->time_of_reporting,
            'reporting_media' => $reporting_media,
            'reporting_media_others' => $request->reporting_media_others,
            'brief_description' => $request->brief_description,
            'incident_status' => STATUS_INCIDENT_REPORT,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        // dd($request);
        if (is_array($request->reporting_media) && !empty($request->reporting_media)) {
            $reporting_media = implode(',', array_map(function ($item) {
                return $item;
            }, $request->reporting_media));
        } elseif (!empty($request->reporting_media)) {
            $reporting_media = $request->reporting_media;
        } else {
            $reporting_media = null; // Ensure it is NULL if empty
        }

        $update_array = array(
            'incident_date_time' => DBdatetimeformat($request->incident_date_time),
            'unit_id' => decryptId($request->unit_id),
            'shift' => $request->shift,
            'location_id' => decryptId($request->location_id),
            'exact_location' => $request->exact_location,
            'iir_type' => $request->iir_type,
            'reported_name' => decryptId($request->reported_name),
            'designation' => $request->designation,
            'department' => $request->department,
            'employee_code' => $request->employee_code,
            'time_of_reporting' => $request->time_of_reporting,
            'reporting_media' => $reporting_media,
            'reporting_media_others' => $request->reporting_media_others,
            'brief_description' => $request->brief_description,
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
        $query = $this->select('ims_initial_incident.*');
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
            'ims_initial_incident.*',
            'masters_employee.emp_name as reported_by',
            'masters_department.department_name as reported_department',
            'ims_initial_incident_evidence_upload.file_path'
        )
            ->where('ims_initial_incident.id', $id)
            ->leftJoin('masters_employee', 'masters_employee.id', '=', 'ims_initial_incident.reported_name')
            ->leftJoin('masters_department', 'masters_department.id', '=', 'ims_initial_incident.department')
            ->leftJoin('ims_initial_incident_evidence_upload', 'ims_initial_incident_evidence_upload.incident_id', '=', 'ims_initial_incident.id')
            ->first();

        return $data;
    }


    public function getEHSVerifyincident($id)
    {
        $data = $this->select('ims_ehs_review.*', 'ims_ehs_review.team_member')
            ->where('ims_initial_incident.id', $id)
            ->leftJoin('ims_ehs_review', 'ims_ehs_review.inicdent_report_id', '=', 'ims_initial_incident.id')
            ->where('ims_ehs_review.type',1)
            ->first();

        if ($data && $data->team_member) {
            $teamMemberIds = explode(',', $data->team_member);

            $employees = DB::table('masters_employee')
                ->whereIn('id', $teamMemberIds)
                ->pluck('emp_name')
                ->toArray();
            $data->team_member_names = implode(', ', $employees);
        }

        return $data;
    }


    public function getInvestigation($id)
    {
        $data = $this->select('ims_initial_incident_investigation.*','ims_master_incident_hiramoc.*')
            ->where('ims_initial_incident.id', $id)
            ->leftJoin('ims_initial_incident_investigation', 'ims_initial_incident_investigation.incident_id', '=', 'ims_initial_incident.id')
            ->leftJoin('ims_master_incident_hiramoc', 'ims_master_incident_hiramoc.incident_id', '=', 'ims_initial_incident.id')
            ->first();
            
        return $data;
    }

    public function updateStatus($incident_Id, $incident_status)
    {
        $request = request();

        $update_array = array(
            'incident_status' => $incident_status,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        );
        return $this->where('id', $incident_Id)->update($update_array);
    }

    public function getEHSReviewincident($id)
    {
        $data = $this->select('ims_ehs_review.*', 'ims_ehs_review.team_member')
            ->where('ims_initial_incident.id', $id)
            ->leftJoin('ims_ehs_review', 'ims_ehs_review.inicdent_report_id', '=', 'ims_initial_incident.id')
            ->where('ims_ehs_review.type',2)
            ->first();

        if ($data && $data->team_member) {
            $teamMemberIds = explode(',', $data->team_member);

            $employees = DB::table('masters_employee')
                ->whereIn('id', $teamMemberIds)
                ->pluck('emp_name')
                ->toArray();
            $data->team_member_names = implode(', ', $employees);
        }

        return $data;
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ims_initial_incident'));

        static::created(function ($model) {

            $uniqueId = 'INCIDENT-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['sr_no' => $uniqueId]);
        });
    }
}
