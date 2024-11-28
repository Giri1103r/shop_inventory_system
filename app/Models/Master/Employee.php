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

class Employee extends Model
{
    use  HasFactory;


    protected $table = 'masters_employee';
    protected $primaryKey = 'id';

    protected $fillable = [
        'emp_id',
        'emp_name',
        'gender',
        'nationality',
        'user_role',
        'id_type',
        'id_number',
        'joining_date',
        'mobile_no',
        'company',
        'location',
        'email',
        'unit',
        'department',
        'designation',
        'employee_status',
        'reporting_manager',
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
        $query = $this->select('masters_employee.*', 'company_management.company_name', 'masters_department.department_name', 'masters_unit.unit_name');
        $query = $query->leftJoin('company_management', 'masters_employee.company', '=', 'company_management.id');
        $query = $query->leftJoin('masters_department', 'masters_employee.department', '=', 'masters_department.id');
        $query = $query->leftJoin('masters_unit', 'masters_employee.unit', '=', 'masters_unit.id');

        $org_total =  $query;
        $org_total_counts = $org_total->count();
        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('masters_employee.emp_id', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('emp_id') && $request->emp_id) {
            $query = $query->where('masters_employee.emp_id', 'LIKE', '%' . $request->emp_id . '%');
        }

        if ($request->has('emp_name') && $request->emp_name) {
            $query = $query->where('masters_employee.emp_name', 'LIKE', '%' . $request->emp_name . '%');
        }
        if ($request->has('email') && $request->email) {
            $query = $query->where('masters_employee.email', 'LIKE', '%' . $request->email . '%');
        }
        if ($request->has('employee_status') && $request->employee_status) {
            $query = $query->where('masters_employee.employee_status', 'LIKE', '%' . $request->employee_status . '%');
        }
      
        // if ($request->has('status') && $request->status) {

        //     $query = $query->where('masters_employee.status', decryptId($request->status));
        // }
        // if ($request->has('company_id') && $request->company_id) {
        //     $query = $query->where('masters_employee.company', decryptId($request->company_id));
        // }
        // if ($request->has('dept_id') && $request->dept_id) {
        //     $query = $query->where('masters_employee.department', decryptId($request->dept_id));
        // }

        // if ($request->has('unit_id') && $request->unit_id) {
        //     $query = $query->where('masters_employee.unit', decryptId($request->unit_id));
        // }


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

    // public function store($emptemp)
    // {
    //     dd($emptemp);
    //     $insertArray = [];
    //     foreach ($emptemp as $item) {

    //         $companyExists = DB::table('company_management')
    //             ->where('company_name', $item->company)
    //             ->first();
    //         $locationExists = DB::table('masters_location')
    //             ->where('location_name', $item->location)
    //             ->first();
    //         $unitExists = DB::table('masters_unit')
    //             ->where('unit_name', $item->unit)
    //             ->first();
    //         $departmentExists = DB::table('masters_department')
    //             ->where('department_name', $item->department)
    //             ->first();
    //         if (!$companyExists) {
    //             $errorMessage = "Company does not exist.";
    //             $this->updateErrorStatus($item->emp_id, $errorMessage);
    //         } elseif (!$unitExists) {
    //             $errorMessage = "Unit does not exist.";
    //             $this->updateErrorStatus($item->emp_id, $errorMessage);
    //         } elseif (!$departmentExists) {
    //             $errorMessage = "Department does not exist.";
    //             $this->updateErrorStatus($item->emp_id, $errorMessage);
    //         } else {

    //             $insertArray[] = [
    //                 'emp_id' => $item->emp_id ?? null,
    //                 'emp_name' => $item->emp_name ?? null,
    //                 'gender' => $item->gender ?? null,
    //                 'nationality' => $item->nationality ?? null,
    //                 'email' => $item->email ?? null,
    //                 'joining_date' => DBdatetimeformat($item->joining_date),
    //                 'mobile_no' => $item->mobile_no ?? null,
    //                 'user_role' => $item->user_role ?? null,
    //                 'company' => $companyExists->id,
    //                 'location_id' => $locationExists->id,
    //                 'unit' => $unitExists->id,
    //                 'department' => $departmentExists->id,
    //                 'designation' => $item->designation ?? null,
    //                 'employee_status' => $item->employee_status ?? null,
    //                 'status' => 1,
    //                 'created_by' => Auth::id(),
    //                 'created_at' => now(),
    //             ];
    //         }
    //     }

    //     if (!empty($insertArray)) {
    //         $batchSize = 500;
    //         $chunks = array_chunk($insertArray, $batchSize);
    //         $insertedRecords = [];

    //         foreach ($chunks as $chunk) {
    //             $this->insert($chunk);
    //             $insertedRecords = array_merge($insertedRecords, $chunk);
    //         }

    //         return $insertedRecords;
    //     }
    // }

    public function store($emptemp)
    {
        $insertArray = [];

        foreach ($emptemp as $item) {

          
            $role = DB::table('template_user_role')
            ->where('role_name', $item->user_role)
            ->first();

            $insertArray[] = [
                'emp_id' => $item->emp_id ?? null,
                'emp_name' => $item->emp_name ?? null,
                'gender' => $item->gender ?? null,
                'nationality' => $item->nationality ?? null,
                'email' => $item->email ?? null,
                'joining_date' => $item->joining_date ? DBdatetimeformat($item->joining_date) : null,
                'mobile_no' => $item->mobile_no ?? null,
                'user_role' => $role->id ?? null,
                'designation' => $item->designation ?? null,
                'employee_status' => $item->employee_status ?? null,
                'status' => 1,
                'created_by' => Auth::id(),
                'created_at' => now(),
            ];
        }

        // Insert data in batches if there are valid records
        if (!empty($insertArray)) {
            $batchSize = 500;
            $chunks = array_chunk($insertArray, $batchSize);
            $insertedRecords = [];

            foreach ($chunks as $chunk) {
                $this->insert($chunk); // Perform batch insert
                $insertedRecords = array_merge($insertedRecords, $chunk);
            }
            return $insertedRecords;
        }

        // Return empty array if no records were inserted
        return [];
    }


    public function updates($id)
    {
        $request = request();
    
        $update_array = [
            'emp_id' => $request->emp_id ?? null,
            'emp_name' => $request->emp_name ?? null,
            'gender' => $request->gender ?? null,
            'nationality' => $request->nationality ?? null,
            'email' => $request->email ?? null,
            'joining_date' => DBdatetimeformat($request->joining_date),
            'mobile_no' => $request->mobile_no ?? null,
            'user_role' => decryptId($request->user_role),
            'company' => decryptId($request->company),
            'location' => decryptId($request->location),
            'unit' => decryptId($request->unit),
            'department' => decryptId($request->department),
            'designation' => $request->designation ?? null,
            'employee_status' => $request->employee_status ?? null,
            'status' => 1,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ];
        // Perform the update
        $this->where('id', $id)->update($update_array);
    
        // Retrieve and return the updated record
        return $this->find($id);
    }
    
    public function updateErrorStatus($emp_id, $errorMessage)
    {
        $update_data = [
            'error_status' => 1,
            'error_remarks' => $errorMessage,
        ];

        return EmployeeTemp::where('emp_id', $emp_id)->update($update_data);
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
        $query = $this->select('masters_employee.*', 'company_management.company_name', 'masters_department.department_name', 'masters_unit.unit_name');
        $query = $query->leftJoin('company_management', 'masters_employee.company', '=', 'company_management.id');
        $query = $query->leftJoin('masters_department', 'masters_employee.department', '=', 'masters_department.id');
        $query = $query->leftJoin('masters_unit', 'masters_employee.unit', '=', 'masters_unit.id');


        if (!empty($request->search)) {
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query->orWhere('masters_employee.emp_id', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('emp_id') && $request->emp_id) {
            $query = $query->where('masters_employee.emp_id', 'LIKE', '%' . $request->emp_id . '%');
        }

        if ($request->has('emp_name') && $request->emp_name) {
            $query = $query->where('masters_employee.emp_name', 'LIKE', '%' . $request->emp_name . '%');
        }
        if ($request->has('email') && $request->email) {
            $query = $query->where('masters_employee.email', 'LIKE', '%' . $request->email . '%');
        }
        if ($request->has('employee_status') && $request->employee_status) {
            $query = $query->where('masters_employee.employee_status', 'LIKE', '%' . $request->employee_status . '%');
        }

        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select('masters_employee.*', 'company_management.company_name', 'masters_department.department_name', 'masters_unit.unit_name','template_user_role.role_name')
            ->leftJoin('company_management', 'masters_employee.company', '=', 'company_management.id')
            ->leftJoin('masters_department', 'masters_employee.department', '=', 'masters_department.id')
            ->leftJoin('masters_unit', 'masters_employee.unit', '=', 'masters_unit.id')
            ->leftJoin('template_user_role', 'masters_employee.user_role', '=', 'template_user_role.id')
            ->where('masters_employee.id', $id)
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
        static::addGlobalScope(new TrashScope('masters_employee'));
    }
}
