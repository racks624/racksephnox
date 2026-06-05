<?php

namespace App\Services\Mpesa;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class MpesaClient
{
    protected $config;
    protected $baseUrl;

    public function __construct()
    {
        $this->config = config('mpesa');
        $env = $this->config['environment'];
        $this->baseUrl = $this->config['urls'][$env];
    }

    public function getAccessToken()
    {
        return Cache::remember('mpesa_access_token', 3500, function () {
            $response = Http::withBasicAuth(
                $this->config['consumer_key'],
                $this->config['consumer_secret']
            )->get($this->baseUrl['oauth']);

            if ($response->failed()) {
                \Log::error('M-Pesa token error: ' . $response->body());
                throw new \Exception('Failed to get M-Pesa access token');
            }

            return $response->json('access_token');
        });
    }

    public function generateStkPassword($timestamp)
    {
        $shortcode = $this->config['shortcode'];
        $passkey = $this->config['passkey'];
        return base64_encode($shortcode . $passkey . $timestamp);
    }

    public function request($endpoint, $data)
    {
        $token = $this->getAccessToken();
        $url = $this->baseUrl[$endpoint] ?? $endpoint;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post($url, $data);

        if ($response->failed()) {
            \Log::error('M-Pesa API error: ' . $response->body());
            throw new \Exception('M-Pesa API error: ' . $response->body());
        }

        return $response->json();
    }
}
