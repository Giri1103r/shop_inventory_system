<?php

namespace App\Http\Controllers\Cron;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OhcManagement\MedicineReceivingController;
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
use App\Models\Master\PpeExemption;
use App\Mail\EmployeeRegisterEmail;
use App\Mail\Ohc\MedicineRequestEmail;
use App\Mail\Ohc\MedicineStockEmail;
use App\Mail\Ohc\MedicineStockRequestEmail;
use App\Mail\PermitExpiryEmail;
use App\Mail\SafetyPermitEmail;
use App\Models\Master\Company;
use App\Models\Master\PpeTypeMaster;
use App\Models\OhcManagement\Master\Medicine;
use App\Models\OhcManagement\MedicineReceiving;
use App\Models\OhcManagement\MedicineStock;
use App\Models\OhcManagement\Report\Inventory;
use App\Models\Permit\Statuslog;
use App\Models\WorkerCompanyDetails;
use Exception;
use Illuminate\Support\Facades\Auth;

class CronController extends Controller
{

    private $worktemp;
    private $emp_temp;
    private $work;
    private $employee;
    private $company;
    private $user;
    private $ppestock;
    private $ppeexemption;
    private $safetypermit;
    private $statuslog;
    private $ppetypemaster;
    private $medicine;
    private $medicine_stock;
    private $medicine_receiving;
    private $worker_details;


    public function __construct()
    {
        $this->safetypermit = new SafetyPermit();
        $this->worktemp = new Worktemp();
        $this->emp_temp = new EmployeeTemp();
        $this->work = new Work();
        $this->employee = new Employee();
        $this->user = new User();
        $this->ppestock = new PpeStockinventory();
        $this->statuslog = new Statuslog();
        $this->ppeexemption = new PpeExemption();
        $this->ppetypemaster = new PpeTypeMaster();
        $this->medicine = new Medicine();
        $this->medicine_stock = new MedicineStock();
        $this->medicine_receiving = new MedicineReceiving();
        $this->worker_details = new WorkerCompanyDetails();
        $this->company = new Company();
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


    public function workMasterAllDetailsTemp()
    {
        try {
            $fromDate = '2001-01-01';
            $toDate = todayDbdate();

            $office_id = $this->company->getcompany();
            $responses = [];
            $errors = [];

            try {
                foreach ($office_id as $company) {
                    $officeName = $company->company_name;
                    $apiUrl = "https://vmsapi.karam.in/emp.asmx/GetWorkerDetails?TokenId=123&OfficeId={$officeName}&fromDate={$fromDate}&toDate={$toDate}";

                    Log::info("Fetching data from API for: {$officeName}");

                    $response = Http::get($apiUrl);

                    if ($response->successful()) {
                        $data = $response->json();

                        if (!empty($data)) {
                            $this->worktemp->store($data);
                            $responses[] = "Data saved successfully for {$officeName}";
                        } else {
                            $responses[] = "No data for {$officeName}";
                        }
                    } else {
                        $errors[] = "API failed for {$officeName}";
                    }
                }


                return response()->json([
                    'message' => 'Processing completed.',
                    'results' => $responses,
                    'errors' => $errors
                ]);
            } catch (Exception $ex) {
                report($ex);
                return response()->json(['message' => 'An error occurred.', 'error' => $ex->getMessage()]);
            }
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['message' => 'An error occurred.', 'error' => $ex->getMessage()]);
        }
    }


