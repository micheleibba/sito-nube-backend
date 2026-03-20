@extends('layouts.app')

@section('title', 'Dashboard - Nube')

@section('content')
{{-- Header --}}
<div class="mb-8">
    <h1 class="text-2xl font-bold tracking-tight">Dashboard</h1>
    <p class="text-sm text-surface-500 mt-1">Benvenuto, {{ auth()->user()->name }}</p>
</div>

{{-- Stats --}}
@php
    $totalUsers = \App\Models\User::count();
    $superusers = \App\Models\User::where('role', 'superuser')->count();
    $admins = \App\Models\User::where('role', 'admin')->count();
    $operators = \App\Models\User::where('role', 'operatore')->count();
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    {{-- Total users --}}
    <div class="card-hover bg-surface-900/50 border border-surface-800/50 rounded-2xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-surface-500 uppercase tracking-wide">Utenti totali</span>
            <div class="w-8 h-8 rounded-lg bg-accent/10 flex items-center justify-center">
                <svg class="w-4 h-4 text-accent-light" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold">{{ $totalUsers }}</p>
    </div>

    {{-- Superusers --}}
    <div class="card-hover bg-surface-900/50 border border-surface-800/50 rounded-2xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-surface-500 uppercase tracking-wide">Superuser</span>
            <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center">
                <svg class="w-4 h-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold">{{ $superusers }}</p>
    </div>

    {{-- Admins --}}
    <div class="card-hover bg-surface-900/50 border border-surface-800/50 rounded-2xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-surface-500 uppercase tracking-wide">Admin</span>
            <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold">{{ $admins }}</p>
    </div>

    {{-- Operators --}}
    <div class="card-hover bg-surface-900/50 border border-surface-800/50 rounded-2xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-surface-500 uppercase tracking-wide">Operatori</span>
            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold">{{ $operators }}</p>
    </div>
</div>

{{-- Quick actions + Recent users --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    {{-- Quick Actions --}}
    <div class="bg-surface-900/50 border border-surface-800/50 rounded-2xl p-5">
        <h3 class="text-sm font-semibold mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
            </svg>
            Azioni rapide
        </h3>
        <div class="space-y-2">
            @if(in_array(auth()->user()->role, ['superuser', 'admin']))
            <a href="{{ route('users.create') }}" class="flex items-center gap-3 p-3 rounded-xl bg-surface-800/30 hover:bg-surface-800/60 transition group">
                <div class="w-8 h-8 rounded-lg bg-accent/10 flex items-center justify-center group-hover:bg-accent/20 transition">
                    <svg class="w-4 h-4 text-accent-light" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium">Nuovo utente</p>
                    <p class="text-xs text-surface-500">Aggiungi un membro al team</p>
                </div>
            </a>
            <a href="{{ route('users.index') }}" class="flex items-center gap-3 p-3 rounded-xl bg-surface-800/30 hover:bg-surface-800/60 transition group">
                <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center group-hover:bg-blue-500/20 transition">
                    <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium">Gestisci utenti</p>
                    <p class="text-xs text-surface-500">Visualizza e modifica il team</p>
                </div>
            </a>
            @endif
        </div>
    </div>

    {{-- Recent users --}}
    <div class="lg:col-span-2 bg-surface-900/50 border border-surface-800/50 rounded-2xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Utenti recenti
            </h3>
            @if(in_array(auth()->user()->role, ['superuser', 'admin']))
            <a href="{{ route('users.index') }}" class="text-xs text-accent-light hover:text-accent transition">Vedi tutti →</a>
            @endif
        </div>
        <div class="space-y-1">
            @foreach(\App\Models\User::latest()->take(5)->get() as $user)
            <div class="flex items-center justify-between p-3 rounded-xl hover:bg-surface-800/30 transition">
                <div class="flex items-center gap-3">
                    @php
                        $gradients = [
                            'superuser' => 'from-purple-500/20 to-purple-600/20 border-purple-500/20 text-purple-300',
                            'admin' => 'from-blue-500/20 to-blue-600/20 border-blue-500/20 text-blue-300',
                            'operatore' => 'from-emerald-500/20 to-emerald-600/20 border-emerald-500/20 text-emerald-300',
                        ];
                    @endphp
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br {{ $gradients[$user->role] }} border flex items-center justify-center text-xs font-semibold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium">{{ $user->name }}</p>
                        <p class="text-xs text-surface-500">{{ $user->email }}</p>
                    </div>
                </div>
                @php
                    $roleColors = [
                        'superuser' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                        'admin' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                        'operatore' => 'bg-surface-800 text-surface-400 border-surface-700',
                    ];
                @endphp
                <span class="text-[11px] font-medium px-2 py-0.5 rounded-full border {{ $roleColors[$user->role] }}">
                    {{ $user->role }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Info card --}}
<div class="mt-4 bg-gradient-to-r from-accent/5 to-purple-600/5 border border-accent/10 rounded-2xl p-5">
    <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-accent/10 flex items-center justify-center flex-shrink-0 mt-0.5">
            <svg class="w-4 h-4 text-accent-light" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
            </svg>
        </div>
        <div>
            <p class="text-sm font-medium text-accent-light">Area in sviluppo</p>
            <p class="text-xs text-surface-500 mt-0.5">La dashboard si arricchirà con nuove funzionalità man mano che il progetto avanza.</p>
        </div>
    </div>
</div>
@endsection
