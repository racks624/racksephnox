<?php

namespace App\Services\Deposit;

use App\Models\DepositRequest;
use App\Models\MpesaStatementEntry;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DepositVerificationService
{
    public const PAYBILL = '200999';
    public const ACCOUNT = '0333060036213';

    /**
     * Validate the format of an M-Pesa transaction code.
     * Real codes are ~10 chars, alphanumeric, starting with a letter.
     */
    public function isValidCodeFormat(string $code): bool
    {
        $code = strtoupper(trim($code));
        return (bool) preg_match('/^[A-Z][A-Z0-9]{8,11}$/', $code);
    }

    /**
     * Check whether the code has already been claimed by another deposit.
     */
    public function isDuplicateCode(string $code, ?int $excludeId = null): bool
    {
        $q = DepositRequest::where('transaction_code', strtoupper(trim($code)))
            ->whereIn('verification_status', ['verified', 'pending']);

        if ($excludeId) $q->where('id', '!=', $excludeId);

        return $q->exists();
    }

    /**
     * Try to auto-match a pending deposit against the M-Pesa statement entries.
     * Returns a confidence score (0-100).
     */
    public function attemptAutoMatch(DepositRequest $deposit): int
    {
        if (!$deposit->transaction_code) return 0;

        $code = strtoupper(trim($deposit->transaction_code));
        $amount = (float) $deposit->amount;

        // Exact match on both code and amount → 100
        $exact = MpesaStatementEntry::where('transaction_code', $code)
            ->where('amount', $amount)
            ->where('match_status', 'unmatched')
            ->first();

        if ($exact) {
            $this->linkStatementEntry($exact, $deposit);
            return 100;
        }

        // Match on code only → 90 (amount differs, needs manual review)
        $codeOnly = MpesaStatementEntry::where('transaction_code', $code)
            ->where('match_status', 'unmatched')
            ->first();

        if ($codeOnly) {
            $this->linkStatementEntry($codeOnly, $deposit);
            return 90;
        }

        // Match on amount + within 24h window → 60 (potential match)
        $fuzzy = MpesaStatementEntry::where('amount', $amount)
            ->where('match_status', 'unmatched')
            ->whereBetween('transaction_time', [
                now()->subDays(2),
                now()->addHours(2),
            ])
            ->orderByRaw('ABS(strftime("%s", transaction_time) - ?)', [now()->timestamp])
            ->first();

        if ($fuzzy) {
            $this->linkStatementEntry($fuzzy, $deposit);
            return 60;
        }

        return 0;
    }

    protected function linkStatementEntry(MpesaStatementEntry $entry, DepositRequest $deposit): void
    {
        $entry->match_status       = 'matched';
        $entry->matched_deposit_id = $deposit->id;
        $entry->matched_at         = now();
        $entry->save();

        $deposit->match_confidence = match ($entry->match_status) {
            'matched' => 100,
            default   => 50,
        };
        $deposit->save();
    }

    /**
     * Verify and credit a deposit. Called by admin action OR by auto-match.
     */
    public function verify(DepositRequest $deposit, ?User $admin = null, ?string $notes = null): array
    {
        if ($deposit->verification_status === 'verified') {
            return ['success' => false, 'message' => 'Already verified.'];
        }

        if (!$this->isValidCodeFormat((string) $deposit->transaction_code)) {
            return ['success' => false, 'message' => 'Invalid transaction code format.'];
        }

        if ($this->isDuplicateCode((string) $deposit->transaction_code, $deposit->id)) {
            return ['success' => false, 'message' => 'Transaction code already used.'];
        }

        return DB::transaction(function () use ($deposit, $admin, $notes) {
            $user   = $deposit->user;
            $wallet = $user->wallet ?? Wallet::firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0, 'locked_balance' => 0, 'currency' => 'KES']
            );

            $amount = (float) $deposit->amount;

            $wallet->balance += $amount;
            $wallet->save();

            $deposit->status              = 'completed';
            $deposit->verification_status = 'verified';
            $deposit->approved_by         = $admin?->id;
            $deposit->approved_at         = now();
            $deposit->verified_by         = $admin?->id;
            $deposit->verified_at         = now();
            $deposit->verification_notes  = $notes;
            $deposit->match_confidence    = $deposit->match_confidence ?: 100;
            $deposit->save();

            Transaction::create([
                'user_id'       => $user->id,
                'wallet_id'     => $wallet->id,
                'type'          => 'deposit',
                'amount'        => $amount,
                'balance_after' => $wallet->balance,
                'description'   => "M-Pesa deposit · {$deposit->transaction_code}",
                'reference'     => $deposit->reference ?? ('DEP-' . $deposit->id),
                'status'        => 'completed',
            ]);

            Log::info('Deposit verified', [
                'deposit_id' => $deposit->id,
                'user_id'    => $user->id,
                'amount'     => $amount,
                'admin_id'   => $admin?->id,
            ]);

            return ['success' => true, 'message' => 'Deposit verified and wallet credited.'];
        });
    }

    /**
     * Reject a deposit with a reason.
     */
    public function reject(DepositRequest $deposit, User $admin, string $reason): array
    {
        if ($deposit->verification_status === 'verified') {
            return ['success' => false, 'message' => 'Cannot reject a verified deposit.'];
        }

        $deposit->status              = 'rejected';
        $deposit->verification_status = 'rejected';
        $deposit->rejection_reason    = $reason;
        $deposit->verified_by         = $admin->id;
        $deposit->verified_at         = now();
        $deposit->save();

        Log::warning('Deposit rejected', [
            'deposit_id' => $deposit->id,
            'reason'     => $reason,
            'admin_id'   => $admin->id,
        ]);

        return ['success' => true, 'message' => 'Deposit rejected.'];
    }

    /**
     * Get dashboard stats for admin panel.
     */
    public function adminStats(): array
    {
        return [
            'pending_verification' => DepositRequest::awaitingVerify()->count(),
            'pending_amount'       => (float) DepositRequest::awaitingVerify()->sum('amount'),
            'verified_today'       => DepositRequest::verified()->whereDate('verified_at', today())->count(),
            'verified_amount_today'=> (float) DepositRequest::verified()->whereDate('verified_at', today())->sum('amount'),
            'rejected_today'       => DepositRequest::rejected()->whereDate('verified_at', today())->count(),
            'duplicate_attempts'   => DepositRequest::whereNotNull('transaction_code')
                                        ->whereIn('verification_status', ['rejected'])
                                        ->count(),
        ];
    }

    public function paybill(): string { return self::PAYBILL; }
    public function account(): string  { return self::ACCOUNT; }
}
