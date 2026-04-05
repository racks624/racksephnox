<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Events\Dispatcher;

class LogUserActivity
{
    public function handle($event)
    {
        if ($event instanceof Login) {
            $action = 'login';
        } elseif ($event instanceof Logout) {
            $action = 'logout';
        } else {
            return;
        }

        AuditLog::create([
            'user_id' => $event->user->id,
            'action' => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen(Login::class, [self::class, 'handle']);
        $events->listen(Logout::class, [self::class, 'handle']);
    }
}
