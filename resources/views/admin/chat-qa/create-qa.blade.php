@extends('layouts.app')

@section('title', 'Nuova Q&A - Nube')

@section('content')
<div class="flex items-center gap-2 text-sm mb-6">
    <a href="{{ route('chat-qa.index') }}" class="text-surface-500 hover:text-white transition">Chat Q&A</a>
    <svg class="w-3.5 h-3.5 text-surface-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
    <span class="text-surface-400">{{ $section->name }}</span>
    <svg class="w-3.5 h-3.5 text-surface-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
    <span class="text-surface-300">Nuova Q&A</span>
</div>

<div class="max-w-2xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight">Nuova Q&A</h1>
        <p class="text-sm text-surface-500 mt-1">Aggiungi una domanda e risposta alla sezione <strong class="text-surface-300">{{ $section->name }}</strong></p>
    </div>

    <div class="bg-surface-900/50 border border-surface-800/50 rounded-2xl p-6">
        <form method="POST" action="{{ route('chat-qa.store-qa', $section) }}" class="space-y-5">
            @csrf

            <div>
                <label for="question" class="block text-xs font-medium text-surface-400 mb-1.5">Domanda (in inglese)</label>
                <input type="text" id="question" name="question" value="{{ old('question') }}" required
                    class="input-field w-full px-3.5 py-2.5 bg-surface-950/50 border border-surface-700/50 rounded-xl text-sm text-white placeholder-surface-600 focus:outline-none"
                    placeholder="e.g. What services do you offer?">
                @error('question')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="answer" class="block text-xs font-medium text-surface-400 mb-1.5">Risposta (in inglese)</label>
                <textarea id="answer" name="answer" rows="6" required
                    class="input-field w-full px-3.5 py-2.5 bg-surface-950/50 border border-surface-700/50 rounded-xl text-sm text-white placeholder-surface-600 focus:outline-none resize-y"
                    placeholder="The detailed answer...">{{ old('answer') }}</textarea>
                @error('answer')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-accent to-accent-dark text-white text-sm font-semibold rounded-xl hover:from-accent-light hover:to-accent transition-all duration-200 shadow-lg shadow-accent/20">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Aggiungi Q&A
                </button>
                <a href="{{ route('chat-qa.index') }}" class="px-5 py-2.5 border border-surface-700/50 rounded-xl text-sm text-surface-400 hover:text-white hover:border-surface-600 transition">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection
