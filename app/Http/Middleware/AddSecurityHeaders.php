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

        // Ensure HTTPS for requests and responses
        if (!$request->isSecure() && app()->environment('production')) {
            return redirect()->secure($request->getRequestUri());
        }

        $response = $next($request);

        // Fetch allowed CORS domain from .env
        $allowedOrigin = env('CORS_ALLOWED_ORIGIN', 'http://localhost');

        // Add security headers
        $response->headers('Content-Security-Policy', "default-src 'self'; frame-ancestors 'none'; upgrade-insecure-requests;");
        $response->headers('X-Frame-Options', 'DENY'); // Clickjacking protection
        $response->headers('X-Content-Type-Options', 'nosniff'); // Prevent content type sniffing
        $response->headers('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload'); // Enforce HTTPS
        $response->headers('Referrer-Policy', 'no-referrer-when-downgrade');
        $response->headers('X-XSS-Protection', '1; mode=block'); // Basic XSS protection
        $response->headers('Cache-Control', 'no-store, no-cache, must-revalidate, private');
        $response->headers('Clear-Site-Data', '"cookies", "storage", "executionContexts"');

        // CORS configuration
        $response->headers('Access-Control-Allow-Origin', $allowedOrigin); // Dynamically set domain
        $response->headers('Access-Control-Allow-Methods', 'GET, POST'); // Only allow GET and POST methods
        $response->headers('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization');
        $response->headers('Access-Control-Allow-Credentials', 'false');


        return $response;
    }
}
