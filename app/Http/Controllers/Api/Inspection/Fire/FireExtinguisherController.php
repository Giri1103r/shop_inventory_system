<?php

namespace App\Http\Controllers\Api\Inspection\Fire;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inspection\Fire\FireStatusLog;
use App\Models\Inspection\Fire\FireExtinguisher;
use App\Models\Inspection\Fire\FireExtinguisherType;
use App\Models\Inspection\Fire\FireExtinguisherDetails;

class FireExtinguisherController extends Controller
{
    private $fire_extinguisher;
    private $fire_extinguisher_details;
    private $fire_extinguisher_type;
    private $statusLog;

    public function __construct()
    {
        $this->fire_extinguisher = new FireExtinguisher();
        $this->fire_extinguisher_details = new FireExtinguisherDetails();
        $this->fire_extinguisher_type = new FireExtinguisherType();
        $this->statusLog = new FireStatusLog();
    }

    public function List(Request $request)
    {
        
    }

    public function View(Request $request)
    {
        
    }
}
