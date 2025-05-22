<?php

namespace App\Http\Controllers\Api\Inspection\Fire;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inspection\Fire\FireStatusLog;
use App\Models\Inspection\Fire\FireFileUpload;
use App\Models\Inspection\Fire\FireAlarmInspection;
use App\Models\Inspection\Fire\FireSignatureUpload;
use App\Models\Inspection\Fire\FireAlarmInspectionDetails;

class FireAlarmController extends Controller
{
    private $inspection;
    private $inspection_details;
    private $statusLog;
    private $files;
    private $signature;

    public function __construct()
    {
        $this->inspection = new FireAlarmInspection();
        $this->inspection_details = new FireAlarmInspectionDetails();
        $this->statusLog = new FireStatusLog();
        $this->files = new FireFileUpload();
        $this->signature = new FireSignatureUpload();
    }

    
}
