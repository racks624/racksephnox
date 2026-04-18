<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Tournament prize distribution (daily at 00:30)
        $schedule->command('lottery:distribute-prizes')->dailyAt('00:30');

        // Update tournament rankings (every hour)
        $schedule->command('lottery:update-rankings')->hourly();

        // Clean up old lottery spins (keep last 30 days) – run daily
        $schedule->command('lottery:cleanup-spins')->dailyAt('02:00');

        // Cache warmup for lottery symbols (every 6 hours)
        $schedule->command('lottery:warmup-cache')->everySixHours();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
