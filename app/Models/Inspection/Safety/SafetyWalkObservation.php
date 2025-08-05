<?php

namespace App\Models\Inspection\Safety;

use App\Scopes\TrashScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class SafetyWalkObservation extends Model
{

    protected $table = 'inspection_safety_walk_observation';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'document_reference_id',
        'date',
        'shift_id',
        'excat_location',
        'location_id',
        'observation_date',
        'image',
        'observation',
        'recomended_action',
        'responsible_persion',
        'observing_status',
        'date_of_compilance',
        'observer_remarks',
        'observer_date',
        'approver_date',
        'remarks',
        'month',
        'unit',
        'safety_walk_taken_by',
        'approval_remarks',
        'observer_person',
        'observation_status',
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
        $user = Auth::user();
        $userRole = string_to_array($user->role);
        $search = '';
        $query = $this->select('inspection_safety_walk_observation.*', 'inspection_shift_option.*', 'masters_unit.*', 'inspection_safety_walk_observation.id as inspection_id', 'inspection_safety_walk_observation.created_at as inspection_created_at')
            ->leftJoin('inspection_shift_option', 'inspection_safety_walk_observation.shift_id', '=', 'inspection_shift_option.id')
            ->leftJoin('masters_unit', 'inspection_safety_walk_observation.unit', '=', 'masters_unit.id')
            ->leftJoin('inspection_static_docno', 'inspection_safety_walk_observation.document_reference_id', '=', 'inspection_static_docno.id');


        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('shift LIKE "%' . $search . '%"');
                $query->orWhereRaw('unit_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('month LIKE "%' . $search . '%"');
            });
        }

        if (
            in_array(ROLE_SUPERADMIN, $userRole)
        ) {

            $query->orderBy('inspection_safety_walk_observation.id', 'DESC');
        } elseif (
            in_array(ROLE_ADMIN, $userRole) ||
            in_array(ROLE_EHS_OFFICER, $userRole)
        ) {

            $query->orderBy('inspection_safety_walk_observation.id', 'DESC');
        } elseif (in_array(ROLE_INSPECTION_CREATOR, $userRole)) {

            $query->where('inspection_safety_walk_observation.created_by', Auth::id());
        } else {

            $query->where(function ($q) {
                $q->whereRaw("FIND_IN_SET(?, inspection_safety_walk_observation.responsible_persion)", [Auth::id()])
                    ->orWhere('inspection_safety_walk_observation.created_by', Auth::id());
            });
        }

        if ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_safety_walk_observation.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_safety_walk_observation.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_safety_walk_observation.created_at', [$startDate, $endDate]);
        }
        if (isset($request->month) && $request->month) {
            $query = $query->where('inspection_safety_walk_observation.month', 'LIKE', '%' . $request->month . '%');
        }
        if (isset($request->emp_id) && $request->emp_id) {

            $query = $query->where('inspection_safety_walk_observation.created_by', $request->emp_id);
        }
        if (isset($request->inspection_date) && $request->inspection_date) {
            $query = $query->whereDate('inspection_safety_walk_observation.date', '=', DBdateformat($request->inspection_date));
        }
        if (isset($request->observation_status) && $request->observation_status) {
            $query = $query->where('inspection_safety_walk_observation.observation_status', decryptId($request->observation_status));
        }
        if (isset($request->unit) && $request->unit) {
            $query = $query->where('inspection_safety_walk_observation.unit', decryptId($request->unit));
        }
        if (isset($request->shift) && $request->shift) {
            $query = $query->where('inspection_safety_walk_observation.shift_id', decryptId($request->shift));
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "date":
                    $query->orderBy('inspection_safety_walk_observation.date', $columnorder);
                    break;
                case "shift_id":
                    $query = $query->orderBy('inspection_safety_walk_observation.shift_id', $columnorder);
                    break;
                case "unit":
                    $query = $query->orderBy('inspection_safety_walk_observation.unit', $columnorder);
                    break;
                case "month":
                    $query = $query->orderBy('inspection_safety_walk_observation.month', $columnorder);
                    break;
                case "observation_status":
                    $query = $query->orderBy('inspection_safety_walk_observation.observation_status', $columnorder);
                    break;
                case "safety_walk_taken_by":
                    $query = $query->orderBy('inspection_safety_walk_observation.safety_walk_taken_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_safety_walk_observation.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_safety_walk_observation.id', 'DESC');
                    break;
            }
        }

        $data_count = $query;
        $total_records = $data_count->count();

        if (isset($request->length) && $request->length != -1) {
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

        $location = $request->location;
        $exact_location = $request->exact_location;
        $observation = $request->observation;
        $recomended_action = $request->recomended_action;
        $responsibility = $request->emp_id;
        $date_of_compliance = $request->date_of_compliance;
        $observation_status = $request->observation_status;
        $remarks = $request->remarks;
        $date_of_observation = $request->date_of_observation;


        foreach ($location as $index => $sr_no_value) {
            $data = array(
                'document_reference_id' => decryptId($request->document_reference_id),
                'date' => DBdateformat($request->inspection_date),
                'month' => $request->month,
                'safety_walk_taken_by' => decryptId($request->safety_walk_taken_by),
                'unit' => decryptId($request->unit),
                'created_by' => Auth::id(),
                'shift_id' => decryptId($request->shift_id),
                'observation_status' => RESPONSIBLE_PERSON_APPROVAL_PENDING,
                'sr_no' => $sr_no_value,
                'excat_location' => $exact_location[$index],
                'location_id' => decryptId($location[$index]),
                'observation' => $observation[$index],
                'recomended_action' => $recomended_action[$index],
                'responsible_persion' => is_array($responsibility[$index]) ? implode(',', $responsibility[$index]) : $responsibility[$index],
                'observing_status' => decryptId($observation_status[$index]),
                'observation_date' => DBdateformat($date_of_observation[$index]),
                'remarks' => $remarks[$index],
                'created_by' => Auth::id(),
            );
            $data =  $this->create($data);
            $safetyFiles = new SafetyWalkObservationFile();
            $safetyFiles->store($data->id, $index, $request->checklist_file);
            $submittedData[] = $data;
        }

        return $submittedData;
    }

    public function store_api()
    {
        $request = request();
        $data = array(
            'document_reference_id' => ($request->document_reference_id),
            'date' => DBdateformat($request->inspection_date),
            'month' => $request->month,
            'safety_walk_taken_by' => $request->safety_walk_taken_by,
            'unit' => ($request->unit),
            'created_by' => Auth::id(),
            'shift_id' => ($request->shift_id),
            'observation_status' => OBSERVATION_PENDING,
        );

        return $this->create($data);
    }


    public function exportdata()
    {
        $request = request();
        $search = '';
        $user = Auth::user();
        $userRole = string_to_array($user->role);
        $query = $this->select('inspection_safety_walk_observation.*', 'inspection_shift_option.*', 'masters_unit.*', 'inspection_safety_walk_observation.id as inspection_id', 'inspection_safety_walk_observation.created_at as inspection_created_at')
            ->leftJoin('inspection_shift_option', 'inspection_safety_walk_observation.shift_id', '=', 'inspection_shift_option.id')
            ->leftJoin('masters_unit', 'inspection_safety_walk_observation.unit', '=', 'masters_unit.id')
            ->leftJoin('inspection_static_docno', 'inspection_safety_walk_observation.document_reference_id', '=', 'inspection_static_docno.id');
        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('shift_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('unit_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('month LIKE "%' . $search . '%"');
            });
        }
        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole) || CheckUserRole(ROLE_EHS_OFFICER)) {
        } elseif (in_array(ROLE_INSPECTION_CREATOR, $userRole)) {
            $query->where('inspection_safety_walk_observation.created_by', Auth::user()->id);
        } else {
            $query->where(function ($q) {
                $q->whereRaw("FIND_IN_SET(?, inspection_safety_walk_observation.responsible_persion)", [Auth::id()])
                    ->orWhere('inspection_safety_walk_observation.created_by', Auth::id());
            });
        }
        if (isset($request->month) && $request->month) {
            $query = $query->where('inspection_safety_walk_observation.month', 'LIKE', '%' . $request->month . '%');
        }
        if (isset($request->inspection_date) && $request->inspection_date) {
            $query = $query->whereDate('inspection_safety_walk_observation.date', '=', DBdateformat($request->inspection_date));
        }
        if (isset($request->observation_status) && $request->observation_status) {
            $query = $query->where('inspection_safety_walk_observation.observation_status', decryptId($request->observation_status));
        }
        if (isset($request->unit) && $request->unit) {
            $query = $query->where('inspection_safety_walk_observation.unit', decryptId($request->unit));
        }
        if (isset($request->shift) && $request->shift) {
            $query = $query->where('inspection_safety_walk_observation.shift_id', decryptId($request->shift));
        }
        if (isset($request->emp_id) && $request->emp_id) {

            $query = $query->where('inspection_safety_walk_observation.created_by', $request->emp_id);
        }
        if ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_safety_walk_observation.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_safety_walk_observation.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_safety_walk_observation.created_at', [$startDate, $endDate]);
        }
        $query->orderBy('inspection_safety_walk_observation.id', 'DESC');

        $results = $query->get();
        $query = $results->groupBy('safety_walk_observation_id');
        return $query;
    }


    public function selectOne($id)
    {
        return $this->where('id', $id)->first();
    }
    public function GetLastMonthObservation($id)
    {
        $data = $this->where('id', $id)->first();
        $current_month = $data->month;
        $current_year = $data->created_at->year;

        $month = Carbon::parse($current_month)->month;
        $last_month = $month - 1;

        if ($last_month == 0) {
            $last_month = 12;
            $current_year -= 1;
        }

        $last_month_name = Carbon::createFromFormat('m', $last_month)->format('F');

        $last_month_record = $this->where('month', $last_month_name)
            ->whereYear('created_at', $current_year)
            ->get();

        return $last_month_record;
    }

    public function approvalSubmit($id, $date, $remarks)
    {
        $request = Request();

        $update_array = array(
            'date_of_compilance' => DBdateformat($date),
            'observer_remarks' => $remarks,
            'observer_person' => Auth::id(),
            'observation_status' => SAFETY_WALK_EHS_OFFICER_PENDING,
        );

        $this->where('id', $id)->update($update_array);
    }

    public function ehsapproval($id, $status, $remarks, $date)
    {
        $request = Request();

        $update_array = array(
            'approver_date' => DBdateformat($date),
            'approval_remarks' => $remarks,
            'approver_id' => Auth::id(),
            'observation_status' => $status,
        );

        $this->where('id', $id)->update($update_array);
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_safety_walk_observation'));
    }
}
