<!DOCTYPE html>
<html lang="it" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Nube Admin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        surface: { 50: '#fafafa', 100: '#f4f4f5', 200: '#e4e4e7', 300: '#d4d4d8', 400: '#a1a1aa', 500: '#71717a', 600: '#52525b', 700: '#3f3f46', 800: '#27272a', 900: '#18181b', 950: '#09090b' },
                        accent: { DEFAULT: '#6366f1', light: '#818cf8', dark: '#4f46e5' },
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        .glass { background: rgba(24, 24, 27, 0.6); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
        .glow { box-shadow: 0 0 60px -12px rgba(99, 102, 241, 0.15); }
        .card-hover { transition: all 0.2s ease; }
        .card-hover:hover { transform: translateY(-1px); box-shadow: 0 8px 30px -8px rgba(0,0,0,0.4); }
        .sidebar-link { transition: all 0.15s ease; }
        .sidebar-link:hover { background: rgba(255,255,255,0.05); }
        .sidebar-link.active { background: rgba(99, 102, 241, 0.1); color: #818cf8; border-right: 2px solid #6366f1; }
        .fade-in { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .input-field { transition: all 0.2s ease; }
        .input-field:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #52525b; }
    </style>
</head>
<body class="h-full bg-surface-950 text-white antialiased">
    @auth
    <div class="flex h-full">
        {{-- Sidebar --}}
        <aside class="w-64 border-r border-surface-800/50 flex flex-col fixed h-full bg-surface-950/80 z-30">
            {{-- Logo --}}
            <div class="px-5 py-5 border-b border-surface-800/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-accent to-purple-600 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                        </svg>
                    </div>
                    <span class="font-bold text-base tracking-tight">Nube</span>
                    <span class="text-[10px] font-medium bg-accent/10 text-accent-light px-1.5 py-0.5 rounded-full">admin</span>
                </div>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 py-3 px-2 space-y-0.5 overflow-y-auto">
                <p class="text-[10px] font-semibold text-surface-500 uppercase tracking-widest px-3 pt-3 pb-2">Generale</p>
                <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('dashboard') ? 'active' : 'text-surface-400' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 12a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1v-7z" />
                    </svg>
                    Dashboard
                </a>

                @if(in_array(auth()->user()->role, ['superuser', 'admin']))
                <p class="text-[10px] font-semibold text-surface-500 uppercase tracking-widest px-3 pt-5 pb-2">Gestione</p>
                <a href="{{ route('users.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('users.*') ? 'active' : 'text-surface-400' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    Utenti
                </a>
                <a href="{{ route('chat-qa.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('chat-qa.*') ? 'active' : 'text-surface-400' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                    </svg>
                    Chat Q&A
                </a>
                <a href="{{ route('blog.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('blog.index') || request()->routeIs('blog.create') || request()->routeIs('blog.edit') ? 'active' : 'text-surface-400' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
                    </svg>
                    Blog
                </a>
                <a href="{{ route('blog.suggestions') }}" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('blog.suggestions*') ? 'active' : 'text-surface-400' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z" />
                    </svg>
                    Suggerisci
                </a>
                @endif
            </nav>

            {{-- User --}}
            <div class="border-t border-surface-800/50 p-3">
                <div class="flex items-center gap-3 px-2 py-2">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-accent/30 to-purple-600/30 flex items-center justify-center text-xs font-semibold text-accent-light border border-accent/20">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] text-surface-500 truncate">{{ auth()->user()->role }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-1.5 rounded-lg hover:bg-surface-800 text-surface-500 hover:text-red-400 transition" title="Esci">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main --}}
        <div class="flex-1 ml-64 overflow-auto">
            <main class="p-8 fade-in">
                @if(session('success'))
                    <div class="mb-6 flex items-center gap-3 p-4 bg-emerald-500/5 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 flex items-center gap-3 p-4 bg-red-500/5 border border-red-500/20 rounded-xl text-red-400 text-sm">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    {{-- Scraper progress bar --}}
    <div id="scraper-bar" class="fixed bottom-0 left-0 right-0 z-50 transition-transform duration-300 translate-y-full">
        <div class="bg-surface-900 border-t border-surface-800/50 px-5 py-3">
            <div class="max-w-5xl mx-auto ml-64 flex items-center gap-4">
                <div class="flex items-center gap-2 flex-shrink-0">
                    <svg class="w-4 h-4 text-accent-light animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-xs font-semibold text-accent-light">SCRAPER</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1">
                        <span id="scraper-phase" class="text-xs text-surface-300 truncate"></span>
                        <span id="scraper-count" class="text-xs text-surface-500 flex-shrink-0 ml-2"></span>
                    </div>
                    <div class="w-full bg-surface-800 rounded-full h-1.5">
                        <div id="scraper-progress" class="bg-gradient-to-r from-accent to-purple-500 h-1.5 rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                </div>
                <span id="scraper-done-icon" class="hidden flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </span>
            </div>
        </div>
    </div>

    <script>
    (function() {
        const bar = document.getElementById('scraper-bar');
        const phase = document.getElementById('scraper-phase');
        const count = document.getElementById('scraper-count');
        const progress = document.getElementById('scraper-progress');
        const doneIcon = document.getElementById('scraper-done-icon');
        const spinner = bar.querySelector('.animate-spin');
        let wasRunning = false;
        let hideTimeout = null;

        function poll() {
            fetch('{{ route("blog.suggestions.status") }}')
                .then(r => r.json())
                .then(data => {
                    if (data.running) {
                        wasRunning = true;
                        bar.classList.remove('translate-y-full');
                        phase.textContent = data.phase || 'In corso...';
                        count.textContent = data.progress_detail || '';

                        let pct = 0;
                        if (data.total > 0 && data.processed !== undefined) {
                            pct = Math.round((data.processed / data.total) * 100);
                        }
                        progress.style.width = pct + '%';

                        if (data.generated > 0) {
                            count.textContent = (data.progress_detail || '') + ' · ' + data.generated + ' generati';
                        }

                        spinner.classList.remove('hidden');
                        doneIcon.classList.add('hidden');
                    } else if (wasRunning) {
                        // Just finished
                        phase.textContent = data.phase || 'Completato!';
                        progress.style.width = '100%';
                        spinner.classList.add('hidden');
                        doneIcon.classList.remove('hidden');

                        if (!hideTimeout) {
                            hideTimeout = setTimeout(() => {
                                bar.classList.add('translate-y-full');
                                wasRunning = false;
                                hideTimeout = null;
                                // Reload if on suggestions page
                                if (window.location.pathname.includes('suggestions')) {
                                    window.location.reload();
                                }
                            }, 4000);
                        }
                    }
                })
                .catch(() => {});
        }

        // Poll every 2 seconds
        setInterval(poll, 2000);
        poll();
    })();
    </script>
    @else
        <main>
            @yield('content')
        </main>
    @endauth
</body>
</html>
