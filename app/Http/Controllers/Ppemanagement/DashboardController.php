<?php

namespace App\Http\Controllers\Ppemanagement;

use App\Http\Controllers\Controller;
use App\Models\Master\PpeExemption;
use App\Models\Master\PpeRequest;
use App\Models\Master\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private $ppeexemption;
    private $pperequest;

    private $unit;

    public function __construct()
    {
        $this->ppeexemption = new PpeExemption();
        $this->pperequest = new PpeRequest();

        $this->unit = new Unit();
    }

    public function login()
    {
        $user = Auth::user();


        if (!$user) {
            return redirect()->route('login');
        }

        $unitIds = $this->unit->getunit();
        $ppeexemptionUnits = $this->ppeexemption->getExemptionUnit($unitIds);
        $ppeRequestUnits = $this->pperequest->getPperequestUnit($unitIds);


        $data = [
            'exemptionUnits' => $ppeexemptionUnits,
            'unitList' =>  $unitIds,
        ];

        if ($user->role == ROLE_SUPERADMIN) {
            return view('ppemanagement.dashboard.dashboard', $data);
        }
    }

    public function ppeexemptiondata(Request $request)
    {
        $units = $this->unit->getunit();
        $fromDate = $request->input('Fromdate');
        $toDate = $request->input('Todate');
        $unitName = $request->input('unit');

        try {
            $chartData = $this->ppeexemption->getExemptionChartData($units, $fromDate, $toDate, $unitName);
            return view('ppemanagement.dashboard.unit_wise_ppeexemption', compact('chartData'));
        } catch (\Exception $ex) {
            Log::error('Error loading PPE exemption data: ' . $ex->getMessage());
            return response()->json([
                'error' => 'Unable to load PPE exemption data. ' . $ex->getMessage(),
            ], 500);
        }
    }

    public function ppeRequestList(Request $request)
    {
        $units = $this->unit->getunit();
        $fromDate = $request->input('Fromdate');
        $toDate = $request->input('Todate');
        $unitName = $request->input('unit');

        try {
            $chartData = $this->pperequest->getRequestChartData($units, $fromDate, $toDate, $unitName);
            return view('ppemanagement.dashboard.unit_wise_pperequest', compact('chartData'));
        } catch (\Exception $ex) {
            dd($ex);
            Log::error('Error loading PPE request data: ' . $ex->getMessage());
            return response()->json([
                'error' => 'Unable to load PPE request data. ' . $ex->getMessage(),
            ], 500);
        }
    }

    public function getshoerequeststatus(){
        if (Auth::check()) {

            $request = request();

            $params = [

                'from_date' => $request->Fromdate ?? null,
                'to_date' => $request->Todate ?? null,
                'unit'=>$request->unit?? null,
            ];

            $shoeRequestStatus = [
                [
                    'name' => 'Total Shoe Request',
                    'count' => ShoerequestStatusCount('', $params),
                    'icon' => 'bx bx-message-square-detail',
                    'icon_color' => 'text-primary',
                    'url' => admin_url('ppe_request/list/'),
                ],
                [
                    'name' => 'Pending Shoe Request',
                    'count' => ShoerequestStatusCount([1,4], $params),
                    'icon' => 'bx bx-file-find',
                    'icon_color' => 'text-primary',
                    'url' => admin_url('ppe_request/list/' . encryptId(1)),
                ],
                [
                    'name' => 'Approved Shoe Request',
                    'count' => ShoerequestStatusCount(8, $params),
                    'icon' => 'bx bx-message-square-edit',
                    'icon_color' => 'text-info',
                    'url' => admin_url('ppe_request/list/' . encryptId(2)),
                ],
                [
                    'name' => 'Rejected Shoe Request',
                    'count' => ShoerequestStatusCount([3, 6], $params),
                    'icon' => 'bx bx-x-circle',
                    'icon_color' => 'text-danger',
                    'url' => admin_url('ppe_request/list/' . encryptId(3)),
                ],

            ];


            $data = [
                'approve_status' => $shoeRequestStatus,
            ];

            return json_encode($data);
        }
    }

    public function getExemptionstatus(){
        if (Auth::check()) {

            $request = request();

            $params = [

                'from_date' => $request->Fromdate ?? null,
                'to_date' => $request->Todate ?? null,
                'unit'=>$request->unit?? null,

            ];
            $shoeExemptionStatus = [
                [
                    'name' => 'Total Exemption ',
                    'count' => ShoeExemptionStatusCount('', $params),
                    'icon' => 'bx bx-message-square-detail',
                    'icon_color' => 'text-primary',
                    'url' => admin_url('ppe_exemption/list/'),
                ],
                [
                    'name' => 'Pending Shoe Exemption',
                    'count' => ShoeExemptionStatusCount(4, $params),
                    'icon' => 'bx bx-file-find',
                    'icon_color' => 'text-primary',
                    'url' => admin_url('ppe_exemption/list/' . encryptId(1)),
                ],
                [
                    'name' => 'Approved Shoe Request',
                    'count' => ShoeExemptionStatusCount(5, $params),
                    'icon' => 'bx bx-message-square-edit',
                    'icon_color' => 'text-info',
                    'url' => admin_url('ppe_exemption/list/' . encryptId(2)),
                ],
                [
                    'name' => 'Rejected Shoe Request',
                    'count' => ShoeExemptionStatusCount(6, $params),
                    'icon' => 'bx bx-x-circle',
                    'icon_color' => 'text-danger',
                    'url' => admin_url('ppe_exemption/list/' . encryptId(3)),
                ],

            ];


            $data = [
                'approve_status' => $shoeExemptionStatus,
            ];

            return json_encode($data);
        }
    }

    public function getmonthwiseRequest(Request $request)
    {
        try {

            $chartData = $this->pperequest->monthwiserequest();


            $overallCounts = array_fill(1, 12, 0);
            $pendingCounts = array_fill(1, 12, 0);
            $rejectedCounts = array_fill(1, 12, 0);

            $completedCounts = array_fill(1, 12, 0);


            foreach ($chartData as $data) {
                $overallCounts[$data->month] = $data->total_count;
                $pendingCounts[$data->month] = $data->pending_count;
                $rejectedCounts[$data->month] = $data->rejected_count;

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
                    'completed' => $completedCounts[$monthIndex + 1]
                ];
            }

            return view('ppemanagement.dashboard.month_wise_request', [
                'getdashdata' => $request,
                'chartDataArray' => $chartDataArray
            ]);
        } catch (\Exception $ex) {
            report($ex);
            return back()->with('error', 'Failed to load month-wise Request data.');
        }
    }

    public function getmonthwiseExemption(Request $request)
    {
        try {
            // Fetch chart data
            $chartData = $this->ppeexemption->monthwiseexemption();

            // Initialize count arrays for each month
            $overallCounts = array_fill(1, 12, 0);
            $pendingCounts = array_fill(1, 12, 0);
            $rejectedCounts = array_fill(1, 12, 0);
            $completedCounts = array_fill(1, 12, 0);

            // Populate counts based on fetched data
            foreach ($chartData as $data) {
                $overallCounts[$data->month] = $data->total_count;
                $pendingCounts[$data->month] = $data->pending_count;
                $rejectedCounts[$data->month] = $data->rejected_count;
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
                    'completed' => $completedCounts[$monthIndex + 1]
                ];
            }

            return view('ppemanagement.dashboard.month_wise_exemption', [
                'getdashdata' => $request,
                'chartDataArray' => $chartDataArray
            ]);
        } catch (\Exception $ex) {
            report($ex);
            return back()->with('error', 'Failed to load month-wise Exemption.');
        }
    }

}
