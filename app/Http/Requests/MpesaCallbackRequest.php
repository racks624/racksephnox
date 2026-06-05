<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MpesaCallbackRequest extends FormRequest
{
    public function authorize()
    {
        // You may verify IP or secret here
        return true;
    }

    public function rules()
    {
        return [
            'Body.stkCallback.MerchantRequestID' => 'sometimes|string',
            'Body.stkCallback.CheckoutRequestID' => 'sometimes|string',
            'Body.stkCallback.ResultCode' => 'sometimes|integer',
        ];
    }
}
