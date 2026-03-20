<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\BlogSuggestion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogScraperService
{
    private array $sources = [
        ['name' => 'MIT Technology Review', 'url' => 'https://www.technologyreview.com/feed/'],
        ['name' => 'TechCrunch AI', 'url' => 'https://techcrunch.com/category/artificial-intelligence/feed/'],
        ['name' => 'The Verge', 'url' => 'https://www.theverge.com/rss/index.xml'],
        ['name' => 'AI News', 'url' => 'https://www.artificialintelligence-news.com/feed/'],
        ['name' => 'KDnuggets', 'url' => 'https://www.kdnuggets.com/feed'],
        ['name' => 'Towards AI', 'url' => 'https://towardsai.net/feed'],
        ['name' => 'Wired', 'url' => 'https://www.wired.com/feed/rss'],
        ['name' => 'IEEE Spectrum', 'url' => 'https://spectrum.ieee.org/feeds/feed.rss'],
    ];

    private function updateStatus(array $data): void
    {
        $current = Cache::get('scraper_status', []);
        Cache::put('scraper_status', array_merge($current, $data), 600);
    }

    public function scrapeAndSuggest(int $maxArticles = 15): array
    {
        $results = [];
        $articlesFound = [];

        // Phase 1: Fetch RSS feeds
        foreach ($this->sources as $i => $source) {
            $this->updateStatus([
                'phase' => "Scansione {$source['name']}...",
                'progress_detail' => ($i + 1) . '/' . count($this->sources) . ' testate',
            ]);

            try {
                $links = $this->fetchRssFeed($source);
                foreach ($links as $link) {
                    // Check duplicates by URL
                    if ($this->isDuplicate($link['url'], $link['title'])) {
                        continue;
                    }
                    $articlesFound[] = [
                        'source_name' => $source['name'],
                        'url' => $link['url'],
                        'title' => $link['title'],
                        'description' => $link['description'] ?? '',
                    ];
                }
            } catch (\Exception $e) {
                Log::warning("Scraper: Failed to fetch {$source['name']}: " . $e->getMessage());
            }
        }

        $this->updateStatus([
            'phase' => 'Selezione articoli migliori con AI...',
            'rss_found' => count($articlesFound),
            'generated' => 0,
        ]);

        if (empty($articlesFound)) {
            return [];
        }

        // Phase 2: Select best articles
        $selectedArticles = $this->selectBestArticles($articlesFound, $maxArticles);
        $totalToProcess = count($selectedArticles);

        $this->updateStatus([
            'phase' => "Rielaborazione articoli (0/{$totalToProcess})...",
            'total' => $totalToProcess,
            'processed' => 0,
        ]);

        // Phase 3: Process each article
        foreach ($selectedArticles as $idx => $article) {
            $this->updateStatus([
                'phase' => "Rielaborazione: " . Str::limit($article['title'], 50) . "...",
                'processed' => $idx,
                'progress_detail' => ($idx + 1) . "/{$totalToProcess} articoli",
            ]);

            try {
                // Double-check duplicate
                if ($this->isDuplicate($article['url'], $article['title'])) {
                    continue;
                }

                // Fetch content
                $this->updateStatus(['phase' => "Lettura articolo " . ($idx + 1) . "/{$totalToProcess}..."]);
                $fullContent = $this->fetchArticleContent($article['url']);
                if (strlen($fullContent) < 100) {
                    $fullContent = ($article['description'] ?? '') . "\n\nTopic: " . $article['title'];
                }

                // Rework with AI
                $this->updateStatus(['phase' => "AI rielaborazione " . ($idx + 1) . "/{$totalToProcess}..."]);
                $reworked = $this->reworkArticle($article['title'], $fullContent, $article['source_name']);
                if (!$reworked) continue;

                // Generate image
                $this->updateStatus(['phase' => "Generazione immagine " . ($idx + 1) . "/{$totalToProcess}..."]);
                $imageUrl = $this->generateCoverImage($reworked['title_en']);
                $imagePath = $this->saveImage($imageUrl);

                // Save suggestion
                $suggestion = BlogSuggestion::create([
                    'source_url' => $article['url'],
                    'source_name' => $article['source_name'],
                    'original_title' => $article['title'],
                    'title_en' => $reworked['title_en'],
                    'title_it' => $reworked['title_it'],
                    'text_en' => $reworked['text_en'],
                    'text_it' => $reworked['text_it'],
                    'meta_description_en' => $reworked['meta_description_en'],
                    'meta_description_it' => $reworked['meta_description_it'],
                    'cover_image_url' => $imageUrl,
                    'cover_image_path' => $imagePath,
                    'status' => 'pending',
                ]);

                $results[] = $suggestion;

                $this->updateStatus([
                    'processed' => $idx + 1,
                    'generated' => count($results),
                    'last_title' => $reworked['title_en'],
                ]);

                Log::info("Scraper: Created suggestion '{$reworked['title_en']}'");
            } catch (\Exception $e) {
                Log::warning("Scraper: Failed to process '{$article['title']}': " . $e->getMessage());
            }
        }

        return $results;
    }

    private function isDuplicate(string $url, string $title): bool
    {
        // Check by exact URL
        $existsByUrl = BlogSuggestion::where('source_url', $url)->exists()
            || BlogPost::where('slug_en', Str::slug($title))->exists();

        if ($existsByUrl) return true;

        // Check by similar title (normalized)
        $normalizedTitle = strtolower(preg_replace('/[^a-z0-9\s]/i', '', $title));
        $words = array_filter(explode(' ', $normalizedTitle), fn($w) => strlen($w) > 3);
        $keyWords = array_slice($words, 0, 5);

        if (empty($keyWords)) return false;

        $existing = BlogSuggestion::query();
        foreach ($keyWords as $word) {
            $existing->where('original_title', 'like', "%{$word}%");
        }
        if ($existing->exists()) return true;

        $existingPost = BlogPost::query();
        foreach ($keyWords as $word) {
            $existingPost->where('title_en', 'like', "%{$word}%");
        }

        return $existingPost->exists();
    }

    private function fetchRssFeed(array $source): array
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
        ])->timeout(15)->get($source['url']);

        if ($response->failed()) return [];

        $xml = $response->body();
        $articles = [];

        libxml_use_internal_errors(true);
        $feed = simplexml_load_string($xml);
        if (!$feed) {
            $feed = simplexml_load_string($xml, 'SimpleXMLElement', 0, 'http://www.w3.org/2005/Atom');
        }
        libxml_clear_errors();
        if (!$feed) return [];

        // RSS 2.0
        if (isset($feed->channel->item)) {
            foreach ($feed->channel->item as $item) {
                $title = trim((string) $item->title);
                $link = trim((string) $item->link);
                $desc = strip_tags(trim((string) ($item->description ?? '')));
                if ($title && $link && strlen($title) > 15) {
                    $articles[] = ['url' => $link, 'title' => $title, 'description' => mb_substr($desc, 0, 500)];
                }
                if (count($articles) >= 8) break;
            }
        }

        // Atom
        if (empty($articles) && isset($feed->entry)) {
            foreach ($feed->entry as $entry) {
                $title = trim((string) $entry->title);
                $link = '';
                if (isset($entry->link)) {
                    foreach ($entry->link as $l) {
                        if ((string) $l['rel'] === 'alternate' || !(string) $l['rel']) {
                            $link = (string) $l['href'];
                            break;
                        }
                    }
                    if (!$link) $link = (string) $entry->link['href'];
                }
                $desc = strip_tags(trim((string) ($entry->summary ?? $entry->content ?? '')));
                if ($title && $link && strlen($title) > 15) {
                    $articles[] = ['url' => $link, 'title' => $title, 'description' => mb_substr($desc, 0, 500)];
                }
                if (count($articles) >= 8) break;
            }
        }

        return $articles;
    }

    private function fetchArticleContent(string $url): string
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
            ])->timeout(15)->get($url);

            if ($response->failed()) return '';
            $html = $response->body();
            $text = '';

            if (preg_match('/<article[^>]*>(.*?)<\/article>/si', $html, $m)) {
                $text = $m[1];
            } elseif (preg_match('/<main[^>]*>(.*?)<\/main>/si', $html, $m)) {
                $text = $m[1];
            }

            if (!$text) {
                preg_match_all('/<p[^>]*>(.*?)<\/p>/si', $html, $matches);
                $text = implode("\n", $matches[1] ?? []);
            }

            $text = strip_tags($text);
            $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
            $text = preg_replace('/\s+/', ' ', $text);
            return mb_substr(trim($text), 0, 6000);
        } catch (\Exception $e) {
            return '';
        }
    }

    private function selectBestArticles(array $articles, int $count): array
    {
        $articleList = collect($articles)->map(fn($a, $i) => "{$i}. [{$a['source_name']}] {$a['title']}")->implode("\n");

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.key'),
        ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => "You are an editor for a tech company blog about AI, software, automation, digital innovation. Select the {$count} most interesting articles from this list. Prefer AI, machine learning, business tech, CRM, automation topics. Return ONLY a JSON array of indices like [0, 3, 7]."],
                ['role' => 'user', 'content' => $articleList],
            ],
            'max_tokens' => 200,
            'temperature' => 0.3,
        ]);

        $content = $response->json('choices.0.message.content', '[]');
        preg_match('/\[[\d,\s]+\]/', $content, $matches);
        $indices = json_decode($matches[0] ?? '[]', true) ?? [];

        return collect($indices)
            ->filter(fn($i) => isset($articles[$i]))
            ->map(fn($i) => $articles[$i])
            ->take($count)
            ->values()
            ->toArray();
    }

    private function reworkArticle(string $title, string $content, string $sourceName): ?array
    {
        $prompt = <<<PROMPT
        You are a world-class technology journalist. Write a completely original blog article inspired by the TOPIC below.

        RULES:
        - Write an ENTIRELY NEW article. Do NOT copy or paraphrase the source.
        - Use the source only to understand the TOPIC, then write your own unique perspective.
        - Add your own examples, analogies, and observations.
        - Different structure and flow from the original.
        - Professional, engaging tone for a software company blog.
        - 600-1000 words. Plain text, paragraphs separated by double newlines. No markdown.

        TOPIC: {$title}
        SOURCE MATERIAL (topic inspiration only):
        {$content}

        Return ONLY valid JSON (no code blocks):
        {"title_en":"Original English title","title_it":"Italian title","text_en":"Full English article","text_it":"Full Italian article","meta_description_en":"SEO desc EN max 155 chars","meta_description_it":"SEO desc IT max 155 chars"}
        PROMPT;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.key'),
        ])->timeout(120)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'max_tokens' => 4000,
            'temperature' => 0.8,
        ]);

        $raw = $response->json('choices.0.message.content', '');
        $raw = preg_replace('/^```json\s*/m', '', $raw);
        $raw = preg_replace('/```\s*$/m', '', $raw);

        $data = json_decode(trim($raw), true);
        if (!$data || !isset($data['title_en'], $data['text_en'])) return null;
        return $data;
    }

    private function generateCoverImage(string $title): ?string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.key'),
            ])->timeout(60)->post('https://api.openai.com/v1/images/generations', [
                'model' => 'dall-e-3',
                'prompt' => "Create a unique, visually striking blog cover image for: \"{$title}\". CRITICAL: absolutely NO text, NO letters, NO words, NO numbers, NO watermarks, NO labels anywhere. Each image must look completely different from others. Randomly pick ONE of these styles: realistic photography, oil painting, watercolor, 3D render, minimalist illustration, collage art, aerial landscape, macro close-up, abstract expressionism, retro vintage poster (no text), cinematic movie still, architectural photography. The image should evoke the MOOD and CONCEPT of the topic, not literally show tech. For example: if about AI, show nature patterns that resemble neural connections; if about data, show flowing water or light trails; if about business, show vast landscapes or cityscapes. Use varied color palettes: sometimes warm earth tones, sometimes cool ocean blues, sometimes vivid sunset colors, sometimes muted pastels, sometimes high contrast black and white. Professional quality, landscape 16:9.",
                'n' => 1,
                'size' => '1792x1024',
                'quality' => 'standard',
            ]);
            return $response->json('data.0.url');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function saveImage(?string $url): ?string
    {
        if (!$url) return null;
        try {
            $contents = Http::timeout(30)->get($url)->body();
            $filename = 'blog/suggestion_' . uniqid() . '.png';
            Storage::disk('public')->put($filename, $contents);
            return $filename;
        } catch (\Exception $e) {
            return null;
        }
    }
}
