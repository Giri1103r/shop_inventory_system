<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'users';

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'role',
        'user_type',
        'employee_id',
        'username',
        'email_verified_at',
        'password',
        'remember_token',
        'company_id',
        'location_id',
        'unit_id',
        'department_id',
        'designation_id',
        'mobile',
        'otp',
        'otp_token',
        'profile_image',
        'permission',
        'created_by',
        'updated_by',
        'status',
        'trash',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('users.*', 'template_user_role.role_name',);
        $query = $query->leftJoin('template_user_role', 'users.role', '=', 'template_user_role.id');
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('name', 'LIKE', '%' . $search . '%');
            });
        }


        if ($request->has('employee_id') && $request->employee_id) {
            $query = $query->where('employee_id', 'LIKE', '%' . $request->employee_id . '%');
        }
        if ($request->has('name') && $request->name) {
            $query = $query->where('name', 'LIKE', '%' . $request->name . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('users.status', decryptId($request->status));
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
    public function store($employees)
    {
        $createdUsers = [];

        foreach ($employees as $item) {
            $emailExists = $this->where('email', $item['email'])->where('employee_id', '!=', $item['emp_id'])->exists();

            if ($emailExists) {
                $errorMessage = "Email already exists.";
                $this->updateErrorStatus($item['emp_id'], $errorMessage);
                continue; // Skip this record
            }

            $userData = [
                'name' => $item['emp_name'],
                'first_name' => $item['emp_name'],
                'last_name' => '',
                'email' => $item['email'],
                'role' => $item['user_role'],
                'user_type' => 1,
                'employee_id' => $item['emp_id'],
                'username' => $item['emp_id'],
                'password' => Hash::make($item['emp_name'] . "@12345"),
                'department_id' => null,
                'designation_id' => $item['designation'],
                'mobile' => $item['mobile_no'],
                'created_by' => Auth::id(),
            ];

            $exists = $this->where('employee_id', $item['emp_id'])->exists();

            if ($exists) {
                $userData['updated_at'] = now();
            } else {
                $userData['created_at'] = now();
            }

            try {
                $this->updateOrInsert(
                    ['employee_id' => $item['emp_id']],
                    $userData
                );
                $createdUsers[] = $userData;
            } catch (\Exception $e) {
                report($e);
            }
        }

        return $createdUsers;
    }




    public function updateErrorStatus($emp_id, $errorMessage)
    {
        $update_data = [
            'error_status' => 1,
            'error_remarks' => $errorMessage,
        ];

        return DB::table('masters_employee')->where('emp_id', $emp_id)->update($update_data);
    }


    public function userUpdate($employee)
    {

        $request = request();


        $decryptedRoleIds = [];


        if ($request->has('user_role')) {
            $decryptedRoleIds = array_map(function ($encryptedId) {
                return $encryptedId;
            }, $request->user_role);

            $commaSeparatedRoles = implode(',', $decryptedRoleIds);
        }

        // dd($employee);

        $data = array(
            'name' => $employee->emp_name,
            'first_name' => $employee->emp_name,
            'last_name' => '',
            'email' => $employee->emp_email,
            'role' => $commaSeparatedRoles,
            'employee_id' => $employee->emp_id,
            'department_id' => decryptId($employee->emp_department_id),
            'designation_id' => decryptId($employee->emp_designation_id),
            'mobile' => $employee->emp_phone_no,
            'updated_by' => Auth::id()
        );


        return $this->where('employee_id', $employee->emp_id)->update($data);
    }

    public function getUserdata()
    {
        $user = Auth::user()->employee_id;
        $data = User::where('employee_id', $user)->first();
        return $data;
    }

    public function findEhsofficer()
    {
        return User::whereRaw('FIND_IN_SET(?, role)', [3])
            ->get();
    }
    public function assigneduser($ehsofficer)
    {
        return $ehsofficer->pluck('id')->toArray();
    }

    public function finduseremail($empId)
    {
        return User::where('employee_id', $empId)
            ->pluck('email')
            ->first();
    }

    public function findDepartmenthod($departmentId)
    {
        return User::where('department_id', $departmentId)
            ->whereRaw('FIND_IN_SET(?, role)', [4])
            ->pluck('email')
            ->first();
    }
    public function requestorId(){
        return $this->pluck('id')->toArray();
    }
    public function findStoremanager()
    {
        return User::whereRaw('FIND_IN_SET(?, role)', [5])
            ->pluck('email')
            ->first();
    }
    public function getdepartmenthodId($departmentId)
    {
        return User::where('department_id', $departmentId)
            ->whereRaw('FIND_IN_SET(?, role)', [4])
            ->pluck('id')
            ->toArray();
    }

    public function getStoreManagerId(){
        return $this->whereRaw('FIND_IN_SET(?, role)', [5])->pluck('id')->toArray();
    }

    public function getrequestId($empId)
    {
        return User::where('employee_id', $empId)->pluck('id')->toArray();
    }


    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('users.*', 'template_user_role.role_name');
        $query = $query->leftJoin('template_user_role', 'users.role', '=', 'template_user_role.id');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query
                    ->orWhere('name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('employee_id') && $request->employee_id) {
            $query = $query->where('employee_id', 'LIKE', '%' . $request->employee_id . '%');
        }
        if ($request->has('name') && $request->name) {
            $query = $query->where('name', 'LIKE', '%' . $request->name . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('users.status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
    }
}
