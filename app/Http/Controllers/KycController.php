<?php

namespace App\Http\Controllers;

use App\Models\KycDocument;
use App\Services\Kyc\DocumentStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class KycController extends Controller
{
    protected $documentStorage;

    public function __construct(DocumentStorage $documentStorage)
    {
        $this->documentStorage = $documentStorage;
    }

    public function index()
    {
        // Cache KYC documents for 1 minute
        $documents = Cache::remember('kyc_docs_' . auth()->id(), 60, function () {
            return auth()->user()->kycDocuments;
        });

        return view('kyc', compact('documents'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'document_type' => 'required|in:national_id,passport,drivers_license,proof_of_address',
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $path = $this->documentStorage->store(
            $request->file('document'),
            auth()->id(),
            $request->document_type
        );

        KycDocument::create([
            'user_id' => auth()->id(),
            'document_type' => $request->document_type,
            'document_path' => $path,
            'status' => 'pending',
        ]);

        // Clear cache for this user's KYC documents
        Cache::forget('kyc_docs_' . auth()->id());

        return redirect()->route('kyc')->with('success', 'Document uploaded successfully.');
    }
}
