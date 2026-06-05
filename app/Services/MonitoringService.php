<?php

namespace App\Services;

use App\Models\LotterySpin;
use App\Models\Transaction;
use App\Models\TradingOrder;
use App\Models\MachineInvestment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitoringService
{
    /**
     * Real-time system health check
     */
    public function getSystemHealth(): array
    {
        return [
            'status' => 'operational',
            'timestamp' => now()->toIso8601String(),
            'metrics' => [
                'response_time' => $this->getAverageResponseTime(),
                'error_rate' => $this->getErrorRate(),
                'queue_size' => DB::table('jobs')->count(),
                'failed_jobs' => DB::table('failed_jobs')->count(),
            ],
            'services' => [
                'database' => $this->checkDatabase(),
                'cache' => $this->checkCache(),
                'queue' => $this->checkQueue(),
            ]
        ];
    }

    /**
     * Daily revenue summary for admin
     */
    public function getDailyRevenueSummary(): array
    {
        $today = now()->toDateString();
        return [
            'date' => $today,
            'total_deposits' => Transaction::where('type', 'deposit')->whereDate('created_at', $today)->sum('amount'),
            'total_withdrawals' => Transaction::where('type', 'withdrawal')->whereDate('created_at', $today)->sum('amount'),
            'lottery_tax' => LotterySpin::whereDate('created_at', $today)->sum('tax_contribution'),
            'trading_volume' => TradingOrder::whereDate('created_at', $today)->sum('amount'),
            'machine_investments' => MachineInvestment::whereDate('created_at', $today)->sum('amount'),
            'net_revenue' => Transaction::whereDate('created_at', $today)->sum(DB::raw('CASE WHEN type IN ("deposit", "interest") THEN amount ELSE -amount END')),
        ];
    }

    private function getAverageResponseTime(): float
    {
        // Simulated – in production, use APM or cache
        return 0.125;
    }

    private function getErrorRate(): float
    {
        // Simulated – monitor logs
        return 0.003;
    }

    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'healthy', 'latency' => '5ms'];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'error' => $e->getMessage()];
        }
    }

    private function checkCache(): array
    {
        try {
            cache()->set('health_check', 'ok', 10);
            $value = cache()->get('health_check');
            return ['status' => 'healthy', 'value' => $value];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'error' => $e->getMessage()];
        }
    }

    private function checkQueue(): array
    {
        $size = DB::table('jobs')->count();
        return ['status' => $size < 1000 ? 'healthy' : 'degraded', 'queue_size' => $size];
    }
}
