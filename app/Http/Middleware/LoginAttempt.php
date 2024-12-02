<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;

class LoginAttempt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $email         = $request->email;
        $ipAddress     = $request->ip();
        $maxAttempts   = 5;
        $decayMinutes  = 10;
        $blockDuration = 30;

        // Check if account is already blocked
        if (RateLimiter::tooManyAttempts('login:password:' . $email, $maxAttempts)) {
            $seconds = RateLimiter::availableIn('login:password:' . $email);
            Session::flash('error', 'Too many incorrect password attempts. Your account is blocked for ' . $blockDuration . ' minutes.');
            return redirect()->back()->withErrors(['email' => 'Too many incorrect password attempts. Your account is blocked for ' . $blockDuration . ' minutes.']);
        }

        // Check if IP is already blocked for email attempts
        if (RateLimiter::tooManyAttempts('login:email:' . $ipAddress, $maxAttempts)) {
            $seconds = RateLimiter::availableIn('login:email:' . $ipAddress);
            Session::flash('error', 'Too many incorrect email attempts. Your IP address is blocked for ' . $blockDuration . ' minutes.');
            return redirect()->back()->withErrors(['email' => 'Too many incorrect email attempts. Your IP address is blocked for ' . $blockDuration . ' minutes.']);
        }

        RateLimiter::hit('login:password:' . $email, $decayMinutes * 60);
        RateLimiter::hit('login:email:' . $ipAddress, $decayMinutes * 60);

        $passwordAttempts = RateLimiter::attempts('login:password:' . $email);
        $emailAttempts = RateLimiter::attempts('login:email:' . $ipAddress);

        if ($passwordAttempts >= $maxAttempts) {
            RateLimiter::hit('login:password:' . $email, $blockDuration * 60);
            Session::flash('error', 'Too many incorrect password attempts. Your account is blocked for ' . $blockDuration . ' minutes.');
            return redirect()->back()->withErrors(['email' => 'Too many incorrect password attempts. Your account is blocked for ' . $blockDuration . ' minutes.']);
        }

        if ($emailAttempts >= $maxAttempts) {
            RateLimiter::hit('login:email:' . $ipAddress, $blockDuration * 60);
            Session::flash('error', 'Too many incorrect email attempts. Your IP address is blocked for ' . $blockDuration . ' minutes.');
            return redirect()->back()->withErrors(['email' => 'Too many incorrect email attempts. Your IP address is blocked for ' . $blockDuration . ' minutes.']);
        }

        return $next($request);
    }
}
