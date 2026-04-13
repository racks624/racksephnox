@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6">
            <h1 class="text-2xl font-bold text-gold mb-6">Edit Trading Profile</h1>

            <form method="POST" action="{{ route('social-trading.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gold-400 mb-1">Username</label>
                        <input type="text" name="username" value="{{ old('username', $profile->username ?? '') }}" required
                            class="w-full px-4 py-2 border border-gold/30 rounded-lg bg-cosmic-void/50 text-white focus:outline-none focus:ring-2 focus:ring-gold">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gold-400 mb-1">Bio</label>
                        <textarea name="bio" rows="4" class="w-full px-4 py-2 border border-gold/30 rounded-lg bg-cosmic-void/50 text-white focus:outline-none focus:ring-2 focus:ring-gold">{{ old('bio', $profile->bio ?? '') }}</textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center gap-3">
                            <input type="checkbox" name="is_public" value="1" {{ old('is_public', $profile->is_public ?? true) ? 'checked' : '' }} class="form-checkbox h-4 w-4 text-gold">
                            <span class="text-ivory">Make profile public</span>
                        </label>

                        <label class="flex items-center gap-3">
                            <input type="checkbox" name="allow_copy_trading" value="1" {{ old('allow_copy_trading', $profile->allow_copy_trading ?? true) ? 'checked' : '' }} class="form-checkbox h-4 w-4 text-gold">
                            <span class="text-ivory">Allow copy trading</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-golden w-full py-2">
                        Save Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
