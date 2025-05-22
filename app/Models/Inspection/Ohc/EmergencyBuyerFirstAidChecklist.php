<?php

namespace App\Models\Inspection\Ohc;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Expr\FuncCall;

class EmergencyBuyerFirstAidChecklist extends Model
{
    use  HasFactory;

    protected $table = 'inspection_ohc_emergency_buyer_first_aid_bag_checklist_details';
    protected $primaryKey = 'id';

    protected $fillable = [
        'date_of_inspection',
        'location_first_aid_bag',
        'shift_id',
        'due_date',
        'unit_id',
        'frequency_id',
        'inspection_data',
        'remark_by',
        'created_by',
        'updated_by',
        'status',
        'trash',
    ];


    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];

    public function list()
    {
        $request = request();
        $search = '';

        $query = $this->select('inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.*', 'inspection_shift_option.shift', 'masters_unit.unit_name', 'inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.created_at as inspection_created_at', 'inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.created_by as inspection_created_by',)
            ->leftJoin('inspection_shift_option', 'inspection_shift_option.id', '=', 'inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.shift_id')
            ->leftJoin('masters_unit', 'masters_unit.id', '=', 'inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.unit_id');

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.date_of_inspection', 'LIKE', '%' . DBdateformat($search) . '%')
                    ->orWhere('inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.location_first_aid_bag', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_shift_option.shift', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.created_at', [$startDate, $endDate]);
        }

        if ($request->has('date_of_inspection') && $request->date_of_inspection) {
            $formattedDate = DBdateformat($request->date_of_inspection);
            $query = $query->whereDate('inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.date_of_inspection', $formattedDate);
        }

        if ($request->has('location_first_aid_bag') && $request->location_first_aid_bag) {
            $query = $query->where('inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.location_first_aid_bag', 'LIKE', '%' . $request->location_first_aid_bag . '%');
        }

        if ($request->has('shift_id') && $request->shift_id) {
            $query = $query->where('shift_id', 'LIKE', '%' . decryptId($request->shift_id) . '%');
        }

        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.unit_id', 'LIKE', '%' . decryptId($request->unit_id) . '%');
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

    public function listApi()
    {
        $request = request();
        $search = '';

        $query = $this->select('inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.*', 'inspection_shift_option.shift', 'masters_unit.unit_name', 'inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.created_at as inspection_created_at', 'inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.created_by as inspection_created_by',)
            ->leftJoin('inspection_shift_option', 'inspection_shift_option.id', '=', 'inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.shift_id')
            ->leftJoin('masters_unit', 'masters_unit.id', '=', 'inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.unit_id');

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search != null || $request->search != '') {

            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.date_of_inspection', 'LIKE', '%' . DBdateformat($search) . '%')
                    ->orWhere('inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.location_first_aid_bag', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_shift_option.shift', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%');
            });
        }

        $data = $query->orderby('inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.id')->get();
        $inspection_data = $data->toArray();
        $data_array = [];
        $refined_data = [];

        foreach ($inspection_data as $index => $data) {
            $data_array['id'] = $data['id'];
            $data_array['date_of_inspection'] = Displaydateformat($data['date_of_inspection']);
            $data_array['location_first_aid_bag'] = $data['location_first_aid_bag'];
            $data_array['shift_name'] = $data['shift'];
            $data_array['unit_name'] = $data['unit_name'];
            $data_array['created_at'] = Displaydateformat($data['inspection_created_at']);
            $data_array['created_by'] = getUsername($data['inspection_created_by']);

            $refined_data[$index] = $data_array;
        }


        return $refined_data;
    }

    public function store()
    {
        $request = request();
        // dd($request);
        $id = $request->medicine_id;
        foreach ($id as $index => $value) {
            $id = decryptId($value);
            $updated_medicine_checklist[$id] = [
                'medicine_id' => $id,
                'freeze_quantity' => $request->freeze_quantity[$index],
                'available_quantity' => $request->available_quantity[$index],
                'expired_date' => dbdateformat($request->expired_date[$index]),
                'remarks' => $request->remarks[$index],

            ];
        }
        $updated_medicine_checklist = json_encode($updated_medicine_checklist);
        $data = [
            'date_of_inspection' =>  DBdateformat($request->date_of_inspection),
            'location_first_aid_bag' => $request->location_first_aid_bag,
            'shift_id' => decryptId($request->shift),
            'due_date' => DBdateformat($request->next_due_date),
            'unit_id' => decryptId($request->unit_id),
            'frequency_id' => decryptId($request->frequency_id),
            'remark_by' => $request->remark_by,
            'inspection_data' => $updated_medicine_checklist,
            'created_by' => Auth::id(),
        ];
        return  $this->create($data);
    }

    public function storeApi()
    {
        $request = request();

        $id = $request->medicine_id;
        foreach ($id as $index => $value) {
            $id = $value;
            $updated_medicine_checklist[$id] = [
                'medicine_id' => $id,
                'freeze_quantity' => $request->freeze_quantity[$index],
                'available_quantity' => $request->available_quantity[$index],
                'expired_date' => dbdateformat($request->expired_date[$index]),
                'remarks' => $request->remarks[$index],

            ];
        }
        $updated_medicine_checklist = json_encode($updated_medicine_checklist);
        $data = [
            'date_of_inspection' =>  DBdateformat($request->date_of_inspection),
            'location_first_aid_bag' => $request->location_first_aid_bag,
            'shift_id' => $request->shift,
            'due_date' => DBdateformat($request->next_due_date),
            'unit_id' => $request->unit_id,
            'frequency_id' => $request->frequency_id,
            'remark_by' => $request->remark_by,
            'inspection_data' => $updated_medicine_checklist,
            'created_by' => Auth::id(),
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
        $query = $this->select('inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.*', 'inspection_shift_option.shift', 'masters_unit.unit_name',)
            ->leftJoin('inspection_shift_option', 'inspection_shift_option.id', '=', 'inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.shift_id')
            ->leftJoin('masters_unit', 'masters_unit.id', '=', 'inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.unit_id');

        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.date_of_inspection', 'LIKE', '%' . DBdateformat($search) . '%')
                    ->orWhere('inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.location_first_aid_bag', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_shift_option.shift', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('date_of_inspection') && $request->date_of_inspection) {
            $formattedDate = DBdateformat($request->date_of_inspection);
            $query = $query->whereDate('inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.date_of_inspection', $formattedDate);
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.created_at', [$startDate, $endDate]);
        }

        if ($request->has('location_first_aid_bag') && $request->location_first_aid_bag) {
            $query = $query->where('inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.location_first_aid_bag', 'LIKE', '%' . $request->location_first_aid_bag . '%');
        }

        if ($request->has('shift_id') && $request->shift_id) {
            $query = $query->where('shift_id', 'LIKE', '%' . decryptId($request->shift_id) . '%');
        }

        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('inspection_ohc_emergency_buyer_first_aid_bag_checklist_details.unit_id', 'LIKE', '%' . decryptId($request->unit_id) . '%');
        }

        $query->orderBy('id', 'DESC');

        return  $query->get();
    }
}
