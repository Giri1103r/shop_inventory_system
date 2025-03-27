<?php

namespace App\Http\Controllers\Admin;

use Log;


use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\PasswordOTPEmail;
use Illuminate\Support\Carbon;
use App\Mail\PasswordResetEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Cache\RateLimiting\Limit;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

use Illuminate\Support\Facades\Cookie;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Session\Session as SessionSession;
use Illuminate\Contr;

use App\Models\BlockedUserLog;

class BlockedController extends Controller
{
    protected $partner;

    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function blocked()
    {
        return view('admin.layouts.blockedlayout');
    }
    


    public function blockedSave(Request $request)
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
        $referer = $request->header('referer') == null ?  $requestUrl  : $request->header('referer');



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

        BlockedUserLog::create($data);

        return response()->json([
            'success' => true,
        ]);
    }
}
