@extends('layouts.app')
@section('content')
<div class="py-12 text-center"><div class="card-golden p-6 inline-block"><h1 class="text-2xl font-bold text-gold mb-4">🎡 Daily Bonus Wheel</h1><div class="w-64 h-64 rounded-full bg-gradient-to-r from-gold-500 to-gold-700 mx-auto flex items-center justify-center"><span class="text-white text-4xl">🎁</span></div>@if($canSpin)<button onclick="spinWheel()" class="btn-golden mt-6">Spin Now</button>@else<p class="text-ivory/50 mt-4">Come back tomorrow for your next spin!</p>@endif</div></div>
<script>async function spinWheel(){const res=await fetch('{{ route("lottery.bonus-wheel.spin") }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}});const data=await res.json();if(data.success)alert(`You won ${data.prize.value} ${data.prize.type === 'kes' ? 'KES' : 'Free Spin(s)'}!`);else alert(data.message);location.reload();}</script>
@endsection
