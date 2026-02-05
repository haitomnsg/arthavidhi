<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreserveTabParameter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // If we're in a tab and the response is a redirect, preserve the _tab parameter
        if ($request->get('_tab') == '1' && $response instanceof \Illuminate\Http\RedirectResponse) {
            $targetUrl = $response->getTargetUrl();
            
            // Check if _tab is already in the URL
            if (!str_contains($targetUrl, '_tab=')) {
                $separator = str_contains($targetUrl, '?') ? '&' : '?';
                $response->setTargetUrl($targetUrl . $separator . '_tab=1');
            }
        }

        return $response;
    }
}
