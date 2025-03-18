<?php

namespace App\Models\Inspection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use App\Scopes\TrashScope;

class MSDSDetails extends Model
{

    use  HasFactory;

    protected $table = 'inspection_msds_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'document_number',
        'issue_date',
        'revision_date',
        'inspection_status',
        'remarks',
        'capa_recommendation',
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
        $query = $this->select('inspection_msds_details.*');
      
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
            'issue_date' => $request->issue_date,
            'revision_date' => todaydate('todaydate'),
            'created_by' => Auth::id()
        );
      
        return $this->create($insert_array);
    }

    public function updates($id)
    { 
        $request = request();
       
        $update_data = array(
            'document_number' => $request->document_number,
            'issue_date' => $request->issue_date,
            'updated_by' => Auth::id()
        );
       
        $result = $this->where('id', $id)->update($update_data);
        return $result;
    }

    public function selectOne($id)
    {
        return $this->where('id', $id)->first();
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
        $query = $this->select('inspection_msds_details.*');
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

        return  $query->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_msds_details'));
    }
}
