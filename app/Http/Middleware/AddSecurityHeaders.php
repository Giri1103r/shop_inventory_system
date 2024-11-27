<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddSecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if ((!$request->isSecure() && $request->header('X-Forwarded-Proto') !== 'https') && app()->environment('production')) {
            return redirect()->secure($request->getRequestUri());
        }

        $response = $next($request);

        $allowedOrigin = env('CORS_ALLOWED_ORIGIN', '*');

        // // Security headers
        $response->headers->set('Content-Security-Policy',"default-src 'self';script-src 'self' 'unsafe-inline';style-src 'self' 'unsafe-inline';img-src 'self' data:;font-src 'self' data:;object-src 'none';frame-ancestors 'none';upgrade-insecure-requests;");
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        // $response->headers->set('Clear-Site-Data', '"cookies", "storage", "executionContexts"');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('Feature-Policy', "geolocation 'none'; microphone 'none'; camera 'none'; payment 'none';");
        $response->headers->set('Permission-Policy', 'geolocation=(), microphone=(), camera=(), fullscreen=(self), payment=()');

        // CORS headers
        $response->headers->set('Access-Control-Allow-Origin', $allowedOrigin);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization, Accept');
        $response->headers->set('Access-Control-Allow-Credentials', env('CORS_ALLOW_CREDENTIALS', 'false'));

        // Cache control
        if ($request->is('api/*')) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
        } else {
            $response->headers->set('Cache-Control', 'public, max-age=31536000');
        }

        return $response;
    }
}
