<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Admin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated and has admin privileges
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Unauthorized access. Admin privileges required.');
        }
        
        return $next($request);
    }
}
