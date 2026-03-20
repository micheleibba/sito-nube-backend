@extends('layouts.app')

@section('title', 'Modifica Sezione - Nube')

@section('content')
<div class="flex items-center gap-2 text-sm mb-6">
    <a href="{{ route('chat-qa.index') }}" class="text-surface-500 hover:text-white transition">Chat Q&A</a>
    <svg class="w-3.5 h-3.5 text-surface-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
    <span class="text-surface-300">Modifica sezione</span>
</div>

<div class="max-w-lg">
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight">Modifica sezione</h1>
    </div>

    <div class="bg-surface-900/50 border border-surface-800/50 rounded-2xl p-6">
        <form method="POST" action="{{ route('chat-qa.update-section', $section) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-xs font-medium text-surface-400 mb-1.5">Nome sezione</label>
                <input type="text" id="name" name="name" value="{{ old('name', $section->name) }}" required
                    class="input-field w-full px-3.5 py-2.5 bg-surface-950/50 border border-surface-700/50 rounded-xl text-sm text-white placeholder-surface-600 focus:outline-none">
                @error('name')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="subtitle" class="block text-xs font-medium text-surface-400 mb-1.5">Sottotitolo (mostrato nei bottoni della chat)</label>
                <input type="text" id="subtitle" name="subtitle" value="{{ old('subtitle', $section->subtitle) }}"
                    class="input-field w-full px-3.5 py-2.5 bg-surface-950/50 border border-surface-700/50 rounded-xl text-sm text-white placeholder-surface-600 focus:outline-none"
                    placeholder="e.g. Learn about who we are">
                @error('subtitle')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="active" value="0">
                    <input type="checkbox" name="active" value="1" {{ old('active', $section->active) ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-surface-600 bg-surface-800 text-accent focus:ring-accent/30 focus:ring-offset-0">
                    <span class="text-sm text-surface-400">Sezione attiva</span>
                </label>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-accent to-accent-dark text-white text-sm font-semibold rounded-xl hover:from-accent-light hover:to-accent transition-all duration-200 shadow-lg shadow-accent/20">
                    Salva modifiche
                </button>
                <a href="{{ route('chat-qa.index') }}" class="px-5 py-2.5 border border-surface-700/50 rounded-xl text-sm text-surface-400 hover:text-white hover:border-surface-600 transition">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection
