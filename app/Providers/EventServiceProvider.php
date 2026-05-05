<?php
namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\InvestmentCreated;
use App\Events\MpesaPaymentReceived;
use App\Listeners\SendInvestmentConfirmation;
use App\Listeners\AwardReferralBonus;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        InvestmentCreated::class => [
            SendInvestmentConfirmation::class,
        ],
        MpesaPaymentReceived::class => [
            AwardReferralBonus::class,
        ],
    ];

    public function boot()
    {
        //
    }
}
