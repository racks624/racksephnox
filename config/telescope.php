<?php

use Laravel\Telescope\Http\Middleware\Authorize;
use Laravel\Telescope\Watchers;

return [
    'domain' => env('TELESCOPE_DOMAIN', null),
    'path' => env('TELESCOPE_PATH', 'telescope'),
    'driver' => env('TELESCOPE_DRIVER', 'database'),
    'storage' => [
        'database' => [
            'connection' => env('DB_CONNECTION', 'sqlite'),
            'chunk' => 1000,
        ],
    ],
    'enabled' => env('TELESCOPE_ENABLED', true),
    'middleware' => [
        'web',
        Authorize::class,
    ],
    'watchers' => [
        Watchers\CacheWatcher::class => env('TELESCOPE_CACHE_WATCHER', true),
        Watchers\CommandWatcher::class => env('TELESCOPE_COMMAND_WATCHER', true),
        Watchers\DumpWatcher::class => env('TELESCOPE_DUMP_WATCHER', true),
        Watchers\EventWatcher::class => env('TELESCOPE_EVENT_WATCHER', true),
        Watchers\ExceptionWatcher::class => env('TELESCOPE_EXCEPTION_WATCHER', true),
        Watchers\GateWatcher::class => env('TELESCOPE_GATE_WATCHER', true),
        Watchers\JobWatcher::class => env('TELESCOPE_JOB_WATCHER', true),
        Watchers\LogWatcher::class => env('TELESCOPE_LOG_WATCHER', true),
        Watchers\MailWatcher::class => env('TELESCOPE_MAIL_WATCHER', true),
        Watchers\ModelWatcher::class => env('TELESCOPE_MODEL_WATCHER', true),
        Watchers\NotificationWatcher::class => env('TELESCOPE_NOTIFICATION_WATCHER', true),
        Watchers\QueryWatcher::class => env('TELESCOPE_QUERY_WATCHER', true),
        Watchers\RedisWatcher::class => env('TELESCOPE_REDIS_WATCHER', true),
        Watchers\RequestWatcher::class => env('TELESCOPE_REQUEST_WATCHER', true),
        Watchers\ScheduleWatcher::class => env('TELESCOPE_SCHEDULE_WATCHER', true),
        Watchers\ViewWatcher::class => env('TELESCOPE_VIEW_WATCHER', true),
    ],
];
