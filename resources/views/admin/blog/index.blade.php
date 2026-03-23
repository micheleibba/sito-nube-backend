@extends('layouts.app')

@section('title', 'Blog - Nube')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Blog</h1>
        <p class="text-sm text-surface-500 mt-1">{{ $posts->count() }} articoli totali</p>
    </div>
    <a href="{{ route('blog.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-accent to-accent-dark text-white text-sm font-semibold rounded-xl hover:from-accent-light hover:to-accent transition-all duration-200 shadow-lg shadow-accent/20">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Nuovo articolo
    </a>
</div>

@if($posts->count())
{{-- Search + Status filter + Bulk actions --}}
<div class="mb-4 flex flex-col sm:flex-row gap-3">
    <div class="flex-1">
        <input type="text" id="blog-search" placeholder="Cerca per titolo o contenuto..."
            class="w-full px-3.5 py-2 bg-surface-900/50 border border-surface-700/50 rounded-xl text-sm text-white placeholder-surface-600 focus:outline-none focus:border-accent/50">
    </div>
    <div class="flex items-center gap-2">
        {{-- Status filter pills --}}
        <button type="button" onclick="filterStatus('all')" class="status-filter active text-xs px-3 py-1.5 rounded-lg border transition" data-filter="all">
            Tutti <span class="ml-1 opacity-60">{{ $posts->count() }}</span>
        </button>
        <button type="button" onclick="filterStatus('published')" class="status-filter text-xs px-3 py-1.5 rounded-lg border transition" data-filter="published">
            Pubblicati <span class="ml-1 opacity-60">{{ $posts->where('published', true)->count() }}</span>
        </button>
        <button type="button" onclick="filterStatus('draft')" class="status-filter text-xs px-3 py-1.5 rounded-lg border transition" data-filter="draft">
            Bozze <span class="ml-1 opacity-60">{{ $posts->where('published', false)->count() }}</span>
        </button>
    </div>
</div>

<div class="mb-4 flex items-center gap-2">
    <label class="flex items-center gap-2 text-xs text-surface-400 cursor-pointer">
        <input type="checkbox" id="select-all-posts" class="w-3.5 h-3.5 rounded border-surface-600 bg-surface-800 text-accent focus:ring-accent/30 focus:ring-offset-0">
        Seleziona tutti
    </label>
    <button type="button" onclick="bulkPostAction('publish')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 text-xs font-medium rounded-lg transition">
        Pubblica sel.
    </button>
    <button type="button" onclick="bulkPostAction('draft')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/20 text-xs font-medium rounded-lg transition">
        Bozza sel.
    </button>
    <button type="button" onclick="bulkPostAction('delete')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-500/5 hover:bg-red-500/10 text-red-400 border border-red-500/20 text-xs font-medium rounded-lg transition">
        Elimina sel.
    </button>
</div>

<form id="bulk-post-form" method="POST" action="{{ route('blog.bulk') }}" class="hidden">
    @csrf
    <input type="hidden" name="action" id="bulk-post-action">
    <input type="hidden" name="ids" id="bulk-post-ids">
</form>

