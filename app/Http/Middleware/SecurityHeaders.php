<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Security Headers
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        // CSP + Permissions Policy
        $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self'; object-src 'none'; frame-ancestors 'none'; base-uri 'self';");
        $response->headers->set('Permissions-Policy', "geolocation=(), microphone=(), camera=()");

        // Cross-Origin isolation
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');
        $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; object-src 'none'; frame-ancestors 'none'; base-uri 'self';");

        // Allowed image domains
        $allowedImageDomains = [
            'https://admin.gosizi.com',
            'https://vertexbazaar.com'
        ];

// Build img-src part
        $imgSrc = "'self' data: http: https:";
        foreach ($allowedImageDomains as $domain) {
            $imgSrc .= "$domain";
        }

// Set CSP
        $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src $imgSrc; object-src 'none'; frame-ancestors 'none'; base-uri 'self';");


        return $response;
    }
}
