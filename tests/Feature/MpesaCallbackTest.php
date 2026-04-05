<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MpesaTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MpesaCallbackTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_handles_stk_push_callback()
    {
        $user = User::factory()->create();
        $transaction = MpesaTransaction::factory()->create([
            'user_id' => $user->id,
            'reference' => 'test_ref',
            'status' => 'pending',
        ]);

        $callbackData = [
            'Body' => [
                'stkCallback' => [
                    'MerchantRequestID' => 'test_ref',
                    'CheckoutRequestID' => 'checkout_id',
                    'ResultCode' => 0,
                    'ResultDesc' => 'Success',
                    'CallbackMetadata' => [
                        'Item' => [
                            ['Name' => 'MpesaReceiptNumber', 'Value' => 'ABC123'],
                            ['Name' => 'Amount', 'Value' => 1000],
                            ['Name' => 'PhoneNumber', 'Value' => '254712345678'],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/mpesa/callback', $callbackData);
        $response->assertJson(['ResultCode' => 0]);

        $this->assertDatabaseHas('mpesa_transactions', [
            'id' => $transaction->id,
            'status' => 'completed',
            'mpesa_receipt_number' => 'ABC123',
        ]);
    }
}
