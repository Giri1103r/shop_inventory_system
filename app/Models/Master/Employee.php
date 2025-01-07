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
        'login_id',
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
                    ->orWhere('masters_employee.emp_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_employee.emp_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_employee.email', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_employee.employee_status', 'LIKE', '%' . $search . '%');
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
        if ($request->has('status') && $request->status) {

            $query = $query->where('masters_employee.status', decryptId($request->status));
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
    public function UniqueCheck($data)
    {

        return $this->where('email',  $data)->get();
    }

    public function ExistuniqueCheck($data, $id)
    {
        return $this->where('email',  $data)
            ->where('id', '!=', $id)
            ->get();
    }

    public function store($emptemp)
    {
        $insertArray = [];
        foreach ($emptemp as $item) {

            $emailExists = $this->where('email', $item->email)->where('emp_id', '!=', $item->emp_id)->exists();

            if ($emailExists) {
                $errorMessage = "Email already exists.";
                $this->updateErrorStatus($item->emp_id, $errorMessage);
                continue;
            }
            $role = DB::table('template_user_role')
                ->where('role_name', $item->user_role)
                ->first();

            $data = [
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
                'reporting_manager' => $item->reporting_manager ?? null,
                'status' => 1,
                'error_status' => 0,
                'error_remarks' => null,
                'created_by' =>1,
            ];

            $exists = $this->where('emp_id', $item->emp_id)->exists();

            if ($exists) {
                $data['updated_at'] = now();
            } else {
                $data['created_at'] = now();
            }
            $insertArray[] = $data;
        }

        if (!empty($insertArray)) {
            $batchSize = 500;
            $chunks = array_chunk($insertArray, $batchSize);
            $insertedRecords = [];

            foreach ($chunks as $chunk) {
                foreach ($chunk as $values) {
                    $this->updateOrInsert(
                        ['emp_id' => $values['emp_id']],
                        $values
                    );
                }
                $insertedRecords = array_merge($insertedRecords, $chunk);
            }

            return $insertedRecords;
        }

        return [];
    }



    public function updates($id)
    {
        $request = request();
        if ($request->has('user_role')) {
            $decryptedRoleIds = array_map(function ($encryptedId) {
                return $encryptedId;
            }, $request->user_role);

            $commaSeparatedRoles = implode(',', $decryptedRoleIds);
        }

        $update_array = [
            'emp_id' => $request->emp_id ?? null,
            'emp_name' => $request->emp_name ?? null,
            'gender' => $request->gender ?? null,
            'nationality' => $request->nationality ?? null,
            'id_type'=>$request->id_type  ?? null,
            'id_number'=>$request->id_number  ?? null,
            'email' => $request->email ?? null,
            'joining_date' => DBdateformat($request->joining_date) ?? '',
            'mobile_no' => $request->mobile_no ?? null,
            'user_role' => $commaSeparatedRoles,
            'company' => decryptId($request->company),
            'location' => decryptId($request->location),
            'unit' => decryptId($request->unit),
            'department' => decryptId($request->department),
            'designation' => $request->designation ?? null,
            'reporting_manager'=>$request->reporting_manager ?? null,
            'employee_status' => $request->employee_status ?? null,
            'status' => 1,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ];

        $this->where('id', $id)->update($update_array);

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
    public function updateUserId($emp_id, $login_id)
    {
        $update_data = [
            'login_id' => $login_id,
        ];

        return $this->where('emp_id', $emp_id)->update($update_data);
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
                $query
                    ->orWhere('masters_employee.emp_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_employee.emp_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_employee.email', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_employee.employee_status', 'LIKE', '%' . $search . '%');
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
        if ($request->has('status') && $request->status) {

            $query = $query->where('masters_employee.status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');
        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select('masters_employee.*', 'company_management.company_name', 'masters_department.department_name', 'masters_unit.unit_name', 'template_user_role.role_name')
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

        $query = $this->select('login_id', 'emp_name')->where('status', 1);

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



    public function getEmployeefulldata()
    {
        return Employee::all();
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('masters_employee'));
    }
}