    public function workMasterTemp()
    {
        try {
            $fromDate = now()->subDay()->format('Y-m-d');
            $toDate = now()->subDay()->format('Y-m-d');

            $office_id = $this->company->getcompany();
            foreach ($office_id as $company) {
                $apiUrl = "https://vmsapi.karam.in/emp.asmx/GetWorkerDetails?TokenId=123&OfficeId={$company->company_name}&fromDate={$fromDate}&toDate={$toDate}";


                $response = Http::get($apiUrl);

                if ($response->successful()) {
                    $data = $response->json();

                    if (!empty($data)) {
                        $work = $this->worktemp->store($data);
                        return response()->json(['message' => 'Data saved successfully.']);
                    } else {
                        return response()->json(['message' => 'No data found in API response.']);
                    }
                }
            }
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['message' => 'An error occurred.', 'error' => $ex->getMessage()]);
        }
    }
    public function workMasterTempCustom(Request $request)
    {
        try {

            $fromDate = $request->fromdate;
            $toDate = $request->todate;
            $office_id = $this->company->getcompany();
            foreach ($office_id as $company) {
                $apiUrl = "https://vmsapi.karam.in/emp.asmx/GetWorkerDetails?TokenId=123&OfficeId={$company->company_name}&fromDate={$fromDate}&toDate={$toDate}";


                $response = Http::get($apiUrl);

                if ($response->successful()) {
                    $data = $response->json();

                    if (!empty($data)) {
                        $work = $this->worktemp->store($data);
                        return response()->json(['message' => 'Data saved successfully.']);
                    } else {
                        return response()->json(['message' => 'No data found in API response.']);
                    }
                }
            }
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['message' => 'An error occurred.', 'error' => $ex->getMessage()]);
        }
    }
    public function workSave()
    {

        try {
            $worktemp = Worktemp::select('*')->where('status', 1)->where('upload_status', 0)->where('error_status', '0')->get();

            if (!empty($worktemp)) {

                $work = $this->work->store($worktemp);
                if (!empty($work)) {
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
            report($ex);
            return response()->json(['message' => 'An error occurred.', 'error' => $ex->getMessage()]);
        }
    }
    public function employeeMasterTemp()
    {
        try {

            // $fromDate = todayDbdate();
            // $toDate = todayDbdate();
            $fromDate = now()->subDay()->format('Y-m-d');
            $toDate = now()->subDay()->format('Y-m-d');

            $apiKeyTokens = $this->company->getApiKeyToken();

            $responses = [];

            foreach ($apiKeyTokens as $token) {
                $apiUrl = "https://hrms.esparsh.in/PunchesAPI/api/Attendance/GetEmployeeDetails?token={$token->api_token_key}&fromDate={$fromDate}&toDate={$toDate}";

                $response = Http::get($apiUrl);

                if ($response->successful()) {
                    $data = $response->json();

                    if (!empty($data['Result'])) {
                        $this->emp_temp->store($data);
                        $responses[] = [
                            'message' => 'Data saved successfully.',
                            'token' => $token->api_token_key
                        ];
                    } else {
                        $responses[] = [
                            'message' => 'No data found in API response.',
                            'token' => $token->api_token_key
                        ];
                    }
                } else {
                    $responses[] = [
                        'message' => 'Failed to fetch data from API.',
                        'status' => $response->status(),
                        'token' => $token->api_token_key
                    ];
                }
            }


            return response()->json($responses);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['message' => 'An error occurred.', 'error' => $ex->getMessage()]);
        }
    }
    public function employeeMasterTempCustom(Request $request)
    {
        try {

            $fromDate = $request->fromdate;
            $toDate = $request->todate;
            $apiKeyTokens = $this->company->getApiKeyToken();

            $responses = [];

            foreach ($apiKeyTokens as $token) {
                $apiUrl = "https://hrms.esparsh.in/PunchesAPI/api/Attendance/GetEmployeeDetails?token={$token->api_token_key}&fromDate={$fromDate}&toDate={$toDate}";

                $response = Http::get($apiUrl);

                if ($response->successful()) {
                    $data = $response->json();

                    if (!empty($data['Result'])) {
                        $this->emp_temp->store($data);
                        $responses[] = [
                            'message' => 'Data saved successfully.',
                            'token' => $token->api_token_key
                        ];
                    } else {
                        $responses[] = [
                            'message' => 'No data found in API response.',
                            'token' => $token->api_token_key
                        ];
                    }
                } else {
                    $responses[] = [
                        'message' => 'Failed to fetch data from API.',
                        'status' => $response->status(),
                        'token' => $token->api_token_key
                    ];
                }
            }


            return response()->json($responses);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['message' => 'An error occurred.', 'error' => $ex->getMessage()]);
        }
    }
    public function employeeMasterTempCustomToken(Request $request)
    {
        try {

            $fromDate = $request->fromdate;
            $toDate = $request->todate;
            $customtoken = "KSCAroz4HhR1EIx8qaz3C13z/quTXBkQ3Q5hj7Qx3aA*";
            $apiKeyTokens = $this->company->getApiKeyToken();

            $responses = [];

            foreach ($apiKeyTokens as $token) {
                $apiUrl = "https://hrms.esparsh.in/PunchesAPI/api/Attendance/GetEmployeeDetails?token={$customtoken}&fromDate={$fromDate}&toDate={$toDate}";

                $response = Http::get($apiUrl);

                if ($response->successful()) {
                    $data = $response->json();

                    if (!empty($data['Result'])) {
                        $this->emp_temp->store($data);
                        $responses[] = [
                            'message' => 'Data saved successfully.',
                            'token' => $token->api_token_key
                        ];
                    } else {
                        $responses[] = [
                            'message' => 'No data found in API response.',
                            'token' => $token->api_token_key
                        ];
                    }
                } else {
                    $responses[] = [
                        'message' => 'Failed to fetch data from API.',
                        'status' => $response->status(),
                        'token' => $token->api_token_key
                    ];
                }
            }


            return response()->json($responses);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['message' => 'An error occurred.', 'error' => $ex->getMessage()]);
        }
    }
    public function employeeMasterTempAllDetails(Request $request)
    {
        try {
            $fromDate = '2001-01-01';
            $toDate = todayDbdate();
            $apiKeyTokens = $this->company->getApiKeyToken();
            $responses = [];

            foreach ($apiKeyTokens as $token) {
                $apiUrl = "https://hrms.esparsh.in/PunchesAPI/api/Attendance/GetEmployeeDetails?token={$token->api_token_key}&fromDate={$fromDate}&toDate={$toDate}";

                $response = Http::get($apiUrl);

                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data['Result'])) {
                        $this->emp_temp->store($data);
                        $responses[] = [
                            'message' => 'Data saved successfully.',
                            'token' => $token->api_token_key
                        ];
                    } else {
                        $responses[] = [
                            'message' => 'No data found in API response.',
                            'token' => $token->api_token_key
                        ];
                    }
                } else {
                    $responses[] = [
                        'message' => 'Failed to fetch data from API.',
                        'status' => $response->status(),
                        'token' => $token->api_token_key
                    ];
                }
            }


            return response()->json($responses);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['message' => 'An error occurred.', 'error' => $ex->getMessage()]);
        }
    }


    // public function ExpireExemption()
    // {
    //     try {
    //         $status = $this->ppeexemption->getExpirestatus();
    //         return response()->json(['message' => 'Data saved successfully.']);
    //     } catch (Exception $ex) {
    //         report($ex);
    //         return response()->json(['message' => 'An error occurred.', 'error' => $ex->getMessage()]);
    //     }
    // }


    public function EmployeeSave()
    {

        try {
            $emp_temp = EmployeeTemp::select('*')->where('upload_status', '0')->where('error_status', '0')->where('status', 1)->get();
            if (!empty($emp_temp)) {

                $employee = $this->employee->store($emp_temp);
                $users = $this->user->store($employee);
                // $allowedEmails = [
                //     'keshav.kashyap@karam.in',
                //     'vinay.kumar@karam.in',
                //     'prashant.singh2@karam.in'
                // ];

                foreach ($users as $user) {

                    // if (!empty($user['created_at']) && !empty($user) && isset($user['email'])) {
                    //     Mail::to($user['email'])->queue(new EmployeeRegisterEmail($user));
                    // }

                    // if (!empty($user['created_at']) && !empty($user) && in_array($user['email'], $allowedEmails)) {
                    //     Mail::to($user['email'])->queue(new EmployeeRegisterEmail($user));
                    // }
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


                if (!empty($employee)) {
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
            report($ex);
            return response()->json(['message' => 'An error occurred.', 'error' => $ex->getMessage()]);
        }
    }


    public function storeItem()
    {
        try {
            $itemcodes = $this->ppetypemaster->getppetypemaster();

            foreach ($itemcodes as $itemCode) {
                $apiUrl = "https://vmsapi.karam.in/emp.asmx/GetPPEInventory?TokenId=123&Orgid=86&Item={$itemCode}";

                $response = Http::get($apiUrl);

                if ($response->successful()) {
                    $data = $response->json();

                    if (!empty($data)) {
                        foreach ($data as $item) {
                            try {
                                $this->ppestock->store($item);
                            } catch (\Exception $e) {
                                Log::error('Failed to store item: ', [
                                    'item' => $item,
                                    'error' => $e->getMessage()
                                ]);
                            }
                        }
                    }
                } else {
                    return response()->json([
                        'message' => 'Failed to fetch data from API.',
                        'status' => $response->status(),
                    ]);
                }
            }

            return response()->json(['message' => 'Data saved successfully.']);
        } catch (Exception $ex) {
            report($ex);
            return response()->json([
                'message' => 'An error occurred.',
                'error' => $ex->getMessage(),
            ]);
        }
    }


    public function stockrequest()
    {
        try {
            $medicinestock = Inventory::whereColumn('balance', '<', 'threshold_limit')
                ->where('status', 1)
                ->where('unit_id', 1)
                ->get();

            $ids = $medicinestock->pluck('medicine_id')->toArray();


            $receivedMedicineIds = MedicineReceiving::whereIn('medicine_id', $ids)->orderByDesc('id')->pluck('medicine_id')->toArray();
            $filteredIds = array_diff($ids, $receivedMedicineIds);

            $mailsubject = 'Medicine Stock Request';
            $user_role = ROLE_PARAMEDICS;
            $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();

            if ($users->isNotEmpty() && !empty($filteredIds)) {
                foreach ($users as $user) {
                    $email_id = $user->email;

                    if (!empty($email_id)) {
                        foreach ($filteredIds as $medicineId) {
                            $medicinedetails = $this->medicine->selectone($medicineId);
                            $dataArray = Inventory::where('unit_id', 1)->where('medicine_id', $medicineId)->first();

                            if ($dataArray) {
                                $data = $dataArray->toArray();
                                $details  = $medicinedetails->toArray();

                                $details['name'] = $user->name;
                                $details['email_id'] =  $email_id;
                                $details['mail_subject'] = $mailsubject;

                                Mail::to($details['email_id'])->queue(new MedicineStockEmail($details, $data));
                            }
                        }
                    }
                }
            }

            $notificationData = array(
                'notification_type' => 4,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => 'Medicine has less than the Threshold Limit',
                    'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $medicineId ?? null,
                    'module' => 1,
                )),
                'web_link' => admin_url('ohc/inventory-tabular-view/list'),
                'assigned_user' => array_to_string([$user->id]),
                'created_by' => Auth::id(),
            );

            notificationSave($notificationData);
            return response()->json(['message' => 'Request sent successfully.']);
        } catch (Exception $ex) {
            report($ex);
            return response()->json([
                'message' => 'An error occurred.',
                'error' => $ex->getMessage(),
            ]);
        }
    }


    public function stockupdate()
    {
        try {
            $eighthoursAhead = Carbon::now()->addHour(8);
            $data = MedicineReceiving::where('approve_status', STATUS_OHC_OPEN)
                ->where('status', 1)
                ->where('cron_time', '<', $eighthoursAhead->toTimeString())
                ->get();


            if ($data->isEmpty()) {
                return response()->json(['message' => 'No pending stock updates.']);
            }

            $ehsofficer = $this->user->findEhsofficer();
            $ehsEmail = $ehsofficer->pluck('email')->toArray();
            $EhsId = $this->user->assigneduser($ehsofficer);

            $ehshead = $this->user->findEhsHead();
            $ehsHeadEmail = $ehshead->pluck('email')->toArray();
            $EhsHeadId = $ehshead->pluck('id')->toArray();

            $message = 'Stock Update for the Medicine';

            foreach ($data as $medicine) {
                $medicineDetails = $this->medicine_receiving->SelectOne($medicine->id);

                $crontime = $this->medicine_receiving->update(['cron_time' => Carbon::now()]);
                $medicineDetails['mail_subject'] = "Stock Update Alert";
                $medicineDetails['medicine_name'] = getMedicinename($medicine->medicine_id);


                $recipients = array_merge($ehsEmail, $ehsHeadEmail);
                if (!empty($recipients)) {
                    Mail::to($recipients)->queue(new MedicineStockRequestEmail($medicineDetails));
                }



                $notificationData = [
                    'notification_type' => 4,
                    'module_type' => 1,
                    'notification_message' => $message,
                    'mobile_notification' => json_encode([
                        'title' => $message,
                        'message' => "{$medicineDetails['medicine_name']} has not been updated. The status is still open.",
                        'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                        'module' => 1,
                        'style' => 'font-size: 1rem;'
                    ]),
                    'web_link' => url('ohc/medicine-receiving-form/list'),
                    'assigned_user' => array_to_string(array_merge($EhsId, $EhsHeadId)),
                    'created_by' => 1,
                ];

                notificationSave($notificationData);
            }

            return response()->json(['message' => 'Emails and notifications sent successfully.']);
        } catch (Exception $ex) {
            return response()->json([
                'message' => 'An error occurred.',
                'error' => $ex->getMessage(),
            ]);
        }
    }
    public function prevoiusmonthstock()
    {
        try {

            $yesterday = Carbon::yesterday();


            $lastDayOfMonth = $yesterday->copy()->endOfMonth()->toDateString();

            if ($yesterday->toDateString() === $lastDayOfMonth) {

                Inventory::query()->update([
                    'previous_month_total' => DB::raw('balance')
                ]);
            }

            return response()->json(['message' => 'Previous month balance updated']);
        } catch (Exception $ex) {
            return response()->json([
                'message' => 'An error occurred.',
                'error' => $ex->getMessage(),
            ]);
        }
    }


    public function permitExpiry()
    {
        try {
            $currentTime = Carbon::now()->format('H:i:s');
            $permits = SafetyPermit::where('trash', 'NO')
                ->where('permit_status', '!=', STATUS_CLOSED)
                ->where('permit_status', '!=', STATUS_PERMIT_EXPIRED)
                ->whereDate('to_date', Carbon::today())
                ->where('time_to', '<', $currentTime)
                ->get();

            // Log::info("Fetched permits for expiry", ['count' => $permits->count(), 'permit_ids' => $permits->pluck('id')]);

            if ($permits->isNotEmpty()) {
                foreach ($permits as $permit) {
                    // Update permit status
                    Log::info("Processing permit", ['permit_id' => $permit->id]);
                    $permit->permit_status = STATUS_PERMIT_EXPIRED;
                    $permit->save();

                    // Add status log
                    $insert_array = [
                        'permit_type' => 1,
                        'permit_id' => $permit->id,
                        'from_status' => $permit->permit_status,
                        'to_status' => STATUS_PERMIT_EXPIRED,
                        'is_reject' => null,
                        'remarks' => 'Permit Expired',
                        'approved_by' => null,
                    ];
                    $this->statuslog->create($insert_array);

                    // Fetch assigned user
                    $assignedUser = User::where('id', $permit->created_by)
                        ->select('name', 'email')
                        ->first();

                    if ($assignedUser && $assignedUser->email) {
                        $mailsubject = 'Permit is Expired';
                        $safetypermitdetails = $this->safetypermit->selectmail($permit->id);

                        $permitrray = $safetypermitdetails->toArray();
                        $permitrray['name'] = $assignedUser->name;
                        $permitrray['email_id'] = $assignedUser->email;
                        $permitrray['mail_subject'] = $mailsubject;

                        Mail::to($permitrray['email_id'])->queue(new SafetyPermitEmail($permitrray));
                        Log::info("Email queued", ['email' => $assignedUser->email, 'permit_id' => $permit->id]);
                    }

                    // Send notification
                    $UserId = User::where('id', $permit->created_by)->pluck('id')->toArray();
                    $notificationData = [
                        'notification_type' => 3,
                        'module_type' => 1,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode([
                            'title' => $mailsubject,
                            'message' => 'Safety Permit is expired',
                            'icon' => admin_url('public/assets/icons/permit_to_work.png'),
                            'module' => 1,
                        ]),
                        'web_link' => admin_url('safetypermit/view/' . encryptId($permit->id)),
                        'assigned_user' => array_to_string($UserId),
                        'created_by' => 1,
                    ];
                    notificationSave($notificationData);
                    Log::info("Notification sent", ['permit_id' => $permit->id]);
                }

                // Log::info('Expired permits updated successfully.', ['count' => $permits->count()]);
                return response()->json(['message' => 'Expired permits updated successfully.']);
            } else {
                Log::info('No permits found for expiry update.');
                return response()->json(['message' => 'No expired permits found.']);
            }
        } catch (Exception $ex) {
            report($ex);
            Log::error('Error in permitExpiry cron job.', ['error' => $ex->getMessage()]);
            return response()->json(['message' => 'An error occurred.', 'error' => $ex->getMessage()]);
        }
    }



    public function permitClose()
    {
        try {
            // Log::info('PermitClose function started.');

            $currentTime = Carbon::now();
            $timeThirtyMinutesAhead = Carbon::now()->addMinutes(30);

            $permits = SafetyPermit::where('trash', 'NO')
                ->where('permit_status', '!=', STATUS_CLOSED)
                ->where('permit_status', '!=', STATUS_EHS_APPROVE_PENDING)
                ->whereDate('date', Carbon::today())
                ->whereTime('time_to', '>=', $currentTime->toTimeString())
                ->whereTime('time_to', '<=', $timeThirtyMinutesAhead->toTimeString())
                ->get();

            // Log::info('Fetched permits: ', ['count' => $permits->count()]);

            $mailsubject = 'Permit is going to expire in 30 minutes';

            foreach ($permits as $permit) {
                $assignedUser = User::where('id', $permit->created_by)
                    ->select('name', 'email')
                    ->first();

                if ($assignedUser && $assignedUser->email) {
                    $safetypermitdetails = $this->safetypermit->selectmail($permit->id);
                    $extensionLink = url('safetypermit/permitExtension/' . encryptId($permit->id));

                    $permitrray = $safetypermitdetails->toArray();

                    $permitrray['name'] = $assignedUser->name;
                    $permitrray['email_id'] = $assignedUser->email;
                    $permitrray['mail_subject'] = $mailsubject;
                    $permitrray['extension_link'] = $extensionLink;

                    Mail::to($permitrray['email_id'])->queue(new PermitExpiryEmail($permitrray));

                    $UserId = User::where('id', $permit->created_by)->pluck('id')->toArray();

                    $notificationData = array(
                        'notification_type' => 3,
                        'module_type' => 1,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode(array(
                            'title' => $mailsubject,
                            'message' => 'Permit is going to expire in 30 minutes',
                            'icon' => admin_url('public/assets/icons/permit_to_work.png'),
                            'module' => 1,
                        )),
                        'web_link' =>  admin_url('safetypermit/view/' . encryptId($permit->id)),
                        'assigned_user' => array_to_string($UserId),
                        'created_by' => 1,
                    );
                    notificationSave($notificationData);

                    // Log::info("Permit expiry email sent.", [
                    //     'email' => $assignedUser->email,
                    //     'permit_id' => $permit->permit_id,
                    // ]);
                } else {
                    Log::warning("No user or email found for permit.", ['permit_id' => $permit->permit_id]);
                }
            }
        } catch (Exception $ex) {

            report($ex);
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


    public function queueProtectiveequipmentmasterImport()
    {
        $queueLength = Queue::size('proteciveequipimport');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'proteciveequipimport',
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

    public function queueEquipinvolvemasterImport()
    {
        $queueLength = Queue::size('equipinvalveimport');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'equipinvalveimport',
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

    public function queueSafeworkmasterImport()
    {
        $queueLength = Queue::size('safeworkimport');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'safeworkimport',
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

    public function queuePrecautionmasterImport()
    {
        $queueLength = Queue::size('precautionimport');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'precautionimport',
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
    public function currentNextCodeImport()
    {
        $queueLength = Queue::size('currentNextCodeImport');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'currentNextCodeImport',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);
            Session::invalidate();
            return response()->json(['message' => 'Queue Current Nxt Code Dailing command executed successfully',  'exit_code' => $exitCode]);
        } else {
            Session::invalidate();
            return response()->json(['message' => 'No jobs in the Current Nxt Code Dailing Import queue to process', 'exit_code' => 0]);
        }
    }
    public function equipmentimport()
    {
        $queueLength = Queue::size('equipmentimport');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'equipmentimport',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);
            Session::invalidate();
            return response()->json(['message' => 'Queue  Safety Equipment Master command executed successfully',  'exit_code' => $exitCode]);
        } else {
            Session::invalidate();
            return response()->json(['message' => 'No jobs in the  Safety Equipment Master Import queue to process', 'exit_code' => 0]);
        }
    }
    public function firstAidEquipmentImport()
    {
        $queueLength = Queue::size('firstAidEquipmentImport');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'firstAidEquipmentImport',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);
            Session::invalidate();
            return response()->json(['message' => 'Queue  OHC Inspection Master command executed successfully',  'exit_code' => $exitCode]);
        } else {
            Session::invalidate();
            return response()->json(['message' => 'No jobs in the OHC Inspection Master Import queue to process', 'exit_code' => 0]);
        }
    }
    public function task()
    {
        $queueLength = Queue::size('task');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'task',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);
            Session::invalidate();
            return response()->json(['message' => 'Queue  OHC Inspection Task Master command executed successfully',  'exit_code' => $exitCode]);
        } else {
            Session::invalidate();
            return response()->json(['message' => 'No jobs in the OHC Inspection Task Master Import queue to process', 'exit_code' => 0]);
        }
    }
    public function queueChecklistmasterImport()
    {
        $queueLength = Queue::size('checklistimport');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'checklistimport',
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

    public function queueMedicineIsuuanceImport()
    {
        $queueLength = Queue::size('medicine_issuance');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'medicine_issuance',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);
            Session::invalidate();
            return response()->json(['message' => 'Queue Medicine Issuance command executed successfully',  'exit_code' => $exitCode]);
        } else {
            Session::invalidate();
            return response()->json(['message' => 'No jobs in the Medicine Issuance Import queue to process', 'exit_code' => 0]);
        }
    }
    public function queueMedicineRequisitionImport()
    {
        $queueLength = Queue::size('medicine_requisition');
        if ($queueLength > 0) {
            $options = [
                '--sleep' => 3,
                '--tries' => 3,
                '--queue' => 'medicine_requisition',
                '--timeout' => 600,
                '--max-jobs' => 10,
            ];

            $exitCode = Artisan::call('queue:work', $options);
            Session::invalidate();
            return response()->json(['message' => 'Queue Medicine Issuance command executed successfully',  'exit_code' => $exitCode]);
        } else {
            Session::invalidate();
            return response()->json(['message' => 'No jobs in the Medicine Issuance Import queue to process', 'exit_code' => 0]);
        }
    }
}
