<?php

namespace App\Models\Inspection\audit;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class AuditAssessment extends Model
{

    protected $table = 'inspection_audit_assessment';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'audit_id',
        'floor_name',
        'audit_date',
        'shift_id',
        'floor_executive',
        'checklist',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_audit_assessment.*', 'masters_employee.emp_name')->leftjoin('masters_employee', 'masters_employee.id', '=', 'inspection_audit_assessment.floor_executive');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('audit_id LIKE "%' . $search . '%"')
                ->orWhereRaw('floor_name LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->audit_id) && $request->audit_id) {
            $query = $query->where('inspection_audit_assessment.audit_id', 'LIKE', '%' . $request->audit_id . '%');
        }
        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_audit_assessment.status', decryptId($request->status));
        }
        $query->orderBy('id', 'desc');

        $data_count = $query;
        $total_records = $data_count->count();

        if (isset($request->length) && $request->length != -1) {
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
            'floor_name' => $request->floor_name,
            'audit_date' => DBdateformat($request->audit_date),
            'shift_id' => decryptId($request->shift_id),
            'floor_executive' => decryptId($request->floor_executive),
            'checklist' => json_encode($request->checklist, true),
            'created_by' => Auth::id(),
        ];
        return self::create($insert_array);
    }

    public function selectOne($id)
    {
        return  $this->where('id', $id)->first();
    }


    public function exportdata()
    {
        $request = request();
        $search = '';

        $query = $this->select('inspection_audit_assessment.*');

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('audit_id LIKE "%' . $search . '%"')
                ->orWhereRaw('floor_name LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->audit_id) && $request->audit_id) {
           
            $query = $query->where('inspection_audit_assessment.audit_id', $request->audit_id );
        }
        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_audit_assessment.status', decryptId($request->status));
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "category_name":
                    $query->orderBy('inspection_audit_assessment.category_name', $columnorder);
                    break;
                case "category_id":
                    $query = $query->orderBy('inspection_audit_assessment.category_id', $columnorder);
                    break;
                case "status":
                    $query = $query->orderBy('inspection_audit_assessment.status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_audit_assessment.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_audit_assessment.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_audit_assessment.id', 'DESC');
                    break;
            }
        }

        return $query->orderBy('id', 'desc')->get();
    }


    public function UniqueCheck($data)
    {
        $unique =  $this->where('category_name',  $data)->get();
        if (count($unique) > 0) {
            return false;
        }
        return true;
    }

    public function ExistuniqueCheck($data)
    {
        $unique =  $this->where('category_name',  $data['category_name'])
            ->where('id', '!=', decryptId($data['id']))
            ->get();
        if (count($unique) > 0) {
            return false;
        }
        return true;
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

    public function deleterecord($id)
    {

        $update_data = array(
            'status' => 0,
            'trash' => 'YES',
        );

        return $this->where('id', $id)->update($update_data);
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_audit_assessment'));
        static::created(function ($model) {

            $uniqueId = 'AUDIT-ASSESSMENT-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['audit_id' => $uniqueId]);
        });
    }
}
