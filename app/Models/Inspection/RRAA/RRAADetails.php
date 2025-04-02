<?php

namespace App\Models\Inspection\RRAA;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\TrashScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class RRAADetails extends Model
{
    use  HasFactory;

    protected $table = 'inspection_rraa_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'document_number',
        'issue_date',
        'revision_date',
        'inspection_status',
        'remarks',
        'capa_recomendation',
        'capa_remarks',
        'capa_ehs_remarks',
        'level_one_manager_remarks',
        'level_two_manager_remarks',
        'verified_by',
        'approved_by',
        'l1_manager_verified_by',
        'l2_manager_verified_by',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_rraa_details.*');

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('document_number', 'LIKE', '%' . $search . '%')
                    ->orWhere('issue_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('revision_date', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('document_number') && $request->document_number) {
            $query = $query->where('inspection_rraa_details.document_number', 'LIKE', '%' . $request->document_number . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->whereDate('issue_date', '=', DBdateformat($request->issue_date));
        }
        if ($request->has('revision_date') && $request->revision_date) {
            $query = $query->where('inspection_rraa_details.revision_date', 'LIKE', '%' . $request->revision_date . '%');
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
            'document_number' => $request->document_number,
            'issue_date' => DBdateformat($request->issue_date),
            'revision_date' => $request->revision_date,
            'created_by' => Auth::id(),
        );

        return $this->create($insert_array);
    }

    public function selectOne($id)
    {
        return $this->where('id', $id)->first();
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_rraa_details.*');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhere('document_number', 'LIKE', '%' . $search . '%')
                    ->orWhere('issue_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('revision_date', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('document_number') && $request->document_number) {
            $query = $query->where('inspection_rraa_details.document_number', 'LIKE', '%' . $request->document_number . '%');
        }
        if (isset($request->issue_date) && $request->issue_date) {
            $query = $query->whereDate('issue_date', '=', DBdateformat($request->issue_date));
        }
        if ($request->has('revision_date') && $request->revision_date) {
            $query = $query->where('inspection_rraa_details.revision_date', 'LIKE', '%' . $request->revision_date . '%');
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_rraa_details'));
    }
}
