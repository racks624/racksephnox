<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class QueryCacheServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (config('database.query_cache_enabled')) {
            DB::listen(function ($query) {
                if (str_starts_with($query->sql, 'select') && !str_contains($query->sql, 'telescope')) {
                    $key = 'query_cache_' . md5($query->sql . serialize($query->bindings));
                    Cache::remember($key, config('database.query_cache_ttl', 300), function () use ($query) {
                        return DB::select($query->sql, $query->bindings);
                    });
                }
            });
        }
    }
}
