<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use App\Models\Master\Employee;
use App\Models\User;
use App\Mail\ContractExpireEmail;
use App\Mail\ContractExpireListEmail;
use Illuminate\Support\Facades\Mail;

use Carbon\Carbon;



class CronController extends Controller
{
    public function queueHigh()
    {
        $queueLength = Queue::size('high');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'high',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);

            return response()->json(['message' => 'Queue High work command executed successfully',  'exit_code' => $exitCode]);
        } else {
            return response()->json(['message' => 'No jobs in the high queue to process', 'exit_code' => 0]);
        }
    }

    public function queueDefault()
    {
        $queueLength = Queue::size('default');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'default',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);

            return response()->json(['message' => 'Queue Default work command executed successfully',  'exit_code' => $exitCode]);
        } else {
            return response()->json(['message' => 'No jobs in the default queue to process', 'exit_code' => 0]);
        }
    }

    public function queueEmail()
    {
        $queueLength = Queue::size('email');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'email',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);

            return response()->json(['message' => 'Queue Email work command executed successfully',  'exit_code' => $exitCode]);
        } else {
            return response()->json(['message' => 'No jobs in the email queue to process', 'exit_code' => 0]);
        }
    }

    public function queueFactoryImport()
    {
        $queueLength = Queue::size('factoryimport');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'factoryimport',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);

            return response()->json(['message' => 'Queue Factory Import work command executed successfully',  'exit_code' => $exitCode]);
        } else {
            return response()->json(['message' => 'No jobs in the Factory Import queue to process', 'exit_code' => 0]);
        }
    }

    public function queueFactoryhallImport()
    {
        $queueLength = Queue::size('factoryhallimport');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'factoryhallimport',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);

            return response()->json(['message' => 'Queue Factory Assembly Hall Import work command executed successfully',  'exit_code' => $exitCode]);
        } else {
            return response()->json(['message' => 'No jobs in the Factory Assembly Hall Import queue to process', 'exit_code' => 0]);
        }
    }

    public function queueFactoryhallareaImport()
    {
        $queueLength = Queue::size('factoryhallareaimport');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'factoryhallareaimport',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);

            return response()->json(['message' => 'Queue Factory Hall Area Import work command executed successfully',  'exit_code' => $exitCode]);
        } else {
            return response()->json(['message' => 'No jobs in the Factory Hall Import queue to process', 'exit_code' => 0]);
        }
    }
    public function queueRoleImport()
    {
        $queueLength = Queue::size('roleimport');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'roleimport',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);

            return response()->json(['message' => 'Queue User Role Import work command executed successfully',  'exit_code' => $exitCode]);
        } else {
            return response()->json(['message' => 'No jobs in the User Role Import queue to process', 'exit_code' => 0]);
        }
    }

    public function queueDepartmentImport()
    {
        $queueLength = Queue::size('departmentimport');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'departmentimport',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);

            return response()->json(['message' => 'Queue Department Import work command executed successfully',  'exit_code' => $exitCode]);
        } else {
            return response()->json(['message' => 'No jobs in the Department Import queue to process', 'exit_code' => 0]);
        }
    }

    public function queueDesignationImport()
    {
        $queueLength = Queue::size('designationimport');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'designationimport',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);

            return response()->json(['message' => 'Queue Designation Import work command executed successfully',  'exit_code' => $exitCode]);
        } else {
            return response()->json(['message' => 'No jobs in the Designation Import queue to process', 'exit_code' => 0]);
        }
    }

    public function queueEmployeeImport()
    {
        $queueLength = Queue::size('empimport');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'empimport',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);

            return response()->json(['message' => 'Queue Employee Import work command executed successfully',  'exit_code' => $exitCode]);
        } else {
            return response()->json(['message' => 'No jobs in the Employee Import queue to process', 'exit_code' => 0]);
        }
    }

    public function queueEmployeetypeImport()
    {
        $queueLength = Queue::size('employeetypeimport');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'employeetypeimport',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);

            return response()->json(['message' => 'Queue Employee Type Import work command executed successfully',  'exit_code' => $exitCode]);
        } else {
            return response()->json(['message' => 'No jobs in the Employee Type Import queue to process', 'exit_code' => 0]);
        }
    }

    public function queueUauccategoryImport()
    {
        $queueLength = Queue::size('uauc_categoryimport');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'uauc_categoryimport',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);

            return response()->json(['message' => 'Queue UA/UC Category Import work command executed successfully',  'exit_code' => $exitCode]);
        } else {
            return response()->json(['message' => 'No jobs in the UA/UC Category Type Import queue to process', 'exit_code' => 0]);
        }
    }

    public function queueSafetycategoryImport()
    {
        $queueLength = Queue::size('safetycategoryimport');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'safetycategoryimport',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);

            return response()->json(['message' => 'Queue Essential Safety Category Import work command executed successfully',  'exit_code' => $exitCode]);
        } else {
            return response()->json(['message' => 'No jobs in the Essential Safety Category Import queue to process', 'exit_code' => 0]);
        }
    }

    public function queueSafetysubcategoryImport()
    {
        $queueLength = Queue::size('safetysubcategoryimport');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'safetysubcategoryimport',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);

            return response()->json(['message' => 'Queue Essential Safety SubCategory Import work command executed successfully',  'exit_code' => $exitCode]);
        } else {
            return response()->json(['message' => 'No jobs in the Essential Safety SubCategory Import queue to process', 'exit_code' => 0]);
        }
    }

    public function contractendDate()
    {
        $today = Carbon::today()->toDateString();

        $employeesToUpdate = Employee::where('employee_type', EMPLOYEE_CONTRACT)
            ->whereDate('contract_end_date', '<', $today)
            ->where('status', 1)
            ->get();

        if ($employeesToUpdate->isNotEmpty()) {
            foreach ($employeesToUpdate as $employee) {
                $loginId = $employee->login_id;
                User::where('id', $loginId)->update(['status' => 0]);
                $employee->status = 0;
                $employee->save();
                Mail::to($employee->email)->queue(new ContractExpireEmail($employee));
            }
            $AdminUserEmail =  User::whereRaw('FIND_IN_SET(' . ROLE_ADMIN . ', role)')->get();
            $AdminUserId =  User::whereRaw('FIND_IN_SET(' . ROLE_ADMIN . ', role)')->pluck('id')->toArray();


            if ($AdminUserId != '' && $AdminUserId != null) {
                if ($AdminUserEmail != null) {
                    foreach ($AdminUserEmail as $user) {
                        Mail::to($user->email)->queue(new ContractExpireListEmail($employeesToUpdate));
                    }
                }
            }
            return response()->json(['message' => 'Contract Employee Login Blocked successfully.']);

        } else {
            return response()->json(['message' => 'No Contract Data Found', 'exit_code' => 0]);
        }
    }

    public function queueCompanyEmployeeImport()
    {
        $queueLength = Queue::size('company_empimport');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'company_empimport',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);

            return response()->json(['message' => 'Queue Employees Import work command executed successfully',  'exit_code' => $exitCode]);
        } else {
            return response()->json(['message' => 'No jobs in the Employee Import queue to process', 'exit_code' => 0]);
        }
    }
}
