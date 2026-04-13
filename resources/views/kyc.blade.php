@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- KYC Status Card -->
        <div class="card-golden p-6 mb-6">
            <h3 class="text-xl font-bold text-gold mb-4">KYC Verification</h3>
            <div class="flex items-center gap-4">
                <span class="text-sm text-ivory/70">Current Level:</span>
                <span class="px-3 py-1 rounded-full bg-gold/20 text-gold text-sm">{{ auth()->user()->kyc_level ?? 'Basic' }}</span>
                @if(auth()->user()->is_verified)
                    <span class="px-3 py-1 rounded-full bg-green-500/20 text-green-400 text-sm">✓ Verified</span>
                @else
                    <span class="px-3 py-1 rounded-full bg-yellow-500/20 text-yellow-400 text-sm">⏳ Pending</span>
                @endif
            </div>
        </div>

        <!-- Upload Document Card -->
        <div class="card-golden p-6 mb-6">
            <h3 class="text-xl font-bold text-gold mb-4">Upload Document</h3>
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-500/20 border border-green-500 rounded-lg text-green-400">{{ session('success') }}</div>
            @endif
            <form method="POST" action="{{ route('kyc.upload') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gold-400 mb-1">Document Type</label>
                        <select name="document_type" class="w-full px-4 py-2 border border-gold/30 rounded-lg bg-cosmic-void/50 text-white focus:outline-none focus:ring-2 focus:ring-gold">
                            <option value="national_id">National ID</option>
                            <option value="passport">Passport</option>
                            <option value="drivers_license">Driver's License</option>
                            <option value="proof_of_address">Proof of Address</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gold-400 mb-1">File (jpg, png, pdf, max 5MB)</label>
                        <input type="file" name="document" class="w-full text-sm text-ivory/70 file:mr-2 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-gold/20 file:text-gold hover:file:bg-gold/30">
                    </div>
                </div>
                <button type="submit" class="btn-golden w-full md:w-auto">Upload Document</button>
            </form>
        </div>

        <!-- Submitted Documents Table -->
        @if($documents->count())
        <div class="card-golden p-6">
            <h3 class="text-xl font-bold text-gold mb-4">Submitted Documents</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-gold/30">
                        <tr>
                            <th class="px-4 py-3 text-left text-gold-400">Type</th>
                            <th class="px-4 py-3 text-left text-gold-400">Status</th>
                            <th class="px-4 py-3 text-left text-gold-400">Uploaded</th>
                        </thead>
                    <tbody class="divide-y divide-gold/20">
                        @foreach($documents as $doc)
                        <tr>
                            <td class="px-4 py-3 text-ivory">{{ str_replace('_', ' ', ucfirst($doc->document_type)) }}</td>
                            <td class="px-4 py-3">
                                @if($doc->status == 'verified')
                                    <span class="px-2 py-1 rounded-full bg-green-500/20 text-green-400">✓ Verified</span>
                                @elseif($doc->status == 'rejected')
                                    <span class="px-2 py-1 rounded-full bg-red-500/20 text-red-400">✗ Rejected</span>
                                @else
                                    <span class="px-2 py-1 rounded-full bg-yellow-500/20 text-yellow-400">⏳ Pending</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-ivory/70">{{ $doc->created_at->format('Y-m-d') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
