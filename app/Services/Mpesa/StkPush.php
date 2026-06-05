<?php

namespace App\Services\Mpesa;

use App\Models\MpesaTransaction;
use Illuminate\Support\Facades\Log;

class StkPush extends MpesaClient
{
    public function initiate($phone, $amount, $reference, $description = 'Investment Deposit')
    {
        $timestamp = now()->format('YmdHis');
        $password = $this->generateStkPassword($timestamp);

        $data = [
            'BusinessShortCode' => $this->config['shortcode'],
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => (int) $amount,
            'PartyA' => $phone,
            'PartyB' => $this->config['shortcode'],
            'PhoneNumber' => $phone,
            'CallBackURL' => $this->config['callback_url']['stk'],
            'AccountReference' => $reference,
            'TransactionDesc' => $description,
        ];

        $response = $this->request('stk_push', $data);

        // Create pending transaction record
        MpesaTransaction::create([
            'user_id' => auth()->id(),
            'transaction_type' => 'stk_push',
            'amount' => $amount,
            'phone' => $phone,
            'reference' => $reference,
            'description' => $description,
            'status' => 'pending',
            'transaction_id' => $response['CheckoutRequestID'] ?? null,
            'raw_callback_data' => $response,
        ]);

        return $response;
    }
}
