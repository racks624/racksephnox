<?php
namespace App\Http\Controllers;

use App\Models\KycDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KycController extends Controller
{
    public function index()
    {
        $documents = KycDocument::where('user_id', Auth::id())->latest()->get();
        return view('kyc.index', compact('documents'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'document_type' => 'required|in:national_id,passport,drivers_license,proof_of_address',
            'document' => 'required|file|mimes:jpg,png,pdf|max:5120',
        ]);
        $path = $request->file('document')->store('kyc/' . Auth::id(), 'public');
        KycDocument::create([
            'user_id' => Auth::id(),
            'document_type' => $request->document_type,
            'document_path' => $path,
            'status' => 'pending',
        ]);
        return back()->with('success', 'Document uploaded successfully.');
    }
}
