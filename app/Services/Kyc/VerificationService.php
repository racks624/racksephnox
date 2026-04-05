<?php

namespace App\Services\Kyc;

use App\Models\User;
use App\Models\KycDocument;
use Illuminate\Support\Facades\Http;

class VerificationService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('kyc');
    }

    /**
     * Verify ID card using IdentityPass
     */
    public function verifyIdCard($idNumber, $firstName, $lastName, $dob)
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->config['identitypass']['api_key'],
            'app-id' => $this->config['identitypass']['secret'],
        ])->post($this->config['identitypass']['base_url'] . '/api/v2/kyc/nin', [
            'number' => $idNumber,
            'firstname' => $firstName,
            'lastname' => $lastName,
            'dob' => $dob,
        ]);

        if ($response->failed()) {
            return ['status' => 'error', 'message' => 'Verification service unavailable'];
        }

        $data = $response->json();
        if ($data['status'] ?? false) {
            return ['status' => 'verified', 'data' => $data];
        }

        return ['status' => 'failed', 'message' => $data['message'] ?? 'Verification failed'];
    }
}
