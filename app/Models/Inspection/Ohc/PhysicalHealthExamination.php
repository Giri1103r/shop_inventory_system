<?php

namespace App\Models\Inspection\Ohc;

use App\Models\Master\Employee;
use App\Models\Master\Work;
use App\Scopes\TrashScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PhysicalHealthExamination extends Model
{
    protected $table = 'inspection_ohc_physical_health_examination';

    protected $primaryKey = 'id';

    protected $fillable = [
        'document_reference_id',
        'emp_id',
        'emp_name',
        'date',
        'mobile_no',
        'blood_group',
        'age',
        'form_number',
        'gender',
        'height',
        'weight',
        'bmi',
        'unit_id',
        'department_id',
        'dob',
        'address',
        'past_history',
        'present_complaint',
        'personal_details',
        'family_history',
        'vital_checkpoints',
        'near_with_glass',
        'near_without_glass',
        'distance_with_glass',
        'distance_without_glass',
        'distance_without_glass_yes',
        'near_without_glass_yes',
        'remarks',
        'status',
        'trash',
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


        $query = $this->select(
            'inspection_ohc_physical_health_examination.*',
            'masters_unit.unit_name',
            'masters_department.department_name',
        )->join('masters_unit', 'inspection_ohc_physical_health_examination.unit_id', '=', 'masters_unit.id')
            ->join('masters_department', 'inspection_ohc_physical_health_examination.department_id', '=', 'masters_department.id');



        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_department.department_name', 'LIKE', '%' . $search . '%');
            });
        }
        if (isset($request->emp_id) && $request->emp_id) {
            $query = $query->where('inspection_ohc_physical_health_examination.emp_id', ($request->emp_id));
        }
        if (isset($request->emp_name) && $request->emp_name) {
            $query = $query->where('inspection_ohc_physical_health_examination.emp_name', ($request->emp_name));
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_physical_health_examination.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_physical_health_examination.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_ohc_physical_health_examination.created_at', [$startDate, $endDate]);
        }
        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_ohc_physical_health_examination.status', decryptId($request->status));
        }
        if (isset($request->unit_id) && $request->unit_id) {
            $query = $query->where('inspection_ohc_physical_health_examination.unit_id', decryptId($request->unit_id));
        }
        if (isset($request->department_id) && $request->department_id) {
            $query = $query->where('inspection_ohc_physical_health_examination.department_id', decryptId($request->department_id));
        }


        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('inspection_ohc_physical_health_examination.id', 'DESC');

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

    public function store($document_no)
    {

        $request = request();

        $employee = Employee::where('emp_id', $request->emp_id)
            ->select('*')
            ->first();


        if (!$employee) {
            $employee = Work::where('emp_id', $request->emp_id)
                ->select('*')
                ->first();
        }


        if ($employee) {
            $unit = $employee->unit;
            $department = $employee->department;
            $mobile_no = $employee->mobile_no;
            $blood_group = $employee->blood_group;
            $gender = $employee->gender;
        }

        $responses = [

            'status' => ($request->persnal_details),

        ];

        $family_details = [
            'status' => ($request->status),

            'remarks' => ($request->family_remarks),
        ];
        $reading_value = [
            'reading_value' => ($request->reading_value),
        ];

        $insert_array = [
            'document_reference_id' => $document_no->id,
            'emp_id' => $request->emp_id,
            'emp_name' => $request->emp_name,
            'date' => DBdateformat($request->date),
            'mobile_no' => $mobile_no,
            'blood_group' => $request->blood_group,
            'age' => $request->age,
            'form_number' => $request->form_number,
            'gender' => $gender,
            'height' => $request->height,
            'weight' => $request->weight,
            'bmi' => $request->bmi,
            'unit_id' => $unit,
            'department_id' => $department,
            'dob' => DBdateformat($request->dob),
            'address' => $request->address,
            'past_history' => $request->past_history,
            'present_complaint' => $request->present_complaints,
            'personal_details' => json_encode($responses),
            'family_history' => json_encode($family_details),
            'vital_checkpoints' => json_encode($reading_value),
            'near_with_glass' => $request->near_with_glasses,
            'near_without_glass' => $request->near_with_out_glasses,
            'near_without_glass_yes' => decryptId($request->near_with_out_glasses_yes),
            'distance_with_glass' => $request->distance_with_glasses,
            'distance_without_glass' => ($request->distance_with_out_glasses),
            'distance_without_glass_yes' => decryptId($request->distance_with_out_glasses_yes),
            'remarks' => $request->remarks,

            'created_by' => Auth::id(),
        ];

        return self::create($insert_array);
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
    public function Selectone($id)
    {
        return $this->where('id', $id)->first();
    }
    public function exportdata()
    {
        $request = request();
        $search = '';

        $query = $this->select(
            'inspection_ohc_physical_health_examination.*'
        );
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_physical_health_examination.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_physical_health_examination.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_ohc_physical_health_examination.created_at', [$startDate, $endDate]);
        }
        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_ohc_physical_health_examination.status', decryptId($request->status));
        }
        if (isset($request->unit_id) && $request->unit_id) {
            $query = $query->where('inspection_ohc_physical_health_examination.unit_id', decryptId($request->unit_id));
        }
        if (isset($request->department_id) && $request->department_id) {
            $query = $query->where('inspection_ohc_physical_health_examination.department_id', decryptId($request->department_id));
        }
        if (isset($request->emp_id) && $request->emp_id) {
            $query = $query->where('inspection_ohc_physical_health_examination.emp_id', ($request->emp_id));
        }
        if (isset($request->emp_name) && $request->emp_name) {
            $query = $query->where('inspection_ohc_physical_health_examination.emp_name', ($request->emp_name));
        }

        $query->orderBy('inspection_ohc_physical_health_examination.id', 'DESC');

        return  $query->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_ohc_physical_health_examination'));
    }
}
