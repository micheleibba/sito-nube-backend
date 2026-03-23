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

    public function generate(BlogScraperService $scraper)
    {
        // Check if already running
        $status = Cache::get('scraper_status', []);
        if (!empty($status['running'])) {
            return redirect()->route('blog.suggestions')->with('error', 'Lo scraper è già in esecuzione.');
        }

        set_time_limit(600); // 10 minutes
        ignore_user_abort(true); // Continue even if user navigates away

        // Send redirect immediately, then continue processing
        // This won't work perfectly but the status bar will track progress
        Cache::put('scraper_status', [
            'running' => true,
            'phase' => 'Avvio scraper...',
            'generated' => 0,
            'total' => 30,
            'processed' => 0,
            'started_at' => now()->toISOString(),
        ], 600);

        // Try background execution first (works on some hosts)
        $launched = $this->tryBackgroundLaunch();

        if ($launched) {
            return redirect()->route('blog.suggestions')->with('success', 'Scraper avviato! Segui il progresso nella barra in basso.');
        }

        // Fallback: run inline (blocks the request but ignore_user_abort keeps it going)
        try {
            $scraper->scrapeAndSuggest(30);
        } catch (\Exception $e) {
            Cache::put('scraper_status', [
                'running' => false,
                'phase' => 'Errore: ' . $e->getMessage(),
                'generated' => 0,
                'error' => true,
            ], 300);
        }

        $status = Cache::get('scraper_status', []);
        Cache::put('scraper_status', [
            ...$status,
            'running' => false,
            'phase' => 'Completato!',
        ], 300);

        return redirect()->route('blog.suggestions')->with('success', ($status['generated'] ?? 0) . ' suggerimenti generati.');
    }

    private function tryBackgroundLaunch(): bool
    {
        $phpBinary = PHP_BINARY;
        $artisan = base_path('artisan');

        // Try exec
        if (function_exists('exec') && !in_array('exec', array_map('trim', explode(',', ini_get('disable_functions'))))) {
            exec("{$phpBinary} {$artisan} blog:scrape --max=30 > /dev/null 2>&1 &");
            return true;
        }

        // Try proc_open
        if (function_exists('proc_open') && !in_array('proc_open', array_map('trim', explode(',', ini_get('disable_functions'))))) {
            $proc = proc_open(
                "{$phpBinary} {$artisan} blog:scrape --max=30",
                [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']],
                $pipes,
                base_path()
            );
            if (is_resource($proc)) {
                fclose($pipes[0]);
                fclose($pipes[1]);
                fclose($pipes[2]);
                return true;
            }
        }

        // Try shell_exec
        if (function_exists('shell_exec') && !in_array('shell_exec', array_map('trim', explode(',', ini_get('disable_functions'))))) {
            shell_exec("{$phpBinary} {$artisan} blog:scrape --max=30 > /dev/null 2>&1 &");
            return true;
        }

        return false;
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
