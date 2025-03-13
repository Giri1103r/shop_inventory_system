<?php

namespace App\Models\Inspection\audit;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class AuditAssessment extends Model
{

    protected $table = 'inspection_audit';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'audit_id',
        'floor_name',
        'audit_date',
        'shift',
        'floor_executive',
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
        $query = $this->select('inspection_audit.*');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('floor_name LIKE "%' . $search . '%"');
            });
        }

        // if (isset($request->category_name) && $request->category_name) {
        //     $query = $query->where('inspection_audit.category_name', 'LIKE', '%' . $request->category_name . '%');
        // }
        // if (isset($request->category_id) && $request->category_id) {
        //     $query = $query->where('inspection_audit.category_id', 'LIKE', '%' . $request->category_id . '%');
        // }


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
            'category_name' => $request->checklist_category,
            'questionary' => decryptId($request->questionary_id),
            'created_by' => Auth::id(),
        ];
        return self::create($insert_array);
    }

    public function selectOne($id)
    {
        return $this->where('id', $id)->first();
    }



    public function exportdata()
    {
        $request = request();
        $search = '';

        $query = $this->select('inspection_audit.*');

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('category_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('category_id LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->category_name) && $request->category_name) {
            $query = $query->where('inspection_audit.category_name', 'LIKE', '%' . $request->category_name . '%');
        }
        if (isset($request->category_id) && $request->category_id) {
            $query = $query->where('inspection_audit.category_id', 'LIKE', '%' . $request->category_id . '%');
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "category_name":
                    $query->orderBy('inspection_audit.category_name', $columnorder);
                    break;
                case "category_id":
                    $query = $query->orderBy('inspection_audit.category_id', $columnorder);
                    break;
                case "status":
                    $query = $query->orderBy('inspection_audit.status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_audit.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_audit.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_audit.id', 'DESC');
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
        static::addGlobalScope(new TrashScope('inspection_audit'));
        static::created(function ($model) {

            $uniqueId = 'CAT-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['category_id' => $uniqueId]);
        });
    }
}
