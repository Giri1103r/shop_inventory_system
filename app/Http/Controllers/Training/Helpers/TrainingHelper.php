<?php

use Illuminate\Support\Facades\DB;
use App\Models\User;

use App\Models\Master\TrainingSchedule;



/**
 * Training Status Count
 */

if (!function_exists('trainingStatusCount')) {
    function trainingStatusCount($type = '', $params = [])
    {
        $training = new TrainingSchedule();
        return $training->statusCount($type, $params);
    }
}


