<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogSuggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class BlogSuggestionController extends Controller
{
    public function index()
    {
        $suggestions = BlogSuggestion::where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.blog.suggestions', compact('suggestions'));
    }

    public function generate()
    {
        // Check if already running
        $status = Cache::get('scraper_status', []);
        if (!empty($status['running'])) {
            return redirect()->route('blog.suggestions')->with('error', 'Lo scraper è già in esecuzione.');
        }

        // Launch in background
        $phpBinary = PHP_BINARY;
        $artisan = base_path('artisan');
        $command = "{$phpBinary} {$artisan} blog:scrape --max=30 > /dev/null 2>&1 &";
        exec($command);

        return redirect()->route('blog.suggestions')->with('success', 'Scraper avviato! Segui il progresso nella barra in basso.');
    }

    public function status()
    {
        $status = Cache::get('scraper_status', [
            'running' => false,
            'phase' => '',
            'found' => 0,
            'total' => 0,
            'processed' => 0,
        ]);

        // Count current pending suggestions
        $status['pending_count'] = BlogSuggestion::where('status', 'pending')->count();

        return response()->json($status);
    }

    public function approve(BlogSuggestion $suggestion)
    {
        $post = BlogPost::create([
            'title_en' => $suggestion->title_en,
            'title_it' => $suggestion->title_it,
            'slug_en' => BlogPost::generateSlug($suggestion->title_en, 'en'),
            'slug_it' => BlogPost::generateSlug($suggestion->title_it, 'it'),
            'text_en' => $suggestion->text_en,
            'text_it' => $suggestion->text_it,
            'meta_description_en' => $suggestion->meta_description_en,
            'meta_description_it' => $suggestion->meta_description_it,
            'cover_image' => $suggestion->cover_image_path,
            'published' => true,
            'published_at' => now(),
        ]);

        $suggestion->update(['status' => 'approved']);

        return redirect()->route('blog.edit', $post)->with('success', 'Articolo pubblicato.');
    }

    public function bulk(Request $request)
    {
        $ids = array_filter(explode(',', $request->input('ids', '')));
        $action = $request->input('action');

        if (empty($ids)) return back()->with('error', 'Nessun elemento selezionato.');

        $suggestions = BlogSuggestion::whereIn('id', $ids)->where('status', 'pending')->get();

        if ($action === 'approve') {
            foreach ($suggestions as $s) {
                BlogPost::create([
                    'title_en' => $s->title_en,
                    'title_it' => $s->title_it,
                    'slug_en' => BlogPost::generateSlug($s->title_en, 'en'),
                    'slug_it' => BlogPost::generateSlug($s->title_it, 'it'),
                    'text_en' => $s->text_en,
                    'text_it' => $s->text_it,
                    'meta_description_en' => $s->meta_description_en,
                    'meta_description_it' => $s->meta_description_it,
                    'cover_image' => $s->cover_image_path,
                    'published' => true,
                    'published_at' => now(),
                ]);
                $s->update(['status' => 'approved']);
            }
            return back()->with('success', $suggestions->count() . ' articoli approvati come bozze.');
        }

        if ($action === 'reject') {
            foreach ($suggestions as $s) {
                if ($s->cover_image_path) Storage::disk('public')->delete($s->cover_image_path);
                $s->update(['status' => 'rejected']);
            }
            return back()->with('success', $suggestions->count() . ' suggerimenti rifiutati.');
        }

        return back();
    }

    public function reject(BlogSuggestion $suggestion)
    {
        if ($suggestion->cover_image_path) {
            Storage::disk('public')->delete($suggestion->cover_image_path);
        }
        $suggestion->update(['status' => 'rejected']);

        return redirect()->route('blog.suggestions')->with('success', 'Suggerimento rifiutato.');
    }
}
