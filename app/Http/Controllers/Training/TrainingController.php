<?php

namespace App\Http\Controllers\Training;

use App\Http\Controllers\Controller;

use DB;
use Exception;
use App\Models\Master\TrainingSchedule;
use App\Models\Master\Department;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

use App\Models\User;

class TrainingController extends Controller
{


    private $user;
    private $training_feedback;
    private $uploadlog;
    private $department;
    private $employee;
    private $topic;
    private $venue;
    private $training_attendance;
    private $training_assessment_feedback;
    private $training_schedule;
    private $unit;
    private $nomination_process;
    private $training_statuslog;



    public function __construct()
    {

        $this->training_schedule = new TrainingSchedule();
        $this->department = new Department();
    }

    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $data = [];

            if (Auth::user()->role == ROLE_SUPERADMIN || Auth::user()->role == ROLE_ADMIN) {
                return view('training_dashboard.dashboard', $data);
            } else {
                return view('admin.userdashboard', $data);
            }
        }
    }
    public function getTrainingStatus()
    {
        if (Auth::check()) {

            $request = request();

            $params = [
                // 'factory_ids' => $request->Factory ? arrayDecrypt($request->Factory) : [],
                'from_date' => $request->Fromdate ?? null,
                'to_date' => $request->Todate ?? null,
            ];
            $trainingStatus = [
                [
                    'name' => 'Total Training',
                    'count' => trainingStatusCount('', $params),
                    'icon' => 'bx bx-message-square-detail',
                    'icon_color' => 'text-primary',
                    'url' => admin_url('training_schedule/list/'),
                ],
                [
                    'name' => 'Training Pending',
                    'count' => trainingStatusCount([1, 2, 4, 5], $params),
                    'icon' => 'bx bx-file-find',
                    'icon_color' => 'text-primary',
                    'url' => admin_url('training_schedule/list/' . encryptId(1)),
                ],
                [
                    'name' => 'Training Rejected',
                    'count' => trainingStatusCount(3, $params),
                    'icon' => 'bx bx-message-square-edit',
                    'icon_color' => 'text-info',
                    'url' => admin_url('training_schedule/list/' . encryptId(2)),
                ],
                [
                    'name' => 'Training in Progress',
                    'count' => trainingStatusCount([6, 7], $params),
                    'icon' => 'bx bx-x-circle',
                    'icon_color' => 'text-danger',
                    'url' => admin_url('training_schedule/list/' . encryptId(3)),
                ],
                [
                    'name' => 'Training Completed',
                    'count' => trainingStatusCount(8, $params),
                    'icon' => 'bx bx-message-square-check',
                    'icon_color' => 'text-success',
                    'url' => admin_url('training_schedule/list/' . encryptId(4)),
                ],
            ];


            $data = [
                'trainingStatus' => $trainingStatus,
            ];

            return json_encode($data);
        }
    }

    public function gettrainingStatusCount(Request $request)
    {
        try {
            $chartData = $this->training_schedule->getTrainingCount();
    
            // Prepare data for the pie chart
            $chartDataArray = [
                'Pending' => $chartData->pending_count ?? 0,
                'Rejected' => $chartData->rejected_count ?? 0,
                'In Progress' => $chartData->inprogress_count ?? 0,
                'Completed' => $chartData->completed_count ?? 0,
            ];
            $data = [
                'getdashdata' => $request,
                'chartDataArray' => $chartDataArray
            ];
            return view('training_dashboard.trainingstatusCount', $data);
        } catch (\Exception $ex) {
            dd($ex); // Debug any errors during execution
        }
    }
    

    public function getDepartment(Request $request)
    {
        try {
            $chartData = $this->training_schedule->getDepartmentData();
            $departmentDetails = $this->department->select('department_name', 'id')->get();

            // Initialize chartDataArray with all departments having count 0
            $chartDataArray = $departmentDetails->pluck('id', 'department_name')->mapWithKeys(function ($value, $key) {
                return [$key => 0];
            });

            // Fill chartDataArray with actual counts from the query
            foreach ($chartData as $data) {
                if (isset($chartDataArray[$data->department_name])) {
                    $chartDataArray[$data->department_name] = $data->count;
                }
            }

            // Use Laravel's filter() to remove departments with count 0
            $chartDataArray = $chartDataArray->filter(function ($count) {
                return $count > 0;
            });

            $data = [
                'getdashdata' => $request,
                'departmentDetails' => $departmentDetails,
                'chartDataArray' => $chartDataArray
            ];

            return view('training_dashboard.departmentData', $data);
        } catch (\Exception $ex) {
            dd($ex); // Debug any errors during execution
        }
    }

    public function getmonthwiseTraining(Request $request)
    {
        try {
            // Fetch chart data
            $chartData = $this->training_schedule->monthwiseTrainingCountData();

            // Initialize count arrays for each month
            $overallCounts = array_fill(1, 12, 0);
            $pendingCounts = array_fill(1, 12, 0);
            $rejectedCounts = array_fill(1, 12, 0);
            $inProgressCounts = array_fill(1, 12, 0);
            $completedCounts = array_fill(1, 12, 0);

            // Populate counts based on fetched data
            foreach ($chartData as $data) {
                $overallCounts[$data->month] = $data->total_count;
                $pendingCounts[$data->month] = $data->pending_count;
                $rejectedCounts[$data->month] = $data->rejected_count;
                $inProgressCounts[$data->month] = $data->inprogress_count;
                $completedCounts[$data->month] = $data->completed_count;
            }

            // Prepare chart data array
            $chartDataArray = [];
            $months = [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December'
            ];

            foreach ($months as $monthIndex => $monthName) {
                $chartDataArray[$monthName] = [
                    'pending' => $pendingCounts[$monthIndex + 1],
                    'rejected' => $rejectedCounts[$monthIndex + 1],
                    'in_progress' => $inProgressCounts[$monthIndex + 1],
                    'completed' => $completedCounts[$monthIndex + 1]
                ];
            }

            return view('training_dashboard.monthwisetraining', [
                'getdashdata' => $request,
                'chartDataArray' => $chartDataArray
            ]);
        } catch (\Exception $ex) {
            report($ex);
            return back()->with('error', 'Failed to load month-wise Training data.');
        }
    }
}
