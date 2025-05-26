<?php

namespace App\Http\Controllers\Api\Inspection\Audit\Master;

use App\Http\Controllers\Controller;
use App\Models\Inspection\audit\Master\Task;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskMasterController extends Controller
{
    private $audit_task;


    public function __construct()
    {
        $this->audit_task = new Task();
    }
    public function list()
    {
        if (Auth::check()) {
            try {
                $data = $this->audit_task->listApi();
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
}
