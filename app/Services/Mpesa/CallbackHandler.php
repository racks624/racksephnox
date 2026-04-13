<?php

namespace App\Services\Mpesa;

use App\Models\MpesaTransaction;
use App\Models\User;
use App\Notifications\DepositConfirmed;
use Illuminate\Support\Facades\Log;

class CallbackHandler
{
    public function handleStkPush($callbackData)
    {
        Log::info('M-Pesa STK Callback received', $callbackData);

        $body = $callbackData['Body'] ?? [];
        $stkCallback = $body['stkCallback'] ?? [];

        $checkoutRequestId = $stkCallback['CheckoutRequestID'] ?? null;
        $resultCode = $stkCallback['ResultCode'] ?? 1;

        $transaction = MpesaTransaction::where('transaction_id', $checkoutRequestId)->first();

        if (!$transaction) {
            Log::error('M-Pesa callback: transaction not found', $callbackData);
            return;
        }

        if ($resultCode == 0) {
            $metadata = $stkCallback['CallbackMetadata']['Item'] ?? [];
            $mpesaReceiptNumber = null;
            $transactionDate = null;

            foreach ($metadata as $item) {
                $name = $item['Name'] ?? '';
                if ($name == 'MpesaReceiptNumber') $mpesaReceiptNumber = $item['Value'];
                if ($name == 'TransactionDate') $transactionDate = $item['Value'];
            }

            $transaction->update([
                'status' => 'completed',
                'mpesa_receipt_number' => $mpesaReceiptNumber,
                'transaction_date' => $transactionDate ? now()->parse($transactionDate) : null,
                'raw_callback_data' => $callbackData,
            ]);

            $user = User::find($transaction->user_id);
            if ($user && $user->wallet) {
                $user->wallet->credit($transaction->amount, 'M-Pesa deposit: ' . $mpesaReceiptNumber, 'deposit');
                $user->notify(new DepositConfirmed($transaction));
            } else {
                Log::error('User/wallet not found for transaction', ['id' => $transaction->id]);
            }
        } else {
            $transaction->update(['status' => 'failed', 'raw_callback_data' => $callbackData]);
        }
    }
}
