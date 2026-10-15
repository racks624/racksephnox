<?php

namespace App\Http\Controllers;

use App\Models\DepositRequest;
use App\Services\Deposit\DepositVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DepositController extends Controller
{
    public function __construct(
        protected DepositVerificationService $service
    ) {}

    /**
     * Show the deposit form with Paybill details.
     */
    public function form()
    {
        $paybill = $this->service->paybill();
        $account = $this->service->account();

        // Recent user deposits
        $recent = DepositRequest::forUser(Auth::id())
            ->latest()
            ->take(10)
            ->get();

        return view('deposit.form', compact('paybill', 'account', 'recent'));
    }

    /**
     * Handle the user's submission of a transaction code.
     */
    public function submit(Request $request)
    {
        $data = $request->validate([
            'amount'           => 'required|numeric|min:10|max:1000000',
            'transaction_code' => 'required|string|min:8|max:16',
            'phone'            => 'required|string|min:10|max:15',
            'notes'            => 'nullable|string|max:500',
        ]);

        $code = strtoupper(trim($data['transaction_code']));

        if (!$this->service->isValidCodeFormat($code)) {
            return back()
                ->withErrors(['transaction_code' => 'Invalid M-Pesa transaction code. Format: 10 letters/digits starting with a letter (e.g., SFE4A8B2C1).'])
                ->withInput();
        }

        if ($this->service->isDuplicateCode($code)) {
            return back()
                ->withErrors(['transaction_code' => 'This transaction code has already been submitted.'])
                ->withInput();
        }

        $deposit = DepositRequest::create([
            'user_id'             => Auth::id(),
            'amount'              => (float) $data['amount'],
            'method'              => 'mpesa',
            'reference'           => 'DEP-' . strtoupper(Str::random(8)),
            'phone'               => $data['phone'],
            'transaction_code'    => $code,
            'paybill_number'      => $this->service->paybill(),
            'account_number'      => $this->service->account(),
            'status'              => 'pending',
            'verification_status' => 'pending',
            'user_submitted_at'   => now(),
            'notes'               => $data['notes'] ?? null,
        ]);

        // Attempt auto-match immediately (in case a statement was already uploaded)
        $this->service->attemptAutoMatch($deposit);

        return redirect()
            ->route('deposit.status', $deposit->id)
            ->with('success', 'Deposit submitted. Admin will verify within 24 hours.');
    }

    /**
     * Show the status of a specific deposit.
     */
    public function status(DepositRequest $deposit)
    {
        abort_unless($deposit->user_id === Auth::id(), 403);

        return view('deposit.status', compact('deposit'));
    }
}
