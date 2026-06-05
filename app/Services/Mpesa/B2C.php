<?php

namespace App\Services\Mpesa;

use Illuminate\Support\Facades\Log;

class B2C extends MpesaClient
{
    public function send($phone, $amount, $reference, $remarks = 'Withdrawal')
    {
        $data = [
            'InitiatorName' => $this->config['initiator_name'],
            'SecurityCredential' => $this->config['initiator_password'],
            'CommandID' => 'BusinessPayment',
            'Amount' => (int) $amount,
            'PartyA' => $this->config['shortcode'],
            'PartyB' => $phone,
            'Remarks' => $remarks,
            'QueueTimeOutURL' => $this->config['callback_url']['b2c_timeout'] ?? '',
            'ResultURL' => $this->config['callback_url']['b2c'],
            'Occasion' => $reference,
        ];

        return $this->request('b2c', $data);
    }
}
