<?php

namespace App\Http\Middleware;

use Closure;

class Onboarding
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if ($user && !$user->onboarding_completed && !$request->routeIs('onboarding.*')) {
            return redirect()->route('onboarding.index');
        }
        return $next($request);
    }
}
