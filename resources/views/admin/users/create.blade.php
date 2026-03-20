@extends('layouts.app')

@section('title', 'Nuovo Utente - Nube')

@section('content')
{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-sm mb-6">
    <a href="{{ route('users.index') }}" class="text-surface-500 hover:text-white transition">Utenti</a>
    <svg class="w-3.5 h-3.5 text-surface-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <span class="text-surface-300">Nuovo utente</span>
</div>

<div class="max-w-lg">
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight">Nuovo utente</h1>
        <p class="text-sm text-surface-500 mt-1">Aggiungi un nuovo membro al team</p>
    </div>

    <div class="bg-surface-900/50 border border-surface-800/50 rounded-2xl p-6">
        <form method="POST" action="{{ route('users.store') }}" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-xs font-medium text-surface-400 mb-1.5">Nome completo</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    class="input-field w-full px-3.5 py-2.5 bg-surface-950/50 border border-surface-700/50 rounded-xl text-sm text-white placeholder-surface-600 focus:outline-none"
                    placeholder="Mario Rossi">
                @error('name')
                    <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-medium text-surface-400 mb-1.5">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    class="input-field w-full px-3.5 py-2.5 bg-surface-950/50 border border-surface-700/50 rounded-xl text-sm text-white placeholder-surface-600 focus:outline-none"
                    placeholder="mario@esempio.it">
                @error('email')
                    <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label for="role" class="block text-xs font-medium text-surface-400 mb-1.5">Ruolo</label>
                <select id="role" name="role" required
                    class="input-field w-full px-3.5 py-2.5 bg-surface-950/50 border border-surface-700/50 rounded-xl text-sm text-white focus:outline-none appearance-none cursor-pointer"
                    style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 24 24%27 stroke=%27%2371717a%27 stroke-width=%272%27%3E%3Cpath stroke-linecap=%27round%27 stroke-linejoin=%27round%27 d=%27M19.5 8.25l-7.5 7.5-7.5-7.5%27/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                    @foreach($roles as $role)
                        <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
                @error('role')
                    <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                        {{ $message }}
                    </p>
                @enderror
                <div class="mt-2 p-3 rounded-xl bg-surface-800/30 border border-surface-700/30">
                    <div class="space-y-1.5 text-[11px] text-surface-500">
                        <div class="flex items-start gap-2">
                            <span class="text-purple-400 font-semibold w-16 flex-shrink-0">Superuser</span>
                            <span>Accesso completo. Gestisce tutti gli utenti.</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-blue-400 font-semibold w-16 flex-shrink-0">Admin</span>
                            <span>Crea admin e operatori. Elimina solo operatori.</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-emerald-400 font-semibold w-16 flex-shrink-0">Operatore</span>
                            <span>Accesso base. Nessuna gestione utenti.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-surface-800/50 pt-5 space-y-4">
                <div>
                    <label for="password" class="block text-xs font-medium text-surface-400 mb-1.5">Password</label>
                    <input type="password" id="password" name="password" required
                        class="input-field w-full px-3.5 py-2.5 bg-surface-950/50 border border-surface-700/50 rounded-xl text-sm text-white placeholder-surface-600 focus:outline-none"
                        placeholder="Minimo 8 caratteri">
                    @error('password')
                        <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-medium text-surface-400 mb-1.5">Conferma password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="input-field w-full px-3.5 py-2.5 bg-surface-950/50 border border-surface-700/50 rounded-xl text-sm text-white placeholder-surface-600 focus:outline-none"
                        placeholder="Ripeti la password">
                </div>
            </div>

            <div class="flex items-center gap-3 pt-3">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-accent to-accent-dark text-white text-sm font-semibold rounded-xl hover:from-accent-light hover:to-accent transition-all duration-200 shadow-lg shadow-accent/20 hover:shadow-accent/30">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Crea utente
                </button>
                <a href="{{ route('users.index') }}"
                    class="px-5 py-2.5 border border-surface-700/50 rounded-xl text-sm text-surface-400 hover:text-white hover:border-surface-600 transition">
                    Annulla
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
