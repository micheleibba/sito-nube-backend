<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogPostController extends Controller
{
    public function index()
    {
        $posts = BlogPost::orderByDesc('published_at')->orderByDesc('created_at')->get();
        return view('admin.blog.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.blog.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_en' => 'required|string|max:255',
            'title_it' => 'required|string|max:255',
            'text_en' => 'required|string',
            'text_it' => 'required|string',
            'meta_description_en' => 'nullable|string|max:500',
            'meta_description_it' => 'nullable|string|max:500',
            'cover_image' => 'nullable|image|max:5120',
            'published' => 'boolean',
        ]);

        $validated['slug_en'] = BlogPost::generateSlug($validated['title_en'], 'en');
        $validated['slug_it'] = BlogPost::generateSlug($validated['title_it'], 'it');
        $validated['published'] = $request->boolean('published');
        $validated['published_at'] = $validated['published'] ? now() : null;

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('blog', 'public');
        }

        BlogPost::create($validated);

        return redirect()->route('blog.index')->with('success', 'Articolo creato con successo.');
    }

    public function edit(BlogPost $post)
    {
        return view('admin.blog.edit', compact('post'));
    }

    public function update(Request $request, BlogPost $post)
    {
        $validated = $request->validate([
            'title_en' => 'required|string|max:255',
            'title_it' => 'required|string|max:255',
            'text_en' => 'required|string',
            'text_it' => 'required|string',
            'meta_description_en' => 'nullable|string|max:500',
            'meta_description_it' => 'nullable|string|max:500',
            'cover_image' => 'nullable|image|max:5120',
            'published' => 'boolean',
        ]);

        $validated['slug_en'] = BlogPost::generateSlug($validated['title_en'], 'en', $post->id);
        $validated['slug_it'] = BlogPost::generateSlug($validated['title_it'], 'it', $post->id);
        $validated['published'] = $request->boolean('published');

        if (!$post->published && $validated['published']) {
            $validated['published_at'] = now();
        } elseif (!$validated['published']) {
            $validated['published_at'] = null;
        }

        if ($request->hasFile('cover_image')) {
            if ($post->cover_image) {
                Storage::disk('public')->delete($post->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('blog', 'public');
        }

        $post->update($validated);

        return redirect()->route('blog.index')->with('success', 'Articolo aggiornato.');
    }

    public function bulk(Request $request)
    {
        $ids = array_filter(explode(',', $request->input('ids', '')));
        $action = $request->input('action');

        if (empty($ids)) return back()->with('error', 'Nessun elemento selezionato.');

        $posts = BlogPost::whereIn('id', $ids)->get();

        if ($action === 'publish') {
            foreach ($posts as $post) {
                $post->update(['published' => true, 'published_at' => $post->published_at ?? now()]);
            }
            return back()->with('success', $posts->count() . ' articoli pubblicati.');
        }

        if ($action === 'draft') {
            foreach ($posts as $post) {
                $post->update(['published' => false, 'published_at' => null]);
            }
            return back()->with('success', $posts->count() . ' articoli messi in bozza.');
        }

        if ($action === 'delete') {
            foreach ($posts as $post) {
                if ($post->cover_image) Storage::disk('public')->delete($post->cover_image);
                $post->delete();
            }
            return back()->with('success', $posts->count() . ' articoli eliminati.');
        }

        return back();
    }

    public function destroy(BlogPost $post)
    {
        if ($post->cover_image) {
            Storage::disk('public')->delete($post->cover_image);
        }
        $post->delete();
        return redirect()->route('blog.index')->with('success', 'Articolo eliminato.');
    }
}
