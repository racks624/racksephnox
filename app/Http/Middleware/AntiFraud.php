<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AntiFraud
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if (!$user) {
            return $next($request);
        }

        $key = 'lottery_spin_count_' . $user->id . '_' . now()->format('Y-m-d_H:i');
        $count = Cache::increment($key);

        if ($count > 60) {
            Log::warning('Lottery fraud detected', ['user_id' => $user->id, 'ip' => $request->ip()]);
            abort(429, 'Too many spins. Please wait a moment.');
        }

        Cache::put($key, $count, 65);
        return $next($request);
    }
}
