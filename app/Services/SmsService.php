<?php
namespace App\Services;
use Illuminate\Support\Facades\Log;
class SmsService
{
    public function send($phone, $message)
    {
        Log::info("SMS to {$phone}: {$message}");
        return true;
    }
}
