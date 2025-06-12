<?php

namespace App\Http\Controllers\KPI;

use Exception;
use Illuminate\Http\Request;
use App\Models\KPI\HSCInputs;
use App\Http\Controllers\Controller;
use App\Models\KPI\HSCInputsLeading;
use App\Models\KPI\LeadingLagging;
use App\Models\Master\Company;
use Illuminate\Support\Facades\Auth;

class LeadingLaggingDashboardController extends Controller
{
    private $hsc_inputs_leading;
    private $hsc_inputs;
    private $leading;
    private $company;

    public function __construct()
    {
        $this->hsc_inputs_leading = new HSCInputsLeading();
        $this->hsc_inputs = new HSCInputs();
        $this->leading = new LeadingLagging();
        $this->company = new Company();
    }
    public function index(Request $request)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        $user = Auth::user();
        $leadings = $this->leading->getLeading();
        $laggings = $this->leading->getLagging();
        $companies = $this->company->getCompany();

        $company = $request->input('company_id');
        $location_id = $request->input('location_id');
        $unit_id = $request->input('unit_id');
        $department_id = $request->input('department_id');
        $year = $request->input('year');
        $month = $request->input('month');

        $leading_array = [];
        $lagging_array = [];

        $inspection_audit_count = array_sum([
            'inspection_count' => array_sum(InspectionCount($from_date = '', $to_date = '')),
            'audit_assesment_count' => gettotalCount('audit_assessment'),
            'audit_analysis_count' => gettotalCount('audit_analysis'),
            'monthly_audit' => gettotalCount('monthly_audit'),
            'inter_unit_audit' => gettotalCount('inter_unit_audit'),
        ]);


        $gemba_walk_count     = InspectionCount($from_date = '', $to_date = '')['GembaWalk'];
        $internal_audit_count = gettotalCount('monthly_audit') + gettotalCount('inter_unit_audit');
        $daily_6s_audit       = gettotalCount('audit_analysis');
        $safety_work_permit   = gettotalCount('safetypermit');
        $fire_mock_drill      = gettotalCount('fire_mock_drill');
        $safety_walk          = gettotalCount('safety_walk');
        $training_schedule          = gettotalCount('training_schedule');
        $training_men_hours = gettotalCount('training_men_hours');

        $leading_array = [
            [
                'name'  => 'Gemba Walk Count',
                'value' => $gemba_walk_count,
            ],
            [
                'name'  => 'Internal Audit Count',
                'value' => $internal_audit_count,
            ],
            [
                'name'  => 'Daily 6S Audit',
                'value' => $daily_6s_audit,
            ],
            [
                'name'  => 'Safety Work Permit',
                'value' => $safety_work_permit,
            ],
            [
                'name'  => 'Fire Mock Drill',
                'value' => $fire_mock_drill,
            ],
            [
                'name'  => 'Safety Walk',
                'value' => $safety_walk,
            ],
            [
                'name'  => 'Training Schedule',
                'value' => $training_schedule,
            ],
            [
                'name'  => 'Training Men Hours',
                'value' => $training_men_hours,
            ],
        ];

        if ($leadings) {
            foreach ($leadings as $leading) {
                $leading_array[] = [
                    'name' => $leading->value,
                    'value' => GetLeadingCount(
                        $leading->id,
                        $company,
                        $location_id,
                        $unit_id,
                        $department_id,
                        $year,
                        $month
                    )
                ];
            }
        }

        $lagging_array = [
            [
                'name'  => 'First Aid',
                'value' => getFirstAidCount($company, $location_id, $unit_id, $department_id, $year, $month),
            ],
            [
                'name'  => 'Road Side First Aid',
                'value' => GetRoadSideFirstAid($company, $location_id, $unit_id, $department_id, $year, $month),
            ],
            [
                'name'  => 'Prescribe to Patient',
                'value' => GetPrescribeToPatient($company, $location_id, $unit_id, $department_id, $year, $month),
            ],
            [
                'name'  => 'No of Fire Incidence',
                'value' => GetImsInitialIncidentReport(FIRE_INCIDENT_REPORT, $company, $location_id, $unit_id, $department_id, $year, $month),
            ],
            [
                'name'  => 'Nos Of Near Miss Incidence',
                'value' => GetImsInitialIncidentReport(NEAR_MISS_INCIDENT_REPORT, $company, $location_id, $unit_id, $department_id, $year, $month),
            ],
        ];

        if (!empty($laggings)) {
            foreach ($laggings as $lagging) {
                $lagging_array[] = [
                    'name'  => $lagging->value,
                    'value' => GetLaggingCount(
                        $lagging->id,
                        $company,
                        $location_id,
                        $unit_id,
                        $department_id,
                        $year,
                        $month
                    ),
                ];
            }
        }


        if ($request->ajax()) {
            return response()->json([
                'leadings' => $leading_array,
                'laggings' => $lagging_array
            ]);
        }

        return view('kpi.leading_lagging_dashboard.dashboard', [
            'leadings' => $leading_array,
            'laggings' => $lagging_array,
            'companies' => $companies,
        ]);
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
