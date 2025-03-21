<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Employee;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
class MedicineRequistionSlipfdodetails extends Model
{
    protected $table = 'inspection_ohc_medicine_requisition_slip_fdo_details';

    protected $primaryKey = 'id';


    protected $fillable = [
        'doc_no',
        'issue_date',
        'revision_date',
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
        'l1_manager_verification',
        'l2_manager_verification',
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
        $query = $this->select('inspection_ohc_medicine_requisition_slip_fdo_details.*');
        $user = Auth::user();
        $userRole = string_to_array($user->role);
        $empId = $user->employee_id;
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('doc_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('revision_date', 'LIKE', '%' . $search . '%');
            });
        }

        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)|| in_array(ROLE_SAFETY_OFFICER, $userRole) || in_array(ROLE_MEDICIAL_ASSISITANT, $userRole)) {
            $query->orderBy('inspection_ohc_medicine_requisition_slip_fdo_details.id', 'DESC');
        } else {
            $query->where('inspection_ohc_medicine_requisition_slip_fdo_details.created_by', Auth::id());
        }
        if (isset($request->document_number) && $request->document_number) {
            $query = $query->where('inspection_ohc_medicine_requisition_slip_fdo_details.doc_no', 'LIKE', '%' . $request->document_number . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->where('inspection_ohc_medicine_requisition_slip_fdo_details.issue_date', 'LIKE', '%' . DBdateformat($request->issue_date) . '%');
        }
        if (isset($request->rev_date) && $request->rev_date) {
            $query = $query->where('inspection_ohc_medicine_requisition_slip_fdo_details.revision_date', 'LIKE', '%' . $request->rev_date . '%');
        }
        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_ohc_medicine_requisition_slip_fdo_details.approve_status', 'LIKE', '%' . decryptId($request->status) . '%');
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
            'doc_no' => $request->document_no,
            'issue_date' => DBdateformat($request->issue_date),
            'shift' => decryptId($request->shift),
            'location' => decryptId($request->location_id),
            'department' => decryptId($request->department_id),
            'unit' => decryptId($request->unit_id),
            'date' => !empty($request->date) ? DBdateformat($request->date) : null,
            'next_due' => !empty($request->next_due_on) ? DBdateformat($request->next_due_on) : null,
            'revision_date' => $request->review_date,
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
    public function safetyofficerapprovalupdate($id,$nextStatus){
        return $this->where('id',$id)->update(['approved_by'=>Auth::id(),'approve_status'=>$nextStatus]);
    }
    public function exportdata()
    {
        $search = '';
        $request = Request();
        $query = $this->select('inspection_ohc_medicine_requisition_slip_fdo_details.*');
        $user = Auth::user();
        $userRole = string_to_array($user->role);
        $empId = $user->employee_id;

        if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)|| in_array(ROLE_SAFETY_OFFICER, $userRole) || in_array(ROLE_MEDICIAL_ASSISITANT, $userRole)) {
            $query->orderBy('inspection_ohc_medicine_requisition_slip_fdo_details.id', 'DESC');
        } else {
            $query->where('inspection_ohc_medicine_requisition_slip_fdo_details.created_by', Auth::id());
        }
        if (isset($request->document_number) && $request->document_number) {
            $query = $query->where('inspection_ohc_medicine_requisition_slip_fdo_details.doc_no', 'LIKE', '%' . $request->document_number . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->where('inspection_ohc_medicine_requisition_slip_fdo_details.issue_date', 'LIKE', '%' . DBdateformat($request->issue_date) . '%');
        }
        if (isset($request->rev_date) && $request->rev_date) {
            $query = $query->where('inspection_ohc_medicine_requisition_slip_fdo_details.revision_date', 'LIKE', '%' . $request->rev_date . '%');
        }
        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_ohc_medicine_requisition_slip_fdo_details.approve_status', 'LIKE', '%' . decryptId($request->status) . '%');
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


        return $query->orderBy('id', 'DESC')->get(); // Add `get()` here
    }


}
