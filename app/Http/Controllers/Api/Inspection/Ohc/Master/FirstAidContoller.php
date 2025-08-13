<?php

namespace App\Http\Controllers\Api\Inspection\Ohc\Master;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Inspection\Ohc\Master\FirstAidEquipment;

class FirstAidContoller extends Controller
{
    private $first_aid_equipment;


    public function __construct()
    {
        $this->first_aid_equipment = new FirstAidEquipment();
    }
    public function medicine_stock_list()
    {
        try {
            if (Auth::check()) {
                $data = $this->first_aid_equipment->listApi();
                if (count($data) > 0) {
                    return response()->json([
                        'status' => true,
                        'data' => $data,
                        'message' => 'Data Recevied Successfully',

                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'data' => $data,
                        'message' => 'No Data Found'
                    ],200);
                }
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 404);
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 404);
        }
    }
}
