@extends('layouts.app')

@section('content')
<div x-data="preferencesManager()" x-init="init()" class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gold">🔔 Notification Preferences</h1>
                    <p class="text-xs text-gold-400/70 mt-1">Customize how you receive divine notifications</p>
                </div>
                <a href="{{ route('notifications.index') }}" class="text-gold-400 hover:text-gold text-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Notifications
                </a>
            </div>

            <form method="POST" action="{{ route('notifications.preferences.update') }}" @submit="showSaving">
                @csrf

                <div class="space-y-8">
                    <!-- Email Notifications -->
                    <div>
                        <h3 class="text-lg font-semibold text-gold mb-4 flex items-center gap-2">
                            <i class="fas fa-envelope"></i> Email Notifications
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <label class="flex items-center justify-between p-3 bg-gold/5 rounded-lg cursor-pointer hover:bg-gold/10 transition">
                                <span class="text-ivory">💸 Deposit Confirmation</span>
                                <input type="checkbox" name="email_deposit" value="1" {{ $preferences['email_deposit'] ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold rounded">
                            </label>
                            <label class="flex items-center justify-between p-3 bg-gold/5 rounded-lg cursor-pointer hover:bg-gold/10 transition">
                                <span class="text-ivory">📈 Investment Updates</span>
                                <input type="checkbox" name="email_investment" value="1" {{ $preferences['email_investment'] ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold rounded">
                            </label>
                            <label class="flex items-center justify-between p-3 bg-gold/5 rounded-lg cursor-pointer hover:bg-gold/10 transition">
                                <span class="text-ivory">💳 Withdrawal Status</span>
                                <input type="checkbox" name="email_withdrawal" value="1" {{ $preferences['email_withdrawal'] ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold rounded">
                            </label>
                            <label class="flex items-center justify-between p-3 bg-gold/5 rounded-lg cursor-pointer hover:bg-gold/10 transition">
                                <span class="text-ivory">₿ Trading Alerts</span>
                                <input type="checkbox" name="email_trading" value="1" {{ $preferences['email_trading'] ?? true ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold rounded">
                            </label>
                        </div>
                    </div>

                    <!-- In-App Notifications -->
                    <div>
                        <h3 class="text-lg font-semibold text-gold mb-4 flex items-center gap-2">
                            <i class="fas fa-database"></i> In-App Notifications
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <label class="flex items-center justify-between p-3 bg-gold/5 rounded-lg cursor-pointer hover:bg-gold/10 transition">
                                <span class="text-ivory">💸 Deposit Confirmation</span>
                                <input type="checkbox" name="database_deposit" value="1" {{ $preferences['database_deposit'] ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold rounded">
                            </label>
                            <label class="flex items-center justify-between p-3 bg-gold/5 rounded-lg cursor-pointer hover:bg-gold/10 transition">
                                <span class="text-ivory">📈 Investment Updates</span>
                                <input type="checkbox" name="database_investment" value="1" {{ $preferences['database_investment'] ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold rounded">
                            </label>
                            <label class="flex items-center justify-between p-3 bg-gold/5 rounded-lg cursor-pointer hover:bg-gold/10 transition">
                                <span class="text-ivory">💳 Withdrawal Status</span>
                                <input type="checkbox" name="database_withdrawal" value="1" {{ $preferences['database_withdrawal'] ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold rounded">
                            </label>
                            <label class="flex items-center justify-between p-3 bg-gold/5 rounded-lg cursor-pointer hover:bg-gold/10 transition">
                                <span class="text-ivory">₿ Trading Alerts</span>
                                <input type="checkbox" name="database_trading" value="1" {{ $preferences['database_trading'] ?? true ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold rounded">
                            </label>
                        </div>
                    </div>

                    <!-- Real-time Broadcast -->
                    <div>
                        <h3 class="text-lg font-semibold text-gold mb-4 flex items-center gap-2">
                            <i class="fas fa-broadcast-tower"></i> Real-time Broadcast (WebSocket)
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <label class="flex items-center justify-between p-3 bg-gold/5 rounded-lg cursor-pointer hover:bg-gold/10 transition">
                                <span class="text-ivory">💸 Deposit Confirmation</span>
                                <input type="checkbox" name="broadcast_deposit" value="1" {{ $preferences['broadcast_deposit'] ?? true ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold rounded">
                            </label>
                            <label class="flex items-center justify-between p-3 bg-gold/5 rounded-lg cursor-pointer hover:bg-gold/10 transition">
                                <span class="text-ivory">📈 Investment Updates</span>
                                <input type="checkbox" name="broadcast_investment" value="1" {{ $preferences['broadcast_investment'] ?? true ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold rounded">
                            </label>
                            <label class="flex items-center justify-between p-3 bg-gold/5 rounded-lg cursor-pointer hover:bg-gold/10 transition">
                                <span class="text-ivory">💳 Withdrawal Status</span>
                                <input type="checkbox" name="broadcast_withdrawal" value="1" {{ $preferences['broadcast_withdrawal'] ?? true ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold rounded">
                            </label>
                            <label class="flex items-center justify-between p-3 bg-gold/5 rounded-lg cursor-pointer hover:bg-gold/10 transition">
                                <span class="text-ivory">₿ Trading Alerts</span>
                                <input type="checkbox" name="broadcast_trading" value="1" {{ $preferences['broadcast_trading'] ?? true ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold rounded">
                            </label>
                        </div>
                    </div>

                    <!-- Digest Settings -->
                    <div>
                        <h3 class="text-lg font-semibold text-gold mb-4 flex items-center gap-2">
                            <i class="fas fa-newspaper"></i> Digest Settings
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <label class="flex items-center justify-between p-3 bg-gold/5 rounded-lg cursor-pointer hover:bg-gold/10 transition">
                                <span class="text-ivory">📅 Daily Digest</span>
                                <input type="checkbox" name="daily_digest" value="1" {{ $preferences['daily_digest'] ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold rounded">
                            </label>
                            <label class="flex items-center justify-between p-3 bg-gold/5 rounded-lg cursor-pointer hover:bg-gold/10 transition">
                                <span class="text-ivory">📊 Weekly Report</span>
                                <input type="checkbox" name="weekly_report" value="1" {{ $preferences['weekly_report'] ?? true ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold rounded">
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-between items-center">
                    <button type="button" @click="resetToDefault" class="btn-outline-silver px-6 py-2 rounded-lg">
                        Reset to Default
                    </button>
                    <button type="submit" class="btn-golden px-8 py-3" :disabled="saving">
                        <i class="fas fa-save mr-2"></i> 
                        <span x-text="saving ? 'Saving...' : 'Save Preferences'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function preferencesManager() {
    return {
        saving: false,
        
        init() {},
        
        showSaving() {
            this.saving = true;
        },
        
        resetToDefault() {
            if (confirm('Reset all notification preferences to default?')) {
                const checkboxes = document.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(checkbox => {
                    const name = checkbox.name;
                    if (name.includes('email') || name.includes('database') || name.includes('broadcast')) {
                        checkbox.checked = true;
                    } else if (name === 'daily_digest') {
                        checkbox.checked = false;
                    } else if (name === 'weekly_report') {
                        checkbox.checked = true;
                    }
                });
            }
        }
    }
}
</script>
@endsection
