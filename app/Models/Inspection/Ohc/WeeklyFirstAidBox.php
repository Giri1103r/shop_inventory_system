<?php

namespace App\Models\Inspection\Ohc;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WeeklyFirstAidBox extends Model
{
    use  HasFactory;

    protected $table = 'inspection_ohc_weekly_first_aid_box_inspection_checklist_details';
    protected $primaryKey = 'id';

    protected $fillable = [
        'document_reference_id',
        'date_of_inspection',
        'location',
        'first_aid_box_no',
        'first_aider',
        'shift',
        'unit',
        'inspection_data',
        'remark_by',
        'created_by',
        'updated_by',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];


    public function list()
    {
        $request = request();
        $search = '';

        $query = $this->select('inspection_ohc_weekly_first_aid_box_inspection_checklist_details.*', 'masters_location.location_name', 'masters_unit.unit_name', 'inspection_shift_option.shift', 'ohc_master_certified_first_aider.certifier_name', 'inspection_ohc_weekly_first_aid_box_inspection_checklist_details.shift as shift_id')
            ->leftJoin('masters_location', 'masters_location.id', '=', 'inspection_ohc_weekly_first_aid_box_inspection_checklist_details.location')
            ->leftJoin('masters_unit', 'masters_unit.id', '=', 'inspection_ohc_weekly_first_aid_box_inspection_checklist_details.unit')
            ->leftJoin('ohc_master_certified_first_aider', 'ohc_master_certified_first_aider.id', '=', 'inspection_ohc_weekly_first_aid_box_inspection_checklist_details.first_aider')
            ->leftJoin('inspection_shift_option', 'inspection_shift_option.id', '=', 'inspection_ohc_weekly_first_aid_box_inspection_checklist_details.shift');


        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_ohc_weekly_first_aid_box_inspection_checklist_details.first_aid_box_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_shift_option.shift', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_location.location_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('ohc_master_certified_first_aider.certifier_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('first_aid_box_no') && $request->first_aid_box_no) {
            $query = $query->where('inspection_ohc_weekly_first_aid_box_inspection_checklist_details.first_aid_box_no', 'LIKE', '%' . $request->first_aid_box_no . '%');
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_weekly_first_aid_box_inspection_checklist_details.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_weekly_first_aid_box_inspection_checklist_details.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_ohc_weekly_first_aid_box_inspection_checklist_details.created_at', [$startDate, $endDate]);
        }
        if ($request->has('shift') && $request->shift) {
            $query = $query->where('inspection_ohc_weekly_first_aid_box_inspection_checklist_details.shift', 'LIKE', '%' . decryptId($request->shift) . '%');
        }

        if ($request->has('location') && $request->location) {
            $query = $query->where('location', 'LIKE', '%' . decryptId($request->location) . '%');
        }

        if ($request->has('unit') && $request->unit) {
            $query = $query->where('unit', 'LIKE', '%' . decryptId($request->unit) . '%');
        }

