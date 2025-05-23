<?php

namespace App\Models\Inspection\Ohc;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class FirstAidBagChecklist extends Model
{
    protected $table = 'inspection_ohc_first_aid_bag_inspection';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'inspection_date',
        'location',
        'shift_id',
        'unit',
        'frequency',
        'next_due',
        'inspection_data',
        'approval_remarks',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];


    public function list()
    {

        $request = request();
        $search = '';

        $query = $this->select('inspection_ohc_first_aid_bag_inspection.*', 'masters_unit.*', 'masters_location.*', 'inspection_frequency_option.*', 'inspection_ohc_first_aid_bag_inspection.id as inspection_id', 'inspection_ohc_first_aid_bag_inspection.created_at as inspection_created_at', 'inspection_ohc_first_aid_bag_inspection.created_by as checked_by', 'inspection_shift_option.*')
            ->leftJoin('masters_location', 'inspection_ohc_first_aid_bag_inspection.location', '=', 'masters_location.id')
            ->leftJoin('masters_unit', 'inspection_ohc_first_aid_bag_inspection.unit', '=', 'masters_unit.id')
            ->leftJoin('inspection_shift_option', 'inspection_ohc_first_aid_bag_inspection.shift_id', '=', 'inspection_shift_option.id')
            ->leftJoin('inspection_frequency_option', 'inspection_ohc_first_aid_bag_inspection.frequency', '=', 'inspection_frequency_option.id');

        $org_total =  $query;
        $org_total_counts = $org_total->count();
        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_ohc_first_aid_bag_inspection.inspection_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_ohc_first_aid_bag_inspection.next_due', 'LIKE', '%' . $search . '%');
                $query->orWhereRaw('masters_location.location_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('masters_unit.unit_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_frequency_option.frequency_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_shift_option.shift_name LIKE "%' . $search . '%"');
            });
        }

        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_first_aid_bag_inspection.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_first_aid_bag_inspection.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_ohc_first_aid_bag_inspection.created_at', [$startDate, $endDate]);
        }

        if ($request->has('inspection_date') && $request->inspection_date) {
            $formattedDate = DBdateformat($request->inspection_date);
            $query = $query->whereDate('inspection_ohc_first_aid_bag_inspection.inspection_date', $formattedDate);
        }

        if ($request->has('next_due') && $request->next_due) {
            $formattedDate = DBdateformat($request->next_due);
            $query = $query->whereDate('inspection_ohc_first_aid_bag_inspection.next_due', $formattedDate);
        }

        if (isset($request->location) && $request->location) {
            $query = $query->where('inspection_ohc_first_aid_bag_inspection.location',  decryptId($request->location));
        }
        if (isset($request->frequency) && $request->frequency) {
            $query = $query->where('inspection_ohc_first_aid_bag_inspection.frequency',  decryptId($request->frequency));
        }
        if (isset($request->unit) && $request->unit) {
            $query = $query->where('inspection_ohc_first_aid_bag_inspection.unit',  decryptId($request->unit));
        }
        if (isset($request->shift) && $request->shift) {
            $query = $query->where('inspection_ohc_first_aid_bag_inspection.shift_id',  decryptId($request->shift));
        }


        if ($request->has('status') && $request->status) {

            $query = $query->where('inspection_ohc_first_aid_bag_inspection.status',  decryptId($request->status));
        }


        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('inspection_ohc_first_aid_bag_inspection.id', 'DESC');

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

    public function listApi()
    {
        $request = request();
        $search = '';

        $query = $this->select('inspection_ohc_first_aid_bag_inspection.*', 'masters_unit.*', 'masters_location.*', 'inspection_frequency_option.*', 'inspection_ohc_first_aid_bag_inspection.id as inspection_id', 'inspection_ohc_first_aid_bag_inspection.created_at as inspection_created_at', 'inspection_ohc_first_aid_bag_inspection.created_by as checked_by', 'inspection_shift_option.*')
            ->leftJoin('masters_location', 'inspection_ohc_first_aid_bag_inspection.location', '=', 'masters_location.id')
            ->leftJoin('masters_unit', 'inspection_ohc_first_aid_bag_inspection.unit', '=', 'masters_unit.id')
            ->leftJoin('inspection_shift_option', 'inspection_ohc_first_aid_bag_inspection.shift_id', '=', 'inspection_shift_option.id')
            ->leftJoin('inspection_frequency_option', 'inspection_ohc_first_aid_bag_inspection.frequency', '=', 'inspection_frequency_option.id');

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search != null || $request->search != '') {
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_ohc_first_aid_bag_inspection.inspection_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_ohc_first_aid_bag_inspection.next_due', 'LIKE', '%' . $search . '%');
                $query->orWhereRaw('masters_location.location_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('masters_unit.unit_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_frequency_option.frequency_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_shift_option.shift_name LIKE "%' . $search . '%"');
            });
        }

        $data = $query->orderby('inspection_ohc_first_aid_bag_inspection.id')->get();
        $inspection_data = $data->toArray();
        $data_array = [];
        $refined_data = [];


        foreach ($inspection_data as $index => $data) {
            $data_array['id'] = $data['id'];
            $data_array['date_of_inspection'] = Displaydateformat($data['inspection_date']);
            $data_array['next_due'] = Displaydateformat($data['next_due']);
            $data_array['location_name'] = $data['location_name'];
            $data_array['shift_name'] = $data['shift'];
            $data_array['frequency_name'] = $data['frequency_name'];
            $data_array['created_at'] = Displaydateformat($data['inspection_created_at']);
            $data_array['created_by'] = getUsername($data['checked_by']);
            $refined_data[$index] = $data_array;
        }
        return $refined_data;
    }

    public function store()
    {
        $request = request();
        $id = $request->id;
        foreach ($id as $index => $value) {
            $id = decryptId($value);
            $updated_medicine_checklist[$id] = [
                'medicine_id' => $id,
                'available_quantity' => $request->available_quantity[$index],
                'expired_date' => dbdateformat($request->expired_date[$index]),
                'emp_id' => $request->emp_id[$index],
                'remarks' => $request->remarks[$index],
                'freeze_quantity' => $request->freeze_quantity[$index],
            ];
        }
        $updated_medicine_checklist = json_encode($updated_medicine_checklist);
        $data = [
            'inspection_date' => DBdateformat($request->inspection_date),
            'next_due' => DBdateformat($request->next_due),
            'inspection_data' =>  $updated_medicine_checklist,
            'created_by' =>  Auth::id(),
            'location' => decryptId($request->location_id),
            'unit' => decryptId($request->unit_id),
            'frequency' => decryptId($request->frequency_id),
            'shift_id' => decryptId($request->shift_id),
        ];
        return  $this->create($data);
    }

    public function store_api()
    {
        $request = request();
        $id = $request->id;
        foreach ($id as $index => $value) {
            $id = ($value);
            $updated_medicine_checklist[$id] = [
                'medicine_id' => $id,
                'available_quantity' => $request->available_quantity[$index],
                'expired_date' => dbdateformat($request->expired_date[$index]),
                'emp_id' => $request->emp_id[$index],
                'remarks' => $request->remarks[$index],
                'freeze_quantity' => $request->freeze_quantity[$index],
            ];
        }
        $updated_medicine_checklist = json_encode($updated_medicine_checklist);
        $data = [
            'inspection_date' => DBdateformat($request->inspection_date),
            'next_due' => DBdateformat($request->next_due),
            'inspection_data' =>  $updated_medicine_checklist,
            'created_by' =>  Auth::id(),
            'location' => ($request->location_id),
            'unit' => ($request->unit_id),
            'frequency' => ($request->frequency_id),
            'shift_id' => ($request->shift_id),
        ];
        return  $this->create($data);
    }

    public function selectOne($id)
    {
        return $this->where('id', $id)->first();
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_ohc_first_aid_bag_inspection.*', 'masters_unit.*', 'masters_location.*', 'inspection_frequency_option.*', 'inspection_ohc_first_aid_bag_inspection.id as inspection_id', 'inspection_ohc_first_aid_bag_inspection.created_by as checked_by', 'inspection_shift_option.*')
            ->leftJoin('masters_location', 'inspection_ohc_first_aid_bag_inspection.location', '=', 'masters_location.id')
            ->leftJoin('masters_unit', 'inspection_ohc_first_aid_bag_inspection.unit', '=', 'masters_unit.id')
            ->leftJoin('inspection_shift_option', 'inspection_ohc_first_aid_bag_inspection.shift_id', '=', 'inspection_shift_option.id')
            ->leftJoin('inspection_frequency_option', 'inspection_ohc_first_aid_bag_inspection.frequency', '=', 'inspection_frequency_option.id');


        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_ohc_first_aid_bag_inspection.inspection_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_ohc_first_aid_bag_inspection.next_due', 'LIKE', '%' . $search . '%');
                $query->orWhereRaw('masters_location.location_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('masters_unit.unit_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_shift_option.shift_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_frequency_option.frequency_name LIKE "%' . $search . '%"');;
            });
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_first_aid_bag_inspection.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_first_aid_bag_inspection.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_ohc_first_aid_bag_inspection.created_at', [$startDate, $endDate]);
        }

        if ($request->has('inspection_date') && $request->inspection_date) {
            $formattedDate = DBdateformat($request->inspection_date);
            $query = $query->whereDate('inspection_ohc_first_aid_bag_inspection.inspection_date', $formattedDate);
        }

        if ($request->has('next_due') && $request->next_due) {
            $formattedDate = DBdateformat($request->next_due);
            $query = $query->whereDate('inspection_ohc_first_aid_bag_inspection.next_due', $formattedDate);
        }
        if (isset($request->location) && $request->location) {
            $query = $query->where('inspection_ohc_first_aid_bag_inspection.location', 'LIKE', '%' . decryptId($request->location) . '%');
        }
        if (isset($request->frequency) && $request->frequency) {
            $query = $query->where('inspection_ohc_first_aid_bag_inspection.frequency', 'LIKE', '%' . decryptId($request->frequency) . '%');
        }
        if (isset($request->unit) && $request->unit) {
            $query = $query->where('inspection_ohc_first_aid_bag_inspection.unit', 'LIKE', '%' . decryptId($request->unit) . '%');
        }
        if (isset($request->shift) && $request->shift) {
            $query = $query->where('inspection_ohc_first_aid_bag_inspection.shift_id', 'LIKE', '%' . decryptId($request->shift) . '%');
        }
        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_ohc_first_aid_bag_inspection.inspection_status', decryptId($request->inspection_status));
        }
        $query->orderBy('inspection_ohc_first_aid_bag_inspection.id', 'DESC');

        return  $query->get();
    }

    public function approvalSubmit($id, $status, $remarks)
    {
        $request = Request();
        if ($status == 1) {
            $update_array = [
                'updated_by' => Auth::id(),
                'inspection_status' => OBSERVATION_APPROVED,
                'approval_remarks' => $remarks,
            ];
        } else {
            $update_array = [
                'updated_by' => Auth::id(),
                'inspection_status' => OBSERVATION_REJECTED,
                'approval_remarks' => $remarks,
            ];
        }
        $this->where('id', $id)->update($update_array);
    }
}
