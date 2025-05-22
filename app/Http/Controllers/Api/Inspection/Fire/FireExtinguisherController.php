<?php

namespace App\Http\Controllers\Api\Inspection\Fire;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Inspection\Fire\FireStatusLog;
use App\Models\Inspection\Fire\FireExtinguisher;
use App\Models\Inspection\Fire\FireExtinguisherType;
use App\Models\Inspection\Fire\FireExtinguisherDetails;

class FireExtinguisherController extends Controller
{
    private $inspection;
    private $inspection_details;
    private $fire_extinguisher_type;
    private $statusLog;

    public function __construct()
    {
        $this->inspection = new FireExtinguisher();
        $this->inspection_details = new FireExtinguisherDetails();
        $this->fire_extinguisher_type = new FireExtinguisherType();
        $this->statusLog = new FireStatusLog();
    }

    public function List(Request $request)
    {
        if (Auth::check()) {
            try {
                $data = $this->inspection->listApi();
                if (count($data) > 0) {
                    return response()->json([
                        'success' => true,
                        'data' => $data,
                        'message' => 'Data Retrieved Successfully',
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'data' => $data,
                        'message' => 'No Data Found',
                    ], 200);
                }
            } catch (Exception $ex) {
                report($ex);
                return $this->sendError(
                    'Unauthorised.',
                    ['error' => 'Please try again after sometimes'],
                    406
                );
            }
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    public function View(Request $request) 
    {
        
    }
}
