<?php

namespace App\Http\Controllers\KPI;

use App\Http\Controllers\Controller;
use App\Models\IMS\Incident\IncidentBodyParts;
use App\Models\Master\Employee;
use DB;
use Exception;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\IMS\Incident\InitialIncident;
use Nette\Schema\DynamicParameter;

class KpiDashboardController extends Controller
{
    private $bodyparts;

    private $incident_ims;

    public function __construct()
    {
        $this->bodyparts = new IncidentBodyParts();
        $this->incident_ims = new InitialIncident();
    }


    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $data = [];
            return view('kpi.dashboard', $data);
        }
    }


    public function getTotalIncident(Request $request)
    {
        try {
            $chartData = $this->incident_ims->getTotalIncidentCountData($request);

            if ($chartData->isEmpty()) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }

            $formattedData = [];
            foreach ($chartData as $row) {
                $unit = $row->unit_name ?? 'Unknown Unit';
                $type = $row->incident_type_name ?? 'Unknown Type';

                if (!isset($formattedData[$type])) {
                    $formattedData[$type] = [];
                }

                if (!isset($formattedData[$type][$unit])) {
                    $formattedData[$type][$unit] = 0;
                }

                $formattedData[$type][$unit]++;
            }
            return view('kpi.totalIncidentsCount', [
                'formattedData' => $formattedData,
                'getdashdata' => $request,
            ]);
        } catch (\Exception $ex) {
            report($ex);
            return back()->with('error', 'Failed to load unit-wise incident data.');
        }
    }

    public function getIncidentTypeChart(Request $request)
    {
        try {
            $chartData = $this->incident_ims->getIncidentTypeCountData($request);

            if ($chartData->isEmpty()) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }

            // Format data for the chart
            $formattedData = [];
            foreach ($chartData as $row) {
                $type = $row->incident_type_name ?? 'Unknown Type';
                $formattedData[$type] = (int) $row->incident_count;
            }

            return view('kpi.incidentTypeconut', [ // Make sure your blade file name matches
                'formattedData' => $formattedData,
                'getdashdata' => $request,
            ]);
        } catch (\Exception $ex) {
            report($ex);
            return back()->with('error', 'Failed to load incident type chart data.');
        }
    }
    public function getHeatmapImsData(Request $request)
    {
        try {
            $chartData = $this->incident_ims->getHeatmapImsCountData($request);

            if ($chartData->isEmpty()) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }

            // Map injury codes to readable labels
            $injuryMap = [
                1 => 'Major',
                2 => 'Minor',
                3 => 'Fatal',
            ];

            // Initialize all month slots for each injury type
            $monthlyCounts = [];
            foreach (range(1, 12) as $month) {
                foreach ($injuryMap as $label) {
                    $monthlyCounts[$label][$month] = 0;
                }
            }

            // Populate the monthly counts from DB results
            foreach ($chartData as $row) {
                if (!isset($row->nature_of_injury) || !isset($injuryMap[$row->nature_of_injury])) {
                    continue; // Skip null or unknown injury types
                }

                $month = (int) $row->month;
                $type = $injuryMap[$row->nature_of_injury];
                $monthlyCounts[$type][$month] = (int) $row->incident_count;
            }

            // Format for ApexCharts
            $formattedData = [];
            foreach ($monthlyCounts as $type => $months) {
                $data = [];
                foreach ($months as $month => $count) {
                    $data[] = [
                        'x' => date('M', mktime(0, 0, 0, $month, 10)), // e.g. Jan, Feb
                        'y' => $count,
                    ];
                }

                $formattedData[] = [
                    'name' => $type,
                    'data' => $data,
                ];
            }

            return view('kpi.heatmapImsData', [
                'formattedData' => $formattedData,
                'getdashdata' => $request,
            ]);
        } catch (\Exception $ex) {
            report($ex);
            return back()->with('error', 'Failed to load heatmap incident data.');
        }
    }




    public function getChart1(Request $request)
    {
        try {

            $data = [
                'getdashdata' => $request,
            ];

            return view('kpi.chartData1', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function getChart2(Request $request)
    {
        try {

            $data = [
                'getdashdata' => $request,
            ];

            return view('kpi.chartData2', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function getChart3(Request $request)
    {
        try {

            $data = [
                'getdashdata' => $request,
            ];

            return view('kpi.chartData3', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function getChart4(Request $request)
    {
        try {

            $data = [
                'getdashdata' => $request,
            ];

            return view('kpi.chartData4', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function getChart5(Request $request)
    {
        try {

            $data = [
                'getdashdata' => $request,
            ];

            return view('kpi.chartData5', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function getChart6(Request $request)
    {
        try {

            $data = [
                'getdashdata' => $request,
            ];

            return view('kpi.chartData6', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function getChart7(Request $request)
    {
        try {

            $data = [
                'getdashdata' => $request,
            ];

            return view('kpi.chartData7', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function getChart8(Request $request)
    {
        try {

            $data = [
                'getdashdata' => $request,
            ];

            return view('kpi.chartData8', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function getChart9(Request $request)
    {
        try {

            $data = [
                'getdashdata' => $request,
            ];

            return view('kpi.chartData9', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function getChart10(Request $request)
    {
        try {

            $data = [
                'getdashdata' => $request,
            ];

            return view('kpi.chartData10', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function getChart11(Request $request)
    {
        try {

            $data = [
                'getdashdata' => $request,
            ];

            return view('kpi.chartData11', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function getChart12(Request $request)
    {
        try {

            $data = [
                'getdashdata' => $request,
            ];

            return view('kpi.chartData12', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function getChart13(Request $request)
    {
        try {

            $data = [
                'getdashdata' => $request,
            ];

            return view('kpi.chartData13', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function getChart14(Request $request)
    {
        try {

            $data = [
                'getdashdata' => $request,
            ];

            return view('kpi.chartData14', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function getChart15(Request $request)
    {
        try {

            $data = [
                'getdashdata' => $request,
            ];

            return view('kpi.chartData15', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function getChart16(Request $request)
    {
        try {

            $data = [
                'getdashdata' => $request,
            ];

            return view('kpi.chartData16', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function getChart17(Request $request)
    {
        try {

            $data = [
                'getdashdata' => $request,
            ];

            return view('kpi.chartData17', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function getChart18(Request $request)
    {
        try {

            $data = [
                'getdashdata' => $request,
            ];

            return view('kpi.chartData18', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function getChart19(Request $request)
    {
        try {

            $data = [
                'getdashdata' => $request,
            ];

            return view('kpi.chartData19', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function getChart20(Request $request)
    {
        try {

            $data = [
                'getdashdata' => $request,
            ];

            return view('kpi.chartData20', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function getChart21(Request $request)
    {
        try {

            $data = [
                'getdashdata' => $request,
            ];

            return view('kpi.chartData21', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function getRCADistributionCount(Request $request)
    {
        try {

            $data = [
                'getdashdata' => $request,
            ];

            return view('kpi.rca_distribution', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }

    public function getInjurypart(Request $request)
    {
        try {
            $countBodyPart = $this->bodyparts->countBodyPart();
            $data = array(
                'countBodyPart' => $countBodyPart
            );
            return view('kpi.injurypartchart', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
}
