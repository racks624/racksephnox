<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class KycController extends Controller
{
    public function index()
    {
        $documents = KycDocument::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();
        return view('admin.kyc.index', compact('documents'));
    }

    public function show(KycDocument $document)
    {
        return view('admin.kyc.show', compact('document'));
    }

    public function approve(KycDocument $document)
    {
        $document->status = 'verified';
        $document->save();

        // Update user verification status
        $user = $document->user;
        $user->is_verified = true;
        $user->kyc_status = 'verified';
        $user->save();

        Cache::forget('kyc_docs_' . $user->id);

        return redirect()->route('admin.kyc.index')->with('success', 'KYC approved.');
    }

    public function reject(Request $request, KycDocument $document)
    {
        $request->validate(['reason' => 'required|string|max:255']);
        $document->status = 'rejected';
        $document->rejection_reason = $request->reason;
        $document->save();

        Cache::forget('kyc_docs_' . $document->user_id);

        return redirect()->route('admin.kyc.index')->with('success', 'KYC rejected.');
    }
}
