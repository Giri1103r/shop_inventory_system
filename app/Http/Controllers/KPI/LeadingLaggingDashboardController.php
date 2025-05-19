<?php

namespace App\Http\Controllers\KPI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\KPI\HSCInputs;
use App\Models\KPI\HSCInputsLeading;
use Illuminate\Support\Facades\Auth;

class LeadingLaggingDashboardController extends Controller
{
    private $hsc_inputs_leading;
    public function __construct()
    {
        $this->hsc_inputs_leading = new HSCInputsLeading();
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
}
