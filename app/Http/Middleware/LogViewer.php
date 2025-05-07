<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class LogViewer
{
  public function handle($request,Closure $next)
  {


    if (!Auth::check()) {
  
        return response()->json(['message' => 'Unauthorized'], 419);
     
    }


  
    return $next($request);
  }
}
