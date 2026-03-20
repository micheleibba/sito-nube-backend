<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ChatRateLimit
{
    // Shared secret for token validation - must match frontend
    private const TOKEN_SECRET = 'nube_chat_2024_s3cr3t';

    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        // === INVISIBLE CAPTCHA: JS Challenge Token ===
        $token = $request->input('_token_check');
        if (!$token || !$this->validateCaptchaToken($token)) {
            // Bot detected - return fake success to not alert the bot
            return response()->json(['reply' => 'Thank you for your message!']);
        }

        // === HONEYPOT: hidden field "website" ===
        if ($request->filled('website')) {
            return response()->json(['reply' => 'Thank you for your message!']);
        }

        // === RATE LIMITING ===

        // Per-minute: 15 requests
        $minuteKey = "chat_rate:{$ip}:minute:" . floor(time() / 60);
        $minuteCount = (int) Cache::get($minuteKey, 0);
        if ($minuteCount >= 15) {
            return response()->json([
                'error' => 'Too many requests. Please wait a moment.',
                'retry_after' => 60,
            ], 429);
        }

        // Per-hour: 150 requests
        $hourKey = "chat_rate:{$ip}:hour:" . floor(time() / 3600);
        $hourCount = (int) Cache::get($hourKey, 0);
        if ($hourCount >= 150) {
            return response()->json([
                'error' => 'Hourly limit reached. Please try again later.',
                'retry_after' => 3600,
            ], 429);
        }

        // Per-day: 500 requests
        $dayKey = "chat_rate:{$ip}:day:" . date('Y-m-d');
        $dayCount = (int) Cache::get($dayKey, 0);
        if ($dayCount >= 500) {
            return response()->json([
                'error' => 'Daily limit reached. Please try again tomorrow.',
                'retry_after' => 86400,
            ], 429);
        }

        // Message length check
        $message = $request->input('message', '');
        if (strlen($message) > 2000) {
            return response()->json(['error' => 'Message too long.'], 422);
        }

        // Increment counters
        Cache::put($minuteKey, $minuteCount + 1, 120);
        Cache::put($hourKey, $hourCount + 1, 7200);
        Cache::put($dayKey, $dayCount + 1, 86400);

        $response = $next($request);

        $response->headers->set('X-RateLimit-Remaining-Minute', max(0, 15 - $minuteCount - 1));
        $response->headers->set('X-RateLimit-Remaining-Hour', max(0, 150 - $hourCount - 1));

        return $response;
    }

    private function validateCaptchaToken(string $token): bool
    {
        // Token format: base64(timestamp:hash)
        $decoded = base64_decode($token, true);
        if (!$decoded) {
            return false;
        }

        $parts = explode(':', $decoded, 2);
        if (count($parts) !== 2) {
            return false;
        }

        [$timestamp, $hash] = $parts;
        $timestamp = (int) $timestamp;

        // Token must be recent (max 5 minutes old)
        $now = time();
        if (abs($now - $timestamp) > 300) {
            return false;
        }

        // Verify hash: the client must compute this from timestamp + secret + simple math
        $expectedHash = hash('sha256', $timestamp . self::TOKEN_SECRET . ($timestamp % 97));

        return hash_equals($expectedHash, $hash);
    }
}
