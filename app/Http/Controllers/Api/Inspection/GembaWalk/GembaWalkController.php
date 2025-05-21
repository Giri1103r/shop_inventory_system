<?php

namespace App\Http\Controllers\Api\Inspection\GembaWalk;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Api\BaseController;
use App\Models\Inspection\GembaWalk\GembaWalk;
use Exception;
use Illuminate\Support\Facades\Auth;

class GembaWalkController extends BaseController
{
    private $gembaWalk;
    public function __construct()
    {
        $this->gembaWalk = new GembaWalk();
    }

    public function list(Request $request)
    {
        if (Auth::check()) {
            try {
                $data = $this->gembaWalk->listApi();
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

    public function view(Request $request){
        try{

            if(Auth::check()){
                $id = $request->id;
                dd($id);

            }else{
                return $this->sendError('Unauthorised.',['error'=>'Unauthorised'],404);
            }

        }catch(Exception $ex){
            report($ex);
            return $this->sendError(
                    'Unauthorised.',
                    ['error' => 'Please try again after sometimes'],
                    406
                );
        }
    }

}
