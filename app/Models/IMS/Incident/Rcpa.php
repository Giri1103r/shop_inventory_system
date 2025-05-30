<?php

namespace App\Models\IMS\Incident;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Master\Employee;

class Rcpa extends Model
{
    use  HasFactory;


    protected $table = 'ims_rcpa_responsible';
    protected $primaryKey = 'id';

    protected $fillable = [
        'incident_id',
        'investigation_id',
        'rcpa_id',
        'rcpa',
        'responsibility',
        'timeline',
        'capa_status',
        'action_submission_by',
        'action_submission_date',
        'action_submission_description',
        'incident_status',
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
        $query = $this->select('ims_rcpa_responsible.*', 'ims_incident_status.status_name', 'ims_incident_status.bg_color', 'ims_initial_incident.sr_no', 'ims_initial_incident.unit_id', 'ims_initial_incident.shift');
        $query = $query->leftJoin('ims_incident_status', 'ims_incident_status.id', '=', 'ims_rcpa_responsible.incident_status');
        $query = $query->leftJoin('ims_initial_incident', 'ims_initial_incident.id', '=', 'ims_rcpa_responsible.incident_id');
        $query = $query->leftJoin('ims_initial_incident_investigation', 'ims_initial_incident_investigation.id', '=', 'ims_rcpa_responsible.investigation_id');
        $org_total =  $query;
        $org_total_counts = $org_total->count();
        /**
         * Role Based list view condition start
         */
        if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_ADMIN) || CheckUserRole(ROLE_EHS_HEAD)) {
            $query->where('ims_initial_incident.status', '1');
        } elseif (CheckUserRole(ROLE_EHS_OFFICER)) {
            $query->where('ims_initial_incident.created_by', Auth::user()->id)->where('ims_initial_incident.status', '1');
        } else {
            // $userId = Auth::id();
            // $userLoginId = Auth::user()->employee_id;

            // $employeeId = Employee::where('emp_id', $userLoginId)->value('id');

            // $query->where(function ($q) use ($userId, $employeeId) {
            //     $q->where('ims_initial_incident.created_by', $userId)
            //         ->orWhereRaw("FIND_IN_SET(?, investigation_assigned)", [$employeeId])
            //         ->orWhereRaw("FIND_IN_SET(?, choose_assignee)", [$employeeId]);
            // })->where('ims_initial_incident.status', '1');

            $query->where('ims_rcpa_responsible.responsibility', Auth::user()->id)->where('ims_initial_incident.status', '1');
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
            $query->whereBetween('ims_rcpa_responsible.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ims_rcpa_responsible.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ims_rcpa_responsible.created_at', '<=', $endDate);
        }
        if ($request->has('incident_status') && $request->incident_status) {

            $query = $query->where('ims_rcpa_responsible.incident_status', decryptId($request->incident_status));
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('ims_initial_incident.status', decryptId($request->status));
        }

        if ($request->has('dash_iirtype_id') && $request->dash_iirtype_id) {
            $query = $query->where('ims_initial_incident.iir_type', ($request->dash_iirtype_id));
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
    public function storeRCPA($incident_id, $investigation_id)
    {
        $request = request();

        $serial_numbers = $request->input('serial_number', []);
        $rcpaItems = $request->input('rcpa', []);
        $responsibilities = $request->input('responsibility', []);
        $timelines = $request->input('timeline', []);
        $statuses = $request->input('capa_status', []);
        $remarks = $request->input('capa_remark', []);

        $result = [];

        foreach ($rcpaItems as $index => $rcpaText) {
            if (trim($rcpaText)) {
                $responsibility_id = isset($responsibilities[$index]) ? decryptId($responsibilities[$index]) : null;
                $data = [
                    'incident_id' => $incident_id,
                    'investigation_id' => $investigation_id,
                    'rcpa_id' => $serial_numbers[$index] ?? null,
                    'rcpa' => $rcpaText,
                    'responsibility' => $responsibility_id,
                    'timeline' => isset($timelines[$index]) ? DBdateformat($timelines[$index]) : null,
                    'capa_status' => isset($statuses[$index]) ? decryptId($statuses[$index]) : null,
                    'incident_status' => STATUS_ACTION_PENDING,
                    'remark' => $remarks[$index] ?? null,
                    'created_by' => Auth::id(),
                ];
                $rcpaRecord = Rcpa::create($data);

                $result[] = [
                    'rcpa_id' => $rcpaRecord->id,
                    'responsibility' => $responsibility_id
                ];
            }
        }

        return $result;
    }
    public function actiontakensubmit($id)
    {

        $request = request();

        $update_array = array(
            'action_submission_by' => Auth::id(),
            'action_submission_date' => DBdateformat($request->action_submission_date),
            'action_submission_description' => $request->action_submission_description,
            'incident_status' => STATUS_EHSAPPROVAL_PENDING
        );

        return $this->where('id', $id)->update($update_array);
    }

    public function updateStatus($rcpa_id, $incident_status)
    {
        $request = request();

        $update_array = array(
            'incident_status' => $incident_status,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        );
        return $this->where('id', $rcpa_id)->update($update_array);
    }
    public function selectOne($id)
    {

        $data = $this->select(
            'ims_rcpa_responsible.*',
        )
            ->where('ims_rcpa_responsible.id', $id)
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

        return $this->where('incident_id', $id)->update($update_data);
    }

  

    public function getRCPA($id)
    {

        $data = $this->select(
            'ims_rcpa_responsible.*'
        )
            ->where('ims_rcpa_responsible.incident_id', $id)
            // ->leftJoin('masters_employee', 'masters_employee.id', '=', 'ims_rcpa_responsible.responsibility')
            ->get();
        return $data;
    }
}
