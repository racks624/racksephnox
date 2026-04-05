@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6">
            <h1 class="text-2xl font-bold text-gold mb-6">🔔 Notification Preferences</h1>

            <form method="POST" action="{{ route('notifications.preferences.update') }}">
                @csrf

                <div class="space-y-8">
                    <div>
                        <h3 class="text-lg font-semibold text-gold mb-4 flex items-center gap-2">📧 Email Notifications</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="flex items-center justify-between p-3 bg-gold/5 rounded-lg">
                                <span>Deposit Confirmation</span>
                                <input type="checkbox" name="email_deposit" value="1" {{ $preferences['email_deposit'] ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold">
                            </label>
                            <label class="flex items-center justify-between p-3 bg-gold/5 rounded-lg">
                                <span>Investment Updates</span>
                                <input type="checkbox" name="email_investment" value="1" {{ $preferences['email_investment'] ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold">
                            </label>
                            <label class="flex items-center justify-between p-3 bg-gold/5 rounded-lg">
                                <span>Withdrawal Status</span>
                                <input type="checkbox" name="email_withdrawal" value="1" {{ $preferences['email_withdrawal'] ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold">
                            </label>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gold mb-4 flex items-center gap-2">💾 In‑App Notifications</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="flex items-center justify-between p-3 bg-gold/5 rounded-lg">
                                <span>Deposit Confirmation</span>
                                <input type="checkbox" name="database_deposit" value="1" {{ $preferences['database_deposit'] ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold">
                            </label>
                            <label class="flex items-center justify-between p-3 bg-gold/5 rounded-lg">
                                <span>Investment Updates</span>
                                <input type="checkbox" name="database_investment" value="1" {{ $preferences['database_investment'] ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold">
                            </label>
                            <label class="flex items-center justify-between p-3 bg-gold/5 rounded-lg">
                                <span>Withdrawal Status</span>
                                <input type="checkbox" name="database_withdrawal" value="1" {{ $preferences['database_withdrawal'] ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold">
                            </label>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gold mb-4 flex items-center gap-2">📡 Real‑time (Broadcast)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="flex items-center justify-between p-3 bg-gold/5 rounded-lg">
                                <span>Deposit Confirmation</span>
                                <input type="checkbox" name="broadcast_deposit" value="1" {{ $preferences['broadcast_deposit'] ?? true ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold">
                            </label>
                            <label class="flex items-center justify-between p-3 bg-gold/5 rounded-lg">
                                <span>Investment Updates</span>
                                <input type="checkbox" name="broadcast_investment" value="1" {{ $preferences['broadcast_investment'] ?? true ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold">
                            </label>
                            <label class="flex items-center justify-between p-3 bg-gold/5 rounded-lg">
                                <span>Withdrawal Status</span>
                                <input type="checkbox" name="broadcast_withdrawal" value="1" {{ $preferences['broadcast_withdrawal'] ?? true ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold">
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit" class="btn-golden">💾 Save Preferences</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
