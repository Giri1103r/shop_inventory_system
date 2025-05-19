<?php

namespace App\Http\Controllers\KPI;

use Exception;
use Illuminate\Http\Request;
use App\Models\KPI\HSCInputs;
use App\Http\Controllers\Controller;
use App\Models\KPI\HSCInputsLeading;
use Illuminate\Support\Facades\Auth;

class LeadingLaggingDashboardController extends Controller
{
    private $hsc_inputs_leading;
    private $hsc_inputs;
    public function __construct()
    {
        $this->hsc_inputs_leading = new HSCInputsLeading();
        $this->hsc_inputs = new HSCInputs();
    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $data = [];
            return view('kpi.leading_lagging_dashboard.dashboard', $data);
        }
    }

    public function getChart1(Request $request)
    {
        try {
            $dates = [
                'from_date' => $request->input('FromDate'),
                'to_date' =>  $request->input('ToDate')
            ];

            $datas = $this->hsc_inputs_leading->getChart1($request);

            if ($datas->isEmpty()) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }

            $chartData = [
                'labels' => [],
                'series' => []
            ];

            $groupedData = [];

            $groupedData = [];

            foreach ($datas as $item) {
                $monthNumber = (int) $item->month;
                $month = date('M', mktime(0, 0, 0, $monthNumber, 1));

                $company = $item->company_id;
                $value = (float) $item->value;

                if (!isset($groupedData[$month])) {
                    $groupedData[$month] = [];
                }

                if (!isset($groupedData[$month][$company])) {
                    $groupedData[$month][$company] = 0;
                }

                $groupedData[$month][$company] += $value;
            }

            $months = array_keys($groupedData);
            sort($months);
            $chartData['labels'] = $months;

            $companyIds = [];
            foreach ($groupedData as $monthData) {
                foreach ($monthData as $companyId => $sum) {
                    $companyIds[$companyId] = true;
                }
            }
            $companyIds = array_keys($companyIds);

            foreach ($companyIds as $companyId) {
                $data = [];
                foreach ($months as $month) {
                    $data[] = $groupedData[$month][$companyId] ?? 0;
                }

                $chartData['series'][] = [
                    'name' => "Company " . getCompanyName($companyId),
                    'data' => $data
                ];
            }

            return view('kpi.leading_lagging_dashboard.chartData1', [
                'chartData' => $chartData,
            ]);
        } catch (\Exception $ex) {
            report($ex);
        }
    }

    public function LaggingIndicatorLine(Request $request)
    {
        try {
            $from_date = $request->FromDate;
            $to_date = $request->ToDate;
            $chartData = $this->hsc_inputs->LaggingLine();
            if ($chartData == false) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No Data Found</h4></div>');
            }

            $years = [];
            foreach ($chartData as $label => $yearData) {
                foreach ($yearData as $year => $val) {
                    if (!in_array($year, $years)) $years[] = $year;
                }
            }
            sort($years);


            $datasets = [];

            foreach ($chartData as $label => $yearData) {
                $dataPoints = [];
                foreach ($years as $year) {
                    $dataPoints[] = $yearData[$year] ?? 0;
                }

                $datasets[] = [
                    'name' => $label,
                    'data' => $dataPoints,
                    'borderColor' => '#' . substr(md5($label), 0, 6),
                    'fill' => false,
                ];
            }

            $data = [
                'years' => $years,
                'datasets' => $datasets,
                'from_date' => $from_date,
                'to_date' => $to_date,
            ];


            return view('kpi.leading_lagging_dashboard.lagging_line', $data);
        } catch (Exception $ex) {
            report($ex);
            return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">Something Went Wrong !</h4></div>');
        }
    }

    public function LaggingDoughNut(Request $request)
    {
        try {

            $from_date = $request->FromDate;
            $to_date = $request->ToDate;
            $chartData = $this->hsc_inputs->LaggingLine();
            if ($chartData == false) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No Data Found</h4></div>');
            }

            $datasets = [];

            foreach ($chartData as $label => $yearData) {
                $total = array_sum($yearData);
                $datasets[] = [
                    'name' => $label,
                    'count' => $total,
                ];
            }

            $data = [
                'chartData' => json_encode($datasets),
                'from_date' => $from_date,
                'to_date' => $to_date,
            ];

            return view('kpi.leading_lagging_dashboard.lagging_indicator', $data);
        } catch (Exception $ex) {
            report($ex);
            return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">Something Went Wrong !</h4></div>');
        }
    }
}
