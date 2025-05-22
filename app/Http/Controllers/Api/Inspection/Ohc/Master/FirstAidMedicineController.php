<?php

namespace App\Http\Controllers\Api\Inspection\Ohc\Master;

use App\Http\Controllers\Controller;
use App\Models\OhcManagement\Master\Medicine;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FirstAidMedicineController extends Controller
{
    private $medicine;

    public function __construct(){
        $this->medicine = new Medicine();
    }
    public function medicine_list(){
        if(Auth::check()){
            try{
                $data = $this->medicine->listApi();
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
            }catch(Exception $ex){
                dd($ex);
                return $this->sendError('Unauthorized',['error','Unauthorized'],404);
            }
        }else{
                return $this->sendError('Unauthorized',['error','Unauthorized'],404);

        }
    }
}
