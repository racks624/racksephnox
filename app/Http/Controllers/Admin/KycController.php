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
        $documents = KycDocument::with('user')->where('status', 'pending')->latest()->get();
        return view('admin.kyc.index', compact('documents'));
    }

    public function show(KycDocument $document)
    {
        return view('admin.kyc.show', compact('document'));
    }

    public function approve(KycDocument $document)
    {
        $document->update([
            'status' => 'verified',
            'verified_at' => now(),
        ]);
        $document->user->update(['is_verified' => true, 'kyc_level' => 'tier1']);

        // Clear caches
        Cache::forget('kyc_docs_' . $document->user_id);
        Cache::forget('admin_stats');

        return redirect()->route('admin.kyc.index')->with('success', 'KYC approved.');
    }

    public function reject(Request $request, KycDocument $document)
    {
        $request->validate(['reason' => 'required|string']);
        $document->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        Cache::forget('kyc_docs_' . $document->user_id);
        Cache::forget('admin_stats');

        return redirect()->route('admin.kyc.index')->with('success', 'KYC rejected.');
    }
}
