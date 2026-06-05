<?php

namespace App\Console\Commands;

use App\Services\CurrencyService;
use Illuminate\Console\Command;

class UpdateExchangeRates extends Command
{
    protected $signature = 'exchange:update';
    protected $description = 'Update exchange rates for all supported currencies';

    public function handle(CurrencyService $currency)
    {
        $currencies = ['KES', 'USD', 'EUR', 'GBP', 'BTC', 'USDT', 'ETH'];
        foreach ($currencies as $from) {
            foreach ($currencies as $to) {
                if ($from !== $to) {
                    $currency->getRate($from, $to);
                }
            }
        }
        $this->info('Exchange rates updated.');
    }
}
