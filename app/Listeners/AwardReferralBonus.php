<?php

namespace App\Listeners;

use App\Events\MpesaPaymentReceived;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AwardReferralBonus
{
    public function handle(MpesaPaymentReceived $event)
    {
        $transaction = $event->transaction;
        $user = $transaction->user;
        
        if ($user->referred_by && $transaction->status === 'completed') {
            $referrer = User::find($user->referred_by);
            if ($referrer) {
                $bonusRate = config('referral.bonus_rate', 5);
                $bonusAmount = $transaction->amount * ($bonusRate / 100);
                
                if ($bonusAmount > 0) {
                    DB::transaction(function () use ($referrer, $bonusAmount, $transaction) {
                        $referrer->wallet->credit($bonusAmount, 'Referral bonus from ' . $transaction->user->name, 'referral_bonus');
                    });
                }
            }
        }
    }
}
