@extends('admin.layouts.app')
@section('content')
<div class="admin-card p-6">
    <h1 class="text-2xl font-bold text-gold mb-4">🎨 Symbol Weight Editor</h1>
    <form method="POST" action="{{ route('admin.lottery.symbols.update') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($symbols as $sym)
            <div class="bg-gold/5 p-3 rounded">
                <div class="flex items-center gap-2"><i class="fas {{ $sym->icon }}"></i><strong>{{ $sym->display_name }}</strong></div>
                <label>Weight: <input type="number" name="weights[{{ $sym->id }}]" value="{{ $sym->weight }}" class="input-golden w-24"></label>
                <label>Multiplier: <input type="number" step="0.1" name="multipliers[{{ $sym->id }}]" value="{{ $sym->multiplier }}" class="input-golden w-20"></label>
            </div>
            @endforeach
        </div>
        <button type="submit" class="btn-golden mt-4">Save Weights</button>
    </form>
    <div class="mt-6 p-4 bg-gold/10 rounded">
        <h3>📊 RTP Simulation</h3>
        <p>Based on current weights and payouts, estimated RTP: <span id="rtpPreview">95%</span></p>
        <button onclick="simulateRTP()" class="btn-outline-silver text-sm">Re‑calculate</button>
    </div>
</div>
<script>
function simulateRTP() { fetch('{{ route("admin.lottery.rtp-simulate") }}').then(r=>r.json()).then(d=>document.getElementById('rtpPreview').innerText=d.rtp+'%'); }
</script>
@endsection
