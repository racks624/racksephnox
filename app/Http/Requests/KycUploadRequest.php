<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KycUploadRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'document_type' => 'required|in:national_id,passport,drivers_license,proof_of_address',
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }
}
