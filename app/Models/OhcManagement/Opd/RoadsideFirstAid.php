<?php

namespace App\Models\OhcManagement\Opd;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RoadsideFirstAid extends Model
{
    protected $table = 'ohc_opd_roadside_first_aid';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'date_of_incident',
        'location_of_incident',
        'time_of_incident',
        'person_condtion',
        'first_aid_provided',
        'first_aider_name',
        'transport_to_medical_facility',
        'first_aider_name',
        'transport_method',
        'incident_report_filled',
        'remarks',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('ohc_opd_roadside_first_aid.*', 'ohc_opd_injured_condition.injured_condtion')
            ->leftjoin('ohc_opd_injured_condition', 'ohc_opd_roadside_first_aid.person_condtion', '=', 'ohc_opd_injured_condition.id')
            ->where('ohc_opd_roadside_first_aid.trash', 'NO');

            $user = Auth::user();
            $userRole = string_to_array($user->role);
            $empId = $user->employee_id;
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];


            $formattedDate = null;
            if (\DateTime::createFromFormat('d-m-Y', $search) !== false) {
                $formattedDate = \Carbon\Carbon::createFromFormat('d-m-Y', $search)->format('Y-m-d');
            }
            $query->where(function ($query) use ($search, $formattedDate) {
                $query
                    ->orWhere('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('remarks', 'LIKE', '%' . $search . '%')
                    ->orWhere('first_aider_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('time_of_incident', 'LIKE', '%' . $search . '%')
                    ->orWhere('location_of_incident', 'LIKE', '%' . $search . '%');

                    if ($formattedDate) {
                        $query->orWhere('date_of_incident', 'LIKE', '%' . $formattedDate . '%');
                    }
            });
        }
        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)) {
            $query->orderBy('ohc_opd_roadside_first_aid.id', 'DESC');
        } else {
            $query->where('ohc_opd_roadside_first_aid.created_by', Auth::id());
        }
        if ($request->has('emp_name') && $request->emp_name) {
            $query = $query->where('ohc_opd_roadside_first_aid.name', 'LIKE', '%' . $request->emp_name . '%');
        }
        if ($request->has('location_of_incident') && $request->location_of_incident) {
            $query = $query->where('ohc_opd_roadside_first_aid.location_of_incident', 'LIKE', '%' . $request->location_of_incident . '%');
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_opd_roadside_first_aid.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_opd_roadside_first_aid.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_opd_roadside_first_aid.created_at', '<=', $endDate);
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('ohc_opd_roadside_first_aid.status', decryptId($request->status));
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

            'name' => $request->emp_name,
            'date_of_incident' => DBdateformat($request->date_of_incident),
            'location_of_incident' => $request->location_of_incident,
            'time_of_incident' => $request->time_of_incident,
            'person_condtion' => $request->person_condtion,
            'transport_to_medical_facility' => $request->transport_to_medical_facility,
            'first_aid_provided' => $request->first_aid_provided,
            'first_aider_name' => $request->first_aider_name,
            'transport_method' => $request->transport_method,
            'incident_report_filled' => $request->incident_report_filled,
            'remarks' => $request->remarks,
            'created_by' => Auth::id(),
        ];
        return $this->create($insert_array);
    }

    public function updates($id)
    {
        $request = request();

        $update_array = [
            'name' => $request->emp_name,
            'date_of_incident' => DBdateformat($request->date_of_incident),
            'location_of_incident' => $request->location_of_incident,
            'time_of_incident' => $request->time_of_incident,
            'person_condtion' => $request->person_condtion,
            'transport_to_medical_facility' => $request->transport_to_medical_facility,
            'first_aid_provided' => $request->first_aid_provided,
            'first_aider_name' => $request->first_aider_name,
            'transport_method' => $request->transport_method,
            'incident_report_filled' => $request->incident_report_filled,
            'remarks' => $request->remarks,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),

        ];
        return $this->where('id', $id)->update($update_array);
    }

    public function Selectone($id)
    {
        $data =  $this->where('ohc_opd_roadside_first_aid.id', $id)
            ->select('ohc_opd_roadside_first_aid.*')

            ->where('ohc_opd_roadside_first_aid.trash', 'No')
            ->first();
        return $data;
    }

    public function UniqueCheck($data)
    {

        return $this->where('name',  $data)->get();
    }

    public function ExistuniqueCheck($data, $id)
    {
        return $this->where('name',  $data)
            ->where('id', '!=', $id)
            ->get();
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
    public function exportdata()
    {
        $request = request();
        $search = '';
        $user = Auth::user();
        $userRole = string_to_array($user->role);
        $empId = $user->employee_id;
        $query = $this->select('ohc_opd_roadside_first_aid.*');
        if (!empty($request->search) && isset($request->search['value']) && $request->search['value'] !== '') {
            $search = $request->search['value'];


            $formattedDate = null;
            if (\DateTime::createFromFormat('d-m-Y', $search) !== false) {
                $formattedDate = \Carbon\Carbon::createFromFormat('d-m-Y', $search)->format('Y-m-d');
            }
            $query->where(function ($query) use ($search, $formattedDate) {
                $query
                    ->orWhere('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('remarks', 'LIKE', '%' . $search . '%')
                    ->orWhere('first_aider_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('time_of_incident', 'LIKE', '%' . $search . '%')
                    ->orWhere('location_of_incident', 'LIKE', '%' . $search . '%');

                    if ($formattedDate) {
                        $query->orWhere('date_of_incident', 'LIKE', '%' . $formattedDate . '%');
                    }
            });
        }
        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)) {
            $query->orderBy('ohc_opd_roadside_first_aid.id', 'DESC');
        } else {
            $query->where('ohc_opd_roadside_first_aid.created_by', Auth::id());
        }
        if ($request->has('emp_name') && $request->emp_name) {
            $query = $query->where('ohc_opd_roadside_first_aid.name', 'LIKE', '%' . $request->emp_name . '%');
        }

        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_opd_roadside_first_aid.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_opd_roadside_first_aid.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_opd_roadside_first_aid.created_at', '<=', $endDate);
        }
        if ($request->has('location_of_incident') && $request->location_of_incident) {
            $query = $query->where('ohc_opd_roadside_first_aid.location_of_incident', 'LIKE', '%' . $request->location_of_incident . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('ohc_opd_roadside_first_aid.status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
    }
}
