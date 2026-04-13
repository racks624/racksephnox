@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gold">Edit Lottery Game: {{ $game->name }}</h1>
        <a href="{{ route('admin.lottery.index') }}" class="btn-golden text-sm py-2 px-4">← Back</a>
    </div>
    <div class="admin-card p-6">
        <form method="POST" action="{{ route('admin.lottery.update-game', $game) }}">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label class="block text-gold-400 mb-2">Game Name</label><input type="text" name="name" value="{{ old('name', $game->name) }}" class="input-golden w-full" required></div>
                <div><label class="block text-gold-400 mb-2">Description</label><textarea name="description" rows="3" class="input-golden w-full">{{ old('description', $game->description) }}</textarea></div>
                <div><label class="block text-gold-400 mb-2">Min Bet (KES)</label><input type="number" step="1" name="min_bet" value="{{ old('min_bet', $game->min_bet) }}" class="input-golden w-full" required></div>
                <div><label class="block text-gold-400 mb-2">Max Bet (KES)</label><input type="number" step="1" name="max_bet" value="{{ old('max_bet', $game->max_bet) }}" class="input-golden w-full" required></div>
                <div><label class="block text-gold-400 mb-2">Ticket Price (KES)</label><input type="number" step="1" name="ticket_price" value="{{ old('ticket_price', $game->ticket_price) }}" class="input-golden w-full" required></div>
                <div><label class="block text-gold-400 mb-2">Free Spins Award</label><input type="number" name="free_spins_award" value="{{ old('free_spins_award', $game->free_spins_award) }}" class="input-golden w-full"></div>
                <div><label class="block text-gold-400 mb-2">Jackpot Contribution Rate (%)</label><input type="number" step="0.5" name="jackpot_contribution_rate" value="{{ old('jackpot_contribution_rate', $game->jackpot_contribution_rate) }}" class="input-golden w-full"></div>
                <div><label class="block text-gold-400 mb-2">Progressive Jackpot (KES)</label><input type="number" step="1" name="progressive_jackpot" value="{{ old('progressive_jackpot', $game->progressive_jackpot) }}" class="input-golden w-full"></div>
                <div class="flex items-center gap-4"><label class="flex items-center gap-2 text-gold-400"><input type="checkbox" name="is_active" value="1" {{ $game->is_active ? 'checked' : '' }} class="rounded border-gold/30"> Active</label></div>
            </div>
            <div class="mt-6 flex justify-end"><button type="submit" class="btn-golden py-2 px-6">Update Game</button></div>
        </form>
    </div>
</div>
@endsection
