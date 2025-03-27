<?php

namespace App\Models\Inspection\audit;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class AuditAnalysisChecklist extends Model
{

    protected $table = 'inspection_audit_analysis_checklist';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'audit_analysis_id',
        'serial_number',
        'department_id',
        'unit_id',
        'marks',
        'no_of_audit',
        'total_marks',
        'marks_obtained',
        'percentage',
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
        $query = $this->select('inspection_audit_analysis_checklist.*');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('floor_name LIKE "%' . $search . '%"');
            });
        }

        // if (isset($request->category_name) && $request->category_name) {
        //     $query = $query->where('inspection_audit_analysis_checklist.category_name', 'LIKE', '%' . $request->category_name . '%');
        // }
        // if (isset($request->category_id) && $request->category_id) {
        //     $query = $query->where('inspection_audit_analysis_checklist.category_id', 'LIKE', '%' . $request->category_id . '%');
        // }
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

    public function store($msdsId)
    {
        $request = request();
        $data = [];

        foreach ($request->serial_number as $index => $serialNumber) {
            $marksPerMonth = [];

            $months = [
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December',
                'January',
                'February',
                'March'
            ];

            foreach ($months as $month) {
                $monthKey = strtolower($month);
                $marksPerMonth[$monthKey] = $request->input("marks_{$monthKey}.{$index}", 0); // Default to 0
            }

            $insert_array = [
                'audit_analysis_id' => $msdsId,
                'serial_number' => $serialNumber,
                'department_id' => $request->department_id[$index] ?? null, // Keep it as an integer
                'unit_id' => $request->unit_id[$index] ?? null, // Keep it as an integer
                'marks' => json_encode($marksPerMonth),
                'no_of_audit' => $request->no_of_audit[$index] ?? 0,
                'total_marks' => $request->total_marks[$index] ?? 0,
                'marks_obtained' => $request->marks_obtained[$index] ?? 0,
                'percentage' => $request->percentage[$index] ?? 0,
                'created_by' => Auth::id(),
            ];
            


            $data[] = $this->create($insert_array);
        }

        return $data;
    }


    public function selectOne($id)
    {
        return  $this->where('id', $id)->first();
    }


    public function exportdata()
    {
        $request = request();
        $search = '';

        $query = $this->select('inspection_audit_analysis_checklist.*');

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('category_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('category_id LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->category_name) && $request->category_name) {
            $query = $query->where('inspection_audit_analysis_checklist.category_name', 'LIKE', '%' . $request->category_name . '%');
        }
        if (isset($request->category_id) && $request->category_id) {
            $query = $query->where('inspection_audit_analysis_checklist.category_id', 'LIKE', '%' . $request->category_id . '%');
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "category_name":
                    $query->orderBy('inspection_audit_analysis_checklist.category_name', $columnorder);
                    break;
                case "category_id":
                    $query = $query->orderBy('inspection_audit_analysis_checklist.category_id', $columnorder);
                    break;
                case "status":
                    $query = $query->orderBy('inspection_audit_analysis_checklist.status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_audit_analysis_checklist.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_audit_analysis_checklist.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_audit_analysis_checklist.id', 'DESC');
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
        static::addGlobalScope(new TrashScope('inspection_audit_analysis_checklist'));
        static::created(function ($model) {

            $uniqueId = 'AUDIT-ASSESSMENT-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['audit_id' => $uniqueId]);
        });
    }
}
