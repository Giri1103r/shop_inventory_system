<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Master\Company;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use App\Models\Master\Work;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class MasterController extends BaseController
{
    private $employee;
    public function __construct()
    {
        $this->employee = new Employee();
    }
    public function employee()
    {
        try {
            if (Auth::check()) {
                $employeeList = Employee::select(
                    'masters_employee.id',
                    'template_user_role.id as role_id',
                    'template_user_role.role_name',
                    'masters_employee.emp_name',
                    'masters_employee.login_id',
                    'masters_employee.emp_id',
                    'masters_employee.user_role',
                    'masters_employee.designation',
                    'masters_department.id as department_id',
                    'masters_department.department_name'
                )
                    ->join('template_user_role', 'masters_employee.user_role', '=', 'template_user_role.id')
                    ->join('masters_department', 'masters_employee.department', '=', 'masters_department.id')
                    ->where('masters_employee.status', 1)
                    ->get()
                    ->map(function ($employee) {
                        return [
                            'id' => $employee->id,
                            'emp_name' => $employee->emp_name,
                            'emp_id' => $employee->emp_id,
                            'login_id' => $employee->login_id,
                            'designation' => $employee->designation,
                            'role' => [
                                'id' => $employee->role_id,
                                'role_name' => $employee->role_name
                            ],
                            'department' => [
                                'id' => $employee->department_id,
                                'department_name' => $employee->department_name
                            ]
                        ];
                    });

                return $this->sendResponse(['responsible_person' => $employeeList], 'Employee details');
            }

            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        } catch (Exception $ex) {
            Log::error('Employee Fetch Error: ' . $ex->getMessage());
            return $this->sendError('Something went wrong.', ['error' => $ex->getMessage()], 500);
        }
    }





    public function worker()
    {
        try {
            if (Auth::check()) {
                $workList = Work::select(
                    'masters_work.id',
                    'masters_work.emp_name',
                    'masters_work.emp_id',
                    'masters_department.id as department_id',
                    'masters_department.department_name'
                )
                    ->join('masters_department', 'masters_work.department', '=', 'masters_department.id')
                    ->where('masters_work.status', 1)
                    ->get()
                    ->map(function ($worker) {
                        return [
                            'id' => $worker->id,
                            'emp_name' => $worker->emp_name,
                            'emp_id' => $worker->emp_id,
                            'department' => [
                                'id' => $worker->department_id,
                                'department_name' => $worker->department_name
                            ]
                        ];
                    });

                $success = [
                    'responsible_person' => $workList,
                ];

                return $this->sendResponse($success, 'Worker Details');
            }

            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        } catch (Exception $ex) {
            Log::error('Worker Fetch Error: ' . $ex->getMessage());

            return $this->sendError('Something went wrong.', ['error' => $ex->getMessage()], 500);
        }
    }


    public function company(Request $request)
    {
        try {

            if (Auth::check()) {

                $companyList = Company::select(
                    'company_management.id',
                    'company_management.company_name',
                )

                    ->where('company_management.status', 1)
                    ->get();

                $success = [
                    'responsible_person' => $companyList,
                ];

                return $this->sendResponse($success, 'Company Details');
            }

            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        } catch (Exception $ex) {
            // Log the error for debugging
            Log::error('Employee Fetch Error: ' . $ex->getMessage());

            return $this->sendError('Something went wrong.', ['error' => $ex->getMessage()], 500);
        }
    }

    public function location(Request $request)
    {
        try {

            if (Auth::user()) {

                $rules = [
                    'company_id' => 'required',

                ];
                $messages = [
                    'company_id.required' => 'Company ID is Required',

                ];

                $validator = Validator::make($request->all(), $rules, $messages);

                if ($validator->fails()) {
                    return $this->sendError('Validation Error', $validator->errors(), 422);
                }

                $locationList = Location::select(
                    'id',
                    'company_id',
                    'location_name',
                )->where('company_id', $request->company_id)
                    ->where('status', 1)
                    ->get();

                $success = [
                    'responsible_person' => $locationList,
                ];

                return $this->sendResponse($success, 'Location Details');
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    public function unit(Request $request)
    {
        try {

            if (Auth::user()) {

                $rules = [
                    'company_id' => 'required',
                    'location_id' => 'required',
                ];
                $messages = [
                    'company_id.required' => 'Company ID is Required',
                    'location_id.required' => 'Location ID is Required',

                ];

                $validator = Validator::make($request->all(), $rules, $messages);

                if ($validator->fails()) {
                    return $this->sendError('Validation Error', $validator->errors(), 422);
                }

                $unitList = Unit::select(
                    'id',
                    'location_id',
                    'company_id',
                    'unit_name',
                )->where('company_id', $request->company_id)
                    ->where('location_id', $request->location_id)

                    ->where('status', 1)
                    ->get();

                $success = [
                    'responsible_person' => $unitList,
                ];

                return $this->sendResponse($success, 'Unit Details');
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }


    public function department(Request $request)
    {
        try {

            if (Auth::user()) {

                $rules = [
                    'company_id' => 'required',
                    'location_id' => 'required',
                    'unit_id' => 'required',

                ];
                $messages = [
                    'company_id.required' => 'Company ID is Required',
                    'location_id.required' => 'Location ID is Required',
                    'unit_id.required' => 'Unit ID is Required',


                ];

                $validator = Validator::make($request->all(), $rules, $messages);

                if ($validator->fails()) {
                    return $this->sendError('Validation Error', $validator->errors(), 422);
                }

                $departmentList = Department::select(
                    'id',
                    'location_id',
                    'company_id',
                    'unit_id',
                    'department_name',

                )
                    ->where('company_id', $request->company_id)
                    ->where('location_id', $request->location_id)
                    ->where('unit_id', $request->unit_id)

                    ->where('status', 1)
                    ->get();

                $success = [
                    'responsible_person' => $departmentList,
                ];

                return $this->sendResponse($success, 'Department Details');
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
