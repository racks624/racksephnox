<?php

namespace App\Http\Controllers;

use App\Models\KycDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class KycController extends Controller
{
    public function index()
    {
        $documents = Auth::user()->kycDocuments;
        return view('kyc', compact('documents'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'document_type' => 'required|in:national_id,passport,drivers_license,proof_of_address',
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $path = $request->file('document')->store('kyc_documents', 'public');

        KycDocument::create([
            'user_id' => Auth::id(),
            'document_type' => $request->document_type,
            'document_path' => $path,
            'status' => 'pending',
        ]);

        return redirect()->route('kyc')->with('success', 'Document uploaded. Awaiting admin review.');
    }
}
