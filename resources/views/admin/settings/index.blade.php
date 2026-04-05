@extends('admin.layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gold mb-6">⚙️ System Settings</h1>

    <div class="card-golden p-6">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm text-gold-400 mb-1">Site Name</label>
                    <input type="text" name="site_name" value="{{ $settings['site_name'] }}" class="input-golden w-full">
                </div>
                <div>
                    <label class="block text-sm text-gold-400 mb-1">Site URL</label>
                    <input type="text" value="{{ $settings['site_url'] }}" class="input-golden w-full" disabled>
                </div>
                <div>
                    <label class="block text-sm text-gold-400 mb-1">Referral Bonus Rate (%)</label>
                    <input type="number" name="referral_bonus_rate" step="0.1" value="{{ $settings['referral_bonus_rate'] }}" class="input-golden w-full">
                </div>
                <div>
                    <label class="block text-sm text-gold-400 mb-1">Minimum Deposit (KES)</label>
                    <input type="number" name="min_deposit" value="{{ $settings['min_deposit'] }}" class="input-golden w-full">
                </div>
                <div>
                    <label class="block text-sm text-gold-400 mb-1">Minimum Withdrawal (KES)</label>
                    <input type="number" name="min_withdrawal" value="{{ $settings['min_withdrawal'] }}" class="input-golden w-full">
                </div>
                <div>
                    <label class="block text-sm text-gold-400 mb-1">Trading Minimum (KES)</label>
                    <input type="number" name="trading_min_amount" value="{{ $settings['trading_min_amount'] }}" class="input-golden w-full">
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="btn-golden">Save Settings</button>
            </div>
        </form>
    </div>

    <div class="card-golden p-6 mt-6">
        <h2 class="text-xl font-bold text-gold mb-4">Maintenance Mode</h2>
        <p class="text-ivory/70 mb-4">Current status: {{ $settings['maintenance_mode'] ? '🔧 Maintenance Mode' : '✅ Live' }}</p>
        <form method="POST" action="{{ route('admin.settings.maintenance') }}">
            @csrf
            <button type="submit" class="btn-golden">{{ $settings['maintenance_mode'] ? 'Bring Live' : 'Enable Maintenance' }}</button>
        </form>
    </div>
</div>
@endsection
