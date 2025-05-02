<?php

namespace App\Models\Inspection\Ohc;

use App\Models\Master\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class MedicineRequistionSlipfloordetails extends Model
{
    protected $table = 'inspection_ohc_medicine_requisition_slip_floor_details';

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
            'inspection_ohc_medicine_requisition_slip_floor_details.*',
            'inspection_static_docno.*',
            'inspection_ohc_medicine_requisition_slip_floor_details.id as inspection_id',
            'inspection_ohc_medicine_requisition_slip_floor_details.created_by as inspection_created_by',
            'inspection_ohc_medicine_requisition_slip_floor_details.created_at as inspection_created_at',
        )
        ->leftJoin('masters_unit', 'inspection_ohc_medicine_requisition_slip_floor_details.unit', '=', 'masters_unit.id')
        ->leftJoin('masters_department', 'inspection_ohc_medicine_requisition_slip_floor_details.department', '=', 'masters_department.id')
            ->leftJoin(
                'inspection_static_docno',
                'inspection_ohc_medicine_requisition_slip_floor_details.document_reference_id',
                '=',
                'inspection_static_docno.id'
            );
        $user = Auth::user();
        $userRole = string_to_array($user->role);
        // dd($query);
        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole) || in_array(ROLE_SAFETY_OFFICER, $userRole) || in_array(ROLE_MEDICAL_ASSISTANT, $userRole) || in_array(ROLE_FLOOR_MANAGER, $userRole)) {
            $query->orderBy('inspection_ohc_medicine_requisition_slip_floor_details.id', 'DESC');
        } else {
            $query->where('inspection_ohc_medicine_requisition_slip_floor_details.created_by', Auth::id());
        }


       if (isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_department.department_name', 'LIKE', '%' . $search . '%');
            });
        }


        if (isset($request->unit_id) && $request->unit_id) {
            $query = $query->where('inspection_ohc_medicine_requisition_slip_floor_details.unit', decryptId($request->unit_id));
        }
        if (isset($request->department_id) && $request->department_id) {
            $query = $query->where('inspection_ohc_medicine_requisition_slip_floor_details.department', decryptId($request->department_id));
        }
        if (isset($request->approve_status) && $request->approve_status) {
            $query = $query->where('inspection_ohc_medicine_requisition_slip_floor_details.approve_status',  decryptId($request->approve_status));
        }

        $user = Auth::user();
        $userRole = string_to_array($user->role);
        $empId = $user->employee_id;
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();



        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "rev_date":
                    $query->orderBy('inspection_ohc_medicine_requisition_slip_floor_details.revision_date', $columnorder);
                    break;
                case "issue_date":
                    $query = $query->orderBy('inspection_ohc_medicine_requisition_slip_floor_details.issue_date', $columnorder);
                    break;
                case "document_number":
                    $query = $query->orderBy('inspection_ohc_medicine_requisition_slip_floor_details.doc_no', $columnorder);
                    break;
                case "status":
                    $query = $query->orderBy('inspection_ohc_medicine_requisition_slip_floor_details.status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_ohc_medicine_requisition_slip_floor_details.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_ohc_medicine_requisition_slip_floor_details.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_ohc_medicine_requisition_slip_floor_details.id', 'DESC');
                    break;
            }
        }


        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('inspection_ohc_medicine_requisition_slip_floor_details.id', 'DESC');

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


            'department' => decryptId($request->department_id),
            'unit' => decryptId($request->unit_id),
            'date' => !empty($request->date) ? DBdateformat($request->date) : null,
            'next_due' => !empty($request->next_due_on) ? DBdateformat($request->next_due_on) : null,
            'document_reference_id' => decryptId($request->document_reference_id),
            'first_aid_box_no' => $request->first_aid_box_no,
            'first_aider' => $request->first_aider,
            'date_of_inspection' => !empty($request->date_of_inspection) ? DBdateformat($request->date_of_inspection) : null,
            'created_by' => Auth::id(),
            'approve_status' => FLOOR_MANAGER_APPROVAL_PENDING,
        ];

        return self::create($insert_array);
    }

    public function Selectone($id)
    {
        return $this->where('id', $id)->first();
    }

    public function safetyofficerapprovalupdate($id, $nextStatus)
    {
        return $this->where('id', $id)->update(['approved_by' => Auth::id(), 'approve_status' => $nextStatus]);
    }

    public function floormanagerapprovalupdate($id, $nextStatus)
    {
        return $this->where('id', $id)->update(['verified_by' => Auth::id(), 'approve_status' => $nextStatus]);
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select(
            'inspection_ohc_medicine_requisition_slip_floor_details.*',
          );
        $user = Auth::user();
        $userRole = string_to_array($user->role);
        // dd($query);
        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole) || in_array(ROLE_SAFETY_OFFICER, $userRole) || in_array(ROLE_MEDICAL_ASSISTANT, $userRole) || in_array(ROLE_FLOOR_MANAGER, $userRole)) {
            $query->orderBy('inspection_ohc_medicine_requisition_slip_floor_details.id', 'DESC');
        } else {
            $query->where('inspection_ohc_medicine_requisition_slip_floor_details.created_by', Auth::id());
        }


       if (isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_department.department_name', 'LIKE', '%' . $search . '%');
            });
        }

        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole) || in_array(ROLE_SAFETY_OFFICER, $userRole) || in_array(ROLE_MEDICAL_ASSISTANT, $userRole)) {
            $query->orderBy('inspection_ohc_medicine_requisition_slip_floor_details.id', 'DESC');
        } else {
            $query->where('inspection_ohc_medicine_requisition_slip_floor_details.created_by', Auth::id());
        }
        if (isset($request->unit_id) && $request->unit_id) {
            $query = $query->where('inspection_ohc_medicine_requisition_slip_floor_details.unit', decryptId($request->unit_id));
        }
        if (isset($request->department_id) && $request->department_id) {
            $query = $query->where('inspection_ohc_medicine_requisition_slip_floor_details.department', decryptId($request->department_id));
        }
        if (isset($request->approve_status) && $request->approve_status) {
            $query = $query->where('inspection_ohc_medicine_requisition_slip_floor_details.approve_status',  decryptId($request->approve_status));
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "rev_date":
                    $query->orderBy('inspection_ohc_medicine_requisition_slip_floor_details.revision_date', $columnorder);
                    break;
                case "issue_date":
                    $query = $query->orderBy('inspection_ohc_medicine_requisition_slip_floor_details.issue_date', $columnorder);
                    break;
                case "document_number":
                    $query = $query->orderBy('inspection_ohc_medicine_requisition_slip_floor_details.doc_no', $columnorder);
                    break;
                case "status":
                    $query = $query->orderBy('inspection_ohc_medicine_requisition_slip_floor_details.status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_ohc_medicine_requisition_slip_floor_details.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_ohc_medicine_requisition_slip_floor_details.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_ohc_medicine_requisition_slip_floor_details.id', 'DESC');
                    break;
            }
        }

        $query->orderBy('inspection_ohc_medicine_requisition_slip_floor_details.id', 'DESC');


        return   $query->get();
    }
}
