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
    public function __construct(){
        $this->gembaWalk = new GembaWalk();
    }

    public function list(Request $request)
    {
           if(Auth::check()){
                try {
                   $data = $this->gembaWalk->listApi();
                   dd($data);
                } catch (Exception $ex) {
                    dd($ex);
                }
           }else{

           }
    }
}
