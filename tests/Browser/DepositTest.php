<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DepositTest extends DuskTestCase
{
    /** @test */
    public function user_can_initiate_deposit()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/mpesa/deposit')
                    ->type('phone', '254712345678')
                    ->type('amount', '100')
                    ->press('Pay with M-Pesa')
                    ->assertPathIs('/wallet')
                    ->assertSee('STK Push sent');
        });
    }
}
