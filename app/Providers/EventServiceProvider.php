<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \App\Events\InvestmentCreated::class => [
            \App\Listeners\SendInvestmentConfirmation::class,
        ],
        \App\Events\MpesaPaymentReceived::class => [
            \App\Listeners\AwardReferralBonus::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
