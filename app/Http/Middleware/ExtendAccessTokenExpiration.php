<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use Carbon\Carbon;

class ExtendAccessTokenExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = Auth::user();

        if ($user && $user->token()) {

            $user->token()->update([
                'expires_at' => Carbon::now()->add(Passport::personalAccessTokensExpireIn())->format('Y-m-d H:i:s'),
            ]);
        }

        return $next($request);
    }
}
