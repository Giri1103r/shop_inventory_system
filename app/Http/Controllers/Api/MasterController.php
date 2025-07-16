<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inspection\InspectionStaticDocno;
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
                    'company_management.id as company_id',
                    'company_management.company_name',
                    'masters_location.id as location_id',
                    'masters_location.location_name',
                    'masters_unit.id as unit_id',
                    'masters_unit.unit_name',
                    'masters_department.id as department_id',
                    'masters_department.department_name',
                )
                    ->join('template_user_role', 'masters_employee.user_role', '=', 'template_user_role.id')
                    ->join('company_management', 'masters_employee.company', '=', 'company_management.id')
                    ->join('masters_location', 'masters_employee.location', '=', 'masters_location.id')
                    ->join('masters_unit', 'masters_employee.unit', '=', 'masters_unit.id')
                    ->join('masters_department', 'masters_employee.department', '=', 'masters_department.id')
                    ->where('masters_employee.status', 1)
                    ->get()
                    ->map(function ($employeeList) {
                        return [
                            'id' => $employeeList->id,
                            'emp_name' => $employeeList->emp_name,
                            'emp_id' => $employeeList->emp_id,
                            'login_id' => $employeeList->login_id,
                            'designation' => $employeeList->designation,
                            'role' => [
                                'id' => $employeeList->role_id,
                                'role_name' => $employeeList->role_name
                            ],
                            'company' => [
                                'id' => $employeeList->company_id,
                                'company_name' => $employeeList->company_name
                            ],
                            'location' => [
                                'id' => $employeeList->location_id,
                                'location_name' => $employeeList->location_name
                            ],
                            'unit' => [
                                'id' => $employeeList->unit_id,
                                'unit_name' => $employeeList->unit_name
                            ],
                            'department' => [
                                'id' => $employeeList->department_id,
                                'department_name' => $employeeList->department_name
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
                    'masters_work.designation',
                    'masters_work.emp_id',
                    'company_management.id as company_id',
                    'company_management.company_name',
                    'masters_location.id as location_id',
                    'masters_location.location_name',
                    'masters_unit.id as unit_id',
                    'masters_unit.unit_name',
                    'masters_department.id as department_id',
                    'masters_department.department_name',
                )
                    ->join('company_management', 'masters_work.company', '=', 'company_management.id')
                    ->join('masters_location', 'masters_work.location', '=', 'masters_location.id')
                    ->join('masters_unit', 'masters_work.unit', '=', 'masters_unit.id')
                    ->join('masters_department', 'masters_work.department', '=', 'masters_department.id')
                    ->where('masters_work.status', 1)
                    ->get()
                    ->map(function ($workList) {
                        return [
                            'id' => $workList->id,
                            'emp_name' => $workList->emp_name,
                            'emp_id' => $workList->emp_id,
                            'designation' => $workList->designation,
                            'company' => [
                                'id' => $workList->company_id,
                                'company_name' => $workList->company_name
                            ],
                            'location' => [
                                'id' => $workList->location_id,
                                'location_name' => $workList->location_name
                            ],
                            'unit' => [
                                'id' => $workList->unit_id,
                                'unit_name' => $workList->unit_name
                            ],
                            'department' => [
                                'id' => $workList->department_id,
                                'department_name' => $workList->department_name
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


    public function documentNumber(Request $request)
    {
        try {

            if (Auth::user()) {


                $documentList = InspectionStaticDocno::select(
                    'id',
                    'type',
                    'doc_no',
                    'issue_date',
                    'rev_dt',

                )

                    ->where('status', 1)
                    ->get();

                $success = [
                    'responsible_person' => $documentList,
                ];

                return $this->sendResponse($success, 'Document Details Details');
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