<div class="grid gap-4" id="posts-list">
    @foreach($posts as $post)
    <div class="post-item card-hover bg-surface-900/50 border border-surface-800/50 rounded-2xl overflow-hidden group"
         data-search="{{ strtolower($post->title_en . ' ' . $post->title_it . ' ' . $post->text_en . ' ' . $post->text_it) }}"
         data-status="{{ $post->published ? 'published' : 'draft' }}">
        <div class="flex items-stretch">
            @if($post->cover_image)
            <div class="w-40 flex-shrink-0 hidden sm:block">
                <img src="{{ asset('storage/' . $post->cover_image) }}" alt="" class="w-full h-full object-cover">
            </div>
            @endif
            <div class="flex-1 p-5 flex items-center justify-between gap-4">
                <div class="flex items-start gap-3 min-w-0">
                    <input type="checkbox" class="post-checkbox mt-1 w-4 h-4 rounded border-surface-600 bg-surface-800 text-accent focus:ring-accent/30 focus:ring-offset-0 cursor-pointer" data-id="{{ $post->id }}">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-semibold text-sm truncate">{{ $post->title_en }}</h3>
                            @if($post->published)
                                <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex-shrink-0">pubblicato</span>
                            @else
                                <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-surface-800 text-surface-500 border border-surface-700 flex-shrink-0">bozza</span>
                            @endif
                        </div>
                        <p class="text-xs text-surface-500 truncate">{{ $post->title_it }}</p>
                        <div class="flex items-center gap-3 mt-2 text-[11px] text-surface-600">
                            <span>/{{ $post->slug_en }}</span>
                            <span>/{{ $post->slug_it }}</span>
                            @if($post->published_at)
                                <span>{{ $post->published_at->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition flex-shrink-0">
                    <a href="{{ config('app.frontend_url', 'https://nubelab.it') }}/blog/{{ $post->slug_en }}" target="_blank" class="p-2 rounded-lg text-surface-500 hover:text-emerald-400 hover:bg-emerald-500/5 transition" title="Anteprima">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </a>
                    <a href="{{ route('blog.edit', $post) }}" class="p-2 rounded-lg text-surface-500 hover:text-accent-light hover:bg-accent/5 transition" title="Modifica">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                    </a>
                    <form method="POST" action="{{ route('blog.destroy', $post) }}" class="inline" onsubmit="return confirm('Eliminare?')">@csrf @method('DELETE')
                        <button type="submit" class="p-2 rounded-lg text-surface-500 hover:text-red-400 hover:bg-red-500/5 transition" title="Elimina">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="bg-surface-900/50 border border-surface-800/50 rounded-2xl p-12 text-center">
    <svg class="w-12 h-12 text-surface-700 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" /></svg>
    <p class="text-sm text-surface-500">Nessun articolo. Crea il primo!</p>
</div>
@endif

<style>
.status-filter { border-color: rgba(63,63,70,0.5); color: rgba(161,161,170,1); }
.status-filter.active { border-color: rgba(99,102,241,0.4); color: rgba(129,140,248,1); background: rgba(99,102,241,0.1); }
</style>

<script>
// Text search
document.getElementById('blog-search')?.addEventListener('input', function(e) {
    const q = e.target.value.toLowerCase();
    document.querySelectorAll('.post-item').forEach(item => {
        const matchesSearch = q === '' || item.dataset.search.includes(q);
        const matchesStatus = item.dataset.statusVisible !== 'false';
        item.style.display = matchesSearch && matchesStatus ? '' : 'none';
    });
});

// Status filter
let currentFilter = 'all';
function filterStatus(filter) {
    currentFilter = filter;
    document.querySelectorAll('.status-filter').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.filter === filter);
    });
    const searchVal = (document.getElementById('blog-search')?.value || '').toLowerCase();
    document.querySelectorAll('.post-item').forEach(item => {
        const matchesStatus = filter === 'all' || item.dataset.status === filter;
        const matchesSearch = searchVal === '' || item.dataset.search.includes(searchVal);
        item.dataset.statusVisible = matchesStatus ? 'true' : 'false';
        item.style.display = matchesSearch && matchesStatus ? '' : 'none';
    });
}

// Select all (visible only)
document.getElementById('select-all-posts')?.addEventListener('change', function(e) {
    document.querySelectorAll('.post-checkbox').forEach(cb => {
        if (cb.closest('.post-item').style.display !== 'none') cb.checked = e.target.checked;
    });
});

// Bulk actions
function bulkPostAction(action) {
    const ids = Array.from(document.querySelectorAll('.post-checkbox:checked')).map(cb => cb.dataset.id);
    if (ids.length === 0) return alert('Seleziona almeno un articolo.');
    if (action === 'delete' && !confirm('Eliminare ' + ids.length + ' articoli?')) return;
    document.getElementById('bulk-post-action').value = action;
    document.getElementById('bulk-post-ids').value = ids.join(',');
    document.getElementById('bulk-post-form').submit();
}
</script>
@endsection
