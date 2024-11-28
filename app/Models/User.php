<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

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
    public function store($employees)
{
    $request = request();

    $data = []; 

    foreach ($employees as $item) {

        $data[] = [
            'name' => $item['emp_name'], 
            'first_name' => $item['emp_name'],
            'last_name' => '',
            'email' => $request->email, 
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
    }

    return $this->insert($data); 
}



    public function userUpdate($employee)
    {

        $request = request();


        // $decryptedRoleIds = [];
        // if ($request->has('emp_role_id')) {
        //     $decryptedRoleIds = array_map(function ($encryptedId) {
        //         return $encryptedId;
        //     }, $request->emp_role_id);
        // }

        // dd($employee);

        $data = array(
            'name' => $employee->emp_name,
            'first_name' => $employee->emp_name,
            'last_name' => '',
            'email' => $employee->emp_email,
            'role' => $employee->user_role,
            'employee_id' => $employee->emp_id,
            'department_id' => decryptId($employee->emp_department_id),
            'designation_id' => decryptId($employee->emp_designation_id),
            'mobile' => $employee->emp_phone_no,
            'updated_by' => Auth::id()
        );

       
        return $this->where('employee_id', $employee->emp_id)->update($data);
    }


    
}
