<?php

namespace App\Http\Controllers\Api\Inspection\Master;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Inspection\Master\ChecklistType;
use Illuminate\Http\Request;

class InspectionMasterController extends BaseController
{
    private $checklistType;
    public function __construct()
    {
        $this->checklistType = new ChecklistType();
    }
}
