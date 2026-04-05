<?php

namespace App\Listeners;

use App\Events\InvestmentCreated;
use App\Mail\InvestmentConfirmation;
use Illuminate\Support\Facades\Mail;

class SendInvestmentConfirmation
{
    public function handle(InvestmentCreated $event)
    {
        Mail::to($event->investment->user->email)
            ->send(new InvestmentConfirmation($event->investment));
    }
}
