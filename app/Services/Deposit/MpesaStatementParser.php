<?php

namespace App\Services\Deposit;

use App\Models\MpesaStatement;
use App\Models\MpesaStatementEntry;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MpesaStatementParser
{
    /**
     * Parse and store an M-Pesa statement CSV or TSV.
     * Returns the MpesaStatement model with match counts.
     */
    public function parse(UploadedFile $file, ?int $userId = null): MpesaStatement
    {
        $contents = file_get_contents($file->getRealPath());
        $lines    = preg_split('/\r\n|\r|\n/', $contents);
        $lines    = array_filter($lines, fn ($l) => trim($l) !== '');

        $header = null;
        $rows   = [];

        foreach ($lines as $line) {
            // M-Pesa statements are usually comma-separated
            $cols = str_getcsv($line, ',');

            // Skip metadata lines until we find the real header
            if (!$header) {
                $first = strtolower(trim($cols[0] ?? ''));
                if (str_contains($first, 'receipt') || str_contains($first, 'transaction')) {
                    $header = array_map('trim', $cols);
                }
                continue;
            }

            if (count($cols) < 3) continue;
            $rows[] = array_combine(
                array_slice($header, 0, count($cols)),
                array_slice($cols, 0, count($header))
            );
        }

        return DB::transaction(function () use ($file, $rows, $userId) {
            $statement = MpesaStatement::create([
                'file_name'        => $file->getClientOriginalName(),
                'file_path'        => 'storage/mpesa_statements/' . $file->hashName(),
                'total_rows'       => count($rows),
                'matched_rows'     => 0,
                'unmatched_rows'   => 0,
                'total_amount'     => 0,
                'uploaded_by'      => $userId,
                'status'           => 'processing',
            ]);

            $totalAmount = 0;

            foreach ($rows as $row) {
                $receipt = $this->pick($row, ['receipt no.', 'receipt no', 'receipt', 'receipt_number']);
                $code    = $this->pick($row, ['transaction code', 'transaction id', 'transaction_code']);
                $time    = $this->pick($row, ['completion time', 'transaction time', 'time', 'date']);
                $amount  = $this->pick($row, ['paid in', 'amount', 'paid_in', 'withdrawn']);
                $payer   = $this->pick($row, ['details', 'payer name', 'name']);
                $phone   = $this->pick($row, ['phone', 'mobile', 'msisdn']);
                $account = $this->pick($row, ['account number', 'account', 'acc no']);

                if (!$receipt && !$code) continue;

                $amt = (float) preg_replace('/[^\d.]/', '', (string) $amount);
                $totalAmount += $amt;

                MpesaStatementEntry::create([
                    'statement_id'     => $statement->id,
                    'receipt_number'   => (string) ($receipt ?? $code),
                    'transaction_code' => $code ? strtoupper(trim($code)) : null,
                    'transaction_time' => $this->parseTime($time),
                    'amount'           => $amt,
                    'payer_name'       => $payer,
                    'payer_phone'      => $payer,
                    'account_number'   => $account,
                    'paybill_number'   => DepositVerificationService::PAYBILL,
                    'match_status'     => 'unmatched',
                    'raw_row'          => $row,
                ]);
            }

            $statement->update([
                'unmatched_rows' => $statement->entries()->count(),
                'total_amount'   => $totalAmount,
                'status'         => 'ready',
            ]);

            Log::info('M-Pesa statement parsed', [
                'statement_id' => $statement->id,
                'rows'         => $statement->total_rows,
                'amount'       => $totalAmount,
            ]);

            return $statement->fresh();
        });
    }

    /**
     * Auto-match all unmatched statement entries against pending deposits.
     */
    public function autoMatchAll(MpesaStatement $statement, DepositVerificationService $service): int
    {
        $matches = 0;
        $deposits = \App\Models\DepositRequest::awaitingVerify()
            ->whereNotNull('transaction_code')
            ->get();

        foreach ($deposits as $deposit) {
            $confidence = $service->attemptAutoMatch($deposit);
            if ($confidence >= 90) $matches++;
        }

        $statement->update([
            'matched_rows'   => $statement->entries()->where('match_status', 'matched')->count(),
            'unmatched_rows' => $statement->entries()->where('match_status', 'unmatched')->count(),
        ]);

        return $matches;
    }

    /* ── helpers ── */
    protected function pick(array $row, array $keys): ?string
    {
        foreach ($keys as $k) {
            foreach ($row as $col => $val) {
                if (stripos((string) $col, $k) !== false && $val !== '') {
                    return trim((string) $val);
                }
            }
        }
        return null;
    }

    protected function parseTime(?string $raw): ?string
    {
        if (!$raw) return null;
        $ts = strtotime($raw);
        return $ts ? date('Y-m-d H:i:s', $ts) : null;
    }
}
