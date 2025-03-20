<?php

namespace App\Models\Inspection;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class GembaWalk extends Model
{
    use  HasFactory;
    protected $table = 'inspection_gemba_walk';

    protected $primaryKey = 'id';

    protected $fillable = [
        'gemba_walk_auto_id',
        'document_no',
        'issue_date',
        'revision_date',
        'status',
        'gemba_walk_status',
        'trash',
        'created_by',
        'updated_by',
        'updated_at',
        'created_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];

    public function list()
    {
        $request = request();

        $search = '';

        $query = $this->select('inspection_gemba_walk.*');


        $org_total =  $query;
        $org_total_counts = $org_total->count();
        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('masters_employee.emp_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_employee.emp_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_employee.email', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_employee.employee_status', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('emp_id') && $request->emp_id) {
            $query = $query->where('masters_employee.emp_id', 'LIKE', '%' . $request->emp_id . '%');
        }

        if ($request->has('emp_name') && $request->emp_name) {
            $query = $query->where('masters_employee.emp_name', 'LIKE', '%' . $request->emp_name . '%');
        }
        if ($request->has('email') && $request->email) {
            $query = $query->where('masters_employee.email', 'LIKE', '%' . $request->email . '%');
        }
        if ($request->has('employee_status') && $request->employee_status) {
            $query = $query->where('masters_employee.employee_status', 'LIKE', '%' . $request->employee_status . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('masters_employee.status', decryptId($request->status));
        }

        if ($request->has('company_id') && $request->company_id) {

            $query = $query->where('masters_employee.company', decryptId($request->company_id));
        }
        if ($request->has('unit_id') && $request->unit_id) {

            $query = $query->where('masters_employee.unit', $request->unit_id);
        }
        if ($request->has('department_id') && $request->department_id) {

            $query = $query->where('masters_employee.department', $request->department_id);
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
        $insert_array = array(
            'gemba_walk_auto_id' => $request->gemba_walk_id,
            'document_no' => $request->document_no,
            'issue_date' => DBdateformat($request->document_upload_date),
            'revision_date' => DBdateformat($request->document_revision_date),
            'gemba_walk_status'=>GEMBA_WALK_INSPECTION_WAITING_FOR_CAPA_ACTION,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }
    public function selectOne($id)
    {
        $data =  $this->select(
                'inspection_gemba_walk.id as gemba_walk_id',
                'inspection_gemba_walk.*',
                'inspection_gemba_walk_checklist.id as checklist_id',
                'inspection_gemba_walk_checklist.*',
                'inspection_gemba_walk_checklist_files.file_path'
            )
            ->leftJoin('inspection_gemba_walk_checklist', 'inspection_gemba_walk_checklist.gemba_walk_id', '=', 'inspection_gemba_walk.id')
            ->leftJoin('inspection_gemba_walk_checklist_files', 'inspection_gemba_walk_checklist_files.gemba_walk_checklist_id', '=', 'inspection_gemba_walk_checklist.id')
            ->where('inspection_gemba_walk.id', $id) 
            ->get();
    
        return $data;

    }

    public function selectMail($id)
    {
        return $this->where('inspection_gemba_walk.id', $id)->where('status',1)->first();
    }
    

    


    public function updateStatus($gembaWalk_id,$gembaWalk_status)
    {
        $request = request();
        // dd($request);

        $update_array = array(
            'gemba_walk_status' =>$gembaWalk_status ,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        );
        return $this->where('id', $gembaWalk_id)->update($update_array);
    }


    
}
