<?php

namespace App\Models\IMS\Incident;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InitialFireIncident extends Model
{
    use  HasFactory;


    protected $table = 'ims_initial_fireincident';
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
        'investigation_assigned',
        'ua_uc_yes_no',
        'ua_or_uc',
        'description_uauc',
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
        $query = $this->select('ims_initial_fireincident.*', 'ims_incident_status.status_name', 'ims_incident_status.to_status', 'ims_incident_status.bg_color', 'ims_initial_fireincident_investigation.risk_analysis');
        $query = $query->leftJoin('ims_incident_status', 'ims_incident_status.id', '=', 'ims_initial_fireincident.incident_status');
        $query = $query->leftJoin('ims_initial_fireincident_investigation', 'ims_initial_fireincident_investigation.incident_id', '=', 'ims_initial_fireincident.id');
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('sr_no', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('sr_no') && $request->sr_no) {
            $query = $query->where('sr_no',  $request->sr_no);
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $unit_id = decryptId($request->unit_id);
            $query = $query->where('unit_id', 'LIKE', $unit_id);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ims_initial_fireincident.created_at', [$startDate, $endDate]);
        } elseif ($request->has('ims_initial_fireincident.') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ims_initial_fireincident.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ims_initial_fireincident.created_at', '<=', $endDate);
        }
        if ($request->has('incident_status') && $request->incident_status) {

            $query = $query->where('incident_status', decryptId($request->incident_status));
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('ims_initial_fireincident.status', decryptId($request->status));
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


    public function updateStatus($fire_incident_id, $incident_status)
    {
        $request = request();

        $update_array = array(
            'incident_status' => $incident_status,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        );
        return $this->where('id', $fire_incident_id)->update($update_array);
    }
    public function investigationassigned($incident_Id)
    {
        $request = request();
        $decryptedTeamMemberIds = is_array($request->team_member)
            ? array_map('decryptId', $request->team_member)
            : [];
        $commaSeparatedTeamMembers = !empty($decryptedTeamMemberIds) ? implode(',', $decryptedTeamMemberIds) : null;
        $update_array = array(
            'investigation_assigned' => $commaSeparatedTeamMembers,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        );
        return $this->where('id', $incident_Id)->update($update_array);
    }

    public function uaucsubmit($id)
    {

        $request = request();
        $ua_or_uc = is_array($request->ua_or_uc) ? implode(',', $request->ua_or_uc) : null;

        $update_array = array(
            'ua_uc_yes_no' => $request->ua_uc_yes_no,
            'ua_or_uc' => $ua_or_uc,
            'description_uauc' => $request->description_uauc
        );

        return $this->where('id', $id)->update($update_array);
    }
    public function actiontakensubmit($id)
    {

        $request = request();

        $update_array = array(
            'action_submission_by' => Auth::id(),
            'action_submission_date' => DBdateformat($request->action_submission_date),
            'action_submission_description' => $request->action_submission_description
        );
        // dd($update_array);
        return $this->where('id', $id)->update($update_array);
    }
    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('ims_initial_fireincident.*', 'ims_incident_status.status_name', 'ims_incident_status.bg_color');
        $query = $query->leftJoin('ims_incident_status', 'ims_incident_status.id', '=', 'ims_initial_fireincident.incident_status');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('sr_no', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('sr_no') && $request->sr_no) {
            $query = $query->where('sr_no',  $request->sr_no);
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $unit_id = decryptId($request->unit_id);
            $query = $query->where('unit_id', 'LIKE', $unit_id);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ims_initial_fireincident.created_at', [$startDate, $endDate]);
        } elseif ($request->has('ims_initial_fireincident.') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ims_initial_fireincident.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ims_initial_fireincident.created_at', '<=', $endDate);
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('incident_status', decryptId($request->status));
        }

        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select(
            'ims_initial_fireincident.*',
            'masters_employee.emp_name as reported_by',
            'masters_department.department_name as reported_department',
            'ims_initial_fireincident_evidence_upload.file_path',
            'ims_master_incident_type.incident_type_name',
            'masters_location.location_name'
        )
            ->where('ims_initial_fireincident.id', $id)
            ->leftJoin('masters_employee', 'masters_employee.id', '=', 'ims_initial_fireincident.reported_name')
            ->leftJoin('masters_department', 'masters_department.id', '=', 'ims_initial_fireincident.department')
            ->leftJoin('ims_master_incident_type', 'ims_master_incident_type.id', '=', 'ims_initial_fireincident.iir_type')
            ->leftJoin('masters_location', 'masters_location.id', '=', 'ims_initial_fireincident.location_id')
            ->leftJoin('ims_initial_fireincident_evidence_upload', 'ims_initial_fireincident_evidence_upload.incident_id', '=', 'ims_initial_fireincident.iir_type')
            ->first();

        return $data;
    }


