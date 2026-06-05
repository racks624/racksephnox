<?php

namespace App\Services;

class WithdrawalService
{
    /**
     * Calculate withdrawal fee based on amount
     * Golden Tiered Fee Structure
     */
    public static function calculateFee($amount)
    {
        $amount = (float) $amount;
        
        if ($amount >= 530 && $amount <= 2500) return 26;
        if ($amount >= 2501 && $amount <= 8000) return 52;
        if ($amount >= 8001 && $amount <= 16000) return 107;
        if ($amount >= 16001 && $amount <= 32000) return 1008;
        if ($amount >= 32001 && $amount <= 64000) return 3500;
        if ($amount >= 64001 && $amount <= 132000) return 10000;
        if ($amount >= 132001 && $amount <= 500000) return 25000;
        if ($amount >= 500001 && $amount <= 1000000) return 88000;
        
        return 0;
    }

    /**
     * Validate withdrawal amount
     */
    public static function validateWithdrawal($amount)
    {
        if ($amount < 530) {
            return ['valid' => false, 'message' => 'Minimum withdrawal amount is KES 530'];
        }
        if ($amount > 1000000) {
            return ['valid' => false, 'message' => 'Maximum withdrawal amount is KES 1,000,000 per request'];
        }
        return ['valid' => true];
    }

    /**
     * Get net amount after fee
     */
    public static function getNetAmount($amount)
    {
        $fee = self::calculateFee($amount);
        return $amount - $fee;
    }
}
