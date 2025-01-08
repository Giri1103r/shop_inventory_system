<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsLogin
{

  public function handle($request, Closure $next)
  {

    session(['requested_url' => $request->fullUrl()]);
    if (!Auth::check()) {

      if ($request->ajax()) {
        $data = [];
        return   response()->json($data, 419);
      } else {
        return redirect(url('login'));
      }
    }
    return $next($request);
  }
}
