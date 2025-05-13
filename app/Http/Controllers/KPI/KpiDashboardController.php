<?php

namespace App\Http\Controllers\KPI;

use App\Models\IMS\Incident\IncidentBodyParts;
use App\Models\IMS\Incident\InitialIncident;

use App\Models\Master\PpeRequest;

use DB;
use Exception;
use App\Models\User;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Models\Master\Employee;
use App\Models\Permit\SafetyPermit;
use App\Http\Controllers\Controller;
use App\Models\Master\TrainingSchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class KpiDashboardController extends Controller
{
    private $bodyparts;
    private $training_schedule;
    private $ims_incident;
    private $ptw;


    public function __construct()
    {
        $this->bodyparts = new IncidentBodyParts();
        $this->training_schedule = new TrainingSchedule();
        $this->ims_incident = new InitialIncident();
        $this->ptw = new SafetyPermit();
        $this->training_schedule = new TrainingSchedule();
    }


    public function index(Request $request)
    {

        if (Auth::check()) {
            $user = Auth::user();
            $data = [];

            return view('kpi.dashboard', $data);
        }
    }


    public function InspectionWiseCount(Request $request)
    {
        try {
            $from_date = $request->input('Fromdate');
            $to_date = $request->input('Todate');

            $inspection_wise_count = InspectionCount($from_date, $to_date);

            $data = [
                'inspection_wise_count' => $inspection_wise_count,
                'from_date' => $from_date,
                'to_date' => $to_date,
            ];

            return view('kpi.inspection_wise_count', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }

    public function PTWActiveVsClose(Request $request)
    {
        try {

            $dates = [
                'from_date' => $request->input('FromDate'),
                'to_date' =>  $request->input('ToDate')
            ];
            $active_close_count = $this->ptw->ActiveVsClose();

            if ($active_close_count == null) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 150px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }

            $data = [
                'active_close_count' => $active_close_count,
                'dates' => $dates,
            ];

            return view('kpi.ptw_open_close', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }


    public function PTWTypeWiseCount(Request $request)
    {
        try {

            $dates = [
                'from_date' => $request->input('FromDate'),
                'to_date' =>  $request->input('ToDate')
            ];

            $work_wise_count = $this->ptw->GetTypeWiseCount();

            $data = [
                'work_wise_count' => $work_wise_count,
                'dates' => $dates,
            ];

            return view('kpi.ptw_type_wise_count', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }

    public function TrainingHoursSafetyDepartmentWise(Request $request)
    {
        try {
            $dates = [
                'from_date' => $request->input('FromDate'),
                'to_date' =>  $request->input('ToDate')
            ];

            $training_data = $this->training_schedule->GetTrainingHoursDepartmentData($request);

            $chartData = [
                'labels' => [],
                'series' => [],
                'departments' => []
            ];

            foreach ($training_data as $item) {
                $chartData['labels'][] = $item->topic_name;
                $chartData['series'][] = (float) $item->total_hours;
                $chartData['departments'][] = $item->department_name;
            }

            return view('kpi.training_hour_department_wise', [
                'training_data' => $training_data,
                'chartData' => $chartData,
                'getdashdata' => (object) $dates
            ]);
        } catch (\Exception $ex) {
            report($ex);
        }
    }

    public function ptwholdviolation(Request $request)
    {
        try {

            $dates = [
                'from_date' => $request->input('FromDate'),
                'to_date' =>  $request->input('ToDate')
            ];

            $hold_count = $this->ptw->getHoldStatus($request);

            $data = [
                'hold_count' => $hold_count,
                'dates' => $dates,
            ];

            return view('kpi.ptw_hold_wise_count', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function TrainingHours(Request $request)
    {
        try {

            $training_data = $this->training_schedule->GetTrainingData();

            $data = [
                'training_data' => $training_data,
            ];

            return view('kpi.iir_wise_rcpa', $data);
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
    public function getPPEIssuanceGroupWise(Request $request)
    {
        try {
            $form_date = $request->input('Fromdate');
            $to_date = $request->input('Todate');
            $chartData = getPPERequestChartData($form_date, $to_date);

            $data = [
                'getdashdata' => $request,
            ];

            return view('kpi.chartPPEIssuanceGroupWise', [
                'getdashdata' => $request,
                'chartData' => $chartData,
            ]);
        } catch (\Exception $ex) {
            report($ex);
        }
    }

    public function getPTWAvgTimeChart(Request $request)
    {
        try {

            $form_date = $request->input('Fromdate');
            $to_date = $request->input('Todate');
            $chartData = getPTWAvgTimeChartData($form_date, $to_date);

            return view('kpi.chartPTWAvgTimeChart', [
                'getdashdata' => $request,
                'chartData' => $chartData,
            ]);
        } catch (\Exception $ex) {
            report($ex);
        }
    }

    public function getPPEAvailabilityChart(Request $request)
    {
        try {

            $form_date = $request->input('Fromdate');
            $to_date = $request->input('Todate');
            $chartData = getPPEAvailabilityChartData($form_date, $to_date);

            return view('kpi.chartPPEAvailability', [
                'getdashdata' => $request,
                'chartData' => $chartData,
            ]);
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
    public function getNearMissCount(Request $request)
    {
        try {
            $chartData = $this->ims_incident->getNearMissCountData($request);

            $formattedData = [
                'labels' => $chartData->pluck('unit_name'),
                'major' => $chartData->pluck('major'),
                'minor' => $chartData->pluck('minor'),
                'fatal' => $chartData->pluck('fatal'),
            ];

            return view('kpi.near_miss', [
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

    public function injurybodycount(Request $request)
    {
        $request = request();
        $parts = $request->input('part');

        $listResp = FacadesDB::table('ims_initial_incident as inc')
            ->select([
                'body.id as bodyids',
                'inc.id as inveeid',
            ])
            ->leftJoin('ims_injury_details as inj', 'inj.incident_id', '=', 'inc.id')
            ->leftJoin('ims_incident_body_parts as body', 'body.injury_id', '=', 'inj.id')
            ->where('inc.trash', 'NO')
            ->where('body.body_parts_label', 'like', '%' . $parts . '%') // Correct LIKE usage
            ->get();

        $inveeid = [];
        $response = [];

        if (!$listResp->isEmpty()) {
            foreach ($listResp as $lists) {
                $inveeid[] = $lists->inveeid;
            }

            $response['count'] = count($listResp);
            $response['inc_id'] = $inveeid;
        } else {
            $response['count'] = 0;
            $response['inc_id'] = [];
        }

        return response()->json($response);
    }


    public function IIRTypeWiseUAUC(Request $request)
    {
        try {
            $chartData = $this->ims_incident->getTypeofIIRUAUCCountData($request);
            $data = [
                'chartData' => $chartData,
            ];

            return view('kpi.iir_wise_uauc', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
}
