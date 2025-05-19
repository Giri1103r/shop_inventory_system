<?php

namespace App\Http\Controllers\KPI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LeadingLaggingDashboardController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $data = [];
            return view('kpi.leading_lagging_dashboard.dashboard', $data);
        }
    }
    

}
