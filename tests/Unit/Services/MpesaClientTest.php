<?php

namespace Tests\Unit\Services;

use App\Services\Mpesa\MpesaClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MpesaClientTest extends TestCase
{
    /** @test */
    public function it_gets_an_access_token()
    {
        Http::fake([
            'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials' => Http::response([
                'access_token' => 'test_token',
                'expires_in' => 3599,
            ], 200),
        ]);

        $client = new MpesaClient();
        $token = $client->getAccessToken();

        $this->assertEquals('test_token', $token);
    }
}
