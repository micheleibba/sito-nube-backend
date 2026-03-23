<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogSuggestion;
use App\Services\BlogScraperService;
use Illuminate\Http\Request;
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
        // Reset stuck status
        Cache::forget('scraper_status');
        return redirect()->route('blog.suggestions')->with('success', 'Clicca il pulsante "Avvia generazione" per iniziare.');
    }

    // Called via AJAX - runs the scraper inline with long timeout
    public function run(BlogScraperService $scraper)
    {
        $status = Cache::get('scraper_status', []);
        if (!empty($status['running'])) {
            return response()->json(['error' => 'Già in esecuzione'], 409);
        }

        set_time_limit(600);
        ignore_user_abort(true);

        // Flush output to release the browser connection
        if (function_exists('fastcgi_finish_request')) {
            // Return response immediately, continue processing
            Cache::put('scraper_status', [
                'running' => true,
                'phase' => 'Avvio scraper...',
                'generated' => 0,
                'total' => 30,
                'processed' => 0,
                'started_at' => now()->toISOString(),
            ], 600);

            echo json_encode(['started' => true]);
            fastcgi_finish_request();

            // Now run the scraper after response is sent
            try {
                $scraper->scrapeAndSuggest(30);
            } catch (\Exception $e) {
                Cache::put('scraper_status', [
                    'running' => false,
                    'phase' => 'Errore: ' . $e->getMessage(),
                    'generated' => 0,
                    'error' => true,
                ], 300);
                return;
            }

            $s = Cache::get('scraper_status', []);
            Cache::put('scraper_status', [...$s, 'running' => false, 'phase' => 'Completato!'], 300);
            return;
        }

        // Fallback without fastcgi: run and return when done
        Cache::put('scraper_status', [
            'running' => true,
            'phase' => 'Avvio scraper...',
            'generated' => 0,
            'total' => 30,
            'processed' => 0,
        ], 600);

        try {
            $scraper->scrapeAndSuggest(30);
        } catch (\Exception $e) {
            Cache::put('scraper_status', [
                'running' => false,
                'phase' => 'Errore: ' . $e->getMessage(),
                'generated' => 0,
                'error' => true,
            ], 300);
            return response()->json(['error' => $e->getMessage()], 500);
        }

        $s = Cache::get('scraper_status', []);
        Cache::put('scraper_status', [...$s, 'running' => false, 'phase' => 'Completato!'], 300);

        return response()->json(['ok' => true, 'generated' => $s['generated'] ?? 0]);
    }

    public function stop()
    {
        Cache::put('scraper_status', [
            'running' => false,
            'phase' => 'Fermato manualmente.',
            'generated' => 0,
            'stop_requested' => true,
        ], 300);

        return response()->json(['ok' => true]);
    }

    public function status()
    {
        $status = Cache::get('scraper_status', [
            'running' => false,
            'phase' => '',
            'generated' => 0,
            'total' => 0,
            'processed' => 0,
        ]);

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
            return back()->with('success', $suggestions->count() . ' articoli approvati e pubblicati.');
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
