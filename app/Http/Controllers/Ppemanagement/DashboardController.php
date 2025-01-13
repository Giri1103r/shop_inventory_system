<?php

namespace App\Http\Controllers\Ppemanagement;

use App\Http\Controllers\Controller;
use App\Models\Master\PpeExemption;
use App\Models\Master\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class DashboardController extends Controller
{
    private $ppeexemption;
    private $unit;

    public function __construct()
    {
        $this->ppeexemption = new PpeExemption();
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

        $data = [
            'exemptionUnits' => $ppeexemptionUnits,
        ];

        if ($user->role == ROLE_SUPERADMIN) {
            return view('ppemanagement.dashboard.dashboard', $data);
        }
    }

    public function ppeexemptiondata()
    {
        $units = $this->unit->getunit();

        try {
            $chartData = $this->ppeexemption->getExemptionChartData($units);
            return view('ppemanagement.dashboard.ppeexemption', compact('chartData'));
        } catch (\Exception $ex) {
            Log::error('Error loading PPE exemption data: ' . $ex->getMessage());
            return response()->json([
                'error' => 'Unable to load PPE exemption data. ' . $ex->getMessage(),
            ], 500);
        }
    }

   
}
