@extends('layouts.app')

@section('title', 'Gestione Utenti - Nube')

@section('content')
{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Utenti</h1>
        <p class="text-sm text-surface-500 mt-1">Gestisci i membri del team</p>
    </div>
    <a href="{{ route('users.create') }}"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-accent to-accent-dark text-white text-sm font-semibold rounded-xl hover:from-accent-light hover:to-accent transition-all duration-200 shadow-lg shadow-accent/20 hover:shadow-accent/30">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Nuovo utente
    </a>
</div>

{{-- Stats pills --}}
<div class="flex flex-wrap gap-2 mb-6">
    @php
        $total = $users->count();
        $counts = $users->groupBy('role')->map->count();
    @endphp
    <span class="text-xs font-medium px-3 py-1.5 rounded-full bg-surface-800/50 border border-surface-700/50 text-surface-300">
        Tutti <span class="text-white ml-1">{{ $total }}</span>
    </span>
    <span class="text-xs font-medium px-3 py-1.5 rounded-full bg-purple-500/5 border border-purple-500/20 text-purple-400">
        Superuser <span class="text-purple-300 ml-1">{{ $counts->get('superuser', 0) }}</span>
    </span>
    <span class="text-xs font-medium px-3 py-1.5 rounded-full bg-blue-500/5 border border-blue-500/20 text-blue-400">
        Admin <span class="text-blue-300 ml-1">{{ $counts->get('admin', 0) }}</span>
    </span>
    <span class="text-xs font-medium px-3 py-1.5 rounded-full bg-emerald-500/5 border border-emerald-500/20 text-emerald-400">
        Operatori <span class="text-emerald-300 ml-1">{{ $counts->get('operatore', 0) }}</span>
    </span>
</div>

{{-- Table --}}
<div class="bg-surface-900/50 border border-surface-800/50 rounded-2xl overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b border-surface-800/50">
                <th class="text-left text-[11px] font-semibold text-surface-500 uppercase tracking-wider px-5 py-3.5">Utente</th>
                <th class="text-left text-[11px] font-semibold text-surface-500 uppercase tracking-wider px-5 py-3.5 hidden md:table-cell">Email</th>
                <th class="text-left text-[11px] font-semibold text-surface-500 uppercase tracking-wider px-5 py-3.5">Ruolo</th>
                <th class="text-left text-[11px] font-semibold text-surface-500 uppercase tracking-wider px-5 py-3.5 hidden sm:table-cell">Registrato</th>
                <th class="text-right text-[11px] font-semibold text-surface-500 uppercase tracking-wider px-5 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-surface-800/30">
            @foreach($users as $user)
            <tr class="group hover:bg-surface-800/20 transition">
                <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        @php
                            $avatarColors = [
                                'superuser' => 'from-purple-500/20 to-purple-600/20 border-purple-500/20 text-purple-300',
                                'admin' => 'from-blue-500/20 to-blue-600/20 border-blue-500/20 text-blue-300',
                                'operatore' => 'from-emerald-500/20 to-emerald-600/20 border-emerald-500/20 text-emerald-300',
                            ];
                        @endphp
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br {{ $avatarColors[$user->role] }} border flex items-center justify-center text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold">{{ $user->name }}</p>
                            <p class="text-xs text-surface-500 md:hidden">{{ $user->email }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-4 hidden md:table-cell">
                    <span class="text-sm text-surface-400">{{ $user->email }}</span>
                </td>
                <td class="px-5 py-4">
                    @php
                        $roleStyles = [
                            'superuser' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                            'admin' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                            'operatore' => 'bg-surface-800/50 text-surface-400 border-surface-700/50',
                        ];
                        $roleIcons = [
                            'superuser' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />',
                            'admin' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />',
                            'operatore' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />',
                        ];
                    @endphp
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-1 rounded-lg border {{ $roleStyles[$user->role] }}">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">{!! $roleIcons[$user->role] !!}</svg>
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td class="px-5 py-4 hidden sm:table-cell">
                    <span class="text-xs text-surface-500">{{ $user->created_at->format('d/m/Y') }}</span>
                </td>
                <td class="px-5 py-4 text-right">
                    @php
                        $canDelete = false;
                        if ($user->id !== auth()->id()) {
                            if (auth()->user()->role === 'superuser') {
                                $canDelete = true;
                            } elseif (auth()->user()->role === 'admin' && $user->role === 'operatore') {
                                $canDelete = true;
                            }
                        }
                    @endphp
                    @if($canDelete)
                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline"
                            onsubmit="return confirm('Sei sicuro di voler eliminare {{ $user->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 rounded-lg text-surface-600 hover:text-red-400 hover:bg-red-500/5 transition opacity-0 group-hover:opacity-100" title="Elimina utente">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($users->isEmpty())
    <div class="text-center py-12">
        <svg class="w-12 h-12 text-surface-700 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
        </svg>
        <p class="text-sm text-surface-500">Nessun utente trovato</p>
    </div>
    @endif
</div>
@endsection
