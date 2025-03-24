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
        $query = $this->select('inspection_gemba_walk.*', 'inspection_gemba_walk_status.status_name', 'inspection_gemba_walk_status.bg_color')
            ->leftJoin('inspection_gemba_walk_status', 'inspection_gemba_walk_status.id', '=', 'inspection_gemba_walk.gemba_walk_status');
        $org_total =  $query;
        $org_total_counts = $org_total->count();
        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_gemba_walk.document_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_gemba_walk.issue_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_gemba_walk.revision_date', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('doc_no') && $request->doc_no) {
            $query = $query->where('inspection_gemba_walk.document_no', 'LIKE', '%' . $request->doc_no . '%');
        }
        if ($request->has('issue_date') && $request->issue_date) {

            $formattedDate = DBdateformat($request->issue_date);
            $query = $query->whereDate('inspection_gemba_walk.issue_date', $formattedDate);
        }

        if ($request->has('revision_date') && $request->revision_date) {

            $formattedDate = DBdateformat($request->revision_date);
            $query = $query->whereDate('inspection_gemba_walk.revision_date', $formattedDate);
        }

        if ($request->has('inspection_status') && $request->inspection_status) {

            $query = $query->where('inspection_gemba_walk.gemba_walk_status',  decryptId($request->inspection_status));
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
            'gemba_walk_status' => GEMBA_WALK_INSPECTION_WAITING_FOR_CAPA_ACTION,
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
        // dd($data);

        return $data;
    }



    public function selectMail($id)
    {
        return $this->where('inspection_gemba_walk.id', $id)->where('status', 1)->first();
    }





    public function updateStatus($gembaWalk_id, $gembaWalk_status)
    {
        $request = request();
        // dd($request);

        $update_array = array(
            'gemba_walk_status' => $gembaWalk_status,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        );
        return $this->where('id', $gembaWalk_id)->update($update_array);
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_gemba_walk.*', 'inspection_gemba_walk_status.status_name', 'inspection_gemba_walk_status.bg_color')
        ->leftJoin('inspection_gemba_walk_status', 'inspection_gemba_walk_status.id', '=', 'inspection_gemba_walk.gemba_walk_status');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_gemba_walk.document_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_gemba_walk.issue_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_gemba_walk.revision_date', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('doc_no') && $request->doc_no) {
            $query = $query->where('inspection_gemba_walk.document_no', 'LIKE', '%' . $request->doc_no . '%');
        }
        if ($request->has('issue_date') && $request->issue_date) {

            $formattedDate = DBdateformat($request->issue_date);
            $query = $query->whereDate('inspection_gemba_walk.issue_date', $formattedDate);
        }

        if ($request->has('revision_date') && $request->revision_date) {

            $formattedDate = DBdateformat($request->revision_date);
            $query = $query->whereDate('inspection_gemba_walk.revision_date', $formattedDate);
        }

        if ($request->has('inspection_status') && $request->inspection_status) {

            $query = $query->where('inspection_gemba_walk.gemba_walk_status',  decryptId($request->inspection_status));
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
    }
}
