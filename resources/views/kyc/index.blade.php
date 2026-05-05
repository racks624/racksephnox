@extends('layouts.app')
@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6">
            <h1 class="text-2xl font-bold text-gold mb-4">🪪 KYC Verification</h1>
            @if(session('success'))<div class="bg-green-500/20 p-3 rounded-lg mb-4">{{ session('success') }}</div>@endif
            <form method="POST" action="{{ route('kyc.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-gold-400">Document Type</label><select name="document_type" class="input-golden w-full">@foreach(['national_id','passport','drivers_license','proof_of_address'] as $type)<option value="{{ $type }}">{{ ucfirst(str_replace('_',' ',$type)) }}</option>@endforeach</select></div>
                    <div><label class="block text-gold-400">File (JPG, PNG, PDF, max 5MB)</label><input type="file" name="document" class="input-golden w-full"></div>
                </div>
                <button type="submit" class="btn-golden mt-4">Upload Document</button>
            </form>
            @if($documents->count())
            <div class="mt-8">
                <h2 class="text-xl font-bold text-gold mb-3">Submitted Documents</h2>
                <div class="space-y-2">
                    @foreach($documents as $doc)
                    <div class="flex justify-between items-center border-b border-gold/20 py-2">
                        <span>{{ ucfirst(str_replace('_',' ',$doc->document_type)) }}</span>
                        <span class="text-sm @if($doc->status == 'verified') text-green-400 @elseif($doc->status == 'rejected') text-red-400 @else text-yellow-400 @endif">{{ ucfirst($doc->status) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
