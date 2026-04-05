<?php

namespace App\Http\Middleware;

use Closure;

class CacheHeaders
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        // Cache static assets for 1 year
        if ($request->is('build/*') || $request->is('img/*') || $request->is('js/*') || $request->is('css/*')) {
            $response->header('Cache-Control', 'public, max-age=31536000, immutable');
        }
        // Cache API responses for 1 minute
        elseif ($request->is('api/*')) {
            $response->header('Cache-Control', 'public, max-age=60');
        }
        
        return $response;
    }
}