    public function getEHSVerifyincident($id)
    {
        $data = $this->select('ims_ehs_review.*', 'ims_ehs_review.team_member')
            ->where('ims_initial_fireincident.id', $id)
            ->leftJoin('ims_ehs_review', 'ims_ehs_review.fire_inicdent_report_id', '=', 'ims_initial_fireincident.id')
            ->where('ims_ehs_review.type', 2)
            ->orderBy('id', 'DESC')
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


    // public function getInvestigation($id)
    // {
    //     $data = $this->select('ims_initial_fireincident_investigation.*', 'ims_master_incident_hiramoc.*')
    //         ->where('ims_initial_fireincident.id', $id)
    //         ->leftJoin('ims_initial_fireincident_investigation', 'ims_initial_fireincident_investigation.incident_id', '=', 'ims_initial_fireincident.id')
    //         ->leftJoin('ims_master_incident_hiramoc', 'ims_master_incident_hiramoc.incident_id', '=', 'ims_initial_fireincident.id')
    //         ->first();
    //     if ($data && $data->witness_id) {
    //         $witnessids = explode(',', $data->witness_id);

    //         $employees = DB::table('masters_employee')
    //             ->whereIn('id', $witnessids)
    //             ->pluck('emp_name')
    //             ->toArray();
    //         $data->witness_name = implode(', ', $employees);
    //     }
    //     return $data;
    // }

    public function getInvestigation($id)
    {
        $data = $this->select('ims_initial_fireincident_investigation.*', 'masters_employee.emp_name as responsible_person')
            ->where('ims_initial_fireincident.id', $id)
            ->leftJoin('ims_initial_fireincident_investigation', 'ims_initial_fireincident_investigation.incident_id', '=', 'ims_initial_fireincident.id')
            ->leftJoin('masters_employee', 'masters_employee.id', '=', 'ims_initial_fireincident_investigation.responsible_person_id')
            ->first();

        if ($data) {
            $hiraMocRecords = DB::table('ims_master_incident_hiramoc as hiramoc')
                ->leftJoin('ims_master_hira as hira', 'hiramoc.hira_id', '=', 'hira.id')
                ->leftJoin('ims_master_hira as moc', 'hiramoc.moc_id', '=', 'moc.id')
                ->where('hiramoc.fire_id', $id)
                ->select('hiramoc.hira_id', 'hira.services as hira_name', 'hiramoc.moc_id', 'moc.services as moc_name')
                ->get();

            $lastHira = null;
            $lastMoc = null;
            foreach ($hiraMocRecords as $record) {

                if ($record->hira_id) {
                    $lastHira = [
                        'hira_id' => $record->hira_id,
                        'hira_name' => $record->hira_name,
                        'moc_id' => null,
                        'moc_name' => null,
                    ];
                }
                if ($record->moc_id) {
                    $lastMoc = [
                        'hira_id' => null,
                        'hira_name' => null,
                        'moc_id' => $record->moc_id,
                        'moc_name' => $record->moc_name,
                    ];
                }
            }
            $mergedHiraMoc = [];
            if ($lastHira && $lastMoc) {
                $mergedHiraMoc[] = [
                    'hira_id' => $lastHira['hira_id'],
                    'hira_name' => $lastHira['hira_name'],
                    'moc_id' => $lastMoc['moc_id'],
                    'moc_name' => $lastMoc['moc_name'],
                ];
            } elseif ($lastHira) {
                $mergedHiraMoc[] = $lastHira;
            } elseif ($lastMoc) {
                $mergedHiraMoc[] = $lastMoc;
            }
            $data->hira_moc = $mergedHiraMoc;

            if (!empty($data->witness_id)) {
                $witnessIds = explode(',', $data->witness_id);
                $employees = DB::table('masters_employee')
                    ->whereIn('id', $witnessIds)
                    ->pluck('emp_name')
                    ->toArray();
                $data->witness_name = implode(', ', $employees);
            }
        }

        return $data;
    }


    public function getwhywhy($id)
    {
        $data = $this->select('ims_incident_whywhyanalysis.*')
            ->where('ims_initial_fireincident.id', $id)
            ->leftJoin('ims_incident_whywhyanalysis', 'ims_incident_whywhyanalysis.fire_id', '=', 'ims_initial_fireincident.id')
            ->get();

        return $data;
    }
    public function getfishbone($id)
    {
        $data = $this->select('ims_incident_fishboneanalysis.*')
            ->where('ims_initial_fireincident.id', $id)
            ->leftJoin('ims_incident_fishboneanalysis', 'ims_incident_fishboneanalysis.fire_id', '=', 'ims_initial_fireincident.id')
            ->get();

        return $data;
    }

    public function getrisklevel($id)
    {
        $data = $this->select('ims_master_incident_riskanalysis.*')
            ->where('ims_initial_fireincident.id', $id)
            ->leftJoin('ims_master_incident_riskanalysis', 'ims_master_incident_riskanalysis.fire_id', '=', 'ims_initial_fireincident.id')
            ->first();

        return $data;
    }
    public function getEHSReviewincident($id)
    {
        $data = $this->select('ims_ehs_review.*', 'ims_ehs_review.team_member')
            ->where('ims_initial_fireincident.id', $id)
            ->leftJoin('ims_ehs_review', 'ims_ehs_review.fire_inicdent_report_id', '=', 'ims_initial_fireincident.id')
            ->where('ims_ehs_review.type', 1)
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

    public function getEHSApprovalincident($id)
    {
        $data = $this->select('ims_ehs_review.*', 'ims_ehs_review.team_member')
            ->where('ims_initial_fireincident.id', $id)
            ->leftJoin('ims_ehs_review', 'ims_ehs_review.fire_inicdent_report_id', '=', 'ims_initial_fireincident.id')
            ->where('ims_ehs_review.type', 3)
            ->orderBy('id', 'DESC')
            ->first();
        return $data;
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ims_initial_fireincident'));

        static::created(function ($model) {

            $uniqueId = 'FIRE-INCIDENT-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['sr_no' => $uniqueId]);
        });
    }
}
