<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class PerformanceServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Share globally cached user data across all views (reduces DB queries)
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Cache::remember('user_' . Auth::id(), 60, function () {
                    return Auth::user()->load('wallet', 'tradingAccount');
                });
                $view->with('cachedUser', $user);
            }
        });
    }

    public function register()
    {
        //
    }
}
