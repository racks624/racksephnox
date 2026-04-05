@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- KYC Status -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">KYC Verification</h3>
                <p>Current Level: <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded">{{ auth()->user()->kyc_level }}</span></p>
                @if(auth()->user()->is_verified)
                    <p class="text-green-600 mt-2">✅ Verified</p>
                @else
                    <p class="text-yellow-600 mt-2">⏳ Pending verification</p>
                @endif
            </div>
        </div>

        <!-- Upload Document -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Upload Document</h3>
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
                @endif
                <form method="POST" action="{{ route('kyc.upload') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Document Type</label>
                            <select name="document_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="national_id">National ID</option>
                                <option value="passport">Passport</option>
                                <option value="drivers_license">Driver's License</option>
                                <option value="proof_of_address">Proof of Address</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">File</label>
                            <input type="file" name="document" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Upload</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Submitted Documents -->
        @if($documents->count())
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Submitted Documents</h3>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Uploaded</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($documents as $doc)
                        <tr>
                            <td class="px-6 py-4">{{ str_replace('_', ' ', ucfirst($doc->document_type)) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded
                                    @if($doc->status == 'verified') bg-green-100 text-green-800
                                    @elseif($doc->status == 'rejected') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ ucfirst($doc->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ $doc->created_at->format('Y-m-d') }}</td>
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
