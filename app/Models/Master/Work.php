<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Master\Location;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Log;

use App\Models\User;

class Work extends Model
{
    use  HasFactory;


    protected $table = 'masters_work';
    protected $primaryKey = 'id';

    protected $fillable = [
        'emp_id',
        'emp_name',
        'gender',
        'nationality',
        'biometric_code',
        'doi',
        'exit_date',
        'mobile_no',
        'company',
        'subdepartment',
        'unit',
        'department',
        'designation',
        'status',
        'wfemptype',
        'skill',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('masters_work.*', 'company_management.company_name', 'masters_department.department_name', 'masters_unit.unit_name');
        $query = $query->leftJoin('company_management', 'masters_work.company', '=', 'company_management.id');
        $query = $query->leftJoin('masters_department', 'masters_work.department', '=', 'masters_department.id');
        $query = $query->leftJoin('masters_unit', 'masters_work.unit', '=', 'masters_unit.id');

        $org_total =  $query;
        $org_total_counts = $org_total->count();
        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('masters_work.emp_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('emp_id') && $request->emp_id) {
            $query = $query->where('masters_work.emp_id', 'LIKE', '%' . $request->emp_id . '%');
        }

        if ($request->has('emp_name') && $request->emp_name) {
            $query = $query->where('masters_work.emp_name', 'LIKE', '%' . $request->emp_name . '%');
        }
        if ($request->has('company_id') && $request->company_id) {
            $query = $query->where('masters_work.company', decryptId($request->company_id));
        }
        if ($request->has('dept_id') && $request->dept_id) {
            $query = $query->where('masters_work.department', decryptId($request->dept_id));
        }

        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('masters_work.unit', decryptId($request->unit_id));
        }


        $data_count = $query->count();
        $total_records = $data_count;

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

    public function UniqueCheck($data)
    {

        return $this->where($data['param'],  $data['value'])->get();
    }

    public function ExistuniqueCheck($data)
    {
        return $this->where($data['param'],  $data['value'])
            ->where('id', '!=', decryptId($data['id']))
            ->get();
    }

    public function store($worktemp)
    {

        $insertArray = [];
        foreach ($worktemp as $item) {

            $companyExists = DB::table('company_management')
                ->where('company_name', $item->company)
                ->first();
            $unitExists = DB::table('masters_unit')
                ->where('unit_name', $item->unit)
                ->first();
            $departmentExists = DB::table('masters_department')
                ->where('department_name', $item->department)
                ->first();
            if (!$companyExists) {
                $errorMessage = "Company does not exist.";
                $this->updateErrorStatus($item->emp_id, $errorMessage);
            } elseif (!$unitExists) {
                $errorMessage = "Unit does not exist.";
                $this->updateErrorStatus($item->emp_id, $errorMessage);
            } elseif (!$departmentExists) {
                $errorMessage = "Department does not exist.";
                $this->updateErrorStatus($item->emp_id, $errorMessage);
            } else {

                $insertArray[] = [
                    'emp_id' => $item->emp_id ?? null,
                    'emp_name' => $item->emp_name ?? null,
                    'gender' => $item->gender ?? null,
                    'nationality' => $item->nationality ?? null,
                    'biometric_code' => $item->biometric_code ?? null,
                    'doi' => DBdatetimeformat($item->doi),
                    'exit_date' => DBdatetimeformat($item->exit_date),
                    'mobile_no' => $item->mobile_no ?? null,
                    'company' => $companyExists->id,
                    'subdepartment' => $item->subdepartment ?? null,
                    'unit' => $unitExists->id,
                    'department' => $departmentExists->id,
                    'designation' => $item->designation ?? null,
                    'wfemptype' => $item->wfemptype ?? null,
                    'skill' => $item->skill ?? null,
                    'status' => 1,
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                ];
            }
        }

        if (!empty($insertArray)) {
            $batchSize = 500;
            $chunks = array_chunk($insertArray, $batchSize);
            $insertedRecords = [];

            foreach ($chunks as $chunk) {
                $this->insert($chunk);
                $insertedRecords = array_merge($insertedRecords, $chunk);
            }

            return $insertedRecords;
        }
    }



    public function updateErrorStatus($emp_id, $errorMessage)
    {
        $update_data = [
            'error_status' => 1,
            'error_remarks' => $errorMessage, 
        ];

        return Worktemp::where('emp_id', $emp_id)->update($update_data);
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

    public function reporting_manager()
    {
        return $this->belongsTo(User::class, 'reporting_manager_id', 'id');
    }
    public function exportdata()
    {
        $request = request();

        $search = '';
        $query = $this->select('masters_work.*', 'company_management.company_name', 'masters_department.department_name', 'masters_unit.unit_name');
        $query = $query->leftJoin('company_management', 'masters_work.company', '=', 'company_management.id');
        $query = $query->leftJoin('masters_department', 'masters_work.department', '=', 'masters_department.id');
        $query = $query->leftJoin('masters_unit', 'masters_work.unit', '=', 'masters_unit.id');
        

        if (!empty($request->search)) {
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query->orWhere('masters_work.emp_name', 'LIKE', '%' . $search . '%');
            });
        }
        
        if ($request->has('emp_id') && $request->emp_id) {
            $query = $query->where('masters_work.emp_id', 'LIKE', '%' . $request->emp_id . '%');
        }

        if ($request->has('emp_name') && $request->emp_name) {
            $query = $query->where('masters_work.emp_name', 'LIKE', '%' . $request->emp_name . '%');
        }
        if ($request->has('company_id') && $request->company_id) {
            $query = $query->where('masters_work.company', decryptId($request->company_id));
        }
        if ($request->has('dept_id') && $request->dept_id) {
            $query = $query->where('masters_work.department', decryptId($request->dept_id));
        }

        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('masters_work.unit', decryptId($request->unit_id));
        }
    
        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select('masters_work.*', 'company_management.company_name', 'masters_department.department_name', 'masters_unit.unit_name')
            ->leftJoin('company_management', 'masters_work.company', '=', 'company_management.id')
            ->leftJoin('masters_department', 'masters_work.department', '=', 'masters_department.id')
            ->leftJoin('masters_unit', 'masters_work.unit', '=', 'masters_unit.id')
            ->where('masters_work.id', $id)
            ->first();
        return $data;
    }

    public function ajaxList($where, $whereIn = [])
    {

        $query = $this->select('login_id', 'emp_name');

        if (count($where) > 0) {
            $query = $query->where($where);
        }

        if (count($whereIn) > 0) {
            $query = $query->whereRaw('FIND_IN_SET(?, role_id)', [$whereIn]);
        }


        $datas = $query->get();

        $list = [];
        foreach ($datas as $data) {
            $listvalue = [];
            $listvalue['id'] = encryptId($data->login_id);
            $listvalue['name'] = $data->emp_name;

            $list[] = $listvalue;
        }

        return $list;
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('masters_work'));
    }
}
