@extends('layouts.app')

@section('title', 'Suggerimenti Blog - Nube')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Suggerimenti</h1>
        <p class="text-sm text-surface-500 mt-1">Articoli generati dalle migliori testate tech</p>
    </div>
    <button type="button" id="start-scraper-btn" onclick="startScraper()"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-accent to-accent-dark text-white text-sm font-semibold rounded-xl hover:from-accent-light hover:to-accent transition-all duration-200 shadow-lg shadow-accent/20">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z" />
        </svg>
        <span class="btn-text">Genera suggerimenti</span>
    </button>
    <script>
    function startScraper() {
        var btn = document.getElementById('start-scraper-btn');
        btn.disabled = true;
        btn.querySelector('.btn-text').textContent = 'Avviato...';

        // Fire and forget - fastcgi_finish_request will release the connection
        fetch('{{ route("blog.suggestions.run") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
        }).catch(function() {
            // Connection might timeout but scraper continues server-side
        });

        // Reset button after 3s
        setTimeout(function() {
            btn.querySelector('.btn-text').textContent = 'In esecuzione...';
        }, 1000);
    }
    </script>
</div>

@if($suggestions->count())
{{-- Search + Bulk actions --}}
<div class="mb-4 flex flex-col sm:flex-row gap-3">
    <div class="flex-1">
        <input type="text" id="suggestion-search" placeholder="Cerca per titolo, contenuto o testata..."
            class="w-full px-3.5 py-2 bg-surface-900/50 border border-surface-700/50 rounded-xl text-sm text-white placeholder-surface-600 focus:outline-none focus:border-accent/50">
    </div>
    <div class="flex items-center gap-2">
        <label class="flex items-center gap-2 text-xs text-surface-400 cursor-pointer">
            <input type="checkbox" id="select-all-suggestions" class="w-3.5 h-3.5 rounded border-surface-600 bg-surface-800 text-accent focus:ring-accent/30 focus:ring-offset-0">
            Seleziona tutti
        </label>
        <button type="button" onclick="bulkAction('approve')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 text-xs font-medium rounded-lg transition">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
            Approva sel.
        </button>
        <button type="button" onclick="bulkAction('reject')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-500/5 hover:bg-red-500/10 text-red-400 border border-red-500/20 text-xs font-medium rounded-lg transition">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            Rifiuta sel.
        </button>
    </div>
</div>

{{-- Bulk action forms --}}
<form id="bulk-approve-form" method="POST" action="{{ route('blog.suggestions.bulk') }}" class="hidden">
    @csrf
    <input type="hidden" name="action" value="approve">
    <input type="hidden" name="ids" id="bulk-approve-ids">
</form>
<form id="bulk-reject-form" method="POST" action="{{ route('blog.suggestions.bulk') }}" class="hidden">
    @csrf
    <input type="hidden" name="action" value="reject">
    <input type="hidden" name="ids" id="bulk-reject-ids">
</form>

<div class="grid gap-4" id="suggestions-list">
    @foreach($suggestions as $s)
    <div class="suggestion-item bg-surface-900/50 border border-surface-800/50 rounded-2xl overflow-hidden"
         data-search="{{ strtolower($s->title_en . ' ' . $s->title_it . ' ' . $s->text_en . ' ' . $s->source_name) }}">
        <div class="flex flex-col lg:flex-row">
            @if($s->cover_image_path)
            <div class="lg:w-64 h-48 lg:h-auto flex-shrink-0">
                <img src="{{ asset('storage/' . $s->cover_image_path) }}" alt="" class="w-full h-full object-cover">
            </div>
            @endif
            <div class="flex-1 p-5">
                <div class="flex items-start gap-3">
                    <input type="checkbox" class="suggestion-checkbox mt-1 w-4 h-4 rounded border-surface-600 bg-surface-800 text-accent focus:ring-accent/30 focus:ring-offset-0 cursor-pointer" data-id="{{ $s->id }}">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-amber-500/10 text-amber-400 border border-amber-500/20">{{ $s->source_name }}</span>
                            <span class="text-[10px] text-surface-600">{{ $s->created_at->diffForHumans() }}</span>
                        </div>
                        <h3 class="font-semibold text-sm mb-1">{{ $s->title_en }}</h3>
                        <p class="text-xs text-surface-500 mb-2">{{ $s->title_it }}</p>
                        <div class="flex gap-2 mb-2">
                            <span class="text-[10px] bg-blue-500/10 text-blue-400 border border-blue-500/20 px-1.5 py-0.5 rounded">EN</span>
                            <p class="text-xs text-surface-600 line-clamp-1">{{ $s->meta_description_en }}</p>
                        </div>
                        <details class="mb-3">
                            <summary class="text-xs text-accent-light cursor-pointer hover:text-accent transition">Anteprima</summary>
                            <div class="mt-2 p-3 bg-surface-800/30 rounded-xl text-xs text-surface-400 max-h-48 overflow-y-auto space-y-2">
                                <p class="whitespace-pre-wrap">{{ Str::limit($s->text_en, 600) }}</p>
                                <hr class="border-surface-700">
                                <p class="whitespace-pre-wrap">{{ Str::limit($s->text_it, 600) }}</p>
                            </div>
                        </details>
                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('blog.suggestions.approve', $s) }}" class="inline">@csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 text-xs font-medium rounded-lg transition">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    Approva
                                </button>
                            </form>
                            <form method="POST" action="{{ route('blog.suggestions.reject', $s) }}" class="inline" onsubmit="return confirm('Rifiutare?')">@csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-500/5 hover:bg-red-500/10 text-red-400 border border-red-500/20 text-xs font-medium rounded-lg transition">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    Rifiuta
                                </button>
                            </form>
                            <a href="{{ $s->source_url }}" target="_blank" class="text-xs text-surface-500 hover:text-surface-300 transition">Fonte</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="bg-surface-900/50 border border-surface-800/50 rounded-2xl p-12 text-center">
    <svg class="w-12 h-12 text-surface-700 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" /></svg>
    <p class="text-sm text-surface-500">Nessun suggerimento. Clicca "Genera suggerimenti".</p>
</div>
@endif

<script>
// Search filter
document.getElementById('suggestion-search')?.addEventListener('input', function(e) {
    const q = e.target.value.toLowerCase();
    document.querySelectorAll('.suggestion-item').forEach(item => {
        item.style.display = item.dataset.search.includes(q) ? '' : 'none';
    });
});

// Select all
document.getElementById('select-all-suggestions')?.addEventListener('change', function(e) {
    document.querySelectorAll('.suggestion-checkbox').forEach(cb => {
        if (cb.closest('.suggestion-item').style.display !== 'none') cb.checked = e.target.checked;
    });
});

// Bulk action
function bulkAction(action) {
    const ids = Array.from(document.querySelectorAll('.suggestion-checkbox:checked')).map(cb => cb.dataset.id);
    if (ids.length === 0) return alert('Seleziona almeno un suggerimento.');
    if (action === 'reject' && !confirm('Rifiutare ' + ids.length + ' suggerimenti?')) return;
    document.getElementById('bulk-' + action + '-ids').value = ids.join(',');
    document.getElementById('bulk-' + action + '-form').submit();
}
</script>
@endsection
