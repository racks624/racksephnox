@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gold">Edit Symbol: {{ $symbol->display_name }}</h1>
        <a href="{{ route('admin.lottery.index') }}" class="btn-golden text-sm py-2 px-4">← Back</a>
    </div>
    <div class="admin-card p-6">
        <form method="POST" action="{{ route('admin.lottery.update-symbol', $symbol) }}">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label class="block text-gold-400 mb-2">Symbol Name (internal)</label><input type="text" name="name" value="{{ old('name', $symbol->name) }}" class="input-golden w-full" required></div>
                <div><label class="block text-gold-400 mb-2">Display Name (emoji + text)</label><input type="text" name="display_name" value="{{ old('display_name', $symbol->display_name) }}" class="input-golden w-full" required></div>
                <div><label class="block text-gold-400 mb-2">FontAwesome Icon Class</label><input type="text" name="icon" value="{{ old('icon', $symbol->icon) }}" class="input-golden w-full" required></div>
                <div><label class="block text-gold-400 mb-2">Weight (probability)</label><input type="number" name="weight" value="{{ old('weight', $symbol->weight) }}" class="input-golden w-full" required min="1"></div>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 text-gold-400"><input type="checkbox" name="is_wild" value="1" {{ $symbol->is_wild ? 'checked' : '' }}> Is Wild</label>
                    <label class="flex items-center gap-2 text-gold-400"><input type="checkbox" name="is_scatter" value="1" {{ $symbol->is_scatter ? 'checked' : '' }}> Is Scatter</label>
                </div>
            </div>
            <div class="mt-6 flex justify-end"><button type="submit" class="btn-golden py-2 px-6">Update Symbol</button></div>
        </form>
    </div>
</div>
@endsection
