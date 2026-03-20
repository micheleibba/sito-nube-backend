<?php

namespace App\Console\Commands;

use App\Services\BlogScraperService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ScrapeBlogArticles extends Command
{
    protected $signature = 'blog:scrape {--max=30 : Maximum articles to generate}';
    protected $description = 'Scrape tech news and generate blog suggestions';

    public function handle(BlogScraperService $scraper): int
    {
        $max = (int) $this->option('max');

        Cache::put('scraper_status', [
            'running' => true,
            'phase' => 'Avvio scraper...',
            'found' => 0,
            'total' => $max,
            'processed' => 0,
            'started_at' => now()->toISOString(),
        ], 600);

        try {
            $scraper->scrapeAndSuggest($max);
        } catch (\Exception $e) {
            Cache::put('scraper_status', [
                'running' => false,
                'phase' => 'Errore: ' . $e->getMessage(),
                'found' => Cache::get('scraper_status')['found'] ?? 0,
                'total' => $max,
                'processed' => Cache::get('scraper_status')['processed'] ?? 0,
                'error' => true,
            ], 300);
            return 1;
        }

        $status = Cache::get('scraper_status', []);
        Cache::put('scraper_status', [
            ...$status,
            'running' => false,
            'phase' => 'Completato!',
        ], 300);

        return 0;
    }
}
