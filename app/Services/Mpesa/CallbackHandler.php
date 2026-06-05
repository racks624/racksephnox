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
        if (!$transaction) { Log::error('M-Pesa callback: transaction not found', $callbackData); return; }
        if ($resultCode == 0) {
            $metadata = $stkCallback['CallbackMetadata']['Item'] ?? [];
            $mpesaReceiptNumber = null;
            foreach ($metadata as $item) {
                if (($item['Name'] ?? '') == 'MpesaReceiptNumber') $mpesaReceiptNumber = $item['Value'] ?? null;
            }
            $transaction->update(['status' => 'completed', 'mpesa_receipt_number' => $mpesaReceiptNumber, 'raw_callback_data' => $callbackData]);
            $user = User::find($transaction->user_id);
            if ($user && $user->wallet) {
                $user->wallet->credit($transaction->amount, 'M-Pesa deposit: ' . $mpesaReceiptNumber, 'deposit');
                $user->notify(new DepositConfirmed($transaction));
            }
        } else {
            $transaction->update(['status' => 'failed', 'raw_callback_data' => $callbackData]);
        }
    }
}
