<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('layouts.app', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $view->with('unreadNotificationsCount', $user->unreadNotifications->count())
                     ->with('latestNotifications', $user->notifications()->latest()->take(5)->get());
            } else {
                $view->with('unreadNotificationsCount', 0)
                     ->with('latestNotifications', collect());
            }
        });
    }

    public function register()
    {
        //
    }
}
