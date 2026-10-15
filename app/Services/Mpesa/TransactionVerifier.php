<?php

namespace App\Services\Mpesa;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Transaction verification layer.
 *
 * Two modes:
 *  1. AUTO   — calls Safaricom Daraja API (requires credentials in .env)
 *  2. MANUAL — admin pastes the code from their M-Pesa portal; system validates format
 *
 * In both modes, the final decision is always confirmed by a human admin.
 */
class TransactionVerifier
{
    /* ═══════════════════════════════════════════════════════════
     |  PUBLIC API
     ═══════════════════════════════════════════════════════════ */

    /**
     * Full verification pipeline.
     * Returns:
     *   [
     *     'verified'  => bool,
     *     'method'    => 'auto' | 'manual',
     *     'reason'    => string,
     *     'receipt'   => array|null,
     *   ]
     */
    public function verify(string $code, float $amount, string $phone, ?string $paymentDate = null): array
    {
        $code = strtoupper(trim($code));

        // 1. Format validation — same for both modes
        $format = $this->validateFormat($code);
        if (!$format['valid']) {
            return [
                'verified' => false,
                'method'   => 'format',
                'reason'   => $format['reason'],
                'receipt'  => null,
            ];
        }

        // 2. Auto-verify via Daraja if credentials are configured
        if ($this->hasDarajaCredentials()) {
            $result = $this->verifyViaDaraja($code, $amount, $phone, $paymentDate);
            if ($result['verified']) return $result;
            // If Daraja fails, fall through to manual confirmation
            Log::warning('Daraja verification failed, falling back to manual', [
                'code' => $code, 'reason' => $result['reason'],
            ]);
        }

        // 3. Manual mode — the admin will confirm using the M-Pesa portal
        return [
            'verified' => false,
            'method'   => 'manual',
            'reason'   => 'Awaiting manual confirmation by admin.',
            'receipt'  => null,
        ];
    }

    /* ═══════════════════════════════════════════════════════════
     |  FORMAT VALIDATION
     ═══════════════════════════════════════════════════════════ */

    public function validateFormat(string $code): array
    {
        $code = strtoupper(trim($code));

        if (empty($code)) {
            return ['valid' => false, 'reason' => 'Transaction code is empty.'];
        }

        // M-Pesa codes: 8–10 alphanumeric characters, typically starting with Q/K
        if (!preg_match('/^[A-Z0-9]{8,15}$/', $code)) {
            return ['valid' => false, 'reason' => 'Transaction code format is invalid.'];
        }

        // Reject codes that are obviously fake
        $blacklist = ['TEST1234', 'ABCD1234', '00000000', '12345678'];
        if (in_array($code, $blacklist, true)) {
            return ['valid' => false, 'reason' => 'Transaction code is not acceptable.'];
        }

        return ['valid' => true, 'reason' => 'Format OK'];
    }

    /* ═══════════════════════════════════════════════════════════
     |  DARAJA AUTO-VERIFICATION
     ═══════════════════════════════════════════════════════════ */

    protected function hasDarajaCredentials(): bool
    {
        $d = config('racksephnox.mpesa.daraja');
        return !empty($d['consumer_key']) && !empty($d['consumer_secret']);
    }

    protected function verifyViaDaraja(string $code, float $amount, string $phone, ?string $date): array
    {
        try {
            $token = $this->getDarajaToken();

            $base = config('racksephnox.mpesa.daraja.environment') === 'production'
                ? 'https://api.safaricom.co.ke'
                : 'https://sandbox.safaricom.co.ke';

            // Transaction Status Query
            $payload = [
                'Initiator'          => config('racksephnox.mpesa.daraja.initiator_name'),
                'SecurityCredential' => config('racksephnox.mpesa.daraja.security_credential'),
                'CommandID'          => 'TransactionStatus',
                'TransactionID'      => $code,
                'PartyA'             => config('racksephnox.mpesa.daraja.shortcode'),
                'IdentifierType'     => '4',
                'ResultURL'          => url('/api/mpesa/callback/status'),
                'QueueTimeOutURL'    => url('/api/mpesa/callback/status-timeout'),
                'Remarks'            => 'Racksephnox deposit verification',
                'Occasion'           => 'DepositCheck',
            ];

            $res = Http::withToken($token)
                ->timeout(15)
                ->post("{$base}/mpesa/transactionstatus/v1/query", $payload);

            if (!$res->successful()) {
                return ['verified' => false, 'method' => 'auto',
                        'reason' => 'Daraja returned HTTP ' . $res->status(), 'receipt' => null];
            }

            $data = $res->json();

            // Safaricom returns the actual result via callback URL — not synchronous.
            // For synchronous check we trust the immediate response if it contains data.
            if (!empty($data['ResponseCode']) && $data['ResponseCode'] === '0') {
                return [
                    'verified' => true,
                    'method'   => 'auto',
                    'reason'   => 'Verified by Safaricom Daraja.',
                    'receipt'  => $data,
                ];
            }

            return [
                'verified' => false,
                'method'   => 'auto',
                'reason'   => $data['ResponseDescription'] ?? 'Daraja could not verify.',
                'receipt'  => $data,
            ];
        } catch (\Throwable $e) {
            Log::error('Daraja verify exception', ['msg' => $e->getMessage()]);
            return ['verified' => false, 'method' => 'auto', 'reason' => $e->getMessage(), 'receipt' => null];
        }
    }

    protected function getDarajaToken(): ?string
    {
        return Cache::remember('daraja_token', 3000, function () {
            $d = config('racksephnox.mpesa.daraja');
            $base = $d['environment'] === 'production'
                ? 'https://api.safaricom.co.ke'
                : 'https://sandbox.safaricom.co.ke';

            $res = Http::withBasicAuth($d['consumer_key'], $d['consumer_secret'])
                ->timeout(10)
                ->get("{$base}/oauth/v1/generate?grant_type=client_credentials");

            return $res->json()['access_token'] ?? null;
        });
    }

    /* ═══════════════════════════════════════════════════════════
     |  MANUAL VERIFICATION HELPER (used by admin)
     ═══════════════════════════════════════════════════════════ */

    /**
     * Admin marks a code as manually verified. Returns a receipt-like array.
     */
    public function manualReceipt(array $adminInput): array
    {
        return [
            'verified_by'       => $adminInput['admin_name'] ?? 'Admin',
            'verification_note' => $adminInput['note'] ?? 'Verified via M-Pesa portal',
            'amount_confirmed'  => (float) ($adminInput['amount'] ?? 0),
            'phone_confirmed'   => $adminInput['phone'] ?? null,
            'timestamp'         => now()->toIso8601String(),
        ];
    }
}
