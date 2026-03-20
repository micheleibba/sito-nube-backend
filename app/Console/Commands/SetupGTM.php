<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SetupGTM extends Command
{
    protected $signature = 'gtm:setup {--token= : Google OAuth2 access token}';
    protected $description = 'Configure GTM container with GA4, Meta Pixel, TikTok Pixel tags and all event triggers';

    private string $token;
    private string $accountId;
    private string $containerId;
    private string $workspaceId;

    private const GTM_API = 'https://www.googleapis.com/tagmanager/v2';
    private const CONTAINER_ID = 'GTM-T33G957V';
    private const GA4_MEASUREMENT_ID = 'G-KP94G91EY5';
    private const META_PIXEL_ID = '932118622861439';
    private const TIKTOK_PIXEL_ID = 'D1NP4B3C77U41SK2MK50';

    public function handle(): int
    {
        $this->token = $this->option('token');
        if (!$this->token) {
            $this->error('Access token required. Get one from:');
            $this->line('  1. Google OAuth Playground: https://developers.google.com/oauthplayground/');
            $this->line('     Scope: https://www.googleapis.com/auth/tagmanager.edit.containers');
            $this->line('  2. Or: gcloud auth print-access-token');
            $this->line('');
            $this->line('Then run: php artisan gtm:setup --token=YOUR_TOKEN');
            return 1;
        }

        // Find account and container
        if (!$this->findContainer()) return 1;

        // Create workspace
        if (!$this->createWorkspace()) return 1;

        $this->info('Setting up GTM container...');

        // === VARIABLES ===
        $this->createBuiltInVariables();

        // === TRIGGERS ===
        $triggers = $this->createTriggers();

        // Consent trigger - fires only when cookies are accepted
        $consentAccepted = $this->api('post', $this->basePath() . '/triggers', [
            'name' => 'Cookie Consent Accepted',
            'type' => 'CUSTOM_EVENT',
            'customEventFilter' => [[
                'type' => 'EQUALS',
                'parameter' => [
                    ['type' => 'TEMPLATE', 'key' => 'arg0', 'value' => '{{_event}}'],
                    ['type' => 'TEMPLATE', 'key' => 'arg1', 'value' => 'cookie_consent_default'],
                ],
            ]],
            'filter' => [[
                'type' => 'EQUALS',
                'parameter' => [
                    ['type' => 'TEMPLATE', 'key' => 'arg0', 'value' => '{{DLV - cookie_consent}}'],
                    ['type' => 'TEMPLATE', 'key' => 'arg1', 'value' => 'accepted'],
                ],
            ]],
        ]);
        $triggers['consent_accepted'] = $consentAccepted['triggerId'] ?? null;
        if ($consentAccepted) $this->line('  ✓ Trigger: Cookie Consent Accepted');

        // Consent update trigger
        $consentUpdate = $this->api('post', $this->basePath() . '/triggers', [
            'name' => 'Cookie Consent Update Accepted',
            'type' => 'CUSTOM_EVENT',
            'customEventFilter' => [[
                'type' => 'EQUALS',
                'parameter' => [
                    ['type' => 'TEMPLATE', 'key' => 'arg0', 'value' => '{{_event}}'],
                    ['type' => 'TEMPLATE', 'key' => 'arg1', 'value' => 'cookie_consent_update'],
                ],
            ]],
            'filter' => [[
                'type' => 'EQUALS',
                'parameter' => [
                    ['type' => 'TEMPLATE', 'key' => 'arg0', 'value' => '{{DLV - cookie_consent}}'],
                    ['type' => 'TEMPLATE', 'key' => 'arg1', 'value' => 'accepted'],
                ],
            ]],
        ]);
        $triggers['consent_update'] = $consentUpdate['triggerId'] ?? null;
        if ($consentUpdate) $this->line('  ✓ Trigger: Cookie Consent Update Accepted');

        // Create cookie_consent DLV
        $this->api('post', $this->basePath() . '/variables', [
            'name' => 'DLV - cookie_consent',
            'type' => 'v',
            'parameter' => [
                ['type' => 'INTEGER', 'key' => 'dataLayerVersion', 'value' => '2'],
                ['type' => 'BOOLEAN', 'key' => 'setDefaultValue', 'value' => 'false'],
                ['type' => 'TEMPLATE', 'key' => 'name', 'value' => 'cookie_consent'],
            ],
        ]);

        // === TAGS ===
        $this->createTags($triggers);

        // Publish
        $this->publishWorkspace();

        $this->newLine();
        $this->info('GTM setup complete! All tags, triggers and variables have been configured.');
        $this->info('GA4, Meta Pixel, and TikTok Pixel are now managed entirely through GTM.');

        return 0;
    }

    private function api(string $method, string $url, array $data = []): ?array
    {
        usleep(2200000); // 2.2s delay to stay under 30 req/min limit

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->timeout(30)->{$method}(self::GTM_API . $url, $data);

        if ($response->failed()) {
            // Retry once on rate limit
            if ($response->status() === 429) {
                $this->warn("Rate limited, waiting 10s...");
                sleep(10);
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->token,
                ])->timeout(30)->{$method}(self::GTM_API . $url, $data);
            }
            if ($response->failed()) {
                $this->warn("API Error ({$method} {$url}): " . mb_substr($response->body(), 0, 200));
                return null;
            }
        }

        return $response->json();
    }

    private function findContainer(): bool
    {
        $this->info('Finding GTM container...');

        $accounts = $this->api('get', '/accounts');
        if (!$accounts || empty($accounts['account'])) {
            $this->error('No GTM accounts found. Check your access token permissions.');
            return false;
        }

        foreach ($accounts['account'] as $account) {
            $containers = $this->api('get', "/accounts/{$account['accountId']}/containers");
            if ($containers && !empty($containers['container'])) {
                foreach ($containers['container'] as $container) {
                    if ($container['publicId'] === self::CONTAINER_ID) {
                        $this->accountId = $account['accountId'];
                        $this->containerId = $container['containerId'];
                        $this->info("Found container: {$container['name']} ({$container['publicId']})");
                        return true;
                    }
                }
            }
        }

        $this->error('Container ' . self::CONTAINER_ID . ' not found.');
        return false;
    }

    private function createWorkspace(): bool
    {
        $this->info('Creating workspace...');

        $ws = $this->api('post', "/accounts/{$this->accountId}/containers/{$this->containerId}/workspaces", [
            'name' => 'Nube Auto Setup ' . date('Ymd-Hi'),
            'description' => 'Auto-configured by Nube backend',
        ]);

        if (!$ws) {
            // Try using default workspace
            $workspaces = $this->api('get', "/accounts/{$this->accountId}/containers/{$this->containerId}/workspaces");
            if ($workspaces && !empty($workspaces['workspace'])) {
                $ws = $workspaces['workspace'][0];
            } else {
                $this->error('Failed to create or find workspace.');
                return false;
            }
        }

        $this->workspaceId = $ws['workspaceId'];
        return true;
    }

    private function basePath(): string
    {
        return "/accounts/{$this->accountId}/containers/{$this->containerId}/workspaces/{$this->workspaceId}";
    }

    private function createBuiltInVariables(): void
    {
        $this->info('Enabling built-in variables...');

        $this->api('post', $this->basePath() . '/built_in_variables', [
            'type' => ['PAGE_URL', 'PAGE_HOSTNAME', 'PAGE_PATH', 'REFERRER', 'EVENT'],
        ]);
    }

    private function createTriggers(): array
    {
        $this->info('Creating triggers...');
        $triggers = [];

        // All Pages trigger
        $t = $this->api('post', $this->basePath() . '/triggers', [
            'name' => 'All Pages',
            'type' => 'PAGEVIEW',
        ]);
        $triggers['all_pages'] = $t['triggerId'] ?? null;

        // Custom event triggers
        $events = [
            'page_view' => 'Page View (Virtual)',
            'chat_message_sent' => 'Chat - Message Sent',
            'chat_section_click' => 'Chat - Section Click',
            'blog_article_view' => 'Blog - Article View',
            'blog_article_click' => 'Blog - Article Click',
            'contact_click' => 'Contact Click',
            'outbound_click' => 'Outbound Link Click',
            'scroll_depth' => 'Scroll Depth',
            'time_on_page' => 'Time on Page',
            'cookie_accept' => 'Cookie Accept',
            'cookie_reject' => 'Cookie Reject',
        ];

        foreach ($events as $eventName => $triggerName) {
            $t = $this->api('post', $this->basePath() . '/triggers', [
                'name' => $triggerName,
                'type' => 'CUSTOM_EVENT',
                'customEventFilter' => [[
                    'type' => 'EQUALS',
                    'parameter' => [
                        ['type' => 'TEMPLATE', 'key' => 'arg0', 'value' => '{{_event}}'],
                        ['type' => 'TEMPLATE', 'key' => 'arg1', 'value' => $eventName],
                    ],
                ]],
            ]);
            $triggers[$eventName] = $t['triggerId'] ?? null;
            if ($t) $this->line("  ✓ Trigger: {$triggerName}");
        }

        return $triggers;
    }

    private function createTags(array $triggers): void
    {
        $this->info('Creating tags...');

        // === GA4 Configuration Tag ===
        $this->api('post', $this->basePath() . '/tags', [
            'name' => 'GA4 - Configuration',
            'type' => 'gaawc',
            'parameter' => [
                ['type' => 'TEMPLATE', 'key' => 'measurementId', 'value' => self::GA4_MEASUREMENT_ID],
                ['type' => 'BOOLEAN', 'key' => 'sendPageView', 'value' => 'true'],
            ],
            'firingTriggerId' => [$triggers['all_pages']],
        ]);
        $this->line('  ✓ GA4 Configuration');

        // === GA4 Event Tags ===
        $ga4Events = [
            'chat_message_sent' => ['message_length', 'message_preview'],
            'chat_section_click' => ['section', 'section_name'],
            'blog_article_view' => ['article_slug', 'content_type'],
            'blog_article_click' => ['article_slug', 'article_title'],
            'contact_click' => ['method', 'url', 'email', 'phone'],
            'outbound_click' => ['url', 'link_text'],
            'scroll_depth' => ['depth', 'page_path'],
            'time_on_page' => ['seconds', 'page_path'],
            'cookie_accept' => ['choice'],
            'cookie_reject' => ['choice'],
        ];

        foreach ($ga4Events as $eventName => $params) {
            if (!isset($triggers[$eventName])) continue;

            $eventParams = [];
            foreach ($params as $param) {
                $eventParams[] = [
                    'type' => 'MAP',
                    'map' => [
                        ['type' => 'TEMPLATE', 'key' => 'name', 'value' => $param],
                        ['type' => 'TEMPLATE', 'key' => 'value', 'value' => "{{DLV - {$param}}}"],
                    ],
                ];
            }

            $this->api('post', $this->basePath() . '/tags', [
                'name' => "GA4 Event - {$eventName}",
                'type' => 'gaawe',
                'parameter' => [
                    ['type' => 'TAG_REFERENCE', 'key' => 'measurementId', 'value' => 'GA4 - Configuration'],
                    ['type' => 'TEMPLATE', 'key' => 'eventName', 'value' => $eventName],
                    ['type' => 'LIST', 'key' => 'eventParameters', 'list' => $eventParams],
                ],
                'firingTriggerId' => [$triggers[$eventName]],
            ]);
            $this->line("  ✓ GA4 Event: {$eventName}");
        }

        // === Meta Pixel - Base Tag (fires only with consent) ===
        $metaFireTriggers = array_filter([$triggers['consent_accepted'] ?? null, $triggers['consent_update'] ?? null]);
        if (empty($metaFireTriggers)) $metaFireTriggers = [$triggers['all_pages']]; // fallback

        $this->api('post', $this->basePath() . '/tags', [
            'name' => 'Meta Pixel - Base',
            'type' => 'html',
            'parameter' => [[
                'type' => 'TEMPLATE',
                'key' => 'html',
                'value' => "<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','" . self::META_PIXEL_ID . "');fbq('track','PageView');</script><noscript><img height=\"1\" width=\"1\" style=\"display:none\" src=\"https://www.facebook.com/tr?id=" . self::META_PIXEL_ID . "&ev=PageView&noscript=1\"/></noscript>",
            ]],
            'firingTriggerId' => $metaFireTriggers,
        ]);
        $this->line('  ✓ Meta Pixel Base (consent-gated)');

        // === Meta Pixel - Event Tags ===
        $metaEvents = [
            'chat_message_sent' => 'Lead',
            'chat_section_click' => 'ViewContent',
            'blog_article_view' => 'ViewContent',
            'blog_article_click' => 'ViewContent',
            'contact_click' => 'Contact',
        ];

        foreach ($metaEvents as $eventName => $fbEvent) {
            if (!isset($triggers[$eventName])) continue;

            $this->api('post', $this->basePath() . '/tags', [
                'name' => "Meta Pixel - {$fbEvent} ({$eventName})",
                'type' => 'html',
                'parameter' => [[
                    'type' => 'TEMPLATE',
                    'key' => 'html',
                    'value' => "<script>if(typeof fbq!=='undefined'){fbq('track','{$fbEvent}');}</script>",
                ]],
                'firingTriggerId' => [$triggers[$eventName]],
            ]);
            $this->line("  ✓ Meta Pixel Event: {$fbEvent} ({$eventName})");
        }

        // === TikTok Pixel - Base Tag (fires only with consent) ===
        $this->api('post', $this->basePath() . '/tags', [
            'name' => 'TikTok Pixel - Base',
            'type' => 'html',
            'parameter' => [[
                'type' => 'TEMPLATE',
                'key' => 'html',
                'value' => "<script>!function(w,d,t){w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=['page','track','identify','instances','debug','on','off','once','ready','alias','group','enableCookie','disableCookie','holdConsent','revokeConsent','grantConsent'],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e};ttq.load=function(e,n){var r='https://analytics.tiktok.com/i18n/pixel/events.js',o=n&&n.partner;ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=r,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var i=document.createElement('script');i.type='text/javascript',i.async=!0,i.src=r+'?sdkid='+e+'&lib='+t;var a=document.getElementsByTagName('script')[0];a.parentNode.insertBefore(i,a)};ttq.load('" . self::TIKTOK_PIXEL_ID . "');ttq.page();}(window,document,'ttq');</script>",
            ]],
            'firingTriggerId' => $metaFireTriggers, // same consent logic as Meta
        ]);
        $this->line('  ✓ TikTok Pixel Base (consent-gated)');

        // === TikTok Pixel - Event Tags ===
        $ttEvents = [
            'chat_message_sent' => 'SubmitForm',
            'chat_section_click' => 'ViewContent',
            'blog_article_view' => 'ViewContent',
            'blog_article_click' => 'ClickButton',
            'contact_click' => 'Contact',
        ];

        foreach ($ttEvents as $eventName => $ttEvent) {
            if (!isset($triggers[$eventName])) continue;

            $this->api('post', $this->basePath() . '/tags', [
                'name' => "TikTok - {$ttEvent} ({$eventName})",
                'type' => 'html',
                'parameter' => [[
                    'type' => 'TEMPLATE',
                    'key' => 'html',
                    'value' => "<script>if(typeof ttq!=='undefined'){ttq.track('{$ttEvent}');}</script>",
                ]],
                'firingTriggerId' => [$triggers[$eventName]],
            ]);
            $this->line("  ✓ TikTok Event: {$ttEvent} ({$eventName})");
        }

        // === Data Layer Variables ===
        $this->info('Creating Data Layer variables...');
        $dlvNames = ['message_length', 'message_preview', 'section', 'section_name', 'article_slug', 'article_title', 'content_type', 'method', 'url', 'email', 'phone', 'link_text', 'depth', 'page_path', 'seconds', 'choice'];

        foreach ($dlvNames as $name) {
            $this->api('post', $this->basePath() . '/variables', [
                'name' => "DLV - {$name}",
                'type' => 'v',
                'parameter' => [
                    ['type' => 'INTEGER', 'key' => 'dataLayerVersion', 'value' => '2'],
                    ['type' => 'BOOLEAN', 'key' => 'setDefaultValue', 'value' => 'false'],
                    ['type' => 'TEMPLATE', 'key' => 'name', 'value' => $name],
                ],
            ]);
        }
        $this->line('  ✓ ' . count($dlvNames) . ' Data Layer variables created');
    }

    private function publishWorkspace(): void
    {
        $this->info('Publishing workspace...');

        $result = $this->api('post', $this->basePath() . ':create_version', [
            'name' => 'Nube Auto Setup ' . date('Ymd-Hi'),
            'notes' => 'GA4 + Meta Pixel + TikTok Pixel with all event tracking',
        ]);

        if ($result && isset($result['containerVersion'])) {
            $versionId = $result['containerVersion']['containerVersionId'];
            $this->api('post', "/accounts/{$this->accountId}/containers/{$this->containerId}/versions/{$versionId}:publish");
            $this->info('Container published successfully!');
        }
    }
}
