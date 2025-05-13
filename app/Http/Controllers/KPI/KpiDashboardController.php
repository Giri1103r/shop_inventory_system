<?php

namespace App\Http\Controllers\KPI;

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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class KpiDashboardController extends Controller
{

    private $ptw;
    private $training_schedule;

    public function __construct()
    {
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

            if($active_close_count == null){
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

    public function TrainingHours(Request $request)
    {
        try {

            $training_data = $this->training_schedule->GetTrainingData();

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
}
