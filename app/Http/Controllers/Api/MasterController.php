<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Master\Employee;
use App\Models\Master\Work;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
                    'masters_employee.emp_name',
                    'masters_employee.emp_id',
                    'masters_department.id as department_id',
                    'masters_department.department_name'
                )
                ->join('masters_department', 'masters_employee.department', '=', 'masters_department.id')
                ->where('masters_employee.status', 1)
                ->get();

            $success = [
                'responsible_person' => $employeeList,
            ];


                return $this->sendResponse($success, 'Employee details');
            }

            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        } catch (Exception $ex) {
            // Log the error for debugging
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
                ->get();

                $success = [
                    'responsible_person' => $workList,
                ];

                return $this->sendResponse($success, 'Worker Details');
            }

            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        } catch (Exception $ex) {
            // Log the error for debugging
            Log::error('Employee Fetch Error: ' . $ex->getMessage());

            return $this->sendError('Something went wrong.', ['error' => $ex->getMessage()], 500);
        }
    }
}
