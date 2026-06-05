<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TwoFactor
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user->two_factor_secret && !$request->session()->get('two_factor_authenticated')) {
            return response()->json(['message' => 'Two-factor authentication required'], 403);
        }

        return $next($request);
    }
}
