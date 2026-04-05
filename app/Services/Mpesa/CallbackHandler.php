<?php

namespace App\Services\Mpesa;

use App\Models\MpesaTransaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Log;

class CallbackHandler
{
    public function handleStkPush($callbackData)
    {
        Log::info('M-Pesa STK Callback received', $callbackData);

        $body = $callbackData['Body'] ?? [];
        $stkCallback = $body['stkCallback'] ?? [];

        $merchantRequestId = $stkCallback['MerchantRequestID'] ?? null;
        $checkoutRequestId = $stkCallback['CheckoutRequestID'] ?? null;
        $resultCode = $stkCallback['ResultCode'] ?? 1;
        $resultDesc = $stkCallback['ResultDesc'] ?? 'Unknown error';

        // Find transaction by either reference
        $transaction = MpesaTransaction::where('reference', $merchantRequestId)
            ->orWhere('transaction_id', $checkoutRequestId)
            ->first();

        if (!$transaction) {
            Log::error('M-Pesa callback: transaction not found', $callbackData);
            return;
        }

        if ($resultCode == 0) {
            // Success
            $callbackMetadata = $stkCallback['CallbackMetadata']['Item'] ?? [];
            $mpesaReceiptNumber = null;
            $transactionDate = null;
            $phone = null;
            $amount = null;

            foreach ($callbackMetadata as $item) {
                $name = $item['Name'] ?? '';
                if ($name == 'MpesaReceiptNumber') $mpesaReceiptNumber = $item['Value'];
                if ($name == 'TransactionDate') $transactionDate = $item['Value'];
                if ($name == 'PhoneNumber') $phone = $item['Value'];
                if ($name == 'Amount') $amount = $item['Value'];
            }

            // Update transaction record
            $transaction->update([
                'status' => 'completed',
                'mpesa_receipt_number' => $mpesaReceiptNumber,
                'transaction_date' => $transactionDate,
                'raw_callback_data' => $callbackData,
            ]);

            // Credit the user's wallet
            $user->notify(new AppNotificationsDepositConfirmed($transaction));
            $user->notify(new AppNotificationsDepositConfirmed($transaction));
            if ($transaction->user_id) {
                $user = User::find($transaction->user_id);
                if ($user && $user->wallet) {
                    $user->wallet->credit($transaction->amount, 'M-Pesa deposit: ' . $mpesaReceiptNumber, 'deposit');
                } else {
                    Log::error('User or wallet not found for transaction', ['transaction_id' => $transaction->id]);
                }
            }
        } else {
            // Failed
            $transaction->update([
                'status' => 'failed',
                'raw_callback_data' => $callbackData,
            ]);
        }
    }
}
