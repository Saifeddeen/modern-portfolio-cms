<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiLocaleMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check for Accept-Language header or a custom X-Locale header
        $locale = $request->header('Accept-Language') ?: $request->header('X-Locale');

        // If provided and supported, set the app locale
        if ($locale && array_key_exists($locale, config('app.locales'))) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
