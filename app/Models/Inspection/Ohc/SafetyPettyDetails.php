<?php

namespace App\Models\Inspection\Ohc;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use App\Scopes\TrashScope;

use Illuminate\Database\Eloquent\Model;

class SafetyPettyDetails extends Model
{
    use  HasFactory;

    protected $table = 'ohc_safety_petty_logbook_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'document_number',
        'issue_date',
        'revision_date',
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
        $query = $this->select('ohc_safety_petty_logbook_details.*');

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
            $query = $query->where('document_number', 'LIKE', '%' . $request->document_number . '%');
        }
        if ($request->has('issue_date') && $request->issue_date) {
            $query = $query->where('issue_date', 'LIKE', '%' . $request->issue_date . '%');
        }
        if ($request->has('revision_date') && $request->revision_date) {
            $query = $query->where('revision_date', 'LIKE', '%' . $request->revision_date . '%');
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('status', decryptId($request->status));
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

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('ohc_safety_petty_logbook_details.*');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhere('document_number', 'LIKE', '%' . $search . '%')
                    ->orWhere('issue_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('revision_date', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('document_number') && $request->document_number) {
            $query = $query->where('document_number', 'LIKE', '%' . $request->document_number . '%');
        }

        if ($request->has('issue_date') && $request->issue_date) {
            $query = $query->where('issue_date', 'LIKE', '%' . $request->issue_date . '%');
        }
        if ($request->has('revision_date') && $request->revision_date) {
            $query = $query->where('revision_date', 'LIKE', '%' . $request->revision_date . '%');
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');

        return $query->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ohc_safety_petty_logbook_details'));
    }

}
