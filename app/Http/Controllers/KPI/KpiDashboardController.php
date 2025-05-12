<?php

namespace App\Http\Controllers\KPI;

use App\Http\Controllers\Controller;
use App\Models\IMS\Incident\IncidentBodyParts;
use App\Models\IMS\Incident\InitialIncident;
use App\Models\Master\Employee;
use App\Models\Master\TrainingSchedule;
use DB;
use Exception;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use App\Models\User;

class KpiDashboardController extends Controller
{
    private $bodyparts;
    private $training_schedule;
    private $ims_incident;


    public function __construct()
    {
        $this->bodyparts = new IncidentBodyParts();
        $this->training_schedule = new TrainingSchedule();
        $this->ims_incident = new InitialIncident();
    }


    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $data = [];

            return view('kpi.dashboard', $data);
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

    public function getTrainingCompletionCount(Request $request)
    {
        try {
            $chartData = $this->training_schedule->getTrainigCompletionCountData($request);

            if (empty($chartData) || $chartData->training_total_count == 0) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }

            $formattedData = [
                'total' => $chartData->training_total_count,
                'closed' => $chartData->closed_count,
                'open' => $chartData->open_count,
                'closed_percentage' => $chartData->closed_percentage,
                'open_percentage' => $chartData->open_percentage,
            ];

            return view('kpi.training_completion', [
                'formattedData' => $formattedData,
                'getdashdata' => $request,
            ]);
        } catch (\Exception $ex) {
            report($ex);
            return back()->with('error', 'Failed to load training completion data.');
        }
    }

    public function getTypeofIIRCount(Request $request)
    {
        try {
            $chartData = $this->ims_incident->getTypeofIIRCountData($request);

            // Format data for chart: separate arrays for names and counts
            $formattedData = [
                'labels' => $chartData->pluck('incident_type_name'),
                'counts' => $chartData->pluck('total'),
            ];

            return view('kpi.type_of_irr', [
                'formattedData' => $formattedData,
                'getdashdata' => $request,
            ]);
        } catch (\Exception $ex) {
            report($ex);
            return back()->with('error', 'Failed to load Type of IIR data.');
        }
    }

    public function getAccidentReportUnitWiseCount(Request $request)
    {
        try {
            $chartData = $this->ims_incident->getAccidentReportUnitWiseCountData($request);

            $formattedData = [
                'labels' => $chartData->pluck('unit_name'),
                'major' => $chartData->pluck('major'),
                'minor' => $chartData->pluck('minor'),
                'fatal' => $chartData->pluck('fatal'),
            ];

            return view('kpi.accident_report_unit_wise', [
                'formattedData' => $formattedData,
                'getdashdata' => $request,
            ]);
        } catch (\Exception $ex) {
            report($ex);
            return back()->with('error', 'Failed to load unit-wise accident data.');
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
