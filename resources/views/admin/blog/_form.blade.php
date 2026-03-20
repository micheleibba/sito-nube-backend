@csrf

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- English --}}
    <div class="space-y-4">
        <div class="flex items-center gap-2 mb-2">
            <span class="text-xs font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/20 px-2 py-0.5 rounded">EN</span>
            <span class="text-sm font-medium text-surface-400">English</span>
        </div>

        <div>
            <label for="title_en" class="block text-xs font-medium text-surface-400 mb-1.5">Title</label>
            <input type="text" id="title_en" name="title_en" value="{{ old('title_en', $post->title_en ?? '') }}" required
                class="input-field w-full px-3.5 py-2.5 bg-surface-950/50 border border-surface-700/50 rounded-xl text-sm text-white placeholder-surface-600 focus:outline-none"
                placeholder="Article title in English">
            @error('title_en')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="text_en" class="block text-xs font-medium text-surface-400 mb-1.5">Content</label>
            <textarea id="text_en" name="text_en" rows="14" required
                class="input-field w-full px-3.5 py-2.5 bg-surface-950/50 border border-surface-700/50 rounded-xl text-sm text-white placeholder-surface-600 focus:outline-none resize-y"
                placeholder="Article content in English...">{{ old('text_en', $post->text_en ?? '') }}</textarea>
            @error('text_en')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="meta_description_en" class="block text-xs font-medium text-surface-400 mb-1.5">Meta description (SEO)</label>
            <textarea id="meta_description_en" name="meta_description_en" rows="2"
                class="input-field w-full px-3.5 py-2.5 bg-surface-950/50 border border-surface-700/50 rounded-xl text-sm text-white placeholder-surface-600 focus:outline-none resize-y"
                placeholder="Brief description for search engines (max 160 chars)...">{{ old('meta_description_en', $post->meta_description_en ?? '') }}</textarea>
        </div>
    </div>

    {{-- Italian --}}
    <div class="space-y-4">
        <div class="flex items-center gap-2 mb-2">
            <span class="text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-2 py-0.5 rounded">IT</span>
            <span class="text-sm font-medium text-surface-400">Italiano</span>
        </div>

        <div>
            <label for="title_it" class="block text-xs font-medium text-surface-400 mb-1.5">Titolo</label>
            <input type="text" id="title_it" name="title_it" value="{{ old('title_it', $post->title_it ?? '') }}" required
                class="input-field w-full px-3.5 py-2.5 bg-surface-950/50 border border-surface-700/50 rounded-xl text-sm text-white placeholder-surface-600 focus:outline-none"
                placeholder="Titolo dell'articolo in italiano">
            @error('title_it')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="text_it" class="block text-xs font-medium text-surface-400 mb-1.5">Contenuto</label>
            <textarea id="text_it" name="text_it" rows="14" required
                class="input-field w-full px-3.5 py-2.5 bg-surface-950/50 border border-surface-700/50 rounded-xl text-sm text-white placeholder-surface-600 focus:outline-none resize-y"
                placeholder="Contenuto dell'articolo in italiano...">{{ old('text_it', $post->text_it ?? '') }}</textarea>
            @error('text_it')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="meta_description_it" class="block text-xs font-medium text-surface-400 mb-1.5">Meta description (SEO)</label>
            <textarea id="meta_description_it" name="meta_description_it" rows="2"
                class="input-field w-full px-3.5 py-2.5 bg-surface-950/50 border border-surface-700/50 rounded-xl text-sm text-white placeholder-surface-600 focus:outline-none resize-y"
                placeholder="Breve descrizione per i motori di ricerca (max 160 caratteri)...">{{ old('meta_description_it', $post->meta_description_it ?? '') }}</textarea>
        </div>
    </div>
</div>

{{-- Cover image + Settings --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <div>
        <label class="block text-xs font-medium text-surface-400 mb-1.5">Cover Image</label>
        @if(isset($post) && $post->cover_image)
            <div class="mb-3 rounded-xl overflow-hidden border border-surface-700/50">
                <img src="{{ asset('storage/' . $post->cover_image) }}" alt="" class="w-full h-40 object-cover">
            </div>
        @endif
        <input type="file" name="cover_image" accept="image/*"
            class="w-full text-sm text-surface-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-surface-800 file:text-surface-300 hover:file:bg-surface-700 file:cursor-pointer cursor-pointer">
        @error('cover_image')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="flex items-end">
        <label class="flex items-center gap-3 cursor-pointer">
            <input type="hidden" name="published" value="0">
            <input type="checkbox" name="published" value="1" {{ old('published', $post->published ?? false) ? 'checked' : '' }}
                class="w-4 h-4 rounded border-surface-600 bg-surface-800 text-accent focus:ring-accent/30 focus:ring-offset-0">
            <div>
                <span class="text-sm font-medium">Pubblica articolo</span>
                <p class="text-xs text-surface-500">Se attivo, l'articolo sarà visibile sul sito</p>
            </div>
        </label>
    </div>
</div>
