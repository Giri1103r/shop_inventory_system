<?php

namespace App\Models\Inspection\GembaWalk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Scopes\TrashScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Builder\Function_;
use PhpParser\Node\Expr\FuncCall;

class GembaWalk extends Model
{
    use  HasFactory;
    protected $table = 'inspection_gemba_walk';

    protected $primaryKey = 'id';

    protected $fillable = [
        'gemba_walk_auto_id',
        'document_reference_id',
        'company_id',
        'date',
        'shift_id',
        'gemba_walk_status',
        'observation_needed',
        'capa_needed',
        'responsible_person_id',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'verified_by',
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
        $query = $this->select('inspection_gemba_walk.*', 'inspection_gemba_walk_status.status_name', 'inspection_gemba_walk_status.bg_color', 'inspection_shift_option.shift', 'inspection_gemba_walk.id as gemba_walk_id')
            ->leftJoin('inspection_shift_option', 'inspection_shift_option.id', '=', 'inspection_gemba_walk.shift_id')
            ->leftJoin('inspection_gemba_walk_status', 'inspection_gemba_walk_status.id', '=', 'inspection_gemba_walk.gemba_walk_status');

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_OFFICER)) {
        } else if (CheckUserRole(ROLE_FLOOR_MANAGER)) {
            $query->where('inspection_gemba_walk.responsible_person_id', Auth::id());
        }

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_gemba_walk.gemba_walk_auto_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_shift_option.shift', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_gemba_walk.date', 'LIKE', '%' . DBdateformat($search) . '%');
            });
        }

        if ($request->has('doc_no') && $request->doc_no) {
            $query = $query->where('inspection_gemba_walk.gemba_walk_auto_id', 'LIKE', '%' . $request->doc_no . '%');
        }

        if ($request->has('date') && $request->date) {

            $formattedDate = DBdateformat($request->date);
            $query = $query->whereDate('inspection_gemba_walk.date', $formattedDate);
        }

        if ($request->has('inspection_status') && $request->inspection_status) {
            $query = $query->where('inspection_gemba_walk.gemba_walk_status',  decryptId($request->inspection_status));
        }

        if ($request->has('shift') && $request->shift) {
            $query = $query->where('inspection_gemba_walk.shift_id',  decryptId($request->shift));
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_gemba_walk.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_gemba_walk.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_gemba_walk.created_at', [$startDate, $endDate]);
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
        $query = $this->select('inspection_gemba_walk.*', 'inspection_gemba_walk_status.status_name', 'inspection_gemba_walk_status.bg_color', 'inspection_shift_option.shift', 'inspection_gemba_walk.id as gemba_walk_id')
            ->leftJoin('inspection_shift_option', 'inspection_shift_option.id', '=', 'inspection_gemba_walk.shift_id')
            ->leftJoin('inspection_gemba_walk_status', 'inspection_gemba_walk_status.id', '=', 'inspection_gemba_walk.gemba_walk_status');

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_OFFICER)) {
        } else if (CheckUserRole(ROLE_FLOOR_MANAGER)) {
            $query->where('inspection_gemba_walk.responsible_person_id', Auth::id());
        }

        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_gemba_walk.gemba_walk_auto_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_shift_option.shift', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_gemba_walk.date', 'LIKE', '%' . DBdateformat($search) . '%');
            });
        }


        $data = $query->orderby('inspection_gemba_walk.id')->get();
        $inspection_data = $data->toArray();
        $data_array = [];
        $refined_data = [];
        foreach ($inspection_data as $index => $listdata) {
            $data_array['id'] = $listdata['id'];
            $data_array['gemba_walk_auto_id'] = $listdata['gemba_walk_auto_id'];
            $data_array['date'] = $listdata['date'];
            $data_array['shift_name'] = getShift($listdata['shift_id']);
            $data_array['status'] = $listdata['status_name'];
            $data_array['created_at'] = Displaydateformat($listdata['created_at']);
            $data_array['created_by'] = getUsername($listdata['created_by']);
            $refined_data[$index] = $data_array;
        }
        return $refined_data;
    }

    public function store()
    {
        $request = request();
        $capa_needed  = decryptId($request->is_passed);

        if ($capa_needed == 2) {

            $insert_array = array(
                'document_reference_id' => $request->document_reference_id,
                'date' => DBdateformat($request->document_upload_date),
                'shift_id' => decryptId($request->shift),
                'company_id' => Auth::user()->company_id,
                'observation_needed' => decryptId($request->observation_needed),
                'capa_needed' => decryptId($request->is_passed),
                'responsible_person_id' => decryptId($request->responsible_person_id),
                'gemba_walk_status' => GEMBA_WALK_INSPECTION_WAITING_FOR_FLOOR_MANAGER_VERIFICATION,
                'created_by' => Auth::id(),
            );
        } else {
            $insert_array = array(
                'document_reference_id' => $request->document_reference_id,
                'date' => DBdateformat($request->document_upload_date),
                'shift_id' => decryptId($request->shift),
                'company_id' => Auth::user()->company_id,
                'observation_needed' => decryptId($request->observation_needed),
                'capa_needed' => decryptId($request->is_passed),
                'responsible_person_id' => decryptId($request->responsible_person_id),
                'gemba_walk_status' => GEMBA_WALK_INSPECTION_CLOSED,
                'created_by' => Auth::id(),
                'verified_by' => Auth::id()
            );
        }


        return $this->create($insert_array);
    }


    public function storeApi(){
        $request = request();

        if ($request->is_passed == 2) {

            $insert_array = array(
                'document_reference_id' => $request->document_reference_id,
                'date' => DBdateformat($request->document_upload_date),
                'shift_id' => $request->shift,
                'company_id' => Auth::user()->company_id,
                'observation_needed' => $request->observation_needed,
                'capa_needed' => $request->is_passed,
                'responsible_person_id' => $request->responsible_person_id,
                'gemba_walk_status' => GEMBA_WALK_INSPECTION_WAITING_FOR_FLOOR_MANAGER_VERIFICATION,
                'created_by' => Auth::id(),
            );
        } else {
            $insert_array = array(
                'document_reference_id' => $request->document_reference_id,
                'date' => DBdateformat($request->document_upload_date),
                'shift_id' => $request->shift,
                'company_id' => Auth::user()->company_id,
                'observation_needed' => $request->observation_needed,
                'capa_needed' => $request->is_passed,
                'responsible_person_id' => $request->responsible_person_id,
                'gemba_walk_status' => GEMBA_WALK_INSPECTION_CLOSED,
                'created_by' => Auth::id(),
                'verified_by' => Auth::id()
            );
        }


                return $this->create($insert_array);

    }

    public function selectOne($id)
    {
        $data =  $this->select(
            'inspection_gemba_walk.id as gemba_walk_id',
            'inspection_gemba_walk.*',
            'inspection_gemba_walk_checklist.id as checklist_id',
            'inspection_gemba_walk_checklist.*',
            'inspection_gemba_walk_checklist_files.file_path',
            'inspection_gemba_walk.created_by as gemba_walk_created_by',

        )
            ->leftJoin('inspection_gemba_walk_checklist', 'inspection_gemba_walk_checklist.gemba_walk_id', '=', 'inspection_gemba_walk.id')
            ->leftJoin('inspection_gemba_walk_checklist_files', function ($join) {
                $join->on('inspection_gemba_walk_checklist_files.gemba_walk_checklist_id', '=', 'inspection_gemba_walk_checklist.id')
                    ->where('inspection_gemba_walk_checklist_files.file_type', '=', 3);
            })
            ->where('inspection_gemba_walk.id', $id)
            ->get();

        return $data;
    }

    public function getUserId($id)
    {
        return $this->where('id', $id)->first();
    }

    public function selectSingnature($id)
    {
        $data =  $this->select(
            'inspection_gemba_walk.*',
            'inspection_gemba_walk_checklist_files.file_path as signature'
        )
            ->leftJoin('inspection_gemba_walk_checklist_files', 'inspection_gemba_walk_checklist_files.gemba_walk_id', '=', 'inspection_gemba_walk.id')
            ->where('file_type', 1)
            ->where('inspection_gemba_walk.id', $id)
            ->get();

        return $data;
    }

    public function selectVerifiedSingnature($id)
    {
        $data =  $this->select(
            'inspection_gemba_walk.*',
            'inspection_gemba_walk_checklist_files.file_path as signature'
        )

            ->leftJoin('inspection_gemba_walk_checklist_files', 'inspection_gemba_walk_checklist_files.gemba_walk_id', '=', 'inspection_gemba_walk.id')
            ->where('file_type', 2)
            ->where('inspection_gemba_walk.id', $id)
            ->get();

        return $data;
    }



    public function selectMail($id)
    {
        return $this->where('inspection_gemba_walk.id', $id)->where('status', 1)->first();
    }

    public function updateStatus($gembaWalk_id, $gembaWalk_status)
    {
        $request = request();
        $update_array = array(
            'gemba_walk_status' => $gembaWalk_status,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        );
        return $this->where('id', $gembaWalk_id)->update($update_array);
    }

    public function updateAprrovel($gembaWalk_id)
    {
        $request = request();
        $update_array = array(
            'verified_by' => Auth::id(),
            'updated_at' => now(),
        );
        return $this->where('id', $gembaWalk_id)->update($update_array);
    }

    public function exportdata()
    {
        $request = request();
        $search = '';

        $query = $this->select(
            'inspection_gemba_walk.*',
            'inspection_gemba_walk.id as inspection_id',
            'inspection_gemba_walk.created_by as inspection_created_by',
            'inspection_gemba_walk.updated_by as verified_by',

            'inspection_gemba_walk_status.status_name',
            'inspection_gemba_walk_status.bg_color',
            'inspection_shift_option.shift',
            'inspection_gemba_walk_checklist.*',
            'inspection_static_docno.*',
            'inspection_gemba_walk_checklist_files.file_path'
        )
            ->leftJoin('inspection_gemba_walk_checklist', 'inspection_gemba_walk_checklist.gemba_walk_id', '=', 'inspection_gemba_walk.id')
            ->leftJoin('inspection_gemba_walk_checklist_files', function ($join) {
                $join->on('inspection_gemba_walk_checklist_files.gemba_walk_checklist_id', '=', 'inspection_gemba_walk_checklist.id')
                    ->where('inspection_gemba_walk_checklist_files.file_type', '=', 3);
            })
            ->leftJoin('inspection_shift_option', 'inspection_shift_option.id', '=', 'inspection_gemba_walk.shift_id')
            ->leftJoin('inspection_gemba_walk_status', 'inspection_gemba_walk_status.id', '=', 'inspection_gemba_walk.gemba_walk_status')
            ->leftJoin('inspection_static_docno', 'inspection_gemba_walk.document_reference_id', '=', 'inspection_static_docno.id');

        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_gemba_walk.gemba_walk_auto_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_shift_option.shift', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_gemba_walk.date', 'LIKE', '%' . DBdateformat($search) . '%');
            });
        }
        if ($request->has('gemba_walk_auto_id') && $request->gemba_walk_auto_id) {
            $query = $query->where('inspection_gemba_walk.gemba_walk_auto_id', 'LIKE', '%' . $request->gemba_walk_auto_id . '%');
        }

        if ($request->has('date') && $request->date) {

            $formattedDate = DBdateformat($request->date);
            $query = $query->whereDate('inspection_gemba_walk.date', $formattedDate);
        }

        if ($request->has('inspection_status') && $request->inspection_status) {
            $query = $query->where('inspection_gemba_walk.gemba_walk_status',  decryptId($request->inspection_status));
        }

        if ($request->has('shift') && $request->shift) {
            $query = $query->where('inspection_gemba_walk.shift_id',  decryptId($request->shift));
        }

        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_gemba_walk.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_gemba_walk.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_gemba_walk.created_at', [$startDate, $endDate]);
        }
        $query->orderBy('inspection_gemba_walk.id', 'DESC');
        $results = $query->get();
        $query = $results->groupBy('gemba_walk_id');

        return  $query;
    }

    public function getResponsiblePerson($id)
    {
        $data = $this->where('inspection_gemba_walk.id', $id)->where('status', 1)->first();
        if ($data != null) {
            return $data->responsible_person_id;
        }

        return false;
    }

    public function getInspectionDetails($id)
    {
        $data = $this->select('inspection_gemba_walk.*','inspection_static_docno.doc_no','inspection_static_docno.issue_date','inspection_static_docno.rev_dt')
                     ->leftjoin('inspection_static_docno','inspection_static_docno.id','=','inspection_gemba_walk.document_reference_id')
                     ->where('inspection_gemba_walk.id',$id)
                     ->first();
        return $data;
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_gemba_walk'));
        static::created(function ($model) {

            $uniqueId = 'GMB-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['gemba_walk_auto_id' => $uniqueId]);
        });
    }
}
