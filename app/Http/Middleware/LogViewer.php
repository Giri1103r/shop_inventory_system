<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogViewer
{
  public function handle(Request $request, Closure $next)
  {
    session(['requested_url' => $request->fullUrl()]);

    if (!Auth::check()) {
      if (!$request->ajax()) {
        return response()->json(['message' => 'Unauthorized'], 419);
      }
    }

    $allowedUserId = null;

    $allowedUserId = env('LOG_VIEWER_CHECK_ID');

    if (Auth::id() != $allowedUserId) {
      dd('test',Auth::id(), Auth::user(), $allowedUserId);
      abort(403, 'Unauthorized');
    }

    return $next($request);
  }
}
