<?php

namespace App\Models\IMS\Incident;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccidentReport extends Model
{
    use  HasFactory;


    protected $table = 'ims_initial_accident_report';
    protected $primaryKey = 'id';

    protected $fillable = [
        'accident_report_no',
        'date_and_time',
        'unit_id',
        'shift',
        'location_id',
        'exact_location',
        'designation',
        'department_id',
        'emp_code',
        'address_of_the_injuredperson',
        'accident_status',
        'ua_uc_yes_no',
        'ua_or_uc',
        'description_uauc',
        'action_submission_by',
        'action_submission_date',
        'action_submission_description',
        'investigation_assigned',
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
        $query = $this->select('ims_initial_accident_report.*', 'masters_unit.unit_name', 'masters_employee.emp_id', 'masters_department.department_name',  'masters_location.location_name', 'ims_incident_status.status_name', 'ims_incident_status.bg_color', 'ims_accident_investigation.risk_analysis');
        $query = $query->leftJoin('ims_incident_status', 'ims_incident_status.id', '=', 'ims_initial_accident_report.accident_status');
        $query = $query->leftJoin('masters_unit', 'ims_initial_accident_report.unit_id', '=', 'masters_unit.id');
        $query = $query->leftJoin('masters_employee', 'ims_initial_accident_report.emp_code', '=', 'masters_employee.emp_id');
        $query = $query->leftJoin('masters_department', 'ims_initial_accident_report.department_id', '=', 'masters_department.id');
        $query = $query->leftJoin('masters_location', 'ims_initial_accident_report.location_id', '=', 'masters_location.id');
        $query = $query->leftJoin('ims_accident_investigation', 'ims_initial_accident_report.id', '=', 'ims_accident_investigation.accident_id');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('accident_report_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_employee.emp_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_department.department_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_location.location_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('accident_report_no') && $request->accident_report_no) {
            $query = $query->where('accident_report_no',  $request->accident_report_no);
        }
        if ($request->has('emp_code') && $request->emp_code) {
            $query = $query->where('ims_initial_accident_report.emp_code', decryptId($request->emp_code));
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('ims_initial_accident_report.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('department_id') && $request->department_id) {
            $query = $query->where('ims_initial_accident_report.department_id', decryptId($request->department_id));
        }
        if ($request->has('location_id') && $request->location_id) {
            $query = $query->where('ims_initial_accident_report.location_id', decryptId($request->location_id));
        }
        if ($request->has('accident_status') && $request->accident_status) {
            $query = $query->where('ims_initial_accident_report.accident_status', decryptId($request->accident_status));
        }
        if ($request->has('from_date_datepicker') && $request->from_date_datepicker) {
            $fromDate = Carbon::createFromFormat('d-m-Y', $request->from_date_datepicker)->startOfDay();
            $query = $query->where('ims_initial_accident_report.created_at', '>=', $fromDate);
        }

        if ($request->has('to_date_datepicker') && $request->to_date_datepicker) {
            $toDate = Carbon::createFromFormat('d-m-Y', $request->to_date_datepicker)->endOfDay();
            $query = $query->where('ims_initial_accident_report.created_at', '<=', $toDate);
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('ims_initial_accident_report.status', decryptId($request->status));
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

        $insert_array = array(
            'accident_report_no' => $request->accident_report_no,
            'date_and_time' => DBdatetimeformat($request->date_and_time),
            'unit_id' =>  decryptId($request->unit_id),
            'location_id' =>  decryptId($request->location_id),
            'department_id' =>  decryptId($request->department_id),
            'shift' => $request->shift,
            'exact_location' => $request->exact_location,
            'designation' => $request->designation,
            'emp_code' => $request->emp_code,
            'accident_status' => 1,
            'address_of_the_injuredperson' => $request->address_of_the_injuredperson,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();
        $update_array = array(
            'accident_report_no' => $request->accident_report_no,
            'date_and_time' => DBdatetimeformat($request->date_and_time),
            'unit_id' =>  decryptId($request->unit_id),
            'location_id' =>  decryptId($request->location_id),
            'department_id' =>  decryptId($request->department_id),
            'shift' => $request->shift,
            'exact_location' => $request->exact_location,
            'designation' => $request->designation,
            'emp_code' => $request->emp_code,
            'address_of_the_injuredperson' => $request->address_of_the_injuredperson,
            'updated_by' => Auth::id()
        );
        return $this->where('id', $id)->update($update_array);
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

        return $this->where('id', $id)->update($update_array);
    }
    public function getEHSReviewaccident($id)
    {
        $data = $this->select('ims_ehs_review.*', 'ims_ehs_review.team_member')
            ->where('ims_initial_accident_report.id', $id)
            ->leftJoin('ims_ehs_review', 'ims_ehs_review.accident_report_id', '=', 'ims_initial_accident_report.id')
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
    public function investigationassigned($accident_Id)
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
        return $this->where('id', $accident_Id)->update($update_array);
    }

    public function getEHSVerifyAccident($id)
    {
        $data = $this->select('ims_ehs_review.*', 'ims_ehs_review.team_member')
            ->where('ims_initial_accident_report.id', $id)
            ->leftJoin('ims_ehs_review', 'ims_ehs_review.accident_report_id', '=', 'ims_initial_accident_report.id')
            ->where('ims_ehs_review.type', 2)
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
    public function getEHSApprovalAccident($id)
    {
        $data = $this->select('ims_ehs_review.*', 'ims_ehs_review.team_member')
            ->where('ims_initial_accident_report.id', $id)
            ->leftJoin('ims_ehs_review', 'ims_ehs_review.accident_report_id', '=', 'ims_initial_accident_report.id')
            ->where('ims_ehs_review.type', 3)
            ->first();
        return $data;
    }
    public function getwhywhy($id)
    {
        $data = $this->select('ims_incident_whywhyanalysis.*')
            ->where('ims_initial_accident_report.id', $id)
            ->leftJoin('ims_incident_whywhyanalysis', 'ims_incident_whywhyanalysis.accident_id', '=', 'ims_initial_accident_report.id')
            ->get();

        return $data;
    }
    public function getfishbone($id)
    {
        $data = $this->select('ims_incident_fishboneanalysis.*')
            ->where('ims_initial_accident_report.id', $id)
            ->leftJoin('ims_incident_fishboneanalysis', 'ims_incident_fishboneanalysis.accident_id', '=', 'ims_initial_accident_report.id')
            ->get();

        return $data;
    }

    public function getrisklevel($id)
    {
        $data = $this->select('ims_master_incident_riskanalysis.*')
            ->where('ims_initial_accident_report.id', $id)
            ->leftJoin('ims_master_incident_riskanalysis', 'ims_master_incident_riskanalysis.accident_id', '=', 'ims_initial_accident_report.id')
            ->first();

        return $data;
    }

    public function getInvestigation($id)
    {
        $data = $this->select('ims_accident_investigation.*', 'masters_employee.emp_name as responsible_person')
            ->leftJoin('ims_accident_investigation', 'ims_accident_investigation.accident_id', '=', 'ims_initial_accident_report.id')
            ->leftJoin('masters_employee', 'masters_employee.id', '=', 'ims_accident_investigation.responsible_person_id')
            ->where('ims_initial_accident_report.id', $id)
            ->first();

        if ($data) {
            $hiraMocRecords = DB::table('ims_master_incident_hiramoc as hiramoc')
                ->leftJoin('ims_master_hira as hira', 'hiramoc.hira_id', '=', 'hira.id')
                ->leftJoin('ims_master_hira as moc', 'hiramoc.moc_id', '=', 'moc.id')
                ->where('hiramoc.accident_id', $id)
                ->select('hiramoc.hira_id', 'hira.services as hira_name', 'hiramoc.moc_id', 'moc.services as moc_name')
                ->get();

            $mergedHiraMoc = [];
            $tempHira = null;

            foreach ($hiraMocRecords as $record) {
                if ($record->hira_id != 0 && $record->moc_id == 0) {
                    $tempHira = [
                        'hira_id' => $record->hira_id,
                        'hira_name' => $record->hira_name,
                        'moc_id' => null,
                        'moc_name' => null
                    ];
                } elseif ($record->hira_id == 0 && $record->moc_id != 0) {

                    if ($tempHira) {
                        $tempHira['moc_id'] = $record->moc_id;
                        $tempHira['moc_name'] = $record->moc_name;
                        $mergedHiraMoc[] = $tempHira;
                        $tempHira = null;
                    } else {

                        $mergedHiraMoc[] = [
                            'hira_id' => null,
                            'hira_name' => null,
                            'moc_id' => $record->moc_id,
                            'moc_name' => $record->moc_name
                        ];
                    }
                } else {

                    $mergedHiraMoc[] = [
                        'hira_id' => $record->hira_id,
                        'hira_name' => $record->hira_name,
                        'moc_id' => $record->moc_id,
                        'moc_name' => $record->moc_name
                    ];
                }
            }
            if ($tempHira) {
                $mergedHiraMoc[] = $tempHira;
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
        $query = $this->select('ims_initial_accident_report.*', 'masters_unit.unit_name', 'masters_employee.emp_id', 'masters_department.department_name',  'masters_location.location_name');
        $query = $query->leftJoin('masters_unit', 'ims_initial_accident_report.unit_id', '=', 'masters_unit.id');
        $query = $query->leftJoin('masters_employee', 'ims_initial_accident_report.emp_code', '=', 'masters_employee.emp_id');
        $query = $query->leftJoin('masters_department', 'ims_initial_accident_report.department_id', '=', 'masters_department.id');
        $query = $query->leftJoin('masters_location', 'ims_initial_accident_report.location_id', '=', 'masters_location.id');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('accident_report_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_employee.emp_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_department.department_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_location.location_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('accident_report_no') && $request->accident_report_no) {
            $query = $query->where('accident_report_no',  $request->accident_report_no);
        }
        if ($request->has('emp_code') && $request->emp_code) {
            $query = $query->where('ims_initial_accident_report.emp_code', decryptId($request->emp_code));
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('ims_initial_accident_report.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('department_id') && $request->department_id) {
            $query = $query->where('ims_initial_accident_report.department_id', decryptId($request->department_id));
        }
        if ($request->has('location_id') && $request->location_id) {
            $query = $query->where('ims_initial_accident_report.location_id', decryptId($request->location_id));
        }
        if ($request->has('accident_status') && $request->accident_status) {
            $query = $query->where('ims_initial_accident_report.accident_status', decryptId($request->accident_status));
        }
        if ($request->has('from_date_datepicker') && $request->from_date_datepicker) {
            $fromDate = Carbon::createFromFormat('d-m-Y', $request->from_date_datepicker)->startOfDay();
            $query = $query->where('ims_initial_accident_report.created_at', '>=', $fromDate);
        }

        if ($request->has('to_date_datepicker') && $request->to_date_datepicker) {
            $toDate = Carbon::createFromFormat('d-m-Y', $request->to_date_datepicker)->endOfDay();
            $query = $query->where('ims_initial_accident_report.created_at', '<=', $toDate);
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('ims_initial_accident_report.status', decryptId($request->status));
        }

        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select('ims_initial_accident_report.*', 'masters_unit.unit_name', 'masters_employee.emp_id', 'masters_department.department_name',  'masters_location.location_name')->leftJoin('masters_unit', 'ims_initial_accident_report.unit_id', '=', 'masters_unit.id')
            ->leftJoin('masters_employee', 'ims_initial_accident_report.emp_code', '=', 'masters_employee.emp_id')
            ->leftJoin('masters_department', 'ims_initial_accident_report.department_id', '=', 'masters_department.id')
            ->leftJoin('masters_location', 'ims_initial_accident_report.location_id', '=', 'masters_location.id')
            ->where('ims_initial_accident_report.id', $id)
            ->first();

        return $data;
    }


    public function updateStatus($accidentId, $accident_status)
    {
        $request = request();

        $update_array = array(
            'accident_status' => $accident_status,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        );
        return $this->where('id', $accidentId)->update($update_array);
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ims_initial_accident_report'));

        static::created(function ($model) {

            $uniqueId = 'ACCIDENT-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['accident_report_no' => $uniqueId]);
        });
    }
}
