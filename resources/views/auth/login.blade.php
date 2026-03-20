@extends('layouts.app')

@section('title', 'Login - Nube')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 relative overflow-hidden">
    {{-- Background effects --}}
    <div class="absolute inset-0 bg-surface-950"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-accent/5 rounded-full blur-3xl"></div>
    <div class="absolute top-1/4 right-1/4 w-[300px] h-[300px] bg-purple-600/5 rounded-full blur-3xl"></div>

    <div class="relative w-full max-w-sm">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="w-12 h-12 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-accent to-purple-600 flex items-center justify-center shadow-lg shadow-accent/20">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                </svg>
            </div>
            <h1 class="text-xl font-bold tracking-tight">Accedi a Nube</h1>
            <p class="text-sm text-surface-500 mt-1">Pannello di amministrazione</p>
        </div>

        {{-- Card --}}
        <div class="bg-surface-900/50 border border-surface-800/50 rounded-2xl p-6 glow">
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-medium text-surface-400 mb-1.5">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="input-field w-full px-3.5 py-2.5 bg-surface-950/50 border border-surface-700/50 rounded-xl text-sm text-white placeholder-surface-600 focus:outline-none"
                        placeholder="nome@esempio.it">
                    @error('email')
                        <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-medium text-surface-400 mb-1.5">Password</label>
                    <input type="password" id="password" name="password" required
                        class="input-field w-full px-3.5 py-2.5 bg-surface-950/50 border border-surface-700/50 rounded-xl text-sm text-white placeholder-surface-600 focus:outline-none"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center pt-1">
                    <input type="checkbox" id="remember" name="remember"
                        class="w-3.5 h-3.5 rounded border-surface-600 bg-surface-800 text-accent focus:ring-accent/30 focus:ring-offset-0 cursor-pointer">
                    <label for="remember" class="ml-2 text-xs text-surface-500 cursor-pointer">Ricordami</label>
                </div>

                <button type="submit"
                    class="w-full py-2.5 bg-gradient-to-r from-accent to-accent-dark text-white text-sm font-semibold rounded-xl hover:from-accent-light hover:to-accent transition-all duration-200 shadow-lg shadow-accent/20 hover:shadow-accent/30 mt-2">
                    Accedi
                </button>
            </form>
        </div>

        <p class="text-center text-[11px] text-surface-600 mt-6">Nube Admin Panel &middot; Accesso riservato</p>
    </div>
</div>
@endsection
