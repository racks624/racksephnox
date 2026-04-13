<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    public function send($phone, $message)
    {
        // Integrate with Africa's Talking or Twilio here
        Log::info("SMS to {$phone}: {$message}");
        return true;
    }
}