        if ($request->has('first_aider') && $request->first_aider) {
            $query = $query->where('first_aider', 'LIKE', '%' . decryptId($request->first_aider) . '%');
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
        $perPage = $request->input('per_page', 10);
        $search = '';

        $query = $this->select('inspection_ohc_weekly_first_aid_box_inspection_checklist_details.*', 'masters_location.location_name', 'masters_unit.unit_name', 'inspection_shift_option.shift', 'ohc_master_certified_first_aider.certifier_name', 'inspection_ohc_weekly_first_aid_box_inspection_checklist_details.shift as shift_id')
            ->leftJoin('masters_location', 'masters_location.id', '=', 'inspection_ohc_weekly_first_aid_box_inspection_checklist_details.location')
            ->leftJoin('masters_unit', 'masters_unit.id', '=', 'inspection_ohc_weekly_first_aid_box_inspection_checklist_details.unit')
            ->leftJoin('ohc_master_certified_first_aider', 'ohc_master_certified_first_aider.id', '=', 'inspection_ohc_weekly_first_aid_box_inspection_checklist_details.first_aider')
            ->leftJoin('inspection_shift_option', 'inspection_shift_option.id', '=', 'inspection_ohc_weekly_first_aid_box_inspection_checklist_details.shift');


        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_ohc_weekly_first_aid_box_inspection_checklist_details.first_aid_box_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_shift_option.shift', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_location.location_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('ohc_master_certified_first_aider.certifier_name', 'LIKE', '%' . $search . '%');
            });
        }

        $paginatedData = $query->orderby('inspection_ohc_weekly_first_aid_box_inspection_checklist_details.id')->paginate($perPage);
        $inspection_data = $paginatedData->toArray();


        if (empty($inspection_data['data'])) {
            return $this->sendError('No records found.', [], 404);
        }

        $data_array = [];
        $refined_data = [];

        foreach ($inspection_data['data'] as $index => $data) {
            $data_array['id'] = $data['id'];
            $data_array['first_aid_box_no'] = $data['first_aid_box_no'];
            $data_array['location_name'] = $data['location_name'];
            $data_array['shift_name'] = $data['shift'];
            $data_array['unit_name'] = $data['unit_name'];
            $data_array['first_aider_name'] = $data['certifier_name'];
            $data_array['created_at'] = Displaydateformat($data['created_at']);
            $data_array['created_by'] = getUsername($data['created_by']);

            $refined_data[$index] = $data_array;
        }

        $response = [
            'per_page' => $paginatedData->perPage(),
            'current_page' => $paginatedData->currentPage(),
            'from' => $paginatedData->firstItem(),
            'to' => $paginatedData->lastItem(),
            'total' => $paginatedData->total(),
            'total_page' => $paginatedData->lastPage(),
            'list' => $refined_data,
        ];

        return $response;
    }

    public function store()
    {
        $request = request();
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

            'document_reference_id' => $request->document_reference_id,
            'date_of_inspection' =>  DBdateformat($request->date_of_inspection),
            'location' => decryptId($request->location_id),
            'first_aid_box_no' => $request->first_aid_box_no,
            'shift' => decryptId($request->shift),
            'unit' => decryptId($request->unit_id),
            'first_aider' => decryptId($request->first_aider),
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

            'document_reference_id' => $request->document_reference_id,
            'date_of_inspection' =>  DBdateformat($request->date_of_inspection),
            'location' => $request->location_id,
            'first_aid_box_no' => $request->first_aid_box_no,
            'shift' => $request->shift,
            'unit' => $request->unit_id,
            'first_aider' => $request->first_aider,
            'remark_by' => $request->remark_by,
            'inspection_data' => $updated_medicine_checklist,
            'created_by' => Auth::id(),
        ];
        $inspection_data =   $this->create($data);

        return $inspection_data;
    }


    public function selectOne($id)
    {
        return $this->where('id', $id)->first();
    }

    public function exportdata()
    {
        $request = request();
        // dd($request);
        $search = '';
        $query = $this->select(
            'inspection_ohc_weekly_first_aid_box_inspection_checklist_details.*',
            'masters_location.location_name',
            'masters_unit.unit_name',
            'inspection_shift_option.shift',
            'ohc_master_certified_first_aider.certifier_name',
            'inspection_ohc_weekly_first_aid_box_inspection_checklist_details.shift as shift_id',
            'inspection_ohc_weekly_first_aid_box_inspection_checklist_details.id as checklist_id',
            'inspection_static_docno.*'
        )
            ->leftJoin('masters_location', 'masters_location.id', '=', 'inspection_ohc_weekly_first_aid_box_inspection_checklist_details.location')
            ->leftJoin('masters_unit', 'masters_unit.id', '=', 'inspection_ohc_weekly_first_aid_box_inspection_checklist_details.unit')
            ->leftJoin('ohc_master_certified_first_aider', 'ohc_master_certified_first_aider.id', '=', 'inspection_ohc_weekly_first_aid_box_inspection_checklist_details.first_aider')
            ->leftJoin('inspection_shift_option', 'inspection_shift_option.id', '=', 'inspection_ohc_weekly_first_aid_box_inspection_checklist_details.shift')
            ->leftJoin('inspection_static_docno', 'inspection_ohc_weekly_first_aid_box_inspection_checklist_details.document_reference_id', '=', 'inspection_static_docno.id');

        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_ohc_weekly_first_aid_box_inspection_checklist_details.first_aid_box_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_shift_option.shift', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_location.location_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('ohc_master_certified_first_aider.certifier_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_weekly_first_aid_box_inspection_checklist_details.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_weekly_first_aid_box_inspection_checklist_details.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_ohc_weekly_first_aid_box_inspection_checklist_details.created_at', [$startDate, $endDate]);
        }
        if ($request->has('first_aid_box_no') && $request->first_aid_box_no) {
            $query = $query->where('inspection_ohc_weekly_first_aid_box_inspection_checklist_details.first_aid_box_no', 'LIKE', '%' . $request->first_aid_box_no . '%');
        }

        if ($request->has('shift') && $request->shift) {
            $query = $query->where('inspection_ohc_weekly_first_aid_box_inspection_checklist_details.shift', 'LIKE', '%' . decryptId($request->shift) . '%');
        }

        if ($request->has('location_id') && $request->location_id) {
            $query = $query->where('location', 'LIKE', '%' . decryptId($request->location_id) . '%');
        }

        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('unit', 'LIKE', '%' . decryptId($request->unit_id) . '%');
        }

        if ($request->has('first_aider') && $request->first_aider) {
            $query = $query->where('first_aider', 'LIKE', '%' . decryptId($request->first_aider) . '%');
        }


        $query->orderBy('inspection_ohc_weekly_first_aid_box_inspection_checklist_details.id', 'DESC');

        return  $query->get();
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


    public function getInspectionData($id)
    {
        $data = $this->select('inspection_ohc_weekly_first_aid_box_inspection_checklist_details.*', 'inspection_static_docno.doc_no', 'inspection_static_docno.issue_date', 'inspection_static_docno.rev_dt')
            ->leftjoin('inspection_static_docno', 'inspection_static_docno.id', '=', 'inspection_ohc_weekly_first_aid_box_inspection_checklist_details.document_reference_id')
            ->where('inspection_ohc_weekly_first_aid_box_inspection_checklist_details.id', $id)
            ->first();

        return $data;
    }
}
