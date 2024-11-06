<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UserLog;

class LogRequestInfo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // Check if the user is logged in
        $userId = auth()->check() ? auth()->user()->id : 0;

        // Get user session ID
        $sessionId = session()->getId();

        // Get request URL
        $requestUrl = $request->fullUrl();

        // Get current Date & Time
        $dateTime = todayDBdatetime();

        // Get client IP address
        $clientIp = $request->ip();

        // Get user agent
        $userAgent = $request->header('User-Agent');

        // Get referer page
        $referer = $request->header('referer') == null ?  $requestUrl  : $request->header('referer') ;



        // Log the information (you can customize the log channel)
        $data = [
            'user_login_id' => $userId,
            'session_id' => $sessionId,
            'request_uri' => $requestUrl,
            'timestamp' => $dateTime,
            'client_ip' => $clientIp,
            'client_user_agent' => $userAgent,
            'referer_page' => $referer,
        ];

        UserLog::create( $data);


        return $next($request);
    }
}
