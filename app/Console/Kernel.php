<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\AccrueInterest::class,
        \App\Console\Commands\AccrueMachineProfits::class,
        \App\Console\Commands\SyncCryptoPrices::class,
        \App\Console\Commands\KycApprove::class,
        \App\Console\Commands\KycReject::class,
        \App\Console\Commands\ForceKyc::class,
        \App\Console\Commands\RecordBtcPrice::class,
        \App\Console\Commands\MatchOrders::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Run heavy tasks at low traffic times
        $schedule->command('investments:accrue')->dailyAt('00:00')->withoutOverlapping();
        $schedule->command('machines:accrue')->dailyAt('00:05')->withoutOverlapping();
        $schedule->command('crypto:sync-prices')->everyFiveMinutes()->withoutOverlapping();
        $schedule->command('btc:record-price')->everyFiveMinutes()->withoutOverlapping();
        $schedule->command('trading:match-orders')->everyMinute()->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
