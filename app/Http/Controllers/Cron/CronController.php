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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use App\Models\Master\Worktemp;
use App\Models\Master\EmployeeTemp;

use App\Models\User;
use App\Models\Master\Work;
use App\Models\Master\Employee;
use App\Models\Master\PpeStockinventory;
use App\Models\Permit\SafetyPermit;

use App\Mail\EmployeeRegisterEmail;
use App\Mail\PermitExpiryEmail;


use Exception;

class CronController extends Controller
{

    private $worktemp;
    private $emp_temp;
    private $work;
    private $employee;
    private $user;
    private $ppestock;
    private $safetypermit;

    public function __construct()
    {
        $this->safetypermit = new SafetyPermit();
        $this->worktemp = new Worktemp();
        $this->emp_temp = new EmployeeTemp();
        $this->work = new Work();
        $this->employee = new Employee();
        $this->user = new User();
        $this->ppestock = new PpeStockinventory();
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
                foreach ($users as $user) {
                    if (!empty($user) && isset($user['email'])) {
                        Mail::to($user['email'])->queue(new EmployeeRegisterEmail($user));
                    }
                    if (!empty($user) && isset($user['employee_id'])) {
                        $userID = DB::table('users')
                            ->select('id')
                            ->where('employee_id', $user['employee_id'])
                            ->first();

                        if ($userID && isset($userID->id)) {
                            $this->employee->updateUserId($user['employee_id'], $userID->id);
                        }
                    }
                }


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


    public function storeItem()
    {
        try {

            $apiUrl = 'https://vmsapi.karam.in/emp.asmx/GetPPEInventory?TokenId=123&Orgid=86&Item=71160-H';

            $response = Http::get($apiUrl);

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data)) {
                    $work = $this->ppestock->store($data);
                    Session::flash('sucess', 'Your data has been created Sucessfully');
                    return redirect('ppe_stock_inventory/list');
                } else {
                    return response()->json(['message' => 'No data found in API response.']);
                }
            } else {
                return response()->json(['message' => 'Failed to fetch data from API.', 'status' => $response->status()]);
            }
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['message' => 'An error occurred.', 'error' => $ex->getMessage()]);
        }
    }

    public function updateItem()
    {
        try {

            $apiUrl = 'https://vmsapi.karam.in/emp.asmx/GetPPEInventory?TokenId=123&Orgid=86&Item=71160-H';

            $response = Http::get($apiUrl);

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data)) {
                    $work = $this->ppestock->update($data);
                    Session::flash('sucess', 'Your data has been created Sucessfully');
                    return redirect('ppe_stock_inventory/list');
                } else {
                    return response()->json(['message' => 'No data found in API response.']);
                }
            } else {
                return response()->json(['message' => 'Failed to fetch data from API.', 'status' => $response->status()]);
            }
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['message' => 'An error occurred.', 'error' => $ex->getMessage()]);
        }
    }


    public function permitExpiry()
    {
        try {

            $currentTime = Carbon::now()->format('H:i:s');
            $permits = SafetyPermit::where('trash', 'NO')
                ->where('permit_status', '!=', STATUS_PLANT_HEAD_APPROVED)
                ->whereDate('date', Carbon::today())
                ->where('time_to', '<', $currentTime)
                ->get();

            if ($permits->isNotEmpty()) {
                foreach ($permits as $permit) {
                    $permit->permit_status = STATUS_PERMIT_EXPIRED;
                    $permit->save();
                }

                Log::info('Expired permits updated successfully.', ['count' => $permits->count()]);
                return response()->json(['message' => 'Expired permits updated successfully.']);
            } else {
                Log::info('No permits found for expiry update.');
                return response()->json(['message' => 'No expired permits found.']);
            }
        } catch (Exception $ex) {
            Log::error('Error in permitExpiry cron job.', ['error' => $ex->getMessage()]);
            return response()->json(['message' => 'An error occurred.', 'error' => $ex->getMessage()]);
        }
    }


    public function permitClose()
    {
        try {
            Log::info('PermitClose function started.');

            $currentTime = Carbon::now();
            $timeThirtyMinutesAhead = Carbon::now()->addMinutes(30);

            $permits = SafetyPermit::where('trash', 'NO')
                ->where('permit_status', '!=', STATUS_PLANT_HEAD_APPROVED)
                ->whereDate('date', Carbon::today())
                ->whereTime('time_to', '>=', $currentTime->toTimeString())
                ->whereTime('time_to', '<=', $timeThirtyMinutesAhead->toTimeString())
                ->get();

            Log::info('Fetched permits: ', ['count' => $permits->count()]);

            $mailsubject = 'Permit is going to expire in 30 minutes';

            foreach ($permits as $permit) {
                $assignedUser = User::where('id', $permit->created_by)
                    ->select('name', 'email')
                    ->first();

                if ($assignedUser && $assignedUser->email) {
                    $safetypermitdetails = $this->safetypermit->selectmail($permit->id);
                    $permitrray = $safetypermitdetails->toArray();

                    $permitrray['name'] = $assignedUser->name;
                    $permitrray['email_id'] = $assignedUser->email;
                    $permitrray['mail_subject'] = $mailsubject;

                    Mail::to($permitrray['email_id'])->queue(new PermitExpiryEmail($permitrray));

                    Log::info("Permit expiry email sent.", [
                        'email' => $assignedUser->email,
                        'permit_id' => $permit->permit_id,
                    ]);
                } else {
                    Log::warning("No user or email found for permit.", ['permit_id' => $permit->permit_id]);
                }
            }
        } catch (Exception $ex) {

            dd($ex);
            Log::error('Error in permitClose cron job.', ['error' => $ex->getMessage()]);
            return response()->json(['message' => 'An error occurred.', 'error' => $ex->getMessage()]);
        }
    }

    public function queueNominationProcessImport()
    {
        $queueLength = Queue::size('nomination_process');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'nomination_process',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);
            Session::invalidate();
            return response()->json(['message' => 'Queue Nomination Process command executed successfully',  'exit_code' => $exitCode]);
        } else {
            Session::invalidate();
            return response()->json(['message' => 'No jobs in the Nomination Process Import queue to process', 'exit_code' => 0]);
        }
    }
    public function queueTrainingScheduleImport()
    {
        $queueLength = Queue::size('training_schedule');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'training_schedule',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);
            Session::invalidate();
            return response()->json(['message' => 'Queue Training Schedule command executed successfully',  'exit_code' => $exitCode]);
        } else {
            Session::invalidate();
            return response()->json(['message' => 'No jobs in the Training Schedule Import queue to process', 'exit_code' => 0]);
        }
    }
    public function queueTrainingMatrixImport()
    {
        $queueLength = Queue::size('training_matrix');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'training_matrix',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);
            Session::invalidate();
            return response()->json(['message' => 'Queue Training Matrix command executed successfully',  'exit_code' => $exitCode]);
        } else {
            Session::invalidate();
            return response()->json(['message' => 'No jobs in the Training Matrix Import queue to process', 'exit_code' => 0]);
        }
    }
    public function queueVenueImport()
    {
        $queueLength = Queue::size('venue');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'venue',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);
            Session::invalidate();
            return response()->json(['message' => 'Queue Venue command executed successfully',  'exit_code' => $exitCode]);
        } else {
            Session::invalidate();
            return response()->json(['message' => 'No jobs in the Venue Import queue to process', 'exit_code' => 0]);
        }
    }
    public function queueTopicImport()
    {
        $queueLength = Queue::size('topic');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'topic',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);
            Session::invalidate();
            return response()->json(['message' => 'Queue Topic command executed successfully',  'exit_code' => $exitCode]);
        } else {
            Session::invalidate();
            return response()->json(['message' => 'No jobs in the Topic Import queue to process', 'exit_code' => 0]);
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
