<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class MedicineRequistionSlipfdodetails extends Model
{
    protected $table = 'inspection_ohc_medicine_requisition_slip_fdo_details';

    protected $primaryKey = 'id';


    protected $fillable = [
        'document_reference_id',
        'date_of_inspection',
        'first_aider',
        'first_aid_box_no',
        'location',
        'shift',
        'next_due',
        'unit',
        'department',
        'frequency',
        'date',
        'checked_by',
        'checklist',
        'verified_by',
        'approve_status',
        'approved_by',
        'l1_manager_verified_by',
        'l2_manager_verified_by',
        'created_by',
        'updated_by',
        'status',
        'trash'
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
            'inspection_ohc_medicine_requisition_slip_fdo_details.*',
            'inspection_static_docno.*',
            'masters_unit.*',
            'masters_department.*',
            'inspection_ohc_medicine_requisition_slip_fdo_details.id as inspection_id',
            'inspection_ohc_medicine_requisition_slip_fdo_details.created_at as inspection_created_at',
            'inspection_ohc_medicine_requisition_slip_fdo_details.created_by as inspection_created_by',
        )
            ->leftJoin('masters_unit', 'inspection_ohc_medicine_requisition_slip_fdo_details.unit', '=', 'masters_unit.id')
            ->leftJoin('masters_department', 'inspection_ohc_medicine_requisition_slip_fdo_details.department', '=', 'masters_department.id')
            ->leftJoin(
                'inspection_static_docno',
                'inspection_ohc_medicine_requisition_slip_fdo_details.document_reference_id',
                '=',
                'inspection_static_docno.id'
            );


        $user = Auth::user();
        $userRole = string_to_array($user->role);
        $empId = $user->employee_id;
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
        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole) || in_array(ROLE_SAFETY_OFFICER, $userRole) || in_array(ROLE_MEDICAL_ASSISTANT, $userRole)) {
            $query->orderBy('inspection_ohc_medicine_requisition_slip_fdo_details.id', 'DESC');
        } else {
            $query->where('inspection_ohc_medicine_requisition_slip_fdo_details.created_by', Auth::id());
        }
        if (isset($request->unit_id) && $request->unit_id) {
            $query = $query->where('inspection_ohc_medicine_requisition_slip_fdo_details.unit', decryptId($request->unit_id));
        }
        if (isset($request->department_id) && $request->department_id) {
            $query = $query->where('inspection_ohc_medicine_requisition_slip_fdo_details.department', decryptId($request->department_id));
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_medicine_requisition_slip_fdo_details.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_medicine_requisition_slip_fdo_details.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_ohc_medicine_requisition_slip_fdo_details.created_at', [$startDate, $endDate]);
        }
        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_ohc_medicine_requisition_slip_fdo_details.approve_status',  decryptId($request->status));
        }
        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "rev_date":
                    $query->orderBy('inspection_ohc_medicine_requisition_slip_fdo_details.revision_date', $columnorder);
                    break;
                case "issue_date":
                    $query = $query->orderBy('inspection_ohc_medicine_requisition_slip_fdo_details.issue_date', $columnorder);
                    break;
                case "document_number":
                    $query = $query->orderBy('inspection_ohc_medicine_requisition_slip_fdo_details.doc_no', $columnorder);
                    break;
                case "status":
                    $query = $query->orderBy('inspection_ohc_medicine_requisition_slip_fdo_details.status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_ohc_medicine_requisition_slip_fdo_details.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_ohc_medicine_requisition_slip_fdo_details.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_ohc_medicine_requisition_slip_fdo_details.id', 'DESC');
                    break;
            }
        }


        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('inspection_ohc_medicine_requisition_slip_fdo_details.id', 'DESC');

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

            'shift' => decryptId($request->shift),
            'location' => decryptId($request->location_id),
            'department' => decryptId($request->department_id),
            'document_reference_id' => decryptId($request->document_reference_id),
            'unit' => decryptId($request->unit_id),
            'date' => Carbon::now(),
            'next_due' => !empty($request->next_due_on) ? DBdateformat($request->next_due_on) : null,
            'first_aid_box_no' => $request->first_aid_box_no,
            'first_aider' => $request->first_aider,
            'date_of_inspection' => !empty($request->date_of_inspection) ? DBdateformat($request->date_of_inspection) : null,
            'created_by' => Auth::id(),
            'approve_status' => SAFETY_OFFICER_APPROVAL_PENDING,
        ];

        return self::create($insert_array);
    }

    public function Selectone($id)
    {
        return $this->where('id', $id)->first();
    }

    public function ExcelSelectone($id)
    {
        return $this->where('id', $id)->get();
    }
    public function safetyofficerapprovalupdate($id, $nextStatus)
    {
        return $this->where('id', $id)->update(['approved_by' => Auth::id(), 'approve_status' => $nextStatus]);
    }
    public function exportdata()
    {
        $search = '';
        $request = Request();
        $query = $this->select(
            'inspection_ohc_medicine_requisition_slip_fdo_details.*'
        );


        $user = Auth::user();
        $userRole = string_to_array($user->role);
        $empId = $user->employee_id;
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

        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole) || in_array(ROLE_SAFETY_OFFICER, $userRole) || in_array(ROLE_MEDICAL_ASSISTANT, $userRole)) {
            $query->orderBy('inspection_ohc_medicine_requisition_slip_fdo_details.id', 'DESC');
        } else {
            $query->where('inspection_ohc_medicine_requisition_slip_fdo_details.created_by', Auth::id());
        }
        if (isset($request->unit_id) && $request->unit_id) {
            $query = $query->where('inspection_ohc_medicine_requisition_slip_fdo_details.unit', decryptId($request->unit_id));
        }
        if (isset($request->department_id) && $request->department_id) {
            $query = $query->where('inspection_ohc_medicine_requisition_slip_fdo_details.department', decryptId($request->department_id));
        }
        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_ohc_medicine_requisition_slip_fdo_details.approve_status',  decryptId($request->status));
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_medicine_requisition_slip_fdo_details.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_medicine_requisition_slip_fdo_details.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_ohc_medicine_requisition_slip_fdo_details.created_at', [$startDate, $endDate]);
        }
        return $query->orderBy('inspection_ohc_medicine_requisition_slip_fdo_details.id', 'DESC')->get();
    }
}
