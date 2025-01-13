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
    public function getDepartment(Request $request)
    {
        try {
            $chartData = $this->training_schedule->getDepartmentData();
            $departmentDetails = $this->department->select('department_name', 'id')->get();

            $chartDataArray = [];

            // Initialize the chart data array with 0 counts
            foreach ($departmentDetails as $category) {
                $chartDataArray[$category->safety_category] = 0;
            }

            // Fill the chart data array with actual counts
            foreach ($chartData as $data) {
                $chartDataArray[$data->safety_category] = $data->count;
            }

            $data = [
                'getdashdata' => $request,
                'departmentDetails' => $departmentDetails,
                'chartDataArray' => $chartDataArray
            ];

            return view('training_dashboard.departmentData', $data);
        } catch (\Exception $ex) {
            dd($ex);
        }
    }
}
