<?php

namespace App\Http\Controllers\KPI;

use App\Http\Controllers\Controller;
use App\Models\Master\Employee;
use App\Models\Master\PpeRequest;
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


    public function __construct() {}


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
    public function getPPEIssuanceGroupWise(Request $request)
    {
        try {
            $form_date = $request->input('Fromdate');
            $to_date = $request->input('Todate');
            $chartData = getPPERequestChartData($form_date, $to_date);

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
}
