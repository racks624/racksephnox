<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DepositRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    use ApiResponse;

    protected $pochiNumbers = [
        '+254 1115335448',
        '+254 1114442426',
        '+254 7162445326',
        '+254 726888888'
    ];

    protected function selectPochiNumber($userId)
    {
        $index = ($userId * 1.61803398875) % count($this->pochiNumbers);
        return $this->pochiNumbers[floor($index)];
    }

    public function getPochiNumber(Request $request)
    {
        $number = $this->selectPochiNumber($request->user()->id);
        return $this->successResponse(['phone_number' => $number]);
    }

    public function submitRequest(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10',
            'transaction_reference' => 'required|string|unique:deposit_requests',
        ]);

        $user = $request->user();
        $phoneNumber = $this->selectPochiNumber($user->id);

        $deposit = DepositRequest::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'transaction_reference' => $request->transaction_reference,
            'phone_number' => $phoneNumber,
            'status' => 'pending',
        ]);

        return $this->successResponse($deposit, 'Deposit request submitted. Awaiting admin verification.', 201);
    }

    public function history(Request $request)
    {
        $deposits = $request->user()->depositRequests()->latest()->paginate(20);
        return $this->successResponse($deposits);
    }
}
