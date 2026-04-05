<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KycDocument;
use App\Services\Kyc\DocumentStorage;
use App\Services\Kyc\VerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class KycController extends Controller
{
    protected $documentStorage;
    protected $verificationService;

    public function __construct(DocumentStorage $documentStorage, VerificationService $verificationService)
    {
        $this->documentStorage = $documentStorage;
        $this->verificationService = $verificationService;
    }

    public function status(Request $request)
    {
        $user = $request->user();
        $documents = Cache::remember('kyc_docs_' . $user->id, 60, function () use ($user) {
            return $user->kycDocuments;
        });
        return $this->successResponse([
            'kyc_level' => $user->kyc_level,
            'is_verified' => $user->is_verified,
            'documents' => $documents,
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'document_type' => 'required|in:national_id,passport,drivers_license,proof_of_address',
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $path = $this->documentStorage->store(
            $request->file('document'),
            $request->user()->id,
            $request->document_type
        );

        KycDocument::create([
            'user_id' => $request->user()->id,
            'document_type' => $request->document_type,
            'document_path' => $path,
            'status' => 'pending',
        ]);

        Cache::forget('kyc_docs_' . $request->user()->id);

        return $this->successResponse(null, 'Document uploaded successfully.', 201);
    }

    public function verifyId(Request $request)
    {
        $request->validate([
            'id_number' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'dob' => 'required|date',
        ]);

        $result = $this->verificationService->verifyIdCard(
            $request->id_number,
            $request->first_name,
            $request->last_name,
            $request->dob
        );

        if ($result['status'] === 'verified') {
            $request->user()->update(['kyc_level' => 'tier1', 'is_verified' => true]);
            Cache::forget('kyc_docs_' . $request->user()->id);
            return $this->successResponse(null, 'Verification successful.');
        }

        return $this->errorResponse('Verification failed', 400);
    }
}
