@extends('layouts.app')

@section('title', 'Nuovo Articolo - Nube')

@section('content')
<div class="flex items-center gap-2 text-sm mb-6">
    <a href="{{ route('blog.index') }}" class="text-surface-500 hover:text-white transition">Blog</a>
    <svg class="w-3.5 h-3.5 text-surface-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
    <span class="text-surface-300">Nuovo articolo</span>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold tracking-tight">Nuovo articolo</h1>
    <p class="text-sm text-surface-500 mt-1">Scrivi il contenuto in entrambe le lingue per la SEO</p>
</div>

<div class="bg-surface-900/50 border border-surface-800/50 rounded-2xl p-6">
    <form method="POST" action="{{ route('blog.store') }}" enctype="multipart/form-data">
        @include('admin.blog._form')

        <div class="flex items-center gap-3 mt-6 pt-4 border-t border-surface-800/50">
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-accent to-accent-dark text-white text-sm font-semibold rounded-xl hover:from-accent-light hover:to-accent transition-all duration-200 shadow-lg shadow-accent/20">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Crea articolo
            </button>
            <a href="{{ route('blog.index') }}" class="px-5 py-2.5 border border-surface-700/50 rounded-xl text-sm text-surface-400 hover:text-white hover:border-surface-600 transition">Annulla</a>
        </div>
    </form>
</div>
@endsection
