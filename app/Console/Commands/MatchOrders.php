<?php

namespace App\Console\Commands;

use App\Models\TradeOrder;
use App\Services\Trading\TradingEngine;
use Illuminate\Console\Command;

class MatchOrders extends Command
{
    protected $signature = 'trading:match-orders';
    protected $description = 'Match pending limit orders';

    protected $tradingEngine;

    public function __construct(TradingEngine $tradingEngine)
    {
        parent::__construct();
        $this->tradingEngine = $tradingEngine;
    }

    public function handle()
    {
        $pendingOrders = TradeOrder::whereIn('status', ['pending', 'partial'])
            ->where('order_type', 'limit')
            ->get();

        foreach ($pendingOrders as $order) {
            $this->tradingEngine->matchOrder($order);
        }

        $this->info('Matching completed.');
    }
}
