<?php

namespace App\Models\Inspection\audit;

use App\Scopes\TrashScope;
use Carbon\Carbon;
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

        if ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_audit_analysis_checklist.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_audit_analysis_checklist.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_audit_analysis_checklist.created_at', [$startDate, $endDate]);
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


    public function store($auditanalysis_id)
    {
        $request = request();

        $audits = $request->input('audit');

        if (!empty($audits) && is_array($audits)) {
            foreach ($audits as $auditsData) {
                $marksPerMonth = [];

                // Month from the form (e.g., 'Sep')
                $inputMonth = $auditsData['month'];
                $mark = $auditsData['mark'] ?? 0;

                // Normalize month keys: April to March
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
                    $marksPerMonth[$monthKey] = (strcasecmp($inputMonth, substr($month, 0, 3)) === 0) ? $mark : 0;
                }

                $data = [
                    'audit_analysis_id' => $auditanalysis_id,
                    'serial_number'     => $auditsData['serial_number'],
                    'department_id'     => decryptId($auditsData['department_id']),
                    'unit_id'           => decryptId($auditsData['unit_id']),
                    'marks'             => json_encode($marksPerMonth),
                    'no_of_audit'       => $auditsData['no_of_audit'],
                    'total_marks'       => $auditsData['total_marks'],
                    'marks_obtained'    => $auditsData['marks_obtained'],
                    'percentage'        => $auditsData['percentage'],
                    'created_by'        => Auth::id(),
                ];
                $this->create($data);
            }
        }
    }

    public function store_api($auditanalysis_id)
    {
        $request = request();

        $audits = $request->input('audit');

        if (!empty($audits) && is_array($audits)) {
            foreach ($audits as $auditsData) {
                $marksPerMonth = [];

                // Month from the form (e.g., 'Sep')
                $inputMonth = $auditsData['month'];
                $mark = $auditsData['mark'] ?? 0;

                // Normalize month keys: April to March
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
                    $marksPerMonth[$monthKey] = (strcasecmp($inputMonth, substr($month, 0, 3)) === 0) ? $mark : 0;
                }

                $data = [
                    'audit_analysis_id' => $auditanalysis_id,
                    'serial_number'     => $auditsData['serial_number'],
                    'department_id'     => ($auditsData['department_id']),
                    'unit_id'           => ($auditsData['unit_id']),
                    'marks'             => json_encode($marksPerMonth),
                    'no_of_audit'       => $auditsData['no_of_audit'],
                    'total_marks'       => $auditsData['total_marks'],
                    'marks_obtained'    => $auditsData['marks_obtained'],
                    'percentage'        => $auditsData['percentage'],
                    'created_by'        => Auth::id(),
                ];
                $this->create($data);
            }
        }
    }

    public function selectOne($auditId)
    {
        $data = $this->select('inspection_audit_analysis_checklist.*', 'masters_department.department_name', 'masters_unit.unit_name', 'inspection_audit_analysis.audit_analysis_id')
            ->leftJoin('masters_department', 'inspection_audit_analysis_checklist.department_id', '=', 'masters_department.id')->leftJoin('inspection_audit_analysis', 'inspection_audit_analysis_checklist.audit_analysis_id', '=', 'inspection_audit_analysis.id')
            ->leftJoin('masters_unit', 'inspection_audit_analysis_checklist.unit_id', '=', 'masters_unit.id')->where('inspection_audit_analysis_checklist.audit_analysis_id', $auditId)
            ->get();
        return $data;
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
        if ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_audit_analysis_checklist.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_audit_analysis_checklist.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_audit_analysis_checklist.created_at', [$startDate, $endDate]);
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

    public function statuschange($auditId)
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

        return $this->where('audit_analysis_id', $auditId)->update($update_data);
    }


    public function getUniqueSchedule($departmentId, $unitId, $year, $month)
    {
        $conflicts = [];

        $exists = self::where('department_id', $departmentId)
            ->where('unit_id', $unitId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->exists();

        if ($exists) {
            $conflicts['exists'] = 'This department and unit already have a record for the selected year and month.';
        }

        return $conflicts;
    }

    public function getExistUniqueSchedule($departmentId, $unitId, $year, $month, $excludeId)
    {
        $conflicts = [];

        $exists = self::where('department_id', $departmentId)
            ->where('unit_id', $unitId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('id', '!=', $excludeId)
            ->exists();

        if ($exists) {
            $conflicts['exists'] = 'Another record with this department, unit, year and month already exists.';
        }

        return $conflicts;
    }

    public function getTotalRecords()
    {
        $request = request();

        $query = $this->where('status', 1);

        // if ($request->has('CompanyId') && $request->CompanyId) {
        //     $query->where('inspection_audit_analysis_checklist.company_id', decryptId($request->CompanyId));
        // }

        if ($request->has('Fromdate') && $request->Fromdate) {
            $query->where('inspection_audit_analysis_checklist.created_at', '>=', DBdateformat($request->Fromdate));
        }

        if ($request->has('Todate') && $request->Todate) {
            $query->where('inspection_audit_analysis_checklist.created_at', '<=', DBdateformat($request->Todate));
        }

        return $query->count();
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_audit_analysis_checklist'));
    }
}
