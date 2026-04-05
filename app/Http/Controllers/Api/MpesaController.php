<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Mpesa\StkPush;
use App\Services\Mpesa\CallbackHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    protected $stkPush;
    protected $callbackHandler;

    public function __construct(StkPush $stkPush, CallbackHandler $callbackHandler)
    {
        $this->stkPush = $stkPush;
        $this->callbackHandler = $callbackHandler;
    }

    public function stkPush(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|regex:/^254[0-9]{9}$/',
            'amount' => 'required|numeric|min:10|max:150000',
        ]);

        $user = $request->user();
        $reference = 'DEP' . $user->id . time();

        try {
            $this->stkPush->initiate($request->phone, $request->amount, $reference);
            return $this->successResponse(null, 'STK Push sent. Check your phone.');
        } catch (\Exception $e) {
            Log::error('API Deposit initiation failed: ' . $e->getMessage());
            return $this->errorResponse('Could not initiate payment.', 500);
        }
    }

    public function callback(Request $request)
    {
        Log::info('API M-Pesa callback received', $request->all());
        $this->callbackHandler->handleStkPush($request->all());
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }

    public function status($id)
    {
        $transaction = auth()->user()->mpesaTransactions()->findOrFail($id);
        return $this->successResponse($transaction);
    }
}
