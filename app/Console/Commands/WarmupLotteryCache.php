<?php

namespace App\Console\Commands;

use App\Models\LotterySymbol;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class WarmupLotteryCache extends Command
{
    protected $signature = 'lottery:warmup-cache';
    protected $description = 'Warm up lottery symbols cache';

    public function handle()
    {
        $symbols = LotterySymbol::all();
        Cache::put('lottery_symbols', $symbols, 3600);
        $this->info('Lottery symbols cache warmed up.');
    }
}
