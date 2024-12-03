<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Session;
use App\Mail\ContractExpireEmail;
use App\Mail\ContractExpireListEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

use Carbon\Carbon;
use App\Models\Master\Worktemp;
use App\Models\Master\EmployeeTemp;

use App\Models\User;
use App\Models\Master\Work;
use App\Models\Master\Employee;

use App\Mail\EmployeeRegisterEmail;


use Exception;

class CronController extends Controller
{

    private $worktemp;
    private $emp_temp;
    private $work;
    private $employee;
    private $user;

    public function __construct()
    {

        $this->worktemp = new Worktemp();
        $this->emp_temp = new EmployeeTemp();
        $this->work = new Work();
        $this->employee = new Employee();
        $this->user = new User();
    }
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
            Session::invalidate();
            return response()->json(['message' => 'Queue High work command executed successfully',  'exit_code' => $exitCode]);
        } else {
            Session::invalidate();
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
            Session::invalidate();
            return response()->json(['message' => 'Queue Default work command executed successfully',  'exit_code' => $exitCode]);
        } else {
            Session::invalidate();
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
            Session::invalidate();
            return response()->json(['message' => 'Queue Email work command executed successfully',  'exit_code' => $exitCode]);
        } else {
            Session::invalidate();
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

    public function workMasterTemp()
    {
        try {

            $apiUrl = 'https://vmsapi.karam.in/emp.asmx/GetWorkerDetails?TokenId=123&OfficeId=PNI';

            $response = Http::get($apiUrl);

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data)) {
                    $work = $this->worktemp->store($data);
                    return response()->json(['message' => 'Data saved successfully.']);
                } else {
                    return response()->json(['message' => 'No data found in API response.']);
                }
            } else {
                return response()->json(['message' => 'Failed to fetch data from API.', 'status' => $response->status()]);
            }
        } catch (Exception $ex) {
            dd($ex);
            return response()->json(['message' => 'An error occurred.', 'error' => $ex->getMessage()]);
        }
    }
    public function workSave()
    {

        try {
            $worktemp = Worktemp::select('*')->where('upload_status', '0')->get();


            if (!empty($worktemp)) {


                $work = $this->work->store($worktemp);
                if (empty($work)) {
                    $this->worktemp->updateAllErrorStatus();
                } else {
                    foreach ($work as $item) {
                        $emp_id = $item['emp_id'];

                        $worktempdata = $this->worktemp->updates($emp_id);
                    }
                }
              
                $baseFolderPath = storage_path('app/private/');

                $month = now()->format('F');
                $date = now()->format('d');

                $folderPath = $baseFolderPath . $month . '/' . $date . '/worker/';
                if (!File::exists($folderPath)) {
                    File::makeDirectory($folderPath, 0755, true);
                }

                $filePath = $folderPath . 'work_data.txt';
                $content = '';
                if (!empty($work)) {
                    foreach ($work as $item) {
                        $content .= 'Emp ID: ' . $item['emp_id'] . "\n";
                        $content .= 'Other Data: ' . json_encode($item) . "\n\n";
                    }
                }

                File::put($filePath, $content);
                return response()->json(['message' => 'Data saved successfully.']);
            } else {

                return response()->json(['message' => 'No data found in API response.']);
            }
        } catch (Exception $ex) {
            dd($ex);
            return response()->json(['message' => 'An error occurred.', 'error' => $ex->getMessage()]);
        }
    }
    public function employeeMasterTemp()
    {
        try {

            $apiUrl = 'https://hrms.esparsh.in/PunchesAPI/api/Attendance/GetEmployeeDetails?token=KARAroz4HhR1EIx8qaz3C13z/quTXBkQ3Q5hj7Qx3aA*&fromDate=2024-01-01&toDate=2024-11-16';

            $response = Http::get($apiUrl);

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data)) {
                    $emp_temp = $this->emp_temp->store($data);
                    return response()->json(['message' => 'Data saved successfully.']);
                } else {
                    return response()->json(['message' => 'No data found in API response.']);
                }
            } else {
                return response()->json(['message' => 'Failed to fetch data from API.', 'status' => $response->status()]);
            }
        } catch (Exception $ex) {
            dd($ex);
            return response()->json(['message' => 'An error occurred.', 'error' => $ex->getMessage()]);
        }
    }

    
    public function EmployeeSave()
    {

        try {
            $emp_temp = EmployeeTemp::select('*')->where('upload_status', '0')->get();

            if (!empty($emp_temp)) {
                
                $employee = $this->employee->store($emp_temp);
                $users = $this->user->store($employee);
             
                // foreach ($users as $user) { 
                //     if (!empty($user) && isset($user->email)) {
                //         $empdetails = $this->employee->selectOne($user->employee_id);
                //         if (!empty($empdetails)) {
                            
                //             $emp = $empdetails->toArray();
                //             Mail::to($user->email)->queue(new EmployeeRegisterEmail($emp));
                //         }
                //     }
                // }
           
                if (empty($employee)) {
                    $this->emp_temp->updateAllErrorStatus();
                } else {
                    foreach ($employee as $item) {
                        $emp_id = $item['emp_id'];

                        $emp_tempdata = $this->emp_temp->updates($emp_id);
                    }
                }
                $baseFolderPath = storage_path('app/private/');

                $month = now()->format('F');
                $date = now()->format('d');

                $folderPath = $baseFolderPath . $month . '/' . $date . '/employee/';
                if (!File::exists($folderPath)) {
                    File::makeDirectory($folderPath, 0755, true);
                }

                $filePath = $folderPath . 'employee_data.txt';
                $content = '';
                if (!empty($employee)) {
                    foreach ($employee as $item) {
                        $content .= 'Emp ID: ' . $item['emp_id'] . "\n";
                        $content .= 'Other Data: ' . json_encode($item) . "\n\n";
                    }
                }

                File::put($filePath, $content);
                return response()->json(['message' => 'Data saved successfully.']);
            } else {

                return response()->json(['message' => 'No data found in API response.']);
            }
        } catch (Exception $ex) {
            dd($ex);
            return response()->json(['message' => 'An error occurred.', 'error' => $ex->getMessage()]);
        }
    }
  


    public function queueCompanyImport()
    {
        $queueLength = Queue::size('company');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'company',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);
            Session::invalidate();
            return response()->json(['message' => 'Queue Company command executed successfully',  'exit_code' => $exitCode]);
        } else {
            Session::invalidate();
            return response()->json(['message' => 'No jobs in the Company Import queue to process', 'exit_code' => 0]);
        }
    }


    public function queuelocationimport()
    {
        $queueLength = Queue::size('location');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'location',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);
            Session::invalidate();
            return response()->json(['message' => 'Queue Location command executed successfully',  'exit_code' => $exitCode]);
        } else {
            Session::invalidate();
            return response()->json(['message' => 'No jobs in the Location Import queue to process', 'exit_code' => 0]);
        }
    }

    public function queueunitimport()
    {
        $queueLength = Queue::size('unit');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'unit',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);
            Session::invalidate();
            return response()->json(['message' => 'Queue Unit command executed successfully',  'exit_code' => $exitCode]);
        } else {
            Session::invalidate();
            return response()->json(['message' => 'No jobs in the Unit Import queue to process', 'exit_code' => 0]);
        }
    }

    public function queueDepartmentuplodimport()
    {
        $queueLength = Queue::size('department');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'department',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);
            Session::invalidate();
            return response()->json(['message' => 'Queue Unit command executed successfully',  'exit_code' => $exitCode]);
        } else {
            Session::invalidate();
            return response()->json(['message' => 'No jobs in the Unit Import queue to process', 'exit_code' => 0]);
        }
    } 

}
