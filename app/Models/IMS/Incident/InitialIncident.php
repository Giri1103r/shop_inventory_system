<?php

namespace App\Models\IMS\Incident;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Master\Employee;

class InitialIncident extends Model
{
    use  HasFactory;


    protected $table = 'ims_initial_incident';
    protected $primaryKey = 'id';

    protected $fillable = [
        'sr_no',
        'random_id',
        'incident_date_time',
        'company_id',
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
        'immediate_action_taken',
        'anyone_injured',
        'investigation_assigned',
        'investigation_reported_by',
        'target_date',
        'choose_assignee',
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
        $query = $this->select('ims_initial_incident.*', 'ims_incident_status.status_name', 'ims_incident_status.bg_color', 'ims_initial_incident_investigation.risk_analysis', 'ims_injury_details.nature_of_injury');
        $query = $query->leftJoin('ims_incident_status', 'ims_incident_status.id', '=', 'ims_initial_incident.incident_status');
        $query = $query->leftJoin('ims_initial_incident_investigation', 'ims_initial_incident_investigation.incident_id', '=', 'ims_initial_incident.id');
        $query = $query->leftJoin('ims_injury_details', 'ims_injury_details.incident_id', '=', 'ims_initial_incident.id');
        $org_total =  $query;
        $org_total_counts = $org_total->count();
        /**
         * Role Based list view condition start
         */
        if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_ADMIN) || CheckUserRole(ROLE_EHS_HEAD)) {
            $query->where('ims_initial_incident.status', '1');
        } elseif (CheckUserRole(ROLE_EHS_OFFICER)) {
            $query->where('ims_initial_incident.created_by', Auth::user()->id);
        } else {

            $query->where('ims_initial_incident.created_by', Auth::user()->id);
        }
        $condition =  decryptId($request->condition);
        // if ($request->has('type') && $request->type) {
        if ($condition  == 1) {
            $type = ($request->type);
            if ($type != ALL) {
                $query = $query->where('ims_injury_details.nature_of_injury', decryptId($type));
            }
        } else if ($condition  == 2) {
            $type = ($request->type);
            if ($type != ALL) {
                $query = $query->whereRaw("FIND_IN_SET(?, ims_initial_incident.ua_or_uc)", [decryptId($type)]);
            }
        } else if ($condition  == 3) {
            $type = ($request->type);
            if ($type != ALL) {
                $query = $query->where('ims_initial_incident.iir_type', decryptId($type));
            }
        } else if ($condition  == 4) {
            $type = ($request->type);
            if ($type != ALL) {
                $query = $query->where('ims_initial_incident.iir_type', decryptId($type));
            }
        }
        

        // }
        /**
         * Role Based list view condition end
         */
        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('sr_no', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('sr_no') && $request->sr_no) {
            $query = $query->where('ims_initial_incident.sr_no',  $request->sr_no);
        }


        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ims_initial_incident.created_at', [$startDate, $endDate]);
        } elseif ($request->has('ims_initial_incident.') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ims_initial_incident.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ims_initial_incident.created_at', '<=', $endDate);
        }
        if ($request->has('incident_status') && $request->incident_status) {

            $query = $query->where('ims_initial_incident.incident_status', decryptId($request->incident_status));
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('ims_initial_incident.status', decryptId($request->status));
        }

        if ($request->has('dash_iirtype_id') && $request->dash_iirtype_id) {
            $query = $query->where('ims_initial_incident.iir_type', ($request->dash_iirtype_id));
        }


        if ($request->has('dash_injuryType') && $request->dash_injuryType) {
            $query = $query->where('ims_injury_details.nature_of_injury', $request->dash_injuryType);
        }

        if ($request->has('dash_month') && $request->dash_month) {
            $query = $query->whereMonth('ims_initial_incident.created_at', $request->dash_month);
        }

        if ($request->has('unit_id') && $request->unit_id) {

            $query = $query->where('ims_initial_incident.unit_id', decryptId($request->unit_id));
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


    public function investigationlist()
    {
        $request = request();
        $search = '';
        $query = $this->select('ims_initial_incident.*', 'ims_incident_status.status_name', 'ims_incident_status.bg_color', 'ims_initial_incident_investigation.risk_analysis');
        $query = $query->leftJoin('ims_incident_status', 'ims_incident_status.id', '=', 'ims_initial_incident.incident_status');
        $query = $query->leftJoin('ims_initial_incident_investigation', 'ims_initial_incident_investigation.incident_id', '=', 'ims_initial_incident.id');
        $org_total =  $query;
        $org_total_counts = $org_total->count();
        /**
         * Role Based list view condition start
         */
        if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_ADMIN) || CheckUserRole(ROLE_EHS_HEAD)) {
            // $query->where('ims_initial_incident.status', '1');
        } else {
            // $userId = Auth::id();
            // $userLoginId = Auth::user()->employee_id;

            // $employeeId = Employee::where('emp_id', $userLoginId)->value('id');

            // $query->where(function ($q) use ($userId, $employeeId) {
            //     $q->where('ims_initial_incident.created_by', $userId)
            //         ->orWhereRaw("FIND_IN_SET(?, investigation_assigned)", [$employeeId])
            //         ->orWhereRaw("FIND_IN_SET(?, choose_assignee)", [$employeeId]);
            // });

            $query->where('ims_initial_incident.investigation_reported_by', Auth::user()->id);
        }


        /**
         * Role Based list view condition end
         */
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
            $query->whereBetween('ims_initial_incident.created_at', [$startDate, $endDate]);
        } elseif ($request->has('ims_initial_incident.') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ims_initial_incident.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ims_initial_incident.created_at', '<=', $endDate);
        }
        if ($request->has('incident_status') && $request->incident_status) {

            $query = $query->where('incident_status', decryptId($request->incident_status));
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('ims_initial_incident.status', decryptId($request->status));
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
            'random_id' => $request->random_id,
            'incident_date_time' => DBdatetimeformat($request->incident_date_time),
            'unit_id' => decryptId($request->unit_id),
            'company_id' => decryptId($request->company_id),
            'shift' => $request->shift,
            'location_id' => decryptId($request->location_id),
            'exact_location' => $request->exact_location,
            'iir_type' => $request->iir_type,
            'employee_code' => $request->employee_code,
            'reported_name' => $request->reported_name,
            'designation' => $request->designation,
            'department' => $request->department,
            'time_of_reporting' => $request->time_of_reporting,
            'reporting_media' => $reporting_media,
            'reporting_media_others' => $request->reporting_media_others,
            'brief_description' => $request->brief_description,
            'immediate_action_taken' => $request->immediate_action_taken,
            'anyone_injured' => $request->anyone_injured,
            'incident_status' => STATUS_INCIDENT_REPORT,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function getTotalIncidentCountData($request)
    {
        $query = DB::table('ims_initial_incident')
            ->select(
                'ims_initial_incident.unit_id',
                'masters_unit.unit_name',
                'ims_initial_incident.iir_type as incident_type_id',
                'ims_master_incident_type.incident_type_name',
                DB::raw('COUNT(ims_initial_incident.id) as incident_count')
            )
            ->leftJoin('masters_unit', 'masters_unit.id', '=', 'ims_initial_incident.unit_id')
            ->leftJoin('ims_master_incident_type', 'ims_master_incident_type.id', '=', 'ims_initial_incident.iir_type')
            ->whereNotNull('ims_initial_incident.id');

        // Apply company Filter
        if ($request->CompanyId != null) {
            $company_id = decryptId($request->CompanyId);
            $query->where('ims_initial_incident.company_id', $company_id);
        }

        // Date Filters
        if ($request->Fromdate && $request->Todate) {
            $query->whereBetween('ims_initial_incident.created_at', [
                DBdateformat($request->Fromdate),
                DBdateformat($request->Todate) . ' 23:59:59'
            ]);
        } elseif ($request->Fromdate) {
            $query->where('ims_initial_incident.created_at', '>=', DBdateformat($request->Fromdate));
        } elseif ($request->Todate) {
            $query->where('ims_initial_incident.created_at', '<=', DBdateformat($request->Todate) . ' 23:59:59');
        }

        return $query->groupBy(
            'ims_initial_incident.unit_id',
            'masters_unit.unit_name',
            'ims_initial_incident.iir_type',
            'ims_master_incident_type.incident_type_name'
        )
            ->orderBy('masters_unit.unit_name')
            ->get();
    }

    public function getIncidentTypeCountData($request)
    {
        $query = DB::table('ims_initial_incident')
            ->select(
                'ims_initial_incident.iir_type as incident_type_id',
                'ims_master_incident_type.incident_type_name',
                DB::raw('COUNT(ims_initial_incident.id) as incident_count')
            )
            ->leftJoin('ims_master_incident_type', 'ims_master_incident_type.id', '=', 'ims_initial_incident.iir_type')
            ->whereNotNull('ims_initial_incident.id');

        // Apply company Filter
        if ($request->CompanyId) {
            $company_id = decryptId($request->CompanyId);
            $query->where('ims_initial_incident.company_id', $company_id);
        }

        // Date Filters
        if ($request->Fromdate && $request->Todate) {
            $query->whereBetween('ims_initial_incident.created_at', [
                DBdateformat($request->Fromdate),
                DBdateformat($request->Todate) . ' 23:59:59'
            ]);
        } elseif ($request->Fromdate) {
            $query->where('ims_initial_incident.created_at', '>=', DBdateformat($request->Fromdate));
        } elseif ($request->Todate) {
            $query->where('ims_initial_incident.created_at', '<=', DBdateformat($request->Todate) . ' 23:59:59');
        }

        return $query->groupBy('ims_master_incident_type.incident_type_name', 'ims_initial_incident.iir_type')
            ->get();
    }
    public function getHeatmapImsCountData($request)
    {
        $query = DB::table('ims_initial_incident')
            ->select(
                DB::raw("MONTH(ims_initial_incident.created_at) as month"),
                'ims_injury_details.nature_of_injury',
                DB::raw('COUNT(ims_initial_incident.id) as incident_count')
            )
            ->leftJoin('ims_injury_details', 'ims_injury_details.incident_id', '=', 'ims_initial_incident.id');

        // Apply company Filter
        if ($request->CompanyId) {
            $company_id = decryptId($request->CompanyId);
            $query->where('ims_initial_incident.company_id', $company_id);
        }

        // Date Filters
        if ($request->Fromdate && $request->Todate) {
            $query->whereBetween('ims_initial_incident.created_at', [
                DBdateformat($request->Fromdate),
                DBdateformat($request->Todate) . ' 23:59:59'
            ]);
        } elseif ($request->Fromdate) {
            $query->where('ims_initial_incident.created_at', '>=', DBdateformat($request->Fromdate));
        } elseif ($request->Todate) {
            $query->where('ims_initial_incident.created_at', '<=', DBdateformat($request->Todate) . ' 23:59:59');
        }

        return $query->groupBy('month', 'ims_injury_details.nature_of_injury')
            ->orderBy('month')
            ->get();
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
            'company_id' => decryptId($request->company_id),
            'shift' => $request->shift,
            'location_id' => decryptId($request->location_id),
            'exact_location' => $request->exact_location,
            'iir_type' => $request->iir_type,
            'reported_name' => $request->reported_name,
            'designation' => $request->designation,
            'department' => $request->department,
            'employee_code' => $request->employee_code,
            'time_of_reporting' => $request->time_of_reporting,
            'reporting_media' => $reporting_media,
            'reporting_media_others' => $request->reporting_media_others,
            'brief_description' => $request->brief_description,
            'immediate_action_taken' => $request->immediate_action_taken,
            'anyone_injured' => $request->anyone_injured,
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
    public function investigationassigned($incident_Id)
    {
        $request = request();

        $decryptedTeamMemberIds = is_array($request->team_member)
            ? array_map('decryptId', $request->team_member)
            : [];
        $commaSeparatedTeamMembers = !empty($decryptedTeamMemberIds) ? implode(',', $decryptedTeamMemberIds) : null;
        $update_array = array(
            'investigation_assigned' => $commaSeparatedTeamMembers,
            'investigation_reported_by' => decryptId($request->reported_by),
            'target_date' => DBdateformat($request->target_date),
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        );

        return $this->where('id', $incident_Id)->update($update_array);
    }

    public function chooseAssigneeUpdate($incident_Id, $choose_assignee)
    {
        $request = request();
        $decryptedTeamMemberIds = is_array($request->team_member)
            ? array_map('decryptId', $request->team_member)
            : [];
        $commaSeparatedTeamMembers = !empty($decryptedTeamMemberIds) ? implode(',', $decryptedTeamMemberIds) : null;
        $update_array = array(
            'choose_assignee' => $commaSeparatedTeamMembers,
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
    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('ims_initial_incident.*', 'ims_incident_status.status_name', 'ims_incident_status.bg_color', 'ims_initial_incident_investigation.risk_analysis');
        $query = $query->leftJoin('ims_incident_status', 'ims_incident_status.id', '=', 'ims_initial_incident.incident_status');
        $query = $query->leftJoin('ims_initial_incident_investigation', 'ims_initial_incident_investigation.incident_id', '=', 'ims_initial_incident.id');

        /**
         * Role Based list view condition start
         */
        if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_ADMIN) || CheckUserRole(ROLE_EHS_HEAD)) {
            $query->where('ims_initial_incident.status', '1');
        } elseif (CheckUserRole(ROLE_EHS_OFFICER)) {
            $query->where('ims_initial_incident.created_by', Auth::user()->id);
        } else {

            $query->where('ims_initial_incident.created_by', Auth::user()->id);
        }


        /**
         * Role Based list view condition end
         */

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
            $query->whereBetween('ims_initial_incident.created_at', [$startDate, $endDate]);
        } elseif ($request->has('ims_initial_incident.') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ims_initial_incident.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ims_initial_incident.created_at', '<=', $endDate);
        }
        if ($request->has('incident_status') && $request->incident_status) {

            $query = $query->where('incident_status', decryptId($request->incident_status));
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('ims_initial_incident.status', decryptId($request->status));
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
            'ims_initial_incident_evidence_upload.file_path',
            'ims_master_incident_type.incident_type_name',
            'masters_location.location_name',
        )
            ->where('ims_initial_incident.id', $id)
            ->leftJoin('masters_employee', 'masters_employee.id', '=', 'ims_initial_incident.reported_name')
            ->leftJoin('masters_department', 'masters_department.id', '=', 'ims_initial_incident.department')
            ->leftJoin('ims_master_incident_type', 'ims_master_incident_type.id', '=', 'ims_initial_incident.iir_type')
            ->leftJoin('masters_location', 'masters_location.id', '=', 'ims_initial_incident.location_id')
            ->leftJoin('ims_initial_incident_evidence_upload', 'ims_initial_incident_evidence_upload.incident_id', '=', 'ims_initial_incident.iir_type')
            ->leftJoin('ims_rcpa_responsible', 'ims_rcpa_responsible.incident_id', '=', 'ims_initial_incident.id')
            ->leftJoin('ims_incident_body_parts', 'ims_incident_body_parts.incident_id', '=', 'ims_initial_incident.id')
            ->leftJoin('ims_injury_details', 'ims_injury_details.incident_id', '=', 'ims_initial_incident.id')
            ->first();

        return $data;
    }


    public function getEHSVerifyincident($id)
    {
        $data = $this->select('ims_ehs_review.*', 'ims_ehs_review.team_member')
            ->where('ims_ehs_review.inicdent_report_id', $id)
            ->leftJoin('ims_ehs_review', 'ims_ehs_review.inicdent_report_id', '=', 'ims_initial_incident.id')
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


    public function getInvestigation($id)
    {
        $data = $this->select('ims_initial_incident_investigation.*')
            ->where('ims_initial_incident.id', $id)
            ->leftJoin('ims_initial_incident_investigation', 'ims_initial_incident_investigation.incident_id', '=', 'ims_initial_incident.id')
            // ->leftJoin('masters_employee', 'masters_employee.id', '=', 'ims_initial_incident_investigation.responsible_person_id')
            ->first();

        if ($data) {
            $hiraMocRecords = DB::table('ims_master_incident_hiramoc as hiramoc')
                ->leftJoin('ims_master_hira as hira', 'hiramoc.hira_id', '=', 'hira.id')
                ->leftJoin('ims_master_hira as moc', 'hiramoc.moc_id', '=', 'moc.id')
                ->where('hiramoc.incident_id', $id)
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
                $employeeNames = DB::table('masters_employee')
                    ->whereIn('emp_id', $witnessIds)
                    ->pluck('emp_name')
                    ->toArray();

                $workerNames = DB::table('masters_work')
                    ->whereIn('emp_id', $witnessIds)
                    ->pluck('emp_name')
                    ->toArray();

                $allNames = array_merge($employeeNames, $workerNames);

                $data->witness_name = implode(', ', $allNames);
            }
        }

        return $data;
    }




    public function getwhywhy($id)
    {
        $data = $this->select('ims_incident_whywhyanalysis.*')
            ->where('ims_initial_incident.id', $id)
            ->leftJoin('ims_incident_whywhyanalysis', 'ims_incident_whywhyanalysis.incident_id', '=', 'ims_initial_incident.id')
            ->get();

        return $data;
    }
    public function getfishbone($id)
    {
        $data = $this->select('ims_incident_fishboneanalysis.*')
            ->where('ims_initial_incident.id', $id)
            ->leftJoin('ims_incident_fishboneanalysis', 'ims_incident_fishboneanalysis.incident_id', '=', 'ims_initial_incident.id')
            ->get();

        return $data;
    }

    public function getrisklevel($id)
    {
        $data = $this->select('ims_master_incident_riskanalysis.*')
            ->where('ims_initial_incident.id', $id)
            ->leftJoin('ims_master_incident_riskanalysis', 'ims_master_incident_riskanalysis.incident_id', '=', 'ims_initial_incident.id')
            ->first();

        return $data;
    }
    public function getEHSReviewincident($id)
    {
        $data = $this->select('ims_ehs_review.*', 'ims_ehs_review.team_member')
            ->where('ims_initial_incident.id', $id)
            ->leftJoin('ims_ehs_review', 'ims_ehs_review.inicdent_report_id', '=', 'ims_initial_incident.id')
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
            ->where('ims_initial_incident.id', $id)
            ->leftJoin('ims_ehs_review', 'ims_ehs_review.inicdent_report_id', '=', 'ims_initial_incident.id')
            ->where('ims_ehs_review.type', 3)
            ->orderBy('id', 'DESC')
            ->first();
        return $data;
    }

    public function getTypeofIIRCountData($request)
    {
        $query = DB::table('ims_initial_incident as iii')
            ->join('ims_master_incident_type as imit', 'iii.iir_type', '=', 'imit.id')
            ->select('imit.incident_type_name', 'iii.iir_type as incident_type_id', DB::raw('COUNT(iii.id) as total'))
            ->groupBy('imit.incident_type_name', 'iii.iir_type')
            ->orderBy('imit.incident_type_name');

        // Apply company Filter
        if ($request->CompanyId) {
            $company_id = decryptId($request->CompanyId);
            $query->where('iii.company_id', $company_id);
        }
        // Date Filters
        if ($request->Fromdate && $request->Todate) {
            $query->whereBetween('iii.created_at', [
                DBdateformat($request->Fromdate),
                DBdateformat($request->Todate) . ' 23:59:59'
            ]);
        } elseif ($request->Fromdate) {
            $query->where('iii.created_at', '>=', DBdateformat($request->Fromdate));
        } elseif ($request->Todate) {
            $query->where('iii.created_at', '<=', DBdateformat($request->Todate) . ' 23:59:59');
        }


        return $query->get(); // returns multiple rows
    }


    public function getAccidentReportUnitWiseCountData($request)
    {
        $query = DB::table('ims_initial_incident as iii')
            ->join('masters_unit as unit', 'iii.unit_id', '=', 'unit.id')
            ->join('ims_injury_details as imit', 'iii.id', '=', 'imit.incident_id')
            ->select(
                'unit.unit_name',
                'unit.id as unit_id',
                DB::raw("SUM(CASE WHEN imit.nature_of_injury = 1 THEN 1 ELSE 0 END) AS major"),
                DB::raw("SUM(CASE WHEN imit.nature_of_injury = 2 THEN 1 ELSE 0 END) AS minor"),
                DB::raw("SUM(CASE WHEN imit.nature_of_injury = 3 THEN 1 ELSE 0 END) AS fatal")
            )
            ->groupBy('unit.unit_name', 'unit.id')
            ->orderBy('unit.unit_name');

        // Apply company Filter
        if ($request->CompanyId) {
            $company_id = decryptId($request->CompanyId);
            $query->where('iii.company_id', $company_id);
        }

        // Date Filters
        if ($request->Fromdate && $request->Todate) {
            $query->whereBetween('iii.created_at', [
                DBdateformat($request->Fromdate),
                DBdateformat($request->Todate) . ' 23:59:59'
            ]);
        } elseif ($request->Fromdate) {
            $query->where('iii.created_at', '>=', DBdateformat($request->Fromdate));
        } elseif ($request->Todate) {
            $query->where('iii.created_at', '<=', DBdateformat($request->Todate) . ' 23:59:59');
        }


        return $query->get();
    }


    public function getTypeofIIRRCPACountData($request)
    {
        $query = DB::table('ims_initial_incident as iii')
            ->join('ims_master_incident_type as imit', 'iii.iir_type', '=', 'imit.id')
            ->leftJoin('ims_rcpa_responsible as rcpa', function ($join) {
                $join->on('iii.id', '=', 'rcpa.incident_id')
                    ->whereNotNull('rcpa.id');
            })
            ->select(
                'imit.incident_type_name',
                'iii.iir_type',
                DB::raw('COUNT(DISTINCT iii.id) as total_incident'),
                DB::raw('COUNT(rcpa.id) as total_rcpa')
            )
            ->groupBy('imit.incident_type_name', 'iii.iir_type')
            ->orderBy('imit.incident_type_name');


        // Apply company Filter
        if ($request->CompanyId) {
            $company_id = decryptId($request->CompanyId);
            $query->where('iii.company_id', $company_id);
        }

        // Date Filters
        if ($request->Fromdate && $request->Todate) {
            $query->whereBetween('iii.created_at', [
                DBdateformat($request->Fromdate),
                DBdateformat($request->Todate) . ' 23:59:59'
            ]);
        } elseif ($request->Fromdate) {
            $query->where('iii.created_at', '>=', DBdateformat($request->Fromdate));
        } elseif ($request->Todate) {
            $query->where('iii.created_at', '<=', DBdateformat($request->Todate) . ' 23:59:59');
        }

        return $query->get(); // returns multiple rows
    }
    public function getTypeofIIRUAUCCountData($request)
    {
        $query = DB::table('ims_initial_incident as iii')
            ->join('ims_master_incident_type as imit', 'iii.iir_type', '=', 'imit.id')
            ->where('iii.ua_uc_yes_no', 1)
            ->select(
                'imit.incident_type_name',
                DB::raw('COUNT(DISTINCT iii.id) as total_incident'),
                DB::raw('SUM(CASE WHEN FIND_IN_SET("1", iii.ua_or_uc) > 0 THEN 1 ELSE 0 END) as unsafe_act'),
                DB::raw('SUM(CASE WHEN FIND_IN_SET("2", iii.ua_or_uc) > 0 THEN 1 ELSE 0 END) as unsafe_condition'),
                DB::raw('SUM(CASE WHEN FIND_IN_SET("3", iii.ua_or_uc) > 0 THEN 1 ELSE 0 END) as natural_causes')
            )
            ->groupBy(
                'imit.incident_type_name',
            )
            ->orderBy('imit.incident_type_name');
        // Apply company Filter
        if ($request->CompanyId) {
            $company_id = decryptId($request->CompanyId);
            $query->where('iii.company_id', $company_id);
        }

        // Date Filters
        if ($request->Fromdate && $request->Todate) {
            $query->whereBetween('iii.created_at', [
                DBdateformat($request->Fromdate),
                DBdateformat($request->Todate) . ' 23:59:59'
            ]);
        } elseif ($request->Fromdate) {
            $query->where('iii.created_at', '>=', DBdateformat($request->Fromdate));
        } elseif ($request->Todate) {
            $query->where('iii.created_at', '<=', DBdateformat($request->Todate) . ' 23:59:59');
        }

        return $query->get(); // returns multiple rows
    }
    public function getNearmiss()
    {
        $nearMissId = DB::table('ims_master_incident_type')
            ->where('incident_type_name', 'LIKE', '%Near Miss%')
            ->where(function ($query) {
                $query->whereRaw("LOWER(REPLACE(incident_type_name, '-', '')) LIKE ?", ['%near miss%']);
            })
            ->where('status', 1)
            ->value('id'); // returns a single id, not an array

        return $nearMissId;
    }

    public function getFireIncidence()
    {
        $fireIncidence = DB::table('ims_master_incident_type')
            ->where('incident_type_name', 'LIKE', '%Fire Incidence%')
            ->where(function ($query) {
                $query->whereRaw("LOWER(REPLACE(incident_type_name, '-', '')) LIKE ?", ['%near miss%']);
            })
            ->where('status', 1)
            ->value('id'); // returns a single id, not an array

        return $fireIncidence;
    }

    public function getNearMissCountData($request)
    {
        $nearMissIds = DB::table('ims_master_incident_type')
            ->where('incident_type_name', 'LIKE', '%Near Miss%')
            ->where(function ($query) {
                $query->whereRaw("LOWER(REPLACE(incident_type_name, '-', '')) LIKE ?", ['%Near Miss%']);
            })
            ->where('status', 1)
            ->pluck('id')
            ->toArray();

        $query = DB::table('ims_initial_incident as iii')
            ->join('ims_master_incident_type as imit', 'iii.iir_type', '=', 'imit.id')
            ->whereIn('iii.iir_type', $nearMissIds)
            ->select(
                'imit.incident_type_name',
                'iii.iir_type',
                DB::raw('MONTH(iii.created_at) as month'),
                DB::raw('YEAR(iii.created_at) as year'),
                DB::raw('COUNT(DISTINCT iii.id) as incident_count')
            )
            ->groupBy('imit.incident_type_name', 'iii.iir_type', 'month', 'year')
            ->orderBy('year')
            ->orderBy('month')
            ->orderBy('imit.incident_type_name');

        // Apply company Filter
        if ($request->CompanyId) {
            $company_id = decryptId($request->CompanyId);
            $query->where('iii.company_id', $company_id);
        }

        // Date Filters
        if ($request->Fromdate && $request->Todate) {
            $query->whereBetween('iii.created_at', [
                DBdateformat($request->Fromdate),
                DBdateformat($request->Todate) . ' 23:59:59'
            ]);
        } elseif ($request->Fromdate) {
            $query->where('iii.created_at', '>=', DBdateformat($request->Fromdate));
        } elseif ($request->Todate) {
            $query->where('iii.created_at', '<=', DBdateformat($request->Todate) . ' 23:59:59');
        }

        $results = $query->get();

        // Format results with month names
        $monthNames = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];

        return $results->map(function ($item) use ($monthNames) {
            return [
                'incident_type_name' => $item->incident_type_name,
                'period' => $monthNames[$item->month] . ' ' . $item->year,
                'month' => $item->month,
                'year' => $item->year,
                'count' => $item->incident_count,
                'iir_type' => $item->iir_type
            ];
        });
    }
    public function getTypeofUAUCStatusCountData($request)
    {
        $query = DB::table('ims_initial_incident as iii')
            ->leftJoin('masters_unit as mu', 'mu.id', '=', 'iii.unit_id')
            ->select(
                'mu.unit_name',
                DB::raw('SUM(CASE WHEN FIND_IN_SET("1", iii.ua_or_uc) > 0 THEN 1 ELSE 0 END) as ua_total'),
                DB::raw('SUM(CASE WHEN FIND_IN_SET("1", iii.ua_or_uc) > 0 AND iii.status != 4 THEN 1 ELSE 0 END) as ua_open'),
                DB::raw('SUM(CASE WHEN FIND_IN_SET("1", iii.ua_or_uc) > 0 AND iii.status = 4 THEN 1 ELSE 0 END) as ua_closed'),

                DB::raw('SUM(CASE WHEN FIND_IN_SET("2", iii.ua_or_uc) > 0 THEN 1 ELSE 0 END) as uc_total'),
                DB::raw('SUM(CASE WHEN FIND_IN_SET("2", iii.ua_or_uc) > 0 AND iii.status != 4 THEN 1 ELSE 0 END) as uc_open'),
                DB::raw('SUM(CASE WHEN FIND_IN_SET("2", iii.ua_or_uc) > 0 AND iii.status = 4 THEN 1 ELSE 0 END) as uc_closed')
            )
            ->where('iii.ua_uc_yes_no', 1)
            ->groupBy('mu.unit_name')
            ->orderBy('mu.unit_name');

        if ($request->Fromdate && $request->Todate) {
            $query->whereBetween('iii.created_at', [
                DBdateformat($request->Fromdate),
                DBdateformat($request->Todate) . ' 23:59:59'
            ]);
        } elseif ($request->Fromdate) {
            $query->where('iii.created_at', '>=', DBdateformat($request->Fromdate));
        } elseif ($request->Todate) {
            $query->where('iii.created_at', '<=', DBdateformat($request->Todate) . ' 23:59:59');
        }

        return $query->get(); // returns multiple rows
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
