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
            'unitList'=>  $unitIds,
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

        public function ppeRequestList(Request $request){
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



}
